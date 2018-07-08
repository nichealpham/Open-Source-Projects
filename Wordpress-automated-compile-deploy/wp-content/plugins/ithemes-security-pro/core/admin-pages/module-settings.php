<?php

/**
 * The iThemes Security Module Settings Page API parent class.
 */
class ITSEC_Module_Settings_Page {
	/**
	 * Unique ID for the module.
	 *
	 * This is used to store the module's data and generate form inputs.
	 *
	 * @access protected
	 * @var string
	 */
	protected $id = '';

	/**
	 * User-friendly display title for the module.
	 *
	 * @access protected
	 * @var string
	 */
	protected $title = '';

	/**
	 * User-friendly display description for the module.
	 *
	 * @access protected
	 * @var string
	 */
	protected $description = '';

	/**
	 * Whether the module is categorized as additional or recommended.
	 *
	 * @access protected
	 * @var string
	 */
	protected $type = 'recommended'; // "additional" or "recommended"

	/**
	 * Whether the settings require resaving after activation in order to fully-activate the module.
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $requires_resave_after_activation = false;

	/**
	 * Whether the module is part of iThemes Security Pro or not.
	 *
	 * @access protected
	 * @var bool
	 */
	protected $pro = false;

	/**
	 * Whether the module settings can be saved.
	 *
	 * @access protected
	 * @var bool
	 */
	protected $can_save = true;

	/**
	 * Whether the module settings should be redrawn on save.
	 *
	 * @access protected
	 * @var bool
	 */
	protected $redraw_on_save = false;

	/**
	 * Whether the module is an iThemes Security Pro module being shown as an upsell or not.
	 *
	 * @access protected
	 * @var bool
	 */
	protected $upsell = false;

	/**
	 * URL to use for upsell if this is one.
	 *
	 * @access protected
	 * @var string
	 */
	protected $upsell_url = 'https://ithemes.com/security/';

	/**
	 * Whether the module is for informational purposes only - no settings, no actions
	 *
	 * @access protected
	 * @var bool
	 */
	protected $information_only = false;


	/**
	 * Constructor.
	 *
	 * Register the module settings to register themselves on init. Each subclass should use the constructor to set the
	 * id, title, description, type, pro, can_save, and redraw_on_save properties to values specific to that module and then call
	 * parent::__construct().
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'itsec-settings-page-register-modules', array( $this, 'register' ) );
	}

	/**
	 * Make protected properties public read-only.
	 *
	 * This function should be left as-is in subclasses.
	 *
	 * @access public
	 *
	 * @param string $name Property to get.
	 *
	 * @return mixed Property.
	 */
	public function __get( $name ) {
		if ( in_array( $name, array( 'id', 'title', 'description', 'type', 'pro', 'can_save', 'redraw_on_save', 'upsell', 'upsell_url', 'information_only' ) ) ) {
			return $this->$name;
		}

		trigger_error( 'Attempted to check invalid property: ' . get_class( $this ) . "->$name", E_USER_ERROR );
	}

	/**
	 * Register the module's settings with the settings page.
	 *
	 * This function should be left as-is in subclasses.
	 *
	 * @access public
	 */
	public function register() {
		foreach ( array( 'id', 'title', 'description' ) as $name ) {
			if ( empty( $this->$name ) ) {
				trigger_error( get_class( $this ) . " has not set the $name variable.", E_USER_ERROR );
			}
		}

		do_action( 'itsec-settings-page-register-module', $this );
	}

	/**
	 * Allow the module to enqueue module-specific scripts and styles.
	 *
	 * @access public
	 */
	public function enqueue_scripts_and_styles() {}

	/**
	 * Allow a module to process an AJAX request.
	 *
	 * The module's implementation of this function can either handle all input manually or return a data structure to
	 * be returned by the module API. The module's Javascript can make use of the itsec_module_send_ajax_request()
	 * Javascript function in order to make the AJAX request. It has a request format of:
	 *     itsecSettingsPage.sendModuleAJAXRequest( module, data, callback );
	 *
	 * @access public
	 *
	 * @param array $data Array of data sent by the AJAX request.
	 */
	public function handle_ajax_request( $data ) {}

	/**
	 * Return the settings for the module.
	 *
	 * @access public
	 *
	 * @return array List of settings.
	 */
	public function get_settings() {
		return ITSEC_Modules::get_settings( $this->id );
	}

	/**
	 * Render the module's settings content.
	 *
	 * This function should be left as-is in subclasses.
	 *
	 * @access public
	 *
	 * @param ITSEC_Form $form ITSEC_Form object used to create inputs.
	 */
	public function render( $form ) {

?>
	<div class="itsec-settings-module-description">
		<?php $this->render_description( $form ); ?>
	</div>
	<div class="itsec-settings-module-settings">
		<?php $this->render_settings( $form ); ?>
	</div>
<?php

	}

	/**
	 * Render the module description.
	 *
	 * The description is shown whether the module is active or not. Ensure that the description adequately informs the
	 * user of the value of the module without requiring them to see the actual settings.
	 *
	 * @access protected
	 *
	 * @param object $form ITSEC_Form object used to create inputs.
	 */
	protected function render_description( $form ) {

?>
	<p>Example module description.</p>
<?php

	}

	/**
	 * Render the module settings.
	 *
	 * The inputs and input descriptions should be output in this function. This output is hidden when a module is
	 * deactivated.
	 *
	 * @access protected
	 *
	 * @param ITSEC_Form $form ITSEC_Form object used to create inputs.
	 */
	protected function render_settings( $form ) {

?>
	<table class="form-table itsec-settings-section">
		<tbody>
			<tr>
				<th><label>Setting 1</label></th>
				<td>
					<?php $form->add_text( 'setting_1' ); ?>
				</td>
			</tr>
			<tr>
				<th><label>Setting 2</label></th>
				<td>
					<?php $form->add_text( 'setting_2' ); ?>
				</td>
			</tr>
		</tbody>
	</table>
<?php

	}

	/**
	 * Process form input.
	 *
	 * This function should be left as-is in subclasses unless specific processing is required.
	 *
	 * @access public
	 *
	 * @param array $data Array of form inputs to be processed and stored.
	 */
	public function handle_form_post( $data ) {
		ITSEC_Modules::set_settings( $this->id, $data );
	}

	/**
	 * Returns the errors array.
	 *
	 * This function should be left as-is in subclasses.
	 *
	 * @access public
	 *
	 * @return array Array of WP_Error objects.
	 */
	public function get_errors() {
		$validator = ITSEC_Modules::get_validator( $this->id );

		if ( is_null( $validator ) ) {
			return array();
		}

		return $validator->get_errors();
	}

	/**
	 * Returns the messages array.
	 *
	 * This function should be left as-is in subclasses.
	 *
	 * @access public
	 *
	 * @return array Array of status or update messages.
	 */
	public function get_messages() {
		$validator = ITSEC_Modules::get_validator( $this->id );

		if ( is_null( $validator ) ) {
			return array();
		}

		return $validator->get_messages();
	}
}
