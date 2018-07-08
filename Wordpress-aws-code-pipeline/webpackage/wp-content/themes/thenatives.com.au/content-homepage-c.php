<div class="bigPostChoose">
    <?php
    if( !get_field('banner_post') ){
        while(the_repeater_field('custom_banner_post')) {
            $title = get_sub_field('title_banner_');
            $link = get_sub_field('link_banner_');
            $date = get_sub_field('post_day_');
        }
    }
    else {
        if(get_field('select_banner_post')){
            $cpost = get_field('select_banner_post');
            $title = get_the_title($cpost);
            $link = get_permalink($cpost);
            $date = get_the_date('d.m.Y',$cpost);
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
                    $link = get_permalink();
                    $date = get_the_date('d.m.Y');
                }
            }
            wp_reset_postdata();
        }
    }
    ?>

    <div class="container">
        <div class="row">
            <h1 class="titleBigPostChoose col-lg-12 col-md-12 col-sm-12 col-xs-12"><a href="<?php echo $link; ?>"><?php echo $title; ?></a></h1>
            <h2 class="timeBigPostChoose col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <span><?php echo $date; ?></span>
            </h2>
        </div>
    </div>
</div>

<?php do_action('thenatives_banner_top');?>

