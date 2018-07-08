<?php

class ITSEC_Password_Expiration {

	private $settings;

	function run() {

		$this->settings = ITSEC_Modules::get_settings( 'password-expiration' );

		add_filter( 'itsec_password_change_requirement_description_for_age', array( $this, 'age_reason' ) );
		add_filter( 'itsec_password_change_requirement_description_for_force', array( $this, 'force_reason' ) );
		add_action( 'itsec_validate_password', array( $this, 'validate_password' ), 10, 4 );

		add_action( 'wp_login', array( $this, 'wp_login' ), 11, 2 );

	}

	/**
	 * Get the reason description for why a password change was set to 'age'.
	 *
	 * @return string
	 */
	public function age_reason() {

		$period = isset( $this->settings['expire_max'] ) ? absint( $this->settings['expire_max'] ) : 120;

		return sprintf( esc_html__( 'Your password has expired. You must create a new password every %d days.', 'it-l10n-ithemes-security-pro' ), $period );
	}

	/**
	 * Get the reason description for why a password change was set to 'force'.
	 *
	 * @return string
	 */
	public function force_reason() {
		return esc_html__( 'An admin has required you to reset your password.', 'it-l10n-ithemes-security-pro' );
	}

	/**
	 * Validate a new password according to the configured strength rules.
	 *
	 * @param WP_Error $error
	 * @param WP_User  $user
	 * @param string   $new_password
	 * @param array    $args
	 */
	public function validate_password( $error, $user, $new_password, $args = array() ) {

		if ( ! $user instanceof WP_User ) {
			return;
		}

		$current = get_userdata( $user->ID )->user_pass;

		if ( ! wp_check_password( $new_password, $current, $user->ID ) ) {
			return;
		}

		$message = wp_kses( __( '<strong>ERROR</strong>: The password you have chosen appears to have been used before. You must choose a new password.', 'it-l10n-ithemes-security-pro' ), array( 'strong' => array() ) );
		$error->add( 'pass', $message );
	}

	/**
	 * Whenever a user logs in, check if their password needs to be changed. If so, mark that the user must change
	 * their password.
	 *
	 * @since 1.8
	 *
	 * @param string  $username the username attempted
	 * @param WP_User $user   wp_user the user
	 *
	 * @return void
	 */
	public function wp_login( $username, $user = null ) {

		// Get a valid user or terminate the hook (all we care about is forcing the password change...
		// Let brute force protection handle the rest
		if ( null !== $user ) {
			$current_user = $user;
		} elseif ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
		} else {
			return;
		}

		$this->maybe_flag_user_requires_password_change( $current_user );
	}

	/**
	 * Flag that a user must have their password changes if they haven't changed their password since the last admin
	 * force, or in the past 120 (default) days.
	 *
	 * @param WP_User $user
	 *
	 * @return bool Whether the user required a password change.
	 */
	private function maybe_flag_user_requires_password_change( $user ) {

		if ( ITSEC_Lib_Password_Requirements::password_change_required( $user ) ) {
			return true;
		}

		$oldest_allowed = 0;

		if ( isset( $this->settings['expire_force'] ) && $this->settings['expire_force'] > 0 ) {
			$oldest_allowed = $this->settings['expire_force'];
			$type			= 'force';
		} elseif ( $this->is_user_allowed_to_expire( $user ) ) {

			if ( isset( $this->settings['expire_max'] ) ) {
				$period = absint( $this->settings['expire_max'] ) * DAY_IN_SECONDS;
			} else {
				$period = 120 * DAY_IN_SECONDS;
			}

			$oldest_allowed = ITSEC_Core::get_current_time_gmt() - $period;
			$type			= 'age';
		}

		if ( ! $oldest_allowed ) {
			return false;
		}

		$last_change = ITSEC_Lib_Password_Requirements::password_last_changed( $user );

		if ( $last_change <= $oldest_allowed ) {
			ITSEC_Lib_Password_Requirements::flag_password_change_required( $user, $type );

			return true;
		}

		return false;
	}

	/**
	 * Is the given user allowed to have their password expire because of a maximum password age setting.
	 *
	 * @param WP_User $user
	 *
	 * @return bool
	 */
	private function is_user_allowed_to_expire( $user ) {

		//determine the minimum role for enforcement
		$min_role = isset( $this->settings['expire_role'] ) ? $this->settings['expire_role'] : 'administrator';

		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-canonical-roles.php' );

		return ITSEC_Lib_Canonical_Roles::is_user_at_least( $min_role, $user );
	}
}
