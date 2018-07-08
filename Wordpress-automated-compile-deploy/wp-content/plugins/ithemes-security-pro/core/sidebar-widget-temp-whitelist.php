<?php

class ITSEC_Settings_Page_Sidebar_Widget_Temp_Whitelist extends ITSEC_Settings_Page_Sidebar_Widget {
	public function __construct() {
		$this->id = 'temp-whitelist';
		$this->title = __( 'Active Lockouts', 'it-l10n-ithemes-security-pro' );

		parent::__construct();
	}

	public function render( $form ) {

		/** @var ITSEC_Lockout $itsec_lockout */
		global $itsec_lockout;

		$lockouts = $itsec_lockout->get_lockouts( 'all', true );
		$users = array();
		$hosts = array();

		foreach ( $lockouts as $lockout ) {
			if ( empty( $lockout['lockout_expire_gmt'] ) ) {
				continue;
			}

			$expiration = strtotime( $lockout['lockout_expire_gmt'] );

			if ( $expiration < ITSEC_Core::get_current_time_gmt() ) {
				continue;
			}

			$data = array( $lockout['lockout_id'], $expiration );

			if ( ! empty( $lockout['lockout_username'] ) ) {
				$users[$lockout['lockout_username']] = $data;
			} else if ( ! empty( $lockout['lockout_host'] ) ) {
				$hosts[$lockout['lockout_host']] = $data;
			}
		}


		if ( empty( $users ) && empty( $hosts ) ) {
			echo '<p>' . __( 'There are no active lockouts at this time.', 'it-l10n-ithemes-security-pro' ) . "</p>\n";
			return;
		}

		if ( ! empty( $users ) ) {
			//echo '<p>' . _n( 'The following user is currently locked out from logging in:', 'The following users are currently locked out from logging in:', count( $users ), 'it-l10n-ithemes-security-pro' ) . "</p>\n";
			echo '<p><strong>' . _n( 'User', 'Users', count( $users ), 'it-l10n-ithemes-security-pro' ) . "</strong></p>\n";
			echo "<ul>\n";

			foreach ( $users as $user => $data ) {
				$label = sprintf( _x( '%1$s - Expires in %2$s', 'USER - Expires in TIME', 'it-l10n-ithemes-security-pro' ), '<strong>' . esc_html( $user ) . '</strong>', '<em>' . human_time_diff( $data[1] ) . '</em>' );
				echo '<li><label>';
				$form->add_multi_checkbox( 'users', $data[0] );
				echo " $label</label></li>\n";
			}

			echo "</ul>\n";
		}

		if ( ! empty( $hosts ) ) {
//			echo '<p>' . _n( 'The following host is currently locked out from accessing the site:', 'The following hosts are currently locked out from accessing the site:', count( $hosts ), 'it-l10n-ithemes-security-pro' ) . "</p>\n";
			echo '<p><strong>' . _n( 'Host', 'Hosts', count( $hosts ), 'it-l10n-ithemes-security-pro' ) . "</strong></p>\n";
			echo "<ul>\n";

			foreach ( $hosts as $host => $data ) {
				$label = sprintf( _x( '%1$s - Expires in %2$s', 'HOST - Expires in TIME', 'it-l10n-ithemes-security-pro' ), '<strong>' . esc_html( strtoupper( $host ) ) . '</strong>', '<em>' . human_time_diff( $data[1] ) . '</em>' );
				echo '<li><label>';
				$form->add_multi_checkbox( 'hosts', $data[0] );
				echo " $label</label></li>\n";
			}

			echo "</ul>\n";
		}

		echo '<p>';
		$form->add_submit( 'release-lockouts', array( 'value' => __( 'Release Selected Lockouts', 'it-l10n-ithemes-security-pro' ), 'class' => 'button-secondary' ) );
		echo "</p>\n";
	}

	protected function save( $data ) {
		global $itsec_lockout;

		$count = 0;

		if ( ! empty( $data['users'] ) && is_array( $data['users'] ) ) {
			foreach ( $data['users'] as $id ) {
				$result = $itsec_lockout->release_lockout( $id );
				$count++;

				if ( ! $result ) {
					$this->errors[] = sprintf( __( 'An unknown error prevented releasing the lockout the user with a lockout ID of %d', 'it-l10n-ithemes-security-pro' ), $id );
				}
			}
		}

		if ( ! empty( $data['hosts'] ) && is_array( $data['hosts'] ) ) {
			foreach ( $data['hosts'] as $id ) {
				$result = $itsec_lockout->release_lockout( $id );
				$count++;

				if ( ! $result ) {
					$this->errors[] = sprintf( __( 'An unknown error prevented releasing the lockout the host with a lockout ID of %d', 'it-l10n-ithemes-security-pro' ), $id );
				}
			}
		}

		if ( empty( $this->errors ) ) {
			if ( $count > 0 ) {
				$this->messages[] = _n( 'Successfully removed the selected lockout.', 'Sucessfully remove the selected lockouts.', $count, 'it-l10n-ithemes-security-pro' );
			} else {
				$this->messages[] = __( 'No lockouts were selected for removal.', 'it-l10n-ithemes-security-pro' );
			}
		}
	}
}
new ITSEC_Settings_Page_Sidebar_Widget_Active_Lockouts();
