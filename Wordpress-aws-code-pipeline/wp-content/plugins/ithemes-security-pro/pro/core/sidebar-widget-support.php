<?php

class ITSEC_Settings_Page_Sidebar_Widget_Support extends ITSEC_Settings_Page_Sidebar_Widget {
	public function __construct() {
		$this->id = 'support';
		$this->title = __( 'Need Help Securing Your Site?', 'it-l10n-ithemes-security-pro' );
		$this->priority = 11;

		parent::__construct();
	}

	public function render( $form ) {
		echo '<p>' . __( 'As an iThemes Security Pro customer, you can create a support ticket now. Our team of experts is ready to help.', 'it-l10n-ithemes-security-pro' ) . '</p>';
		echo '<p><a class="button-secondary" href="http://ithemes.com/member/support.php" target="_blank" rel="noopener noreferrer">' . __( 'Create a Support Ticket', 'it-l10n-ithemes-security-pro' ) . '</a></p>';
	}

}
new ITSEC_Settings_Page_Sidebar_Widget_Support();
