<?php

final class ITSEC_404_Detection_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = '404-detection';
		$this->title = __( '404 Detection', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Automatically block users snooping around for pages to exploit.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		
		parent::__construct();
	}
	
	protected function render_description( $form ) {
		
?>
	<p><?php _e( '404 detection looks at a user who is hitting a large number of non-existent pages and getting a large number of 404 errors. 404 detection assumes that a user who hits a lot of 404 errors in a short period of time is scanning for something (presumably a vulnerability) and locks them out accordingly. This also gives the added benefit of helping you find hidden problems causing 404 errors on unseen parts of your site. All errors will be logged in the "View Logs" page. You can set thresholds for this feature below.', 'it-l10n-ithemes-security-pro' ); ?></p>
<?php
		
	}
	
	protected function render_settings( $form ) {
		
?>
	<?php echo $GLOBALS['itsec_lockout']->get_lockout_description(); ?>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="itsec-404-detection-check_period"><?php _e( 'Minutes to Remember 404 Error (Check Period)', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_text( 'check_period', array( 'class' => 'small-text' ) ); ?>
				<label for="itsec-404-detection-check_period"><?php _e( 'Minutes', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'The number of minutes in which 404 errors should be remembered and counted towards lockouts.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-404-detection-error_threshold"><?php _e( 'Error Threshold', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_text( 'error_threshold', array( 'class' => 'small-text' ) ); ?>
				<label for="itsec-404-detection-error_threshold"><?php _e( 'Errors', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php _e( 'The numbers of errors (within the check period time frame) that will trigger a lockout. Set to zero (0) to record 404 errors without locking out users. This can be useful for troubleshooting content or other errors. The default is 20.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-404-detection-white_list"><?php _e( '404 File/Folder White List', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_textarea( 'white_list', array( 'wrap' => 'off' ) ); ?>
				<p class="description"><?php _e( 'Use the white list above to prevent recording common 404 errors. If you know a common file on your site is missing and you do not want it to count towards a lockout record it here. You must list the full path beginning with the "/".', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-404-detection-types"><?php _e( 'Ignored File Types', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_textarea( 'types', array( 'wrap' => 'off' ) ); ?>
				<p class="description"><?php _e( 'File types listed here will be recorded as 404 errors but will not lead to lockouts.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
	</table>
<?php
		
	}
}

new ITSEC_404_Detection_Settings_Page();
