<?php
global $xml_arr_file, $xml_headers;
$xml_arr_file = array();
$target_dir = get_template_directory() ."/config_xml/";

$files = scandir($target_dir);
$xml_arr_file = array();
foreach($files as $file) {
	$pos = strripos($file, '.xml');
	$pos2 = strripos($file, 'color');
	if($pos2 !== false && $pos !== false) array_push($xml_arr_file, substr($file, 0, $pos));
}

function thenatives_get_export_color_theme(){
	global $smof_data;
	$slug = $_POST['slug'];
	$file = $smof_data[$slug];
	$xml_dir = get_template_directory() ."/config_xml/";
	$img_dir = get_template_directory() ."/admin/assets/images/";
	$xml_exports = get_template_directory() ."/config_xml/exports/";
	if(!file_exists($xml_exports)) mkdir($xml_exports, 0755);
	$url_xml_file = THEME_DIR."/config_xml/".$file.".xml";
	$objXML_color = simplexml_load_file($url_xml_file);
	foreach ($objXML_color->children() as $child) {
		foreach ($child->items->children() as $childofchild) {
			$name =  (string)$childofchild->slug;
			$childofchild->std = $smof_data['thenatives_'.$name];
		}
	}
	$objXML_color->asXML($xml_exports.$file."_export.xml");
	$zip = new ZipArchive;
	if(file_exists($xml_exports.$file."_export.zip")) {
		unlink($xml_exports.$file."_export.zip");
	}
	$res = $zip->open($xml_exports.$file."_export.zip", ZipArchive::CREATE);
	if ($res === TRUE) {
		$zip->addFile($xml_exports.$file."_export.xml", $file.'_export.xml');
		$zip->addFile($img_dir.$file.'.png', $file.'_export.png');
		$zip->close();
		unlink($xml_exports.$file."_export.xml");
		echo XML_DIR . 'exports/' . $file.'_export.zip';
		die();
	} else {
		unlink($xml_exports.$file."_export.xml");
		echo 'error';
	}
	
}
add_action('wp_ajax_export_color_theme','thenatives_get_export_color_theme',10);

function thenatives_remove_color_theme(){
	$file = $_POST['file'];
	$pa_id = $_POST['pa_id'];
	$color_theme_saved = of_get_options($pa_id);
	$res = array();
	if($file !== $color_theme_saved ) {
		$xml_file = XML_PATH . $file . '.xml';
		$img_file = ADMIN_PATH . "assets/images/" . $file . '.png';
		if (file_exists($xml_file) && file_exists($img_file)) {
			@unlink($xml_file);
			@unlink($img_file);
			$res['status'] = 'success';
			$res['msg'] = $color_theme_saved;
		} else {
			$res['status'] = 'error';
			$res['msg'] = __('Error: Color theme files are not exist.', 'wpdance');
		}
		
	} else {
		$res['status'] = 'error';
		$res['msg'] = __('Error: Color theme are using, you can\'t remove it.', 'wpdance');
	}
	echo json_encode($res);
	die();
}
add_action('wp_ajax_remove_color_theme','thenatives_remove_color_theme',10);

$xml_headers = array(
		'Name' => 'Name',
		'Slug' => 'Slug',
		'Description' => 'Description',
    );

function thenatives_import_color_theme(){
	global $xml_headers;
	$uploadedfile = $_FILES['file_upload'];
	$xml_dir = get_template_directory() . "/config_xml/";
	$target_dir = XML_PATH . "tmp/";
	if(file_exists($target_dir)) thenatives_removeDir($target_dir);
	mkdir($target_dir, 0777);
	$target_file = $target_dir . basename( $_FILES["file_upload"]["name"]);
	$resturn = array();
	
	if($_FILES["file_upload"]["type"] === 'application/octet-stream') {
		if (move_uploaded_file($_FILES["file_upload"]["tmp_name"], $target_file)) {
			$zip = new ZipArchive;
			$res = $zip->open($target_file);
			if($res) {
				$zip->extractTo($target_dir);
				$zip->close();
				unlink($target_file);
				$tmp_file_xml = glob($target_dir . "*.xml");
				$tmp_file_png = glob($target_dir . "*.png");
				$header_datas = get_file_data($tmp_file_xml[0], $xml_headers);
				if(isset($header_datas['Slug']) && $header_datas['Slug'] !== '') {
					if(!file_exists(XML_PATH . $header_datas['Slug'].'.xml')) {
						if(copy($tmp_file_png[0],ADMIN_PATH .'assets/images/'.$header_datas['Slug'].'.png') 
							&& copy($tmp_file_xml[0],XML_PATH.$header_datas['Slug'].'.xml')) {
							$resturn['status'] = "success";
							$resturn['msg'] = __('Import success!', 'wpdance');
						} else {
							$resturn['status'] = 'error';
							$resturn['msg'] = __('Error: Import color theme not success.', 'wpdance');
						}
					} else {
						$resturn['status'] = 'error';
						$resturn['msg'] = __('Error: Color theme slug exist.', 'wpdance');
					}
				} else {
					$resturn['status'] = 'error';
					$resturn['msg'] = __('Error: XML syntax error.', 'wpdance');
				}
					
			} else {
				$resturn['status'] = 'error';
				$resturn['msg'] = __('Error: Extracting file is error.', 'wpdance');
			}
		} else {
			$resturn['status'] = 'error';
			$resturn['msg'] = __('Error: Upload file is error', 'wpdance');
		}
	}
    thenatives_removeDir($target_dir);
	echo json_encode($resturn);
	die();
}
add_action('wp_ajax_import_color_theme','thenatives_import_color_theme',10);

function thenatives_removeDir($path) {
	if(file_exists($path)) {
		$path = rtrim($path, '/') . '/';
		$items = glob($path . '*');
		foreach($items as $item) {
			is_dir($item) ? thenatives_removeDir($item) : unlink($item);
		}
		rmdir($path);
	}
}

function thenatives_get_tab_html_content(){
	global $thenatives_of_options,$xml_arr_file, $xml_headers;
	
	$nonce=$_POST['security'];
	
	if (! wp_verify_nonce($nonce, 'of_ajax_nonce') ) die('-1'); 
	
	$thenatives_of_options = array();
	
	$xml_file = $_POST['file'];
	
	$url =  ADMIN_DIR . 'assets/images/';
	$color_image_options = array();
	foreach($xml_arr_file as $xml){
		$header_datas = get_file_data(XML_PATH . $xml . '.xml', $xml_headers);
		$color_image_options[$xml]['img'] = $url . $xml .'.png';
		$color_image_options[$xml]['name'] = $header_datas['Name'];
		$color_image_options[$xml]['desc'] = $header_datas['Description'];
	}
	$thenatives_of_options[] = array( 	"name" 		=> "Theme Scheme",
							"desc" 		=> "Select a color.",
							"id" 		=> "thenatives_color_scheme",
							"std" 		=> $xml_file,
							"type" 		=> "theme_colors",
							"actions"	=> 1,
							"update"	=> "1",
							"options" 	=> $color_image_options
					);
	
	$url_xml_file = THEME_URI."/config_xml/".$xml_file.".xml";

	$objXML_color = simplexml_load_file($url_xml_file);
	foreach ($objXML_color->children() as $child) {	//group
		$group_name = (string)$child->getName();
		$thenatives_of_options[] = array( 	"name" 		=> $group_name." Scheme"
				,"id" 		=> "introduction_".$group_name
				,"std" 		=> "<h3 slug='".$group_name."' style=\"margin: 0 0 10px;\">".$group_name." Scheme</h3>"
				,"icon" 	=> true
				,"type" 	=> "info"
		);	

		foreach ($child->items->children() as $childofchild) { //items => item
		
			$name =  (string)$childofchild->name;
			$slug =  (string)$childofchild->slug; 
			$std =  (string)$childofchild->std; 
			//$class_name =  (string)$childofchild->class_name;		
			
			if($childofchild->getName()=='background_item'){
				$thenatives_of_options[] = array( 	"name" 		=> "Background Image"
						,"id" 		=> "thenatives_".$slug.'_image'
						,"type" 	=> "upload"
				);
				$thenatives_of_options[] = array( 	"name" 		=> "Repeat Image"
						,"id" 		=> "thenatives_".$slug.'_repeat'
						,"std" 		=> "repeat"
						,"type" 	=> "select"
						,"options"	=> array("repeat","no-repeat","repeat-x","repeat-y")
				);
				$thenatives_of_options[] = array( 	"name" 		=> "Position Image"
						,"id" 		=> "thenatives_".$slug.'_position'
						,"std" 		=> "left top"
						,"type" 	=> "select"
						,"options"	=> array("left top","right top","center top","center center")
				);
			}
			
			
			$thenatives_of_options[] = array( 	"name" 		=> trim($name)
					,"id" 		=> "thenatives_".$slug
					,"std" 		=> $std
					,"type" 	=> "color-update"
			);
		}
	}	
	
	$rs_arr = array();
	$thenatives_options_machine = new Options_Machine($thenatives_of_options);
	$rs_arr = $thenatives_options_machine->Inputs;
	echo json_encode( $rs_arr );
	die(1);
}
add_action('wp_ajax_tab_refesh','thenatives_get_tab_html_content',10);


function thenatives_import_xml(){
	//$file = $_POST['upload_file'];
	//echo json_encode($file);
	echo "kinhdon";
}

add_action('wp_ajax_import_xml','thenatives_import_xml',10);

function of_filter_save_media_upload($data) {

    if(!is_array($data)) return $data;
    
    foreach ($data as $key => $value) {
        if( $key == 'background_image' || $key == 'background_image_thumb' )
            continue;
        if (is_string($value)) {
            $data[$key] = str_replace(
                array(
                    site_url('', 'http'),
                    site_url('', 'https'),
                ),
                array(
                    '[site_url]',
                    '[site_url_secure]',
                ),
                $value
            );
        }
    }

    return $data;
}
add_filter('of_options_before_save', 'of_filter_save_media_upload');

/**
 * Filter URLs from uploaded media fields and replaces the site URL keywords
 * with the actual site URL.
 * 
 * @since 1.4.0
 * @param $data Options array
 * @return array
 */
function of_filter_load_media_upload($data) {
    
    if(!is_array($data)) return $data;

    foreach ($data as $key => $value) {
        if( $key == 'background_image' || $key == 'background_image_thumb' )
            continue;
        if (is_string($value)) {
            $data[$key] = str_replace(
                array(
                    '[site_url]', 
                    '[site_url_secure]',
                ),
                array(
                    site_url('', 'http'),
                    site_url('', 'https'),
                ),
                $value
            );
        }
    }

    return $data;
}
add_filter('of_options_after_load', 'of_filter_load_media_upload');

function thenatives_settings_admin_init() 
{
	// Rev up the Options Machine
	global $of_options, $options_machine, $smof_data, $smof_details;
	if (!isset($options_machine))
		$options_machine = new Options_Machine($of_options);
	do_action('thenatives_settings_admin_init_before', array(
			'of_options'		=> $of_options,
			'options_machine'	=> $options_machine,
			'smof_data'			=> $smof_data
		));
	if (empty($smof_data['smof_init'])) { // Let's set the values if the theme's already been active
		of_save_options($options_machine->Defaults);
		of_save_options(date('r'), 'smof_init');
		$smof_data = of_get_options();
		$options_machine = new Options_Machine($of_options);
	}

	do_action('thenatives_settings_admin_init_after', array(
		'of_options'		=> $of_options,
		'options_machine'	=> $options_machine,
		'smof_data'			=> $smof_data
	));

}

function thenatives_settings_add_admin() {
	
    $of_page = add_theme_page( THEMENAME, 'Theme Options', 'edit_theme_options', 'thenatives_settings', 'thenatives_settings_page');

	// Add framework functionaily to the head individually
	
	add_action("admin_print_scripts-$of_page", 'of_load_only');
	add_action("admin_print_styles-$of_page",'of_style_only');
	
}

function thenatives_settings_page(){
	
	global $options_machine;
	include_once( ADMIN_PATH . 'front-end.php' );

}

function of_style_only(){
	wp_enqueue_style('admin-style', ADMIN_DIR . 'assets/css/admin-style.css');
	wp_enqueue_style('color-picker', ADMIN_DIR . 'assets/css/colorpicker.css');
	wp_enqueue_style('jquery-ui-custom-admin', ADMIN_DIR .'assets/css/jquery-ui-custom.css', array('yit-plugin-style'));

	if ( !wp_style_is( 'wp-color-picker','registered' ) ) {
		wp_register_style( 'wp-color-picker', ADMIN_DIR . 'assets/css/color-picker.min.css' );
	}
	if( !did_action('wp_enqueue_media') ){
		wp_enqueue_media();
	}

	wp_register_style( 'select2-css', ADMIN_DIR . "assets/css/select2.min.css" );
	wp_enqueue_style('select2-css');
	wp_register_style( 'fontawesome-style', THEME_CSS . "/font-awesome.min.css" );
	wp_enqueue_style('fontawesome-style');
	wp_register_style( 'admin-custom-style', ADMIN_DIR . "assets/css/admin.css" );
	wp_enqueue_style('admin-custom-style');
	wp_register_script( 'admin-custom-script', ADMIN_DIR . "assets/js/admin.js", array('jquery') );
	wp_enqueue_script('admin-custom-script');
	wp_register_script( 'admin-widget-script', ADMIN_DIR . "assets/js/admin-widget.js", array('jquery') );
	wp_enqueue_script('admin-widget-script');
	wp_register_script( 'select2-script', ADMIN_DIR . "assets/js/select2.full.min.js", array('jquery') );
	wp_enqueue_script('select2-script');
	wp_enqueue_style( 'wp-color-picker' );
	do_action('of_style_only_after');
}

function of_load_only() 
{
	//add_action('admin_head', 'smof_admin_head');
	
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('jquery-input-mask', ADMIN_DIR .'assets/js/jquery.maskedinput-1.2.2.js', array( 'jquery' ));
	wp_enqueue_script('tipsy', ADMIN_DIR .'assets/js/jquery.tipsy.js', array( 'jquery' ));
	//wp_enqueue_script('color-picker', ADMIN_DIR .'assets/js/colorpicker.js', array('jquery'));
	wp_enqueue_script('bootstrap-colorpicker', ADMIN_DIR .'assets/js/bootstrap-colorpicker.js', array('jquery'));
	wp_enqueue_script('cookie', ADMIN_DIR . 'assets/js/cookie.js', 'jquery');
	wp_enqueue_script('smof', ADMIN_DIR .'assets/js/smof.js', array( 'jquery' ));


	// Enqueue colorpicker scripts for versions below 3.5 for compatibility
	if ( !wp_script_is( 'wp-color-picker', 'registered' ) ) {
		wp_register_script( 'iris', ADMIN_DIR .'assets/js/iris.min.js', array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
		wp_register_script( 'wp-color-picker', ADMIN_DIR .'assets/js/color-picker.min.js', array( 'jquery', 'iris' ) );
	}
	wp_enqueue_script( 'wp-color-picker' );
	

	/**
	 * Enqueue scripts for file uploader
	 */
	
	if ( function_exists( 'wp_enqueue_media' ) )
		wp_enqueue_media();

	do_action('of_load_only_after');

}

function of_ajax_callback() 
{
	global $options_machine, $of_options;
	$nonce=$_POST['security'];
	
	if (! wp_verify_nonce($nonce, 'of_ajax_nonce') ) die('-1'); 
			
	//get options array from db
	$all = of_get_options();
	
	$save_type = $_POST['type'];
	
	//echo $_POST['data'];
	
	//Uploads
	if($save_type == 'upload')
	{
		
		$clickedID = $_POST['data']; // Acts as the name
		$filename = $_FILES[$clickedID];
       	$filename['name'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', $filename['name']); 
		
		$override['test_form'] = false;
		$override['action'] = 'wp_handle_upload';    
		$uploaded_file = wp_handle_upload($filename,$override);
		 
			$upload_tracking[] = $clickedID;
				
			//update $options array w/ image URL			  
			$upload_image = $all; //preserve current data
			
			$upload_image[$clickedID] = $uploaded_file['url'];
			
			of_save_options($upload_image);
		
				
		 if(!empty($uploaded_file['error'])) {echo 'Upload Error: ' . $uploaded_file['error']; }	
		 else { echo $uploaded_file['url']; } // Is the Response
		 
	}
	elseif($save_type == 'image_reset')
	{
			
			$id = $_POST['data']; // Acts as the name
			
			$delete_image = $all; //preserve rest of data
			$delete_image[$id] = ''; //update array key with empty value	 
			of_save_options($delete_image ) ;
	
	}
	elseif($save_type == 'backup_options')
	{
			
		$backup = $all;
		$backup['backup_log'] = date('r');
		
		of_save_options($backup, BACKUPS) ;
			
		die('1'); 
	}
	elseif($save_type == 'restore_options')
	{
			
		$smof_data = of_get_options(BACKUPS);

		of_save_options($smof_data);
		
		die('1'); 
	}
	elseif($save_type == 'import_options'){


		$smof_data = unserialize(base64_decode($_POST['data'])); //100% safe - ignore theme check nag
		of_save_options($smof_data);

		
		die('1'); 
	}
	elseif ($save_type == 'save')
	{
		wp_parse_str(stripslashes($_POST['data']), $smof_data);
		unset($smof_data['security']);
		unset($smof_data['of_save']);
		of_save_options($smof_data);
		die('1');
	}
	elseif ($save_type == 'reset')
	{
		of_save_options($options_machine->Defaults);
		
        die('1'); //options reset
	}

  	die();
}

require_once ( ADMIN_PATH . 'options.php' );

function of_head() { do_action( 'of_head' ); }

/**
 * Add default options upon activation else DB does not exist
 *
 * DEPRECATED, Class_options_machine now does this on load to ensure all values are set
 *
 * @since 1.0.0
 */
function of_option_setup()	
{
	global $of_options, $options_machine;
	$options_machine = new Options_Machine($of_options);
		
	if (!of_get_options())
	{
		of_save_options($options_machine->Defaults);
	}
}

/**
 * Change activation message
 *
 * @since 1.0.0
 */
function thenatives_settings_admin_message() { 
	
	//Tweaked the message on theme activate
	?>
    <script type="text/javascript">
    jQuery(function(){
    	
        var message = '<p>This theme comes with an <a href="<?php echo admin_url('admin.php?page=thenatives_settings'); ?>">options panel</a> to configure settings. This theme also supports widgets, please visit the <a href="<?php echo admin_url('widgets.php'); ?>">widgets settings page</a> to configure them.</p>';
    	jQuery('.themes-php #message2').html(message);
    
    });
    </script>
    <?php
	
}

/**
 * Get header classes
 *
 * @since 1.0.0
 */
function of_get_header_classes_array() 
{
	global $of_options;
	
	foreach ($of_options as $value) 
	{
		if ($value['type'] == 'heading')
			$hooks[] = str_replace(' ','',strtolower($value['name']));	
	}
	
	return $hooks;
}

/**
 * Get options from the database and process them with the load filter hook.
 *
 * @author Jonah Dahlquist
 * @since 1.4.0
 * @return array
 */
function of_get_options($key = null, $data = null) {
	global $smof_data;
	do_action('of_get_options_before', array(
		'key'=>$key, 'data'=>$data
	));
	if ($key != null) { // Get one specific value
		$data = get_theme_mod($key, $data);
	} else { // Get all values
		$data = get_theme_mods();	
	}
	$data = apply_filters('of_options_after_load', $data);
	if ($key == null) {
		$smof_data = $data;
	} else {
		$smof_data[$key] = $data;
	}
	do_action('of_option_setup_before', array(
		'key'=>$key, 'data'=>$data
	));
	return $data;

}

/**
 * Save options to the database after processing them
 *
 * @param $data Options array to save
 * @author Jonah Dahlquist
 * @since 1.4.0
 * @uses update_option()
 * @return void
 */

function of_save_options($data, $key = null) {
	global $smof_data;
    if (empty($data))
        return;	
    do_action('of_save_options_before', array(
		'key'=>$key, 'data'=>$data
	));
	$data = apply_filters('of_options_before_save', $data);
	if ($key != null) { // Update one specific value
		if ($key == BACKUPS) {
			unset($data['smof_init']); // Don't want to change this.
		}
		set_theme_mod($key, $data);
	} else { // Update all values in $data
		foreach ( $data as $k=>$v ) {
			if (!isset($smof_data[$k]) || $smof_data[$k] != $v) { // Only write to the DB when we need to
				set_theme_mod($k, $v);
			} else if (is_array($v)) {
				foreach ($v as $key=>$val) {
					if ($key != $k && $v[$key] == $val) {
						set_theme_mod($k, $v);
						break;
					}
				}
			}
	  	}
	}
    do_action('of_save_options_after', array(
		'key'=>$key, 'data'=>$data
	));
}

//fix bug activate theme no value default
function thenatives_of_option_setup()	
{
	global $of_options, $options_machine;
	$thenatives = of_get_options();
	$options_machine = new Options_Machine($of_options);
	$thenatives = thenatives_array_atts($options_machine->Defaults, $thenatives);
	thenatives_of_save_options($thenatives);	
}

function thenatives_of_save_options($data){
	foreach ( $data as $k=>$v ) {
		if (is_array($v)) {
			foreach ($v as $key=>$val) {
				if ($key != $k && $v[$key] == $val) {
					set_theme_mod($k, $v);
					break;
				}
			}
		} else {
			set_theme_mod($k, $v);
		}
	}
	
}

/**
 * For use in themes
 *
 * @since forever
 */



$thenatives = of_get_options();
if (!isset($smof_details))
	$smof_details = array();