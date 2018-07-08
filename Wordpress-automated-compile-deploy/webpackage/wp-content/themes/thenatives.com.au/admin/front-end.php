<div class="wrap" id="of_container">

	<div id="of-popup-save" class="of-save-popup">
		<div class="of-save-save">Options Updated</div>
	</div>
	
	<div id="of-popup-reset" class="of-save-popup">
		<div class="of-save-reset">Options Reset</div>
	</div>
	
	<div id="of-popup-fail" class="of-save-popup">
		<div class="of-save-fail">Error!</div>
	</div>
	
	<div id="of-popup-loading" class="of-save-popup">
		<div class="of-save-loading">Loading...</div>
	</div>
	
	<span style="display: none;" id="hooks"><?php echo json_encode(of_get_header_classes_array()); ?></span>
	<input type="hidden" id="reset" value="<?php if(isset($_REQUEST['reset'])) echo esc_attr($_REQUEST['reset']); ?>" />
	<input type="hidden" id="security" name="security" value="<?php echo wp_create_nonce('of_ajax_nonce'); ?>" />

	<form id="of_form" method="post" action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] ) ?>" enctype="multipart/form-data" >
	
		<div id="header">
		
			<div class="logo">
				<h2><?php echo THEMENAME; ?></h2>
				<span><?php echo ('v'. THEMEVERSION); ?></span>
			</div>
		
			<div id="js-warning">Warning- This options panel will not work properly without javascript!</div>
			<div class="icon-option"></div>
			<div class="clear"></div>
		
    	</div>

		<div id="info_bar">
		
			<a>
				<div id="expand_options" class="expand">Expand</div>
			</a>
						
			<img style="display:none" src="<?php echo ADMIN_DIR; ?>assets/images/loading-bottom.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working..." />
			<!--span style="display:none" class="ajax-loading-img ajax-loading-img-bottom"><i class="fa fa-spinner fa-spin"></i></span-->

			<button id="of_save" type="button" class="button-primary">
				<?php _e('Save All Changes',"duong");?>
			</button>
			
		</div><!--.info_bar--> 	
		
		<div id="main">
		
			<div id="of-nav">
				<ul>
				  <?php echo $options_machine->Menu ?>
				</ul>
			</div>

			<div id="content">
		  		<?php echo $options_machine->Inputs /* Settings */ ?>
		  	</div>
		  	
			<div class="clear"></div>
			
		</div>
		
		<div class="save_bar"> 		
			<button id ="of_reset" type="button" class="button submit-button reset-button" ><?php _e('Options Reset',"wpdance");?></button>
			<div>
				<img style="display:none" src="<?php echo ADMIN_DIR; ?>assets/images/loading-bottom.gif" class="ajax-reset-loading-img ajax-loading-img-bottom" alt="Working..." />
			</div>
			<button id ="of_save" type="button" class="button-primary"><?php _e('Save All Changes',"wpdance");?></button>
			<div>
				<img style="display:none" src="<?php echo ADMIN_DIR; ?>assets/images/loading-bottom.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working..." />	
			</div>
			
		</div><!--.save_bar--> 
 
	</form>
	
	<div style="clear:both;"></div>

</div><!--wrap-->
<!--<div class="smof_footer_info">Slightly Modified Options Framework <strong><?php echo SMOF_VERSION; ?></strong></div>-->