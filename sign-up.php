<?php

require_once('init.php');
require_once('helpers.php');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;
    $required = ['name', 'email', 'password'];



    $rules = [
        'name' => function ($value) {
            return validate_length($value, 255);
        },
        'email' => function($value) {
            if ($value) {
                return validate_length($value, 255);
            }
            if (!(filter_var($value, FILTER_VALIDATE_EMAIL))) {
                return "Введите корректный email";
            }
            else {
                return null;
            }
        }
    ];

    $form = filter_input_array(INPUT_POST, [
        'name' => FILTER_DEFAULT,
        'email' => FILTER_DEFAULT,
        'password' => FILTER_DEFAULT],
        true);
    foreach ($form as $key => $value) {
        if (in_array($key, $required) && empty($value)) {
            $errors[$key] = "Поле $key надо заполнить";
        } else {
            if (isset($rules[$key])) {
                $rule = $rules[$key];
                $errors[$key] = $rule($value);
            }
        }
    }
  $errors = array_filter($errors);

    if (empty($errors)) {
        $email = mysqli_real_escape_string($link, $form['email']);
        $sql = 'SELECT id FROM users WHERE email = ?';
        $stmt = db_get_prepare_stmt($link, $sql, [$email]);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        }
        else {
            $password = password_hash($form['password'], PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (dt_add, email, name, password) VALUES (NOW(), ?, ?, ?)';
            $stmt = db_get_prepare_stmt($link, $sql, [$form['email'], $form['name'], $password]);
            $res = mysqli_stmt_execute($stmt);
        }
        if ($res && empty($errors)) {
            header("Location: /index.php");
            exit();
        }
    }
}

$content = include_template('register.php', ['errors'=>$errors]);

$layout_content = include_template('layout.php', [
    'content' => $content,
    'title' => 'Дела в порядке'
]);

print($layout_content);
