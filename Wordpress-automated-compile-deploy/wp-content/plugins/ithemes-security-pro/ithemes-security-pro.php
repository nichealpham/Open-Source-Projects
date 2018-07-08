<?php

/*
 * Plugin Name: iThemes Security Pro
 * Plugin URI: https://ithemes.com/security
 * Description: Take the guesswork out of WordPress security. iThemes Security offers 30+ ways to lock down WordPress in an easy-to-use WordPress security plugin.
 * Author: iThemes
 * Author URI: https://ithemes.com
 * Version: 4.0.0
 * Text Domain: it-l10n-ithemes-security-pro
 * Domain Path: /lang
 * Network: True
 * License: GPLv2
 * iThemes Package: ithemes-security-pro
 */


$locale = apply_filters( 'plugin_locale', get_locale(), 'it-l10n-ithemes-security-pro' );
load_textdomain( 'it-l10n-ithemes-security-pro', WP_LANG_DIR . "/plugins/ithemes-security-pro/it-l10n-ithemes-security-pro-$locale.mo" );
load_plugin_textdomain( 'it-l10n-ithemes-security-pro', false, basename( dirname( __FILE__ ) ) . '/lang/' );

if ( isset( $itsec_dir ) || class_exists( 'ITSEC_Core' ) ) {
	include( dirname( __FILE__ ) . '/core/show-multiple-version-notice.php' );
	return;
}


if ( ! function_exists( 'itsec_pro_register_modules' ) ) {
	// Add pro modules at priority 11 so they are added after core modules (thus taking precedence)
	add_action( 'itsec-register-modules', 'itsec_pro_register_modules', 11 );

	function itsec_pro_register_modules() {
		$path = dirname( __FILE__ );

		ITSEC_Modules::register_module( 'core', "$path/pro/core", 'always-active' );
		ITSEC_Modules::register_module( 'dashboard-widget', "$path/pro/dashboard-widget", 'always-active' );
		ITSEC_Modules::register_module( 'malware-scheduling', "$path/pro/malware-scheduling", 'default-active' );
		ITSEC_Modules::register_module( 'online-files', "$path/pro/online-files", 'always-active' );
		ITSEC_Modules::register_module( 'privilege', "$path/pro/privilege" );
		ITSEC_Modules::register_module( 'password-expiration', "$path/pro/password-expiration" );
		ITSEC_Modules::register_module( 'recaptcha', "$path/pro/recaptcha" );
		ITSEC_Modules::register_module( 'import-export', "$path/pro/import-export", 'always-active' );
		ITSEC_Modules::register_module( 'two-factor', "$path/pro/two-factor", 'default-active' );
		ITSEC_Modules::register_module( 'user-logging', "$path/pro/user-logging", 'default-active' );
		ITSEC_Modules::register_module( 'version-management', "$path/pro/version-management", 'default-active' );
		ITSEC_Modules::register_module( 'user-security-check', "$path/pro/user-security-check", 'always-active' );

		if ( class_exists( 'WP_CLI' ) && class_exists( 'WP_CLI_Command' ) ) {
			require( "$path/pro/wp-cli/class-itsec-wp-cli-command-itsec.php" );
			WP_CLI::add_command( 'itsec', 'ITSEC_WP_CLI_Command_ITSEC' );
		}
	}
}


$itsec_dir = dirname( __FILE__ );

require( "$itsec_dir/core/core.php" );
$itsec_core = ITSEC_Core::get_instance();
$itsec_core->init( __FILE__, esc_html__( 'iThemes Security Pro', 'it-l10n-ithemes-security-pro' ) );


if ( is_admin() ) {
	require( "$itsec_dir/lib/icon-fonts/load.php" );
}


if ( ! function_exists( 'ithemes_repository_name_updater_register' ) ) {
	function ithemes_repository_name_updater_register( $updater ) {
		$updater->register( 'ithemes-security-pro', __FILE__ );
	}
	add_action( 'ithemes_updater_register', 'ithemes_repository_name_updater_register' );

	require( "$itsec_dir/lib/updater/load.php" );
}
