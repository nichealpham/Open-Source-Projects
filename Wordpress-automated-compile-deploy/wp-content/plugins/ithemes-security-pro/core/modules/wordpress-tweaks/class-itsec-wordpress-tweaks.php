<?php

final class ITSEC_WordPress_Tweaks {
	private static $instance = false;

	private $config_hooks_added = false;
	private $settings;
	private $first_xmlrpc_credentials;


	private function __construct() {
		$this->init();
	}

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function activate() {
		$self = self::get_instance();

		$self->add_config_hooks();
		ITSEC_Response::regenerate_server_config();
		ITSEC_Response::regenerate_wp_config();
	}

	public static function deactivate() {
		$self = self::get_instance();

		$self->remove_config_hooks();
		ITSEC_Response::regenerate_server_config();
		ITSEC_Response::regenerate_wp_config();
	}

	public function add_config_hooks() {
		if ( $this->config_hooks_added ) {
			return;
		}

		add_filter( 'itsec_filter_apache_server_config_modification', array( $this, 'filter_apache_server_config_modification' ) );
		add_filter( 'itsec_filter_nginx_server_config_modification', array( $this, 'filter_nginx_server_config_modification' ) );
		add_filter( 'itsec_filter_litespeed_server_config_modification', array( $this, 'filter_litespeed_server_config_modification' ) );
		add_filter( 'itsec_filter_wp_config_modification', array( $this, 'filter_wp_config_modification' ) );

		$this->config_hooks_added = true;
	}

	public function remove_config_hooks() {
		remove_filter( 'itsec_filter_apache_server_config_modification', array( $this, 'filter_apache_server_config_modification' ) );
		remove_filter( 'itsec_filter_nginx_server_config_modification', array( $this, 'filter_nginx_server_config_modification' ) );
		remove_filter( 'itsec_filter_litespeed_server_config_modification', array( $this, 'filter_litespeed_server_config_modification' ) );
		remove_filter( 'itsec_filter_wp_config_modification', array( $this, 'filter_wp_config_modification' ) );

		$this->config_hooks_added = false;
	}

	public function init() {
		$this->add_config_hooks();


		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			// Don't risk blocking anything with WP_CLI.
			return;
		}


		$this->settings = ITSEC_Modules::get_settings( 'wordpress-tweaks' );

		add_action( 'wp_print_scripts', array( $this, 'store_jquery_version' ) );

		// Functional code for the allow_xmlrpc_multiauth setting.
		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST && ! $this->settings['allow_xmlrpc_multiauth'] ) {
			add_filter( 'authenticate', array( $this, 'block_multiauth_attempts' ), 0, 3 );
		}

		//remove wlmanifest link if turned on
		if ( $this->settings['wlwmanifest_header'] ) {
			remove_action( 'wp_head', 'wlwmanifest_link' );
		}

		//remove rsd link from header if turned on
		if ( $this->settings['edituri_header'] ) {
			remove_action( 'wp_head', 'rsd_link' );
		}

		//Disable XML-RPC
		if ( 2 == $this->settings['disable_xmlrpc'] ) {
			add_filter( 'xmlrpc_enabled', '__return_null' );
			add_filter( 'bloginfo_url', array( $this, 'remove_pingback_url' ), 10, 2 );
		} else if ( 1 == $this->settings['disable_xmlrpc'] ) { // Disable pingbacks
			add_filter( 'xmlrpc_methods', array( $this, 'xmlrpc_methods' ) );
		}

		add_filter( 'rest_dispatch_request', array( $this, 'filter_rest_dispatch_request' ), 10, 4 );

		if ( $this->settings['safe_jquery'] ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'current_jquery' ) );
		}

		//Process remove login errors
		if ( $this->settings['login_errors'] ) {
			add_filter( 'login_errors', '__return_null' );
		}

		//Process require unique nicename
		if ( $this->settings['force_unique_nicename'] ) {
			add_action( 'user_profile_update_errors', array( $this, 'force_unique_nicename' ), 10, 3 );
		}

		//Process remove extra author archives
		if ( $this->settings['disable_unused_author_pages'] ) {
			add_action( 'template_redirect', array( $this, 'disable_unused_author_pages' ) );
		}

		if ( $this->settings['block_tabnapping'] ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'add_block_tabnapping_script' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'add_block_tabnapping_script' ) );
		}
	}

	/**
	 * Require capabilities for reading from WordPress object routes.
	 *
	 * @param null|WP_REST_Response|WP_Error $result
	 * @param WP_REST_Request                $request
	 * @param string                         $route_regex
	 * @param array                          $handler
	 *
	 * @return WP_Error
	 */
	public function filter_rest_dispatch_request( $result, $request, $route_regex, $handler ) {
		if ( in_array( $this->settings['rest_api'], array( 'enable', 'default-access' ) ) ) {
			return $result;
		}

		$route = $request->get_route();
		$route_parts = explode( '/', trim( $route, '/' ) );

		if ( 'wp' !== $route_parts[0] ) {
			// Only interested in the wp endpoints for now.
			return $result;
		}

		if ( ! isset( $route_parts[2] ) ) {
			// Only interested in requests that extend beyond the wp/v2 endpoint.
			return $result;
		}

		if ( 'settings' === $route_parts[2] ) {
			// The settings endpoint requires specific capabilities already.
			return $result;
		}

		// Each of the following endpoints can be restricted based on a simple capability check.
		$endpoint_caps = array(
			'comments'   => 'moderate_comments',
			'statuses'   => 'edit_posts',
			'taxonomies' => 'edit_terms',
			'types'      => 'edit_posts',
		);

		foreach ( $endpoint_caps as $endpoint => $cap ) {
			if ( $endpoint === $route_parts[2] ) {
				if ( current_user_can( $cap ) ) {
					return $result;
				}

				return new WP_Error( 'itsec_rest_api_access_restricted', __( 'You do not have sufficient permission to access this endpoint. Access to REST API requests is restricted by iThemes Security settings.', 'it-l10n-ithemes-security-pro' ) );
			}
		}

		if ( 'users' === $route_parts[2] ) {
			if ( isset( $route_parts[3] ) && 'me' === $route_parts[3] ) {
				// The users/me endpoint has its own permissions checks.
				return $result;
			}

			if ( current_user_can( 'list_users' ) ) {
				// All other users endpoints can be restricted to those with the list_users cap.
				return $result;
			}

			return new WP_Error( 'itsec_rest_api_access_restricted', __( 'You do not have sufficient permission to access this endpoint. Access to REST API requests is restricted by iThemes Security settings.', 'it-l10n-ithemes-security-pro' ) );
		}


		// Pulling the specific taxonomy or post type object out for proper cap checking is a bit complex.

		if ( is_array( $handler['callback'] ) && isset( $handler['callback'][0] ) && is_object( $handler['callback'][0] ) ) {
			// Get the callback object if one exists.
			$callback_object = $handler['callback'][0];
		} else {
			return $result;
		}

		if ( is_a( $callback_object, 'WP_REST_Terms_Controller' ) ) {
			// The callback handles requests for terms, so we know that the request is for a term.

			// Get the registered taxonomies.
			$taxonomies = get_taxonomies( array(), 'objects' );

			foreach ( $taxonomies as $taxonomy ) {
				// Find the taxonomy that matches the request.

				if ( ( isset( $taxonomy->rest_base ) && $taxonomy->rest_base === $route_parts[2] ) || $taxonomy->name === $route_parts[2] ) {
					// This is the requested taxonomy. Check to ensure that the current user can edit this taxonomy.
					if ( current_user_can( $taxonomy->cap->edit_terms ) ) {
						return $result;
					} else {
						return new WP_Error( 'itsec_rest_api_access_restricted', __( 'You do not have sufficient permission to access this endpoint. Access to REST API requests is restricted by iThemes Security settings.', 'it-l10n-ithemes-security-pro' ) );
					}
				}
			}

			return $result;
		}

		if ( is_a( $callback_object, 'WP_REST_Posts_Controller' ) ) {
			// The callback handles requests for post types, so we know that the request is for a post type.

			// Get the registered post types
			$post_types = get_post_types( array(), 'objects' );

			foreach ( $post_types as $post_type ) {
				// Find the post type that matches the request.

				if ( ( isset( $post_type->rest_base ) && $post_type->rest_base === $route_parts[2] ) || $post_type->name === $route_parts[2] ) {
					// This is the requested post type. Check to ensure that the current user can edit this post type.
					if ( current_user_can( $post_type->cap->edit_posts ) ) {
						return $result;
					} else {
						return new WP_Error( 'itsec_rest_api_access_restricted', __( 'You do not have sufficient permission to access this endpoint. Access to REST API requests is restricted by iThemes Security settings.', 'it-l10n-ithemes-security-pro' ) );
					}
				}
			}

			return $result;
		}


		// We don't have any specific rules to handle this request, default to doing nothing.
		return $result;
	}


	public function add_block_tabnapping_script() {
		wp_enqueue_script( 'blankshield', plugins_url( 'js/blankshield/blankshield.min.js', __FILE__ ), array(), ITSEC_Core::get_plugin_build(), true );
		wp_enqueue_script( 'itsec-wt-block-tabnapping', plugins_url( 'js/block-tabnapping.js', __FILE__ ), array( 'blankshield' ), ITSEC_Core::get_plugin_build(), true );
	}

	/**
	 * Prevent an attacker from trying multiple login credentials in a single XML-RPC request.
	 *
	 * @param WP_User|WP_Error|null $filter_val
	 * @param string                $username
	 * @param string                $password
	 *
	 * @return null|\WP_User|\WP_Error
	 */
	public function block_multiauth_attempts( $filter_val, $username, $password ) {
		if ( empty( $this->first_xmlrpc_credentials ) ) {
			$this->first_xmlrpc_credentials = array(
				$username,
				$password
			);

			return $filter_val;
		}

		if ( $username === $this->first_xmlrpc_credentials[0] && $password === $this->first_xmlrpc_credentials[1] ) {
			return $filter_val;
		}

		status_header( 405 );
		header( 'Content-Type: text/plain' );
		die( __( 'XML-RPC services are disabled on this site.' ) );
	}

	/**
	 * Attempt to force the core version of jQuery to be loaded.
	 *
	 * This will deregister the current version of jQuery and re-enqueue with the core version of the script.
	 *
	 * This could probably be refactored to use the 'script_loader_src' filter.
	 */
	public function current_jquery() {

		global $itsec_is_old_admin;

		if ( ! is_admin() && ! $itsec_is_old_admin ) {

			wp_deregister_script( 'jquery' );
			wp_deregister_script( 'jquery-core' );

			wp_register_script( 'jquery', false, array( 'jquery-core', 'jquery-migrate' ), '1.11.0' );
			wp_register_script( 'jquery-core', '/' . WPINC . '/js/jquery/jquery.js', false, '1.11.0' );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-core' );

		}

	}

	/**
	 * Redirects to 404 page if the requested author has 0 posts.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function disable_unused_author_pages() {

		global $wp_query;

		if ( is_author() && $wp_query->post_count < 1 ) {

			ITSEC_Lib::set_404();

		}

	}

	/**
	 * Requires a user's nicename to be distinct from their username.
	 *
	 * This helps to prevent username leaking.
	 *
	 * @since 4.0
	 *
	 * @param \WP_Error $errors
	 * @param bool      $update
	 * @param \WP_User  $user
	 *
	 * @return void
	 */
	public function force_unique_nicename( &$errors, $update, &$user ) {

		$display_name = isset( $user->display_name ) ? $user->display_name : wp_generate_password( 14, false );

		if ( ! empty( $user->nickname ) ) {

			if ( $user->nickname == $user->user_login ) {

				$errors->add( 'user_error', __( 'Your Nickname must be different than your login name. Please choose a different Nickname.', 'it-l10n-ithemes-security-pro' ) );

			} else {

				$user->user_nicename = sanitize_title( $user->nickname, $display_name );

			}

		} elseif ( ! empty( $user->first_name ) && ! empty( $user->last_name ) ) {

			$full_name = $user->first_name . ' ' . $user->last_name;

			$user->nickname = $full_name;

			$user->user_nicename = sanitize_title( $full_name, $display_name );

		} else {

			$errors->add( 'user_error', __( 'A Nickname is required. Please choose a nickname or fill out your first and last name.', 'it-l10n-ithemes-security-pro' ) );

		}

	}

	/**
	 * Gets the version of jQuery enqueued
	 */
	function store_jquery_version() {
		global $wp_scripts;

		if ( ( is_home() || is_front_page() ) && is_user_logged_in() ) {
			$stored_jquery_version = ITSEC_Modules::get_setting( 'wordpress-tweaks', 'jquery_version' );
			$current_jquery_version = $wp_scripts->registered['jquery']->ver;

			if ( $current_jquery_version !== $stored_jquery_version ) {
				ITSEC_Modules::set_setting( 'wordpress-tweaks', 'jquery_version', $current_jquery_version );
			}
		}
	}

	/**
	 * Removes the pingback header
	 *
	 * @param string $output
	 * @param string $show
	 *
	 * @return array
	 */
	function remove_pingback_url( $output, $show ) {

		if ( $show == 'pingback_url' ) {
			$output = '';
		}

		return $output;
	}

	/**
	 * removes version number on header scripts
	 *
	 * @param string $src script source link
	 *
	 * @return string script source link without version
	 */
	function remove_script_version( $src ) {

		if ( strpos( $src, 'ver=' ) ) {
			return substr( $src, 0, strpos( $src, 'ver=' ) - 1 );
		} else {
			return $src;
		}

	}

	/**
	 * Removes the pingback ability from XML-RPC
	 *
	 * @since 4.0.20
	 *
	 * @param array $methods XML-RPC methods
	 *
	 * @return array XML-RPC methods
	 */
	public function xmlrpc_methods( $methods ) {

		if ( isset( $methods['pingback.ping'] ) ) {
			unset( $methods['pingback.ping'] );
		}

		if ( isset( $methods['pingback.extensions.getPingbacks'] ) ) {
			unset( $methods['pingback.extensions.getPingbacks'] );
		}

		return $methods;

	}


	public function filter_wp_config_modification( $modification ) {
		require_once( dirname( __FILE__ ) . '/config-generators.php' );

		return ITSEC_WordPress_Tweaks_Config_Generators::filter_wp_config_modification( $modification );
	}

	public function filter_apache_server_config_modification( $modification ) {
		require_once( dirname( __FILE__ ) . '/config-generators.php' );

		return ITSEC_WordPress_Tweaks_Config_Generators::filter_apache_server_config_modification( $modification );
	}

	public function filter_nginx_server_config_modification( $modification ) {
		require_once( dirname( __FILE__ ) . '/config-generators.php' );

		return ITSEC_WordPress_Tweaks_Config_Generators::filter_nginx_server_config_modification( $modification );
	}

	public function filter_litespeed_server_config_modification( $modification ) {
		require_once( dirname( __FILE__ ) . '/config-generators.php' );

		return ITSEC_WordPress_Tweaks_Config_Generators::filter_litespeed_server_config_modification( $modification );
	}
}


ITSEC_WordPress_Tweaks::get_instance();
