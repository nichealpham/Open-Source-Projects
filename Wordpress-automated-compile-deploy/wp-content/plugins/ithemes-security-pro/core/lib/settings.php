<?php

abstract class ITSEC_Settings {
	protected $settings;
	
	public function __construct() {
		$this->load();
		
		add_action( 'itsec-lib-clear-caches', array( $this, 'load' ), 0 );
	}
	
	abstract public function get_id();
	abstract public function get_defaults();
	protected function after_save() {}
	protected function handle_settings_changes( $old_settings ) {}
	
	public function export() {
		return $this->settings;
	}
	
	public function import( $settings ) {
		$this->set_all( $settings );
	}
	
	public function get( $name, $default = null ) {
		if ( isset( $this->settings[$name] ) ) {
			return $this->settings[$name];
		}
		
		return $default;
	}
	
	public function get_all() {
		return $this->settings;
	}
	
	public function set( $name, $value ) {
		$settings = $this->settings;
		$settings[$name] = $value;
		
		return $this->set_all( $settings );
	}
	
	public function set_all( $settings ) {
		$retval = array(
			'old_settings' => $this->settings,
			'new_settings' => $this->settings,
			'errors'       => array(),
			'messages'     => array(),
			'saved'        => false,
		);
		
		$validator = ITSEC_Modules::get_validator( $this->get_id() );
		
		if ( is_null( $validator ) ) {
			$retval['errors'][] = new WP_Error( 'itsec-settings-missing-validator-for-' . $this->get_id(), sprintf( __( 'The data validator for %1$s is missing. Data for the module cannot be saved without the validator. This error could indicate a bad install of iThemes Security. Please remove the plugin and reinstall it. If this message persists, please contact support and send them this error message.', 'it-l10n-ithemes-security-pro' ), $this->get_id() ) );
		} else {
			$validator->validate( $settings );
			
			$retval['errors'] = $validator->get_errors();
			$retval['messages'] = $validator->get_messages();
			
			if ( $validator->can_save() ) {
				$this->settings = $validator->get_settings();
				
				ITSEC_Storage::set( $this->get_id(), $this->settings );
				$this->after_save();
				$this->handle_settings_changes( $retval['old_settings'] );
				
				$retval['new_settings'] = $this->settings;
				$retval['saved'] = true;
			} else {
				ITSEC_Response::set_success( false );
			}
		}
		
		ITSEC_Response::add_errors( $retval['errors'] );
		ITSEC_Response::add_messages( $retval['messages'] );
		
		return $retval;
	}
	
	public function load() {
		$this->settings = ITSEC_Storage::get( $this->get_id() );
		$defaults = $this->get_defaults();
		
		if ( ! is_array( $this->settings ) ) {
			$this->settings = array();
		}
		
		$this->settings = array_merge( $defaults, $this->settings );
	}
}
