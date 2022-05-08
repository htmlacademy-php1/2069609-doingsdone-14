<?php
require_once 'init.php';
require_once 'helpers.php';

function is_task_important($task_date): bool
{
    if ($task_date) {
        $current_time = time();
        return (strtotime($task_date) - $current_time < SECONDS_IN_DAY);
    }
    return false;
}

$tasks_section = [
    [
        'name'=>'Все задачи',
        'deadline' => 'all'
    ],
     [
        'name'=>'Повестка дня',
        'deadline' => 'today'
    ],
    [
        'name'=>'Завтра',
        'deadline'=>'tomorrow'
    ],
    [
        'name'=>'Просроченные',
        'deadline'=>'yesterday'
    ]
];
$current_project_id = (int) filter_input(INPUT_GET, 'project_id', FILTER_SANITIZE_NUMBER_INT);
$show_complete_tasks = (int) filter_input(INPUT_GET, 'show_completed', FILTER_SANITIZE_NUMBER_INT);
$current_deadline = filter_input(INPUT_GET, 'deadline', FILTER_SANITIZE_SPECIAL_CHARS);
$check_task_status = filter_input(INPUT_GET, 'check', FILTER_SANITIZE_NUMBER_INT);
$check_task_id = filter_input(INPUT_GET, 'task_id', FILTER_SANITIZE_NUMBER_INT);


if (empty($current_deadline)) {
    $current_deadline = 'all';
}

if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
}
else {
    if (array_key_exists ('user', $_SESSION)) {
        $current_user_id = $_SESSION['user']['id'];
        require 'list_of_projects.php';
        if ($list_of_projects) {
            $projects = mysqli_fetch_all($list_of_projects, MYSQLI_ASSOC);
        } else {
            $error = mysqli_error($link);
            $content = include_template('error.php', ['error' => $error]);
        }

        $what_tasks_section_show = '';

        if ($current_deadline == 'yesterday') {
            $what_tasks_section_show = 'AND t.due_date < CURDATE()';
        } else if ($current_deadline == 'today') {
            $what_tasks_section_show = 'AND t.due_date = CURDATE()';
        } else if ($current_deadline == 'tomorrow') {
            $what_tasks_section_show = 'AND t.due_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)';
        }

        $search = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$search) {

            $sql = 'SELECT t.name as task_name, p.name as project_name, p.id as project_id, ' .
                't.due_date as task_date, t.status as task_status, t.link_to_file as path , t.id as task_id ' .
                'FROM tasks as t ' .
                'JOIN projects as p '.
                'ON t.project_id = p.id '.
                'WHERE t.user_id = ? ' . $what_tasks_section_show . ' ORDER BY t.date_of_create DESC';

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
                        'current_project_id' => $current_project_id,
                        'tasks_section' =>$tasks_section,
                        'current_deadline' =>$current_deadline
                    ]);

                } else {
                    if (count_of_tasks($tasks, $current_project_id) === 0) {
                        http_response_code(404);
                        $content = include_template('error.php', ['error' => 'Задачи не найдены']);
                    } else {
                        $content = include_template('main.php', [
                            'tasks' => $tasks,
                            'projects' => $projects,
                            'show_complete_tasks' => $show_complete_tasks,
                            'current_project_id' => $current_project_id,
                            'tasks_section' =>$tasks_section,
                            'current_deadline' =>$current_deadline

                        ]);
                    }
                }
            } else {
                $error = mysqli_error($link);
                $content = include_template('error.php', ['error' => $error]);
            }
        }
        else {
           $search = trim($search);
           if (!empty($search)) {
                require_once 'search.php';
           } else {
               header("Location: index.php");
           }
        }

        if ($check_task_status!==null) {
            $sql = 'UPDATE tasks SET status = not status WHERE id = ' . $check_task_id;

            if ($result = mysqli_query($link, $sql)) {
                header("Location: index.php");
            }

        }
    } else{
        $content = include_template('guest.php');
    }
}

require 'session_init.php';

$layout_content = include_template('layout.php',[
            'content' => $content,
            'title'=> 'Дела в порядке',
            'current_user_name' => $current_user_name,
            'is_auth' => $is_auth
]);

print($layout_content);

?>
