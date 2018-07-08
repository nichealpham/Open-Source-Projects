<div class="contentVertical">
    <div class="container">
        <div class="row imgPosts">
            <?php $count = 3; if(have_rows('featured_section')): ?>
                <?php while(have_rows('featured_section')) : the_row(); ?>
                    <?php if(get_sub_field('post')) : $post_ID = get_sub_field('post'); $count--; ?>
                        <div class="colVer">
                            <div class="hrefThumbnails">
                                <a href="<?php echo get_the_permalink($post_ID); ?>" class="imageThumbnails">
                                    <?php if(has_post_thumbnail($post_ID)): ?>
                                        <figure class="imageThumbnail">
                                            <img class="img-responsive image-lg" src="<?php echo get_the_post_thumbnail_url($post_ID,'large'); ?>" alt="<?php echo get_the_title($post_ID); ?>">
                                        </figure>
                                    <?php endif; ?>
                                    <div class="boxContain contentThumbnails-<?php echo get_sub_field('title_position'); ?>">
                                        <div class="innerBoxContain textLabels">
                                            <p<?php echo get_sub_field('font')?(' style="font-family: '.get_sub_field('font').', sans-serif;"'):'' ?>><?php echo get_the_title($post_ID); ?></p>
                                            <?php
                                            $category = get_the_category($post_ID);
                                            $parent_cat = get_category($category[0]->category_parent)?get_category($category[0]->category_parent):$category[0];
                                            while($parent_cat->category_parent){
                                                $parent_cat = get_category($parent_cat->category_parent);
                                            }
                                            ?>
                                            <span class="tagName sponsored featured-section"><?php echo $parent_cat->name; ?></span>
                                        </div>
                                    </div>
                                </a>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            <?php endif; ?>
            <?php if($count > 0): ?>
                <?php
                $args = array(
                    'post_type'				=> 'post',
                    'post_status'			=> 'publish',
                    'posts_per_page' 		=> $count,
                    'orderby' 				=> 'date',
                    'order' 				=> 'DESC',
                );
                $the_query = new WP_Query( $args );
                if($the_query->have_posts()){
                    for($i = 0; $i < count($the_query->posts); $i++): $post_ID=$the_query->posts[$i];
                        ?>
                        <div class="colVer">
                            <div class="hrefThumbnails">
                                <a href="<?php echo get_the_permalink($post_ID); ?>" class="imageThumbnails">
                                    <?php if(has_post_thumbnail($post_ID)): ?>
                                        <figure class="imageThumbnail">
                                            <img class="img-responsive image-lg" src="<?php echo get_the_post_thumbnail_url($post_ID,'full'); ?>" alt="<?php echo get_the_title($post_ID); ?>">
                                        </figure>
                                    <?php endif; ?>
                                    <div class="boxContain contentThumbnails-<?php echo (get_field('position',$post_ID))?ucfirst(get_field('position',$post_ID)):'Top'; ?>">
                                        <div class="innerBoxContain textLabels">
                                            <p<?php echo get_sub_field('font')?(' style="font-family: '.get_sub_field('font').', sans-serif;"'):'' ?>><?php echo get_the_title($post_ID); ?></p>
                                            <?php
                                            if (get_field('sponsored_post',$post_ID)){
                                                $tag = 'sponsored';
                                            }else {
                                                $category = get_the_category($post_ID);
                                                $parent_cat = $category[0]->category_parent?get_category(get_category($category[0]->category_parent)):$category[0];
                                                while($parent_cat->category_parent){
                                                    $parent_cat = get_category($parent_cat->category_parent);
                                                }
                                                $tag = $parent_cat->name;
                                            }
                                            ?>
                                            <span class="tagName sponsored featured-section"><?php echo $tag; ?></span>
                                        </div>
                                    </div>
                                </a>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?php
                        $count--;
                    endfor;
                }
                ?>
            <?php endif; ?>
        </div>
    </div>
</div>