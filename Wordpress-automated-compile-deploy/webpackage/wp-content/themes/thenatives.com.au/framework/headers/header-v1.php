<?php global $thenatives; ?>
<header id="header" class="header-v1<?php if ($thenatives['thenatives_sticky_header']) echo ' sticky' ?>">
    <?php do_action('thenatives_banner_top'); ?>
    <div class="header-main">
        <div class="myProgressBar"></div>
        <div class="container">
            <div class="row">
                <?php
                if (is_single()){
                    $class = 'col-lg-8 col-md-10 col-sm-10 col-xs-8 wrapperLogo';
                }
                else {
                    $class = 'col-lg-6 col-md-6 col-sm-6 col-xs-6 wrapperLogo';
                }
                ?>
                <div class="<?php echo $class; ?>">
                    <?php do_action('thenatives_logo'); ?>
                </div>


                <?php if (is_single()): ?>
                    <div class="col-lg-8 col-md-10 col-sm-10 col-xs-8 socialHeader flexDisplay">
                        <div class="col-lg-2 col-md-2 col-sm-3 hidden-xs padding-space">
                            <div class="wrapper-social-share">
                                <?php get_template_part('framework/include/sharebox'); ?>
                            </div>
                            <div class="wrapperMoreTag">
                                <?php if(get_post_type()!='career' && get_post_type()!='event' && get_post_type()!='sale' && get_post_type()!='sponsored'): ?>
                                    <?php
                                    $category = get_the_category();
                                    $parent_cat = $category[0];
                                    while ($parent_cat->category_parent) {
                                        $parent_cat = get_category($parent_cat->category_parent);
                                    }
                                    ?>
                                    <span class="more">more</span>
                                    <a href="<?php echo get_category_link($parent_cat); ?>" class="tagName fashion"><?php echo $parent_cat->name; ?></a>
                                <?php else : ?>
                                    <span class="more">more</span>
                                    <a href="<?php echo get_post_type_archive_link( get_post_type() ); ?>; ?>" class="tagName fashion"><?php echo get_post_type().'s'; ?></a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-lg-10 col-md-10 col-sm-9 col-xs-12 showPost">
                            <?php $prevpost = (get_post_type()!='career' && get_post_type()!='event' && get_post_type()!='sale' && get_post_type()!='sponsored')?get_next_post(true):get_next_post(); ?>
                            <?php if ($prevpost): ?>
                                <div class="prevPost col-lg-6 col-md-6 col-sm-6 hidden-xs">
                                    <a href="<?php echo get_the_permalink($prevpost->ID); ?>">
                                        <?php if (has_post_thumbnail($prevpost->ID)): ?>
                                            <img class="img-responsive"
                                                 src="<?php echo get_the_post_thumbnail_url($prevpost->ID); ?>">
                                        <?php endif; ?>
                                        <div class="wrapperContentPostHeader">
                                            <p class="titleTextPost"><?php echo get_the_title($prevpost->ID); ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="currentPost col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <?php if (has_post_thumbnail()): ?>
                                    <img class="img-responsive hidden-xs" src="<?php echo get_the_post_thumbnail_url(); ?>">
                                <?php endif; ?>
                                <div class="wrapperContentPostHeader">
                                    <p class="status">reading</p>
                                    <p class="titleTextPost"><?php the_title(); ?></p>
                                </div>
                            </div>
                            <?php $nextpost = (get_post_type()!='career' && get_post_type()!='event' && get_post_type()!='sale' && get_post_type()!='sponsored')?get_previous_post(true):get_previous_post(); ?>
                            <?php if ($nextpost): ?>
                                <div class="nextPost col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <a href="<?php echo get_the_permalink($nextpost->ID); ?>">
                                        <?php if (has_post_thumbnail($nextpost->ID)): ?>
                                            <img class="img-responsive"
                                                 src="<?php echo get_the_post_thumbnail_url($nextpost->ID); ?>">
                                        <?php endif; ?>
                                        <div class="wrapperContentPostHeader">
                                            <p class="upNext">UP NEXT</p>
                                            <p class="titleTextPost"><?php echo get_the_title($nextpost->ID); ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                if (is_single()){
                    $class = 'col-lg-4 col-md-2 col-sm-2 col-xs-4 flexDisplay';
                }
                else {
                    $class = 'col-lg-6 col-md-6 col-sm-6 col-xs-6 flexDisplay';
                }
                ?>
                <div class="<?php echo $class; ?>">
                    <?php do_action('thenatives_page_title'); ?>
                    <div class="buttonMenu pull-right">
                        <img class="tagButtonMenu tagE" src="<?php echo THEME_IMAGES; ?>/TagE.png" alt="Tag E">
                        <img class="tagButtonMenu tagF" src="<?php echo THEME_IMAGES; ?>/TagF.png" alt="Tag F">
                        <img class="tagButtonMenu tagS" src="<?php echo THEME_IMAGES; ?>/TagS.png" alt="Tag S">
                        <button>MENU</button>
                    </div>
                    <nav id="menu" class="nav menu">
                        <a class="closeButton">
                            <img class="blackClose" src="<?php echo THEME_IMAGES; ?>/close-black.svg" alt="image">
                        </a>
                        <button id="searchButton">
                            search
                        </button>
                        <?php if (has_nav_menu('category')) : ?>
                            <?php wp_nav_menu(array('theme_location' => 'category')); ?>
                        <?php endif; ?>

                        <?php if (has_nav_menu('social')) : ?>
                            <?php wp_nav_menu(array('theme_location' => 'social')); ?>
                        <?php endif; ?>

                        <?php if (has_nav_menu('page')) : ?>
                            <?php wp_nav_menu(array('theme_location' => 'page')); ?>
                        <?php endif; ?>
                    </nav>
                </div>
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
                    <a href="/?s=competitions&cats=win">Competitions</a>,
                    <a href="/?s=features&cats=features-beauty,features-fashion,features-life,features-music">Features</a>,
                    <a href="/?s=shoots&cats=shoots">Shoots</a>,
                    <a href="/?s=collections&cats=collections">Collections</a>,
                    <a href="/?s=reviews&cats=reviews">Reviews</a>,
                    <a href="/?s=books&cats=books">Books</a>,
                    <a href="/?s=health&cats=health">Health</a>,
                    <a href="/?s=travel&cats=travel">Travel</a>,
                    <a href="/?s=diy+recipes+life&cats=diy-recipes-life">DIY & Recipes</a>,
                    <a href="/?s=videos&tags=video">Videos</a>
                </p>
            </div>
        </div>
    </div>
</header>