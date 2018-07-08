<?php

class ITSEC_Settings_Page_Sidebar_Widget_Pro_Upsell extends ITSEC_Settings_Page_Sidebar_Widget {
	public function __construct() {
		$this->id = 'pro-upsell';
		$this->title = __( 'Get iThemes Security Pro', 'it-l10n-ithemes-security-pro' );
		$this->priority = 5;

		parent::__construct();
	}

	public function render( $form ) {
		echo '<p>' . sprintf( __( 'Add an extra layer of protection to your WordPress site with <a href="%s">iThemes Security Pro</a>, including:', 'it-l10n-ithemes-security-pro' ), 'https://ithemes.com/security/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta' ) . '</p>';
		echo '<ul>';
		echo '<li>' . __( 'Two-factor authentication', 'it-l10n-ithemes-security-pro' ) . '</li>';
		echo '<li>' . __( 'Scheduled malware scanning', 'it-l10n-ithemes-security-pro' ) . '</li>';
		echo '<li>' . __( 'Google reCAPTCHA integration', 'it-l10n-ithemes-security-pro' ) . '</li>';
		echo '<li>' . __( 'Private, ticketed support', 'it-l10n-ithemes-security-pro' ) . '</li>';
		echo '<li>' . __( '+ more Pro-only features', 'it-l10n-ithemes-security-pro' ) . '</li>';
		echo '</ul>';
		echo '<a href="https://ithemes.com/security/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta" class="button-primary" target="_blank" rel="noopener noreferrer">' . __( 'Get iThemes Security Pro', 'it-l10n-ithemes-security-pro' ) . '</a>';
	}

}
new ITSEC_Settings_Page_Sidebar_Widget_Pro_Upsell();
