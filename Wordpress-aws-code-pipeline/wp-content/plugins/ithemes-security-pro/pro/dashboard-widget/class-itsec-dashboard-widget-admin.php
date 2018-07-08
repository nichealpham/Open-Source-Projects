<?php

class ITSEC_Dashboard_Widget_Admin {

	function run() {

		add_action( 'admin_init', array( $this, 'admin_init' ) );

	}

	/**
	 * Execute all hooks on admin init
	 *
	 * All hooks on admin init to make certain user has the correct permissions
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function admin_init() {

		if ( ITSEC_Core::current_user_can_manage() ) {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) ); //enqueue scripts for admin page
			add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup' ) );
			add_action( 'wp_ajax_itsec_release_dashboard_lockout', array( $this, 'itsec_release_dashboard_lockout' ) );
			add_action( 'wp_ajax_itsec_dashboard_summary_postbox_toggle', array( $this, 'itsec_dashboard_summary_postbox_toggle' ) );
			add_action( 'wp_ajax_itsec_dashboard_file_check', array( $this, 'run_file_check' ) );

		}
	}

	/**
	 * Add malware scheduling admin Javascript
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		if ( isset( get_current_screen()->id ) && ( strpos( get_current_screen()->id, 'dashboard' ) !== false ) ) {

			$module_path = ITSEC_Lib::get_module_path( __FILE__ );

			wp_enqueue_script( 'itsec_dashboard_widget_js', $module_path . 'js/admin-dashboard-widget.js', array( 'jquery' ), ITSEC_Core::get_plugin_build() );

			wp_register_style( 'itsec_dashboard_widget_css', $module_path . 'css/admin-dashboard-widget.css', array(), ITSEC_Core::get_plugin_build() ); //add multi-select css
			wp_enqueue_style( 'itsec_dashboard_widget_css' );

			wp_localize_script( 'itsec_dashboard_widget_js', 'itsec_dashboard_widget_js', array(
				'host'          => '<p>' . __( 'Currently no hosts are locked out of this website.', 'it-l10n-ithemes-security-pro' ) . '</p>',
				'user'          => '<p>' . __( 'Currently no users are locked out of this website.', 'it-l10n-ithemes-security-pro' ) . '</p>',
				'scanning'      => __( 'Scanning files...', 'it-l10n-ithemes-security-pro' ),
				'scan_nonce'    => wp_create_nonce( 'itsec_dashboard_scan_files' ),
				'postbox_nonce' => wp_create_nonce( 'itsec_dashboard_summary_postbox_toggle' ),
			) );

		}

	}

	/**
	 * Echo dashboard widget content
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function dashboard_widget_content() {
		global $itsec_lockout;

		$white_class = '';

		if ( function_exists( 'wp_get_current_user' ) ) {

			$current_user = wp_get_current_user();

			$meta = get_user_meta( $current_user->ID, 'itsec_dashboard_widget_status', true );

			if ( is_array( $meta ) ) {

				if ( isset( $meta['itsec_lockout_summary_postbox'] ) && $meta['itsec_lockout_summary_postbox'] == 'close' ) {
					$white_class = ' closed';
				}
			}

		}

		//Access Logs
		echo '<div class="itsec_links widget-section clear">';
		echo '<ul>';
		echo '<li><a href="' . esc_url( ITSEC_Core::get_settings_page_url() ) . '">' . __( '> Plugin Settings', 'it-l10n-ithemes-security-pro' ) . '</a></li>';
		echo '<li><a href="' . esc_url( ITSEC_Core::get_logs_page_url() ) . '">' . __( '> View Security Logs', 'it-l10n-ithemes-security-pro' ) . '</a></li>';
		echo '</ul>';
		echo '</div>';

		//Whitelist
		echo '<div class="itsec_summary_widget widget-section clear postbox' . $white_class . '" id="itsec_lockout_summary_postbox">';

		$lockouts = $itsec_lockout->get_lockouts( 'all' );
		$current  = sizeof( $itsec_lockout->get_lockouts( 'host', true ) ) + sizeof( $itsec_lockout->get_lockouts( 'user', true ) );

		$users = get_users( array( 'fields' => 'ID', 'number' => 1000 ) );

		if ( count( $users ) < 1000 ) {
			$itsec_two_factor = class_exists( 'ITSEC_Two_Factor' )? ITSEC_Two_Factor::get_instance() : false;
			$users_without_2fa = $users_with_weak_password = 0;

			foreach ( $users as $user_id ) {
				// If 2fa isn't available, or if this user isn't using it
				if ( false === $itsec_two_factor || ! $itsec_two_factor->is_user_using_two_factor( $user_id ) ) {
					$users_without_2fa++;
				}

				// If we've already stored the security of this user's password, don't continue
				$password_strength = get_user_meta( $user_id, 'itsec-password-strength', true );

				// If the password strength isn't >= 3
				if ( false !== $password_strength && '' !== $password_strength && $password_strength < 3 ) {
					$users_with_weak_password++;
				}

			}
		}

		echo '<div class="handlediv" title="Click to toggle"><br /></div>';
		echo '<h4 class="dashicons-before dashicons-shield-alt">' . __( 'Security Summary', 'it-l10n-ithemes-security-pro' ) . '</h4>';
		echo '<div class="inside">';
		echo '<div class="summary-item">';
		echo '<h5>' . __( 'Times protected from attack.', 'it-l10n-ithemes-security-pro' ) . '</h5>';
		echo '<span class="summary-total">' . sizeof( $lockouts ) . '</span>';
		echo '</div>';
		echo '<div class="summary-item">';
		echo '<h5>' . __( 'Current Number of lockouts.', 'it-l10n-ithemes-security-pro' ) . '</h5>';
		echo '<span class="summary-total" id="current-itsec-lockout-summary-total">' . $current . '</span>';
		echo '</div>';

		if ( isset( $users_without_2fa ) ) {
			echo '<div class="summary-item">';
			echo '<h5>' . __( 'Users without Two-Factor Authentication', 'it-l10n-ithemes-security-pro' ) . '</h5>';
			echo '<span class="summary-total">' . absint( $users_without_2fa ) . '</span>';
			echo '</div>';
		}

		if ( isset( $users_with_weak_password ) ) {
			echo '<div class="summary-item">';
			echo '<h5>' . __( 'Users without strong password', 'it-l10n-ithemes-security-pro' ) . '</h5>';
			echo '<span class="summary-total">' . absint( $users_with_weak_password ) . '</span>';
			echo '</div>';
		}

		echo '<a href="' . esc_url( admin_url( 'admin.php?page=itsec&module=user-security-check' ) ) . '" class="button-secondary itsec-widget-user-security-check">User Security Check</a>';
		echo '</div>';
		echo '</div>';

		//Run file-change Scan

		if ( ITSEC_Files::can_write_to_files() ) {

			echo '<div class="itsec_file-change_widget widget-section ">';
			$this->file_scan();
			echo '</div>';

		}

		//Show lockouts table
		echo '<div class="itsec_lockouts_widget widget-section clear">';
		$this->lockout_metabox();
		echo '</div>';

	}

	/**
	 * Show file scan button
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	private function file_scan() {
		if ( ITSEC_Modules::is_active( 'file-change' ) ) {
			$file_settings = ITSEC_Modules::get_settings( 'file-change' );

			echo '<p><input type="button" id="itsec_dashboard_one_time_file_check" class="button-primary" value="' . ( isset( $file_settings['split'] ) && $file_settings['split'] === true ? __( 'Scan Next File Chunk', 'it-l10n-ithemes-security-pro' ) : __( 'Scan Files Now', 'it-l10n-ithemes-security-pro' ) ) . '" /></p>';
			echo '<div id="itsec_dashboard_one_time_file_check_results"></div>';
		}
	}

	public function run_file_check() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'itsec_dashboard_scan_files' ) ) {
			die ( __( 'Security error', 'it-l10n-ithemes-security-pro' ) );
		}

		ITSEC_Modules::load_module_file( 'scanner.php', 'file-change' );

		$result = ITSEC_File_Change_Scanner::run_scan( false );

		if ( true === $result ) {
			$type = 'error';
			$message = sprintf( __( 'Changes were found. View details on the <a href="%1$s">logs page</a>.', 'it-l10n-ithemes-security-pro' ), ITSEC_Core::get_logs_page_url( 'file_change' ) );
		} else if ( false === $result ) {
			$type = 'updated fade';
			$message = __( 'No changes were found.', 'it-l10n-ithemes-security-pro' );
		} else if ( -1 === $result ) {
			$type = 'error';
			$message = __( 'A file scan is currently in progress. Please wait a few minutes before attempting another file scan.', 'it-l10n-ithemes-security-pro' );
		} else {
			$type = 'error';
			$message = sprintf( __( 'The file scanner returned an unknown response of type <code>%1$s</code>.', 'it-l10n-ithemes-security-pro' ), gettype( $result ) );
		}

		$message = "<div class='$type inline'><p><strong>$message</strong></p></div>";

		wp_send_json( $message );
	}

	/**
	 * Active lockouts table and form for dashboard.
	 *
	 * @Since 1.9
	 *
	 * @return void
	 */
	private function lockout_metabox() {

		global $itsec_lockout;

		$host_class = '';
		$user_class = '';

		if ( function_exists( 'wp_get_current_user' ) ) {

			$current_user = wp_get_current_user();

			$meta = get_user_meta( $current_user->ID, 'itsec_dashboard_widget_status', true );

			if ( is_array( $meta ) ) {

				if ( isset( $meta['itsec_lockout_host_postbox'] ) && $meta['itsec_lockout_host_postbox'] == 'close' ) {
					$host_class = ' closed';
				}

				if ( isset( $meta['itsec_lockout_user_postbox'] ) && $meta['itsec_lockout_user_postbox'] == 'close' ) {
					$user_class = ' closed';
				}
			}

		}

		//get locked out hosts and users from database
		$host_locks = $itsec_lockout->get_lockouts( 'host', true, 100 );
		$user_locks = $itsec_lockout->get_lockouts( 'user', true, 100 );
		?>
		<div class="postbox<?php echo $host_class; ?>" id="itsec_lockout_host_postbox">
			<div class="handlediv" title="Click to toggle"><br/></div>
			<h4 class="dashicons-before dashicons-lock"><?php _e( 'Locked out hosts', 'it-l10n-ithemes-security-pro' ); ?></h4>

			<div class="inside">
				<?php if ( sizeof( $host_locks ) > 0 ) { ?>

					<ul>
						<?php foreach ( $host_locks as $host ) { ?>

							<li>
								<label for="lo_<?php echo $host['lockout_id']; ?>">
									<a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( ITSEC_Lib::get_trace_ip_link( $host['lockout_host'] ) ); ?>"><?php esc_html_e( $host['lockout_host'] ); ?></a>
									<a href="<?php echo wp_create_nonce( 'itsec_reloease_dashboard_lockout' . $host['lockout_id'] ); ?>" id="<?php echo $host['lockout_id']; ?>" class="itsec_release_lockout locked_host">
										<span class="itsec-locked-out-remove">&mdash;</span>
									</a>
								</label>
							</li>

						<?php } ?>
					</ul>

				<?php } else { //no host is locked out ?>

					<p><?php _e( 'Currently no hosts are locked out of this website.', 'it-l10n-ithemes-security-pro' ); ?></p>

				<?php } ?>
			</div>
		</div>
		<div class="postbox<?php echo $user_class; ?>" id="itsec_lockout_user_postbox">
			<div class="handlediv" title="Click to toggle"><br/></div>
			<h4 class="dashicons-before dashicons-admin-users"><?php _e( 'Locked out users', 'it-l10n-ithemes-security-pro' ); ?></h4>

			<div class="inside">
				<?php if ( sizeof( $user_locks ) > 0 ) { ?>
					<ul>
						<?php foreach ( $user_locks as $user ) { ?>

							<?php $userdata = get_userdata( $user['lockout_user'] ); ?>

							<li>
								<label for="lo_<?php echo $user['lockout_id']; ?>">

									<a href="<?php echo wp_create_nonce( 'itsec_reloease_dashboard_lockout' . $user['lockout_id'] ); ?>"
									   id="<?php echo $user['lockout_id']; ?>"
									   class="itsec_release_lockout locked_user"><span
											class="itsec-locked-out-remove">&mdash;</span><?php echo isset( $userdata->user_login ) ? $userdata->user_login : ''; ?>
									</a>
								</label>
							</li>

						<?php } ?>
					</ul>
				<?php } else { //no user is locked out ?>

					<p><?php _e( 'Currently no users are locked out of this website.', 'it-l10n-ithemes-security-pro' ); ?></p>

				<?php } ?>
			</div>
		</div>
	<?php
	}

	/**
	 * Process the ajax call for opening and closing postboxes
	 *
	 * @since 1.9
	 *
	 * @return string json string for success or failure
	 */
	public function itsec_dashboard_summary_postbox_toggle() {

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'itsec_dashboard_summary_postbox_toggle' ) ) {
			die ( __( 'Security error', 'it-l10n-ithemes-security-pro' ) );
		}

		$id        = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : false;
		$direction = isset( $_POST['direction'] ) ? sanitize_text_field( $_POST['direction'] ) : false;

		if ( $id === false || $direction === false || ! function_exists( 'wp_get_current_user' ) || ! function_exists( 'get_user_meta' ) ) {
			die( false );
		}

		$current_user = wp_get_current_user();

		$meta = get_user_meta( $current_user->ID, 'itsec_dashboard_widget_status', true );

		if ( ! is_array( $meta ) ) {

			$meta = array(
				$id => $direction,
			);

		} else {

			$meta[$id] = $direction;

		}

		update_user_meta( $current_user->ID, 'itsec_dashboard_widget_status', $meta );

		die( true );

	}

	/**
	 * Process the ajax call for releasing lockouts from the dashboard
	 *
	 * @since 1.9
	 *
	 * @return string json string for success or failure
	 */
	public function itsec_release_dashboard_lockout() {

		/** @var ITSEC_Lockout $itsec_lockout */
		global $itsec_lockout;

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'itsec_reloease_dashboard_lockout' . sanitize_text_field( $_POST['resource'] ) ) ) {
			die ( __( 'Security error', 'it-l10n-ithemes-security-pro' ) );
		}

		die( $itsec_lockout->release_lockout( absint( $_POST['resource'] ) ) );

	}

	/**
	 * Create dashboard widget
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function wp_dashboard_setup() {

		wp_add_dashboard_widget(
			'itsec-dashboard-widget',
			ITSEC_Core::get_plugin_name(),
			array( $this, 'dashboard_widget_content' )
		);

	}

}
