<main id="articleC-Page" class="articleC">
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
            <h1 class="titleArticle_C"><?php the_title() ?></h1>
            <?php if (have_rows('slider')): ?>
                <div class="articleSlider articleSliderC">
                    <?php $i = 0;
                    while (have_rows('slider')): the_row();
                        $i++; ?>
                        <div class="articleCSlide">
                            <?php $caption = get_sub_field('caption') ? get_sub_field('caption') : ''; ?>
                            <?php if(get_sub_field('video')): ?>
                                <iframe src="<?php echo str_replace('watch?v=','embed/',get_sub_field('video')); ?>" frameborder="0" allowfullscreen></iframe>
                            <?php else: ?>
                                <img src="<?php the_sub_field('image'); ?>" alt="<?php echo $caption; ?>">
                            <?php endif; ?>
                            <h4><?php echo $caption; ?></h4>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="contentArticle">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 articleRight">
                    <?php if(get_field('author') || get_field('photographer') || get_field('image_by') || get_field('illustration_by')): ?>
                        <div class="titleText">
                            <?php if(get_field('author')): ?>
                                <div><?php the_field('author'); ?></div>
                            <?php endif; ?>
                            <?php if(get_field('photographer')): ?>
                                <div>Photography by <?php the_field('photographer'); ?></div>
                            <?php endif; ?>
                            <?php if(get_field('image_by')): ?>
                                <div>Image by <?php the_field('image_by'); ?></div>
                            <?php endif; ?>
                            <?php if(get_field('illustration_by')): ?>
                                <div>Illustration by <?php the_field('illustration_by'); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="wrapperContain">
                        <?php if(get_field('issue')): ?>
                            <h3 class="contentIssue textLabels-Left"><?php the_field('issue'); ?></h3>
                        <?php endif; ?>
                        <?php the_content(); ?>
                    </div>

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