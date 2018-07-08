jQuery(document).ready(function(){
	function media_upload( button_class ) {
        var _custom_media = true,
            _orig_send_attachment = wp.media.editor.send.attachment;
         jQuery('body').on('click','.custom_media_upload',function(e) {
            var button_id ='#'+jQuery(this).attr( 'id' );
            var button_id_s = jQuery(this).attr( 'id' ); 
            console.log(button_id); 
            var self = jQuery(button_id);
            var send_attachment_bkp = wp.media.editor.send.attachment;
            var button = jQuery(button_id);
            var id = button.attr( 'id' ).replace( '_button', '' );
            _custom_media = true;

            wp.media.editor.send.attachment = function(props, attachment ){
                if ( _custom_media ) {
                    jQuery( '.' + button_id_s + '_media_id' ).val(attachment.id); 
                    jQuery( '.' + button_id_s + '_media_url' ).val(attachment.url);
                    jQuery( '.' + button_id_s + '_media_image' ).attr( 'src',attachment.url).css( 'display','block' ); 
                } else {
                    return _orig_send_attachment.apply( button_id, [props, attachment] );
                }
            }
            wp.media.editor.open(button);
            return false;
        });
    }
    media_upload( '.custom_media_upload' );
	jQuery('.of-color').wpColorPicker();
});