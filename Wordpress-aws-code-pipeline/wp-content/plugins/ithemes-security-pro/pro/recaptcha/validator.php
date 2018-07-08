<?php

class ITSEC_Recaptcha_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'recaptcha';
	}

	protected function sanitize_settings() {
		$this->set_previous_if_empty( array( 'validated', 'last_error' ) );

		$this->sanitize_setting( array_keys( $this->get_valid_types() ), 'type', esc_html__( 'Type', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'string', 'site_key', esc_html__( 'Site Key', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'string', 'secret_key', esc_html__( 'Secret Key', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'login', esc_html__( 'Use on Login', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'register', esc_html__( 'Use on New User Registration', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'comments', esc_html__( 'Use on Comments', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( array_keys( $this->get_valid_languages() ), 'language', esc_html__( 'Language', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'theme', esc_html__( 'Use Dark Theme', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'positive-int', 'error_threshold', esc_html__( 'Lockout Error Threshold', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'positive-int', 'check_period', esc_html__( 'Lockout Check Period', 'it-l10n-ithemes-security-pro' ) );

		if ( $this->settings['type'] !== $this->previous_settings['type'] || $this->settings['site_key'] !== $this->previous_settings['site_key'] || $this->settings['secret_key'] !== $this->previous_settings['secret_key'] ) {
			$this->settings['validated'] = false;
			$this->settings['last_error'] = '';
		}
	}

	protected function validate_settings() {
		if ( ! $this->can_save() ) {
			return;
		}

		if ( ITSEC_Core::doing_data_upgrade() ) {
			return;
		}

		if ( empty( $this->settings['site_key'] ) && empty( $this->settings['secret_key'] ) ) {
			$this->add_error( esc_html__( 'The reCAPTCHA feature will not be fully functional until you provide a Site Key and Secret Key.', 'it-l10n-ithemes-security-pro' ) );
		} else if ( empty( $this->settings['site_key'] ) ) {
			$this->add_error( esc_html__( 'The reCAPTCHA feature will not be fully functional until you provide a Site Key.', 'it-l10n-ithemes-security-pro' ) );
		} else if ( empty( $this->settings['secret_key'] ) ) {
			$this->add_error( esc_html__( 'The reCAPTCHA feature will not be fully functional until you provide a Secret Key.', 'it-l10n-ithemes-security-pro' ) );
		}
	}

	public function get_valid_types() {
		return array(
			'v2'        => esc_html__( 'reCAPTCHA V2', 'it-l10n-ithemes-security-pro' ),
			'invisible' => esc_html__( 'Invisible reCAPTCHA', 'it-l10n-ithemes-security-pro' ),
		);
	}

	public function get_valid_types_with_description() {
		return array(
			'v2'        => esc_html__( 'reCAPTCHA V2 - Validate users with the "I\'m not a robot" checkbox.', 'it-l10n-ithemes-security-pro' ),
			'invisible' => esc_html__( 'Invisible reCAPTCHA - Validate users in the background.', 'it-l10n-ithemes-security-pro' ),
		);
	}

	public function get_valid_languages() {
		return array(
			''       => esc_html__( 'Detect', 'it-l10n-ithemes-security-pro' ),
			'ar'     => esc_html__( 'Arabic', 'it-l10n-ithemes-security-pro' ),
			'af'     => esc_html__( 'Afrikaans', 'it-l10n-ithemes-security-pro' ),
			'am'     => esc_html__( 'Amharic', 'it-l10n-ithemes-security-pro' ),
			'hy'     => esc_html__( 'Armenian', 'it-l10n-ithemes-security-pro' ),
			'az'     => esc_html__( 'Azerbaijani', 'it-l10n-ithemes-security-pro' ),
			'eu'     => esc_html__( 'Basque', 'it-l10n-ithemes-security-pro' ),
			'bn'     => esc_html__( 'Bengali', 'it-l10n-ithemes-security-pro' ),
			'bg'     => esc_html__( 'Bulgarian', 'it-l10n-ithemes-security-pro' ),
			'ca'     => esc_html__( 'Catalan', 'it-l10n-ithemes-security-pro' ),
			'zh-HK'  => esc_html__( 'Chinese (Hong Kong)', 'it-l10n-ithemes-security-pro' ),
			'zh-CN'  => esc_html__( 'Chinese (Simplified)', 'it-l10n-ithemes-security-pro' ),
			'zh-TW'  => esc_html__( 'Chinese (Traditional)', 'it-l10n-ithemes-security-pro' ),
			'hr'     => esc_html__( 'Croatian', 'it-l10n-ithemes-security-pro' ),
			'cs'     => esc_html__( 'Czech', 'it-l10n-ithemes-security-pro' ),
			'da'     => esc_html__( 'Danish', 'it-l10n-ithemes-security-pro' ),
			'nl'     => esc_html__( 'Dutch', 'it-l10n-ithemes-security-pro' ),
			'en-GB'  => esc_html__( 'English (UK)', 'it-l10n-ithemes-security-pro' ),
			'en'     => esc_html__( 'English (US)', 'it-l10n-ithemes-security-pro' ),
			'et'     => esc_html__( 'Estonian', 'it-l10n-ithemes-security-pro' ),
			'fil'    => esc_html__( 'Filipino', 'it-l10n-ithemes-security-pro' ),
			'fi'     => esc_html__( 'Finnish', 'it-l10n-ithemes-security-pro' ),
			'fr'     => esc_html__( 'French', 'it-l10n-ithemes-security-pro' ),
			'fr-CA'  => esc_html__( 'French (Canadian)', 'it-l10n-ithemes-security-pro' ),
			'gl'     => esc_html__( 'Galician', 'it-l10n-ithemes-security-pro' ),
			'ka'     => esc_html__( 'Georgian', 'it-l10n-ithemes-security-pro' ),
			'de'     => esc_html__( 'German', 'it-l10n-ithemes-security-pro' ),
			'de-AT'  => esc_html__( 'German (Austria)', 'it-l10n-ithemes-security-pro' ),
			'de-CH'  => esc_html__( 'German (Switzerland)', 'it-l10n-ithemes-security-pro' ),
			'el'     => esc_html__( 'Greek', 'it-l10n-ithemes-security-pro' ),
			'gu'     => esc_html__( 'Gujarati', 'it-l10n-ithemes-security-pro' ),
			'iw'     => esc_html__( 'Hebrew', 'it-l10n-ithemes-security-pro' ),
			'hi'     => esc_html__( 'Hindi', 'it-l10n-ithemes-security-pro' ),
			'hu'     => esc_html__( 'Hungarain', 'it-l10n-ithemes-security-pro' ),
			'is'     => esc_html__( 'Icelandic', 'it-l10n-ithemes-security-pro' ),
			'id'     => esc_html__( 'Indonesian', 'it-l10n-ithemes-security-pro' ),
			'it'     => esc_html__( 'Italian', 'it-l10n-ithemes-security-pro' ),
			'ja'     => esc_html__( 'Japanese', 'it-l10n-ithemes-security-pro' ),
			'kn'     => esc_html__( 'Kannada', 'it-l10n-ithemes-security-pro' ),
			'ko'     => esc_html__( 'Korean', 'it-l10n-ithemes-security-pro' ),
			'lo'     => esc_html__( 'Laothian', 'it-l10n-ithemes-security-pro' ),
			'lv'     => esc_html__( 'Latvian', 'it-l10n-ithemes-security-pro' ),
			'lt'     => esc_html__( 'Lithuanian', 'it-l10n-ithemes-security-pro' ),
			'ms'     => esc_html__( 'Malay', 'it-l10n-ithemes-security-pro' ),
			'ml'     => esc_html__( 'Malayalam', 'it-l10n-ithemes-security-pro' ),
			'mr'     => esc_html__( 'Marathi', 'it-l10n-ithemes-security-pro' ),
			'mn'     => esc_html__( 'Mongolian', 'it-l10n-ithemes-security-pro' ),
			'no'     => esc_html__( 'Norwegian', 'it-l10n-ithemes-security-pro' ),
			'fa'     => esc_html__( 'Persian', 'it-l10n-ithemes-security-pro' ),
			'pl'     => esc_html__( 'Polish', 'it-l10n-ithemes-security-pro' ),
			'pt'     => esc_html__( 'Portuguese', 'it-l10n-ithemes-security-pro' ),
			'pt-BR'  => esc_html__( 'Portuguese (Brazil)', 'it-l10n-ithemes-security-pro' ),
			'pt-PT'  => esc_html__( 'Portuguese (Portugal)', 'it-l10n-ithemes-security-pro' ),
			'ro'     => esc_html__( 'Romanian', 'it-l10n-ithemes-security-pro' ),
			'ru'     => esc_html__( 'Russian', 'it-l10n-ithemes-security-pro' ),
			'sr'     => esc_html__( 'Serbian', 'it-l10n-ithemes-security-pro' ),
			'si'     => esc_html__( 'Sinhalese', 'it-l10n-ithemes-security-pro' ),
			'sk'     => esc_html__( 'Slovak', 'it-l10n-ithemes-security-pro' ),
			'sl'     => esc_html__( 'Slovenian', 'it-l10n-ithemes-security-pro' ),
			'es'     => esc_html__( 'Spanish', 'it-l10n-ithemes-security-pro' ),
			'es-419' => esc_html__( 'Spanish (Latin America)', 'it-l10n-ithemes-security-pro' ),
			'sw'     => esc_html__( 'Swahili', 'it-l10n-ithemes-security-pro' ),
			'sv'     => esc_html__( 'Swedish', 'it-l10n-ithemes-security-pro' ),
			'ta'     => esc_html__( 'Tamil', 'it-l10n-ithemes-security-pro' ),
			'te'     => esc_html__( 'Telugu', 'it-l10n-ithemes-security-pro' ),
			'th'     => esc_html__( 'Thai', 'it-l10n-ithemes-security-pro' ),
			'tr'     => esc_html__( 'Turkish', 'it-l10n-ithemes-security-pro' ),
			'uk'     => esc_html__( 'Ukrainian', 'it-l10n-ithemes-security-pro' ),
			'ur'     => esc_html__( 'Urdu', 'it-l10n-ithemes-security-pro' ),
			'vi'     => esc_html__( 'Vietnamese', 'it-l10n-ithemes-security-pro' ),
			'zu'     => esc_html__( 'Zulu', 'it-l10n-ithemes-security-pro' ),
		);
	}
}

ITSEC_Modules::register_validator( new ITSEC_Recaptcha_Validator() );
