<?php
/*
 * Template Name: Post A Purchase
 */
?>
<?php
    $id = isset($_GET['id'])?$_GET['id']:'';
    if(!$id){
        wp_redirect(get_user_dashboard_link());
    }
    $args = array(
        'post_type' => array('career','sale','event'),
        'p' => $id,
        'author' => get_current_user_id(),
    );
    $the_query = new WP_Query($args);
    if($the_query->have_posts()){
        while ($the_query->have_posts()) {
            $the_query->the_post();
            if(get_post_status()=='publish') {
                wp_redirect(get_user_dashboard_link());
            }
        }
    }
    else {
        wp_redirect(get_user_dashboard_link());
    }
    if(get_post_type() == 'career') {
        $packages = get_terms( array(
            'taxonomy' => 'career-packages',
            'hide_empty' => false,
        ));
    }
    elseif(get_post_type() == 'event') {
        $packages = get_terms( array(
            'taxonomy' => 'event-packages',
            'hide_empty' => false,
        ));
    }
    else {
        $packages = get_terms( array(
            'taxonomy' => 'sale-packages',
            'hide_empty' => false,
        ));
    }
?>
<?php get_header();?>
<section>
    <div class="postPurchase userForm <?php echo get_post_type(); ?>">
        <div class="container">
            <div class="titleSale">
                <a href="<?php the_user_dashboard_link(); ?>">User</a> <a class="createJob" href="<?php the_user_dashboard_link(); ?>/post-a-job">create job</a>  <span>purchase</span>
            </div>
            <div class="wrapter postPurchase">
                <ul class="nav nav-tabs">
                    <?php foreach ($packages as $key=>$package): ?>
                        <?php
                        $price = '';
                        if(get_field('price',get_post_type().'-packages_'.$package->term_id)){
                            $price.= '$'.get_field('price',get_post_type().'-packages_'.$package->term_id);
                            if(get_field('gst',get_post_type().'-packages_'.$package->term_id)) {
                                $price.= ' + GST';
                            }
                        }
                        ?>
                        <li data-type="<?php echo $package->term_id; ?>"<?php if(!$key) echo ' class="active"'; ?>><a data-toggle="tab" href="#<?php echo $package->slug?>"><?php echo $package->name; ?><?php if($price) echo '<span>'.$price.'</span>'; ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <div class="tab-content">
                    <?php foreach ($packages as $key=>$package):?>
                        <div id="<?php echo $package->slug?>" class="tab-pane fade<?php if(!$key) echo ' active in'; ?>">
                            <div class="row contentPurchase">
                                <?php if($package->description): ?>
                                <div class="col-sm-5">
                                    <ul class="detail">
                                        <?php foreach (explode(PHP_EOL,$package->description) as $item): ?>
                                            <li><?php echo $item; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php else: ?>
                                <div class="col-sm-5" style="visibility: hidden;"></div>
                                <?php endif; ?>
                                <div class="col-sm-3">
                                    <?php if(get_post_type() == 'career') : ?>
                                        <div class="hrefThumbnails">
                                            <a href="<?php the_permalink(); ?>" class="imageThumbnails">
                                                <img class="img-responsive image-md" src="<?php echo get_the_post_thumbnail_url('','medium'); ?>" alt="<?php the_title(); ?>">
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
                                    <?php else : ?>
                                        <div class="Event">
                                            <div class="row" style="margin: 0;">
                                                <?php get_template_part('content-archive',get_post_type()); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="row ">
                        <div class="bgButton bgButtonPurchase">
                            <div class="col-xs-6 colPrev">
                                <div class="buttonPrev">
                                    <img class="controlPrev" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow.png">
                                    <a href="<?php the_post_job_link(); ?>/?id=<?php the_ID(); ?>">BACK</a>
                                </div>
                            </div>
                            <div class="col-xs-6 colNext">
                                <div class="buttonNext btnNextPurchase">
                                    <a href="<?php bloginfo('url');?>/contact">CONTINUE</a>
                                    <img class="controlNext" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow.png">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mainControl">
                        <form action="" id="purChaseCareer" method="POST">
                            <input type="submit" name="user-submit" value="Save" class="user-submit" tabindex="103" style="display: none;">
                            <input type="hidden" name="career_type" id="career_type" value="<?php echo $packages[0]->term_id; ?>" />
                            <input type="hidden" name="post_id" id="post_id" value="<?php echo $_GET['id']; ?>" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php get_footer('page'); ?>