<?php

$sql = 'SELECT t.name as task_name, p.name as project_name, p.id as project_id, t.due_date as task_date, ' .
    't.status as task_status, t.link_to_file as path, t.id as task_id ' .
       'FROM tasks as t ' .
       'JOIN projects as p '.
       'ON t.project_id = p.id '.
       'WHERE MATCH(t.name) AGAINST(?) AND t.user_id = ? ORDER BY t.date_of_create DESC';
$stmt = db_get_prepare_stmt($link, $sql, [$search, $current_user_id]);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result) {
    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $content = include_template('main.php', [
        'tasks' => $tasks,
        'projects' => $projects,
        'show_complete_tasks' => $show_complete_tasks,
        'current_project_id' => $current_project_id,
        'tasks_section' =>$tasks_section
    ]);
} else {
    $error = mysqli_error($link);
    $content = include_template('error.php', ['error' => $error]);
}
