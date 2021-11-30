<?php
set_error_handler('error_handler');
set_exception_handler('exception_handler');
error_reporting(E_ALL | E_STRICT);

ob_start();
session_name('uda-competenze');
session_start();

if (file_exists(__DIR__ . '/config.php'))
    require_once __DIR__ . '/config.php';

require_once __DIR__ . '/vendor/autoload.php';

try {
    $esse3 = new PDO(get_config('ESSE3_DSN'), get_config('ESSE3_USERNAME'), get_config('ESSE3_PASSWORD'),
        [ PDO::MYSQL_ATTR_SSL_CA =>  __DIR__ . '/ca_esse3.pem' ]);
} catch (PDOException $e) {
    die('MySQL Connection to ESSE3 failed: ' . $e->getMessage());
}

try {
    $pe = new PDO(get_config('PE_DSN'), get_config('PE_USERNAME'), get_config('PE_PASSWORD'));
} catch (PDOException $e) {
    die('MySQL Connection to PE failed: ' . $e->getMessage());
}

$iris_dsn = 'mongodb://'.rawurlencode(get_config('IRIS_USERNAME')) . ':' .
              rawurlencode(get_config('IRIS_PASSWORD')).'@'.rawurlencode(get_config('IRIS_HOST'));
$iris = (new MongoDB\Client($iris_dsn))->iris;

function h($text) {
    return htmlspecialchars($text);
}

function redirect_browser($url) {
    header("Location: $url");
    die();
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
    if (get_config('ERROR_MODE')=='debug') { ?>
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
    if (get_config('ERROR_MODE')=='debug') {
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

function get_config($name) {
    // get the configuration parameter $NAME either by environment variables or local constants
    if (defined($name))
        return constant($name);
    $result = getenv($name);
    if ($result != false) return $result;
    $result = getenv($name . '_FILE');
    if ($result != false) {
        $f = fopen($result, 'r');
        $result = trim(fgets($f));
        fclose ($f);
        return $result;
    }
    trigger_error("Configuration parameter $name has not been specified");
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

function iris_display_paper($paper) {
    $l = $paper['lookupValues'];
    $appeared = $paper['collection']['id']== 23 ? "in {$l['book']}, " : '';
    ?>
    <?= h($l['contributors']) ?><br>
    <a href="https://ricerca.unich.it/handle/<?= h($paper['handle']) ?>" target="_blank">
      <?= h($l['title']) ?>
    </a><br>
    <?php if ($paper['collection']['id']== 23) { ?>
        in <?= h($l['book']) ?><br>
    <?php } ?>
    <?= h($l['year']) ?>
    <?php
}

function iris_get_docenti($search, $start=0, $limit=20) {
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
    $results = array_slice($results, $start, $limit);
    return $results;
}

function pe_id_from_username($username) {
    global $pe;
    $query = $pe -> prepare('SELECT id FROM researchers WHERE username = ?');
    $query -> execute([$username]);
    $result = $query -> fetch();
    return $result ? $result['id'] : null;
}

function pe_get_researcher($id) {
    global $pe;
    $query = $pe -> prepare('SELECT * FROM researchers WHERE id = ?');
    $result = $query -> execute([$id]);
    $data = $query -> fetch(PDO::FETCH_ASSOC);

    $query = $pe -> prepare(<<<SQL
        SELECT *
        FROM researcher_keywords rk JOIN keywords k ON rk.id_keyword = k.id
        WHERE rk.id_researcher = ?
        ORDER BY pos;
    SQL);
    $result = $query -> execute([$data['id']]);
    $keywords = $query -> fetchAll(PDO::FETCH_ASSOC);

    $keywords_en = array_map(
        function ($keyword) { return $keyword['keyword']; },
        array_filter($keywords, function ($keyword) { return $keyword['lang'] == 'en'; })
    );
    $keywords_it = array_map(
        function ($keyword) { return $keyword['keyword']; },
        array_filter($keywords, function ($keyword) { return $keyword['lang'] == 'it'; })
    );

    $data['keywords_en'] = $keywords_en;
    $data['keywords_it'] = $keywords_it;

    return $data;
}

function pe_get_researchers_from_keyword($keyword_id) {
    global $pe;
    $query = $pe -> prepare('SELECT * FROM researchers r JOIN researcher_keywords rk ON r.id = rk.id_researcher WHERE rk.id_keyword = ?');
    $result = $query -> execute([$keyword_id]);
    return $query -> fetchAll(PDO::FETCH_ASSOC);
}

function pe_create_researcher($username) {
    global $pe;
    $query = $pe -> prepare('INSERT INTO researchers (username) VALUES (?)');
    $result = $query -> execute([$username]);
    return $result;
}

function pe_get_keyword_id($keyword, $lang) {
    global $pe;
    $query = $pe -> prepare('SELECT id FROM keywords WHERE keyword = ? AND lang = ?');
    $query -> execute([$keyword, $lang]);
    $value = $query -> fetch();
    return $value ? $value['id'] : null;
}

function pe_get_keywords($lang, $prefix = '') {
    global $pe;
    $query = $pe -> prepare('SELECT id, keyword FROM keywords WHERE lang = ? AND keyword LIKE ? ORDER BY keyword ASC');
    $escapedprefix = addcslashes($prefix, '%_');
    $result = $query -> execute([$lang, $escapedprefix . '%']);
    return $query -> fetchAll();
}

function pe_add_keyword($keyword, $lang) {
    global $pe;
    $query = $pe -> prepare('INSERT INTO keywords (keyword, lang) VALUES (?, ?)');
    $query -> execute([$keyword, $lang]);
    return $pe -> lastInsertId();
}

function pe_associate_researcher_keyword($researcher_id, $keyword_id, $pos) {
    global $pe;
    $query = $pe -> prepare('INSERT INTO researcher_keywords VALUES (?, ?, ?)');
    $result = $query -> execute([$researcher_id, $keyword_id, $pos]);
    return $result;
}


function pe_delete_associated_keywords($researcher_id) {
    global $pe;
    $query = $pe -> prepare('DELETE FROM researcher_keywords WHERE id_researcher = ?');
    $result = $query -> execute([$researcher_id]);
    return $result;
}

function pe_edit_researcher($id, $keywords_en, $keywords_it, $data) {
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
        WHERE id = ?
    SQL);

    $result = $query -> execute([
        join(' ',$keywords_en), $data['interests_en'], $data['demerging_en'], $data['awards_en'], $data['curriculum_en'],
        join(' ',$keywords_it), $data['interests_it'], $data['demerging_it'], $data['awards_it'], $data['curriculum_it'],
        $id
    ]);

    pe_delete_associated_keywords($id);

    foreach ($keywords_en as $pos => $keyword) {
        $keyword_id = pe_get_keyword_id($keyword, 'en') ?? pe_add_keyword($keyword, 'en');
        pe_associate_researcher_keyword($id, $keyword_id, $pos);
    }

    foreach ($keywords_it as $pos => $keyword) {
        $keyword_id = pe_get_keyword_id($keyword, 'it') ?? pe_add_keyword($keyword, 'it');
        pe_associate_researcher_keyword($id, $keyword_id, $pos);
    }

    return true;
}

function list_to_tagify($list) {
    $tags = [];
    foreach ($list as $item) {
        $obj = [ 'value' => $item ];
        $tags[] = $obj;
    }
    return json_encode($tags);
}
