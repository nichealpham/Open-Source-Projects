<main class="layout-vertical">
    <section class="selectAria">
        <div class="container">
            <div class="row margin-space">
                <div class="col-lg-3 col-md-3 col-sm-6 hidden-xs chooseCity chooseOption colImages">
                    <?php
                    $category = get_category(get_query_var('cat'),false);
                    $categories = get_categories(
                        array( 'parent' => $category->cat_ID )
                    );
                    ?>
                    <?php if(count($categories)): ?>
                        <select>
                            <option value="">All categories</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat->slug; ?>"><?php echo $cat->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 hidden-xs chooseCity chooseOption colImages">
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 hidden-xs chooseCity chooseOption colImages">
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 hidden-xs chooseCity chooseOption colImages">
                </div>
            </div>
        </div>
    </section>

    <div class="verticalSpacing-md"></div>

    <?php
    $category = get_category(get_query_var('cat'),false);
    $posts_per_page = 8;
    $time = 0;
    global $wp_query;
    $count = 0;
    $args = array(
        'post_type'				=> 'post',
        'post_status'			=> 'publish',
        'posts_per_page' 		=> $posts_per_page,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
        'cat'                   => $category->term_id,
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
        'post_type'				=> 'post',
        'post_status'			=> 'publish',
        'posts_per_page' 		=> $posts_per_page,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
        'cat'                   => $category->term_id,
    );
    if($time){
        $args['offset'] = $time*$posts_per_page;
    }
    $time++;
    $wp_query = new WP_Query( $args );
    ?>
    <?php if (have_posts()) : ?>
        <section class="contentVertical">

        <?php if($banner_middle = get_field('advertise_middle','category_'.$category->term_id)): ?>
            <section class="adNeuw">
                <div class="verticalSpacing-lg"></div>
                <div class="bgNeuw">
                    <div class="imgBanner-small">
                        <?php if(get_field('url',$banner_middle->ID)): ?>
                            <a href="<?php echo get_field('url',$banner_middle->ID); ?>">
                                <img src="<?php echo get_field('image',$banner_middle->ID); ?>">
                            </a>
                        <?php else: ?>
                            <img src="<?php echo get_field('image',$banner_middle->ID); ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        <section class="container Event sectionEvent">
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

    <section class="contentVertical">

    <?php if($banner_bottom = get_field('advertise_bottom','category_'.$category->term_id)): ?>
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
</main>
