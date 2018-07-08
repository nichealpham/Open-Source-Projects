<?php

class Ithemes_Sync_Verb_ITSEC_Override_Away_Mode extends Ithemes_Sync_Verb {
	public static $name = 'itsec-override-away-mode';
	public static $description = 'Override current away mode status.';
	
	public $default_arguments = array(
		'intention' => '',
	);
	
	public function run( $arguments ) {
		$arguments = Ithemes_Sync_Functions::merge_defaults( $arguments, $this->default_arguments );
		
		
		$details = ITSEC_Away_Mode::is_active( true );
		$settings = ITSEC_Modules::get_settings( 'away-mode' );
		$defaults = ITSEC_Modules::get_defaults( 'away-mode' );
		
		$errors = array();
		
		
		if ( 'activate' === $arguments['intention'] ) {
			if ( $details['active'] ) {
				$action = 'stayed-active';
				$success = true;
			} else if ( $details['override_active'] && 'deactivate' === $details['override_type'] ) {
				$action = 'removed-deactivate-override';
				
				$settings['override_type'] = $defaults['override_type'];
				$settings['override_end'] = $defaults['override_end'];
			} else if ( false === $details['next'] ) {
				$action = 'denied-activate';
				$errors[] = new WP_Error( 'itsec-sync-verb-itsec-override-away-mode-cannot-override-activate-expired-one-time', __( 'iThemes Security received a request to modify the override behavior of the Away Mode module. However, the request is invalid as the module is configured for a one-time lockout that occurred in the past. Allowing an activate override would result in an unending Away Mode lockout.', 'it-l10n-ithemes-security-pro' ) );
				$success = false;
			} else {
				$action = 'added-activate-override';
				
				$settings['override_type'] = 'activate';
				$settings['override_end'] = ITSEC_Core::get_current_time() + $details['next'];
			}
		} else if ( 'deactivate' === $arguments['intention'] ) {
			if ( ! $details['active'] ) {
				$action = 'stayed-inactive';
				$success = true;
			} else if ( $details['override_active'] && 'activate' === $details['override_type'] ) {
				$action = 'removed-activate-override';
				
				$settings['override_type'] = $defaults['override_type'];
				$settings['override_end'] = $defaults['override_end'];
			} else {
				$action = 'added-deactivate-override';
				
				$settings['override_type'] = 'deactivate';
				$settings['override_end'] = ITSEC_Core::get_current_time() + $details['remaining'];
			}
		} else if ( empty( $arguments['intention'] ) ) {
			$action = 'missing-intention';
			$errors[] = new WP_Error( 'itsec-sync-verb-itsec-override-away-mode-missing-intention', __( 'iThemes Security received a request to modify the override behavior of the Away Mode module. However, the request is invalid as the required "intention" argument is missing.', 'it-l10n-ithemes-security-pro' ) );
			$success = false;
		} else {
			$action = 'unknown-intention';
			$errors[] = new WP_Error( 'itsec-sync-verb-itsec-override-away-mode-unknown-intention', sprintf( __( 'iThemes Security received a request to modify the override behavior of the Away Mode module. However, the request is invalid as the required "intention" argument is set to an unrecognized value: "".', 'it-l10n-ithemes-security-pro' ), $arguments['intention'] ) );
			$success = false;
		}
		
		if ( ! isset( $success ) ) {
			ITSEC_Core::set_interactive( false );
			$results = ITSEC_Modules::set_settings( 'away-mode', $settings );
			
			if ( $results['saved'] ) {
				$success = true;
			} else {
				$errors = $results['errors'];
				$success = false;
			}
		}
		
		
		if ( $success ) {
			$status = "{$arguments['intention']}d";
		} else {
			$status = 'error';
		}
		
		$response = array(
			'api'     => '1',
			'status'  => $status,
			'action'  => $action,
			'errors'  => $errors,
			'details' => ITSEC_Away_Mode::is_active( true ),
		);
		
		
		return $response;
	}
}
