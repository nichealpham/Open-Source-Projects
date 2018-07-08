<?php get_header ();?>
<?php if(have_posts()): ?>
    <?php while(have_posts()): the_post() ?>
        <main id="jobListing-page" class="jobListing">
            <section class="container">
                <div class="subMeta">
                    <div class="row">
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                            <a href="<?php echo get_post_type_archive_link('career'); ?>" class="postType titleText">careers</a>
                            <?php $city = get_field('city_career'); ?>
                            <?php if($city): ?>
                                <span class="titleText"><?php echo $city; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-5">
                            <span class="titleText"><?php the_date('d.m.Y')?></span>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <?php if(!is_user_logged_in()): ?>
                                <a class="linkEmployLogin" href="<?php the_login_link(); ?>">
                                    <h4 class="titleGrey">EMPLOYER LOGIN</h4>
                                </a>
                            <?php else: ?>
                                <!--<a class="linkLoginUser" href="<?php /*the_user_dashboard_link(); */?>">
                                    <div class="iconUser">
                                        <div class="head-icon"></div>
                                        <div class="body-icon"></div>
                                    </div>
                                </a>-->
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="jobContent">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 articleRight">
                            <?php $company = get_field('companies_career'); ?>
                            <?php if($company): ?>
                                <h1 class="bigTitle titleName"><?php echo $company; ?></h1>
                            <?php endif; ?>
                            <h2 class="bigTitle titleJob"><?php the_title(); ?></h2>
                            <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
                            <div class="titleText textFloat">
                                <div class="careersInfo">
                                    <table style="border-spacing: 0;border-collapse: collapse;">
                                        <?php if($company): ?>
                                            <tr>
                                                <td class="careersInfoTitle">company:</td>
                                                <td><?php echo $company; ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td class="careersInfoTitle">job title:</td>
                                            <td><?php the_title(); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="careersInfoTitle">location:</td>
                                            <td>fitzroy, vic</td>
                                        </tr>
                                        <?php $type = get_the_terms(get_the_ID(),'career-types'); ?>
                                        <?php if($type): ?>
                                            <tr>
                                                <td class="careersInfoTitle">work type:</td>
                                                <td><?php echo $type[0]->name; ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php $level = get_the_terms(get_the_ID(),'career-levels'); ?>
                                        <?php if($level): ?>
                                            <tr>
                                                <td class="careersInfoTitle">level:</td>
                                                <td><?php echo $level[0]->name; ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if(get_field('closing')): ?>
                                            <tr>
                                                <td class="careersInfoTitle">closing:</td>
                                                <td><?php the_field('closing'); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                            <?php if(get_field('role_description')): ?>
                            <h4 class="titleText">Role Description</h4>
                            <div class="textContent">

                                    <?php the_field('role_description'); ?>

                            </div>
                            <?php endif ;?>
                            <?php if(get_field('key_responsibilities')): ?>
                            <h4 class="titleText">Key Responsibilities</h4>
                            <div class="listContent">

                                    <?php the_field('key_responsibilities'); ?>

                            </div>
                            <?php endif ;?>
                            <?php if(get_the_content()): ?>
                            <h4 class="titleText">About Us</h4>
                            <div class="textContent">
                                <?php the_content();?>
                            </div>
                            <?php endif; ?>
                            <div class="listingApply applyLeft">
                                <div class="linkApply">
                                    <div class="btnApply pull-left">
                                        <div class="textApply">APPLY NOW</div>
                                    </div>
                                    <div class="strokeApply pull-right"></div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="formApply">
                                    <?php echo do_shortcode('[gravityform id=4 title=false description=false ajax=true tabindex=49]') ?>
                                </div>
                            </div>
                            <?php get_template_part('framework/include/sharebox'); ?>

                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 sidebarLef colForm">
                            <div class="listingApply applyRight pull-right formSideBar">
                                <div class="linkApply">
                                    <div class="btnApply pull-left">
                                        <div class="textApply">APPLY NOW</div>
                                    </div>
                                    <div class="strokeApply pull-right"></div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="formApply">
                                    <?php echo do_shortcode('[gravityform id=3 title=false description=false ajax=true tabindex=49]') ?>
                                </div>
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
                        </div>
                    </div>
                </div>

                <?php if(check_related_career()) : ?>
                    <?php get_template_part('framework/include/related-career'); ?>
                    <?php if(total_related_career() - check_related_career() > 0): ?>
                        <div id="lazyRelatedCareer" class="lazyLoad" data-time="1" data-offset="8" data-tax="" data-post-in="<?php the_ID(); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            </section>
        </main>
        <?php endwhile; ?>
    <?php endif; ?>
<?php get_footer(); ?>