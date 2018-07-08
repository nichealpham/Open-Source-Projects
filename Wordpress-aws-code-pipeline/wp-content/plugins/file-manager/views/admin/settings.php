<?php 
if(!defined('ABSPATH')) die();
global $FileManager;

// Settings processing
if( isset( $_POST ) && !empty( $_POST ) ){
	
	if( ! wp_verify_nonce( $_POST['file-manager-settings-security-token'] ,'file-manager-settings-security-token') || !current_user_can( 'manage_options' ) ) wp_die();
	
	if( isset($_POST['show_url_path']) && ($_POST['show_url_path'] == 'show' || $_POST['show_url_path'] == 'hide') ) $FileManager->options['file_manager_settings']['show_url_path'] = $_POST['show_url_path']; 
	
	$FileManager->options['file_manager_settings']['language'] = sanitize_text_field($_POST['language']);
	
	$FileManager->options['file_manager_settings']['size']['width'] = $_POST['width'];
	$FileManager->options['file_manager_settings']['size']['height'] =$_POST['height'];
	
}

//~ $FileManager->pr($FileManager->options['file_manager_settings']);

$admin_page_url = admin_url()."admin.php?page={$FileManager->prefix}";

if( !isset($_GET['sub_page']) || empty($_GET['sub_page']) ) $_GET['sub_page'] = 'files';
// Escaping data
$_GET['sub_page'] = preg_replace( "/[<>#$%]/", "", $_GET['sub_page']);
// Sanitizing data
$_GET['sub_page'] = filter_var($_GET['sub_page'], FILTER_SANITIZE_STRING);

/**
 * 
 * array(
 * 	'page_slug' => array('page_slug', 'page_path', 'name')
 * )
 * 
 * */

$admin_menu_pages = array(
	'files' => array( 'files', ABSPATH . 'wp-content' . DS . 'plugins' . DS . 'file-manager' . DS . 'views' . DS . 'admin' . DS . 'files.php', 'Files'),
);

$admin_menu_pages = apply_filters('fm_admin_menu_sub_pages', $admin_menu_pages);

// Enqueing admin assets
$FileManager->admin_assets();

// Language
include 'language-code.php';
global $fm_languages;
?>
<?php require_once( 'header.php' ); ?>
<div class='fm-container'>
	
	<div class='col-main'>
		
		<div class='gb-fm-row fmp-settings'>
			
			<h2>Settings</h2>
		
			<form action='' method='post' class='fmp-settings-form'>
					<input type='hidden' name='file-manager-settings-security-token' value='<?php echo wp_create_nonce('file-manager-settings-security-token'); ?>'>
					<table>
						<tr>
							<td><h4>URL and Path</h4></td>
							<td>
								<label for='show_url_path_id'> Show </label>
								<input type='radio' name='show_url_path' id='show_url_path_id' value='show' <?php  if( isset( $FileManager->options['file_manager_settings']['show_url_path'] ) && !empty( $FileManager->options['file_manager_settings']['show_url_path'] ) && $FileManager->options['file_manager_settings']['show_url_path'] == 'show' ) echo 'checked'; ?>/>
								
								<label for='hide_url_path_id'> Hide </label>
								<input type='radio' name='show_url_path' id='hide_url_path_id' value='hide' <?php  if( isset( $FileManager->options['file_manager_settings']['show_url_path'] ) && !empty( $FileManager->options['file_manager_settings']['show_url_path'] ) && $FileManager->options['file_manager_settings']['show_url_path'] == 'hide' ) echo 'checked'; ?>/>
							</td>
						</tr>
						<tr>
							<td><h4>Select Language</h4></td>
							<td>
								<?php 
									$lang = $fm_languages->available_languages(); 
									if(!is_array($FileManager->options['file_manager_settings']['language'])) $language_settings = unserialize(stripslashes($FileManager->options['file_manager_settings']['language']));
										else $language_settings = $FileManager->options['file_manager_settings']['language'];
									$language_code = $language_settings['code'];
									
								?>
								<select name='language'>
									<?php foreach($lang as $L): ?>
									<option <?php if($language_code == $L['code']) echo "selected='selected'"; ?> value='<?php echo serialize($L)?>'><?php echo $L['name']?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><h4>Size</h4></td>
							<td>
								<label for='fm-width-id'>Width</label><input id='fm-width-id' type='text' name='width' value='<?php if(isset($FileManager->options['file_manager_settings']['size']['width']) && !empty($FileManager->options['file_manager_settings']['size']['width'])) echo $FileManager->options['file_manager_settings']['size']['width']; else echo 'auto';?>'>
								<label for='fm-height-id'>Height</label><input id='fm-height-id' type='text' name='height' value='<?php if(isset($FileManager->options['file_manager_settings']['size']['height']) && !empty($FileManager->options['file_manager_settings']['size']['height'])) echo $FileManager->options['file_manager_settings']['size']['height']; else echo 400;?>'>
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<input type='submit' value='Save' />
							</td>
						</tr>
					</table>
					
			</form>
		
		</div>
		
		<div class='gb-fm-row fm-data'>
			<?php require_once('utility.php'); ?>
		</div>
		
	</div>
	
	<?php require_once('sidebar.php'); ?>
	
</div>

<?php require_once('footer.php'); ?>
<!--

-->
