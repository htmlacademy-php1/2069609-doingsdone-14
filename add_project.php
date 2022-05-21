<?php

require_once 'helpers.php';
require_once 'init.php';
require_once 'session_init.php';

$current_project_id = (int)filter_input(INPUT_GET, 'project_id', FILTER_SANITIZE_NUMBER_INT);
$show_complete_tasks = (int)filter_input(INPUT_GET, 'show_completed', FILTER_SANITIZE_NUMBER_INT);
$current_deadline = filter_input(INPUT_GET, 'deadline', FILTER_SANITIZE_SPECIAL_CHARS);

if ($is_auth === 1) {
    $current_user_id = $_SESSION['user']['id'];
} else {
    header("Location: auth.php");
    exit();
}

if ($link) {
    require_once 'list_of_projects.php';
    $project_names = [];
    if ($list_of_projects) {
        $projects = mysqli_fetch_all($list_of_projects, MYSQLI_ASSOC);
        $project_names = array_column($projects, 'name');
    } else {
        $error = mysqli_connect_error();
        $content = include_template('error.php', ['error' => $error]);
    }

    require_once 'list_of_tasks.php';

    $content = include_template('form_project.php', [
        'projects' => $projects,
        'tasks_for_counting' => $tasks,
        'show_complete_tasks' => $show_complete_tasks,
        'current_deadline' => $current_deadline,
        'current_project_id' => $current_project_id
    ]);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $project['name'] = filter_input(INPUT_POST, 'name');
        $errors = function ($value) use ($project_names) {
            if (empty($value)) {
                return 'Заполните поле Название';
            }
            if (validate_project($value, $project_names)) {
                return 'Проект с таким названием уже существует';
            }
            if (!is_validate_length($value, MAXIMUM_LENGTH)) {
                return 'Название должно быть не более 255 символов';
            }
            $value = trim($value);
            if (empty($value)) {
                return 'Название проекта не может состоять из одних пробелов';
            }
            return null;
        };

        $project_name = $project['name'];
        if ($errors($project_name) !== null) {
            $error_name = $errors($project_name);
            $content = include_template('form_project.php', [
                'error' => $error_name,
                'projects' => $projects,
                'show_complete_tasks' => $show_complete_tasks,
                'current_deadline' => $current_deadline,
                'tasks_for_counting' => $tasks,
                'current_project_id' => $current_project_id
            ]);
        } else {
            $sql = 'INSERT INTO projects (name, user_id) VALUES (?, ?)';
            $stmt = db_get_prepare_stmt($link, $sql, [$project['name'], $current_user_id]);
            $res = mysqli_stmt_execute($stmt);
            if ($res) {
                header("Location: index.php");
                exit();
            } else {
                http_response_code(500);
                $content = include_template('error.php', ['error' => 'Ошибка на сервере']);
            }
        }
    } else {
        $content = include_template('form_project.php', [
            'projects' => $projects,
            'show_complete_tasks' => $show_complete_tasks,
            'current_deadline' => $current_deadline,
            'tasks_for_counting' => $tasks,
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
