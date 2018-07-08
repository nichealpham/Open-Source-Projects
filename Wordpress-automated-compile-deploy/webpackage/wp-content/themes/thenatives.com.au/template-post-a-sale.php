<?php
/*
 * Template Name: Post A Sale
 */ ?>
<?php
if(!is_user_logged_in()){
    $redirect = get_login_link()?get_login_link():site_url();
    wp_redirect($redirect);
    exit();
}
$user = get_currentuserinfo();
$userdata = get_userdata($user->ID);
if($_GET['id']){
    $args = array(
        'post_type' => 'sale',
        'p' => $_GET['id'],
        'author' => get_current_user_id(),
    );
    $the_query = new WP_Query($args);
    if($the_query->have_posts()){
        while ($the_query->have_posts()) {
            $the_query->the_post();
        }
    }
    else {
        wp_redirect(get_user_dashboard_link());
    }
    $title = 'edit sale';
}
else {
    $title = 'create sale';
}
?>
<?php get_header(); ?>
    <section class="container postSale">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="columnForm">
                    <div class="titleSale">
                        <a href="<?php the_user_dashboard_link(); ?>">User</a><span><?php echo $title ;?></span>
                    </div>
                    <form id="post-sale" class="formSale userForm" method="post">
                        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 saleLeft">
                            <div class="formContent">
                                <div class="groupControl groupForm">
                                    <input type="text" name="sale_brand_name" id="sale_brand_name" class="inputText" data-required="true" value="<?php if(isset($_GET['id']) && get_field('sale_brand_name',$_GET['id'])) echo get_field('sale_brand_name',$_GET['id']); ?>" placeholder="BRAND NAME">
                                </div>
                                <div class="groupControl groupForm">
                                    <input type="text" name="sale_name" id="sale_name" class="inputText" data-required="true" value="<?php if(isset($_GET['id'])) the_title(); ?>" placeholder="SALE NAME">
                                </div>
                                <div class="groupControl textForm"><h4>sale Location</h4></div>
                                <div class="groupControl groupForm line1">
                                    <input type="text" name="sale_address_line_1" id="sale_address_line_1" class="inputText" data-required="true" value="<?php if(isset($_GET['id']) && get_field('sale_address_line_1',$_GET['id'])) echo get_field('sale_address_line_1',$_GET['id']); ?>" placeholder="ADDRESS LINE 1">
                                </div>
                                <div class="groupControl groupForm line2">
                                    <input type="text" name="sale_address_line_2" id="sale_address_line_2" class="inputText"  value="<?php if(isset($_GET['id']) && get_field('sale_address_line_2',$_GET['id'])) echo get_field('sale_address_line_2',$_GET['id']); ?>" placeholder="ADDRESS LINE 2">
                                </div>
                                <div class="groupControl groupForm line1">
                                    <input type="text" name="sale_suburb" id="suburb" class="inputText" data-required="true" value="<?php if(isset($_GET['id']) && get_field('sale_suburb',$_GET['id'])) echo get_field('sale_suburb',$_GET['id']); ?>" placeholder="SUBURB">
                                </div>
                                <div class="groupControl groupForm line3">
                                    <input type="text" name="sale_city" id="sale_city" class="inputText" data-required="true" value="<?php if(isset($_GET['id']) && get_field('sale_city',$_GET['id'])) echo get_field('sale_city',$_GET['id']); ?>" placeholder="CITY">
                                </div>
                                <div class="groupControl groupForm line4">
                                    <input type="text" name="sale_state" id="sale_state" class="inputText" data-required="true" value="<?php if(isset($_GET['id']) && get_field('sale_state',$_GET['id'])) echo get_field('sale_state',$_GET['id']); ?>" placeholder="STATE">
                                </div>
                                <div class="groupControl groupForm row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <div class="groupControl groupForm ">
                                                    <label for="start_date" class="textLabel">START DATE</label>
                                                    <input type="text" name="start_date" id="start_date" class="inputText inputDate" data-required="true" value="<?php if(isset($_GET['id']) && get_field('sale_starting_day',$_GET['id'])) echo get_field('sale_starting_day',$_GET['id']); ?>" placeholder="FRIDAY JUNE 23">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <div class="groupControl groupForm">
                                                    <label for="end_date" class="textLabel">END DATE</label>
                                                    <input type="text" name="end_date" id="end_date" class="inputText inputDate" data-required="true" value="<?php if(isset($_GET['id']) && get_field('sale_end_day',$_GET['id'])) echo get_field('sale_end_day',$_GET['id']); ?>" placeholder="FRIDAY JUNE 23">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <div class="groupControl groupForm">
                                                    <div class="groupControl groupForm">
                                                        <label for="start_time" class="textLabel">START TIME</label>
                                                        <input type="text" name="start_time" id="start_time" class="inputText inputTime" data-required="true" value="<?php if(isset($_GET['id']) && get_field('sale_start_time',$_GET['id'])) echo get_field('sale_start_time',$_GET['id']); ?>" placeholder="06:00AM">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <div class="groupControl groupForm">
                                                    <label for="end_time" class="textLabel">END TIME</label>
                                                    <input type="text" name="end_time" id="end_time" class="inputText inputTime" data-required="true" value="<?php if(isset($_GET['id']) && get_field('sale_end_time',$_GET['id'])) echo get_field('sale_end_time',$_GET['id']); ?>" placeholder="12.30PM">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php /*
                                <div class="groupControl groupForm line1">
                                    <label for="time_zone" class="textLabel">TIME ZONE</label>
                                    <?php
                                    $timezone = timezone_list();
                                    $selected = (isset($_GET['id']) && get_field('sale_time_zone',$_GET['id']))?get_field('sale_time_zone',$_GET['id']):'';
                                    ?>
                                    <select id="time_zone" name="time_zone" class="select" data-required="true">
                                        <option value="">Select a Timezone</option>
                                        <?php foreach ($timezone as $val): ?>
                                            <option value="<?php echo $val; ?>"<?php if($selected == $val) echo ' selected';?>><?php echo $val; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="groupControl groupForm line1">
                                    <label for="sale_organizer_email" class="textLabel">ORGANIZER EMAIL</label>
                                    <input type="text" name="sale_organizer_email" id="sale_organizer_email" class="inputText" data-required="true" value="<?php if(isset($_GET['id']) && get_field('sale_organizer_email',$_GET['id'])) echo get_field('sale_organizer_email',$_GET['id']); ?>">
                                </div>
                                */?>
                                <div class="groupControl textForm"><h4 class="hyperlink">HYPERLINKS</h4></div>
                                <div class="groupControl groupForm hyperlink">
                                    <input type="text" name="facebook_sale_url" id="facebook_sale_url" class="inputText" data-required="true" value="<?php if(isset($_GET['id']) && get_field('sale_facebook_url',$_GET['id'])) echo get_field('sale_facebook_url',$_GET['id']); ?>" placeholder="FACEBOOK EVENT URL">
                                </div>
                                <div class="groupControl groupForm line1">
                                    <input type="text" name="sale_website_URL_1" id="sale_website_URL_1" class="inputText"  value="<?php if(isset($_GET['id']) && get_field('sale_website_url_1',$_GET['id'])) echo get_field('sale_website_url_1',$_GET['id']); ?>" placeholder="WEBSITE URL 1">
                                </div>
                                <div class="groupControl groupForm line2">
                                    <input type="text" name="sale_website_URL_2" id="sale_website_URL_2" class="inputText"  value="<?php if(isset($_GET['id']) && get_field('sale_website_url_2',$_GET['id'])) echo get_field('sale_website_url_2',$_GET['id']); ?>" placeholder="WEBSITE URL 2">
                                </div>
                                <div class="groupControl textForm"><h4>DATES AND OPENING HOURS</h4></div>
                                <div class="groupControl groupForm errorMarginTextarea">
                                    <textarea type="text" name="date_and_opening" id="date_and_opening" cols="40" rows="10" class="inputText" data-required="true" placeholder="Friday June 23 until Monday June 26, 9am-6pm. Saturday June 24, 9am-4pm."><?php if(isset($_GET['id']) && get_field('date_and_opening',$_GET['id'],false)) echo get_field('date_and_opening',$_GET['id']); ?></textarea>
                                </div>
                                <div class="groupControl textForm"><h4>SALE INFO</h4></div>
                                <div class="groupControl groupForm errorMarginTextarea">
                                    <textarea type="text" name="sale_info" id="sale_info" cols="40" rows="10" class="inputText" data-required="true" placeholder="Please include all offers and discounts."><?php if(isset($_GET['id'])) echo stripslashes(get_the_content()); ?></textarea>
                                </div>
                            </div>
                            <div class="row lastButtons">
                                <div class="bgButton">
                                    <div class="col-xs-6 colPrev">
                                        <?php if(!isset($_GET['id']) || get_post_status()!='publish'): ?>
                                            <div class="buttonPrev">
                                                <img class="controlPrev" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow.png">
                                                <a href="<?php the_user_dashboard_link(); ?>">BACK</a>
                                            </div>
                                        <?php else: ?>
                                            <div class="buttonPrev buttonNext buttonSave">
                                                <img class="buttonNext" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow.png">
                                                <a href="<?php the_user_dashboard_link(); ?>">Save</a>
                                            </div>
                                        <?php endif; ?>
                                        <p class="TitleLast">
                                            *Listing will be reviewed and edited before going live. For any queries or information on custom packages, please email <a href="mailto:leah@furstmedia.com.au">leah@furstmedia.com.au</a></p>
                                    </div>
                                    <?php if(!isset($_GET['id']) || get_post_status()!='publish'): ?>
                                        <div class="col-xs-6 colNext">
                                            <div class="buttonNext">
                                                <a href="">Continue</a>
                                                <img class="controlNext" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow.png">
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                        <div class="sidebarImageUpload col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="uploadImage pull-right">
                                <div class="groupControl">
                                    <label for="image">
                                        <span>Image</span>
                                        <?php $maxsize = ini_get('upload_max_filesize'); ?>
                                        <input type="hidden" name="image_id" data-required="true" value="<?php if(isset($_GET['id'])) echo get_post_thumbnail_id( $_GET['id'] ); ?>">
                                        <input type="file" class="uploadImage" name="image" id="image" max="<?php echo $maxsize; ?>"  value="" style="display: none">
                                        <div class="divUploadImage" src="<?php if(isset($_GET['id'])) echo get_the_post_thumbnail_url( $_GET['id'] ); ?>">
                                            <button type="button" class="removeImage" style="display: none;"><img src="<?php echo THEME_IMAGES; ?>/close-black.svg" alt="Button Remove Image"></button>
                                        </div>
                                    </label>
                                </div>
                                <p>To ensure your listing gets published quickly, please supply an image with no overlaid text or logos.</p>
                            </div>
                        </div>
                        <div class="mainControl">
                            <input type="submit" name="user-submit" value="Save" class="user-submit" tabindex="103" style="display: none;">
                            <input type="hidden" name="redirect_to" value="/sign-up/?register=true">
                            <input type="hidden" name="user_id" value="<?php echo $user->ID; ?>" />
                            <?php if($_GET['id']): ?>
                                <input type="hidden" name="post_id" value="<?php echo $_GET['id']; ?>" />
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>
<?php get_footer('page');?>