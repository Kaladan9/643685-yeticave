<?php
// ставки пользователей, которыми надо заполнить таблицу
$bets = [
    ['name' => 'Иван', 'price' => 11500, 'ts' => strtotime('-' . rand(1, 50) .' minute')],
    ['name' => 'Константин', 'price' => 11000, 'ts' => strtotime('-' . rand(1, 18) .' hour')],
    ['name' => 'Евгений', 'price' => 10500, 'ts' => strtotime('-' . rand(25, 50) .' hour')],
    ['name' => 'Семён', 'price' => 10000, 'ts' => strtotime('last week')]
];

$is_auth = (bool) rand(0, 1);

$title = 'Главная';
$user_name = 'Константин';
$user_avatar = 'img/user.jpg';

$product_categories = [
    'Доски и лыжи', 'Крепления', 'Ботинки',
    'Одежда', 'Инструменты', 'Разное'
];

$product_cards = [
    0 => [
        'category' => 'Доски и лыжи',
        'name' => '2014 Rossignol District Snowboard',
        'price' => '10999',
        'product_img_url' => 'img/lot-1.jpg'
    ],
    1 => [
        'category' => 'Доски и лыжи',
        'name' => 'DC Ply Mens 2016/2017 Snowboard',
        'price' => '159999',
        'product_img_url' => 'img/lot-2.jpg'
    ],
    2 => [
        'category' => 'Крепления',
        'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'price' => '8000',
        'product_img_url' => 'img/lot-3.jpg'
    ],
    3 => [
        'category' => 'Ботинки',
        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'price' => '10999',
        'product_img_url' => 'img/lot-4.jpg'
    ],
    4 => [
        'category' => 'Одежда',
        'name' => 'Куртка для сноуборда DC Mutiny Charocal',
        'price' => '7500',
        'product_img_url' => 'img/lot-5.jpg'
    ],
    5 => [
        'category' => 'Разное',
        'name' => 'Маска Oakley Canopy',
        'price' => '5400',
        'product_img_url' => 'img/lot-6.jpg'
    ],
];
