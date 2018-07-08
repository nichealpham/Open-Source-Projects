<?php

final class ITSEC_WordPress_Tweaks_Config_Generators {
	public static function filter_wp_config_modification( $modification ) {
		$input = ITSEC_Modules::get_settings( 'wordpress-tweaks' );

		if ( $input['file_editor'] ) {
			$modification .= "define( 'DISALLOW_FILE_EDIT', true ); // " . __( 'Disable File Editor - Security > Settings > WordPress Tweaks > File Editor', 'it-l10n-ithemes-security-pro' ) . "\n";
		}

		return $modification;
	}

	public static function filter_litespeed_server_config_modification( $modification ) {
		return self::filter_apache_server_config_modification( $modification, 'litespeed' );
	}

	public static function filter_apache_server_config_modification( $modification, $server = 'apache' ) {
		$input = ITSEC_Modules::get_settings( 'wordpress-tweaks' );

		$rewrites = '';


		if ( 2 == $input['disable_xmlrpc'] ) {
			$modification .= "\n";
			$modification .= "\t# " . __( 'Disable XML-RPC - Security > Settings > WordPress Tweaks > XML-RPC', 'it-l10n-ithemes-security-pro' ) . "\n";
			$modification .= "\t<files xmlrpc.php>\n";

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

		if ( $input['comment_spam'] ) {
			$valid_referers = self::get_valid_referers( 'apache' );

			$rewrites .= "\n";
			$rewrites .= "\t\t# " . __( 'Reduce Comment Spam - Security > Settings > WordPress Tweaks > Comment Spam', 'it-l10n-ithemes-security-pro' ) . "\n";
			$rewrites .= "\t\tRewriteCond %{REQUEST_METHOD} POST\n";
			$rewrites .= "\t\tRewriteCond %{REQUEST_URI} /wp-comments-post\.php\$\n";

			if ( empty( $valid_referers ) || in_array( '*', $valid_referers ) ) {
				$rewrites .= "\t\tRewriteCond %{HTTP_USER_AGENT} ^$\n";
			} else {
				foreach ( $valid_referers as $index => $referer ) {
					if ( '*.' == substr( $referer, 0, 2 ) ) {
						$referer = '([^/]+.)?' . substr( $referer, 2 );
					}

					$referer = str_replace( '.', '\.', $referer );
					$referer = rtrim( $referer, '/' );

					$valid_referers[$index] = $referer;
				}
				$valid_referers = implode( '|', $valid_referers );

				$rewrites .= "\t\tRewriteCond %{HTTP_USER_AGENT} ^$ [OR]\n";
				$rewrites .= "\t\tRewriteCond %{HTTP_REFERER} !^https?://($valid_referers)(/|$) [NC]\n";
			}

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
		$input = ITSEC_Modules::get_settings( 'wordpress-tweaks' );


		if ( 2 == $input['disable_xmlrpc'] ) {
			$modification .= "\n";
			$modification .= "\t# " . __( 'Disable XML-RPC - Security > Settings > WordPress Tweaks > XML-RPC', 'it-l10n-ithemes-security-pro' ) . "\n";
			$modification .= "\tlocation ~ xmlrpc.php { deny all; }\n";
		}

		if ( $input['comment_spam'] ) {
			$valid_referers = self::get_valid_referers( 'nginx' );

			$modification .= "\n";
			$modification .= "\t# " . __( 'Reduce Comment Spam - Security > Settings > WordPress Tweaks > Comment Spam', 'it-l10n-ithemes-security-pro' ) . "\n";
			$modification .= "\tlocation = /wp-comments-post.php {\n";
			$modification .= "\t\tlimit_except POST { deny all; }\n";
			$modification .= "\t\tif (\$http_user_agent ~ \"^$\") { return 403; }\n";

			if ( ! empty( $valid_referers ) && ! in_array( '*', $valid_referers ) ) {
				$modification .= "\t\tvalid_referers " . implode( ' ', $valid_referers ) . ";\n";
				$modification .= "\t\tif (\$invalid_referer) { return 403; }\n";
			}

			$modification .= "\t}\n";
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
