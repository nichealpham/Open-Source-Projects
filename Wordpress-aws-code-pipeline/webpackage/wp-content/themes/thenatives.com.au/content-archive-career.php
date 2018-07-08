<?php
$package = wp_get_post_terms( get_the_ID(), 'career-packages' );
$package = $package[0];
?>
<div class="hrefThumbnails">
    <a href="<?php the_permalink(); ?>" class="imageThumbnails">
        <figure class="imageThumbnail">
            <img class="img-responsive image-md" src="<?php echo (is_front_page())?get_the_post_thumbnail_url('','small'):get_the_post_thumbnail_url('','medium'); ?>" alt="<?php the_title(); ?>">
        </figure>
        <div class="boxContain contentThumbnails-Bottom boxHover">
            <p>APPLY NOW</p>
        </div>
        <?php if(get_field('days','career-packages_'.$package->term_id)): ?>
            <span class="tagName featured">Featured</span>
        <?php endif; ?>
    </a>
    <?php  $ct = get_field('city_career'); ?>
        <h4 class="thumbnailLocal">
            <?php if($ct): ?><?php echo $ct; ?><?php endif; ?>
        </h4>
    <?php $company = get_field('companies_career'); ?>
        <p class="nameModel">
            <?php if($company): ?><?php echo $company; ?><?php endif; ?>
        </p>
    <p class="thumbnailName"><?php the_title(); ?></p>
</div>