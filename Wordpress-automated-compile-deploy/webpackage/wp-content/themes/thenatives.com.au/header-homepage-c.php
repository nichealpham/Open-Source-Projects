<!DOCTYPE html>
<html <?php language_attributes(); ?> />
<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="profile" href="http://gmgp.org/xfn/11" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <?php wp_head(); ?>
</head>
<body <?php body_class('page-template-template-homepage-c'); ?> >
<div id="wrapper">
    <?php global $thenatives; ?>
    <?php
    $bg = '';
    if( !get_field('banner_post') ){
        while(the_repeater_field('custom_banner_post')){
            $bg = get_sub_field('bg_');
        }
    }
    else{
        if(get_field('select_banner_post')){
            $bg = get_the_post_thumbnail_url(get_field('select_banner_post'));
        }
        else {
            $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => 1,
                'orderby' => 'date',
                'order' => 'DESC',
            );
            $the_query = new WP_Query($args);
            if($the_query->have_posts()){
                while($the_query->have_posts()){
                    $the_query->the_post();
                    $bg = get_the_post_thumbnail_url();
                }
            }
            wp_reset_postdata();
        }
    }
    ?>
    <header id="header" class="header-v1<?php if($thenatives['thenatives_sticky_header']) echo ' sticky'?>"<?php if($bg) echo 'style="background-image: url('.$bg.')"'; ?>>
        <div class="header-main">
            <div class="container">
                <div class="row">

                    <?php if(is_single()): ?>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 wrapperLogo">
                        <?php else: ?>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 wrapperLogo">
                            <?php endif; ?>
                            <?php do_action('thenatives_logo');?>
                        </div>


                        <?php if(is_single()): ?>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 socialHeader flexDisplay">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 padding-space">
                                    <?php get_template_part('framework/include/sharebox'); ?>
                                </div>

                                <div class="col-lg-10 col-lg-10 col-lg-10 col-xs-10 showPost">
                                    <?php $prevpost = get_previous_post(true); ?>
                                    <?php if($prevpost): ?>
                                        <div class="prevPost col-lg-6 col-lg-6 col-lg-6 col-xs-6">
                                            <a href="<?php echo get_the_permalink($nextpost->ID); ?>">
                                                <?php if(has_post_thumbnail($prevpost->ID)): ?>
                                                    <img class="img-responsive" src="<?php echo get_the_post_thumbnail_url($prevpost->ID); ?>">
                                                <?php endif; ?>
                                                <div class="wrapperContentPostHeader">
                                                    <p class="titleTextPost"><?php echo get_the_title($prevpost->ID); ?></p>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="currentPost col-lg-12 col-lg-12 col-lg-12 col-xs-12">
                                        <?php if(has_post_thumbnail()): ?>
                                            <img class="img-responsive" src="<?php echo get_the_post_thumbnail_url(); ?>">
                                        <?php endif; ?>
                                        <div class="wrapperContentPostHeader">
                                            <p class="status">reading</p>
                                            <p class="titleTextPost"><?php the_title(); ?></p>
                                        </div>
                                    </div>
                                    <?php $nextpost = get_next_post(true); ?>
                                    <?php if($nextpost): ?>
                                        <div class="nextPost col-lg-6 col-lg-6 col-lg-6 col-xs-6">
                                            <a href="<?php echo get_the_permalink($nextpost->ID); ?>">
                                                <?php if(has_post_thumbnail($nextpost->ID)): ?>
                                                    <img class="img-responsive" src="<?php echo get_the_post_thumbnail_url($nextpost->ID); ?>">
                                                <?php endif; ?>
                                                <div class="wrapperContentPostHeader">
                                                    <p class="titleTextPost"><?php echo get_the_title($nextpost->ID); ?></p>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if(is_single()): ?>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 flexDisplay">
                            <?php else: ?>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 flexDisplay">
                                <?php endif; ?>
                                <div class="buttonMenu pull-right">
                                    <img class="tagButtonMenu tagE" src="<?php echo THEME_IMAGES; ?>/TagE.png" alt="Tag E">
                                    <img class="tagButtonMenu tagF" src="<?php echo THEME_IMAGES; ?>/TagF.png" alt="Tag F">
                                    <img class="tagButtonMenu tagS" src="<?php echo THEME_IMAGES; ?>/TagS.png" alt="Tag S">
                                    <button>MENU</button>
                                </div>
                            </div>
                            <nav id="menu" class="nav menu">
                                <a class="closeButton">
                                    <img class="blackClose" src="<?php echo THEME_IMAGES; ?>/close-black.svg" alt="image">
                                </a>
                                <button id="searchButton">
                                    search
                                </button>
                                <?php if ( has_nav_menu( 'category' ) ) : ?>
                                    <?php wp_nav_menu( array( 'theme_location' => 'category' ) ); ?>
                                <?php endif; ?>

                                <?php if ( has_nav_menu( 'social' ) ) : ?>
                                    <?php wp_nav_menu( array( 'theme_location' => 'social' ) ); ?>
                                <?php endif; ?>

                                <?php if ( has_nav_menu( 'page' ) ) : ?>
                                    <?php wp_nav_menu( array( 'theme_location' => 'page' ) ); ?>
                                <?php endif; ?>
                            </nav>
                        </div>
                    </div>
                </div>

                <div id="searchPopup" class="">
                    <div class="wrapSearchPopup">
                        <a class="closeButton">
                            <img class="blackClose" src="http://fj.nativesdev.com.au/wp-content/themes/thenatives.com.au/images/close-black.svg" alt="image">
                        </a>
                        <div class="wrapContent">
                            <form class="formSearch" method="GET" action="<?php echo get_bloginfo('url'); ?>">
                                <input type="text" placeholder="search" name="s">
                                <button type="submit">go</button>
                            </form>
                            <h4 class="searchTitle">search suggestions</h4>
                            <p class="searchDescription">
                                <a href="/?s=competitions">Competitions</a>,
                                <a href="/?s=features">Features</a>,
                                <a href="/?s=shoots">Shoots</a>,
                                <a href="/?s=collections">Collections</a>,
                                <a href="/?s=reviews">Reviews</a>,
                                <a href="/?s=books">Books</a>,
                                <a href="/?s=health">Health</a>,
                                <a href="/?s=travel">Travel</a>,
                                <a href="/?s=div+%26+recipes">DIY & Recipes</a>,
                                <a href="/?s=videos">Videos</a>
                            </p>
                        </div>
                    </div>
                </div>
    </header>
    <div id="body" class="site-main">