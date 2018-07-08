<?php

if (!defined('ABSPATH')) exit;


function elfsight_instashow_add_action_links($links) {
    $links[] = '<a href="' . esc_url(admin_url('admin.php?page=elfsight-instashow')) . '">Settings</a>';
    $links[] = '<a href="http://codecanyon.net/user/elfsight/portfolio?ref=Elfsight" target="_blank">More plugins by Elfsight</a>';
    return $links;
}
add_filter('plugin_action_links_' . ELFSIGHT_INSTASHOW_PLUGIN_SLUG, 'elfsight_instashow_add_action_links');


function elfsight_instashow_admin_init() {
    wp_register_style('elfsight-instashow-admin', plugins_url('assets/instashow-admin.css', ELFSIGHT_INSTASHOW_FILE), array(), ELFSIGHT_INSTASHOW_VERSION);
    wp_register_script('elfsight-instashow', plugins_url('assets/instashow/dist/jquery.instashow.packaged.js', ELFSIGHT_INSTASHOW_FILE), array('jquery'), ELFSIGHT_INSTASHOW_VERSION, true);
    wp_register_script('elfsight-instashow-admin', plugins_url('assets/instashow-admin.js', ELFSIGHT_INSTASHOW_FILE), array('jquery', 'elfsight-instashow'), ELFSIGHT_INSTASHOW_VERSION, true);
}

function elfsight_instashow_admin_scripts() {
    wp_enqueue_style('elfsight-instashow-admin');
    wp_enqueue_script('elfsight-instashow');
    wp_enqueue_script('elfsight-instashow-admin');
}

function elfsight_instashow_create_menu() {
    $page_hook = add_menu_page(__('InstaShow', ELFSIGHT_INSTASHOW_TEXTDOMAIN), __('InstaShow', ELFSIGHT_INSTASHOW_TEXTDOMAIN), 'manage_options', ELFSIGHT_INSTASHOW_SLUG, 'elfsight_instashow_settings_page', plugins_url('assets/img/instashow-wp-icon.png', ELFSIGHT_INSTASHOW_FILE));
    add_action('admin_init', 'elfsight_instashow_admin_init');
    add_action('admin_print_styles-' . $page_hook, 'elfsight_instashow_admin_scripts');
}
add_action('admin_menu', 'elfsight_instashow_create_menu');


function elfsight_instashow_underscore_to_cc($l) {
    return strtoupper(substr($l[0], 1));
}

function elfsight_instashow_rmdir_recursive($dir) {
    foreach(scandir($dir) as $file) {
        if ('.' === $file || '..' === $file) {
            continue;
        }

        if (is_dir("$dir/$file")) {
            elfsight_instashow_rmdir_recursive("$dir/$file");
        }
        else {
            unlink("$dir/$file");
        }
    }
    rmdir($dir);
}


function elfsight_instashow_update_activation_data() {
    if (!wp_verify_nonce($_REQUEST['nonce'], 'elfsight_instashow_update_activation_data_nonce')) {
        exit;
    }

    update_option('elfsight_instashow_purchase_code', !empty($_REQUEST['purchase_code']) ? $_REQUEST['purchase_code'] : '');
    update_option('elfsight_instashow_activated', !empty($_REQUEST['activated']) ? $_REQUEST['activated'] : '');
}
add_action('wp_ajax_elfsight_instashow_update_activation_data', 'elfsight_instashow_update_activation_data');


function elfsight_instashow_get_new_version() {
    $latest_version = get_option('elfsight_instashow_latest_version', '');
    $last_check_datetime = get_option('elfsight_instashow_last_check_datetime', '');

    $result = array();

    if (!empty($last_check_datetime)) {
        $result['message'] = sprintf(__('Last checked on %1$s at %2$s', ELFSIGHT_INSTASHOW_TEXTDOMAIN), date_i18n(get_option('date_format'), $last_check_datetime), date_i18n(get_option('time_format'), $last_check_datetime));
    }

    if (!empty($latest_version) && version_compare(ELFSIGHT_INSTASHOW_VERSION, $latest_version, '<')) {
        $result['version'] = $latest_version;
    }

    die(json_encode($result));
}
add_action('wp_ajax_elfsight_instashow_get_new_version', 'elfsight_instashow_get_new_version');


function elfsight_instashow_update_preferences() {
    if (!wp_verify_nonce($_REQUEST['nonce'], 'elfsight_instashow_update_preferences_nonce')) {
        exit;
    }

    $result = array();

    // force script add
    if (isset($_REQUEST['preferences_force_script_add'])) {
        $result['success'] = true;

        update_option('elfsight_instashow_force_script_add',  $_REQUEST['preferences_force_script_add']);
    }

    // custom css
    if (isset($_REQUEST['preferences_custom_css'])) {
        $file_type = 'css';
        $file_content = $_REQUEST['preferences_custom_css'];
    }

    // custom js
    if (isset($_REQUEST['preferences_custom_js'])) {
        $file_type = 'js';
        $file_content = $_REQUEST['preferences_custom_js'];
    }

    if (isset($file_content)) {
        $uploads_dir_params = wp_upload_dir();
        $uploads_dir = $uploads_dir_params['basedir'] . '/' . ELFSIGHT_INSTASHOW_SLUG;

        if (!is_dir($uploads_dir)) {
            wp_mkdir_p($uploads_dir);
        }

        $path = $uploads_dir . '/instashow-custom.' . $file_type;

        if (file_exists($path) && !is_writable($path)) {
            $result['success'] = false;
            $result['error'] = __('The file can not be overwritten. Please check the permissions.', ELFSIGHT_INSTASHOW_TEXTDOMAIN);

        } else {
            $result['success'] = true;

            file_put_contents($path, stripslashes($file_content));
        }
    }

    // config.json
    $config_available_options = array(
        'preferences_media_limit' => 100, 
        'preferences_cache_time' => 3600, 
        'preferences_allowed_usernames' => '*', 
        'preferences_allowed_tags' => '*'
    );

    if (array_intersect(array_keys($config_available_options), array_keys($_REQUEST))) {
        $preferences_custom_api_url = get_option('elfsight_instashow_custom_api_url', ELFSIGHT_INSTASHOW_API_URL);
        $preferences_config_url_path = parse_url($preferences_custom_api_url, PHP_URL_PATH);
        
        if (preg_match('/\/[^\.\/]+\.[^$]+$/', $preferences_config_url_path)) {
            $preferences_config_url_path = dirname($preferences_config_url_path);
        }

        $preferences_api_path = $_SERVER['DOCUMENT_ROOT'] . rtrim($preferences_config_url_path, '/');
        $preferences_api_config_path = $preferences_api_path . '/config.json';

        if (!file_exists($preferences_api_config_path) || !is_writable($preferences_api_config_path)) {
            $result['success'] = false;
            $result['error'] = __('The file config.json can not be overwritten. Please check the permissions.', ELFSIGHT_INSTASHOW_TEXTDOMAIN);
        }
        else {
            $result['success'] = true;

            $config_data = json_decode(file_get_contents($preferences_api_config_path), true);
            $config = $config_data ? $config_data : array();

            foreach ($config_available_options as $option_name => $option_value) {
                $option_short_name = str_replace('preferences_', '', $option_name);

                if (!empty($_REQUEST[$option_name])) {
                    $config[$option_short_name] = $_REQUEST[$option_name];
                }
                else {
                    $config[$option_short_name] = !empty($config[$option_short_name]) ? $config[$option_short_name] : $option_value;
                }
            }

            // cast to int
            $config['media_limit'] = (int)$config['media_limit'];
            $config['cache_time'] = (int)$config['cache_time'];

            file_put_contents($preferences_api_config_path, json_encode($config));

            // remove storage directory
            $preferences_api_storage_path = $preferences_api_path . '/storage';

            if (is_dir($preferences_api_storage_path)) {
                elfsight_instashow_rmdir_recursive($preferences_api_storage_path, true);
            }
        }
    }

    // custom api url
    if (isset($_REQUEST['preferences_custom_api_url'])) {
        $result['success'] = true;

        update_option('elfsight_instashow_custom_api_url',  $_REQUEST['preferences_custom_api_url']);
    }
   
    exit(json_encode($result));
}
add_action('wp_ajax_elfsight_instashow_update_preferences', 'elfsight_instashow_update_preferences');


function elfsight_instashow_hide_other_products() {
    if (!wp_verify_nonce($_REQUEST['nonce'], 'elfsight_instashow_hide_other_products_nonce')) {
        exit;
    }

    update_option('elfsight_instashow_other_products_hidden', $_REQUEST['other_products_hidden']);
}
add_action('wp_ajax_elfsight_instashow_hide_other_products', 'elfsight_instashow_hide_other_products');


function elfsight_instashow_settings_page() {
    global $elfsight_instashow_defaults;

    wp_elfsight_instashow_widgets_upgrade();

    // widgets
    $widgets_clogged = get_option('elfsight_instashow_widgets_clogged', '');


    // defaults to json
    $instashow_json = array();
    foreach ($elfsight_instashow_defaults as $name => $val) {
        if ($name == 'source' || $name == 'filter_only' || $name == 'filter_except') {
            $val = array();
        }

        if ($name == 'info' || $name == 'popup_info') {
            $val = explode(', ', $val);
        }

        $instashow_json[preg_replace_callback('/(_.)/', 'elfsight_instashow_underscore_to_cc', $name)] = $val;
    }


    // preferences
    $uploads_dir_params = wp_upload_dir();
    $uploads_dir = $uploads_dir_params['basedir'] . '/' . ELFSIGHT_INSTASHOW_SLUG;

    $custom_css_path = $uploads_dir . '/instashow-custom.css';
    $custom_js_path = $uploads_dir . '/instashow-custom.js';
    $preferences_custom_css = is_readable($custom_css_path) ? file_get_contents($custom_css_path) : '';
    $preferences_custom_js = is_readable($custom_js_path) ? file_get_contents($custom_js_path) : '';

    $preferences_force_script_add = get_option('elfsight_instashow_force_script_add');
    $preferences_custom_api_url = get_option('elfsight_instashow_custom_api_url', ELFSIGHT_INSTASHOW_API_URL);

    $preferences_config_url_path = parse_url($preferences_custom_api_url, PHP_URL_PATH);

    if (preg_match('/\/[^\.\/]+\.[^$]+$/', $preferences_config_url_path)) {
        $preferences_config_url_path = dirname($preferences_config_url_path);
    }

    $preferences_api_config_path = $_SERVER['DOCUMENT_ROOT'] . rtrim($preferences_config_url_path, '/') . '/config.json';
 
    if (is_writable($preferences_api_config_path)) {
        $preferences_api_config = json_decode(file_get_contents($preferences_api_config_path), true);
    }
    else {
        $preferences_api_config_error = 'The ' . $preferences_api_config_path . ' file is not writable. Please set write permissions for the file or contact support@elfsight.com';
    }

    $preferences_media_limit = !empty($preferences_api_config) && is_int($preferences_api_config['media_limit']) ? $preferences_api_config['media_limit'] : 100;
    $preferences_cache_time = !empty($preferences_api_config) && is_int($preferences_api_config['cache_time']) ? $preferences_api_config['cache_time'] : 3600;
    $preferences_allowed_usernames = !empty($preferences_api_config) && !empty($preferences_api_config['allowed_usernames']) ? $preferences_api_config['allowed_usernames'] : '*';
    $preferences_allowed_tags = !empty($preferences_api_config) && !empty($preferences_api_config['allowed_tags']) ? $preferences_api_config['allowed_tags'] : '*';


    // activation
    $purchase_code = get_option('elfsight_instashow_purchase_code', '');
    $activated = get_option('elfsight_instashow_activated', '') === 'true';

    $latest_version = get_option('elfsight_instashow_latest_version', '');
    $last_check_datetime = get_option('elfsight_instashow_last_check_datetime', '');
    $has_new_version = !empty($latest_version) && version_compare(ELFSIGHT_INSTASHOW_VERSION, $latest_version, '<');

    $activation_css_classes = '';
    if ($activated) {
        $activation_css_classes .= 'instashow-admin-activation-activated ';
    }
    else if (!empty($purchase_code)) {
        $activation_css_classes .= 'instashow-admin-activation-invalid ';
    }
    if ($has_new_version) {
        $activation_css_classes .= 'instashow-admin-activation-has-new-version ';
    }

    // other products
    $other_products_hidden = get_option('elfsight_instashow_other_products_hidden');

    ?><div class="<?php echo $activation_css_classes; ?>instashow-admin wrap">
        <h2 class="instashow-admin-wp-notifications-hack"></h2>

        <?php require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'admin', 'templates', 'header.php'))); ?>

        <main class="instashow-admin-main instashow-admin-loading" data-is-admin-feeds-clogged="<?php echo $widgets_clogged; ?>">
            <div class="instashow-admin-loader"></div>

            <div class="instashow-admin-menu-container">
                <?php require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'admin', 'templates', 'menu.php'))); ?>

                <?php require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'admin', 'templates', 'menu-actions.php'))); ?>
            </div>

            <div class="instashow-admin-pages-container">
                <?php require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'admin', 'templates', 'page-welcome.php'))); ?>

                <?php require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'admin', 'templates', 'page-feeds.php'))); ?>

                <?php require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'admin', 'templates', 'page-edit-feed.php'))); ?>

                <?php require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'admin', 'templates', 'page-support.php'))); ?>

                <?php require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'admin', 'templates', 'page-preferences.php'))); ?>

                <?php require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'admin', 'templates', 'page-activation.php'))); ?>

                <?php require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'admin', 'templates', 'page-error.php'))); ?>
            </div>
        </main>

        <?php require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'admin', 'templates', 'other-products.php'))); ?>
    </div>
<?php } ?>
