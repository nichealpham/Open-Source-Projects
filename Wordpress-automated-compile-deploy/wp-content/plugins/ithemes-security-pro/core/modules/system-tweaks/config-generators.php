<?php

final class ITSEC_System_Tweaks_Config_Generators {
	public static function filter_litespeed_server_config_modification( $modification ) {
		return self::filter_apache_server_config_modification( $modification, 'litespeed' );
	}

	public static function filter_apache_server_config_modification( $modification, $server = 'apache' ) {
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-utility.php' );

		$input = ITSEC_Modules::get_settings( 'system-tweaks' );
		$wp_includes = WPINC;

		if ( $input['protect_files'] ) {
			$files = array(
				'.htaccess',
				'readme.html',
				'readme.txt',
				'wp-config.php',
			);

			$modification .= "\n";
			$modification .= "\t# " . __( 'Protect System Files - Security > Settings > System Tweaks > System Files', 'it-l10n-ithemes-security-pro' ) . "\n";

			foreach ( $files as $file ) {
				$modification .= "\t<files $file>\n";

				if ( 'apache' === $server ) {
					$modification .= "\t\t<IfModule mod_authz_core.c>\n";
					$modification .= "\t\t\tRequire all denied\n";
					$modification .= "\t\t</IfModule>\n";
					$modification .= "\t\t<IfModule !mod_authz_core.c>\n";
					$modification .= "\t\t\tOrder allow,deny\n";
					$modification .= "\t\t\tDeny from all\n";
					$modification .= "\t\t</IfModule>\n";
				} else {
					$modification .= "\t\t<IfModule mod_litespeed.c>\n";
					$modification .= "\t\t\tOrder allow,deny\n";
					$modification .= "\t\t\tDeny from all\n";
					$modification .= "\t\t</IfModule>\n";
				}

				$modification .= "\t</files>\n";
			}
		}

		if ( $input['directory_browsing'] ) {
			$modification .= "\n";
			$modification .= "\t# " . __( 'Disable Directory Browsing - Security > Settings > System Tweaks > Directory Browsing', 'it-l10n-ithemes-security-pro' ) . "\n";
			$modification .= "\tOptions -Indexes\n";
		}


		$rewrites = '';

		if ( $input['protect_files'] ) {
			$rewrites .= "\n";
			$rewrites .= "\t\t# " . __( 'Protect System Files - Security > Settings > System Tweaks > System Files', 'it-l10n-ithemes-security-pro' ) . "\n";
			$rewrites .= "\t\tRewriteRule ^wp-admin/install\.php$ - [F]\n";
			$rewrites .= "\t\tRewriteRule ^wp-admin/includes/ - [F]\n";

			if ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) {
				$rewrites .= "\t\tRewriteRule ^$wp_includes/ms-files.php$ - [S=4]\n";
			}

			$rewrites .= "\t\tRewriteRule !^$wp_includes/ - [S=3]\n";
			$rewrites .= "\t\tRewriteRule ^$wp_includes/[^/]+\.php$ - [F]\n";
			$rewrites .= "\t\tRewriteRule ^$wp_includes/js/tinymce/langs/.+\.php - [F]\n";
			$rewrites .= "\t\tRewriteRule ^$wp_includes/theme-compat/ - [F]\n";
		}

		if ( $input['uploads_php'] ) {
			$dir = ITSEC_Lib_Utility::get_relative_upload_url_path();

			if ( ! empty( $dir ) ) {
				$dir = preg_quote( $dir );

				$rewrites .= "\n";
				$rewrites .= "\t\t# " . __( 'Disable PHP in Uploads - Security > Settings > System Tweaks > PHP in Uploads', 'it-l10n-ithemes-security-pro' ) . "\n";
				$rewrites .= "\t\tRewriteRule ^$dir/.*\.(?:php[1-7]?|pht|phtml?|phps)$ - [NC,F]\n";
			}
		}

		if ( $input['plugins_php'] ) {
			$dir = ITSEC_Lib_Utility::get_relative_url_path( WP_PLUGIN_URL );

			if ( ! empty( $dir ) ) {
				$dir = preg_quote( $dir );

				$rewrites .= "\n";
				$rewrites .= "\t\t# " . __( 'Disable PHP in Plugins - Security > Settings > System Tweaks > PHP in Plugins', 'it-l10n-ithemes-security-pro' ) . "\n";
				$rewrites .= "\t\tRewriteRule ^$dir/.*\.(?:php[1-7]?|pht|phtml?|phps)$ - [NC,F]\n";
			}
		}

		if ( $input['themes_php'] ) {
			$dir = ITSEC_Lib_Utility::get_relative_url_path( get_theme_root_uri() );

			if ( ! empty( $dir ) ) {
				$dir = preg_quote( $dir );

				$rewrites .= "\n";
				$rewrites .= "\t\t# " . __( 'Disable PHP in Themes - Security > Settings > System Tweaks > PHP in Themes', 'it-l10n-ithemes-security-pro' ) . "\n";
				$rewrites .= "\t\tRewriteRule ^$dir/.*\.(?:php[1-7]?|pht|phtml?|phps)$ - [NC,F]\n";
			}
		}

		if ( $input['request_methods'] ) {
			$rewrites .= "\n";
			$rewrites .= "\t\t# " . __( 'Filter Request Methods - Security > Settings > System Tweaks > Request Methods', 'it-l10n-ithemes-security-pro' ) . "\n";
			$rewrites .= "\t\tRewriteCond %{REQUEST_METHOD} ^(TRACE|DELETE|TRACK) [NC]\n";
			$rewrites .= "\t\tRewriteRule ^.* - [F]\n";
		}

		if ( $input['suspicious_query_strings'] ) {
			$rewrites .= "\n";
			$rewrites .= "\t\t# " . __( 'Filter Suspicious Query Strings in the URL - Security > Settings > System Tweaks > Suspicious Query Strings', 'it-l10n-ithemes-security-pro' ) . "\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} \.\.\/ [OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} \.(bash|git|hg|log|svn|swp|cvs) [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} etc/passwd [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} boot\.ini [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} ftp: [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} https?: [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} (<|%3C)script(>|%3E) [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} mosConfig_[a-zA-Z_]{1,21}(=|%3D) [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} base64_decode\( [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} %24&x [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} 127\.0 [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} (globals|encode|localhost|loopback) [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} (request|concat|insert|union|declare) [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} %[01][0-9A-F] [NC]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} !^loggedout=true\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} !^action=jetpack-sso\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} !^action=rp\n";
			$rewrites .= "\t\tRewriteCond %{HTTP_COOKIE} !wordpress_logged_in_\n";
			$rewrites .= "\t\tRewriteCond %{HTTP_REFERER} !^http://maps\.googleapis\.com\n";
			$rewrites .= "\t\tRewriteRule ^.* - [F]\n";
		}

		if ( $input['non_english_characters'] ) {
			$rewrites .= "\n";
			$rewrites .= "\t\t# " . __( 'Filter Non-English Characters - Security > Settings > System Tweaks > Non-English Characters', 'it-l10n-ithemes-security-pro' ) . "\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} %[A-F][0-9A-F] [NC]\n";
			$rewrites .= "\t\tRewriteRule ^.* - [F]\n";
		}

		if ( ! empty( $rewrites ) ) {
			$modification .= "\n";
			$modification .= "\t<IfModule mod_rewrite.c>\n";
			$modification .= "\t\tRewriteEngine On\n";
			$modification .= $rewrites;
			$modification .= "\t</IfModule>\n";
		}


		return $modification;
	}

	public static function filter_nginx_server_config_modification( $modification ) {
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-utility.php' );

		$input = ITSEC_Modules::get_settings( 'system-tweaks' );
		$wp_includes = WPINC;

		if ( $input['protect_files'] ) {
			$config_file = ITSEC_Lib::get_htaccess();

			if ( 0 === strpos( $config_file, ABSPATH ) ) {
				$config_file = '/' . substr( $config_file, strlen( ABSPATH ) );
			} else {
				$config_file = '/nginx.conf';
			}

			$modification .= "\n";
			$modification .= "\t# " . __( 'Protect System Files - Security > Settings > System Tweaks > System Files', 'it-l10n-ithemes-security-pro' ) . "\n";
			$modification .= "\tlocation = /wp-admin/install\.php { deny all; }\n";
			$modification .= "\tlocation = $config_file { deny all; }\n";
			$modification .= "\tlocation ~ /\.htaccess$ { deny all; }\n";
			$modification .= "\tlocation ~ /readme\.html$ { deny all; }\n";
			$modification .= "\tlocation ~ /readme\.txt$ { deny all; }\n";
			$modification .= "\tlocation ~ /wp-config.php$ { deny all; }\n";
			$modification .= "\tlocation ~ ^/wp-admin/includes/ { deny all; }\n";

			if ( ! is_multisite() || ! get_site_option( 'ms_files_rewriting' ) ) {
				// nginx can only reliably block PHP files in wp-includes if requests to wp-includes/ms-files.php are
				// not required. This is because there is no skip directive as Apache has.
				$modification .= "\tlocation ~ ^/$wp_includes/[^/]+\.php$ { deny all; }\n";
			}

			$modification .= "\tlocation ~ ^/$wp_includes/js/tinymce/langs/.+\.php$ { deny all; }\n";
			$modification .= "\tlocation ~ ^/$wp_includes/theme-compat/ { deny all; }\n";
		}

		// Rewrite Rules for Disable PHP in Uploads
		if ( $input['uploads_php'] ) {
			$dir = ITSEC_Lib_Utility::get_relative_upload_url_path();

			if ( ! empty( $dir ) ) {
				$dir = preg_quote( $dir );

				$modification .= "\n";
				$modification .= "\t# " . __( 'Disable PHP in Uploads - Security > Settings > System Tweaks > PHP in Uploads', 'it-l10n-ithemes-security-pro' ) . "\n";
				$modification .= "\tlocation ~ ^/$dir/.*\.(?:php[1-7]?|pht|phtml?|phps)$ { deny all; }\n";
			}
		}

		// Rewrite Rules for Disable PHP in Plugins
		if ( $input['plugins_php'] ) {
			$dir = ITSEC_Lib_Utility::get_relative_url_path( WP_PLUGIN_URL );

			if ( ! empty( $dir ) ) {
				$dir = preg_quote( $dir );

				$modification .= "\n";
				$modification .= "\t# " . __( 'Disable PHP in Plugins - Security > Settings > System Tweaks > PHP in Plugins', 'it-l10n-ithemes-security-pro' ) . "\n";
				$modification .= "\tlocation ~ ^/$dir/.*\.(?:php[1-7]?|pht|phtml?|phps)$ { deny all; }\n";
			}
		}

		// Rewrite Rules for Disable PHP in Themes
		if ( $input['themes_php'] ) {
			$dir = ITSEC_Lib_Utility::get_relative_url_path( get_theme_root_uri() );

			if ( ! empty( $dir ) ) {
				$dir = preg_quote( $dir );

				$modification .= "\n";
				$modification .= "\t# " . __( 'Disable PHP in Themes - Security > Settings > System Tweaks > PHP in Themes', 'it-l10n-ithemes-security-pro' ) . "\n";
				$modification .= "\tlocation ~ ^/$dir/.*\.(?:php[1-7]?|pht|phtml?|phps)$ { deny all; }\n";
			}
		}

		// Apache rewrite rules for disable http methods
		if ( $input['request_methods'] ) {
			$modification .= "\n";
			$modification .= "\t# " . __( 'Filter Request Methods - Security > Settings > System Tweaks > Request Methods', 'it-l10n-ithemes-security-pro' ) . "\n";
			$modification .= "\tif ( \$request_method ~* ^(TRACE|DELETE|TRACK)$ ) { return 403; }\n";
		}

		// Process suspicious query rules
		if ( $input['suspicious_query_strings'] ) {
			$modification .= "\n";
			$modification .= "\t# " . __( 'Filter Suspicious Query Strings in the URL - Security > Settings > System Tweaks > Suspicious Query Strings', 'it-l10n-ithemes-security-pro' ) . "\n";
			$modification .= "\tset \$susquery 0;\n";
			$modification .= "\tif ( \$args ~* \"\.\./\" ) { set \$susquery 1; }\n";
			$modification .= "\tif ( \$args ~* \"\.(bash|git|hg|log|svn|swp|cvs)\" ) { set \$susquery 1; }\n";
			$modification .= "\tif ( \$args ~* \"etc/passwd\" ) { set \$susquery 1; }\n";
			$modification .= "\tif ( \$args ~* \"boot\.ini\" ) { set \$susquery 1; }\n";
			$modification .= "\tif ( \$args ~* \"ftp:\" ) { set \$susquery 1; }\n";
			$modification .= "\tif ( \$args ~* \"https?:\" ) { set \$susquery 1; }\n";
			$modification .= "\tif ( \$args ~* \"(<|%3C)script(>|%3E)\" ) { set \$susquery 1; }\n";
			$modification .= "\tif ( \$args ~* \"mosConfig_[a-zA-Z_]{1,21}(=|%3D)\" ) { set \$susquery 1; }\n";
			$modification .= "\tif ( \$args ~* \"base64_decode\(\" ) { set \$susquery 1; }\n";
			$modification .= "\tif ( \$args ~* \"%24&x\" ) { set \$susquery 1; }\n";
			$modification .= "\tif ( \$args ~* \"127\.0\" ) { set \$susquery 1; }\n";
			$modification .= "\tif ( \$args ~* \"(globals|encode|localhost|loopback)\" ) { set \$susquery 1; }\n";
			$modification .= "\tif ( \$args ~* \"(request|insert|concat|union|declare)\" ) { set \$susquery 1; }\n";
			$modification .= "\tif ( \$args ~* \"%[01][0-9A-F]\" ) { set \$susquery 1; }\n";
			$modification .= "\tif ( \$args ~ \"^loggedout=true\" ) { set \$susquery 0; }\n";
			$modification .= "\tif ( \$args ~ \"^action=jetpack-sso\" ) { set \$susquery 0; }\n";
			$modification .= "\tif ( \$args ~ \"^action=rp\" ) { set \$susquery 0; }\n";
			$modification .= "\tif ( \$http_cookie ~ \"wordpress_logged_in_\" ) { set \$susquery 0; }\n";
			$modification .= "\tif ( \$http_referer ~* \"^https?://maps\.googleapis\.com/\" ) { set \$susquery 0; }\n";
			$modification .= "\tif ( \$susquery = 1 ) { return 403; }\n";

		}

		// Process filtering of foreign characters
		if ( $input['non_english_characters'] ) {
			$modification .= "\n";
			$modification .= "\t# " . __( 'Filter Non-English Characters - Security > Settings > System Tweaks > Non-English Characters', 'it-l10n-ithemes-security-pro' ) . "\n";
			$modification .= "\tif (\$args ~* \"%[A-F][0-9A-F]\") { return 403; }\n";
		}

		return $modification;
	}

	protected static function get_valid_referers( $server_type ) {
		$valid_referers = array();

		if ( 'apache' === $server_type ) {
			$domain = ITSEC_Lib::get_domain( get_site_url() );

			if ( '*' == $domain ) {
				$valid_referers[] = $domain;
			} else {
				$valid_referers[] = "*.$domain";
			}
		} else if ( 'nginx' === $server_type ) {
			$valid_referers[] = 'server_names';
		} else {
			return array();
		}

		$valid_referers[] = 'jetpack.wordpress.com/jetpack-comment/';
		$valid_referers = apply_filters( 'itsec_filter_valid_comment_referers', $valid_referers, $server_type );

		if ( is_string( $valid_referers ) ) {
			$valid_referers = array( $valid_referers );
		} else if ( ! is_array( $valid_referers ) ) {
			$valid_referers = array();
		}

		foreach ( $valid_referers as $index => $referer ) {
			$valid_referers[$index] = preg_replace( '|^https?://|', '', $referer );
		}

		return $valid_referers;
	}
}
