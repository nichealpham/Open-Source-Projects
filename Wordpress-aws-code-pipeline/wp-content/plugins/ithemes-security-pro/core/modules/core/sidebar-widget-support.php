<?php

class ITSEC_Settings_Page_Sidebar_Widget_Support extends ITSEC_Settings_Page_Sidebar_Widget {
	public function __construct() {
		$this->id = 'support';
		$this->title = __( 'Need Help Securing Your Site?', 'it-l10n-ithemes-security-pro' );
		$this->priority = 11;

		parent::__construct();
	}

	public function render( $form ) {
		echo '<p>' . __( 'Since you are using the free version of iThemes Security from WordPress.org, you can get free support from the WordPress community.', 'it-l10n-ithemes-security-pro' ) . '</p>';
		echo '<p><a class="button-secondary" href="http://wordpress.org/support/plugin/better-wp-security" target="_blank" rel="noopener noreferrer">' . __( 'Get Free Support', 'it-l10n-ithemes-security-pro' ) . '</a></p>';
		echo '<p>' . __( 'Get added peace of mind with professional support from our expert team and pro features with iThemes Security Pro.', 'it-l10n-ithemes-security-pro' ) . '</p>';
		echo '<p><a class="button-secondary" href="https://ithemes.com/security/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta" target="_blank" rel="noopener noreferrer">' . __( 'Get iThemes Security Pro', 'it-l10n-ithemes-security-pro' ) . '</a></p>';
	}

}
new ITSEC_Settings_Page_Sidebar_Widget_Support();
