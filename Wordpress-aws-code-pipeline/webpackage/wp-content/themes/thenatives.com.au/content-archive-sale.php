<div class="hrefThumbnails">
    <a href="<?php the_permalink(); ?>" class="imageThumbnails">
        <figure class="imageThumbnail">
            <img class="img-responsive image-md" src="<?php echo (is_front_page())?get_the_post_thumbnail_url('','small'):get_the_post_thumbnail_url('','medium'); ?>" alt="<?php the_title(); ?>">
        </figure>
        <?php if(get_field('sale_starting_day',$post->ID) && get_field('sale_end_day',$post->ID)): ?>
        <div class="boxContain contentThumbnails-Bottom">
            <div class="innerBoxContain beforeHover">
                <?php
                $sale_starting = date('d M', strtotime(get_field('sale_starting_day',$post->ID)));
                $sale_end = date('d M', strtotime(get_field('sale_end_day',$post->ID)));
                $date = date('d M');
                ?>
                <p>
                    <?php if($date==$sale_starting && $date==$sale_end){?>ONLY NOW <?php } else { ?>
                        <?php if ($date == $sale_starting) { ?>NOW TIL<?php } elseif ($sale_end != $sale_starting) { ?>
                            <?php echo $sale_starting; ?>
                        <?php } ?>
                        <br/>
                        <?php if ($date == $sale_end) { ?>TIL NOW<?php } else { ?>
                            <?php echo $sale_end; ?>
                        <?php }
                    }
                    ?>
                </p>
            </div>
            <div class="innerBoxContain afterHover">
                <p>add to calendar</p>
                <span class="tagName">sales</span>
            </div>
        </div>
        <?php endif; ?>
    </a>
    <?php $ct = get_field('sale_city'); ?>
        <h6 class="thumbnailLocal">
            <?php if($ct): ?><?php echo $ct; ?><?php endif; ?>
        </h6>
    <p class="thumbnailName"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
    <div class="clearfix"></div>
</div>
