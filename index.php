<?php

// Создаем функцию подсчета проектов number_of_pr, в зависимости от категории задачи
function count_of_projects($array_of_task, $name_of_category) {
    $count_of_project = 0;
    foreach ($array_of_task as $task){
        if ($task['project_name']===$name_of_category){
            $count_of_project++;
        }
    }
    return $count_of_project;
}
define ('SECONDS_IN_DAY' , 86400);

function is_task_important($task_date): bool
{
        $current_time = time();
        return (strtotime($task_date)-$current_time < SECONDS_IN_DAY);
}

require_once ('helpers.php');
$show_complete_tasks = rand(0, 1);
$current_user_id = 3;
require_once 'init.php';

if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
}
else {
    $sql = 'SELECT * FROM projects WHERE user_id = ?';
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $current_user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        else {
            $error = mysqli_error($link);
            $content = include_template('error.php', ['error' => $error]);
        }

    $sql = 'SELECT t.name as task_name, p.name as project_name, t.due_date as task_date, t.status as task_status '
    . 'FROM tasks t '
    . 'JOIN projects p ON t.project_id = p.id WHERE t.user_id = ?';
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $current_user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $content = include_template('main.php', ['tasks' => $tasks, 'projects' => $projects, 'show_complete_tasks' => $show_complete_tasks]);
    }
    else {
        $error = mysqli_error($link);
        $content = include_template('error.php', ['error' => $error]);
    }
}

$layout_content = include_template('layout.php',['content' => $content, 'title'=>'Дела в порядке']);
print($layout_content);

?>
