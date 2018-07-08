<?php get_header(); ?>
<main id="template-events" class="events-page">
    <div class="verticalSpacing-md"></div>
    <?php
    $posts_per_page = 8;
    $time = 0;
    global $wp_query;
    $count = 0;
    $args = array(
        'post_type'				=> get_post_type(),
        'post_status'			=> 'publish',
        'posts_per_page' 		=> $posts_per_page,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
    );
    if($time){
        $args['offset'] = $time*$posts_per_page;
    }
    $time++;
    $wp_query = new WP_Query( $args );
    ?>
    <?php if (have_posts()) : ?>
    <section class="container Event">
        <div class="row">
        <?php while(have_posts()): the_post();?>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 colImages">
                <?php get_template_part('content','archive-event'); ?>
            </div>
        <?php endwhile; ?>
        </div>
    </section>
    <?php endif; ?>
    <?php wp_reset_query(); ?>
    <?php
    global $wp_query;
    $args = array(
        'post_type'				=> get_post_type(),
        'post_status'			=> 'publish',
        'posts_per_page' 		=> $posts_per_page,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
    );
    if($time){
        $args['offset'] = $time*$posts_per_page;
    }
    $time++;
    $wp_query = new WP_Query( $args );
    ?>
    <?php if (have_posts()) : ?>
        <div class="verticalSpacing-lg"></div>

        <section class="bgNeuw bgNewEvent">
            <div class="imgBanner">
                <a href="<?php echo bloginfo('url') ?>">
                    <img src="<?php echo THEME_IMAGES; ?>/group.png">
                </a>
            </div>
        </section>

        <section class="container Event sectionEvent">
            <div class="row">
                <?php while(have_posts()): the_post();?>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 colImages">
                        <?php get_template_part('content','archive-event'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
        <?php
        $count = count($wp_query->posts)+ ($time-1) * $posts_per_page;
        $total = wp_count_posts(get_post_type());
        $subtotal = $total->publish - $count ;
        ?>
        <?php if($subtotal > 0): ?>
            <div class="lazyLoad" data-time="<?php echo $time; ?>" data-offset="<?php echo $posts_per_page; ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php wp_reset_query(); ?>

    <div class="verticalSpacing-lg"></div>

    <section class="bgNeuw bgNewEvent">
        <div class="imgBanner">
            <a href="<?php echo bloginfo('url') ?>">
                <img src="<?php echo THEME_IMAGES; ?>/group1.png">
            </a>
        </div>
    </section>
</main>
<?php
if(get_field('footer_') == 'random') {
    $field = get_field_object('footer_');
    $index = rand(0,2);
    $i = 0;
    foreach ($field['choices'] as $key=>$option) {
        if ($i == $index) {
            $footer_style = $key;
            break;
        }
        $i++;
    }
}
else {
    $footer_style = get_field('footer_');
}
get_footer($footer_style);
?>
