<?php

final class ITSEC_Privilege_Escalation_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'privilege';
		$this->title = __( 'Privilege Escalation', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Allow administrators to temporarily grant extra access to a user of the site for a specified period of time.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;
		$this->can_save = false;

		parent::__construct();
	}

	protected function render_description( $form ) {

?>
	<p><?php _e( 'Enabling this feature will allow administrators to temporarily grant extra access to a user of the site for a specified period of time. For example, a contractor can be granted developer access to the site for 24 hours after which his or her status would be automatically revoked.', 'it-l10n-ithemes-security-pro' ); ?></p>
<?php

	}

	protected function render_settings( $form ) {

?>
	<p><?php printf( __( 'In order to escalate a user\'s privileges to that of a higher role, go to the <a href="%s">Users</a> page and edit the user you wish to provide with escalated privileges. A section titled "Temporary Privilege Escalation" near the bottom of the settings will allow you to select which role you would like to escalate that user to and for how many days you would like them to have those escalated privileges.', 'it-l10n-ithemes-security-pro' ), admin_url( 'users.php' ) ); ?></p>
<?php

	}
}

new ITSEC_Privilege_Escalation_Settings_Page();
