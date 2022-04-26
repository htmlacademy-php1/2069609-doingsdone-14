<?php
require_once ('helpers.php');
require_once ('init.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;
    $required = ['email', 'password'];
    $errors = [];

    $form = filter_input_array(INPUT_POST, [
        'email' => FILTER_DEFAULT,
        'password' => FILTER_DEFAULT],
        true
    );

    foreach ($form as $key => $value) {
        if (in_array($key, $required) && empty($value)) {
            $errors[$key] = "Поле $key надо заполнить";
        }
    }
    $errors = array_filter($errors);

    $sql = 'SELECT * FROM users WHERE email = ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$form['email']]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    // такая запись чтобы привыкнуть
    //$user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;
    if ($res) {
        $current_user = mysqli_fetch_array($res, MYSQLI_ASSOC);
    } else {
        $current_user = null;
    }

    if (!empty($errors)) {
        $content = include_template('auth.php', ['form' => $form, 'errors' => $errors]);
    } else if (empty($errors) and $current_user) {
        if (password_verify($form['password'], $current_user['password'])) {
            $_SESSION['user'] = [
                'id'=>$current_user['id'],
                'name'=>$current_user['name']
            ];
        } else {
            $errors['password'] = 'Неверный пароль';
        }
    } else {
        $errors['email'] = 'Такой пользователь не найден';
    }
    if (!count($errors)) {
        header("Location: /index.php");
        // что за exit?
        exit();
    }
    else {
        $content = include_template('auth.php', ['form' => $form, 'errors' => $errors]);
    }
}
else {
    $content = include_template('auth.php');

    if (isset($_SESSION['user'])) {
        header("Location: /index.php");
        exit();
    }
}

require('values_is_auth_and_current_user_name.php');

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'current_user_name' => $current_user_name,
    'content' => $content,
    'title' => 'Дела в порядке'
]);

print($layout_content);


