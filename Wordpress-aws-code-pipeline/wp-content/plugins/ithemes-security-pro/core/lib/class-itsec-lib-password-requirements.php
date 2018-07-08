<?php
/**
 * Tool to manage password requirements across modules.
 *
 * @since   3.9.0
 * @license GPLv2+
 */

/**
 * Class ITSEC_Lib_Password_Requirements
 */
class ITSEC_Lib_Password_Requirements {

	const LOGIN_ACTION = 'itsec_update_password';
	const META_KEY = '_itsec_update_password_key';

	/** @var string */
	private $error_message = '';

	public function run() {

		add_action( 'user_profile_update_errors', array( $this, 'forward_profile_pass_update' ), 0, 3 );
		add_action( 'validate_password_reset', array( $this, 'forward_reset_pass' ), 10, 2 );
		add_action( 'profile_update', array( $this, 'set_password_last_updated' ), 10, 2 );

		add_action( 'wp_login', array( $this, 'wp_login' ), 12, 2 );
		add_filter( 'wp_login_errors', array( $this, 'token_expired_message' ) );
		add_action( 'login_form_' . self::LOGIN_ACTION, array( $this, 'handle_update_password_form' ), 9 );
		add_action( 'login_form_' . self::LOGIN_ACTION, array( $this, 'display_update_password_form' ) );
	}

	/**
	 * When a user's password is updated, or a new user created, verify that the new password is valid.
	 *
	 * @param WP_Error         $errors
	 * @param bool             $update
	 * @param WP_User|stdClass $user
	 */
	public function forward_profile_pass_update( $errors, $update, $user ) {

		if ( ! isset( $user->user_pass ) ) {
			return;
		}

		if ( ! $update ) {
			$context = 'admin-user-create';
		} elseif ( isset( $user->ID ) && $user->ID === get_current_user_id() ) {
			$context = 'profile-update';
		} else {
			$context = 'admin-profile-update';
		}

		$args = array(
			'error'   => $errors,
			'context' => $context
		);

		if ( isset( $user->role ) ) {
			$args['role'] = $user->role;
		}

		self::validate_password( $user, $user->user_pass, $args );
	}

	/**
	 * When a user attempts to reset their password, verify that the new password is valid.
	 *
	 * @param WP_Error $errors
	 * @param WP_User  $user
	 */
	public function forward_reset_pass( $errors, $user ) {

		if ( ! isset( $_POST['pass1'] ) ) {
			// The validate_password_reset action fires when first rendering the reset page and when handling the form
			// submissions. Since the pass1 data is missing, this must be the initial page render. So, we don't need to
			// do anything yet.
			return;
		}

		self::validate_password( $user, $user->user_pass, array(
			'error'   => $errors,
			'context' => 'reset-password',
		) );
	}

	/**
	 * Whenever a user object is updated, set when their password was last updated.
	 *
	 * @param int    $user_id
	 * @param object $old_user_data
	 */
	public function set_password_last_updated( $user_id, $old_user_data ) {

		$user = get_userdata( $user_id );

		if ( $user->user_pass === $old_user_data->user_pass ) {
			return;
		}

		delete_user_meta( $user_id, 'itsec_password_change_required' );
		update_user_meta( $user_id, 'itsec_last_password_change', ITSEC_Core::get_current_time_gmt() );
	}

	/**
	 * Whenever a user logs in, check if their password needs to be changed. If so, mark that the user must change
	 * their password.
	 *
	 * @since 1.8
	 *
	 * @param string  $username the username attempted
	 * @param WP_User $user     wp_user the user
	 *
	 * @return void
	 */
	public function wp_login( $username, $user = null ) {

		//Get a valid user or terminate the hook (all we care about is forcing the password change... Let brute force protection handle the rest
		if ( null !== $user ) {
			$current_user = $user;
		} elseif ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
		} else {
			return;
		}

		if ( ! self::password_change_required( $current_user ) ) {
			return;
		}

		$token = $this->set_update_password_key( $current_user );
		$this->destroy_session( $current_user );

		$this->login_html( $current_user, $token );
		exit;
	}

	/**
	 * Add a message that the update password token has expired and they must login again.
	 *
	 * @param WP_Error $errors
	 *
	 * @return WP_Error
	 */
	public function token_expired_message( $errors ) {

		if ( ! empty( $_GET['itsec_update_pass_expired'] ) ) {
			$errors->add(
				'itsec_update_pass_expired',
				esc_html__( 'Sorry, the update password request has expired. Please log in again.', 'it-l10n-ithemes-security-pro' )
			);
		}

		return $errors;
	}

	/**
	 * Handle the request to update the user's password.
	 */
	public function handle_update_password_form() {

		if ( empty( $_POST['itsec_update_password'] ) || empty( $_POST['itsec_update_password_user'] ) || empty( $_POST['pass1'] ) ) {
			return;
		}

		$user = get_userdata( $_POST['itsec_update_password_user'] );

		if ( ! $user || empty( $_POST['itsec_update_password_token'] ) || ! $this->verify_update_password_key( $user, $_POST['itsec_update_password_token'] ) ) {

			$url = add_query_arg( 'itsec_update_pass_expired', 1, wp_login_url() );
			wp_safe_redirect( set_url_scheme( $url, 'login_post' ) );
			die();
		}

		$error = self::validate_password( $user, $_POST['pass1'] );

		if ( $error->get_error_message() ) {
			$this->error_message = $error->get_error_message();

			return;
		}

		$user->user_pass = $_POST['pass1'];
		$error			 = wp_update_user( $user );

		if ( is_wp_error( $error ) ) {
			$this->error_message = $error->get_error_message();

			return;
		}

		$this->delete_update_password_key( $user );
		wp_set_auth_cookie( $user->ID, ! empty( $_REQUEST['rememberme'] ) );

		if ( ! empty( $_REQUEST['redirect_to'] ) ) {
			$redirect_to = apply_filters( 'login_redirect', $_REQUEST['redirect_to'], $_REQUEST['redirect_to'], $user );
			wp_safe_redirect( $redirect_to );
		} else {
			wp_safe_redirect( admin_url( 'index.php' ) );
		}

		exit;
	}

	/**
	 * When the login page is loaded with the 'itsec_update_password' action, maybe display the update password form,
	 * or redirect to a standard login page.
	 */
	public function display_update_password_form() {

		$user = null;
		$token = '';

		if ( is_user_logged_in() ) {
			$user  = wp_get_current_user();
			$token = $this->set_update_password_key( $user );
			$this->destroy_session( $user );
		} elseif ( ! empty( $_POST['itsec_update_password_user'] ) ) {
			$user  = get_userdata( $_POST['itsec_update_password_user'] );
			$token = empty( $_POST['itsec_update_password_token'] ) ? '' : $_POST['itsec_update_password_token'];
		}

		if ( ! $user ) {
			wp_safe_redirect( set_url_scheme( wp_login_url(), 'login_post' ) );
			die();
		}

		if ( ! self::password_change_required( $user ) ) {
			wp_safe_redirect( set_url_scheme( wp_login_url(), 'login_post' ) );
			die();
		}

		$this->login_html( $user, $token );
		exit;
	}

	/**
	 * Destroy the session for a user.
	 *
	 * @param WP_User $user
	 */
	private function destroy_session( $user ) {
		WP_Session_Tokens::get_instance( $user->ID )->destroy_all();
		wp_clear_auth_cookie();
	}

	/**
	 * Verify that the update password key is valid.
	 *
	 * @param WP_User $user
	 * @param string  $key
	 *
	 * @return bool
	 */
	private function verify_update_password_key( $user, $key ) {
		$expected = get_user_meta( $user->ID, self::META_KEY, true );

		if ( ! $expected || ! is_array( $expected ) ) {
			return false;
		}

		if ( empty( $expected['expires'] ) || $expected['expires'] < ITSEC_Core::get_current_time_gmt() ) {
			return false;
		}

		return hash_equals( $expected['key'], $key );
	}

	/**
	 * Set the update password key for a user.
	 *
	 * @param WP_User $user
	 *
	 * @return string
	 */
	private function set_update_password_key( $user ) {
		$key = $this->generate_update_password_key();

		update_user_meta( $user->ID, self::META_KEY, array(
			'key'     => $key,
			'expires' => ITSEC_Core::get_current_time_gmt() + HOUR_IN_SECONDS
		) );

		return $key;
	}

	/**
	 * Generate a token to be used to verify intent of updating password.
	 *
	 * We can't use nonces here because the WordPress Session Tokens won't be initialized yet.
	 *
	 * @return string
	 */
	private function generate_update_password_key() {
		return wp_generate_password( 32, true, false );
	}

	/**
	 * Delete the update password key for a user.
	 *
	 * @param WP_User $user
	 */
	private function delete_update_password_key( $user ) {
		delete_user_meta( $user->ID, self::META_KEY );
	}

	/**
	 * Display an interstitial form during the login process to force a user to update their password.
	 *
	 * @param WP_User $user
	 * @param string  $token
	 */
	protected function login_html( $user, $token ) {

		$wp_login_url = set_url_scheme( wp_login_url(), 'login_post' );
		$wp_login_url = add_query_arg( 'action', self::LOGIN_ACTION, $wp_login_url );

		if ( isset( $_GET['wpe-login'] ) && ! preg_match( '/[&?]wpe-login=/', $wp_login_url ) ) {
			$wp_login_url = add_query_arg( 'wpe-login', $_GET['wpe-login'], $wp_login_url );
		}

		$interim_login = isset( $_REQUEST['interim-login'] );
		$redirect_to   = '';

		$rememberme = ! empty( $_REQUEST['rememberme'] );

		wp_enqueue_script( 'user-profile' );

		if ( ! function_exists( 'login_header' ) ) {
			require_once( dirname( __FILE__ ) . '/includes/function.login-header.php' );
		}

		login_header();

		$type = self::password_change_required( $user );
		// Modules are responsible for providing escaped reason messages
		$reason = $this->get_message_for_password_change_reason( $type );
		?>

		<?php if ( $this->error_message ) : ?>
			<div id="login-error" class="message" style="border-left-color: #dc3232;">
				<?php echo $this->error_message; ?>
			</div>
		<?php else: ?>
			<p class="message"><?php echo $reason; ?></p>
		<?php endif; ?>

		<form name="resetpassform" id="resetpassform" action="<?php echo esc_url( $wp_login_url ); ?>" method="post"
			  autocomplete="off">

			<div class="user-pass1-wrap">
				<p><label for="pass1"><?php _e( 'New Password', 'it-l10n-ithemes-security-pro' ); ?></label></p>
			</div>

			<div class="wp-pwd">
				<span class="password-input-wrapper">
					<input type="password" data-reveal="1"
						   data-pw="<?php echo esc_attr( wp_generate_password( 16 ) ); ?>" name="pass1" id="pass1"
						   class="input" size="20" value="" autocomplete="off" aria-describedby="pass-strength-result"/>
				</span>
				<div id="pass-strength-result" class="hide-if-no-js" aria-live="polite"><?php _e( 'Strength indicator', 'it-l10n-ithemes-security-pro' ); ?></div>
			</div>

			<p class="user-pass2-wrap">
				<label for="pass2"><?php _e( 'Confirm new password' ) ?></label><br/>
				<input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off"/>
			</p>

			<p class="description indicator-hint"><?php echo wp_get_password_hint(); ?></p>
			<br class="clear"/>

			<p class="submit">
				<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large"
					   value="<?php esc_attr_e( 'Update Password', 'it-l10n-ithemes-security-pro' ); ?>"/>
			</p>

			<?php if ( $interim_login ) : ?>
				<input type="hidden" name="interim-login" value="1"/>
			<?php else : ?>
				<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>"/>
			<?php endif; ?>

			<input type="hidden" name="rememberme" id="rememberme" value="<?php echo esc_attr( $rememberme ); ?>"/>
			<input type="hidden" name="itsec_update_password" value="1">
			<input type="hidden" name="itsec_update_password_token" value="<?php echo esc_attr( $token ); ?>">
			<input type="hidden" name="itsec_update_password_user" value="<?php echo esc_attr( $user->ID ); ?>">
		</form>

		<p id="backtoblog">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( 'Are you lost?', 'it-l10n-ithemes-security-pro' ); ?>">
				<?php echo esc_html( sprintf( __( '&larr; Back to %s', 'it-l10n-ithemes-security-pro' ), get_bloginfo( 'title', 'display' ) ) ); ?>
			</a>
		</p>

		</div>
		<?php do_action( 'login_footer' ); ?>
		<div class="clear"></div>
		</body>
		</html>
		<?php
	}

	/**
	 * Get a message indicating to the user why a password change is required.
	 *
	 * @param string $reason
	 *
	 * @return string
	 */
	protected function get_message_for_password_change_reason( $reason ) {

		/**
		 * Retrieve a human readable description as to why a password change has been required for the current user.
		 *
		 * Modules MUST HTML escape their reason strings before returning them with this filter.
		 *
		 * @param string $message
		 */
		$message = apply_filters( "itsec_password_change_requirement_description_for_{$reason}", '' );

		if ( $message ) {
			return $message;
		}

		return esc_html__( 'A password change is required for your account.', 'it-l10n-ithemes-security-pro' );
	}

	/**
	 * Validate a user's password.
	 *
	 * @param WP_User|stdClass|int $user
	 * @param string               $new_password
	 * @param array                $args
	 *
	 * @return WP_Error Error object with new errors.
	 */
	public static function validate_password( $user, $new_password, $args = array() ) {

		$args = wp_parse_args( $args, array(
			'error'   => new WP_Error(),
			'context' => ''
		) );

		$error = isset( $args['error'] ) ? $args['error'] : new WP_Error();

		$user = $user instanceof stdClass ? $user : ITSEC_Lib::get_user( $user );

		if ( ! $user ) {
			$error->add( 'invalid_user', esc_html__( 'Invalid User', 'it-l10n-ithemes-security-pro' ) );

			return $error;
		}

		/**
		 * Fires when modules should validate a password according to their rules.
		 *
		 * @since 3.9.0
		 *
		 * @param \WP_Error         $error
		 * @param \WP_User|stdClass $user
		 * @param string            $new_password
		 * @param array             $args
		 */
		do_action( 'itsec_validate_password', $error, $user, $new_password, $args );

		return $error;
	}

	/**
	 * Flag that a password change is required for a user.
	 *
	 * @param WP_User|int $user
	 * @param string      $reason
	 */
	public static function flag_password_change_required( $user, $reason ) {
		$user = ITSEC_Lib::get_user( $user );

		if ( $user ) {
			update_user_meta( $user->ID, 'itsec_password_change_required', $reason );
		}
	}

	/**
	 * Check if a password change is required for the given user.
	 *
	 * @param WP_User|int $user
	 *
	 * @return string|false Either the reason code a change is required, or false.
	 */
	public static function password_change_required( $user ) {
		$user = ITSEC_Lib::get_user( $user );

		if ( ! $user ) {
			return false;
		}

		$reason = get_user_meta( $user->ID, 'itsec_password_change_required', true );

		if ( ! $reason ) {
			return false;
		}

		return $reason;
	}

	/**
	 * Get the GMT time the user's password has last been changed.
	 *
	 * @param WP_User|int $user
	 *
	 * @return int
	 */
	public static function password_last_changed( $user ) {

		$user = ITSEC_Lib::get_user( $user );

		if ( ! $user ) {
			return 0;
		}

		$changed    = (int) get_user_meta( $user->ID, 'itsec_last_password_change', true );
		$deprecated = (int) get_user_meta( $user->ID, 'itsec-password-updated', true );

		if ( $deprecated > $changed ) {
			return $deprecated;
		}

		return $changed;
	}
}