<?php

class ITSEC_Global_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'global';
	}

	protected function sanitize_settings() {
		if ( is_dir( WP_PLUGIN_DIR . '/iwp-client' ) ) {
			$this->sanitize_setting( 'bool', 'infinitewp_compatibility', __( 'Add InfiniteWP Compatibility', 'it-l10n-ithemes-security-pro' ) );
		} else {
			$this->settings['infinitewp_compatibility'] = $this->previous_settings['infinitewp_compatibility'];
		}

		if ( 'nginx' === ITSEC_Lib::get_server() ) {
			$this->sanitize_setting( 'writable-file', 'nginx_file', __( 'NGINX Conf File', 'it-l10n-ithemes-security-pro' ), false );
		} else {
			$this->settings['nginx_file'] = $this->previous_settings['nginx_file'];
		}


		$this->set_previous_if_empty( array( 'did_upgrade', 'log_info', 'show_new_dashboard_notice', 'show_security_check', 'digest_last_sent', 'digest_messages', 'build', 'activation_timestamp' ) );
		$this->set_default_if_empty( array( 'log_location', 'nginx_file' ) );


		$this->sanitize_setting( 'bool', 'write_files', __( 'Write to Files', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'digest_email', __( 'Send Digest Email', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'blacklist', __( 'Blacklist Repeat Offender', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'email_notifications', __( 'Email Lockout Notifications', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'allow_tracking', __( 'Allow Data Tracking', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'lock_file', __( 'Disable File Locking', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'proxy_override', __( 'Override Proxy Detection', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'hide_admin_bar', __( 'Hide Security Menu in Admin Bar', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'show_error_codes', __( 'Show Error Codes', 'it-l10n-ithemes-security-pro' ) );

		$this->sanitize_setting( 'string', 'lockout_message', __( 'Host Lockout Message', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'string', 'user_lockout_message', __( 'User Lockout Message', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'string', 'community_lockout_message', __( 'Community Lockout Message', 'it-l10n-ithemes-security-pro' ) );

		$this->sanitize_setting( 'writable-directory', 'log_location', __( 'Path to Log Files', 'it-l10n-ithemes-security-pro' ) );

		$this->sanitize_setting( 'positive-int', 'blacklist_count', __( 'Blacklist Threshold', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'positive-int', 'blacklist_period', __( 'Blacklist Lockout Period', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'positive-int', 'lockout_period', __( 'Lockout Period', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'positive-int', 'log_rotation', __( 'Days to Keep Database Logs', 'it-l10n-ithemes-security-pro' ) );

		$log_types = array_keys( $this->get_valid_log_types() );
		$this->sanitize_setting( $log_types, 'log_type', __( 'Log Type', 'it-l10n-ithemes-security-pro' ) );

		$this->sanitize_setting( 'newline-separated-ips', 'lockout_white_list', __( 'Lockout White List', 'it-l10n-ithemes-security-pro' ) );

		$this->sanitize_setting( 'newline-separated-emails', 'notification_email', __( 'Notification Email', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'newline-separated-emails', 'backup_email', __( 'Backup Delivery Email', 'it-l10n-ithemes-security-pro' ) );


		$allowed_tags = $this->get_allowed_tags();

		$this->settings['lockout_message'] = trim( wp_kses( $this->settings['lockout_message'], $allowed_tags ) );
		$this->settings['user_lockout_message'] = trim( wp_kses( $this->settings['user_lockout_message'], $allowed_tags ) );
		$this->settings['community_lockout_message'] = trim( wp_kses( $this->settings['community_lockout_message'], $allowed_tags ) );

		if ( $this->settings['digest_last_sent'] <= 0 ) {
			$this->settings['digest_last_sent'] = ITSEC_Core::get_current_time_gmt();
		}
	}

	public function get_valid_log_types() {
		return array(
			'database' => __( 'Database Only', 'it-l10n-ithemes-security-pro' ),
			'file'     => __( 'File Only', 'it-l10n-ithemes-security-pro' ),
			'both'     => __( 'Both', 'it-l10n-ithemes-security-pro' ),
		);
	}

	private function get_allowed_tags() {
		return array(
			'a'      => array(
				'href'  => array(),
				'title' => array(),
			),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'h1'     => array(),
			'h2'     => array(),
			'h3'     => array(),
			'h4'     => array(),
			'h5'     => array(),
			'h6'     => array(),
			'div'    => array(
				'style' => array(),
			),
		);
	}
}

ITSEC_Modules::register_validator( new ITSEC_Global_Validator() );
