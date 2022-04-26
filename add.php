<?php

require_once ('helpers.php');
require_once 'init.php';

if(isset($_SESSION['user'])) {
    $current_user_id = $_SESSION['user']['id'];
    $is_auth = 1;
    $current_user_name = $_SESSION['user']['name'];
}
else {
    $is_auth = 0;
    $current_user_name = '';
    // спросить, почему здесь в адресе нужен слеш
    header("Location: /auth.php");
    exit();
}

if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
}
else {
    require ('list_of_projects.php');
    $projects_ids = [];

    if ($list_of_projects) {
        $projects = mysqli_fetch_all($list_of_projects, MYSQLI_ASSOC);
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

    $content = include_template('form_task.php', [
        'tasks' => $tasks,
        'projects' => $projects
    ]);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['name', 'project_id'];
        $errors = [];
        $rules = [
            'project_id' => function ($value) use ($projects_ids) {
                return validate_project($value, $projects_ids);
            },
            'due_date' => function($value) {
                if ($value) {
                    // потом попробовать поменять местами, посмотреть что будет
                    if (!is_date_greater_than_today($value)) {
                        return "Дата должна быть больше или равна текущей";
                    }
                    if (!is_date_valid($value)) {
                        return "Введите дату в формате ГГГГ-ММ-ДД";
                    }
                    return null;
                }
                //добавила недавно
                return null;
            }
        ];

        $task = filter_input_array(INPUT_POST, [
            'name' => FILTER_DEFAULT,
            'project_id' => FILTER_DEFAULT,
            'due_date' => FILTER_DEFAULT],
            true
        );

        if (empty($task['due_date'])) {
            $task['due_date'] = null;
        }

        foreach ($task as $key => $value) {
            if (in_array($key, $required) && empty($value)) {
                $errors[$key] = "Поле $key надо заполнить";
            }
            else {
                if (isset($rules[$key])) {
                    $rule = $rules[$key];
                    $errors[$key] = $rule($value);
                }
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
            $content = include_template('form_task.php', [
                'tasks' => $tasks,
                'errors' => $errors,
                'projects' => $projects
            ]);
        }
        else {
            $sql = 'INSERT INTO tasks (date_of_create, name, link_to_file, due_date, user_id, project_id)' .
                ' VALUES (NOW(), ?, ?, ?, ?, ?)';
            $stmt = db_get_prepare_stmt($link, $sql, [$task['name'], $task['link_to_file'], $task['due_date'], $current_user_id, $task['project_id']]);
            $res = mysqli_stmt_execute($stmt);
            if ($res) {
                header("Location: /index.php");
            }
        }
    }
    else {
        $content = include_template('form_task.php', [
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
