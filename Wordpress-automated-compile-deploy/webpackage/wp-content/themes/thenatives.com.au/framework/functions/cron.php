<?php
add_filter( 'cron_schedules', 'thenatives_cron_schedule' );
function thenatives_cron_schedule( $schedules ) {
    $schedules['minutely'] = array(
        'interval' => 60, // Every 6 hours
        'display'  => __( 'Minutely' ),
    );
    return $schedules;
}

add_action('thenative_update_job','thenative_update_job');
function thenative_update_job(){
    $args = array(
        'post_type'				=> 'career',
        'post_status'			=> 'publish',
        'posts_per_page' 		=> '-1',
        'orderby' 				=> 'taxonomy.career-packages',
        'ordertax' 				=> 'DESC',
        'order'                 => 'DESC',
    );
    $the_query = new WP_Query( $args );
    $check = false;
    $packages = get_terms( array(
        'taxonomy' => 'career-packages',
        'hide_empty' => false,
    ));
    if($the_query->have_posts()){
        while ($the_query->have_posts()){
            $the_query->the_post();
            $package = wp_get_post_terms( get_the_ID(), 'career-packages' );
            $package = $package[0];
            if(get_field('days',get_post_type().'-packages_'.$package->term_id)){
                $date = date('Y/m/d',strtotime(get_the_date('Y/m/d').' + '.intval(get_field('days',get_post_type().'-packages_'.$package->term_id)). ' days'));
                if(strtotime($date) <  strtotime(date('Y/m/d'))){
                    update_field('priority',$package->term_id);
                    wp_set_object_terms( get_the_ID(), $packages[0]->term_id, 'career-packages' );
                }
            }
        }
    }
    wp_reset_postdata();
}

if ( ! wp_next_scheduled( 'thenative_update_job' ) ) {
    wp_schedule_event( time(), 'twicedaily', 'thenative_update_job' );
}