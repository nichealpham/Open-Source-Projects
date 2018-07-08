<?php

final class ITSEC_File_Permissions_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'file-permissions';
		$this->title = __( 'File Permissions', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Lists file and directory permissions of key areas of the site.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->information_only = true;
		$this->can_save = false;

		parent::__construct();
	}

	protected function render_description( $form ) {}

	protected function render_settings( $form ) {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			echo '<p>' . __( 'Click the button to load the current file permissions.', 'it-l10n-ithemes-security-pro' ) . '</p>';
			echo '<p>' . $form->add_button( 'load_file_permissions', array( 'value' => __( 'Load File Permissions Details', 'it-l10n-ithemes-security-pro' ), 'class' => 'button-primary itsec-reload-module' ) ) . '</p>';

			return;
		}


		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-config-file.php' );

		$wp_upload_dir = ITSEC_Core::get_wp_upload_dir();

		$path_data = array(
			array(
				ABSPATH,
				0755,
			),
			array(
				ABSPATH . WPINC,
				0755,
			),
			array(
				ABSPATH . 'wp-admin',
				0755,
			),
			array(
				ABSPATH . 'wp-admin/js',
				0755,
			),
			array(
				WP_CONTENT_DIR,
				0755,
			),
			array(
				get_theme_root(),
				0755,
			),
			array(
				WP_PLUGIN_DIR,
				0755
			),
			array(
				$wp_upload_dir['basedir'],
				0755,
			),
			array(
				ITSEC_Lib_Config_File::get_wp_config_file_path(),
				0444,
			),
			array(
				ITSEC_Lib_Config_File::get_server_config_file_path(),
				0444,
			),
		);


		$rows = array();

		foreach ( $path_data as $path ) {
			$row = array();

			list( $path, $suggested_permissions ) = $path;

			$display_path = preg_replace( '/^' . preg_quote( ABSPATH, '/' ) . '/', '', $path );
			$display_path = ltrim( $display_path, '/' );

			if ( empty( $display_path ) ) {
				$display_path = '/';
			}

			$row[] = $display_path;
			$row[] = sprintf( '%o', $suggested_permissions );

			$permissions = fileperms( $path ) & 0777;
			$row[] = sprintf( '%o', $permissions );

			if ( ! $permissions || $permissions != $suggested_permissions ) {
				$row[] = __( 'WARNING', 'it-l10n-ithemes-security-pro' );
				$row[] = '<div style="background-color: #FEFF7F; border: 1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;</div>';
			} else {
				$row[] = __( 'OK', 'it-l10n-ithemes-security-pro' );
				$row[] = '<div style="background-color: #22EE5B; border: 1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;</div>';
			}

			$rows[] = $row;
		}


		$class = 'entry-row';

?>
	<p><?php $form->add_button( 'reload_file_permissions', array( 'value' => __( 'Reload File Permissions Details', 'it-l10n-ithemes-security-pro' ), 'class' => 'button-primary itsec-reload-module' ) ); ?></p>
	<table class="widefat">
		<thead>
			<tr>
				<th><?php _e( 'Relative Path', 'it-l10n-ithemes-security-pro' ); ?></th>
				<th><?php _e( 'Suggestion', 'it-l10n-ithemes-security-pro' ); ?></th>
				<th><?php _e( 'Value', 'it-l10n-ithemes-security-pro' ); ?></th>
				<th><?php _e( 'Result', 'it-l10n-ithemes-security-pro' ); ?></th>
				<th><?php _e( 'Status', 'it-l10n-ithemes-security-pro' ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><?php _e( 'Relative Path', 'it-l10n-ithemes-security-pro' ); ?></th>
				<th><?php _e( 'Suggestion', 'it-l10n-ithemes-security-pro' ); ?></th>
				<th><?php _e( 'Value', 'it-l10n-ithemes-security-pro' ); ?></th>
				<th><?php _e( 'Result', 'it-l10n-ithemes-security-pro' ); ?></th>
				<th><?php _e( 'Status', 'it-l10n-ithemes-security-pro' ); ?></th>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ( $rows as $row ) : ?>
				<tr class="<?php echo $class; ?>">
					<?php foreach ( $row as $column ) : ?>
						<td><?php echo $column; ?></td>
					<?php endforeach; ?>
				</tr>
				<?php $class = ( 'entry-row' === $class ) ? 'entry-row alternate' : 'entry-row'; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
	<br />
<?php

	}
}
new ITSEC_File_Permissions_Settings_Page();
