<?php

final class ITSEC_Online_Files_Custom_Settings {
	public static function render_settings( $form ) {
		$form->set_option( 'compare_file_hashes', ITSEC_Modules::get_setting( 'online-files', 'compare_file_hashes' ) );
		
?>
	<tr>
		<th scope="row"><label for="itsec-file-change-"><?php _e( 'Compare Files Online', 'it-l10n-ithemes-security-pro' ); ?></label></th>
		<td>
			<?php $form->add_checkbox( 'compare_file_hashes' ); ?>
			<label for="itsec-file-change-compare_file_hashes"><?php _e( 'Enable online file comparison', 'it-l10n-ithemes-security-pro' ); ?></label>
			<p class="description"><?php _e( 'When any WordPress core file or file in an iThemes plugin or theme has been changed on your system, this feature will compare it with the version on WordPress.org or iThemes (as appropriate) to determine if the change was malicious. Currently this feature only works with WordPress core files and plugins and themes by iThemes (plugins and themes from other sources will be added as available).', 'it-l10n-ithemes-security-pro' ); ?></p>
		</td>
	</tr>
<?php
		
	}
	
	public static function sanitize_settings( $settings ) {
		if ( isset( $settings['compare_file_hashes'] ) ) {
			$result = ITSEC_Modules::set_setting( 'online-files', 'compare_file_hashes', (bool) $settings['compare_file_hashes'] );
			
			unset( $settings['compare_file_hashes'] );
		}
		
		return $settings;
	}
}
