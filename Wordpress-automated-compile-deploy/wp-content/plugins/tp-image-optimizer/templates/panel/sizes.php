<div class='tpio-size-settings io-setting-block'>
    <div class='wmax-500'>
    <h3><i class="ion-images"></i> <?php echo esc_html__("Size setting", 'tp-image-optimizer'); ?>
        <span class="faq-i faq-size" data-val="size"></span><br/>
    </h3>
    <p>
        <?php
        echo esc_html__('The following image sizes will be optimized  by TP Image Optimizer', 'tp-image-optimizer');
        ?>
    </p>
    <div class="element">
        <span class='option-title'>
            <b><?php echo esc_html__('Original', 'tp-image-optimizer'); ?> <span class="faq-i faq-original" data-val='original'></span></b>
        </span>
        <div class="onoffswitch">
            <input type="checkbox" class="onoffswitch-checkbox io-size-change" value="full" id='io-size-full' <?php
	            if (in_array('full', $optimize_sizes)) : echo "checked";
	            endif
            ?> data-size='full'>
            <label class="onoffswitch-label choose-full update-size-process" for="io-size-full">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
            </label>
        </div>

        <div class="notice-switch-done"></div>
    </div>

    <?php
    if (!empty($sizes)):
        foreach ($sizes as $size):
            ?>
            <div class="element">
                <span class='option-title'>
                    <?php echo $size ?>
                </span> 

                <div class="onoffswitch">
                    <input type="checkbox" value='io-size-<?php echo esc_attr($size) ?>'<?php
	                    if (in_array($size, $optimize_sizes)): echo esc_html("checked");
	                    endif;
                    ?> id='io-size-<?php echo esc_attr($size) ?>' class='onoffswitch-checkbox io-size-change' data-size='<?php echo esc_attr($size) ?>'>
                    <label class="onoffswitch-label update-size-process" for="io-size-<?php echo esc_attr($size) ?>">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
                <div class="notice-switch-done"></div>
            </div>
            <?php
        endforeach;
    endif;
    ?>
    </div>
    <hr/>
</div>
    