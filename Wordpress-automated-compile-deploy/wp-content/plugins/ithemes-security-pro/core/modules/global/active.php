<?php

function itsec_global_filter_whitelisted_ips( $whitelisted_ips ) {
	return array_merge( $whitelisted_ips, ITSEC_Modules::get_setting( 'global', 'lockout_white_list', array() ) );
}
add_action( 'itsec_white_ips', 'itsec_global_filter_whitelisted_ips', 0 );


function itsec_global_add_notice() {
	if ( ITSEC_Modules::get_setting( 'global', 'show_new_dashboard_notice' ) && current_user_can( ITSEC_Core::get_required_cap() ) ) {
		ITSEC_Core::add_notice( 'itsec_global_show_new_dashboard_notice' );
	}
}
add_action( 'admin_init', 'itsec_global_add_notice', 0 );

function itsec_global_show_new_dashboard_notice() {
	echo '<div class="updated itsec-notice"><span class="it-icon-itsec"></span>'
		 . __( 'New! The iThemes Security dashboard just got a new look.', 'it-l10n-ithemes-security-pro' )
		 . '<a class="itsec-notice-button" href="' . esc_url( 'https://ithemes.com/security/new-ithemes-security-dashboard/' ) . '">' . esc_html( __( "See what's new", 'it-l10n-ithemes-security-pro' ) ) . '</a>'
		 . '<button class="itsec-notice-hide" data-nonce="' . wp_create_nonce( 'dismiss-new-dashboard-notice' ) . '" data-source="new_dashboard">&times;</button>'
		 . '</div>';
}

function itsec_global_dismiss_new_dashboard_notice() {
	if ( wp_verify_nonce( $_REQUEST['notice_nonce'], 'dismiss-new-dashboard-notice' ) ) {
		ITSEC_Modules::set_setting( 'global', 'show_new_dashboard_notice', false );
		wp_send_json_success();
	}
	wp_send_json_error();
}
add_action( 'wp_ajax_itsec-dismiss-notice-new_dashboard', 'itsec_global_dismiss_new_dashboard_notice' );


function itsec_network_brute_force_add_notice() {
	if ( ITSEC_Modules::get_setting( 'network-brute-force', 'api_nag' ) && current_user_can( ITSEC_Core::get_required_cap() ) ) {
		ITSEC_Core::add_notice( 'itsec_network_brute_force_show_notice' );
	}
}
add_action( 'admin_init', 'itsec_network_brute_force_add_notice' );

function itsec_network_brute_force_show_notice() {
	echo '<div id="itsec-notice-network-brute-force" class="updated itsec-notice"><span class="it-icon-itsec"></span>'
		 . __( 'New! Take your site security to the next level by activating iThemes Brute Force Network Protection.', 'it-l10n-ithemes-security-pro' )
		 . '<a class="itsec-notice-button" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'module' => 'network-brute-force', 'enable' => 'network-brute-force' ), ITSEC_Core::get_settings_page_url() ), 'itsec-enable-network-brute-force', 'itsec-enable-nonce' ) ) . '" onclick="document.location.href=\'?itsec_no_api_nag=off&_wpnonce=' . wp_create_nonce( 'itsec-nag' ) . '\';">' . __( 'Get Free API Key', 'it-l10n-ithemes-security-pro' ) . '</a>'
		 . '<button class="itsec-notice-hide" data-nonce="' . wp_create_nonce( 'dismiss-brute-force-network-notice' ) . '" data-source="brute_force_network">&times;</button>'
		 . '</div>';
}

function itsec_network_brute_force_dismiss_notice() {
	if ( wp_verify_nonce( $_REQUEST['notice_nonce'], 'dismiss-brute-force-network-notice' ) ) {
		ITSEC_Modules::set_setting( 'network-brute-force', 'api_nag', false );
		wp_send_json_success();
	}
	wp_send_json_error();
}
add_action( 'wp_ajax_itsec-dismiss-notice-brute_force_network', 'itsec_network_brute_force_dismiss_notice' );
