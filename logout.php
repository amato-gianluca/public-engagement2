<?php
require_once 'config.php';
require_once 'library.php';

unset($_SESSION['username']);
header('Location: .');
die();
