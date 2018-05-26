<?php
/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($con, $sql, $data = []) {
    $stmt = mysqli_prepare($con, $sql);

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = null;

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);
    }

    return $stmt;
}


function formatted_price($price) {
    ceil($price);

    $result_price = "$price" . " ₽";
    if ($price > 1000) {
        $result_price = number_format($price, 0, '', ' ') . "  ₽";
    }

    return $result_price;
}


function include_templates($template_url, $template_data = []) {
    if (!file_exists($template_url)) {
        return print('');
    }

    ob_start();
    extract($template_data);
    require $template_url;
    $html = ob_get_clean();
    return $html;
}

date_default_timezone_set('Europe/Moscow');

function lot_time_ending($end_date) {
    $ts_lotend = strtotime($end_date);
    $time_to_lotend = $ts_lotend - time();

    $hour = floor($time_to_lotend / 3600);
    $minute = floor(($time_to_lotend % 3600)/ 60);

    $ending_time = $hour . ':' . $minute;

    return $ending_time;
}


function get_product_cat($con) {
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

    return $product_categories;
}

function get_sqlcon_info($con) {
    if (!$con) {
        $sql_error = mysqli_connect_error();
        print('Ошибка подключения: ' . $sql_error);
        return;
    }
}

function show_sql_err($con) {
    $sql_error = mysqli_error($con);
    print('Ошибка БД: ' . $sql_error);
}

function get_lots($con) {
    $sql_lots = 'SELECT l.id AS lot_id, l.name AS name, l.primary_price AS price, l.img_url AS product_img_url, l.end_date AS end_date, c.name AS category '
    . 'FROM lots l '
    . 'JOIN categories c '
    . 'ON l.category_id = c.id '
    . 'WHERE NOW() < l.end_date '
    . 'ORDER BY l.add_date DESC LIMIT 6';

    if ($res = mysqli_query($con, $sql_lots)) {
        $product_cards = mysqli_fetch_all($res, MYSQLI_ASSOC);
        return $product_cards;
    }
    show_sql_err($con);
}


function get_lot_info($con, $lot_id) {
    $lot_sql = "SELECT l.id AS lot_id, l.name AS name, l.dscr AS description,
    l.img_url AS product_img_url, l.author_id AS author_id, l.end_date AS end_date, c.name AS category
    FROM lots l
    JOIN categories c
    ON l.category_id = c.id
    WHERE l.id = '$lot_id'";

    if  ($lot_res = mysqli_query($con, $lot_sql)) {
        $lot_info = mysqli_fetch_all($lot_res, MYSQLI_ASSOC);
        return $lot_info;
    }
    return show_sql_err($con);
}

function get_lotprice_info($con, $lot_id) {
    $price_sql = "SELECT l.id, l.primary_price AS primary_price, l.rate_step AS rate_step, MAX(b.amount) AS max_bet
    FROM lots l
    JOIN bet b
    ON l.id = b.lot_id
    WHERE l.id = '$lot_id'";

    if ($price_res = mysqli_query($con, $price_sql)) {
        $price_info = mysqli_fetch_all($price_res, MYSQLI_ASSOC);
        return $price_info;
    }
    return show_sql_err($con);
}

function get_bet_info($con, $lot_id) {
    $bet_sql = "SELECT b.bet_date AS bet_date, b.amount AS amount, u.name AS name
    FROM bet b
    JOIN users u
    ON u.id = b.user_id
    WHERE lot_id = '$lot_id'
    ORDER BY bet_date DESC LIMIT 10";


    if ($bet_res = mysqli_query($con, $bet_sql)) {
        $bet_info = mysqli_fetch_all($bet_res, MYSQLI_ASSOC);
        return $bet_info;
    }
    return show_sql_err($con);
}

function is_user_bet($con, $user_id, $lot_id) {
    $sql = "SELECT user_id, lot_id FROM bet
    WHERE user_id = '$user_id' AND lot_id = '$lot_id'";

    if ($res = mysqli_query($con, $sql)) {
        $bet_exist = mysqli_fetch_all($res, MYSQLI_ASSOC);
        return boolval($bet_exist);
    }
}

function get_min_amount ($primary_price, $max_bet, $rate_step) {
    $max_price = max($primary_price, $max_bet);
    $min_amount = $max_price + $rate_step;
    return $min_amount;
}

function formated_bet_date($bet_date) {
    $bet_time = strtotime($bet_date);
    $time_after_bet = time() - $bet_time;

    if ($time_after_bet < 3600) {
        $result = ($time_after_bet / 60) % 60 . ' минут назад';
    } elseif ($time_after_bet < 86400) {
        $result = (($time_after_bet / 60) % 60) % 60 . ' часов назад';
    }else {
        $result = date('d.m.y в H:i', $time_after_bet);
    }
    return $result;
}

function add_user_bet($con, $amount, $user_id, $lot_id) {
    $sql = 'INSERT INTO bet (bet_date, amount, user_id, lot_id)
    VALUES (NOW(), ?, ?, ?)';

    $stmt = db_get_prepare_stmt($con, $sql, [$amount, $user_id, $lot_id]);
    $res = mysqli_stmt_execute($stmt);

    if ($res) {
        header("Location: lot.php?lot_id=" . $lot_id);
        return;
    }
    return show_sql_err($con);
}

?>
