<!--Register form-->
<div class='io-register io-register-wrapper'>
    <form action='' method='POST'>
        <label class='step1'>
            <span class='number'>1</span>
            <?php echo esc_html__('Enter your email ', 'tp-image-optimizer'); ?>
        </label>
        <label class='step2'>
            <span class='number'>2</span>
            <?php echo esc_html__('Verify token', 'tp-image-optimizer'); ?>
        </label>
        <div class='step1'>
            <input type='text' name='email' id='email-register'>
        </div>
        <!--        <div class='step2'>
                    <input type='text' name='token'>
                </div>-->
        <span class='load-speeding-wheel'></span>
        <?php submit_button("Register", "button-primary register-btn", "register-api", "update", false, array("type='submit'")); ?>
    </form>
</div>