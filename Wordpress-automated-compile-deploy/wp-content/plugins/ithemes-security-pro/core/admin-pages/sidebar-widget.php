<?php

/**
 * The iThemes Security Settings Page Sidebar Widget API parent class.
 */
class ITSEC_Settings_Page_Sidebar_Widget {
	/**
	 * Unique ID for the widget.
	 *
	 * This is used for form and data handling.
	 *
	 * @access protected
	 * @var string
	 */
	protected $id = '';
	
	/**
	 * User-friendly display title for the widget.
	 *
	 * @access protected
	 * @var string
	 */
	protected $title = '';
	
	/**
	 * Array of default values for the form inputs.
	 *
	 * @access protected
	 * @var array
	 */
	protected $defaults = array();
	
	/**
	 * Array of WP_Error objects.
	 *
	 * This array is filled by the validate() member function in the event of form input errors.
	 *
	 * @access protected
	 * @var array
	 */
	protected $errors = array();
	
	/**
	 * Array or status or update messages.
	 *
	 * This array is filled by the validate() member function when a widget needs to report back messages after form
	 * input submission.
	 *
	 * @access protected
	 * @var array
	 */
	protected $messages = array();

	/**
	 * Priority to register this widget at - Default: 10
	 *
	 * @access protected
	 * @var int
	 */
	protected $priority = 10;

	/**
	 * Most widgets are wrapped in a form meant for saving settings. Set this to false to avoid using that automated form.
	 *
	 * @access protected
	 * @var bool
	 */
	protected $settings_form = true;

	/**
	 * Constructor.
	 *
	 * Register the widget settings to register themselves on init. Each subclass should use the constructor to set the
	 * id, title, and defaults properties to values specific to that widget and then call parent::__construct().
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'itsec-settings-page-register-widgets', array( $this, 'register' ), $this->priority );
		add_action( 'itsec-logs-page-register-widgets', array( $this, 'register' ), $this->priority );
	}

	/**
	 * Make protected properties public read-only.
	 *
	 * This function should be left as-is in subclasses.
	 *
	 * @access public
	 *
	 * @param string $name Property to get.
	 * @return mixed Property.
	 */
	public function __get( $name ) {
		if ( in_array( $name, array( 'id', 'title', 'settings_form' ) ) ) {
			return $this->$name;
		}

		trigger_error( 'Attempted to check invalid property: ' . get_class( $this ) . "->$name", E_USER_ERROR );
	}

	/**
	 * Register the widget's settings with the settings page.
	 *
	 * This function should be left as-is in subclasses.
	 *
	 * @access public
	 */
	public function register() {
		foreach ( array( 'id', 'title' ) as $name ) {
			if ( empty( $this->$name ) ) {
				trigger_error( get_class( $this ) . " has not set the $name variable.", E_USER_ERROR );
			}
		}
		
		do_action( 'itsec-settings-page-register-widget', $this );
		do_action( 'itsec-logs-page-register-widget', $this );
	}
	
	/**
	 * Allow the widget to enqueue widget-specific scripts and styles.
	 *
	 * @access public
	 */
	public function enqueue_scripts_and_styles() {}
	
	/**
	 * Allow a widget to process an AJAX request.
	 *
	 * The widget's implementation of this function can either handle all input manually or return a data structure to
	 * be returned by the widget API. The widget's Javascript can make use of the itsec_widget_send_ajax_request()
	 * Javascript function in order to make the AJAX request. It has a request format of:
	 *     itsecSettingsPage.sendWidgetAJAXRequest( widget, data, callback );
	 *
	 * @access public
	 *
	 * @param array $data Array of data sent by the AJAX request.
	 */
	public function handle_ajax_request( $data ) {}
	
	/**
	 * Return the default settings for the widget.
	 *
	 * This function should be left as-is in subclasses. The defaults property should be set in the widget's
	 * constructor.
	 *
	 * @access public
	 *
	 * @return array List of settings.
	 */
	public function get_defaults() {
		return $this->defaults;
	}
	
	/**
	 * Render the settings form.
	 *
	 * Each subclass must implement this function in order to provide widget-specific information and inputs.
	 *
	 * @access public
	 *
	 * @param object ITSEC_Form object used to create inputs.
	 */
	public function render( $form ) {}
	
	/**
	 * Process form input by calling validate and save member functions.
	 *
	 * This function should be left as-is in subclasses unless specific processing is required, such as allowing the
	 * data to be saved on an input error.
	 *
	 * @access public
	 *
	 * @param array Array of form inputs to be processed and stored.
	 */
	public function handle_form_post( $data ) {
		$data = $this->validate( $data );
		
		if ( ! is_null( $data ) ) {
			$this->save( $data );
		}
	}
	
	/**
	 * Validate form input data.
	 *
	 * This function must be implemented in subclasses in order to provide widget-specific processing of form data.
	 * If an error is found, add a new WP_Error object to the $this->errors array for each error. Any status or update
	 * messages should be added to the $this->messages array. If the data should not be saved, such as on a critical
	 * error in the input, return a null to prevent the save function from running.
	 *
	 * @access public
	 *
	 * @param array Array of form inputs to be validated.
	 * @return array|null Array of processed form inputs that are ready to be saved or null if the input should not be
	 *                    saved.
	 */
	protected function validate( $data ) {
		return $data;
	}
	
	/**
	 * Save the validated form input data.
	 *
	 * This function should be left as-is in subclasses unless widget-specific handling is required. An example where a
	 * custom function could be useful is a widget which stores some input in different locations.
	 *
	 * @access public
	 *
	 * @param array Array of data to be saved.
	 */
	protected function save( $data ) {
		ITSEC_Storage::set( $this->id, $data );
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
		return $this->errors;
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
		return $this->messages;
	}
}
