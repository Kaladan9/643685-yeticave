<?php
require_once 'functions.php';
require_once 'sqlconnect.php';

session_start();

$con = mysqli_connect('localhost', 'root', '', 'yeticave');
    //проверяем подключение к БД
get_sqlcon_info($con);

    //получаем список категорий
$product_categories = get_product_cat($con);

    //проверка существования GET запроса
$get_lot_id = $_GET['lot_id'];

$sql_lot_id = mysqli_query($con, "SELECT id FROM lots WHERE id = '$get_lot_id'");

$row_cnt = mysqli_num_rows($sql_lot_id);

$lot_info = [];
$price_info = [];
$bet_info =[];

if (isset($get_lot_id) && !is_null($get_lot_id) && $row_cnt > 0) {

    $lot_info = get_lot_info($con, $get_lot_id);

    $price_info = get_lotprice_info($con, $get_lot_id);

    $bet_info = get_bet_info($con, $get_lot_id);

    //определяем показывать ли пользователю блок с добавлением ставки:
    $bet_div_visible = true;

    //авторизован ли пользователь?
    if (!isset($_SESSION['user'])) {
        $bet_div_visible = false;
    }

    //делал ли пользователь ставку на текущий лот?
    $bet_exist = is_user_bet($con, $_SESSION['user']['id'], $get_lot_id);

    if ($bet_exist) {
        $bet_div_visible = false;
    }

    //создан ли лот текущим пользователем?
    if ($lot_info['0']['author_id'] === $_SESSION['user']['id']) {
        $bet_div_visible =false;
    }

    //истек ли срок размещения лота?
    if (strtotime($lot_info['0']['end_date']) < time()) {
        $bet_div_visible = false;
    }

    $page_content = include_templates('templates/lot.php', [
        'lot_info' => $lot_info,
        'price_info' => $price_info,
        'bet_info' => $bet_info,
        'bet_div_visible' => $bet_div_visible,
        'product_categories' => $product_categories]);

} else {
    header('HTTP/1.1 404 Not Found');
    header('Status: 404 Not Found');

    $page_content = 'Страница 404';
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bet = $_POST['bet'];

    $required = ['amount'];
    $dict = ['amount' => 'Ваша ставка'];
    $errors = [];

    //определяем минимально возможную ставку
    $min_amount = get_min_amount($price_info['0']['primary_price'], $price_info['0']['max_bet'], $price_info['0']['rate_step']);

    if (!ctype_digit($bet['amount']) || ((int)($bet['amount']) < $min_amount)) {
        $errors['amount'] = 'Введите корректную ставку';
    }

    if (empty($bet['amount'])) {
        $errors['amount'] = 'Это поле надо заполнить';
    }

    if (count($errors)) {
        $page_content = include_templates('templates/lot.php', [
            'lot_info' => $lot_info,
            'price_info' => $price_info,
            'bet_info' => $bet_info,
            'errors' => $errors,
            'dict' => $dict,
            'bet' => $bet,
            'bet_div_visible' => $bet_div_visible,
            'product_categories' => $product_categories]);
    } else {
    //добавляем ставку в БД
        add_user_bet($con, $bet['amount'], $_SESSION['user']['id'], $get_lot_id);
    }
    $page_content = include_templates('templates/lot.php', [
        'lot_info' => $lot_info,
        'price_info' => $price_info,
        'bet_info' => $bet_info,
        'errors' => $errors,
        'dict' => $dict,
        'bet' => $bet,
        'bet_div_visible' => $bet_div_visible,
        'product_categories' => $product_categories]);
}

$layout_content = include_templates('templates/layout.php', [
    'page_content' => $page_content,
    'title' => 'Yeticave - Просмотр лота',
    'product_categories' => $product_categories
]);

print($layout_content);
?>
