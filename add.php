<?php
require_once 'functions.php';
require_once 'data.php';

session_start();

$con = mysqli_connect('localhost', 'root', '', 'yeticave');

get_sqlcon_info($con);

$product_categories = get_product_cat($con);

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
        VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt = db_get_prepare_stmt($con, $sql, [$lot['name'], $lot['description'], $lot['img_url'], $lot['primary_price'],
            $lot['end_date'], $lot['rate_step'], $_SESSION['user']['id'], $lot['category']]);
        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            $lot_id = mysqli_insert_id($con);
            header("Location: lot.php?lot_id=" . $lot_id);
        } else {
            show_sql_err($con);
        }

    }
} else {
    $page_content = include_templates('templates/add-lot.php', ['product_categories' => $product_categories]);
}


if (!isset($_SESSION['user'])) {
    header('HTTP/1.1 403 Forbidden');
    header('Status: 403 Forbidden');

    $page_content = 'Страница 403, доступ запрещен';
}

$layout_content = include_templates('templates/layout.php', [
    'page_content' => $page_content,
    'title' => 'Yeticave - Добавление лота',
    'product_categories' => $product_categories
]);

print($layout_content);

?>
