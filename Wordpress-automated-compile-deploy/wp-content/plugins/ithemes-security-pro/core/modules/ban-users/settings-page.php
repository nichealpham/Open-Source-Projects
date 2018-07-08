<?php

final class ITSEC_Ban_Users_Settings_Page extends ITSEC_Module_Settings_Page {
	private $script_version = 1;


	public function __construct() {
		$this->id = 'ban-users';
		$this->title = __( 'Banned Users', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Block specific IP addresses and user agents from accessing the site.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';

		parent::__construct();
	}

	protected function render_description( $form ) {

?>
	<p><?php _e( 'This feature allows you to completely ban hosts and user agents from your site without having to manage any configuration of your server. Any IP addresses or user agents found in the lists below will not be allowed any access to your site.', 'it-l10n-ithemes-security-pro' ); ?></p>
<?php

	}

	protected function render_settings( $form ) {

?>
	<table class="form-table itsec-settings-section">
		<tr>
			<th scope="row"><label for="itsec-ban-users-default"><?php _e( 'Default Blacklist', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'default' ); ?>
				<label for="itsec-ban-users-default"><?php _e( 'Enable HackRepair.com\'s blacklist feature', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php esc_html_e( 'As a getting-started point you can include the blacklist developed by Jim Walker.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-ban-users-enable_ban_lists"><?php _e( 'Ban Lists', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'enable_ban_lists', array( 'class' => 'itsec-settings-toggle' ) ); ?>
				<label for="itsec-ban-users-enable_ban_lists"><?php _e( 'Enable Ban Lists', 'it-l10n-ithemes-security-pro' ); ?></label>
			</td>
		</tr>
		<tr class="itsec-ban-users-enable_ban_lists-content">
			<th scope="row"><label for="itsec-ban-users-host_list"><?php _e( 'Ban Hosts', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_textarea( 'host_list', array( 'wrap' => 'off' ) ); ?>
				<p><?php _e( 'Use the guidelines below to enter hosts that will not be allowed access to your site.', 'it-l10n-ithemes-security-pro' ); ?></p>
				<ul>
					<li>
						<?php _e( 'You may ban users by individual IP address or IP address range using wildcards or CIDR notation.', 'it-l10n-ithemes-security-pro' ); ?>
						<ul>
							<li><?php _e( 'Individual IP addresses must be in IPv4 or IPv6 standard format (###.###.###.### or ####:####:####:####:####:####:####:####).', 'it-l10n-ithemes-security-pro' ); ?></li>
							<li><?php _e( 'CIDR notation is allowed to specify a range of IP addresses (###.###.###.###/## or ####:####:####:####:####:####:####:####/###).', 'it-l10n-ithemes-security-pro' ); ?></li>
							<li><?php _e( 'Wildcards are also supported with some limitations. If using wildcards (*), you must start with the right-most chunk in the IP address. For example ###.###.###.* and ###.###.*.* are permitted but ###.###.*.### is not. Wildcards are only for convenient entering of IP addresses, and will be automatically converted to their appropriate CIDR notation format on save.', 'it-l10n-ithemes-security-pro' ); ?></li>
						</ul>
					</li>
					<li><?php _e( 'Enter only 1 IP address or 1 IP address range per line.', 'it-l10n-ithemes-security-pro' ); ?></li>
					<li><?php _e( 'Note: You cannot ban yourself.', 'it-l10n-ithemes-security-pro' ); ?></li>
				</ul>
				<p><a href="<?php echo esc_url( ITSEC_Lib::get_trace_ip_link() ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Lookup IP Address.', 'it-l10n-ithemes-security-pro' ); ?></a></p>
			</td>
		</tr>
		<tr class="itsec-ban-users-enable_ban_lists-content">
			<th scope="row"><label for="itsec-ban-users-agent_list"><?php _e( 'Ban User Agents', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_textarea( 'agent_list', array( 'wrap' => 'off' ) ); ?>
				<p><?php _e( 'Use the guidelines below to enter user agents that will not be allowed access to your site.', 'it-l10n-ithemes-security-pro' ); ?></p>
				<ul>
					<li><?php _e( 'Enter only 1 user agent per line.', 'it-l10n-ithemes-security-pro' ); ?></li>
				</ul>
			</td>
		</tr>
	</table>
<?php

	}
}

new ITSEC_Ban_Users_Settings_Page();
