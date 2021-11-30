<?php
require_once '../library.php';

header('Content-Type: application/json; charset=utf-8');

$pe_users = pe_get_researchers_from_keyword($_GET['search']);

$esse3_users = array_map(
    function ($pe_user) {
        $esse3_user = esse3_get_author_by_matricola($pe_user['username']);
        return $esse3_user;
    },
    $pe_users
);
echo json_encode($esse3_users);
?>
