<?php
require_once 'functions.php';
require_once 'data.php';


$con = mysqli_connect('localhost', 'root', '', 'yeticave');

if (!$con) {
    $sql_error = mysqli_connect_error();
    print('Ошибка подключения: ' . $sql_error);

} else {
    $sql_lots = 'SELECT l.name AS name, l.primary_price AS price, l.img_url AS product_img_url, c.name AS category '
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
}

$sql = 'SELECT name FROM categories '
     . 'ORDER BY id';

$result = mysqli_query($con, $sql);
$product_categories = [];

if ($result) {
    $cats = mysqli_fetch_all($result, MYSQLI_ASSOC);
    foreach ($cats as $cat) {
        $product_categories[] = $cat['name'];
    }

} else {
    $sql_error = mysqli_error($con);
    $page_content = '';
    print('Ошибка БД: ' . $sql_error);
}


$layout_content = include_templates('templates/layout.php', [
    'page_content' => $page_content,
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'user_avatar' => $user_avatar,
    'product_categories' => $product_categories
]);

print($layout_content);

?>
