<?php
/*
 * Template Name: Post A Checkout
 */
?>
<?php require get_template_directory(). '/stripe.php'; ?>
<?php
global $thenatives;
$id = isset($_GET['id'])?$_GET['id']:'';
if(!$id && !isset($_POST['stripeToken'])){
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
$package = get_term_by('id', get_field('priority'), get_post_type().'-packages');
$price = '';
if(get_field('price',get_post_type().'-packages_'.$package->term_id)){
    $price.= '$'.get_field('price',get_post_type().'-packages_'.$package->term_id);
    if(get_field('gst',get_post_type().'-packages_'.$package->term_id)) {
        $price.= ' + GST';
    }
}
if(isset($_POST['stripeToken'])) {
    $error = '';
    $success = '';
    \Stripe\Stripe::setVerifySslCerts(false);
    \Stripe\Stripe::setApiKey($thenatives['stripe_secret_key']);

    $job = get_post($_GET['id']);
    $desc = $job->post_title . ' - ' . $package->name;
    try {

        $token = $_POST['stripeToken'];
        if ($_POST['value_bool'] == 'yes') {
            $customer = \Stripe\Customer::create(array(
                "email" => $_POST['card_name'],
                "source" => $token,
            ));

            \Stripe\Charge::create(
                array("amount" => $_POST['post_price'] * 100,
                    "currency" => "usd",
                    "customer" => $customer->id,
                    "description" => $desc,
                ));
        } else {
            \Stripe\Charge::create(
                array("amount" => $_POST['post_price'] * 100,
                    "currency" => "usd",
                    "description" => $desc,
                    "source" => $token));

        }
        $my_post = array(
            'ID' => $_GET['id'],
            'post_status' => 'pending',
        );
        update_field('priority', $package->term_id, $_GET['id']);
        wp_update_post($my_post);
        wp_set_post_terms($_GET['id'], $package->term_id, get_post_type($_GET['id']) . '-packages');
        echo '<script>window.location.href = "'.get_user_dashboard_link().'/?purchase=success"</script>';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
if(get_post_type()=='career'){
    $company = wp_get_post_terms( get_the_ID(), 'career-companies' );
}
else {
    $company = wp_get_post_terms( get_the_ID(), get_post_type().'-cities' );
}
$date = date('d/m/y',strtotime($date.' + '.intval(get_field('days',get_post_type().'-packages_'.$package->term_id)). ' days'));

$real_price = (get_field('gst',get_post_type().'-packages_'.$package->term_id))?get_field('price',get_post_type().'-packages_'.$package->term_id)*1.1:get_field('price',get_post_type().'-packages_'.$package->term_id);
?>
<?php get_header();?>
<style>
.payment-sucess {
    color: green;
    font-size: 30px;
    margin-bottom: 50px;
    font-weight: bold;
}
</style>
<div class="container">
    <section id="post-purchar-checkout">
        <div class="listProduct relatedSection">
            <table>
                <tr>
                    <th>Jobs</th>
                    <th></th>
                    <th>price</th>
                    <th>expiry date</th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <td><?php the_title(); ?></td>
                    <td><?php echo count($company)?$company[0]->name:''; ?></td>
                    <td><?php echo $price; ?></td>
                    <td><?php echo $date; ?></td>
                    <td><a href="<?php the_post_job_link(); ?>/?id=<?php the_ID(); ?>" class="btnEdit"><img src="<?php echo THEME_IMAGES; ?>/edit.svg"></a></td>
                    <td><a href="" class="btnDelete" data-post="<?php the_ID(); ?>"><img src="<?php echo THEME_IMAGES; ?>/close-black.svg"></a></td>
                </tr>
            </table>
        </div>
        <div class="formPayPurchar">
            <?php
            $user = get_currentuserinfo();
            $userdata = get_userdata($user->ID);
            ?>
            <div id="form-purchar-checkout">
                <form id="formCheckoutCareer" class="userForm" method="post">
                    <h4 class="titleForm">Payment Details</h4>
                    <?php
					$have_payment=($thenatives['stripe_publishable_key'] && $thenatives['stripe_secret_key'])?1:0;
					if(!$have_payment){
						$alert=(current_user_can('administrator'))?'You must config Payment Gateways first. Go to <a href="'.admin_url('themes.php?page=thenatives_settings').'" target="_blank" >here</a>':'Contact admin to config payment first';
					?>
					<div class="">
						<div class="groupControl alert-config">
							<span class="form-error-message"><?php echo $alert; ?></span>
						</span>
					</div>
					<?php
					}
                    require get_template_directory(). '/card-form.php';
                    $erro = (isset($error) && $error)?$error:'';
                    echo '<span class="payment-errors form-error-message">'.$error.'</span>';
					?>
                </form>
                <div class="row userForm">
                    <div class="bgButton bgButtonPurchase">
                        <div class="col-xs-6 colPrev">
                            <div class="buttonPrev">
                                <img class="controlPrev" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow.png">
                                <a href="<?php the_purchase_link(); ?>/?id=<?php the_ID(); ?>">BACK</a>
                            </div>
                        </div>
                        <div class="col-xs-6 colNext">
                            <div class="buttonNext btnNextPurchase_checkout">
                                <a href="<?php bloginfo('url');?>/contact">purchase </a>
                                <img class="controlNext" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow.png">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="textFormFooter">
                    <p>By clicking purchase, we will charge your credit card and review your job listing before making it
                        visible on our site. After purchasing and submitting your listing to our team for review, you will not
                        be able to make further edits.</p>
                </div>
            </div>
        </div>
    </section>
</div>
<?php get_footer('page'); ?>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">
	var $ = jQuery;
	function is_email(email) {
	  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  return regex.test(email);
	}
	Stripe.setPublishableKey('<?php echo $thenatives['stripe_publishable_key']; ?>');
	function stripeResponseHandler(status, response) {
		if (response.error) {
			$('.btnNextPurchase').removeAttr("disabled");
			$(".payment-errors").html(response.error.message);
		} else {
			var form$ = $("#formCheckoutCareer");
			var token = response['id'];
			form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
			form$.get(0).submit();
		}
	}
	$(document).ready(function() {
		$(".btnNextPurchase_checkout").click(function() {
			var err=0;
			var name=$('#card_name').val();
			var cvc =$('#card_cvc').val();
			var value_bool= $('#value-bool').val();
			if(cvc==""){
				err=1;
				$(".payment-errors").html("Your card's security code is invalid.");
			}
			if(!is_email(name) && value_bool=="yes"){
				err=1;
				$(".payment-errors").html('Check your email.');
			}
			
			if(err==0){
				Stripe.createToken({
					number: $('#card_number').val(),
					cvc: $('#card_cvc').val(),
					exp_month: $('#date_mm').val(),
					exp_year: $('#date_yy').val()
				}, stripeResponseHandler);
			}
			return false; 
		});
	});
</script>