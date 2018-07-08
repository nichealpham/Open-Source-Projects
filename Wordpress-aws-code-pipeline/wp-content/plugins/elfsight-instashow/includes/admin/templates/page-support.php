<?php

if (!defined('ABSPATH')) exit;

?><article class="instashow-admin-page-support instashow-admin-page" data-is-admin-page-id="support">
	<div class="instashow-admin-page-heading">
		<h2><?php _e('Support', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></h2>

		<div class="instashow-admin-page-heading-subheading">
			<?php _e('We understand all the importance of product support for our customers. That’s why we are ready to solve all your issues and answer any question related to our plugin.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
		</div>
    </div>

    <div class="instashow-admin-divider"></div>

	<div class="instashow-admin-page-support-ticket">
		<h4><?php _e('Before submitting a ticket, be sure that:', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></h4>

		<ul class="instashow-admin-page-support-ticket-steps">
			<li class="instashow-admin-page-support-ticket-steps-item-latest-version instashow-admin-page-support-ticket-steps-item">
				<span class="instashow-admin-page-support-ticket-steps-item-icon">
					<span class="instashow-admin-icon-support-latest-version instashow-admin-icon"></span>
				</span>

				<span class="instashow-admin-page-support-ticket-steps-item-label"><?php _e('You use the latest version', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
			</li>

			<li class="instashow-admin-page-support-ticket-steps-item-javascript-errors instashow-admin-page-support-ticket-steps-item">
				<span class="instashow-admin-page-support-ticket-steps-item-icon">
					<span class="instashow-admin-icon-support-javascript-errors instashow-admin-icon"></span>
				</span>

				<span class="instashow-admin-page-support-ticket-steps-item-label"><?php _e('There are no javascript errors on your website', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
			</li>

			<li class="instashow-admin-page-support-ticket-steps-item-documentation instashow-admin-page-support-ticket-steps-item">
				<span class="instashow-admin-page-support-ticket-steps-item-icon">
					<span class="instashow-admin-icon-support-documentation instashow-admin-icon"></span>
				</span>

				<span class="instashow-admin-page-support-ticket-steps-item-label"><?php _e('The documentation can\'t help', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
			</li>
		</ul>

		<div class="instashow-admin-page-support-ticket-submit">
			<?php printf(__('Nothing of the above helped? <a href="%1$s" target="_blank">Submit a ticket</a> to our Support Center.', ELFSIGHT_INSTASHOW_TEXTDOMAIN), ELFSIGHT_INSTASHOW_SUPPORT_URL); ?>
		</div>
	</div>

	<div class="instashow-admin-divider"></div>

	<div class="instashow-admin-page-support-includes-container">
		<div class="instashow-admin-page-support-includes">
			<h4><?php _e('Our Support Includes', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></h4>

			<ul class="instashow-admin-page-support-includes-list">
				<li class="instashow-admin-page-support-includes-list-item">
					<div class="instashow-admin-page-support-includes-list-item-title"><?php _e('Fixing Product Bugs', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
					
					<p class="instashow-admin-page-support-includes-list-item-description"><?php _e('Our product doesn’t work properly on your website? Report your issue or bug by describing it in detail and providing us with a link to your website. We will do our best to find a solution.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></p>
				</li>
				
				<li class="instashow-admin-page-support-includes-list-item">
					<div class="instashow-admin-page-support-includes-list-item-title"><?php _e('Life-Time Updates', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
					
					<p class="instashow-admin-page-support-includes-list-item-description"><?php _e('We provide you with all possible updates and new features, which were and will be also released in the future. Just don’t forget to check the latest version in your WordPress admin panel.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></p>
				</li>

				<li class="instashow-admin-page-support-includes-list-item">
					<div class="instashow-admin-page-support-includes-list-item-title"><?php _e('Considering Your Suggestions', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
					
					<p class="instashow-admin-page-support-includes-list-item-description"><?php _e('We are open to your ideas. If you want to see some specific features, which might improve our products, then just drop us a line. We will consider them and include the best in further updates.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></p>
				</li>
			</ul>
		</div>

		<div class="instashow-admin-page-support-not-includes">
			<h4><?php _e('Our Support Doesn’t Include', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></h4>
			
			<ul class="instashow-admin-page-support-not-includes-list">
				<li class="instashow-admin-page-support-not-includes-list-item">
					<div class="instashow-admin-page-support-not-includes-list-item-title"><?php _e('Product Installation', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
					
					<p class="instashow-admin-page-support-not-includes-list-item-description"><?php _e('We don’t provide installation services for our products. Otherwise, we can give you our recommendations concerning its installation. And if you face any issue during installation, feel free to contact us. ', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></p>
				</li>
				
				<li class="instashow-admin-page-support-not-includes-list-item">
					<div class="instashow-admin-page-support-not-includes-list-item-title"><?php _e('Customization of Our Products', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
					
					<p class="instashow-admin-page-support-not-includes-list-item-description"><?php _e('We don’t provide customization services of our products. If you want to see more features in our product, then send us a description of your ideas and we will consider them for future updates. ', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></p>
				</li>

				<li class="instashow-admin-page-support-not-includes-list-item">
					<div class="instashow-admin-page-support-not-includes-list-item-title"><?php _e('3rd-Party Issues', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
					
					<p class="instashow-admin-page-support-not-includes-list-item-description"><?php _e('We don’t fix bugs or issues caused by other plugins and themes, which relate to 3rd-party developers. Also we don’t provide services for integrating our products with 3rd-party plugins and themes.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></p>
				</li>
			</ul>
		</div>
	</div>
</article>