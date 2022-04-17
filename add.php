<?php

function count_of_tasks($array_of_task, $id_of_category) {
    $count_of_task = 0;
    foreach ($array_of_task as $task){
        if ($task['project_id']===$id_of_category){
            $count_of_task++;
        }
    }
    return $count_of_task;
}

require_once ('helpers.php');
require_once 'init.php';
$current_user_id = 3;

if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
}
else {
    $sql = 'SELECT * FROM projects WHERE user_id = ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$current_user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $projects_ids = [];

    if ($result) {
        $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $projects_ids = array_column($projects, 'id');
    }
    else {
        $error = mysqli_connect_error();
        $content = include_template('error.php', ['error' => $error]);
    }

    $sql = 'SELECT project_id FROM tasks WHERE user_id = ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$current_user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    else {
        $error = mysqli_connect_error();
        $content = include_template('error.php', ['error' => $error]);
    }

    $content = include_template('form_task.php', ['tasks' => $tasks, 'projects' => $projects]);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $required = ['name', 'project_id'];
        $errors = [];

        $rules = [
            'project_id' => function ($value) use ($projects_ids) {
                return validateCategory($value, $projects_ids);
            },
            'due_date' => function($value) {
            return is_date_valid($value);
        }
        ];

        $task = filter_input_array(INPUT_POST, ['name' => FILTER_DEFAULT, 'project_id' => FILTER_DEFAULT, 'due_date' => FILTER_DEFAULT], true);

        foreach ($task as $key => $value) {
            if (isset($rules[$key])) {
                $rule = $rules[$key];
                $errors[$key] = $rule($value);
            }
            if (in_array($key, $required) && empty($value)) {
                $errors[$key] = "Поле $key надо заполнить";
            }
        }
        $errors = array_filter($errors);

        if (!empty($_FILES['file_txt']['name'])) {
            $tmp_name = $_FILES['file_txt']['tmp_name'];
            $link_to_file = $_FILES['file_txt']['name'];
            $filename = uniqid() . ' .txt';
            move_uploaded_file($tmp_name, 'uploads/' . $filename);
            $task['link_to_file'] = $filename;
        }

        if (count($errors)) {
            $content = include_template('form_task.php', ['tasks' => $tasks, 'errors' => $errors, 'projects' => $projects]);
        }
        else {
            // !!!!!ВОТ ЗДЕСЬ НЕ ПОНИМАЮ В ЧЕМ ОШИБКА: Data truncated for column 'project_id' at row 1
            $sql = 'INSERT INTO tasks (date_of_create, name, link_to_file, due_date, user_id, project_id) VALUES (NOW(), ?, ?, ?, 3, ?)';
            $stmt = db_get_prepare_stmt($link, $sql, [$task]);
            $res = mysqli_stmt_execute($stmt);
            if ($res) {
                header("Location: /index.php");
            }
        }
    }
    else {
        $content = include_template('form_task.php', ['projects' => $projects, 'tasks' => $tasks]);
    }
}

$layout_content = include_template('layout.php',[
    'content' => $content,
    // ЗАЧЕМ??
    //'projects' => [],
    //'tasks' => [],
    'title'=> 'Дела в порядке']);
print($layout_content);
?>
