<?php
/*
Plugin Name: Elfsight InstaShow
Description: Instagram feed for WordPress. Create unique galleries of Instagram photos. User friendly, flexible and fully responsive. Amazing look for stunning images.
Plugin URI: https://elfsight.com/instagram-feed-instashow/wordpress/
Version: 2.4.1
Author: Elfsight
Author URI: https://elfsight.com/
*/

if (!defined('ABSPATH')) exit;


define('ELFSIGHT_INSTASHOW_SLUG', 'elfsight-instashow');
define('ELFSIGHT_INSTASHOW_VERSION', '2.4.1');
define('ELFSIGHT_INSTASHOW_FILE', __FILE__);
define('ELFSIGHT_INSTASHOW_PATH', plugin_dir_path(__FILE__));
define('ELFSIGHT_INSTASHOW_URL', plugin_dir_url( __FILE__ ));
define('ELFSIGHT_INSTASHOW_PLUGIN_SLUG', plugin_basename( __FILE__ ));
define('ELFSIGHT_INSTASHOW_TEXTDOMAIN', 'instashow');
define('ELFSIGHT_INSTASHOW_UPDATE_URL', 'https://a.elfsight.com/updates/');
define('ELFSIGHT_INSTASHOW_SUPPORT_URL', 'https://elfsight.ticksy.com/submit/#product100003625');
define('ELFSIGHT_INSTASHOW_PRODUCT_URL', 'https://codecanyon.net/item/instagram-feed-wordpress-gallery-for-instagram/13004086?ref=Elfsight');
define('ELFSIGHT_INSTASHOW_API_URL', ELFSIGHT_INSTASHOW_URL . 'api/');
define('ELFSIGHT_INSTASHOW_YOTTIE_URL', 'https://elfsight.com/youtube-channel-plugin-yottie/wordpress/?utm_source=markets&utm_medium=envato&utm_content=adminpanel&utm_campaign=ISWP&utm_term=YTWP');
define('ELFSIGHT_INSTASHOW_INSTALINK_URL', 'https://elfsight.com/instagram-widget-instalink/wordpress/?utm_source=markets&utm_medium=envato&utm_content=adminpanel&utm_campaign=ISWP&utm_term=ILWP');


require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'instashow-defaults.php')));
require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'instashow-update.php')));
require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'instashow-widgets-api.php')));
require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'admin', 'instashow-admin.php')));
require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'instashow-shortcode.php')));
require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'instashow-widget.php')));
require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'instashow-vc.php')));
require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'instashow-lib.php')));
require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'instashow-analytics.php')));

?>
