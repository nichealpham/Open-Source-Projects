<?php

if (!defined('TP_IMAGE_OPTIMIZER_BASE')) {
    exit; // Exit if accessed directly
}

/**
 * SERVICE COMPRESS
 * Provide featured to request optimize service.
 *
 * @class    TP_Image_Optimizer_Service
 * @package  TP_Image_Optimizer/Classes
 * @category Class
 * @version  1.0
 */
if (!class_exists('TP_Image_Optimizer_Service')) {

    class TP_Image_Optimizer_Service {

        /**
         * Address of service
         *
         * @type String
         */
        public static $service;

        /**
         * Info of website, used to validate action
         *
         * @var Object
         */
        public static $authentication;

        /**
         * @var string User Token
         */
        public static $token;

        /**
         * @var int Compress Level
         */
        public static $compress_level;
        private static $response;
        private static $status_http_code;
        private static $curl = false;
        /**
         *  Main address of service
         *
         * @var String
         */
        public $api_main;

        public static function init() {
            add_action('tpio_process_optimize', array(__CLASS__, 'cron_process_optimize'), 10);
            add_action('wp_ajax_cancel_cronjob', array(__CLASS__, 'cancel_optimize'), 10);

            TP_Image_Optimizer_Service::$service = "http://api.themespond.com/api/v1/io/";
            TP_Image_Optimizer_Service::$token = get_option('tp_image_optimizer_token');

            $authentication = array(
                'token' => TP_Image_Optimizer_Service::$token
            );
            $authentication = json_encode($authentication);
            TP_Image_Optimizer_Service::$authentication = base64_encode($authentication);
            TP_Image_Optimizer_Service::$compress_level = get_option('tp_image_optimizer_compress_level');
        }

        public function __construct() {
            $this->api_main = "http://api.themespond.com/io/";
        }

        public function __get($name) {
            return $this->$name;
        }

        /**
         * Get token from server
         * Update token key to WP Option
         *
         * @category Ajax
         * @since    1.0.0
         */
        public function get_token() {

            $token = get_option('tp_image_optimizer_token');

            // Check key exist
            if (($token != false) && (strlen($token) == 35)) {
                $data['log'] = esc_html__('Detect token of service has already created before !', 'tp-image-optimizer');
                wp_send_json_success($data);
            }

            $url = TP_Image_Optimizer_Service::$service . "request";

            $data = array(
                'timeout' => 3000,
                'body'    => array(
                    'action' => 'request_token'
                )
            );

            $response = wp_remote_post($url, $data);

            $status_code = wp_remote_retrieve_response_code($response);

            if ($status_code == 200) {
                $response = wp_remote_retrieve_body($response);
                $response = json_decode($response);
                if (isset($response->key)) {
                    update_option('tp_image_optimizer_token', $response->key);
                    wp_send_json_success($response);
                }
                wp_send_json_error(esc_html__('Cannot get token key, some thing error was happened.', 'tp-image-optimizer'));
            }
            wp_send_json_error(esc_html__('Service cannot established. ' . $status_code, 'tp-image-optimizer'));
        }

        /**
         * Get Stastics from ThemesPond service
         *
         * @category Ajax
         * @since    1.0.0
         */
        public function get_statistics() {

            // Get cache
            $data = get_transient('tp_image_optimizer_statistics_service');

            if (!empty($data)) {
                wp_send_json_success($data);
            }

            // If no cache or expired
            $url = TP_Image_Optimizer_Service::$service . 'statistics/' . TP_Image_Optimizer_Service::$token;

            $data = array(
                'timeout' => 3000,
            );

            $response = wp_remote_get($url, $data);
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                wp_send_json_error($error_message);
            }
            $status_code = wp_remote_retrieve_response_code($response);
            if ($status_code == 404 || $status_code == 500 || !$status_code) {
                wp_send_json_error(esc_html__("Service cannot established.", 'tp-image-optimizer'));
            }
            if ($status_code == 401) {
                delete_option('tp_image_optimizer_token');
                $this->get_token();
            }

            $response = wp_remote_retrieve_body($response);
            $response = json_decode($response);
            if (!empty($response->success) && $response->success == true && !empty($response->data)) {
                //set_transient( 'tp_image_optimizer_statistics_service', $response, 24 * 60 * 60 );
                wp_send_json_success($response);
            }
            wp_send_json_error("Unexpected error.");
        }

        /**
         * Get token by email
         *
         * @category Ajax
         * @since    1.0.6
         */
        public function register_by_mail() {
            $email = htmlentities($_POST['email']);

            $url = $this->api_main . 'api/register';
            $authen = array(
                'email' => $email,
            );

            $authen = base64_encode(json_encode($authen));
            $data = array(
                'body' => array(
                    'action'         => 'register',
                    'authentication' => $authen
                ),
            );

            $response = wp_remote_post($url, $data);
            $response = wp_remote_retrieve_body($response);
            wp_send_json_success($response);
        }

        /**
         * Running optimize image as background service
         *
         *
         * @since 1.0.7
         */
        public function cronjob_optimize_progress() {
            if (!empty($_POST['force'])) {
                $force = $_POST['force'];
                update_option('tpio_force', $force);
            } else {
                $force = get_option('tpio_force');
            }
            update_option('tpio_cron_message', "");
            update_option("tpio_cron_status", 1);
            wp_clear_scheduled_hook('tpio_process_optimize');

            if (!wp_next_scheduled('tpio_process_optimize')) {
                wp_schedule_single_event(time() + 1, 'tpio_process_optimize', array($force));
            }
            wp_die();
        }

        /**
         * Image process cronjob
         *
         * @param boolean $force Force optimize
         * @return type
         *
         * @since 1.0.0
         */
        public static function cron_process_optimize($force) {
            global $wpdb;
            // Lock cron
            $lock_cron = _get_cron_lock();
            update_option("tpio_error_count", 0);
            update_option("tpio_current_cron", $lock_cron);
            // Clear cache statistics
            delete_transient('tp_image_optimizer_statistics_service');
            $db_table = new TP_Image_Optimizer_Table();

            /**
             * MULTI OPTIMIZER
             */
            // Remove cache
            $total_image = $db_table->count_optimize_image($force);
            update_option("tpio_cron_total", $total_image['total']);

            $error_count = 0;
            // Get list image size
            $list_size = get_option('tp_image_optimizer_sizes');
            $list_size = preg_split("/[\s,]+/", $list_size);

            update_option("tpio_cron_status", 1);
            if ($force) {
                update_option("tpio_cron_run", 0);
            } else {
                update_option("tpio_cron_run", ($total_image['compressed']));
            }
            update_option("tpio_cron_count", $total_image['count']);

            update_option("tpio_cron_compressed", $total_image['compressed']);
            for ($number = 0; $number < $total_image['count'] + 1; $number++) {
                update_option("tpio_cron_number", $number);
                // Update current running
                if (!$force) {
                    update_option("tpio_cron_run", $number + $total_image['compressed']);
                } else {
                    update_option("tpio_cron_run", $number);
                }
                // Result compress
                $result = array();

                $attachment_id = $db_table->get_pre_optimize_image($number, $force, $error_count);
                $check_error = 0;
                // Flag size
                $flag_size = 0;
                foreach ($list_size as $size_name) {
                    // Check cronjob running
                    $query = $wpdb->prepare("SELECT `option_value` FROM $wpdb->options WHERE option_name = %s", 'tpio_cron_status');
                    $check_cronjob_running = $wpdb->get_row($query, OBJECT);
                    if (!$check_cronjob_running->option_value) {
                        // STOP COMPRESS
                        TP_Image_Optimizer_Service::cancel_optimize();
                        return;
                    }

                    $request_service = TP_Image_Optimizer_Service::request_service($attachment_id, $size_name);
                    if (isset($request_service['success']) && ($request_service['success'] != 1)) {
                        $result['success'] = false;
                        $result['log'] = $request_service['error_log'];
                        $db_table->update_status_for_attachment($attachment_id, "full", "error");

                        $check_error = $check_error + 1;
                        update_option("tpio_error_count", $error_count);
                        update_option('tpio_cron_last_compress_status', false);
                    } else {
                        $result['success'] = true;
                        $result['url'] = wp_get_attachment_thumb_url($attachment_id);
                        // Set stastus for flag to exclude this attachment id from pre-optimize list
                        if ((isset($request_service['success'])) && ($request_service['success'] == true)) {
                            if ($request_service['old_size'] > $flag_size) {
                                $flag_size = $request_service['old_size'];
                                $result['full_detail'] = $request_service;
                            }
                        }
                    }
                }

                // Check error
                if ($check_error > 0) {
                    update_option('tpio_cron_image_last_error_log', $result['log']);
                    $error_count = $error_count + 1;
                    update_option('tpio_error_count', $error_count);
                    update_option('tpio_cron_image_result_success', '');
                } else {
                    $db_table->update_status_for_attachment($attachment_id, 'full', "optimized");
                    update_option('tpio_cron_last_compress_status', true);
                    $result = json_encode($result['full_detail']);
                    update_option('tpio_cron_image_result_success', $result);
                }
                // Update success result for cronjob
                update_option('tpio_cron_image_done', $attachment_id);

                // COMPRESS DONE
                if ($number == ($total_image['count'] - 1)) {
                    update_option('tpio_cron_last_result_success', $result);
                    update_option('tpio_cron_last_optimizer', $attachment_id);
                }
                if ($number == $total_image['count']) {
                    TP_Image_Optimizer_Service::cancel_optimize();

                    return;
                }
            }
        }

        /**
         *  Run optimize with Ajax - For low hosting
         *  No running as background service, you need keep browser when to progress complete
         *
         * @since 2.0.3
         */
        public function optimize_progress() {
            update_option('tpio_error_count', 0);
            $db_table = new TP_Image_Optimizer_Table();
            /**
             * MULTI OPTIMIZER
             */
            $number = intval(esc_html($_POST['start']));
            $error_count = intval(esc_html($_POST['error_count']));
            $force = esc_html($_POST['force']);
            if($force == 'false') {
                $force = false;
            }else{
                $force = true;
            }

            $attachment_id = $db_table->get_pre_optimize_image($number, $force, $error_count);
            $list_size = $_POST['list_size']; // Get list image size
            $result = array(
                'id'      => $attachment_id,
                'success' => false,
                'number'  => $number,
                'reload'  => false,
                'count'   => $error_count,
                'force' => $force
            );

            if ($attachment_id == '' || $attachment_id == null) {
                $result['reload'] = true;
                wp_send_json_error($result);
            }
            // START  OPTIMIZE WITH EACH SIZE NAME
            $flag_size = 0;
            foreach ($list_size as $size_name) {
                $rs = $this->request_service($attachment_id, $size_name);
                if (isset($rs['success']) && ($rs['success'] != 1)) {
                    $result['success'] = false;
                    $result['log'] = $rs['error_log'];
                    $db_table->update_status_for_attachment($attachment_id, "full", "error");
                } else {
                    $result['success'] = true;
                    $result['url'] = wp_get_attachment_thumb_url($attachment_id);
                    // Set status for flag to exclude this attachment id from pre-optimize list
                    $db_table->update_status_for_attachment($attachment_id, "full", "optimized");
                    if (($rs['size'] == 'full') && (isset($rs['compressed'])) && ($rs['compressed'] == true)) {
                        $result['full_detail'] = $rs;
                    }
                    if ((isset($rs['success'])) && ($rs['success'] == true)) {
                        if ($rs['old_size'] > $flag_size) {
                            $flag_size = $rs['old_size'];
                            $result['full_detail'] = $rs;
                        }
                    }
                }
            }

            if (isset($result['success']) && ($result['success'] == false)) {
                $err = intval(get_option('tp_image_optimizer_error'));
                update_option('tp_image_optimizer_error', $err + 1);
                $error_num = $err + 1;
                $result['error_num'] = $error_num;
                wp_send_json_error($result);
            }
            // If success
            wp_send_json_success($result);
        }


        /**
         * Request ThemesPond compress service
         * Send image to optimize by ThemesPond compress service
         *
         * @category Ajax
         *
         * @param int    $attachment_id - ID of attachment image
         * @param string $size_name     - Size of attachment will be optimized
         * @param int    $timeout       Timeout
         *
         * @return string  - Data for display notification
         * @throws Exception
         * @since    1.0.0
         */
        public static function request_service($attachment_id = '', $size_name = 'full', $timeout = 4500) {
            update_option('tpio_id_processing', $attachment_id); // Update image processing
            ini_set('max_execution_time', 0);// Fix if timeout is low
            $db_table = new TP_Image_Optimizer_Table();

            $image_file = tp_image_optimizer_scaled_image_path($attachment_id, $size_name);
            $file_size_old = filesize($image_file);
            // Data return to debug
            $data_return = array(
                'id'        => $attachment_id,
                'success'   => false,
                'log'       => '',
                'size'      => $size_name,
                'old_size'  => $file_size_old,
                'new_size'  => $file_size_old,
                'error_log' => esc_html__("Unexpected error!", 'tp-image-optimizer')
            );

            $check_image_on_db = $db_table->check_image_size_on_db($attachment_id, $size_name);
            if (!$check_image_on_db && $file_size_old > 0) {
                $db_table->assign_attachment_to_io($attachment_id, $size_name);
            }
            // Validate supported image mime
            $image_mime = get_post_mime_type($attachment_id);
            if (($image_mime != 'image/png') && ($image_mime != 'image/jpeg')) {
                $image_mime = strtoupper(str_replace('image/', '', $image_mime));
                $data_return['error_log'] = esc_html__(sprintf("%s isn't support at this time", $image_mime), 'tp-image-optimizer');

                return $data_return;
            }

            // If image removed before optimizer
            if (!file_exists($image_file)) {
                $data_return['success'] = true;
                $data_return['error_log'] = esc_html__("404 error: This attachment image (original image or cropped image by WordPress) has been existing in Database, but removed.", "tp-image-optimizer");
                //$db_table->remove_deleted_attachment_image($attachment_id);// Remove this ID from IO Database Table
                return $data_return;
            }
            // Image is too small
            if (filesize($image_file) < 5120) {
                $data_return['success'] = true;
                $data_return['log'] = esc_html__("Image is too small", "tp-image-optimizer");

                return $data_return;
            }
            // Validate Image Type
            if (!wp_attachment_is_image($attachment_id)) {
                $data_return['error_log'] = esc_html__("This attachment isn't image type", 'tp-image-optimizer');
                // Remove this ID from IO Database Table
                $db_table->remove_deleted_attachment_image($attachment_id);

                return $data_return;
            }

            /**
             * Data send to service API
             */
            TP_Image_Optimizer_Service::send_image_to_service($image_file, $image_mime, $timeout);// Sending to service
            $response = TP_Image_Optimizer_Service::$response;
            $status_code = TP_Image_Optimizer_Service::$status_http_code;
            /**
             * Service error
             */
            if ($status_code != 200) {
                update_option('tpio_cron_status', 0);
                wp_send_json(array(
                        'success'   => false,
                        'error_log' => esc_html__('Cannot connect to service - ' . $response, 'tp-image-optimizer'),
                        'data'      => array(
                            'log' => esc_html__('Cannot connect to service -' . $response, 'tp-image-optimizer'),
                        ),
                        'status'    => $status_code
                    )
                );
            }
            if (!TP_Image_Optimizer_Service::$curl) {
                /**
                 * Catch unexpected error
                 */
                if (is_wp_error(TP_Image_Optimizer_Service::$response)) {
                    $error_message = TP_Image_Optimizer_Service::$response->get_error_message();
                    $error_remote = "Something went wrong: $error_message";
                    update_option('tp_remote_error', $error_remote); // Log to database
                    $result = TP_Image_Optimizer_Service::request_service($attachment_id, $size_name, 9000);
                    return $result;
                }
                $response = wp_remote_retrieve_body($response);
            }

            if (($response == '') || ($response == 'false')) { //Timeout or no internet connection
                // Check number error by timeout
                $number = intval(get_option("tpio_timeout_count"));
                $number = $number + 1;
                update_option('tpio_timeout_count', $number);
                if ($number < 2) {
                    $result = TP_Image_Optimizer_Service::request_service($attachment_id, $size_name, 9000);
                    return $result;
                } else {
                    $data_return['error_log'] = esc_html__('Cannot process this image because maximum execution timeout ! Please increase max_execution_time in php.ini to fix this.', 'tp-image-optimizer');
                    return $data_return;
                }
            }

            /*  ****************************************
             * VALIDATE DATA RESPONSE IS IMAGE or NOT *
             * *************************************** */
            // Condition 1 : Service return error
            $check = isJSON($response);// If $check == true, it mean server return an error
            // Condition 2 : API unexpected error
            $check2 = false;
            if ((strpos($response, 'something went wrong') !== false) || (strpos($response, '<html') !== false)) {
                $check2 = true;
            }

            /**
             * RETURN RESULT --------------------------------------------------------------------------------------
             */
            if (!$check && !$check2) {
                $data_return['success'] = true;
                unset($data_return['error_log']); // No error
                delete_option('tpio_timeout_count'); // Clear timeout statistics
                /**
                 *  Replace original attachment image by optimized file
                 *  Override original image by response image from PostImage Service
                 */
                $origin_path = tp_image_optimizer_scaled_image_path($attachment_id, $size_name);
                $img_origin_load = @fopen($origin_path, "w");
                $result_write_file = fwrite($img_origin_load, $response);
                // Notice for user
                $data_return['log'] = esc_html__("Success optimizer #", 'tp-image-optimizer') . $attachment_id . ' - ' . $size_name;
                $data_return['new_size'] = $result_write_file;// Update current size after optimized
                $db_table->update_current_size_for_attachment($attachment_id, $size_name, $result_write_file);
            } else {
                if (!$check2) {
                    /**
                     * SERVER RETURN JSON LOG
                     * Catch error
                     */
                    $error_data = json_decode($response);
                    $data_return['error_log'] = $error_data->error;
                }
            }
            return $data_return;
        }

        /**
         * Optimize image with compress service
         *  Select Curl or WP Remote Post
         *
         * @param $image_file
         * @param $image_mime
         * @param $timeout
         */
        public static function send_image_to_service($image_file, $image_mime, $timeout) {
            $service = TP_Image_Optimizer_Service::$service . 'compress/' . TP_Image_Optimizer_Service::$token;
            $check_curl = function_exists('curl_version');
            if ($check_curl) {
                TP_Image_Optimizer_Service::$curl = true;
                try {
                    $data = array(
                        'compress-level' => TP_Image_Optimizer_Service::$compress_level,
                        'mime_type'      => $image_mime
                    );

                    $ch = curl_init(); // Init CURL
                    if (FALSE === $ch)
                        throw new Exception('failed to initialize');
                    if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
                        // Add CURL File
                        $data['image'] = new CURLFile($image_file);
                    } else {
                        $data['image'] = '@' . $image_file;
                    }
                    $option = array(
                        CURLOPT_URL            => $service,
                        //CURLOPT_SAFE_UPLOAD    => false,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_SSL_VERIFYPEER => false,
                        // After get attachment and select compress option, set it to post field of CURL
                        CURLOPT_POST           => count($data),
                        CURLOPT_POSTFIELDS     => $data,
                        CURLOPT_USERAGENT      => curl_user_agent()
                    );
                    // Set option to Curl
                    curl_setopt_array($ch, $option);
                    // Execute CURL
                    $response_from_service = curl_exec($ch);

                    if (FALSE === $response_from_service)
                        throw new Exception(curl_error($ch), curl_errno($ch));

                    TP_Image_Optimizer_Service::$response = $response_from_service;
                    TP_Image_Optimizer_Service::$status_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    // Close CURL connection
                    curl_close($ch);
                    return;
                } catch (Exception $e) {
                    $data_return['success'] = false;
                    $data_return['error_log'] = esc_html__("Lost connection to service !", 'tp-image-optimizer');
                    return $data_return;
                }
            } else {
                /**
                 * Data send to service API
                 */
                $data = array(
                    'headers' => array(
                        'compress-level' => TP_Image_Optimizer_Service::$compress_level,
                        'accept'         => 'application/json', // The API returns JSON
                        'content-type'   => 'application/binary', // Set content type to binary
                        'image-type'     => $image_mime
                    ),
                    'timeout' => $timeout,
                    'body'    => file_get_contents($image_file)
                );
                // Sending to service
                TP_Image_Optimizer_Service::$response = wp_remote_post($service, $data);
                TP_Image_Optimizer_Service::$status_http_code = wp_remote_retrieve_response_code(TP_Image_Optimizer_Service::$response);
            }
        }

        /**
         * Cancel WordPress cronjob
         *
         * @since 1.0.8
         */
        public static function cancel_optimize() {
            $response = update_option("tpio_cron_status", 0);
            delete_option('tpio_cron_id');

            $id_cronjob = get_option('tpio_current_cron');
            wp_clear_scheduled_hook('tpio_process_optimize');
            wp_clear_scheduled_hook($id_cronjob);

            $doing_cron = get_transient('doing_cron');
            $lock_cron = get_option('tpio_current_cron');
            if ($lock_cron == $doing_cron) {
                delete_transient('doing_cron');
            }
            delete_option('tpio_current_cron');
            delete_option('tpio_force');
            delete_option('tpio_error_count');
            delete_option('tpio_id_processing');
            update_option('tpio_timeout_count', 0);
            sleep(5); // Time to clear cronjob
            wp_send_json($response);
        }

        /**
         * Manual compress
         *
         * @category Ajax
         * @since    1.0.8
         */
        public function manual_optimize_progress() {
            $attachment_id = esc_html($_POST['id']);
            $list_size = get_option('tp_image_optimizer_sizes');
            $list_size = preg_split("/[\s,]+/", $list_size);
            $number_size = count($list_size);

            $result = array();
            $error = 0;
            $count = 0;
            $flag_size = 0;
            foreach ($list_size as $size) {
                $count++;
                $request_service = TP_Image_Optimizer_Service::request_service($attachment_id, $size);
                $result['success'] = true;
                if (!$request_service['success']) {
                    $result['log'] = $request_service['error_log'];
                    $error = $error + 1;
                } else {
                    if (($count == $number_size) && (isset($request_service['success'])) && ($request_service['success'] == true)) {
                        $table = new TP_Image_Optimizer_Table();
                        $table->update_status_for_attachment($attachment_id, 'full', 'optimized');
                    }
                }
                $result['count'] = $count;
                if ($request_service['old_size'] > $flag_size) {
                    $flag_size = $request_service['old_size'];
                    $result['full_detail'] = $request_service;
                }
            }
            if ($error > 0) {
                $result['number_error'] = $error;
                $result['success'] = false;
                wp_send_json_error($result);
            }
            wp_send_json_success($result);
        }

        /**
         * Clear when cronjob done
         *
         * @sicne 1.0.8
         */
        public function clear_when_cronjob_done() {
            delete_option('tpio_cron_run');
            delete_option('tpio_cron_total');
            delete_option('tpio_cron_image_done');
            delete_option('tpio_cron_image_result_success');
            delete_option('tpio_cron_last_optimizer');
            delete_option('tpio_cron_last_compress_status');
            delete_option('tpio_cron_last_result_success');
            wp_send_json_success();
        }

        /**
         * Connect to service and get total count and filter result by date
         *
         * @category Ajax
         * @since    1.0.8
         */
        public function show_compress_by_date() {
            $range = esc_html($_POST['range']);
            $this->get_range_data_from_service($range);
        }

        /**
         * Update range chart
         *
         * @category Ajax
         * @since    2.0.0
         */
        public function update_range_chart() {
            if (!empty($_POST['range'])) {
                $range = esc_html($_POST['range']);
                $title = esc_html($_POST['title']);
                update_option('tpio_range', $range);
                update_option('tpio_range_title', $title);
            } else {
                $range = get_option('tpio_range');
            }

            $this->get_range_data_from_service($range);
        }


        /**
         * Connect service and get
         *
         * @param string $range
         *
         */
        private function get_range_data_from_service($range) {
            if (empty(get_transient('tpio_range_filter_' . $range))) {
                // Nocache
                $data = array(
                    'timeout' => 4500,
                    'body'    => array(
                        'range' => $range
                    )
                );
                $service = TP_Image_Optimizer_Service::$service . 'range/' . TP_Image_Optimizer_Service::$token;
                // Send to service
                $response = wp_remote_post($service, $data);
                $status_code = wp_remote_retrieve_response_code($response);
                if ($status_code == 200) {
                    $response = wp_remote_retrieve_body($response);
                    $result = json_decode($response);
                    if ($result->success) {
                        set_transient('tpio_range_filter_' . $range, $response, 12 * HOUR_IN_SECONDS);
                    }
                } else {
                    wp_send_json_error($response);
                }
            } else {
                // Cached
                $response = get_transient('tpio_range_filter_' . $range);
            }
            wp_send_json_success(json_decode($response));
        }

    }

}

TP_Image_Optimizer_Service::init();

