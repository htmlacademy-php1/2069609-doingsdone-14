<?php

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

require_once 'vendor/autoload.php';
require_once 'init.php';
require_once 'helpers.php';

// Конфигурация транспорта
$dsn = 'smtp://annaselvyan:vdeisnmgryghjfrk@smtp.yandex.ru:465';
$transport = Transport::fromDsn($dsn);

// Находим всех пользователей
$sql = 'SELECT email, name, id FROM users';
$result = mysqli_query($link, $sql);
if ($result) {
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($users as $user) {
        // Находим все задачи со статусом 0, для каждого пользователя
        $sql = 'SELECT name  FROM tasks WHERE status = 0 AND due_date = CURDATE() AND user_id = ' . $user['id'];
        $result = mysqli_query($link, $sql);
        // Количество задач
        $tasks_count = mysqli_num_rows($result);

        if ($result and $tasks_count !== 0) {
            $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
            // Извлечем из массива tasks столбец с именем name
            $task_for_message_array = array_column($tasks, 'name');
            // Значения столбцов приведем к строке, разделенных запятой
            $task_for_message = implode(", ", $task_for_message_array);
            // Текущая дата
            $date_for_message = date("Y/m/d");
            $email = $user['email'];
            $name = $user['name'];
            // Находим форму слов 'запланирована' и 'задача' для сообщения
            $first_word = get_noun_plural_form($tasks_count, 'запланирована', 'запланированы', 'запланированы');
            $second_word = get_noun_plural_form($tasks_count, 'задача', 'задачи', 'задачи');

            //Формирование сообщения
            $message = new Email();
            $message->to("$email");

            // !! с keks@phpdemo.ru не сработало, но сработало с annaselvyan@yandex.ru
            $message->from("keks@phpdemo.ru");
            $message->subject("Уведомление от сервиса «Дела в порядке»");
            $message->text(
                "Уважаемый (ая), $name. У вас $first_word $second_word $task_for_message на $date_for_message."
            );

            // Отправка сообщения
            $mailer = new Mailer($transport);
            $mailer->send($message);
        }
    }
}
