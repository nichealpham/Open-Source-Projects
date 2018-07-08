<?php

if (!defined('ABSPATH')) exit;

?><header class="instashow-admin-header">
    <div class="instashow-admin-header-title"><?php _e('Instagram Feed Plugin', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

    <a class="instashow-admin-header-logo" href="<?php echo admin_url('admin.php?page=elfsight-instashow'); ?>" title="<?php _e('InstaShow - WordPress Instagram Feed Plugin', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>">
        <img src="<?php echo plugins_url('assets/img/logo.png', ELFSIGHT_INSTASHOW_FILE); ?>" width="169" height="44" alt="<?php _e('InstaShow - WordPress Instagram Feed Plugin', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>">
    </a>

    <div class="instashow-admin-header-version">
        <span class="instashow-admin-tooltip-trigger">
            <span class="instashow-admin-tag-2"><?php _e('Version ' . ELFSIGHT_INSTASHOW_VERSION, ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
            
            <?php if ($activated && !empty($last_check_datetime) && !$has_new_version): ?>
                <span class="instashow-admin-tooltip-content">
                    <span class="instashow-admin-tooltip-content-inner">
                        <b><?php _e('You have the latest version', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></b><br>
                        <?php printf(__('Last checked on %1$s at %2$s', ELFSIGHT_INSTASHOW_TEXTDOMAIN), date_i18n(get_option('date_format'), $last_check_datetime), date_i18n(get_option('time_format'), $last_check_datetime)); ?>
                    </span>
                </span>
            <?php endif ?>
        </span>
    </div>
    
    <div class="instashow-admin-header-support">
        <a class="instashow-admin-button-transparent instashow-admin-button-small instashow-admin-button" href="#/support/" data-is-admin-page="support"><?php _e('Need help?', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></a>
    </div>
</header>