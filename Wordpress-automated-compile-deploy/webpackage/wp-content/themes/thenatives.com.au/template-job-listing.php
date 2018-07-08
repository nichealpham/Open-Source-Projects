<?php
/*
 * Template Name: Job Listing
 */
?>

<?php get_header ();?>
<main id="jobListing-page" class="jobListing">
    <section class="container">
        <div class="subMeta">
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-6">
                    <a href="" class="tagCareers titleText">careers</a>
                    <span class="titleText">sydney</span>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                    <span class="titleText">14.03.2017</span>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <a class="linkEmployLogin" href="<?php echo bloginfo('url') ?>"><h4 class="titleGrey">EMPLOYER LOGIN</h4></a>
                </div>
            </div>
        </div>
        <div class="jobContent">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                    <h1 class="bigTitle titleName">Mimco</h1>
                    <h2 class="bigTitle titleJob">Marketing Coordinator</h2>
                    <img src="<?php echo THEME_IMAGES; ?>/gallery.png" alt="Marketing Coordinator">
                    <div class="titleText textFloat">
                        <div class="careersInfo">
                            <table style="border-spacing: 0;border-collapse: collapse;">
                                <?php if($company): ?>
                                    <tr>
                                        <td class="careersInfoTitle">company:</td>
                                        <td><?php echo $company[0]->name; ?></td>
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
                    <h4 class="titleText">Role Description</h4>
                    <div class="textContent"><p>We have an exciting opportunity for a Marketing Coordinator to join the Mimco Marketing team. Reporting to
                        the Marketing Campaign Manager, this position will provide key support in executing local area marketing
                        activities and planning customer facing events and will provide general support in the day to day management
                        of David Jones, Myer and South Africa partnerships.</p>
                    </div>
                    <h4 class="titleText">Key Responsibilities</h4>
                    <div class="listContent">
                        <ul>
                            <li>Writing all promotions & competition terms and conditions</li>
                            <li>Writing all store communication in relation to promotions</li>
                            <li>Assisting with the coordination of marketing competitions when relevant including prize fulfillment with
                                winners</li>
                            <li>Assisting Marketing Campaign Manager in the coordination of marketing activity for David Jones, Myer
                                and South Africa</li>
                            <li>Coordinating samples for David Jones and Myer with Styling Manager and Sample Coordinator</li>
                            <li>Coordinating all Mimcollective events each season with sign off from Marketing Campaign Manager</li>
                            <li>Building a comprehensive communication program for Top 20 stores within centres, ensuring that all
                                opportunities are maximised and Mimco is proactively seeking opportunities</li>
                        </ul>
                    </div>
                    <h4 class="titleText">About Us</h4>
                    <div class="textContent">
                        <p>MIMCO designs unique accessories collections.</p>

                        <p>We dream, explore and play with whatever gives us delight in the moment. Our environments are full of
                            precious personality-filled products and our in-store experience is all about you - the strong individuals who
                            are drawn to something different.</p>

                        <p>Country Road Group is an equal opportunity employer committed to providing a working environment that
                            embraces and values diversity and inclusion. If you have any support or access requirements, we encourage
                            you to advise us at time of application to assist you through the recruitment process.</p>

                        <p>Country Road Group prefers to manage all sourcing directly, please submit your applications to this role if you
                            are interested. Please note introductions via agency will not be accepted.</p>
                    </div>
                    <div class="listingApply applyLeft">
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
                    <div class="iconSocial">
                        <a href="<?php echo bloginfo('url') ?>">
                            <i class="fa fa-facebook"></i>
                        </a>
                        <a href="<?php echo bloginfo('url') ?>">
                            <i class="fa fa-twitter"></i>
                        </a>
                        <a href="<?php echo bloginfo('url') ?>">
                            <i class="fa fa-pinterest"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 colForm">
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
                    <div class="sideBarImage pull-right">
                        <img src="<?php echo THEME_IMAGES; ?>/half-page-x-600.png" alt="Neuw Nordic Stone">
                    </div>
                </div>
            </div>
        </div>
        <div class="listingItems relatedArticle">
            <h2 class="titleItem">Similar jobs you might like</h2>
            <div class="row">
                <div class="containerItems relatedArticleSub">
                    <div class="col-md-2 col-sm-4 col-xs-6 padding-space">
                        <div class="hrefThumbnails">
                            <a href="<?php echo bloginfo('url') ?>" class="imageThumbnails">
                                <img class="img-responsive image-sm" src="<?php echo THEME_IMAGES; ?>/image1.png" alt="Store Manager QVB">
                                <span class="tagName featured">FEATURED</span>
                                <h4 class="titleGrey">melbourne</h4>
                                <p>
                                    <span>Jo Mercer</span>
                                    <br>Store Manager QVB
                                </p>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-6 padding-space">
                        <div class="hrefThumbnails">
                            <a href="<?php echo bloginfo('url') ?>" class="imageThumbnails">
                                <img class="img-responsive image-sm" src="<?php echo THEME_IMAGES; ?>/image12x.jpg" alt="Finance Manager Company Accountant">
                                <span class="tagName featured">FEATURED</span>
                                <h4 class="titleGrey">sydney</h4>
                                <p>
                                    <span>Jac + Jack</span>
                                    <br>Finance Manager/ Company Accountant
                                </p>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-6 padding-space">
                        <div class="hrefThumbnails">
                            <a href="<?php echo bloginfo('url') ?>" class="imageThumbnails">
                                <img class="img-responsive image-sm" src="<?php echo THEME_IMAGES; ?>/image22x.jpg" alt="Garment Technician">
                                <h4 class="titleGrey">melbourne</h4>
                                <p>
                                    <span>Bardot</span>
                                    <br>Garment Technician
                                </p>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-6 padding-space">
                        <div class="hrefThumbnails">
                            <a href="<?php echo bloginfo('url') ?>" class="imageThumbnails">
                                <img class="img-responsive image-sm" src="<?php echo THEME_IMAGES; ?>/image-small.png" alt="Australia Flagship Store Manager">
                                <h4 class="titleGrey">melbourne</h4>
                                <p>
                                    <span>Spring Court</span>
                                    <br>Australia Flagship Store Manager
                                </p>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-6 padding-space">
                        <div class="hrefThumbnails">
                            <a href="<?php echo bloginfo('url') ?>" class="imageThumbnails">
                                <img class="img-responsive image-sm" src="<?php echo THEME_IMAGES; ?>/image32x.jpg" alt="Sales Assistant">
                                <h4 class="titleGrey">melbourne</h4>
                                <p>
                                    <span>Mon Purse</span>
                                    <br>Sales Assistant
                                </p>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-6 padding-space">
                        <div class="hrefThumbnails">
                            <a href="<?php echo bloginfo('url') ?>" class="imageThumbnails">
                                <img class="img-responsive image-sm" src="<?php echo THEME_IMAGES; ?>/image12x.jpg" alt="Graphic Design Digital Marketing Specialist">
                                <h4 class="titleGrey">melbourne</h4>
                                <p>
                                    <span>By Johnny</span>
                                    <br>Graphic Design / Digital Marketing Specialist
                                </p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
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
