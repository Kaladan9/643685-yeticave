<?php
require_once 'functions.php';
require_once 'data.php';

session_start();

$con = mysqli_connect('localhost', 'root', '', 'yeticave');

if (!$con) {
    $sql_error = mysqli_connect_error();
    print('Ошибка подключения: ' . $sql_error);

} else {

    $cat_sql = "SELECT id, name FROM categories
    ORDER BY id";

    $cat_res = mysqli_query($con, $cat_sql);
    $product_categories = [];

    if ($cat_res) {
        $product_categories = mysqli_fetch_all($cat_res, MYSQLI_ASSOC);

    } else {
        $sql_error = mysqli_error($con);
        $page_content = '';
        print('Ошибка БД: ' . $sql_error);
    }


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $signup = $_POST['signup'];

        $required = ['email', 'name', 'pass', 'contacts'];
        $dict = ['email' => 'E-mail',
        'name' => 'Имя пользователя',
        'pass' => 'Пароль',
        'contacts' => 'Контакты',
        'file' => 'Аватар'];
        $errors = [];

        foreach ($required as $key) {
            if (empty($signup[$key])) {
                $errors[$key] = 'Это поле надо заполнить';
            }
        }

        $signup['avatar'] = '';

        if  (($_FILES['avatar']['name']) !== '') {
            $tmp_name = $_FILES['avatar']['tmp_name'];
            $path = $_FILES['avatar']['name'];

            $file_type = mime_content_type($tmp_name);

            if ($file_type == 'image/png' || $file_type == 'image/jpeg') {
                $filename = uniqid();
                $signup['avatar'] = 'img/' . $filename . '.jpg';
                move_uploaded_file($tmp_name, $signup['avatar']);
            } else {
                $errors['file'] = 'Загрузите картинку в формате PNG, JPG или JPEG';
            }
        }

        if (!filter_var($signup['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введите корректный E-mail';
        }


        $email = mysqli_real_escape_string($con, $signup['email']);
        $email_sql = "SELECT id FROM users WHERE email = '$email'";
        $res = mysqli_query($con, $email_sql);

        if (mysqli_num_rows($res) > 0)  {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        }

        if (count($errors)) {
            $page_content = include_templates('templates/sign-up.php', ['signup' => $signup, 'errors' => $errors, 'dict' => $dict, 'product_categories' => $product_categories]);
        } else {
            $password = password_hash($signup['pass'], PASSWORD_DEFAULT);

            $user_sql = 'INSERT INTO users (add_date, email, name, pass, avatar_url, contacts) VALUES (NOW(), ?, ?, ?, ?, ?)';
            $stmt = db_get_prepare_stmt($con, $user_sql, [$signup['email'], $signup['name'], $password, $signup['avatar'], $signup['contacts']]);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                header("Location: index.php");
            } else {
                $sql_error = mysqli_error($con);
                $page_content = '';
                print('Ошибка БД: ' . $sql_error);
            }
        }
    } else {
        $page_content = include_templates('templates/sign-up.php', ['product_categories' => $product_categories]);
    }
}


$layout_content = include_templates('templates/layout.php', [
    'page_content' => $page_content,
    'title' => 'Yeticave - Регистрация пользователя',
    'is_auth' => $is_auth,
    'product_categories' => $product_categories
]);

print($layout_content);

?>
