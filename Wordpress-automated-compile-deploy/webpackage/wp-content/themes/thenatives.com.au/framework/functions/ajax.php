<?php
add_action( 'wp_ajax_nopriv_archive_career_lazyload', 'archive_career_lazyload' );
add_action( 'wp_ajax_archive_career_lazyload', 'archive_career_lazyload' );
function archive_career_lazyload(){
    $posts_per_page = $_POST['offset'];
    $time = $_POST['time'];
    global $wp_query;
    $args = array(
        'post_type' => 'career',
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_page,
        'orderby' => 'taxonomy.career-packages',
        'ordertax' => 'DESC',
        'order' => 'DESC',
    );
    if ($time) {
        $time++;
        $args['offset'] = ($time-1)*$posts_per_page;
    }
    $wp_query = new WP_Query($args);
    ?>
    <?php if (have_posts()) : ?>
        <?php if($time>3): global $thenatives;?>
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
        <?php endif; ?>
        <?php if($time>2): ?>
            <div class="verticalSpacing-lg"></div>
        <?php endif; ?>
        <section class="groupCareer groupMargin">
            <div class="container wrapperVer ">
                <div class="row">
                    <?php while (have_posts()): the_post(); ?>
                        <div class="col-sm-3 col-xs-12 colImages showImage">
                            <?php get_template_part('content', 'archive-career'); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
        <?php
        $count = count($wp_query->posts) + ($time-1) * $posts_per_page;
        $args['posts_per_page'] = '-1';
        $subtotal = thenative_posts_count($args) - $count;
        ?>
        <?php if ($subtotal > 0): ?>
            <div class="lazyLoad" data-time="<?php echo $time; ?>" data-offset="<?php echo $_POST['offset']; ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php
    wp_reset_query();
    die();
}

add_action( 'wp_ajax_nopriv_archive_event_lazyload', 'archive_event_lazyload' );
add_action( 'wp_ajax_archive_event_lazyload', 'archive_event_lazyload' );
function archive_event_lazyload() {
    $posts_per_page = $_POST['offset'];
    $time = $_POST['time'];
    global $wp_query;
    $args = array(
        'post_type'				=> 'event',
        'post_status'			=> 'publish',
        'posts_per_page' 		=> $posts_per_page,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
    );
    if ($time) {
        $time++;
        $args['offset'] = ($time-1)*$posts_per_page;
    }
    $wp_query = new WP_Query( $args );
    ?>
    <?php if (have_posts()) :  ?>
        <?php if($time>3): global $thenatives;?>
            <?php if($thenatives['thenatives_event_ads_middle']): ?>
                <div class="verticalSpacing-lg"></div>

                <section class="bgNeuw">
                    <div class="imgBanner">
                        <a href="<?php echo get_field('url',$thenatives['thenatives_event_ads_middle']); ?>">
                            <img src="<?php echo get_field('image',$thenatives['thenatives_event_ads_middle']); ?>">
                        </a>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>
        <?php if($time>2): ?>
            <div class="verticalSpacing-lg"></div>
        <?php endif; ?>
        <section class="container Event sectionEvent">
            <div class="row">
                <?php while(have_posts()): the_post();?>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 colImages">
                        <?php get_template_part('content','archive-event'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
        <?php
        $count = count($wp_query->posts) + ($time-1) * $posts_per_page;
        $args['posts_per_page'] = '-1';
        $subtotal = thenative_posts_count($args) - $count;
        ?>
        <?php if($subtotal > 0): ?>
            <div class="lazyLoad" data-time="<?php echo $time; ?>" data-offset="<?php echo $_POST['offset']; ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php
    wp_reset_query();
    die();
}

add_action( 'wp_ajax_nopriv_archive_sale_lazyload', 'archive_sale_lazyload' );
add_action( 'wp_ajax_archive_sale_lazyload', 'archive_sale_lazyload' );
function archive_sale_lazyload() {
    $posts_per_page = $_POST['offset'];
    $time = $_POST['time'];
    global $wp_query;
    $args = array(
        'post_type'				=> 'sale',
        'post_status'			=> 'publish',
        'posts_per_page' 		=> $posts_per_page,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
    );
    if ($time) {
        $time++;
        $args['offset'] = ($time-1)*$posts_per_page;
    }
    $wp_query = new WP_Query( $args );
    ?>
    <?php if (have_posts()) :  ?>
        <?php if($time>3): global $thenatives;?>
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
        <?php endif; ?>
        <?php if($time>2): ?>
            <div class="verticalSpacing-lg"></div>
        <?php endif; ?>
        <section class="container Event sectionEvent">
            <div class="row">
                <?php while(have_posts()): the_post();?>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 colImages">
                        <?php get_template_part('content','archive-sale'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
        <?php
        $count = count($wp_query->posts) + ($time-1) * $posts_per_page;
        $args['posts_per_page'] = '-1';
        $subtotal = thenative_posts_count($args) - $count;
        ?>
        <?php if($subtotal > 0): ?>
            <div class="lazyLoad" data-time="<?php echo $time; ?>" data-offset="<?php echo $_POST['offset']; ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php
    wp_reset_query();
    die();
}

add_action( 'wp_ajax_nopriv_homepage_lazyload', 'homepage_lazyload' );
add_action( 'wp_ajax_homepage_lazyload', 'homepage_lazyload' );
function homepage_lazyload() {
    $posts_per_page = $_POST['offset'];
    $time = $_POST['time'];
    $post__not_in = explode(',',$_POST['post_in']);
    global $wp_query;
    $args = array(
        'post_type'				=> 'post',
        'post_status'			=> 'publish',
        'posts_per_page' 		=> $posts_per_page,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
        'category__not_in'      => array( 1 ),
    );
    if($post__not_in){
        $args['post__not_in'] = $post__not_in;
    }
    if($time){
        $time++;
        $args['offset'] = $time*$posts_per_page-$_POST['offset'];
    }
    $search = '';
    if(isset($_POST['search']) && $_POST['search']) {
        $args['s'] = $_POST['search'];
        $search = $_POST['search'];
        $args['sentence'] = true;
        $posts_per_page = $_POST['offset'];
        $args['offset'] = ($time-1) * $posts_per_page;
        $args['posts_per_page'] = $posts_per_page;
    }
    if(isset($_POST['tax']) && $_POST['tax']){
        unset($args['category__not_in']);
        $args['category_name'] = $_POST['tax'];
        unset($args['s']);
        unset($args['sentence']);
    }
    $wp_query = new WP_Query( $args );
    ?>
    <?php if (have_posts()) :  ?>
        <?php if($time>2): ?>
            <?php $frontpage_id = get_option( 'page_on_front' ); ?>
            <?php if($frontpage_id): ?>
                <?php if(get_field('banner_home',$frontpage_id)): ?>
                    <?php $banner_top = get_field('banner_home',$frontpage_id); ?>
                    <div class="verticalSpacing-lg"></div>
                    <div class="banner-top">
                        <div class="advertiseLeaderboard">
                            <?php if(!get_field('type',$banner_top->ID)): ?>
                                <?php if(get_field('url',$banner_top->ID)): ?>
                                    <a href="<?php echo get_field('url',$banner_top->ID); ?>">
                                        <img src="<?php echo get_field('image',$banner_top->ID); ?>">
                                    </a>
                                <?php else: ?>
                                    <img src="<?php echo get_field('image',$banner_top->ID); ?>">
                                <?php endif; ?>
                            <?php elseif(get_field('type',$banner_top->ID)=='1'): ?>
                                <?php if(get_field('code',$banner_top->ID)): ?>
                                    <?php echo get_field('code',$banner_top->ID); ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if(get_field('header',$banner_top->ID) && get_field('body',$banner_top->ID)): ?>
                                    <?php echo get_field('body',$banner_top->ID); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if($time > 1): ?>
            <div class="verticalSpacing-lg"></div>
        <?php endif; ?>

        <div class="groupPost homepage">
            <div class="container">
                <div class="row">
                    <div class="imgPosts">
                        <?php while(have_posts()): the_post();?>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 colImages">
                                <?php get_template_part('content', 'archive-medium'); ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $args['posts_per_page'] = '-1';
        unset($args['post__not_in']);
        if(isset($_POST['search']) && $_POST['search']) {
            $count = $time*$posts_per_page;
        }
        else {
            $count = count($wp_query->posts) + ($time - 2) * $posts_per_page + $_POST['offset'];
        }
        $subtotal = thenative_posts_count($args) - $count - count($post__not_in);
        ?>
        <?php if($subtotal > 0): ?>
            <div class="lazyLoad" data-time="<?php echo $time; ?>" data-offset="<?php echo $_POST['offset']; ?>" data-tax="<?php if($_POST['tax']) echo $_POST['tax']; ?>" data-post-in="<?php echo implode(',',$post__not_in); ?>" data-search="<?php echo $search;?>">
                <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php
    wp_reset_query();
    die();
}

add_action( 'wp_ajax_nopriv_related_post_lazyload', 'thenatives_related_post_lazyload' );
add_action( 'wp_ajax_related_post_lazyload', 'thenatives_related_post_lazyload' );
function thenatives_related_post_lazyload() {
    $posts_per_page = $_POST['offset'];
    $time = $_POST['time'];
    $post__not_in = explode(',',$_POST['post_in']);
    $offset = ($time - 1) * $posts_per_page + 6;
    $args = array(
        'post_type'				=> 'post',
        'post_status'			=> 'publish',
        'posts_per_page' 		=> $posts_per_page,
        'offset'                => $offset,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
    );
    if($post__not_in){
        $args['post__not_in'] = $post__not_in;
    }
    if(isset($_POST['tax']) && $_POST['tax']){
        $args['cat'] = $_POST['tax'];
    }
    $the_query = new WP_Query($args);
    $time++;
    ?>
    <?php if ($the_query->have_posts()) :  ?>
        <?php if($time > 2 && get_field('advertise_bottom',$_POST['post_in'])): ?>
            <?php $banner_footer = get_field('advertise_bottom',$_POST['post_in']) ?>
            <section class="adNeuw">
                <div class="verticalSpacing-lg"></div>
                <div class="bgNeuw">
                    <div class="imgBanner">
                        <?php if(get_field('url',$banner_footer->ID)): ?>
                            <a href="<?php echo get_field('url',$banner_footer->ID); ?>">
                                <img src="<?php echo get_field('image',$banner_footer->ID); ?>">
                            </a>
                        <?php else: ?>
                            <img src="<?php echo get_field('image',$banner_footer->ID); ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($time > 1): ?>
            <div class="verticalSpacing-lg"></div>
        <?php endif; ?>

        <div class="groupPost homepage">
            <div class="container">
                <div class="row">
                    <div class="imgPosts">
                        <?php while($the_query->have_posts()): $the_query->the_post();?>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 colImages">
                                <?php get_template_part('content', 'archive-medium'); ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $count = count($the_query->posts) + ($time-1) * $posts_per_page + 6;
        $args['posts_per_page'] = '-1';
        $subtotal = thenative_posts_count($args) - $count - count($post__not_in);
        ?>
        <?php if($subtotal > 0): ?>
            <div id="lazyRelatedPost" class="lazyLoad" data-time="<?php echo $time; ?>" data-offset="<?php echo $_POST['offset']; ?>" data-tax="<?php if($_POST['tax']) echo $_POST['tax']; ?>" data-post-in="<?php echo implode(',',$post__not_in); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
            </div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>
    <?php
    die();
}

add_action( 'wp_ajax_nopriv_related_career_lazyload', 'thenatives_related_career_lazyload' );
add_action( 'wp_ajax_related_career_lazyload', 'thenatives_related_career_lazyload' );
function thenatives_related_career_lazyload() {
    $posts_per_page = $_POST['offset'];
    $time = $_POST['time'];
    $post__not_in = explode(',',$_POST['post_in']);
    $offset = ($time - 1) * $posts_per_page + 6;
    $args = array(
        'post_type'				=> 'career',
        'post_status'			=> 'publish',
        'posts_per_page' 		=> $posts_per_page,
        'offset'                => $offset,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
    );
    if($post__not_in){
        $author = get_post($post__not_in[0])->post_author;
        $args['post__not_in'] = $post__not_in;
        $args['author'] = $author;
    }
    $the_query = new WP_Query($args);
    $time++;
    ?>
    <?php if ($the_query->have_posts()) :  ?>
        <?php if($time > 2 && get_field('advertise_bottom',$_POST['post_in'])): ?>
            <?php $banner_footer = get_field('advertise_bottom',$_POST['post_in']) ?>
            <section class="adNeuw">
                <div class="verticalSpacing-lg"></div>
                <div class="bgNeuw">
                    <div class="imgBanner">
                        <?php if(get_field('url',$banner_footer->ID)): ?>
                            <a href="<?php echo get_field('url',$banner_footer->ID); ?>">
                                <img src="<?php echo get_field('image',$banner_footer->ID); ?>">
                            </a>
                        <?php else: ?>
                            <img src="<?php echo get_field('image',$banner_footer->ID); ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($time > 1): ?>
            <div class="verticalSpacing-lg"></div>
        <?php endif; ?>

        <div class="groupCareer homepage">
            <div class="container">
                <div class="row">
                    <div class="imgPosts">
                        <?php while($the_query->have_posts()): $the_query->the_post();?>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 colImages">
                                <?php get_template_part('content', 'archive-career'); ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $count = count($the_query->posts) + ($time-2) * $posts_per_page + 6;
        $args['posts_per_page'] = '-1';
        $subtotal = thenative_posts_count($args) - $count;
        ?>
        <?php if($subtotal > 0): ?>
            <div id="lazyRelatedCareer" class="lazyLoad" data-time="<?php echo $time; ?>" data-offset="<?php echo $_POST['offset']; ?>" data-tax="<?php if($_POST['tax']) echo $_POST['tax']; ?>" data-post-in="<?php echo implode(',',$post__not_in); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
            </div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>
    <?php
    die();
}

add_action( 'wp_ajax_nopriv_related_event_lazyload', 'thenatives_related_event_lazyload' );
add_action( 'wp_ajax_related_event_lazyload', 'thenatives_related_event_lazyload' );
function thenatives_related_event_lazyload() {
    $posts_per_page = $_POST['offset'];
    $time = $_POST['time'];
    $post__not_in = explode(',',$_POST['post_in']);
    $offset = ($time - 1) * $posts_per_page + 6;
    $args = array(
        'post_type'				=> 'event',
        'post_status'			=> 'publish',
        'posts_per_page' 		=> $posts_per_page,
        'offset'                => $offset,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
    );
    if($post__not_in){
        $author = get_post($post__not_in[0])->post_author;
        $args['post__not_in'] = $post__not_in;
        $args['author'] = $author;
    }
    $the_query = new WP_Query($args);
    $time++;
    ?>
    <?php if ($the_query->have_posts()) :  ?>
        <?php if($time > 2 && get_field('advertise_bottom',$_POST['post_in'])): ?>
            <?php $banner_footer = get_field('advertise_bottom',$_POST['post_in']) ?>
            <section class="adNeuw">
                <div class="verticalSpacing-lg"></div>
                <div class="bgNeuw">
                    <div class="imgBanner">
                        <?php if(get_field('url',$banner_footer->ID)): ?>
                            <a href="<?php echo get_field('url',$banner_footer->ID); ?>">
                                <img src="<?php echo get_field('image',$banner_footer->ID); ?>">
                            </a>
                        <?php else: ?>
                            <img src="<?php echo get_field('image',$banner_footer->ID); ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($time > 1): ?>
            <div class="verticalSpacing-lg"></div>
        <?php endif; ?>

        <div class="Event">
            <div class="container">
                <div class="row">
                    <?php while($the_query->have_posts()): $the_query->the_post();?>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 colImages">
                            <?php get_template_part('content', 'archive-event'); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <?php
        $count = count($the_query->posts) + ($time-2) * $posts_per_page + 6;
        $args['posts_per_page'] = '-1';
        $subtotal = thenative_posts_count($args) - $count;
        ?>
        <?php if($subtotal > 0): ?>
            <div id="lazyRelatedEvent" class="lazyLoad" data-time="<?php echo $time; ?>" data-offset="<?php echo $_POST['offset']; ?>" data-tax="<?php if($_POST['tax']) echo $_POST['tax']; ?>" data-post-in="<?php echo implode(',',$post__not_in); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
            </div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>
    <?php
    die();
}

add_action( 'wp_ajax_nopriv_related_sale_lazyload', 'thenatives_related_sale_lazyload' );
add_action( 'wp_ajax_related_sale_lazyload', 'thenatives_related_sale_lazyload' );
function thenatives_related_sale_lazyload() {
    $posts_per_page = $_POST['offset'];
    $time = $_POST['time'];
    $post__not_in = explode(',',$_POST['post_in']);
    $offset = ($time - 1) * $posts_per_page + 6;
    $args = array(
        'post_type'				=> 'sale',
        'post_status'			=> 'publish',
        'posts_per_page' 		=> $posts_per_page,
        'offset'                => $offset,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
    );
    if($post__not_in){
        $author = get_post($post__not_in[0])->post_author;
        $args['post__not_in'] = $post__not_in;
        $args['author'] = $author;
    }
    $the_query = new WP_Query($args);
    $time++;
    ?>
    <?php if ($the_query->have_posts()) :  ?>
        <?php if($time > 2 && get_field('advertise_bottom',$_POST['post_in'])): ?>
            <?php $banner_footer = get_field('advertise_bottom',$_POST['post_in']) ?>
            <section class="adNeuw">
                <div class="verticalSpacing-lg"></div>
                <div class="bgNeuw">
                    <div class="imgBanner">
                        <?php if(get_field('url',$banner_footer->ID)): ?>
                            <a href="<?php echo get_field('url',$banner_footer->ID); ?>">
                                <img src="<?php echo get_field('image',$banner_footer->ID); ?>">
                            </a>
                        <?php else: ?>
                            <img src="<?php echo get_field('image',$banner_footer->ID); ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($time > 1): ?>
            <div class="verticalSpacing-lg"></div>
        <?php endif; ?>

        <div class="Event">
            <div class="container">
                <div class="row">
                    <?php while($the_query->have_posts()): $the_query->the_post();?>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 colImages">
                            <?php get_template_part('content', 'archive-sale'); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <?php
        $count = count($the_query->posts) + ($time-2) * $posts_per_page + 6;
        $args['posts_per_page'] = '-1';
        $subtotal = thenative_posts_count($args) - $count;
        ?>
        <?php if($subtotal > 0): ?>
            <div id="lazyRelatedSale" class="lazyLoad" data-time="<?php echo $time; ?>" data-offset="<?php echo $_POST['offset']; ?>" data-tax="<?php if($_POST['tax']) echo $_POST['tax']; ?>" data-post-in="<?php echo implode(',',$post__not_in); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
            </div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>
    <?php
    die();
}

add_action( 'wp_ajax_nopriv_filter_category_post', 'thenatives_filter_category_post' );
add_action( 'wp_ajax_filter_category_post', 'thenatives_filter_category_post' );
function thenatives_filter_category_post() {
    get_template_part('content','archive-'.$_POST['style']);
    die();
}

add_action( 'wp_ajax_nopriv_filter_career', 'thenatives_filter_career' );
add_action( 'wp_ajax_filter_career', 'thenatives_filter_career' );
function thenatives_filter_career() {
    get_template_part('body-archive-career');
    die();
}

add_action( 'wp_ajax_nopriv_filter_event', 'thenatives_filter_event' );
add_action( 'wp_ajax_filter_event', 'thenatives_filter_event' );
function thenatives_filter_event() {
    get_template_part('body-archive-event');
    die();
}

add_action( 'wp_ajax_nopriv_filter_sale', 'thenatives_filter_sale' );
add_action( 'wp_ajax_filter_sale', 'thenatives_filter_sale' );
function thenatives_filter_sale() {
    get_template_part('body-archive-sale');
    die();
}

add_action( 'wp_ajax_nopriv_login_ajax', 'thenatives_login_ajax' );
add_action( 'wp_ajax_login_ajax', 'thenatives_login_ajax' );
function thenatives_login_ajax() {
    parse_str($_POST['data'], $form);
    extract($form);
    if(isset($log) && isset($pwd)) {
        $info = array(
            'user_login' => $log,
            'user_password' => $pwd,
            'remember' => false,
        );
        $user = wp_signon($info, false);
        if (is_wp_error($user)) {
            $json = array('status' => 'error');
            foreach($user->errors as $key => $error) {
                $json['error'] = $key;
                break;
            }
        }
        else {
            $redirect_to = isset($redirect_to) ? $redirect_to : '';
            $json = array('status' => 'success','redirect' => $redirect_to);
        }
        echo json_encode($json);
    }
    die();
}

add_action( 'wp_ajax_nopriv_register_ajax', 'thenatives_register_ajax' );
add_action( 'wp_ajax_register_ajax', 'thenatives_register_ajax' );
function thenatives_register_ajax() {
    parse_str($_POST['data'], $form);
    $json = array();
    extract($form);
    if(isset($user_name) && isset($user_surname) && isset($user_company) && isset($user_phone) && isset($user_pass) && isset($user_email)) {
        $userdata = array(
            'first_name' => esc_attr($user_name),
            'last_name' => esc_attr($user_surname),
            'user_email' => trim($user_email),
            'user_pass' => $user_pass,
            'role' => get_option( 'author' ),
            'user_login' => md5(date('d/m/Y H:i:s')),
        );
        $new_user = wp_insert_user( $userdata );
        if($new_user->errors){
            $json['status'] = 'error';
            $json['error'] = 'is_existed';
        }
        else {
            $token = substr(md5(trim($_POST['user_email']).date('d/m/Y h:i:s')),0,16);
            update_user_meta($new_user,'user_phone',esc_attr( $user_phone ));
            update_user_meta($new_user,'user_company',esc_attr( $user_company ));
            update_user_meta($new_user,'token',$token);
            $email_to = $userdata['user_email'];
            $email_subject = 'Confirm account is registered';
            $email_header = array('Content-Type: text/html; charset=UTF-8');
            $active_link = get_register_link().'?active='.$userdata['user_email'].'&token='.$token;
            $html = '<div>';
            $html.= '<p>Hi <b>'.$userdata['first_name'].'</b></p>';
            $html.= "<p>Welcome to ".get_bloginfo('sitename').", we're happy you've joined the club. Activate your account to get started.</p>";
            $html.= '<p><a href="'.$active_link.'" style="display: inline-block; background: #000;color: #fff;text-decoration: none; padding: 5px 30px;">ACTIVATE NOW</a></p>';
            $html.= '</div>';
            if(wp_mail($email_to, $email_subject, $html, $email_header)){
                $json['status'] = 'success';
                $redirect_to = isset($redirect_to) ? $redirect_to : '';
                $json['redirect'] = $redirect_to;
            }
            else {
                wp_delete_user($new_user);
                $json['status'] = 'error';
                $json['error'] = 'send_mail_failed';
            }
        }
    }
    echo json_encode($json);
    die();
}

add_action( 'wp_ajax_nopriv_account_settings_ajax', 'thenatives_account_settings_ajax' );
add_action( 'wp_ajax_account_settings_ajax', 'thenatives_account_settings_ajax' );
function thenatives_account_settings_ajax() {
    parse_str($_POST['data'], $form);
    extract($form);
    $user_id = $form['user_id'];
    $user = get_user_by('id',$user_id);
    $userdata = get_userdata($user->ID);
    $json = array('status' => 'success');
    if(isset($user_name) && $user_name && $userdata->first_name != $user_name){
        update_user_meta($user_id,'first_name',esc_attr( $user_name ));
    }
    if(isset($user_surname) && $user_surname && $userdata->last_name != $user_surname){
        update_user_meta($user_id,'last_name',esc_attr( $user_surname ));
    }
    if(isset($user_email) && $user_email && $userdata->last_name != $user_email){
        $old_user = get_user_by('email',$user_email);
        if(!$old_user){
            update_user_meta($user_id,'change_email',esc_attr( $user_email ));
            $email_to = $user_email;
            $email_subject = 'Confirm change email';
            $email_header = array('Content-Type: text/html; charset=UTF-8');
            $html = '<div>';
            $html.= '<p>Dear <b>'.$user_name.' '.$user_surname.'</b></p>';
            $html.= '<p>Welcome to '.get_bloginfo('sitename').'</p>';
            $html.= '<p>You can <a href="'.get_account_settings_link().'/?action=change-email">click here</a> to change your mail.</p>';
            $html.= '<p>Thank you!</p>';
            $html.= '</div>';
            if(wp_mail($email_to, $email_subject, $html, $email_header)){
                $json['redirect'] = get_user_dashboard_link();
            }
            else {
                $json['status'] = 'error';
                $json['message'] = 'Send mail not request';
                $json['object'] = 'user_email';
            }
        }
        else {
            $json['status'] = 'error';
            $json['message'] = 'Email is existed';
            $json['object'] = 'user_email';
        }
    }
    if(isset($user_company) && $user_company && $userdata->user_company != $user_company){
        update_user_meta($user_id,'user_company',esc_attr( $user_company ));
    }
    if(isset($user_phone) && $user_phone && $userdata->user_phone != $user_phone){
        update_user_meta($user_id,'user_phone',esc_attr( $user_phone ));
    }
    if(isset($user_email) && $user_email && $userdata->user_email != $user_email){
        update_user_meta($user_id,'user_email',esc_attr( $user_email ));
    }
    if(isset($user_cardname) && $user_cardname && $userdata->user_cardname != $user_cardname){
        update_user_meta($user_id,'user_cardname',esc_attr( $user_cardname ));
    }
    if(isset($user_cardnumber) && $user_cardnumber && $userdata->user_cardnumber != $user_cardnumber){
        update_user_meta($user_id,'user_cardnumber',esc_attr( $user_cardnumber ));
    }
    if(isset($user_cardexpiry1) && $user_cardexpiry1 && $userdata->user_cardexpiry1 != $user_cardexpiry1){
        update_user_meta($user_id,'user_cardexpiry1',esc_attr( $user_cardexpiry1 ));
    }
    if(isset($user_cardexpiry2) && $user_cardexpiry2 && $userdata->user_cardexpiry2 != $user_cardexpiry2){
        update_user_meta($user_id,'user_cardexpiry2',esc_attr( $user_cardexpiry2 ));
    }
    if(isset($user_cardcvc) && $user_cardcvc && $userdata->user_cardcvc != $user_cardcvc){
        update_user_meta($user_id,'user_cardcvc',esc_attr( $user_cardcvc ));
    }
    if(isset($user_password) && $user_password){
        $email_to = $user_email;
        $email_subject = 'Confirm change password';
        $email_header = array('Content-Type: text/html; charset=UTF-8');
        $html = '<div>';
        $html.= '<p>Dear <b>'.$user_name.' '.$user_surname.'</b></p>';
        $html.= '<p>Welcome to '.get_bloginfo('sitename').'</p>';
        $html.= '<p>Your account infomation:</p>';
        $html.= '<p> - Username: '.$email_to.'</p>';
        $html.= '<p> - Password: '.$user_password.'</p>';
        $html.= '<p>You can <a href="'.get_login_link().'">click here</a> to login.</p>';
        $html.= '<p>Thank you!</p>';
        $html.= '</div>';
        if(wp_mail($email_to, $email_subject, $html, $email_header)){
            $user = wp_get_current_user();
            wp_set_password( $user_password, $user->ID );
            wp_set_auth_cookie($user->ID);
            wp_set_current_user($user->ID);
            do_action('wp_login', $user->user_login, $user);
        }
        $json['redirect'] = get_user_dashboard_link();
    }
    echo json_encode($json);
    die();
}

add_action( 'wp_ajax_nopriv_upload_image_media', 'thenatives_upload_image_media' );
add_action( 'wp_ajax_upload_image_media', 'thenatives_upload_image_media' );
function thenatives_upload_image_media() {
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    $uploaded = $_FILES['file'];

    $allowed_file_types = array('jpg' =>'image/jpg', 'jpeg' =>'image/jpeg', 'png' => 'image/png');
    $overrides = array('test_form' => false, 'mimes' => $allowed_file_types);
    $upload = wp_handle_upload( $uploaded, $overrides );
    if ( $upload && ! isset( $upload['error'] ) ) {
        $filename = $upload['file'];
        $filetype = wp_check_filetype( basename( $filename ), null );
        $wp_upload_dir = wp_upload_dir();
        $attachment = array(
            'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );
        $attach_id = wp_insert_attachment( $attachment, $filename );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        $json = array('status'=>'success','link'=>$attachment['guid'], 'val'=> $attach_id );
    } else {
        if(strpos($upload['error'],'upload_max_filesize')){
            $error = 'upload_max_filesize';
            $message = "File size must be less ".ini_get('upload_max_filesize').'B';
        }
        else {
            $error = 'invalid_file_type';
            $message = "Only upload file .png or .jpg";
        }
        $json = array( 'status' => 'error','error' => $error,'message' => $message);
    }
    echo json_encode($json);
    die();
}

add_action( 'wp_ajax_nopriv_forgot_password', 'thenatives_forgot_password' );
add_action( 'wp_ajax_forgot_password', 'thenatives_forgot_password' );
function thenatives_forgot_password() {
    parse_str($_POST['data'], $form);
    extract($form);
    $json = array();
    if(isset($email)) {
        $userdata = get_user_by('email', $email);
        if($userdata){
            $json = array('status'=>'success','message'=>'<h2>Thank you<br><br>Check your inbox to take your new password.</h2>');
            $password = substr(md5(date('d/m/Y h:i:s')),0, 16);
            $email_subject = get_bloginfo('sitename').' - New Password';
            $email_header = array('Content-Type: text/html; charset=UTF-8');
            $html = '<div>';
            $html.= '<p>Dear <b>'.$userdata->first_name.' '.$userdata->last_name.'</b></p>';
            $html.= '<p>Welcome to '.get_bloginfo('sitename').'</p>';
            $html.= '<p>Your new password is: '.$password.'</p>';
            $html.= '<p>Thank you!</p>';
            $html.= '</div>';
            if(wp_mail($email, $email_subject, $html, $email_header)){
                wp_set_password( $password, $userdata->ID );
            }
            else {
                $json['status'] = 'error';
                $json['message'] = 'Please try again later';
            }
        }
        else {
            $json = array('status'=>'error','message'=>'Email does not exist');
        }
    }
    echo json_encode($json);
    die();
}

add_action( 'wp_ajax_nopriv_reset_password', 'thenatives_reset_password' );
add_action( 'wp_ajax_reset_password', 'thenatives_reset_password' );
function thenatives_reset_password() {
    parse_str($_POST['data'], $form);
    extract($form);
    $json = array();
    if(isset($reset_email) && isset($new_password)) {
        $userdata = get_user_by('email', $reset_email);
        if($userdata){
            $json = array('status'=>'success','message'=>'<h2>Thank you<br><br>Check your inbox to reset your password.</h2>');
            $email_subject = get_bloginfo('sitename').' - New Password';
            $email_header = array('Content-Type: text/html; charset=UTF-8');
            $token = substr(md5(trim($reset_email).date('d/m/Y h:i:s')),0,16);
            update_user_meta($userdata->ID,'token',$token);
            update_user_meta($userdata->ID,'new_password',$new_password);
            $active_link = get_reset_password_link().'?reset='.$reset_email.'&token='.$token;
            $html = '<div>';
            $html.= '<p>Dear <b>'.$userdata->first_name.' '.$userdata->last_name.'</b></p>';
            $html.= '<p>Welcome to '.get_bloginfo('sitename').'</p>';
            $html.= '<p>Your new password is: '.$new_password.'</p>';
            $html.= '<p>Please, you <a href="'.$active_link.'">click here</a> to confirm password reset</p>';
            $html.= '<p>Thank you!</p>';
            $html.= '</div>';
            if(!wp_mail($reset_email, $email_subject, $html, $email_header)){
                $json['status'] = 'error';
                $json['message'] = 'Please try again later';
            }
        }
        else {
            $json = array('status'=>'error','message'=>'Email does not exist');
        }
    }
    echo json_encode($json);
    die();
}

add_action( 'wp_ajax_nopriv_post_job_ajax', 'thenatives_post_job_ajax' );
add_action( 'wp_ajax_post_job_ajax', 'thenatives_post_job_ajax' );
function thenatives_post_job_ajax() {
    parse_str($_POST['data'], $form);
    extract($form);
    $user_id = $form['user_id'];
    $json = array();
    if (isset($title)) {
        if(!isset($post_id)) {
            $post = wp_insert_post(array('post_title' => $title, 'post_status' => 'draft', 'post_type' => 'career', 'post_author' => $user_id));
        }
        else{
            wp_update_post( array(
                'ID'           => $post_id,
                'post_title'   => $title,
            ) );
            $post = $post_id;
        }
        if($post){
            $json = array('status' => 'success', 'redirect' => get_purchase_link().'/?id='.$post);
            $uploaded = $_FILES['file'];
            if($uploaded) {
                $allowed_file_types = array('jpg' => 'image/jpg', 'jpeg' => 'image/jpeg', 'png' => 'image/png');
                $overrides = array('test_form' => false, 'mimes' => $allowed_file_types);
                $upload = wp_handle_upload($uploaded, $overrides);
                if ($upload && !isset($upload['error'])) {
                    $filename = $upload['file'];
                    $filetype = wp_check_filetype(basename($filename), null);
                    $wp_upload_dir = wp_upload_dir();
                    $attachment = array(
                        'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                        'post_mime_type' => $filetype['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    $attach_id = wp_insert_attachment($attachment, $filename);
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                    $data = $_POST['image'];
                    list($file_type, $data) = explode(';', $data);
                    list(, $file_type) = explode(':', $file_type);
                    list(, $imagetype) = explode('/', $filetype['type']);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $dir = wp_upload_dir();
                    $path = $dir['basedir'].'/';
                    $filename = substr(strrchr($attach_data['file'], '/'), 1);
                    $path = $path.str_replace($filename,'',$attach_data['file']);
                    $imagetype = substr(strrchr($filename, '.'), 1);
                    $filename = str_replace('.'.$imagetype,'',$filename).'-304x421.'.$imagetype;
                    $fileupload = $path.$filename;
                    file_put_contents($fileupload, $data);
                    $attach_data['sizes']['medium'] = array(
                        'file' => $filename,
                        'width' => 304,
                        'height' => 421,
                        'mime-type' => $filetype['type'],
                    );
                    wp_update_attachment_metadata( $attach_id, $attach_data );
                    set_post_thumbnail( $post, $attach_id );
                }
            }
            else {
                if(isset($image_id) && $image_id) {
                    $attach_data = wp_get_attachment_metadata($image_id);
                    $data = $_POST['image'];
                    list(, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $dir = wp_upload_dir();
                    $path = $dir['basedir'].'/';
                    $filename = substr(strrchr($attach_data['file'], '/'), 1);
                    $path = $path.str_replace($filename,'',$attach_data['file']);
                    $imagetype = substr(strrchr($filename, '.'), 1);
                    $filename = str_replace('.'.$imagetype,'',$filename).'-304x384.'.$imagetype;
                    $fileupload = $path.$filename;
                    file_put_contents($fileupload, $data);
                    if($imagetype=='jpg') {
                        $imagetype = 'jpeg';
                    }
                    $filetype = "image/$imagetype";
                    if(!isset($attach_data['sizes']['show'])) {
                        $attach_data['sizes']['show'] = array(
                            'file' => $filename,
                            'width' => 304,
                            'height' => 384,
                            'mime-type' => $filetype,
                        );
                    }
                }
            }
            if(isset($companycareer) && $companycareer){
                update_field('companies_career', $companycareer, $post);
            }
            if(isset($type) && $type){
                wp_set_object_terms( $post, array(intval($type)), 'career-types' );
            }
            if(isset($level) && $level){
                wp_set_object_terms( $post, array(intval($level)), 'career-levels' );
            }
            if(isset($citycareer) && $citycareer){
                update_field('city_career', $citycareer, $post);
            }
            if(isset($suburb) && $suburb){
                update_field('suburb', $suburb, $post);
            }
            if(isset($state) && $state){
                update_field('state', $state, $post);
            }
            if(isset($description) && $description){
                update_field('role_description', $description, $post);
            }
            if(isset($responsibilities) && $responsibilities){
                update_field('key_responsibilities', $responsibilities, $post);
            }
            if(isset($max_salary) && $max_salary){
                update_field('salary_max', $max_salary, $post);
            }
            if(isset($min_salary) && $min_salary){
                update_field('salary_min', $min_salary, $post);
            }
            if(isset($show_salary) && $show_salary) {
                update_field('show_salary', $show_salary, $post);
            }
            if(isset($closing) && $closing){
                update_field('closing', $closing, $post);
            }
            if(isset($email) && $email){
                update_field('applications_sent_to', $email, $post);
            }
        }
        else {
            $json = array('status' => 'error');
        }
    }
    echo json_encode($json);
    die();
}

add_action( 'wp_ajax_nopriv_post_job_purchase', 'thenatives_post_job_purchase' );
add_action( 'wp_ajax_post_job_purchase', 'thenatives_post_job_purchase' );
function thenatives_post_job_purchase() {
    parse_str($_POST['data'], $form);
    extract($form);
    $json = array();
    if(isset($post_id)){
        $post = get_post($post_id);
        if(isset($career_type)){
            update_field('priority', $career_type, $post_id);
        }
        $json = array('status' => 'success', 'redirect' => get_checkout_link().'?id='.$post_id);
    }
    echo json_encode($json);
    die();
}

add_action( 'wp_ajax_nopriv_post_job_checkout', 'thenatives_post_job_checkout' );
add_action( 'wp_ajax_post_job_checkout', 'thenatives_post_job_checkout' );
function thenatives_post_job_checkout() {
    parse_str($_POST['data'], $form);
    extract($form);
    $json = array();
    if(isset($post_id)){
        /*$post = get_post($post_id);
        if(isset($career_type)){
            update_field('purchase', $career_type, $post_id);
        }*/
        $json = array('status' => 'success');
    }
    echo json_encode($json);
    die();
}

add_action( 'wp_ajax_nopriv_delete_post', 'thenatives_delete_post' );
add_action( 'wp_ajax_delete_post', 'thenatives_delete_post' );
function thenatives_delete_post() {
    parse_str($_POST['data'], $form);
    extract($form);
    $json = array();
    if(isset($post_id)){
        wp_delete_post($post_id);
        $json = array('status' => 'success', 'redirect'=> get_user_dashboard_link());
    }
    echo json_encode($json);
    die();
}

add_action( 'wp_ajax_nopriv_post_sale_ajax', 'thenatives_post_sale_ajax' );
add_action( 'wp_ajax_post_sale_ajax', 'thenatives_post_sale_ajax' );
function thenatives_post_sale_ajax()
{
    parse_str($_POST['data'], $form);
    extract($form);
    $user_id = $form['user_id'];
    $json = array();
    $title = '';
    if (isset($sale_name) && $sale_name) {
        $title =($sale_name);
    }
    if (isset($sale_info) && $sale_info) {
        $content =($sale_info);
    }
    if (isset($title)) {
        if(!isset($post_id)){
            $post = wp_insert_post(array('post_title' => $title,'post_content' => $content ,'post_status' => 'draft', 'post_type' => 'sale', 'post_author' => $user_id ));
        }
        else{
            wp_update_post( array(
                'ID'           => $post_id,
                'post_title'   => $title,
                'post_content' => $content,
            ) );
            $post = $post_id;
        }
        if ($post) {
            $json = array('status' => 'success','redirect' => get_purchase_link().'?id='.$post);
            $uploaded = $_FILES['file'];
            if($uploaded) {
                $allowed_file_types = array('jpg' => 'image/jpg', 'jpeg' => 'image/jpeg', 'png' => 'image/png');
                $overrides = array('test_form' => false, 'mimes' => $allowed_file_types);
                $upload = wp_handle_upload($uploaded, $overrides);
                if ($upload && !isset($upload['error'])) {
                    $filename = $upload['file'];
                    $filetype = wp_check_filetype(basename($filename), null);
                    $wp_upload_dir = wp_upload_dir();
                    $attachment = array(
                        'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                        'post_mime_type' => $filetype['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    $attach_id = wp_insert_attachment($attachment, $filename);
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                    $data = $_POST['image'];
                    list($file_type, $data) = explode(';', $data);
                    list(, $file_type) = explode(':', $file_type);
                    list(, $imagetype) = explode('/', $filetype['type']);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $dir = wp_upload_dir();
                    $path = $dir['basedir'].'/';
                    $filename = substr(strrchr($attach_data['file'], '/'), 1);
                    $path = $path.str_replace($filename,'',$attach_data['file']);
                    $imagetype = substr(strrchr($filename, '.'), 1);
                    $filename = str_replace('.'.$imagetype,'',$filename).'-304x384.'.$imagetype;
                    $fileupload = $path.$filename;
                    file_put_contents($fileupload, $data);
                    $attach_data['sizes']['show'] = array(
                        'file' => $filename,
                        'width' => 304,
                        'height' => 384,
                        'mime-type' => $filetype['type'],
                    );
                    wp_update_attachment_metadata( $attach_id, $attach_data );
                    set_post_thumbnail( $post, $attach_id );
                }
            }
            else {
                if(isset($image_id) && $image_id) {
                    $attach_data = wp_get_attachment_metadata($image_id);
                    $data = $_POST['image'];
                    list(, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $dir = wp_upload_dir();
                    $path = $dir['basedir'].'/';
                    $filename = substr(strrchr($attach_data['file'], '/'), 1);
                    $path = $path.str_replace($filename,'',$attach_data['file']);
                    $imagetype = substr(strrchr($filename, '.'), 1);
                    $filename = str_replace('.'.$imagetype,'',$filename).'-304x384.'.$imagetype;
                    $fileupload = $path.$filename;
                    file_put_contents($fileupload, $data);
                    if($imagetype=='jpg') {
                        $imagetype = 'jpeg';
                    }
                    $filetype = "image/$imagetype";
                    if(!isset($attach_data['sizes']['show'])) {
                        $attach_data['sizes']['show'] = array(
                            'file' => $filename,
                            'width' => 304,
                            'height' => 384,
                            'mime-type' => $filetype,
                        );
                    }
                }
            }
            if (isset($sale_brand_name) && $sale_brand_name) {
                update_field('sale_brand_name', $sale_brand_name, $post);
            }
            if (isset($time_zone) && $time_zone) {
                update_field('sale_time_zone', $time_zone, $post);
            }
            if (isset($sale_address_line_1) && $sale_address_line_1) {
                update_field('sale_address_line_1', $sale_address_line_1, $post);
            }
            if (isset($sale_address_line_2) && $sale_address_line_2) {
                update_field('sale_address_line_2', $sale_address_line_2, $post);
            }
            if (isset($sale_suburb) && $sale_suburb) {
                update_field('sale_suburb', $sale_suburb, $post);
            }
            if (isset($sale_city) && $sale_city) {
                update_field('sale_city', $sale_city, $post);
            }
            if (isset($sale_state) && $sale_state) {
                update_field('sale_state', $sale_state, $post);
            }
            if (isset($start_date) && $start_date) {
                update_field('sale_starting_day', $start_date, $post);
            }
            if (isset($end_date) && $end_date) {
                update_field('sale_end_day', $end_date, $post);
            }
            if (isset($start_time) && $start_time) {
                update_field('sale_start_time', $start_time, $post);
            }
            if (isset($end_time) && $end_time) {
                update_field('sale_end_time', $end_time, $post);
            }
            if (isset($date_and_opening) && $date_and_opening) {
                update_field('date_and_opening', $date_and_opening, $post);
            }
            if (isset($facebook_sale_url) && $facebook_sale_url) {
                update_field('sale_facebook_url', array('url'=>$facebook_sale_url), $post);
            }
            if (isset($sale_website_URL_1) && $sale_website_URL_1) {
                update_field('sale_website_url_1', array('url'=>$sale_website_URL_1), $post);
            }
            if (isset($sale_website_URL_2) && $sale_website_URL_2) {
                update_field('sale_website_url_2', array('url'=>$sale_website_URL_2), $post);
            }
            if (isset($sale_organizer_email) && $sale_organizer_email) {
                update_field('sale_organizer_email', $sale_organizer_email, $post);
            }

        } else {
            $json = array('status' => 'error');
        }
    }
    echo json_encode($json);
    die();
}

add_action( 'wp_ajax_nopriv_post_event_ajax', 'thenatives_post_event_ajax' );
add_action( 'wp_ajax_post_event_ajax', 'thenatives_post_event_ajax' );
function thenatives_post_event_ajax()
{
    parse_str($_POST['data'], $form);
    extract($form);
    $user_id = $form['user_id'];
    $json = array();
    $title = '';
    if (isset($event_name) && $event_name) {
        $title =($event_name);
    }
    if (isset($event_info) && $event_info) {
        $content =($event_info);
    }
    if (isset($title)) {
        if(!isset($post_id)){
            $post = wp_insert_post(array('post_title' => $title,'post_content' => $content ,'post_status' => 'draft', 'post_type' => 'event', 'post_author' => $user_id ));
        }
        else{
            wp_update_post( array(
                'ID'           => $post_id,
                'post_title'   => $title,
                'post_content' => $content,
            ) );
            $post = $post_id;
        }
        if ($post) {
            $json = array('status' => 'success','redirect' => get_purchase_link().'?id='.$post);
            $uploaded = $_FILES['file'];
            if($uploaded) {
                $allowed_file_types = array('jpg' => 'image/jpg', 'jpeg' => 'image/jpeg', 'png' => 'image/png');
                $overrides = array('test_form' => false, 'mimes' => $allowed_file_types);
                $upload = wp_handle_upload($uploaded, $overrides);
                if ($upload && !isset($upload['error'])) {
                    $filename = $upload['file'];
                    $filetype = wp_check_filetype(basename($filename), null);
                    $wp_upload_dir = wp_upload_dir();
                    $attachment = array(
                        'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                        'post_mime_type' => $filetype['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    $attach_id = wp_insert_attachment($attachment, $filename);
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                    $data = $_POST['image'];
                    list($file_type, $data) = explode(';', $data);
                    list(, $file_type) = explode(':', $file_type);
                    list(, $imagetype) = explode('/', $filetype['type']);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $dir = wp_upload_dir();
                    $path = $dir['basedir'].'/';
                    $filename = substr(strrchr($attach_data['file'], '/'), 1);
                    $path = $path.str_replace($filename,'',$attach_data['file']);
                    $imagetype = substr(strrchr($filename, '.'), 1);
                    $filename = str_replace('.'.$imagetype,'',$filename).'-304x384.'.$imagetype;
                    $fileupload = $path.$filename;
                    file_put_contents($fileupload, $data);
                    $attach_data['sizes']['show'] = array(
                        'file' => $filename,
                        'width' => 304,
                        'height' => 384,
                        'mime-type' => $filetype['type'],
                    );
                    wp_update_attachment_metadata( $attach_id, $attach_data );
                    set_post_thumbnail( $post, $attach_id );
                }
            }
            else {
                if(isset($image_id) && $image_id) {
                    $attach_data = wp_get_attachment_metadata($image_id);
                    $data = $_POST['image'];
                    list(, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $dir = wp_upload_dir();
                    $path = $dir['basedir'].'/';
                    $filename = substr(strrchr($attach_data['file'], '/'), 1);
                    $path = $path.str_replace($filename,'',$attach_data['file']);
                    $imagetype = substr(strrchr($filename, '.'), 1);
                    $filename = str_replace('.'.$imagetype,'',$filename).'-304x384.'.$imagetype;
                    $fileupload = $path.$filename;
                    file_put_contents($fileupload, $data);
                    if($imagetype=='jpg') {
                        $imagetype = 'jpeg';
                    }
                    $filetype = "image/$imagetype";
                    if(!isset($attach_data['sizes']['show'])) {
                        $attach_data['sizes']['show'] = array(
                            'file' => $filename,
                            'width' => 304,
                            'height' => 384,
                            'mime-type' => $filetype,
                        );
                    }
                }
            }
            if (isset($event_brand_name) && $event_brand_name) {
                update_field('event_brand_name', $event_brand_name, $post);
            }
            if (isset($time_zone) && $time_zone) {
                update_field('time_zone', $time_zone, $post);
            }
            if (isset($event_address_line_1) && $event_address_line_1) {
                update_field('event_address_line_1', $event_address_line_1, $post);
            }
            if (isset($event_address_line_2) && $event_address_line_2) {
                update_field('event_address_line_2', $event_address_line_2, $post);
            }
            if (isset($event_suburb) && $event_suburb) {
                update_field('event_suburb', $event_suburb, $post);
            }
            if (isset($event_city) && $event_city) {
                update_field('event_city', $event_city, $post);
            }
            if (isset($event_state) && $event_state) {
                update_field('event_state', $event_state, $post);
            }
            if (isset($start_date) && $start_date) {
                update_field('starting_day', $start_date, $post);
            }
            if (isset($end_date) && $end_date) {
                update_field('end_day', $end_date, $post);
            }
            if (isset($start_time) && $start_time) {
                update_field('event_start_time', $start_time, $post);
            }
            if (isset($end_time) && $end_time) {
                update_field('event_end_time', $end_time, $post);
            }
            if (isset($facebook_event_url) && $facebook_event_url) {
                update_field('event_facebook_url', array('url'=>$facebook_event_url), $post);
            }
            if (isset($event_website_URL_1) && $event_website_URL_1) {
                update_field('event_website_url_1', array('url'=>$event_website_URL_1), $post);
            }
            if (isset($event_website_URL_2) && $event_website_URL_2) {
                update_field('event_website_url_2', array('url'=>$event_website_URL_2), $post);
            }
            if (isset($event_info) && $event_info) {
                update_field('event_info', $event_info, $post);
            }
            if (isset($organizer_email) && $organizer_email) {
                update_field('organizer_email', $organizer_email, $post);
            }
        } else {
            $json = array('status' => 'error');
        }
    }
    echo json_encode($json);
    die();
}