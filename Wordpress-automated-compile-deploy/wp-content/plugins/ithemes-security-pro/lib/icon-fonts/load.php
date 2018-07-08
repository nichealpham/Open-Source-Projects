<?php
/**
 * Load custom icon fonts submodule.
 *
 * @package icon-fonts
 * @author iThemes
 * @version 1.2.0
*/

$it_registration_list_version   = '1.2.0';
$it_registration_list_library   = 'icon-fonts';
$it_registration_list_init_file = dirname( __FILE__ ) . '/init.php';

$GLOBALS['it_classes_registration_list'][$it_registration_list_library][$it_registration_list_version] = $it_registration_list_init_file;

if ( ! function_exists( 'it_registration_list_init' ) ) {
	function it_registration_list_init() {
		// The $wp_locale variable is set just before the theme's functions.php file is loaded,
		// this acts as a good check to ensure that both the plugins and the theme have loaded.
		global $wp_locale;
		if ( ! isset( $wp_locale ) )
			return;


		$init_files = array();

		foreach ( (array) $GLOBALS['it_classes_registration_list'] as $library => $versions ) {
			$max_version = '-10000';
			$init_file = '';

			foreach ( (array) $versions as $version => $file ) {
				if ( version_compare( $version, $max_version, '>' ) ) {
					$max_version = $version;
					$init_file = $file;
				}
			}

			if ( ! empty( $init_file ) )
				$init_files[] = $init_file;
		}

		unset( $GLOBALS['it_classes_registration_list'] );

		foreach ( (array) $init_files as $init_file )
			require_once( $init_file );

		do_action( 'it_libraries_loaded' );
	}

	global $wp_version;

	if ( version_compare( $wp_version, '2.9.7', '>' ) )
		add_action( 'after_setup_theme', 'it_registration_list_init' );
	else
		add_action( 'set_current_user', 'it_registration_list_init' );
}
