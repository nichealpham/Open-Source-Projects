<div class='sync update-image'>
    <label><?php echo esc_html__('Sync media data', 'tp-image-optimizer'); ?>
        <div class='count-media'><span class='percent-update'>0</span>%</div>
    </label>
    <p><?php echo esc_html__('When you click this button - All image of your media library will be updated to plugin data - Old statistics data for each image of TP Image Optimizer will be cleared.', 'tp-image-optimizer');
		?></p>
    <div class="update-image-btn">
        <input type="submit" name="re-check" id="update-image" class="refresh-library button button-secondary"
               value="<?php echo esc_html("Update Data", "tp-image-optimizer"); ?>">
        <div class="load-speeding-wheel"></div>
    </div>
</div>
