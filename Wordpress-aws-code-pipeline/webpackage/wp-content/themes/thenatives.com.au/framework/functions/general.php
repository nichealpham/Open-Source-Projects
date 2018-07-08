<?php
if(!is_admin()) {
    show_admin_bar( false );
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'print_emoji_detection_script',7);
    remove_action('wp_print_styles','print_emoji_styles');
    remove_action('embed_head','wp_print_styles',20);
}

if(!function_exists ('thenatives_array_atts')){
    function thenatives_array_atts($pairs, $atts) {
        $atts = (array)$atts;
        $out = array();
        foreach($pairs as $name => $default) {
            if ( array_key_exists($name, $atts) ){
                if( strlen(trim($atts[$name])) > 0 ){
                    $out[$name] = $atts[$name];
                }else{
                    $out[$name] = $default;
                }
            }
            else{
                $out[$name] = $default;
            }
        }
        return $out;
    }
}

function thenatives_upload_media ($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'thenatives_upload_media');

if ( is_plugin_active( 'gravityforms/gravityforms.php') ) {
    add_filter('gform_init_scripts_footer', '__return_true');
    add_filter('gform_cdata_open', 'wrap_gform_cdata_open');
    function wrap_gform_cdata_open($content = '')
    {
        $content = 'document.addEventListener( "DOMContentLoaded", function() { ';
        return $content;
    }

    add_filter('gform_cdata_close', 'wrap_gform_cdata_close');
    function wrap_gform_cdata_close($content = '')
    {
        $content = ' }, false );';
        return $content;
    }

    add_action('wp_enqueue_scripts', function () {
        if (function_exists('gravity_form_enqueue_scripts')) {
            // newsletter subscription form
            gravity_form_enqueue_scripts(5);
        }
    });
}

add_action('thenative_before_body','add_script_calendar');
function add_script_calendar() {
    if (is_single()) {
        if (get_post_type() == 'event' || get_post_type() == 'sale' ) {
            ?>
            <script type="text/javascript">(function () {
                    if (window.addtocalendar)if(typeof window.addtocalendar.start == "function")return;
                    if (window.ifaddtocalendar == undefined) { window.ifaddtocalendar = 1;
                        var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
                        s.type = 'text/javascript';s.charset = 'UTF-8';s.async = true;
                        s.src = ('https:' == window.location.protocol ? 'https' : 'http')+'://addtocalendar.com/atc/1.5/atc.min.js';
                        var h = d[g]('body')[0];h.appendChild(s); }})();
            </script>
            <?php
        }
    }
}

function timezone_list() {
    $zones = timezone_identifiers_list();
    foreach ($zones as $zone)
    {
        $zone = explode('/', $zone); // 0 => Continent, 1 => City

        // Only use "friendly" continent names
        if ($zone[0] == 'Africa' || $zone[0] == 'America' || $zone[0] == 'Antarctica' || $zone[0] == 'Arctic' || $zone[0] == 'Asia' || $zone[0] == 'Atlantic' || $zone[0] == 'Australia' || $zone[0] == 'Europe' || $zone[0] == 'Indian' || $zone[0] == 'Pacific')
        {
            if (isset($zone[1]) != '')
            {
                $locations[$zone[0]][$zone[0]. '/' . $zone[1]] = str_replace('_', ' ', $zone[1]); // Creates array(DateTimeZone => 'Friendly name')
            }
        }
    }
    $arrList = array();
    foreach($locations as $abbr => $timezone){
        foreach($timezone as $val){
            $arrList[] = $abbr.'/'.$val;
        }
    }
    return $arrList;
}