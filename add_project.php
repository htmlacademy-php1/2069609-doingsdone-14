<?php
require_once('helpers.php');
require_once('init.php');
require('session_init.php');

if ($is_auth == 1) {
    $current_user_id = $_SESSION['user']['id'];
}
else {
    header("Location: auth.php");
    exit();
}

if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
}
else {
    require ('list_of_projects.php');
    $project_names = [];
    if ($list_of_projects) {
        $projects = mysqli_fetch_all($list_of_projects, MYSQLI_ASSOC);
        $project_names = array_column($projects, 'name');
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

    $content = include_template('form_project.php', [
        'tasks' => $tasks,
        'projects' => $projects
    ]);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $project['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $errors = function ($value) use ($project_names) {
            if (empty($value)) {
                return 'Заполните поле Название';
            } else if (validate_project($value, $project_names)) {
                return 'Проект с таким названием уже существует';
            } else if (!is_validate_length($value, MAXIMUM_LENGTH)) {
                return 'Название должно быть менее 255 символов';
            } else {
                return null;
            }
        };
        $project = $project['name'];
        if ($errors($project) !== null) {
            $error_name = $errors($project);
            $content = include_template('form_project.php', [
                'tasks' => $tasks,
                'error' => $error_name,
                'projects' => $projects
            ]);
        } else {
            $sql = 'INSERT INTO projects (name, user_id) VALUES (?, ?)';
            $stmt = db_get_prepare_stmt($link, $sql, [$project['name'], $current_user_id]);
            $res = mysqli_stmt_execute($stmt);
            if ($res) {
                header("Location: index.php");
                exit();
            }
        }
    }
    else {
        $content = include_template('form_project.php', [
            'projects' => $projects,
            'tasks' => $tasks
        ]);
    }
}

$layout_content = include_template('layout.php',[
    'content' => $content,
    'title'=> 'Дела в порядке',
    'is_auth' => $is_auth,
    'current_user_name' => $current_user_name
]);

print($layout_content);
?>
