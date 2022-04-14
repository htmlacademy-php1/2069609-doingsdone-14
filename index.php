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
    } else {
        $error = mysqli_error($link);
        $content = include_template('error.php', ['error' => $error]);
    }

    // узнаю, какая категория выбрана в левом меню
    $current_category_id = filter_input(INPUT_GET, 'cat_id');

    // если не выбран проект, показываю все задачи всех проектов(категорий)
    $all_projects = ' t.user_id = ?';
    // если выбран проект, показываю задачи только для этого проекта
    $current_project = ' t.user_id = ? AND p.id = ?';

    // по умолчанию, видим все задачи всех поектов
    $show_tasks = $all_projects;

    // если выбрана категория, $show_tasks меняется на показ задач только для выбранного преокта
    if ($current_category_id) {
        $show_tasks = $current_project;
    }
    // $show_tasks подставляем в запрос
    $sql = 'SELECT t.name as task_name, p.name as project_name, t.due_date as task_date, t.status as task_status'
         . ' FROM tasks t JOIN projects p ON t.project_id = p.id WHERE' . $show_tasks;
    $stmt = mysqli_prepare($link, $sql);
    // приходится опять проверять на то, выбран ли проект
    if ($current_category_id) {
        mysqli_stmt_bind_param($stmt, 'ii', $current_user_id, $current_category_id);
    } else {
        mysqli_stmt_bind_param($stmt, 'i', $current_user_id);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    //приходится независимым образом находить все задачи, чтобы справа от названия проекта считалась их количество.
    // Тк выше, когда выбран один проект, количества остальных поектов обнуляется
    $sql = 'SELECT t.name as task_name, p.name as project_name'
        . ' FROM tasks t JOIN projects p ON t.project_id = p.id WHERE' . $all_projects;
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $current_user_id);
    mysqli_stmt_execute($stmt);
    $result_all_tasks = mysqli_stmt_get_result($stmt);


    if ($result and $result_all_tasks) {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $all_tasks = mysqli_fetch_all($result_all_tasks, MYSQLI_ASSOC);
        $content = include_template('main.php', [
            // $all_tasks теперь подставляется в функцию подсчета количества в main.php
                'all_tasks' => $all_tasks,
                'tasks' => $tasks,
                'projects' => $projects,
                'show_complete_tasks' => $show_complete_tasks,
                'current_category_id' => $current_category_id]);
    } else {
        $error = mysqli_error($link);
        $content = include_template('error.php', ['error' => $error]);
    }
}






$layout_content = include_template('layout.php',['content' => $content, 'title'=>'Дела в порядке']);
print($layout_content);

?>
