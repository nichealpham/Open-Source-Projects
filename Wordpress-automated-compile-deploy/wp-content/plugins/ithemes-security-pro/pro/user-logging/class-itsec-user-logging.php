<?php

class ITSEC_User_logging {
	private $role_list;
	private $settings;

	public function run() {
		$this->settings = ITSEC_Modules::get_settings( 'user-logging' );

		//roles and subroles
		$this->role_list = array(
			'subscriber'    => array( 'subscriber', 'author', 'contributor', 'editor', 'administrator' ),
			'contributor'   => array( 'author', 'contributor', 'editor', 'administrator' ),
			'author'        => array( 'contributor', 'editor', 'administrator' ),
			'editor'        => array( 'editor', 'administrator' ),
			'administrator' => array( 'administrator' ),
		);

		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'itsec_logger_modules', array( $this, 'register_logger' ) );
		add_action( 'wp_login', array( $this, 'wp_login' ) );
	}

	/**
	 * Load logging hooks if needed by user level
	 *
	 * @since 4.2
	 *
	 * @return void
	 */
	public function init() {
		$user = wp_get_current_user();

		foreach ( $user->roles as $role ) {
			if ( in_array( $role, $this->role_list[$this->settings['role']] ) ) {
				add_action( 'wp_logout', array( $this, 'wp_logout' ) );
				add_action( 'transition_post_status', array( $this, 'transition_post_status' ), 10, 3 );

				return;
			}
		}
	}

	/**
	 * Register user logging for logger
	 *
	 * @since 4.1
	 *
	 * @param  array $logger_modules array of logger modules
	 *
	 * @return array                   array of logger modules
	 */
	public function register_logger( $logger_modules ) {
		$logger_modules['user_logging'] = array(
			'type'     => 'user_logging',
			'function' => __( 'User Action', 'it-l10n-ithemes-security-pro' ),
		);

		return $logger_modules;
	}

	/**
	 * Log post status transition
	 *
	 * @since 4.2
	 *
	 * @param array  $new_status new post status array
	 * @param string $old_status old post status
	 * @param int    $post       the post id
	 *
	 * @return void
	 */
	public function transition_post_status( $new_status, $old_status, $post ) {
		global $itsec_logger;

		if ( in_array( $new_status, array( 'auto-draft', 'inherit' ) ) ) {
			return;
		} else if ( $old_status === 'auto-draft' && $new_status === 'draft' ) {
			$action = __( 'Content Drafted', 'it-l10n-ithemes-security-pro' );
		} else if ( ( $old_status === 'auto-draft' || $old_status === 'draft' ) && in_array( $new_status, array( 'publish', 'private' ) ) ) {
			$action = __( 'Content Published', 'it-l10n-ithemes-security-pro' );
		} else if ( $old_status === 'publish' && in_array( $new_status, array( 'draft' ) ) ) {
			$action = __( 'Content Unpublished', 'it-l10n-ithemes-security-pro' );
		} else if ( $new_status === 'trash' ) {
			$action = __( 'Content Moved to Trash', 'it-l10n-ithemes-security-pro' );
		} else {
			$action = __( 'Content Updated', 'it-l10n-ithemes-security-pro' );
		}

		$user = wp_get_current_user();

		$itsec_logger->log_event(
			'user_logging',
			1,
			array(
				'action' => $action,
				'post'   => $post->ID,
			),
			ITSEC_Lib::get_ip(),
			$user->user_login,
			$user->ID
		);
	}

	/**
	 * Log successful user login
	 *
	 * @since 4.1
	 *
	 * @return void
	 */
	public function wp_login( $user_login ) {
		global $itsec_logger;

		$user = get_user_by( 'login', $user_login );

		foreach ( $user->roles as $role ) {
			if ( in_array( $role, $this->role_list[$this->settings['role']] ) ) {
				$itsec_logger->log_event(
					'user_logging',
					1,
					array(
						'action' => __( 'User Login', 'it-l10n-ithemes-security-pro' ),
					),
					ITSEC_Lib::get_ip(),
					$user_login,
					'',
					'',
					''
				);

				return;
			}
		}
	}

	/**
	 * Log successful user logout
	 *
	 * @since 4.1
	 *
	 * @return void
	 */
	public function wp_logout() {
		global $itsec_logger;

		$itsec_logger->log_event(
			'user_logging',
			1,
			array(
				'action' => __( 'A User Logged Out', 'it-l10n-ithemes-security-pro' ),
			),
			ITSEC_Lib::get_ip(),
			'',
			'',
			'',
			''
		);
	}
}
