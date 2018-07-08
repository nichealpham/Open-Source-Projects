<?php
global $wp_query;
$category = get_the_category();
$args = array(
    'post_type'				=> get_post_type(),
    'post_status'			=> 'publish',
    'posts_per_page' 		=> 6,
    'orderby' 				=> 'date',
    'order'                 => 'DESC',
    'post__not_in'          => array(get_the_ID()),
    'cat'                   => $category[0]->term_id,
);
$wp_query = new WP_Query( $args );
?>
<?php if (have_posts()) : ?>
    <div class="verticalSpacing-lg"></div>
    <div class="relatedArticle">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h2 class="titleRelatedArticle">Liked this? You’ll love these...</h2>
                <div class="relatedArticleSub">
                    <?php while(have_posts()): the_post();?>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 padding-space">
                            <div class="hrefThumbnails">
                                <a href="<?php the_permalink(); ?>" class="imageThumbnails">
                                    <figure class="imageThumbnail">
                                        <img class="img-responsive image-sm" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
                                    </figure>
                                    <p class="thumbnailDescription"><?php the_title(); ?></p>
                                </a>
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