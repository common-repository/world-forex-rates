jQuery(document).ready(function ($) {
    $(document).on('change custom_change', ".wfr-container input,.wfr-container select", function () {
        var base, cross, display, calc, amount, language, theme;
        var container = $(this).closest('.wfr-container');
        base = container.find('.wfr-base_currency').val();
        display = container.find('.wfr-currency_display').val();
        if (container.find('.wfr-calc').is(":checked")) {
            calc = 'on';
        } else {
            calc = 'off'
        }
        amount = container.find('.wfr-amount').val();
        cross = container.find('.wfr-cross').val();
        language = container.find('.wfr-language').val();
        theme = container.find('.wfr-theme').val();
        var params = 'action=wfr_ajax_response&base=' + base + '&display=' + display + '&cross=' + cross + '&calc=' + calc + '&amount=' + amount + '&theme=' + theme + '&language=' + language;
        $.ajax({
            type: 'get',
            url: ajaxurl + '?' + params,
            dataType: 'json',
            success: function (data) {
                container.find('.wfr-shortcode').html(data.shortcode);
                container.find(".wfr-preview").html(data.code);
            },
            error: function () {

            },
            timeout: 10000
        });

    });
    $(".wfr-base_currency").trigger('custom_change');
    $('#widgets-right .wfr-cross').wfrselect();
    $(document).on('widget-updated', function (e, widget) {
        widget.find(".wfr-base_currency").trigger('custom_change');
        // "widget" represents jQuery object of the affected widget's DOM element
        widget.find('.wfr-cross').wfrselect();
    });
    $(document).on('widget-added', function (e, widget) {
        widget.find(".wfr-base_currency").trigger('custom_change');
        // "widget" represents jQuery object of the affected widget's DOM element
        widget.find('.wfr-cross').wfrselect();
    });
})
