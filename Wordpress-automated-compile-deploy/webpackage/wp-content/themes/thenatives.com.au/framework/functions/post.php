<?php
function check_related_post(){
    $category = get_the_category();
    $args = array(
        'post_type'				=> get_post_type(),
        'post_status'			=> 'publish',
        'posts_per_page' 		=> 6,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
        'post__not_in'          => array(get_the_ID()),
        'cat'                   => $category[0]->term_id,
    );
    return thenative_posts_count($args);
}

function total_related_post(){
    $category = get_the_category();
    $args = array(
        'post_type'				=> get_post_type(),
        'post_status'			=> 'publish',
        'posts_per_page' 		=> -1,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
        'post__not_in'          => array(get_the_ID()),
        'cat'                   => $category[0]->term_id,
        'count'                 => 1,
    );
    return thenative_posts_count($args);
}

function check_related_career(){
    $p = get_post();
    $args = array(
        'post_type'				=> get_post_type(),
        'post_status'			=> 'publish',
        'posts_per_page' 		=> 6,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
        'post__not_in'          => array(get_the_ID()),
        'author'                => $p->post_author,
    );
    $the_query = new WP_Query( $args );
    if(count($the_query->posts)){
        return count($the_query->posts);
    }
    return thenative_posts_count($args);
}

function total_related_career(){
    $p = get_post();
    $args = array(
        'post_type'				=> get_post_type(),
        'post_status'			=> 'publish',
        'posts_per_page' 		=> -1,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
        'post__not_in'          => array(get_the_ID()),
        'author'                => $p->post_author,
    );
    return thenative_posts_count($args);
}

function check_related_event(){
    $p = get_post();
    $args = array(
        'post_type'				=> get_post_type(),
        'post_status'			=> 'publish',
        'posts_per_page' 		=> 6,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
        'post__not_in'          => array(get_the_ID()),
        'author'                => $p->post_author,
    );
    return thenative_posts_count($args);
}

function total_related_event(){
    $p = get_post();
    $args = array(
        'post_type'				=> get_post_type(),
        'post_status'			=> 'publish',
        'posts_per_page' 		=> -1,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
        'post__not_in'          => array(get_the_ID()),
        'author'                => $p->post_author,
    );
    return thenative_posts_count($args);
}

function check_related_sale(){
    $p = get_post();
    $args = array(
        'post_type'				=> get_post_type(),
        'post_status'			=> 'publish',
        'posts_per_page' 		=> 6,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
        'post__not_in'          => array(get_the_ID()),
        'author'                => $p->post_author,
    );
    return thenative_posts_count($args);
}

function total_related_sale(){
    $p = get_post();
    $args = array(
        'post_type'				=> get_post_type(),
        'post_status'			=> 'publish',
        'posts_per_page' 		=> -1,
        'orderby' 				=> 'date',
        'order'                 => 'DESC',
        'post__not_in'          => array(get_the_ID()),
        'author'                => $p->post_author,
    );
    return thenative_posts_count($args);
}

add_shortcode('show_related_post','show_related_post');
function show_related_post($atts,$content) {
    extract(shortcode_atts(array(
        'id' => '',
        'align' => 'left',
    ),$atts,'show_related_post'));

    $args = array(
        'post_type'				=> 'post',
        'post_status'			=> 'publish',
        'posts_per_page' 		=> 1,
        'orderby' 				=> 'date',
        'order'                 => 'DESC'
    );
    if($id){
        $args['p'] = $id;
    }

    $the_query = new WP_Query($args);
    ob_start();
    ?>
    <?php if($the_query->have_posts()): ?>
        <?php while($the_query->have_posts()): $the_query->the_post(); ?>
            <?php if(has_post_thumbnail()): ?>
                <?php $align = ($align=='right')?'right':'left'; ?>
                <?php $margin = ($align=='right')?'margin-left':'margin-right'; ?>
                <div class="relatedPostInContent hrefThumbnails" style="float: <?php echo $align; ?>;<?php echo $margin; ?>: 20px;margin-top: 10px; width: 198px">
                    <a class="imageThumbnails"href="<?php the_permalink(); ?>">
                        <figure class="imageThumbnail">
                            <?php the_post_thumbnail(); ?>
                        </figure>
                        <div class="boxContain contentThumbnails-<?php echo (get_field('position'))?ucfirst(get_field('position')):'Top'; ?>">
                            <div class="innerBoxContain textBottom containerText">
                                <span class="ReLaTeD">related</span>
                                <p class="size-other"<?php echo get_field('font_post')?(' style="font-family: '.str_replace('-Black','-Regular',get_field('font_post'))):'' ?>"><?php the_title(); ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
        <?php endwhile; ?>
    <?php endif; ?>
    <?php
    $html = ob_get_contents();
    ob_end_clean();
    wp_reset_postdata();
    return $html;
}


add_filter( 'posts_clauses', 'thenatives_career_clauses', 10, 2 );
function thenatives_career_clauses( $clauses, $wp_query ) {
    if($wp_query->get('post_type') == 'career'){
        $orderby_arg = $wp_query->get('orderby');
        if ( ! empty( $orderby_arg ) && substr_count( $orderby_arg, 'taxonomy.' ) ) {
            global $wpdb;
            $bytax = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC)";
            $array = explode( ' ', $orderby_arg );
            if ( ! isset( $array[1] ) ) {
                $array = array( $bytax, "{$wpdb->posts}.post_date" );
                $taxonomy = str_replace( 'taxonomy.', '', $orderby_arg );
            }
            else {
                foreach ( $array as $i => $t ) {
                    if ( substr_count( $t, 'taxonomy.' ) )  {
                        $taxonomy = str_replace( 'taxonomy.', '', $t );
                        $array[$i] = $bytax;
                    } elseif ( $t === 'meta_value' || $t === 'meta_value_num' ) {
                        $cast = ( $t === 'meta_value_num' ) ? 'SIGNED' : 'CHAR';
                        $array[$i] = "CAST( {$wpdb->postmeta}.meta_value AS {$cast} )";
                    } else {
                        $array[$i] = "{$wpdb->posts}.{$t}";
                    }
                }
            }
            $order = strtoupper( $wp_query->get('order') ) === 'ASC' ? ' ASC' : ' DESC';
            $ot = strtoupper( $wp_query->get('ordertax') );
            $ordertax = $ot === 'DESC' || $ot === 'ASC' ? " $ot" : " $order";
            $clauses['orderby'] = implode(', ',
                array_map(function ($a) use ($ordertax, $order) {
                    return (strpos($a, 'GROUP_CONCAT') === 0) ? $a . $ordertax : $a . $order;
                }, $array)
            );
            $clauses['join'] = " LEFT JOIN {$wpdb->term_relationships} ";
            $clauses['join'] .= "ON {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id";
            $clauses['join'] .= " LEFT JOIN {$wpdb->term_taxonomy} ";
            $clauses['join'] .= "USING (term_taxonomy_id)";
            $clauses['join'] .= " LEFT JOIN {$wpdb->terms} USING (term_id)";
            $clauses['groupby'] = "object_id";
            if($wp_query->get('tax_query')) {
                $where = explode('AND', $clauses['where']);
                $clauses['where'] = '';
                foreach ($where as $key => $item) {
                    if (!strpos($item, 'term_taxonomy_id')) {
                        if ($key) {
                            $clauses['where'] .= 'AND';
                        }
                        $clauses['where'] .= $item;
                    }
                }
                $subQuery = "SELECT id ";
                $subQuery.= "FROM {$wpdb->posts} ";
                $join = '';
                $where = ' AND (';
                $operator = '';
                foreach ($wp_query->get('tax_query') as $key=>$tax){
                    if(is_array($tax)){
                        $i = $key+1;
                        $join .= " LEFT JOIN {$wpdb->term_relationships} as tt{$i} ON tt{$i}.object_id = {$wpdb->posts}.ID";
                        if($key){
                            $where.= $operator;
                        }
                        $where .= "tt{$i}.term_taxonomy_id = ".$tax['terms'];
                    }
                    else {
                        $operator = ' '.$tax.' ';
                    }
                }
                $where = " WHERE (1) {$clauses['where']}{$where})";
                $subQuery .= $join.$where." GROUP BY {$wpdb->posts}.ID";
                $clauses['where'] .= " AND {$wpdb->posts}.ID IN ({$subQuery})";
            }
            $clauses['where'] .= " AND (taxonomy = '{$taxonomy}' OR taxonomy IS NULL)";
        }
    }
    return $clauses;
}

add_filter( 'posts_clauses', 'thenatives_count_clauses', 10, 2 );
function thenatives_count_clauses( $clauses, $wp_query ) {
    if($wp_query->get('count')){
        global $wpdb;
        $clauses['fields'] = "{$wpdb->posts}.ID,count(DISTINCT {$wpdb->posts}.ID) as count";
        $clauses['groupby'] = "";
    }
    return $clauses;
}
function thenative_posts_count($args) {
    global $wpdb;
    $args['count'] = 1;
    $the_query = new WP_Query( $args );
    $sql = "SELECT count(*) as count from (".str_replace("ORDER BY","GROUP BY {$wpdb->posts}.ID ORDER BY",str_replace("SQL_CALC_FOUND_ROWS ",'',$the_query->request)).") as sub";
    $row = $wpdb->get_row( $sql );
    if($row){
        return $row->count;
    }
    return 0;
}