<?php

final class ITSEC_Online_Files_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'online-files';
	}
	
	protected function sanitize_settings() {
		$this->sanitize_setting( 'bool', 'compare_file_hashes', __( 'Compare Files Online', 'it-l10n-ithemes-security-pro' ) );
	}
}

ITSEC_Modules::register_validator( new ITSEC_Online_Files_Validator() );
