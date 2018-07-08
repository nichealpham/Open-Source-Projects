jQuery(function ($) {
    $(document).on('ready', function () {
        // tabs description product
        $('.tp-tabs .tp-tabs-nav li').on('click', function () {
            var tab_id = $(this).attr('data-tab');
            $('.tp-tabs .tp-tabs-nav li').removeClass('active');

            $(".images-chart").html();
            $('.tp-tab-panel').removeClass('active');
            $(this).addClass('active');
            $('#' + tab_id).addClass('active');
        })


        // tabs description product
        $('.tp-tabs-vertical .tp-tabs-nav li').on('click', function () {

            var tab_id = $(this).attr('data-tab');

            $('.tp-tabs-vertical .tp-tabs-nav li').removeClass('active');
            $('.tp-tabs-vertical .tp-tab-panel').removeClass('active');

            $(this).addClass('active');
            $('#' + tab_id).addClass('active');
        })
    });
});
