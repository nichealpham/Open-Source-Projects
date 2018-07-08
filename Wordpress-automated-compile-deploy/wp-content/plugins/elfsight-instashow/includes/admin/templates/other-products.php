<?php

if (!defined('ABSPATH')) exit;

?><div class="<?php echo !empty($other_products_hidden) && $other_products_hidden === 'true' ? 'instashow-admin-other-products-hidden-permanently' : 'instashow-admin-other-products-hidden'; ?> instashow-admin-other-products" data-nonce="<?php echo wp_create_nonce('elfsight_instashow_hide_other_products_nonce'); ?>">
	<h4 class="instashow-admin-other-products-title"><?php _e('More products by Elfsight', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></h4>

	<a href="#" class="instashow-admin-other-products-close"><?php _e('Close', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></a>

	<div class="instashow-admin-other-products-list">
		<div class="instashow-admin-other-products-list-item-yottie instashow-admin-other-products-list-item">
			<a href="<?php echo ELFSIGHT_INSTASHOW_YOTTIE_URL; ?>" target="_blank">
				<span class="instashow-admin-other-products-list-item-info">
					<img class="instashow-admin-other-products-list-item-image" src="<?php echo plugins_url('assets/img/yottie-logo.svg', ELFSIGHT_INSTASHOW_FILE); ?>">
			
					<span class="instashow-admin-other-products-list-item-title"><?php _e('Yottie', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
					<span class="instashow-admin-other-products-list-item-description"><?php _e('Display YouTube channels and videos on your website.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
				</span>

				<span class="instashow-admin-other-products-list-item-more">
					<span class="instashow-admin-other-products-list-item-more-label">
						<?php _e('Learn more', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
					</span>

					<svg class="instashow-admin-svg-arrow-more">
                        <line x1="0" y1="0" x2="4" y2="4"></line>
                        <line x1="0" y1="8" x2="4" y2="4"></line>
                    </svg>
				</span>
			</a>
		</div>

		<div class="instashow-admin-other-products-list-item-instalink instashow-admin-other-products-list-item">
			<a href="<?php echo ELFSIGHT_INSTASHOW_INSTALINK_URL; ?>" target="_blank">
				<span class="instashow-admin-other-products-list-item-info">
					<img class="instashow-admin-other-products-list-item-image" src="<?php echo plugins_url('assets/img/instalink-logo.svg', ELFSIGHT_INSTASHOW_FILE); ?>">
			
					<span class="instashow-admin-other-products-list-item-title"><?php _e('InstaLink', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
					<span class="instashow-admin-other-products-list-item-description"><?php _e('Embed Instagram profile to your website.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
				</span>

				<span class="instashow-admin-other-products-list-item-more">
					<span class="instashow-admin-other-products-list-item-more-label">
						<?php _e('Learn more', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
					</span>

					<svg class="instashow-admin-svg-arrow-more">
                        <line x1="0" y1="0" x2="4" y2="4"></line>
                        <line x1="0" y1="8" x2="4" y2="4"></line>
                    </svg>
				</span>
			</a>
		</div>
	</div>
</div>