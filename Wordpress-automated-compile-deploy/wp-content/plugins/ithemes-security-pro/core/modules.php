<?php

final class ITSEC_Modules {
	/**
	 * @var ITSEC_Modules - Static property to hold our singleton instance
	 */
	static $instance = false;

	private $_available_modules = false;
	private $_module_paths = array();
	private $_default_active_modules = array();
	private $_always_active_modules = array();
	private $_active_modules = false;
	private $_active_modules_list = false;
	private $_module_settings = false;
	private $_module_validators = false;
	private $_settings_files_loaded = false;

	protected function __construct() {
		require_once( dirname( __FILE__ ) . '/lib/settings.php' );
		require_once( dirname( __FILE__ ) . '/lib/storage.php' );

		// Action triggered from another part of Security which runs when the settings page is loaded.
		add_action( 'itsec-settings-page-init', array( $this, 'load_settings_page' ) );
		add_action( 'itsec-logs-page-init', array( $this, 'load_settings_page' ) );
	}

	/**
	 * Function to instantiate our class and make it a singleton
	 *
	 * @return ITSEC_Modules
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Registers a single module
	 *
	 * @static
	 *
	 * @param string $slug The unique slug to use for the module.
	 * @param string $path The absolute path to the module.
	 * @param string $type [optional] 'always-active', 'default-active', 'default-inactive' (default)
	 *
	 * @return bool True on success, false otherwise.
	 */
	public static function register_module( $slug, $path, $type = 'default-inactive' ) {
		$self = self::get_instance();
		$slug = sanitize_title_with_dashes( $slug );

		if ( ! is_dir( $path ) ) {
			trigger_error( sprintf( __( 'An attempt to register the %1$s module failed since the supplied path (%2$s) is invalid. This could indicate an invalid modification or incomplete installation of the iThemes Security plugin. Please reinstall the plugin and try again.', 'it-l10n-ithemes-security-pro' ), $slug, $path ) );
			return false;
		}

		$self->_module_paths[ $slug ] = $path;
		$self->_available_modules = array_keys( $self->_module_paths );

		if ( 'always-active' === $type ) {
			$self->_always_active_modules[$slug] = true;
		} else if ( 'default-active' === $type ) {
			$self->_default_active_modules[$slug] = true;
		}

		return true;
	}

	/**
	 * Deregisters a single module
	 *
	 * @static
	 *
	 * @param string $slug The unique slug to use for the module
	 *
	 * @return bool True on success, false otherwise.
	 */
	public static function deregister_module( $slug ) {
		$self = self::get_instance();
		$slug = sanitize_title_with_dashes( $slug );

		if ( isset( $self->_module_paths[ $slug ] ) ) {
			unset( $self->_module_paths[ $slug ] );
			$self->_available_modules = array_keys( $self->_module_paths );

			unset( $self->_always_active_modules[$slug] );
			unset( $self->_default_active_modules[$slug] );

			return true;
		}

		return false;
	}

	/**
	 * Update the stored paths for each module.
	 *
	 * This is predominantly used when changing the WordPress content directory.
	 *
	 * @param string $old_dir
	 * @param string $new_dir
	 */
	public static function update_module_paths( $old_dir, $new_dir ) {
		$self = self::get_instance();

		foreach ( $self->_module_paths as $slug => $path ) {
			$self->_module_paths[$slug] = str_replace( $old_dir, $new_dir, $path );
		}
	}

	/**
	 * Register a module's settings controller.
	 *
	 * @param ITSEC_Settings $settings
	 */
	public static function register_settings( $settings ) {
		$self = self::get_instance();
		$self->_module_settings[ $settings->get_id() ] = $settings;
	}

	/**
	 * Retrieve a module's settings controller.
	 *
	 * This will load a module's settings file if it has not yet been loaded.
	 *
	 * @param string $slug The module slug.
	 *
	 * @return ITSEC_Settings|null
	 */
	public static function get_settings_obj( $slug ) {
		$self = self::get_instance();

		if ( ! isset( $self->_module_settings[ $slug ] ) ) {
			self::load_module_file( 'settings.php', $slug );
		}

		if ( ! isset( $self->_module_settings[ $slug ] ) ) {
			return null;
		}

		return $self->_module_settings[ $slug ];
	}

	/**
	 * Get the default settings for a module.
	 *
	 * @param string $slug The module slug.
	 *
	 * @return array
	 */
	public static function get_defaults( $slug ) {
		$self = self::get_instance();

		$settings_obj = self::get_settings_obj( $slug );

		if ( is_null( $settings_obj ) || ! is_callable( array( $settings_obj, 'get_defaults' ) ) ) {
			return array();
		}

		return $settings_obj->get_defaults();
	}

	/**
	 * Retrieve the default value of specific setting in a module.
	 *
	 * @param string     $slug    The module slug.
	 * @param string     $name    The name of the setting.
	 * @param mixed|null $default Optionally, specify a default value to be used if the module did not declare one.
	 *
	 * @return mixed
	 */
	public static function get_default( $slug, $name, $default = null ) {
		$self = self::get_instance();

		$defaults = self::get_defaults( $slug );

		if ( isset( $defaults[$name] ) ) {
			return $defaults[$name];
		}

		return $default;
	}

	/**
	 * Retrieve all of the settings for a module.
	 *
	 * @param string $slug The module slug.
	 *
	 * @return array
	 */
	public static function get_settings( $slug ) {
		$self = self::get_instance();

		$settings_obj = self::get_settings_obj( $slug );

		if ( is_null( $settings_obj ) || ! is_callable( array( $settings_obj, 'get_all' ) ) ) {
			return array();
		}

		return $settings_obj->get_all();
	}

	/**
	 * Retrieve the value of a specific setting in a module.
	 *
	 * @param string     $slug    The module slug.
	 * @param string     $name    The name of the setting.
	 * @param mixed|null $default Optionally, specify a default value to be used if the requested setting does not
	 *                            exist.
	 *
	 * @return mixed
	 */
	public static function get_setting( $slug, $name, $default = null ) {
		$self = self::get_instance();

		$settings_obj = self::get_settings_obj( $slug );

		if ( is_null( $settings_obj ) || ! is_callable( array( $settings_obj, 'get' ) ) ) {
			return $default;
		}

		return $settings_obj->get( $name, $default );
	}

	/**
	 * Update all of a module's settings at once.
	 *
	 * The values will be validated, updated in-memory, and persisted.
	 *
	 * @param string $slug     The module slug.
	 * @param array  $settings New settings values.
	 *
	 * @return array|WP_Error
	 */
	public static function set_settings( $slug, $settings ) {
		$self = self::get_instance();

		$settings_obj = self::get_settings_obj( $slug );

		if ( is_null( $settings_obj ) || ! is_callable( array( $settings_obj, 'set_all' ) ) ) {
			$error = new WP_Error( 'itsec-modules-invalid-settings-object', sprintf( __( 'Unable to find a valid settings object for %s. Settings were unable to be saved.', 'it-l10n-ithemes-security-pro' ), $slug ) );
			ITSEC_Response::add_error( $error );

			return $error;
		}

		return $settings_obj->set_all( $settings );
	}

	/**
	 * Update a single setting in a module.
	 *
	 * The new value will be validated, updated- in-memory, and persisted.
	 *
	 * @param string $slug The module slug.
	 * @param string $name The setting name to updated.
	 * @param mixed $value The settings' new value.
	 *
	 * @return array|false
	 */
	public static function set_setting( $slug, $name, $value ) {
		$self = self::get_instance();

		$settings_obj = self::get_settings_obj( $slug );

		if ( is_null( $settings_obj ) || ! is_callable( array( $settings_obj, 'set_all' ) ) ) {
			trigger_error( sprintf( __( 'Unable to find a valid settings object for %s. Setting was unable to be saved.', 'it-l10n-ithemes-security-pro' ), $slug ) );
			return false;
		}

		return $settings_obj->set( $name, $value );
	}

	/**
	 * Register a module's validator controller.
	 *
	 * Only one validator per-module is supported.
	 *
	 * @param ITSEC_Validator $validator
	 */
	public static function register_validator( $validator ) {
		$self = self::get_instance();
		$self->_module_validators[ $validator->get_id() ] = $validator;
	}

	/**
	 * Retrieve the validator for a given module.
	 *
	 * This will load a module's validator component if not yet loaded.
	 *
	 * @param string $slug The module slug.
	 *
	 * @return ITSEC_Validator|null
	 */
	public static function get_validator( $slug ) {
		$self = self::get_instance();

		if ( ! isset( $self->_module_validators[ $slug ] ) ) {
			require_once( ITSEC_Core::get_core_dir() . '/lib/validator.php' );
			self::load_module_file( 'validator.php', $slug );
		}

		if ( ! isset( $self->_module_validators[ $slug ] ) ) {
			return null;
		}

		return $self->_module_validators[ $slug ];
	}

	/**
	 * Retrieve the slugs of all modules available to the plugin.
	 *
	 * This function is internally cached.
	 *
	 * @return string[]
	 */
	public static function get_available_modules() {
		$self = self::get_instance();

		if ( false !== $self->_available_modules ) {
			return $self->_available_modules;
		}

		if ( ! is_array( $self->_module_paths ) ) {
			$self->_module_paths = array();
		}

		$self->_available_modules = array_keys( $self->_module_paths );

		return $self->_available_modules;
	}

	/**
	 * Retrieve the slugs of all active modules.
	 *
	 * This includes user activated and default activated modules. The result is internally cached.
	 *
	 * @return string[]
	 */
	public static function get_active_modules() {
		$self = self::get_instance();

		if ( is_array( $self->_active_modules_list ) ) {
			return $self->_active_modules_list;
		}

		$self->_active_modules = get_site_option( 'itsec_active_modules', array() );

		if ( ! is_array( $self->_active_modules ) ) {
			$self->_active_modules = array();
		} else if ( isset( $self->_active_modules[0] ) ) {
			// Found data from an old format.
			foreach ( $self->_active_modules as $key => $value ) {
				if ( ! is_bool( $value ) ) {
					unset( $self->_active_modules[$key] );

					if ( ! isset( $self->_active_modules[$value] ) ) {
						$self->_active_modules[$value] = true;
					}
				}
			}

			if ( is_multisite() ) {
				update_site_option( 'itsec_active_modules', $self->_active_modules );
			} else {
				update_option( 'itsec_active_modules', $self->_active_modules );
			}
		}

		$default_active_modules = apply_filters( 'itsec-default-active-modules', array_keys( $self->_default_active_modules ) );

		if ( ! is_array( $default_active_modules ) ) {
			$default_active_modules = array();
		}

		foreach ( $default_active_modules as $module ) {
			if ( ! isset( $self->_active_modules[ $module ] ) ) {
				$self->_active_modules[ $module ] = true;
			}
		}

		$self->_active_modules_list = array();

		foreach ( $self->_active_modules as $module => $active ) {
			if ( $active ) {
				$self->_active_modules_list[] = $module;
			}
		}

		return $self->_active_modules_list;
	}

	/**
	 * Retrieve the slugs of all modules that are required to be active.
	 *
	 * @return string[]
	 */
	public static function get_always_active_modules() {
		$self = self::get_instance();
		return array_keys( $self->_always_active_modules );
	}

	/**
	 * Check if a module is configured to be always active.
	 *
	 * @param string $module_id The module slug.
	 *
	 * @return bool
	 */
	public static function is_always_active( $module_id ) {
		$self = self::get_instance();

		if ( ! empty( $self->_always_active_modules[$module_id] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if a module is active.
	 *
	 * @param string $module_id The module slug.
	 *
	 * @return bool
	 */
	public static function is_active( $module_id ) {
		$self = self::get_instance();

		if ( ! is_array( $self->_active_modules ) ) {
			self::get_active_modules();
		}

		if ( ! empty( $self->_always_active_modules[$module_id] ) ) {
			return true;
		}

		if ( isset( $self->_active_modules[ $module_id ] ) ) {
			return $self->_active_modules[ $module_id ];
		}

		return false;
	}

	/**
	 * Activate a single module using its ID
	 *
	 * @param string $module_id The ID of the module to activate
	 *
	 * @return bool|WP_Error If the module can be activated, true if it was previously active and false if it was
	 *                       previously inactive. If the module cannot be activated, a WP_Error object is returned.
	 */
	public static function activate( $module_id ) {
		$self = self::get_instance();

		if ( self::is_always_active( $module_id ) ) {
			return new WP_Error( 'itsec-modules-cannot-activate-always-active-module', sprintf( __( 'The %s module is a Core module and cannot be activated or deactivated.', 'it-l10n-ithemes-security-pro' ), $module_id ) );
		}

		if ( ! is_array( $self->_active_modules ) ) {
			self::get_active_modules();
		}

		$was_active = false;

		if ( isset( $self->_active_modules[ $module_id ] ) ) {
			$was_active = $self->_active_modules[ $module_id ];
		}

		self::load_module_file( 'activate.php', $module_id );

		$self->_active_modules[ $module_id ] = true;
		self::set_active_modules( $self->_active_modules );

		return $was_active;
	}

	/**
	 * Deactivate a single module using its ID
	 *
	 * @param string $module_id The ID of the module to deactivate
	 *
	 * @return bool|WP_Error If the module can be deactivated, true if it was previously active and false if it was
	 *                       previously inactive. If the module cannot be deactivated, a WP_Error object is returned.
	 */
	public static function deactivate( $module_id ) {
		$self = self::get_instance();

		if ( self::is_always_active( $module_id ) ) {
			return new WP_Error( 'itsec-modules-cannot-activate-always-active-module', sprintf( __( 'The %s module is a Core module and cannot be activated or deactivated.', 'it-l10n-ithemes-security-pro' ), $module_id ) );
		}

		if ( ! is_array( $self->_active_modules ) ) {
			self::get_active_modules();
		}

		$was_active = false;

		if ( isset( $self->_active_modules[ $module_id ] ) ) {
			$was_active = $self->_active_modules[ $module_id ];
		}

		self::load_module_file( 'deactivate.php', $module_id );

		$self->_active_modules[ $module_id ] = false;
		self::set_active_modules( $self->_active_modules );

		return $was_active;
	}

	/**
	 * Change the active and deactivate modules in bulk.
	 *
	 * The deactivation routine for no-longer active modules will NOT be run.
	 *
	 * @param string[] $new_active_modules
	 *
	 * @return bool
	 */
	public static function set_active_modules( $new_active_modules ) {
		$self = self::get_instance();

		if ( ! is_array( $new_active_modules ) ) {
			return false;
		}

		// Ensure that the new values are sane by using the current active modules as a starting set of defaults.
		self::get_active_modules();
		$self->_active_modules = array_merge( $self->_active_modules, $new_active_modules );

		$self->_active_modules_list = array();

		foreach ( $self->_active_modules as $module => $active ) {
			if ( $active ) {
				$self->_active_modules_list[] = $module;
			}
		}

		if ( is_multisite() ) {
			update_site_option( 'itsec_active_modules', $self->_active_modules );
		} else {
			update_option( 'itsec_active_modules', $self->_active_modules );
		}

		return true;
	}

	/**
	 * Attempt to load a module(s)'s file.
	 *
	 * The file will only be loaded once and will not error if does not exist.
	 *
	 * @param string $file    The file name to load, including extension.
	 * @param string|string[] $modules The modules to load the files from. Accepts either a module slug, an array of
	 *                                 module slugs, ':all' to load the files from all modules, or ':active' to load the
	 *                                 files from active modules.
	 *
	 * @return bool True if a module matching the $modules parameter is found, false otherwise.
	 */
	public static function load_module_file( $file, $modules = ':all' ) {
		$self = self::get_instance();

		if ( ':all' === $modules ) {
			$modules = self::get_available_modules();
		} else if ( ':active' === $modules ) {
			$modules = self::get_active_modules();

			$modules = array_merge( $modules, array_keys( $self->_always_active_modules ) );
			$modules = array_unique( $modules );
		} else if ( is_string( $modules ) ) {
			$modules = array( $modules );
		} else if ( ! is_array( $modules ) ) {
			return false;
		}

		foreach ( $modules as $module ) {
			if ( ! empty( $self->_module_paths[$module] ) && file_exists( "{$self->_module_paths[$module]}/{$file}" ) ) {
				include_once( "{$self->_module_paths[$module]}/{$file}" );
			}
		}

		return true;
	}

	/**
	 * Fires an action to begin the registration of modules.
	 */
	public static function init_modules() {
		do_action( 'itsec-register-modules' );
	}

	/**
	 * Load and run all active modules.
	 */
	public static function run_active_modules() {
		// The active.php file is for code that will only run when the module is active.
		self::load_module_file( 'active.php', ':active' );
	}

	/**
	 * Run the activation routine for all registered modules.
	 */
	public function run_activation() {
		self::load_module_file( 'setup.php' );

		do_action( 'itsec_modules_do_plugin_activation' );
	}

	/**
	 * Run the deactivation routine for all registered modules.
	 */
	public function run_deactivation() {
		self::load_module_file( 'setup.php' );

		do_action( 'itsec_modules_do_plugin_deactivation' );
	}

	/**
	 * Run the uninstall routine for all registered modules.
	 */
	public static function run_uninstall() {
		self::load_module_file( 'setup.php' );

		do_action( 'itsec_modules_do_plugin_uninstall' );
	}

	/**
	 * Run the upgrade routine for all registered modules.
	 *
	 * @param int $old_version
	 * @param int $new_version
	 */
	public function run_upgrade( $old_version, $new_version ) {
		self::load_module_file( 'setup.php' );

		do_action( 'itsec_modules_do_plugin_upgrade', $old_version, $new_version );
	}

	/**
	 * Load the settings controller for all registered modules.
	 *
	 * This function can only be run once per-request.
	 */
	public function load_settings_page() {
		if ( $this->_settings_files_loaded ) {
			return;
		}

		self::load_module_file( 'settings-page.php' );

		$this->_settings_files_loaded = true;
	}
}
ITSEC_Modules::get_instance();
