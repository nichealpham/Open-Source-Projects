<?php
/**
 *
 * Plugin Name: File Manager
 * Author: Aftabul Islam
 * Author URI: http://www.giribaz.com
 * Version: 5.0.1
 * Author Email: toaihimel@gmail.com
 * License: GPLv2
 * Description: Manage your file the way you like. You can upload, delete, copy, move, rename, compress, extract files. You don't need to worry about ftp. It is realy simple and easy to use.
 *
 * */

// Directory Seperator
if( !defined( 'DS' ) ){
	
	PHP_OS == "Windows" || PHP_OS == "WINNT" ? define("DS", "\\") : define("DS", "/");
	
} 

// Including elFinder class
require_once('elFinder' . DS . 'elFinder.php');

// Including bootstarter
require_once('BootStart' . DS . 'BootStart.php');

// Including other necessary files
require_once('inc/__init__.php');

class FM extends FM_BootStart {
	
	/**
	 * 
	 * @var $version Wordpress file manager plugin version
	 * 
	 * */
	public $version;
	
	/**
	 * 
	 * @var $site Site url
	 * 
	 * */
	public $site;
	
	/**
	 * 
	 * @var $giribaz_landing_page Landing page for giribaz
	 * 
	 * */
	public $giribaz_landing_page;
	
	/**
	 * 
	 * @var $support_page Support ticket page
	 * 
	 * */
	public $support_page;
	
	/**
	 * 
	 * @var $feedback_page Feedback page
	 * 
	 * */
	public $feedback_page;
	
	/**
	 * 
	 * @var $file_manager_view_path View path of file manager
	 * 
	 * */
	public $file_manager_view_path;

	public function __construct($name){
		
		$this->version = '5.0.1';
		$this->site = 'http://www.giribaz.com';
		$this->giribaz_landing_page = 'http://www.giribaz.com/wordpress-file-manager-plugin';
		$this->support_page = 'http://giribaz.com/support/';
		$this->feedback_page = 'https://wordpress.org/support/plugin/file-manager/reviews/';
		$this->file_manager_view_path = plugin_dir_path(__FILE__);
		
		// Adding Menu
		$this->menu_data = array(
			'type' => 'menu',
		);

		// Adding Ajax
		$this->add_ajax('connector'); // elFinder ajax call
		$this->add_ajax('fm_site_backup'); // Site backup function invoked

		parent::__construct($name);
		
		// Adding plugins page links
		add_filter('plugin_action_links', array(&$this, 'plugin_page_links'), 10, 2);
		
		// Admin Notices
		add_action('admin_notices', array(&$this, 'admin_notice'));
	}

	/**
	 *
	 * File manager connector function
	 *
	 * */
	public function connector(){
		
		// Allowed mime types 
		$mime_list = array( 
			'text',
			'image', 
			'video', 
			'audio', 
			'application',
			'model',
			'chemical',
			'x-conference',
			'message',	 
		);
		
		$opts = array(
			'bind' => array(
				'ls.pre tree.pre parents.pre tmb.pre zipdl.pre size.pre mkdir.pre mkfile.pre rm.pre rename.pre duplicate.pre paste.pre upload.pre get.pre put.pre archive.pre extract.pre search.pre info.pre dim.pre resize.pre netmount.pre url.pre callback.pre chmod.pre' => array(&$this, 'security_check'),
				'*' => 'logger'
			),
			'debug' => true,
			'roots' => array(
				array(
					'driver'        => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
					'path'          => ABSPATH,                     // path to files (REQUIRED)
					'URL'           => site_url(),                  // URL to files (REQUIRED)
					'uploadDeny'    => array(),                // All Mimetypes not allowed to upload
					'uploadAllow'   => $mime_list, // All MIME types is allowed
					'uploadOrder'   => array('allow', 'deny'),      // allowed Mimetype `image` and `text/plain` only
					//auto::  'accessControl' => 'access',
					'disabled'      => array()    // List of disabled operations
					//~ 'attributes'
				)
			)
		);
		
		/**
		 * 
		 * @filter fm_options :: Options filter
		 * Implementation Example: add_filter('fm_options', array($this, 'fm_options_test'), 10, 1);
		 * 
		 * */
		$opts = apply_filters('fm_options_filter', $opts);
		$elFinder = new FM_EL_Finder();
		$elFinder = $elFinder->connect($opts);
		$elFinder->run();

		wp_die();
	}
	
	public function security_check(){
		// Checks if the current user have enough authorization to operate.
		if( ! wp_verify_nonce( $_POST['file_manager_security_token'] ,'file-manager-security-token') || !current_user_can( 'manage_options' ) ) wp_die();
		check_ajax_referer('file-manager-security-token', 'file_manager_security_token');
	}
		
	/**
	 * 
	 * Adds plugin page links,
	 * 
	 * */
	public function plugin_page_links($links, $file){
		
		static $this_plugin;
		
		if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
		 
		if ($file == $this_plugin){
			array_unshift( $links, '<a target=\'blank\' href="http://www.giribaz.com/support/">'. "Support" .'</a>');
			
			array_unshift( $links, '<a href="admin.php?page=file-manager-settings">'. "File Manager" .'</a>');
				
			if( !defined('FILE_MANAGER_PREMIUM') && !defined('FILE_MANAGER_BACKEND') )
				array_unshift( $links, '<a target=\'blank\' class="file-manager-admin-panel-pro" href="http://www.giribaz.com/wordpress-file-manager-plugin/" style="color: white; font-weight: bold; background-color: red; padding-right: 5px; padding-left: 5px; border-radius: 40%;">'. "Pro" .'</a>');
		
		}
		
		return $links;
	}
	
	/**
	 * 
	 * @function admin_notice
	 * @description Adds admin notices to the admin page
	 * @param void
	 * @return void
	 * 
	 * */
	public function admin_notice(){
		
		// DISALLOW_FILE_EDIT Macro checking
		if(defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT):
		?>
		<div class='update-nag fm-error'><b>DISALLOW_FILE_EDIT</b> is set to <b>TRUE</b>. You will not be able to edit files with <a href='admin.php?page=file-manager-settings'>File Manager</a>. Please set <b>DISALLOW_FILE_EDIT</b> to <b>FALSE</b></div>
		<style>
			.fm-error{
				border-left: 4px solid red;
				display: block;
			}
		</style>
		<?php
		endif;
	}
	
}

global $FileManager;
$FileManager = new FM('File Manager');

if(!function_exists('pr')):
function pr($obj){
	if (!defined('GB_DEBUG')) return;
	echo "<pre>";
	print_r($obj);
	echo "</pre>";
}
endif;
