<?php
$posts_per_page = 8;
$time = 0;
global $wp_query,$thenatives;
$count = 0;
$args = array(
    'post_type'         => 'sale',
    'post_status'       => 'publish',
    'posts_per_page'    => $posts_per_page,
    'orderby'           => 'date',
    'order'             => 'DESC',
);
if((isset($_POST['city']) && $_POST['city'])|| (isset($_POST['category']) && $_POST['category'])) {
    $tax_query = array(
        'relation' => 'AND',
    );
    if(isset($_POST['city']) && $_POST['city']){
        $tax_query[] = array(
            'taxonomy' => 'sale-cities',
            'field' => 'id',
            'terms' => $_POST['city'],
            'include_children' => true,
            'operator' => 'IN'
        );
    }
    if(isset($_POST['category']) && $_POST['category']){
        $tax_query[] = array(
            'taxonomy' => 'sale-categories',
            'field' => 'id',
            'terms' => $_POST['category'],
            'include_children' => true,
            'operator' => 'IN'
        );
    }
    $args['tax_query'] = $tax_query;
}
if($time){
    $args['offset'] = $time*$posts_per_page;
}
$time++;
$wp_query = new WP_Query( $args );
?>
<?php if (have_posts()) : ?>
    <section class="container Event">
        <div class="row">
            <?php while(have_posts()): the_post(); $count++;?>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 colImages showImage">
                    <?php get_template_part('content','archive-sale'); ?>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
<?php endif; ?>
<?php wp_reset_query(); ?>
<?php
if($time){
    $args['offset'] = $time*$posts_per_page;
}
$time++;
$wp_query = new WP_Query( $args );
?>
<?php if (have_posts()) : ?>
    <?php if($thenatives['thenatives_sale_ads_middle']): ?>
        <div class="verticalSpacing-lg"></div>
        <section class="bgNeuw">
            <div class="imgBanner">
                <a href="<?php echo get_field('url',$thenatives['thenatives_sale_ads_middle']); ?>">
                    <img src="<?php echo get_field('image',$thenatives['thenatives_sale_ads_middle']); ?>">
                </a>
            </div>
        </section>
    <?php endif; ?>

    <div class="verticalSpacing-lg"></div>

    <section class="container Event sectionEvent">
        <div class="row">
            <?php while(have_posts()): the_post(); $count++;?>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 colImages showImage">
                    <?php get_template_part('content','archive-sale'); ?>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    <?php
    $args['posts_per_page'] = '-1';
    $subtotal = thenative_posts_count($args) - $count;
    ?>
    <?php if($subtotal > 0): ?>
        <div class="lazyLoad" data-time="<?php echo $time; ?>" data-offset="<?php echo $posts_per_page; ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php wp_reset_query(); ?>
<?php if($count>= 13): ?>
    <?php if($thenatives['thenatives_sale_ads_bottom']): ?>
        <div class="verticalSpacing-lg"></div>

        <section class="bgNeuw">
            <div class="imgBanner-small">
                <a href="<?php echo get_field('url',$thenatives['thenatives_sale_ads_bottom']); ?>">
                    <img src="<?php echo get_field('image',$thenatives['thenatives_sale_ads_bottom']); ?>">
                </a>
            </div>
        </section>
    <?php endif; ?>
<?php endif; ?>
<?php wp_reset_query(); ?>