<?php
add_action( 'init', 'thenatives_buttons' );
function thenatives_buttons() {
    add_filter( "mce_external_plugins", "thenatives_add_buttons" );
    add_filter( 'mce_buttons', 'thenatives_register_buttons' );
}
function thenatives_add_buttons( $plugin_array ) {
    $plugin_array['thenatives'] = get_template_directory_uri() . '/admin/assets/js/editor.js';
    return $plugin_array;
}
function thenatives_register_buttons( $buttons ) {
    array_push( $buttons, 'show_related_post' );
    return $buttons;
}