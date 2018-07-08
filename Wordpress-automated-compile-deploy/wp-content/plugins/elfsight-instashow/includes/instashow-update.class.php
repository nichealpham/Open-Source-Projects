<?php

if (!defined('ABSPATH')) exit;


class InstaShowUpdate {
        /**
         * The plugin current version
         * @var string
         */
        public $current_version;

        /**
         * The plugin update url
         * @var string
         */
        public $update_url;

        /**
         * Plugin Slug (plugin_directory/plugin_file.php)
         * @var string
         */
        public $plugin_slug;

        /**
         * Plugin name (plugin_file)
         * @var string
         */
        public $slug;

        /**
         * Purcahse code
         * @var string
         */
        public $purchase_code;

        /**
         * Initialize a new instance of the InstaShow Update class
         * @param string $current_version
         * @param string $update_url
         * @param string $plugin_slug
         * @param string $purchase_code
         */
        function __construct($current_version, $update_url, $plugin_slug, $purchase_code) {
            $this->current_version = $current_version;
            $this->update_url = $update_url;
            $this->plugin_slug = $plugin_slug;
            $this->purchase_code = $purchase_code;

            list($t1, $t2) = explode('/', $plugin_slug);
            $this->slug = str_replace('.php', '', $t2);

            add_filter('pre_set_site_transient_update_plugins', array(&$this, 'check_update'));
            add_filter('plugins_api', array(&$this, 'check_info'), 10, 3);
        }

        /**
         * Add our self-hosted autoupdate plugin to the filter transient
         *
         * @param $transient
         * @return object $ transient
         */
        public function check_update($transient) {
            if (empty($transient->checked)) {
                return $transient;
            }

            $result = $this->get_info('version');
            update_option(str_replace('-', '_', $this->slug) . '_last_check_datetime', time());

            if (is_object($result) && empty($result->error) && !empty($result->data) && version_compare($this->current_version, $result->data->version, '<')) {
                update_option(str_replace('-', '_', $this->slug) . '_latest_version', $result->data->version);
                $transient->response[$this->plugin_slug] = $result->data;
            }

            return $transient;
        }

        /**
         * Add our self-hosted description to the filter
         *
         * @param boolean $false
         * @param array $action
         * @param object $arg
         * @return bool|object
         */
        public function check_info($result, $action, $args) {
            $result = false;

            if (isset($args->slug) && $args->slug === $this->slug) {
                $info = $this->get_info('info');

                if (is_object($info) && empty($info->error) && !empty($info->data)) {
                    if (!empty($info->data->sections)) {
                        $info->data->sections = (array)$info->data->sections;
                    }

                    $result = $info->data;
                }
            }

            return $result;
        }

        /**
         * Return information about the lastest version
         *
         * @param string $action
         * @return bool|string
         */
        public function get_info($action) {
            $request_string = array(
                'body' => array(
                    'action' => urlencode($action),
                    'slug' => urlencode($this->slug),
                    'purchase_code' => urlencode($this->purchase_code),
                    'version' => urlencode($this->current_version),
                    'host' => !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : get_site_url()
                )
            );

            $result = false;

            $response = wp_remote_post($this->update_url, $request_string);

            if (!is_wp_error($response) || wp_remote_retrieve_response_code($response) === 200) {
                if ($response_body = json_decode(wp_remote_retrieve_body($response))) {
                    $result = $response_body;
                }
            }

            return $result;
        }
}
