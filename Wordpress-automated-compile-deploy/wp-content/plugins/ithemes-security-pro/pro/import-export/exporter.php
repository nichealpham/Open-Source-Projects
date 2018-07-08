<?php

final class ITSEC_Import_Export_Exporter {
	public static function create( $email ) {
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-file.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php' );


		$email = sanitize_text_field( trim( $email ) );

		if ( ! is_email( $email ) ) {
			/* translators: 1: email address */
			return new WP_Error( 'itsec-import-export-exporter-create-invalid-email', sprintf( __( 'The supplied email address (%1$s) is invalid. The settings were not exported. Please supply a valid email address and try again.', 'it-l10n-ithemes-security-pro' ), $email ) );
		}


		$content = self::get_export_content();
		$content = json_encode( $content );


		$base_dir = ITSEC_Core::get_storage_dir() . '/export-' . current_time( 'Ymd-His' ) . '-';
		$dir = $base_dir . wp_generate_password( 10, false );
		$count = 0;

		while ( ITSEC_Lib_Directory::is_dir( $dir ) ) {
			$dir = $base_dir . wp_generate_password( 10, false );

			if ( ++$count > 20 ) {
				return new WP_Error( 'itsec-import-export-exporter-create-cannot-find-unique-directory', __( 'Unable to find a unique, new directory to store the generated export file. The settings were not exported.', 'it-l10n-ithemes-security-pro' ) );
			}
		}

		$result = ITSEC_Lib_Directory::create( $dir );

		if ( is_wp_error( $result ) ) {
			/* translators: 1: original error message */
			return new WP_Error( $result->get_error_code(), sprintf( __( 'Unable to create the directory to hold the export file. %1$s', 'it-l10n-ithemes-security-pro' ), $result->get_error_message() ) );
		}


		$settings_file = "$dir/itsec_options.json";
		$zip_file      = "$dir/itsec_options.zip";

		$result = ITSEC_Lib_File::write( $settings_file, $content );

		if ( is_wp_error( $result ) ) {
			/* translators: 1: original error message */
			return new WP_Error( $result->get_error_code(), sprintf( __( 'Unable to create the export file. %1$s', 'it-l10n-ithemes-security-pro' ), $result->get_error_message() ) );
		}


		$zip = new PclZip( $zip_file );

		$result = $zip->create( $settings_file, PCLZIP_OPT_REMOVE_PATH, dirname( $settings_file ) );

		if ( 0 === $result ) {
			$export_file = $settings_file;
		} else {
			$export_file = $zip_file;
			@unlink( $settings_file );
		}


		/* translators: 1: site title */
		$subject = sprintf( __( 'Security Settings Export for %1$s', 'it-l10n-ithemes-security-pro' ), get_bloginfo( 'name' ) );
		$subject = apply_filters( 'itsec_backup_email_subject', $subject );

		/* translators: 1: home URL, 2: date, 3: time */
		$body = '<p>' . sprintf( __( 'Attached is the settings file for %1$s created on %2$s at %3$s.', 'it-l10n-ithemes-security-pro' ), network_home_url(), date_i18n( get_option( 'date_format' ) ), date_i18n( get_option( 'time_format' ) ) ) . '</p>';

		if ( defined( 'ITSEC_DEBUG' ) && true === ITSEC_DEBUG ) {
			$body .= '<p>Debug info (source page): ' . esc_url( $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) . '</p>';
		}

		$message = "<html>$body</html>";

		$headers = array(
			sprintf( 'From: %s <%s>', get_bloginfo( 'name' ), get_option( 'admin_email' ) ),
		);

		$attachments = array( $export_file );

		add_filter( 'wp_mail_content_type', array( 'ITSEC_Import_Export_Exporter', 'get_html_content_type' ) );
		$result = wp_mail( $email, $subject, $message, $headers, $attachments );
		remove_filter( 'wp_mail_content_type', array( 'ITSEC_Import_Export_Exporter', 'get_html_content_type' ) );

		if ( false === $result ) {
			/* translators: 1: absolute path to export file */
			return new WP_Error( 'itsec-import-export-exporter-create-wp-mail-failed', sprintf( __( 'Sending the email message failed. The exported settings file can be found at <code>%1$s</code>.', 'it-l10n-ithemes-security-pro' ), $export_file ) );
		}


		ITSEC_Lib_Directory::remove( $dir );

		return true;
	}

	public static function get_export_content() {
		global $wpdb;


		$ignored_options = array(
			'itsec_data',
			'itsec_file_change_warning',
			'itsec_initials',
			'itsec_jquery_version',
			'itsec_local_file_list',
			'itsec_local_file_list_0',
			'itsec_local_file_list_1',
			'itsec_local_file_list_2',
			'itsec_local_file_list_3',
			'itsec_local_file_list_4',
			'itsec_local_file_list_5',
			'itsec_local_file_list_6',
			'itsec_message_queue',
			'itsec_rewrites_changed',
			'itsec_config_changed',
			'itsec_temp_whitelist_ip',
		);

		$raw_options = $wpdb->get_results( "SELECT * FROM `" . $wpdb->options . "` WHERE `option_name` LIKE 'itsec%';", ARRAY_A );

		$options = array();

		foreach ( $raw_options as $option ) {
			if ( in_array( $option['option_name'], $ignored_options ) ) {
				continue;
			}

			$options[] = array(
				'name'  => $option['option_name'],
				'value' => maybe_unserialize( $option['option_value'] ),
				'auto'  => ( 'yes' === $option['autoload'] ) ? 'yes' : 'no',
			);
		}


		$content = array(
			'exporter_version' => 1,
			'plugin_build'     => ITSEC_Core::get_plugin_build(),
			'timestamp'        => ITSEC_Core::get_current_time_gmt(),
			'site'             => network_home_url(),
			'options'          => $options,
			'abspath'          => ABSPATH,
		);

		return $content;
	}

	public static function get_html_content_type() {
		return 'text/html';
	}
}
