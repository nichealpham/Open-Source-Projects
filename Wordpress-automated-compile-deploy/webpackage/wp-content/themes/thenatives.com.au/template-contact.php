<?php
/*
 * Template Name: Contact
 */
?>

<?php get_header ();?>
<main id="contact-page" class="contact-page">
    <div class="container">
        <?php if(get_field('contact_content')):?>
            <?php while(has_sub_field('contact_content')): ?>
                <div class="row contentPage">
                    <div class="col-md-3 col-xs-4">
                        <h3><?php the_sub_field('contact_title'); ?></h3>
                    </div>
                    <div class="col-md-9 col-xs-8">
                        <div class="row">
                            <?php if(get_sub_field('content_contact')):?>
                                <?php while(has_sub_field('content_contact')): ?>
                                    <div class="col-sm-<?php the_sub_field('colspan') ?>">
                                        <div class="textContact">
                                            <?php $title_content = get_sub_field('title_content_contact') ;
                                                if($title_content){?>
                                                    <h5><?php echo $title_content; ?></h5>
                                            <?php } ?>
                                            <?php $content_contact = get_sub_field('content_contact') ;
                                                if($content_contact){?>
                                                    <h5><?php echo $content_contact; ?></h5>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif;?>
        <div class="row lastButtons">
            <div class="bgButton">
                <div class="col-xs-6 colPrev">
                    <div class="buttonPrev">
                        <img class="controlPrev" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow.png">
                        <a href="<?php bloginfo('url');?>/about/">ABOUT</a>
                    </div>
                </div>
                <div class="col-xs-6 colNext">
                    <div class="buttonNext">
                        <a href="<?php bloginfo('url');?>/the-fine-print/">THE FINE PRINT</a>
                        <img class="controlNext" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow.png">
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php get_footer('page');?>