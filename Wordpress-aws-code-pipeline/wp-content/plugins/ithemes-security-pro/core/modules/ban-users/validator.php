<?php

class ITSEC_Ban_Users_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'ban-users';
	}
	
	protected function sanitize_settings() {
		$this->sanitize_setting( 'bool', 'default', __( 'Default Blacklist', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'enable_ban_lists', __( 'Ban Lists', 'it-l10n-ithemes-security-pro' ) );
		
		$this->sanitize_setting( 'newline-separated-ips', 'host_list', __( 'Ban Hosts', 'it-l10n-ithemes-security-pro' ) );
		
		if ( is_array( $this->settings['host_list'] ) ) {
			require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-ip-tools.php' );
			
			$whitelisted_hosts = array();
			$current_ip = ITSEC_Lib::get_ip();
			
			foreach ( $this->settings['host_list'] as $host ) {
				if ( is_user_logged_in() && ITSEC_Lib_IP_Tools::intersect( $current_ip, ITSEC_Lib_IP_Tools::ip_wild_to_ip_cidr( $host ) ) ) {
					$this->set_can_save( false );
					
					/* translators: 1: input name, 2: invalid host */
					$this->add_error( sprintf( __( 'The following host in %1$s matches your current IP and cannot be banned: %2$s', 'it-l10n-ithemes-security-pro' ), __( 'Ban Hosts', 'it-l10n-ithemes-security-pro' ), $host ) );
					
					continue;
				}
				
				if ( ITSEC_Lib::is_ip_whitelisted( $host ) ) {
					$whitelisted_hosts[] = $host;
				}
			}
			
			if ( ! empty( $whitelisted_hosts ) ) {
				$this->set_can_save( false );
				
				/* translators: 1: input name, 2: invalid host list */
				$this->add_error( wp_sprintf( _n( 'The following IP in %1$s is whitelisted and cannot be banned: %2$l', 'The following IPs in %1$s are whitelisted and cannot be banned: %2$l', count( $whitelisted_hosts ), 'it-l10n-ithemes-security-pro' ), __( 'Ban Hosts', 'it-l10n-ithemes-security-pro' ), $whitelisted_hosts ) );
			}
		}
		
		$this->sanitize_setting( array( $this, 'sanitize_agent_list_entry' ), 'agent_list', __( 'Ban User Agents', 'it-l10n-ithemes-security-pro' ) );
	}
	
	protected function sanitize_agent_list_entry( $entry ) {
		return trim( sanitize_text_field( $entry ) );
	}
	
	protected function validate_settings() {
		if ( ! $this->can_save() ) {
			return;
		}
		
		
		$previous_settings = ITSEC_Modules::get_settings( $this->get_id() );
		
		foreach ( $this->settings as $key => $val ) {
			if ( ! isset( $previous_settings[$key] ) || $previous_settings[$key] != $val ) {
				ITSEC_Response::regenerate_server_config();
				break;
			}
		}
	}
}

ITSEC_Modules::register_validator( new ITSEC_Ban_Users_Validator() );
