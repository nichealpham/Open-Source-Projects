<?php

final class ITSEC_User_Logging_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'user-logging';
		$this->title = __( 'User Logging', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Log user actions such as login, saving content and others.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;
		
		parent::__construct();
	}
	
	protected function render_description( $form ) {
		
?>
	<p><?php _e( 'Log user actions such as login, saving content and others.', 'it-l10n-ithemes-security-pro' ); ?></p>
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
	<table class="form-table" id="user_logging-enabled">
		<tr>
			<th scope="row"><label for="itsec-user-logging-role"><?php _e( 'Select Role for User Logging', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_select( 'role', $roles ); ?>
				<br />
				<label for="itsec-user-logging-role"><?php _e( 'Minimum role at which user actions are logged.', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php printf( __( 'For more information on WordPress roles and capabilities please see the WordPress Codex page on <a href="%s">Roles and Capabilities</a>.', 'it-l10n-ithemes-security-pro' ), 'http://codex.wordpress.org/Roles_and_Capabilities' ); ?></p>
				<p class="itsec-warning-message"><?php _e( 'Warning: If your site invites public registrations setting the role too low may result in some very large logs.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
	</table>
<?php
		
	}
}

new ITSEC_User_Logging_Settings_Page();
