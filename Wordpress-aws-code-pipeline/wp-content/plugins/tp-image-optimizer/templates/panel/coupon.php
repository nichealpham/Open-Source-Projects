<div class="io-setting-block"><h3><i class="ion-ribbon-b"></i> <?php echo esc_html__("Coupon", 'tp-image-optimizer'); ?></h3>
<form class="coupon-form tp-group-submit">
    <p>
        <?php echo esc_html__('One coupon, many benefits. Add your coupon code and enjoy special features.', 'tp-image-optimizer') ?> 
    </p>

    <input type="text" class="widefat tp-input xs-mr-15" value="" name="coupon_code" placeholder="<?php echo esc_attr__('Coupon code', 'tp-image-optimizer') ?>"/>
    <button class="apply-coupon" type="button"><?php echo esc_html__('Apply', 'tp-image-optimizer') ?></button>
    <input type="hidden" name="action" value="tpio_verify_coupon"/>
    <?php wp_nonce_field('tpio_verify_coupon', '_coupon_nonce') ?>
    <div class="result_alert"></div>

</form></div>