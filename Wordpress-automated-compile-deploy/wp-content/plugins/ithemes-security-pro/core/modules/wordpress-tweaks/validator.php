<?php

class ITSEC_WordPress_Tweaks_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'wordpress-tweaks';
	}

	protected function sanitize_settings() {
		$previous_settings = ITSEC_Modules::get_settings( $this->get_id() );

		if ( ! isset( $this->settings['jquery_version'] ) ) {
			$this->settings['jquery_version'] = $previous_settings['jquery_version'];
		}

		if ( ! isset( $this->settings['safe_jquery'] ) ) {
			$this->settings['safe_jquery'] = false;
		}

		$this->sanitize_setting( 'bool', 'wlwmanifest_header', __( 'Windows Live Writer Header', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'edituri_header', __( 'EditURI Header', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'comment_spam', __( 'Comment Spam', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'file_editor', __( 'File Editor', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'positive-int', 'disable_xmlrpc', __( 'XML-RPC', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( array( 0, 1, 2 ), 'disable_xmlrpc', __( 'XML-RPC', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'allow_xmlrpc_multiauth', __( 'Multiple Authentication Attempts per XML-RPC Request', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( array( 'default-access', 'restrict-access' ), 'rest_api', __( 'REST API', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'safe_jquery', __( 'Replace jQuery With a Safe Version', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'login_errors', __( 'Login Error Messages', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'force_unique_nicename', __( 'Force Unique Nickname', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'disable_unused_author_pages', __( 'Disable Extra User Archives', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'block_tabnapping', __( 'Protect Against Tabnapping', 'it-l10n-ithemes-security-pro' ) );
	}

	protected function validate_settings() {
		if ( ! $this->can_save() ) {
			return;
		}


		$previous_settings = ITSEC_Modules::get_settings( $this->get_id() );

		if ( $this->settings['file_editor'] !== $previous_settings['file_editor'] ) {
			ITSEC_Response::regenerate_wp_config();
		}

		if ( $this->settings['disable_xmlrpc'] !== $previous_settings['disable_xmlrpc'] || $this->settings['comment_spam'] !== $previous_settings['comment_spam'] ) {
			ITSEC_Response::regenerate_server_config();
		}
	}
}

ITSEC_Modules::register_validator( new ITSEC_WordPress_Tweaks_Validator() );
