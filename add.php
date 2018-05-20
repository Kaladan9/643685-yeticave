<?php
require_once 'functions.php';
require_once 'data.php';


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
        $lot = $_POST['lot'];

        $required = ['name', 'category', 'description', 'primary_price', 'rate_step', 'end_date'];
        $dict = ['name' => 'Наименование',
        'category' => 'Категория',
        'description' => 'Описание',
        'primary_price' => 'Начальная цена',
        'rate_step' => 'Шаг ставки',
        'end_date' => 'Дата окончания торгов',
        'file' => 'Лот'];
        $errors = [];

        foreach ($required as $key) {
            if (empty($lot[$key])) {
                $errors[$key] = 'Это поле надо заполнить';
            }
        }

        if  (($_FILES['lot_img']['name']) !== '') {
            $tmp_name = $_FILES['lot_img']['tmp_name'];
            $path = $_FILES['lot_img']['name'];

            $file_type = mime_content_type($tmp_name);

            if ($file_type == 'image/png' || $file_type == 'image/jpeg') {
                $filename = uniqid();
                $lot['img_url'] = 'img/' . $filename . '.jpg';
                move_uploaded_file($tmp_name, $lot['img_url']);
            } else {
                $errors['file'] = 'Загрузите картинку в формате PNG, JPG или JPEG';
            }
        } else {
            $errors['file'] = 'Вы не загрузили файл';
        }

        if (!is_numeric($lot['primary_price']) || ($lot['primary_price'] <= 0)) {
            $errors['primary_price'] = 'Введите число больше 0';
        }


        $form_date = strtotime($lot['end_date']);
        if (!is_numeric($form_date) || ($form_date < strtotime('+1 day'))) {
            $errors['end_date'] = 'Введите корректную дату';
        }


        if (!ctype_digit($lot['rate_step']) || ((int)($lot['rate_step']) < 0)) {
            $errors['rate_step'] = 'Введите целое число больше 0';
        }

        if (count($errors)) {
            $page_content = include_templates('templates/add-lot.php', ['lot' => $lot, 'errors' => $errors, 'dict' => $dict, 'product_categories' => $product_categories]);

        } else {
            $sql = 'INSERT INTO lots (add_date, name, dscr, img_url, primary_price, end_date, rate_step, author_id, category_id)
            VALUES (NOW(), ?, ?, ?, ?, ?, ?, 1, ?)';

            $stmt = db_get_prepare_stmt($con, $sql, [$lot['name'], $lot['description'], $lot['img_url'], $lot['primary_price'],
                $lot['end_date'], $lot['rate_step'], $lot['category']]);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                $lot_id = mysqli_insert_id($con);

                header("Location: lot.php?lot_id=" . $lot_id);
            } else {
                $sql_error = mysqli_error($con);
                $page_content = '';
                print('Ошибка БД: ' . $sql_error);
            }

        }
    } else {
        $page_content = include_templates('templates/add-lot.php', ['product_categories' => $product_categories]);
    }
}

$layout_content = include_templates('templates/layout.php', [
    'page_content' => $page_content,
    'title' => 'Yeticave - Добавление лота',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'user_avatar' => $user_avatar,
    'product_categories' => $product_categories
]);

print($layout_content);

?>
