<?php

/**
 * Online File Scan Execution
 *
 * Handles all online file scan execution once the feature has been
 * enabled by the user.
 *
 * @since   1.10.0
 *
 * @package iThemes_Security
 */
class ITSEC_Online_Files {
	function run() {
		add_action( 'itsec-file-change-start-hash-comparisons', array( $this, 'load' ) );
		add_action( 'itsec-file-change-settings-form', array( $this, 'render_settings' ) );
		
		add_filter( 'itsec-file-change-sanitize-settings', array( $this, 'sanitize_settings' ) );
	}
	
	public function load() {
		if ( ! ITSEC_Modules::get_setting( 'online-files', 'compare_file_hashes' ) ) {
			return;
		}
		
		require_once( dirname( __FILE__ ) . '/comparison-engine.php' );
	}
	
	public function render_settings( $form ) {
		require_once( dirname( __FILE__ ) . '/custom-settings.php' );
		
		ITSEC_Online_Files_Custom_Settings::render_settings( $form );
	}
	
	public function sanitize_settings( $settings ) {
		require_once( dirname( __FILE__ ) . '/custom-settings.php' );
		
		return ITSEC_Online_Files_Custom_Settings::sanitize_settings( $settings );
	}
}
