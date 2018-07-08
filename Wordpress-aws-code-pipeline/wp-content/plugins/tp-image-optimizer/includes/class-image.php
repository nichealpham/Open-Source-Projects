<?php

if (!defined('TP_IMAGE_OPTIMIZER_BASE')) {
    exit; // Exit if accessed directly
}

/**
 * IMAGE WORK
 * Provide function to get and assign image off Media Library to Plugin.
 *
 * @class TP_Image_Optimizer_Image
 * @package TP_Image_Optimizer/Classes
 * @category Class
 * @version 1.0
 *
 */
if (!class_exists('TP_Image_Optimizer_Image')) {

    class TP_Image_Optimizer_Image {

        /**
         *
         * @var int
         */
        public $number_image;

        /**
         * Get list id of attachment from Media
         *
         * @param int $paged Pagination
         * @return Array List attachment id of Media
         * @since 1.0.0
         */
        public function get_list_image($paged) {
            $query_images_args = array(
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'post_status'    => 'inherit',
                'posts_per_page' => 800,
                'paged'          => $paged,
                'order'          => 'ASC',
                'orderby'        => 'date',
            );
            $query_images      = new WP_Query($query_images_args);

            $attachment_ids = array();

            foreach ($query_images->posts as $image) {
                $attachment_ids[] = $image->ID;
            }

            wp_reset_postdata();
            return $attachment_ids;
        }

        /**
         *  Show all image library
         *  Use WP Table List
         *  Display all attachment Image on WP
         *
         * @return String All table content
         * @since 1.0.0
         */
        public function display_table_image() {
            $db_table = new TP_Image_Optimizer_Table();

            $attachment_ids = $db_table->get_list_full_image_pagination();

            $api = new TP_Image_Optimizer_Service(8);
            // Call WP LIST TABLE
            tp_image_optimizer_table('detail');
            // Dont show detail if wp version < 4.3
            if (version_compare(get_bloginfo('version'), '4.3', '>=')) {
                $table             = new TP_Image_Optimizer_List_Table();
                $table->data_table = array();
                $i                 = 0;
                ob_start();
                if (count($attachment_ids) != 0) {
                    foreach ($attachment_ids as $attachment_id) {
                        $i++;
                        $attachment_id = $attachment_id->attachment_id;

                        if (wp_attachment_is_image($attachment_id)) {
                            $statistics     = $db_table->get_all_statistic_image($attachment_id, "full");
                            $origin_size  = tp_image_optimizer_caculator_size($statistics[0]['origin_size']);
                            $current_size = tp_image_optimizer_caculator_size($statistics[0]['current_size']);
                            // Remove comma
                            $origin_size  = str_replace(",", "", $origin_size);
                            $current_size = str_replace(",", "", $current_size);

                            $image_status = $db_table->get_status_an_attachment($attachment_id);

                            if ($image_status == 'optimized') {
                                $action = '<span class="result"><span class="success-optimize"></span>';
                            } else {
                                $action = "<span class='result compress-$attachment_id' data-id='$attachment_id'>"
                                        . "<a href='$attachment_id' class='single-compress button button-secondary'><b>"
                                        . esc_html__("Compress", 'tp-image-optimizer')
                                        . "</b></a><p class='load-speeding-wheel'></p></span>";
                            }

                            $view_optimizer = "<a href='$attachment_id' class='badge' id='badge-$attachment_id' class='badge-$attachment_id' >"
                                    . "<b class='badge-$attachment_id ion-information-circled'></b>"
                                    . "</a>";

                            $item = array(
                                'id'           => $i,
                                'image'        => tp_image_optimizer_display_image($attachment_id),
                                'origin_size'  => "<span class='table-detail-$attachment_id'>" . tp_image_optimizer_dislay_size($origin_size * 1000) . '</span>',
                                'current_size' => "<span class='table-detail-$attachment_id'>" . tp_image_optimizer_dislay_size($current_size * 1000) . '</span>',
                                'optimizer'    => $this->get_reduce_after_optimized($attachment_id, $origin_size, $current_size) . $view_optimizer,
                                'action' => $action,
                            );
                            array_push($table->data_table, $item);
                        } else {
                            $db_table->remove_deleted_attachment_image($attachment_id);
                        }
                    }
                    $table->total_items = $db_table->get_total_image();
                    $table->prepare_items();
                    $table->display();

                    return ob_get_clean();
                } else {
                    $this->no_media_found();
                }
            } else {
                $this->old_version();
            }
            return;
        }

        /**
         * Get atatchment file size ( KB )
         *
         * @param String ID of attachment image
         * @return double file size
         * @since 1.0.0
         */
        public function get_file_size($id) {
            $file = get_attached_file($id);
            if (file_exists($file)) {
                $filesize = filesize($file);
                $filesize = tp_image_optimizer_caculator_size($filesize);
                return $filesize;
            }
            return null;
        }

        /**
         * Import all attachment to TP Image Optimizer's Database Table
         *
         * @category Ajax
         * @since 1.0.0
         */
        public function assign_all_attachment_image_to_io() {
            $db_table = new TP_Image_Optimizer_Table();
            if (isset($_POST['paged'])) {
                $paged  = esc_html($_POST['paged']);
                $images = $this->get_list_image($paged);
                foreach ($images as $image) {
                    $db_table->assign_attachment_to_io($image, "full");
                }
            }
            $data = array(
                'image' => $images,
                'paged' => $paged
            );
            wp_send_json_success($data);
        }

        /**
         * Get reduce number kb have been optimized / percent
         *
         * @param double $origin_size Size of original attachment
         * @param double $new_size  Size of attachment after opimized
         * @since 1.0.0
         */
        public function get_reduce_after_optimized($attachment_id, $origin_size, $new_size) {
            $percent = 0;
            if ($origin_size == 0) {
                return '';
            }
            $result = (double) $origin_size - (double) $new_size;
            if ($result != 0) {
                $reduce  = number_format($result, 2);
                $reduce  = str_replace(',', '', $reduce);
                $percent = number_format($reduce * 100 / $origin_size, 2);
            }

            $output = esc_html__('Saving ', 'tp-image-optimizer');

            $output .= "<span class='detail-saving-$attachment_id'>" . tp_image_optimizer_dislay_size($result * 1000) . '</span> ';
            $output .= " / <b class='percent-saving-$attachment_id'>" . $percent . esc_html__('%', 'tp-image-optimizer') . '</b>';

            return $output;
        }

        /**
         * Count attachment file on WordPress
         *
         * @return double Number total of attachment fille
         * @since 1.0.0
         */
        public function count_attachment_file() {
            $count = get_transient('tpio_count');
            if (empty($count)) {
	            $args         = array(
		            'post_type'      => 'attachment',
		            'post_status'    => 'inherit',
		            'post_mime_type' => 'image',
		            'posts_per_page' => 0,
	            );
	            $attatchments = new WP_Query($args);

	            $count = $attatchments->found_posts;
	            // Count all the attachments
	            wp_reset_postdata();
                set_transient('tpio_count', $count);
            }
            return $count;
        }

        /**
         * Delete attachment counter was saved in transient
         * @since 1.0.3
         */
        public static function remove_attachment_count() {
            delete_transient('tpio_count');
        }

        public function abc($post_id) {

        }

        /**
         * Return total number image Media for AJAX
         *
         * @return Double Total number image
         * @since 1.0.3
         */
        public function count_attachment_file_ajax() {
            $total = $this->count_attachment_file();
            wp_send_json_success($total);
        }

        /**
         * Update list WordPress size will be optimized
         */
        public function update_sizes() {
            $sizes = $_POST['listsizes'];
            update_option("tp_image_optimizer_sizes", $sizes);
            wp_send_json_success(esc_html__('All image sizes were updated successfully.', 'tp-image-optimizer'));
        }

        /**
         * When system has removed an ImageSize, We need remove it from option Sizes
         *
         * @return Array List valid ImageSize choose by user
         * @since 1.0.0
         */
        public function get_selected_image_size() {
            $all_sizes = get_intermediate_image_sizes();

            $list_current_size = get_option('tp_image_optimizer_sizes');
            $list_current_size = preg_split("/[\s,]+/", $list_current_size);

            array_push($all_sizes, 'full');
            // Detect imagesize is not exist
            $invalid_size = array_diff($list_current_size, $all_sizes);
            $list_valid   = array_diff($list_current_size, $invalid_size);
            $option_size  = implode(',', $list_valid);

            update_option('tp_image_optimizer_sizes', $option_size);
            // Return list valid imagesize
            return $list_valid;
        }

        /**
         * Display requirement WP 4.3+
         *
         * @since 1.0.2
         */
        private function old_version() {
            echo "<div class='old-version'><span class='label'>";
            echo esc_html__('This feature is only compatible with WordPress 4.3+. Update your WordPress to enjoy the perfect performance of Image Optimizer', 'tp-image-optimizer');
            echo '</span>';
            echo '<div class = "refresh-library"></div>';
            echo '</div>';
        }

        /**
         * Display no media
         *
         * @since 1.0.2
         */
        private function no_media_found() {
            echo "<div class='no-media'><span class='label'>";
            echo esc_html__('No media files found.', 'tp-image-optimizer');
            echo '</span>';
            echo '<button class="button button-default refresh-library">' . esc_html__('Refresh', 'tp-image-optimizer') . '</button>';
            echo '</div>';
        }

        /**
         * Update enable or disable size progress
         *
         */
        public function update_size_progress() {
            $size_name = esc_html($_POST['size_name']);
            $list_size = get_option("tp_image_optimizer_sizes");

            $size_process = explode(',', $list_size);
            $size_process = array_diff($size_process, array(""));

            $enable = esc_html($_POST['enable']);
            if ($enable !== 'false') {
                $list_size = array_push($size_process, $size_name);
            } else {
                $size_process = array_diff($size_process, array($size_name));
            }
            $list_size = implode(",", $size_process);
            update_option("tp_image_optimizer_sizes", $list_size);
            wp_send_json_success($list_size);
        }

    }

}
