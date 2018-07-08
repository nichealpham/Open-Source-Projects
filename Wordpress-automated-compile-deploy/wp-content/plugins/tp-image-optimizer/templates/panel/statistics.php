<div class=' io-statistics-wrapper'>
    <div class="preload-statistics" style="display: block;">
        <div class="chartPreload" >
            <div class="loader">
                <div class="cssload-loader">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="service-statistics">
        <div class="io-service-statistics">
            <div class='tp-element statistics-chart'>
                <div class="tp-progress-circle " data-progress="40">
                    <div class="ko-circle">
                        <div class="full tp-progress-circle__slice">
                            <div class="tp-progress-circle__fill"></div>
                        </div>
                        <div class="tp-progress-circle__slice">
                            <div class="tp-progress-circle__fill"></div>
                            <div class="tp-progress-circle__fill tp-progress-circle__bar"></div>
                        </div>
                    </div>
                    <div class="tp-progress-circle__overlay"><span class='progress-val'></span>% </div>
                </div>
                <div class="tp-progress-circle-des"><?php echo esc_html__('Total optimized saving', 'tp-image-optimizer'); ?>
                    <span class='faq-i faq-statistics_service' data-val='statistics_service'></span>
                </div>
            </div>
            
            <div class="detail">
                <ul >
                    <li><p class='tp-element'><span class='total-image'></span> <?php echo esc_html__('Total optimized image', 'tp-image-optimizer'); ?> </p></li>
                    <li><p class='tp-element'><span class='uploaded-size'></span><?php echo esc_html__('Total uploaded size ', 'tp-image-optimizer'); ?> </p></li>
                    <li><p class='tp-element'><span class='compressed-size'></span><?php echo esc_html__('Total size after being optimized', 'tp-image-optimizer'); ?> </p></li>
                    <li><p class='tp-element'><b><?php echo esc_html__('Total saving size  ', 'tp-image-optimizer'); ?></b> <span class='saving-size'></span></p></li>
                </ul>
            </div>

        </div>
    </div>
    <div class="connect-err"></div>
</div>