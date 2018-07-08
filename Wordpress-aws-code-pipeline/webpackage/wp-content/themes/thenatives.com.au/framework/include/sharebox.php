<div class="social-share iconSocial">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <a href="http://linkedin.com/shareArticle?mini=true&amp;url=<?php the_permalink();?>&amp;title=<?php the_title();?>" target="_blank" class="share-linkedin" title="<?php _e( 'Share via', 'thenatives' ) ?> <?php _e( 'LinkedIn', 'thenatives' ) ?>"><i class="fa fa-linkedin"></i></a>
            <a href="http://www.facebook.com/sharer.php?u=<?php the_permalink();?>&amp;t=<?php the_title(); ?>" class="share-facebook" target="_blank" title="<?php _e( 'Share via', 'thenatives' ) ?> <?php _e( 'Facebook', 'thenatives' ) ?>"><i class="fa fa-facebook"></i></a>
            <a href="http://twitter.com/home?status=<?php the_title(); ?> <?php the_permalink(); ?>" class="share-twitter" target="_blank" title="<?php _e( 'Share via', 'thenatives' ) ?> <?php _e( 'Twitter', 'thenatives' ) ?>"><i class="fa fa-twitter"></i></a>
            <a href="http://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&amp;media=<?php $url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); echo esc_url($url); ?>&amp;" target="_blank" class="share-pinterest" title="<?php _e( 'Share via', 'thenatives' ) ?> <?php _e( 'Pinterest', 'thenatives' ) ?>"><i class="fa fa-pinterest"></i></a>
            <a href="mailto:?subject=<?php the_title();?>&amp;body=<?php the_permalink() ?>" class="share-mail" title="<?php _e( 'Share via', 'thenatives' ) ?> <?php _e( 'E-Mail', 'thenatives' ) ?>"><i class="fa fa-envelope"></i></a>
        </div>
    </div>
</div>