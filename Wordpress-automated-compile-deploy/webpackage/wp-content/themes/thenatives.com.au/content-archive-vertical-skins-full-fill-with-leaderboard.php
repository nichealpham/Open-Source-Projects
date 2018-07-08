<div class="vertical-leader vertical-full">
    <section class="contentVertical">
        <?php
        $catid = isset($_POST['cat'])?$_POST['cat']:get_query_var('cat');
        $category = get_category($catid,false);
        if($category->category_parent) {
            $parent_cat = get_category($category->category_parent);
            while ($parent_cat->category_parent) {
                $parent_cat = get_category($parent_cat->category_parent);
            }
        }
        else {
            $parent_cat = $category;
        }
        $count = 0;
        $fpList = array();
        ?>
        <?php
        global $wp_query;
        $args = array(
            'post_type'				=> 'post',
            'post_status'			=> 'publish',
            'posts_per_page' 		=> 3,
            'orderby' 				=> 'date',
            'order'                 => 'DESC',
            'cat'                   => $category->term_id,
        );
        $wp_query = new WP_Query( $args );
        ?>
        <div class="container wrapperVer">
            <div class="row">
                <?php if (have_posts()) : ?>
                    <?php while(have_posts()): the_post(); $count++; ?>
                        <div class="colVer">
                            <?php get_template_part('content', 'archive-large'); ?>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
                <?php wp_reset_query(); ?>
            </div>
            <div class="verticalSpacing-lg"></div>
        </div>
    </section>

    <?php
    global $wp_query;
    $args = array(
        'post_type'				=> 'post',
        'post_status'			=> 'publish',
        'posts_per_page' 		=> 6,
        'offset'                => 3,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
        'cat'                   => $category->term_id,
    );
    $wp_query = new WP_Query( $args );
    ?>
    <?php if (have_posts()) : ?>
        <section class="groupPost">
            <div class="container wrapperVer">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="titleOla">
                            <p>Ola.</p>
                        </div>
                    </div>
                </div>
                    <div class="row">
                        <?php while(have_posts()): the_post(); $count++; ?>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 colImages">
                                <?php get_template_part('content', 'archive-medium'); ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php wp_reset_query(); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php
    global $wp_query;
    $args = array(
        'post_type'				=> 'post',
        'post_status'			=> 'publish',
        'posts_per_page' 		=> 6,
        'offset'                => 8,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
        'cat'                   => $category->term_id,
    );
    $wp_query = new WP_Query( $args );
    ?>
    <?php if (have_posts()) : ?>
        <?php if($banner_middle = get_field('advertise_middle','category_'.$parent_cat->term_id)): ?>
            <section class="adNeuw">
                <div class="verticalSpacing-lg"></div>
                <div class="bgNeuw">
                    <div class="imgBanner">
                        <?php if(get_field('url',$banner_middle->ID)): ?>
                            <a href="<?php echo get_field('url',$banner_middle->ID); ?>">
                                <img src="<?php echo get_field('image',$banner_middle->ID); ?>">
                            </a>
                        <?php else: ?>
                            <img src="<?php echo get_field('image',$banner_middle->ID); ?>">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="verticalSpacing-lg"></div>
            </section>
        <?php endif; ?>

        <section class="groupPost">
            <div class="container wrapperVer">
                <div class="titleOla">
                    <p>Ola.</p>
                </div>
                    <div class="row">
                        <?php while(have_posts()): the_post(); $count++; $fpList[] = get_the_ID(); ?>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 colImages">
                                <?php get_template_part('content', 'archive-medium'); ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php wp_reset_query(); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if($banner_bottom = get_field('advertise_bottom','category_'.$parent_cat->term_id)): ?>
        <section class="adNeuw">
            <div class="verticalSpacing-lg"></div>
            <div class="bgNeuw">
                <div class="imgBanner-small">
                    <?php if(get_field('url',$banner_bottom->ID)): ?>
                        <a href="<?php echo get_field('url',$banner_bottom->ID); ?>">
                            <img src="<?php echo get_field('image',$banner_bottom->ID); ?>">
                        </a>
                    <?php else: ?>
                        <img src="<?php echo get_field('image',$banner_bottom->ID); ?>">
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if($banner_left = get_field('advertise_left','category_'.$parent_cat->term_id)): ?>
        <div class="bannerLeft">
            <?php if(get_field('url',$banner_left->ID)): ?>
                <a href="<?php echo get_field('url',$banner_left->ID); ?>">
                    <img src="<?php echo get_field('image',$banner_left->ID); ?>">
                </a>
            <?php else: ?>
                <img src="<?php echo get_field('image',$banner_left->ID); ?>">
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if($banner_right = get_field('advertise_right','category_'.$parent_cat->term_id)): ?>
        <div class="bannerRight">
            <?php if(get_field('url',$banner_right->ID)): ?>
                <a href="<?php echo get_field('url',$banner_right->ID); ?>">
                    <img src="<?php echo get_field('image',$banner_right->ID); ?>">
                </a>
            <?php else: ?>
                <img src="<?php echo get_field('image',$banner_right->ID); ?>">
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php
    $args['posts_per_page'] = -1;
    $subtotal = thenative_posts_count($args) - $count - count($fpList);
    ?>
    <?php if($subtotal > 0): ?>
        <div class="lazyLoad" data-time="1" data-offset="8" data-tax="<?php echo $category->slug; ?>" data-post-in="<?php echo implode(',',$fpList); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
        </div>
    <?php endif; ?>
</div>