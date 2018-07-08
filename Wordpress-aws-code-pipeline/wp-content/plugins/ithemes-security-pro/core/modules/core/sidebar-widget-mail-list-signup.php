<?php

class ITSEC_Settings_Page_Sidebar_Widget_Mail_List_Signup extends ITSEC_Settings_Page_Sidebar_Widget {
	public function __construct() {
		$this->id = 'mail-list-signup';
		$this->title = __( 'Download Our WordPress Security Pocket Guide', 'it-l10n-ithemes-security-pro' );
		$this->priority = 6;
		$this->settings_form = false;

		parent::__construct();
	}

	public function render( $form ) {
		wp_enqueue_script( 'itsec-mc-validate', plugins_url( '/js/mc-validate.js', __FILE__ ), array( 'jquery' ), '20160526', true );
		$this->inline_js = "(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';}(jQuery));";
		if ( function_exists( 'wp_add_inline_script' ) ) {
			wp_add_inline_script( 'itsec-mc-validate', $this->inline_js );
		} else {
			add_filter( 'script_loader_tag', array( $this, 'script_loader_tag' ), null, 2 );
		}
		?>

		<div id="mc_embed_signup">
			<form
				action="https://ithemes.us2.list-manage.com/subscribe/post?u=7acf83c7a47b32c740ad94a4e&amp;id=5176bfed9e"
				method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate"
				target="_blank" novalidate>
				<div style="text-align: center;">
					<img src="<?php echo plugins_url( 'img/security-ebook.png', __FILE__ ) ?>" width="145" height="187" alt="WordPress Security - A Pocket Guide">
				</div>
				<p><?php _e( 'Get tips for securing your site + the latest WordPress security updates, news and releases from iThemes.', 'better-wp-security' ); ?></p>

				<div id="mce-responses" class="clear">
					<div class="response" id="mce-error-response" style="display:none"></div>
					<div class="response" id="mce-success-response" style="display:none"></div>
				</div>
				<label for="mce-EMAIL" style="display: block;margin-bottom: 3px;"><?php _e( 'Email Address', 'better-wp-security' ); ?></label>
				<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="email@domain.com"> <br/><br/>
				<input type="submit" value="<?php _e( 'Subscribe', 'better-wp-security' ); ?>" name="subscribe" id="mc-embedded-subscribe" class="button button-secondary">
			</form>
		</div>
		<?php
	}

	/**
	 * Used to replicate the functionality of wp_add_inline_script
	 *
	 * @todo remove when we only support WordPress 4.5+
	 */
	public function script_loader_tag( $tag, $handle ) {
		if ( 'itsec-mc-validate' === $handle ) {
			$tag .= sprintf( "<script type='text/javascript'>\n%s\n</script>\n", $this->inline_js );
		}
		return $tag;
	}

}
new ITSEC_Settings_Page_Sidebar_Widget_Mail_List_Signup();
