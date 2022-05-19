<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'session_init.php';

$tasks_section = [
    [
        'name' => 'Все задачи',
        'deadline' => 'all'
    ],
    [
        'name' => 'Повестка дня',
        'deadline' => 'today'
    ],
    [
        'name' => 'Завтра',
        'deadline' => 'tomorrow'
    ],
    [
        'name' => 'Просроченные',
        'deadline' => 'yesterday'
    ]
];

$user_project_ids = [];
$user_tasks = [];

$_GET['project_id'] = '';

$current_project_id = (int)filter_input(INPUT_GET, 'project_id', FILTER_SANITIZE_NUMBER_INT);
$show_complete_tasks = (int)filter_input(INPUT_GET, 'show_completed', FILTER_SANITIZE_NUMBER_INT);
$current_deadline = filter_input(INPUT_GET, 'deadline', FILTER_SANITIZE_SPECIAL_CHARS);
$check_task_status = filter_input(INPUT_GET, 'check', FILTER_SANITIZE_NUMBER_INT);
$check_task_id = filter_input(INPUT_GET, 'task_id', FILTER_SANITIZE_NUMBER_INT);

$error_no_tasks = '';
$content_error_404 = '';

if (empty($current_deadline)) {
    $current_deadline = 'all';
}

if (array_key_exists('user', $_SESSION)) {
    $current_user_id = $_SESSION['user']['id'];

    // Находим все существующие проекты пользователя
    $projects = [];
    require_once 'list_of_projects.php';

    if ($list_of_projects) {
        $projects = mysqli_fetch_all($list_of_projects, MYSQLI_ASSOC);
        $user_project_ids = array_column($projects, 'id');
    } else {
        $error = mysqli_error($link);
        $content = include_template('error.php', ['error' => $error]);
    }

    $search = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS);

    // Найдем все задачи пользователя
    $sql = 'SELECT t.name as task_name, p.id as project_id, t.id as task_id FROM tasks as t JOIN projects as p ' .
        'ON t.project_id = p.id ' .
        'WHERE t.user_id = ?';

    $stmt = db_get_prepare_stmt($link, $sql, [$current_user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $user_tasks = $tasks;
    } else {
        $error = mysqli_error($link);
        $content = include_template('error.php', ['error' => $error]);
    }

    if (!$search) {
        $what_tasks_section_show = '';

        if ($current_deadline === 'yesterday') {
            $what_tasks_section_show = 'AND t.due_date < CURDATE()';
        }
        if ($current_deadline === 'today') {
            $what_tasks_section_show = 'AND t.due_date = CURDATE()';
        }
        if ($current_deadline === 'tomorrow') {
            $what_tasks_section_show = 'AND t.due_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)';
        }

        $sql = 'SELECT t.name as task_name, p.name as project_name, p.id as project_id, ' .
            't.due_date as task_date, t.status as task_status, t.link_to_file as path , t.id as task_id ' .
            'FROM tasks as t ' .
            'JOIN projects as p ' .
            'ON t.project_id = p.id ' .
            'WHERE t.user_id = ? ' . $what_tasks_section_show . ' ORDER BY t.date_of_create DESC';

        $stmt = db_get_prepare_stmt($link, $sql, [$current_user_id]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $current_project_id_without_filter = filter_input(INPUT_GET, 'project_id');

            if ($current_project_id === 0 and count($tasks) === 0) {
                $error_no_tasks = 'Нет задач';
            }
            if (!$current_project_id_without_filter and count($user_tasks) === 0) {
                $error_no_tasks = 'Нет задач';
            }
            if ($current_project_id_without_filter) {
                if (in_array($current_project_id, $user_project_ids)) {
                    if (count_of_tasks($tasks, $current_project_id) === 0) {
                        $error_no_tasks = 'Нет задач';
                    }
                } else {
                    $error_404 = 'Вы обращаетесь к несуществующему проекту';
                    http_response_code(404);
                    $content_error_404 = include_template('error.php', ['error' => $error_404]);
                }
            }

            $content = include_template('main.php', [
                'tasks' => $tasks,
                'projects' => $projects,
                'show_complete_tasks' => $show_complete_tasks,
                'current_project_id' => $current_project_id,
                'tasks_section' => $tasks_section,
                'current_deadline' => $current_deadline,
                'error_no_tasks' => $error_no_tasks,
                'tasks_for_counting' => $user_tasks,
                'content_error_404' => $content_error_404
            ]);
        } else {
            $error = mysqli_error($link);
            $content = include_template('error.php', ['error' => $error]);
        }
    } else {
        $search = trim($search);
        if (!empty($search)) {
            require_once 'search.php';
        } else {
            header("Location: index.php");
            exit();
        }
    }

    if ($check_task_status !== null) {
        $sql = 'UPDATE tasks SET status = not status WHERE id = ? AND user_id = ?';
        $stmt = db_get_prepare_stmt($link, $sql, [$check_task_id, $current_user_id]);
        $result = mysqli_stmt_execute($stmt);
        if ($result) {
            header("Location: index.php");
            exit();
        }
    }
} else {
    $content = include_template('guest.php');
}

$layout_content = include_template('layout.php', [
    'content' => $content,
    'title' => 'Дела в порядке',
    'current_user_name' => $current_user_name,
    'is_auth' => $is_auth
]);

print($layout_content);

?>
