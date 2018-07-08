<?php

// Security Check
defined('ABSPATH') or die();

// Directory Seperator
if( !defined( 'DS' ) ){
	
	PHP_OS == "Windows" || PHP_OS == "WINNT" ? define("DS", "\\") : define("DS", "/");
	
} 

/**
 *
 * The starter file that holds everything togather.
 *
 * @package BootStart_1_0_0
 *
 * @since version 0.1.1
 *
 * */

/**
 *
 * Holds almost all the functionality that this nano framework supports.
 *
 *
 * We will eventually add more detailed description later.
 *
 * */
abstract class FM_BootStart{

	/**
	 *
	 * @var string $name name of the plugin
	 *
	 * */
	 public $name;

	/**
	 *
	 * @var string $prefix Plugin wide prefix that will be used to differentiate from other plugin / or system vars
	 *
	 * */
	 public $prefix;

	/**
	 *
	 * @var string $path Absolute path of the plugin.
	 *
	 * */
	 protected $path;

	/**
	 *
	 * @var array $SCD Short Code Data
	 *
	 * */
	 protected $SCD;

	/**
	 *
	 * @var object $options The object of the options class
	 *
	 * */
	public $options;

	/**
	 *
	 * @var string $upload_path :: This variable holds the path of the default upload folder
	 *
	 * */
	 public $upload_path;
	
	/**
	 *
	 * @var string $upload_url :: This variable holds the url of the default upload folder
	 *
	 * */
	 public $upload_url;

	/**
	 *
	 * @var array $menu :: Defines how the menu would be
	 *
	 * */
	protected $menu_data;
	
	/**
	 *
	 * Constructor function
	 *
	 *
	 * This function does the works that every plugin must do like checking ABSPATH,
	 * triggering activation and deactivation hooks etc.
	 *
	 * @todo Add an uninstall function
	 *
	 * */
	 function __construct($name){

		// Assigning name
		$this->name = trim($name);

		// Assigning prefix
		$this->prefix = str_replace( ' ', '-', strtolower(trim($this->name)) );

		// Assigning path
		$this->path = __FILE__;

		// Assigning DevEnv
		$this->devEnv = false;

		// Upload folder path
		$upload = wp_upload_dir();
		$this->upload_path = $upload['basedir'] . DS . $this->prefix;

		// Upload folder url
		$upload = wp_upload_dir();
		$this->upload_url = $upload['baseurl'] . '/' . $this->prefix;

		// Setting php.ini variables
		$this->php_ini_settings();

		// Loading Options
		// Options
		$this->options = get_option($this->prefix);
		if(empty($this->options)) $this->options = array( // Setting up default values
			'file_manager_settings' => array(
				'show_url_path' => 'show',
				'language' => array('code' => 'LANG'),
				'size' => array(
                    'width' => 'auto',
                    'height' => 600
                ),
			),
		);
		register_shutdown_function(array(&$this, 'save_options'));
		
		//auto::  $this->options = new FM_OptionsManager($this->name);
		
		// Creating upload folder.
	   	$this->upload_folder();
		
		// Frontend asset loading
		add_action('wp_enqueue_scripts', array(&$this, 'assets') );
		
		// Dashboard asset loading
		add_action('admin_enqueue_scripts', array(&$this, 'admin_assets') );

		// Adding a menu at admin area
		add_action( 'admin_menu', array(&$this, 'menu') );

		// Shortcode hook
		add_action( 'init', array(&$this, 'shortcode') );
		
	 }

	/**
	 *
	 * Set the all necessary variables of php.ini file.
	 *
	 * @todo Add some php.ini variables.
	 *
	 * */
	 protected function php_ini_settings(){

		// This should have a standard variable list.
		/**
		 * 
		 * ## Increase file upload limit
		 * ## Turn on error if of php if debugging variable is defined and set to true.
		 * 
		 * */
		ini_set('post_max_size', '128M');
		ini_set('upload_max_filesize', '128M');
	 }

	/**
	 *
	 * Loads frontend assets
	 *
	 * */
	 public function assets(){
		
		$this->elfinder_assets(); // Loads all the assets necessary for elFinder
		
		// Including front-style.css
		wp_register_style('fm-front-style', $this->url('css/front-style.css'), false);

		// Including front-script.js
		wp_register_script('fm-front-script', $this->url('js/front-script.js'), array(), '1.0.0', true );
		 
	 }

	/*
	 *
	 * Loads the backend / admin assets
	 *
	 * */
	 public function admin_assets(){
		
		$this->elfinder_assets(); // Loads all the assets necessary for elFinder
		
		// Including admin-style.css
		wp_register_style( 'fmp-admin-style', $this->url('css/admin-style.css') );

		// Including admin-script.js
		wp_register_script( 'fmp-admin-script', $this->url('js/admin-script.js'), array('jquery') );

	}
	
	/**
	 * 
	 * @function elfinder_assets
	 * @description Registers all the elfinder assets
	 * 
	 * */
	public function elfinder_assets(){
		
		$jquery_ui_url = $this->url('jquery-ui-1.11.4/jquery-ui.min.css');
		$jquery_ui_url = apply_filters('fm_jquery_ui_theme_hook', $jquery_ui_url);
		
		// Jquery UI CSS
		wp_register_style( 'fmp-jquery-ui-css',  $jquery_ui_url);
		
		// elFinder CSS
		wp_register_style( 'fmp-elfinder-css', $this->url('elFinder/css/elfinder.min.css'), array('fmp-jquery-ui-css') );
		
		// elFinder theme CSS
		if($this->url('jquery-ui-1.11.4/jquery-ui.min.css') == $jquery_ui_url ) wp_register_style( 'fmp-elfinder-theme-css', $this->url('elFinder/css/theme.css'), array('fmp-elfinder-css') );
		
		// elFinder Scripts depends on jQuery UI core, selectable, draggable, droppable, resizable, dialog and slider.
		wp_register_script( 'fmp-elfinder-script', $this->url('elFinder/js/elfinder.full.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-selectable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-resizable', 'jquery-ui-dialog', 'jquery-ui-slider', 'jquery-ui-tabs') );
		
	}

	/**
	 *
	 * Adds a sidebar/sub/top menu
	 *
	 * */
	public function menu(){

		if( empty( $this->menu_data ) ) return;

		if($this->menu_data['type'] == 'menu'){
						
			$capabilities = 'administrator';
			$capabilities = apply_filters('fm_capabilities', $capabilities);
			
			// Main Menu
			add_menu_page( $this->name, $this->name, $capabilities, $this->prefix, array(&$this, 'admin_panel'), $this->url('img/icon-24x24.png'), 7 );
			
			// Settings Page
			add_submenu_page( $this->prefix, 'File Manager Settings', 'Settings', 'manage_options', $this->zip( 'File Manager Settings' ), array( &$this, 'settings' ) );

			if(!defined('FILE_MANAGER_PREMIUM')){
				add_submenu_page( 
					'file-manager', // Parent Slug
					'File Manager Permission System(pro)', // Page title
					'Permission System', // Menu title
					'manage_options', // User capabilities
					'file-manager-permission-system', // Menu Slug
					create_function( '', 'include plugin_dir_path( __FILE__ ) . ".." . DS . "views" . DS . "admin" . DS . "permission_system.php";' )
				);
			}
			
		}

	}

	/**
	 *
	 * Adds an admin page to the backend.
	 *
	 * */
	 public function admin_panel(){

		$this->render('', 'admin' . DS . 'index');

	}
	
	/**
	 * Adds a settings page
	 * 
	 * */
	public function settings(){
		
		if(!current_user_can('manage_options')) die( $this->render('', 'access-denied') );

		$this->render('', 'admin' . DS . 'settings');
		
	}

	/**
	 *
	 * Absolute URL finder
	 *
	 * @param string $string the relative url
	 *
	 * */
   	 public function url($string){

		return plugins_url( '/' . $this->prefix . '/' . $string );

	}

	/**
	 *
	 * Adds ajax hooks and functions automatically
	 *
	 *
	 * @param string $name Name of the function
	 *
	 * @param bool $guest Should the function work for guests *Default: false*
	 *
	 * */
	 public function add_ajax($name, $guest = false){

		// Adds admin ajax
		$hook = 'wp_ajax_'.$name;
		add_action( $hook, array($this, $name) );

		// Allow guests
		if(!$guest) return;

		$hook = 'wp_ajax_nopriv_'.$name;
		add_action( $hook, array($this, $name) );

	 }

	/**
	 *
	 * Get the script for ajax request
	 *
	 *
	 * @param string $name Name of the ajax request fuction.
	 *
	 * @param array $data Post data to send
	 *
	 * @return string $script A jQuery.post() request function to show on the the main page.
	 *
	 * */
	 public function get_ajax_script($name, $data){

		$data['action'] = $name;

	 ?>

		jQuery.post(
			'<?php echo admin_url('admin-ajax.php'); ?>',
			<?php echo json_encode($data);?>
			'<?php echo $name; ?>'
		);

	 <?php

	}

	/**
	 *
	 * Adds Shortcodes
	 *
	 * */
	 public function shortcode(){

		if( empty($this->STD) ) return;

		foreach ( $this->STD as $std ){

			$ret = add_shortcode($std, array($this, $std.'_view') );

		}

	 }

	/**
	 *
	 * Includes a view file form the view folder which matches the called functions name
	 *
	 * @param string $view_file Name of the view file.
	 *
	 * */
	 protected function render($data=null, $view_file = null){

		if($view_file == null){

			// Generates file name from function name
			$trace = debug_backtrace();
			$view_file = $trace[1]['function'].'.php';

		} else {

			$view_file .='.php';

		}

		include( plugin_dir_path( __FILE__ ) . ".." . DS . "views" . DS . $view_file);

	 }

	/**
	 *
	 * @function upload_folder Checks if the upload folder is present. If not creates a upload folder.
	 *
	 * */
	protected function upload_folder(){

		// Creats upload directory for this specific plugin
		if( !is_dir($this->upload_path ) ) mkdir( $this->upload_path , 0777 );

	}
	
	/**
	 * 
	 * string compression function
	 * 
	 * */
	public function zip($string){
		
		$string = trim($string);
		$string = str_replace(' ', '-', $string);
		$string = strtolower($string);
		return $string;
		
	}
	
	/**
	 * 
	 * @function save_options
	 * 
	 * */
	public function save_options(){
		update_option($this->prefix, $this->options);
	}
}
