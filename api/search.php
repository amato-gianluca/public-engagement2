<?php
require_once '../library.php';

header('Content-Type: application/json; charset=utf-8');

$results1 = iris_search($_GET['search'] ?? '',  0, $_GET['limit'] ?? 20);
//$results2 = pe_search($_GET['search'] ?? '', 0,  $_GET['limit'] ?? 20);

echo json_encode($results1);
?>
