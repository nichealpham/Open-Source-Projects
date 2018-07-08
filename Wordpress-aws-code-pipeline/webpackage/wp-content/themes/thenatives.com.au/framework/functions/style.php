<?php 
if(!function_exists('thenatives_custom_style')){
	function thenatives_custom_style() {
		global $thenatives;
		echo '<style type="text/css">';
		if (file_exists(THEME_DIR."/config_xml/font_config.xml")) {
			$objXML_typo = simplexml_load_file(THEME_DIR."/config_xml/font_config.xml");
			foreach ($objXML_typo->children() as $child) {
				foreach ($child->items->children() as $childofchild) {
					$type 		= (string) $childofchild->type;
					$slug 		= (string) $childofchild->slug;
					$fold 		= (string) $childofchild->fold;
					$fold_val 	= (string) $childofchild->fold_val;
					if($fold_val == $thenatives[$fold] && !($type == 'font_family' && $thenatives[$slug]=="custom")){
						$value = $thenatives[$slug];
						if($type == 'font_family'){
							$value = ucfirst($value);
						}
						$selector 	= (string) $childofchild->selector;
						$attribute 	= (string) $childofchild->attribute;
						if($attribute) {
							echo "\n".$selector.'{'.$attribute.': '.$value.';}';
						}
					}
				}
			}
		}
		$xml_file = isset($xml_file) && strlen(trim($xml_file)) > 0 ? $xml_file : 'color_default';
		$url_xml_file = THEME_DIR."/config_xml/".$xml_file.".xml";
		if (file_exists($url_xml_file)) {
			$objXML_color = simplexml_load_file($url_xml_file);
			foreach ($objXML_color->children() as $child) {
				foreach ($child->items->children() as $childofchild) {
					$slug 		= (string) $childofchild->slug;
					$selector 	= (string) $childofchild->selector;
					$attribute 	= (string) $childofchild->attribute;
					if($attribute) {
						echo "\n".$selector.'{'.$attribute.': '.$thenatives[$slug].';}';
					}
				}
			}
		}
		if($thenatives['thenatives_custom_css']){
			echo $thenatives['thenatives_custom_css'];
		}
		echo "\n".'</style>';
	}
	add_action( 'wp_head', 'thenatives_custom_style',90);
}