<?php

final class ITSEC_Away_Mode_Settings_Page extends ITSEC_Module_Settings_Page {
	private $version = 1;
	
	
	public function __construct() {
		$this->id = 'away-mode';
		$this->title = __( 'Away Mode', 'it-l10n-ithemes-security-pro' );
		$this->description = __( 'Disable access to the WordPress Dashboard on a schedule.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		
		parent::__construct();
	}
	
	public function enqueue_scripts_and_styles() {
		wp_enqueue_script( 'itsec-away-mode-settings-page-script', plugins_url( 'js/settings-page.js', __FILE__ ), array( 'jquery-ui-datepicker' ), $this->version, true );
		
		wp_enqueue_style( 'itsec-jquery-ui', plugins_url( 'css/jquery-ui.min.css', __FILE__ ), array(), '1.11.4' );
		wp_enqueue_style( 'itsec-jquery-ui-datepicker', plugins_url( 'css/jquery.datepicker.css', __FILE__ ), array( 'itsec-jquery-ui' ), '2014.03.27' );
	}
	
	protected function render_description( $form ) {
		
?>
	<p><?php _e( 'As most sites are only updated at certain times of the day it is not always necessary to provide access to the WordPress dashboard 24 hours a day, 7 days a week. The options below will allow you to disable access to the WordPress Dashboard for the specified period. In addition to limiting exposure to attackers this could also be useful to disable site access based on a schedule for classroom or other reasons.', 'it-l10n-ithemes-security-pro' ); ?></p>
<?php
		
	}
	
	private function set_datetime_options( $form, $prefix, $has_meridiems ) {
		$timestamp = $form->get_option( $prefix );
		$timestamp += ITSEC_Core::get_time_offset();
		
		$form->set_option( "{$prefix}_date", date( 'Y-m-d', $timestamp ) );
		
		if ( $has_meridiems ) {
			$form->set_option( "{$prefix}_hour", intval( date( 'g', $timestamp ) ) );
			$form->set_option( "{$prefix}_meridiem", date( 'a', $timestamp ) );
		} else {
			$form->set_option( "{$prefix}_hour", intval( date( 'G', $timestamp ) ) );
		}
		
		$form->set_option( "{$prefix}_minute", intval( date( 'i', $timestamp ) ) );
	}
	
	protected function render_settings( $form ) {
		global $wp_locale;
		
		
		$settings = $form->get_options();
		$validator = ITSEC_Modules::get_validator( $this->id );
		
		
		$types = $validator->get_valid_types();
		
		
		if ( 1 === $settings['start'] ) {
			$tomorrow = date( 'Y-m-d', current_time( 'timestamp' ) + DAY_IN_SECONDS );
			$new_start = strtotime( "$tomorrow 1:00 am" ) - ITSEC_Core::get_time_offset();
			
			$form->set_option( 'start', $new_start );
		}
		
		if ( 1 === $settings['end'] ) {
			$tomorrow = date( 'Y-m-d', current_time( 'timestamp' ) + DAY_IN_SECONDS );
			$new_end = strtotime( "$tomorrow 6:00 am" ) - ITSEC_Core::get_time_offset();
			
			$form->set_option( 'end', $new_end );
		}
		
		
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );
		
		if ( false !== strpos( $time_format, 'G' ) ) {
			for ( $hour = 0; $hour < 24; $hour++ ) {
				$hours[$hour] = $hour;
			}
		} else if ( false !== strpos( $time_format, 'H' ) ) {
			for ( $hour = 0; $hour < 24; $hour++ ) {
				$hours[$hour] = sprintf( '%02d', $hour );
			}
		} else {
			for ( $hour = 1; $hour <= 12; $hour++ ) {
				$hours[$hour] = $hour;
			}
			
			if ( false !== strpos( $time_format, 'A' ) ) {
				$am = $wp_locale->get_meridiem( 'AM' );
				$pm = $wp_locale->get_meridiem( 'PM' );
			} else {
				$am = $wp_locale->get_meridiem( 'am' );
				$pm = $wp_locale->get_meridiem( 'pm' );
			}
			
			$meridiems = array(
				'am' => $am,
				'pm' => $pm,
			);
		}
		
		for ( $minute = 0; $minute <= 59; $minute++ ) {
			$minutes[$minute] = sprintf( '%02d', $minute );
		}
		
		
		$this->set_datetime_options( $form, 'start', isset( $meridiems ) );
		$this->set_datetime_options( $form, 'end', isset( $meridiems ) );
		
		
		/* translators: 1: date, 2: time */
		$datetime_format = _x( '%1$s \a\t %2$s', 'Date and time format', 'it-l10n-ithemes-security-pro' );
		$datetime_format = sprintf( $datetime_format, $date_format, $time_format );
		
		$current_datetime = date_i18n( $datetime_format );
		
?>
	<p><?php printf( __( 'Please note that according to your <a href="%s">WordPress Timezone settings</a> your current time is:', 'it-l10n-ithemes-security-pro' ), admin_url( 'options-general.php#timezone_string' ) ); ?></p>
	<p class="current-date-time"><?php echo $current_datetime; ?></p>
	<p><?php printf( __( 'If this is incorrect, please update it on the <a href="%s">WordPress General Settings page</a> by selecting the appropriate time zone. Failure to set the correct timezone may result in unintended lockouts.', 'it-l10n-ithemes-security-pro' ), admin_url( 'options-general.php#timezone_string' ) ); ?></p>
	<table class="form-table itsec-settings-section">
		<tr>
			<th scope="row"><label for="itsec-away-mode-type"><?php _e( 'Type of Restriction', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_select( 'type', $types ); ?>
				<br />
				<p class="description"><?php _e( 'Select the type of restriction you would like to enable.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-away-mode-start_date"><?php _e( 'Start Date', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_text( 'start_date' ); ?>
				<br />
				<p class="description"><?php _e( 'Date when the admin dashboard should become unavailable.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-away-mode-start_hour"><?php _e( 'Start Time', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_select( 'start_hour', $hours ); ?>
				<?php $form->add_select( 'start_minute', $minutes ); ?>
				<?php if ( isset( $meridiems ) ) : ?>
					<?php $form->add_select( 'start_meridiem', $meridiems ); ?>
				<?php endif; ?>
				<br />
				<p class="description"><?php _e( 'Time when the admin dashboard should become unavailable.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-away-mode-end_date"><?php _e( 'End Date', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_text( 'end_date' ); ?>
				<br />
				<p class="description"><?php _e( 'Date when the admin dashboard should become available again.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-away-mode-end_hour"><?php _e( 'End Time', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_select( 'end_hour', $hours ); ?>
				<?php $form->add_select( 'end_minute', $minutes ); ?>
				<?php if ( isset( $meridiems ) ) : ?>
					<?php $form->add_select( 'end_meridiem', $meridiems ); ?>
				<?php endif; ?>
				<p class="description"><?php _e( 'Time when the admin dashboard should become available again.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
	</table>
<?php
		
	}
}

new ITSEC_Away_Mode_Settings_Page();
