<?php

require_once 'init.php';
require_once 'helpers.php';

$errors = [];
$form = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;
    $required = ['name', 'email', 'password'];
    $rules = [
        'email' => function ($value) {
            if ($value) {
                if (!is_validate_length($value, MAXIMUM_LENGTH)) {
                    return "Длина не более 255 символов";
                }
                if (!(filter_var($value, FILTER_VALIDATE_EMAIL))) {
                    return "Введите корректный email";
                }
                return null;
            }
            return null;
        },
        'name' => function ($value) {
            if (!is_validate_length($value, MAXIMUM_LENGTH)) {
                return "Длина не более 255 символов";
            }
            $value = trim($value);
            if (empty($value)) {
                return 'Имя не может состоять из одних пробелов';
            }
            return null;
        }
    ];

    $form = filter_input_array(
        INPUT_POST,
        [
            'name' => FILTER_DEFAULT,
            'email' => FILTER_DEFAULT,
            'password' => FILTER_DEFAULT
        ],
        true
    );

    foreach ($form as $key => $value) {
        require 'find_errors.php';
    }
    $errors = array_filter($errors);

    if (empty($errors)) {
        $email = $form['email'];
        $sql = 'SELECT id FROM users WHERE email = ?';
        $stmt = db_get_prepare_stmt($link, $sql, [$email]);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        } else {
            $password = password_hash($form['password'], PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (dt_add, email, name, password) VALUES (NOW(), ?, ?, ?)';
            $stmt = db_get_prepare_stmt($link, $sql, [$form['email'], $form['name'], $password]);
            $res = mysqli_stmt_execute($stmt);
        }
        if ($res && empty($errors)) {
            header("Location: index.php");
            exit();
        }
    }
}

$content = include_template('register.php', [
    'errors' => $errors,
    'form' => $form
]);
require 'session_init.php';

$layout_content = include_template('layout.php', [
    'content' => $content,
    'title' => 'Дела в порядке',
    'current_user_name' => $current_user_name,
    'is_auth' => $is_auth
]);

print($layout_content);
