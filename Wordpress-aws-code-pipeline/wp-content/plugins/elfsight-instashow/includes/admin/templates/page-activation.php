<?php

if (!defined('ABSPATH')) exit;

?><article class="instashow-admin-page-activation instashow-admin-page" data-is-admin-page-id="activation">
	<div class="instashow-admin-page-heading">
		<h2><?php _e('Activation', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></h2>

        <div class="instashow-admin-page-activation-status">
            <span class="instashow-admin-page-activation-status-activated"><?php _e('Activated', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
            <span class="instashow-admin-page-activation-status-not-activated"><?php _e('Not Activated', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
        </div>

		<div class="instashow-admin-page-heading-subheading">
			<?php _e('Activate your plugin in order to get awesome benefits for our customers!', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
		</div>
    </div>

    <div class="instashow-admin-divider"></div>

    <div class="instashow-admin-page-activation-benefits">
        <h4><?php _e('Get Awesome Benefits', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></h4>

        <ul class="instashow-admin-page-activation-benefits-list">
            <li class="instashow-admin-page-activation-benefits-list-item-live-updates instashow-admin-page-activation-benefits-list-item">
                <div class="instashow-admin-page-activation-benefits-list-item-icon-container">
                    <span class="instashow-admin-page-activation-benefits-list-item-icon">
                        <span class="instashow-admin-icon-live-updates instashow-admin-icon"></span>
                    </span>
                </div>

                <div class="instashow-admin-page-activation-benefits-list-item-info">
                    <div class="instashow-admin-page-activation-benefits-list-item-title"><?php _e('Simple Live Updates', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                    <div class="instashow-admin-page-activation-benefits-list-item-description"><?php _e('Always be aware of fresh updates and download them easily and quickly right from your admin panel.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
                </div>
            </li>

            <li class="instashow-admin-page-activation-benefits-list-item-support instashow-admin-page-activation-benefits-list-item">
                <div class="instashow-admin-page-activation-benefits-list-item-icon-container">
                    <span class="instashow-admin-page-activation-benefits-list-item-icon">
                        <span class="instashow-admin-icon-support instashow-admin-icon"></span>
                    </span>
                </div>

                <div class="instashow-admin-page-activation-benefits-list-item-info">
                    <div class="instashow-admin-page-activation-benefits-list-item-title"><?php _e('Fast & Premium Support', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                    <div class="instashow-admin-page-activation-benefits-list-item-description"><?php _e('Submit your ticket and get our direct support in the fastest way. We are ready to solve all your issues.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
                </div>
            </li>
        </ul>
    </div>

    <div class="instashow-admin-divider"></div>

	<div class="instashow-admin-page-activation-form-container">
        <form class="instashow-admin-page-activation-form" data-nonce="<?php echo wp_create_nonce('elfsight_instashow_update_activation_data_nonce'); ?>" data-activation-url="<?php echo ELFSIGHT_INSTASHOW_UPDATE_URL; ?>" data-activation-slug="<?php echo ELFSIGHT_INSTASHOW_SLUG; ?>" data-activation-version="<?php echo ELFSIGHT_INSTASHOW_VERSION; ?>">
            <h4><?php _e('Activate InstaShow', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></h4>

            <div class="instashow-admin-page-activation-form-field">
                <label>
                    <span class="instashow-admin-page-activation-form-field-label"><?php _e('Please enter your CodeCanyon InstaShow purchase code', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                    <input class="instashow-admin-page-activation-form-activated-input" type="hidden" name="activated" value="<?php echo $activated; ?>">
                    <input class="instashow-admin-page-activation-form-purchase-code-input" type="text" placeholder="<?php _e('Purchase code', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>" name="purchase_code" value="<?php echo $purchase_code; ?>" class="regular-text" spellcheck="false" autocomplete="off">
                </label>
            </div>

            <div class="instashow-admin-page-activation-form-message-success instashow-admin-page-activation-form-message"><?php _e('InstaShow is successfuly activated', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
            <div class="instashow-admin-page-activation-form-message-error instashow-admin-page-activation-form-message"><?php _e('Your purchase code is not valid', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
            <div class="instashow-admin-page-activation-form-message-fail instashow-admin-page-activation-form-message"><?php _e('Error occurred while checking your purchase code. Please, contact our support team via <a href="mailto:support@elfsight.com">support@elfsight.com</a>. We apologize for inconveniences.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

            <div class="instashow-admin-page-activation-form-field">
                <div class="instashow-admin-page-activation-form-submit instashow-admin-button-green instashow-admin-button"><?php _e('Activate', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
            </div>
        </form>

        <div class="instashow-admin-page-activation-faq">
            <h4><?php _e('FAQ', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></h4>

            <ul class="instashow-admin-page-activation-faq-list">
                <li class="instashow-admin-page-activation-faq-list-item">
                    <div class="instashow-admin-page-activation-faq-list-item-title"><?php _e('What is item purchase code?', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
                    <div class="instashow-admin-page-activation-faq-list-item-text">
                        <?php printf(__('Purchase code is a licensed key, which you will get after buying item on <a href="%1$s" target="_blank">Codecanyon</a>.', ELFSIGHT_INSTASHOW_TEXTDOMAIN), ELFSIGHT_INSTASHOW_PRODUCT_URL); ?>
                    </div>
                </li>

                <li class="instashow-admin-page-activation-faq-list-item">
                    <div class="instashow-admin-page-activation-faq-list-item-title"><?php _e('How to get purchase code?', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
                    <div class="instashow-admin-page-activation-faq-list-item-text">
                        <?php _e('After buying the item you have to visit the following page <a href="http://codecanyon.net/downloads" target="_blank">http://codecanyon.net/downloads</a>, click the Download button and select “License Certificate & Purchase Code”. In the downloaded file you’ll find your purchase code. More info in our article:<br><a href="https://elfsight.com/blog/2016/04/where-to-find-your-envato-purchase-code/" target="_blank">Where to find your Envato purchase code?</a>.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</article>