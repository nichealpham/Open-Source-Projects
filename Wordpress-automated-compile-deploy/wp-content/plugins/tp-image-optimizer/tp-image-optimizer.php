<?php

/*
 * Plugin Name: TP Image Optimizer
 * Description: A WordPress plugin that allows you to reduce image file sizes and optimize all images in the media library.
 * Version: 2.1.0
 * Author: ThemesPond
 * Author URI: https://themespond.com/
 * License: GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Requires at least: 4.0
 * Tested up to: 4.8.2
 * Text Domain: tp-image-optimizer
 * Domain Path: /languages/
 *
 * @package TP_Image_Optimizer
 */

class TP_Image_Optimizer {

    private $title;

    public function __construct() {
        $this->title = esc_html__('TP Image Optimizer', 'tp-image-optimizer');
        $this->defined();
        $this->includes();
        $this->hook();
    }

    private function defined() {
        define('TP_IMAGE_OPTIMIZER_DIR', plugin_dir_path(__FILE__));
        define('TP_IMAGE_OPTIMIZER_URL', plugin_dir_url(__FILE__));
        define('TP_IMAGE_OPTIMIZER_BASE', 'tp-image-optimizer');
        define('TP_IMAGE_OPTIMIZER_VER', '2.1.0');
    }

    /**
     * Register plugin page
     *
     * @since 1.0.0
     */
    public function register_page() {
        add_menu_page($this->title, esc_html__('Image Optimizer', 'tp-image-optimizer'), 'manage_options', TP_IMAGE_OPTIMIZER_BASE, array($this, 'plugin_load'), 'dashicons-images-alt2', 12);
    }

    /**
     * Load content
     *
     * @return void
     * @since 1.0.0
     */
    public function plugin_load() {
        $image = new TP_Image_Optimizer_Image();
        $statistics = new TP_Image_Optimizer_Statistics();

        $data = array(
            'title'           => $this->title,
            'total_image'     => $image->count_attachment_file(),
            'cron'            => get_option('tpio_cron_status'),
            'total_pre_image' => $statistics->get_total_uncompress_img()
        );

        $install_check = get_option('tp_image_optimizer_installed');

        if ($install_check === 'false') {
            $data['title'] = esc_html__('Install TP Image Optimizer', 'tp-image-optimizer');

            tp_image_optimizer_template('install', $data);
        } else {
            tp_image_optimizer_template('content', $data);
        }
    }

    /**
     * Include class
     *
     * @since 1.0.0
     */
    private function includes() {

        include TP_IMAGE_OPTIMIZER_DIR . '/includes/helpers-function.php';
        tp_image_optimizer_class('lang');
        tp_image_optimizer_class('metabox');
        tp_image_optimizer_class('table');
        tp_image_optimizer_class('image');
        tp_image_optimizer_class('service');
        tp_image_optimizer_class('statistics');
        tp_image_optimizer_class('notice');
    }

    /**
     * Enqueue admin script
     *
     * @since 1.0
     * @param string $hook
     * @return void
     */
    public function admin_scripts($hook) {
        $screen = get_current_screen();
        if ($screen->id == "toplevel_page_tp-image-optimizer") {
            // Drag log box
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-draggable');
            // Popup - Tooltip
            wp_enqueue_script('jbox-js', TP_IMAGE_OPTIMIZER_URL . 'assets/lib/jbox/jBox.min.js', array('jquery'), true);
            // ChartJS
            wp_enqueue_script('chart-js', TP_IMAGE_OPTIMIZER_URL . 'assets/lib/chart/chart.min.js', array(), '2.6.0', true);
            // Process ajax
            wp_enqueue_script('io-admin-js', TP_IMAGE_OPTIMIZER_URL . 'assets/js/ajax.js', array(), TP_IMAGE_OPTIMIZER_VER, true);
            // ThemesPond UI
            wp_enqueue_script('tpui-js', TP_IMAGE_OPTIMIZER_URL . 'assets/js/tpui.js', array(), TP_IMAGE_OPTIMIZER_VER, true);
            // Javascript of plugin
            wp_enqueue_script('io-plugin-js', TP_IMAGE_OPTIMIZER_URL . 'assets/js/io.js', array(), TP_IMAGE_OPTIMIZER_VER, true);

            /** *******************
             * STYLE
             ** ******************/
            wp_enqueue_style('tpui-admin', TP_IMAGE_OPTIMIZER_URL . 'assets/css/tpui.css', array(), TP_IMAGE_OPTIMIZER_VER);
            wp_enqueue_style('ionicons', TP_IMAGE_OPTIMIZER_URL . 'assets/css/ionicons.min.css', array(), TP_IMAGE_OPTIMIZER_VER);
            wp_enqueue_style('tpui-fonts', $this->font_url(), array(), null);
            wp_enqueue_style('jbox-css', TP_IMAGE_OPTIMIZER_URL . 'assets/lib/jbox/jBox.css');
            wp_enqueue_style('animate-css', TP_IMAGE_OPTIMIZER_URL . 'assets/css/animate.css');
            wp_enqueue_style('installer-css', TP_IMAGE_OPTIMIZER_URL . 'assets/css/installer.css');
            wp_enqueue_style('io-admin-css', TP_IMAGE_OPTIMIZER_URL . 'assets/css/style.css', null, TP_IMAGE_OPTIMIZER_VER);
            wp_localize_script('io-admin-js', 'tp_image_optimizer_admin_js', array('ajax_url' => admin_url('admin-ajax.php')));
        }
        // Add language
        $lang = new TP_Image_Optimizer_Lang();
        wp_localize_script('io-admin-js', 'tp_image_optimizer_lang', array(
            'main'              => $lang->get_main_text(),
            'success'           => $lang->get_success_notice(),
            'error'             => $lang->get_error_notice(),
            'load'              => $lang->get_loading_notice(),
            'request'           => $lang->get_request_notice(),
            'install'           => $lang->get_install_notice(),
            'size'              => $lang->size(),
            'faq'               => $lang->faq(),
            'cron'              => $lang->cron(),
            'wait'              => esc_html__('Please wait...', 'tp-image-optimizer'),
            'getstarted'        => esc_html__('Get Started', 'tp-image-optimizer'),
            'confirm_fix_token' => esc_html__('Your token is invalid, please reload to fix it.', 'tp-image-optimizer'),
            'standard'          => esc_html__('Standard User', 'tp-image-optimizer'),
            'pro'               => esc_html__('Premium User', 'tp-image-optimizer')
        ));
        wp_enqueue_script('tpio-notice', TP_IMAGE_OPTIMIZER_URL . 'assets/js/notice.js', array('jquery'), TP_IMAGE_OPTIMIZER_VER, true);
    }

    /**
     * Load local files.
     *
     * @since 1.0
     * @return void
     */
    public function load_plugin_textdomain() {

        // Set filter for plugin's languages directory
        $dir = TP_IMAGE_OPTIMIZER_DIR . 'languages/';
        $dir = apply_filters('tp_image_optimizer_languages_directory', $dir);

        // Traditional WordPress plugin locale filter
        $locale = apply_filters('plugin_locale', get_locale(), 'tp-image-optimizer');
        $mofile = sprintf('%1$s-%2$s.mo', 'tp-image-optimizer', $locale);

        // Setup paths to current locale file
        $mofile_local = $dir . $mofile;

        $mofile_global = WP_LANG_DIR . '/tp-image-optimizer/' . $mofile;

        if (file_exists($mofile_global)) {
            // Look in global /wp-content/languages/tp-image-optimizer folder
            load_textdomain('tp-image-optimizer', $mofile_global);
        } elseif (file_exists($mofile_local)) {
            // Look in local /wp-content/plugins/tp-image-optimizer/languages/ folder
            load_textdomain('tp-image-optimizer', $mofile_local);
        } else {
            // Load the default language files
            load_plugin_textdomain('tp-image-optimizer', false, $dir);
        }
    }

    /**
     * Hook
     *
     * @since 1.0.0
     */
    private function hook() {

        register_activation_hook(__FILE__, array($this, 'install'));
        register_deactivation_hook(__FILE__, array($this, 'uninstall'));

        $service = new TP_Image_Optimizer_Service();
        $db_table = new TP_Image_Optimizer_Table();
        $statistics = new TP_Image_Optimizer_Statistics();
        $lib = new TP_Image_Optimizer_Image();

        add_action('admin_menu', array($this, 'register_page'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'), 10);
        add_action('wp_ajax_recheck_library', array($lib, 'assign_all_attachment_image_to_io'), 10);

        // Action optimizer image
        add_action('wp_ajax_get_img_optimizer', array($db_table, 'count_list_optimize_image'), 10);

        // Action update list sizes will be optimized
        add_action('wp_ajax_update_sizes', array($lib, 'update_sizes'), 10);

        // Get detail statistics for Attachment #ID
        add_action('wp_ajax_get_statistics_detail', array($statistics, 'get_statistics_for_detail'), 10);

        // Get token key AJAX
        add_action('wp_ajax_get_token', array($service, 'get_token'), 10);

        // Setting
        add_action('wp_ajax_update_setting', array($db_table, 'update_setting'), 10);
        add_action('wp_ajax_update_size_progress', array($lib, 'update_size_progress'), 10);

        // Statistics from service
        add_action('wp_ajax_get_statistics_from_service', array($service, 'get_statistics'), 10);
        add_action('wp_ajax_get_statistics_from_media', array($statistics, 'get_statistics_media'), 10);
        add_action("wp_ajax_show_compress_by_date", array($service, 'show_compress_by_date'), 10);
        add_action("wp_ajax_update_range_chart", array($service, 'update_range_chart'), 10);

        // Register email
        add_action('wp_ajax_register_email', array($service, 'register_by_mail'));

        // Set status plugin to installed
        add_action('wp_ajax_set_status_to_installed', array($db_table, 'set_to_installed'), 10);
        add_action('wp_ajax_uninstall', array($db_table, 'uninstall'), 10);
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));

        // Update image
        add_action('add_attachment', array('TP_Image_Optimizer_Image', 'remove_attachment_count'));
        add_action('delete_attachment', array('TP_Image_Optimizer_Image', 'remove_attachment_count'));
        add_action('wp_ajax_clear_image_library', array($db_table, 'refresh_image_library'), 10);
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'));

        // Auto update when media libary change
        add_action('delete_attachment', array($db_table, 'removed_attachment_id'));
        add_action('add_attachment', array($db_table, 'add_attachment_id'));

        // Optimize progress
        add_action('wp_ajax_optimize_image', array($service, 'optimize_progress'), 10);
        add_action('wp_ajax_cron_optimize_image', array($service, 'cronjob_optimize_progress'), 10);
        add_action('wp_ajax_manual_optimizer', array($service, 'manual_optimize_progress'), 10);
        add_action('clear_optimize_progress', array($service, 'clear_optimize_progress'), 10);
        add_action('wp_ajax_get_statistics_for_cron', array($statistics, 'get_cron_statics'), 10);
        add_action('wp_ajax_compress_origin_select', array($db_table, 'compress_origin_select'), 10);
        add_action('wp_ajax_clear_when_cronjob_done', array($service, 'clear_when_cronjob_done'));
        add_action('wp_ajax_update_cronjob_selected', array($db_table, 'ajax_update_cronjob_selected'));
    }


    /**
     * Add links to Plugins page
     *
     * @since 1.0.5
     * @return array
     */
    function add_action_links($links) {
        $mylinks = array(
            '<a href="' . admin_url() . 'admin.php?page=' . TP_IMAGE_OPTIMIZER_BASE . '">' . esc_html__('Optimize Now', 'tp-image-optimizer') . '</a>',
        );

        return array_merge($links, $mylinks);
    }

    /**
     * Uninstall plugin
     *
     * @global type $wpdb
     */
    public function uninstall() {

    }

    /**
     * Register TP UI page font url
     *
     * @since 1.0.0
     * @return string Font url
     */
    public function font_url() {

        $fonts_url = '';
        $font_families = array();

        $font1 = _x('on', 'Poppins font', 'tpui');

        if ('off' !== $font1) {
            $font_families[] = 'Poppins:300,400,600,700';
        }

        $font2 = _x('on', 'Baloo font', 'tpui');

        if ('off' !== $font2) {
            $font_families[] = 'Baloo';
        }

        if (!empty($font_families)) {
            $query_args = array(
                'family' => urlencode(implode('|', $font_families)),
                'subset' => urlencode('latin,latin-ext'),
            );

            $fonts_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');

            $fonts_url = apply_filters('tpui_fonts_url', $fonts_url);
        }

        return esc_url_raw($fonts_url);
    }

    /**
     * Install plugin
     *
     * @since 1.0.0
     */
    function install() {
        $table = new TP_Image_Optimizer_Table();
        $table->create(); // Create data table

        if (!get_option('tp_image_optimizer_installed')) {
            add_option('tp_image_optimizer_installed', 'false', '', 'yes');
        }

        // Error option
        if (!get_option('tpio_error_count')) {
            add_option('tpio_error_count', 0, '', 'yes');
        }

        // Select optimize all size
        $all_size = get_intermediate_image_sizes();
        array_push($all_size, 'full');
        $all_size = implode(',', $all_size);
        update_option('tp_image_optimizer_sizes', $all_size);

        // Compress option
        update_option('tp_image_optimizer_compress_level', 3);
    }

}

new TP_Image_Optimizer();


