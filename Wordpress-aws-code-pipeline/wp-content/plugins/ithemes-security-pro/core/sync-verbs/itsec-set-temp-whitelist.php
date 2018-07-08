<?php

class Ithemes_Sync_Verb_ITSEC_Set_Temp_Whitelist extends Ithemes_Sync_Verb {
	public static $name = 'itsec-set-temp-whitelist';
	public static $description = 'Set temporarily whitelisted IP.';

	public $default_arguments = array(
		'direction' => 'add',
		'ip'        => '',
	);


	public function run( $arguments ) {
		/** @var ITSEC_Lockout $itsec_lockout */
		global $itsec_lockout;


		if ( ! isset( $arguments['ip'] ) ) {
			return new WP_Error( 'missing-argument-ip', __( 'The ip argument is missing.', 'it-l10n-ithemes-security-pro' ) );
		}

		$arguments = Ithemes_Sync_Functions::merge_defaults( $arguments, $this->default_arguments );
		$ip = sanitize_text_field( $arguments['ip'] );

		if ( empty( $ip ) ) {
			return new WP_Error( 'empty-ip', __( 'An empty ip argument was submitted.', 'it-l10n-ithemes-security-pro' ) );
		}

		$direction = isset( $arguments['direction'] ) ? $arguments['direction'] : 'add';

		if ( 'add' === $direction ) {
			$itsec_lockout->add_to_temp_whitelist( $ip );
		} else if ( 'remove' === $direction ) {
			$itsec_lockout->remove_from_temp_whitelist( $ip );
		} else if ( 'clear' === $direction ) {
			$itsec_lockout->clear_temp_whitelist();
		} else {
			return new WP_Error( 'invalid-argument-value-for-direction', __( 'The direction argument must be either "add", "clear", or "remove".', 'it-l10n-ithemes-security-pro' ) );
		}

		return true;
	}
}
