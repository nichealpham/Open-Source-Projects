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
    <div class="listingItems relatedArticle">
        <h2 class="titleItem">Similar event you might like</h2>
        <div class="containerItems Event relatedArticleSub">
            <div class="row">
                <?php while(have_posts()): the_post();?>
                    <div class="col-md-2 col-sm-4 col-xs-6 padding-space">
                        <div class="hrefThumbnails">
                            <a href="<?php the_permalink(); ?>" class="imageThumbnails">
                                <figure class="imageThumbnail">
                                    <img class="img-responsive image-sm" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
                                </figure>
                                <?php if(get_field('starting_day',$post->ID) && get_field('end_day',$post->ID)): ?>
                                    <div class="boxContain contentThumbnails-Bottom">
                                        <div class="innerBoxContain beforeHover">
                                            <?php
                                            $starting = date('d M', strtotime(get_field('starting_day',$post->ID)));
                                            $end = date('d M', strtotime(get_field('end_day',$post->ID)));
                                            $date = date('d M');
                                            ?>
                                            <p><?php if($date==$starting && $date==$end){?>ONLY NOW <?php } else { ?>
                                                    <?php if ($date == $starting) { ?>NOW TIL<?php } elseif ($end != $starting) { ?>
                                                        <?php echo $starting; ?>
                                                    <?php } ?>
                                                    <br/>
                                                    <?php if ($date == $end) { ?>TIL NOW<?php } else { ?>
                                                        <?php echo $end; ?>
                                                    <?php }
                                                }
                                                ?>
                                            </p>
                                        </div>
                                        <div class="innerBoxContain afterHover">
                                            <p>add to calendar</p>
                                            <span class="tagName">events</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </a>
                            <?php $ct = get_the_terms(get_the_ID(),'sale-cities'); ?>
                            <?php if($ct): ?>
                                <h6 class="thumbnailLocal"><?php echo $ct[0]->name; ?></h6>
                            <?php endif; ?>
                            <p class="thumbnailName"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php wp_reset_query(); ?>