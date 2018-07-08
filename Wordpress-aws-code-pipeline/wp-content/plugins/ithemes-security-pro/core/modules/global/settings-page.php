<?php

final class ITSEC_Global_Settings_Page extends ITSEC_Module_Settings_Page {
	private $version = 1;


	public function __construct() {
		$this->id = 'global';
		$this->title = __( 'Global Settings', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Configure basic settings that control how iThemes Security functions.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';

		parent::__construct();

		add_filter( 'admin_body_class', array( $this, 'filter_body_classes' ) );
	}

	public function filter_body_classes( $classes ) {
		if ( ITSEC_Modules::get_setting( 'global', 'show_error_codes' ) ) {
			$classes .= ' itsec-show-error-codes';
		}

		if ( ITSEC_Modules::get_setting( 'global', 'write_files' ) ) {
			$classes .= ' itsec-write-files-enabled';
		} else {
			$classes .= ' itsec-write-files-disabled';
		}

		$classes = trim( $classes );

		return $classes;
	}

	public function enqueue_scripts_and_styles() {
		$vars = array(
			'ip'           => ITSEC_Lib::get_ip(),
			'log_location' => ITSEC_Modules::get_default( $this->id, 'log_location' ),
		);

		wp_enqueue_script( 'itsec-global-settings-page-script', plugins_url( 'js/settings-page.js', __FILE__ ), array( 'jquery' ), $this->version, true );
		wp_localize_script( 'itsec-global-settings-page-script', 'itsec_global_settings_page', $vars );
	}

	public function handle_form_post( $data ) {
		$retval = ITSEC_Modules::set_settings( $this->id, $data );

		if ( $retval['saved'] ) {
			if ( $retval['old_settings']['show_error_codes'] !== $retval['new_settings']['show_error_codes'] ) {
				ITSEC_Response::add_js_function_call( 'itsec_change_show_error_codes', array( (bool) $retval['new_settings']['show_error_codes'] ) );
			}

			if ( $retval['old_settings']['write_files'] !== $retval['new_settings']['write_files'] ) {
				ITSEC_Response::add_js_function_call( 'itsec_change_write_files', array( (bool) $retval['new_settings']['write_files'] ) );
			}
		}
	}

	protected function render_description( $form ) {

?>
	<p><?php _e( 'The following settings modify the behavior of many of the features offered by iThemes Security.', 'it-l10n-ithemes-security-pro' ); ?></p>
<?php

	}

	protected function render_settings( $form ) {
		$validator = ITSEC_Modules::get_validator( $this->id );

		$log_types = $validator->get_valid_log_types();

		$show_error_codes_options = array(
			false => __( 'No (default)' ),
			true  => __( 'Yes' ),
		);

?>
	<table class="form-table itsec-settings-section">
		<tr>
			<th scope="row"><label for="itsec-global-write_files"><?php _e( 'Write to Files', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'write_files' ); ?>
				<label for="itsec-global-write_files"><?php _e( 'Allow iThemes Security to write to wp-config.php and .htaccess.', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'Whether or not iThemes Security should be allowed to write to wp-config.php and .htaccess automatically. If disabled you will need to manually place configuration options in those files.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-notification_email"><?php _e( 'Notification Email', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_textarea( 'notification_email', array( 'class' => 'textarea-small' ) ); ?>
				<p class="description"><?php _e( 'The email address(es) all security notifications will be sent to. One address per line.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-digest_email"><?php _e( 'Send Digest Email', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'digest_email' ); ?>
				<label for="itsec-global-digest_email"><?php _e( 'Send digest email', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'During periods of heavy attack or other times a security plugin can generate a LOT of email just telling you that it is doing its job. Turning this on will reduce the emails from this plugin to no more than one per day for any notification.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-backup_email"><?php _e( 'Backup Delivery Email', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_textarea( 'backup_email', array( 'class' => 'textarea-small' ) ); ?>
				<br />
				<p class="description"><?php _e( 'The email address(es) all database backups will be sent to. One address per line.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-lockout_message"><?php _e( 'Host Lockout Message', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_textarea( 'lockout_message', array( 'class' => 'widefat' ) ); ?>
				<p class="description"><?php _e( 'The message to display when a computer (host) has been locked out.', 'it-l10n-ithemes-security-pro' ); ?></p>
				<p class="description"><?php _e( 'You can use HTML in your message. Allowed tags include: a, br, em, strong, h1, h2, h3, h4, h5, h6, div.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-user_lockout_message"><?php _e( 'User Lockout Message', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_textarea( 'user_lockout_message', array( 'class' => 'widefat' ) ); ?>
				<p class="description"><?php _e( 'The message to display to a user when their account has been locked out.', 'it-l10n-ithemes-security-pro' ); ?></p>
				<p class="description"><?php _e( 'You can use HTML in your message. Allowed tags include: a, br, em, strong, h1, h2, h3, h4, h5, h6, div.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-community_lockout_message"><?php _e( 'Community Lockout Message', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_textarea( 'community_lockout_message', array( 'class' => 'widefat' ) ); ?>
				<p class="description"><?php _e( 'The message to display to a user when their IP has been flagged as bad by the iThemes network.', 'it-l10n-ithemes-security-pro' ); ?></p>
				<p class="description"><?php _e( 'You can use HTML in your message. Allowed tags include: a, br, em, strong, h1, h2, h3, h4, h5, h6, div.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-blacklist"><?php _e( 'Blacklist Repeat Offender', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'blacklist' ); ?>
				<label for="itsec-global-blacklist"><?php _e( 'Enable Blacklist Repeat Offender', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'If this box is checked the IP address of the offending computer will be added to the "Ban Users" blacklist after reaching the number of lockouts listed below.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-blacklist_count"><?php _e( 'Blacklist Threshold', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_text( 'blacklist_count', array( 'class' => 'small-text' ) ); ?>
				<label for="itsec-global-blacklist_count"><?php _e( 'Lockouts', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'The number of lockouts per IP before the host is banned permanently from this site.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-blacklist_period"><?php _e( 'Blacklist Lookback Period', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_text( 'blacklist_period', array( 'class' => 'small-text' ) ); ?>
				<label for="itsec-global-blacklist_period"><?php _e( 'Days', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'How many days should a lockout be remembered to meet the blacklist count above.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-lockout_period"><?php _e( 'Lockout Period', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_text( 'lockout_period', array( 'class' => 'small-text' ) ); ?>
				<label for="itsec-global-lockout_period"><?php _e( 'Minutes', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'The length of time a host or user will be banned from this site after hitting the limit of bad logins. The default setting of 15 minutes is recommended as increasing it could prevent attacking IP addresses from being added to the blacklist.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-lockout_white_list"><?php _e( 'Lockout White List', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_textarea( 'lockout_white_list' ); ?>
				<p><?php $form->add_button( 'add-to-whitelist', array( 'value' => __( 'Add my current IP to the White List', 'it-l10n-ithemes-security-pro' ), 'class' => 'button-primary' ) ); ?></p>
				<p class="description"><?php _e( 'Use the guidelines below to enter hosts that will not be locked out from your site. This will keep you from locking yourself out of any features if you should trigger a lockout. Please note this does not override away mode and will only prevent a temporary ban. Should a permanent ban be triggered you will still be added to the "Ban Users" list unless the IP address is also white listed in that section.', 'it-l10n-ithemes-security-pro' ); ?></p>
				<ul>
					<li>
						<?php _e( 'You may white list users by individual IP address or IP address range using wildcards or CIDR notation.', 'it-l10n-ithemes-security-pro' ); ?>
						<ul>
							<li><?php _e( 'Individual IP addresses must be in IPv4 or IPv6 standard format (###.###.###.### or ####:####:####:####:####:####:####:####).', 'it-l10n-ithemes-security-pro' ); ?></li>
							<li><?php _e( 'CIDR notation is allowed to specify a range of IP addresses (###.###.###.###/## or ####:####:####:####:####:####:####:####/###).', 'it-l10n-ithemes-security-pro' ); ?></li>
							<li><?php _e( 'Wildcards are also supported with some limitations. If using wildcards (*), you must start with the right-most chunk in the IP address. For example ###.###.###.* and ###.###.*.* are permitted but ###.###.*.### is not. Wildcards are only for convenient entering of IP addresses, and will be automatically converted to their appropriate CIDR notation format on save.', 'it-l10n-ithemes-security-pro' ); ?></li>
						</ul>
					</li>
					<li><?php _e( 'Enter only 1 IP address or 1 IP address range per line.', 'it-l10n-ithemes-security-pro' ); ?></li>
				</ul>
				<p><a href="<?php echo esc_url( ITSEC_Lib::get_trace_ip_link() ); ?>" target="_blank" rel="noopener noreferrer"><?php _e( 'Lookup IP Address.', 'it-l10n-ithemes-security-pro' ); ?></a></p>
				<p class="description"><strong><?php _e( 'This white list will prevent any IP listed from triggering an automatic lockout. You can still block the IP address manually in the banned users settings.', 'it-l10n-ithemes-security-pro' ); ?></strong></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-email_notifications"><?php _e( 'Email Lockout Notifications', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'email_notifications' ); ?>
				<label for="itsec-global-email_notifications"><?php _e( 'Enable Email Lockout Notifications', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'This feature will trigger an email to be sent to the email addresses listed in the Notification Email setting whenever a host or user is locked out of the system.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-log_type"><?php _e( 'Log Type', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_select( 'log_type', $log_types ); ?>
				<label for="itsec-global-log_type"><?php _e( 'How should event logs be kept', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'iThemes Security can log events in multiple ways, each with advantages and disadvantages. Database Only puts all events in the database with your posts and other WordPress data. This makes it easy to retrieve and process but can be slower if the database table gets very large. File Only is very fast but the plugin does not process the logs itself as that would take far more resources. For most users or smaller sites Database Only should be fine. If you have a very large site or a log processing software then File Only might be a better option.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-log_rotation"><?php _e( 'Days to Keep Database Logs', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_text( 'log_rotation', array( 'class' => 'small-text' ) ); ?>
				<label for="itsec-global-log_rotation"><?php _e( 'Days', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'The number of days database logs should be kept. File logs will be kept indefinitely but will be rotated once the file hits 10MB.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-log_location"><?php _e( 'Path to Log Files', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_text( 'log_location', array( 'class' => 'large-text code' ) ); ?>
				<p><label for="itsec-global-log_location"><?php _e( 'The path on your server where log files should be stored.', 'it-l10n-ithemes-security-pro' ); ?></label></p>
				<p class="description"><?php _e( 'This path must be writable by your website. For added security, it is recommended you do not include it in your website root folder.', 'it-l10n-ithemes-security-pro' ); ?></p>
				<p><?php $form->add_button( 'reset-log-location', array( 'value' => __( 'Restore Default Log File Path', 'it-l10n-ithemes-security-pro' ), 'class' => 'button-secondary' ) ); ?></p>
			</td>
		</tr>
		<?php if ( is_dir( WP_PLUGIN_DIR . '/iwp-client' ) ) : ?>
			<tr>
				<th scope="row"><label for="itsec-global-infinitewp_compatibility"><?php _e( 'Add InfiniteWP Compatibility', 'it-l10n-ithemes-security-pro' ); ?></label></th>
				<td>
					<?php $form->add_checkbox( 'infinitewp_compatibility' ); ?>
					<label for="itsec-global-infinitewp_compatibility"><?php _e( 'Enable InfiniteWP Compatibility', 'it-l10n-ithemes-security-pro' ); ?></label>
					<p class="description"><?php printf( __( 'Turning this feature on will enable compatibility with <a href="%s" target="_blank" rel="noopener noreferrer">InfiniteWP</a>. Do not turn it on unless you use the InfiniteWP service.', 'it-l10n-ithemes-security-pro' ), 'http://infinitewp.com' ); ?></p>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th scope="row"><label for="itsec-global-allow_tracking"><?php _e( 'Allow Data Tracking', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'allow_tracking' ); ?>
				<label for="itsec-global-allow_tracking"><?php _e( 'Allow iThemes to track plugin usage via anonymous data.', 'it-l10n-ithemes-security-pro' ); ?></label>
			</td>
		</tr>
		<?php if ( 'nginx' === ITSEC_Lib::get_server() ) : ?>
			<tr>
				<th scope="row"><label for="itsec-global-nginx_file"><?php _e( 'NGINX Conf File', 'it-l10n-ithemes-security-pro' ); ?></label></th>
				<td>
					<?php $form->add_text( 'nginx_file', array( 'class' => 'large-text code' ) ); ?>
					<p><label for="itsec-global-nginx_file"><?php _e( 'The path on your server where the nginx config file is located.', 'it-l10n-ithemes-security-pro' ); ?></label></p>
					<p class="description"><?php _e( 'This path must be writable by your website. For added security, it is recommended you do not include it in your website root folder.', 'it-l10n-ithemes-security-pro' ); ?></p>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th scope="row"><label for="itsec-global-lock_file"><?php _e( 'Disable File Locking', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'lock_file' ); ?>
				<label for="itsec-global-lock_file"><?php _e( 'Disable File Locking', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'iThemes Security uses file locking to prevent operations from being executed twice. We do not recommend disabling file locking unless your host prevents it from working correctly.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-proxy_override"><?php _e( 'Override Proxy Detection', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'proxy_override' ); ?>
				<label for="itsec-global-proxy_override"><?php _e( 'Disable Proxy IP Detection', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'If you\'re not using a proxy service such as Varnish, Cloudflare or others turning this on may result in more accurate IP detection.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-hide_admin_bar"><?php _e( 'Hide Security Menu in Admin Bar', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'hide_admin_bar' ); ?>
				<label for="itsec-global-hide_admin_bar"><?php _e( 'Hide security menu in admin bar.', 'it-l10n-ithemes-security-pro' ); ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-global-show_error_codes"><?php _e( 'Show Error Codes', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_select( 'show_error_codes', $show_error_codes_options ); ?>
				<p class="description"><?php _e( 'Each error message in iThemes Security has an associated error code that can help diagnose an issue. Changing this setting to "Yes" causes these codes to display. This setting should be left set to "No" unless iThemes Security support requests that you change it.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
	</table>
<?php

	}
}

new ITSEC_Global_Settings_Page();
