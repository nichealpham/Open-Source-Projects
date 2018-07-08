<?php
/**
 * METABOX GENERATOR
 * Create metabox
 *
 * @class    TP_Image_Optimizer_Metabox
 * @package  TP_Image_Optimizer/Classes
 * @category Class
 * @version  1.0
 *
 */
if (!defined('TP_IMAGE_OPTIMIZER_BASE')) {
    exit; // Exit if accessed directly
}
if (!class_exists('TP_Image_Optimizer_Metabox')) {

    class TP_Image_Optimizer_Metabox {

        public $image_work;
        private $size_list;

        /**
         * Meta box detail table
         *
         * @since 1.0.0
         */
        public function metabox_detail() {
            $flag = true;

            $list_img = array();
            $image = new TP_Image_Optimizer_Image();

            $table = $image->display_table_image();

            if ($table == "nodata") {
                $flag = false;
            }
            tp_image_optimizer_template('panel/detail', array(
                'list_img' => $list_img, 'table' => $table, 'flag' => $flag
            ));
        }

        /**
         * Show progress bar when running
         *
         * @since 2.0.0
         */
        private function show_progress() {
            // CRON WORK
            $check_cron = get_option('tpio_cron_status');
            $check_cron = intval($check_cron);
            $percent_cron = 0;
            if ($check_cron) {
                $total_image_cron = get_option('tpio_cron_total');
                if ($total_image_cron > 0) {
                    $current_cron_image = get_option('tpio_cron_run');
                    $percent_cron = ($current_cron_image * 100) / $total_image_cron;
                }
            }

            $optimize_sizes = get_option('tp_image_optimizer_sizes');
            $this->size_list = explode(",", $optimize_sizes);
            ?>
            <div class="tp-panel__progress-bar <?php
            if ($check_cron): echo "active-cron hehehehe";
            endif;
            ?>">
                <div class="progress_wrap">
                    <div class="progress">
                        <div class="progress-bar">
                            <span class="progress-percent"><?php echo $percent_cron; ?>%</span>
                        </div>
                    </div>
                </div>

            </div>
            <?php
        }

        /**
         * Metabox top content
         *
         * @since 1.0.0
         */
        public function metabox_do_optimize_box() {
            ?>
            <div class='top'>
                <?php
                $statistics = new TP_Image_Optimizer_Statistics();
                $check_cron = get_option('tpio_cron_status');
                $check_cron = intval($check_cron);
                $run_in_background = get_option('tpio_cronjob_selected');

                $data = array(
                    'total_file' => $statistics->get_total_cron_image(),
                    'total_error' => $statistics->get_number_image_error(),
                    'total_selected_size' => $statistics->get_total_selected_size(),
                    'cron' => $check_cron,
                    'run_in_background' => $run_in_background
                );
                tp_image_optimizer_template('panel/statistics_running', $data);
                ?>
                <div class='account-info'>
                    <?php $this->account(); ?>
                </div>
            </div> <?php
            // PROGRESS BAR
            $this->show_progress();
            // ACTION
            tp_image_optimizer_template('panel/optimizer', $data);
            ?>
            <?php
        }

        /**
         * Metabox stastic box
         *
         * Data statistics
         * Range filter chart
         *
         * @since 2.0.0
         */
        public function metabox_service_statistics() {
            // Statistics
            $image = new TP_Image_Optimizer_Image();
            $statistics = new TP_Image_Optimizer_Statistics();
            $total_image_with_size = count($this->size_list) * $statistics->get_total_image();
            // Statistics
            $data_statistics = array(
                'total_current_in_media' => $image->count_attachment_file(), 'total_file' => $statistics->get_total_image(), 'total_uncompress' => $statistics->get_total_uncompress_img(), 'total_compressed' => $statistics->get_total_compressed_img(), 'count_selected_size' => $total_image_with_size,
            );
            if ($data_statistics['total_current_in_media'] != $data_statistics['total_file']) {
                tp_image_optimizer_template('panel/sync', $data_statistics);
            }
            ?>
            <div class='top-bar'>
                <?php
                tp_image_optimizer_template('panel/statistics', $data_statistics);
                // DATA RANGE FILTER CHART
                $this->show_range_filter_chart();
                ?>
            </div>
            <?php


        }

        /**
         * Display data chart
         *
         * @since 2.0.0
         */
        public function show_range_filter_chart() {
            $data_range = array(
                'last_30' => esc_html__("Last 30 day", 'tp-image-optimizer'), 'current_month' => esc_html__("Current month", 'tp-image-optimizer'), 'last_month' => esc_html__("Last month", 'tp-image-optimizer')
            );
            $option_range = get_option("tpio_range");
            if (empty($option_range)) {
                $option_range = 'last_30';
            }

            $data_chart = array(
                'data_range' => $data_range, 'option_range' => $option_range
            );
            tp_image_optimizer_template('panel/range-chart', $data_chart);
        }

        /**
         * Metabox size setting
         *
         * @since 1.0.0
         */
        public function metabox_get_size() {
            $list_img_size = get_intermediate_image_sizes();
            tp_image_optimizer_template('panel/sizes', array('sizes' => $list_img_size, 'optimize_sizes' => $this->size_list));
        }

        /**
         * Setting metabox
         *
         * @since 1.0.0
         */
        public function metabox_setting() {

            $option_select = array(
                1 => esc_attr__('Lower', 'tp-image-optimizer'), 2 => esc_attr__('Medium', 'tp-image-optimizer'), 3 => esc_attr__('High (Recommend)', 'tp-image-optimizer'), 4 => esc_attr__('Very high', 'tp-image-optimizer'),
            );
            $option_compress = get_option('tp_image_optimizer_compress_level');
            $data = array(
                'option' => $option_select, 'compress' => $option_compress
            );
            tp_image_optimizer_template('panel/settings', $data);
        }

        /**
         * Sticky box - Help box to fix error
         *
         * @since 1.0.0
         */
        public function sticky_box_show() {
            $db = new TP_Image_Optimizer_Table();

            $list_error = $db->get_list_error_image();
            $data = array();
            tp_image_optimizer_template('sticky-box', $data);
        }

        /*
         * Register form
         *
         * @since 1.0.6
         */

        public function metabox_register() {
            $data = array();
            tp_image_optimizer_template('panel/register', $data);
        }

        /**
         * Display coupon metabox
         *
         * @since 1.0.7
         */
        public function metabox_coupon() {
            tp_image_optimizer_template('panel/coupon');
        }

        /**
         * Display account info
         *
         * @since 1.0.7
         */
        public function metabox_account_info() {
            tp_image_optimizer_template('panel/account');
        }

        public function setting() {
            $this->metabox_setting();
            $this->metabox_get_size();
            $this->metabox_coupon();
        }

        public function heading() {
            $this->metabox_do_optimize_box();
            $this->metabox_service_statistics();
        }

        public function content() {
            $this->metabox_detail();
        }

        public function account() {
            $this->metabox_account_info();
        }

    }

}

new TP_Image_Optimizer_Metabox();

