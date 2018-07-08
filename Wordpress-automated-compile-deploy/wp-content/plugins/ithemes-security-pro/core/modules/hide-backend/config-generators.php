<?php

final class ITSEC_Hide_Backend_Config_Generators {
	public static function filter_apache_server_config_modification( $modification ) {
		$settings = ITSEC_Modules::get_settings( 'hide-backend' );

		if ( ! $settings['enabled'] ) {
			return $modification;
		}

		$home_root = ITSEC_Lib::get_home_root();

		$modification .= "\n";
		$modification .= "\t# " . __( 'Enable the hide backend feature - Security > Settings > Hide Login Area > Hide Backend', 'it-l10n-ithemes-security-pro' ) . "\n";
		$modification .= "\tRewriteRule ^($home_root)?{$settings['slug']}/?$ {$home_root}wp-login.php [QSA,L]\n";

		if ( 'wp-register.php' != $settings['register'] ) {
			$modification .= "\tRewriteRule ^($home_root)?{$settings['register']}/?$ {$home_root}{$settings['slug']}?action=register [QSA,L]\n";
		}

		return $modification;
	}

	public static function filter_nginx_server_config_modification( $modification ) {
		$settings = ITSEC_Modules::get_settings( 'hide-backend' );

		if ( ! $settings['enabled'] ) {
			return $modification;
		}

		$home_root = ITSEC_Lib::get_home_root();

		$modification .= "\n";
		$modification .= "\t# " . __( 'Enable the hide backend feature - Security > Settings > Hide Login Area > Hide Backend', 'it-l10n-ithemes-security-pro' ) . "\n";
		$modification .= "\trewrite ^($home_root)?{$settings['slug']}/?$ {$home_root}wp-login.php?\$query_string break;\n";

		if ( 'wp-register.php' != $settings['register'] ) {
			$modification .= "\trewrite ^($home_root)?{$settings['register']}/?$ {$home_root}{$settings['slug']}?action=register break;\n";
		}

		return $modification;
	}
}
