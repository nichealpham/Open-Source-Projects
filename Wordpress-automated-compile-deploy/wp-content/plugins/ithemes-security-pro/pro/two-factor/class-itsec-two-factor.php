<?php

/**
 * Two-Factor Execution
 *
 * Handles all two-factor execution once the feature has been
 * enabled by the user.
 *
 * @since   1.2.0
 *
 * @package iThemes_Security
 */
class ITSEC_Two_Factor {
	private static $instance = false;

	/**
	 * Helper class
	 *
	 * @access private
	 * @var ITSEC_Two_Factor_Helper
	 */
	private $helper;

	/**
	 * The user meta provider key.
	 *
	 * @access private
	 * @var string
	 */
	private $_provider_user_meta_key = '_two_factor_provider';

	/**
	 * The user meta enabled providers key.
	 *
	 * @access private
	 * @var string
	 */
	private $_enabled_providers_user_meta_key = '_two_factor_enabled_providers';

	/**
	 * The user meta nonce key.
	 *
	 * @var string
	 */
	private $_user_meta_nonce_key = '_two_factor_nonce';

	/**
	 * Used to store the provider label in the event of a failed authentication.
	 *
	 * @var string
	 */
	private $failed_provider_label = '';

	/**
	 * The current session token.
	 *
	 * @var string
	 */
	public $token;

	private function __construct() {
		add_action( 'set_logged_in_cookie',     array( $this, 'set_logged_in_cookie' ) );
		add_action( 'wp_login',                 array( $this, 'handle_authenticated_login' ), 10, 2 );
		add_action( 'login_form_validate_2fa',  array( $this, 'login_form_validate_2fa' ) );
		add_action( 'login_form_backup_2fa',    array( $this, 'backup_2fa' ) );
		add_action( 'show_user_profile',        array( $this, 'user_two_factor_options' ) );
		add_action( 'edit_user_profile',        array( $this, 'user_two_factor_options' ) );
		add_action( 'personal_options_update',  array( $this, 'user_two_factor_options_update' ) );
		add_action( 'edit_user_profile_update', array( $this, 'user_two_factor_options_update' ) );

		add_filter( 'itsec_logger_modules', array( $this, 'itsec_logger_modules' ) );
		add_action( 'ithemes_sync_register_verbs', array( $this, 'register_sync_verbs' ) );
		add_filter( 'itsec-filter-itsec-get-everything-verbs', array( $this, 'register_sync_get_everything_verbs' ) );

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'wp_ajax_itsec-dismiss-notice-2fa-recommended-remind-again', array( $this, 'dismiss_recommended_notice_remind_again' ) );
		add_action( 'wp_ajax_itsec-dismiss-notice-2fa-recommended', array( $this, 'dismiss_recommended_notice' ) );

		add_action( 'load-profile.php', array( $this, 'add_profile_page_styling' ) );
		add_action( 'load-user-edit.php', array( $this, 'add_profile_page_styling' ) );

		$this->load_helper();
	}

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function load_helper() {
		if ( ! isset( $this->helper ) ) {
			require_once( dirname( __FILE__ ) . '/class-itsec-two-factor-helper.php' );
			$this->helper  = ITSEC_Two_Factor_Helper::get_instance();
		}
	}

	/**
	 * On every admin page, determine if the user needs to be reminded about setting up Two Factor for their account.
	 */
	public function admin_init() {
		global $pagenow;

		if ( isset( $_GET['itsec-action'] ) && 'configure-two-factor' === $_GET['itsec-action'] && 'profile.php' !== $pagenow ) {
			wp_safe_redirect( admin_url( 'profile.php#two-factor-user-options' ) );
		}


		if ( defined( 'ITSEC_DISABLE_TWO_FACTOR' ) && ITSEC_DISABLE_TWO_FACTOR ) {
			if ( is_multisite() ) {
				add_action( 'network_admin_notices', array( $this, 'show_two_factor_disabled_warning' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'show_two_factor_disabled_warning' ) );
			}
		}

		$user_id = get_current_user_id();

		if ( 0 === $user_id ) {
			return;
		}

		// Permanently hidden?
		$hidden = get_user_meta( $user_id, 'itsec-two-factor-hide-recommended-notice', true );
		// Hidden only until next login?
		$hidden_for_session = get_user_meta( $user_id, 'itsec-two-factor-hide-recommended-notice-this-session', true );
		// Hidden on this page
		$hidden_for_page = ( 'profile.php' === $pagenow ) ? true : false;

		if ( ! $hidden && ! $hidden_for_session && ! $hidden_for_page && ! $this->is_user_using_two_factor() ) {
			ITSEC_Core::add_notice( array( $this, 'recommend_2fa_dashboard_notice' ), true );
		}
	}

	public function show_two_factor_disabled_warning() {
		if ( ! current_user_can( ITSEC_Core::get_required_cap() ) ) {
			return;
		}

		echo '<div class="error"><p><strong>';
		echo wp_kses( __( 'The <code>ITSEC_DISABLE_TWO_FACTOR</code> define is present. As long as the define is present, two-factor authentication is disabled for all users which makes your site more vulnerable. Please make any necessary changes and remove the define as soon as possible.', 'it-l10n-ithemes-security-pro' ), array( 'code' => array() ) );
		echo '</strong></p></div>';
	}

	/**
	 * Dismisses the recommended nag for this login.
	 */
	public function dismiss_recommended_notice_remind_again() {
		if ( update_user_meta( get_current_user_id(), 'itsec-two-factor-hide-recommended-notice-this-session', true ) ) {
			wp_send_json_success();
		}
		wp_send_json_error();
	}

	/**
	 * Dismisses the recommended nag permanently.
	 */
	public function dismiss_recommended_notice() {
		if ( update_user_meta( get_current_user_id(), 'itsec-two-factor-hide-recommended-notice', true ) ) {
			wp_send_json_success();
		}
		wp_send_json_error();
	}

	/**
	 * Register two-factor for logger.
	 *
	 * Registers the two-factor module with the core logger functionality.
	 *
	 * @since 1.2.0
	 *
	 * @param  array $logger_modules array of logger modules
	 *
	 * @return array array of logger modules
	 */
	public function itsec_logger_modules( $logger_modules ) {

		$logger_modules['two_factor'] = array(
			'type'     => 'two_factor',
			'function' => __( 'Two-Factor Login Failure', 'it-l10n-ithemes-security-pro' ),
		);

		return $logger_modules;

	}

	/**
	 * Register verbs for Sync.
	 *
	 * @since 3.6.0
	 *
	 * @param Ithemes_Sync_API Sync API object.
	 */
	public function register_sync_verbs( $api ) {
		$api->register( 'itsec-get-two-factor-users', 'Ithemes_Sync_Verb_ITSEC_Get_Two_Factor_Users', dirname( __FILE__ ) . '/sync-verbs/itsec-get-two-factor-users.php' );
		$api->register( 'itsec-override-two-factor-user', 'Ithemes_Sync_Verb_ITSEC_Override_Two_Factor_User', dirname( __FILE__ ) . '/sync-verbs/itsec-override-two-factor-user.php' );
	}

	/**
	 * Filter to add verbs to the response for the itsec-get-everything verb.
	 *
	 * @since 3.6.0
	 *
	 * @param  array Array of verbs.
	 *
	 * @return array Array of verbs.
	 */
	public function register_sync_get_everything_verbs( $verbs ) {
		$verbs['two_factor'][] = 'itsec-get-two-factor-users';

		return $verbs;
	}

	/**
	 * Add user profile fields.
	 *
	 * This executes during the `show_user_profile` & `edit_user_profile` actions.
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 */
	public function user_two_factor_options( $user ) {
		$this->load_helper();

		$enabled_providers = get_user_meta( $user->ID, $this->_enabled_providers_user_meta_key, true );
		if ( empty( $enabled_providers ) ) {
			// Because get_user_meta() has no way of providing a default value.
			$enabled_providers = array();
		}
		$primary_provider = get_user_meta( $user->ID, $this->_provider_user_meta_key, true );
		wp_nonce_field( 'user_two_factor_options', '_nonce_user_two_factor_options', false );
		?>
		<h3 id="two-factor-user-options"><?php esc_html_e( 'Two-Factor Authentication Options', 'it-l10n-ithemes-security-pro' ); ?></h3>
		<p><?php esc_html_e( 'Enabling two-factor authentication greatly increases the security of your user account on this site. With two-factor authentication enabled, after you login with your username and password, you will be asked for an authentication code before you can successfully log in.');?><strong> <?php esc_html_e('Two-factor authentication codes can come from an app that runs on your mobile device, an email that is sent to you after you login with your username and password, or from a pre-generated list of codes.');?></strong> <?php esc_html_e('The settings below allow you to configure which of these authentication code providers are enabled for your user.', 'it-l10n-ithemes-security-pro' ); ?></p>

		<table class="two-factor-methods-table widefat wp-list-table striped">
			<thead>
				<tr>
					<th scope="col" class="manage-column column-primary column-method"><?php esc_html_e( 'Provider', 'it-l10n-ithemes-security-pro' ); ?></th>
					<th scope="col" class="manage-column column-enable"><?php esc_html_e( 'Enabled', 'it-l10n-ithemes-security-pro' ); ?></th>
					<th scope="col" class="manage-column column-make-primary"><?php esc_html_e( 'Primary', 'it-l10n-ithemes-security-pro' ); ?></th>
				</tr>
			</thead>
			<tbody id="the-list">
			<?php foreach ( $this->helper->get_enabled_provider_instances() as $class => $object ) : ?>
				<tr>
					<td class="column-method column-primary" style="width:60%;vertical-align:top;">
						<strong><?php $object->print_label(); ?></strong>
						<?php do_action( 'two-factor-user-options-' . $class, $user ); ?>
						<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
					</td>
					<td class="column-enable" style="width:20%;vertical-align:top;">
						<input type="checkbox" name="<?php echo esc_attr( $this->_enabled_providers_user_meta_key ); ?>[]" id="<?php echo esc_attr( $this->_enabled_providers_user_meta_key . '-' . $class ); ?>" value="<?php echo esc_attr( $class ); ?>" <?php checked( in_array( $class, $enabled_providers ) ); ?> />
						<label for="<?php echo esc_attr( $this->_enabled_providers_user_meta_key . '-' . $class ); ?>">
							<?php esc_html_e( 'Enable', 'it-l10n-ithemes-security-pro' )  ?>
							<?php
							if ( $object->recommended ) {
								echo ' <strong>' . __( '(recommended)', 'it-l10n-ithemes-security-pro' ) . '</strong>';
							}
							?>
						</label>
					</td>
					<td class="column-make-primary" style="width:20%;vertical-align:top;">
						<input type="radio" name="<?php echo esc_attr( $this->_provider_user_meta_key ); ?>" value="<?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->_provider_user_meta_key . '-' . $class ); ?>" <?php checked( $class, $primary_provider ); ?> />
						<label for="<?php echo esc_attr( $this->_provider_user_meta_key . '-' . $class ); ?>">
							<?php esc_html_e( 'Make Primary', 'it-l10n-ithemes-security-pro' )  ?>
							<?php
							if ( $object->recommended ) {
								echo ' <strong>' . __( '(recommended)', 'it-l10n-ithemes-security-pro' ) . '</strong>';
							}
							?>
						</label>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<th scope="col" class="manage-column column-primary column-method"><?php esc_html_e( 'Method', 'it-l10n-ithemes-security-pro' ); ?></th>
					<th scope="col" class="manage-column column-enable"><?php esc_html_e( 'Enabled', 'it-l10n-ithemes-security-pro' ); ?></th>
					<th scope="col" class="manage-column column-make-primary"><?php esc_html_e( 'Primary', 'it-l10n-ithemes-security-pro' ); ?></th>
				</tr>
			</tfoot>
		</table>
		<?php
		/**
		 * Fires after the Two Factor methods table.
		 *
		 * To be used by Two Factor methods to add settings UI.
		 */
		do_action( 'show_user_security_settings', $user );
	}

	/**
	 * Update the user meta value.
	 *
	 * This executes during the `personal_options_update` & `edit_user_profile_update` actions.
	 *
	 * @param int $user_id User ID.
	 */
	public function user_two_factor_options_update( $user_id ) {
		$this->load_helper();

		if ( isset( $_POST['_nonce_user_two_factor_options'] ) ) {
			check_admin_referer( 'user_two_factor_options', '_nonce_user_two_factor_options' );
			$providers         = $this->helper->get_enabled_provider_instances();
			// If there are no providers enabled for the site, then let's not worry about this.
			if ( empty( $providers ) ) {
				return;
			}

			$enabled_providers = isset( $_POST[ $this->_enabled_providers_user_meta_key ] )? $_POST[$this->_enabled_providers_user_meta_key] : array();
			$this->set_enabled_providers_for_user( $enabled_providers, $user_id );

			// Whitelist the new values to only the available classes and empty.
			$primary_provider = isset( $_POST[ $this->_provider_user_meta_key ] )? $_POST[ $this->_provider_user_meta_key ]:'';
			$this->set_primary_provider_for_user( $primary_provider, $user_id );
		}
	}

	/**
	 * Update the list of enabled Two Factor providers for a user.
	 *
	 * @param array    $enabled_providers
	 * @param int|null $user_id
	 */
	public function set_enabled_providers_for_user( $enabled_providers, $user_id = null ) {
		$this->load_helper();

		$providers = $this->helper->get_enabled_providers();
		// If there are no providers enabled for the site, then let's not worry about this.
		if ( empty( $providers ) ) {
			return;
		}
		if ( ! isset( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		if ( ! is_array( $enabled_providers ) ) {
			// Make sure enabled providers is an array
			$enabled_providers = array();
		} else {
			// Only site-enabled providers can be enabled for a user
			$enabled_providers = array_intersect( $enabled_providers, array_keys( $providers ) );
		}
		update_user_meta( $user_id, $this->_enabled_providers_user_meta_key, $enabled_providers );
	}

	/**
	 * Set the primary provider for a user.
	 *
	 * @param string   $primary_provider
	 * @param int|null $user_id
	 */
	public function set_primary_provider_for_user( $primary_provider, $user_id = null ) {
		$this->load_helper();

		$providers = $this->helper->get_enabled_providers();
		// If there are no providers enabled for the site, then let's not worry about this.
		if ( empty( $providers ) ) {
			return;
		}
		if ( ! isset( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		if ( empty( $primary_provider ) || array_key_exists( $primary_provider, $providers ) ) {
			update_user_meta( $user_id, $this->_provider_user_meta_key, $primary_provider );
		}
	}

	/**
	 * Get all Two-Factor Auth providers that are enabled for the specified|current user.
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 * @return array
	 */
	public function get_enabled_providers_for_user( $user = null ) {
		$this->load_helper();

		if ( empty( $user ) || ! is_a( $user, 'WP_User' ) ) {
			$user = wp_get_current_user();
		}

		$providers         = $this->helper->get_enabled_provider_instances();
		$enabled_providers = get_user_meta( $user->ID, $this->_enabled_providers_user_meta_key, true );
		if ( empty( $enabled_providers ) ) {
			$enabled_providers = array();
		}
		$enabled_providers = array_intersect( $enabled_providers, array_keys( $providers ) );

		return $enabled_providers;
	}

	/**
	 * Get all Two-Factor Auth providers that are both enabled and configured for the specified|current user.
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 * @return array
	 */
	public function get_available_providers_for_user( $user = null ) {
		$this->load_helper();

		if ( empty( $user ) || ! is_a( $user, 'WP_User' ) ) {
			$user = wp_get_current_user();
		}

		if ( ! ( $user instanceof WP_User ) ) {
			return array();
		}

		$providers            = $this->helper->get_enabled_provider_instances();
		$enabled_providers    = $this->get_enabled_providers_for_user( $user );
		$configured_providers = array();

		foreach ( $providers as $classname => $provider ) {
			if ( in_array( $classname, $enabled_providers ) && $provider->is_available_for_user( $user ) ) {
				$configured_providers[ $classname ] = $provider;
			}
		}

		if ( ! isset( $configured_providers['Two_Factor_Email'] ) && isset( $providers['Two_Factor_Email'] ) && $this->user_requires_two_factor( $user->ID ) ) {
			$configured_providers['Two_Factor_Email'] = $providers['Two_Factor_Email'];
		}

		return $configured_providers;
	}

	/**
	 * Get the reason that two factor is required for a given user.
	 *
	 * 'user_type' - Required because all users are required, their role requires it, or they are a privileged user.
	 * 'vulnerable_users' - Requried because they have a weak password.
	 * 'vulnerable_site' - Required because the site is running outdated versions of plugins.
	 *
	 * @param int|null $user_id
	 *
	 * @return string|false
	 */
	public function get_two_factor_requirement_reason( $user_id = null ) {
		$this->load_helper();

		if ( empty( $user_id ) || ! is_numeric( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$providers = $this->helper->get_enabled_provider_instances();

		if ( ! isset( $providers['Two_Factor_Email'] ) ) {
			// Two-factor can't be a requirement if the Email method is not available.
			return false;
		}

		$user = get_userdata( $user_id );

		if ( ! ( $user instanceof WP_User ) ) {
			return false;
		}

		$settings = ITSEC_Modules::get_settings( 'two-factor' );

		if ( 'all_users' === $settings['protect_user_type'] ) {
			return 'user_type';
		} else if ( 'privileged_users' === $settings['protect_user_type'] ) {
			require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-canonical-roles.php' );

			if ( ITSEC_Lib_Canonical_Roles::is_user_at_least( 'contributor', $user ) ) {
				return 'user_type';
			}
		} else if ( 'custom' === $settings['protect_user_type'] ) {
			if ( is_object( $user ) && isset( $user->roles ) && is_array( $user->roles ) ) {
				$shared_roles = array_intersect( $settings['protect_user_type_roles'], $user->roles );
			}

			if ( ! empty( $shared_roles ) ) {
				return 'user_type';
			}
		}

		if ( $settings['protect_vulnerable_users'] ) {
			$password_strength = get_user_meta( $user_id, 'itsec-password-strength', true );

			if ( ( is_string( $password_strength ) || is_int( $password_strength ) ) && $password_strength >= 0 && $password_strength <= 2 ) {
				return 'vulnerable_users';
			}
		}

		if ( $settings['protect_vulnerable_site'] && ITSEC_Modules::is_active( 'version-management' ) ) {
			$version_management_settings = ITSEC_Modules::get_settings( 'version-management' );

			if ( $version_management_settings['is_software_outdated'] ) {
				return 'vulnerable_site';
			}
		}
	}

	/**
	 * Get a description for the reason Two Factor is required.
	 *
	 * @param string $reason
	 *
	 * @return string
	 */
	public function get_reason_description( $reason ) {
		if ( 'user_type' === $reason ) {
			return esc_html__( 'Your user requires two-factor in order to log in.', 'it-l10n-ithemes-security-pro' );
		} else if ( 'vulnerable_users' === $reason ) {
			return esc_html__( 'The site requires any user with a weak password to use two-factor in order to log in.', 'it-l10n-ithemes-security-pro' );
		} else if ( 'vulnerable_site' === $reason ) {
			return esc_html__( 'This site requires two-factor in order to log in.', 'it-l10n-ithemes-security-pro' );
		} else {
			return '';
		}
	}

	/**
	 * Does the given user require Two Factor to be enabled.
	 *
	 * @param int|null $user_id
	 *
	 * @return bool
	 */
	public function user_requires_two_factor( $user_id = null ) {
		$reason = $this->get_two_factor_requirement_reason( $user_id );

		if ( empty( $reason ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Gets the Two-Factor Auth provider for the specified|current user.
	 *
	 * @param int $user_id Optional. User ID. Default is 'null'.
	 *
	 * @return Two_Factor_Provider|null
	 */
	public function get_primary_provider_for_user( $user_id = null ) {
		$this->load_helper();

		if ( empty( $user_id ) || ! is_numeric( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$providers      = $this->helper->get_enabled_provider_instances();
		$user_providers = $this->get_available_providers_for_user( get_userdata( $user_id ) );

		if ( empty( $user_providers ) ) {
			return null;
		} else if ( 1 === count( $user_providers ) ) {
			$provider = key( $user_providers );
		} else {
			$provider = get_user_meta( $user_id, $this->_provider_user_meta_key, true );

			// If the provider specified isn't enabled, just grab the first one that is.
			if ( ! isset( $user_providers[ $provider ] ) ) {
				$provider = key( $user_providers );
			}
		}

		/**
		 * Filter the two-factor authentication provider used for this user.
		 *
		 * @param string $provider The provider currently being used.
		 * @param int    $user_id  The user ID.
		 */
		$provider = apply_filters( 'two_factor_primary_provider_for_user', $provider, $user_id );

		if ( isset( $providers[ $provider ] ) ) {
			return $providers[ $provider ];
		}

		return null;
	}

	/**
	 * Quick boolean check for whether a given user is using two-step.
	 *
	 * @param int $user_id Optional. User ID. Default is 'null'.
	 *
	 * @return bool|null True if they are using it. False if not using it. Null if disabled site-wide.
	 */
	public function is_user_using_two_factor( $user_id = null ) {
		if ( defined( 'ITSEC_DISABLE_TWO_FACTOR' ) && ITSEC_DISABLE_TWO_FACTOR ) {
			return;
		}

		$provider = $this->get_primary_provider_for_user( $user_id );
		return ! empty( $provider );
	}

	/**
	 * Handle the browser-based login.
	 *
	 * The wp_login action that this is connected to fires after a user is successfully logged in. We use it to provide
	 * the two-factor prompt for users that require two-factor to successfully log in.
	 *
	 * @param string  $user_login Username.
	 * @param WP_User $user WP_User object of the logged-in user.
	 */
	public function handle_authenticated_login( $user_login, $user ) {
		if ( ! $this->is_user_using_two_factor( $user->ID ) ) {
			// The user is logged in and not using two factor, remove user meta to show recommended notice again.
			delete_user_meta( $user->ID, 'itsec-two-factor-hide-recommended-notice-this-session' );
			return;
		}

		if ( $this->token ) {
			// Destroy the session token so that the authentication cookie is no longer valid. This prevents
			// side-stepping the two-factor requirement.

			// Based on wp_destroy_current_session() but uses $user->ID instead of get_current_user_id()
			$manager = WP_Session_Tokens::get_instance( $user->ID );
			$manager->destroy( $this->token );
		}
		wp_clear_auth_cookie();

		$this->show_two_factor_login( $user );
		exit;
	}

	/**
	 * Store the current session token in $this->token to use it to wipe out the session if a 2fa challenge is given
	 *
	 * @param string $cookie Logged in cookie
	 */
	public function set_logged_in_cookie( $cookie ) {
		$cookie = wp_parse_auth_cookie( $cookie, 'logged_in' );
		$this->token = ! empty( $cookie['token'] ) ? $cookie['token'] : '';
	}

	/**
	 * Display the login form.
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 */
	public function show_two_factor_login( $user ) {
		if ( ! $user ) {
			$user = wp_get_current_user();
		}

		$login_nonce = $this->create_login_nonce( $user->ID );
		if ( ! $login_nonce ) {
			wp_die( esc_html__( 'Could not save login nonce.', 'it-l10n-ithemes-security-pro' ) );
		}

		$redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : $_SERVER['REQUEST_URI'];

		$this->login_html( $user, $login_nonce['key'], $redirect_to );
	}

	/**
	 * Display the backup Two Factor form.
	 */
	public function backup_2fa() {
		if ( ! isset( $_GET['wp-auth-id'], $_GET['wp-auth-nonce'], $_GET['provider'] ) ) {
			return;
		}

		$user = get_userdata( $_GET['wp-auth-id'] );
		if ( ! $user ) {
			return;
		}

		$nonce = $_GET['wp-auth-nonce'];
		if ( true !== $this->verify_login_nonce( $user->ID, $nonce ) ) {
			wp_safe_redirect( get_bloginfo( 'url' ) );
			exit;
		}

		$providers = $this->get_available_providers_for_user( $user );
		if ( isset( $providers[ $_GET['provider'] ] ) ) {
			$provider = $providers[ $_GET['provider'] ];
		} else {
			wp_die( esc_html__( 'Cheatin&#8217; uh?', 'it-l10n-ithemes-security-pro' ), 403 );
		}

		$this->login_html( $user, $_GET['wp-auth-nonce'], $_GET['redirect_to'], '', $provider );

		exit;
	}

	public function return_empty_array( $actions ) {
		return array();
	}

	/**
	 * Generates the html form for the second step of the authentication process.
	 *
	 * @param WP_User       $user WP_User object of the logged-in user.
	 * @param string        $login_nonce A string nonce stored in usermeta.
	 * @param string        $redirect_to The URL to which the user would like to be redirected.
	 * @param string        $error_msg Optional. Login error message.
	 * @param string|object $provider An override to the provider.
	 */
	public function login_html( $user, $login_nonce, $redirect_to, $error_msg = '', $provider = null ) {
		add_filter( 'jetpack_sso_allowed_actions', array( $this, 'return_empty_array' ) );

		if ( empty( $provider ) ) {
			$provider = $this->get_primary_provider_for_user( $user->ID );
		} elseif ( is_string( $provider ) && method_exists( $provider, 'get_instance' ) ) {
			$provider = call_user_func( array( $provider, 'get_instance' ) );
		}

		$provider_class = get_class( $provider );

		$available_providers = $this->get_available_providers_for_user( $user );
		$backup_providers = array_diff_key( $available_providers, array( $provider_class => null ) );
		$interim_login = isset($_REQUEST['interim-login']);

		$wp_login_url = set_url_scheme( wp_login_url(), 'login_post' );
		$wp_login_url = add_query_arg( 'action', 'validate_2fa', $wp_login_url );

		if ( isset( $_GET['wpe-login'] ) && ! preg_match( '/[&?]wpe-login=/', $wp_login_url ) ) {
			$wp_login_url = add_query_arg( 'wpe-login', $_GET['wpe-login'], $wp_login_url );
		}

		$rememberme = 0;
		if ( isset( $_REQUEST['rememberme'] ) && $_REQUEST['rememberme'] ) {
			$rememberme = 1;
		}

		if ( ! function_exists( 'login_header' ) ) {
			// login_header() should be migrated out of `wp-login.php` so it can be called from an includes file.
			require_once( ITSEC_Core::get_core_dir() . '/lib/includes/function.login-header.php' );
		}

		login_header();

		if ( ! empty( $error_msg ) ) {
			echo '<div id="login_error"><strong>' . esc_html( $error_msg ) . '</strong><br /></div>';
		}

?>
				<form name="validate_2fa_form" id="loginform" action="<?php echo esc_url( $wp_login_url ); ?>" method="post" autocomplete="off">
					<input type="hidden" name="provider" id="provider" value="<?php echo esc_attr( $provider_class ); ?>" />
					<input type="hidden" name="wp-auth-id" id="wp-auth-id" value="<?php echo esc_attr( $user->ID ); ?>" />
					<input type="hidden" name="wp-auth-nonce" id="wp-auth-nonce" value="<?php echo esc_attr( $login_nonce ); ?>" />
					<?php if ( $interim_login ) : ?>
						<input type="hidden" name="interim-login" value="1" />
					<?php else : ?>
						<input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
					<?php endif; ?>
					<input type="hidden" name="rememberme" id="rememberme" value="<?php echo esc_attr( $rememberme ); ?>" />

					<?php $provider->authentication_page( $user ); ?>

					<?php if ( $backup_providers ) : ?>
						<div class="itsec-backup-methods" style="clear:both;margin-top:4em;padding-top:2em;border-top:1px solid #ddd;">
							<p><?php esc_html_e( 'Or, use a backup method:', 'it-l10n-ithemes-security-pro' ); ?></p>
							<ul style="margin-left:1em;">
								<?php foreach ( $backup_providers as $backup_classname => $backup_provider ) : ?>
									<li><a href="<?php echo esc_url( add_query_arg( urlencode_deep( array(
											'action'        => 'backup_2fa',
											'provider'      => $backup_classname,
											'wp-auth-id'    => $user->ID,
											'wp-auth-nonce' => $login_nonce,
											'redirect_to'   => $redirect_to,
											'rememberme'    => $rememberme,
										) ), $wp_login_url ) ); ?>"><?php $backup_provider->print_label(); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</form>

				<p id="backtoblog">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( 'Are you lost?', 'it-l10n-ithemes-security-pro' ); ?>"><?php echo esc_html( sprintf( __( '&larr; Back to %s', 'it-l10n-ithemes-security-pro' ), get_bloginfo( 'title', 'display' ) ) ); ?></a>
				</p>

			</div>

			<div class="clear"></div>
		</body>
	</html>
<?php

	}

	/**
	 * Create the login nonce.
	 *
	 * This is an actual number used once, not a WordPress nonce.
	 *
	 * @param int $user_id User ID.
	 *
	 * @return array|false
	 */
	public function create_login_nonce( $user_id ) {
		$login_nonce               = array();
		$login_nonce['key']        = wp_hash( $user_id . mt_rand() . microtime(), 'nonce' );
		$login_nonce['expiration'] = time() + HOUR_IN_SECONDS;

		if ( ! update_user_meta( $user_id, $this->_user_meta_nonce_key, $login_nonce ) ) {
			return false;
		}

		return $login_nonce;
	}

	/**
	 * Delete the login nonce.
	 *
	 * @param int $user_id User ID.
	 *
	 * @return bool
	 */
	public function delete_login_nonce( $user_id ) {
		return delete_user_meta( $user_id, $this->_user_meta_nonce_key );
	}

	/**
	 * Verify the login nonce.
	 *
	 * @param int    $user_id User ID.
	 * @param string $nonce Login nonce.
	 *
	 * @return bool
	 */
	public function verify_login_nonce( $user_id, $nonce ) {
		$login_nonce = get_user_meta( $user_id, $this->_user_meta_nonce_key, true );
		if ( ! $login_nonce ) {
			return false;
		}

		if ( $nonce !== $login_nonce['key'] || time() > $login_nonce['expiration'] ) {
			$this->delete_login_nonce( $user_id );
			return false;
		}

		return true;
	}

	/**
	 * Login form validation.
	 */
	public function login_form_validate_2fa() {
		if ( ! isset( $_POST['wp-auth-id'], $_POST['wp-auth-nonce'] ) ) {
			return;
		}

		$user = get_userdata( $_POST['wp-auth-id'] );
		if ( ! $user ) {
			return;
		}

		$nonce = $_POST['wp-auth-nonce'];
		if ( true !== $this->verify_login_nonce( $user->ID, $nonce ) ) {
			wp_safe_redirect( get_bloginfo( 'url' ) );
			exit;
		}

		global $interim_login;

		$interim_login = isset($_REQUEST['interim-login']);

		/**
		 * iThemes Sync override
		 */
		$sync_override    = intval( get_user_option( 'itsec_two_factor_override', $user->ID ) ) === 1 ? true : false;
		$override_expires = intval( get_user_option( 'itsec_two_factor_override_expires', $user->ID ) );

		if ( ! $sync_override || current_time( 'timestamp' ) > $override_expires ) {
			if ( isset( $_POST['provider'] ) ) {
				$providers = $this->get_available_providers_for_user( $user );
				if ( isset( $providers[ $_POST['provider'] ] ) ) {
					$provider = $providers[ $_POST['provider'] ];
				} else {
					wp_die( esc_html__( 'Cheatin&#8217; uh?', 'it-l10n-ithemes-security-pro' ), 403 );
				}
			} else {
				$provider = $this->get_primary_provider_for_user( $user->ID );
			}

			if ( true !== $provider->validate_authentication( $user ) ) {
				$this->failed_provider_label = $provider->get_label();
				add_filter( 'itsec-filter-failed-login-details', array( $this, 'filter_failed_login_details' ) );

				do_action( 'wp_login_failed', $user->user_login );

				$login_nonce = $this->create_login_nonce( $user->ID );
				if ( ! $login_nonce ) {
					return;
				}

				if ( empty( $_REQUEST['redirect_to'] ) ) {
					$_REQUEST['redirect_to'] = '';
				}
				$this->login_html( $user, $login_nonce['key'], $_REQUEST['redirect_to'], esc_html__( 'ERROR: Invalid verification code.', 'it-l10n-ithemes-security-pro' ) );
				exit;
			}
		}

		$this->delete_login_nonce( $user->ID );

		$rememberme = false;
		if ( isset( $_REQUEST['rememberme'] ) && $_REQUEST['rememberme'] ) {
			$rememberme = true;
		}

		wp_set_auth_cookie( $user->ID, $rememberme );

		if ( $interim_login ) {
			$customize_login = isset( $_REQUEST['customize-login'] );
			if ( $customize_login ) {
				wp_enqueue_script( 'customize-base' );
			}
			$message = '<p class="message">' . __('You have logged in successfully.') . '</p>';
			$interim_login = 'success';
			login_header( '', $message ); ?>
			</div>
			<?php
			/** This action is documented in wp-login.php */
			do_action( 'login_footer' ); ?>
			<?php if ( $customize_login ) : ?>
				<script type="text/javascript">setTimeout( function(){ new wp.customize.Messenger({ url: '<?php echo wp_customize_url(); ?>', channel: 'login' }).send('login') }, 1000 );</script>
			<?php endif; ?>
			</body></html>
<?php		exit;
		}

		$redirect_to = apply_filters( 'login_redirect', $_REQUEST['redirect_to'], $_REQUEST['redirect_to'], $user );
		wp_safe_redirect( $redirect_to );

		exit;
	}

	public function filter_failed_login_details( $details ) {
		if ( empty( $this->failed_provider_label ) ) {
			$details['authentication_types'] = array( __( 'unknown_two_factor_provider', 'it-l10n-ithemes-security-pro' ) );
		} else {
			$details['authentication_types'] = array( $this->failed_provider_label );
		}

		return $details;
	}

	/**
	 * Admin notice telling users that it's recommended to set up two factor
	 */
	public function recommend_2fa_dashboard_notice() {
		$activate_link = apply_filters( 'itsec-two-factor-notice-active-link', get_edit_user_link() . '#two-factor-user-options' );
		echo  '<div class="updated itsec-notice itsec-two-factor-notice"><span class="it-icon-itsec"></span>'
		    . __( 'Two Factor Authentication has been enabled for this site. It is highly recommended that you take advantage of this feature to secure your user login &amp; password.', 'it-l10n-ithemes-security-pro' )
		    . '<p><a class="itsec-notice-button" href="' . esc_url( $activate_link ) . '">' . __( 'Activate Two-Factor Authentication', 'it-l10n-ithemes-security-pro' ) . '</a>'
		    . '<button class="itsec-notice-button itsec-notice-hide" data-nonce="' . wp_create_nonce( 'dismiss-2fa-recommended-dashboard-notice-remind-again' ) . '" data-source="2fa-recommended-remind-again">' . __( 'Remind Me Later', 'it-l10n-ithemes-security-pro' ) . '</button></p>'
		    . '<button class="itsec-notice-hide" data-nonce="' . wp_create_nonce( 'dismiss-2fa-recommended-dashboard-notice' ) . '" data-source="2fa-recommended">&times;</button>'
		    . '</div>';
	}

	/**
	 * Enqueue the css/profile-page.css file.
	 */
	public function add_profile_page_styling() {
		wp_enqueue_style( 'itsec-two-factor-profile-page', plugins_url( 'css/profile-page.css', __FILE__ ), array(), ITSEC_Core::get_plugin_build() );

		$this->load_helper();
		$this->helper->get_enabled_provider_instances();
	}
}
