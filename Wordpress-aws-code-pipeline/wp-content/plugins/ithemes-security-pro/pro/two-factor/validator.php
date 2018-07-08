<?php

final class ITSEC_Two_Factor_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'two-factor';
	}

	protected function sanitize_settings() {
		if ( $this->sanitize_setting( 'string', 'available_methods', esc_html__( 'Authentication Methods Available to Users', 'it-l10n-ithemes-security-pro' ) ) ) {
			$this->sanitize_setting( array_keys( $this->get_available_methods() ), 'available_methods', esc_html__( 'Authentication Methods Available to Users', 'it-l10n-ithemes-security-pro' ) );
		}

		if ( $this->sanitize_setting( 'array', 'custom_available_methods', esc_html__( 'Select Available Providers', 'it-l10n-ithemes-security-pro' ) ) ) {
			$this->sanitize_setting( array_keys( $this->get_methods() ), 'custom_available_methods', esc_html__( 'Select Available Providers', 'it-l10n-ithemes-security-pro' ) );
		}

		if ( $this->sanitize_setting( 'string', 'protect_user_type', esc_html__( 'User Type Protection', 'it-l10n-ithemes-security-pro' ) ) ) {
			$this->sanitize_setting( array_keys( $this->get_protect_user_types() ), 'protect_user_type', esc_html__( 'User Type Protection', 'it-l10n-ithemes-security-pro' ) );
		}

		if ( $this->sanitize_setting( 'array', 'protect_user_type_roles', esc_html__( 'Select Roles to Protect', 'it-l10n-ithemes-security-pro' ) ) ) {
			$this->sanitize_setting( array_keys( $this->get_protect_user_type_roles() ), 'protect_user_type_roles', esc_html__( 'Select Roles to Protect', 'it-l10n-ithemes-security-pro' ) );
		}

		$this->sanitize_setting( 'bool', 'protect_vulnerable_users', esc_html__( 'Vulnerable User Protection', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'protect_vulnerable_site', esc_html__( 'Vulnerable Site Protection', 'it-l10n-ithemes-security-pro' ) );
	}

	public function get_available_methods() {
		$types = array(
			'all'       => esc_html__( 'All Methods (recommended)', 'it-l10n-ithemes-security-pro' ),
			'not_email' => esc_html__( 'All Except Email', 'it-l10n-ithemes-security-pro' ),
			'custom'    => esc_html__( 'Select Methods Manually', 'it-l10n-ithemes-security-pro' ),
		);

		return $types;
	}

	public function get_methods() {
		require_once( dirname( __FILE__ ) . '/class-itsec-two-factor-helper.php' );
		$helper = ITSEC_Two_Factor_Helper::get_instance();

		return $helper->get_all_provider_instances();
	}

	public function get_protect_user_types() {
		$methods = array(
			'privileged_users' => esc_html__( 'Privileged Users (recommended)', 'it-l10n-ithemes-security-pro' ),
			'all_users'        => esc_html__( 'All Users (not recommended)', 'it-l10n-ithemes-security-pro' ),
			'custom'           => esc_html__( 'Select Roles Manually', 'it-l10n-ithemes-security-pro' ),
			'disabled'         => esc_html__( 'Disabled', 'it-l10n-ithemes-security-pro' ),
		);

		return $methods;
	}

	public function get_protect_user_type_roles() {
		$wp_roles = wp_roles();

		return $wp_roles->get_names();
	}
}

ITSEC_Modules::register_validator( new ITSEC_Two_Factor_Validator() );
