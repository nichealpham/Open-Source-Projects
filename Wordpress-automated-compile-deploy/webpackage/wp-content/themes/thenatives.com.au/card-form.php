<div class="row">
		<div class="groupControl col-lg-6 col-ms-6 col-sm-6 col-xs-12">
			<input id="card_name" class="input stripe-sensitive required" name="card_name" type="text" data-required="true" value="<?php if(isset($userdata->user_cardname)) echo $userdata->user_cardname; ?>" placeholder="EMAIL">
		</div>
		<div class="groupControl nonePaddingRes col-lg-6 col-ms-6 col-sm-6 col-xs-12">
			<input id="card_number" class="input stripe-sensitive card_number required" name="card_number"  type="text" data-required="true" value="<?php if(isset($userdata->user_cardnumber)) echo $userdata->user_cardnumber; ?>" placeholder="card number">
		</div>
	</div>
	<div class="row">
		<div class="groupControl col-lg-6 col-ms-6 col-sm-6 col-xs-12">
			<div class="row">
				<label class="textLabel labelBlock" for="date-mm">Card Expiry Date</label>
				<div class="groupControl dateExpiry col-sm-6">
					<select id="date_mm" class="input stripe-sensitive card_month required" name="card_month" type="text" data-required="true">
					<?php for($i = 1; $i <= 12; $i++ ): ?>
						<option value="<?php echo $i; ?>"<?php if(isset($userdata->user_cardexpiry1) && $userdata->user_cardexpiry1 == $i) echo ' selected'; ?>><?php if($i<10) echo "0"; echo $i; ?></option>
					<?php endfor; ?>
					</select>
				</div>
				<div class="groupControl dateExpiry nonePaddingRes col-sm-6">
					<select id="date_yy" class="input stripe-sensitive card_year required"  type="text" data-required="true">
						<?php for($i = date('y'); $i <= intval(date('y'))+11; $i++ ): ?>
							<option value="<?php echo $i; ?>"<?php if(isset($userdata->user_cardexpiry2) && $userdata->user_cardexpiry2 == $i) echo ' selected'; ?>><?php if($i<10) echo "0"; echo $i; ?></option>
						<?php endfor; ?>
					</select>
				</div>
			</div>
		</div>
		<div class="groupControl col-lg-6 col-ms-6 col-sm-6 col-xs-12">
			<div class="row">
				<div class="groupControl col-sm-6">
					<label class="textLabel" for="card-cvc">CVC</label>
					<input id="card_cvc" class="input stripe-sensitive card_cvc required"  type="text" data-required="true" value="<?php if(isset($userdata->user_cardcvc)) echo $userdata->user_cardcvc; ?>" placeholder="CVC">
				</div>
				<div class="groupControl col-sm-6">
					<label class="textLabel" for="">Save Payment Details</label>
					<select id="value-bool" name="value_bool" data-required="true" class="select">
						<option value="yes">Yes</option>
						<option value="no">No</option>
					</select>
				</div>
			</div>
		</div>
	</div>

	<div class="mainControl">
		<input type="hidden" name="post_id" id="post_id" value="<?php echo $_GET['id']; ?>" />
		<input type="hidden" name="post_name" id="post_name" value="<?php the_title(); ?>" />
		<input type="hidden" name="post_price" id="post_price" value="<?php echo $real_price; ?>" />
		<input type="hidden" name="post_date" id="post_date" value="<?php echo $date; ?>" />
		<input type="hidden" name="post_package" id="post_package" value="<?php echo $package->term_id; ?>" />
	</div>