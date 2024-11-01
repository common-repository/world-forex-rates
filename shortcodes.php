<?php

add_shortcode('worldforexrates', 'wfr_worldforexrates');
function wfr_worldforexrates($atts, $content = null) {
    extract(shortcode_atts(array('amount' => 1, 'base' => 'USD', 'cross' => 'USD,EUR,GBP,AUD,CAD,CHF,JPY', 'language' => 'en','display'=>'code','theme'=>'default','calc'=>'off'), $atts));
    global $wfr_lang;
    $cross_arr= explode(',',$cross);
    if (in_array($base, $cross_arr)) {
        $all_rows_count = count($cross_arr);
    } else {
        $all_rows_count = count($cross_arr) + 1;
    }
    $credit_an=$wfr_lang['link_anchor'];
    $credit_ln=$wfr_lang['link'];
    $height = $all_rows_count * 32 + 40 + 2;
    $code = '<!-- Exchange rates table widget starts here -->';
    $code .= '<iframe src="https://www.worldforexrates.com/webmasters/widget-table-loader.php?base=' . $base . '&cross=' . $cross . '&display=' . $display . '&calc=' . $calc . '&amount=' . $amount . '&theme=' . $theme . '&lang=' . $language . '" width="100%" height ="' . $height . '" frameborder="0" scrolling="no"></iframe>';
    $code .= '<div style="font-size:12px;font-family:arial;text-align:right;"><a href="'.$credit_ln.'" style="text-decoration:none;color:#999;">' .$credit_an . '</a></div>';
    $code .= '<!-- Exchange rates table widget ends here -->';
    return $code;
}
