<?php
require_once '../library.php';

header('Content-Type: application/json; charset=utf-8');

echo json_encode(pe_keywords_from_lang_and_prefix($_GET['lang'] ?? '', $_GET['value'] ?? ''));
?>
