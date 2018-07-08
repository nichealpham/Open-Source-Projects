<?php

if (!defined('ABSPATH')) exit;


// register styles and scripts
function elfsight_instashow_lib() {
	global $elfsight_instashow_add_scripts;

	$force_script_add = get_option('elfsight_instashow_force_script_add');

	$uploads_dir_params = wp_upload_dir();
	$uploads_dir = $uploads_dir_params['basedir'] . '/' . ELFSIGHT_INSTASHOW_SLUG;
	$uploads_url = $uploads_dir_params['baseurl'] . '/' . ELFSIGHT_INSTASHOW_SLUG;

	wp_register_script('instashow', plugins_url('assets/instashow/dist/jquery.instashow.packaged.js', ELFSIGHT_INSTASHOW_FILE), array(), ELFSIGHT_INSTASHOW_VERSION);
	wp_register_script('instashow-custom', $uploads_url . '/instashow-custom.js', array(), ELFSIGHT_INSTASHOW_VERSION);
	
	wp_register_style('instashow-custom', $uploads_url . '/instashow-custom.css', array(), ELFSIGHT_INSTASHOW_VERSION);

	if ($elfsight_instashow_add_scripts || $force_script_add === 'on') {
		$custom_css_path = $uploads_dir . '/instashow-custom.css';
		$custom_js_path = $uploads_dir . '/instashow-custom.js';

		wp_print_scripts('instashow');

		if (is_readable($custom_js_path) && filesize($custom_js_path) > 0) {
			wp_print_scripts('instashow-custom');
		}

		if (is_readable($custom_css_path) && filesize($custom_css_path) > 0) {
			wp_print_styles('instashow-custom');
		}
	}
}
add_action('wp_footer', 'elfsight_instashow_lib');

?>
