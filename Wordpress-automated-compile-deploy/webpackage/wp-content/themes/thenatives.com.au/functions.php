<?php
require_once get_template_directory()."/framework/abstract.php";
$theme = new Thenatives(array(
    'theme_name'	=>	"Thenatives",
    'theme_slug'	=>	'thenatives'
));
$theme->init();
require_once ('admin/index.php');

add_filter("gform_confirmation_anchor", create_function("","return false;"));

/** REMOVE DEFAULT IMAGE SIZE **/
add_filter('intermediate_image_sizes_advanced', 'hero_remove_default_image_sizes');
function hero_remove_default_image_sizes( $sizes) {
    //unset( $sizes['thumbnail']);
    unset( $sizes['medium']);
    unset( $sizes['large']);
    unset( $sizes['medium_large']);
    return $sizes;
}

add_action('add_meta_boxes', 'boot_add_post_meta');
function boot_add_post_meta() {
    add_meta_box('Option', 'Option', 'boot_show_post_meta', 'post');
}
function boot_show_post_meta() {
    global $post;
    echo '<input type="hidden" name="boot_custom_meta_box_nonce" value= "' . wp_create_nonce(basename(__FILE__)) . '"/>';
    ?>
    <div class="option">
        <input name="_fgd2wp_old_node_id" value="<?php echo get_post_meta($post->ID,'_fgd2wp_old_node_id',true)?>" />
        <textarea name="gallery_url" style=" width:100%; height:300px;"><?php echo get_post_meta($post->ID,'gallery_url',true)?></textarea>
        <textarea name="video_url" style=" width:100%; height:300px;"><?php echo get_post_meta($post->ID,'video_url',true)?></textarea>
        <?php
        ?>
    </div>
    <?php
}

add_action('save_post', 'boot_save_custom_meta_box');
function boot_save_custom_meta_box($post_id) {
    global $custom_meta_fields;
    // verify nonce
    if(isset($_POST['boot_custom_meta_box_nonce'])){
        if (!wp_verify_nonce($_POST['boot_custom_meta_box_nonce'], basename(__FILE__)))
            return $post_id;
        $metas=array('node_id','gallery_url');
        foreach($metas as $meta){
            update_post_meta($post_id, $meta, $_POST[$meta]);
        }

    }
}

function delete_associated_media($id) {
    // check if page
    if ('post' == get_post_type($id)) {
        $media = get_children(array(
            'post_parent'   => $id,
            'post_type'     => 'attachment'
        ));
        if (!empty($media)) {
            foreach ($media as $file) {
                // pick what you want to do
                //unlink(get_attached_file($file->ID));
                wp_delete_attachment($file->ID);
            }
        }
    }
}
add_action('before_delete_post', 'delete_associated_media');


function get_attach_thumb_id_2($imageUrl, $post_id) {
    // Get the file name
    $item_date=(get_the_date('Y-m-d H:i:s',$post_id ));
    $filename = substr($imageUrl, (strrpos($imageUrl, '/'))+1);
    if (!(($uploads = wp_upload_dir($item_date) ) && false === $uploads['error'])) {
        return null;
    }
    // Generate unique file name
    $filename = wp_unique_filename( $uploads['path'], $filename );
    // Move the file to the uploads dir
    $new_file = $uploads['path'] . "/$filename";
    if (!ini_get('allow_url_fopen')) {
        $file_data = curl_get_file_contents($imageUrl);
    } else {
        $file_data = @file_get_contents($imageUrl);
    }
    if (!$file_data) {
        return null;
    }
    file_put_contents($new_file, $file_data);
    // Set correct file permissions
    $stat = stat( dirname( $new_file ));
    $perms = $stat['mode'] & 0000666;
    @ chmod( $new_file, $perms );
    // Get the file type. Must to use it as a post thumbnail.
    $wp_filetype = wp_check_filetype( $filename, $mimes=null );
    extract( $wp_filetype );
    // No file type! No point to proceed further
    if ( ( !$type || !$ext ) && !current_user_can( 'unfiltered_upload' ) ) {
        return null;
    }
    // Compute the URL
    $url = $uploads['url'] . "/$filename";
    // Construct the attachment array
    $attachment = array(
        'post_mime_type' => $type,
        'guid' => $url,
        'post_parent' => null,
        'post_title' => '',
        'post_content' => '',
    );
    $thumb_id = wp_insert_attachment($attachment, $filename, $post_id);
    if ( !is_wp_error($thumb_id) ) {
        require_once(ABSPATH . '/wp-admin/includes/image.php');

        // Added fix by misthero as suggested
        wp_update_attachment_metadata( $thumb_id, wp_generate_attachment_metadata( $thumb_id, $new_file ) );
        update_attached_file( $thumb_id, $new_file );
        return $thumb_id;
    }
    return null;

}
add_action('wp_enqueue_scripts', 'hr_frontend_script');
function hr_frontend_script(){

    wp_enqueue_script('labory', get_template_directory_uri().'/js/labory.js');
    wp_localize_script('labory', 'hr', array('p_url' => get_template_directory_uri(),'a_url'=>admin_url('admin-ajax.php')));

}

function get_count_gal($paged){
    $count=0;
    $arrs = get_posts(
        array(
            'post_type'=>'post',
            'posts_per_page'=>500,
            'paged'=>$paged,
            'meta_query' => array(
                array(
                    'key' => 'gallery_url',
                    'value'=>'[]',
                    'compare' => '!=',
                )
            )
        )
    );
    foreach($arrs as $arr){
        $thumb=get_post_meta($arr->ID,'_thumbnail_id',true);
        if(!$thumb){ $count++; }
    }
    return $count;
}

function get_post_gal($paged){
    $arrs = get_posts(
        array(
            'post_type'=>'post',
            'posts_per_page'=>500,
            'paged'=>$paged,
            'meta_query' => array(
                array(
                    'key' => 'gallery_url',
                    'value'=>'[]',
                    'compare' => '!=',
                )
            )
        )
    );
    foreach($arrs as $arr){
        $thumb=get_post_meta($arr->ID,'_thumbnail_id',true);
        if(!$thumb){ return $arr->ID; }
    }
    return 0;
}

add_action('wp_ajax_hr_load_gallery','hr_load_gallery');
add_action('wp_ajax_nopriv_hr_load_gallery','hr_load_gallery');
function hr_load_gallery(){
    $arrid=get_post_gal($_POST['page']);
    if($arrid){
        $akks=(get_post_meta($arrid,'gallery_url',true))?json_decode(get_post_meta($arrid,'gallery_url',true)):array();
        if(!empty($akks)){
            foreach($akks as $num=>$akk){
                $akk=str_replace(' ','%20',$akk);
                $thum_id=get_attach_thumb_id_2($akk,$arrid);
                if($thum_id){
                    if($num==0){
                        update_post_meta($arrid, '_thumbnail_id', $thum_id );
                    }
                    $row = array(
                        'image'	=> $thum_id,
                    );
                    update_row('slider',($num+1),$row,$arrid);
                }
            }
            if(!get_post_meta($arrid, '_thumbnail_id',true )){
                $thum_id=get_attach_thumb_id_2('http://fashionjournal.com.au/sites/default/files/FJ_header_logo_0.png',$arrid);
                update_post_meta($arrid, '_thumbnail_id', $thum_id );
                update_post_meta($arrid, '_thumbnail_id_', 1);
            }
        }
    }

    $arrid=get_post_gal($_POST['page']);
    if($arrid){
        $pst=get_post($arrid);
        ?>
        <textarea id="title_append"><?php echo htmlspecialchars($pst->post_title, ENT_QUOTES); ?></textarea>
        <script>
            jQuery(".list_process").prepend('<p>'+jQuery("#title_append").val()+'</p>');
            jQuery(".count_process").html('<?php echo get_count_gal($_POST['page']) ?>');
            postbyurl('hide_me','<?php echo admin_url('admin-ajax.php?action=hr_load_gallery') ?>','page=<?php echo $_POST['page']; ?>');
        </script>
        <?php
    }
    else{
        ?>
        <script>
            jQuery(".count_process").html('0');
            jQuery(".list_process").prepend('<p>Complete</p>');
        </script>
        <?php
    }
    exit;
}
add_action('wp_ajax_modified_advertise','modified_advertise');
add_action('wp_ajax_nopriv_modified_advertise','modified_advertise');
function modified_advertise() {
    $args = array(
        'post_type'=> array('post','sale','event','career'),
        'post_status'=>"publish",
        'posts_per_page'=>1,
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_query' => array(
            array(
                'key' => 'modified_advertise_post',
                'compare' => 'NOT EXISTS'
            ),
        ),
    );
    $the_query = new WP_Query($args);
    if($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            the_title();
//            update_field('advertise_top','2236');
//            update_field('advertise_bottom','141');
//            update_field('advertise_right','627');
            //update_post_meta( get_the_ID(), 'modified_advertise_post', 1 );
        }
    }
    die();
}

add_action('wp_ajax_create_thumbnail_image','create_thumbnail_image');
add_action('wp_ajax_nopriv_create_thumbnail_image','create_thumbnail_image');
function create_thumbnail_image() {
    $args = array(
        'post_type'=>array('post'),
        'post_status'=>"publish",
        'orderby' => 'date',
        'posts_per_page' => 50,
        'order' => 'DESC',
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'update_image_thumbnail',
                'compare' => 'NOT EXISTS'
            ),
            array(
                'key' => 'update_image_thumbnail',
                'value' => '2',
                'compare' => '!='
            ),
        ),
    );
    $the_query = new WP_Query($args);
    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            $the_query->the_post();
            $attach_id = get_post_thumbnail_id();
            $meta = wp_get_attachment_metadata($attach_id);
            $file = substr(strrchr($meta['file'], '/'), 1);
            $type = substr(strrchr($file, '.'), 1);
            $name = str_replace(".{$type}",'',$file);
            the_title();
            echo '<br>';
            if(strtolower($type) == 'png' || strtolower($type) == 'jpg' || strtolower($type) == 'jpeg') {

                $path = wp_upload_dir();
                $path_url = $path['baseurl'];
                $path = $path['basedir'];
                $folder = '';
                $arr = explode('/', $meta['file']);
                $count = 1;
                foreach ($arr as $item) {
                    if ($count >= count($arr)) {
                        break;
                    }
                    $folder .= '/' . $item;
                    $count++;
                }
                $path .= $folder;
                $path_url .= $folder;
                $url = "{$path}/{$name}.{$type}";
                $new_image = '';
                if (file_exists($url)) {
                    $filetype = "image/" . ((strtolower($type) == 'png') ? 'png' : 'jpg');

                    //Small Image
                    $width = 198;
                    $height = 274;
                    if ($meta['width'] > $width || $meta['height'] > $height) {
                        $filename = "{$name}-{$width}x{$height}.{$type}";
                        $meta['sizes']['small'] = array(
                            'file' => $filename,
                            'width' => $width,
                            'height' => $height,
                            'mime-type' => $filetype,
                        );
                        $new_file = "{$path}/$filename";
                        $resizeObj = new Optimizer($url);
                        $resizeObj->resizeImage($width, $height, "crop");
                        $resizeObj->saveImage($new_file, 80);
                        $new_image .= $path_url.'/'.$filename.'<br>';

                        //Medium Image
                        $width = 304;
                        $height = 421;
                        if ($meta['width'] > $width || $meta['height'] > $height) {
                            $filename = "{$name}-{$width}x{$height}.{$type}";
                            $meta['sizes']['medium'] = array(
                                'file' => $filename,
                                'width' => $width,
                                'height' => $height,
                                'mime-type' => $filetype,
                            );
                            $new_file = "{$path}/$filename";
                            $resizeObj->resizeImage($width, $height, "crop");
                            $resizeObj->saveImage($new_file, 80);
                            $new_image .= $path_url.'/'.$filename.'<br>';

                            //Large Image
                            $width = 515;
                            $height = 714;
                            if ($meta['width'] > $width || $meta['height'] > $height) {
                                $filename = "{$name}-{$width}x{$height}.{$type}";
                                $meta['sizes']['large'] = array(
                                    'file' => $filename,
                                    'width' => $width,
                                    'height' => $height,
                                    'mime-type' => $filetype,
                                );
                                $new_file = "{$path}/$filename";
                                $resizeObj->resizeImage($width, $height, "crop");
                                $resizeObj->saveImage($new_file, 80);
                                $new_image .= $path_url.'/'.$filename.'<br>';
                            }
                        }
                    }
                }
                echo $new_image.'<br>';
                wp_update_attachment_metadata( $attach_id, $meta );
            }
            update_post_meta( get_the_ID(), 'update_image_thumbnail', 2 );
        }
    }
    die();
}
?>
