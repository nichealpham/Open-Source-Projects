<?php

if (!defined('ABSPATH')) exit;

?><article class="instashow-admin-page-edit-feed instashow-admin-page" data-is-admin-page-id="edit-feed">
    <div class="instashow-admin-page-heading">
        <a class="instashow-admin-page-back-button" href="#/feeds/" data-is-admin-page="feeds">
            <svg class="instashow-admin-svg-arrow-back">
                <line x1="0.5" y1="4.5" x2="4.5" y2="0"></line>
                <line x1="0.5" y1="4.5" x2="4.5" y2="8.5"></line>
            </svg>
            <?php _e('Back to list', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
        </a>

        <h2 class="instashow-admin-page-edit-feed-title-add"><?php _e('Add Feed', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></h2>

        <h2 class="instashow-admin-page-edit-feed-title-edit"><?php _e('Edit Feed', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></h2>

        <div class="instashow-admin-page-heading-subheading"><?php _e('Name your feed, adjust options and save it. In the feeds list you will see the shortcode of your new feed, which you can copy paste in the desired place of your website.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
    </div>

    <div class="instashow-admin-divider"></div>

    <div class="instashow-admin-page-edit-feed-form">
        <div class="instashow-admin-page-edit-feed-form-field">
            <label>
                <span class="instashow-admin-page-edit-feed-form-field-label"><?php _e('Feed name', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                <input class="instashow-admin-page-edit-feed-name-input" type="text" name="feedName">
            </label>        

            <div class="instashow-admin-page-edit-feed-form-field-hint">
                <?php _e('Give any name to your feed. It will be displayed only in your admin panel.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
            </div>
        </div>

        <div class="instashow-admin-divider"></div>

        <div class="instashow-admin-page-edit-feed-form-field">
            <div class="instashow-admin-page-edit-feed-form-field-label"><?php _e('Adjust options', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
            
            <div class="instashow-admin-demo-container"></div>

            <template class="instashow-admin-template-demo instashow-admin-template">
                <?php require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'admin', 'instashow-demo.php'))); ?>
            </template>

             <script>
                function getInstaShowDefaults() {
                    return <?php echo json_encode($instashow_json); ?>;
                }
            </script>
        </div>

        <div class="instashow-admin-page-edit-feed-form-field">
            <div class="instashow-admin-page-edit-feed-form-submit instashow-admin-button-large instashow-admin-button-green instashow-admin-button"><?php _e('Save feed', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
        </div>
    </div>
</article>