<?php
require_once '../library.php';

header('Content-Type: application/json; charset=utf-8');

$limit = intval($_GET['limit'] ?? '');
if ($limit <= 0) $limit = intval(get_config('DEFAULT_SEARCH_LIMIT'));

$results1 = iris_search($_GET['search'] ?? '',  0, $limit);
$results2 = pe_search($_GET['search'] ?? '',  0, $limit);

echo json_encode(array_merge($results2, $results1));
?>