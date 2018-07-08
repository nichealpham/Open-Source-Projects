<?php

if (!defined('TP_IMAGE_OPTIMIZER_BASE')) {
    exit; // Exit if accessed directly
}

/**
 * DATA TABLE
 * Provide method to get or set data table of Image Optimize - Update database.
 *
 * @class    TP_Image_Optimizer_Table
 * @package  TP_Image_Optimizer/Classes
 * @category Class
 * @version  1.0
 */
if (!class_exists('TP_Image_Optimizer_Table')) {

    class TP_Image_Optimizer_Table {
        /*
         *  Name of data table on database
         * @var String Database name
         */

        private $db;

        public function __construct() {
            global $wpdb;
            $this->db = $wpdb->prefix . 'tp_image_optimizer';

            // If table is not exist
            $result = $wpdb->query("SHOW TABLES LIKE '" . $this->db . "'");
            if (!$result) {
                $this->create();
            }
        }

        /**
         * Create database table for storage data of plugin
         *
         * @global type $wpdb
         * @since 1.0.0
         */
        public final function create() {
            global $wpdb;
            $table_check = $wpdb->query("SHOW TABLES LIKE '$this->db'");

            $charset_collate = $wpdb->get_charset_collate();
            $sql_create_io = "CREATE TABLE $this->db (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                size_name text NOT NULL,
                attachment_id mediumint(10) NOT NULL,
                origin_size mediumint(10) NOT NULL,
                current_size mediumint(10) NOT NULL,
                status text,
                UNIQUE KEY id (id)
           ) $charset_collate;";

            $sql_indexes = "CREATE INDEX `attachment_id` ON $this->db (`attachment_id`)";

            if ($table_check == 0) {
                $wpdb->query($sql_create_io);
                $wpdb->query($sql_indexes);
            }
        }

        /**
         * Search attachment
         *
         * If no condition, it will return all attachment storage on Image Optimize 's Database Table
         *
         * @global type  $wpdb
         * @param string $sql_condition
         * @return Array Attachment
         * @since 1.0.0
         */
        private function get_list_attachment($sql_condition, $limit = 0, $select = "*", $sort = '') {
            global $wpdb;
            $paged = 1;

            if ($sql_condition != '') {
                $sql_condition = "WHERE " . $sql_condition;
            }

            $order = '';
            if ($sort != '') {
                $order = "ORDER BY `id` $sort";
            }

            if ($limit != 0) {
                if (isset($_GET['paged'])) {
                    $paged = esc_html($_GET['paged']);
                }
                // Pagination
                $start = ($paged - 1) * $limit;
                $sql_search = "SELECT $select FROM $this->db $sql_condition $order LIMIT $start,$limit";
            } else {
                $sql_search = "SELECT $select FROM $this->db $sql_condition $order";
            }

            return $wpdb->get_results($sql_search);
        }

        /**
         * Get total image
         *
         * @return int Number total image
         * @since 1.0.0
         */
        public function get_total_image() {
            return count($this->get_list_attachment("`size_name`='full'"));
        }

        /**
         * Search image by id and size
         *
         * @global type $wpdb
         * @param type  $attachment_id
         * @return boolean
         * @since 1.0.0
         */
        private function search_an_image($attachment_id, $size_name = '') {

            $sql = "`attachment_id`='$attachment_id'";
            if ($size_name != '') {
                $sql = "`attachment_id`='$attachment_id' AND `size_name`='$size_name'";
            }

            $results = $this->get_list_attachment($sql);
            if (isset($results[0])) {
                return $results[0];
            }

            return;
        }

        /**
         * Check an attachment ID is isset on library
         *
         * @param String $attachment_id
         * @since 1.0.0
         */
        private function check_isset_attachment($attachment_id, $size) {
            $rs = count($this->search_an_image($attachment_id, $size));
            if ($rs == 1) {
                return true;
            }

            return false;
        }

        /**
         * Add image to database IO
         *
         * @global type $wpdb
         * @param type  $attachment_id
         * @return boolean
         * @since 1.0.0
         */
        public function assign_attachment_to_io($attachment_id, $size_name) {

            $check_isset_img = $this->check_isset_attachment($attachment_id, $size_name);
            if (!($check_isset_img)) {
                global $wpdb;

                $get_size = filesize(tp_image_optimizer_scaled_image_path($attachment_id, $size_name));

                $rs = $wpdb->insert(
                    $this->db, array(
                    'size_name'     => $size_name,
                    'attachment_id' => $attachment_id,
                    'origin_size'   => $get_size,
                    'current_size'  => $get_size,
                    'status'        => 'pending',
                ), array(
                        '%s',
                        '%d'
                    )
                );

                if ($rs = false) {
                    return false;
                }

                return true;
            }

            return false;
        }

        /**
         * Set plugin to installed
         *
         * @since 1.0.3
         */
        public function set_to_installed() {
            // Update installed
            update_option('tp_image_optimizer_installed', 'true');
            wp_die();
        }

        /**
         * Get origin size of an attachment
         *
         * @param double $attachment_id ID of attachment
         * @since 1.0.0
         */
        public function get_origin_size($attachment_id) {
            $rs = $this->search_an_image($attachment_id);
            $origin_size = $rs->origin_size;
            if ($origin_size != 0) {
                $origin_size = number_format($origin_size / 1024, 2);
            }

            return $origin_size;
        }

        /**
         * Update info of attachment ID on IO_Optimizer table
         *
         * @param double $attachment_id : ID of attachment
         * @param double $size_name     Size name
         * @param String $name_collum   Name of Column Database table
         * @param unknow $value         Value update for $name_collum
         * @since 1.0.0
         */
        public function set_attachment_info($attachment_id, $size_name, $name_collum, $value) {
            global $wpdb;
            $this->db = $wpdb->prefix . 'tp_image_optimizer';
            $wpdb->update(
                $this->db, array(
                $name_collum => $value,
            ), array(
                    'attachment_id' => $attachment_id,
                    'size_name'     => $size_name
                )
            );
        }

        /**
         * Get all image from TP Image Optimizer database
         *
         * @return Array List id of attachment image has been recorded by image optimizer
         * @since 1.0.0
         */
        public function get_list_optimize_image() {
            $list_attachment_id = array();
            $results = $this->get_list_attachment("");
            foreach ($results as $result) {
                array_push($list_attachment_id, $result->attachment_id);
            }

            return $list_attachment_id;
        }

        /**
         * Get list of origin attachment IDs
         *
         * @return List attachment ID - Full size
         * @since 1.0.0
         */
        public function get_list_full_image_pagination() {
            $results = $this->get_list_attachment("`size_name`='full'", 15, 'attachment_id', 'DESC');

            return $results;
        }

        /**
         * Set status for attachment image
         *
         * @param $attachment_id ID of attachment
         * @param $size_name     : Size name update
         * @since 1.0.0
         */
        public function update_status_for_attachment($attachment_id, $size_name, $status) {
            $this->set_attachment_info($attachment_id, $size_name, 'status', $status);
        }

        /**
         * Update current size of attachment image
         *
         * @param $attachment_id ID of attachment
         * @param $size_name     : Size name update
         * @return void
         * @since 1.0.0
         */
        public function update_current_size_for_attachment($attachment_id, $size_name, $size) {
            $this->set_attachment_info($attachment_id, $size_name, 'current_size', $size);
        }

        /**
         * Get stattus of attachment image to optimized
         *
         * @param $attachment_id ID of attachment
         * @return Status of attachment
         * @since 1.0.0
         */
        public function get_status_an_attachment($attachment_id) {
            $result = $this->search_an_image($attachment_id, 'full');

            return $result->status;
        }

        /**
         * Get total optimized image
         *
         * @return Total of optimized attachment image
         * @since 1.0.0
         */
        public function get_total_optimized_image() {
            $status = "`status`= 'optimized' AND `size_name`='full'";
            $rs = $this->get_list_attachment($status);

            return count($rs);
        }

        /**
         * Count selected optimizer image
         *
         * @category Ajax
         * @since    1.0.0
         */
        public function count_list_optimize_image() {
            $force = false;
            update_option('tpio_error_count', 0);

            if (isset($_POST['force']) && ($_POST['force'] == 'true')) {
                $force = true;
            }
            global $wpdb;

            if (!$force) {
                // Get pending or error image to compress
                $sql_search = "SELECT COUNT(*) FROM $this->db WHERE ((`status`='pending') OR (`status`='error')) AND `size_name`='full' ORDER BY `id` ASC ";
            } else {
                $sql_search = "SELECT COUNT(*) FROM $this->db  WHERE `size_name`='full' ORDER BY `id` ASC";
            }

            $results = $wpdb->get_var($sql_search);

            $list_size = get_option('tp_image_optimizer_sizes');
            $list_size = preg_split("/[\s,]+/", $list_size);

            $data = array(
                'count'     => $results,
                'force'     => $force,
                'list_size' => $list_size
            );

            wp_send_json_success($data);
        }

        /**
         * Get list error image
         *
         * @return array List IDs attachment error
         * @since 1.0.0
         */
        public function get_list_error_image() {
            global $wpdb;
            $sql_search = "SELECT `attachment_id` FROM $this->db WHERE `status`='error' and `size_name`='full' ORDER BY `id` ASC ";
            $results = $wpdb->get_results($sql_search);
            $arr_image_error = array();
            foreach ($results as $result) {
                array_push($arr_image_error, $result->attachment_id);
            }

            return $arr_image_error;
        }

        /**
         * Get statistics of images with size name
         *
         * @param String $attachment_id ID of attachment
         * @param String $size_name     Size name of attachment
         * @since 1.0.0
         */
        public function get_all_statistic_image($attachment_id, $size_name) {
            global $wpdb;
            $statistics = array();
            $sql_search = "SELECT * FROM $this->db WHERE `attachment_id`='$attachment_id' AND `size_name`='$size_name'";
            $results = $wpdb->get_results($sql_search);
            foreach ($results as $result) {
                $kq = array(
                    'size_name'    => $result->size_name,
                    'origin_size'  => $result->origin_size,
                    'current_size' => $result->current_size
                );
                array_push($statistics, $kq);
            }

            return $statistics;
        }

        /**
         * Remove deleted attachment image from IO_Optimizer
         *
         * @param double $attachment_id ID of attachment
         *                              $return void
         * @since 1.0.0
         */
        public function remove_deleted_attachment_image($attachment_id) {
            global $wpdb;
            $this->db = $wpdb->prefix . 'tp_image_optimizer';
            $wpdb->delete($this->db, array('attachment_id' => $attachment_id));
        }

        /**
         * Update setting
         *
         * @since 1.0.0
         */
        public function update_setting() {

            $update_check = false;

            // Compress level
            if (isset($_POST['level'])) {
                $setting_level = $_POST['level'];
                $update_check = update_option('tp_image_optimizer_compress_level', $setting_level);
                wp_send_json_success(esc_html__('Quanlity was updated successfully.', 'tp-image-optimizer'));
            }
        }

        /**
         * Check Image with Size on Database
         * If image with size have record on database, return true
         *
         * @return boolean
         * @since 1.0.0
         */
        public function check_image_size_on_db($attachment_id, $size_name) {
            global $wpdb;
            $sql_search = "SELECT `status` FROM $this->db WHERE (`attachment_id` = '$attachment_id' ) AND ( `size_name` = '$size_name' )";
            if (count($wpdb->get_results($sql_search)) > 0) {
                return true;
            }

            return false;
        }

        /**
         * Use on optimize progress
         *
         * @global type   $wpdb
         * @param int     $start
         * @param boolean $force       Force optimize
         * @param int     $error_count Error count
         * @return String attachment_id
         * @since 1.0.0
         */
        public function get_pre_optimize_image($start = "1", $force, $error_count = 0) {
            global $wpdb;

            $sql = "LIMIT  $start,1;";
            if ($start == '0') {
                $sql = ';';
            }
            if (!$force) {
                $sql_search = "SELECT `attachment_id` FROM $this->db WHERE (`size_name` = 'full') AND ((`status`='pending') or (`status`='error') ) ORDER BY `id` DESC LIMIT  $error_count,1;";
            } else {
                $sql_search = "SELECT `attachment_id` FROM $this->db WHERE `size_name` = 'full' ORDER BY `id` DESC " . $sql;
            }
            $attachment_id = $wpdb->get_row($sql_search);
            $attachment_id = $attachment_id->attachment_id;

            return $attachment_id;
        }

        /*
         * Count selected optimizer image
         *
         * @param boolean $force
         * @since 1.0.0
         */
        public function count_optimize_image($force) {
            global $wpdb;
            $total_image = $wpdb->get_var("SELECT COUNT(*) FROM $this->db  WHERE `size_name`='full' ORDER BY `id` ASC");
            $total_pre_compress = $total_image;
            if (!$force) {
                $total_pre_compress = $wpdb->get_var("SELECT COUNT(*) FROM $this->db WHERE ((`status`='pending') OR (`status`='error')) AND `size_name`='full' ORDER BY `id` ASC "); // Get pending or eror image to compress
            }

            $data = array(
                'count'      => $total_pre_compress,
                'total'      => $total_image,
                'compressed' => $total_image - $total_pre_compress
            );

            return $data;
        }

        /**
         * Uninstall data of plugin
         * By default, this action not show on panel
         * Useful for developer
         *
         * @global type $wpdb Unintall plugin
         * @since 1.0.1
         */
        public function uninstall() {
            global $wpdb;
            $sql = "DROP TABLE IF EXISTS $this->db;";
            $rs = $wpdb->query($sql);
            update_option('tp_image_optimizer_installed', 'false');
            wp_die();
        }

        /**
         * Refresh image list
         *
         */
        public function refresh_image_library() {
            delete_transient('tpio_count');
            global $wpdb;
            $sql = "TRUNCATE TABLE $this->db;";
            $rs = $wpdb->query($sql);
            wp_send_json_success($rs);
        }

        /**
         * Compress origin image
         *
         * @category Ajax
         * @since    1.0.0
         */
        public function compress_origin_select() {
            $list_size = get_option('tp_image_optimizer_sizes');
            $list_size = preg_split("/[\s,]+/", $list_size);
            $check = $_POST['origin_compress'];
            if ($check == 'false') {
                $list_size = array_diff($list_size, array('full'));
            } else {
                array_push($list_size, 'full');
            }
            $list_size = implode(",", $list_size);
            $check = update_option('tp_image_optimizer_sizes', $list_size);
            wp_send_json_success($check);
        }

        /**
         * Remove attachment id in TP Image Optimizer Table when user delete attachment image
         *
         * @param int $attachment_id
         * @since 1.0.8
         */
        public function removed_attachment_id($attachment_id) {
            global $wpdb;
            $table = $wpdb->prefix . "tp_image_optimizer";
            $sql = $wpdb->prepare("DELETE FROM $table WHERE `attachment_id`='%s'", $attachment_id);
            $query = $wpdb->query($sql);
            wp_update_attachment_metadata($attachment_id, $data);
        }

        /**
         * Update attachment ID in TP Image Optimizer Table when user upload attachment image
         *
         * @param int $attachment_id
         * @since 1.0.8
         */
        public function add_attachment_id($attachment_id) {
            update_option('upload_option', $attachment_id);
            $this->assign_attachment_to_io($attachment_id, 'full');
        }

        /**
         * Update cronjob selected option
         *
         * @since 2.0.3
         */
        public function ajax_update_cronjob_selected() {
            $check = $_POST['cronjob'];
            $cron = update_option('tpio_cronjob_selected', $check);
            wp_send_json_success($cron);
        }

    }

}