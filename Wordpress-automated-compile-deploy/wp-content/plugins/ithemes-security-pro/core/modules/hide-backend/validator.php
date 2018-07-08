<?php

final class ITSEC_Hide_Backend_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'hide-backend';
	}

	protected function sanitize_settings() {
		$this->sanitize_setting( 'bool', 'enabled', __( 'Hide Backend', 'it-l10n-ithemes-security-pro' ) );

		if ( ! $this->settings['enabled'] ) {
			// Ignore all non-enabled settings changes when enabled is not checked.
			foreach ( $this->previous_settings as $name => $val ) {
				if ( 'enabled' !== $name ) {
					$this->settings[$name] = $val;
				}
			}

			return;
		}

		if ( ! isset( $this->settings['register'] ) ) {
			$this->settings['register'] = $this->previous_settings['register'];
		}

		$this->sanitize_setting( 'non-empty-title', 'slug', __( 'Login Slug', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'non-empty-title', 'register', __( 'Register Slug', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'theme_compat', __( 'Enable Redirection', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'non-empty-title', 'theme_compat_slug', __( 'Redirection Slug', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'title', 'post_logout_slug', __( 'Custom Login Action', 'it-l10n-ithemes-security-pro' ) );
	}

	protected function validate_settings() {
		if ( ! $this->can_save() ) {
			return;
		}


		$forbidden_slugs = array( 'admin', 'login', 'wp-login.php', 'dashboard', 'wp-admin' );

		if ( in_array( $this->settings['slug'], $forbidden_slugs ) ) {
			$this->add_error( __( 'The Login Slug cannot be "%1$s" as WordPress uses that slug.', 'it-l10n-ithemes-security-pro' ) );
			$this->set_can_save( false );
			return;
		}


		if ( $this->settings['enabled'] && $this->settings['slug'] !== $this->previous_settings['slug'] ) {
			$url = get_site_url() . '/' . $this->settings['slug'];
			ITSEC_Response::add_message( sprintf( __( 'The Hide Backend feature is now active. Your new login URL is <strong><code>%1$s</code></strong>. Please note this may be different than what you sent as the URL was sanitized to meet various requirements. A reminder has also been sent to the notification email addresses set in iThemes Security\'s Global settings.', 'it-l10n-ithemes-security-pro' ), esc_url( $url ) ) );
		} else if ( $this->settings['enabled'] && ! $this->previous_settings['enabled'] ) {
			$url = get_site_url() . '/' . $this->settings['slug'];
			ITSEC_Response::add_message( sprintf( __( 'The Hide Backend feature is now active. Your new login URL is <strong><code>%1$s</code></strong>. A reminder has also been sent to the notification email addresses set in iThemes Security\'s Global settings.', 'it-l10n-ithemes-security-pro' ), esc_url( $url ) ) );
		} else if ( ! $this->settings['enabled'] && $this->previous_settings['enabled'] ) {
			$url = get_site_url() . '/wp-login.php';
			ITSEC_Response::add_message( sprintf( __( 'The Hide Backend feature is now disabled. Your new login URL is <strong><code>%1$s</code></strong>. A reminder has also been sent to the notification email addresses set in iThemes Security\'s Global settings.', 'it-l10n-ithemes-security-pro' ), esc_url( $url ) ) );
		}

		if ( isset( $url ) ) {
			$this->send_new_login_url( $url );
			ITSEC_Response::prevent_modal_close();
		}

		if (
			$this->settings['enabled'] !== $this->previous_settings['enabled'] ||
			$this->settings['slug'] !== $this->previous_settings['slug'] ||
			$this->settings['register'] !== $this->previous_settings['register']
		) {
			ITSEC_Response::regenerate_server_config();
		}


		ITSEC_Response::reload_module( $this->get_id() );
	}

	private function send_new_login_url( $url ) {
		if ( ITSEC_Core::doing_data_upgrade() ) {
			// Do not send emails when upgrading data. This prevents spamming users with notifications just because the
			// data was ported from an old version to a new version.
			return;
		}

		$message = '<p>' . __( 'Dear Site Admin,', 'it-l10n-ithemes-security-pro' ) . "</p>\n";

		/* translators: 1: Site name, 2: Site address, 3: New login address */
		$message .= '<p>' . sprintf( __( 'The login address for %1$s (<code>%2$s</code>) has changed. The new login address is <code>%3$s</code>. You will be unable to use the old login address.', 'it-l10n-ithemes-security-pro' ), get_bloginfo( 'name' ), esc_url( get_site_url() ), esc_url( $url ) ) . "</p>\n";

		if ( defined( 'ITSEC_DEBUG' ) && ITSEC_DEBUG === true ) {
			$message.= '<p>Debug info (source page): ' . esc_url( $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) . "</p>\n";
		}

		$message = "<html>\n$message</html>\n";


		//Setup the remainder of the email
		$recipients = ITSEC_Modules::get_setting( 'global', 'notification_email' );
		$subject    = sprintf( __( '[%1$s] WordPress Login Address Changed', 'it-l10n-ithemes-security-pro' ), get_site_url() );
		$subject    = apply_filters( 'itsec_lockout_email_subject', $subject );
		$headers    = 'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>' . "\r\n";

		//Use HTML Content type
		add_filter( 'wp_mail_content_type', array( $this, 'get_html_content_type' ) );

		//Send emails to all recipients
		foreach ( $recipients as $recipient ) {
			$recipient = trim( $recipient );

			if ( is_email( $recipient ) ) {

				wp_mail( $recipient, $subject, $message, $headers );

			}

		}

		//Remove HTML Content type
		remove_filter( 'wp_mail_content_type', array( $this, 'get_html_content_type' ) );

	}

	/**
	 * Set HTML content type for email
	 *
	 * @return string html content type
	 */
	public function get_html_content_type() {
		return 'text/html';
	}
}

ITSEC_Modules::register_validator( new ITSEC_Hide_Backend_Validator() );
