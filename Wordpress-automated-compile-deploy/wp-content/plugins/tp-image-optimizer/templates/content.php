<div id='tp-image-optimizer' class="tpui tp-image-optimizer io-detail-page wrap" data-process='false'
     data-total='<?php echo esc_html($total_image); ?>' data-pre-optimize="<?php echo esc_html($total_pre_image); ?>">

        <?php
        /**
         * Include header right
         */
        tp_image_optimizer_template('header', array('title' => $title));
        $metabox = new TP_Image_Optimizer_Metabox();
        ?>
        <div class="tp-io-notice-bar">
        </div>
        <div class="tp-panel" id="tp-wrapper-panel" data-range="<?php echo get_option("tpio_range");?>" data-title="<?php echo get_option("tpio_range_title");?>">
            <div class=" tp-tabs">
                <div class="tp-tabs-list">
                    <ul class="tp-tabs-nav tabs" role="tablist">
                        <li data-tab="tp-panel" class='active enable-chart'>
                            <span><?php echo esc_html__("Dashboard", 'tp-image-optimizer'); ?></span></li>
                        <li data-tab="appearance" class='disable-chart'>
                            <span><?php echo esc_html__("Settings", 'tp-image-optimizer'); ?></span></li>
                    </ul>
                </div>
                <div class="tp-tab-content">
                    <!--CONTENT TAB DASHBOARD-->
                    <div class="tp-tab-panel  active" id="tp-panel">
                        <div class='io-top-panel'>
                            <div class='panel-settings'>
                                <?php $metabox->heading(); ?>
                            </div>
                        </div>
                        <div class='panel_statistics'>
                            <?php $metabox->content(); ?>
                        </div>
                        <br class="clear">
                    </div>
                    <div class='tp-io-tab'></div>
                    <!--/CONTENT TAB DASHBOARD-->

                    <!-- CONTENT TAB APPEARANCE -->
                    <div class="tp-tab-panel setting-page" id="appearance">
                        <?php $metabox->setting(); ?>
                    </div>
                    <!--/CONTENT TAB APPEARANCE -->
                </div>

            </div>
        </div>
        <?php $metabox->sticky_box_show(); ?>
    </div>
