(function ($) {

    var id;
    var popup;
    /**
     * Hover view on detail table
     * Open Tooltip : Stastics of images by sizes
     * 
     * @since 1.0.0
     */
    $(document).on("hover", ".badge", function (e) {
        e.preventDefault()
        id = $(this).attr("href");
        popup = new jBox('Mouse', {
            title: tp_image_optimizer_lang.main.detail_of + $(this).attr("href"),
            adjustPosition: true,
            ajax: {
                type: 'GET',
                attach: '.badge-' + id,
                url: tp_image_optimizer_admin_js.ajax_url,
                beforeSend: function () {
                    this.position();
                },
                data: {
                    action: 'get_statistics_detail',
                    id: id
                },
                reload : 'strict',
                success: function (html) {
                    popup.position();
                }
            },
            onCloseComplete: function () {
                this.destroy();
            },
        }).open();
        $(document).on('mouseleave', '.badge', function (event) {
            $(".jBox-Mouse").remove()
        })
    });
    /**
     * Optimize log box
     * Open or Collapse Sticky log
     * 
     * @since 1.0.0
     */

    $(document).on("click", ".sticky-header", function (e) {
        e.preventDefault();
        if ($(".io-sticky-notice").hasClass("collapse")) {
            $(".io-sticky-notice").removeClass("collapse");
        } else {
            $(".io-sticky-notice").addClass("collapse");
        }
    });
    /** 
     * Show statistics of image by size
     * Actived when user click to link of  Success Log
     * 
     * @since 1.0.0
     */
    $(document).on("click", ".io-sticky-notice li", function (event) {
        event.preventDefault();
        id = $(this).data('id');
        popup = new jBox('Modal', {
            title: tp_image_optimizer_lang.main.detail_of + id,
            adjustPosition: true,
            isolateScroll: true,
            ajax: {
                type: 'GET',
                url: tp_image_optimizer_admin_js.ajax_url,
                data: {
                    action: 'get_statistics_detail',
                    id: id
                },
                success: function (html) {
                },
            },
            closeOnClick: true,
            onCloseComplete: function () {
                this.destroy();
            },
        });
        popup.open();
    })

    /**
     * Show tooltip for FAQ
     * 
     * @param String name of tooltip
     * @since 2.0.0
     */
    var array_jbox = [];
    $(".faq-i").each(function () {
        var name = $(this).attr('data-val');
        var title = name + '_title';
        array_jbox[name] = new jBox('Tooltip', {
            title: tp_image_optimizer_lang.faq[title],
            content: tp_image_optimizer_lang.faq[name],
            attach: ".faq-" + name,
            width: 250,
        });
    })

    $(".faq-statistics_original").hover(function(){
        popup = new jBox('Mouse', {
            title: tp_image_optimizer_lang.faq['statistics_original_title'],
            content: tp_image_optimizer_lang.faq['statistics_original'],
            adjustPosition: true,
            width: 250,
            onCloseComplete: function () {
                this.destroy();
            },
        }).open();
        $(document).on('mouseleave', '.faq-statistics_original', function (event) {
            $(".jBox-Mouse").remove()
        })

    })

    // Prevent default click badge
    $(document).on("click", ".badge", function (e) {
        e.preventDefault();
    });
    /**
     * FAQ Help - Error tooltip
     * 
     * @since 1.0.2
     */
    $(document).on("hover", ".faq-compress_error", function (e) {
        e.preventDefault()
        if ($(this).data('log') != '') {
            popup = new jBox('Mouse', {
                title: tp_image_optimizer_lang.faq.compress_error_title,
                content: $(this).data('log'),
                adjustPosition: true,
                width: 250,
                onCloseComplete: function () {
                    this.destroy();
                },
            }).open();
            $(document).on('mouseleave', ".faq-compress_error", function (event) {
                $(".jBox-Mouse").remove()
            })
            return;
        }
        faq_tooltip('compress_error');
    });


    /**
     * Stastics from library
     * Ajax
     */

    new jBox('Tooltip', {
        attach: '.faq-local-statistics',
        title: tp_image_optimizer_lang.main.library,
        ajax: {
            type: 'GET',
            url: tp_image_optimizer_admin_js.ajax_url,
            data: {
                action: 'get_statistics_from_media',
                id: id
            },
            success: function (html) {
            },
        },
    });

    /**
     * Keep origin
     * Set select or unselect Original Compression
     * 
     * @since 1.0.8
     */
    $(document).on('change', '.keep_original', function () {
        var check = $("input#io-keep-original").prop("checked");
        $(".tpio-size-settings input[value='full']").prop("checked", check);
        $.ajax({
            type: 'POST',
            url: tp_image_optimizer_admin_js.ajax_url,
            data: {
                action: 'compress_origin_select',
                origin_compress: check
            },
            success: function (res) {
                if (!res.data.success) {
                    var $animate = $('.origin-check');
                    show_notice_switch_done($animate);
                }
            }
        });
    });

    /**
     * Update run in background option
     *
     * @since 2.0.3
     */
    $(document).on('change', '#run-in-background', function () {
        var check = $(this).prop("checked");
        console.log(check);
        $.ajax({
            type: 'POST',
            url: tp_image_optimizer_admin_js.ajax_url,
            data: {
                action: 'update_cronjob_selected',
                cronjob: check
            },
            success: function (res) {
                if (!res.data.success) {
                    var $animate = $('.run-in-background-check');
                    show_notice_switch_done($animate);
                }
            }
        });
    });


    /**
     * UPDATE SETTING SITE
     * 
     * @since 1.0.0
     */
    $(document).on('click', '.io-setting-api label', function (e) {
        var $this = $(this);

        if ($this.hasClass('disabled')) {
            return false;
        }
        var level = $(this).attr("for");
        level = level.replace("size_setting_", "");
        update_compress_option(level);
    });

    /**
     * Update compress option
     * 
     * @param int level
     */
    function update_compress_option(level) {
        var $result = $('.io-setting-api');
        $.ajax({
            type: 'POST',
            url: tp_image_optimizer_admin_js.ajax_url,
            data: {
                level: level,
                action: 'update_setting',
            },
            success: function (res) {
                if (res.success) {
                    show_notice_switch_done($result);
                }
            }
        });
    }

    /**
     * Display success update ajax notice for swicth
     * 
     */
    function show_notice_switch_done($class_notice) {
        var $class_done = $class_notice.find('.notice-switch-done');
        $class_done.html("");
        $class_done.append("<i class='ion-android-done'></i>");
        $class_done.addClass('animated');
        $class_done.removeClass('fadeInUp');
        $class_done.removeClass('zoomOut');

        $class_done.addClass('jello');
        $class_done.show();
        setTimeout(function () {
            $class_done.removeClass('jello');
            $class_done.addClass('zoomOut');
            //$class_done.hide();
        }, 1200)
    }

    /**
     * Choose keep original image
     * 
     * @since 2.0.0
     */
    $(document).on('change', '.choose-full', function () {
        var check = $(".tpio-size-settings input[value='full']").prop("checked");
        $("#io-keep-original").prop("checked", check);
    })

    $(document).on('ready', function () {
        var check = $(".tpio-size-settings input[value='full']").prop("checked");
        $("#io-keep-original").prop("checked", check);


        /**
         * Update list size compress
         * 
         * @category Ajax
         * @since 2.0.0
         */
        $('input.io-size-change').change(function () {
            var size_name = $(this).attr('data-size');
            var enable = $(this).prop("checked");
            $.ajax({
                type: 'POST',
                url: tp_image_optimizer_admin_js.ajax_url,
                data: {
                    action: 'update_size_progress',
                    enable: enable,
                    size_name: size_name
                },
                success: function (res) {
                    var $size_setting = $("#io-size-" + size_name).closest(".element");
                    show_notice_switch_done($size_setting);
                }
            })
        })
        /**
         * Social header effect
         *
         */
        $('.tp-header-icon').hover(function(){
            $(this).addClass('active');
            $('.tpui-header-right').addClass('active');
        });
        $('.tp-header-icon').on("mouseleave",function(){
            $('.tp-header-icon').removeClass('active');
            $('.tpui-header-right').removeClass('active');
        });

        $('.tp-tabs .tp-tabs-nav li.disable-chart').on('click', function (e) {
            e.preventDefault();
            var dashboard = $(".tp-tab-panel#tp-panel").html();
            $(".tp-io-tab").html(dashboard);

            var tab_id = $(this).attr('data-tab');
            $('.tp-tabs .tp-tabs-nav li').removeClass('active');

            $(".images-chart").html();
            $('.tp-tab-panel').removeClass('active');
            $(this).addClass('active');
            $('#' + tab_id).addClass('active');
        })
        /**
         * Click to tab
         *
         */
        $('.tp-tabs .tp-tabs-nav li.enable-chart').on('click', function () {
            if($(".tp-tab-panel#tp-panel").length > 0){
                $(".tp-io-tab").html("");
            } else{
                $(".tp-io-tab").addClass('tp-tab-panel active');
                $(".tp-io-tab.tp-tab-panel").attr('id','tp-panel');
                $(".tp-tab-panel").removeClass('tp-io-tab');
                $(".tp-tab-content").append("<div class='tp-io-tab'></div>");
                $(".tp-io-tab").html("");
            }
        });
    })
})(jQuery);