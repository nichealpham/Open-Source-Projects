<?php

class ITSEC_WordPress_Salts_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'wordpress-salts';
	}
	
	protected function sanitize_settings() {
		$previous_settings = ITSEC_Modules::get_settings( $this->get_id() );
		
		if ( ! isset( $this->settings['last_generated'] ) ) {
			$this->settings['last_generated'] = $previous_settings['last_generated'];
		}
		
		$this->sanitize_setting( 'bool', 'regenerate', __( 'Change WordPress Salts', 'it-l10n-ithemes-security-pro' ), false );
		$this->sanitize_setting( 'positive-int', 'last_generated', __( 'Last Generated', 'it-l10n-ithemes-security-pro' ), false );
		
		$this->vars_to_skip_validate_matching_fields[] = 'regenerate';
	}
	
	protected function validate_settings() {
		if ( ! $this->can_save() ) {
			return;
		}
		
		if ( ! $this->settings['regenerate'] ) {
			unset( $this->settings['regenerate'] );
			
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST['module'] ) && $this->get_id() === $_POST['module'] ) {
				// Request to modify just this module.
				
				$this->set_can_save( false );
				
				if ( ITSEC_Modules::get_setting( 'global', 'write_files' ) ) {
					$this->add_error( new WP_Error( 'itsec-wordpress-salts-skipping-regeneration-empty-checkbox', __( 'You must check the Change WordPress Salts checkbox in order to change the WordPress salts.', 'it-l10n-ithemes-security-pro' ) ) );
				} else {
					$this->add_error( new WP_Error( 'itsec-wordpress-salts-skipping-regeneration-write-files-disabled', __( 'The "Write to Files" setting is disabled in Global Settings. In order to use this feature, you must enable the "Write to Files" setting.', 'it-l10n-ithemes-security-pro' ) ) );
				}
			}
			
			return;
		}
		
		
		unset( $this->settings['regenerate'] );
		
		require_once( dirname( __FILE__ ) . '/utilities.php' );
		
		$result = ITSEC_WordPress_Salts_Utilities::generate_new_salts();
		
		if ( is_wp_error( $result ) ) {
			$this->add_error( $result );
			$this->set_can_save( false );
		} else {
			$this->add_message( __( 'The WordPress salts were successfully regenerated.', 'it-l10n-ithemes-security-pro' ) );
			$this->settings['last_generated'] = ITSEC_Core::get_current_time_gmt();
			
			ITSEC_Response::force_logout();
		}
	}
}

ITSEC_Modules::register_validator( new ITSEC_WordPress_Salts_Validator() );
