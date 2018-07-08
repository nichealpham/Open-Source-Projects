<?php
/*
 * Template Name: Account Settings
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
if(isset($_GET['action'])){
    if($_GET['action'] == 'change-email') {
        $user_id = $user->ID;
        $new_email = get_user_meta($user_id, 'change_email', true);
        wp_update_user(array('ID'=>$user->ID,'user_email'=>$new_email));
        wp_redirect(get_user_dashboard_link());
    }
}
?>
<?php get_header(); ?>
<section class="account-setting">
    <div class="container">
        <div class="formAccount">
            <form id="settingUser" class="user-account userForm" method="POST">
                <div class="row">
                    <div class="userFormWrapper col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <h2 class="titleRando">Account Settings</h2>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="groupControl">
                                    <label for="user_name" class="textLabel">NAME</label>
                                    <input type="text" name="user_name" id="user_name" class="input" data-required="true" value="<?php if(isset($userdata->first_name)) echo $userdata->first_name; ?>" placeholder="NAME">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="groupControl">
                                    <label for="user_surname" class="textLabel">SURNAME</label>
                                    <input type="text" name="user_surname" id="user_surname" class="input" data-required="true" value="<?php if(isset($userdata->last_name)) echo $userdata->last_name; ?>" placeholder="SURNAME">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="groupControl">
                                    <label for="user_company" class="textLabel">COMPANY</label>
                                    <input type="text" name="user_company" id="user_company" class="input" data-required="true" value="<?php if(isset($userdata->user_company)) echo $userdata->user_company; ?>" placeholder="COMPANY">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="groupControl">
                                    <label for="user_phone" class="textLabel">PHONE NUMBER</label>
                                    <input type="text" name="user_phone" id="user_phone" class="input" data-required="true" value="<?php if(isset($userdata->user_phone)) echo $userdata->user_phone; ?>" placeholder="PHONE NUMBER">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="groupControl">
                                    <label for="user_email" class="textLabel">EMAIL</label>
                                    <input type="text" name="user_email" id="user_email" class="input" data-required="true" value="<?php if(isset($userdata->user_email)) echo $userdata->user_email; ?>" placeholder="EMAIL">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="groupControl">
                                    <label for="user_password" class="textLabel">PASSWORD</label>
                                    <input type="password" name="user_password" id="user_password" class="input" value placeholder="PASSWORD">
                                </div>
                            </div>
                        </div>
                        <h2 class="titleRando spacingText">Saved Card</h2>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="groupControl">
                                    <label for="user_cardname" class="textLabel">NAME ON CARD</label>
                                    <input type="text" name="user_cardname" id="user_cardname" class="input" value="<?php if(isset($userdata->user_cardname)) echo $userdata->user_cardname; ?>" placeholder="NAME ON CARD">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="groupControl">
                                    <label for="user_cardnumber" class="textLabel">CARD NUMBER</label>
                                    <input type="text" name="user_cardnumber" id="user_cardnumber" class="input" value="<?php if(isset($userdata->user_cardnumber)) echo $userdata->user_cardnumber; ?>" placeholder="CARD NUMBER">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="groupControl">
                                    <label for="user_cardexpiry1" class="textLabel">CARD EXPIRY</label>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 inputExpiry">
                                            <input type="text" name="user_cardexpiry1" id="user_cardexpiry1" class="input expiryFirst" value="<?php if(isset($userdata->user_cardexpiry1)) echo $userdata->user_cardexpiry1; ?>" placeholder="MM">
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <input type="text" name="user_cardexpiry2" id="user_cardexpiry2" class="input expirySecond" value="<?php if(isset($userdata->user_cardexpiry2)) echo $userdata->user_cardexpiry2; ?>" placeholder="YYYY">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="groupControl">
                                    <label for="user_cardcvc" class="textLabel">CVC</label>
                                    <input type="text" name="user_cardcvc" id="user_cardcvc" class="input" value="<?php if(isset($userdata->user_cardcvc)) echo $userdata->user_cardcvc; ?>" placeholder="CVC">
                                </div>
                            </div>
                        </div>
                        <div class="mainControl">
                            <a href="<?php echo wp_logout_url( get_login_link() ); ?>" class="user-logout pull-left">Logout</a>
                            <input type="submit" name="user-submit" value="Save" class="user-submit pull-right" tabindex="104">
                            <input type="hidden" name="redirect_to" value="<?php the_login_link(); ?>">
                            <input type="hidden" name="user_id" value="<?php echo $user->ID; ?>" />
                            <div class="submitLoading">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Submit Loading">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php get_footer('page');?>
