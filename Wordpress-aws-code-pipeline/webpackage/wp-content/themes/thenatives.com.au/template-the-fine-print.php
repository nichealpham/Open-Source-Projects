<?php 
/*
 * Template Name: The Fine Print
 */
?>

<?php get_header ();?>
<main id="fine-print" class="fine-print">
    <div class="container">
<?php if(get_field('the_fine_print')):?>
    <?php while(has_sub_field('the_fine_print')): ?>
        <div class="row">
            <div class="col-sm-3 colLeft">
                <div class="titleLeft">
                    <h3><?php the_sub_field('title_the_fine'); ?></h3>
                </div>
            </div>
            <div class="col-sm-9 colRight">
                <?php if(get_sub_field('content_the_fine')): ?>
                    <?php while(has_sub_field('content_the_fine')): ?>
                        <div class="contentTerm">
                            <?php $title_content = get_sub_field('title_content_fine') ;
                                if($title_content){?>
                                    <h4><?php echo $title_content; ?></h4>
                              <?php } ?>
                            <?php $content_fine = get_sub_field('content_fine') ;
                                if($content_fine){?>
                                    <?php echo $content_fine; ?>
                            <?php } ?>
                        </div>
                    <?php endwhile; ?>
                <?php endif;?>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif;?>
        <div class="row lastButtons">
            <div class="bgButton">
                <div class="col-xs-6 colPrev">
                    <div class="buttonPrev">
                        <img class="controlPrev" src="<?php echo THEME_IMAGES ?>/dropdown-arrow.png">
                        <a href="<?php bloginfo('url');?>/contact">contact</a>
                    </div>
                </div>
                <div class="col-xs-6 colNext">
                    <div class="buttonNext">
                        <a href="<?php bloginfo('url');?>">privacy policy</a>
                        <img class="controlNext" src="<?php echo THEME_IMAGES ?>/dropdown-arrow.png">
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php get_footer('page');?>