<?php

/**
 * iThemes Security Core.
 *
 * Core class for iThemes Security sets up globals and other items and dispatches modules.
 *
 * @package iThemes_Security
 *
 * @since   4.0
 *
 * @global array  $itsec_globals Global variables for use throughout iThemes Security.
 * @global object $itsec_logger  iThemes Security logging class.
 * @global object $itsec_lockout Class for handling lockouts.
 *
 */
if ( ! class_exists( 'ITSEC_Core' ) ) {

	final class ITSEC_Core {

		private static $instance = false;

		/**
		 * This number keeps track of data format changes and triggers data upgrade handlers.
		 *
		 * @access private
		 */
		private $plugin_build = 4070;

		/**
		 * Used to distinguish between a user modifying settings and the API modifying settings (such as from Sync
		 * requests).
		 *
		 * @access private
		 */
		private $interactive = false;

		private $notices_loaded = false;
		private $doing_data_upgrade = false;

		private
			$itsec_files,
			$itsec_notify,
			$sync_api,
			$plugin_file,
			$plugin_dir,
			$plugin_name,
			$current_time,
			$current_time_gmt,
			$is_iwp_call,
			$request_type,
			$wp_upload_dir,
			$storage_dir;


		/**
		 * Private constructor to make this a singleton
		 *
		 * @access private
		 */
		private function __construct() {}

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
		 * Loads core functionality across both admin and frontend.
		 *
		 * Creates all plugin globals, registers activation and related hooks,
		 * loads the text domain and loads all plugin modules
		 *
		 * @since 4.0
		 *
		 * @access private
		 *
		 * @param string $plugin_file The main plugin file
		 * @param string $plugin_name The plugin name
		 *
		 */
		public function init( $plugin_file, $plugin_name ) {
			global $itsec_globals, $itsec_logger, $itsec_lockout;

			$this->plugin_file = $plugin_file;
			$this->plugin_dir = dirname( $plugin_file ) . '/';
			$this->plugin_name = $plugin_name;
			$this->current_time = current_time( 'timestamp' );
			$this->current_time_gmt = current_time( 'timestamp', true );

			$itsec_globals = array(
				'plugin_dir'       => $this->plugin_dir,
				'current_time'     => $this->current_time,
				'current_time_gmt' => $this->current_time_gmt,
			);

			register_activation_hook( $this->plugin_file, array( 'ITSEC_Core', 'handle_activation' ) );
			register_deactivation_hook( $this->plugin_file, array( 'ITSEC_Core', 'handle_deactivation' ) );
			register_uninstall_hook( $this->plugin_file, array( 'ITSEC_Core', 'handle_uninstall' ) );


			require( $this->plugin_dir . 'core/modules.php' );
			add_action( 'itsec-register-modules', array( $this, 'register_modules' ) );
			ITSEC_Modules::init_modules();

			require( $this->plugin_dir . 'core/lib.php' );
			require( $this->plugin_dir . 'core/logger.php' );
			require( $this->plugin_dir . 'core/lockout.php' );
			require( $this->plugin_dir . 'core/files.php' );
			require( $this->plugin_dir . 'core/notify.php' );
			require( $this->plugin_dir . 'core/response.php' );
			require( $this->plugin_dir . 'core/lib/class-itsec-lib-user-activity.php' );
			require( $this->plugin_dir . 'core/lib/class-itsec-lib-password-requirements.php' );

			$this->itsec_files = ITSEC_Files::get_instance();
			$this->itsec_notify = new ITSEC_Notify();
			$itsec_logger = new ITSEC_Logger();
			$itsec_lockout = new ITSEC_Lockout( $this );

			// Handle upgrade if needed.
			if ( ITSEC_Modules::get_setting( 'global', 'build' ) < $this->plugin_build ) {
				add_action( 'plugins_loaded', array( $this, 'handle_upgrade' ), -100 );
			}


			if ( is_admin() ) {
				require( $this->plugin_dir . 'core/admin-pages/init.php' );

				add_filter( 'plugin_action_links', array( $this, 'add_action_link' ), 10, 2 );
				add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links' ), 10, 4 );
			}


			add_action( 'plugins_loaded', array( $this, 'continue_init' ), -90 );
			add_action( 'wp_login_failed', array( 'ITSEC_Lib', 'handle_wp_login_failed' ) );

			add_action( 'ithemes_sync_register_verbs', array( $this, 'register_sync_verbs' ) );
		}

		/**
		 * Perform initialization that requires the plugins_loaded hook to be fired.
		 */
		public function continue_init() {
			ITSEC_Modules::run_active_modules();

			//Admin bar links
			if ( ! ITSEC_Modules::get_setting( 'global', 'hide_admin_bar' ) ) {
				add_action( 'admin_bar_menu', array( $this, 'modify_admin_bar' ), 99 );
			}

			$disabled = defined( 'ITSEC_DISABLE_PASSWORD_REQUIREMENTS') && ITSEC_DISABLE_PASSWORD_REQUIREMENTS;

			if ( ! $disabled && has_action( 'itsec_validate_password' ) ) {
				$pass_requirements = new ITSEC_Lib_Password_Requirements();
				$pass_requirements->run();
			}

			do_action( 'itsec_initialized' );
		}

		/**
		 * Retrieve the global instance of the files utility.
		 *
		 * @return ITSEC_Files
		 */
		public static function get_itsec_files() {
			$self = self::get_instance();
			return $self->itsec_files;
		}

		/**
		 * Retrieve the global instance of the user notification utility.
		 *
		 * @return ITSEC_Notify
		 */
		public static function get_itsec_notify() {
			$self = self::get_instance();
			return $self->itsec_notify;
		}

		/**
		 * Retrieve the global instance of the Sync API.
		 *
		 * The API is not available until iThemes Sync verbs have been registered ( init#11 ).
		 *
		 * @return Ithemes_Sync_API|null
		 */
		public static function get_sync_api() {
			$self = self::get_instance();
			return $self->sync_api;
		}

		/**
		 * Register ITSEC verbs with sync.
		 *
		 * @param Ithemes_Sync_API $sync_api
		 */
		public function register_sync_verbs( $sync_api ) {
			// For use by the itsec-get-everything verb as it has to run other verbs to get their details.
			$this->sync_api = $sync_api;

			$sync_api->register( 'itsec-get-everything', 'Ithemes_Sync_Verb_ITSEC_Get_Everything', dirname( __FILE__ ) . '/sync-verbs/itsec-get-everything.php' );
		}

		/**
		 * Register core modules.
		 */
		public function register_modules() {
			$path = dirname( __FILE__ );

			ITSEC_Modules::register_module( 'security-check', "$path/modules/security-check", 'always-active' );
			ITSEC_Modules::register_module( 'global', "$path/modules/global", 'always-active' );
			ITSEC_Modules::register_module( '404-detection', "$path/modules/404-detection" );
			ITSEC_Modules::register_module( 'away-mode', "$path/modules/away-mode" );
			ITSEC_Modules::register_module( 'ban-users', "$path/modules/ban-users", 'default-active' );
			include( "$path/modules/ban-users/init.php" ); // Provides the itsec_ban_users_handle_new_blacklisted_ip function which is always needed.
			ITSEC_Modules::register_module( 'brute-force', "$path/modules/brute-force", 'default-active' );
			ITSEC_Modules::register_module( 'core', "$path/modules/core", 'always-active' );
			ITSEC_Modules::register_module( 'backup', "$path/modules/backup", 'default-active' );
			ITSEC_Modules::register_module( 'file-change', "$path/modules/file-change" );
			ITSEC_Modules::register_module( 'file-permissions', "$path/modules/file-permissions", 'always-active' );
			ITSEC_Modules::register_module( 'hide-backend', "$path/modules/hide-backend", 'always-active' );
			ITSEC_Modules::register_module( 'network-brute-force', "$path/modules/ipcheck", 'default-active' );
			ITSEC_Modules::register_module( 'malware', "$path/modules/malware", 'always-active' );
			ITSEC_Modules::register_module( 'ssl', "$path/modules/ssl" );
			ITSEC_Modules::register_module( 'strong-passwords', "$path/modules/strong-passwords", 'default-active' );
			ITSEC_Modules::register_module( 'system-tweaks', "$path/modules/system-tweaks" );
			ITSEC_Modules::register_module( 'wordpress-tweaks', "$path/modules/wordpress-tweaks", 'default-active' );

			if ( is_multisite() ) {
				ITSEC_Modules::register_module( 'multisite-tweaks', "$path/modules/multisite-tweaks" );
			}

			ITSEC_Modules::register_module( 'admin-user', "$path/modules/admin-user", 'always-active' );
			ITSEC_Modules::register_module( 'wordpress-salts', "$path/modules/salts", 'always-active' );
			ITSEC_Modules::register_module( 'content-directory', "$path/modules/content-directory", 'always-active' );
			ITSEC_Modules::register_module( 'database-prefix', "$path/modules/database-prefix", 'always-active' );
			ITSEC_Modules::register_module( 'file-writing', "$path/modules/file-writing", 'always-active' );

			if ( ! ITSEC_Core::is_pro() ) {
				ITSEC_Modules::register_module( 'pro-module-upsells', "$path/modules/pro", 'always-active' );
			}
		}

		/**
		 * Add action link to plugin page.
		 *
		 * Adds plugin settings link to plugin page in WordPress admin area.
		 *
		 * @since 4.0
		 *
		 * @param array  $links Array of WordPress links
		 * @param string $file  String name of current file
		 *
		 * @return array Array of WordPress links
		 */
		public function add_action_link( $links, $file ) {

			static $this_plugin;

			if ( empty( $this_plugin ) ) {
				$this_plugin = str_replace( WP_PLUGIN_DIR . '/', '', self::get_plugin_file() );
			}

			if ( $file == $this_plugin ) {
				$settings_link = '<a href="' . esc_url( self::get_settings_page_url() ) . '">' . __( 'Settings', 'it-l10n-ithemes-security-pro' ) . '</a>';
				array_unshift( $links, $settings_link );
			}

			return $links;
		}

		/**
		 * Adds links to the plugin row meta
		 *
		 * @since 4.0
		 *
		 * @param array  $meta        Existing meta
		 * @param string $plugin_file the wp plugin slug (path)
		 *
		 * @return array
		 */
		public function add_plugin_meta_links( $meta, $plugin_file ) {

			$plugin_base = str_replace( WP_PLUGIN_DIR . '/', '', self::get_plugin_file() );

			if ( $plugin_base == $plugin_file ) {

				$meta = apply_filters( 'itsec_meta_links', $meta );

			}

			return $meta;
		}

		/**
		 * Add admin bar items
		 *
		 * @since 4.0
		 *
		 * @param WP_Admin_Bar $wp_admin_bar
		 *
		 * @return void
		 */
		public function modify_admin_bar( $wp_admin_bar ) {

			if ( ! ITSEC_Core::current_user_can_manage() ) {
				return;
			}

			// Add the Parent link.
			$wp_admin_bar->add_node(
				array(
					'title' => __( 'Security', 'it-l10n-ithemes-security-pro' ),
					'href'  => self::get_settings_page_url(),
					'id'    => 'itsec_admin_bar_menu',
				)
			);

			$wp_admin_bar->add_node(
				array(
					'parent' => 'itsec_admin_bar_menu',
					'title'  => __( 'Settings', 'it-l10n-ithemes-security-pro' ),
					'href'   => self::get_settings_page_url(),
					'id'     => 'itsec_admin_bar_settings',
				)
			);

			$wp_admin_bar->add_node(
				array(
					'parent' => 'itsec_admin_bar_menu',
					'title'  => __( 'Security Check', 'it-l10n-ithemes-security-pro' ),
					'href'   => self::get_security_check_page_url(),
					'id'     => 'itsec_admin_bar_security_check',
				)
			);

			$wp_admin_bar->add_node(
				array(
					'parent' => 'itsec_admin_bar_menu',
					'title'  => __( 'Logs', 'it-l10n-ithemes-security-pro' ),
					'href'   => self::get_logs_page_url(),
					'id'     => 'itsec_admin_bar_logs',
				)
			);
		}

		/**
		 * Dispatch a request to upgrade the data schema to another version.
		 *
		 * @param int|bool $build The version of the data storage format. Pass false to default to the current version.
		 */
		public function handle_upgrade( $build = false ) {
			$this->doing_data_upgrade = true;

			require_once( self::get_core_dir() . '/setup.php' );
			ITSEC_Setup::handle_upgrade( $build );
		}

		public static function handle_activation() {
			require_once( self::get_core_dir() . '/setup.php' );
			ITSEC_Setup::handle_activation();
		}

		public static function handle_deactivation() {
			require_once( self::get_core_dir() . '/setup.php' );
			ITSEC_Setup::handle_deactivation();
		}

		public static function handle_uninstall() {
			require_once( self::get_core_dir() . '/setup.php' );
			ITSEC_Setup::handle_uninstall();
		}

		/**
		 * Register a notice to be displayed in the WordPress admin.
		 *
		 * @param callable $callback Function that will render a notice.
		 * @param bool    $all_pages Display the notice on all pages or only on ITSEC, plugins, and upgrade page.
		 */
		public static function add_notice( $callback, $all_pages = false ) {
			global $pagenow, $plugin_page;

			if ( ! $all_pages && ! in_array( $pagenow, array( 'plugins.php', 'update-core.php' ) ) && ( ! isset( $plugin_page ) || ! in_array( $plugin_page, array( 'itsec', 'itsec-logs' ) ) ) ) {
				return;
			}

			$self = self::get_instance();

			if ( ! $self->notices_loaded ) {
				wp_enqueue_style( 'itsec-notice', plugins_url( 'core/css/itsec_notice.css', ITSEC_Core::get_core_dir() ), array(), '20160609' );
				wp_enqueue_script( 'itsec-notice', plugins_url( 'core/js/itsec-notice.js', ITSEC_Core::get_core_dir() ), array(), '20160512' );

				$self->notices_loaded = true;
			}

			if ( is_multisite() ) {
				add_action( 'network_admin_notices', $callback );
			} else {
				add_action( 'admin_notices', $callback );
			}
		}

		/**
		 * Get the required capability to manage ITSEC.
		 *
		 * @return string
		 */
		public static function get_required_cap() {
			return apply_filters( 'itsec_cap_required', is_multisite() ? 'manage_network_options' : 'manage_options' );
		}

		/**
		 * Does the current user have permission to manage ITSEC.
		 *
		 * @return bool
		 */
		public static function current_user_can_manage() {
			return current_user_can( self::get_required_cap() );
		}

		public static function get_plugin_file() {
			$self = self::get_instance();
			return $self->plugin_file;
		}

		public static function set_plugin_file( $plugin_file ) {
			$self = self::get_instance();
			$self->plugin_file = $plugin_file;
			$self->plugin_dir = dirname( $plugin_file ) . '/';
		}

		public static function get_plugin_build() {
			$self = self::get_instance();
			return $self->plugin_build;
		}

		public static function get_plugin_dir() {
			$self = self::get_instance();
			return $self->plugin_dir;
		}

		public static function get_core_dir() {
			return self::get_plugin_dir() . 'core/';
		}

		public static function get_plugin_name() {
			$self = self::get_instance();
			return $self->plugin_name;
		}

		/**
		 * Is this an iThemes Security Pro installation.
		 *
		 * This value is not cached.
		 *
		 * @return bool
		 */
		public static function is_pro() {
			return is_dir( self::get_plugin_dir() . 'pro' );
		}

		/**
		 * Get the current local timestamp.
		 *
		 * This value will be the same throughout the entire request.
		 *
		 * @return int
		 */
		public static function get_current_time() {
			$self = self::get_instance();
			return $self->current_time;
		}

		/**
		 * Get the current UTC timestamp.
		 *
		 * This value will be the same throughout the entire request.
		 *
		 * @return int
		 */
		public static function get_current_time_gmt() {
			$self = self::get_instance();
			return $self->current_time_gmt;
		}

		/**
		 * Get the UTC offset in seconds.
		 *
		 * @return int
		 */
		public static function get_time_offset() {
			$self = self::get_instance();
			return $self->current_time - $self->current_time_gmt;
		}

		public static function get_settings_page_url() {
			$url = network_admin_url( 'admin.php?page=itsec' );

			return $url;
		}

		public static function get_logs_page_url( $filter = false ) {
			$url = network_admin_url( 'admin.php?page=itsec-logs' );

			if ( ! empty( $filter ) ) {
				$url = add_query_arg( array( 'filter' => $filter ), $url );
			}

			return $url;
		}

		public static function get_backup_creation_page_url() {
			$url = network_admin_url( 'admin.php?page=itsec&module=backup' );

			$url = apply_filters( 'itsec-filter-backup-creation-page-url', $url );

			return $url;
		}

		public static function get_security_check_page_url() {
			return network_admin_url( 'admin.php?page=itsec&module=security-check' );
		}

		public static function get_settings_module_url( $module ) {
			return network_admin_url( 'admin.php?page=itsec&module=' . $module );
		}

		/**
		 * Specify whether there is a user modifying settings or if an API is modifying settings.
		 *
		 * @param bool $interactive
		 */
		public static function set_interactive( $interactive ) {
			$self = self::get_instance();
			$self->interactive = (bool) $interactive;
		}

		/**
		 * Is a user modifying settings or the API modifying settings (such as from Sync requests).
		 *
		 * @return bool
		 */
		public static function is_interactive() {
			$self = self::get_instance();
			return $self->interactive;
		}

		/**
		 * Determine whether the current request is an Infinite WP API call.
		 *
		 * @return bool
		 */
		public static function is_iwp_call() {
			$self = self::get_instance();

			if ( isset( $self->is_iwp_call ) ) {
				return $self->is_iwp_call;
			}


			$self->is_iwp_call = false;

			if ( false && ! ITSEC_Modules::get_setting( 'global', 'infinitewp_compatibility' ) ) {
				return false;
			}


			$post_data = @file_get_contents( 'php://input' );

			if ( ! empty( $post_data ) ) {
				$data = base64_decode( $post_data );

				if ( false !== strpos( $data, 's:10:"iwp_action";' ) ) {
					$self->is_iwp_call = true;
				}
			}

			return $self->is_iwp_call;
		}

		/**
		 * Get the configured WordPress upload directory of the main site.
		 *
		 * This value is cached for both the lifetime of the request and possibly indefinitely when WordPress is
		 * using an object cache.
		 *
		 * @see wp_upload_dir
		 *
		 * @return array
		 */
		public static function get_wp_upload_dir() {
			$self = self::get_instance();

			if ( isset( $self->wp_upload_dir ) ) {
				return $self->wp_upload_dir;
			}

			$wp_upload_dir = get_site_transient( 'itsec_wp_upload_dir' );

			if ( ! is_array( $wp_upload_dir ) || ! isset( $wp_upload_dir['basedir'] ) || ! is_dir( $wp_upload_dir['basedir'] ) ) {
				if ( is_multisite() ) {
					switch_to_blog( 1 );
					$wp_upload_dir = wp_upload_dir();
					restore_current_blog();
				} else {
					$wp_upload_dir = wp_upload_dir();
				}

				set_site_transient( 'itsec_wp_upload_dir', $wp_upload_dir, DAY_IN_SECONDS );
			}

			$self->wp_upload_dir = $wp_upload_dir;

			return $self->wp_upload_dir;
		}

		/**
		 * Set a new upload directory and ensure the previously cached value is cleared.
		 *
		 * @param string $old_dir
		 * @param string $new_dir
		 */
		public static function update_wp_upload_dir( $old_dir, $new_dir ) {
			$self = self::get_instance();

			// Prime caches.
			self::get_wp_upload_dir();

			$self->wp_upload_dir = str_replace( $old_dir, $new_dir, $self->wp_upload_dir );

			// Ensure that the transient will be regenerated on the next page load.
			delete_site_transient( 'itsec_wp_upload_dir' );
		}

		/**
		 * Retrieve and/or create a directory for ITSEC to store data.
		 *
		 * @param string $dir Optionally specify an additional sub-directory.
		 *
		 * @return string
		 */
		public static function get_storage_dir( $dir = '' ) {
			$self = self::get_instance();

			require_once( self::get_core_dir() . '/lib/class-itsec-lib-directory.php' );

			if ( ! isset( $self->storage_dir ) ) {
				$wp_upload_dir = self::get_wp_upload_dir();

				$self->storage_dir = $wp_upload_dir['basedir'] . '/ithemes-security/';
			}

			$dir = $self->storage_dir . $dir;
			$dir = rtrim( $dir, '/' );

			ITSEC_Lib_Directory::create( $dir );

			return $dir;
		}

		public static function doing_data_upgrade() {
			$self = self::get_instance();

			return $self->doing_data_upgrade;
		}

		public static function is_ajax_request() {
			if ( function_exists( 'wp_doing_ajax' ) ) {
				return wp_doing_ajax();
			}

			return defined( 'DOING_AJAX' ) && DOING_AJAX;
		}

		public static function is_xmlrpc_request() {
			return defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST;
		}

		/**
		 * Is this a WordPress REST API request.
		 *
		 * This function is suitable to be used immediately, not just after REST_REQUEST has been defined.
		 *
		 * @return bool
		 */
		public static function is_rest_api_request() {
			if ( isset( $GLOBALS['__itsec_core_is_rest_api_request'] ) ) {
				return $GLOBALS['__itsec_core_is_rest_api_request'];
			}

			if ( ! function_exists( 'rest_get_url_prefix' ) ) {
				$GLOBALS['__itsec_core_is_rest_api_request'] = false;
				return false;
			}

			if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
				$GLOBALS['__itsec_core_is_rest_api_request'] = true;
				return true;
			}

			$home_path = parse_url( get_option( 'home' ), PHP_URL_PATH );
			$home_path = trim( $home_path, '/' );

			$rest_api_path = "/$home_path/" . rest_get_url_prefix() . '/';

			if ( 0 === strpos( $_SERVER['REQUEST_URI'], $rest_api_path ) ) {
				$GLOBALS['__itsec_core_is_rest_api_request'] = true;
			} else {
				$GLOBALS['__itsec_core_is_rest_api_request'] = false;
			}

			return $GLOBALS['__itsec_core_is_rest_api_request'];
		}

		/**
		 * Is this a request to wp-admin/admin-post.php?
		 *
		 * @return bool
		 */
		public static function is_admin_post_php_request() {
			if ( 'wp-admin/admin-post.php' === ITSEC_Lib::get_request_path() ) {
				return true;
			}

			return false;
		}

		/**
		 * Is the current request being made by a WordPress API.
		 *
		 * @param bool $include_ajax           Whether to include Ajax requests as a subset of API requests.
		 * @param bool $include_admin_post_php Whether to include wp-admin/admin-post.php requests as a subset of API
		 *                                     requests.
		 *
		 * @return bool
		 */
		public static function is_api_request( $include_ajax = true, $include_admin_post_php = true ) {
			if ( $include_ajax && self::is_ajax_request() ) {
				return true;
			}

			if ( $include_admin_post_php && self::is_admin_post_php_request() ) {
				return true;
			}

			if ( self::is_rest_api_request() || self::is_xmlrpc_request() ) {
				return true;
			}

			return false;
		}
	}
}
