<?php

final class ITSEC_Backup_Settings_Page extends ITSEC_Module_Settings_Page {
	private $script_version = 1;


	public function __construct() {
		$this->id = 'backup';
		$this->title = __( 'Database Backups', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Create backups of your site\'s database. The backups can be created manually and on a schedule.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';

		parent::__construct();
	}

	public function enqueue_scripts_and_styles() {
		wp_enqueue_script( 'jquery-multi-select', plugins_url( 'js/jquery.multi-select.js', __FILE__ ), array( 'jquery' ), $this->script_version, true );

		$vars = array(
			'default_backup_location' => ITSEC_Modules::get_default( $this->id, 'location' ),
			'available_tables_label'  => __( 'Tables for Backup', 'it-l10n-ithemes-security-pro' ),
			'excluded_tables_label'   => __( 'Excluded Tables', 'it-l10n-ithemes-security-pro' ),
			'creating_backup_text'    => __( 'Creating Backup...', 'it-l10n-ithemes-security-pro' ),
		);

		wp_enqueue_script( 'itsec-backup-settings-page-script', plugins_url( 'js/settings-page.js', __FILE__ ), array( 'jquery', 'jquery-multi-select' ), $this->script_version, true );
		wp_localize_script( 'itsec-backup-settings-page-script', 'itsec_backup', $vars );

		wp_enqueue_style( 'itsec-backup-settings-page-style', plugins_url( 'css/settings-page.css', __FILE__ ), array(), $this->script_version );
	}

	public function handle_ajax_request( $data ) {
		global $itsec_backup;

		if ( ! isset( $itsec_backup ) ) {
			require_once( 'class-itsec-backup.php' );
			$itsec_backup = new ITSEC_Backup();
			$itsec_backup->run();
		}

		$result = $itsec_backup->do_backup( true );
		$message = '';

		if ( is_wp_error( $result ) ) {
			$errors = ITSEC_Response::get_error_strings( $result );

			foreach ( $errors as $error ) {
				$message .= '<div class="error inline"><p><strong>' . $error . '</strong></p></div>';
			}
		} else if ( is_string( $result ) ) {
			$message = '<div class="updated fade inline"><p><strong>' . $result . '</strong></p></div>';
		} else {
			$message = '<div class="error inline"><p><strong>' . sprintf( __( 'The backup request returned an unexpected response. It returned a response of type <code>%1$s</code>.', 'it-l10n-ithemes-security-pro' ), gettype( $result ) ) . '</strong></p></div>';
		}

		ITSEC_Response::set_response( $message );
	}

	protected function render_description( $form ) {

?>
	<p><?php _e( 'One of the best ways to protect yourself from an attack is to have access to a database backup of your site. If something goes wrong, you can get your site back by restoring the database from a backup and replacing the files with fresh ones. Use the button below to create a backup of your database for this purpose. You can also schedule automated backups and download or delete previous backups.', 'it-l10n-ithemes-security-pro' ); ?></p>
<?php

	}

	protected function render_settings( $form ) {
		$settings = $form->get_options();


		$methods = array(
			0 => __( 'Save Locally and Email', 'it-l10n-ithemes-security-pro' ),
			1 => __( 'Email Only', 'it-l10n-ithemes-security-pro' ),
			2 => __( 'Save Locally Only', 'it-l10n-ithemes-security-pro' ),
		);

		$excludes = $this->get_excludable_tables( $settings );

?>
	<div class="hide-if-no-js">
		<p><?php _e( 'Press the button below to create a database backup using the saved settings.', 'it-l10n-ithemes-security-pro' ); ?></p>
		<p><?php $form->add_button( 'create_backup', array( 'value' => __( 'Create a Database Backup', 'it-l10n-ithemes-security-pro' ), 'class' => 'button-primary' ) ); ?></p>
		<div id="itsec_backup_status"></div>
	</div>

	<table class="form-table itsec-settings-section">
		<tr>
			<th scope="row"><label for="itsec-backup-all_sites"><?php _e( 'Backup Full Database', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'all_sites' ); ?>
				<label for="itsec-backup-all_sites"><?php _e( 'Checking this box will have the backup script backup all tables in your database, even if they are not part of this WordPress site.', 'it-l10n-ithemes-security-pro' ); ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-backup-method"><?php _e( 'Backup Method', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_select( 'method', $methods ); ?>
				<br />
				<label for="itsec-backup-method"><?php _e( 'Backup Save Method', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'Select what we should do with your backup file. You can have it emailed to you, saved locally or both.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr class="itsec-backup-method-file-content">
			<th scope="row"><label for="itsec-backup-location"><?php _e( 'Backup Location', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_text( 'location', array( 'class' => 'large-text' ) ); ?>
				<label for="itsec-backup-location"><?php _e( 'The path on your machine where backup files should be stored.', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'This path must be writable by your website. For added security, it is recommended you do not include it in your website root folder.', 'it-l10n-ithemes-security-pro' ); ?></p>
				<div class="hide-if-no-js">
					<?php $form->add_button( 'reset_backup_location', array( 'value' => __( 'Restore Default Location', 'it-l10n-ithemes-security-pro' ), 'class' => 'button-secondary' ) ); ?>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-backup-retain"><?php _e( 'Backups to Retain', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_text( 'retain', array( 'class' => 'small-text' ) ); ?>
				<label for="itsec-backup-retain"><?php _e( 'Backups', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'Limit the number of backups stored locally (on this server). Any older backups beyond this number will be removed. Setting to "0" will retain all backups.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-backup-zip"><?php _e( 'Compress Backup Files', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'zip' ); ?>
				<label for="itsec-backup-zip"><?php _e( 'Zip Database Backups', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'You may need to turn this off if you are having problems with backups.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-backup-exclude"><?php _e( 'Exclude Tables', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<label for="itsec-backup-exclude"><?php _e( 'Tables with data that does not need to be backed up', 'it-l10n-ithemes-security-pro' ); ?></label>
				<?php $form->add_multi_select( 'exclude', $excludes ); ?>
			<p class="description"><?php _e( 'Some plugins can create log files in your database. While these logs might be handy for some functions, they can also take up a lot of space and, in some cases, even make backing up your database almost impossible. Select log tables above to exclude their data from the backup. Note: The table itself will be backed up, but not the data in the table.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-backup-enabled"><?php _e( 'Schedule Database Backups', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'enabled', array( 'class' => 'itsec-settings-toggle' ) ); ?>
				<label for="itsec-backup-enabled"><?php _e( 'Enable Scheduled Database Backups', 'it-l10n-ithemes-security-pro' ); ?></label>
			</td>
		</tr>
		<?php if ( ! defined( 'ITSEC_BACKUP_CRON' ) || ! ITSEC_BACKUP_CRON ) : ?>
			<tr class="itsec-backup-enabled-content">
				<th scope="row"><label for="itsec-backup-interval"><?php _e( 'Backup Interval', 'it-l10n-ithemes-security-pro' ); ?></label></th>
				<td>
					<?php $form->add_text( 'interval', array( 'class' => 'small-text' ) ); ?>
					<label for="itsec-backup-interval"><?php _e( 'Days', 'it-l10n-ithemes-security-pro' ); ?></label>
					<p class="description"><?php _e( 'The number of days between database backups.', 'it-l10n-ithemes-security-pro' ); ?></p>
				</td>
			</tr>
		<?php endif; ?>
	</table>
<?php

	}

	private function get_excludable_tables( $settings ) {
		global $wpdb;

		$ignored_tables = array(
			'commentmeta',
			'comments',
			'links',
			'options',
			'postmeta',
			'posts',
			'term_relationships',
			'term_taxonomy',
			'terms',
			'usermeta',
			'users',
		);

		if ( $settings['all_sites'] ) {
			$query = 'SHOW TABLES';
		} else {
			$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', "{$wpdb->base_prefix}%" );
		}

		$tables = $wpdb->get_results( $query, ARRAY_N );
		$excludes = array();

		foreach ( $tables as $table ) {
			$short_table = substr( $table[0], strlen( $wpdb->prefix ) );

			if ( in_array( $short_table, $ignored_tables ) ) {
				continue;
			}

			$excludes[$short_table] = $table[0];
		}

		return $excludes;
	}
}

new ITSEC_Backup_Settings_Page();
