<?php

final class ITSEC_Password_Expiration_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'password-expiration';
		$this->title = __( 'Password Expiration', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Strengthen the passwords on the site with automated password expiration.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;

		parent::__construct();
	}

	protected function render_description( $form ) {

?>
	<p><?php _e( 'Strengthen the passwords on the site with automated password expiration.', 'it-l10n-ithemes-security-pro' ); ?></p>
<?php

	}

	protected function render_settings( $form ) {
		$roles = array(
			'administrator' => translate_user_role( 'Administrator' ),
			'editor'        => translate_user_role( 'Editor' ),
			'author'        => translate_user_role( 'Author' ),
			'contributor'   => translate_user_role( 'Contributor' ),
			'subscriber'    => translate_user_role( 'Subscriber' ),
		);

		$form->set_option( 'expire_force', false );

?>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="itsec-password-expiration-expire_role"><?php _e( 'Select Minimum Role for Password Expiration', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_select( 'expire_role', $roles ); ?>
				<br />
				<label for="itsec-password-expiration-expire_role"><?php _e( 'Minimum role at which password expiration is enforced.', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'We suggest enabling this setting for all users, but it may lead to users forgetting their passwords. The minimum role option above allows you to select the lowest user role to apply strong password generation.', 'it-l10n-ithemes-security-pro' ); ?></p>
				<p class="description"><?php _e( 'For more information on WordPress roles and capabilities please see <a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank" rel="noopener noreferrer">http://codex.wordpress.org/Roles_and_Capabilities</a>.', 'it-l10n-ithemes-security-pro' ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-password-expiration-expire_force"><?php _e( 'Force Password Change on Next Login', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'expire_force' ); ?>
				<label for="itsec-password-expiration-expire_force"><?php _e( 'Force password change', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'Checking this box will force all users to change their password upon their next login.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-password-expiration-expire_max"><?php _e( 'Maximum Password Age', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_text( 'expire_max', array( 'class' => 'small-text code' ) ); ?>
				<label for="itsec-password-expiration-expire_max"><?php _e( 'Days', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'The maximum number of days a password may be kept before it is expired.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
	</table>
<?php

	}
}

new ITSEC_Password_Expiration_Settings_Page();
