<?php
/**
 * 
 * @file donate.php Donate links will go here
 * 
 * */

// Security Check
if( !defined('ABSPATH') ) die();
return;
?>

<div class='fm-donate'>
<h2>Donate Us</h2>
<p>
	It takes time, effort and investment to develop, maintain and support a plugin. If you want us to continue further work on the plugin, please support us with your donation. Even a small amount of donation helps.
</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="X95MSRJU4MQW4">
	<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

</div>
