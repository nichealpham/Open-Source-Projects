<?php
function get_register_link() {
    $page = get_posts( array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-sign-up.php', // Change this to your template file name
        'hierarchical' => 0,
        'posts_per_page' => 1,
    ));
    if(count($page)) {
        $page = current($page);
        return get_page_link($page->ID);
    }
    return '';
}

function the_register_link() {
    echo get_register_link();
}

function get_login_link() {
    $page = get_posts( array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-sign-in.php', // Change this to your template file name
        'hierarchical' => 0,
        'posts_per_page' => 1,
    ));
    if(count($page)) {
        $page = current($page);
        return get_page_link($page->ID);
    }
    return '';
}

function the_login_link() {
    echo get_login_link();
}

function get_account_settings_link() {
    $page = get_posts( array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-account-settings.php', // Change this to your template file name
        'hierarchical' => 0,
        'posts_per_page' => 1,
    ));
    if(count($page)) {
        $page = current($page);
        return get_page_link($page->ID);
    }
    return '';
}

function the_account_settings_link() {
    echo get_account_settings_link();
}

function get_purchase_link() {
    $page = get_posts( array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-post-purchase.php', // Change this to your template file name
        'hierarchical' => 0,
        'posts_per_page' => 1,
    ));
    if(count($page)) {
        $page = current($page);
        return get_page_link($page->ID);
    }
    return '';
}

function the_purchase_link() {
    echo get_purchase_link();
}

function get_checkout_link() {
    $page = get_posts( array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-post-checkout.php', // Change this to your template file name
        'hierarchical' => 0,
        'posts_per_page' => 1,
    ));
    if(count($page)) {
        $page = current($page);
        return get_page_link($page->ID);
    }
    return '';
}

function the_checkout_link() {
    echo get_checkout_link();
}

function get_reset_password_link() {
    $page = get_posts( array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-password-reset.php', // Change this to your template file name
        'hierarchical' => 0,
        'posts_per_page' => 1,
    ));
    if(count($page)) {
        $page = current($page);
        return get_page_link($page->ID);
    }
    return '';
}

function the_reset_password_link() {
    echo get_reset_password_link();
}

function get_user_dashboard_link() {
    $page = get_posts( array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-user-dashboard.php', // Change this to your template file name
        'hierarchical' => 0,
        'posts_per_page' => 1,
    ));
    if(count($page)) {
        $page = current($page);
        return get_page_link($page->ID);
    }
    return '';
}

function the_user_dashboard_link() {
    echo get_user_dashboard_link();
}

function get_post_job_link() {
    $page = get_posts( array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-post-a-job.php', // Change this to your template file name
        'hierarchical' => 0,
        'posts_per_page' => 1,
    ));
    if(count($page)) {
        $page = current($page);
        return get_page_link($page->ID);
    }
    return '';
}

function the_post_job_link() {
    echo get_post_job_link();
}

function get_post_event_link() {
    $page = get_posts( array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-post-an-event.php', // Change this to your template file name
        'hierarchical' => 0,
        'posts_per_page' => 1,
    ));
    if(count($page)) {
        $page = current($page);
        return get_page_link($page->ID);
    }
    return '';
}

function the_post_event_link() {
    echo get_post_event_link();
}

function get_post_sale_link() {
    $page = get_posts( array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-post-a-sale.php', // Change this to your template file name
        'hierarchical' => 0,
        'posts_per_page' => 1,
    ));
    if(count($page)) {
        $page = current($page);
        return get_page_link($page->ID);
    }
    return '';
}

function the_post_sale_link() {
    echo get_post_sale_link();
}

function is_user_page() {
    if(is_page_template( 'template-password-reset.php' )){
        return 1;
    }
    elseif(is_page_template( 'template-sign-in.php' )){
        return 1;
    }
    elseif(is_page_template( 'template-sign-up.php' )){
        return 1;
    }
    elseif(is_page_template( 'template-user-dashboard.php' )){
        return true;
    }
    elseif(is_page_template( 'template-account-settings.php' )){
        return true;
    }
    elseif(is_page_template( 'template-post-a-job.php' )){
        return true;
    }
    elseif(is_page_template( 'template-post-a-sale.php' )){
        return true;
    }
    elseif(is_page_template( 'template-post-an-event.php' )){
        return true;
    }
    elseif(is_page_template( 'template-post-purchase.php' )){
        return true;
    }
    elseif(is_page_template( 'template-post-checkout.php' )){
        return true;
    }
    return false;
}

add_action( 'theme_register_form', 'thenatives_register_form' );
function thenatives_register_form() {

    $name = ( ! empty( $_POST['user_name'] ) ) ? trim( $_POST['user_name'] ) : '';
    $surname = ( ! empty( $_POST['user_surname'] ) ) ? trim( $_POST['user_surname'] ) : '';
    $company = ( ! empty( $_POST['user_company'] ) ) ? trim( $_POST['user_company'] ) : '';
    $phone = ( ! empty( $_POST['user_phone'] ) ) ? trim( $_POST['user_phone'] ) : '';
    $email = ( ! empty( $_POST['user_email'] ) ) ? trim( $_POST['user_email'] ) : '';
    $password = ( ! empty( $_POST['user_pass'] ) ) ? trim( $_POST['user_pass'] ) : '';
    ?>
    <form id="registerForm" method="post" class="userForm">
        <div class="row">
            <div class="userFormWrapper col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <div class="row">
                    <div class="groupControl col-sm-6">
                        <input type="text" name="user_name" id="user_name" class="input" data-required="true" value="<?php echo esc_attr( wp_unslash( $name ) ); ?>" placeholder="NAME"/>
                    </div>
                    <div class="groupControl col-sm-6">
                        <input type="text" name="user_surname" id="user_surname" class="input" data-required="true" value="<?php echo esc_attr( wp_unslash( $surname ) ); ?>" placeholder="SURNAME"/>
                    </div>
                </div>
                <div class="row">
                    <div class="groupControl col-sm-6">
                        <input type="text" name="user_company" id="user_company" class="input" value="<?php echo esc_attr( wp_unslash( $company ) ); ?>" placeholder="COMPANY"/>
                    </div>
                    <div class="groupControl col-sm-6">
                        <input type="text" name="user_phone" id="user_phone" class="input" data-required="true" value="<?php echo esc_attr( wp_unslash( $phone ) ); ?>" placeholder="PHONE NUMBER"/>
                    </div>
                </div>
                <div class="row">
                    <div class="groupControl col-sm-6">
                        <input type="email" name="user_email" id="user_email" class="input" data-required="true" value="<?php echo esc_attr( wp_unslash( $email ) ); ?>" placeholder="EMAIL"/>
                    </div>
                    <div class="groupControl col-sm-6">
                        <input type="password" name="user_pass" id="user_pass" class="input" data-required="true" value="<?php echo esc_attr( wp_unslash( $password ) ); ?>" placeholder="PASSWORD"/>
                    </div>
                </div>
                <div class="mainControl">
                    <input type="submit" name="user-submit" value="<?php _e('Sign up','thenatives'); ?>" class="user-submit" tabindex="103" />
                    <?php $register = $_GET['register']; if($register == true) { echo '<p>Check your email for the password!</p>'; } ?>
                    <input type="hidden" name="redirect_to" value="<?php the_register_link(); ?>?register=true" />
                    <div class="submitLoading">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Submit Loading">
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php
}

add_action( 'wp_head', 'thenatives_registerd_activation' );
function thenatives_registerd_activation(){
    if(isset($_GET['active']) && isset($_GET['token'])) {
        $user = get_user_by('email',trim($_GET['active']));
        $userdata = get_userdata($user->ID);
        if($userdata->token == $_GET['token']){
            $user->set_role('author');
            $redirect_link = get_login_link();
            if(!$redirect_link){
                $redirect_link = get_register_link();
            }
            wp_redirect($redirect_link);
            exit;
        }
    }
    if(isset($_GET['reset']) && isset($_GET['token'])) {
        $user = get_user_by('email',trim($_GET['reset']));
        $userdata = get_userdata($user->ID);
        if($userdata->token == $_GET['token']){
            if($userdata->new_password){
                wp_set_password( $userdata->new_password, $userdata->ID );
            }
            wp_redirect(get_login_link());
            exit;
        }
    }
    if(is_user_page() === true && !is_user_logged_in()) {
        wp_redirect(get_login_link());
        exit;
    }
}

add_action( 'theme_login_form', 'thenatives_login_form' );
function thenatives_login_form(){
?>
    <form  id="loginForm" method="post" class="userForm">
        <div class="row">
            <div class="userFormWrapper col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <div class="row">
                    <div class="groupControl col-sm-6">
                        <input type="text" data-type="email" name="log" data-required="true" class="input" value="" id="user_login" placeholder="EMAIL" />
                    </div>
                    <div class="groupControl col-sm-6">
                        <input type="password" name="pwd" value="" data-required="true" class="input" size="20" id="user_pass" placeholder="PASSWORD" />
                    </div>
                </div>

                <div class="mainControl">
                    <div class="forgotPassword"><a href="#">Forgot your password?</a></div>
                    <input type="submit" name="user-submit" value="<?php _e('Login'); ?>" tabindex="14" class="user-submit" />
                    <input type="hidden" name="redirect_to" value="<?php the_user_dashboard_link(); ?>" />
                    <div class="submitLoading">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Submit Loading">
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php
}