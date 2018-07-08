<?php get_header(); ?>
<?php
//$user = get_currentuserinfo();
//$userdata = get_userdata($user->ID);
//$usermail = $userdata->user_email ;
//?>
<?php if (have_posts()): ?>
    <?php while (have_posts()): the_post();
        $usermail = get_the_author_meta('email');
        ?>
        <main id="articleA-Page" class="articleA">
            <section class="container">
                <div class="articleMeta">
                    <div class="row">
                        <div class="col-lg-7 col-md-8 col-sm-7 col-xs-7">
                            <a href="<?php echo get_post_type_archive_link('event'); ?>" class="titleText postType">Events</a>
                            <?php if(get_field('event_city')):?>
                                <span class="titleText"><?php the_field('event_city'); ?></span>
                            <?php endif;?>
                        </div>
                        <div class="col-lg-5 col-md-4 col-sm-5 col-xs-5">
                            <span class="titleText"><?php the_date('d.m.Y') ?></span>
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
                                    $starting = date('M d', strtotime(get_field('starting_day')));
                                    $end = date('M d', strtotime(get_field('end_day')));
                                    $date = date('M d');
                                    ?>
                                    <p>
                                        <?php if ($date == $starting && $date == $end){ ?>ONLY NOW <?php } else { ?>
                                <?php if ($date == $starting) { ?><p>NOW TIL</p>p<?php } elseif ($end != $starting) { ?>
                                    <p class="eventStarts"> <?php echo $starting; ?> -</p>
                                <?php } ?>
                                <?php if ($date == $end) { ?><p>TIL NOW</p><?php } else { ?>
                                    <p class="eventEnds"><?php echo $end; ?></p>
                                <?php }
                                }
                                ?>
                                    </p>
                                </div>
                                <div class="imgBannerEvent">
                                    <img class="" src="<?php echo get_the_post_thumbnail_url(); ?>"
                                         alt="<?php the_title(); ?>">
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
                                        <?php if (get_field('event_brand_name')): ?>
                                            <p class="eventBrand"><?php the_field('event_brand_name'); ?></p>
                                        <?php endif; ?>
                                        <?php if (get_field('event_address_line_1') || get_field('event_address_line_1') || get_field('event_suburb') || get_field('event_state')):
                                            $check = false;
                                            ?>
                                            <p class="eventAddress1">
                                                <?php
                                                if (get_field('event_address_line_1')) {
                                                    echo $check ? ", " : '';
                                                    if (!$check) {
                                                        $check = true;
                                                    }
                                                    the_field('event_address_line_1');
                                                }
                                                if (get_field('event_address_line_2')) {
                                                    echo $check ? ", " : '';
                                                    if (!$check) {
                                                        $check = true;
                                                    }
                                                    the_field('event_address_line_2');
                                                }
                                                if (get_field('event_suburb')) {
                                                    echo $check ? ", " : '';
                                                    if (!$check) {
                                                        $check = true;
                                                    }
                                                    the_field('event_suburb');
                                                }
                                                if (get_field('event_city')) {
                                                    echo $check ? ", " : '';
                                                    if (!$check) {
                                                        $check = true;
                                                    }
                                                    the_field('sale_city');
                                                }
                                                if (get_field('event_state')) {
                                                    echo $check ? ", " : '';
                                                    if (!$check) {
                                                        $check = true;
                                                    }
                                                    the_field('event_state');
                                                }
                                                ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php
                                        $starting = date('M d', strtotime(get_field('starting_day')));
                                        $end = date('M d', strtotime(get_field('end_day')));
                                        $starttime = get_field('event_start_time');
                                        $endtime = get_field('end_time');
                                        ?>
                                        <?php if ($starting || $end || $starttime || $endtime):
                                            $check = false; ?>
                                            <p class="detailsDay">
                                                <?php if ($starting) {
                                                    echo $check ? ", " : '';
                                                    if (!$check) {
                                                        $check = true;
                                                    }
                                                    echo $starting;
                                                }
                                                ?>
                                                <?php if ($starttime) {
                                                    echo $check ? ", " : '';
                                                    if (!$check) {
                                                        $check = true;
                                                    }
                                                    echo $starttime;
                                                }
                                                ?><br>
                                                <?php if ($end) {
                                                    echo $end;
                                                }
                                                ?>
                                                <?php if ($endtime) {
                                                    echo $check ? ", " : '';
                                                    if (!$check) {
                                                        $check = true;
                                                    }
                                                    echo $endtime;
                                                }
                                                ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php
                                        $e_fb = get_field('event_facebook_url');
                                        $e_url_1 = get_field('event_website_url_1');
                                        $e_url_2 = get_field('event_website_url_2');
                                        ?>
                                        <?php if ($e_fb || $e_url_1 || $e_url_2): ?>
                                            <p class="eventfb">
                                                <?php if ($e_fb): ?>
                                                    <a href="<?php echo $e_fb; ?>">facebook event</a><br>
                                                <?php endif; ?>
                                                <?php if ($e_url_1): ?>
                                                    <a href="<?php echo $e_url_1; ?>"><?php echo explode('/', explode('//', $e_url_1)[1])[0]; ?></a>
                                                    <br>
                                                <?php endif; ?>
                                                <?php if ($e_url_2): ?>
                                                    <a href="<?php echo $e_url_2; ?>"><?php echo explode('/', explode('//', $e_url_2)[1])[0]; ?></a>
                                                <?php endif; ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if (get_field('event_location')): ?>
                                            <p class="eventLocation"><?php the_field('event_location'); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $ct = get_field('event_city');
                            //                            $timezone= str_replace(' ','_',get_field('time_zone'));
                            $organizer_email = $usermail;
                            $starting = date('Y-m-d', strtotime($starting)) . ' ' . $starttime;
                            $end = date('Y-m-d', strtotime($end)) . ' ' . $endtime;
                            ?>
                            <span class="addtocalendar atc-style-blue">
                                <span class="leftIcon"></span>
                                <span class="rightIcon"></span>
                                <var class="atc_event">
                                    <var class="atc_date_start"><?php echo date('Y-m-d H:i:s', strtotime($starting)); ?></var>
                                    <var class="atc_date_end"><?php echo date('Y-m-d H:i:s', strtotime($end)); ?></var>
                                    <var class="atc_timezone">Australia/Melbourne</var>
                                    <var class="atc_title"><?php the_title(); ?></var>
                                    <var class="atc_description"><?php the_content(); ?></var>
                                    <var class="atc_location"><?php echo get_field('event_address_line_1') . " " . get_field('event_address_line_2') . " " . get_field('event_suburb') . " " . $ct . " " . get_field('event_state') ?></var>
                                    <var class="atc_organizer">Fashion Journal</var>
                                    <var class="atc_organizer_email"><?php echo $organizer_email; ?></var>
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
                                    <var class="atc_date_start"><?php echo date('Y-m-d H:i:s', strtotime($starting)); ?></var>
                                    <var class="atc_date_end"><?php echo date('Y-m-d H:i:s', strtotime($end)); ?></var>
                                    <var class="atc_timezone">Australia/Melbourne</var>
                                    <var class="atc_title"><?php the_title(); ?></var>
                                    <var class="atc_description"><?php the_content(); ?></var>
                                    <var class="atc_location"><?php echo get_field('event_address_line_1') . " " . get_field('event_address_line_2') . " " . get_field('event_suburb') . " " . $ct . " " . get_field('event_state') ?></var>
                                    <var class="atc_organizer">Fashion Journal</var>
                                    <var class="atc_organizer_email"><?php echo $organizer_email; ?></var>
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

                <?php if (check_related_event()) : ?>
                    <?php get_template_part('framework/include/related-event'); ?>
                    <?php if (total_related_event() - check_related_event() > 0): ?>
                        <div id="lazyRelatedEvent" class="lazyLoad" data-time="1" data-offset="8" data-tax=""
                             data-post-in="<?php the_ID(); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif"
                                 alt="Lazy Loading">
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </section>
        </main>
    <?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>
