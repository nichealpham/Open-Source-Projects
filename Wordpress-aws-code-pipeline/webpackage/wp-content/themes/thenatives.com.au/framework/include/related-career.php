<?php
$p = get_post();
global $wp_query;
$args = array(
    'post_type'				=> get_post_type(),
    'post_status'			=> 'publish',
    'posts_per_page' 		=> 6,
    'orderby' 				=> 'date',
    'order'                 => 'DESC',
    'post__not_in'          => array(get_the_ID()),
    'author'                => $p->post_author,
);
$wp_query = new WP_Query( $args );
?>
<?php if (have_posts()) : ?>
    <div class="verticalSpacing-lg"></div>
    <div class="listingItems relatedArticle groupCareer">
        <h2 class="titleItem">Similar jobs you might like</h2>
        <div class="containerItems relatedArticleSub">
            <div class="row">
                <?php while(have_posts()): the_post();?>
                    <?php
                    $package = wp_get_post_terms( get_the_ID(), 'career-packages' );
                    $package = $package[0];
                    ?>
                    <div class="col-md-2 col-sm-4 col-xs-6 padding-space">
                        <div class="hrefThumbnails">
                            <a href="<?php the_permalink(); ?>" class="imageThumbnails">
                                <figure class="imageThumbnail">
                                    <img class="img-responsive image-sm" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
                                </figure>
                                <div class="boxContain contentThumbnails-Bottom boxHover">
                                    <p>APPLY NOW</p>
                                </div>
                                <?php if(get_field('days','career-packages_'.$package->term_id)): ?>
                                    <span class="tagName featured"><?php echo $package->name; ?></span>
                                <?php endif; ?>
                            </a>
                            <?php $city = get_the_terms(get_the_ID(),'career-cities'); ?>
                            <?php if($city): ?>
                                <h4 class="titleGrey"><?php echo $city[0]->name; ?></h4>
                            <?php endif; ?>
                            <p>
                                <?php $company = get_the_terms(get_the_ID(),'career-companies'); ?>
                                <?php if($company): ?>
                                    <span><?php echo $company[0]->name; ?></span>
                                    <br>
                                <?php endif; ?>
                                <?php the_title(); ?>
                            </p>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php wp_reset_query(); ?>