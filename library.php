<?php
if (DEBUG) {
    ini_set('error_reporting', true);
    error_reporting(E_ALL | E_STRICT);
}

session_name('uda-competenze');
session_start();

require_once __DIR__ . '/vendor/autoload.php';

try {
    $esse3 = new PDO(ESSE3_DSN, ESSE3_USERNAME, ESSE3_PASSWORD,
        [ PDO::MYSQL_ATTR_SSL_CA =>  __DIR__ . '/ca_esse3.pem' ]);
} catch (PDOException $e) {
    die('MySQL Connection failed: ' . $e->getMessage());
}

$iris_dsn = 'mongodb://'.rawurlencode(IRIS_USERNAME).':'.rawurlencode(IRIS_PASSWORD).'@'.rawurlencode(IRIS_HOST);
$iris = (new MongoDB\Client($iris_dsn))->iris;

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
?>
