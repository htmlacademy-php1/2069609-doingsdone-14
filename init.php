<?php

session_start();

define('CACHE_DIR', basename(__DIR__ . DIRECTORY_SEPARATOR . 'cache'));
define('UPLOAD_PATH', basename(__DIR__ . DIRECTORY_SEPARATOR . 'uploads'));

$db = require_once 'db.php';

$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);

if ($link) {
    mysqli_set_charset($link, "utf8");
    $content = '';
} else {
    http_response_code(500);
    $error = 'Не удалось подключиться к базе данных';
    $content = include_template('error.php', ['error' => $error]);
    $layout_content = include_template('layout.php', [
        'content' => $content,
        'title' => 'Дела в порядке',
        'current_user_name' => '',
        'is_auth' => 0
    ]);
    print($layout_content);
    exit();
}

$projects = [];
$tasks = [];
define('SECONDS_IN_DAY', 86400);
define('MAXIMUM_LENGTH', 255);
