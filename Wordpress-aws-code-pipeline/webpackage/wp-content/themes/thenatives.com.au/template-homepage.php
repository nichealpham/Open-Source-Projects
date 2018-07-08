<?php
/*
 * Template Name: Home Page
 */
?>
<?php $header = (get_field('style') == 'homepage-c') ? 'homepage-c' : '' ?>
<?php
$fpList = array();
switch (get_field('style')) {
    case 'homepage-a' : {
        if(have_rows('featured_section')) {
            while (have_rows('featured_section')) {
                the_row();
                if (get_sub_field('post')) {
                    $post_ID = get_sub_field('post');
                    $fpList[] = $post_ID;
                } else {
                    $count = 2 - count($fpList);
                }
            }
        }
        else {
            $count = 3;
        }
        if($count) {
            $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => $count,
                'orderby' => 'date',
                'order' => 'DESC'
            );
            $the_query = new WP_Query($args);
            if ($the_query->have_posts()) {
                for ($i = 0; $i < count($the_query->posts); $i++) {
                    $fpList[] = $the_query->posts[$i]->ID;
                }
                wp_reset_postdata();
            }
        }
        break;
    }
    case 'homepage-b' : {
        if (get_field('featured_section_b')) {
            if (get_field('select_feature')) {
                $spost = get_field('select_feature');
                $fpList[] = $spost->ID;
            } else {
                $args = array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'posts_per_page' => 1,
                    'orderby' => 'date',
                    'order' => 'DESC',
                );
                $the_query = new WP_Query($args);
                if ($the_query->have_posts()) {
                    while ($the_query->have_posts()) {
                        $the_query->the_post();
                        $fpList[] = get_the_ID();
                    }
                    wp_reset_postdata();
                }
            }
        }
        break;
    }
    case 'homepage-c' : {
        if (get_field('banner_post')) {
            if (get_field('select_banner_post')) {
                $cpost = get_field('select_banner_post');
                $fpList[] = $cpost->ID;
            } else {
                $args = array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'posts_per_page' => 1,
                    'orderby' => 'date',
                    'order' => 'DESC',
                );
                $the_query = new WP_Query($args);
                if ($the_query->have_posts()) {
                    while ($the_query->have_posts()) {
                        $the_query->the_post();
                        $fpList[] = get_the_ID();
                    }
                    wp_reset_postdata();
                }
            }
        }
        break;
    }
}
?>
<?php get_header($header); ?>
    <section>
        <?php get_template_part('content', get_field('style')); ?>
        <?php
        global $wp_query;
        $args = array(
            'post_type' => 'event',
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'cat' => $category->term_id,
        );
        $limitevent = get_field('limit_event');
        if ($limitevent) {
            $args['posts_per_page'] = get_field('limit_event');
        } else {
            $args['posts_per_page'] = 16;
        }
        $the_query = new WP_Query($args);
        if ($the_query->have_posts()) : ?>
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
                            <?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
                                <div class="itemSlider">
                                    <?php get_template_part('content', 'archive-event'); ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php wp_reset_postdata();
        endif;
        ?>

        <?php
        $posts_per_page = 8;
        $time = 0;
        $count = 0;
        global $wp_query;
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'orderby' => 'date',
            'order' => 'DESC',
            'post__not_in' => $fpList,
            'category__not_in' => array(1),
        );
        $wp_query = new WP_Query($args);
        ?>
        <?php if (have_posts()) : $time = 1; ?>

            <div class="verticalSpacing-lg"></div>

            <div class="groupPost homepage">
                <div class="container">
                    <div class="row">
                        <div class="titleOla col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <p>Ola.</p>
                        </div>
                        <div class="imgPosts">
                            <?php while (have_posts()): the_post();
                                $count++; ?>
                                <div class="col-sm-3 col-xs-6 colImages">
                                    <?php get_template_part('content', 'archive-medium'); ?>
                                </div>
                            <?php endwhile; ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php wp_reset_query(); ?>
        <?php endif; ?>

        <?php
        global $wp_query;
        $args = array(
            'post_type' => 'career',
            'post_status' => 'publish',
            'posts_per_page' => 16,
            'orderby' => 'date',
            'order' => 'DESC',
        );
        $limitcareer = get_field('limit_career');
        if ($limitcareer) {
            $args['posts_per_page'] = get_field('limit_career');
        } else {
            $args['posts_per_page'] = 16;
        }
        $the_query = new WP_Query($args);
        ?>
        <?php if ($the_query->have_posts()) : ?>
            <div class="verticalSpacing-lg"></div>

            <div class="sliderC">
                <div class="container">
                    <div class="row">
                        <div class="tag tagc col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <a href="<?php echo get_post_type_archive_link('career'); ?>">
                                <img class="image-sm" src="<?php echo THEME_IMAGES; ?>/careers-tag.png" alt="tag C">
                            </a>
                        </div>
                        <div class="wrapperSlider groupCareer col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php while ($the_query->have_posts()): $the_query->the_post(); ?>
                                <div class="itemSlider">
                                    <?php get_template_part('content', 'archive-career'); ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>

        <?php
        $total = wp_count_posts('post');
        $subtotal = $total->publish - $count;
        ?>
        <?php if ($subtotal > 0): ?>
            <div class="lazyLoad" data-time="<?php echo $time; ?>" data-offset="<?php echo $posts_per_page; ?>"
                 data-post-in="<?php echo implode(',', $fpList); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
            </div>
        <?php endif; ?>

    </section>
<?php
if (get_field('footer_') == 'random') {
    $field = get_field_object('footer_');
    $index = rand(0, 2);
    $i = 0;
    foreach ($field['choices'] as $key => $option) {
        if ($i == $index) {
            $footer_style = $key;
            break;
        }
        $i++;
    }
} else {
    $footer_style = get_field('footer_');
}
get_footer($footer_style);
?>