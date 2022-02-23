<?php
require_once '../library.php';

header('Content-Type: application/json; charset=utf-8');

$pe_users = pe_researcher_from_keywordid($_GET['search']);

$esse3_users = array_map(
    function ($pe_user) {
        $esse3_username = esse3_displayname_from_matricola($pe_user['username']);
        return [ "matricola" => $pe_user['username'], "displayname" => $esse3_username ];
    },
    $pe_users
);
echo json_encode($esse3_users);
?>
