<div class="hrefThumbnails">
    <a href="<?php the_permalink(); ?>" class="imageThumbnails">
        <?php if(has_post_thumbnail()): ?>
            <figure class="imageThumbnail">
                <img class="img-responsive image-md" src="<?php echo get_the_post_thumbnail_url('','medium'); ?>" alt="<?php the_title(); ?>">
            </figure>
        <?php endif; ?>
        <div class="boxContain contentThumbnails-<?php echo (get_field('position'))?ucfirst(get_field('position')):'Top'; ?>">
            <div class="innerBoxContain textBottom containerText">
                <p class="size-other"<?php echo get_field('font_post')?(' style="font-family: '.str_replace('-Black','-Regular',get_field('font_post'))):'' ?>"><?php the_title(); ?></p>
            </div>
        </div>
    </a>
    <div class="clearfix"></div>
</div>