<?php
require_once 'functions.php';
require_once 'data.php';

session_start();

$con = mysqli_connect('localhost', 'root', '', 'yeticave');

get_sqlcon_info($con);

$product_cards = get_lots($con);

$page_content = include_templates('templates/index.php', ['product_cards' => $product_cards]);
$product_categories = get_product_cat($con);

$layout_content = include_templates('templates/layout.php', [
    'page_content' => $page_content,
    'title' => $title,
    'product_categories' => $product_categories
]);

print($layout_content);

?>
