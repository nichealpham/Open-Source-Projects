<?php

final class ITSEC_Database_Prefix_Utility {
	public static function change_database_prefix() {
		global $wpdb;


		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-config-file.php' );
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-file.php' );

		$response = array(
			'errors'     => array(),
			'new_prefix' => false,
		);


		//suppress error messages due to timing
//		error_reporting( 0 );
//		@ini_set( 'display_errors', 0 );

		$check_prefix = true; //Assume the first prefix we generate is unique

		//generate a new table prefix that doesn't conflict with any other in use in the database
		while ( $check_prefix ) {

			$avail = 'abcdefghijklmnopqrstuvwxyz0123456789';

			//first character should be alpha
			$new_prefix = $avail[ mt_rand( 0, 25 ) ];

			//length of new prefix
			$prelength = mt_rand( 4, 9 );

			//generate remaning characters
			for ( $i = 0; $i < $prelength; $i ++ ) {
				$new_prefix .= $avail[ mt_rand( 0, 35 ) ];
			}

			//complete with underscore
			$new_prefix .= '_';

			$new_prefix = esc_sql( $new_prefix ); //just be safe

			$check_prefix = $wpdb->get_results( 'SHOW TABLES LIKE "' . $new_prefix . '%";', ARRAY_N ); //if there are no tables with that prefix in the database set checkPrefix to false

		}


		$config_file_path = ITSEC_Lib_Config_File::get_wp_config_file_path();
		$config = ITSEC_Lib_File::read( $config_file_path );

		if ( is_wp_error( $config ) ) {
			/* translators: 1: Specific error details */
			$response['errors'][] = new WP_Error( $config->get_error_code(), sprintf( __( 'Unable to read the <code>wp-config.php</code> file in order to update the Database Prefix. Error details as follows: %1$s', 'it-l10n-ithemes-security-pro' ), $config->get_error_message() ) );
			return $response;
		}


		$regex = '/(\$table_prefix\s*=\s*)([\'"]).+?\\2(\s*;)/';
		$config = preg_replace( $regex, "\${1}'$new_prefix'\${3}", $config );

		$write_result = ITSEC_Lib_File::write( $config_file_path, $config );

		if ( is_wp_error( $write_result ) ) {
			/* translators: 1: Specific error details */
			$response['errors'][] = new WP_Error( $write_result->get_error_code(), sprintf( __( 'Unable to update the <code>wp-config.php</code> file in order to update the Database Prefix. Error details as follows: %1$s', 'it-l10n-ithemes-security-pro' ), $config->get_error_message() ) );
			return $response;
		}


		$response['new_prefix'] = $new_prefix;



		$tables = $wpdb->get_results( 'SHOW TABLES LIKE "' . $wpdb->base_prefix . '%"', ARRAY_N ); //retrieve a list of all tables in the DB

		//Rename each table
		foreach ( $tables as $table ) {

			$table = substr( $table[0], strlen( $wpdb->base_prefix ), strlen( $table[0] ) ); //Get the table name without the old prefix

			//rename the table and generate an error if there is a problem
			if ( $wpdb->query( 'RENAME TABLE `' . $wpdb->base_prefix . $table . '` TO `' . $new_prefix . $table . '`;' ) === false ) {

				$response['errors'][] = new WP_Error( 'itsec-database-prefix-utility-change-database-prefix-failed-table-rename', sprintf( __( 'Could not rename table %1$s. You may have to rename the table manually.', 'it-l10n-ithemes-security-pro' ), $wpdb->base_prefix . $table ) );

			}

		}

		if ( is_multisite() ) { //multisite requires us to rename each blogs' options

			$blogs = $wpdb->get_col( "SELECT blog_id FROM `" . $new_prefix . "blogs` WHERE public = '1' AND archived = '0' AND mature = '0' AND spam = '0' ORDER BY blog_id DESC" ); //get list of blog id's

			if ( is_array( $blogs ) ) { //make sure there are other blogs to update

				//update each blog's user_roles option
				foreach ( $blogs as $blog ) {

					$wpdb->query( 'UPDATE `' . $new_prefix . $blog . '_options` SET option_name = "' . $new_prefix . $blog . '_user_roles" WHERE option_name = "' . $wpdb->base_prefix . $blog . '_user_roles" LIMIT 1;' );

				}

			}

		}

		$upOpts = $wpdb->query( 'UPDATE `' . $new_prefix . 'options` SET option_name = "' . $new_prefix . 'user_roles" WHERE option_name = "' . $wpdb->base_prefix . 'user_roles" LIMIT 1;' ); //update options table and set flag to false if there's an error

		if ( $upOpts === false ) { //set an error

			$response['errors'][] = new WP_Error( 'itsec-database-prefix-utility-change-database-prefix-failed-options-update', __( 'Could not update prefix references in options table.', 'it-l10n-ithemes-security-pro' ) );

		}

		$rows = $wpdb->get_results( 'SELECT * FROM `' . $new_prefix . 'usermeta`' ); //get all rows in usermeta

		//update all prefixes in usermeta
		foreach ( $rows as $row ) {

			if ( substr( $row->meta_key, 0, strlen( $wpdb->base_prefix ) ) == $wpdb->base_prefix ) {

				$pos = $new_prefix . substr( $row->meta_key, strlen( $wpdb->base_prefix ), strlen( $row->meta_key ) );

				$result = $wpdb->query( 'UPDATE `' . $new_prefix . 'usermeta` SET meta_key="' . $pos . '" WHERE meta_key= "' . $row->meta_key . '" LIMIT 1;' );

				if ( $result == false ) {

					$response['errors'][] = new WP_Error( 'itsec-database-prefix-utility-change-database-prefix-failed-usermeta-update', __( 'Could not update prefix references in usermeta table.', 'it-l10n-ithemes-security-pro' ) );

				}

			}

		}


		return $response;
	}
}
