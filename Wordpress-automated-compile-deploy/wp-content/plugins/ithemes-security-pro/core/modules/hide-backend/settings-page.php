<?php

final class ITSEC_Hide_Backend_Settings_Page extends ITSEC_Module_Settings_Page {
	private $version = 1;


	public function __construct() {
		$this->id = 'hide-backend';
		$this->title = __( 'Hide Backend', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Hide the login page by changing its name and preventing access to wp-login.php and wp-admin.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'advanced';

		parent::__construct();
	}

	public function handle_form_post( $data ) {
		$retval = ITSEC_Modules::set_settings( $this->id, $data );

		if ( $retval['saved'] ) {
			if ( $retval['new_settings']['enabled'] ) {
				$args = array(
					'wp-login.php?',
					$retval['new_settings']['slug'] . '?',
				);
			} else {
				$args = array(
					$retval['old_settings']['slug'] . '?',
					'wp-login.php?',
				);
			}

			ITSEC_Response::add_js_function_call( 'itsec_hide_backend_update_logout_url', $args );
		}
	}

	public function enqueue_scripts_and_styles() {
		wp_enqueue_script( 'itsec-hide-backend-settings-page-script', plugins_url( 'js/settings-page.js', __FILE__ ), array( 'jquery' ), $this->version, true );
	}

	protected function render_description( $form ) {

?>
	<p><?php _e( 'Hides the login page (wp-login.php, wp-admin, admin and login) making it harder to find by automated attacks and making it easier for users unfamiliar with the WordPress platform.', 'it-l10n-ithemes-security-pro' ); ?></p>
<?php

	}

	protected function render_settings( $form ) {
		$settings = $form->get_options();
		$permalink_structure = get_option( 'permalink_structure', false );

		if ( empty( $permalink_structure ) && ! is_multisite() ) {
			echo '<div class="itsec-warning-message">';
			printf( __( 'You must change <a href="%s">WordPress permalinks</a> to a setting other than "Plain" in order to use this feature.', 'it-l10n-ithemes-security-pro' ), network_admin_url( 'options-permalink.php' ) );
			echo "</div>\n";

			return;
		}

?>
	<div class="itsec-write-files-disabled">
		<div class="itsec-warning-message"><?php _e( 'The "Write to Files" setting is disabled in Global Settings. In order to use this feature, you must enable the "Write to Files" setting.', 'it-l10n-ithemes-security-pro' ); ?></div>
	</div>

	<div class="itsec-write-files-enabled">
		<table class="form-table itsec-settings-section">
			<tr>
				<th scope="row"><label for="itsec-hide-backend-enabled"><?php _e( 'Hide Backend', 'it-l10n-ithemes-security-pro' ); ?></label></th>
				<td>
					<?php $form->add_checkbox( 'enabled', array( 'class' => 'itsec-settings-toggle' ) ); ?>
					<label for="itsec-hide-backend-enabled"><?php _e( 'Enable the hide backend feature.', 'it-l10n-ithemes-security-pro' ); ?></label>
				</td>
			</tr>
		</table>

		<table class="form-table itsec-settings-section itsec-hide-backend-enabled-content">
			<tr>
				<th scope="row"><label for="itsec-hide-backend-slug"><?php _e( 'Login Slug', 'it-l10n-ithemes-security-pro' ); ?></label></th>
				<td>
					<?php $form->add_text( 'slug', array( 'class' => 'text code' ) ); ?>
					<br />
					<label for="itsec-hide-backend-slug"><?php printf( __( 'Login URL: %s', 'it-l10n-ithemes-security-pro' ), trailingslashit( get_option( 'siteurl' ) ) . '<span style="color: #4AA02C">' . sanitize_title( $settings['slug'] ) . '</span>' ); ?></label>
					<p class="description"><?php _e( 'The login url slug cannot be "login," "admin," "dashboard," or "wp-login.php" as these are use by default in WordPress.', 'it-l10n-ithemes-security-pro' ); ?></p>
					<p class="description"><em><?php _e( 'Note: The output is limited to alphanumeric characters, underscore (_) and dash (-). Special characters such as "." and "/" are not allowed and will be converted in the same manner as a post title. Please review your selection before logging out.', 'it-l10n-ithemes-security-pro' ); ?></em></p>
				</td>
			</tr>
			<?php if ( get_site_option( 'users_can_register' ) ) : ?>
				<tr>
					<th scope="row"><label for="itsec-hide-backend-register"><?php _e( 'Register Slug', 'it-l10n-ithemes-security-pro' ); ?></label></th>
					<td>
						<?php $form->add_text( 'register', array( 'class' => 'text code' ) ); ?>
						<br />
						<label for="itsec-hide-backend-register"><?php printf( __( 'Registration URL: %s', 'it-l10n-ithemes-security-pro' ), trailingslashit( get_option( 'siteurl' ) ) . '<span style="color: #4AA02C">' . sanitize_title( $settings['register'] ) . '</span>' ); ?></label>
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<th scope="row"><label for="itsec-hide-backend-theme_compat"><?php _e( 'Enable Redirection', 'it-l10n-ithemes-security-pro' ); ?></label></th>
				<td>
					<?php $form->add_checkbox( 'theme_compat', array( 'class' => 'itsec-settings-toggle' ) ); ?>
					<label for="itsec-hide-backend-theme_compat"><?php _e( 'Redirect users to a custom location on your site, instead of throwing a 403 (forbidden) error.', 'it-l10n-ithemes-security-pro' ); ?></label>
				</td>
			</tr>
			<tr class="itsec-hide-backend-theme_compat-content">
				<th scope="row"><label for="itsec-hide-backend-theme_compat_slug"><?php _e( 'Redirection Slug', 'it-l10n-ithemes-security-pro' ); ?></label></th>
				<td>
					<?php $form->add_text( 'theme_compat_slug', array( 'class' => 'text code' ) ); ?>
					<br />
					<label for="itsec-hide-backend-theme_compat_slug"><?php printf( __( 'Redirect Location: %s', 'it-l10n-ithemes-security-pro' ), trailingslashit( get_option( 'siteurl' ) ) . '<span style="color: #4AA02C">' . sanitize_title( $settings['theme_compat_slug'] ) . '</span>' ); ?></label>
					<p class="description"><?php _e( 'The slug to redirect users to when they attempt to access wp-admin while not logged in.', 'it-l10n-ithemes-security-pro' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="itsec-hide-backend-post_logout_slug"><?php _e( 'Custom Login Action', 'it-l10n-ithemes-security-pro' ); ?></label></th>
				<td>
					<?php $form->add_text( 'post_logout_slug', array( 'class' => 'text code' ) ); ?>
					<br />
					<p class="description"><?php _e( 'WordPress uses the "action" variable to handle many login and logout functions. By default this plugin can handle the normal ones but some plugins and themes may utilize a custom action (such as logging out of a private post). If you need a custom action please enter it here.', 'it-l10n-ithemes-security-pro' ); ?></p>
				</td>
			</tr>
		</table>
	</div>
<?php

	}
}

new ITSEC_Hide_Backend_Settings_Page();
