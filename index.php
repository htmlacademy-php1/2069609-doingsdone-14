<?php
 // Создаем функцию подсчета проектов number_of_pr, в зависимости от категории задачи

function count_of_tasks($array_of_task, $name_of_category) {
    $count_of_project = 0;
    foreach ($array_of_task as $task){
        if ($task['category']===$name_of_category){
            $count_of_project++;
        }
    }
    return $count_of_project;
}

require_once ('helpers.php');
$projects = ['Входящие','Учеба', 'Работа', 'Домашние дела', 'Авто'];
$tasks = [
    [
        'name' => 'Собеседование в IT компании',
        'date' => '03.04.2022',
        'category' => 'Работа',
        'status' => true
    ],
    [
        'name' => 'Выполнить тестовое задание',
        'date' => '03.04.2022',
        'category' => 'Работа',
        'status' => false
    ],
    [
        'name' => 'Сделать задание первого раздела',
        'date' => '01.05.2022',
        'category' => 'Учеба',
        'status' => true
    ],
    [
        'name' => 'Встреча с другом',
        'date' => '22.12.2022',
        'category' => 'Входящие',
        'status' => false
    ],
    [
        'name' => 'Купить корм для кота',
        'date' => '05.04.2022',
        'category' => 'Домашние дела',
        'status' => false
    ],
    [
        'name' => 'Заказать пиццу',
        'date' => null,
        'category' => 'Домашние дела',
        'status' => false
    ]
];
$show_complete_tasks = rand(0, 1);

$page_content = include_template('main.php',[
    'tasks'=>$tasks,
    'projects'=>$projects,
    'show_complete_tasks'=>$show_complete_tasks
]);

$layout_content = include_template('layout.php',[
    'title'=>'Дела в порядке',
    'content'=>$page_content]);
print($layout_content);

?>
