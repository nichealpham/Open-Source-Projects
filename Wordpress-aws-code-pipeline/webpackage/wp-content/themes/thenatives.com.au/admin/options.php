<?php
add_action('init','of_options');
if (!function_exists('of_options'))
{
	function of_options()
	{	
		$of_sidebars 	= array();
		global $default_sidebars;
		if($default_sidebars){
			foreach( $default_sidebars as $key => $_sidebar ){
				$of_sidebars[$_sidebar['id']] = $_sidebar['name'];
			}
		}

		$logo = THEME_IMAGES. '/logo.png';
		$favicon = THEME_IMAGES. '/favicon.png'; 

		//More Options
		$body_repeat 		= array("no-repeat","repeat-x","repeat-y","repeat");
		$body_pos 			= array("top left","top center","top right","center left","center center","center right","bottom left","bottom center","bottom right");
		
		$default_font_size = array(	
			"10px"
			,"11px"
			,"12px"
			,"13px"
			,"14px"
			,"15px"
			,"16px"
			,"17px"
			,"18px"
			,"19px"
			,"20px"
			,"21px"
			,"22px"
			,"23px"
			,"24px"
			,"25px"
			,"26px"
			,"27px"
			,"28px"
			,"29px"
			,"30px"
			,"31px"
			,"32px"
			,"33px"
			,"34px"
			,"35px"
			,"36px"
			,"37px"
			,"38px"
			,"39px"
			,"40px"
			,"41px"
			,"42px"
			,"43px"
			,"44px"
			,"45px"
			,"46px"
			,"47px"
			,"48px"
			,"49px"
			,"50px"
		);
		
		$faces = array(
					'custom'=>'Custom',
					'arial'=>'Arial',
					'verdana'=>'Verdana',
					'fantasy'=>'Fantasy',
					'georgia' =>'Georgia',
					'times'=>'Times New Roman',
					'tahoma'=>'Tahoma',
					'monospace'=>'Monospace',
					'helvetica'=>'Helvetica',
					'cursive'=>'Cursive',
				);

		define('ADMIN_ASSETS_IMG_DIR', ADMIN_DIR . 'assets/images/');

		$default_font_size = array_combine($default_font_size, $default_font_size);
		
		//Get list menu
		/*$menus = wp_get_nav_menus();
		$arr_menu = array();
		if($menus) {
			foreach($menus as $menu) { 
				$arr_temp = array($menu->term_id => $menu->name);
				//array_push($arr_menu, $arr_temp);
				$arr_menu = array_merge($arr_menu, $arr_temp);
			}
		}*/
		
		$url =  ADMIN_DIR . 'assets/images/';

/***************** THENATIVES : GENERAL ****************/	
global $of_options,$thenatives_google_fonts;

$of_options = array();
					
$of_options[] = array( 	
					"name" 		=> "General"
					,"type" 		=> "heading"
					,"icon"		=> '<i class="fa fa-globe" aria-hidden="true"></i>'
				);

$of_options[] = array(
					"name" 		=> "Logo & Favicon"
					,"id" 		=> "logo_and_favicon"
					,"type" 	=> "info"
				);					

$of_options[] = array( 	
					"name" 		=> "Logo image"
					,"desc" 	=> "Change your logo."
					,"id" 		=> "thenatives_logo"
					,"std"		=> $logo
					,"type" 	=> "upload"
				);

$of_options[] = array(
					"name" 		=> "Logo sticky"
					,"desc" 	=> "Change your logo sticky."
					,"id" 		=> "thenatives_logo_sticky"
					,"std"		=> $logo
					,"type" 	=> "upload"
				);

$of_options[] = array( 	
					"name" 		=> "Favicon image"
					,"desc" 	=> "Accept ICO files, PNG files"
					,"id" 		=> "thenatives_favicon"
					,"std" 		=> $favicon
					,"type" 	=> "media"
				);
				
$of_options[] = array( 	
					"name" 		=> "Text Logo"
					,"desc" 	=> "Text Logo"
					,"id" 		=> "thenatives_text_logo"
					,"std" 		=> "The Natives"
					,"type" 	=> "text"
				);

$of_options[] = array(
					"name" 		=> "Global"
					,"id" 		=> "info_global"
					,"type" 	=> "info"
				);

$of_options[] = array(
                    "name" 		=> "Google DFP"
                    ,"desc" 	=> "Enable / Disable Google DFP"
                    ,"id" 		=> "thenatives_enable_google_dfp"
                    ,"on"		=> "Enable"
                    ,"off"		=> "Disable"
                    ,"std" 		=> 1
                    ,"type" 	=> "switch"
                );

$of_options[] = array(
					"name" 		=> "Google Analytic Code"
					,"desc" 	=> "Add code Google Analytic"
					,"id" 		=> "thenatives_google_analytic_code"
					,"std" 		=> ""
					,"type" 	=> "text"
				);
				
$of_options[] = array( 	
					"name" 		=> "Enable Totop button"
					,"desc" 	=> "Enable/Disable Totop Button on site"
					,"id" 		=> "thenatives_totop"
					,"on"		=> "Enable"
					,"off"		=> "Disable"
					,"std" 		=> 1
					,"type" 	=> "switch"
				);
				
/***************** THENATIVES : Header ****************/
				
$of_options[] = array(
					"name" 		=> "Header"
					,"type" 	=> "heading"
					,"icon"		=> '<i class="fa fa-hourglass-start" aria-hidden="true"></i>'
				);

$of_options[] = array(
					"name" 		=> "Header Layout"
					,"id" 		=> "info_header"
					,"type" 	=> "info"
				);
				
$of_options[] = array(
					"name" 		=> "Header Layout"
					,"desc"		=> "Select the header Layout<br><b>PLEASE NOTE</b>: Only Header 1 & 6 can be transparent. Keep that in mind when choosing your Page specific Headers."
					,"id" 		=> "thenatives_header_style"
					,"std" 		=> "v1"
					,"type" 	=> "images"
					,"options" 	=> array(
						'v1' 	=> ADMIN_ASSETS_IMG_DIR . 'header/header-v1.jpg'
						,'v2' 	=> ADMIN_ASSETS_IMG_DIR . 'header/header-v2.jpg'
						,'v3' 	=> ADMIN_ASSETS_IMG_DIR . 'header/header-v3.jpg'
						,'v4' 	=> ADMIN_ASSETS_IMG_DIR . 'header/header-v4.jpg'
						,'v5' 	=> ADMIN_ASSETS_IMG_DIR . 'header/header-v5.jpg'
						,'v6' 	=> ADMIN_ASSETS_IMG_DIR . 'header/header-v6.jpg'
					)
				);
					
$of_options[] = array(
					"name" 		=> "Enable Social Media Icons"
					,"desc" 	=> "Enable / Disable Social Media Icons"
					,"id" 		=> "thenatives_enable_social_media_icon"
					,"on"		=> "Enable"
					,"off"		=> "Disable"
					,"std" 		=> 1
					,"type" 	=> "switch"
				);
				
$of_options[] = array(
					"name" 		=> "Enable Search"
					,"desc" 	=> "Enable / Disable Search"
					,"id" 		=> "thenatives_enable_search"
					,"on"		=> "Enable"
					,"off"		=> "Disable"
					,"std" 		=> 1
					,"type" 	=> "switch"
				);
					
$of_options[] = array(
					"name" 		=> "Sticky Header"
					,"desc" 	=> "Enable / Disable Sticky Header"
					,"id" 		=> "thenatives_sticky_header"
					,"on"		=> "Enable"
					,"off"		=> "Disable"
					,"std" 		=> 1
					,"type" 	=> "switch"
				);

$of_options[] = array(
					"name" 		=> "Breadcrumbs"
					,"id" 		=> "info_breadcrumbs"
					,"type" 	=> "info"
				);

$of_options[] = array(
					"name" 		=> "Breadcrumbs Background"
					,"desc" 	=> "Add breadcrumbs background"
					,"id" 		=> "thenatives_bg_breadcrumbs"
					,"std"		=> ''
					,"type" 	=> "upload"
				);

/***************** THENATIVES : SOCIAL MEDIA ****************/
$of_options[] = array(
					"name" 		=> "Social Media"
					,"type" 	=> "heading"
					,"icon"		=> '<i class="fa fa-twitter" aria-hidden="true"></i>'
				);

$of_options[] = array(
					"name" 		=> "Social Media Icons"
					,"id" 		=> "info_social_media"
					,"type" 	=> "info"
				);
				
$of_options[] = array( 	
					"name" 		=> "Facebook"
					,"desc" 	=> "Enter URL to your Facebook Account"
					,"id" 		=> "thenatives_facebook_url"
					,"std" 		=> ""
					,"type" 	=> "text"
				);
				
$of_options[] = array( 	
					"name" 		=> "Twitter"
					,"desc" 	=> "Enter URL to your Twitter Account"
					,"id" 		=> "thenatives_twitter_url"
					,"std" 		=> ""
					,"type" 	=> "text"
				);
				
$of_options[] = array( 	
					"name" 		=> "Google Plus"
					,"desc" 	=> "Enter URL to your Google Plus Account"
					,"id" 		=> "thenatives_google_plus_url"
					,"std" 		=> ""
					,"type" 	=> "text"
				);
				
$of_options[] = array( 	
					"name" 		=> "Youtube"
					,"desc" 	=> "Enter URL to your Youtube Account"
					,"id" 		=> "thenatives_youtube_url"
					,"std" 		=> ""
					,"type" 	=> "text"
				);
				
$of_options[] = array( 	
					"name" 		=> "Instagram"
					,"desc" 	=> "Enter URL to your Instagram Account"
					,"id" 		=> "thenatives_instagram_url"
					,"std" 		=> ""
					,"type" 	=> "text"
				);
				
$of_options[] = array( 	
					"name" 		=> "LinkedIn"
					,"desc" 	=> "Enter URL to your LinkedIn Account"
					,"id" 		=> "thenatives_linkedin_url"
					,"std" 		=> ""
					,"type" 	=> "text"
				);
				
$of_options[] = array( 	
					"name" 		=> "Pinterest"
					,"desc" 	=> "Enter URL to your Pinterest Account"
					,"id" 		=> "thenatives_pinterest_url"
					,"std" 		=> ""
					,"type" 	=> "text"
				);
				
$of_options[] = array( 	
					"name" 		=> "Flickr"
					,"desc" 	=> "Enter URL to your Flickr Account"
					,"id" 		=> "thenatives_flickr_url"
					,"std" 		=> ""
					,"type" 	=> "text"
				);

/***************** THENATIVES : FOOTER ****************/
$of_options[] = array(
					"name" 		=> "Footer"
					,"type" 	=> "heading"
					,"icon"		=> '<i class="fa fa-hourglass-end" aria-hidden="true"></i>'
				);

$of_options[] = array(
                    "name" 		=> "Footer General"
                    ,"id" 		=> "info_footer_general"
                    ,"type" 	=> "info"
                );

$of_options[] = array(
                    "name" 		=> "Form Title",
                    "desc" 		=> "Change title for Form.",
                    "id" 		=> "thenatives_form_title",
                    "std" 		=> "",
                    "type" 		=> "textarea"
                );

$of_options[] = array(
                    "name" 		=> "Background Form",
                    "desc" 		=> "Change background for Form.",
                    "id" 		=> "thenatives_bg_form",
                    "std" 		=> "",
                    "type" 		=> "upload"
                );

$of_options[] = array(
					"name" 		=> "Footer Widgets"
					,"id" 		=> "info_footer_widgets"
					,"type" 	=> "info"
				);
				
$of_options[] = array(
					"name" 		=> "Footer Widget Area"
					,"desc" 	=> "Enable / Disable widgetized Footer Area."
					,"id" 		=> "thenatives_enable_footer_widget"
					,"on"		=> "Enable"
					,"off"		=> "Disable"
					,"std" 		=> 1
					,"type" 	=> "switch"
				);
				
$of_options[] = array(
					"name" 		=> "Footer Widget Columns"
					,"desc"		=> "Select Footer Columns"
					,"fold"		=> "thenatives_enable_footer_widget"
					,"id" 		=> "thenatives_footer_widget_columns"
					,"std" 		=> "1"
					,"type" 	=> "images"
					,"options" 	=> array(
						'1' 	=> ADMIN_ASSETS_IMG_DIR . 'footer/footer-widget-v1.jpg'
						,'2' 	=> ADMIN_ASSETS_IMG_DIR . 'footer/footer-widget-v2.jpg'
						,'3' 	=> ADMIN_ASSETS_IMG_DIR . 'footer/footer-widget-v3.jpg'
						,'4' 	=> ADMIN_ASSETS_IMG_DIR . 'footer/footer-widget-v4.jpg'
					)
				);

$of_options[] = array(
					"name" 		=> "Copyright"
					,"id" 		=> "info_copyright"
					,"type" 	=> "info"
				);
				
$of_options[] = array( 	
					"name" 		=> "Copyright Area"
					,"desc" 	=> "Enable / Disable Copyright Area."
					,"id" 		=> "thenatives_enable_copy_right"
					,"on"		=> "Enable"
					,"off"		=> "Disable"
					,"std" 		=> 1
					,"type" 	=> "switch"
				);
				
$of_options[] = array( 	
					"name" 		=> "Copyright"
					,"desc" 	=> "Copyright"
					,"id" 		=> "footer_copyright"
					,"fold"		=> "thenatives_enable_copy_right"
					,"std" 		=> '&copy; '.date("Y").' thenatives.com.au . All Rights Reserved. '
					,"type" 	=> "textarea"
				);


/***************** THENATIVES : STYLE ****************/					
/***************** DON'T ADD MORE ANY ELEMENTS HERE ****************/				
$of_options[] = array(
					"name" 		=> "Styling"
					,"type" 	=> "heading"
					,"icon"		=> '<i class="fa fa-paint-brush" aria-hidden="true"></i>'
				);
global $xml_arr_file, $xml_headers;
$url =  ADMIN_DIR . 'assets/images/';
$color_image_options = array();
foreach($xml_arr_file as $xml){
	$header_datas = get_file_data( XML_PATH . $xml . '.xml', $xml_headers );
	$color_image_options[$xml]['img'] = $url . $xml .'.png';
	$color_image_options[$xml]['name'] = $header_datas['Name'];
	$color_image_options[$xml]['desc'] = $header_datas['Description'];
}

$xml_file = get_option(THEME_SLUG.'color_select');
$xml_file = isset($xml_file) && strlen(trim($xml_file)) > 0 ? $xml_file : 'color_default';
$url_xml_file = THEME_DIR."/config_xml/".$xml_file.".xml";
$objXML_color = simplexml_load_file($url_xml_file);
	foreach ($objXML_color->children() as $child) {	//group
		$group_name = (string)$child->getName();
		$of_options[] = array( 	
							"name" 		=> $child->text
							,"id" 		=> "thenatives_".$group_name
							,"icon" 	=> true
							,"type" 	=> "info"
						);	

		foreach ($child->items->children() as $childofchild) {
		
			$name =  (string)$childofchild->name;
			$slug =  (string)$childofchild->slug; 
			$std =  (string)$childofchild->std; 
			$nodeName =  (string)$childofchild->getName();	
			
			if($nodeName =='background_item'){

				$of_options[] = array(
									"name" 		=> "Background Image"
									,"id" 		=> $slug.'_image'
									,"type" 	=> "upload"
								);

				$of_options[] = array(
									"name" 		=> "Repeat Image"
									,"id" 		=> $slug.'_repeat'
									,"std" 		=> "no-repeat"
									,"type" 	=> "select"
									,"options"	=> $body_repeat
								);

				$of_options[] = array(
									"name" 		=> "Position Image"
									,"id" 		=> $slug.'_position'
									,"std" 		=> "center center"
									,"type" 	=> "select"
									,"options"	=> $body_pos
								);
			}
			
			
			$of_options[] = array(
								"name" 		=> trim($name)
								,"id" 		=> $slug
								,"std" 		=> $std
								,"type" 	=> "color"
							);
		}
	}
				
/***************** THENATIVES : TYPO ****************/		

$of_options[] = array( 	"name" 		=> "Typography"
						,"type" 	=> "heading"
						,"icon"		=> '<i class="fa fa-font" aria-hidden="true"></i>'
				);
		
$objXML = simplexml_load_file(THEME_DIR."/config_xml/font_config.xml");
foreach ($objXML->children() as $key=>$child) {	//group
	$group_name = (string)$child->getName();
	$of_options[] = array( 	
						"name" 		=> $child->text
						,"id" 		=> "thenatives_".$group_name
						,"icon" 	=> true
						,"type" 	=> "info"
					);
	foreach ($child->items->children() as $childofchild) {
		$childofchild = (array) $childofchild;
		$array = array(
					"name" 		=> $childofchild['name']
					,"id" 		=> $childofchild['slug']
					,"std"		=> $childofchild['std']
					,"desc"		=> $childofchild['desc']
				);
		if($childofchild['type'] == 'font_family') {
			$default_family_font = in_array($childofchild['std'],$faces) ? strtolower($childofchild['std']) : 'arial';
			$array['std'] = trim($default_family_font);
			$array['type'] = "select";
			$array['options'] = $faces;
		}
		elseif($childofchild['type'] == 'font_size') {
			$array['type'] = "select";
			$array['options'] = $default_font_size;
		}
		else {
			$array['type'] = $childofchild['type'];
		}
		if($childofchild['fold']){
			$array['fold'] = $childofchild['fold'];
		}
		if($childofchild['fold_val']=='0' || $childofchild['fold_val']){
			$fold_val = explode(',', $childofchild['fold_val']);
			foreach ($fold_val as $key => $value) {
				$fold_val[$key] = (int) $value;
			}
			$array['fold_val'] = $fold_val;
		}
		$of_options[] = $array;
	}
}
			
/***************** THENATIVES : Blog Options ****************/	
$of_options[] = array( 	
					"name" 		=> "Blog"
					,"type" 	=> "heading"
					,"icon"		=> ' <i class="fa fa-newspaper-o" aria-hidden="true"></i>'
				);

$of_options[] = array(
					"name" 		=> "Blog Layout"
					,"id" 		=> "info_blog_layout"
					,"type" 	=> "info"
				);

$of_options[] = array( 	
					"name" 		=> "Blog Layout"
					,"desc" 	=> "Select main content and sidebar alignment. Choose between 1, 2 column layout."
					,"id" 		=> "thenatives_blog_layout"
					,"std" 		=> "full"
					,"type" 	=> "images"
					,"options" 	=> array(
						'full' 		=> $url . 'full.png'
						,'aside' 	=> $url . 'aside.png'
						,'sidebar' 	=> $url . 'sidebar.png'
						,'two-col' 	=> $url . 'two-col.png'
					)
				);

$of_options[] = array( 	
					"name" 		=> "Left Sidebar"
					,"id" 		=> "thenatives_blog_left_sidebar"
					,"desc" 	=> "Blog left sidebar"
					,"std" 		=> "blog-left-widget-area"
					,"type" 	=> "select"
					,"options" 	=> $of_sidebars
				);	
$of_options[] = array( 	
					"name" 		=> "Right Sidebar"
					,"id" 		=> "thenatives_blog_right_sidebar"
					,"desc" 	=> "Blog right sidebar"
					,"std" 		=> "blog-right-widget-area"
					,"type" 	=> "select"
					,"options" 	=> $of_sidebars
				);
				
$of_options[] = array( 	
					"name" 		=> "Sticky Sidebar"
					,"id" 		=> "thenatives_blog_sticky_sidebar"
					,"desc" 	=> "Blog sticky sidebar"
					,"std" 		=> "none"
					,"type" 	=> "select"
					,"options" 	=> array(
						'none' => 'None',
						'sidebar' => 'Sidebar Left',
						'aside' => 'Sidebar Right',
						'two-col' => 'Two Sidebar',
					)
				);

$of_options[] = array(
					"name" 		=> "Blog Options"
					,"id" 		=> "info_blog_options"
					,"type" 	=> "info"
				);

$of_options[] = array( 	
					"name" 		=> "Blog Thumbnail"
					,"desc" 	=> "Show/hide Thumbnail"
					,"id" 		=> "thenatives_blog_thumbnail"
					,"std" 		=> 1
					,"on" 		=> "Show"
					,"off" 		=> "Hide"
					,"folds"	=> 1
					,"type" 	=> "switch"		
				);
					
$of_options[] = array( 	
					"name" 		=> "Blog Excerpt"
					,"desc" 	=> "Show/hide Excerpt"
					,"id" 		=> "thenatives_blog_excerpt"
					,"std" 		=> 1
					,"on" 		=> "Show"
					,"off" 		=> "Hide"
					,"folds"	=> 1
					,"type" 	=> "switch"		
				);
				
$of_options[] = array( 	
					"name" 		=> "Blog Excerpt Words Limit"
					,"desc" 	=> "Min: 15, max: 250, step: 1, default value: 35"
					,"id" 		=> "thenatives_blog_excerpt_words_limit"
					,"type" 	=> "text" 
				);
					
$of_options[] = array( 	
					"name" 		=> "Show Read More"
					,"desc" 	=> "Show/hide Read More Button"
					,"id" 		=> "thenatives_blog_readmore"
					,"std" 		=> 1
					,"on" 		=> "Show"
					,"off" 		=> "Hide"
					,"folds"	=> 1
					,"type" 	=> "switch"		
				);
				
$of_options[] = array( 	
					"name" 		=> "Blog Categories"
					,"desc" 	=> "Show/hide Categories"
					,"id" 		=> "thenatives_blog_categories"
					,"std" 		=> 1
					,"on" 		=> "Show"
					,"off" 		=> "Hide"
					,"type" 	=> "switch"		
				);		
				
$of_options[] = array( 	
					"name" 		=> "Blog Author"
					,"desc" 	=> "Show/hide Author"
					,"id" 		=> "thenatives_blog_author"
					,"std" 		=> 1
					,"on" 		=> "Show"
					,"off" 		=> "Hide"
					,"type" 	=> "switch"		
				);

$of_options[] = array( 	
					"name" 		=> "Blog Time"
					,"desc" 	=> "Show/hide Time"
					,"id" 		=> "thenatives_blog_time"
					,"std" 		=> 1
					,"on" 		=> "Show"
					,"off" 		=> "Hide"
					,"folds"	=> 1
					,"type" 	=> "switch"		
				);

/***************** THENATIVES : Blog Details ****************/
	
$of_options[] = array( 	
					"name" 		=> "Blog Details"
					,"type" 	=> "heading"
				);

$of_options[] = array(
					"name" 		=> "Blog Detail Layout"
					,"id" 		=> "info_blog_detail_layout"
					,"type" 	=> "info"
				);

$of_options[] = array( 	
					"name" 		=> "Blog Detail Layout"
					,"desc" 	=> "Select main content and sidebar alignment. Choose between 1, 2 column layout."
					,"id" 		=> "thenatives_blog_detail_layout"
					,"std" 		=> "full"
					,"type" 	=> "images"
					,"options" 	=> array(
						'full' 		=> $url . 'full.png'
						,'aside' 	=> $url . 'aside.png'
						,'sidebar' 	=> $url . 'sidebar.png'
						,'two-col' 	=> $url . 'two-col.png'
					)
				);
$of_options[] = array( 	
					"name" 		=> "Left Sidebar"
					,"id" 		=> "thenatives_blog_detail_left_sidebar"
					,"desc" 	=> "Blog detail left sidebar"
					,"std" 		=> "blog-left-widget-area"
					,"type" 	=> "select"
					//,"mod"		=> "mini"
					,"options" 	=> $of_sidebars
				);	
$of_options[] = array( 	
					"name" 		=> "Right Sidebar"
					,"id" 		=> "thenatives_blog_detail_right_sidebar"
					,"desc" 	=> "Blog detail right sidebar"
					,"std" 		=> "blog-right-widget-area"
					,"type" 	=> "select"
					//,"mod"		=> "mini"
					,"options" 	=> $of_sidebars
				);	
				
$of_options[] = array( 	
					"name" 		=> "Sticky Sidebar"
					,"id" 		=> "thenatives_blog_detail_sticky_sidebar"
					,"desc" 	=> "Blog detail sticky sidebar"
					,"std" 		=> "none"
					,"type" 	=> "select"
					,"options" 	=> array(
						'none' => 'None',
						'sidebar' => 'Sidebar Left',
						'aside' => 'Sidebar Right',
						'two-col' => 'Two Sidebar',
					)
				);	

$of_options[] = array(
					"name" 		=> "Blog Detail Options"
					,"id" 		=> "info_blog_detail_options"
					,"type" 	=> "info"
				);

$of_options[] = array( 	
					"name" 		=> "Blog Detail Thumbnail"
					,"desc" 	=> "Show/hide Thumbnail"
					,"id" 		=> "thenatives_blog_details_thumbnail"
					,"std" 		=> 1
					,"on" 		=> "Show"
					,"off" 		=> "Hide"
					,"folds"	=> 1
					,"type" 	=> "switch"		
				);
				
$of_options[] = array( 
					"name" 		=> "Blog Detail Time"
					,"desc" 	=> "Show/hide Time"
					,"id" 		=> "thenatives_blog_details_time"
					,"std" 		=> 1
					,"on" 		=> "Show"
					,"off" 		=> "Hide"
					,"folds"	=> 1
					,"type" 	=> "switch"		
				);	

$of_options[] = array( 	
					"name" 		=> "Blog Detail Author"
					,"desc" 	=> "Show/hide Author"
					,"id" 		=> "thenatives_blog_details_author"
					,"std" 		=> 1
					,"on" 		=> "Show"
					,"off" 		=> "Hide"
					,"folds"	=> 1
					,"type" 	=> "switch"		
				);

$of_options[] = array( 	
					"name" 		=> "Blog Detail Categories"
					,"desc" 	=> "Show/hide Categories"
					,"id" 		=> "thenatives_blog_details_categories"
					,"std" 		=> 1
					,"on" 		=> "Show"
					,"off" 		=> "Hide"
					,"folds"	=> 1
					,"type" 	=> "switch"		
				);

$of_options[] = array( 	
					"name" 		=> "Blog Detail Tags"
					,"desc" 	=> "Show/hide Tags"
					,"id" 		=> "thenatives_blog_details_tags"
					,"std" 		=> 1
					,"on" 		=> "Show"
					,"off" 		=> "Hide"
					,"folds"	=> 1
					,"type" 	=> "switch"		
				);

$of_options[] = array( 	
					"name" 		=> "Blog Detail Social Sharing"
					,"desc" 	=> "Show/hide Social Sharing"
					,"id" 		=> "thenatives_blog_details_socialsharing"
					,"std" 		=> 1
					,"on" 		=> "Show"
					,"off" 		=> "Hide"
					,"folds"	=> 1
					,"type" 	=> "switch"		
				);

$of_options[] = array( 	
					"name" 		=> "Blog Detail Related Posts"
					,"desc" 	=> "Show/hide Related Posts"
					,"id" 		=> "thenatives_blog_details_related"
					,"std" 		=> 1
					,"on" 		=> "Show"
					,"off" 		=> "Hide"
					,"folds"	=> 1
					,"type" 	=> "switch"		
				);
					
$of_options[] = array( 	
					"name" 		=> "Blog Detail Related Number"
					,"desc" 	=> "Related Number"
					,"id" 		=> "thenatives_blog_details_relatednumber"
					,"std" 		=> "6"
					,"mod"		=> "mini"
					,"fold"		=> "thenatives_blog_details_related"
					,"type" 	=> "select"	
					,"options"	=> array(4,5,6,7,8,9,10)
				);	
					
$of_options[] = array( 	
					"name" 		=> "Blog Detail Comment"
					,"desc" 	=> "Show/hide Comment"
					,"id" 		=> "thenatives_blog_details_comment"
					,"std" 		=> 1
					,"on" 		=> "Show"
					,"off" 		=> "Hide"
					,"folds"	=> 1
					,"type" 	=> "switch"		
				);

/***************** THENATIVES : Career Options ****************/
$of_options[] = array(
                "name" 		=> "Career"
                ,"type" 	=> "heading"
                ,"icon"		=> ' <i class="fa fa-newspaper-o" aria-hidden="true"></i>'
);

$of_options[] = array(
                "name" 		=> "General"
                ,"id" 		=> "info_general"
                ,"type" 	=> "info"
            );

$of_options[] = array(
                "name" 		=> "Advertise Top"
                ,"desc" 	=> "Add advertise top for career"
                ,"id" 		=> "thenatives_career_ads_top"
                ,"std"		=> ''
                ,"postType" => 'advertise'
                ,"type" 	=> "posts"
);

$of_options[] = array(
                "name" 		=> "Advertise Middle"
                ,"desc" 	=> "Add advertise middle for career"
                ,"id" 		=> "thenatives_career_ads_middle"
                ,"std"		=> ''
                ,"postType" => 'advertise'
                ,"type" 	=> "posts"
);

$of_options[] = array(
                "name" 		=> "Advertise Bottom"
                ,"desc" 	=> "Add advertise bottom for career"
                ,"id" 		=> "thenatives_career_ads_bottom"
                ,"std"		=> ''
                ,"postType" => 'advertise'
                ,"type" 	=> "posts"
);

$of_options[] = array(
                "name" 		=> "Form Title",
                "desc" 		=> "Title for Form.",
                "id" 		=> "thenatives_career_title_form",
                "std" 		=> "",
                "type" 		=> "textarea"
);

$of_options[] = array(
                "name" 		=> "Background Form"
                ,"desc" 	=> "Add background form for career"
                ,"id" 		=> "thenatives_career_bg_form"
                ,"std"		=> ''
                ,"type" 	=> "upload"
);

/***************** THENATIVES : Career Options ****************/
$of_options[] = array(
                "name" 		=> "Event"
                ,"type" 	=> "heading"
                ,"icon"		=> ' <i class="fa fa-newspaper-o" aria-hidden="true"></i>'
);

$of_options[] = array(
                "name" 		=> "Advertise Top"
                ,"desc" 	=> "Add advertise top for event"
                ,"id" 		=> "thenatives_event_ads_top"
                ,"std"		=> ''
                ,"postType" => 'advertise'
                ,"type" 	=> "posts"
);

$of_options[] = array(
                "name" 		=> "Advertise Middle"
                ,"desc" 	=> "Add advertise middle for event"
                ,"id" 		=> "thenatives_event_ads_middle"
                ,"std"		=> ''
                ,"postType" => 'advertise'
                ,"type" 	=> "posts"
);

$of_options[] = array(
                "name" 		=> "Advertise Bottom"
                ,"desc" 	=> "Add advertise bottom for event"
                ,"id" 		=> "thenatives_event_ads_bottom"
                ,"std"		=> ''
                ,"postType" => 'advertise'
                ,"type" 	=> "posts"
);

$of_options[] = array(
                "name" 		=> "Form Title",
                "desc" 		=> "Title for Form.",
                "id" 		=> "thenatives_event_title_form",
                "std" 		=> "",
                "type" 		=> "textarea"
);

$of_options[] = array(
                "name" 		=> "Background Form"
                ,"desc" 	=> "Add background form for event"
                ,"id" 		=> "thenatives_event_bg_form"
                ,"std"		=> ''
                ,"type" 	=> "upload"
);

/***************** THENATIVES : Career Options ****************/
$of_options[] = array(
                "name" 		=> "Sale"
                ,"type" 	=> "heading"
                ,"icon"		=> ' <i class="fa fa-newspaper-o" aria-hidden="true"></i>'
);

$of_options[] = array(
                "name" 		=> "Advertise Top"
                ,"desc" 	=> "Add advertise top for sale"
                ,"id" 		=> "thenatives_sale_ads_top"
                ,"std"		=> ''
                ,"postType" => 'advertise'
                ,"type" 	=> "posts"
);

$of_options[] = array(
                "name" 		=> "Advertise Middle"
                ,"desc" 	=> "Add advertise middle for sale"
                ,"id" 		=> "thenatives_sale_ads_middle"
                ,"std"		=> ''
                ,"postType" => 'advertise'
                ,"type" 	=> "posts"
);

$of_options[] = array(
                "name" 		=> "Advertise Bottom"
                ,"desc" 	=> "Add advertise bottom for sale"
                ,"id" 		=> "thenatives_sale_ads_bottom"
                ,"std"		=> ''
                ,"postType" => 'advertise'
                ,"type" 	=> "posts"
);

$of_options[] = array(
                "name" 		=> "Form Title",
                "desc" 		=> "Title for Form.",
                "id" 		=> "thenatives_sale_title_form",
                "std" 		=> "",
                "type" 		=> "textarea"
);

$of_options[] = array(
                "name" 		=> "Background Form"
                ,"desc" 	=> "Add background form for sale"
                ,"id" 		=> "thenatives_sale_bg_form"
                ,"std"		=> ''
                ,"type" 	=> "upload"
);

/***************** THENATIVES : Custom CSS ****************/
$of_options[] = array( 	
					"name" 		=> "Custom CSS"
					,"type" 	=> "heading"
					,"icon"		=> '<i class="fa fa-css3" aria-hidden="true"></i>'
				);

$of_options[] = array(
					"name" 		=> "Custom CSS"
					,"id" 		=> "info_custom_css"
					,"type" 	=> "info"
				);

$of_options[] = array( 	"name" 		=> "CSS Code",
						"desc" 		=> "Quickly add some CSS to your theme by adding it to this block.",
						"id" 		=> "thenatives_custom_css",
						"std" 		=> "",
						"type" 		=> "textarea"
				);

/***************** THENATIVES : Payment ****************/
$of_options[] = array( 	
					"name" 		=> "Payment Gateways",
					"type" 	=> "heading",
					"icon"		=> '<i class="fa fa-money" aria-hidden="true"></i>',
				);

$of_options[] = array(
					"name" 		=> "Stripe API"
					,"id" 		=> "payment_stipe"
					,"type" 	=> "info"
				);

$of_options[] = array( 	"name" 		=> "Publishable Key",
						"desc" 		=> 'Get Publishable Key <a href="https://dashboard.stripe.com/account/apikeys" target="_blank">here</a>',
						"id" 		=> "stripe_publishable_key",
						"type" 		=> "text"
					);
$of_options[] = array( 	"name" 		=> "Secret Key",
						"desc" 		=> 'Get Secret Key <a href="https://dashboard.stripe.com/account/apikeys" target="_blank">here</a>',
						"id" 		=> "stripe_secret_key",
						"type" 		=> "text"
				);
	}
}