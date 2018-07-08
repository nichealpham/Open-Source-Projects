<?php

final class ITSEC_Strong_Passwords {
	public function __construct() {

		add_filter( 'itsec_password_change_requirement_description_for_strength', array( $this, 'strength_reason' ) );
		add_action( 'user_profile_update_errors', array( $this, 'filter_user_profile_update_errors' ), 0, 3 );
		add_action( 'itsec_validate_password', array( $this, 'validate_password' ), 10, 4 );

		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'add_scripts' ) );
	}

	/**
	 * Enqueue script to add measured password strength to the form submission data.
	 *
	 * @return void
	 */
	public function add_scripts() {
		$module_path = ITSEC_Lib::get_module_path( __FILE__ );

		wp_enqueue_script( 'itsec_strong_passwords', $module_path . 'js/script.js', array( 'jquery' ), ITSEC_Core::get_plugin_build() );
	}

	/**
	 * Get the reason description for why a password change was set to 'strength'.
	 *
	 * @return string
	 */
	public function strength_reason() {

		$message = __( 'Due to site rules, a strong password is required for your account. Please choose a new password that rates as <strong>Strong</strong> on the meter.', 'it-l10n-ithemes-security-pro' );

		return wp_kses( $message, array( 'strong' => '' ) );
	}

	/**
	 * Handle submission of a form to create or edit a user.
	 *
	 * @param WP_Error $errors WP_Error object.
	 * @param bool     $update Whether this is a user update.
	 * @param stdClass $user   User object.
	 *
	 * @return WP_Error
	 */
	public function filter_user_profile_update_errors( $errors, $update, $user ) {

		// An error regarding the password was already found.
		if ( $errors->get_error_data( 'pass' ) ) {
			return $errors;
		}

		if ( isset( $user->user_pass ) || ! $update ) {
			return $errors;
		}

		// The password was not changed, but an update is occurring. Test to see if we need to prompt for a password change.
		// This also handles the case where a user's role is being changed to one that requires strong password enforcement.

		$strength = get_user_meta( $user->ID, 'itsec-password-strength', true );

		if ( ! is_numeric( $strength ) || $strength < 0 || $strength > 4 ) {
			// Not enough data to determine whether a change of password is required.
			return $errors;
		}

		if ( isset( $user->role ) ) {
			$role = $this->get_canonical_role_from_role_and_user( $user->role, $user );
		} else {
			$role = ITSEC_Lib_Canonical_Roles::get_user_role( $user );
		}

		if ( ! $this->role_requires_strong_password( $role ) ) {
			return $errors;
		}

		if ( $strength === 4 ) {
			return $errors;
		}

		if ( ! $update ) {
			$context = 'admin-user-create';
		} elseif ( $user->ID === get_current_user_id() ) {
			$context = 'profile-update';
		} else {
			$context = 'admin-profile-update';
		}

		$errors->add( 'pass', $this->make_error_message( $context ) );

		return $errors;
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

		if ( isset( $args['strength'] ) ) {
			$reported_strength = $args['strength'];
		} else {
			$reported_strength = false;
		}

		if ( isset( $args['role'] ) ) {
			$role = $this->get_canonical_role_from_role_and_user( $args['role'], $user );
		} else {
			require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-canonical-roles.php' );
			$role = ITSEC_Lib_Canonical_Roles::get_user_role( $user->ID );
		}

		if ( ! $this->role_requires_strong_password( $role ) ) {
			return;
		}

		if ( ! $this->fails_enforcement( $user, $new_password, $reported_strength ) ) {
			return;
		}

		$message = $this->make_error_message( $args['context'] );

		$error->add( 'pass', $message );
	}

	/**
	 * Retrieve a canonical role for a user and a role.
	 *
	 * @param string $role
	 * @param WP_User $user
	 *
	 * @return string
	 */
	private function get_canonical_role_from_role_and_user( $role, $user ) {
		$role_caps = array_keys( array_filter( wp_roles()->get_role( $role )->capabilities ) );
		$user_caps = array();

		if ( isset( $user->caps ) ) {
			$wp_roles = wp_roles();

			foreach ( $user->caps as $cap => $has ) {
				if ( $has && ! $wp_roles->is_role( $cap ) ) {
					$user_caps[] = $has;
				}
			}
		}

		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-canonical-roles.php' );

		return ITSEC_Lib_Canonical_Roles::get_role_from_caps( array_merge( $role_caps, $user_caps ) );
	}

	/**
	 * Get the strong password error message according to the given context.
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	private function make_error_message( $context ) {
		$message = __( '<strong>Error</strong>: Due to site rules, a strong password is required. Please choose a new password that rates as <strong>Strong</strong> on the meter.', 'it-l10n-ithemes-security-pro' );

		if ( 'admin-user-create' === $context ) {
			$message .= ' ' . __( 'The user has not been created.', 'it-l10n-ithemes-security-pro' );
		} elseif ( 'admin-profile-update' === $context ) {
			$message .= ' ' . __( 'The user changes have not been saved.', 'it-l10n-ithemes-security-pro' );
		} elseif ( 'profile-update' === $context ) {
			$message .= ' ' . __( 'Your profile has not been updated.', 'it-l10n-ithemes-security-pro' );
		} elseif ( 'reset-password' === $context ) {
			$message .= ' ' . __( 'The password has not been updated.', 'it-l10n-ithemes-security-pro' );
		}

		return wp_kses( $message, array( 'strong' => array() ) );
	}

	/**
	 * Does the given role require a strong password.
	 *
	 * @param string $role The user's canonical role.
	 *
	 * @return bool
	 */
	private function role_requires_strong_password( $role ) {
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-canonical-roles.php' );

		$min_role = ITSEC_Modules::get_setting( 'strong-passwords', 'role' );

		return ITSEC_Lib_Canonical_Roles::is_canonical_role_at_least( $min_role, $role );
	}

	/**
	 * Determine if the user requires enforcement and if it fails that enforcement.
	 *
	 * @param WP_User|stdClass $user            Requires either a valid WP_User object or an object that has the following members:
	 *                                          user_login, first_name, last_name, nickname, display_name, user_email, user_url, and
	 *                                          description. A member of user_pass is required if $password_strength is false.
	 * @param string         $new_password      The user's new password.
	 * @param int|boolean    $password_strength [optional] An integer value representing the password strength, if known, or false.
	 *                                          Defaults to false.
	 *
	 * @return boolean True if the user requires enforcement and has a password weaker than strong. False otherwise.
	 */
	private function fails_enforcement( $user, $new_password, $password_strength = false ) {

		if ( false !== $password_strength ) {
			return $password_strength < 4;
		}

		if ( ! empty( $_POST['password_strength'] ) && 'strong' !== $_POST['password_strength'] ) {
			// We want to validate the password strength if the form data says that the password is strong since we want
			// to protect against spoofing. If the form data says that the password isn't strong, believe it.

			$password_strength = 1;
		} else {
			// The form data does not indicate a password strength or the data claimed that the password is strong,
			// which is a claim that must be validated. Use the zxcvbn library to find the password strength score.

			$penalty_strings = array(
				get_site_option( 'admin_email' )
			);
			$user_properties = array( 'user_login', 'first_name', 'last_name', 'nickname', 'display_name', 'user_email', 'user_url', 'description' );

			foreach ( $user_properties as $user_property ) {
				if ( isset( $user->$user_property ) ) {
					$penalty_strings[] = $user->$user_property;
				}
			}

			$results = ITSEC_Lib::get_password_strength_results( $new_password, $penalty_strings );
			$password_strength = $results->score;
		}

		return $password_strength < 4;
	}
}

new ITSEC_Strong_Passwords();
