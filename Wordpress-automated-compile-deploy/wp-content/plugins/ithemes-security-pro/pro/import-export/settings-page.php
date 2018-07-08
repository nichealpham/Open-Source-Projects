<?php

final class ITSEC_Import_Export_Settings_Page extends ITSEC_Module_Settings_Page {
	private $version = 1;


	public function __construct() {
		$this->id = 'import-export';
		$this->title = __( 'Settings Import and Export', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Export your settings as a backup or to import on other sites for quicker setup.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;
		$this->can_save = false;

		parent::__construct();
	}

	public function enqueue_scripts_and_styles() {
		$vars = array(
			'text' => array(
				'exporting' => __( 'Creating Export...', 'it-l10n-ithemes-security-pro' ),
				'importing' => __( 'Loading Settings...', 'it-l10n-ithemes-security-pro' ),
			),
		);

		wp_enqueue_script( 'jquery-fileupload', plugins_url( 'js/jquery.fileupload.js', __FILE__ ), array( 'jquery-ui-widget' ), '9.12.3', true );

		wp_enqueue_script( 'itsec-import-export-page-script', plugins_url( 'js/settings-page.js', __FILE__ ), array( 'jquery-fileupload' ), $this->version, true );
		wp_localize_script( 'itsec-import-export-page-script', 'itsecImportExportSettingsPage', $vars );
	}

	public function handle_ajax_request( $data ) {
		if ( 'export' === $data['method'] ) {
			if ( empty( $data['email'] ) ) {
				return new WP_Error( 'itsec-import-export-export-empty-email', __( 'You must supply an Email Address to receive the exported settings file.', 'it-l10n-ithemes-security-pro' ) );
			}

			$email = sanitize_text_field( $data['email'] );

			if ( ! is_email( $email ) ) {
				return new WP_Error( 'itsec-import-export-export-invalid-email', __( 'You must supply a valid Email Address to receive the exported settings file.', 'it-l10n-ithemes-security-pro' ) );
			}


			require_once( dirname( __FILE__ ) . '/exporter.php' );
			$result = ITSEC_Import_Export_Exporter::create( $email );

			if ( is_wp_error( $result ) ) {
				ITSEC_Response::set_response( $result );
			} else {
				$message = sprintf( __( 'The exported settings were successfully sent to <code>%1$s</code>.', 'it-l10n-ithemes-security-pro' ), $email );
				$message = '<div class="updated fade inline"><p><strong>' . $message . '</strong></p></div>';

				ITSEC_Response::set_response( $message );
			}
		} else if ( 'import' === $data['method'] ) {
			if ( empty( $_FILES['import_file'] ) ) {
				return new WP_Error( 'itsec-import-export-import-empty-file', __( 'You must supply a Settings File to import.', 'it-l10n-ithemes-security-pro' ) );
			}

			require_once( dirname( __FILE__ ) . '/importer.php' );
			$result = ITSEC_Import_Export_Importer::import_from_form( 'import_file' );

			if ( is_wp_error( $result ) ) {
				ITSEC_Response::set_response( $result );
			} else {
				$message = __( 'The submitted settings were successfully loaded onto the site.', 'it-l10n-ithemes-security-pro' );
				$message = '<div class="updated fade inline"><p><strong>' . $message . '</strong></p></div>';

				ITSEC_Response::set_response( $message );
			}
		}
	}

	protected function render_description( $form ) {

?>
	<p><?php _e( 'Have more than one site? Want to just backup your settings for later?', 'it-l10n-ithemes-security-pro' ); ?></p>
<?php

	}

	protected function render_settings( $form ) {

?>
	<hr />

	<h4><?php _e( 'Export', 'it-l10n-ithemes-security-pro' ); ?></h4>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="itsec-import-export-email_address"><?php _e( 'Email Address', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_text( 'email_address' ); ?>
				<?php /* translators: 1: Directory where the settings export will be saved. */ ?>
				<p class="description"><?php _e( 'Enter the email address to send the exported settings to.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
	</table>
	<p class="submit"><?php $form->add_button( 'export', array( 'value' => __( 'Create Export', 'it-l10n-ithemes-security-pro' ), 'class' => 'button-primary' ) ); ?></p>
	<div class="itsec-import-export-export-results-wrapper"></div>

	<hr />

	<h4><?php _e( 'Import', 'it-l10n-ithemes-security-pro' ); ?></h4>
	<p><?php _e( 'Note: If you are importing settings from a different site or from a different, you will need to update any path settings such as logs or backup files after the import.', 'it-l10n-ithemes-security-pro' ); ?></p>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="itsec-import-export-settings_file"><?php _e( 'Settings File', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<div id="itsec-import-export-import-select-file">
					<?php $form->add_file( 'settings_file' ); ?>
					<p class="description"><?php _e( 'Select a settings file to import.', 'it-l10n-ithemes-security-pro' ); ?></p>
				</div>
				<a id="itsec-import-export-import-removed-selected-file" class="button-secondary" href="#"><?php _e( 'Select a different file', 'it-l10n-ithemes-security-pro' ); ?></a>
			</td>
		</tr>
	</table>
	<p class="submit"><?php $form->add_button( 'import', array( 'value' => __( 'Load Settings', 'it-l10n-ithemes-security-pro' ), 'class' => 'button-primary' ) ); ?></p>
	<div class="itsec-import-export-import-results-wrapper"></div>
<?php

	}
}

new ITSEC_Import_Export_Settings_Page();
