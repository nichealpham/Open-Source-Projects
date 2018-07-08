<?php

if (!defined('ABSPATH')) exit;

?><div class="instashow-admin-menu-actions">
	<div class="instashow-admin-menu-actions-activate-container">
    	<a class="instashow-admin-menu-actions-activate instashow-admin-button-red instashow-admin-button-border instashow-admin-button" href="#/activation/" data-is-admin-page="activation"><?php _e('Activate now', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></a>
	</div>

    <?php if ($has_new_version) {?>
        <span class="instashow-admin-menu-actions-update-container">
        	<span class="instashow-admin-menu-actions-update-label instashow-admin-tag-2"><?php _e('A new version is available', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

        	<a class="instashow-admin-menu-actions-update instashow-admin-button-green instashow-admin-button" href="<?php echo is_multisite() ? network_admin_url('update-core.php') : admin_url('update-core.php'); ?>"><?php _e('Update to', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?> <?php echo $latest_version; ?></a>
    	</span>
    <?php }?>
</div>