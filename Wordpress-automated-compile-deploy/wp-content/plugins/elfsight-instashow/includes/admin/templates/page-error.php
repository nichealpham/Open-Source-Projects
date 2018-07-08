<?php

if (!defined('ABSPATH')) exit;

?><article class="instashow-admin-page-error instashow-admin-page" data-is-admin-page-id="error">
    <h1><?php _e('Oops, something went wrong', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></h1>

    <p class="instashow-admin-page-error-message">
        <?php _e('Unfortunately, there is no such page in InstaShow.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
    </p>

    <a class="instashow-admin-page-error-button instashow-admin-button-large instashow-admin-button-green instashow-admin-button" href="#/feeds/" data-is-admin-page="feeds"><?php _e('Back to Home', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></a>
</article>