<?php

final class ITSEC_Ban_Users_Config_Generators {
	public static function get_server_config_default_blacklist_rules( $server_type ) {
		$rules = '';
		
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-file.php' );
		
		$file = plugin_dir_path( __FILE__ ) . "lists/hackrepair-$server_type.inc";
		
		if ( ITSEC_Lib_File::is_file( $file ) ) {
			$default_list = ITSEC_Lib_File::read( $file );
			
			if ( ! empty( $default_list ) ) {
				$default_list = preg_replace( '/^/m', "\t", $default_list );
				
				$rules .= "\n";
				$rules .= "\t# " . __( 'Enable HackRepair.com\'s blacklist feature - Security > Settings > Banned Users > Default Blacklist', 'it-l10n-ithemes-security-pro' ) . "\n";
				$rules .= $default_list;
			}
		}
		
		return $rules;
	}
	
	public static function get_server_config_ban_hosts_rules( $server_type ) {
		$host_list = ITSEC_Modules::get_setting( 'ban-users', 'host_list', array() );
		
		if ( ! is_array( $host_list ) || empty( $host_list ) ) {
			return '';
		}
		
		if ( ! class_exists( 'ITSEC_Lib_IP_Tools' ) ) {
			require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-ip-tools.php' );
		}
		
		
		$host_rules = '';
		$set_env_rules = '';
		$deny_rules = '';
		$require_rules = '';
		
		// process hosts list
		foreach ( $host_list as $host ) {
			$host = ITSEC_Lib_IP_Tools::ip_wild_to_ip_cidr( trim( $host ) );
			
			if ( empty( $host ) ) {
				continue;
			}
			
			if ( ITSEC_Lib::is_ip_whitelisted( $host ) ) {
				/**
				 * @todo warn the user the ip to be banned is whitelisted
				 */
				continue;
			}
			
			
			if ( in_array( $server_type, array( 'apache', 'litespeed' ) ) ) {
				$converted_host = ITSEC_Lib_IP_Tools::ip_cidr_to_ip_regex( $host );
				
				if ( empty( $converted_host ) ) {
					continue;
				}
				
				$set_env_rules .= "\tSetEnvIF REMOTE_ADDR \"^$converted_host$\" DenyAccess\n"; // Ban IP
				$set_env_rules .= "\tSetEnvIF X-FORWARDED-FOR \"^$converted_host$\" DenyAccess\n"; // Ban IP from a proxy
				$set_env_rules .= "\tSetEnvIF X-CLUSTER-CLIENT-IP \"^$converted_host$\" DenyAccess\n"; // Ban IP from a load balancer
				$set_env_rules .= "\n";
				
				$require_rules .= "\t\t\tRequire not ip $host\n";
				$deny_rules .= "\t\tDeny from $host\n";
			} else if ( 'nginx' === $server_type ) {
				$host_rules .= "\tdeny $host;\n";
			}
		}
		
		
		$rules = '';
		
		if ( 'apache' === $server_type ) {
			if ( ! empty( $set_env_rules ) ) {
				$rules .= "\n";
				$rules .= "\t# " . __( 'Ban Hosts - Security > Settings > Banned Users', 'it-l10n-ithemes-security-pro' ) . "\n";
				$rules .= $set_env_rules;
				$rules .= "\t<IfModule mod_authz_core.c>\n";
				$rules .= "\t\t<RequireAll>\n";
				$rules .= "\t\t\tRequire all granted\n";
				$rules .= "\t\t\tRequire not env DenyAccess\n";
				$rules .= $require_rules;
				$rules .= "\t\t</RequireAll>\n";
				$rules .= "\t</IfModule>\n";
				$rules .= "\t<IfModule !mod_authz_core.c>\n";
				$rules .= "\t\tOrder allow,deny\n";
				$rules .= "\t\tAllow from all\n";
				$rules .= "\t\tDeny from env=DenyAccess\n";
				$rules .= $deny_rules;
				$rules .= "\t</IfModule>\n";
			}
		} else if ( 'litespeed' === $server_type ) {
			if ( ! empty( $set_env_rules ) ) {
				$rules .= "\n";
				$rules .= "\t# " . __( 'Ban Hosts - Security > Settings > Banned Users', 'it-l10n-ithemes-security-pro' ) . "\n";
				$rules .= $set_env_rules;
				$rules .= "\t<IfModule mod_litespeed.c>\n";
				$rules .= "\t\tOrder allow,deny\n";
				$rules .= "\t\tAllow from all\n";
				$rules .= "\t\tDeny from env=DenyAccess\n";
				$rules .= $deny_rules;
				$rules .= "\t</IfModule>\n";
			}
		} else if ( 'nginx' === $server_type ) {
			if ( ! empty( $host_rules ) ) {
				$rules .= "\n";
				$rules .= "\t# " . __( 'Ban Hosts - Security > Settings > Banned Users', 'it-l10n-ithemes-security-pro' ) . "\n";
				$rules .= $host_rules;
			}
		}
		
		return $rules;
	}
	
	public static function get_server_config_ban_user_agents_rules( $server_type ) {
		$agent_list = ITSEC_Modules::get_setting( 'ban-users', 'agent_list', array() );
		
		if ( ! is_array( $agent_list ) || empty( $agent_list ) ) {
			return '';
		}
		
		
		$agent_rules = '';
		$rewrite_rules = '';
		
		foreach ( $agent_list as $index => $agent ) {
			$agent = trim( $agent );
			
			if ( empty( $agent ) ) {
				continue;
			}
			
			
			$agent = preg_quote( $agent );
			
			if ( in_array( $server_type, array( 'apache', 'litespeed' ) ) ) {
				$agent = str_replace( ' ', '\\ ', $agent );
				$rewrite_rules .= "\t\tRewriteCond %{HTTP_USER_AGENT} ^$agent [NC,OR]\n";
			} else if ( 'nginx' === $server_type ) {
				$agent = str_replace( '"', '\\"', $agent );
				$agent_rules .= "\tif (\$http_user_agent ~* \"^$agent\") { return 403; }\n";
			}
		}
		
		if ( in_array( $server_type, array( 'apache', 'litespeed' ) ) && ! empty( $rewrite_rules ) ) {
			$rewrite_rules = preg_replace( "/\[NC,OR\]\n$/", "[NC]\n", $rewrite_rules );
			
			$agent_rules .= "\t<IfModule mod_rewrite.c>\n";
			$agent_rules .= "\t\tRewriteEngine On\n";
			$agent_rules .= $rewrite_rules;
			$agent_rules .= "\t\tRewriteRule ^.* - [F]\n";
			$agent_rules .= "\t</IfModule>\n";
		}
		
		
		$rules = '';
		
		if ( ! empty( $agent_rules ) ) {
			$rules .= "\n";
			$rules .= "\t# " . __( 'Ban User Agents - Security > Settings > Banned Users', 'it-l10n-ithemes-security-pro' ) . "\n";
			$rules .= $agent_rules;
		}
		
		return $rules;
	}
}
