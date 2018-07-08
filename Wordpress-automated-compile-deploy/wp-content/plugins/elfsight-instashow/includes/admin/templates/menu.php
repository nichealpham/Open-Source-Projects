<?php

if (!defined('ABSPATH')) exit;

?><nav class="instashow-admin-menu">
    <ul class="instashow-admin-menu-list">
        <li class="instashow-admin-menu-list-item"><a href="#/feeds/" data-is-admin-page="feeds"><?php _e('Feeds', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></a></li>
        <li class="instashow-admin-menu-list-item"><a href="#/support/" data-is-admin-page="support"><?php _e('Support', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></a></li>
        <li class="instashow-admin-menu-list-item"><a href="#/preferences/" data-is-admin-page="preferences"><?php _e('Preferences', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></a></li>
        <li class="instashow-admin-menu-list-item-activation instashow-admin-menu-list-item">
            <a href="#/activation/" data-is-admin-page="activation" class="instashow-admin-tooltip-trigger">
                <?php _e('Activation', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>

                <span class="instashow-admin-menu-list-item-notification"></span>

                <span class="instashow-admin-tooltip-content">
                    <span class="instashow-admin-tooltip-content-inner">
                        <?php _e('InstaShow is not activated', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                    </span>
                </span>
            </a>
        </li>
    </ul>
</nav>   