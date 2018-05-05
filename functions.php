<?php

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
?>
