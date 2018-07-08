<?php

if (!defined('ABSPATH')) exit;

?><article class="instashow-admin-page-feeds instashow-admin-page" data-is-admin-page-id="feeds">
    <div class="instashow-admin-page-heading">
        <h2><?php _e('Feeds', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></h2>

        <a class="instashow-admin-page-feeds-add-new instashow-admin-button-green instashow-admin-button" href="#/add-feed/" data-is-admin-page="add-feed"><?php _e('Add feed', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></a>

        <div class="instashow-admin-page-heading-subheading"><?php _e('Create, edit or remove your Instagram feeds. Use their shortcodes to insert them into the desired place.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
    </div>

    <table class="instashow-admin-page-feeds-list">
        <thead>
            <tr>
                <th><span><?php _e('Name', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span></th>
                <th><span><?php _e('Shortcode', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span></th>
                <th><span><?php _e('Actions', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span></th>
            </tr>
        </thead>

        <tbody></tbody>
    </table>

    <template class="instashow-admin-template-feeds-list-item instashow-admin-template">
        <tr class="instashow-admin-page-feeds-list-item">
            <td class="instashow-admin-page-feeds-list-item-name"><a href="#" data-is-admin-page="edit-feed"></a></td>

            <td class="instashow-admin-page-feeds-list-item-shortcode">
                <span class="instashow-admin-page-feeds-list-item-shortcode-hidden"></span>

                <input type="text" class="instashow-admin-page-feeds-list-item-shortcode-value" readonly></input>

                <div class="instashow-admin-page-feeds-list-item-shortcode-copy">
                    <span class="instashow-admin-page-feeds-list-item-shortcode-copy-trigger"><span>Copy</span></span>
                    
                    <div class="instashow-admin-page-feeds-list-item-shortcode-copy-error">Press Cmd+C to copy</div>
                </div>
            </td>

            <td class="instashow-admin-page-feeds-list-item-actions">
                <a href="#" class="instashow-admin-page-feeds-list-item-actions-edit"><?php _e('Edit', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></a>
                <a href="#" class="instashow-admin-page-feeds-list-item-actions-duplicate"><?php _e('Duplicate', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></a>
                <a href="#" class="instashow-admin-page-feeds-list-item-actions-remove"><?php _e('Remove', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></a>

                <span class="instashow-admin-page-feeds-list-item-actions-restore">
                    <span class="instashow-admin-page-feeds-list-item-actions-restore-label"><?php _e('The feed has been removed.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                    <a href="#"><?php _e('Restore it', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></a>
                </span>
            </td>
        </tr>
    </template>

     <template class="instashow-admin-template-feeds-list-empty instashow-admin-template">
        <tr class="instashow-admin-page-feeds-list-empty-item">
            <td class="instashow-admin-page-feeds-list-empty-item-text" colspan="3">
                <?php _e('There is no any feed yet.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                <a href="#/add-feed/" data-is-admin-page="add-feed"><?php _e('Create the first one.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></a>
            </td>
        </tr>
    </template>
</article>
