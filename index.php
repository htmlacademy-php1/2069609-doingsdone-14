<?php

// Создаем функцию подсчета проектов number_of_pr, в зависимости от категории задачи

define ('SECONDS_IN_DAY' , 86400);

function is_task_important($task_date): bool
{
    if ($task_date) {
        $current_time = time();
        return (strtotime($task_date) - $current_time < SECONDS_IN_DAY);
    }
    return false;
}
require_once ('helpers.php');
$show_complete_tasks = rand(0, 1);
$current_user_id = 3;
$current_project_id = (int) filter_input(INPUT_GET, 'project_id', FILTER_SANITIZE_NUMBER_INT);

require_once 'init.php';

if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
}
else {
    require ('list_of_projects.php');
    if ($result) {
        $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    else {
        $error = mysqli_error($link);
        $content = include_template('error.php', ['error' => $error]);
    }
    $sql = 'SELECT t.name as task_name, p.name as project_name, p.id as project_id,' .
        ' t.due_date as task_date, t.status as task_status, t.link_to_file as path'
        . ' FROM tasks t JOIN projects p ON t.project_id = p.id WHERE t.user_id = ? ORDER BY t.date_of_create DESC';
    $stmt = db_get_prepare_stmt($link, $sql, [$current_user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
        if ($current_project_id === 0) {
            $content = include_template('main.php', [
                'tasks' => $tasks,
                'projects' => $projects,
                'show_complete_tasks' => $show_complete_tasks,
                'current_project_id' => $current_project_id
            ]);
        }
        else {
            if (count_of_tasks($tasks, $current_project_id) === 0) {
                http_response_code(404);
                $content = include_template('error.php', ['error' => 'Задачи не найдены']);
            }
            else {
                $content = include_template('main.php', [
                    'tasks' => $tasks,
                    'projects' => $projects,
                    'show_complete_tasks' => $show_complete_tasks,
                    'current_project_id' => $current_project_id
                ]);
            }
        }
    }
    else {
        $error = mysqli_error($link);
        $content = include_template('error.php', ['error' => $error]);
    }
}
$layout_content = include_template('layout.php',['content' => $content, 'title'=> 'Дела в порядке']);
print($layout_content);

?>
