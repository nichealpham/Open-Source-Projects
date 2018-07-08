<?php

final class ITSEC_Version_Management_Settings_Page extends ITSEC_Module_Settings_Page {
	private $version = 1;


	public function __construct() {
		$this->id = 'version-management';
		$this->title = __( 'Version Management', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Protect your site when outdated software is not updated quickly enough.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;

		parent::__construct();
	}

	public function enqueue_scripts_and_styles() {
		wp_enqueue_style( 'itsec-version-management-style', plugins_url( 'css/settings-page.css', __FILE__ ), array(), $this->version );
	}

	protected function render_description( $form ) {

?>
	<p><?php _e( 'Even with recommended security settings, running vulnerable software on your site can give an attacker an entry point into your site. These settings help protect your site with options to automatically update to new versions or to increase use security when the site\'s software is outdated.', 'it-l10n-ithemes-security-pro' ); ?></p>
<?php

	}

	protected function render_settings( $form ) {
		$validator = ITSEC_Modules::get_validator( $this->id );

		$users_and_roles = $validator->get_available_admin_users_and_roles();

		$users = $users_and_roles['users'];
		$roles = $users_and_roles['roles'];

		natcasesort( $users );


		$contacts = $form->get_option( 'email_contacts' );

		if ( empty( $contacts ) || ! is_array( $contacts ) ) {
			$form->set_option( 'email_contacts', array_keys( $roles ) );
		}

?>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="itsec-version-management-wordpress_automatic_updates"><?php esc_html_e( 'WordPress Updates', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<p>
					<?php $form->add_checkbox( 'wordpress_automatic_updates' ); ?>
					<label for="itsec-version-management-wordpress_automatic_updates"><?php esc_html_e( 'Automatically install the latest WordPress release.', 'it-l10n-ithemes-security-pro' ); ?></label>
					<?php $this->render_tooltip( __( 'This should be enabled unless you actively maintain this site on a daily basis and install the updates manually shortly after they are released.', 'it-l10n-ithemes-security-pro' ) ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-version-management-plugin_automatic_updates"><?php esc_html_e( 'Plugin Updates', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<p>
					<?php $form->add_checkbox( 'plugin_automatic_updates' ); ?>
					<label for="itsec-version-management-plugin_automatic_updates"><?php esc_html_e( 'Automatically install the latest plugin updates.', 'it-l10n-ithemes-security-pro' ); ?></label>
					<?php $this->render_tooltip( __( 'This should be enabled unless you actively maintain this site on a daily basis and install the updates manually shortly after they are released.', 'it-l10n-ithemes-security-pro' ) ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-version-management-theme_automatic_updates"><?php esc_html_e( 'Theme Updates', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<p>
					<?php $form->add_checkbox( 'theme_automatic_updates' ); ?>
					<label for="itsec-version-management-theme_automatic_updates"><?php esc_html_e( 'Automatically install the latest theme updates.', 'it-l10n-ithemes-security-pro' ); ?></label>
					<?php $this->render_tooltip( __( 'This should be enabled unless your theme has file customizations.', 'it-l10n-ithemes-security-pro' ) ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-version-management-strengthen_when_outdated"><?php esc_html_e( 'Strengthen Site When Running Outdated Software', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<p>
					<?php $form->add_checkbox( 'strengthen_when_outdated' ); ?>
					<label for="itsec-version-management-strengthen_when_outdated"><?php esc_html_e( 'Automatically add extra protections to the site when an available update has not been installed for a month.', 'it-l10n-ithemes-security-pro' ); ?>
					<?php
						$tooltip = esc_html__( 'This will harden your website security in a couple of key ways:', 'it-l10n-ithemes-security-pro' ) . '<br/><br/>';
						$tooltip .= esc_html__( 'It will force all users that do not have two-factor enabled to provide a login code sent to their email address before logging back in.', 'it-l10n-ithemes-security-pro' ) . '<br/><br/>';
						$tooltip .= esc_html__( 'Additionally, it will disable the WP File Editor (which blocks people from editing plugin or theme code), XML-RPC ping backs, and block multiple authentication attempts per XML-RPC request (both of which will make XML-RPC stronger against attacks without having to completely turn it off).', 'it-l10n-ithemes-security-pro' );

						$this->render_tooltip( $tooltip );
					?>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-version-management-scan_for_old_wordpress_sites"><?php esc_html_e( 'Scan For Old WordPress Sites', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<p>
					<?php $form->add_checkbox( 'scan_for_old_wordpress_sites' ); ?>
					<label for="itsec-version-management-scan_for_old_wordpress_sites"><?php esc_html_e( 'Run a daily scan of the hosting account for old WordPress sites that could allow an attacker to compromise the server.', 'it-l10n-ithemes-security-pro' ); ?></label>
					<?php $this->render_tooltip( __( 'This feature will check for outdated WordPress installs on your hosting account. A single outdated WordPress site with a vulnerability could allow attackers to compromise all the other sites on the same hosting account.', 'it-l10n-ithemes-security-pro' ) ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Email Contacts', 'it-l10n-ithemes-security-pro' ); ?></th>
			<td>
				<p><?php _e( 'Select which users should get an email if a version management issue is found.', 'it-l10n-ithemes-security-pro' ); ?></p>

				<ul>
					<?php foreach ( $roles as $role => $name ) : ?>
						<li>
							<?php $form->add_multi_checkbox( 'email_contacts', $role ); ?>
							<label for="itsec-version-management-email_contacts-role-<?php echo esc_attr( preg_replace( '/^role:/', '', $role ) ); ?>"><?php echo esc_html( sprintf( _x( 'All %s users', 'role', 'it-l10n-ithemes-security-pro' ), $name ) ); ?></label>
						</li>
					<?php endforeach; ?>
				</ul>

				<ul>
					<?php foreach ( $users as $id => $name ) : ?>
						<li>
							<?php $form->add_multi_checkbox( 'email_contacts', $id ); ?>
							<label for="itsec-version-management-email_contacts-<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $name ); ?></label>
						</li>
					<?php endforeach; ?>
				</ul>
			</td>
		</tr>
	</table>
<?php

	}

	private function render_tooltip( $text ) {
		/* translators: hover over this text to see the tooltip. */
		$placeholder = __( '?', 'it-l10n-ithemes-security-pro' );

		printf( '<!-- Tooltip --><span class="tooltip"><span class="tooltip-container">%1$s<span class="info"><span class="text">%2$s</span></span></span></span><!-- /Tooltip -->', $placeholder, $text );
	}
}

new ITSEC_Version_Management_Settings_Page();
