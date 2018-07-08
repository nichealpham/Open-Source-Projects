jQuery("document").ready(function($){
	if(typeof jQuery('.select-theme-option').select2 === 'function') {
        jQuery('.select-theme-option').select2();
	}
	jQuery('.d_update_featured_action').on('click', function(){
		var _this = jQuery(this);
		jQuery.ajax({
			type: 'post',
			url: ajaxurl,
			data: {
				action: _this.data('action'),
				id: _this.data('id'),
				val: _this.data('val')
			},
			beforeSend: function(){
				_this.addClass('fa-spin');
			},
			success: function(o){
				_this.data('val',o);
				_this.removeClass('fa-spin');
				if(o == 0) {
					_this.removeClass('fa-star-o').addClass('fa-star');
				} else {
					_this.removeClass('fa-star').addClass('fa-star-o');
				}
			}
		});
	});

	if($('div.acf-field[data-name="style"] input,div.acf-field[data-name="style"] select').length) {
		console.log($('div.acf-field[data-name="style"] input:checked,div.acf-field[data-name="style"] select').val());
		if($('div.acf-field[data-name="style"] input:checked,div.acf-field[data-name="style"] select').val() == "article-shoot") {
            $('#postdivrich').hide();
        }
        else {
            $('#postdivrich').show();
        }
        $(document).on('change', 'div.acf-field[data-name="style"] input,div.acf-field[data-name="style"] select', function () {
            if ($(this).val() == "article-shoot") {
                $('#postdivrich').hide();
            }
            else {
                $('#postdivrich').show();
            }
        });
    }
});