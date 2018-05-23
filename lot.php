<?php
require_once 'functions.php';
require_once 'data.php';

session_start();

$con = mysqli_connect('localhost', 'root', '', 'yeticave');

if (!$con) {
    $sql_error = mysqli_connect_error();
    print('Ошибка подключения: ' . $sql_error);

} else {
    //получаем список категорий
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
    //проверка существования GET запроса
    $get_lot_id = $_GET['lot_id'];

    $sql_lot_id = mysqli_query($con, "SELECT id FROM lots WHERE id = '$get_lot_id'");

    $row_cnt = mysqli_num_rows($sql_lot_id);

    if (isset($get_lot_id) && !is_null($get_lot_id) && $row_cnt > 0) {
        //получаем информацию о лоте
        $lot_sql = "SELECT l.id AS lot_id, l.name AS name, l.dscr AS description,
        l.img_url AS product_img_url, c.name AS category
        FROM lots l
        JOIN categories c
        ON l.category_id = c.id
        WHERE l.id = '$get_lot_id'";

        if  ($lot_res = mysqli_query($con, $lot_sql)) {
            $lot_info = mysqli_fetch_all($lot_res, MYSQLI_ASSOC);

        } else {
            $sql_error = mysqli_error($con);
            $page_content = '';
            print('Ошибка БД: ' . $sql_error);
        }

        //получаем информацию о цене
        $price_sql = "SELECT l.id, l.primary_price AS primary_price, l.rate_step AS rate_step, MAX(b.amount) AS max_bet
        FROM lots l
        JOIN bet b
        ON l.id = b.lot_id
        WHERE l.id = '$get_lot_id'";

        if ($price_res = mysqli_query($con, $price_sql)) {
            $price_info = mysqli_fetch_all($price_res, MYSQLI_ASSOC);

        } else {
            $sql_error = mysqli_error($con);
            $page_content = '';
            print('Ошибка БД: ' . $sql_error);
        }

        //получаем информацию о ставках
        $bet_sql = "SELECT b.bet_date AS bet_date, b.amount AS amount, u.name AS name
        FROM bet b
        JOIN users u
        ON u.id = b.user_id
        WHERE lot_id = '$get_lot_id'
        ORDER BY bet_date DESC LIMIT 10";


        if ($bet_res = mysqli_query($con, $bet_sql)) {
            $bet_info = mysqli_fetch_all($bet_res, MYSQLI_ASSOC);

        } else {
            $sql_error = mysqli_error($con);
            $page_content = '';
            print('Ошибка БД: ' . $sql_error);
        }

        $page_content = include_templates('templates/lot.php', [
            'lot_info' => $lot_info,
            'price_info' => $price_info,
            'bet_info' => $bet_info,
            'product_categories' => $product_categories]);

        $layout_content = include_templates('templates/layout.php', [
            'page_content' => $page_content,
            'title' => $title,
            'is_auth' => $is_auth,
            'user_name' => $user_name,
            'user_avatar' => $user_avatar,
            'product_categories' => $product_categories
        ]);

    } else {
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');

        $page_content = 'Страница 404';

        $layout_content = include_templates('templates/layout.php', [
            'page_content' => $page_content,
            'title' => 'Yeticave - Просмотр лота',
            'is_auth' => $is_auth,
            'user_name' => $user_name,
            'user_avatar' => $user_avatar,
            'product_categories' => $product_categories
        ]);
    }
}

print($layout_content);
?>
