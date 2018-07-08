<?php
/*
 * Template Name: Post A Job
 */
?>
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
        'post_type' => 'career',
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
    $title = 'edit job';
}
else {
    $title = 'create job';
}
?>
<?php get_header(); ?>
<div class="container">
<section id="post-ajob">
    <div class="titleSale">
        <a href="<?php the_user_dashboard_link(); ?>">User</a><span><?php echo $title;?></span>
    </div>
    <div class="formPostAJob formSignup">

        <form id="formPostAJob" class="userForm">
            <div class="row contentForm">
                <div class="userFormWrapper col-lg-8 col-md-8 col-sm-8 col-xs-12">
                    <div class="row">
                        <div class="groupControl col-sm-6">
                            <input id="companycareer" class="input" name="companycareer" type="text" data-required="true" placeholder="company" value="<?php if(isset($_GET['id']) && get_field('companies_career',$_GET['id'])) echo get_field('companies_career',$_GET['id']); ?>">
                        </div>
                        <div class="groupControl col-sm-6">
                            <input id="title" class="input" name="title" type="text" data-required="true" placeholder="job title" value="<?php if(isset($_GET['id'])) the_title(); ?>">
                        </div>
                    </div>
                    <div class="row jobTop">
                        <div class="groupControl col-sm-6">
                            <?php
                            $types = get_terms( array(
                                'taxonomy' => 'career-types',
                                'hide_empty' => false,
                            ) );
                            $select_types = '';
                            if($_GET['id']){
                                $select_types = get_the_terms($_GET['id'],'career-types')[0];
                            }
                            ?>
                            <select name="type" id="type" class="select" data-required="true">
                                <option value="">WORK TYPE</option>
                                <?php foreach($types as $type): ?>
                                    <option value="<?php echo $type->term_id; ?>"<?php if($select_types && $select_types->term_id==$type->term_id) echo 'selected'; ?>><?php echo $type->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="groupControl col-sm-6">
                            <?php
                            $levels = get_terms( array(
                                'taxonomy' => 'career-levels',
                                'hide_empty' => false,
                            ) );
                            $select_levels = '';
                            if($_GET['id']){
                                $select_levels = get_the_terms($_GET['id'],'career-levels')[0];
                            }
                            ?>
                            <select name="level" id="level" class="select" data-required="true">
                                <option value="">JOB LEVEL</option>
                                <?php foreach($levels as $level): ?>
                                    <option value="<?php echo $level->term_id; ?>"<?php if($select_levels && $select_levels->term_id==$level->term_id) echo 'selected'; ?>><?php echo $level->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="groupControl col-sm-6">
                            <input id="suburb" class="input" name="suburb" type="text" data-required="true" placeholder="suburb" value="<?php if(isset($_GET['id']) && get_field('suburb',$_GET['id'])) echo get_field('suburb',$_GET['id']); ?>">
                        </div>
                        <div class="groupControl col-sm-6">
                            <div class="row">
                                <div class="col-sm-6">
                                    <input id="citycareer" class="input" name="citycareer" type="text" data-required="true" placeholder="city" value="<?php if(isset($_GET['id']) && get_field('city_career',$_GET['id'])) echo get_field('city_career',$_GET['id']); ?>">
                                </div>
                                <div class="col-sm-6">
                                    <input id="state" class="input" name="state" type="text" data-required="true" placeholder="state" value="<?php if(isset($_GET['id']) && get_field('state',$_GET['id'])) echo get_field('state',$_GET['id']); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="groupControl decription errorMarginTextarea">
                        <label class="textLabel" for="decription">Role Description</label>
                        <textarea id="description" name="description" data-required="true" class="textarea" placeholder="A brief overview of what the job entails"><?php if(isset($_GET['id']) && strip_tags(get_field('role_description',$_GET['id']))) echo strip_tags(get_field('role_description',$_GET['id'])); ?></textarea>
                    </div>
                    <div class="groupControl responsibilities errorMarginTextarea">
                        <label class="textLabel" for="responsibilities">Key Responsibilities</label>
                        <div class="textarea-editor textarea ul-editor" contenteditable="true" for="responsibilities">
                            <?php if(isset($_GET['id']) && get_field('key_responsibilities',$_GET['id'])): ?>
                                <?php echo get_field('key_responsibilities',$_GET['id']); ?>
                            <?php else : ?>
                                <span class="place-holder">Write this in dot-point format.</span>
                            <?php endif; ?>
                        </div>
                        <textarea  id="responsibilities" name="responsibilities" data-required="true" class="textarea" placeholder="Write this in dot-point format."><?php if(isset($_GET['id']) && get_field('key_responsibilities',$_GET['id'])) echo get_field('key_responsibilities',$_GET['id']); ?></textarea>
                    </div>
                    <div class="row salary">
                        <div class="groupControl SalarY col-sm-9">
                            <label class="textLabel" for="">Salary</label>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?php $args = array(
                                        'posts_per_page'   => 1,
                                        'post_type'        => 'career',
                                    );
                                    $posts_array = get_posts( $args );?>
                                        <?php
                                        $slarymin = get_field_object('salary_min',$posts_array[0]->ID);
                                        $selected = get_field('salary_min');
                                        $choice = $slarymin['choices'];
                                        ?>
                                        <select id="min_salary" name="min_salary" class="select" data-required="true">
                                            <option value="">MIN</option>
                                            <?php foreach ($choice as $choices): ?>
                                                <option value="<?php echo $choices; ?>"<?php if($selected  == $choices) echo ' selected';?>><?php echo $choices; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                </div>
                                <div class="col-sm-6">
                                    <?php $args = array(
                                        'posts_per_page'   => 1,
                                        'post_type'        => 'career',
                                    );
                                    $posts_array = get_posts( $args );?>
                                    <?php
                                    $slarymin = get_field_object('salary_max',$posts_array[0]->ID);
                                    $selected = get_field('salary_max');
                                    $choice = $slarymin['choices'];
                                    ?>
                                    <select id="max_salary" name="max_salary" class="select" data-required="true">
                                        <option value="">MAX</option>
                                        <?php foreach ($choice as $choices): ?>
                                            <option value="<?php echo $choices; ?>"<?php if($selected == $choices) echo ' selected';?>><?php echo $choices; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="groupControl showSalarY col-sm-3">
                            <label class="textLabel" for="show_salary">Show Salary</label>
                            <select name="show_salary" id="show_salary" data-required="true" class="select">
                                <option value="1" <?php if(isset($_GET['id']) && get_field('show_salary',$_GET['id'])) echo "selected"; ?>>Yes</option>
                                <option value="0" <?php if(isset($_GET['id']) && !get_field('show_salary',$_GET['id'])) echo "selected"; ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="row salary">
                        <div class="groupControl col-sm-6">
                            <label class="textLabel" for="closing">Application Closing Date</label>
                            <input class="input inputDateYear" id="closing" type="text" data-required="true" name="closing" value="<?php if(isset($_GET['id']) && get_field('closing',$_GET['id'])) echo get_field('closing',$_GET['id']); ?>" placeholder="<?php echo date('d.m.Y'); ?>">
                        </div>
                        <div class="groupControl col-sm-6">
                            <label class="textLabel" for="email">Applications Sent To</label>
                            <input class="input" id="email" type="email" data-type="email" data-required="true" name="email" value="
                            <?php if(isset($_GET['id']) && get_field('applications_sent_to',$_GET['id'])) {
                                echo get_field('applications_sent_to', $_GET['id']);
                            }
                            elseif(isset($userdata->user_email)) echo $userdata->user_email; ?>
                            " placeholder="EXAMPLE@MAIL.COM">
                        </div>
                    </div>
                    <div class="row lastButtons">
                        <div class="bgButton btnSubmit">
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
                <div class="sidebarImageUpload col-sm-4">
                    <div class="uploadImage pull-right">
                        <div class="groupControl">
                            <label for="image">
                                <span>Image</span>
                                <?php $maxsize = ini_get('upload_max_filesize'); ?>
                                <input type="hidden" name="image_id" data-required="true" value="<?php if(isset($_GET['id'])) echo get_post_thumbnail_id( $_GET['id'] ); ?>">
                                <input type="file" class="uploadImage" name="image" id="image" value="" max="<?php echo $maxsize; ?>" style="display: none">
                                <div class="divUploadImage" src="<?php if(isset($_GET['id'])) echo get_the_post_thumbnail_url( $_GET['id'] ); ?>">
                                    <button type="button" class="removeImage" style="display: none;"><img src="<?php echo THEME_IMAGES; ?>/close-black.svg" alt="Button Remove Image"></button>
                                </div>
                            </label>
                        </div>
                        <p>To ensure your listing gets published quickly, please supply an image with no overlaid text or logos.</p>
                    </div>
                </div>
            </div>
            <div class="mainControl">
                <input type="submit" name="user-submit" value="Save" class="user-submit" tabindex="103" style="display: none;">
                <input type="hidden" name="user_id" value="<?php echo $user->ID; ?>" />
                <?php if($_GET['id']): ?>
                    <input type="hidden" name="post_id" value="<?php echo $_GET['id']; ?>" />
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>
</div>
<?php get_footer('page');?>
