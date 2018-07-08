<div class="hrefThumbnails">
    <a href="<?php the_permalink(); ?>" class="imageThumbnails">
        <?php if(has_post_thumbnail()): ?>
        <img class="img-responsive image-lg" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
        <?php endif; ?>
        <div class="boxContain contentThumbnails-<?php echo ucfirst(get_field('position')); ?>">
            <div class="innerBoxContain textLabels">
                <p><?php the_title(); ?></p>
                <?php $category = get_the_category();
                $parent_cat = get_category($category[0]->category_parent);
                while($parent_cat->category_parent){
                    $parent_cat = get_category($parent_cat->category_parent);
                } ?>
                <span class="tagName life"><?php echo $parent_cat->name; ?></span>
            </div>
        </div>
    </a>
    <div class="clearfix"></div>
</div>