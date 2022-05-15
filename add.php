<?php

require_once 'helpers.php';
require_once 'init.php';
require_once 'session_init.php';

if ($is_auth === 1) {
    $current_user_id = $_SESSION['user']['id'];
} else {
    header("Location: auth.php");
    exit();
}

if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
} else {
    require_once 'list_of_projects.php';
    $projects_ids = [];

    if ($list_of_projects) {
        $projects = mysqli_fetch_all($list_of_projects, MYSQLI_ASSOC);
        $projects_ids = array_column($projects, 'id');
    } else {
        $error = mysqli_connect_error();
        $content = include_template('error.php', ['error' => $error]);
    }

    require_once 'list_of_tasks.php';

    $content = include_template('form_task.php', [
        'tasks' => $tasks,
        'projects' => $projects
    ]);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['name', 'project_id'];
        $errors = [];
        $rules = [
            'project_id' => function ($value) use ($projects_ids) {
                if (!validate_project($value, $projects_ids)) {
                    return 'Указана несуществующая категория';
                }
                return null;
            },
            'due_date' => function ($value) {
                if ($value) {
                    if (!is_date_greater_than_today($value)) {
                        return "Дата должна быть больше или равна текущей";
                    }
                    if (!is_date_valid($value)) {
                        return "Введите дату в формате ГГГГ-ММ-ДД";
                    }
                    return null;
                }
                return null;
            }
        ];

        $task = filter_input_array(
            INPUT_POST,
            [
                'name' => FILTER_DEFAULT,
                'project_id' => FILTER_DEFAULT,
                'due_date' => FILTER_DEFAULT
            ],
            true
        );

        if (empty($task['due_date'])) {
            $task['due_date'] = null;
        }

        foreach ($task as $key => $value) {
            require 'find_errors.php';
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
        } else {
            $task['link_to_file'] = null;
        }

        if (count($errors)) {
            $content = include_template('form_task.php', [
                'tasks' => $tasks,
                'errors' => $errors,
                'projects' => $projects
            ]);
        } else {
            $sql = 'INSERT INTO tasks (date_of_create, name, link_to_file, due_date, user_id, project_id)' .
                ' VALUES (NOW(), ?, ?, ?, ?, ?)';
            $stmt = db_get_prepare_stmt(
                $link,
                $sql,
                [$task['name'], $task['link_to_file'], $task['due_date'], $current_user_id, $task['project_id']]
            );
            $res = mysqli_stmt_execute($stmt);
            if ($res) {
                header("Location: index.php");
                exit();
            }
        }
    } else {
        $content = include_template('form_task.php', [
            'projects' => $projects,
            'tasks' => $tasks
        ]);
    }
}

$layout_content = include_template('layout.php', [
    'content' => $content,
    'title' => 'Дела в порядке',
    'is_auth' => $is_auth,
    'current_user_name' => $current_user_name
]);

print($layout_content);
?>
