(function ($) {
    $(document).on('ready', function () {

        /**
         * Size box
         * @constructor
         */
        function TP_Image_Optimizer() {
            this.$wrapper = $("#tp-image-optimizer");
            this.getTotal = function(){
               return  parseInt(this.$wrapper.data("total"));
            };
            this.getTotalPending = function(){
                return  parseInt(this.$wrapper.data("pre-optimize"));
            };
            this.updatePending = function(){
                var totalPending = this.getTotalPending();
                this.$wrapper.attr("data-pre-optimize", totalPending - 1);
            };
            return this;
        }

        var TP_Image_Optimizer = new TP_Image_Optimizer();
            /**
             * Optimizer
             *
             * @returns {Optimizer}
             * @constructor
             */
            function Optimizer() {
                this.$wrapper = $(".io-optimizer-wrapper");
                // Button
                this.$optimize_btn = this.$wrapper.find('#optimizer_btn');
                // Option box
                this.$option_optimizer = this.$wrapper.find('.option');
                // Cancel button
                this.$cancel_btn = this.$wrapper.find(".cancel_optimizer");
                // Log progress & Notify
                this.$show_log = this.$wrapper.find(".io-show-log");
                // Progress statistics ---------------------------------------------------------
                this.$notify_group = this.$wrapper.find('.io-notify-group');
                this.$total_image = this.$notify_group.find('.total-number');
                this.$optimized_number = this.$notify_group.find('.optimized-number');
                this.$error_detect = this.$notify_group.find(".io-error");
                this.$compressed_image = this.$notify_group.find('.compressed-image');
                this.$total_compress_images = this.$notify_group.find('.total-compressed-images');
                this.$label_optimizing_statistics = this.$notify_group.find('.label-statistic-optimizing');
                this.getOptimizedNumber = function () {
                    return parseInt(this.$optimized_number.html());
                };
                /**
                 * Update optimize statistic when processing
                 *
                 * @param run
                 * @param number_size
                 * @param error
                 */
                this.updateOptimizingStatistics = function (run, number_size, error) {
                    var total_img = TP_Image_Optimizer.getTotal();
                    if(run <= total_img){
                        this.$optimized_number.html(run);
                    }
                    this.$total_image.html(total_img);
                    this.$compressed_image.html(number_size * run);
                    this.$total_compress_images.html(number_size * total_img);
                    if (null !== error) {
                        this.$error_detect.html(error);
                    }
                };
                /**
                 * Reset optimize
                 */
                this.setStartOptimize = function (force) {
                    this.$notify_group.addClass("active");
                    var number_size = this.getNumberSelectedSize();
                    if (force) {
                        this.updateOptimizingStatistics(0, number_size, 0);
                        Progress_Bar.setPercent("0%");
                        return;
                    }
                    var total_img = TP_Image_Optimizer.getTotal();
                    var total_compressed_img = TP_Image_Optimizer.getTotalPending();
                    this.updateOptimizingStatistics(total_img - total_compressed_img, number_size, 0);
                    Progress_Bar.calPercentAndSet(total_img - total_compressed_img, total_img);
                };
                // Setting box
                this.$alert_box = this.$wrapper.find('.io_alert_box');

                this.getNumberSelectedSize = function () {
                    return parseInt(this.$compressed_image.data('number-selected-size'));
                };
                this.disableOption = function () {
                    this.$option_optimizer.addClass('disabled');
                    this.$option_optimizer.find('input').prop('disabled', true);
                };
                this.enableOption = function () {
                    this.$option_optimizer.removeClass('disabled');
                    this.$option_optimizer.find('input').prop('disabled', false);
                };
                return this;
            }

            var Optimizer = new Optimizer();

            /**
             * Statistics box
             *
             * @returns {Statistics}
             * @constructor
             */
            function Statistics() {
                this.$wrapper = $(".io-statistics-wrapper");
                this.$preload = this.$wrapper.find(".preload-statistics");
                this.$statistics = this.$wrapper.find(".service-statistics");
                this.$error_notice = this.$wrapper.find('.connect-err');
                // Statistics data
                this.$total_number_compressed = this.$wrapper.find('.total-image');
                this.$total_size_uploaded = this.$wrapper.find('.uploaded-size');
                this.$total_size_compressed = this.$wrapper.find('.compressed-size');
                this.$total_size_saving = this.$wrapper.find('.saving-size');
                /**
                 * Display statistic of this token from server
                 * @param responseData
                 */
                this.showStatisticFromServer = function (responseData) {
                    this.$statistics.fadeIn();
                    this.$total_number_compressed.html(responseData.total_image_success);
                    this.$total_size_uploaded.html(tp_image_optimizer_dislay_size(responseData.total_uploaded_success));
                    this.$total_size_compressed.html(tp_image_optimizer_dislay_size(responseData.total_compressed_success));
                    this.$total_size_saving.html(tp_image_optimizer_dislay_size(responseData.total_saving));

                    percent_success = parseInt(responseData.total_percent_success);
                    $('.tp-progress-circle').attr('data-progress', percent_success);
                    $(".progress-val").html(percent_success);
                    // Show chart
                    $('.io-statistics-wrapper .chart').addClass('active');
                    // Update chart
                    if (responseData.hasOwnProperty('user') && tp_image_optimizer_lang.hasOwnProperty(responseData.user)) {
                        $('.account_info .account_info__text').text(tp_image_optimizer_lang[responseData.user]);
                        if (responseData.user == 'pro') {
                            $('.account_info__icon').attr('class', 'account_info__icon account_info__icon--pro');
                        } else {
                            $('.account_info__icon').attr('class', 'account_info__icon');
                        }
                    }
                };
                this.showErrorNotice = function () {
                    this.$error_notice.fadeIn();
                };
                return this;
            }

            var Statistics = new Statistics();

            /**
             * Size box
             * @constructor
             */
            function Size_Setting() {
                this.$wrapper = $(".tpio-size-settings");
                this.disableOption = function () {
                    this.$wrapper.addClass('disabled');
                    this.$wrapper.find('input').prop('disabled', true);
                };
                this.enableOption = function () {
                    this.$wrapper.removeClass('disabled');
                    this.$wrapper.find('input').prop('disabled', false);
                };
                return this;
            }

            var Size_Setting = new Size_Setting();

            /**
             *  Sticky box
             *  @constructor
             */
            function Log() {
                this.$wrapper = $(".io-sticky-wrapper");
                this.$header = this.$wrapper.find(".sticky-header");
                this.$content = this.$wrapper.find(".sticky-content");
                this.$loading_box = this.$wrapper.find(".loading-sticky-box");
                this.$processing_text = this.$wrapper.find(".processing");
                this.$processing_id = this.$processing_text.find("span");
                this.show_current_notify = function () {
                    Log.$loading_box.css('display', 'block');
                };

                this.hide_loading = function () {
                    Log.$loading_box.css('display', 'none');
                };

                // Collapse sticky box
                this.collapse = function () {
                    this.hide_loading();
                    this.$wrapper.addClass('collapse');
                    Log.$header.html(tp_image_optimizer_lang.main.optimized + ' <a class=\"sticky-header-close\" href=\"#\">-</a>');
                };
                // Collapse sticky box
                this.open = function () {
                    Log.$header.html(tp_image_optimizer_lang.main.optimizing + ' <a class=\"sticky-header-close\" href=\"#\">-</a>');
                    this.$wrapper.removeClass('collapse');
                    this.$content.addClass("active");
                    // Open sticky box
                    this.$wrapper.addClass("active");
                    this.draggable();
                    // Show notify on Sticky box
                    this.show_current_notify();
                };
                this.close = function () {
                    this.$content.removeClass("active");
                    // Open sticky box
                    this.$wrapper.removeClass("active");
                };

                this.setProcessingID = function (id) {
                    this.$processing_id.html(id);
                };

                // Make sticky box to draggable
                this.draggable = function () {
                    Log.$wrapper.draggable(
                        {
                            axis: "x",
                            containment: "window"
                        }
                    );
                    Log.$wrapper.css('top', '');
                }
            }

            var Log = new Log();

            /**
             * Progress bar
             * @constructor
             */
            function Progress_Bar() {
                this.$wrapper = $(".tp-panel__progress-bar");
                this.$active = $(".tp-panel__progress-bar.active-cron");
                this.$progress_bar = this.$wrapper.find(".progress-bar");
                this.$progress_percent = this.$wrapper.find('.progress-percent');
                this.show = function () {
                    this.$wrapper.addClass('active-cron');
                };
                this.hide = function () {
                    this.$wrapper.removeClass('active-cron');
                };
                /**
                 * Set percent of progress bar
                 */
                this.setPercent = function (percent) {
                    this.$progress_bar.css('width', percent);
                    this.$progress_percent.html(percent);
                };
                /**
                 * Calculator percent and set it to progress bar
                 *
                 * @param run : Compressed
                 * @param total : Total image
                 */
                this.calPercentAndSet = function (run, total) {
                    if (total != 0) {
                        var percent = ( run / total) * 100 * 100;
                        percent = Math.round(percent);
                        percent = percent / 100;
                        if(percent > 100){
                            return;
                        }
                        percent = percent + "%";
                        this.setPercent(percent);
                        return;
                    }
                    this.setPercent("0%");
                }
            }

            var Progress_Bar = new Progress_Bar();
            /**
             * Update statistics from server
             *
             * @since 1.0.0
             */
            if ($('.io-statistics-wrapper').length) {
                var percent_success;
                $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'get_statistics_from_service'
                    },
                    success: function (response) {
                        Statistics.$preload.fadeOut(); // Hide preload

                        if (!response.success) {
                            Statistics.$error_notice.html(response.data).fadeIn();
                            Statistics.$statistics.hide();
                            $(".data-chart").hide();
                        } else if (response.success && response.data.hasOwnProperty('key')) {
                            var r = confirm(tp_image_optimizer_lang.confirm_fix_token);
                            if (r) {
                                location.reload();
                            }
                        } else {
                            var responseData = response.data;
                            Statistics.showStatisticFromServer(responseData); // Update statistics from server
                        }
                    }
                });
            }

            /**
             * REFRESH IMAGE LIST
             *
             * @since 1.0.0
             */
            $(document).on('click', '.refresh-library', function (e) {
                e.preventDefault();
                /**
                 * Request clear image library
                 */
                $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'clear_image_library'
                    },
                    complete: function (response) {
                        if (!response.hasOwnProperty('responseJSON')) {
                            return;
                        }
                    }
                });
                var $this = $(this);
                if ($this.attr('disabled') == undefined) {
                    $this.text(tp_image_optimizer_lang.wait);
                    $this.attr('disabled', 'disabled');
                    $('.count-media, .update-image .load-speeding-wheel').css('display', 'inline-block');
                    add_image_to_plugin(0);
                }

            });

            /**
             * Accept install
             *
             * @since 1.0.0
             */
            var $install_progressbar = $('.tp-installer.tp-installer--progressbar .progress-bar');
            $(document).on('click', '#accept-install', function (e) {
                e.preventDefault();
                var $this = $(this);
                if ($this.hasClass('disabled')) {
                    return false;
                }
                if (false == navigator.onLine) {
                    $(".install-required").show();
                    return;
                }
                var Ajax = $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'get_token',
                    },
                    beforeSend: function () {
                        //$(".tp-installer .progress").show();
                        $(".tp-installer--progressbar .progress").fadeIn();
                        $this.addClass('disabled');
                        $this.text(tp_image_optimizer_lang.wait);
                        $install_progressbar.css('width', '0%').removeClass('progress-bar--error');
                    }
                }).done(function (res) {
                    if (!res.success) {
                        $('.install-required').html(res.data).fadeIn();
                        Ajax.abort();
                        $this.removeClass('disabled').text(tp_image_optimizer_lang.getstarted);
                        $install_progressbar.css('width', '100%').addClass('progress-bar--error');
                        $install_progressbar.find('.progress-percent').text('Error');
                    } else {
                        $install_progressbar.css('width', '0%');
                        $install_progressbar.find('.progress-percent').text('0%');
                        setTimeout(function () {
                            add_image_to_plugin(0);
                        }, 1500);
                    }
                });
            });

            /**
             * Add image to plugin
             * Refresh library
             *
             * @param int count_flag Pagination
             * @since 1.0.3
             */
            function add_image_to_plugin(count_flag) {
                var total_image = TP_Image_Optimizer.getTotal(); // Total image
                var number = total_image / 800 + 1;
                var number_percent = (100 / (number)).toFixed(0);
                var percent_update;
                $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'recheck_library',
                        paged: count_flag
                    }
                }).done(function (res) {
                    if (res.success) {
                        percent_update = number_percent * count_flag;
                        if (percent_update < 100) {
                            $install_progressbar.css('width', percent_update + '%');
                            $install_progressbar.find('.progress-percent').text(percent_update + '%');
                        }
                        count_flag++;
                        if (count_flag < number) {
                            add_image_to_plugin(count_flag);
                        } else {
                            setTimeout(set_status_to_installed, 1000);
                        }
                    }
                });
            }

            /**
             * Set status plugin to installed
             *
             * @since 1.0.3
             */
            function set_status_to_installed() {
                $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'set_status_to_installed'
                    }
                }).done(function () {
                    $install_progressbar.css('width', '100%');
                    $install_progressbar.find('.progress-percent').text('100%');
                    setTimeout(function () {
                        location.reload(); // Reload the page.
                    }, 2000);
                });
            }


            var content_append;
            var text;

            /**
             * Append success image to logbox
             *
             * @since 1.0.0
             */
            function append_success_compressed_to_log(attachment_id) {
                text = tp_image_optimizer_lang.success.optimized + attachment_id;
                content_append = "<li data-id=" + attachment_id + ">"
                    + "<span class='sticky-number-id'></span>"
                    + "<a href ='#' data-id=" + attachment_id + ">" + text + "</a>"
                    + "</li>";
                Log.$content.find("ul").prepend(content_append);
            }

            /**
             *  Add error on compress progress to log box
             *
             * @param {type} size
             * @returns {Object.size}
             * @since 1.0.0
             */
            function log_error_on_compress_progress(attachment_id, log) {
                content_append = "<li data-id=" + attachment_id + " >"
                    + "<span class='sticky-number-id error'></span>"
                    + "<a href ='#' data-id=" + attachment_id + "> #" + attachment_id + ' - ' + log + "</a>"
                    + "</li>";
                Log.$content.find("ul").prepend(content_append);
            }


            /**
             * Display image size
             *
             * @param type $size
             * @return String Display size ( Byte, KB, MB )
             */
            function tp_image_optimizer_dislay_size(size) {

                var display_size;
                if (size < 1024) {
                    display_size = size + tp_image_optimizer_lang.size.B;
                } else if (size < 1024 * 1024) {
                    size = (size / (1024)).toFixed(2);
                    display_size = size + tp_image_optimizer_lang.size.KB;
                } else {
                    size = (size / (1024 * 1024)).toFixed(2);
                    display_size = size + tp_image_optimizer_lang.size.MB;
                }
                return display_size;
            }


            /**
             * Compress option for specific image
             *
             * @since 1.0.1
             */
            $(document).on('click', '.single-compress', function (e) {
                e.preventDefault();
                $(this).remove();
                var id = $(this).attr('href');
                $('.compress-' + id + ' .load-speeding-wheel').addClass('active');
                $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'manual_optimizer',
                        id: id,
                    },
                    complete: function (result) {
                        if (result.hasOwnProperty('responseJSON')) {
                            if (result.responseJSON.success) {
                                if (result.responseJSON.data.full_detail != null) {
                                    update_statistics_detail_after_optimized(id, result.responseJSON.data.full_detail.old_size, result.responseJSON.data.full_detail.new_size);
                                } else {
                                    update_statistics_detail_after_optimized(id, 0, 0);
                                }
                            } else {
                                add_status_for_image(id, false, result.responseJSON.data.log);
                            }
                        }
                        delete id;
                    }
                });
            })

            /**
             * Update statistics for an image after ajax completed
             *
             * @param int attachment_id
             * @param double orginal_size
             * @param double current_size
             * @returns void
             * @since 1.0.2
             */
            function update_statistics_detail_after_optimized(attachment_id, original_size, current_size) {
                // Calculator
                var new_size = tp_image_optimizer_dislay_size(current_size);
                var saving = original_size - current_size;
                var percent_raw = ((saving / original_size) * 100).toFixed(2);
                // Count saving
                var saving = original_size - current_size;
                if (percent_raw > 1) {
                    // New size
                    $('.current_size .table-detail-' + attachment_id).html(new_size);
                    // Saving
                    $('.detail-saving-' + attachment_id).html(tp_image_optimizer_dislay_size(saving));
                    var percent = ((saving / original_size) * 100).toFixed(2);
                    percent = percent + '%'
                    $('.percent-saving-' + attachment_id).html(percent);
                }
                // Show success icon
                $('.compress-' + attachment_id).html('');
                $('.compress-' + attachment_id).append('<span class="success-optimize"></span>');
            }

            /**
             * Add status for image
             *
             * @param int attachment_id
             * @param boolean Success or error
             * @param String Error log
             * @since 1.0.8
             */
            function add_status_for_image(attachment_id, success, error_log) {
                $('.compress-' + attachment_id).html('');
                if (success) {
                    $('.compress-' + attachment_id).append('<span class="success-optimize"></span>');
                } else {
                    $('.compress-' + attachment_id).append('<span class="faq-compress_error" data-log="' + error_log + '"></span>');
                }
            }


            /**
             * Show statistics for cronjob work
             * - Update progress bar
             * - Active log bar
             */
            if (Progress_Bar.$active.length && $("#run-in-background:checked").length) {
                Log.open();
                get_statistics_for_cron();
            } else {
                stop_optimize();
            }

            /**
             * Start running cronjob optimizer
             *
             * @since 1.0.0
             */

            $(document).on('click', '#optimizer_btn', function (e) {
                e.preventDefault();
                var force = 0;
                if ($("#io-reoptimized:checked").length) {
                    force = 1;
                }
                Optimizer.setStartOptimize(force);
                if ($("#run-in-background").prop("checked")) {
                    run_as_bg_service();
                } else {
                    run_optimize_ajax();
                }
            });

            /**
             * Run compress as background service
             * @since 2.1.0
             */
            function run_as_bg_service() {
                // Optimizer group
                var force = 0;// Force optimizer
                if ($("#io-reoptimized:checked").length) {
                    force = 1;
                }
                // Clear option
                $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'clear_when_cronjob_done'
                    },
                    complete: function (res, data) {
                        start_optimizer();
                    }
                });
                show_notice_can_close(); // Show notice cronjob running as background service
                if (Progress_Bar.length) {
                    Progress_Bar.show();
                }

                $(".result_alert").fadeOut();

                $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'cron_optimize_image',
                        force: force
                    },
                    complete: function () {
                        Log.open();
                        Optimizer.$notify_group.css('display', 'block');
                        get_statistics_for_cron();
                    }
                })
            }

            /**
             *
             * @since 2.1
             */
            var list_size;
            var count_list_size;
            var total_image = 0;
            var force = false; // Force compress
            var max_image_compress;
            var total_uncompress;

            function run_optimize_ajax() {
                start_optimizer(); // Style for optimizer
                $(".tp-image-optimizer").data('process', 'true'); // Set status page to process - Useful to prevent reload
                if ($("input[name='force-re-optiomizer']").prop("checked")) {
                    force = true;
                }
                total_image = TP_Image_Optimizer.getTotal();
                total_uncompress = TP_Image_Optimizer.getTotalPending();
                Log.open(); // OPEN LOG BOX
                $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'get_img_optimizer',
                        force: force
                    },
                    beforeSend: function () {
                        Progress_Bar.calPercentAndSet(total_image - total_uncompress, total_image);
                    }
                }).done(function (res) {
                    data = res.data;
                    list_size = data.list_size;
                    count_list_size = list_size.length;
                    // Optimizer with list image
                    var total_image_pending = parseInt(data.count);

                    if (force) {
                        max_image_compress = total_image;  // Restart
                    } else {
                        max_image_compress = total_image_pending; // Continue
                        if(total_image_pending ==0){
                            style_for_complete_optimize();
                            return;
                        }
                    }
                    tp_image_optimizer(0);
                });
            }

            var xhr;
            var data;
            var error_count;

            /**
             * Ajax Optimizer for an attachment image
             *
             * @since 1.0.0
             */
            function tp_image_optimizer(number) {
                var success_flag = true;
                error_count = parseInt($(".io-error").html());
                xhr = $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'optimize_image',
                        start: number,
                        force: force,
                        list_size: list_size,
                        error_count: error_count
                    },
                    success: function (result) {
                        data = result.data;
                        // If error
                        if (!result.success) {
                            // IF detect error on load Attachment ID on SQL - Reload ID
                            if (data.reload) {
                                success_flag = false;
                                tp_image_optimizer(number);
                            } else {
                                // If have an error, logging it to log bar
                                Optimizer.$error_detect.html(error_count + 1);
                                // Append this error to Log
                                $error_log = tp_image_optimizer_lang.error.undefined;
                                if (data.hasOwnProperty('log')) {
                                    $error_log = data.log;
                                }
                                Optimizer.$show_log.html($error_log);
                                log_error_on_compress_progress(data.id, $error_log);
                                add_status_for_image(data.id, result.success, $error_log);
                            }
                        } else {
                            // Update statistics for detail table
                            if (data.hasOwnProperty('full_detail')) {
                                update_style_for_success_compress_image(data.id, data.full_detail.old_size, data.full_detail.new_size);
                                if (force !== true) {
                                    TP_Image_Optimizer.updatePending();
                                }
                            }
                        }
                        // STYLE PROGRESS BAR AND OPTIMIZING STATISTICS
                        var total_compressed = total_image - total_uncompress + number + 1;
                        if (force === true) {
                            total_compressed = number + 1;
                        }
                        Progress_Bar.calPercentAndSet(total_compressed, total_image); // Update percent
                        Optimizer.updateOptimizingStatistics(total_compressed, count_list_size, error_count); // Update optimize statistics
                    }
                }).done(function (res) {
                    if (!res.success && res.status == 404) {
                        return false;
                    }
                    if (success_flag == true) {
                        number++;
                        if (number < max_image_compress) {
                            tp_image_optimizer(number); // Continue compress
                        } else {
                            style_for_complete_optimize(); // Complete compress
                        }
                    }
                });
            }

            /**
             * Style for complete optimize
             *
             * @since 2.1
             */
            function style_for_complete_optimize() {
                Log.hide_loading();
                stop_optimize(true);
                // Hide log box
                Log.collapse();

                Optimizer.$label_optimizing_statistics.html(tp_image_optimizer_lang.success.processed);
            }

            /**
             * Style for success compressed image
             *
             * @param id
             * @param old_size
             * @param new_size
             */
            function update_style_for_success_compress_image(id, old_size, new_size) {
                add_status_for_image(id, true, '');
                update_statistics_detail_after_optimized(id, old_size, new_size);
                // Show log for image
                append_success_compressed_to_log(id);
                // Update for Image statistics
                update_statistics_detail_after_optimized(id, old_size, new_size);
            }

            /**
             * Event CANCEL when optimizing image
             *
             * @since 1.0.0
             */
            $(document).on("click", '#cancel_optimizer', function (e) {
                Optimizer.$show_log.html(tp_image_optimizer_lang.main.pause);
                Optimizer.$label_optimizing_statistics.html(tp_image_optimizer_lang.success.processed);
                if (!$("#run-in-background").prop("checked")) {
                    xhr.abort();
                    style_for_complete_optimize();
                    return;
                }

                // Set status page to stop process - Useful to prevent reload
                $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'cancel_cronjob',
                    },
                    beforeSend: function () {
                        // Change text to loading cancel
                        $("#cancel_optimizer").attr("value", tp_image_optimizer_lang.load.wait);
                        setTimeout(3000);
                    },
                    complete: function (res) {
                        if (res.hasOwnProperty('responseJSON')) {
                            $("#cancel_optimizer").attr("value",tp_image_optimizer_lang.main.stop);
                        }
                        style_for_complete_optimize();
                    }
                });

            });

            /**
             * Get statistics for cronjob
             * Update per second
             *
             * @category Cronjob
             * @since 1.0.8
             */
            function get_statistics_for_cron() {
                // Run cronjob
                if (!$("#run-in-background").prop("checked")) {
                    return;
                }
                show_notice_can_close(); // CAN CLOSE
                $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'get_statistics_for_cron'
                    },
                    complete: function (response) {
                        if (response.hasOwnProperty('responseJSON')) {
                            var response_data = response.responseJSON.data;
                            if (response_data.processing == '') {
                                Log.$processing_text.hide();
                            } else {
                                Log.$processing_text.show();
                                Log.setProcessingID(response_data.processing);
                            }
                            var total_image = response_data.total_image; // Count total image
                            var total_error = response_data.total_error; // Count total detect error
                            var run = response_data.run; // Count number image processed done
                            var number_size = Optimizer.getNumberSelectedSize();
                            var total_number = response_data.total_image;
                            Optimizer.$total_image.html(total_number);
                            Optimizer.$total_compress_images.html(total_number * number_size);
                            if (run != 0) {
                                Optimizer.disableOption();
                                Size_Setting.disableOption();
                                Optimizer.updateOptimizingStatistics(run, number_size, total_error);
                            }
                            // Update progress bar
                            var percent = response_data.percent + "%";
                            Progress_Bar.setPercent(percent);

                            // Detail progress
                            var attachment_id = response_data.id_completed; // ID attachment procressed complete
                            var error_log = response_data.last_error_log; // Error log if last_status = false
                            // Size
                            var old_size = 1;
                            var new_size = 1;
                            var success_detail = null;
                            if (response_data.hasOwnProperty('success_detail') && response_data.success_detail != null) {
                                success_detail = response_data.success_detail;
                                old_size = success_detail.old_size; // Size of image before compress
                                new_size = success_detail.new_size; // Size of imaeg after compress
                            }

                            if (Progress_Bar.$progress_bar.data('compressed') != attachment_id) {
                                // Append success compress image to log box
                                if ((attachment_id) && (attachment_id != 'N/A')) {
                                    if ((success_detail == null) && (response_data.last_error_log != "")) {
                                        log_error_on_compress_progress(attachment_id, error_log);
                                        add_status_for_image(attachment_id, false, error_log);
                                    } else if (response_data.last_status == '1' && (success_detail != null) && (success_detail.success)) {
                                        // Show log for image
                                        update_style_for_success_compress_image(attachment_id, old_size, new_size);
                                        // Update HTML for Un-compress statistics
                                        if (response.responseJSON.data.force != '1') {
                                            var $uncompress_image = $(".io-total-uncompress");
                                            var uncompress = parseInt($uncompress_image.html());
                                            uncompress = uncompress - 1;
                                            if (uncompress >= 0) {
                                                $uncompress_image.html(uncompress);
                                            }
                                        }
                                    }
                                }
                                Progress_Bar.$progress_bar.data('compressed', attachment_id);
                            }

                            if (parseInt(response_data.cron) != 0) {
                                setTimeout(function () {
                                    get_statistics_for_cron();
                                }, 1000);
                            } else {
                                style_for_complete_optimize(); // STYLE FOR COMPLETE OPTIMIZE
                                if (Optimizer.getOptimizedNumber() < total_image) {
                                    Optimizer.updateOptimizingStatistics(Optimizer.getOptimizedNumber() + 1, number_size, null);
                                }
                                if (run == total_image) {
                                    if (total_error == 0) {
                                        display_finish_compress_notice(1);
                                    } else {
                                        display_finish_compress_notice(2);
                                    }
                                } else {
                                    display_finish_compress_notice(3);
                                }
                                stop_optimize();
                            }
                        } else {
                            setTimeout(get_statistics_for_cron(), 1000);
                        }
                    }
                })
            }

            /**
             * Display finish notice
             *
             * @param int success
             * @returns void
             * @since 1.0.8
             */
            function display_finish_compress_notice(success) {
                switch (success) {
                    case 1 :
                        var $facebook = "<a href ='//www.facebook.com/sharer/sharer.php?u=https%3A//wordpress.org/plugins/tp-image-optimizer/' target='_blank'><i class='ion-social-facebook'></i></a>";
                        var $twitter = "<a href ='//twitter.com/home?status=%23imageoptimizer,%20%23wordpress%0Ahttps%3A//wordpress.org/plugins/tp-image-optimizer/' target='_blank'><i class='ion-social-twitter'></i></a>";
                        var $gplus = "<a href ='//plus.google.com/share?url=https%3A//wordpress.org/plugins/tp-image-optimizer/' target='_blank'><i class='ion-social-googleplus-outline'></i></a>";
                        var $share = "<div class='share'>" + tp_image_optimizer_lang.success.share + $facebook + " " + $twitter + " " + $gplus + "</div>";

                        Optimizer.$alert_box.html('<div class="result_alert" style="display: block;"><i class="ion-ios-checkmark"></i> ' + tp_image_optimizer_lang.success.done + $share + '</div>');
                        break;
                    case 2 :
                        Optimizer.$alert_box.html('<div class="result_alert result_alert--warning" style="display: block;">' + tp_image_optimizer_lang.error.detect + '</div>');
                        break;
                    case 3 :
                        Optimizer.$alert_box.html('<div class="result_alert result_alert--warning" style="display: block;">' + tp_image_optimizer_lang.cron.stop + '</div>');
                        break;
                    default:
                        Optimizer.$alert_box.html('<div class="result_alert result_alert--warning" style="display: block;">' + tp_image_optimizer_lang.success.finish + '</div>');
                }
            }

            /**
             * Switch optimize status to no activity
             * @since 1.0.0
             */
            function stop_optimize(){
                Optimizer.$optimize_btn.addClass('is-active');
                Optimizer.$cancel_btn.removeClass('is-active');
                Optimizer.enableOption();
                Size_Setting.enableOption();
                Progress_Bar.hide();
            }
            function start_optimizer(){
                Progress_Bar.$progress_bar.css("width","0%");
                $(".progress-percent").html("0%");
                Optimizer.$optimize_btn.removeClass('is-active');
                Optimizer.$cancel_btn.addClass('is-active');
                Optimizer.disableOption();
                Size_Setting.disableOption();
                Progress_Bar.show();
            }

            /**
             *
             * @chart Chart
             */
            var rangeChart;
            /**
             * Render range chart when open plugin
             *
             * @since 2.0.0
             */
            $(document).ready(function () {
                if ($("#tp-panel .data-chart").length) { // Only run when tab active
                    var range = $("input[name='select-range']:checked").val();
                    var title_range = $("input[name='select-range']:checked").attr('data-title');
                    var $dataChart = $("#dataChart");
                    $.ajax({
                        type: 'POST',
                        url: tp_image_optimizer_admin_js.ajax_url,
                        data: {
                            action: 'show_compress_by_date',
                            range: range
                        },
                        beforeSend: function () {
                            $dataChart.hide();
                        },
                        success: function (res) {
                            $dataChart.show();
                            $(".chartPreload").hide();
                            if (res.success) {
                                var data_date = [];
                                for (var i = 1; i < res.data.total; i++) {
                                    data_date.push(i);
                                }
                                draw_range_chart(res.data.data, title_range); // Draw filter range chart
                            }
                        }
                    });
                }
            })
            /**
             * Update range chart when choose other range
             *
             * @since 2.0.0
             */
            $(document).on('click', ".select-range", function () {
                var chart_range = $(this).attr("data-chart");
                var title_range = $("input[name='select-range']:checked").attr('data-title');
                $("#dataChart").html();
                $(".chartjs-hidden-iframe").remove();

                $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'update_range_chart',
                        range: chart_range,
                        title: title_range
                    },
                    beforeSend: function () {
                        $(".chartPreload").show();// Preload
                    },
                    success: function (res) {
                        if (res.success) {
                            setTimeout(function () {
                                $(".chartPreload").hide();
                                $("#dataChart").show();
                                draw_range_chart(res.data.data, title_range); // Draw filter range chart
                            }, 500)
                        }
                    }
                })
            })

            /**
             * Fix for tab
             * Reload tab when active tab
             *
             */
            $(document).on('click', ".enable-chart", function () {
                $(".images-chart").html('<div class="chartPreload" ><div id="loader"><div class="cssload-loader"><div></div><div></div><div></div><div></div><div></div> </div></div></div><canvas id="dataChart" width="800" height="600"></canvas>');
                $("#dataChart").html();
                $(".chartjs-hidden-iframe").remove();
                $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'update_range_chart'
                    },
                    beforeSend: function () {
                        $(".chartPreload").show(); // Preload
                    },
                    success: function (res) {
                        var title = $("#tp-wrapper-panel").attr("data-title");
                        var range = $("#tp-wrapper-panel").attr("data-range");
                        if (title == '') {
                            title = 'Last 30 day';
                        }
                        if (range == '') {
                            range = 'last_30';
                        }
                        $("input#" + range).prop("checked", true);
                        if (res.success) {
                            setTimeout(function () {
                                $(".chartPreload").hide();
                                $("#dataChart").show();
                                draw_range_chart(res.data.data, title); // Draw filter range chart
                            }, 500)
                        }
                    }
                })
            })

            /**
             * Draw range chart
             *
             * @param Object data
             * @returns
             * @since 2.0.0
             */
            function draw_range_chart(data, title) {
                var ctx = document.getElementById("dataChart").getContext('2d');

                rangeChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.list_date,
                        datasets: [{
                            label: title,
                            borderColor: "#297ff6",
                            data: data.list_images,
                            backgroundColor: '#E9F2FE',
                            fill: true,
                        }],
                    },
                    options: {
                        //responsive:true
                    }
                });
            }

            /**
             * Show notice : User can close window when processing
             *
             * @since 2.0.0
             */
            function show_notice_can_close() {
                // Show notice
                $(".tp-io-notice-bar").html("<div class='tp-notify xs-mb-5'> " + tp_image_optimizer_lang.main.can_close_window + " </div>'");
            }

            /*
             * Uninstall
             * Not show on default panel
             * Usefull for developer
             *
             * @since 1.0.1
             */
            $(document).on('click', '#uninstall', function (e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: tp_image_optimizer_admin_js.ajax_url,
                    data: {
                        action: 'uninstall',
                    },
                    beforeSend: function () {
                        $('.io-sizes-option-wrapper .spinner').addClass('is-active');
                    },
                    success: function () {
                        $('.io-sizes-option-wrapper .spinner').removeClass('is-active');
                    }

                }).done(function () {
                    setTimeout(function () {
                        location.reload(); // Reload the page.
                    }, 2000);
                })
            })

        }
    );
})(jQuery);