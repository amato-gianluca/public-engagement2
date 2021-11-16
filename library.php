<?php
set_error_handler('error_handler');
set_exception_handler('exception_handler');
error_reporting(E_ALL | E_STRICT);

ob_start();
session_name('uda-competenze');
session_start();

require_once __DIR__ . '/vendor/autoload.php';

try {
    $esse3 = new PDO(ESSE3_DSN, ESSE3_USERNAME, ESSE3_PASSWORD,
        [ PDO::MYSQL_ATTR_SSL_CA =>  __DIR__ . '/ca_esse3.pem' ]);
} catch (PDOException $e) {
    die('MySQL Connection to ESSE3 failed: ' . $e->getMessage());
}

try {
    $pe = new PDO(PE_DNS, PE_USERNAME, PE_PASSWORD);
} catch (PDOException $e) {
    die('MySQL Connection to PE failed: ' . $e->getMessage());
}

$iris_dsn = 'mongodb://'.rawurlencode(IRIS_USERNAME).':'.rawurlencode(IRIS_PASSWORD).'@'.rawurlencode(IRIS_HOST);
$iris = (new MongoDB\Client($iris_dsn))->iris;

function h($text) {
    return htmlspecialchars($text);
}

function map_error_code ($errno) {
    $error = $log = null;
    switch ($errno) {
        case E_PARSE:
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
            $error = 'Fatal Error';
            break;
        case E_WARNING:
        case E_USER_WARNING:
        case E_COMPILE_WARNING:
        case E_RECOVERABLE_ERROR:
            $error = 'Warning';
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $error = 'Notice';
            break;
        case E_STRICT:
            $error = 'Strict';
            break;
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            $error = 'Deprecated';
            break;
        default :
            break;
    }
    return $error;
}

function exception_handler($ex) {
    ob_clean();
    if (DEBUG) { ?>
        <b>Uncaught exception</b>
        <pre><?= $ex ?></pre>
    <?php } else { ?>
        Si è verificato un errore. Contatta lo sviluppatore del sofware.
    <?php }
    die();
}

function error_handler($errno, $errstr, $errfile, $errline) {
    //if (($errno & error_reporting()) == 0) return;
    ob_clean();
    if (DEBUG) {
        $errstr = htmlspecialchars($errstr);
    ?>
        <b><?= map_error_code($errno) ?>:</b> <?= $errstr ?> in <?= $errfile ?> on line <?= $errline ?>
        <br>
        <pre><?php debug_print_backtrace(); ?></pre>
    <?php } else { ?>
        Si è verificato un errore. Contatta lo sviluppatore del sofware.
    <?php }
    die();
}

function esse3_get_docenti($dip_id = '031313') {
    global $esse3;

    $query = $esse3 -> prepare('SELECT * FROM V_IE_RU_PERS_ATTIVO WHERE CD_AFF_ORG = ? ORDER BY COGNOME, NOME');
    $result = $query -> execute([$dip_id]);
    return $query -> fetchAll();
}

function esse3_get_author_by_matricola($matricola) {
    global $esse3;

    $query = $esse3 -> prepare('SELECT * FROM DOCENTI WHERE MATRICOLA = ?');
    $result = $query -> execute([$matricola]);
    return $query->fetch();
}

function esse3_get_authors_by_matricole($matricole) {
    // It can be used to speed-up repeated executions of esse3_get_author_by_matricola, useful
    // when the RTT between this web app and the ESSE3 mirror is high.
    global $esse3;

    $inQuery = implode(',', array_fill(0, count($matricole), '?'));
    $query = $esse3 -> prepare('SELECT * FROM DOCENTI WHERE MATRICOLA IN ('.$inQuery.')');
    foreach ($matricole as $k => $matricola) {
        $query->bindValue($k+1, $matricola);
    }
    $result = $query -> execute();
    return $query->fetchAll();
}

function esse3_get_cv_by_matricola($matricola) {
    global $esse3;

    $query = $esse3 -> prepare('SELECT * FROM CV_PERSONE WHERE MATRICOLA = ?');
    $result = $query -> execute([$matricola]);
    return $query->fetch();
}

function esse3_get_role_by_matricola($matricola) {
    global $esse3;

    $query = $esse3 -> prepare('SELECT * FROM V_IE_RU_PERS_ATTIVO WHERE MATRICOLA = ?');
    $result = $query -> execute([$matricola]);
    return $query->fetch();
}

function iris_crisId_to_matricola($crisId) {
    global $iris;

    // sebbene anche il campo userSet.username contenga il numero di matricola, non è presente sempre
    $author = $iris->authors->findOne(['crisId' => $crisId], ['projection' => ['gaSourceIdentifiers' => 1]]);
    foreach ($author['gaSourceIdentifiers'] as &$source) {
        if ($source['sourceTable'] == "UGOV.CSA.PERSON")
            return $source['sourceId'];
    }
    return null;
}

function iris_matricola_to_crisId($username) {
    global $iris;
    $author = $iris->authors->findOne(['gaSourceIdentifiers.sourceId' => $username], ['projection' => ['crisId' => 1]]);
    return $author['crisId'];
}

function iris_get_paper_from_crisId($crisId) {
    global $iris;
    $items = $iris->items->find(['internalAuthors.authority' => $crisId, 'lookupValues.year' => [ '$gte' => strval(2015) ]]);
    return $items;
}

function iris_format_paper($paper) {
    $l = $paper['lookupValues'];
    $appeared = $paper['collection']['id']== 23 ? "in {$l['book']}, " : '';
    return <<<DOC
        {$l['contributors']}<br>
        <i>{$l['title']}</i><br>
        $appeared
        {$l['year']}
    DOC;
}

function iris_get_docenti($search, $limit=20) {
    global $iris;

    $results = [ ];
    $items = $iris->items->find([
        '$text' => [ '$search' => $search ]
    ], [
        'projection' =>  [
            'score' => [ '$meta' => 'textScore' ],
            'internalAuthors' => 1
        ]
    ]);
    foreach  ($items as $item) {
        $authors = $item['internalAuthors'];
        foreach ($authors as &$author) {
            $crisId = $author['authority'];
            if (! array_key_exists($crisId, $results)) {
                $results[$crisId] = [ 'name' => $author['author'], 'crisId' => $crisId, 'score' => 0.0 ];
            }
            $results[$crisId]['score'] += $item['score'];
        }
    }
    usort($results, function ($a, $b) { return ($a['score'] == $b['score']) ? 0 : (($a['score'] < $b['score']) ? 1 : -1); });
    $results = array_slice($results, 0, $limit);
    foreach ($results as &$author) {
        $matricola = iris_crisId_to_matricola($author['crisId']);
        $esse3_author = esse3_get_author_by_matricola($matricola);
        if ($esse3_author) {
            $author['name'] = $esse3_author['COGNOME'] . ' ' .  $esse3_author['NOME'];
        } else {
            $author['name'] = '---> ' . $matricola. ' ' .$author['name'];
        }
    }
    return $results;
}

function pe_get_researcher($username) {
    global $pe;
    $query = $pe -> prepare('SELECT * FROM researchers WHERE username = ?');
    $result = $query -> execute([$username]);
    return $query -> fetch();
}

function pe_create_researcher($username) {
    global $pe;
    $query = $pe -> prepare('INSERT INTO researchers (username) VALUES (?)');
    $result = $query -> execute([$username]);
    return $result;
}

function pe_edit_researcher($username, $data) {
    global $pe;

    $query = $pe -> prepare(<<<SQL
        UPDATE researchers
        SET
            keywords_en = ?,
            interests_en = ?,
            demerging_en = ?,
            awards_en = ?,
            curriculum_en = ?,
            keywords_it = ?,
            interests_it = ?,
            demerging_it = ?,
            awards_it = ?,
            curriculum_it = ?
        WHERE username = ?
    SQL);
    $result = $query -> execute([
        $data['keywords_en'], $data['interests_en'], $data['demerging_en'], $data['awards_en'], $data['curriculum_en'],
        $data['keywords_it'], $data['interests_it'], $data['demerging_it'], $data['awards_it'], $data['curriculum_it'],
        $username
    ]);
    return $result;
}

function redirect_browser($url) {
    header("Location: $url");
    die();
}