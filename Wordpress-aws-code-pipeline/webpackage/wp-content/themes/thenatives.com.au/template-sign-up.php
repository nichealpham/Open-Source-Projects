<?php
/*
 * Template Name: Sign Up
 */
?>
<?php get_header(); ?>
<section class="sign-up">
    <div class="container">
        <div class="titleSign">
            <p>Already have an account? <a href="<?php the_login_link(); ?>" class="typeFace">Sign in.</a></p>
        </div>
        <div class="verticalSpacing-sm"></div>
        <div class="formSignup">
            <?php do_action('theme_register_form'); ?>
        </div>
        <h4 class="textTerm">
            By creating this account, I agree to the Terms of Use and Privacy Policy.
        </h4>
    </div>
</section>
<?php get_footer('page');?>
