<div class="hrefThumbnails">
    <a href="<?php the_permalink(); ?>" class="imageThumbnails">
        <?php if(has_post_thumbnail()): ?>
            <figure class="imageThumbnail">
                <img class="img-responsive image-lg" src="<?php echo get_the_post_thumbnail_url('','large'); ?>" alt="<?php the_title(); ?>">
            </figure>
        <?php endif; ?>
        <div class="boxContain contentThumbnails-<?php echo (ucfirst(get_field('position')))?ucfirst(get_field('position')):'Bottom'; ?>">
            <div class="innerBoxContain textLabels">
                <p><?php the_title(); ?></p>
                <?php if (get_field('sponsored_post',$post_ID)): ?>
                    <span class="tagName sponsored">sponsored</span>
                <?php endif; ?>
            </div>
        </div>
    </a>
    <div class="clearfix"></div>
</div>