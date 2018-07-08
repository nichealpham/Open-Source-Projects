<?php

class ITSEC_Network_Brute_Force_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'network-brute-force';
	}
	
	protected function sanitize_settings() {
		$previous_settings = ITSEC_Modules::get_settings( $this->get_id() );
		$this->settings = array_merge( $previous_settings, $this->settings );
		
		if ( isset( $this->settings['email'] ) ) {
			$this->sanitize_setting( 'email', 'email', __( 'Email Address', 'it-l10n-ithemes-security-pro' ) );
			$this->vars_to_skip_validate_matching_fields[] = 'email';
		}
		
		$this->sanitize_setting( 'bool', 'updates_optin', __( 'Receive Email Updates', 'it-l10n-ithemes-security-pro' ) );
		
		$this->sanitize_setting( 'string', 'api_key', __( 'API Key', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'string', 'api_secret', __( 'API Secret', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'enable_ban', __( 'Ban Reported IPs', 'it-l10n-ithemes-security-pro' ) );
	}
	
	protected function validate_settings() {
		if ( ! $this->can_save() ) {
			return;
		}
		
		
		if ( isset( $this->settings['email'] ) ) {
			require_once( dirname( __FILE__ ) . '/utilities.php' );
			
			$key = ITSEC_Network_Brute_Force_Utilities::get_api_key( $this->settings['email'], $this->settings['updates_optin'] );
			
			if ( is_wp_error( $key ) ) {
				$this->set_can_save( false );
				$this->add_error( $key );
			} else {
				$secret = ITSEC_Network_Brute_Force_Utilities::activate_api_key( $key );
				
				if ( is_wp_error( $secret ) ) {
					$this->set_can_save( false );
					$this->add_error( $secret );
				} else {
					$this->settings['api_key'] = $key;
					$this->settings['api_secret'] = $secret;

					$this->settings['api_nag'] = false;

					ITSEC_Response::reload_module( $this->get_id() );
				}
			}
		}
		
		if ( $this->can_save() ) {
			unset( $this->settings['email'] );
		}
	}
}

ITSEC_Modules::register_validator( new ITSEC_Network_Brute_Force_Validator() );
