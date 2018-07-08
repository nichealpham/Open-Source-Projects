<?php

class ITSEC_Four_Oh_Four_Validator extends ITSEC_Validator {
	public function get_id() {
		return '404-detection';
	}
	
	protected function sanitize_settings() {
		$this->sanitize_setting( 'positive-int', 'check_period', __( 'Minutes to Remember 404 Error (Check Period)', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'positive-int', 'error_threshold', __( 'Error Threshold', 'it-l10n-ithemes-security-pro' ) );
		
		$this->sanitize_setting( array( $this, 'sanitize_white_list_entry' ), 'white_list', __( '404 File/Folder White List', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( array( $this, 'sanitize_types_entry' ), 'types', __( '404 File/Folder White List', 'it-l10n-ithemes-security-pro' ) );
	}
	
	protected function sanitize_white_list_entry( $entry ) {
		if ( '/' !== substr( $entry, 0, 1 ) ) {
			return false;
		}
		
		return $entry;
	}
	
	protected function sanitize_types_entry( $entry ) {
		if ( '.' !== substr( $entry, 0, 1 ) ) {
			return false;
		}
		
		return $entry;
	}
}

ITSEC_Modules::register_validator( new ITSEC_Four_Oh_Four_Validator() );
