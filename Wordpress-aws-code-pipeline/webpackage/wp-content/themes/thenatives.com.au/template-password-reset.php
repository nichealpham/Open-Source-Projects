<?php
/*
 * Template Name: Password Reset
 */
?>
<?php get_header(); ?>
<section class="reset-password">
    <div class="container">
        <div class="formResetPass">
            <form id="resetPassUser" class="user-reset-password userForm" method="POST">
                <div class="row">
                    <div class="userFormWrapper col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <h2 class="titleRando">Reset your password</h2>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="groupControl">
                                    <input type="email" name="reset_email" id="reset_email" class="input" data-required="true" value placeholder="EMAIL">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="groupControl">
                                    <input type="password" name="new_password" id="new_password" class="input" data-required="true" value placeholder="NEW PASSWORD">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="groupControl">
                                    <input type="password" name="confirm_new" id="confirm_new" class="input" data-required="true" data-equal="new_password" value placeholder="CONFIRM NEW PASSWORD">
                                </div>
                            </div>
                        </div>
                        <div class="mainControl">
                            <input type="submit" name="user-submit" value="Reset" class="user-submit" tabindex="103">
                            <input type="hidden" name="redirect_to" value="/sign-up/?register=true">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php get_footer('page');?>
