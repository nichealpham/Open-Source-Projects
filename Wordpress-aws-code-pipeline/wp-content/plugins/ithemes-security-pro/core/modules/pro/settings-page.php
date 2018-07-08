<?php
/**
 * All pro modules are here as upsell modules
 */


final class ITSEC_Malware_Scheduling_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'malware-scheduling';
		$this->title = __( 'Malware Scan Scheduling', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Protect your site with automated malware scans. When this feature is enabled, the site will be automatically scanned each day. If a problem is found, an email is sent to select users.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;
		$this->upsell = true;
		$this->upsell_url = 'http://ithemes.com/security/wordpress-malware-scan/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta';

		parent::__construct();
	}
}
new ITSEC_Malware_Scheduling_Settings_Page();


final class ITSEC_Privilege_Escalation_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'privilege';
		$this->title = __( 'Privilege Escalation', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Allow administrators to temporarily grant extra access to a user of the site for a specified period of time.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;
		$this->upsell = true;
		$this->upsell_url = 'https://ithemes.com/security/wordpress-privilege-escalation/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta';

		parent::__construct();
	}
}
new ITSEC_Privilege_Escalation_Settings_Page();


final class ITSEC_Password_Expiration_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'password-expiration';
		$this->title = __( 'Password Expiration', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Strengthen the passwords on the site with automated password expiration.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;
		$this->upsell = true;
		$this->upsell_url = 'https://ithemes.com/security/wordpress-password-security/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta';

		parent::__construct();
	}
}
new ITSEC_Password_Expiration_Settings_Page();


final class ITSEC_Recaptcha_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'recaptcha';
		$this->title = __( 'reCAPTCHA', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Protect your site from bots by verifying that the person submitting comments or logging in is indeed human.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;
		$this->upsell = true;
		$this->upsell_url = 'https://ithemes.com/security/wordpress-recaptcha/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta';

		parent::__construct();
	}
}
new ITSEC_Recaptcha_Settings_Page();


final class ITSEC_Import_Export_Settings_Page extends ITSEC_Module_Settings_Page {
	private $version = 1;


	public function __construct() {
		$this->id = 'import-export';
		$this->title = __( 'Settings Import and Export', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Export your settings as a backup or to import on other sites for quicker setup.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;
		$this->upsell = true;
		$this->upsell_url = 'https://ithemes.com/security/import-export-settings/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta';

		parent::__construct();
	}
}
new ITSEC_Import_Export_Settings_Page();


final class ITSEC_Two_Factor_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'two-factor';
		$this->title = __( 'Two-Factor Authentication', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Two-Factor Authentication greatly increases the security of your WordPress user account by requiring additional information beyond your username and password in order to log in.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;
		$this->upsell = true;
		$this->upsell_url = 'https://ithemes.com/security/wordpress-two-factor-authentication/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta';

		parent::__construct();
	}
}
new ITSEC_Two_Factor_Settings_Page();


final class ITSEC_User_Logging_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'user-logging';
		$this->title = __( 'User Logging', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Log user actions such as login, saving content and others.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;
		$this->upsell = true;
		$this->upsell_url = 'https://ithemes.com/security/wordpress-user-log/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta';

		parent::__construct();
	}
}
new ITSEC_User_Logging_Settings_Page();


final class ITSEC_User_Security_Check_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'user-security-check';
		$this->title = __( 'User Security Check', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Every user on your site affects overall security. See how your users might be affecting your security and take action when needed.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;
		$this->upsell = true;
		$this->upsell_url = 'https://ithemes.com/security/wordpress-user-security-check/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta';

		parent::__construct();
	}
}
new ITSEC_User_Security_Check_Settings_Page();


final class ITSEC_Version_Management_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'version-management';
		$this->title = __( 'Version Management', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Protect your site when outdated software is not updated quickly enough.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;
		$this->upsell = true;
		$this->upsell_url = 'https://ithemes.com/wordpress-version-management-ithemes-security-pro/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta';

		parent::__construct();
	}
}
new ITSEC_Version_Management_Settings_Page();
