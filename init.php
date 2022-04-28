<?php
session_start();

define('CACHE_DIR', basename(__DIR__ . DIRECTORY_SEPARATOR . 'cache'));
define('UPLOAD_PATH', basename(__DIR__ . DIRECTORY_SEPARATOR . 'uploads'));

$db = require_once 'db.php';
$db = array_values($db);

$link = mysqli_connect(...$db);
mysqli_set_charset($link, "utf8");

$projects = [];
$tasks = [];
$content = '';
define('SECONDS_IN_DAY', 86400);
define('MAXIMUM_LENGTH',255);
