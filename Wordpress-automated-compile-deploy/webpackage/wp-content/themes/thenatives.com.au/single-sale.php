<?php get_header(); ?>
<?php if(have_posts()): ?>
    <?php while(have_posts()): the_post();
        $usermail = get_the_author_meta('email');
        ?>
        <main id="articleA-Page" class="articleA">
            <section class="container">
                <div class="articleMeta">
                    <div class="row">
                        <div class="col-lg-7 col-md-8 col-sm-7 col-xs-7">
                            <a href="<?php echo get_post_type_archive_link('sale'); ?>" class="titleText postType">Sales</a>
                            <?php if(get_field('sale_city')):?>
                                <span class="titleText"><?php the_field('sale_city'); ?></span>
                            <?php endif;?>
                        </div>
                        <div class="col-lg-5 col-md-4 col-sm-5 col-xs-5">
                            <span class="titleText"><?php the_date('d.m.Y')?></span>
                        </div>
                    </div>
                </div>
                <div class="contentEvent">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 articleRight">
                            <h1 class="titleEvent"><?php the_title(); ?></h1>
                            <div class="imageEventList">
                                <div class="titleDate pull-left">
                                    <?php
                                    $sale_starting = date('d M',strtotime(get_field('sale_starting_day',$post->ID)));
                                    $sale_end = date('d M',strtotime(get_field('sale_end_day',$post->ID)));
                                    $date = date('d M');
                                    ?>
                                    <p>
                                        <?php if($date==$sale_starting && $date==$sale_end){?>ONLY NOW <?php } else { ?>
                                <?php if ($date == $sale_starting) { ?><p>NOW TIL</p><?php } elseif ($sale_end != $sale_starting) { ?>
                                    <p class="eventStarts"> <?php echo $sale_starting; ?> -</p>
                                <?php } ?>
                                <?php if ($date == $sale_end) { ?><p>TIL NOW</p><?php } else { ?>
                                    <p class="eventEnds"><?php echo $sale_end; ?></p>
                                <?php }
                                }
                                ?>
                                    </p>
                                </div>
                                <div class="imgBannerEvent">
                                    <img class="" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
                                </div>
                            </div>

                            <div class="titleEventText">
                                <h3>Clear your schedule.</h3>
                            </div>

                            <div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <div class="textBlock titleEventText">
                                        <?php the_content(); ?>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12">
                                    <div class="textBlockDetail">
                                        <h4>THE DETAILS</h4>
                                        <?php if(get_field('sale_brand_name')): ?>
                                            <p class="eventBrand"><?php the_field('sale_brand_name'); ?></p>
                                        <?php endif ;?>
                                        <?php if(get_field('sale_address_line_1') || get_field('sale_address_line_1') || get_field('sale_suburb') ||get_field('sale_state') ):
                                            $check = false;
                                            ?>
                                            <p class="eventAddress1">
                                                <?php
                                                if(get_field('sale_address_line_1')){
                                                    echo $check?", ":'';
                                                    if (!$check){
                                                        $check = true;
                                                    }
                                                    the_field('sale_address_line_1');
                                                }
                                                if(get_field('sale_address_line_2')){
                                                    echo $check?", ":'';
                                                    if (!$check){
                                                        $check = true;
                                                    }
                                                    the_field('sale_address_line_2');
                                                }
                                                if(get_field('sale_suburb')){
                                                    echo $check?", ":'';
                                                    if (!$check){
                                                        $check = true;
                                                    }
                                                    the_field('sale_suburb');
                                                }
                                                if(get_field('sale_city')){
                                                    echo $check?", ":'';
                                                    if (!$check){
                                                        $check = true;
                                                    }
                                                    the_field('sale_city');
                                                }
                                                if(get_field('sale_state')){
                                                    echo $check?", ":'';
                                                    if (!$check){
                                                        $check = true;
                                                    }
                                                    the_field('sale_state');
                                                }
                                                ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php
                                        $date_and_opening = get_field('date_and_opening');
                                        ?>
                                        <?php if ($date_and_opening ):?>
                                            <p class="detailsDay">
                                                <?php if($date_and_opening): ?>
                                                    <?php echo $date_and_opening ; ?>
                                                <?php endif; ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php
                                        $s_fb= get_field('sale_facebook_url');
                                        $s_url_1= get_field('sale_website_url_1');
                                        $s_url_2= get_field('sale_website_url_2');
                                        ?>
                                        <?php if($s_fb || $s_url_1 || $s_url_2): ?>
                                            <p class="salefb">
                                                <?php if($s_fb): ?>
                                                    <a href="<?php echo $s_fb; ?>">facebook sale</a><br>
                                                <?php endif; ?>
                                                <?php if($s_url_1): ?>
                                                    <a href="<?php echo $s_url_1; ?>"><?php echo explode('/',explode('//',$s_url_1)[1])[0]; ?></a><br>
                                                <?php endif; ?>
                                                <?php if($s_url_2): ?>
                                                    <a href="<?php echo $s_url_2; ?>"><?php echo explode('/',explode('//',$s_url_2)[1])[0]; ?></a>
                                                <?php endif; ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $ct= get_field('sale_city');
//                            $timezone= str_replace(' ','_',get_field('sale_time_zone'));
                            $organizer_email = $usermail;

                            $starting = get_field('sale_starting_day');
                            $end = get_field('sale_end_day');
                            $starttime = get_field('sale_start_time');
                            $endtime = get_field('sale_end_time');

                            $starting = date('Y-m-d',strtotime($starting)).' '.$starttime;
                            $end = date('Y-m-d',strtotime($end)).' '.$endtime;

                            ?>
                            <span class="addtocalendar atc-style-blue">
                                <span class="leftIcon"></span>
                                <span class="rightIcon"></span>
                                <var class="atc_event">
                                <var class="atc_date_start"><?php echo date('Y-m-d H:i:s',strtotime($starting)); ?></var>
                                <var class="atc_date_end"><?php echo date('Y-m-d H:i:s',strtotime($end)); ?></var>
                                <var class="atc_timezone">Australia/Melbourne</var>
                                <var class="atc_title"><?php the_title(); ?></var>
                                <var class="atc_description"><?php echo strip_tags(get_the_content()); ?></var>
                                <var class="atc_location"><?php echo get_field('sale_address_line_1') . " " . get_field('sale_address_line_2') . " " . get_field('sale_suburb') . " " . $ct . " " . get_field('sale_state')?></var>
                                <var class="atc_organizer">Fashion Journal</var>
                                <var class="atc_organizer_email"><?php echo $organizer_email ;?></var>
                                </var>
                            </span>

                            <!--<div class="btnEvent btnAddToCalendar pull-left">
                                <select>
                                    <option value="apple">apple</option>
                                    <option value="google">google</option>
                                    <option value="outlook">outlook</option>
                                    <option value="yahoo">yahoo</option>
                                </select>
                            </div>-->
                            <?php get_template_part('framework/include/sharebox'); ?>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-5 hidden-xs sidebarLef">
                            <div id="category-tabs" class="pull-right btnAddToCalendar">
                                <span class="addtocalendar atc-style-blue">
                                    <span class="leftIcon"></span>
                                    <span class="rightIcon"></span>
                                    <var class="atc_event">
                                    <var class="atc_date_start"><?php echo date('Y-m-d H:i:s',strtotime($starting)); ?></var>
                                    <var class="atc_date_end"><?php echo date('Y-m-d H:i:s',strtotime($end)); ?></var>
                                    <var class="atc_timezone">Australia/Melbourne</var>
                                    <var class="atc_title"><?php the_title(); ?></var>
                                    <var class="atc_description"><?php echo strip_tags(get_the_content()); ?></var>
                                    <var class="atc_location"><?php echo get_field('sale_address_line_1') . " " . get_field('sale_address_line_2') . " " . get_field('sale_suburb') . " " . $ct[0]->name . " " . get_field('sale_state')?></var>
                                    <var class="atc_organizer">Fashion Journal</var>
                                    <var class="atc_organizer_email"><?php echo $organizer_email ;?></var>
                                    </var>
                                </span>

                                <!--<select>
                                    <option value="apple">apple</option>
                                    <option value="google">google</option>
                                    <option value="outlook">outlook</option>
                                    <option value="yahoo">yahoo</option>
                                </select>-->
                            </div>
                            <?php
                            if(get_field('advertise_right')){
                                $banner_right = get_field('advertise_right');
                            }
                            ?>
                            <?php if($banner_right): ?>
                                <div class="sideBarImage pull-right">
                                    <img class="img-responsive" src="<?php the_field('image',$banner_right->ID); ?>"
                                         alt="<?php echo get_the_title($banner_right->ID); ?>">
                                </div>
                                <div class="clearfix"></div>
                            <?php endif; ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

                <?php if(check_related_sale()) : ?>
                    <?php get_template_part('framework/include/related-sale'); ?>
                    <?php if(total_related_sale() - check_related_sale() > 0): ?>
                        <div id="lazyRelatedSale" class="lazyLoad" data-time="1" data-offset="8" data-tax="" data-post-in="<?php the_ID(); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </section>
        </main>
    <?php endwhile; ?>
<?php endif; ?>
<?php
if(get_field('footer_') == 'random') {
    $field = get_field_object('footer_');
    $index = rand(0,2);
    $i = 0;
    foreach ($field['choices'] as $key=>$option) {
        if ($i == $index) {
            $footer_style = $key;
            break;
        }
        $i++;
    }
}
else {
    $footer_style = get_field('footer_');
}
get_footer($footer_style);
?>
