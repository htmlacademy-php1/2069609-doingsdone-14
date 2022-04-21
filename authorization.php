<?php
require_once ('helpers.php');
require_once ('init.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;
    $required = ['email', 'password'];
    $errors = [];
    foreach ($required as $field) {
        if (empty($form[$field])) {
            $errors[$field] = 'Это поле надо заполнить';
        }
    }
    $email = mysqli_real_escape_string($link, $form['email']);
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $res = mysqli_query($link, $sql);
    // такая запись чтобы привыкнуть
    //$user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;
    if ($res) {
        $current_user = mysqli_fetch_array($res, MYSQLI_ASSOC);
    } else {
        $current_user = null;
    }
    if (!count($errors) and $current_user) {
        if (password_verify($form['password'], $current_user['password'])) {
            $_SESSION['user'] = $current_user;
        } else {
            $errors['password'] = 'Неверный пароль';
        }
    }
    else {
        $errors['email'] = 'Такой пользователь не найден';
    }
    if (count($errors)) {
        $content = include_template('form-authorization.php', ['form' => $form, 'errors' => $errors]);
    }
    else {
        header("Location: /index.php");
        // что за exit?
        exit();
    }
}
else {
    $content = include_template('form-authorization.php');
    if (isset($_SESSION['user'])) {
        header("Location: /index.php");
        exit();
    }
}


$layout_content = include_template('layout.php', [
    'content' => $content,
    'title' => 'Дела в порядке'
]);

print($layout_content);













$content = include_template('form-authorization.php', [
    'title'=>'Дела в порядке'
]);
print ($content);
