<div class="hrefThumbnails">
    <a href="<?php the_permalink(); ?>" class="imageThumbnails">
        <figure class="imageThumbnail">
            <img class="img-responsive image-md" src="<?php echo (is_front_page())?get_the_post_thumbnail_url('','small'):get_the_post_thumbnail_url('','medium'); ?>" alt="<?php the_title(); ?>">
        </figure>
        <?php if(get_field('starting_day',$post->ID) && get_field('end_day',$post->ID)): ?>
            <div class="boxContain contentThumbnails-Bottom">
                <div class="innerBoxContain beforeHover">
                    <?php
                    $starting = date('M d',strtotime(get_field('starting_day',$post->ID)));
                    $end = date('M d',strtotime(get_field('end_day',$post->ID)));
                    $date = date('M d');
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
    <?php $ct = get_field('event_city'); ?>
    <h6 class="thumbnailLocal">
        <?php if($ct): ?><?php echo $ct; ?><?php endif; ?>
    </h6>
    <p class="thumbnailName"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
    <div class="clearfix"></div>
</div>
