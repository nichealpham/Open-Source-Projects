<?php

if (!defined('ABSPATH')) exit;

?><article class="instashow-admin-page-preferences instashow-admin-page" data-is-admin-page-id="preferences">
	<div class="instashow-admin-page-heading">
		<h2><?php _e('Preferences', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></h2>

		<div class="instashow-admin-page-heading-subheading">
			<?php _e('These settings will be accepted for each InstaShow feed<br> on your website.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
		</div>
    </div>

    <div class="instashow-admin-divider"></div>

	<div class="instashow-admin-page-preferences-form" data-nonce="<?php echo wp_create_nonce('elfsight_instashow_update_preferences_nonce'); ?>">
        <div class="instashow-admin-page-preferences-option-force-script instashow-admin-page-preferences-option">
            <div class="instashow-admin-page-preferences-option-info">
                <h4 class="instashow-admin-page-preferences-option-info-name">
                    <label for="forceScriptAdd"><?php _e('Add InstaShow script to every page', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></label>
                </h4>

                <div class="instashow-admin-caption">
                    <?php _e('By default the plugin adds its scripts only on pages with InstaShow shortcode. This option makes the plugin add scripts on every page. It is useful for ajax websites.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                </div>
            </div>

            <div class="instashow-admin-page-preferences-option-input-container">
                <input type="checkbox" name="preferences_force_script_add" value="true" id="forceScriptAdd" class="instashow-admin-page-preferences-option-input-toggle"<?php echo ($preferences_force_script_add === 'on') ? ' checked' : ''?>>
                <label for="forceScriptAdd"><i></i></label>
            </div>
        </div>

        <div class="instashow-admin-divider"></div>

        <div class="instashow-admin-page-preferences-option-css instashow-admin-page-preferences-option">
            <div class="instashow-admin-page-preferences-option-info">
                <h4 class="instashow-admin-page-preferences-option-info-name">
                    <?php _e('Custom CSS', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                </h4>

                <div class="instashow-admin-caption">
                    <?php _e('Here you can specify custom styles for InstaShow. It will be printed on each page with the widget.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                </div>
            </div>

            <div class="instashow-admin-page-preferences-option-input-container">
                <div class="instashow-admin-page-preferences-option-editor">
                    <div class="instashow-admin-page-preferences-option-editor-code" id="instaShowPreferencesSnippetCSS"><?php echo htmlspecialchars($preferences_custom_css)?></div>
                </div>

                <div class="instashow-admin-page-preferences-option-save-container">
                    <a href="#" class="instashow-admin-page-preferences-option-css-save instashow-admin-page-preferences-option-save instashow-admin-button-green instashow-admin-button">
                        <span class="instashow-admin-page-preferences-option-save-label"><?php _e('Save', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                        <span class="instashow-admin-page-preferences-option-save-loader"></span>
                    </a>

                    <span class="instashow-admin-page-preferences-option-save-success">
                        <span class="instashow-admin-icon-check-green-small instashow-admin-icon"></span><span class="instashow-admin-page-preferences-option-save-success-label"><?php _e('Done!', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                    </span>

                    <span class="instashow-admin-page-preferences-option-save-error"></span>
                </div>
            </div>
        </div>

        <div class="instashow-admin-divider"></div>

        <div class="instashow-admin-page-preferences-option-js instashow-admin-page-preferences-option">
            <div class="instashow-admin-page-preferences-option-info">
                <h4 class="instashow-admin-page-preferences-option-info-name">
                    <?php _e('Custom JavaScript', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                </h4>

                <div class="instashow-admin-caption">
                    <?php _e('Here you can specify custom JS for initiation of InstaShow. This script will be printed on each page with the widget.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                </div>
            </div>
            
            <div class="instashow-admin-page-preferences-option-input-container">
                <div class="instashow-admin-page-preferences-option-editor">
                    <div class="instashow-admin-page-preferences-option-editor-code" id="instaShowPreferencesSnippetJS"><?php echo htmlspecialchars($preferences_custom_js) ?></div>
                </div>

                <div class="instashow-admin-page-preferences-option-save-container">
                    <a href="#" class="instashow-admin-page-preferences-option-js-save instashow-admin-page-preferences-option-save instashow-admin-button-green instashow-admin-button">
                        <span class="instashow-admin-page-preferences-option-save-label"><?php _e('Save', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                        <span class="instashow-admin-page-preferences-option-save-loader"></span>
                    </a>

                    <span class="instashow-admin-page-preferences-option-save-success">
                        <span class="instashow-admin-icon-check-green-small instashow-admin-icon"></span><span class="instashow-admin-page-preferences-option-save-success-label"><?php _e('Done!', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                    </span>

                    <span class="instashow-admin-page-preferences-option-save-error"></span>
                </div>
            </div>
        </div>

        <div class="instashow-admin-divider"></div>

        <div class="instashow-admin-page-preferences-option-api-settings instashow-admin-page-preferences-option">
            <div class="instashow-admin-page-preferences-option-info">
                <h4 class="instashow-admin-page-preferences-option-info-name">
                    <label><?php _e('API Settings (Advanced)', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></label>
                </h4>

                <div class="instashow-admin-caption">
                    <div><?php _e('You can manage some settings of the plugin\'s API. There are 4 available options:', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
                    <ul>
                        <li><b><?php _e('Media limit', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></b> <?php _e('allows you to restrict the number of photos which you get from Instagram. The number of photos influences of the plugin\'s performance. We don\'t recommend you to set more than 100 photos without a real need. Default value: 100', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></li>
                        <li><b><?php _e('Cache time', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></b> <?php _e('defines how often in seconds the plugin requests Instagram and loads new photos. The option also affects the plugin\'s loading speed and your server load. We don\'t recommend you to set less than 3600 seconds. Default value: 3600', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></li>
                        <li><b><?php _e('Allowed usernames', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></b> <?php _e('is a security option. You can set the list of available usernames (without "@" symbol) separated by commas to prevent the usage of the plugin by third-parties. Default value: *', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></li>
                        <li><b><?php _e('Allowed tags', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></b> <?php _e('is a security option. You can set the list of available hashtags (without "#"" symbol) separated by commas to prevent the usage of the plugin by third-parties. Default value: *', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></li>
                    </ul>
                </div>
            </div>

            <div class="instashow-admin-page-preferences-option-input-container">
                <label class="instashow-admin-page-preferences-option-api-settings-media-limit">
                    <?php _e('Media limit', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                    <input type="text" name="preferences_media_limit" value="<?php echo $preferences_media_limit; ?>">
                </label>

                <label class="instashow-admin-page-preferences-option-api-settings-cache-time">
                    <?php _e('Cache time', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                    <input type="text" name="preferences_cache_time" value="<?php echo $preferences_cache_time; ?>">
                </label>

                <label class="instashow-admin-page-preferences-option-api-settings-allowed-usernames">
                    <?php _e('Allowed usernames', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                    <input type="text" name="preferences_allowed_usernames" value="<?php echo $preferences_allowed_usernames; ?>">
                </label>

                <label class="instashow-admin-page-preferences-option-api-settings-allowed-tags">
                    <?php _e('Allowed tags', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                    <input type="text" name="preferences_allowed_tags" value="<?php echo $preferences_allowed_tags; ?>">
                </label>
                
                <div class="instashow-admin-page-preferences-option-save-container">
                    <?php if(empty($preferences_api_config_error)) {?>
                        <a href="#" class="instashow-admin-page-preferences-option-api-settings-save instashow-admin-page-preferences-option-save instashow-admin-button-green instashow-admin-button">
                            <span class="instashow-admin-page-preferences-option-save-label"><?php _e('Save', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                            <span class="instashow-admin-page-preferences-option-save-loader"></span>
                        </a>

                        <span class="instashow-admin-page-preferences-option-save-success">
                            <span class="instashow-admin-icon-check-green-small instashow-admin-icon"></span><span class="instashow-admin-page-preferences-option-save-success-label"><?php _e('Done!', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                        </span>

                        <span class="instashow-admin-page-preferences-option-save-error"></span>
                    <?php } else { ?>
                        <div class="instashow-admin-page-preferences-option-save-error"><?php echo $preferences_api_config_error; ?></div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="instashow-admin-divider"></div>

        <div class="instashow-admin-page-preferences-option-custom-api-url instashow-admin-page-preferences-option">
            <div class="instashow-admin-page-preferences-option-info">
                <h4 class="instashow-admin-page-preferences-option-info-name">
                    <label for="customApiUrl"><?php _e('Custom API URL', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></label>
                </h4>

                <div class="instashow-admin-caption">
                    <?php _e('Defines URL address to the plugin api directory. In case you don\'t know how this option works, please, don\'t change the specified URL. Default value: ' , ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                    <?php echo ELFSIGHT_INSTASHOW_API_URL; ?>
                </div>
            </div>

            <div class="instashow-admin-page-preferences-option-input-container">
                <input type="text" name="preferences_custom_api_url" id="customApiUrl" value="<?php echo $preferences_custom_api_url; ?>">
                
                <div class="instashow-admin-page-preferences-option-save-container">
                    <a href="#" class="instashow-admin-page-preferences-option-custom-api-url-save instashow-admin-page-preferences-option-save instashow-admin-button-green instashow-admin-button">
                        <span class="instashow-admin-page-preferences-option-save-label"><?php _e('Save', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                        <span class="instashow-admin-page-preferences-option-save-loader"></span>
                    </a>

                    <span class="instashow-admin-page-preferences-option-save-success">
                        <span class="instashow-admin-icon-check-green-small instashow-admin-icon"></span><span class="instashow-admin-page-preferences-option-save-success-label"><?php _e('Done!', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                    </span>

                    <span class="instashow-admin-page-preferences-option-save-error"></span>
                </div>
            </div>
        </div>
    </div>
</article>