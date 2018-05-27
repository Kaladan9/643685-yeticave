<?php
/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $con mysqli Ресурс соединения
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

/**
 * Форматирует цену лота, добавляет знак рубля
 *
 * @param $price string Цена для форматирования
 *
 * @return $result_price string Отформатированная цена
 */
function formatted_price($price) {
    ceil($price);

    $result_price = "$price" . " ₽";
    if ($price > 1000) {
        $result_price = number_format($price, 0, '', ' ') . "  ₽";
    }

    return $result_price;
}

/**
 * Функция-шаблонизатор с буферизацией вывода, для захвата содержимого
 *
 * @param $template_url string Путь к шаблону
 * @param $template_data array Массив с данными для шаблона
 *
 * @return $html Готовый html шаблон
 */
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

/**
 * Временная зона в московское время
*/
date_default_timezone_set('Europe/Moscow');

/**
 * Определяет время до завершения лота в часах и минутах
 *
 * @param $end_date string Цена Время завершения лота
 *
 * @return $ending_time string Время до завершения лота
 */
function lot_time_ending($end_date) {
    $ts_lotend = strtotime($end_date);
    $time_to_lotend = $ts_lotend - time();

    $hour = floor($time_to_lotend / 3600);
    $minute = floor(($time_to_lotend % 3600)/ 60);

    $ending_time = $hour . ':' . $minute;

    return $ending_time;
}

/**
 * Получает данные о категориях из БД
 *
 * @param $con mysqli Ресурс соединения
 *
 * @return $product_categories array Массив с данными о категориях
 */
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

/**
 * Сообщает об ошибке подключения к БД
 *
 * @param $con mysqli Ресурс соединения
 *
 * @return string Текст с ошибкой подключения
 */
function get_sqlcon_info($con) {
    if (!$con) {
        $sql_error = mysqli_connect_error();
        print('Ошибка подключения: ' . $sql_error);
        return;
    }
}

/**
 * Сообщает об ошибке запроса к БД
 *
 * @param $con mysqli Ресурс соединения
 *
 * @return string Текст с ошибкой запроса
 */
function show_sql_err($con) {
    $sql_error = mysqli_error($con);
    print('Ошибка БД: ' . $sql_error);
}

/**
 * Запрос к БД о самых последних лотах
 *
 * @param $con mysqli Ресурс соединения
 *
 * @return $product_cards array Массив с данными о последних лотах
 */
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
    return show_sql_err($con);
}

/**
 * Запрос к БД об основной информации по конкретному лоту
 *
 * @param $con mysqli Ресурс соединения
 * @param $lot_id string ID нужного лота
 *
 * @return $lot_info array Массив с данными о нужном лоте
 */
function get_lot_info($con, $lot_id) {
    $lot_id = mysqli_real_escape_string($con, $lot_id);
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

/**
 * Запрос к БД о цене, шаге и максимальной ставке по конкретному лоту
 *
 * @param $con mysqli Ресурс соединения
 * @param $lot_id string ID нужного лота
 *
 * @return $price_info array Массив с ценовой информацией о нужном лоте
 */
function get_lotprice_info($con, $lot_id) {
    $lot_id = mysqli_real_escape_string($con, $lot_id);
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

/**
 * Запрос к БД о ставках по конкретному лоту
 *
 * @param $con mysqli Ресурс соединения
 * @param $lot_id string ID нужного лота
 *
 * @return $bet_info array Массив с информацией о ставках на нужный лот
 */
function get_bet_info($con, $lot_id) {
    $lot_id = mysqli_real_escape_string($con, $lot_id);
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

/**
 * Запрос к БД о существовании ставки пользователя на конкретный лот
 *
 * @param $con mysqli Ресурс соединения
 * @param $user_id string ID пользователя в сессии
 * @param $lot_id string ID нужного лота
 *
 * @return bool флаг о существовании ставки пользователя на конкретный лот
 */
function is_user_bet($con, $user_id, $lot_id) {
    $user_id = mysqli_real_escape_string($con, $user_id);
    $lot_id = mysqli_real_escape_string($con, $lot_id);
    $sql = "SELECT user_id, lot_id FROM bet
    WHERE user_id = '$user_id' AND lot_id = '$lot_id'";

    if ($res = mysqli_query($con, $sql)) {
        $bet_exist = mysqli_fetch_all($res, MYSQLI_ASSOC);
        return boolval($bet_exist);
    }
}

/**
 * Определение минимально возможной ставки на лот
 *
 * @param $primary_price string Начальная цена лота
 * @param $max_bet string Максимальная ставка на лот
 * @param $rate_step string Заданный шаг ставки
 *
 * @return $min_amount int Минимальная ставка на лот
 */
function get_min_amount ($primary_price, $max_bet, $rate_step) {
    $max_price = max($primary_price, $max_bet);
    $min_amount = $max_price + $rate_step;

    return $min_amount;
}

/**
 * Форматированние даты размещения ставки в зависимости от прошедшего времени
 *
 * @param $bet_date string Дата ставки
 *
 * @return $result string Отформатированная дата
 */
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

/**
 * Добавление ставки пользователя в БД
 *
 * @param $con mysqli Ресурс соединения
 * @param $amount string Сумма ставки
 * @param $user_id ID пользователя в сессии
 * @param $lot_id ID лота по которому делается ставка
 *
 * @return Обновление страницы лота в случае успешного добавления
 */
function add_user_bet($con, $amount, $user_id, $lot_id) {
    $sql = 'INSERT INTO bet (bet_date, amount, user_id, lot_id)
    VALUES (NOW(), ?, ?, ?)';

    $res = mysqli_prepare($con, $sql);
    $stmt = db_get_prepare_stmt($con, $sql, [$amount, $user_id, $lot_id]);
    $res = mysqli_stmt_execute($stmt);

    if ($res) {
        header("Location: lot.php?lot_id=" . $lot_id);
        return;
    }
    return show_sql_err($con);
}

?>
