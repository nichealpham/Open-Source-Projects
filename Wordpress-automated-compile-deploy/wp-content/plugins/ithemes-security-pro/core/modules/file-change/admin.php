<?php

final class ITSEC_File_Change_Admin {
	private $script_version = 1;
	private $dismiss_nonce;
	
	
	public function __construct() {
		if ( ! ITSEC_Modules::get_setting( 'file-change', 'show_warning' ) ) {
			return;
		}
		
		add_action( 'init', array( $this, 'init' ) );
	}
	
	public function init() {
		global $blog_id;
		
		if ( ( is_multisite() && ( 1 != $blog_id || ! current_user_can( 'manage_network_options' ) ) ) || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		
		
		add_action( 'wp_ajax_itsec_file_change_dismiss_warning', array( $this, 'dismiss_file_change_warning' ) );
		
		if ( ! empty( $_GET['file_change_dismiss_warning'] ) ) {
			$this->dismiss_file_change_warning();
		} else {
			add_action( 'admin_print_scripts', array( $this, 'add_scripts' ) );
			$this->dismiss_nonce = wp_create_nonce( 'itsec-file-change-dismiss-warning' );
			
			if ( is_multisite() ) {
				add_action( 'network_admin_notices', array( $this, 'show_file_change_warning' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'show_file_change_warning' ) );
			}
		}
	}
	
	public function add_scripts() {
		$vars = array(
			'ajax_action' => 'itsec_file_change_dismiss_warning',
			'ajax_nonce'  => $this->dismiss_nonce
		);
		
		wp_enqueue_script( 'itsec-file-change-script', plugins_url( 'js/script.js', __FILE__ ), array(), $this->script_version, true );
		wp_localize_script( 'itsec-file-change-script', 'itsec_file_change', $vars );
	}
	
	public function dismiss_file_change_warning() {
		ini_set( 'display_errors', 1 );
		
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'itsec-file-change-dismiss-warning' ) ) {
			die( 'Security check' );
		}
		
		ITSEC_Modules::set_setting( 'file-change', 'show_warning', false );
	}
	
	public function show_file_change_warning() {
		$args = array(
			'file_change_dismiss_warning' => '1',
			'nonce'                       => $this->dismiss_nonce,
		);
		
		$dismiss_url = add_query_arg( $args, ITSEC_Core::get_settings_page_url() );
		$logs_url = ITSEC_Core::get_logs_page_url();
		$message = __( 'iThemes Security noticed file changes in your WordPress site. Please review the logs to make sure your system has not been compromised.', 'it-l10n-ithemes-security-pro' );
		
		echo "<div id='itsec-file-change-warning-dialog' class='error'>\n";
		echo "<p>$message</p>\n";
		echo "<p>";
		echo "<a class='button-primary' href='" . esc_url( $logs_url ) . "'>" . __( 'View Logs', 'it-l10n-ithemes-security-pro' ) . "</a> ";
		echo "<a id='itsec-file-change-dismiss-warning' class='button-secondary' href='" . esc_url( $dismiss_url ) . "'>" . __( 'Dismiss Warning', 'it-l10n-ithemes-security-pro' ) . "</a>";
		echo "</p>\n";
		echo "</div>\n";
	}
}

new ITSEC_File_Change_Admin();
