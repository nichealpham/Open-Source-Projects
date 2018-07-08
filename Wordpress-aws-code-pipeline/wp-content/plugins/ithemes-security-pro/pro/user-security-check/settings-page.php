<?php

final class ITSEC_User_Security_Check_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'user-security-check';
		$this->title = __( 'User Security Check', 'it-l10n-ithemes-security-pro' );
		// @todo Description.
		$this->description = __( 'Every user on your site affects overall security. See how your users might be affecting your security and take action when needed.', 'it-l10n-ithemes-security-pro' );
		$this->type = 'recommended';
		$this->pro = true;
		$this->can_save = false;

		parent::__construct();
	}

	protected function render_description( $form ) {
		// @todo Description.
?>
	<p><?php printf( __( '', 'it-l10n-ithemes-security-pro' ) ); ?></p>
<?php

	}

	protected function render_settings( $form ) {
		require_once( 'class-itsec-wp-users-list-table.php' );
		wp_enqueue_script( 'itsec-user-security-check', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery', 'wp-util', 'itsec-settings-page-script' ), null, true );
		$wp_list_table = new ITSEC_WP_Users_List_Table();

		$wp_list_table->prepare_items();

		$wp_list_table->views();
		$wp_list_table->search_box( __( 'Search Users' ), 'user' );
		wp_nonce_field( 'itsec-user-security-check-user-search', '_nonce-itsec-user-security-check', false );
		echo '<div id="itsec-user-table">';
		$wp_list_table->display();
		echo '</div>';
	}
}

new ITSEC_User_Security_Check_Settings_Page();
