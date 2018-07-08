<?php

final class ITSEC_Strong_Passwords_Settings_Page extends ITSEC_Module_Settings_Page {
	private $script_version = 1;


	public function __construct() {
		$this->id = 'strong-passwords';
		$this->title = __( 'Strong Password Enforcement', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Force users to use strong passwords as rated by the WordPress password meter.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';

		parent::__construct();
	}

	protected function render_description( $form ) {

?>
	<p><?php _e( 'Force users to use strong passwords as rated by the WordPress password meter.', 'it-l10n-ithemes-security-pro' ); ?></p>
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

?>
	<table class="form-table itsec-settings-section">
		<tr>
			<th scope="row"><label for="itsec-strong-passwords-role"><?php _e( 'Select Role for Strong Passwords', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_select( 'role', $roles ); ?>
				<br />
				<label for="itsec-strong-passwords-role"><?php _e( 'Minimum role at which a user must choose a strong password.', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php printf( __( 'For more information on WordPress roles and capabilities please see <a href="%1$s" target="_blank" rel="noopener noreferrer">%1$s</a>.', 'it-l10n-ithemes-security-pro' ), 'http://codex.wordpress.org/Roles_and_Capabilities' ); ?></p>
				<p class="warningtext description"><?php _e( 'Warning: If your site invites public registrations setting the role too low may annoy your members.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
	</table>
<?php

	}
}

new ITSEC_Strong_Passwords_Settings_Page();
