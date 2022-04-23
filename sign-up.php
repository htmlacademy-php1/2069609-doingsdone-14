<?php

require_once('init.php');
require_once('helpers.php');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;
    $req_fields = ['name', 'email', 'password'];

    foreach ($req_fields as $field) {
        if (empty($form[$field])) {
            $errors[$field] = "Не заполнено поле " . $field;
        }
    }
    if ($form['email']){
        if (!(filter_var($form['email'], FILTER_VALIDATE_EMAIL))) {
            $errors['email'] = "Введите корректный email";
        }
    }

    if (empty($errors)) {
        $email = mysqli_real_escape_string($link, $form['email']);
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $res = mysqli_query($link, $sql);
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
