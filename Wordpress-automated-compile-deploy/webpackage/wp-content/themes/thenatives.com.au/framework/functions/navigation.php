<?php
add_filter('wp_nav_menu_objects', 'ad_filter_category_menu', 10, 2);
function ad_filter_category_menu($sorted_menu_objects, $args) {
    foreach ($sorted_menu_objects as $menu_object) {
        if(get_field('background_color',$menu_object) && get_field('text',$menu_object)){
            $data_bg = ' data-bg="'.get_field('background_color',$menu_object).'"';
            $data_color = ' data-color="'.get_field('text',$menu_object).'"';
            $menu_object->title = '<span'.$data_bg.$data_color.'><span class="before"></span>' . $menu_object->title . '<span class="after"></span></span>';
        }
        else {
            $menu_object->title = '<span>'.$menu_object->title.'</span>';
        }
    }
    return $sorted_menu_objects;
}