<?php
/*
 * Template Name: Sign In
 */
?>
<?php
/*if(is_user_logged_in()){
    wp_redirect(site_url());
    exit();
}*/
?>
<?php get_header(); ?>
<section class="sign-in">
    <div class="container">
        <div class="textSignIn">
            For sales, events and job listings log in below.<br>
            For all other advertising enquiries, email leah@furstmedia.com.au
        </div>
        <div class="titleSign">
            <p>Don’t have an account? <a href="<?php the_register_link(); ?>" class="typeFace">Sign up.</a></p>
        </div>
        <div class="verticalSpacing-sm"></div>
        <div class="formSignin">
            <?php do_action('theme_login_form'); ?>
        </div>
    </div>
</section>
<?php get_footer('page'); ?>