if ( typeof q2w3_sidebar_options != 'undefined' && q2w3_sidebar_options.length > 0 ) {
	if ( window.jQuery ) {
		if ( q2w3_sidebar_options[0].window_load_hook ) {
			jQuery(window).load(q2w3_sidebar_init());
		} else {
			jQuery(document).ready(q2w3_sidebar_init());
		}  
	} else {
		console.log('jQuery is not loaded!');
	}
} else {
	console.log('q2w3_sidebar_options not found!');
}

function q2w3_sidebar_init() {
	for ( var i = 0; i < q2w3_sidebar_options.length; i++ ) {
		q2w3_sidebar(q2w3_sidebar_options[i]);		
	}
	jQuery(window).on('resize', function(){
		for ( var i = 0; i < q2w3_sidebar_options.length; i++ ) {
			q2w3_sidebar(q2w3_sidebar_options[i]);
		}
	});
	var MutationObserver = (function(){
		var prefixes = ['WebKit', 'Moz', 'O', 'Ms', ''];
		for ( var i = 0; i < prefixes.length; i++ ) {
			if ( prefixes[i] + 'MutationObserver' in window ) {
				return window[prefixes[i] + 'MutationObserver'];
			}
		}
		return false;
	}());
	if ( q2w3_sidebar_options[0].disable_mo_api == false && MutationObserver ) { 
		q2w3Refresh = false
		var htmlObserver = new MutationObserver(function(mutations) {  
			mutations.forEach( function(mutation) {
				if ( q2w3_exclude_mutations_array(q2w3_sidebar_options).indexOf(mutation.target.id) == -1 && mutation.target.className.indexOf('q2w3-fixed-widget-container') == -1 ) {
					q2w3Refresh = true;
					//console.log('Mutation detected!');
				}
			});
	    });
	    htmlObserver.observe(document.body, {childList: true, attributes: true, attributeFilter:['style', 'class'], subtree: true}); 
	    setInterval(function(){
	    	if ( q2w3Refresh ) { 
	    		for ( var i = 0; i < q2w3_sidebar_options.length; i++ ) {
	    			q2w3_sidebar(q2w3_sidebar_options[i]);
		    	}
	    		q2w3Refresh = false;
	    	}
	    }, 300); 
	} else {	
		console.log('MutationObserver not supported or disabled!');
		if ( q2w3_sidebar_options[0].refresh_interval > 0 ) {
			setInterval(function(){
		    	for ( var i = 0; i < q2w3_sidebar_options.length; i++ ) {
		    		q2w3_sidebar(q2w3_sidebar_options[i]);
			    }
		    }, q2w3_sidebar_options[0].refresh_interval);
		}
	}
}

function q2w3_exclude_mutations_array(q2w3_sidebar_options) {
	var out = new Array();
	for ( var i = 0; i < q2w3_sidebar_options.length; i++ ) {
		if ( q2w3_sidebar_options[i].widgets.length > 0 ) {
			for ( var k = 0; k < q2w3_sidebar_options[i].widgets.length; k++ ) {
				out.push(q2w3_sidebar_options[i].widgets[k]);
				out.push(q2w3_sidebar_options[i].widgets[k] + '_clone');
			}
		}
	}  
	return out;
}

function q2w3_sidebar(options) {
	
	if ( !options ) return false;
	
	if ( !options.widgets) return false;
	
	if ( options.widgets.length < 1) return false;
	
	if ( !options.sidebar) options.sidebar = 'q2w3-default-sidebar'; 
	
	//console.log(options.sidebar + '  call');
	
	function widget() {} // widget class
	
	var widgets = new Array();
	
	var window_height = jQuery(window).height();
	var document_height = jQuery(document).height();
	var fixed_margin_top = options.margin_top;
	
	if ( jQuery('#wpadminbar').length )  { // WordPress admin bar 
		fixed_margin_top = options.margin_top + jQuery('#wpadminbar').height();
	}
	
	jQuery('.q2w3-widget-clone-' + options.sidebar).remove(); // clear fixed mode p1
	
	for ( var i = 0; i < options.widgets.length; i++ ) {
		widget_obj = jQuery('#' + options.widgets[i]);
		widget_obj.css('position',''); // clear fixed mode p2
		if ( widget_obj.attr('id') ) { 
			widgets[i] = new widget();
			widgets[i].obj = widget_obj;
			widgets[i].clone = widget_obj.clone();
			widgets[i].clone.children().remove();
			widgets[i].clone_id = widget_obj.attr('id') + '_clone';
			widgets[i].clone.addClass('q2w3-widget-clone-' + options.sidebar);
			widgets[i].clone.attr('id', widgets[i].clone_id);
			widgets[i].clone.css('height', widget_obj.height());
			widgets[i].clone.css('visibility', 'hidden');
			widgets[i].offset_top = widget_obj.offset().top;
			widgets[i].fixed_margin_top = fixed_margin_top;
			widgets[i].height = widget_obj.outerHeight(true);
			widgets[i].fixed_margin_bottom = fixed_margin_top + widgets[i].height;
			fixed_margin_top += widgets[i].height;
		} else {
			widgets[i] = false;			
		}
	}
	
	var next_widgets_height = 0;
	
	var widget_parent_container;
		
	for ( var i = widgets.length - 1; i >= 0; i-- ) {
		if (widgets[i]) {
			widgets[i].next_widgets_height = next_widgets_height;
			widgets[i].fixed_margin_bottom += next_widgets_height;
			next_widgets_height += widgets[i].height;
			if ( !widget_parent_container ) {
				widget_parent_container = widget_obj.parent();
				widget_parent_container.addClass('q2w3-fixed-widget-container');
				widget_parent_container.css('height','');
				widget_parent_container.height(widget_parent_container.height());
			}
		}
	}
	
	jQuery(window).off('scroll.' + options.sidebar); 
	
	for ( var i = 0; i < widgets.length; i++ ) {
		if (widgets[i]) fixed_widget(widgets[i]);
	}
	
	function fixed_widget(widget) {
		
		//console.log('fixed_widget call: ' + widget.obj.attr('id'));
		
		var trigger_top = widget.offset_top - widget.fixed_margin_top;
		var trigger_bottom = document_height - options.margin_bottom;

		if ( options.stop_id && jQuery('#' + options.stop_id).length ) {
            trigger_bottom = jQuery('#' + options.stop_id).offset().top - options.margin_bottom;
        }

		var widget_width; if ( options.width_inherit ) widget_width = 'inherit'; else widget_width = widget.obj.css('width');
		
		var style_applied_top = false;
		var style_applied_bottom = false;
		var style_applied_normal = false;
		
		jQuery(window).on('scroll.' + options.sidebar, function(event) {
			if ( jQuery(window).width() <= options.screen_max_width || jQuery(window).height() <= options.screen_max_height ) {
				if ( ! style_applied_normal ) { 
					widget.obj.css('position', '');
					widget.obj.css('top', '');
					widget.obj.css('bottom', '');
					widget.obj.css('width', '');
					widget.obj.css('margin', '');
					widget.obj.css('padding', '');
					widget_obj.parent().css('height','');
					if ( jQuery('#'+widget.clone_id).length > 0 ) jQuery('#'+widget.clone_id).remove();
					style_applied_normal = true;
					style_applied_top = false;
					style_applied_bottom = false;		
				}
				//console.log('jQuery(window).width() <= options.screen_max_width');
			} else {
				var scroll = jQuery(this).scrollTop();
				//console.log('Srcoll: ' + scroll + ' | Trigger top: ' + trigger_top + ' | Trigger bottom: ' + trigger_bottom);
				if ( scroll + widget.fixed_margin_bottom >= trigger_bottom ) { // fixed bottom
					if ( !style_applied_bottom ) {
						widget.obj.css('position', 'fixed');
						widget.obj.css('top', '');
						widget.obj.css('width', widget_width);
						if(jQuery('#'+widget.clone_id).length <= 0) widget.obj.before(widget.clone);
						style_applied_bottom = true;
						style_applied_top = false;
						style_applied_normal = false;
					}
					widget.obj.css('bottom', scroll + window_height + widget.next_widgets_height - trigger_bottom);
				} else if ( scroll >= trigger_top ) { // fixed top
					if ( !style_applied_top ) {
						widget.obj.css('position', 'fixed');
						widget.obj.css('top', widget.fixed_margin_top);
						widget.obj.css('bottom', '');
						widget.obj.css('width', widget_width);
						if(jQuery('#'+widget.clone_id).length <= 0) widget.obj.before(widget.clone);
						style_applied_top = true;
						style_applied_bottom = false;
						style_applied_normal = false;
					}
				} else { // normal
					if ( !style_applied_normal ) {
						widget.obj.css('position', '');
						widget.obj.css('top', '');
						widget.obj.css('bottom', '');
						widget.obj.css('width', '');
						if(jQuery('#'+widget.clone_id).length > 0) jQuery('#'+widget.clone_id).remove();
						style_applied_normal = true;
						style_applied_top = false;
						style_applied_bottom = false;
					}
				}
			}
		}).trigger('scroll.' + options.sidebar);
		
	}	
	
}
