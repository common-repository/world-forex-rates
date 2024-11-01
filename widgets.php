<?php
add_action('widgets_init', 'wfr_register_widgets');

function wfr_register_widgets() {
    register_widget('wfr_worldforexrates_widget');
}

class wfr_worldforexrates_widget extends WP_Widget {

    function __construct() {
        $widget_ops = array('classname' => 'wfr_worldforexrates_widget', 'description' => 'World Forex Rates');
        $control_ops = array('id_base' => 'wfr_worldforexrates_widget');
        parent::__construct('wfr_worldforexrates_widget', 'World Forex Rates', $widget_ops, $control_ops);
    }

    function form($instance) {
        global $wfr_lang;
        $currency_array = wfr_currency_array();
        $langs = wfr_languages();
        $themes = wfr_themes();
        $defaults = array(
            'title' => __('World Forex Rates', 'wfr'),
            'language' => wfr_get_current_lang(),
            'base_currency' => 'USD',
            'amount' => '1',
            'currency_array' => array('USD', 'EUR', 'GBP', 'AUD', 'CAD', 'CHF', 'INR', 'JPY', 'BTC'),
            'calc' => false,
            'currency_display' => 'code',
            'theme' => 'default',
        );
        $instance = wp_parse_args((array) $instance, $defaults); //Merge together an array of arguments and an array of default values
        wp_enqueue_script('wfrselect-js', WFR_PLUGIN_URL . '/assets/wfrselect.js', array('jquery'));
        wp_enqueue_style('wfr-admin-css', WFR_PLUGIN_URL . '/assets/admin.css');
        if (is_rtl()) {
            wp_enqueue_style('wfr-admin-css-rtl', WFR_PLUGIN_URL . '/assets/admin-rtl.css');
        }
        wp_enqueue_script('wfr-admin-js', WFR_PLUGIN_URL . '/assets/admin.js', array('jquery'));
        $title = sanitize_text_field($instance['title']);
        $calc = sanitize_text_field($instance['calc']);
        $amount = sanitize_text_field($instance['amount']);
        $language = sanitize_text_field($instance['language']);
        $base_currency = sanitize_text_field($instance['base_currency']);
        $inst_currency_array = array();
        foreach ($instance['currency_array'] as $index => $item) {
            $inst_currency_array[] = sanitize_text_field($item);
        }
        $currency_display = sanitize_text_field($instance['currency_display']);
        $inst_theme = sanitize_text_field($instance['theme']);
        ?>
        <p><?php _e('Title', 'wfr') ?>: <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
        <div class="wfr-container">
            <br>
            <input class="wfr-calc" id="<?php echo $this->get_field_id('calc') ?>" type="checkbox" name="<?php echo $this->get_field_name('calc') ?>" <?php if ($calc) : ?> checked="checked" <?php endif ?> />
            <label for="<?php echo $this->get_field_id('calc') ?>"><?php _e('Built-in Calculator', 'wfr') ?></label> <br> <br>


            <input class="wfr-amount" id="<?php echo $this->get_field_id('amount') ?>" type="number" min="0.01" step="0.01" name="<?php echo $this->get_field_name('amount') ?>" value="<?php echo $amount ?>" required>
            <label for="<?php echo $this->get_field_id('amount') ?>"><?php _e('Default Amount', 'wfr') ?></label> <br> <br>


            <select class="wfr-language" name="<?php echo $this->get_field_name('language') ?>" id='<?php echo $this->get_field_id('language') ?>'>
                <?php foreach ($langs as $lang_code => $lang_name) { ?>
                    <option value="<?php echo $lang_code ?>" <?php if (isset($language) && $language == $lang_code) { ?>selected<?php } ?>><?php echo $lang_name ?></option>
                <?php } ?>
            </select>
            <label for="<?php echo $this->get_field_id('language') ?>"><?php _e('Language', 'wfr') ?></label>
            <br> <br>

            <label for="<?php echo $this->get_field_id('base_currency') ?>"><?php _e('Base Currency', 'wfr') ?></label> 
            <select class="wfr-base_currency" name="<?php echo $this->get_field_name('base_currency') ?>" id='<?php echo $this->get_field_id('base_currency') ?>'>
                <?php foreach ($currency_array as $symbol) { ?>
                    <option value="<?php echo $symbol ?>" <?php if (isset($base_currency) && $base_currency == $symbol) { ?>selected<?php } ?>><?php echo $symbol ?> <?php echo $wfr_lang['cur'][$symbol]['name'] ?></option>
                <?php } ?>
            </select>
            <br> <br>

            <label><?php _e("Select Cross Currencies", 'wfr'); ?></label><br>
            <select name="<?php echo $this->get_field_name("currency_array") ?>[]" class="wfr-cross" multiple id="<?php echo $this->get_field_id("currency_array") ?>">
                <?php foreach ($currency_array as $symbol) { ?>
                    <option title="<?php echo $wfr_lang['cur'][$symbol]['name'] ?>" name="base" value="<?php echo $symbol ?>" <?php if (is_array($inst_currency_array) && in_array($symbol, $inst_currency_array)) echo "selected" ?>> <?php echo $symbol ?></option>
                <?php } ?>
            </select>
            <br> <br>
            <select class="wfr-currency_display" name="<?php echo $this->get_field_name('currency_display') ?>" id='<?php echo $this->get_field_id('currency_display') ?>'>
                <option value="code" <?php if (isset($currency_display) && $currency_display == 'code') { ?>selected<?php } ?>><?php _e('Code', 'wfr') ?></option>
                <option value="name" <?php if (isset($currency_display) && $currency_display == 'name') { ?>selected<?php } ?>><?php _e('Name', 'wfr') ?></option>
            </select>
            <label for="<?php echo $this->get_field_id('currency_display') ?>"><?php _e('Currency Display', 'wfr') ?></label> 
            <br> <br>
            <select class="wfr-theme" name="<?php echo $this->get_field_name('theme') ?>" id='<?php echo $this->get_field_id('theme') ?>'>
                <?php foreach ($themes as $theme) { ?>
                    <option value="<?php echo $theme ?>" <?php if (isset($inst_theme) && $inst_theme == $theme) { ?>selected<?php } ?>><?php echo ucfirst($theme) ?></option>
                <?php } ?>
            </select>
            <label for="<?php echo $this->get_field_id('theme') ?>"><?php _e('Theme', 'wfr') ?></label> <br> <br>
            <div class="wfr-preview loading"></div>
            <label><?php echo __("<b>Shortcode:</b> Use this shortcode to insert the table in any post or page."); ?></label><br>
            <textarea class="form-control wfr-shortcode" rows="5" onclick="this.select()" readonly></textarea>
        </div>
        <style>

        </style>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $wfr_currency_array = wfr_currency_array();
        $wfr_langs = wfr_languages();
        $wfr_themes = wfr_themes();
        //Sanitize and get values
        $title = sanitize_text_field($new_instance['title']);
        $calc = sanitize_text_field($new_instance['calc']);
        $language = sanitize_text_field($new_instance['language']);
        $amount = sanitize_text_field($new_instance['amount']);
        $base_currency = sanitize_text_field($new_instance['base_currency']);
        $currency_array = array();
        foreach ($new_instance['currency_array'] as $item) {
            $currency_array[] = sanitize_text_field($item);
        }
        $currency_display = sanitize_text_field($new_instance['currency_display']);
        $theme = sanitize_text_field($new_instance['theme']);
        //Validate
        if (!array_key_exists($language, $wfr_langs)) {
            $language = 'en';
        }
        if (!is_numeric($amount) || $amount <= 0) {
            $amount = 1;
        }
        if (!in_array($base_currency, $wfr_currency_array)) {
            $base_currency = 'USD';
        }

        if (!in_array($currency_display, array('code', 'name'))) {
            $currency_display = 'code';
        }
        if (!in_array($theme, $wfr_themes)) {
            $theme = 'default';
        }
        $validated_currency_array= array();
        foreach ($currency_array as $item) {
            if (!in_array($item, $wfr_currency_array))
                continue;
            $validated_currency_array[]=$item;
        }

        $instance['title'] = $title;
        $instance['calc'] = $calc;
        $instance['language'] = $language;
        $instance['base_currency'] = $base_currency;
        $instance['amount'] = $amount;
        $instance['currency_array'] = $validated_currency_array;
        $instance['currency_display'] = $currency_display;
        $instance['theme'] = $theme;

        return $instance;
    }

    function widget($args, $instance) {
        global $wfr_lang;
        extract($args);
        $currency_array = array();
        foreach ($instance['currency_array'] as $index => $item) {
            $currency_array[] = sanitize_text_field($item);
        }
        $cross = implode(',', $currency_array);

        $base = sanitize_text_field($instance['base_currency']);

        if (in_array($base, $currency_array)) {
            $all_rows_count = count($currency_array);
        } else {
            $all_rows_count = count($currency_array) + 1;
        }
        $height = $all_rows_count * 32 + 40 + 2;

        $title = apply_filters('widget_title', sanitize_text_field($instance['title']));
        $display = sanitize_text_field($instance['currency_display']);
        $amount = sanitize_text_field($instance['amount']);
        $theme = sanitize_text_field($instance['theme']);
        $language = sanitize_text_field($instance['language']);
        $calc = isset($instance['calc']) ? sanitize_text_field($instance['calc']) : 'on';

        $credit_an = $wfr_lang['link_anchor'];
        $credit_ln = $wfr_lang['link'];

        $code = '<!-- Exchange rates table widget starts here -->';
        $code .= '<div class="wfr-irame">';
        $code .= '<iframe src="https://www.worldforexrates.com/webmasters/widget-table-loader.php?base=' . $base . '&cross=' . $cross . '&display=' . $display . '&calc=' . $calc . '&amount=' . $amount . '&theme=' . $theme . '&lang=' . $language . '" width="100%" height ="' . $height . '" frameborder="0" scrolling="no"></iframe>';
        $code .= '<div style="font-size:12px;font-family:arial;text-align:right;"><a href="' . $credit_ln . '" style="text-decoration:none;color:#999;">' . $credit_an . '</a></div>';
        $code .= '</div>';
        $code .= '<!-- Exchange rates table widget ends here -->';

        echo $before_widget;
        if (!empty($title)) {
            echo $before_title . $title . $after_title;
        }
        echo $code;
        echo $after_widget;
    }

}
