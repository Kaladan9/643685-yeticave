<?php
require_once 'functions.php';
require_once 'data.php';

session_start();

$con = mysqli_connect('localhost', 'root', '', 'yeticave');

if (!$con) {
    $sql_error = mysqli_connect_error();
    print('Ошибка подключения: ' . $sql_error);

} else {

    $sql_lots = 'SELECT l.id AS lot_id, l.name AS name, l.primary_price AS price, l.img_url AS product_img_url, c.name AS category '
              . 'FROM lots l '
              . 'JOIN categories c '
              . 'ON l.category_id = c.id '
              . 'WHERE NOW() < l.end_date '
              . 'ORDER BY l.add_date DESC LIMIT 6';

    if ($res = mysqli_query($con, $sql_lots)) {
        $product_cards = mysqli_fetch_all($res, MYSQLI_ASSOC);
        $page_content = include_templates('templates/index.php', ['product_cards' => $product_cards]);
   } else {
        $sql_error = mysqli_error($con);
        $page_content = '';
        print('Ошибка БД: ' . $sql_error);
   }


    $sql = 'SELECT id, name FROM categories '
         . 'ORDER BY id';

    $result = mysqli_query($con, $sql);
    $product_categories = [];

    if ($result) {
        $product_categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

    } else {
        $sql_error = mysqli_error($con);
        $page_content = '';
        print('Ошибка БД: ' . $sql_error);
    }
}

$layout_content = include_templates('templates/layout.php', [
    'page_content' => $page_content,
    'title' => $title,
    'is_auth' => $is_auth,
    'product_categories' => $product_categories
]);

print($layout_content);

?>
