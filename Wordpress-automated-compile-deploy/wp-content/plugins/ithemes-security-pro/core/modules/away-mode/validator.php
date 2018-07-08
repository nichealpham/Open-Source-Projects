<?php

class ITSEC_Away_Mode_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'away-mode';
	}
	
	public function get_valid_types() {
		return array(
			'daily'    => __( 'Daily', 'it-l10n-ithemes-security-pro' ),
			'one-time' => __( 'One Time', 'it-l10n-ithemes-security-pro' ),
		);
	}
	
	protected function sanitize_settings() {
		if ( ! isset( $this->settings['override_type'] ) ) {
			$this->settings['override_type'] = $this->previous_settings['override_type'];
		}
		if ( ! isset( $this->settings['override_end'] ) ) {
			$this->settings['override_end'] = $this->previous_settings['override_end'];
		}
		
		
		$types = array_keys( $this->get_valid_types() );
		$this->sanitize_setting( $types, 'type', __( 'Type of Restriction', 'it-l10n-ithemes-security-pro' ) );
		
		$this->sanitize_setting( array( '', 'activate', 'deactivate' ), 'override_type', __( 'Override Type', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'int', 'override_end', __( 'Override End', 'it-l10n-ithemes-security-pro' ) );
		
		$this->sanitize_datetime( 'start', __( 'Start Timestamp', 'it-l10n-ithemes-security-pro' ), __( 'Start Date', 'it-l10n-ithemes-security-pro' ), __( 'Start Time', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_datetime( 'end', __( 'End Timestamp', 'it-l10n-ithemes-security-pro' ), __( 'End Date', 'it-l10n-ithemes-security-pro' ), __( 'End Time', 'it-l10n-ithemes-security-pro' ) );
	}
	
	private function sanitize_datetime( $prefix, $msg_timestamp, $msg_date, $msg_time ) {
		$this->vars_to_skip_validate_matching_fields[] = "{$prefix}_date";
		$this->vars_to_skip_validate_matching_fields[] = "{$prefix}_hour";
		$this->vars_to_skip_validate_matching_fields[] = "{$prefix}_minute";
		$this->vars_to_skip_validate_matching_fields[] = "{$prefix}_meridiem";
		$this->vars_to_skip_validate_matching_fields[] = "{$prefix}_time";
		
		
		if ( isset( $this->settings[$prefix] ) ) {
			$this->sanitize_setting( 'positive-int', $prefix, $msg_timestamp );
			return;
		}
		
		
		$valid_date = $this->sanitize_setting( 'date', "{$prefix}_date", $msg_date );
		
		if ( isset( $this->settings["{$prefix}_meridiem"] ) ) {
			if ( $this->sanitize_setting( array( 'am', 'pm' ), "{$prefix}_meridiem", $msg_time ) ) {
				$meridiem = $this->settings["{$prefix}_meridiem"];
			} else {
				$meridiem = false;
			}
			
			$valid_hours = range( 1, 12 );
		} else {
			$meridiem = '';
			$valid_hours = range( 0, 23 );
		}
		
		$valid_hour = $this->sanitize_setting( 'positive-int', "{$prefix}_hour", $msg_time ) && $this->sanitize_setting( $valid_hours, "{$prefix}_hour", $msg_time );
		$valid_minute = $this->sanitize_setting( 'positive-int', "{$prefix}_minute", $msg_time ) && $this->sanitize_setting( range( 0, 59 ), "{$prefix}_minute", $msg_time );
		
		
		if ( $valid_date && $valid_hour && $valid_minute && false !== $meridiem ) {
			$datetime = $this->settings["{$prefix}_date"] . ' ';
			$datetime .= sprintf( '%d:%02d %s', $this->settings["{$prefix}_hour"], $this->settings["{$prefix}_minute"], $meridiem );
			$datetime = trim( $datetime );
			
			$timestamp = strtotime( $datetime );
			
			if ( false === $timestamp ) {
				$id = $this->get_id();
				
				/* translators: 1: date input name, 2: time input name, 3: submitted date time */
				$this->add_error( new WP_Error( "itsec-validator-$id-invalid-datetime", sprintf( __( 'The %1$s and %2$s values resulted in a date and time of <code>%3$s</code>, which was unable to be processed properly. This could be an issue with PHP or a server configuration issue.', 'it-l10n-ithemes-security-pro' ), $msg_date, $msg_time, $datetime ) ) );
				
				$this->vars_to_skip_validate_matching_fields[] = $prefix;
			} else {
				$this->settings[$prefix] = intval( $timestamp - ITSEC_Core::get_time_offset() );
				$this->settings["{$prefix}_time"] = $timestamp - strtotime( date( 'Y-m-d', $timestamp ) );
				
				unset( $this->settings["{$prefix}_date"] );
				unset( $this->settings["{$prefix}_hour"] );
				unset( $this->settings["{$prefix}_minute"] );
				unset( $this->settings["{$prefix}_meridiem"] );
			}
		} else {
			$this->vars_to_skip_validate_matching_fields[] = $prefix;
		}
	}
	
	protected function validate_settings() {
		// Only validate settings if the data was successfully sanitized.
		if ( ! $this->can_save() ) {
			return;
		}
		
		
		require_once( dirname( __FILE__) . '/utilities.php' );
		
		
		$id = $this->get_id();
		
		if ( 'one-time' === $this->settings['type'] ) {
			if ( $this->settings['start'] >= $this->settings['end'] ) {
				/* translators: 1: "Start Date", 2: "Start Time", 3: "End Date", 4: "End Time" */
				$this->add_error( new WP_Error( "itsec-validator-$id-start-after-end", sprintf( __( 'The %1$s and %2$s must be before the %3$s and %4$s.', 'it-l10n-ithemes-security-pro' ), __( 'Start Date', 'it-l10n-ithemes-security-pro' ), __( 'Start Time', 'it-l10n-ithemes-security-pro' ), __( 'End Date', 'it-l10n-ithemes-security-pro' ), __( 'End Time', 'it-l10n-ithemes-security-pro' ) ) ) );
				$this->set_can_save( false );
			} else if ( false === ITSEC_Away_Mode_Utilities::is_current_timestamp_active( $this->settings['start'], $this->settings['end'], true ) ) {
				/* translators: 1: "End Date", 2: "End Time" */
				$this->add_error( new WP_Error( "itsec-validator-$id-end-already-ended", sprintf( __( 'The selected restriction date and time has already ended. Please select an %1$s and %2$s that has not already ended.', 'it-l10n-ithemes-security-pro' ), __( 'End Date', 'it-l10n-ithemes-security-pro' ), __( 'End Time', 'it-l10n-ithemes-security-pro' ) ) ) );
				$this->set_can_save( false );
			} else if ( ITSEC_Core::is_interactive() && ITSEC_Away_Mode_Utilities::is_current_timestamp_active( $this->settings['start'], $this->settings['end'] ) ) {
				/* translators: 1: "Start Date", 2: "Start Time" */
				$this->add_error( new WP_Error( "itsec-validator-$id-start-already-started", sprintf( __( 'The selected restriction date and time has already started and would result in locking you out immediately. Please select a %1$s and %2$s that has not already started.', 'it-l10n-ithemes-security-pro' ), __( 'Start Date', 'it-l10n-ithemes-security-pro' ), __( 'Start Time', 'it-l10n-ithemes-security-pro' ) ) ) );
				$this->set_can_save( false );
			}
		} else {
			if ( $this->settings['start_time'] === $this->settings['end_time'] ) {
				/* translators: 1: "Start Time", 2: "End Time" */
				$this->add_error( new WP_Error( "itsec-validator-$id-start-equals-end", sprintf( __( 'The %1$s and %2$s cannot be the same.', 'it-l10n-ithemes-security-pro' ), __( 'Start Time', 'it-l10n-ithemes-security-pro' ), __( 'End Time', 'it-l10n-ithemes-security-pro' ) ) ) );
				$this->set_can_save( false );
			} else if ( ITSEC_Core::is_interactive() && ITSEC_Away_Mode_Utilities::is_current_time_active( $this->settings['start_time'], $this->settings['end_time'] ) ) {
				/* translators: 1: "Start Time", 2: "End Time" */
				$this->add_error( new WP_Error( "itsec-validator-$id-settings-result-in-current-lockout", sprintf( __( 'The %1$s and %2$s settings restrict the current time and would result in locking you out immediately. Please select a %1$s and %2$s that does not restrict the current time.', 'it-l10n-ithemes-security-pro' ), __( 'Start Time', 'it-l10n-ithemes-security-pro' ), __( 'End Time', 'it-l10n-ithemes-security-pro' ) ) ) );
				$this->set_can_save( false );
			}
		}
	}
}

ITSEC_Modules::register_validator( new ITSEC_Away_Mode_Validator() );
