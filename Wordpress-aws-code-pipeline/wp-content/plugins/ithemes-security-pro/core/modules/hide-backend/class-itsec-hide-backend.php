<?php

class ITSEC_Hide_Backend {
	private $auth_cookie_expired = false;

	private $settings;
	private $action;

	public function run() {
		add_filter( 'itsec_filter_apache_server_config_modification', array( $this, 'filter_apache_server_config_modification' ) );
		add_filter( 'itsec_filter_litespeed_server_config_modification', array( $this, 'filter_apache_server_config_modification' ) );
		add_filter( 'itsec_filter_nginx_server_config_modification', array( $this, 'filter_nginx_server_config_modification' ) );


		$this->settings = ITSEC_Modules::get_settings( 'hide-backend' );

		if ( ! $this->settings['enabled'] ) {
			return;
		}


		$this->action = isset( $_GET['action'] ) ? $_GET['action'] : '';


		add_action( 'auth_cookie_expired', array( $this, 'auth_cookie_expired' ) );
		add_action( 'init', array( $this, 'handle_specific_page_requests' ), 1000 );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 11 );

		add_filter( 'site_url', array( $this, 'filter_generated_url' ), 100, 2 );
		add_filter( 'network_site_url', array( $this, 'filter_generated_url' ), 100, 2 );
		add_filter( 'wp_redirect', array( $this, 'filter_redirect' ) );
		add_filter( 'retrieve_password_message', array( $this, 'retrieve_password_message' ) );
		add_filter( 'comment_moderation_text', array( $this, 'filter_comment_moderation_text' ) );

		remove_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );
	}

	public function filter_redirect( $url ) {
		if ( 0 === strpos( $url, 'wp-login.php' ) && 'wp-login.php' !== $this->settings['slug'] ) {
			$search = 'wp-login.php';
			$replacement = $this->settings['slug'];
		} else if ( 0 === strpos( $url, 'wp-register.php' ) && 'wp-register.php' !== $this->settings['register'] ) {
			$search = 'wp-register.php';
			$replacement = $this->settings['register'];
		} else if ( 0 === strpos( $url, 'wp-signup.php' ) && 'wp-register.php' !== $this->settings['register'] ) {
			$search = 'wp-signup.php';
			$replacement = $this->settings['register'];
		}

		if ( isset( $replacement ) ) {
			$url = preg_replace( '/^' . preg_quote( $search, '/' ) . '/', $replacement, $url );
		}

		return $url;
	}

	public function filter_generated_url( $url, $path ) {
		if ( 'wp-login.php' === $path && 'wp-login.php' !== $this->settings['slug'] ) {
			$replacement = $this->settings['slug'];
		} else if ( in_array( $path, array( 'wp-register.php', 'wp-signup.php' ) ) && 'wp-register.php' !== $this->settings['register'] ) {
			$replacement = $this->settings['register'];
		}

		if ( isset( $replacement ) ) {
			$url = preg_replace( '/' . preg_quote( $path, '/' ) . '$/', $replacement, $url );
		}

		return $url;
	}

	/**
	 * Lets the module know that this is a reauthorization
	 *
	 * @since 4.1
	 *
	 * @return void
	 */
	public function auth_cookie_expired() {
		if ( $this->auth_cookie_expired ) {
			// Prevent infinite loops.
			return;
		}

		$this->auth_cookie_expired = true;
		wp_clear_auth_cookie();
	}

	/**
	 * Filters emailed comment moderation links to use modified login links with redirection.
	 *
	 * @since 4.5
	 *
	 * @param sting $notify_message Notification message
	 *
	 * @return string Notification message
	 */
	public function filter_comment_moderation_text( $notify_message ) {
		if ( ! preg_match_all( '|(https?:\/\/((.*)wp-admin(.*)))|', $notify_message, $urls ) ) {
			return $notify_message;
		}

		foreach ( $urls[0] as $url ) {
			$url = trim( $url );
			$notify_message = str_replace( $url, wp_login_url( $url ), $notify_message );
		}

		return $notify_message;
	}

	/**
	 * Block access to hidden pages and provide support for custom page slugs.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function handle_specific_page_requests() {
		if ( ITSEC_Core::is_api_request() ) {
			return;
		}

		$request_path = ITSEC_Lib::get_request_path();

		if ( $request_path === $this->settings['slug'] ) {
			$this->handle_login_page();
		} else if ( $request_path === $this->settings['register'] ) {
			$this->handle_registration_page();
		} else if ( in_array( $request_path, array( 'wp-register.php', 'wp-signup.php' ) ) ) {
			$this->handle_canonical_signup_page();
		} else if ( in_array( $request_path, array( 'wp-login', 'wp-login.php' ) ) ) {
			$this->handle_canonical_login_page();
		} else if ( 'wp-admin' === $request_path || 'wp-admin/' === substr( $request_path, 0, 9 ) ) {
			$this->handle_wp_admin_page();
		} else if ( ! empty( $_REQUEST['redirect_to'] ) && 'wp-admin/customize.php' === ITSEC_Lib::get_url_path( $_REQUEST['redirect_to'] ) ) {
			// I'm not sure why this was added. It should probably be removed.
			$this->handle_customize_theme_redirect();
		}
	}

	private function handle_login_page() {
		$post_logout_slug = trim( $this->settings['post_logout_slug'] );

		if ( ! is_user_logged_in() ) {
			//Add the login form

			if ( $this->action === $post_logout_slug ) {
				do_action( 'itsec_custom_login_slug' ); //add hook here for custom users
			}

			//suppress error messages due to timing
			error_reporting( 0 );
			@ini_set( 'display_errors', 0 );

			status_header( 200 );

			//don't allow domain mapping to redirect
			if ( defined( 'DOMAIN_MAPPING' ) && DOMAIN_MAPPING ) {
				remove_action( 'login_head', 'redirect_login_to_orig' );
			}

			if ( ! function_exists( 'login_header' ) ) {
				include( ABSPATH . 'wp-login.php' );
				exit;
			}
		} else if ( ! in_array( $this->action, array( 'logout', 'postpass', $post_logout_slug ) ) ) {
			//Just redirect them to the dashboard (for logged in users)

			if ( ! $this->auth_cookie_expired ) {
				wp_redirect( get_admin_url() );
				exit();
			}
		} else if ( in_array( $this->action, array( 'postpass', $post_logout_slug ) ) ) {
			//handle private posts for

			if ( $this->action === $post_logout_slug ) {
				do_action( 'itsec_custom_login_slug' ); //add hook here for custom users
			}

			//suppress error messages due to timing
			error_reporting( 0 );
			@ini_set( 'display_errors', 0 );

			status_header( 200 ); //its a good login page. make sure we say so

			//include the login page where we need it
			if ( ! function_exists( 'login_header' ) ) {
				include( ABSPATH . '/wp-login.php' );
				exit;
			}

			//Take them back to the page if we need to
			if ( isset( $_SERVER['HTTP_REFERRER'] ) ) {
				wp_redirect( sanitize_text_field( $_SERVER['HTTP_REFERRER'] ) );
				exit();
			}
		}
	}

	private function handle_registration_page() {
		if ( get_site_option( 'users_can_register' ) ) {
			wp_redirect( wp_login_url() . '?action=register' );
			exit;
		}
	}

	private function handle_canonical_signup_page() {
		if ( ! get_option( 'users_can_register' ) ) {
			$this->block_access();
		} else if ( 'wp-register.php' !== $this->settings['register'] ) {
			$this->block_access();
		}
	}

	private function handle_canonical_login_page() {
		if ( is_user_logged_in() ) {
			return;
		}

		if ( 'jetpack_json_api_authorization' === $this->action && has_filter( 'login_form_jetpack_json_api_authorization' ) ) {
			// Jetpack handles authentication for this action. Processing is left to it.
			return;
		}

		if ( 'jetpack-sso' === $this->action && has_filter( 'login_form_jetpack-sso' ) ) {
			// Jetpack's SSO redirects from wordpress.com to wp-login.php on the site. Only allow this process to
			// continue if they successfully log in, which should happen by login_init in Jetpack which happens just
			// before this action fires.
			add_action( 'login_form_jetpack-sso', array( $this, 'block_access_if_not_logged_in' ) );
			return;
		}

		$this->block_access();
	}

	private function handle_customize_theme_redirect() {
		$this->block_access();
	}

	private function handle_wp_admin_page() {
		if ( ! is_user_logged_in() ) {
			$this->block_access();
		}
	}

	public function block_access_if_not_logged_in() {
		if ( ! is_user_logged_in() ) {
			$this->block_access();
		}
	}

	private function block_access() {
		if ( $this->auth_cookie_expired ) {
			return;
		}

		$GLOBALS['itsec_is_old_admin'] = true;

		if ( $this->settings['theme_compat'] ) {
			// Theme compat (process theme and redirect to a 404)
			wp_redirect( ITSEC_Lib::get_home_root() . $this->settings['theme_compat_slug'], 302 );
			exit;
		} else {
			// Throw a 403 forbidden
			wp_die( __( 'This has been disabled.', 'it-l10n-ithemes-security-pro' ), 403 );
		}
	}

	/**
	 * Actions for plugins loaded.
	 *
	 * Makes certain logout is processed on NGINX.
	 *
	 * @return void
	 */
	public function plugins_loaded() {
		if ( 'logout' !== $this->action || ! is_user_logged_in() ) {
			return;
		}

		$request_path = ITSEC_Lib::get_request_path();

		if ( ! in_array( $request_path, array( $this->settings['slug'], 'wp-login.php' ) ) ) {
			// Only try to process requests for login pages.
			return;
		}

		check_admin_referer( 'log-out' );
		wp_logout();

		$redirect_to = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : 'wp-login.php?loggedout=true';
		wp_safe_redirect( $redirect_to );
		exit();
	}

	public function retrieve_password_message( $message ) {

		return str_replace( 'wp-login.php', $this->settings['slug'], $message );

		return $message;

	}

	public function filter_apache_server_config_modification( $modification ) {
		require_once( dirname( __FILE__ ) . '/config-generators.php' );

		return ITSEC_Hide_Backend_Config_Generators::filter_apache_server_config_modification( $modification );
	}

	public function filter_nginx_server_config_modification( $modification ) {
		require_once( dirname( __FILE__ ) . '/config-generators.php' );

		return ITSEC_Hide_Backend_Config_Generators::filter_nginx_server_config_modification( $modification );
	}
}
