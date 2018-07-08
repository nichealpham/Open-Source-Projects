<?php
/**
 * 
 * @file site-backup.php Bacup and restore site
 * 
 * */

// Security Check
if( !defined('ABSPATH') ) die();
global $FileManager;
$FileManager->admin_assets();
?>
<?php require_once( 'header.php' ); ?>

<div class='fm-container'>
	
	<div class='col-main'>
		
		<div class='gb-fm-row backup-restore'>
			<h2>Backup & Restore</h2>
			<ul>
				<li id='fm-backup'>Backup Now</li>
			</ul>
			<br/>
			<br/>
			<br/>
			<br/>
			<table>
				<tr>
					<th>File Name</th>
					<th>Size</th>
					<th>Date</th>
					<th>Actions</th>
				</tr>
				<tr>
					<td>979870_site_backup.zip</td>
					<td>89.09 MB</td>
					<td>20/09/2016</td>
					<td>
						<button>Restore</button>
						<button>Download</button>
						<button>Delete</button>
					</td>
				</tr>
			</table>
		</div>
		
		<div class='gb-fm-row fm-data'>
			<?php require_once('utility.php'); ?>
		</div>
		
	</div>
	
	<?php require_once('sidebar.php'); ?>
	
</div>

<div id="dialog" title="Backup Site">
	
	<input type='radio' id='fm-full-backup-id' name='fm-full-backup' value='fm-full-backup' checked>
	<label for='fm-full-backup-id'>Full Backup</label>
	
	<br/>
	<br/>
	<input type='radio' id='fm-partial-backup-id' name='fm-full-backup' value='fm-partial-backup'>
	<label for='fm-partial-backup-id'>Partial Backup</label>
		
	<div id='fm-custom-backup-wrapper'>
	
		<br/>
		<input type='checkbox' value='database' name='database' id='fm-database-backup-id'>
		<label for='fm-database-backup-id'>Database Backup</label>
		
		<br/>
		<input type='checkbox' value='file' name='file' id='fm-file-backup-id'>
		<label for='fm-file-backup-id'>File Backup</label>
		
		<div id='fm-partial-file-backup-wrapper-id'>
			
			<input type='checkbox' value='wp-content' name='wp-content' id='fm-wp-content-id'>
			<label for='fm-wp-content-id'>wp-content</label>
			<br/>
			<input type='checkbox' value='plugins' name='plugins' id='fm-plugins-id'>
			<label for='fm-plugins-id'>plugins</label>
			
			<br/>
			<input type='checkbox' value='themes' name='themes' id='fm-themes-id'>
			<label for='fm-themes-id'>themes</label>
			
			<br/>
			<input type='checkbox' value='uploads' name='uploads' id='fm-uploads-id'>
			<label for='fm-uploads-id'>uploads</label>
			
		</div>
		
	</div>
	
	<br/>
	<br/>
	<input id='fm-submit-id' type='submit' value='Backup Now'>
	
</div>

<?php require_once('footer.php'); ?>

<script>

jQuery(document).ready(function(){
	
	jQuery('#fm-submit-id').on('click', function(){
	
	// Extracting data
	var backup_instructions = {};
	if( jQuery('#fm-full-backup-id').is(':checked') ){
		
		var data = {
			'action': 'fm_site_backup',
			'instructions': {
				'full-backup' : true,
			}
		}; 
		
		jQuery.post(ajaxurl, data, function(response) {
			alert('Got this from the server: ' + response);
		});
		
	}
	
});

});

jQuery( function() {
    jQuery( "#dialog" ).dialog({
		autoOpen: false,
		show: {
			effect: "drop",
			duration: 1000
		},
		hide: {
			effect: "drop",
			duration: 1000
		}
    });
 
    jQuery( "#fm-backup" ).on( "click", function() {
      jQuery( "#dialog" ).dialog( "open" );
    });
  } );
 
jQuery(document).ready(function(){
	
	// Hiding partial backup portion
	if( !jQuery('#fm-partial-backup-id').is(':checked') ) jQuery('#fm-custom-backup-wrapper').hide()
	
	// Hiding file backup portion on starting
	if( !jQuery('#fm-file-backup-id').is(':checked') ) jQuery('#fm-partial-file-backup-wrapper-id').hide();
	
	// Toggling of the partial backup portion
	jQuery('#fm-partial-backup-id').on('change', function(){
		
		if( jQuery('#fm-partial-backup-id').is(':checked') ) jQuery('#fm-custom-backup-wrapper').show();
			else jQuery('#fm-custom-backup-wrapper').hide();
	});
	
	// Toggling of the partial backup portion
	jQuery('#fm-full-backup-id').on('change', function(){
		
		if( jQuery('#fm-full-backup-id').is(':checked') ) jQuery('#fm-custom-backup-wrapper').hide();
			else jQuery('#fm-custom-backup-wrapper').show();
	});
	
	// File Backup toggling
	jQuery('#fm-file-backup-id').on('change', function(){
		
		if( jQuery('#fm-file-backup-id').is(':checked') ) jQuery('#fm-partial-file-backup-wrapper-id').show();
			else jQuery('#fm-partial-file-backup-wrapper-id').hide();
	});
	
});
</script>

<style>
.backup-restore > h2 {
	text-align: center;
	padding-top: 20px;
	text-decoration: underline;
}

.backup-restore > ul {
	float: right;
}

.backup-restore > ul > li {
	display: inline-block;
	margin: 10px;
	padding: 10px;
	font-size: 120%;
	color: white;
	background-color: #0073AA;
}

.backup-restore > ul > li:hover{
	cursor: pointer;
}

.backup-restore > table{
	margin-left: auto;
	margin-right: auto;
}
.backup-restore > table td {
	padding: 10px;
}

#fm-submit-id{
	padding: 10px;
	color: white;
	background-color: #F8B74C;
}

/**
Popup form style
*/
#fm-custom-backup-wrapper{
	padding-left: 20px;
}

#fm-partial-file-backup-wrapper-id{
	padding-left: 20px;
}
</style>
