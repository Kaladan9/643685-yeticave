<?php
require_once 'functions.php';
require_once 'data.php';

$page_content = include_templates('templates/index.php', ['product_cards' => $product_cards]);
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
