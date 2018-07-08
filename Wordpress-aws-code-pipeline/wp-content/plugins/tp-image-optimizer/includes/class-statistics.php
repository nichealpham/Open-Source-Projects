<?php

if (!defined('TP_IMAGE_OPTIMIZER_BASE')) {
    exit; // Exit if accessed directly
}

/**
 * STASTICS
 * Provide method to get statistics of Optimize service.
 * 
 * @class TP_Image_Optimizer_Statistics
 * @package TP_Image_Optimizer/Classes
 * @category Class
 * @version 1.0
 */
if (!class_exists('TP_Image_Optimizer_Statistics')) {

    class TP_Image_Optimizer_Statistics {

        /**
         * @since 1.0.0
         */
        public function get_total_attachment_img() {
            $image_list = new TP_Image_Optimizer_Image();
            return $image_list->count_attachment_file();
        }

        /**
         * 
         * @return int Total image
         * @since 1.0.0
         */
        public function get_total_image() {
            $tb = new TP_Image_Optimizer_Table();
            return $tb->get_total_image();
        }
        
        /**
         * Get total cron image
         * 
         * @return int Total image
         * @since 1.0.0
         */
        public function get_total_cron_image() {
            return get_option('tpio_cron_total');
        }

        /**
         * Count number attachment error on compressed
         * 
         * @return int Number of error optimized attachment
         * @since 1.0.0
         */
        public function get_number_image_error() {
            $images    = new TP_Image_Optimizer_Table();
            $array_img = $images->get_list_error_image();

            return count($array_img);
        }

        /**
         * Total number uncompress image
         * 
         * @return double
         * @since 1.0.0
         */
        public function get_total_uncompress_img() {
            $db_table            = new TP_Image_Optimizer_Table();
            $total_img           = $this->get_total_image();
            $total_err           = $this->get_number_image_error();
            $total_img_optimized = $db_table->get_total_optimized_image();
            return ($total_img - $total_img_optimized);
        }

        /* Get number total compressed image assigned on Image Optimized
         *
         * @return Total compressed image
         * @since 1.0.0
         */

        public function get_total_compressed_img() {
            $db_table            = new TP_Image_Optimizer_Table();
            $total_img_optimized = $db_table->get_total_optimized_image();
            return $total_img_optimized;
        }


        /**
         * Get statistics of image by sizes
         * 
         * @category Ajax
         * @since 1.0.0
         */
        public function get_statistics_for_detail() {
            if (isset($_GET['id'])) {
                // List size have been choosen to optimize
                $sizes = get_option('tp_image_optimizer_sizes');
                $sizes = explode(",", $sizes);

                $table = new TP_Image_Optimizer_Table();
                $id    = $_GET['id'];

                $total_origin_size  = 0;
                $total_current_size = 0;

                echo "<table>
                <tr>
                    <th>" . esc_html__("Size name", "tp-image-optimizer") . "</th>
                    <th>" . esc_html__("Original Size ", 'tp-image-optimizer') . "</th> 
                    <th>" . esc_html__("Current size", 'tp-image-optimizer') . "</th>
                    <th>" . esc_html__("Saving", 'tp-image-optimizer') . "</th>
                  </tr>";
                foreach ($sizes as $size) {

                    $results      = $table->get_all_statistic_image($id, $size);
                    $current_size = filesize(tp_image_optimizer_scaled_image_path($id, $size));
                    if (isset($results[0]['origin_size'])) {
                        $origin_size = $results[0]['origin_size'];
                    } else {
                        // Not record on database
                        $origin_size = filesize(tp_image_optimizer_scaled_image_path($id, $size));
                        // if file is created, record it to db
                        if ($origin_size > 0) {
                            $table->assign_attachment_to_io($id, $size);
                        }
                    }

                    $total_origin_size  = $total_origin_size + $origin_size;
                    $total_current_size = $total_current_size + $current_size;

                    $reduce = 0;
                    if ($origin_size != 0) {
                        $reduce = (($origin_size - $current_size ) / $origin_size) * 100;
                        if ($reduce != 0) {
                            $reduce = number_format($reduce, 2);
                        }
                    }

                    echo "<tr><td>$size</td>";
                    echo "<td>" . tp_image_optimizer_dislay_size($origin_size) . "</td>";
                    echo "<td>" . tp_image_optimizer_dislay_size($current_size) . "</td>";
                    echo "<td>" . $reduce . "%</td></tr>";
                }
                $save_size = $total_origin_size - $total_current_size;

                echo '<tr class="io-total-size-save"><td>';
                echo esc_html__('Total saving : ', 'tp-image-optimizer') . '</td><td></td><td>';
                echo '<span >' . tp_image_optimizer_dislay_size($save_size) . '<span>';
                echo '</td></tr>';
                echo '</table>';
            } else {
                echo esc_html__('Please try again... ', 'tp-image-optimizer');
            }
            wp_die();
        }

        /**
         * Get total selected size
         * 
         * @return int Number total selected size
         * @since 1.0.0
         */
        public function get_total_selected_size() {
            $list_current_size = get_option('tp_image_optimizer_sizes');
            $list_size         = explode(',', $list_current_size);
            return count($list_size);
        }

        /**
         * Get optimizer statistics when cron running
         * 
         * @return json
         * @category Cron
         * @since 1.0.8
         */
        public function get_cron_statics() {
            $check_cron         = get_option('tpio_cron_status');
            $total_cron         = intval(get_option('tpio_cron_total'));
            $total_processed_cron     = intval(get_option('tpio_cron_run'));
            $last_compressed_id = get_option("tpio_cron_image_done");
            $last_status        = get_option("tpio_cron_last_compress_status");
            $last_error_log     = get_option('tpio_cron_image_last_error_log');
            $success_detail     = get_option('tpio_cron_image_result_success');
            $total_error        = get_option('tpio_error_count');
            $force              = get_option('tpio_force');

            if ($total_processed_cron == $total_cron) {
                //update_option('tpio_cron_status', 0);
            }
            $percent = 0;
            if ($total_cron > 0) {
                $percent = ($total_processed_cron) * 100 / ($total_cron);
                $percent = round($percent, 2);
            }

            if (!$check_cron) {
                $check_cron = 0;
            }
            if (!$last_compressed_id) {
                $last_compressed_id = get_option("tpio_cron_last_optimizer");
                $success_detail     = get_option("tpio_cron_last_result_success");
            }

	        $attachment_processing = get_option('tpio_id_processing');

            $data = array(
                'cron'           => $check_cron,
                'total_image'    => $total_cron,
                'run'            => $total_processed_cron,
                'percent'        => $percent,
                'id_completed'   => $last_compressed_id,
                'last_status'    => $last_status,
                'last_error_log' => $last_error_log,
                'success_detail' => json_decode($success_detail),
                'total_error'    => intval($total_error),
                'force'          => $force,
                'processing' => $attachment_processing
            );
            wp_send_json_success($data);
        }

        /**
         * Return statistics Library media
         * 
         * @since 2.0.0
         */
        public function get_statistics_media() {
            $total_file            = $this->get_total_image();
            $total_error           = $this->get_number_image_error();
            $total_selected_size   = $this->get_total_selected_size();
            $total_image_with_size = $total_file * $total_selected_size;
            $total_uncompress      = $this->get_total_uncompress_img();
            print "<div class='local-analytics'>
                <ul>
                    <li>" . esc_html__('All images: ', 'tp-image-optimizer') . " <b><span>$total_file</span></b></li>
                    <li>" . esc_html__('Total image with selected size: ', 'tp-image-optimizer') . "<b><span>$total_image_with_size</span></b></li>
                    <li>" . esc_html__('Uncompressed image: ', 'tp-image-optimizer') . "<b><span>$total_uncompress</span></b></li>
                </ul>
            </div>";
            die();
        }

        /**
         * Update range chart
         * 
         * @since 2.0.0
         */
        public function update_range_chart() {
            $range = esc_html($_POST['range']);
            update_option('tpio_range', $range);
        }

    }

}