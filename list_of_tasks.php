<?php

$sql = 'SELECT * FROM tasks WHERE user_id = ?';
$stmt = db_get_prepare_stmt($link, $sql, [$current_user_id]);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
}
