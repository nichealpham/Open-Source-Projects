<?php

final class ITSEC_Database_Prefix_Settings_Page extends ITSEC_Module_Settings_Page {
	private $version = 1;
	
	
	public function __construct() {
		$this->id = 'database-prefix';
		$this->title = __( 'Change Database Table Prefix', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Change the database table prefix that WordPress uses.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'advanced';
		
		parent::__construct();
	}
	
	protected function render_description( $form ) {
		
?>
	<p><?php _e( 'By default, WordPress assigns the prefix <code>wp_</code> to all tables in the database where your content, users, and objects exist. For potential attackers, this means it is easier to write scripts that can target WordPress databases as all the important table names for 95% of sites are already known. Changing the <code>wp_</code> prefix makes it more difficult for tools that are trying to take advantage of vulnerabilities in other places to affect the database of your site. <strong>Before using this tool, we strongly recommend creating a backup of your database.</strong>', 'it-l10n-ithemes-security-pro' ); ?></p>
	<p><?php _e( 'Note: The use of this tool requires quite a bit of system memory which may be more than some hosts can handle. If you back your database up you can\'t do any permanent damage but without a proper backup you risk breaking your site and having to perform a rather difficult fix.', 'it-l10n-ithemes-security-pro' ); ?></p>
	<div class="itsec-warning-message"><?php printf( __( '<span>WARNING: </span><a href="%1$s">Backup your database</a> before using this tool.', 'it-l10n-ithemes-security-pro' ), ITSEC_Core::get_backup_creation_page_url() ); ?></div>
<?php
		
	}
	
	protected function render_settings( $form ) {
		global $wpdb;
		
		$yes_or_no = array(
			'yes' => __( 'Yes', 'it-l10n-ithemes-security-pro' ),
			'no'  => __( 'No', 'it-l10n-ithemes-security-pro' ),
		);
		
		$form->set_option( 'change_prefix', 'no' );
		
?>
	<div class="itsec-write-files-disabled">
		<div class="itsec-warning-message"><?php _e( 'The "Write to Files" setting is disabled in Global Settings. In order to use this feature, you must enable the "Write to Files" setting.', 'it-l10n-ithemes-security-pro' ); ?></div>
	</div>
	
	<div class="itsec-write-files-enabled">
		<?php if ( 'wp_' === $wpdb->base_prefix ) : ?>
			<p><strong><?php _e( 'Your database is using the default table prefix <code>wp_</code>. You should change this.', 'it-l10n-ithemes-security-pro' ); ?></strong></p>
		<?php else : ?>
			<?php /* translators: 1: WordPress database table prefix */ ?>
			<p><?php printf( __( 'Your current database table prefix is <code>%1$s</code>.', 'it-l10n-ithemes-security-pro' ), esc_html( $wpdb->base_prefix ) ); ?></p>
		<?php endif; ?>
		
		<table class="form-table itsec-settings-section">
			<tr>
				<th scope="row"><label for="itsec-database-prefix-change_prefix"><?php _e( 'Change Prefix', 'it-l10n-ithemes-security-pro' ); ?></label></th>
				<td>
					<?php $form->add_select( 'change_prefix', $yes_or_no ); ?>
					<br />
					<p class="description"><?php _e( 'Select "Yes" and save the settings to change the database table prefix.', 'it-l10n-ithemes-security-pro' ); ?></p>
				</td>
			</tr>
		</table>
	</div>
<?php
		
	}
	
	public function handle_form_post( $data ) {
		require_once( dirname( __FILE__ ) . '/utility.php' );
		
		if ( isset( $data['change_prefix'] ) && 'yes' === $data['change_prefix'] ) {
			$result = ITSEC_Database_Prefix_Utility::change_database_prefix();
			
			ITSEC_Response::add_errors( $result['errors'] );
			ITSEC_Response::reload_module( $this->id );
			
			if ( false === $result['new_prefix'] ) {
				ITSEC_Response::set_success( false );
			} else {
				/* translators: 1: New database table prefix */
				ITSEC_Response::add_message( sprintf( __( 'The database table prefix was successfully changed to <code>%1$s</code>.', 'it-l10n-ithemes-security-pro' ), $result['new_prefix'] ) );
			}
		}
	}
}

new ITSEC_Database_Prefix_Settings_Page();
