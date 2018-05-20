<?php
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

function lot_time_ending() {

    $ts_midnight = strtotime('tomorrow');
    $time_to_midnight = $ts_midnight - time();

    $hour = floor($time_to_midnight / 3600);
    $minute = floor(($time_to_midnight % 3600)/ 60);

    $ending_time = $hour . ':' . $minute;

    return strftime('%R', strtotime($ending_time));
}

?>
