<main id="articleShoot-Page" class="articleShoot">
    <section class="container">
        <div class="articleMeta">
            <div class="row">
                <?php
                $category = get_the_category();
                $check_parent = false;
                if($category[0]->category_parent) {
                    $parent_cat = get_category($category[0]->category_parent);
                    while ($parent_cat->category_parent) {
                        $parent_cat = get_category($parent_cat->category_parent);
                    }
                    $check_parent = true;
                }
                else {
                    $parent_cat = $category[0];
                }
                ?>
                <div class="col-lg-7 col-md-8 col-sm-7 col-xs-7">
                    <?php if($category): ?>
                        <?php if($parent_cat): ?>
                            <a href="<?php echo get_category_link($parent_cat); ?>" class="tagName fashion"><?php echo $parent_cat->name; ?></a>
                        <?php endif; ?>
                        <?php if($check_parent): ?>
                            <span class="titleText"><?php echo $category[0]->name; ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="col-lg-5 col-md-4 col-sm-5 col-xs-5">
                    <span class="titleText"><?php echo get_the_date('d.m.Y'); ?></span>
                </div>
            </div>
        </div>

        <div class="articleHeader">
            <?php
            if (have_rows('slider')): ?>
                <div class="articleShootSlideCover">
                    <?php while (have_rows('slider')): the_row(); ?>
                        <?php $caption = get_sub_field('caption') ? get_sub_field('caption') : ''; ?>
                        <img src="<?php the_sub_field('image'); ?>" alt="<?php echo $caption; ?>">
                    <?php break ; endwhile; ?>
                    <div class="wrapperHeader">
                        <h1 class="titleArticle_Shoot"><?php the_title() ?></h1>
                        <p class="description_TitleArticle_Shoot"><?php the_sub_field('description'); ?></p>
                        <p class="descriptionContent_TitleArticle_Shoot"><?php the_sub_field('content_banner'); ?></p>
                    </div>
                    <div class="title-first-image">
                        <p class="pull-right look-slide">12 LOOKS</p>
                    </div>
                </div>
                <div class="articleSliderShoot">
                    <?php $i = 0; reset_rows();
                    while (have_rows('slider')): the_row();
                        $i++; ?>
                        <div class="articleShootSlide">
                            <?php $caption = get_sub_field('caption') ? get_sub_field('caption') : ''; ?>
                            <?php if(get_sub_field('video')): ?>
                                <iframe src="<?php echo str_replace('watch?v=','embed/',get_sub_field('video')); ?>" frameborder="0" allowfullscreen></iframe>
                            <?php else: ?>
                                <img src="<?php the_sub_field('image'); ?>" alt="<?php echo $caption; ?>">
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
                <a class="getLook">GET THIS LOOK</a>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div>
        <div class="contentArticle">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 articleRight">
                    <?php if (have_rows('slider')): ?>
                    <div class="sliderPost row">
                        <?php
                            $i = 0;
                            while (have_rows('slider')): the_row(); $i++;
                        ?>
                        <?php if(get_sub_field('image')):?>
                            <div id="sliderPostItem<?php echo $i ?>" class="sliderPostItem col-lg-6 col-me-6 col-sm-12 col-xs-12">
                            <p class="numberSliderPost"><?php echo ($i<10)?('0'.$i):$i; ?></p>
                            <?php $caption = get_sub_field('caption') ? get_sub_field('caption') : ('Slider ' . $i); ?>
                            <img class="img-responsive imageSliderPost"
                                 src="<?php the_sub_field('image'); ?>" alt="<?php echo $caption; ?>">
                            <p class="descriptionSliderPost"><?php the_sub_field('content_thumbnail'); ?></p>
                            </div>
                        <?php endif ;?>
                                    <?php ;endwhile; ?>
                    </div>
                    <?php endif; ?>

                    <?php get_template_part('framework/include/sharebox'); ?>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 setPosition">
                    <?php get_template_part('framework/include/article-sidebar'); ?>
                </div>
            </div>
        </div>
        <?php if(check_related_post()) : ?>
            <?php get_template_part('framework/include/related'); ?>
            <?php if(total_related_post() - check_related_post() > 0): ?>
                <div id="lazyRelatedPost" class="lazyLoad" data-time="1" data-offset="8" data-tax="<?php echo $category[0]->term_id; ?>" data-post-in="<?php the_ID(); ?>">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>