<?php

final class ITSEC_Multisite_Tweaks {
	function run() {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			// Don't risk blocking anything with WP_CLI.
			return;
		}
		
		add_action( 'init', array( $this, 'init' ) );
	}
	
	public function init() {
		if ( ITSEC_Core::is_iwp_call() ) {
			return;
		}
		
		if ( current_user_can( 'manage_options' ) ) {
			return;
		}
		
		
		$settings = ITSEC_Modules::get_settings( 'multisite-tweaks' );
		
		if ( $settings['theme_updates'] ) {
			remove_action( 'load-update-core.php', 'wp_update_themes' );
			add_filter( 'pre_site_transient_update_themes', '__return_null' );
			wp_clear_scheduled_hook( 'wp_update_themes' );
		}
		
		if ( $settings['plugin_updates'] ) {
			remove_action( 'load-update-core.php', 'wp_update_plugins' );
			add_filter( 'pre_site_transient_update_plugins', '__return_null' );
			wp_clear_scheduled_hook( 'wp_update_plugins' );
		}
		
		if ( $settings['core_updates'] ) {
			remove_action( 'admin_notices', 'update_nag', 3 );
			add_filter( 'pre_site_transient_update_core', '__return_null' );
			wp_clear_scheduled_hook( 'wp_version_check' );
		}
	}
}
