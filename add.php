<?php

require_once ('helpers.php');
require_once 'init.php';
$current_user_id = 3;


if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
}
else {
    require ('list_of_projects.php');
    $projects_ids = [];

    if ($result) {
        $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $projects_ids = array_column($projects, 'id');
    }
    else {
        $error = mysqli_connect_error();
        $content = include_template('error.php', ['error' => $error]);
    }

    $sql = 'SELECT * FROM tasks WHERE user_id = ?';
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['name', 'project_id'];
        $errors = [];

        $rules = [
            'project_id' => function ($value) use ($projects_ids) {
                return validate_category($value, $projects_ids);
            },
            'due_date' => function($value) {
                return is_date_correct($value) && is_date_valid($value);
            }
        ];

        $task = filter_input_array(INPUT_POST, ['name' => FILTER_DEFAULT, 'project_id' => FILTER_DEFAULT, 'due_date' => FILTER_DEFAULT], true);
        if (empty($task['due_date'])) {
            $task['due_date'] = null;
        }

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

        if (!empty($_FILES['file']['name'])) {
            $tmp_name = $_FILES['file']['tmp_name'];
            $link_to_file = $_FILES['file']['name'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($finfo, $tmp_name);
            $filename = uniqid() . ".$file_type";
            move_uploaded_file($tmp_name, 'uploads/' . $filename);
            $task['link_to_file'] = $filename;
        }
        else {
            $task['link_to_file'] = null;
        }

        if (count($errors)) {
            $content = include_template('form_task.php', ['tasks' => $tasks, 'errors' => $errors, 'projects' => $projects]);
        }
        else {
            $sql = 'INSERT INTO tasks (date_of_create, name, link_to_file, due_date, user_id, project_id) VALUES (NOW(), ?, ?, ?, '. $current_user_id .', ?)';
            $stmt = db_get_prepare_stmt($link, $sql, [$task['name'], $task['link_to_file'], $task['due_date'], $task['project_id']]);
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
