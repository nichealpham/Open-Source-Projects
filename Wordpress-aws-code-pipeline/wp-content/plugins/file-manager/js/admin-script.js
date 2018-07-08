function __(OBJ){console.log(OBJ);}
jQuery(document).ready(function(){
	
	jQuery('#root-directory-id').on('keyup', function(){
		
		console.log("working");
		
	});
	
});


/**
 * 
 * Form manager plugin for jQuery mobile
 * 
 * */
var JQ = jQuery.noConflict(); // Using JQ as jQuery for no conflict and shortcut
JQ.fn.fManager = function(data){
	
	data = JQ.parseJSON(data); // string to json conversion
	__(data.menu_options);
	
	// Working with menu options
	STR = '<ul>';
	
	JQ.each(data.menu_options, function(index, value){
		
		//~ __("Index: " + index + ", Value: " + value);
		STR += "<li>" + value + "</li>";
		
		
	});
	
	STR += '</ul>';
	
	JQ("#fmp_permission_wrapper_id").html(STR);
	
}
