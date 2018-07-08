<?php

if (!defined('ABSPATH')) exit;


require_once(ELFSIGHT_INSTASHOW_PATH . '/includes/instashow-update.class.php');

function elfsight_instashow_update() {
    $purchase_code = get_option('elfsight_instashow_purchase_code', '');

    new InstaShowUpdate(ELFSIGHT_INSTASHOW_VERSION, ELFSIGHT_INSTASHOW_UPDATE_URL, ELFSIGHT_INSTASHOW_PLUGIN_SLUG, $purchase_code);
}
add_action('init', 'elfsight_instashow_update');

?>