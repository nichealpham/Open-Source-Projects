<?php
if( function_exists( 'wp_get_theme' ) ) {
	if( is_child_theme() ) {
		$temp_obj = wp_get_theme();
		$theme_obj = wp_get_theme( $temp_obj->get('Template') );
	} else {
		$theme_obj = wp_get_theme();    
	}

	$theme_version = $theme_obj->get('Version');
	$theme_name = $theme_obj->get('Name');
	$theme_uri = $theme_obj->get('ThemeURI');
	$author_uri = $theme_obj->get('AuthorURI');
} else {
	$theme_data = wp_get_theme( get_template_directory().'/style.css' );
	$theme_version = $theme_data['Version'];
	$theme_name = $theme_data['Name'];
	$theme_uri = $theme_data['ThemeURI'];
	$author_uri = $theme_data['AuthorURI'];
}

if( !defined('ADMIN_PATH') )
	define( 'ADMIN_PATH', get_template_directory() . '/admin/' );
if( !defined('ADMIN_DIR') )
	define( 'ADMIN_DIR', get_template_directory_uri() . '/admin/' );

define( 'ADMIN_IMAGES', ADMIN_DIR . 'assets/images/' );
define( 'XML_DIR', get_template_directory_uri() . '/config_xml/' );
define( 'XML_PATH', get_template_directory() . '/config_xml/' );

define( 'LAYOUT_PATH', ADMIN_PATH . 'layouts/' );
define( 'THEMENAME', $theme_name );
/* Theme version, uri, and the author uri are not completely necessary, but may be helpful in adding functionality */
define( 'THEMEVERSION', $theme_version );
define( 'THEMEURI', $theme_uri );
define( 'THEMEAUTHORURI', $author_uri );

define( 'BACKUPS','backups' );

if (is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {
	add_action('admin_head','thenatives_of_option_setup');
}
add_action('admin_head', 'thenatives_settings_admin_message');
add_action('admin_init','thenatives_settings_admin_init');
add_action('admin_menu', 'thenatives_settings_add_admin');

add_action( 'admin_init', function(){
    wp_register_script( 'admin-custom-script', ADMIN_DIR . "assets/js/admin.js", array('jquery') );
    wp_enqueue_script('admin-custom-script');
},10);

require_once ( ADMIN_PATH . 'functions.php' );
require_once ( ADMIN_PATH . 'options-machine.php' );
add_action('wp_ajax_of_ajax_post_action', 'of_ajax_callback');