<?php if(!defined('ABSPATH')) die(); // Security check?>
<?php

include_once( 'php' . DS . 'autoload.php' );

/**
 * 
 * elFinder class to manipulate elfinder
 * 
 * */

class FM_EL_Finder{
	
	// Important data
	
	/**
	 * 
	 * @var array $base_path Base url(s) for the current user
	 * 
	 * */
	public $base_path;
	
	/**
	 * 
	 * Constructor function
	 * 
	 * */
	public function __construct(){
	
		
	}
	
	/**
	 * 
	 * Connect function
	 * @return object
	 * 
	 * */
	public function connect($options){
		
		return new elFinderConnector(new elFinder($options));
		
	}
	
}
