<?php
require_once 'functions.php';
require_once 'sqlconnect.php';

session_start();

$con = mysqli_connect('localhost', 'root', '', 'yeticave');

get_sqlcon_info($con);

$product_categories = get_product_cat($con);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];

    $required = ['email', 'pass'];
    $dict = ['email' => 'E-mail',
    'pass' => 'Пароль'];
    $errors = [];

    foreach ($required as $key) {
        if (empty($login[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    $email = mysqli_real_escape_string($con, $login['email']);
    $email_sql = "SELECT id, email, pass, avatar_url, name FROM users WHERE email = '$email'";
    $res = mysqli_query($con, $email_sql);

    $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

    if (!filter_var($login['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите корректный E-mail';
    }

    if (!count($errors) && $user) {
        if (password_verify($login['pass'], $user['pass'])) {
            $_SESSION['user'] = $user;
        } else {
            $errors['pass'] = 'Неверный пароль';
        }
    } else {
        $errors['email'] = 'Пользователь не найден';
    }

    if (count($errors)) {
        $page_content = include_templates('templates/login.php', ['login' => $login,
            'errors' => $errors, 'dict' => $dict, 'product_categories' => $product_categories]);
    } else {
        header("Location: index.php");
    }
} else {
    if (isset($_SESSION['user'])) {
        header("Location: index.php");
    }
    else {
        $page_content = include_templates('templates/login.php', ['product_categories' => $product_categories]);
    }
}


$layout_content = include_templates('templates/layout.php', [
    'page_content' => $page_content,
    'title' => 'Yeticave - Вход',
    'product_categories' => $product_categories
]);

print($layout_content);

?>







