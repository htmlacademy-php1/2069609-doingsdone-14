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
        true);
    foreach ($form as $key => $value) {
        if (in_array($key, $required) && empty($value)) {
            $errors[$key] = "Поле $key надо заполнить";
        }
    }
    $errors = array_filter($errors);

    $email = mysqli_real_escape_string($link, $form['email']);
    $sql = 'SELECT * FROM users WHERE email = ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$email]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
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
            $_SESSION['user'] = [
                'id'=>$current_user['id'],
                'name'=>$current_user['name']
            ];
        } else {
            $errors['password'] = 'Неверный пароль';
        }
    }
    else if (!$errors['email']){
        $errors['email'] = 'Такой пользователь не найден';
    }

    if (count($errors)) {
        $content = include_template('auth.php', ['form' => $form, 'errors' => $errors]);
    }
    else {
        header("Location: /index.php");
        // что за exit?
        exit();
    }
}
else {
    $content = include_template('auth.php');
    if (isset($_SESSION['user'])) {
        header("Location: /index.php");
        exit();
    }
}


$layout_content = include_template('layout.php', [
    '_SESSION' => $_SESSION,
    'content' => $content,
    'title' => 'Дела в порядке'
]);

print($layout_content);
$content = include_template('form-authorization.php', [
    'title'=>'Дела в порядке'
]);
print ($content);

