<?php


final class ITSEC_Settings_Page {
	private $version = 1.5;

	private $self_url = '';
	private $modules = array();
	private $widgets = array();
	private $translations = array();


	public function __construct() {
		add_action( 'itsec-settings-page-register-module', array( $this, 'register_module' ) );
		add_action( 'itsec-settings-page-register-widget', array( $this, 'register_widget' ) );

		add_action( 'itsec-page-show', array( $this, 'handle_page_load' ) );
		add_action( 'itsec-page-ajax', array( $this, 'handle_ajax_request' ) );
		add_action( 'admin_print_scripts', array( $this, 'add_scripts' ) );
		add_action( 'admin_print_styles', array( $this, 'add_styles' ) );

		add_filter( 'admin_body_class', array( $this, 'add_settings_classes' ) );

		$this->set_translation_strings();

		if ( ! empty( $_GET['enable'] ) && ! empty( $_GET['itsec-enable-nonce'] ) && wp_verify_nonce( $_GET['itsec-enable-nonce'], 'itsec-enable-' . $_GET['enable'] ) ) {
			ITSEC_Modules::activate( $_GET['enable'] );
		}

		require( dirname( __FILE__ ) . '/module-settings.php' );
		require( dirname( __FILE__ ) . '/sidebar-widget.php' );

		require_once( ITSEC_Core::get_core_dir() . '/lib/form.php' );


		do_action( 'itsec-settings-page-init' );
		do_action( 'itsec-settings-page-register-modules' );
		do_action( 'itsec-settings-page-register-widgets' );


		if ( ! empty( $_POST ) && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			$this->handle_post();
		}
	}

	public function add_settings_classes( $classes ) {
		if ( ITSEC_Modules::get_setting( 'global', 'show_error_codes' ) ) {
			$classes .= ' itsec-show-error-codes';
		}

		if ( ITSEC_Modules::get_setting( 'global', 'write_files' ) ) {
			$classes .= ' itsec-write-files-enabled';
		} else {
			$classes .= ' itsec-write-files-disabled';
		}

		$classes = trim( $classes );

		return $classes;
	}

	public function add_scripts() {
		foreach ( $this->modules as $id => $module ) {
			$module->enqueue_scripts_and_styles();
		}

		foreach ( $this->widgets as $id => $widget ) {
			$widget->enqueue_scripts_and_styles();
		}

		$vars = array(
			'ajax_action'         => 'itsec_settings_page',
			'ajax_nonce'          => wp_create_nonce( 'itsec-settings-nonce' ),
			'show_security_check' => ITSEC_Modules::get_setting( 'global', 'show_security_check' ),
			'translations'        => $this->translations,
		);

		if ( $vars['show_security_check'] ) {
			ITSEC_Modules::set_setting( 'global', 'show_security_check', false );

			if ( ! empty( $_GET['module'] ) && 'security-check' === $_GET['module'] ) {
				$vars['show_security_check'] = false;
			}
		}

		wp_enqueue_script( 'itsec-settings-page-script', plugins_url( 'js/script.js', __FILE__ ), array(), $this->version, true );
		wp_localize_script( 'itsec-settings-page-script', 'itsec_page', $vars );
	}

	public function add_styles() {
		wp_enqueue_style( 'itsec-settings-page-style', plugins_url( 'css/style.css', __FILE__ ), array(), $this->version );
	}

	private function set_translation_strings() {
		$this->translations = array(
			'save_settings'     => __( 'Save Settings', 'it-l10n-ithemes-security-pro' ),
			'close_settings'    => __( 'Close', 'it-l10n-ithemes-security-pro' ),
			'show_settings'     => __( 'Configure Settings', 'it-l10n-ithemes-security-pro' ),
			'hide_settings'     => __( 'Hide Settings', 'it-l10n-ithemes-security-pro' ),
			'show_description'  => __( 'Learn More', 'it-l10n-ithemes-security-pro' ),
			'hide_description'  => __( 'Hide Details', 'it-l10n-ithemes-security-pro' ),
			'show_information'  => __( 'Show Details', 'it-l10n-ithemes-security-pro' ),
			'activate'          => __( 'Enable', 'it-l10n-ithemes-security-pro' ),
			'deactivate'        => __( 'Disable', 'it-l10n-ithemes-security-pro' ),
			'error'             => __( 'Error', 'it-l10n-ithemes-security-pro' ),

			/* translators: 1: module name */
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

	public function handle_ajax_request() {
		if ( WP_DEBUG ) {
			ini_set( 'display_errors', 1 );
		}


		ITSEC_Core::set_interactive( true );

		$method = ( isset( $_POST['method'] ) && is_string( $_POST['method'] ) ) ? $_POST['method'] : '';
		$module = ( isset( $_POST['module'] ) && is_string( $_POST['module'] ) ) ? $_POST['module'] : '';

		if ( empty( $GLOBALS['hook_suffix'] ) ) {
			$GLOBALS['hook_suffix'] = 'toplevel_page_itsec';
		}


		if ( false === check_ajax_referer( 'itsec-settings-nonce', 'nonce', false ) ) {
			ITSEC_Response::add_error( new WP_Error( 'itsec-settings-page-failed-nonce', __( 'A nonce security check failed, preventing the request from completing as expected. Please try reloading the page and trying again.', 'it-l10n-ithemes-security-pro' ) ) );
		} else if ( ! ITSEC_Core::current_user_can_manage() ) {
			ITSEC_Response::add_error( new WP_Error( 'itsec-settings-page-insufficient-privileges', __( 'A permissions security check failed, preventing the request from completing as expected. The currently logged in user does not have sufficient permissions to make this request. Please try reloading the page and trying again.', 'it-l10n-ithemes-security-pro' ) ) );
		} else if ( empty( $method ) ) {
			ITSEC_Response::add_error( new WP_Error( 'itsec-settings-page-missing-method', __( 'The server did not receive a valid request. The required "method" argument is missing. Please try again.', 'it-l10n-ithemes-security-pro' ) ) );
		} else if ( 'save' === $method ) {
			$this->handle_post();
		} else if ( empty( $module ) ) {
			ITSEC_Response::add_error( new WP_Error( 'itsec-settings-page-missing-module', __( 'The server did not receive a valid request. The required "module" argument is missing. Please try again.', 'it-l10n-ithemes-security-pro' ) ) );
		} else if ( 'activate' === $method ) {
			ITSEC_Response::set_response( ITSEC_Modules::activate( $module ) );
		} else if ( 'deactivate' === $method ) {
			ITSEC_Response::set_response( ITSEC_Modules::deactivate( $module ) );
		} else if ( 'is_active' === $method ) {
			ITSEC_Response::set_response( ITSEC_Modules::is_active( $module ) );
		} else if ( 'get_refreshed_module_settings' === $method ) {
			ITSEC_Response::set_response( $this->get_module_settings( $module ) );
		} else if ( 'get_refreshed_widget_settings' === $method ) {
			ITSEC_Response::set_response( $this->get_widget_settings( $module ) );
		} else if ( 'handle_module_request' === $method ) {
			if ( isset( $this->modules[$module] ) ) {
				if ( isset( $_POST['data'] ) ) {
					$returned_value = $this->modules[$module]->handle_ajax_request( $_POST['data'] );

					if ( ! is_null( $returned_value ) ) {
						ITSEC_Response::set_response( $returned_value );
					}
				} else {
					ITSEC_Response::add_error( new WP_Error( 'itsec-settings-page-module-request-missing-data', __( 'The server did not receive a valid request. The required "data" argument for the module is missing. Please try again.', 'it-l10n-ithemes-security-pro' ) ) );
				}
			} else {
				ITSEC_Response::add_error( new WP_Error( 'itsec-settings-page-module-request-invalid-module', __( "The server did not receive a valid request. The supplied module, \"$module\", does not exist. Please try again.", 'it-l10n-ithemes-security-pro' ) ) );
			}
		} else if ( 'handle_widget_request' === $method ) {
			if ( isset( $this->widgets[$module] ) ) {
				if ( isset( $_POST['data'] ) ) {
					$this->widgets[$module]->handle_ajax_request( $_POST['data'] );
				} else {
					ITSEC_Response::add_error( new WP_Error( 'itsec-settings-page-widget-request-missing-data', __( 'The server did not receive a valid request. The required "data" argument for the widget is missing. Please try again.', 'it-l10n-ithemes-security-pro' ) ) );
				}
			} else {
				ITSEC_Response::add_error( new WP_Error( 'itsec-settings-page-widget-request-invalid-widget', __( "The server did not receive a valid request. The supplied widget, \"$module\", does not exist. Please try again.", 'it-l10n-ithemes-security-pro' ) ) );
			}
		} else {
			ITSEC_Response::add_error( new WP_Error( 'itsec-settings-page-unknown-method', __( 'The server did not receive a valid request. An unknown "method" argument was supplied. Please try again.', 'it-l10n-ithemes-security-pro' ) ) );
		}


		ITSEC_Response::send_json();
	}

	public function register_module( $module ) {
		if ( ! is_object( $module ) || ! is_a( $module, 'ITSEC_Module_Settings_Page' ) ) {
			trigger_error( 'An invalid module was registered.', E_USER_ERROR );
			return;
		}

		if ( isset( $this->modules[$module->id] ) ) {
			trigger_error( "A module with the id of {$module->id} is already registered. Module id's must be unique." );
			return;
		}

		$this->modules[$module->id] = $module;
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
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			// Only process the nonce when the request is not an AJAX request as the AJAX handler has its own nonce check.
			ITSEC_Form::check_nonce( 'itsec-settings-page' );
		}


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

	public function handle_page_load( $self_url ) {
		$this->self_url = $self_url;

		$this->show_settings_page();
	}

	private function get_module_settings( $id, $form = false, $echo = false ) {
		if ( ! isset( $this->modules[$id] ) ) {
			$error = new WP_Error( 'itsec-settings-page-get-module-settings-invalid-id', sprintf( __( 'The requested module (%s) does not exist. Settings for it cannot be rendered.', 'it-l10n-ithemes-security-pro' ), $id ) );

			if ( $echo ) {
				ITSEC_Lib::show_error_message( $error );
			} else {
				return $error;
			}
		}

		if ( false === $form ) {
			$form = new ITSEC_Form();
		}

		$module = $this->modules[$id];

		$form->add_input_group( $id );
		$form->set_defaults( $module->get_settings() );

		if ( ! $echo ) {
			ob_start();
		}

		$module->render( $form );

		$form->remove_all_input_groups();

		if ( ! $echo ) {
			return ob_get_clean();
		}
	}

	private function get_widget_settings( $id, $form = false, $echo = false ) {
		if ( ! isset( $this->widgets[$id] ) ) {
			$error = new WP_Error( 'itsec-settings-page-get-widget-settings-invalid-id', sprintf( __( 'The requested widget (%s) does not exist. Settings for it cannot be rendered.', 'it-l10n-ithemes-security-pro' ), $id ) );

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
		$form = new ITSEC_Form();


		$module_filters = array(
			'all'         => array(
				_x( 'All', 'List all modules', 'it-l10n-ithemes-security-pro' ),
				0,
			),
			'recommended' => array(
				_x( 'Recommended', 'List recommended modules', 'it-l10n-ithemes-security-pro' ),
				0,
			),
			'advanced'    => array(
				_x( 'Advanced', 'List advanced modules', 'it-l10n-ithemes-security-pro' ),
				0,
			),
		);


		$current_type = isset( $_REQUEST['module_type'] ) ? $_REQUEST['module_type'] : 'recommended';
		$visible_modules = array();

		foreach ( $this->modules as $id => $module ) {
			$module_filters['all'][1]++;

			if ( 'all' === $current_type ) {
				$visible_modules[] = $id;
			}


			if ( isset( $module_filters[$module->type] ) ) {
				$module_filters[$module->type][1]++;

				if ( $module->type === $current_type ) {
					$visible_modules[] = $id;
				}
			}


			$module->enabled = ITSEC_Modules::is_active( $id );
			$module->always_active = ITSEC_Modules::is_always_active( $id );
		}

		$feature_tabs = array();

		foreach ( $module_filters as $type => $data ) {
			if ( $current_type === $type ) {
				$class = 'current';
			} else {
				$class = '';
			}

			$feature_tabs[] = "<li class='itsec-module-filter' id='itsec-module-filter-$type'><a href='" . esc_url( add_query_arg( 'module_type', $type, $this->self_url ) ) . "' class='$class'>{$data[0]} <span class='count'>({$data[1]})</span></a>";
		}


		$whitelisted_ips = ITSEC_Lib::get_whitelisted_ips();
		$blacklisted_ips = ITSEC_Lib::get_blacklisted_ips();

		// Get user's view preference
		$view = get_user_meta( get_current_user_id(), 'itsec-settings-view', true );

		// Default to grid view for users that have an invalid or unspecified view
		if ( ! in_array( $view, array( 'grid', 'list' ) ) ) {
			$view = 'grid';
		}

?>
	<div class="wrap">
		<h1>
			<?php _e( 'iThemes Security', 'it-l10n-ithemes-security-pro' ); ?>
			<a href="<?php echo esc_url( ITSEC_Core::get_logs_page_url() ); ?>" class="page-title-action"><?php _e( 'View Logs', 'it-l10n-ithemes-security-pro' ); ?></a>
			<a href="<?php echo esc_url( apply_filters( 'itsec_support_url', 'https://wordpress.org/support/plugin/better-wp-security' ) ); ?>" target="_blank" rel="noopener noreferrer" class="page-title-action"><?php _e( 'Support', 'it-l10n-ithemes-security-pro' ); ?></a>
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
					<div class="itsec-module-section-heading">
						<div class="itsec-settings-view-toggle hide-if-no-js" data-nonce="<?php echo esc_attr( wp_create_nonce( 'set-user-setting-itsec-settings-view' ) ); ?>">
							<a class="itsec-grid<?php if ( 'grid' === $view ) { echo ' itsec-selected'; } ?>"><span class="dashicons dashicons-grid-view"></span></a>
							<a class="itsec-list<?php if ( 'list' === $view ) { echo ' itsec-selected'; } ?>"><span class="dashicons dashicons-list-view"></span></a>
						</div>
						<ul class="subsubsub itsec-feature-tabs hide-if-no-js">
							<?php echo implode( $feature_tabs, " |</li>\n" ) . "</li>\n"; ?>
						</ul>
					</div>
					<div class="itsec-module-cards-container <?php echo $view; ?> hide-if-js">
						<?php $form->start_form( 'itsec-module-settings-form' ); ?>
							<?php $form->add_nonce( 'itsec-settings-page' ); ?>
							<ul class="itsec-module-cards">
								<?php foreach ( $this->modules as $id => $module ) : ?>
									<?php
										if ( ! in_array( $id, $visible_modules ) ) {
//											continue;
										}

										$classes = array(
											'itsec-module-type-' . $module->type,
											'itsec-module-type-' . ( $module->enabled ? 'enabled' : 'disabled' ),
										);

										if ( $module->upsell ) {
											$classes[] = 'itsec-module-pro-upsell';
										}

										if ( $module->pro ) {
											$classes[] = 'itsec-module-type-pro';
										}
									?>
									<li id="itsec-module-card-<?php echo $id; ?>" class="itsec-module-card <?php echo implode( ' ', $classes ); ?>" data-module-id="<?php echo $id; ?>">
										<div class="itsec-module-card-content">
											<?php if ( $module->upsell ) : ?>
												<a href="<?php echo esc_url( $module->upsell_url ); ?>" target="_blank" rel="noopener noreferrer" class="itsec-pro-upsell">&nbsp;</a>
											<?php endif; ?>
											<h2><?php echo esc_html( $module->title ); ?></h2>
											<?php if ( $module->pro ) : ?>
												<div class="itsec-pro-label"><?php _e( 'Pro', 'it-l10n-ithemes-security-pro' ); ?></div>
											<?php endif; ?>
											<p class="module-description"><?php echo $module->description; ?></p>
											<?php if ( ! $module->upsell ) : ?>
												<div class="module-actions hide-if-no-js">
													<?php if ( $module->information_only ) : ?>
														<button class="button button-secondary itsec-toggle-settings information-only"><?php echo $this->translations['show_information']; ?></button>
													<?php elseif ( $module->enabled || $module->always_active ) : ?>
														<button class="button button-secondary itsec-toggle-settings"><?php echo $this->translations['show_settings']; ?></button>
														<?php if ( ! $module->always_active ) : ?>
															<button class="button button-secondary itsec-toggle-activation"><?php echo $this->translations['deactivate']; ?></button>
														<?php endif; ?>
													<?php else : ?>
														<button class="button button-secondary itsec-toggle-settings"><?php echo $this->translations['show_description']; ?></button>
														<button class="button button-primary itsec-toggle-activation"><?php echo $this->translations['activate']; ?></button>
													<?php endif; ?>
												</div>
											<?php endif; ?>
										</div>
										<?php if ( ! $module->upsell ) : ?>
											<div class="itsec-module-settings-container">
												<div class="itsec-modal-navigation">
													<button class="dashicons itsec-close-modal"></button>
													<button class="itsec-right dashicons hidden"><span class="screen-reader-text"><?php _e( 'Configure next iThemes Security setting', 'it-l10n-ithemes-security-pro' ); ?></span></button>
													<button class="itsec-left dashicons hidden"><span class="screen-reader-text"><?php _e( 'Configure previous iThemes Security setting', 'it-l10n-ithemes-security-pro' ); ?></span></button>
												</div>
												<div class="itsec-module-settings-content-container">
													<div class="itsec-module-settings-content">
														<h3 class="itsec-modal-header"><?php echo esc_html( $module->title ); ?></h3>
														<div class="itsec-module-messages-container"></div>
														<div class="itsec-module-settings-content-main">
															<?php $this->get_module_settings( $id, $form, true ); ?>
														</div>
													</div>
												</div>
												<div class="itsec-list-content-footer hide-if-no-js">
													<?php if ( $module->can_save ) : ?>
														<button class="button button-primary align-left itsec-module-settings-save"><?php echo $this->translations['save_settings']; ?></button>
													<?php endif; ?>
													<button class="button button-secondary align-left itsec-module-settings-cancel"><?php _e( 'Cancel', 'it-l10n-ithemes-security-pro' ); ?></button>
												</div>
												<div class="itsec-modal-content-footer">
													<?php if ( $module->enabled || $module->always_active || $module->information_only ) : ?>
														<?php if ( ! $module->always_active && ! $module->information_only ) : ?>
															<button class="button button-secondary align-right itsec-toggle-activation"><?php echo $this->translations['deactivate']; ?></button>
														<?php endif; ?>
													<?php else : ?>
														<button class="button button-primary align-right itsec-toggle-activation"><?php echo $this->translations['activate']; ?></button>
													<?php endif; ?>

													<?php if ( $module->can_save ) : ?>
														<button class="button button-primary align-left itsec-module-settings-save"><?php echo $this->translations['save_settings']; ?></button>
													<?php else : ?>
														<button class="button button-primary align-left itsec-close-modal"><?php echo $this->translations['close_settings']; ?></button>
													<?php endif; ?>
												</div>
											</div>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
								<li class="itsec-module-card-filler"></li>
							</ul>

						<?php $form->end_form(); ?>
					</div>
				</div>
				<div class="itsec-modal-background"></div>

				<div id="postbox-container-1" class="postbox-container">
					<?php foreach ( $this->widgets as $id => $widget ) : ?>
						<?php if ( $widget->settings_form ) : ?>
						<?php $form->start_form( "itsec-sidebar-widget-form-$id" ); ?>
							<?php $form->add_nonce( 'itsec-settings-page' ); ?>
							<?php $form->add_hidden( 'widget-id', $id ); ?>
						<?php endif; ?>
							<div id="itsec-sidebar-widget-<?php echo $id; ?>" class="postbox itsec-sidebar-widget">
								<h3 class="hndle ui-sortable-handle"><span><?php echo esc_html( $widget->title ); ?></span></h3>
								<div class="inside">
									<?php $this->get_widget_settings( $id, $form, true ); ?>
								</div>
							</div>
						<?php
						if ( $widget->settings_form ) {
							$form->end_form();
						}
						?>
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
}

new ITSEC_Settings_Page();
