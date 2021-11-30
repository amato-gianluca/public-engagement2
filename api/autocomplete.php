<?php
require_once '../library.php';

header('Content-Type: application/json; charset=utf-8');

echo json_encode(pe_get_keywords($_GET['lang']));
?>
