<div class="contentVertical homePageB">
    <?php
    $open = get_field('option_open');
    if( !get_field('featured_section_b') ){
        while(the_repeater_field('custom_feature')) {
            $title = get_sub_field('title_feature_c');
            $link = get_sub_field('link_feature_c');
            $img = get_sub_field('image_feature_c');
            $cat = get_sub_field('category_feature-c');
        }
    }else {
        if(get_field('select_feature')){
            $spost = get_field('select_feature');
            $category = get_the_category($spost);
            $cat = get_category($category[0]->category_parent);
            while($cat->category_parent){
                $cat = get_category($cat->category_parent);
            }
            $title = get_the_title($spost);
            $img = get_the_post_thumbnail_url(get_field('select_feature'));
            $link = get_permalink($spost);
        }
        else {
            $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => 1,
                'orderby' => 'date',
                'order' => 'DESC',
            );
            $the_query = new WP_Query($args);
            if($the_query->have_posts()){
                while($the_query->have_posts()){
                    $the_query->the_post();
                    $title = get_the_title();
                    $img = get_the_post_thumbnail_url();
                    $link = get_permalink();
                    $category = get_the_category();
                    if($category[0]->category_parent) {
                        $cat = get_category($category[0]->category_parent);
                        while ($cat->category_parent) {
                            $cat = get_category($cat->category_parent);
                        }
                    }
                    else{
                        $cat = $category[0];
                    }
                }
            }
            wp_reset_postdata();
        }
    }
    ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="hrefThumbnails">
                    <a target="<?php echo $open; ?>" href="<?php echo $link; ?>" class="imageThumbnails">
                            <img class="img-responsive image-lg" src="<?php echo $img;?>" alt="<?php echo $title; ?>">
                        <div class="boxContain contentThumbnails-Top">
                            <div class="innerBoxContain textLabels">
                                <p><?php echo $title; ?></p>
                                <?php if( !get_field('featured_section_b') ){?>
                                    <span class="tagName life"><?php echo $cat; ?></span>
                                <?php } else { ?>
                                    <?php if(get_field('select_feature')){?>
                                        <span class="tagName life"><?php echo $cat->name; ?></span>
                                    <?php } else {?>
                                        <span class="tagName life"><?php echo $cat->name; ?></span>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </a>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
