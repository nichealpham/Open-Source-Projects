<?php

final class ITSEC_File_Change_Settings_Page extends ITSEC_Module_Settings_Page {
	private $script_version = 1;


	public function __construct() {
		$this->id = 'file-change';
		$this->title = __( 'File Change Detection', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Monitor the site for unexpected file changes.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';

		parent::__construct();
	}

	public function enqueue_scripts_and_styles() {
		$settings = ITSEC_Modules::get_settings( $this->id );

		$logs_page_url = ITSEC_Core::get_logs_page_url( 'file_change' );

		$vars = array(
			'button_text'          => isset( $settings['split'] ) && true === $settings['split'] ? __( 'Scan Next File Chunk', 'it-l10n-ithemes-security-pro' ) : __( 'Scan Files Now', 'it-l10n-ithemes-security-pro' ),
			'scanning_button_text' => __( 'Scanning...', 'it-l10n-ithemes-security-pro' ),
			'no_changes'           => __( 'No changes were detected.', 'it-l10n-ithemes-security-pro' ),
			'found_changes'        => sprintf( __( 'Changes were detected. Please check the <a href="%s" target="_blank" rel="noopener noreferrer">logs page</a> for details.', 'it-l10n-ithemes-security-pro' ), esc_url( $logs_page_url ) ),
			'unknown_error'        => __( 'An unknown error occured. Please try again later', 'it-l10n-ithemes-security-pro' ),
			'already_running'      => sprintf( __( 'A scan is already in progress. Please check the <a href="%s" target="_blank" rel="noopener noreferrer">logs page</a> at a later time for the results of the scan.', 'it-l10n-ithemes-security-pro' ), esc_url( $logs_page_url ) ),
			'ABSPATH'              => ITSEC_Lib::get_home_path(),
			'nonce'                => wp_create_nonce( 'itsec_do_file_check' ),
		);

		wp_enqueue_script( 'itsec-file-change-settings-script', plugins_url( 'js/settings-page.js', __FILE__ ), array( 'jquery' ), $this->script_version, true );
		wp_localize_script( 'itsec-file-change-settings-script', 'itsec_file_change_settings', $vars );


		$vars = array(
			'nonce' => wp_create_nonce( 'itsec_jquery_filetree' ),
		);

		wp_enqueue_script( 'itsec-file-change-admin-filetree-script', plugins_url( 'js/filetree/jqueryFileTree.js', __FILE__ ), array( 'jquery' ), $this->script_version, true );
		wp_localize_script( 'itsec-file-change-admin-filetree-script', 'itsec_jquery_filetree', $vars );


		wp_enqueue_style( 'itsec-file-change-admin-filetree-style', plugins_url( 'js/filetree/jqueryFileTree.css', __FILE__ ), array(), $this->script_version );
		wp_enqueue_style( 'itsec-file-change-admin-style', plugins_url( 'css/settings.css', __FILE__ ), array(), $this->script_version );
	}

	public function handle_ajax_request( $data ) {
		if ( 'one-time-scan' === $data['method'] ) {
			require_once( dirname( __FILE__ ) . '/scanner.php' );

			ITSEC_Response::set_response( ITSEC_File_Change_Scanner::run_scan( false ) );
		} else if ( 'get-filetree-data' === $data['method'] ) {
			ITSEC_Response::set_response( $this->get_filetree_data( $data ) );
		}
	}

	protected function render_description( $form ) {

?>
	<p><?php _e( 'Even the best security solutions can fail. How do you know if someone gets into your site? You will know because they will change something. File Change detection will tell you what files have changed in your WordPress installation alerting you to changes not made by yourself. Unlike other solutions, this plugin will look only at your installation and compare files to the last check instead of comparing them with a remote installation thereby taking into account whether or not you modify the files yourself.', 'it-l10n-ithemes-security-pro' ); ?></p>
<?php

	}

	protected function render_settings( $form ) {
		$methods = array(
			'exclude' => __( 'Exclude Selected', 'it-l10n-ithemes-security-pro' ),
			'include' => __( 'Include Selected', 'it-l10n-ithemes-security-pro' ),
		);


		$file_list = $form->get_option( 'file_list' );

		if ( is_array( $file_list ) ) {
			$file_list = implode( "\n", $file_list );
		} else {
			$file_list = '';
		}

		$form->set_option( 'file_list', $file_list );

		$split = $form->get_option( 'split' );
		$one_time_button_label = ( true === $split ) ? __( 'Scan Next File Chunk', 'it-l10n-ithemes-security-pro' ) : __( 'Scan Files Now', 'it-l10n-ithemes-security-pro' )

?>
	<div class="hide-if-no-js">
		<p><?php _e( "Press the button below to scan your site's files for changes. Note that if changes are found this will take you to the logs page for details.", 'it-l10n-ithemes-security-pro' ); ?></p>
		<p><?php $form->add_button( 'one_time_check', array( 'value' => $one_time_button_label, 'class' => 'button-primary' ) ); ?></p>
		<div id="itsec_file_change_status"></div>
	</div>

	<table class="form-table itsec-settings-section">
		<tr>
			<th scope="row"><label for="itsec-file-change-split"><?php _e( 'Split File Scanning', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'split' ); ?>
				<label for="itsec-file-change-split"><?php _e( 'Split file checking into chunks.', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'Splits file checking into 7 chunks (plugins, themes, wp-admin, wp-includes, uploads, the rest of wp-content and everything that is left over) and divides the checks evenly over the course of a day. This feature may result in more notifications but will allow for the scanning of bigger sites to continue even on a lower-end web host.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-file-change-method"><?php _e( 'Include/Exclude Files and Folders', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_select( 'method', $methods ); ?>
				<label for="itsec-file-change-method"><?php _e( 'Include/Exclude Files', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'Select whether we should exclude files and folders selected or whether the scan should only include files and folders selected.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Files and Folders List', 'it-l10n-ithemes-security-pro' ); ?></th>
			<td>
				<p class="description"><?php _e( 'Exclude files or folders by clicking the red minus next to the file or folder name.', 'it-l10n-ithemes-security-pro' ); ?></p>
				<div class="file_list">
					<div class="file_chooser"><div class="jquery_file_tree"></div></div>
					<div class="list_field"><?php $form->add_textarea( 'file_list', array( 'wrap' => 'off' ) ); ?></div>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-file-change-types"><?php _e( 'Ignore File Types', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_textarea( 'types', array( 'wrap' => 'off', 'cols' => 20, 'rows' => 10 ) ); ?>
				<br />
				<label for="itsec-file-change-types"><?php _e( 'File types listed here will not be checked for changes. While it is possible to change files such as images it is quite rare and nearly all known WordPress attacks exploit php, js and other text files.', 'it-l10n-ithemes-security-pro' ); ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-file-change-email"><?php _e( 'Email File Change Notifications', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'email' ); ?>
				<label for="itsec-file-change-email"><?php _e( 'Email file change notifications', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'Notifications will be sent to all emails set to receive notifications on the global settings page.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-file-change-notify_admin"><?php _e( 'Display File Change Admin Warning', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'notify_admin' ); ?>
				<label for="itsec-file-change-notify_admin"><?php _e( 'Display file change admin warning', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'Disabling this feature will prevent the file change warning from displaying to the site administrator in the WordPress Dashboard. Note that disabling both the error message and the email notification will result in no notifications of file changes. The only way you will be able to tell is by manually checking the log files.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<?php do_action( 'itsec-file-change-settings-form', $form ); ?>
	</table>
<?php

	}

	/**
	 * Gets file list for tree.
	 *
	 * Processes the ajax request for retreiving the list of files and folders that can later either
	 * excluded or included.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function get_filetree_data( $data ) {

		$directory = sanitize_text_field( $data['dir'] );
		$directory = urldecode( $directory );
		$directory = realpath( $directory );

		$base_directory = realpath( ITSEC_Lib::get_home_path() );

		// Ensure that requests cannot traverse arbitrary directories.
		if ( 0 !== strpos( $directory, $base_directory ) ) {
			$directory = $base_directory;
		}

		$directory .= '/';

		ob_start();

		if ( file_exists( $directory ) ) {

			$files = scandir( $directory );

			natcasesort( $files );

			if ( 2 < count( $files ) ) { /* The 2 accounts for . and .. */

				echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";

				//two loops keep directories sorted before files

				// All files and directories (alphabetical sorting)
				foreach ( $files as $file ) {

					if ( '.' != $file && '..' != $file && file_exists( $directory . $file ) && is_dir( $directory . $file ) ) {

						echo '<li class="directory collapsed"><a href="#" rel="' . htmlentities( $directory . $file ) . '/">' . htmlentities( $file ) . '<div class="itsec_treeselect_control"><img src="' . plugins_url( 'images/redminus.png', __FILE__ ) . '" style="vertical-align: -3px;" title="Add to exclusions..." class="itsec_filetree_exclude"></div></a></li>';

					} elseif ( '.' != $file && '..' != $file && file_exists( $directory . $file ) && ! is_dir( $directory . $file ) ) {

						$ext = preg_replace( '/^.*\./', '', $file );
						echo '<li class="file ext_' . $ext . '"><a href="#" rel="' . htmlentities( $directory . $file ) . '">' . htmlentities( $file ) . '<div class="itsec_treeselect_control"><img src="' . plugins_url( 'images/redminus.png', __FILE__ ) . '" style="vertical-align: -3px;" title="Add to exclusions..." class="itsec_filetree_exclude"></div></a></li>';

					}

				}

				echo "</ul>";

			}

		}

		return ob_get_clean();

	}

}

new ITSEC_File_Change_Settings_Page();
