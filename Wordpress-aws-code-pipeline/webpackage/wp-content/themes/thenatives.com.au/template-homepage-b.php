

<?php get_header(); ?>
<section>

    <div class="contentVertical homePageB">
        <?php
        $category = get_category(get_query_var('cat'), false);
        ?>
        <?php
        global $wp_query;
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'orderby' => 'date',
            'order' => 'DESC',
            'cat' => $category->term_id,
        );
        $wp_query = new WP_Query($args);
        ?>
        <div class="container">
            <div class="row">
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()): the_post(); ?>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php get_template_part('content', 'home'); ?>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
                <?php wp_reset_query(); ?>
            </div>
        </div>
    </div>

    <div class="verticalSpacing-lg"></div>

    <div class="sliderE">
        <div class="container">
            <div class="row">
                <div class="tag tagE col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <a href="<?php echo get_post_type_archive_link('event'); ?>">
                        <img class=" image-sm" src="<?php echo THEME_IMAGES; ?>/events-tag.png" alt="tag E">
                    </a>
                </div>
                <div class="wrapperSlider col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <?php
                    global $wp_query;
                    $args = array(
                        'post_type' => 'event',
                        'post_status' => 'publish',
                        'posts_per_page' => -1,
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'cat' => $category->term_id,
                    );
                    $wp_query = new WP_Query($args);
                    if (have_posts()) : while (have_posts()) : the_post(); ?>
                        <div class="itemSlider">
                            <div class="hrefThumbnails">
                                <a class="imageThumbnails" href="<?php the_permalink(); ?>">
                                    <img class="img-responsive image-sm"
                                         src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
                                    <div class="boxContain contentThumbnails-Bottom">
                                        <div class="innerBoxContain beforeHover">
                                            <p>19-20 <br/> may</p>
                                        </div>
                                        <div class="innerBoxContain afterHover">
                                            <p>add to calendar</p>
                                            <span class="tagName sales">sales</span>
                                        </div>
                                    </div>
                                </a>
                                <?php $ct = get_the_terms(get_the_ID(), 'event-cities'); ?>
                                <?php if ($ct): ?>
                                    <h6 class="thumbnailLocal"><?php echo $ct[0]->name; ?></h6>
                                <?php endif; ?>
                                <p class="thumbnailName"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </p>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?php
                    endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="verticalSpacing-lg"></div>

    <div class="groupPost homepage">
        <div class="container">
            <div class="row">
                <div class="titleOla col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <p>Ola.</p>
                </div>
                <?php
                global $wp_query;
                $args = array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'posts_per_page' => 8,
                    'offset' => 2,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'cat' => $category->term_id,
                );
                $wp_query = new WP_Query($args);
                ?>
                <?php if (have_posts()) : ?>
                    <div class="imgPosts">
                        <?php while (have_posts()): the_post(); ?>
                            <div class="col-sm-3 col-xs-6 colImages">
                                <?php get_template_part('content', 'archive-medium'); ?>
                            </div>
                        <?php endwhile; ?>
                        <div class="clearfix"></div>
                    </div>
                <?php endif; ?>
                <?php wp_reset_query(); ?>
            </div>
        </div>
    </div>

    <div class="verticalSpacing-lg"></div>

    <?php
    global $wp_query;
    $args = array(
        'post_type' => 'career',
        'post_status' => 'publish',
        'posts_per_page' => 16,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    $wp_query = new WP_Query($args);
    ?>
    <?php if (have_posts()) : ?>
        <div class="sliderC">
            <div class="container">
                <div class="row">
                    <div class="tag tagc col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <a href="<?php echo get_post_type_archive_link('career'); ?>">
                            <img class="image-sm" src="<?php echo THEME_IMAGES; ?>/careers-tag.png" alt="tag C">
                        </a>
                    </div>
                    <div class="wrapperSlider col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <?php while (have_posts()): the_post(); ?>
                            <div class="itemSlider">
                                <div class="hrefThumbnails">
                                    <a href="<?php the_permalink(); ?>" class="imageThumbnails">
                                        <img class="img-responsive image-sm"
                                             src="<?php echo get_the_post_thumbnail_url(); ?>"
                                             alt="image">
                                    </a>
                                    <?php $city = get_the_terms(get_the_ID(), 'career-cities'); ?>
                                    <?php if ($city): ?>
                                        <h6 class="thumbnailLocal"><?php echo $city[0]->name; ?></h6>
                                    <?php endif; ?>
                                    <?php $company = get_the_terms(get_the_ID(), 'career-companies'); ?>
                                    <?php if ($company): ?>
                                        <p class="nameModel"><?php echo $company[0]->name; ?></p>
                                    <?php endif; ?>
                                    <p class="thumbnailName"><a
                                                href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php wp_reset_query(); ?>
</section>
<?php get_footer('homepage-b'); ?>
