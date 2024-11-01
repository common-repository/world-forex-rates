<?php

/*
  Plugin Name: World Forex Rates
  Description: Exchange rates table where you can select from up to 200 currencies to display real time accurate exchange rates. Includes multi-language support and also supports RTL websites.
  Plugin URI: https://www.worldforexrates.com/webmasters/widget-table.php
  Version: 2.2.0
  Author: Wael Shaheen
  5/1/2020
 */

define('WFR_PLUGIN_DIR', dirname(__FILE__));
define('WFR_PLUGIN_FOLDER', basename(WFR_PLUGIN_DIR));
define('WFR_PLUGIN_URL', plugins_url() . '/' . WFR_PLUGIN_FOLDER);

add_action('init', 'wfr_init');
function wfr_init() {
    global $wfr_lang, $wfr_language;
    load_plugin_textdomain('wfr', false, dirname(plugin_basename(__FILE__)) . '/lang');
    $wfr_language = get_locale();

    if (file_exists(WFR_PLUGIN_DIR . '/lang/' . $wfr_language . '.php')) {
        require_once (WFR_PLUGIN_DIR . '/lang/' . $wfr_language . '.php');
    } else {
        require_once (WFR_PLUGIN_DIR . '/lang/en_US.php');
    }
}

require_once(WFR_PLUGIN_DIR . '/functions.php');
require_once(WFR_PLUGIN_DIR . '/shortcodes.php');
require_once(WFR_PLUGIN_DIR . '/widgets.php');
?>
