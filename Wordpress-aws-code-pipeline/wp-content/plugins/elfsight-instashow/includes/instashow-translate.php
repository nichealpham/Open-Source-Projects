<?php

if (!defined('ABSPATH')) exit;


function elfsight_instashow_textdomain() {
	load_plugin_textdomain(ELFSIGHT_INSTASHOW_TEXTDOMAIN, false, dirname(ELFSIGHT_INSTASHOW_PLUGIN_SLUG) . '/lang/');
}
add_action('plugins_loaded', 'elfsight_instashow_textdomain');

?>