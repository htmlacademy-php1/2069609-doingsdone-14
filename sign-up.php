<?php

require_once('init.php');
require_once('helpers.php');

$tpl_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;
    $errors = [];

    $req_fields = ['name', 'email', 'password'];

    foreach ($req_fields as $field) {
        if (empty($form[$field])) {
            $errors[$field] = "Не заполнено поле " . $field;
        }
    }
// не понимаю как разделить два правила к одному полю, чтобы потом разделить это в выводе ошибок
    if ($form['email']){
        if (!(filter_var($form['email'], FILTER_VALIDATE_EMAIL))) {
            $errors['email_valid'] = "Введите корректный email";
        }
    }

    if (empty($errors)) {
        $email = mysqli_real_escape_string($link, $form['email']);
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $res = mysqli_query($link, $sql);
        if (mysqli_num_rows($res) > 0) {
            $errors[] = 'Пользователь с этим email уже зарегистрирован';
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
    $tpl_data['errors'] = $errors;
    $tpl_data['values'] = $form;
}

$content = include_template('register.php', $tpl_data);

$layout_content = include_template('layout.php', [
    'content' => $content,
    'title' => 'Дела в порядке'
]);

print($layout_content);
