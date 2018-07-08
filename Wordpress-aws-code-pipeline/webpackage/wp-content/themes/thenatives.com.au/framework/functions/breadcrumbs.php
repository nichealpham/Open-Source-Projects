<?php
if(!function_exists('thenatives_breadcrumbs')){
    function thenatives_breadcrumbs() {
        wp_reset_query();
        $home = __('Home','thenatives'); // text for the 'Home' link
        $before = '<li class="breadcrumb-item active">'; // tag before the current crumb
        $after = '</li>'; // tag after the current crumb
        global $wp_rewrite;
        $rewriteUrl = $wp_rewrite->using_permalinks();
        if ( !is_home() && !is_front_page() || is_paged() ) {

            echo '<div id="crumbs" class="headBreadcrumb"><div class="container"><ol class="breadcrumb">';

            global $post;
            $homeLink = home_url(); //get_bloginfo('url');

            echo '<li class="breadcrumb-item"><a href="' . $homeLink . '">' . $home . '</a></li>';

            if ( is_category() ) {

                global $wp_query;
                $cat_obj = $wp_query->get_queried_object();
                $thisCat = $cat_obj->term_id;
                $thisCat = get_category($thisCat);
                $catList = array();
                if($thisCat->category_parent) {
                    $parent_cat = get_category($thisCat->category_parent);
                    $catList[] = $parent_cat;
                    while ($parent_cat->category_parent) {
                        $parent_cat = get_category($parent_cat->category_parent);
                        $catList[] = $parent_cat;
                    }
                }
                for($i = (count($catList)-1); $i>=0; $i--){
                    echo '<li class="breadcrumb-item"><a href="'.get_category_link($catList[$i]).'">'.$catList[$i]->name.'</a></li>';
                    break;
                }
                echo $before . single_cat_title('', false) . $after;
            } elseif ( is_search() ) {
                echo $before . __('Search results for "','thenatives') . get_search_query() . '"' . $after;
            } elseif ( is_day() ) {
                echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>';
                echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ';
                echo $before . get_the_time('d') . $after;
            } elseif ( is_month() ) {
                echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ';
                echo $before . get_the_time('F') . $after;

            } elseif ( is_year() ) {
                echo $before . get_the_time('Y') . $after;
            } elseif ( is_single() && !is_attachment() ) {
                if ( get_post_type() != 'post' ) {
                    $post_type = get_post_type_object(get_post_type());
                    $slug = $post_type->rewrite;
                    $post_type_name = $post_type->labels->singular_name;
                    if(strcmp('Portfolio Item',$post_type->labels->singular_name)==0){
                        $post_type_name = __('Portfolio','thenatives');
                    }
                    if(strcmp('Product',$post_type->labels->singular_name)==0){
                        $post_type_name = __('Shop','thenatives');
                    }
                    if($rewriteUrl){
                        echo '<li class="breadcrumb-item"><a href="' . $homeLink .'/' . $slug['slug'] . '/">' . $post_type_name . '</a></li>' /*. $delimiter . ' '*/;
                    }else{
                        echo '<li class="breadcrumb-item"><a href="' . $homeLink . '/?post_type=' . get_post_type() . '">' . $post_type_name . '</a></li>' /*. $delimiter . ' '*/;
                    }
                } else {
                    $cat = get_the_category();
                    $cat = $cat[0];
                    $catList = array($cat);
                    if($cat->category_parent) {
                        $parent_cat = get_category($cat->category_parent);
                        $catList[] = $parent_cat;
                        while ($parent_cat->category_parent) {
                            $parent_cat = get_category($parent_cat->category_parent);
                            $catList[] = $parent_cat;
                        }
                    }
                    for($i = (count($catList)-1); $i>=0; $i--){
                        echo '<li class="breadcrumb-item"><a href="'.get_category_link($catList[$i]).'">'.$catList[$i]->name.'</a></li>';
                        break;
                    }
                }

            } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                $post_type_name = $post_type->labels->singular_name;

                if(strcmp('Portfolio Item',$post_type->labels->singular_name)==0){
                    $post_type_name = __('Portfolio','thenatives');
                }
                if(strcmp('Product',$post_type->labels->singular_name)==0){
                    $post_type_name = __('Shop','thenatives');
                }

                if ( is_tag() ) {
                    echo $before . __('Tagged "','thenatives') . single_tag_title('', false) . '"' . $after;
                } elseif(is_taxonomy_hierarchical(get_query_var('taxonomy'))){

                    if($rewriteUrl){
                        if( strpos( $slug['slug'], '/' ) == 0 ) $slug['slug'] = substr($slug['slug'], 1);
                        echo '<li class="breadcrumb-item"><a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type_name . '</a></li>';
                    }else{
                        echo '<li class="breadcrumb-item"><a href="' . $homeLink . '/?post_type=' . get_post_type() . '">' . $post_type_name . '</a></li>';
                    }

                    $curTaxanomy = get_query_var('taxonomy');
                    $curTerm = get_query_var( 'term' );

                    $termNow = get_term_by( "slug",$curTerm, $curTaxanomy);

                    $pushPrintArr = array();
                    while ((int)$termNow->parent != 0){

                        $parentTerm = get_term((int)$termNow->parent,get_query_var('taxonomy'));
                        array_push($pushPrintArr,'<a href="' . get_term_link((int)$parentTerm->term_id,$curTaxanomy) . '">' . $parentTerm->name . '</a> ');

                        $curTerm = $parentTerm->name;
                        $termNow = get_term_by( "slug",$curTerm, $curTaxanomy);
                    }
                    $pushPrintArr = array_reverse($pushPrintArr);

                    //array_push($pushPrintArr,$before  . get_query_var( 'term' ) . $after);
                    $cat_break_str = implode($pushPrintArr);
                    if($cat_break_str !== ''){
                        $cat_break_pos = strrpos($cat_break_str);
                        if(strlen($cat_break_str) <= absint($cat_break_pos) + 1) {
                            $cat_break_str = substr($cat_break_str, 0, $cat_break_pos);
                        }
                    } else {
                        $termNow = get_term_by( "slug",$curTerm, $curTaxanomy);
                        array_push($pushPrintArr,$before  . $termNow->name . $after);
                        $cat_break_str = implode($pushPrintArr);
                    }
                    echo $cat_break_str;

                }else{
                    echo $before . $post_type_name . $after;
                }

            } elseif ( is_attachment() ) {

                if( (int)$post->post_parent > 0 ){
                    $parent = get_post($post->post_parent);
                    $cat = get_the_category($parent->ID);
                    if( count($cat) > 0 ){
                        $cat = $cat[0];
                        echo get_category_parents($cat, TRUE, '');
                    }
                    echo '<li class="breadcrumb-item"><a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a></li>';
                }
                echo $before . get_the_title() . $after;
            } elseif ( is_page() && !$post->post_parent ) {
                echo $before . get_the_title() . $after;

            } elseif ( is_page() && $post->post_parent ) {
                $parent_id  = $post->post_parent;
                $breadcrumbs = array();
                while ($parent_id) {
                    $page = get_post($parent_id);
                    $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                    $parent_id  = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                foreach ($breadcrumbs as $crumb) echo $crumb;
                echo $before . get_the_title() . $after;

            } elseif ( is_tag() ) {
                echo $before . __('Tagged "','thenatives') . single_tag_title('', false) . '"' . $after;

            } elseif ( is_author() ) {
                global $author;
                $userdata = get_userdata($author);
                echo $before . __('Articles posted by ','thenatives') . $userdata->display_name . $after;

            } elseif ( is_404() ) {
                echo $before . __('Error 404','thenatives') . $after;
            }

            if ( get_query_var('paged') ) {
                if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_page_template() ||  is_post_type_archive() || is_archive() ) {
                    echo $before .' (';
                    echo __('Page','thenatives') . ' ' . get_query_var('paged');
                }
                if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_page_template() ||  is_post_type_archive() || is_archive() ) echo ')'. $after;
            } else{
                if ( get_query_var('page') ) {
                    if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_page_template() ||  is_post_type_archive() || is_archive() ) {
                        echo $before .' (';
                        echo __('Page','thenatives') . ' ' . get_query_var('page');
                    }
                    if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_page_template() ||  is_post_type_archive() || is_archive() ) echo ')'. $after;
                }
            }
            echo '</ol></div></div>';

        }
        wp_reset_query();
    } // end ew_breadcrumbs()
}

if(!function_exists("thenatives_show_breadcrumbs")){
    function thenatives_show_breadcrumbs(){ ?>
        <div class="top-page">
            <?php thenatives_breadcrumbs();?>
        </div>
        <?php
    }
}

add_action('theme_breadcrumbs','theme_breadcrumbs');
if (!function_exists('theme_breadcrumbs')) {
    function theme_breadcrumbs(){
        global $thenatives;
        //if(get_page_template_slug() != 'template-homepage.php' && (is_single() || !is_page())){
        if(is_single()){
            thenatives_breadcrumbs();
        }
    }
}
?>