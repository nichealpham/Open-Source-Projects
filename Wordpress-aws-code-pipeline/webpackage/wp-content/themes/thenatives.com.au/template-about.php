<?php
/*
 * Template Name: About
 */
?>

<?php get_header ();?>
<main id="about-page" class="about-fj">
    <div class="container">
        <?php if(get_field('about_field')):?>
            <div class="aboutContent">
            <?php while(has_sub_field('about_field')): ?>
                <div class="row">
                    <div class="col-sm-6 colImage">
                        <img src="<?php the_sub_field('image_about'); ?>">
                    </div>
                    <div class="col-sm-6 colContent">
                        <h4><?php the_sub_field('title_about')?></h4>
                        <?php the_sub_field('content_about')?>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
        <?php endif;?>
        <div class="row contentSite">
        <?php if(get_field('site_credits')== 'show_'):?>
            <div class="col-sm-12">
                <div class="verticalSpacing-lg"></div>
                <p>site credits</p>
                <a href="http://tcyk.com.au/" target="_blank">design by the company you keep</a>
            <br>
                <a href="https://thenatives.com.au/" target="_blank">build by the natives</a>

            </div>
        <?php endif; ?>
        </div>
        <div class="row lastButtons">
            <div class="bgButton">
                <div class="col-xs-6 colPrev">
                    <div class="buttonPrev">
                        <img class="controlPrev" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow.png">
                        <a href="<?php bloginfo('url');?>">ADVERTISE</a>
                    </div>
                </div>
                <div class="col-xs-6 colNext">
                    <div class="buttonNext">
                        <a href="<?php bloginfo('url');?>/contact">CONTACT</a>
                        <img class="controlNext" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow.png">
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php get_footer('page');?>