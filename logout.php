<?php
require_once 'library.php';

unset($_SESSION['userid']);
header('Location: .');
die();
