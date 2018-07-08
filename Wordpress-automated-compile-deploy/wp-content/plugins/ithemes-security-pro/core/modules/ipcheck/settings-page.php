<?php

final class ITSEC_Network_Brute_Force_Settings_Page extends ITSEC_Module_Settings_Page {
	protected $script_version = 2;


	public function __construct() {
		$this->id = 'network-brute-force';
		$this->title = __( 'Network Brute Force Protection', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Join a network of sites that reports and protects against bad actors on the internet.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';

		parent::__construct();
	}

	public function enqueue_scripts_and_styles() {
		$settings = ITSEC_Modules::get_settings( $this->id );

		$vars = array(
			'resetting_button_text' => __( 'Resetting...', 'it-l10n-ithemes-security-pro' ),
		);

		wp_enqueue_script( 'itsec-network-brute-force-settings-page-script', plugins_url( 'js/settings-page.js', __FILE__ ), array( 'jquery' ), $this->script_version, true );
		wp_localize_script( 'itsec-network-brute-force-settings-page-script', 'itsec_network_brute_force', $vars );
	}

	public function handle_ajax_request( $data ) {
		if ( 'reset-api-key' === $data['method'] ) {
			$defaults = ITSEC_Modules::get_defaults( $this->id );
			$results = ITSEC_Modules::set_settings( $this->id, $defaults );

			ITSEC_Response::set_response( $results['saved'] );
			ITSEC_Response::add_errors( $results['errors'] );
			ITSEC_Response::add_messages( $results['messages'] );

			if ( $results['saved'] ) {
				ITSEC_Response::reload_module( $this->id );
			} else if ( empty( $results['errors'] ) ) {
				ITSEC_Response::add_error( new WP_Error( 'itsec-network-brute-force-settings-page-handle-ajax-request-bad-response', __( 'An unknown error prevented the API key from being reset properly. An unrecognized response was received. Please wait a few minutes and try again.', 'it-l10n-ithemes-security-pro' ) ) );
			}
		}
	}

	protected function render_description( $form ) {

?>
	<p><?php _e( 'If one had unlimited time and wanted to try an unlimited number of password combinations to get into your site they eventually would, right? This method of attack, known as a brute force attack, is something that WordPress is acutely susceptible to as, by default, the system doesn\'t care how many attempts a user makes to login. It will always let you try again. Enabling login limits will ban the host user from attempting to login again after the specified bad login threshold has been reached.', 'it-l10n-ithemes-security-pro' ); ?></p>
<?php

	}

	protected function render_settings( $form ) {
		$settings = $form->get_options();

?>
	<p>
		<strong><?php _e( 'Network vs Local Brute Force Protection', 'it-l10n-ithemes-security-pro' ); ?></strong>
		<br />
		<?php _e( 'Local brute force protection looks only at attempts to access your site and bans users per the lockout rules specified locally. Network brute force protection takes this a step further by banning users who have tried to break into other sites from breaking into yours. The network protection will automatically report the IP addresses of failed login attempts to iThemes and will block them for a length of time necessary to protect your site based on the number of other sites that have seen a similar attack.', 'it-l10n-ithemes-security-pro' ); ?>
	</p>
	<?php if ( empty( $settings['api_key'] ) || empty( $settings['api_secret'] ) ) : ?>
		<br />
		<p><?php _e( 'To get started with iThemes Network Brute Force Protection, please supply your email address and save the settings. This will provide this site with an API key and starts the site protection.', 'it-l10n-ithemes-security-pro' ); ?></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="itsec-network-brute-force-email"><?php _e( 'Email Address', 'it-l10n-ithemes-security-pro' ); ?></label></th>
				<td>
					<?php $form->add_text( 'email', array( 'class' => 'regular-text', 'value' => get_option( 'admin_email' ) ) ); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="itsec-network-brute-force-updates_optin"><?php _e( 'Receive Email Updates', 'it-l10n-ithemes-security-pro' ); ?></label></th>
				<td>
					<?php $form->add_checkbox( 'updates_optin' ); ?>
					<label for="itsec-network-brute-force-updates_optin"><?php _e( 'Receive email updates about WordPress Security from iThemes.', 'it-l10n-ithemes-security-pro' ); ?></label>
				</td>
			</tr>
		</table>
	<?php else : ?>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="itsec-network-brute-force-api_key"><?php _e( 'API Key', 'it-l10n-ithemes-security-pro' ); ?></label></th>
				<td>
					<?php $form->add_text( 'api_key', array( 'class' => 'regular-text code', 'readonly' => 'readonly' ) ); ?>
					<?php $form->add_button( 'reset_api_key', array( 'value' => __( 'Reset API Key', 'it-l10n-ithemes-security-pro' ), 'class' => 'button-primary' ) ); ?>
					<div id="itsec-network-brute-force-reset-status"></div>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="itsec-network-brute-force-enable_ban"><?php _e( 'Ban Reported IPs', 'it-l10n-ithemes-security-pro' ); ?></label></th>
				<td>
					<?php $form->add_checkbox( 'enable_ban' ); ?>
					<label for="itsec-network-brute-force-enable_ban"><?php _e( 'Automatically ban IPs reported as a problem by the network.', 'it-l10n-ithemes-security-pro' ); ?></label>
				</td>
			</tr>
		</table>
	<?php endif; ?>
<?php

	}
}

new ITSEC_Network_Brute_Force_Settings_Page();
