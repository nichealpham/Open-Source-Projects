<?php
/**
 *
 * @file footer.php Footer file of the plugin
 *
 * */
// Security check
if( !defined('ABSPATH') ) die();
global $FileManager;
?>
<div class='fm-footer'>
	
	<ul>
		<li><a href='http://giribaz.com/faq/'>FAQ</a></li>
		<li><a href='https://wordpress.org/plugins/file-manager/changelog/'>Changelog</a></li>
		<li><a href='http://giribaz.com/contacts/'>Contacts</a></li>
		<li><a href='http://giribaz.com/documentations/'>Docs</a></li>
		<li><a href='<?= $FileManager->feedback_page; ?>'>Review</a></li>
		<li><a href='<?= $FileManager->support_page; ?>'>Help & Support</a></li>
		<li><a href='<?= $FileManager->site; ?>'>Giribaz</a></li>
	</ul>

</div>
