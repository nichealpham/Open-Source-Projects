<?php

class ITSEC_Settings_Page_Sidebar_Widget_BackupBuddy_Cross_Promo extends ITSEC_Settings_Page_Sidebar_Widget {
	public function __construct() {
		$this->id = 'backupbuddy-cross-promo';
		$this->title = __( 'Complete Your Security Strategy With BackupBuddy', 'it-l10n-ithemes-security-pro' );
		$this->priority = 7;

		parent::__construct();
	}

	public function render( $form ) {
		echo '<p style="text-align: center;"><img src="' . plugins_url( 'img/backupbuddy-logo.png', __FILE__ ) . '" alt="BackupBuddy"></p>';
		echo '<p>' . __( 'BackupBuddy is the complete backup, restore and migration solution for your WordPress site. Schedule automated backups, store your backups safely off-site and restore your site quickly & easily.', 'it-l10n-ithemes-security-pro' ) . '</p>';
		echo sprintf( '<p style="font-weight: bold; font-size: 1em;">%s<span style="display: block; text-align: center; font-size: 1.2em; background: #ebebeb; padding: .5em;">%s</span></p>', __( '25% off BackupBuddy with coupon code', 'it-l10n-ithemes-security-pro' ), __( 'BACKUPPROTECT', 'it-l10n-ithemes-security-pro' ) );
		echo '<a href="http://ithemes.com/better-backups" class="button-secondary" target="_blank" rel="noopener noreferrer">' . __( 'Get BackupBuddy', 'it-l10n-ithemes-security-pro' ) . '</a>';
	}

}
new ITSEC_Settings_Page_Sidebar_Widget_BackupBuddy_Cross_Promo();
