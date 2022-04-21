<?php
$sql = 'SELECT * FROM projects WHERE user_id = ?';
$stmt = db_get_prepare_stmt($link, $sql, [$current_user_id]);
mysqli_stmt_execute($stmt);
$list_of_projects = mysqli_stmt_get_result($stmt);
