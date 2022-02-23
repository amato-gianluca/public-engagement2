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

// DB connections initialization

try {
    $esse3 = new PDO(get_config('ESSE3_DSN'), get_config('ESSE3_USERNAME'), get_config('ESSE3_PASSWORD'),
        [
            PDO::MYSQL_ATTR_SSL_CA =>  __DIR__ . '/ca_esse3.pem',
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
} catch (PDOException $e) {
    trigger_error('MySQL Connection to ESSE3 failed: ' . $e->getMessage(), E_USER_ERROR);
}

try {
    $pe = new PDO(get_config('PE_DSN'), get_config('PE_USERNAME'), get_config('PE_PASSWORD'),
        [ PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC ]);
} catch (PDOException $e) {
    trigger_error('MySQL Connection to PE failed: ' . $e->getMessage(), E_USER_ERROR);
}

$iris_dsn = 'mongodb://'.rawurlencode(get_config('IRIS_USERNAME')) . ':' .
              rawurlencode(get_config('IRIS_PASSWORD')).'@'.rawurlencode(get_config('IRIS_HOST'));
$iris = (new MongoDB\Client($iris_dsn))->iris;

// Utility functions

function h($text) {
    return htmlspecialchars($text);
}

function redirect_browser($url) {
    header("Location: $url");
    die();
}

function list_to_tagify($list) {
    $tags = [];
    foreach ($list as $item) {
        $obj = [ 'value' => $item ];
        $tags[] = $obj;
    }
    return json_encode($tags);
}

// Error handling

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
        <pre><?= h($ex) ?></pre>
    <?php } else { ?>
        Si è verificato un errore. Si prega di contattareontatta lo sviluppatore del sofware,
        <a href="<?= h(get_config('DEVELOPER_ADDRESS')) ?>"><?= h(get_config('DEVELOPER_NAME')) ?></a>.
    <?php }
    die();
}

function error_handler($errno, $errstr, $errfile, $errline) {
    ob_clean();
    if (get_config('ERROR_MODE')=='debug') { ?>
        <b><?= map_error_code($errno) ?>:</b> <?= h($errstr) ?> in <?= h($errfile) ?> on line <?= h($errline) ?>
        <br>
        <pre><?php debug_print_backtrace(); ?></pre>
    <?php } else { ?>
        Si è verificato un errore. Si prega di contattare lo sviluppatore del sofware,
        <a href="<?= h(get_config('DEVELOPER_ADDRESS')) ?>"><?= h(get_config('DEVELOPER_NAME')) ?></a>.
    <?php }
    die();
}

/**
 * Returns a configuration parameter.
 *
 * Given a configuration parameter in $name, it returns, in order of priority:
 * - the content of a constant $name: new constants may be provided trough a config.php file;
 * - the content of an environment variable $name;
 * - the content of a file whose name is in the environment variable $name_FILE.
 *
 * @param name the name of the configuration parameter
 * @todo memoize the results, so that file access is not repeated at each call
 */

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
    trigger_error('Configuration parameter ' . h($name) . ' has not been specified');
}

/**
 * Returns the list of teachers in a given department, ordered by cognome and nome.
 *
 * @param dip_id identifier of the department
 * @todo use the correct table in ESSE3 for this query
 */
function esse3_docenti_from_department($department = '031313') {
    global $esse3;

    $query = $esse3 -> prepare('SELECT * FROM V_IE_RU_PERS_ATTIVO WHERE CD_AFF_ORG = ? ORDER BY COGNOME, NOME');
    $result = $query -> execute([$department]);
    return $query -> fetchAll();
}

/**
 * Returns true if the matricola number is associated to a tacher of the university, false otherwise.
 * If a person is recorded in CSA but she is not a teacher, it should return false.
 *
 * @param matricola matricola number of the teacher
 * @todo use the correct table in ESSE3 for this query
 */
function esse3_check_docente_from_matricola($matricola) {
    global $esse3;

    $query = $esse3 -> prepare('SELECT 1 FROM DOCENTI WHERE MATRICOLA = ?');
    $query -> execute([$matricola]);
    return boolval($query -> fetch());
}

/**
 * Returns a teacher given its matricola number.  A field `displayname` is
 * added in the form 'nome cognome'.
 *
 * @param matricola matricola number of the teacher
 * @todo use the correct table in ESSE3 for this query
 */
function esse3_docente_from_matricola($matricola) {
    global $esse3;

    $query = $esse3 -> prepare('SELECT CONCAT(NOME, " ", COGNOME) AS displayname FROM DOCENTI WHERE MATRICOLA = ?');
    $query -> execute([$matricola]);
    $result = $query->fetch();
}

/**
 * Returns a list of teachers given their matricola numbers.
 *
 * @param matricole list of matricola numbers for the teachers
 * @todo use the correct table in ESSE3 for this query
 */
function esse3_docenti_from_matricole($matricole) {
    // It can be used to speed-up repeated executions of esse3_docente_from_matricola, useful
    // when the RTT between this web app and the ESSE3 mirror is high.
    global $esse3;

    $inQuery = implode(',', array_fill(0, count($matricole), '?'));
    $query = $esse3 -> prepare('SELECT CONCAT(NOME, " ", COGNOME) AS displayname FROM DOCENTI WHERE MATRICOLA IN ('.$inQuery.')');
    foreach ($matricole as $k => $matricola) {
        $query->bindValue($k+1, $matricola);
    }
    $result = $query -> execute();
    return $query->fetchAll();
}

/**
 * Returns the full name of a teacher given its matricola number.
 *
 * @param matricola matricola number
 * @todo use the correct table in ESSE3 for this query
 */
function esse3_displayname_from_matricola($matricola) {
    global $esse3;

    $query = $esse3 -> prepare('SELECT CONCAT(NOME, " ", COGNOME) AS displayname FROM DOCENTI WHERE MATRICOLA = ?');
    $query -> execute([$matricola]);
    $result = $query -> fetch();
    return $result['displayname'];
}

/**
 * Returns the link to the CV of a person given its matricola number.
 *
 * @param matricola matricola number
 */
function esse3_cv_from_matricola($matricola) {
    global $esse3;

    $query = $esse3 -> prepare('SELECT * FROM CV_PERSONE WHERE MATRICOLA = ?');
    $result = $query -> execute([$matricola]);
    return $query->fetch();
}

/**
 * Returns the role of a person given its matricola number.
 *
 * @param matricola matricola number
 * @todo use the correct table in ESSE3 for this query
 */
function esse3_role_from_matricola($matricola) {
    global $esse3;

    $query = $esse3 -> prepare('SELECT * FROM V_IE_RU_PERS_ATTIVO WHERE MATRICOLA = ?');
    $result = $query -> execute([$matricola]);
    return $query->fetch();
}

/**
 * Returns the matricola corresponding to a crisId (identifier for the author table in IRIS). If
 * the given crisId does not exists, or there is no corresponding matricola number, it returns null.
 */
function iris_matricola_from_crisid($crisId) {
    global $iris;

    // sebbene anche il campo userSet.username contenga il numero di matricola, non è presente sempre
    $author = $iris->authors->findOne(['crisId' => $crisId], ['projection' => ['gaSourceIdentifiers' => 1]]);
    foreach ($author['gaSourceIdentifiers'] as &$source) {
        if ($source['sourceTable'] == "UGOV.CSA.PERSON")
            return $source['sourceId'];
    }
    return null;
}

/**
 * Returns the matricola crisId corresponding to a given matricola number. We assume that there there a single
 * author in iris for a given matricola number. If the given matricola numbers is not found in the author
 * table of IRIS, it returns null.
 */
function iris_crisid_from_matricola($matricola) {
    global $iris;
    $author = $iris->authors->findOne(['gaSourceIdentifiers.sourceId' => $matricola], ['projection' => ['crisId' => 1]]);
    return $author ? $author['crisId'] : null;
}

/**
 * Returns the items which belongs to a given crisId (author id), in descending order by year and id.
 *
 * @param crisId crisId of (one of the) author of the returned items
 * @param year only returns items published after the specified year (default 2015)
 */
function iris_items_from_crisid($crisId, $year = 2015) {
    global $iris;
    $items = $iris->items->find([
        'internalAuthors.authority' => $crisId,
        'lookupValues.year' => [ '$gte' => strval($year) ]
    ],[
        'sort' => [ 'lookupValues.year' => -1, '_id' => -1 ]
    ]);
    return $items;
}

/**
 * Returns the items belonging to a crisId (author id), which satisfy a given search query,
 * in descending order by year and id. For each item, it also returns the score of the search.
 *
 * @param crisId crisId of (one of the) author of the returned items
 * @param search the search query
 * @param year only returns items published after the specified year (default 2015)
 */
function iris_items_from_crisid_with_score($crisId, $search, $year = 2015) {
    global $iris;
    $items = $iris->items->find([
        '$text' => [ '$search' => $search, '$language' => 'en' ],
        'internalAuthors.authority' => $crisId,
        'lookupValues.year' => [ '$gte' => strval($year) ]
    ],[
        'projection' =>  [
            'score' => [ '$meta' => 'textScore' ],
        ],
        'sort' => [ 'lookupValues.year' => -1, '_id' => -1 ]
    ]);
    return $items;
}

/**
 * Returns the score of a specific item part for a given search query.
 *
 * @param id the MongoDB id of the item
 * @param search the search query
 */
function iris_score_from_itemid($id, $search) {
    global $iris;
    $item = $iris->items->findOne([
        '_id' => $id,
        '$text' => [ '$search' => $search, '$language' => 'en' ],
    ], [
        'projection' =>  [
            'score' => [ '$meta' => 'textScore' ],
            '_id' => 1,
        ]
    ]);
    return $item ? $item['score'] : 0;
}

/**
 * Format an displays as item as returned from the items table in IRIS.
 */
function iris_display_item($item) {
    $l = $item['lookupValues'];
    $appeared = $item['collection']['id']== 23 ? "in {$l['book']}, " : '';
    $authors = $item['metadata']['dc/authority/people'];
    $numauthor = 0;
    foreach ($item['metadata']['dc/authority/people'] as $author)  {
        if ($numauthor > 5) {
            echo " et al.";
            break;
        };
    ?>
        <span class="border"><?= $author['value']; ?></span>
    <?php
        $numauthor += 1;
    }
    ?>
    <br>
    <?php $handle = $item['handle'] ?? null ?>
    <?php if ($handle) { ?>
    <a href="https://ricerca.unich.it/handle/<?= h($handle) ?>" target="_blank">
    <?php } ?>
      <?= h($l['title']) ?>
    <?php if ($handle) { ?>
    </a>
    <?php } ?>
    <br>
    <?php if ($item['collection']['id']== 23) { ?>
        in <?= h($l['book']) ?><br>
    <?php } ?>
    <?= h($l['year']) ?>
    <?php
    $abstract = $item['metadata']['dc/description/abstract'][0]['value'] ?? '';
    if ($abstract) {
    ?>
    <div class="mt-2">
        <div class="d-flex w-100 justify-content-between">
            <strong>Abstract</strong>&nbsp;
            <span class="me-auto collapsed text-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#abstract-<?= h($item['itemId']) ?>" aria-expanded="false" aria-controls="#abstract-<?= h($item['itemId']) ?>">
                <i class="fas fa-arrow-down text-expanded"></i>
                <i class="fas fa-arrow-right text-collapsed"></i>
            </span>
            <?php if ($item['score'] ?? null && get_config('ERROR_MODE')=='debug') { ?>
                <span class="ms-auto">Punteggio: <?= h($item['score'] ?? 0) ?></span>
            <?php }  ?>
        </div>
        <div id="abstract-<?= h($item['itemId']) ?>" class="collapse">
            <?= h($abstract) ?>
        </div>
    </div>
    <?php } ?>
    <?php
}

/**
 * Returns the authors of items which satisfy a the query $search. The score of each item satisfying the
 * query is computed, and each person is assiged a score which is the sum of the scores of its papers.
 * Persons which do not have all the keywords specified in $keywords are removed from the list, and the same happens
 * to authors which do not seem to be teacher of the university. Finally, the list is put in descending order
 * according to the accumulated score, and the positions between $start and $start+$limit are returned.
 */
function iris_search($search, $keywords, $start=0, $limit=20) {
    global $iris;

    // Return items (items) relevant to the search
    $items = $iris->items->find([
        '$text' => [ '$search' => $search, '$language' => 'en' ],
        'lookupValues.year' => [ '$gte' => strval(2015) ],
    ], [
        'projection' =>  [
            'score' => [ '$meta' => 'textScore' ],
            'internalAuthors' => 1,
        ]
    ]);

    // Group items according to internal authors. For each author, computes the sum of all the scores of its items.
    $results = [ ];
    foreach  ($items as $item) {
        $authors = $item['internalAuthors'];
        foreach ($authors as &$author) {
            $crisId = $author['authority'];
            if (! array_key_exists($crisId, $results)) {
                $results[$crisId] = [ /* 'name' => $author['author'],  */ 'crisId' => $crisId, 'score' => 0.0 ];
            }
            $results[$crisId]['score'] += $item['score'];
        }
    }

    // Sorts authors according to total score
    usort($results, function ($a, $b) { return ($a['score'] == $b['score']) ? 0 : (($a['score'] < $b['score']) ? 1 : -1); });

    // Copy in $real_results the data to be returned to the user. We cannot simply return the results from $start to
    // $start + $limit since there are some people listed as internalAuthors in  item (see rp46750) simply because they
    // appear in esse3, but are not really personnel of the university. Therefore, we exclue all the authors which are not
    // presente in the ESSE3 table DOCENTI. This is probably not 100% accurate. Moreover, we update the author data
    // with matricola and name taken by ESSE3 (we could use the name returnes by IRIS, but they do not correspond an we prefer to
    // stick with the result given by ESSE3).

    $keywords_id = pe_keywordsid_from_keywords($keywords, 'en');
    $real_results = [];
    $i = 0;
    foreach ($results as &$author) {
        $crisId = $author['crisId'];
        $matricola = iris_matricola_from_crisid($crisId);
        if (! $matricola) continue;
        if (! pe_check_keywords($matricola, $keywords_id)) continue;
        $name = esse3_displayname_from_matricola($matricola);
        if (! $name) continue;

        $i += 1;
        if ($i <= $start) continue;

        $author['name'] = $name;
        $author['matricola'] = $matricola;
        $real_results[] = $author;
        if ($i == $start + $limit) break;
    }

    // Only returns the required results, according to $start and $limit.
    // This post-filtering is not very efficient. It might be merged with the previous
    // foreach, so that additional informations is sought only for authors which are actually returned.
    return $real_results;
}

/**
 * Returns the id of a researcher in the PE database given its Shibboleth username, null if
 * such an researcher does not exists.
 * @todo in many places we assume the Shibboleth usernames are matricola numbers.
 */
function pe_id_from_username($username) {
    global $pe;
    $query = $pe -> prepare('SELECT id FROM researchers WHERE username = ?');
    $query -> execute([$username]);
    $result = $query -> fetch();
    return $result ? $result['id'] : null;
}

/**
 * Returns all the data of a researcher given its id. Keywords are returned as a list
 * of english and italian keywords, without reference to their internal id numbers.
 */
function pe_researcher_from_id($id) {
    global $pe;
    $query = $pe -> prepare('SELECT * FROM researchers WHERE id = ?');
    $result = $query -> execute([$id]);
    $data = $query -> fetch();

    $query = $pe -> prepare(<<<SQL
        SELECT *
        FROM researcher_keywords rk JOIN keywords k ON rk.id_keyword = k.id
        WHERE rk.id_researcher = ?
        ORDER BY pos;
    SQL);
    $result = $query -> execute([$data['id']]);
    $keywords = $query -> fetchAll();

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

/**
 * Returns a list of all researchers who have a given keyword id.
 */
function pe_researcher_from_keywordid($keyword_id) {
    global $pe;
    $query = $pe -> prepare('SELECT * FROM researchers r JOIN researcher_keywords rk ON r.id = rk.id_researcher WHERE rk.id_keyword = ?');
    $result = $query -> execute([$keyword_id]);
    return $query -> fetchAll();
}

/**
 * Returns a list of all researchers who have a given list of keywords (in any language).
 */
function pe_researchers_from_all_keywords($keywords) {
    global $pe;
    $query = $pe -> prepare(<<<SQL
        SELECT r.*, JSON_ARRAYAGG(k.keyword) kwords
        FROM researchers r
        JOIN researcher_keywords rk ON r.id = rk.id_researcher
        JOIN keywords k ON k.id = rk.id_keyword
        HAVING JSON_CONTAINS(kwords, '[ "artificial intelligence" ]')
    SQL);
    $result = $query -> execute([]);
    return $query -> fetchAll();
}

/**
 * Returns a list of all researchers who have any of the keyword in $keywords,
 * in descending order according to he number of keywords.
 */
function pe_researchers_from_any_keyword($keywords) {
    global $pe;
    $keywords_list = implode(',', $keywords);
    $query = $pe -> prepare(<<<SQL
        SELECT r.username, COUNT(DISTINCT k.keyword) AS score
        FROM researchers r
        JOIN researcher_keywords rk ON r.id = rk.id_researcher
        JOIN keywords k ON k.id = rk.id_keyword
        WHERE FIND_IN_SET(k.keyword, ?)
        GROUP BY r.username
        ORDER BY score DESC
    SQL);
    $result = $query -> execute([$keywords_list]);
    return $query -> fetchAll();
}

/**
 * Creates a blank researcher with a given username. Returns the result of the operation
 * Returns true on success or false on failure. k.keyword MEMBER OF ('[ "artificial intelligence" ]')
 */
function pe_researcher_create($username) {
    global $pe;
    $query = $pe -> prepare('INSERT INTO researchers (username) VALUES (?)');
    $result = $query -> execute([$username]);
    return $result;
}

/**
 * Returns the keyword id corresponding to a given keyword (a pair of the actual keyword and the language).
 * Returns null if there is not such a keyword.
 */
function pe_keywordid_from_keyword($keyword, $lang) {
    global $pe;
    $query = $pe -> prepare('SELECT id FROM keywords WHERE keyword = ? AND lang = ?');
    $query -> execute([$keyword, $lang]);
    $value = $query -> fetch();
    return $value ? $value['id'] : null;
}

/**
 * Returns a list of keyword id corresponding to a list of keywords.
 * All the keywords are in the same language.
 */
function pe_keywordsid_from_keywords($keywords, $lang) {
    $results = [];
    foreach ($keywords as $keyword) {
        $id = pe_keywordid_from_keyword($keyword, $lang);
        if ($id) $results[] = intval($id);
    }
    return $results;
}

/**
 * Returns a list of keywords (both id and text) of a given language which begins with a given prefix.
 */
function pe_keywords_from_lang_and_prefix($lang = '', $prefix = '') {
    global $pe;

    $escapedprefix = addcslashes($prefix, '%_');
    if ($lang) {
        $query = $pe -> prepare('SELECT id, keyword FROM keywords WHERE lang = ? AND keyword LIKE ? ORDER BY keyword ASC');
        $result = $query -> execute([$lang, $escapedprefix . '%']);
    } else {
        $query = $pe -> prepare('SELECT id, keyword FROM keywords WHERE keyword LIKE ? ORDER BY keyword ASC');
        $result = $query -> execute([$escapedprefix . '%']);
    }
    return $query -> fetchAll();
}

/**
 * Creates a new keyword (specified by text and language) and returns its id.
 */
function pe_keyword_create($keyword, $lang) {
    global $pe;
    $query = $pe -> prepare('INSERT INTO keywords (keyword, lang) VALUES (?, ?)');
    $query -> execute([$keyword, $lang]);
    return $pe -> lastInsertId();
}

/**
 * Returns true if the given $username has all the keywords in $keywords_id.
 */
function pe_check_keywords($username, $keywords_id) {
    global $pe;
    if (empty($keywords_id)) return true;
    $query = $pe -> prepare('SELECT rk.id_keyword FROM researchers r JOIN researcher_keywords rk ON r.id = rk.id_researcher WHERE r.username = ?');
    $result = $query -> execute([$username]);
    $keywords = [];
    while ($k = $query -> fetch()) {
        $keywords[] = intval($k['id_keyword']);
    }
    return ! array_diff($keywords_id, $keywords);
}

/**
 * Associate a given researcher with the specified keywords.
 *
 * @param researcher_id the id of the researcher
 * @param keyword_id the id of the keyword
 * @param pos position where keyword_id is inserted in the list of keywords
 * @todo better make a different function which associated all keywords in the same time
 */
function pe_researcher_associate_keyword($researcher_id, $keyword_id, $pos) {
    global $pe;
    $query = $pe -> prepare('INSERT INTO researcher_keywords VALUES (?, ?, ?)');
    $result = $query -> execute([$researcher_id, $keyword_id, $pos]);
    return $result;
}

/**
 * Remove all keywords associated to a given researcher.
 * Returns true on success or false on failure.
 */
function pe_researcher_delete_keywords($researcher_id) {
    global $pe;
    $query = $pe -> prepare('DELETE FROM researcher_keywords WHERE id_researcher = ?');
    $result = $query -> execute([$researcher_id]);
    return $result;
}

/**
 * Edit information for a researcher. Keywords are created when they do not exists.
 *
 * @param researcher_id id of the researcher
 * @param keywords_en list of english keywords to be associated with the researcher
 * @param keywords_it list of italian keywords to be associated with the researcher
 * @param data new data for the research, with the expection of the keywords
 */
function pe_researcher_edit($researcher_id, $keywords_en, $keywords_it, $data) {
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
        $researcher_id
    ]);

    pe_researcher_delete_keywords($researcher_id);

    foreach ($keywords_en as $pos => $keyword) {
        $keyword_id = pe_keywordid_from_keyword($keyword, 'en') ?? pe_keyword_create($keyword, 'en');
        pe_researcher_associate_keyword($researcher_id, $keyword_id, $pos);
    }

    foreach ($keywords_it as $pos => $keyword) {
        $keyword_id = pe_keywordid_from_keyword($keyword, 'it') ?? pe_keyword_create($keyword, 'it');
        pe_researcher_associate_keyword($researcher_id, $keyword_id, $pos);
    }

    return true;
}

/**
 * Returns the researchers in the pe database which satisfy a given query. The list of researchers is put
 * in descending order by score, and only elements in positions between $start and $start+$limit are returned.
 */
function pe_search($search, $start=0, $limit=20) {
    global $pe;

    // Query the local DB for the relevant data
    $query = $pe -> prepare(<<<SQL
        SELECT
          username,
          MATCH (keywords_en,interests_en,demerging_en,position_en,awards_en,curriculum_en,keywords_it,interests_it,demerging_it,position_it,awards_it,curriculum_it) AGAINST (? IN NATURAL LANGUAGE MODE) AS score
        FROM researchers
        WHERE MATCH(keywords_en,interests_en,demerging_en,position_en,awards_en,curriculum_en,keywords_it,interests_it,demerging_it,position_it,awards_it,curriculum_it) AGAINST (? IN NATURAL LANGUAGE MODE)
        ORDER BY score DESC
        LIMIT {$start}, {$limit}
    SQL);
    $query -> execute([$search, $search]);

    // Sorts authors according to total score
    $results = $query -> fetchAll();
    usort($results, function ($a, $b) { return ($a['score'] == $b['score']) ? 0 : (($a['score'] < $b['score']) ? 1 : -1); });

    // Add new information for each result, namely crisId and name. Moreove, do not return results
    // which do not appear in the DOCENTI table of ESSE3 (to be consistent with iris_search, see there
    // for the rationale under this behaviour).
    $i = 0;
    $real_results = [];
    foreach ($results as $researcher) {
        $matricola = $researcher['username'];
        // CHECK. Are username and matricola the same thing ?
        $name = esse3_displayname_from_matricola($matricola);
        if (! $name) continue;

        $i += 1;
        if ($i <= $start) continue;

        $crisId = iris_crisid_from_matricola($matricola);
        if (! $crisId) continue;
        // CHECK. Is iris_crisid_from_matricola injective ?
        $real_results[] = [ 'crisId' => $crisId, 'score' => doubleval($researcher['score']), 'name' => $name, 'matricola' => $matricola ];
        if ($i == $start + $limit) break;
    }

    return $real_results;
}

/**
 * Returns the researchers in the pe database which satisfy a given query. The list of researchers is put
 * in descending order by score, and only elements in positions between $start and $start+$limit are returned.
 */
function pe_search_keywords($keywords) {
    $researchers = pe_researchers_from_any_keyword($keywords);
    foreach ($researchers as &$researcher) {
        $name = esse3_displayname_from_matricola($researcher['username']);
        $researcher['name'] = $name;
        $researcher['score'] = doubleval($researcher['score']);
    }
    return $researchers;
}
