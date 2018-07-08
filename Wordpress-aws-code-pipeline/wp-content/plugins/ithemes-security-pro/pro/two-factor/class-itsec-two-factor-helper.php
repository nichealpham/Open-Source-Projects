<?php

/**
 * Two-Factor Helper Class
 *
 * Code that's needed for both front end and admin.
 *
 * @package iThemes_Security
 */
class ITSEC_Two_Factor_Helper {

	/**
	 * The name of the module's saved options setting
	 *
	 * @access private
	 * @var string
	 */
	private $_setting_name = 'itsec_two_factor';

	/**
	 * Array of two-factor providers
	 *
	 * @access private
	 * @var array where key is class name and value is file
	 */
	private $_providers;

	/**
	 * Array of instances of two-factor providers
	 *
	 * @access private
	 * @var array where key is class name and value is the instance of that class
	 */
	private $_provider_instances;

	/**
	 * @var ITSEC_Two_Factor_Helper - Static property to hold our singleton instance
	 */
	private static $instance = false;

	/**
	 * private construct to enforce singleton
	 */
	private function __construct() {

		require_once( 'class-itsec-two-factor-core-compat.php' );

		/**
		 * Include the base provider class here, so that other plugins can also extend it.
		 */
		require_once( 'providers/class.two-factor-provider.php' );

		/**
		 * Include the application passwords system.
		 */
		require_once( 'application-passwords.php' );
		ITSEC_Application_Passwords::add_hooks();

		if ( is_admin() ) {
			// Always instantiate enabled providers in admin for use in settings, etc
			add_action( 'init', array( $this, 'get_enabled_provider_instances' ) );
		} else {
			add_action( 'init', array( $this, 'get_all_providers' ) );
		}

	}

	/**
	 * Function to instantiate our class and make it a singleton
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Get a list of providers
	 *
	 * @return array where key is provider class name and value is the provider file
	 */
	public function get_all_providers( $refresh = false ) {
		if ( ! empty( $this->_providers ) && ! $refresh ) {
			return $this->_providers;
		}

		$this->_providers = array(
			'Two_Factor_Totp'         => 'providers/class.two-factor-totp.php',
			'Two_Factor_Email'        => 'providers/class.two-factor-email.php',
			'Two_Factor_Backup_Codes' => 'providers/class.two-factor-backup-codes.php',
		);

		/**
		 * Filter the supplied providers.
		 *
		 * This lets third-parties either remove providers (such as Email), or
		 * add their own providers (such as text message or Clef).
		 *
		 * @param array $providers A key-value array where the key is the class name, and
		 *                         the value is the path to the file containing the class.
		 */
		$this->_providers = apply_filters( 'two_factor_providers', $this->_providers );

		return $this->_providers;
	}

	/**
	 * Get a list of enabled providers
	 *
	 * @param bool $refresh Whether to bypass the in-memory cache.
	 *
	 * @return array where key is provider class name and value is the provider file
	 */
	public function get_enabled_providers( $refresh = false ) {
		if ( ! empty( $this->_enabled_providers ) && ! $refresh ) {
			return $this->_enabled_providers;
		}

		$settings = ITSEC_Modules::get_settings( 'two-factor' );
		$all_providers = $this->get_all_providers( $refresh );

		if ( 'all' === $settings['available_methods'] ) {
			$this->_enabled_providers = $all_providers;
		} else if ( 'not_email' === $settings['available_methods'] ) {
			$this->_enabled_providers = $all_providers;
			unset( $this->_enabled_providers['Two_Factor_Email'] );
		} else {
			$this->_enabled_providers = array_intersect_key( $all_providers, array_fill_keys( $settings['custom_available_methods'], '' ) );
		}


		/**
		 * Filter the supplied providers.
		 *
		 * This lets third-parties either remove providers (such as Email), or
		 * add their own providers (such as text message or Clef).
		 *
		 * @param array $providers A key-value array where the key is the class name, and
		 *                         the value is the path to the file containing the class.
		 */
		$this->_enabled_providers = apply_filters( 'enabled_two_factor_providers', $this->_enabled_providers );

		return $this->_enabled_providers;
	}

	/**
	 * Return a list of provider instances for all available providers.
	 *
	 * @param bool $refresh Whether to bypass the in-memory cache.
	 *
	 * @return array
	 */
	public function get_all_provider_instances( $refresh = false ) {
		if ( ! empty( $this->_provider_instances ) && ! $refresh ) {
			return $this->_provider_instances;
		}

		$this->_provider_instances = $this->_instantiate_providers( $this->get_all_providers( $refresh ) );

		return $this->_provider_instances;
	}

	/**
	 * Return a list of provider instances for all enabled providers.
	 *
	 * @param bool $refresh Whether to bypass the in-memory cache.
	 *
	 * @return array
	 */
	public function get_enabled_provider_instances( $refresh = false ) {
		if ( ! empty( $this->_enabled_provider_instances ) && ! $refresh ) {
			return $this->_enabled_provider_instances;
		}

		$this->_enabled_provider_instances = $this->_instantiate_providers( $this->get_enabled_providers( $refresh ) );

		return $this->_enabled_provider_instances;
	}

	private function _instantiate_providers( $providers ) {
		$provider_instances = array();

		foreach ( $providers as $class => $path ) {
			include_once( $path );

			/**
			 * Confirm that it's been successfully included before instantiating.
			 */
			if ( class_exists( $class ) ) {
				$provider_instances[ $class ] = call_user_func( array( $class, 'get_instance' ) );
			}
		}

		return $provider_instances;
	}

}
