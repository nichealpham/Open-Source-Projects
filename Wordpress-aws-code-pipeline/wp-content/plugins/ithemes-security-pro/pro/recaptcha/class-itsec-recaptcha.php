<?php

class ITSEC_Recaptcha {

	private $settings;

	public function run() {
		// Run on init so that we can use is_user_logged_in()
		// Warning: BuddyPress has issues with using is_user_logged_in() on plugins_loaded
		add_action( 'init', array( $this, 'setup' ) );

		add_filter( 'itsec_lockout_modules', array( $this, 'register_lockout_module' ) );
		add_filter( 'itsec_logger_modules', array( $this, 'register_logger_module' ) );
	}

	public function setup() {
		$this->settings = ITSEC_Modules::get_settings( 'recaptcha' );

		if ( empty( $this->settings['site_key'] ) || empty( $this->settings['secret_key'] ) ) {
			// Only run when the settings are fully filled out.
			return;
		}


		// Logged in users are people, we don't need to re-verify
		if ( is_user_logged_in() ) {
			if ( is_multisite() ) {
				add_action( 'network_admin_notices', array( $this, 'show_last_error' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'show_last_error' ) );
			}

			return;
		}


		add_action( 'login_enqueue_scripts', array( $this, 'login_enqueue_scripts' ) );

		if ( $this->settings['comments'] ) {

			if ( version_compare( $GLOBALS['wp_version'], '4.2', '>=' ) ) {
				add_filter( 'comment_form_submit_button', array( $this, 'comment_form_submit_button' ) );
			} else {
				add_filter( 'comment_form_field_comment', array( $this, 'comment_form_field_comment' ) );
			}
			add_filter( 'preprocess_comment', array( $this, 'filter_preprocess_comment' ) );

		}

		if ( $this->settings['login'] ) {

			add_action( 'login_form', array( $this, 'login_form' ) );
			add_filter( 'wp_authenticate_user', array( $this, 'filter_wp_authenticate_user' ) );

		}

		if ( $this->settings['register'] ) {

			add_action( 'register_form', array( $this, 'register_form' ) );
			add_filter( 'registration_errors', array( $this, 'registration_errors' ) );

		}

	}

	public function show_last_error() {
		if ( ! ITSEC_Core::current_user_can_manage() || $this->settings['validated'] || empty( $this->settings['last_error'] ) ) {
			return;
		}

		echo '<div class="error"><p><strong>';
		printf( wp_kses( __( 'The reCAPTCHA settings for iThemes Security are invalid. %1$s Bots will not be blocked until <a href="%2$s">the reCAPTCHA settings</a> are set properly.', 'it-l10n-ithemes-security-pro' ), array( 'a' => array( 'href' => array() ) ) ), esc_html( $this->settings['last_error'] ), ITSEC_Core::get_settings_module_url( 'recaptcha' ) );
		echo '</strong></p></div>';
	}

	/**
	 * Add recaptcha form to comment form
	 *
	 * @since 1.17
	 *
	 * @param string  $comment_field The comment field in the comment form
	 *
	 * @return string The comment field with our recaptcha field appended
	 */
	public function comment_form_field_comment( $comment_field ) {

		$comment_field .= $this->get_recaptcha();

		return $comment_field;

	}

	/**
	 * Preferred method to add recaptcha form to comment form. Used in WP 4.2+
	 *
	 * @since 1.17
	 *
	 * @param string  $submit_button The submit button in the comment form
	 *
	 * @return string The submit button with our recaptcha field prepended
	 */
	public function comment_form_submit_button( $submit_button ) {

		$submit_button = $this->get_recaptcha() . $submit_button;

		return $submit_button;

	}

	/**
	 * Add appropriate scripts to login page
	 *
	 * @since 1.13
	 *
	 * @return void
	 */
	public function login_enqueue_scripts() {

		wp_enqueue_style( 'itsec-recaptcha', plugin_dir_url( __FILE__ ) . 'css/itsec-recaptcha.css', array(), ITSEC_Core::get_plugin_build() );

	}

	/**
	 * Add the recaptcha field to the login form
	 *
	 * @since 1.13
	 *
	 * @return void
	 */
	public function login_form() {

		$this->show_recaptcha();

	}

	/**
	 * Process recaptcha for comments
	 *
	 * @since 1.13
	 *
	 * @param array $comment_data Comment data.
	 *
	 * @return array Comment data.
	 */
	public function filter_preprocess_comment( $comment_data ) {

		$result = $this->validate_captcha();

		if ( is_wp_error( $result ) ) {
			wp_die( $result->get_error_message() );
		}

		return $comment_data;

	}

	/**
	 * Add the recaptcha field to the registration form
	 *
	 * @since 1.13
	 *
	 * @return void
	 */
	public function register_form() {

		$this->show_recaptcha();

	}

	/**
	 * Set the registration error if captcha wasn't validated
	 *
	 * @since 1.13
	 *
	 * @param WP_Error $errors               A WP_Error object containing any errors encountered
	 *                                       during registration.
	 *
	 * @return WP_Error A WP_Error object containing any errors encountered
	 *                                       during registration.
	 */
	public function registration_errors( $errors ) {

		$result = $this->validate_captcha();

		if ( is_wp_error( $result ) ) {
			$errors->add( $result->get_error_code(), $result->get_error_message() );
		}

		return $errors;

	}

	// Leave this in as iThemes Exchange relies upon it.
	public function show_field( $echo = true, $deprecated1 = true, $margin_top = 0, $margin_right = 0, $margin_bottom = 0, $margin_left = 0, $deprecated2 = null ) {
		if ( $echo ) {
			$this->show_recaptcha( $margin_top, $margin_right, $margin_bottom, $margin_left );
		} else {
			return $this->get_recaptcha( $margin_top, $margin_right, $margin_bottom, $margin_left );
		}
	}

	public function show_recaptcha( $margin_top = 10, $margin_right = 0, $margin_bottom = 10, $margin_left = 0 ) {
		echo $this->get_recaptcha( $margin_top, $margin_right, $margin_bottom, $margin_left );
	}

	public function get_recaptcha( $margin_top = 0, $margin_right = 0, $margin_bottom = 0, $margin_left = 0 ) {
		$script = 'https://www.google.com/recaptcha/api.js';

		$query_args = array();

		if ( ! empty( $this->settings['language'] ) ) {
			$query_args['hl'] = $this->settings['language'];
		}

		if ( ! empty( $query_args ) ) {
			$script .= '?' . http_build_query( $query_args, '', '&' );
		}

		$recaptcha = '<script src="' . esc_url( $script ) . '" async defer></script>';


		if ( 'invisible' === $this->settings['type'] ) {
			wp_enqueue_script( 'itsec-recaptcha-script', plugin_dir_url( __FILE__ ) . 'js/invisible-recaptcha.js', array( 'jquery' ) );

			$recaptcha .= '<div class="g-recaptcha" data-sitekey="' . esc_attr( $this->settings['site_key'] ) . '" data-callback="itsecRecaptchaCallback" data-size="invisible"></div>';
		} else {
			$theme = $this->settings['theme'] ? 'dark' : 'light';
			$style_value = sprintf( 'margin:%dpx %dpx %dpx %dpx', $margin_top, $margin_right, $margin_bottom, $margin_left );

			$recaptcha .= '<div class="g-recaptcha" data-sitekey="' . esc_attr( $this->settings['site_key'] ) . '" data-theme="' . esc_attr( $theme ) . '" style="' . esc_attr( $style_value ) . '"></div>';
		}


		$recaptcha .= '<noscript>
			<div>
				<div style="width: 302px; height: 422px; position: relative;">
					<div style="width: 302px; height: 422px; position: absolute;">
						<iframe src="https://www.google.com/recaptcha/api/fallback?k=' . esc_attr( $this->settings['site_key'] ) . '" frameborder="0" scrolling="no" style="width: 302px; height:422px; border-style: none;"></iframe>
					</div>
				</div>
				<div style="width: 300px; height: 60px; border-style: none; bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px; background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">
					<textarea id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response" style="width: 250px; height: 40px; border: 1px solid #c1c1c1; margin: 10px 25px; padding: 0px; resize: none;"></textarea>
				</div>
			</div>
		</noscript>';


		return $recaptcha;

	}

	/**
	 * Validates the captcha code
	 *
	 * This function is used both internally in iThemes Security and externally in other projects, such as iThemes
	 * Exchange.
	 *
	 * @since 1.13
	 *
	 * @return bool|WP_Error Returns true or a WP_Error object on error.
	 */
	public function validate_captcha() {
		if ( isset( $GLOBALS['__itsec_recaptcha_cached_result'] ) ) {
			return $GLOBALS['__itsec_recaptcha_cached_result'];
		}

		if ( empty( $_POST['g-recaptcha-response'] ) ) {
			if ( ! $this->settings['validated'] ) {
				ITSEC_Modules::set_setting( 'recaptcha', 'last_error', esc_html__( 'The Site Key may be invalid or unrecognized. Verify that you input the Site Key and Private Key correctly.', 'it-l10n-ithemes-security-pro' ) );

				$GLOBALS['__itsec_recaptcha_cached_result'] = true;
				return $GLOBALS['__itsec_recaptcha_cached_result'];
			}

			$this->log_failed_validation();

			$GLOBALS['__itsec_recaptcha_cached_result'] = new WP_Error( 'itsec-recaptcha-form-not-submitted', esc_html__( 'You must verify you are a human.', 'it-l10n-ithemes-security-pro' ) );
			return $GLOBALS['__itsec_recaptcha_cached_result'];
		}


		$url = add_query_arg(
			array(
				'secret'   => $this->settings['secret_key'],
				'response' => esc_attr( $_POST['g-recaptcha-response'] ),
				'remoteip' => ITSEC_Lib::get_ip(),
			),
			'https://www.google.com/recaptcha/api/siteverify'
		);

		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			// Don't lock people out when reCAPTCHA servers cannot be contacted.
			$GLOBALS['__itsec_recaptcha_cached_result'] = true;
			return $GLOBALS['__itsec_recaptcha_cached_result'];
		}


		$status = json_decode( $response['body'], true );

		if ( ! isset( $status['success'] ) ) {
			// Unrecognized response. Do not prevent access.
			$GLOBALS['__itsec_recaptcha_cached_result'] = true;
			return $GLOBALS['__itsec_recaptcha_cached_result'];
		}

		if ( $status['success'] ) {
			if ( ! $this->settings['validated'] ) {
				ITSEC_Modules::set_setting( 'recaptcha', 'validated', true );
			}

			if ( ! empty( $this->settings['last_error'] ) ) {
				ITSEC_Modules::set_setting( 'recaptcha', 'last_error', '' );
			}

			$GLOBALS['__itsec_recaptcha_cached_result'] = true;
			return $GLOBALS['__itsec_recaptcha_cached_result'];
		}

		if ( ! $this->settings['validated'] ) {
			if ( ! empty( $status['error-codes'] ) ) {
				if ( array( 'invalid-input-secret' ) === $status['error-codes'] ) {
					ITSEC_Modules::set_setting( 'recaptcha', 'last_error', esc_html__( 'The Secret Key is invalid or unrecognized.', 'it-l10n-ithemes-security-pro' ) );
				} else if ( 1 === count( $status['error-codes'] ) ) {
					$code = current( $status['error-codes'] );

					ITSEC_Modules::set_setting( 'recaptcha', 'last_error', sprintf( esc_html__( 'The reCAPTCHA server reported the following error: <code>%1$s</code>.', 'it-l10n-ithemes-security-pro' ), $code ) );
				} else {
					ITSEC_Modules::set_setting( 'recaptcha', 'last_error', sprintf( esc_html__( 'The reCAPTCHA server reported the following errors: <code>%1$s</code>.', 'it-l10n-ithemes-security-pro' ), implode( ', ', $status['error-codes'] ) ) );
				}
			}

			$GLOBALS['__itsec_recaptcha_cached_result'] = true;
			return $GLOBALS['__itsec_recaptcha_cached_result'];
		}

		$this->log_failed_validation();

		$GLOBALS['__itsec_recaptcha_cached_result'] = new WP_Error( 'itsec-recaptcha-incorrect', esc_html__( 'The captcha response you submitted does not appear to be valid. Please try again.', 'it-l10n-ithemes-security-pro' ) );
		return $GLOBALS['__itsec_recaptcha_cached_result'];
	}

	private function log_failed_validation() {
		/** @var ITSEC_Lockout $itsec_lockout */
		global $itsec_lockout, $itsec_logger;

		$itsec_logger->log_event(
			'recaptcha',
			5,
			array(),
			ITSEC_Lib::get_ip(),
			'',
			'',
			esc_sql( $_SERVER['REQUEST_URI'] ),
			isset( $_SERVER['HTTP_REFERER'] ) ? esc_sql( $_SERVER['HTTP_REFERER'] ) : ''
		);

		$itsec_lockout->do_lockout( 'recaptcha' );
	}

	/**
	 * Set the login error if captcha wasn't validated
	 *
	 * @since 1.13
	 *
	 * @param WP_User|WP_Error $user     WP_User or WP_Error object if a previous
	 *                                   callback failed authentication.
	 *
	 * @return WP_User|WP_Error     WP_User or WP_Error object if a previous
	 *                                   callback failed authentication.
	 */
	public function filter_wp_authenticate_user( $user ) {

		if ( is_wp_error( $user ) || ITSEC_Core::is_api_request() ) {
			return $user;
		}

		$result = $this->validate_captcha();

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $user;

	}

	/**
	 * Register recaptcha for lockout
	 *
	 * @since 1.13
	 *
	 * @param  array $lockout_modules array of lockout modules
	 *
	 * @return array                   array of lockout modules
	 */
	public function register_lockout_module( $lockout_modules ) {

		$lockout_modules['recaptcha'] = array(
			'type'   => 'recaptcha',
			'reason' => __( 'too many failed captcha submissions.', 'it-l10n-ithemes-security-pro' ),
			'host'   => isset( $this->settings['error_threshold'] ) ? absint( $this->settings['error_threshold'] ) : 7,
			'period' => isset( $this->settings['check_period'] ) ? absint( $this->settings['check_period'] ) : 5,
		);

		return $lockout_modules;

	}

	/**
	 * Register recaptcha detection for logger
	 *
	 * @since 1.13
	 *
	 * @param  array $logger_modules array of logger modules
	 *
	 * @return array                   array of logger modules
	 */
	public function register_logger_module( $logger_modules ) {

		$logger_modules['recaptcha'] = array(
			'type'     => 'recaptcha',
			'function' => __( 'Failed Recaptcha submission', 'it-l10n-ithemes-security-pro' ),
		);

		return $logger_modules;

	}
}
