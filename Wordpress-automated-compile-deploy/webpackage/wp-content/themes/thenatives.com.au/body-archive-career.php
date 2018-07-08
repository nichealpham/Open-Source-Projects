<?php
$posts_per_page = 8;
$time = 0;
global $wp_query,$thenatives;
$count = 0;
$args = array(
    'post_type'				=> 'career',
    'post_status'			=> 'publish',
    'posts_per_page' 		=> $posts_per_page,
    'orderby' 				=> 'taxonomy.career-packages',
    'ordertax' 				=> 'DESC',
    'order'                 => 'DESC',
);
if($time){
    $args['offset'] = $time*$posts_per_page;
}
if((isset($_POST['city']) && $_POST['city'])|| (isset($_POST['level']) && $_POST['level']) || (isset($_POST['type']) && $_POST['type'])) {
    $tax_query = array(
        'relation' => 'AND',
    );
    if(isset($_POST['city']) && $_POST['city']){
        $tax_query[] = array(
            'taxonomy' => 'career-cities',
            'field' => 'id',
            'terms' => $_POST['city'],
            'include_children' => true,
            'operator' => 'IN'
        );
    }
    if(isset($_POST['level']) && $_POST['level']){
        $tax_query[] = array(
            'taxonomy' => 'career-levels',
            'field' => 'id',
            'terms' => $_POST['level'],
            'include_children' => true,
            'operator' => 'IN'
        );
    }
    if(isset($_POST['type']) && $_POST['type']){
        $tax_query[] = array(
            'taxonomy' => 'career-types',
            'field' => 'id',
            'terms' => $_POST['type'],
            'include_children' => true,
            'operator' => 'IN'
        );
    }
    $args['tax_query'] = $tax_query;
}
$time++;
$wp_query = new WP_Query( $args );
?>
<?php if (have_posts()) : ?>
    <section class="groupCareer">
        <div class="container wrapperVer ">
            <div class="row">
                <?php while(have_posts()): the_post(); $count++;?>
                    <div class="col-sm-3 col-xs-12 colImages showImage">
                        <?php get_template_part('content','archive-career'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
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
    <?php if($thenatives['thenatives_career_ads_middle']): ?>
        <div class="verticalSpacing-lg"></div>

        <section class="bgNeuw">
            <div class="imgBanner">
                <a href="<?php echo get_field('url',$thenatives['thenatives_career_ads_middle']); ?>">
                    <img src="<?php echo get_field('image',$thenatives['thenatives_career_ads_middle']); ?>">
                </a>
            </div>
        </section>
    <?php endif; ?>

    <div class="verticalSpacing-lg"></div>

    <section class="groupCareer groupMargin">
        <div class="container wrapperVer ">
            <div class="row">
                <?php while(have_posts()): the_post(); $count++;?>
                    <div class="col-sm-3 col-xs-12 colImages showImage">
                        <?php get_template_part('content','archive-career'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
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
<?php if($count>= 13): ?>
    <?php if($thenatives['thenatives_career_ads_bottom']): ?>
        <div class="verticalSpacing-lg"></div>
        <section class="bgNeuw">
            <div class="imgBanner-small">
                <a href="<?php echo get_field('url',$thenatives['thenatives_career_ads_bottom']); ?>">
                    <img src="<?php echo get_field('image',$thenatives['thenatives_career_ads_bottom']); ?>">
                </a>
            </div>
        </section>
    <?php endif; ?>
<?php endif; ?>
<?php wp_reset_query(); ?>