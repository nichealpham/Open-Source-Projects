<div id='tp-image-optimizer' class="tp-image-optimizer io-detail-page wrap" data-total='<?php echo $total_image;?>'>

    <div class="full-install-page tp-panel-content">
        <div class="width-80 install-center">
            <div class="header">
                <?php
                /**
                 * Include header right
                 */
                tp_image_optimizer_template('header', array('title' => esc_html__('TP Image Optimizer', 'tp-image-optimizer'))); ?>
            </div>
            <div class="tpui">
                <div class="tp-installer tp-installer--progressbar">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" style="width:0%">
                                <span class="progress-percent">0%</span>
                            </div>
                        </div>
                    <h3><?php echo esc_html("During installation, the plugin will", "tp-image-optimizer"); ?></h3>
                    <ul>
                        <li>
                            <i class="ion-checkmark-round"></i> <?php echo esc_html("1. Get a free token key.", "tp-image-optimizer"); ?>
                        </li>
                        <li>
                            <i class="ion-checkmark-round"></i><?php echo esc_html("2. Basic image optimizing options are auto-selected, you can change them after the installation is completed.", "tp-image-optimizer"); ?>
                        </li>
                        <li>
                            <i class="ion-checkmark-round"></i><?php echo esc_html("3. Add all image data in the Media to the pending list for optimizing.", "tp-image-optimizer"); ?>
                        </li>
                    </ul>
                    <button type="submit" name="accept-install" id="accept-install"
                            class="tp-btn-primary xs-mr-25"><?php echo esc_html("Get Started", "tp-image-optimizer"); ?></button>
                    <div class="image-person"><img src="<?php echo TP_IMAGE_OPTIMIZER_URL;?>/assets/images/person-01.png">
                    </div>
                </div>
            </div>
        </div>
    </div>