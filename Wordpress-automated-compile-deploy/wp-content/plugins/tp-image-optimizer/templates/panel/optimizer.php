<div class="io-optimizer-wrapper">
    <div class='io_alert_box'></div>
    <div class="tp-element xs-pl-20 xs-pr-20 optimize-action-bar md-mb-20 ">
        <div class='option <?php if ($cron): echo "disabled";endif; ?>'>
            <div class="force-input input" style="overflow:hidden;">
                <label class='label'>
                    <?php echo esc_html__('Force Re-Optimize', 'tp-image-optimizer'); ?>
                    <span class="faq-i faq-force fa-force" data-val="force"></span>
                </label>
                <div class="onoffswitch">
                    <input type="checkbox" name="force-re-optiomizer" class="onoffswitch-checkbox"
                           id="io-reoptimized" <?php if ($cron): echo "disabled";endif; ?>>
                    <label class="onoffswitch-label" for="io-reoptimized">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>
            <div class="keep_original input" style="overflow:hidden;">
                <label class='original_label label'>
                    <?php echo esc_html__('Compress original image', 'tp-image-optimizer'); ?>
                    <span class="faq-i faq-original" data-val='original'></span>
                </label>
                <div class="onoffswitch origin-check">
                    <input type="checkbox" name="keep-original" class="onoffswitch-checkbox" id="io-keep-original"
                           checked <?php if ($cron): echo "disabled";endif; ?> >
                    <label class="onoffswitch-label" for="io-keep-original">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                    <div class="notice-switch-done"></div>
                </div>
            </div>
            <div class="select-cronjob input" style="overflow:hidden;">
                <label class='run_cron_label label'>
                    <?php echo esc_html__('Run in background', 'tp-image-optimizer'); ?>
                    <span class="faq-i faq-run_in_background" data-val='run_in_background'></span>
                </label>
                <div class="onoffswitch run-in-background-check">
                    <input type="checkbox" name="run-in-background" class="onoffswitch-checkbox" id="run-in-background"
                        <?php if ($run_in_background =='true'): echo "checked";endif; ?> >
                    <label class="onoffswitch-label" for="run-in-background">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                    <div class="notice-switch-done"></div>
                </div>
            </div>
        </div>
        <div class='submit-optimizer'>
            <button type="submit" name="optimizer_btn" id="optimizer_btn" class="tp-button tp-btn-primary icon <?php
            if (!$cron): echo "is-active";
            endif;
            ?>">
                <?php echo esc_html__("One click optimize ", 'tp-image-optimizer'); ?>
            </button>
            <input type="button" name="cancel_btn" id="cancel_optimizer"
                   class="tp-button cancel_optimizer tp-btn-primary tp-btn-light <?php
                   if ($cron): echo "is-active";
                   endif;
                   ?>" value="<?php echo esc_html__("STOP ", 'tp-image-optimizer'); ?>">
        </div>
    </div>
</div>