<?php
require_once '../config.php';
require_once '../library.php';

header('Content-Type: application/json; charset=utf-8');

$results = iris_get_docenti($_GET['search'] ?? '', $_GET['limit'] ?? 20);
echo json_encode($results);
?>
