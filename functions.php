<?php

function wfr_currency_array() {
    $currency_array = array('EGP', 'SAR', 'AED', 'SDG', 'DZD', 'BHD', 'IQD', 'JOD', 'KWD', 'LBP', 'LYD', 'MAD', 'SYP', 'SOS', 'OMR', 'QAR', 'TND', 'YER', 'DJF', 'AOA', 'BWP', 'BIF', 'CVE', 'ERN', 'ETB', 'GMD', 'GHS', 'GNF', 'LSL', 'LRD', 'KHR', 'KYD', 'KES', 'KMF', 'MGA', 'MRO', 'MUR', 'MWK', 'MZN', 'NAD', 'NGN', 'RWF', 'SCR', 'SHP', 'SLL', 'STD', 'SZL', 'TZS', 'UGX', 'XAF', 'XOF', 'ZAR', 'ZMK', 'ZMW', 'ZWD', 'ZWL', 'AFN', 'AMD', 'AZN', 'BAM', 'BDT', 'BND', 'BTN', 'CNY', 'HKD', 'JPY', 'IDR', 'ILS', 'INR', 'IRR', 'LKR', 'LAK', 'KGS', 'KPW', 'KRW', 'KZT', 'MMK', 'MNT', 'MOP', 'MVR', 'MYR', 'NPR', 'PHP', 'PKR', 'SGD', 'THB', 'TJS', 'TMT', 'TRY', 'TWD', 'UZS', 'VND', 'EUR', 'ALL', 'BYN', 'BYR', 'GBP', 'GEL', 'CHF', 'CZK', 'GIP', 'NOK', 'DKK', 'EEK', 'HRK', 'HUF', 'ISK', 'SEK', 'PLN', 'RON', 'RUB', 'JMD', 'MDL', 'MKD', 'SKK', 'RSD', 'UAH', 'ANG', 'AWG', 'BBD', 'BSD', 'BMD', 'BZD', 'CAD', 'CRC', 'CUP', 'DOP', 'GTQ', 'HTG', 'MXN', 'NIO', 'PAB', 'SVC', 'TTD', 'USD', 'ARS', 'BOB', 'BRL', 'CLP', 'COP', 'GYD', 'VEF', 'VES', 'XCD', 'FKP', 'PEN', 'PYG', 'SRD', 'UYU', 'AUD', 'NZD', 'HNL', 'PGK', 'XPF', 'SBD', 'TOP', 'VUV', 'WST', 'XAG', 'XAU', 'XPT', 'BTC');

    asort($currency_array);
    return $currency_array;
}

function wfr_languages() {
    $langs = array('en' => 'English', 'fr' => 'French', 'es' => 'Spanish', 'de' => 'German', 'ru' => 'Russian', 'ar' => 'Arabic');
    return $langs;
}

function wfr_themes() {
    $langs = array('default', 'blue', 'green', 'red', 'yellow');
    return $langs;
}

function wfr_get_current_lang() {
    if (defined('ICL_LANGUAGE_CODE')) {
        $lang_code = ICL_LANGUAGE_CODE;
    } else {
        $lang_code = substr(get_locale(), 0, 2);
    }
    if (array_key_exists($lang_code, wfr_languages())) {
        return $lang_code;
    } else {
        return 'en';
    }
}

add_action("wp_ajax_wfr_ajax_response", "wfr_ajax_response");

function wfr_ajax_response() {
    $currency_major = 'USD, EUR,GBP,AUD,CAD,CHF';
    $wfr_currency_array = wfr_currency_array();
    $wfr_langs = wfr_languages();
    $wfr_themes = wfr_themes();
    $wfr_currency_display = array('name','code');

    if (isset($_GET['language']) && array_key_exists($_GET['language'], $wfr_langs)) {
        $language = sanitize_text_field($_GET['language']);
    } else {
        $language = 'en';
    }
    if (isset($_GET['base']) && in_array($_GET['base'], $wfr_currency_array)) {
        $base = sanitize_text_field($_GET['base']);
    } else {
        $base = 'USD';
    }
    if (isset($_GET['cross'])) {
        $cross = sanitize_text_field($_GET['cross']);
    } else {
        $cross = $currency_major;
    }
    $cross_arr = explode(',', $cross);
    foreach ($cross_arr as $item) {
        if (!in_array($item, $wfr_currency_array))
            continue;
        $validated_cross_arr[] = $item;
    }
    if (isset($_GET['display']) && in_array($_GET['display'], $wfr_currency_display)) {
        $display = sanitize_text_field($_GET['display']);
    } else {
        $display = 'code';
    }
    if (isset($_GET['calc'])) {
        $calc = sanitize_text_field($_GET['calc']);
    } else {
        $calc = 'off';
    }
    if (isset($_GET['amount']) && is_numeric($_GET['amount']) && $_GET['amount'] > 0) {
        $amount = sanitize_text_field($_GET['amount']);
    } else {
        $amount = '1';
    }
    if (isset($_GET['theme']) && in_array($_GET['theme'], $wfr_themes)) {
        $theme = sanitize_text_field($_GET['theme']);
    } else {
        $theme = 'default';
    }
    
    
    if (in_array($base, $validated_cross_arr)) {
        $all_rows_count = count($validated_cross_arr);
    } else {
        $all_rows_count = count($validated_cross_arr) + 1;
    }
    $height = $all_rows_count * 32 + 40 + 2;
    $code = '<!-- Exchange rates table widget starts here -->';
    $code .= '<iframe src="https://www.worldforexrates.com/webmasters/widget-table-loader.php?base=' . $base . '&cross=' . $cross . '&display=' . $display . '&calc=' . $calc . '&amount=' . $amount . '&theme=' . $theme . '&lang=' . $language . '" width="100%" height ="' . $height . '" frameborder="0" scrolling="no"></iframe>';
    $code .= '<div style="font-size:12px;font-family:arial;text-align:right;"><a href="https://www.worldforexrates.com/" target="_blank" style="text-decoration:none;color:#999;">' . __('Currency Converter') . '</a></div>';
    $code .= '<!-- Exchange rates table widget ends here -->';
    $shortcode = "[worldforexrates calc=$calc language=$language display=$display base=$base cross=$cross amount=$amount theme=$theme]";
    $result['code'] = $code;
    $result['shortcode'] = esc_html($shortcode);
    $echo = json_encode($result);
    echo $echo;
    exit;
}

?>