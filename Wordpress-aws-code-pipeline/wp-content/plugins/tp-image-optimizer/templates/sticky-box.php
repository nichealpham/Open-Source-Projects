<div class='io-sticky-notice io-sticky-wrapper'>
    <div class='sticky-header'>
        <?php echo esc_html__("Optimizing ...", "tp-image-optimizer"); ?>
        <a class='sticky-header-close' href="#">-</a>
    </div>
    <div class='sticky-content'>
        <div class="loading-sticky-box">
            <div class="optimizing active">
                <div class='load-speeding-wheel active'></div>
                <div class="processing"><?php echo esc_html__("Processsing image #",'tp-image-optimizer');
                ?><span></span></div>
            </div> 
            <span class="log"></span>
        </div>
        <ul>
            <!--- LOG WILL APPEND HERE -->
        </ul>
    </div>
</div>
