<div class='tp-radio-group io-setting-api io-setting-wrapper io-setting-block'>
    <h3><i class="ion-gear-b"></i> <?php echo esc_html__("Select compress quality ", 'tp-image-optimizer'); ?>
        <span class='faq-i faq-quality' data-val='quality'></span>
    </h3>
    <div class='compress_option_group'>
        <?php
        $count_array = count($option);
        $i           = 0;
        foreach ($option as $key => $item) {
            $i++;
            $class = '';
            if ($i == 1) {
                $class = 'option-first';
            } else if ($i == $count_array) {
                $class = 'option-last';
            }
            ?>
            <input type="radio" class="io-compress-level" name="tp_image_optimizer_compress_level" id="size_setting_<?php echo esc_html($key); ?>" value="<?php echo esc_html($key); ?>" <?php if ($compress == $key) : ?>checked<?php endif; ?>>
            <label for="size_setting_<?php echo esc_html($key); ?>" class="<?php echo $class; ?>"><?php echo esc_html($item); ?></label>
        <?php };
        ?>
    </div>
    <div class="notice-switch-done"></div>
    <?php echo wp_nonce_field("api_nonce_key", 'api-check-key') ?>
    <hr/>
</div>
