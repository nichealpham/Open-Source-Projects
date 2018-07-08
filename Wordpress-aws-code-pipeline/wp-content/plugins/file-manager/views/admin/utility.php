<?php
/**
 * 
 * @file utility.php Utility information about the plugin
 * 
 * */

// Security Check
if( !defined( 'ABSPATH' ) ) die();
global $FileManager;
?>
<?php

?>
<table>
	
	<tr>
		<td>PHP version</td>
		<td><?php echo phpversion(); ?></td>
	</tr>
	
	<tr>
		<td>Maximum file upload size</td>
		<td><?php echo ini_get('upload_max_filesize'); ?></td>
	</tr>

	<tr>
		<td>Post maximum file upload size</td>
		<td><?php echo ini_get('post_max_size'); ?></td>
	</tr>
	
	<tr>
		<td>Memory Limit</td>
		<td><?php echo ini_get('memory_limit'); ?></td>
	</tr>
	
	<tr>
		<td>Timeout</td>
		<td><?php echo ini_get('max_execution_time'); ?></td>
	</tr>
	
	<tr>
		<td>Browser and OS</td>
		<td><?php echo $_SERVER['HTTP_USER_AGENT']; ?></td>
	</tr>
	
	<tr>
		<td>DISALLOW_FILE_EDIT</td>
		<td>
		<?php
			if(defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) echo "TRUE";
				else echo "FALSE";
		?>
		</td>
	</tr>
	
</table>
