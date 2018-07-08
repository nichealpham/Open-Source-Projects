<?php


final class ITSEC_Logs_Page {
	private $version = 1.5;

	private $self_url = '';
	private $modules = array();
	private $widgets = array();
	private $translations = array();
	private $logger_modules = array();
	private $logger_displays = array();


	public function __construct() {
		add_action( 'itsec-logs-page-register-widget', array( $this, 'register_widget' ) );

		add_action( 'itsec-page-show', array( $this, 'handle_page_load' ) );
		add_action( 'itsec-page-ajax', array( $this, 'handle_ajax_request' ) );
		add_action( 'admin_print_scripts', array( $this, 'add_scripts' ) );
		add_action( 'admin_print_styles', array( $this, 'add_styles' ) );

		$this->set_translation_strings();


		$this->logger_modules = apply_filters( 'itsec_logger_modules', array() );
		$this->logger_displays = apply_filters( 'itsec_logger_displays', array() );


		require( dirname( __FILE__ ) . '/module-settings.php' );
		require( dirname( __FILE__ ) . '/sidebar-widget.php' );

		require_once( ITSEC_Core::get_core_dir() . '/lib/form.php' );


		do_action( 'itsec-logs-page-init' );
		do_action( 'itsec-logs-page-register-widgets' );


		if ( ! empty( $_POST ) && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			$this->handle_post();
		}
	}

	public function add_scripts() {
		foreach ( $this->modules as $id => $module ) {
			$module->enqueue_scripts_and_styles();
		}

		foreach ( $this->widgets as $id => $widget ) {
			$widget->enqueue_scripts_and_styles();
		}

		$vars = array(
			'ajax_action'   => 'itsec_settings_page',
			'ajax_nonce'    => wp_create_nonce( 'itsec-settings-nonce' ),
			'logs_page_url' => ITSEC_Core::get_logs_page_url(),
			'translations'  => $this->translations,
		);

		wp_enqueue_script( 'itsec-settings-page-script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery-ui-dialog' ), $this->version, true );
		wp_localize_script( 'itsec-settings-page-script', 'itsec_page', $vars );
	}

	public function add_styles() {
		wp_enqueue_style( 'itsec-settings-page-style', plugins_url( 'css/style.css', __FILE__ ), array(), $this->version );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
	}

	private function set_translation_strings() {
		$this->translations = array(
			'successful_save'   => __( 'Settings saved successfully for %1$s.', 'it-l10n-ithemes-security-pro' ),

			'ajax_invalid'      => new WP_Error( 'itsec-settings-page-invalid-ajax-response', __( 'An "invalid format" error prevented the request from completing as expected. The format of data returned could not be recognized. This could be due to a plugin/theme conflict or a server configuration issue.', 'it-l10n-ithemes-security-pro' ) ),

			'ajax_forbidden'    => new WP_Error( 'itsec-settings-page-forbidden-ajax-response: %1$s "%2$s"',  __( 'A "request forbidden" error prevented the request from completing as expected. The server returned a 403 status code, indicating that the server configuration is prohibiting this request. This could be due to a plugin/theme conflict or a server configuration issue. Please try refreshing the page and trying again. If the request continues to fail, you may have to alter plugin settings or server configuration that could account for this AJAX request being blocked.', 'it-l10n-ithemes-security-pro' ) ),

			'ajax_not_found'    => new WP_Error( 'itsec-settings-page-not-found-ajax-response: %1$s "%2$s"', __( 'A "not found" error prevented the request from completing as expected. The server returned a 404 status code, indicating that the server was unable to find the requested admin-ajax.php file. This could be due to a plugin/theme conflict, a server configuration issue, or an incomplete WordPress installation. Please try refreshing the page and trying again. If the request continues to fail, you may have to alter plugin settings, alter server configurations, or reinstall WordPress.', 'it-l10n-ithemes-security-pro' ) ),

			'ajax_server_error' => new WP_Error( 'itsec-settings-page-server-error-ajax-response: %1$s "%2$s"', __( 'A "internal server" error prevented the request from completing as expected. The server returned a 500 status code, indicating that the server was unable to complete the request due to a fatal PHP error or a server problem. This could be due to a plugin/theme conflict, a server configuration issue, a temporary hosting issue, or invalid custom PHP modifications. Please check your server\'s error logs for details about the source of the error and contact your hosting company for assistance if required.', 'it-l10n-ithemes-security-pro' ) ),

			'ajax_unknown'      => new WP_Error( 'itsec-settings-page-ajax-error-unknown: %1$s "%2$s"', __( 'An unknown error prevented the request from completing as expected. This could be due to a plugin/theme conflict or a server configuration issue.', 'it-l10n-ithemes-security-pro' ) ),

			'ajax_timeout'      => new WP_Error( 'itsec-settings-page-ajax-error-timeout: %1$s "%2$s"', __( 'A timeout error prevented the request from completing as expected. The site took too long to respond. This could be due to a plugin/theme conflict or a server configuration issue.', 'it-l10n-ithemes-security-pro' ) ),

			'ajax_parsererror'  => new WP_Error( 'itsec-settings-page-ajax-error-parsererror: %1$s "%2$s"', __( 'A parser error prevented the request from completing as expected. The site sent a response that jQuery could not process. This could be due to a plugin/theme conflict or a server configuration issue.', 'it-l10n-ithemes-security-pro' ) ),
		);

		foreach ( $this->translations as $key => $message ) {
			if ( is_wp_error( $message ) ) {
				$messages = ITSEC_Response::get_error_strings( $message );
				$this->translations[$key] = $messages[0];
			}
		}
	}

	public function register_widget( $widget ) {
		if ( ! is_object( $widget ) || ! is_a( $widget, 'ITSEC_Settings_Page_Sidebar_Widget' ) ) {
			trigger_error( 'An invalid widget was registered.', E_USER_ERROR );
			return;
		}

		if ( isset( $this->modules[$widget->id] ) ) {
			trigger_error( "A widget with the id of {$widget->id} is registered. Widget id's must be unique from any other module or widget." );
			return;
		}

		if ( isset( $this->widgets[$widget->id] ) ) {
			trigger_error( "A widget with the id of {$widget->id} is already registered. Widget id's must be unique from any other module or widget." );
			return;
		}


		$this->widgets[$widget->id] = $widget;
	}

	private function handle_post() {
		if ( ! empty( $_POST['itsec_clear_logs'] ) && 'clear_logs' === $_POST['itsec_clear_logs'] ) {
			if ( ! wp_verify_nonce( $_POST['wp_nonce'], 'itsec_clear_logs' ) ) {
				die( __( 'Security error!', 'it-l10n-ithemes-security-pro' ) );
			}

			global $itsec_logger;

			$itsec_logger->purge_logs( true );
		} else {
			$post_data = ITSEC_Form::get_post_data();
			$saved = true;
			$js_function_calls = array();

			if ( ! empty( $_POST['widget-id'] ) ) {
				$id = $_POST['widget-id'];

				if ( isset( $post_data[$id] ) && isset( $this->widgets[$id] ) ) {
					$widget = $this->widgets[$id];

					$widget->handle_form_post( $post_data[$id] );
				}
			} else {
				if ( ! empty( $_POST['module'] ) ) {
					if ( isset( $this->modules[$_POST['module']] ) ) {
						$modules = array( $_POST['module'] => $this->modules[$_POST['module']] );
					} else {
						ITSEC_Response::add_error( new WP_Error( 'itsec-settings-save-unrecognized-module', sprintf( __( 'The supplied module (%s) is not recognized. The module settings could not be saved.', 'it-l10n-ithemes-security-pro' ), $_POST['module'] ) ) );
						$modules = array();
					}
				} else {
					$modules = $this->modules;
				}

				foreach ( $modules as $id => $module ) {
					if ( isset( $post_data[$id] ) ) {
						$results = $module->handle_form_post( $post_data[$id] );
					}
				}

				if ( ITSEC_Response::is_success() ) {
					if ( ITSEC_Response::get_show_default_success_message() ) {
						ITSEC_Response::add_message( __( 'The settings saved successfully.', 'it-l10n-ithemes-security-pro' ) );
					}
				} else {
					if ( ITSEC_Response::get_show_default_error_message() ) {
						$error_count = ITSEC_Response::get_error_count();

						if ( $error_count > 0 ) {
							ITSEC_Response::add_error( new WP_Error( 'itsec-settings-data-not-saved', _n( 'The settings could not be saved. Please correct the error above and try again.', 'The settings could not be saved. Please correct the errors above and try again.', $error_count, 'it-l10n-ithemes-security-pro' ) ) );
						} else {
							ITSEC_Response::add_error( new WP_Error( 'itsec-settings-data-not-saved-missing-error', __( 'The settings could not be saved. Due to an unknown error. Please try refreshing the page and trying again.', 'it-l10n-ithemes-security-pro' ) ) );
						}
					}
				}
			}


			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				return;
			}

			ITSEC_Response::maybe_regenerate_wp_config();
			ITSEC_Response::maybe_regenerate_server_config();
			ITSEC_Response::maybe_do_force_logout();
			ITSEC_Response::maybe_do_redirect();
		}
	}

	public function handle_page_load( $self_url ) {
		$this->self_url = $self_url;

		$this->show_settings_page();
	}

	private function get_widget_settings( $id, $form = false, $echo = false ) {
		if ( ! isset( $this->widgets[$id] ) ) {
			$error = new WP_Error( 'itsec-settings-page-get-widget-settings-invalid-id', sprintf( __( 'The requested widget (%s) does not exist. Logs for it cannot be rendered.', 'it-l10n-ithemes-security-pro' ), $id ) );

			if ( $echo ) {
				ITSEC_Lib::show_error_message( $error );
			} else {
				return $error;
			}
		}

		if ( false === $form ) {
			$form = new ITSEC_Form();
		}

		$widget = $this->widgets[$id];

		$form->add_input_group( $id );
		$form->set_defaults( $widget->get_defaults() );

		if ( ! $echo ) {
			ob_start();
		}

		$widget->render( $form );

		$form->remove_all_input_groups();

		if ( ! $echo ) {
			return ob_get_clean();
		}
	}

	private function show_settings_page() {
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-wp-list-table.php' );


		if ( isset( $_GET['filter'] ) ) {
			$filter = $_GET['filter'];
		} else {
			$filter = 'all';
		}


		$form = new ITSEC_Form();


		$filters = array(
			'all' => __( 'All Log Data', 'it-l10n-ithemes-security-pro' ),
		);

		foreach ( $this->logger_displays as $log_provider ) {
			$filters[$log_provider['module']] = $log_provider['title'];
		}


		$form->set_option( 'filter', $filter );

?>
	<div class="wrap">
		<h1>
			<?php _e( 'iThemes Security', 'it-l10n-ithemes-security-pro' ); ?>
			<a href="<?php echo esc_url( ITSEC_Core::get_settings_page_url() ); ?>" class="page-title-action"><?php _e( 'Manage Settings', 'it-l10n-ithemes-security-pro' ); ?></a>
			<a href="<?php echo esc_url( apply_filters( 'itsec_support_url', 'https://wordpress.org/support/plugin/better-wp-security' ) ); ?>" class="page-title-action"><?php _e( 'Support', 'it-l10n-ithemes-security-pro' ); ?></a>
		</h1>

		<div id="itsec-settings-messages-container">
			<?php
				foreach ( ITSEC_Response::get_errors() as $error ) {
					ITSEC_Lib::show_error_message( $error );
				}

				foreach ( ITSEC_Response::get_messages() as $message ) {
					ITSEC_Lib::show_status_message( $message );
				}
			?>
		</div>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2 hide-if-no-js">
				<div id="postbox-container-2" class="postbox-container">
					<?php if ( 'file' === ITSEC_Modules::get_setting( 'global', 'log_type' ) ) : ?>
						<p><?php _e( 'To view logs within the plugin you must enable database logging in the Global Settings. File logging is not available for access within the plugin itself.', 'it-l10n-ithemes-security-pro' ); ?></p>
					<?php else : ?>
						<div class="itsec-module-cards-container list">
							<p><?php _e( 'Below are various logs of information collected by iThemes Security Pro. This information can help you get a picture of what is happening with your site and the level of success you have achieved in your security efforts.', 'it-l10n-ithemes-security-pro' ); ?></p>
							<p><?php _e( 'Logging settings can be managed in the Global Settings.', 'it-l10n-ithemes-security-pro' ); ?></p>


							<?php $form->start_form( 'itsec-module-settings-form' ); ?>
								<?php $form->add_nonce( 'itsec-settings-page' ); ?>
								<p><?php $form->add_select( 'filter', $filters ); ?></p>
							<?php $form->end_form(); ?>

							<?php $form->start_form( array( 'method' => 'GET' ) ); ?>
								<?php $this->show_filtered_logs( $filter ); ?>
							<?php $form->end_form(); ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="itsec-modal-background"></div>

				<div id="postbox-container-1" class="postbox-container">
					<?php foreach ( $this->widgets as $id => $widget ) : ?>
						<?php $form->start_form( "itsec-sidebar-widget-form-$id" ); ?>
							<?php $form->add_nonce( 'itsec-logs-page' ); ?>
							<?php $form->add_hidden( 'widget-id', $id ); ?>
							<div id="itsec-sidebar-widget-<?php echo $id; ?>" class="postbox itsec-sidebar-widget">
								<h3 class="hndle ui-sortable-handle"><span><?php echo esc_html( $widget->title ); ?></span></h3>
								<div class="inside">
									<?php $this->get_widget_settings( $id, $form, true ); ?>
								</div>
							</div>
						<?php $form->end_form(); ?>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="hide-if-js">
				<p class="itsec-warning-message"><?php _e( 'iThemes Security requires Javascript in order for the settings to be modified. Please enable Javascript to configure the settings.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</div>
		</div>
	</div>
<?php

	}

	private function show_filtered_logs( $filter ) {
		$callback = null;

		foreach ( $this->logger_displays as $display ) {
			if ( $display['module'] === $filter ) {
				$callback = $display['callback'];
				break;
			}
		}

		echo '<form method="get">';
		echo '<input type="hidden" name="page" value="' . ( isset( $_GET['page'] ) ? esc_attr( $_GET['page'] ) : '' ) . '">';

		if ( $callback ) {
			call_user_func( $callback );
		} else {
			$this->all_logs_content( false );
		}

		echo '</form>';

		if ( ! $callback ) {
			$this->clear_logs_form();
		}
	}

	/**
	 * Displays all logs content
	 *
	 * @since 4.3
	 *
	 * @param bool $include_clear_logs_form Whether to include the form to clear all logs.
	 *
	 * @return void
	 */
	public function all_logs_content( $include_clear_logs_form = true ) {

		require_once( ITSEC_Core::get_core_dir() . '/logger-all-logs.php' );

		$log_display = new ITSEC_Logger_All_Logs();
		$log_display->prepare_items();
		$log_display->display();

		if ( $include_clear_logs_form ) {
			$this->clear_logs_form();
		}
	}

	/**
	 * Display the clear logs form.
	 */
	public function clear_logs_form() {

		global $wpdb;
		$log_count = $wpdb->get_var( "SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "itsec_log`;" );

		?>
		<form method="post" action="">
			<?php wp_nonce_field( 'itsec_clear_logs', 'wp_nonce' ); ?>
			<input type="hidden" name="itsec_clear_logs" value="clear_logs"/>
			<table class="form-table">
				<tr valign="top">
					<th scope="row" class="settinglabel">
						<?php _e( 'Log Summary', 'it-l10n-ithemes-security-pro' ); ?>
					</th>
					<td class="settingfield">

						<p><?php _e( 'Your database contains', 'it-l10n-ithemes-security-pro' ); ?>
							<strong><?php echo $log_count; ?></strong> <?php _e( 'log entries.', 'it-l10n-ithemes-security-pro' ); ?>
						</p>

						<p><?php _e( 'Use the button below to purge the log table in your database. Please note this will purge all log entries in the database including 404s.', 'it-l10n-ithemes-security-pro' ); ?></p>

						<p class="submit"><input type="submit" class="button-primary"
						                         value="<?php _e( 'Clear Logs', 'it-l10n-ithemes-security-pro' ); ?>"/></p>
					</td>
				</tr>
			</table>
		</form>
		<?php
	}
}

new ITSEC_Logs_Page();
