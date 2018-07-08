<?php
/*
 * Template Name: Test
 */
?>

<?php wp_head();?>
<?php
$args = array(
    'post_type'=>array('post'),
    'post_status'=>"publish",
    'orderby' => 'date',
    'posts_per_page' => -1,
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
$total = thenative_posts_count($args);
?>
    <div class="counter"><span>0</span>/<span><?php echo $total; ?></span></div>
    <script>
        jQuery(document).ready(function($){
            modified_taxonomy_ajax();
            function modified_taxonomy_ajax() {
                $.ajax({
                    url: "<?php echo admin_url( 'admin-ajax.php' )?>",
                    type: 'post',
                    data: {
                        action: 'create_thumbnail_image'
                    },
                    beforeSend: function () {
                    },
                    success: function (html) {
                        $('.counter span:first-child').text(parseInt($('.counter span:first-child').text())+50);
                        $('body').append(html);
                        if(parseInt($('.counter span:first-child').text()) < parseInt($('.counter span:last-child').text())) {
                            modified_taxonomy_ajax();
                        }
                    }
                });
            }
        });
    </script>
<?php wp_footer();?>