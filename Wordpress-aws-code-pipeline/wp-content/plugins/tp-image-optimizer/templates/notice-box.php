<div class="notice_box is-dismissible">
	<div class="notice_box__content">
		<div class="notice_box__icon"></div>
		<p>
			<strong><?php echo esc_html__( 'Thanks for installing TP Image Optimizer. Do you have any coupon?', 'tp-image-optimizer' ) ?></strong> <br/>
			<?php echo esc_html__( 'One coupon, many benefits. Add your coupon code and enjoy special features. or', 'tp-image-optimizer' ) ?> <a href="#" class="notice_box__dissmiss"><?php echo esc_html__( 'Dissmiss this notice', 'tp-image-optimizer' ) ?></a>
		</p>
	</div>
	<div class="notice_box__form">
		<form class="coupon-form">
			<div class="coupon-form__warning"></div>
			<input type="text" value="" name="coupon_code" placeholder="<?php echo esc_attr__( 'Coupon code', 'tp-image-optimizer' ) ?>"/>
			<button type="button"><?php echo esc_html__( 'Apply coupon', 'tp-image-optimizer' ) ?></button>
			<input type="hidden" name="action" value="tpio_verify_coupon"/>
			<?php wp_nonce_field( 'tpio_verify_coupon', '_coupon_nonce' ) ?>
		</form>
	</div>
</div>