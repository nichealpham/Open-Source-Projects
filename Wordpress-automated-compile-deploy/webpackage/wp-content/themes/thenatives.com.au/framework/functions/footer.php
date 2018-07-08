<?php
if(!function_exists ('thenatives_to_top_button')){
	function thenatives_to_top_button() { 
		global $thenatives;
		if($thenatives['thenatives_totop']){ 
		?>
			<div id="toTop"><a href="javascript:void(0)"><i class="fa fa-long-arrow-up" aria-hidden="true"></i></a></div>
		<?php
		}
	}
	add_action('wp_footer','thenatives_to_top_button',1);
}