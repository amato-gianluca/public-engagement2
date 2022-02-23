<?php
require_once '../library.php';

header('Content-Type: application/json; charset=utf-8');

$limit = intval($_GET['limit'] ?? '');
if ($limit <= 0) $limit = intval(get_config('DEFAULT_SEARCH_LIMIT'));

$keywords = $_GET['keywords'] ? array_map(
    function($tag) { return $tag->value; },
    json_decode($_GET['keywords'])
) : [];

$search = $_GET['search'] ?? '';

if ($search) {
    $results1 = iris_search($_GET['search'] ?? '', $keywords, 0, $limit);
    $results2 = pe_search($_GET['search'] ?? '',  0, $limit);
    $results = array_merge($results2, $results1);
} else {
    $results = pe_search_keywords($keywords);
}

echo json_encode($results);
?>
