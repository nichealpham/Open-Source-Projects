<?php global $thenatives; ?>
<?php
    $bg_form = $thenatives['thenatives_bg_form']?(' style="background-image: url('.$thenatives['thenatives_bg_form'].')"'):'';
    $form_title = trim($thenatives['thenatives_form_title'])?trim($thenatives['thenatives_form_title']):'let’s be friends fill in your deets so we can keep you in the loop';
    if(is_archive()){
        if(get_the_archive_title() == 'Archives: Careers'){
            if(trim($thenatives['thenatives_career_title_form'])){
                $form_title = trim($thenatives['thenatives_career_title_form']);
            }
            if($thenatives['thenatives_career_bg_form']){
                $bg_form = ' style="background-image: url('.$thenatives['thenatives_career_bg_form'].')"';
            }
        }
        elseif(get_the_archive_title() == 'Archives: Events'){
            if(trim($thenatives['thenatives_event_title_form'])){
                $form_title = trim($thenatives['thenatives_event_title_form']);
            }
            if($thenatives['thenatives_event_bg_form']){
                $bg_form = ' style="background-image: url('.$thenatives['thenatives_event_bg_form'].')"';
            }
        }
    }
    elseif(is_single()) {
        if(get_post_type() == 'career'){
            if(trim($thenatives['thenatives_career_title_form'])){
                $form_title = trim($thenatives['thenatives_career_title_form']);
            }
            if($thenatives['thenatives_career_bg_form']){
                $bg_form = ' style="background-image: url('.$thenatives['thenatives_career_bg_form'].')"';
            }
        }
        elseif(get_post_type() == 'event'){
            if(trim($thenatives['thenatives_event_title_form'])){
                $form_title = trim($thenatives['thenatives_event_title_form']);
            }
            if($thenatives['thenatives_event_bg_form']){
                $bg_form = ' style="background-image: url('.$thenatives['thenatives_event_bg_form'].')"';
            }
        }
    }
?>
        </div>
        <footer id="footer">
            <div class="footer-main">
                <div class="container">
                    <?php if(!is_404()): ?>
                        <div class="bgFooter"<?php echo $bg_form; ?>>
                            <div class="row wrapFormFooter">
                                <div class="col-sm-4 footerTitleAria">
                                    <div class="footerTitle">
                                        <h3><?php echo $form_title; ?></h3>
                                    </div>
                                </div>
                                <div class="col-sm-8 footerFormAria">
                                    <div class="footerForm">
                                        <?php echo do_shortcode('[gravityform id=1 title=false description=false ajax=true tabindex=100]')?>
                                    </div>
                                </div>
<!--                                <div class="maskFormFooter"></div>-->
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if(is_single() && get_field('advertise_bottom')): ?>
                <?php $banner_footer = get_field('advertise_bottom') ?>
                <section class="adNeuw">
                    <div class="verticalSpacing-lg"></div>
                    <div class="bgNeuw">
                        <div class="imgBanner">
                            <?php if(get_field('url',$banner_footer->ID)): ?>
                                <a href="<?php echo get_field('url',$banner_footer->ID); ?>">
                                    <img src="<?php echo get_field('image',$banner_footer->ID); ?>">
                                </a>
                            <?php else: ?>
                                <img src="<?php echo get_field('image',$banner_footer->ID); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        </footer>
    </div>
<?php wp_footer(); ?>
</body>
</html>