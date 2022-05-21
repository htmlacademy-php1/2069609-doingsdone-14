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

$current_project_id = (int)filter_input(INPUT_GET, 'project_id', FILTER_SANITIZE_NUMBER_INT);
$show_complete_tasks = (int)filter_input(INPUT_GET, 'show_completed', FILTER_SANITIZE_NUMBER_INT);
$current_deadline = filter_input(INPUT_GET, 'deadline', FILTER_SANITIZE_SPECIAL_CHARS);

if ($link) {
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
        'projects' => $projects,
        'tasks_for_counting' => $tasks,
        'show_complete_tasks' => $show_complete_tasks,
        'current_deadline' => $current_deadline,
        'current_project_id' => $current_project_id
    ]);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['name', 'project_id'];
        $errors = [];
        $error_file = '';
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
            },
            'name' => function ($value) {
                if (!is_validate_length($value, MAXIMUM_LENGTH)) {
                    return 'Название должно быть не более 255 символов';
                }
                $value = trim($value);
                if (empty($value)) {
                    return 'Название не может состоять из одних пробелов';
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
            $file_type = pathinfo($link_to_file, PATHINFO_EXTENSION);
            $filename = uniqid() . ".$file_type";
            $result = move_uploaded_file($tmp_name, 'uploads/' . $filename);
            if ($result) {
                $task['link_to_file'] = $filename;
            } else {
                $errors['file'] = 'Не удалось загрузить файл';
            }
        } else {
            $task['link_to_file'] = null;
        }

        if (count($errors)) {
            $content = include_template('form_task.php', [
                'projects' => $projects,
                'tasks_for_counting' => $tasks,
                'show_complete_tasks' => $show_complete_tasks,
                'current_deadline' => $current_deadline,
                'current_project_id' => $current_project_id,
                'errors' => $errors
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
            'tasks_for_counting' => $tasks,
            'show_complete_tasks' => $show_complete_tasks,
            'current_deadline' => $current_deadline,
            'current_project_id' => $current_project_id
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
