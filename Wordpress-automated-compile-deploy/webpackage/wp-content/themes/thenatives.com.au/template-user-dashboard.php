<?php
/*
 * Template Name: User Dashboard
 */
?>
<?php get_header(); ?>
<?php global $thenatives; ?>
<section class="advertiser-dashboard">
    <div class="container">
        <div class="relatedSettings">
            <button class="btnNew">
                <div>NEW <span class="pull-right">+</span></div>
                <ul class="addNewList" style="display: none;">
                    <li><a href="<?php the_post_job_link(); ?>">Post A Job</a></li>
                    <li><a href="<?php the_post_event_link(); ?>">Post An Event</a></li>
                    <li><a href="<?php the_post_sale_link(); ?>">Post A Sale</a></li>
                </ul>
            </button>
            <h3 class="textH3"><a href="<?php the_account_settings_link(); ?>">SETTINGS</a></h3>
        </div>
        <?php if(isset($_GET['purchase']) && $_GET['purchase']): ?>
        <div class="thanks-aria">
            <p>Thanks you for submitting your listing. It is being reviewed and will be published soon.</p>
        </div>
        <?php endif; ?>
        <?php
        $args = array(
            'post_type' => array('career','event','sale'),
            'posts_per_page' => '-1',
            'author' => get_current_user_id(),
            'orderby'=> 'DATE',
            'order' => 'DESC',
            'post_status' => 'any',
        );
        $the_query = new WP_Query($args);
        ?>
        <?php if($the_query->have_posts()): ?>
            <div class="relatedDashboard">
                <h2 class="titleRando">History</h2>
                <?php
                $args = array(
                    'post_type' => 'career',
                    'posts_per_page' => '-1',
                    'author' => get_current_user_id(),
                    'orderby'=> 'DATE',
                    'order' => 'DESC',
                    'post_status' => 'any',
                );
                $the_query = new WP_Query($args);
                ?>
                <?php if($the_query->have_posts()): ?>
                    <div class="relatedSection">
                        <table>
                            <tr>
                                <th>Jobs</th>
                                <th>Company</th>
                                <th>price</th>
                                <th>expiry date</th>
                                <th>status</th>
                                <th></th>
                                <th></th>
                            </tr>
                            <?php while($the_query->have_posts()): $the_query->the_post();?>
                                <?php
                                $package = wp_get_post_terms( get_the_ID(), 'career-packages' );
                                if(!count($package)){
                                    $package = get_term_by('id', get_field('priority'), 'career-packages');
                                    $date = date('d/m/y',strtotime(date('Y/m/d').' + '.intval(get_field('days','career-packages_'.$package->term_id)). ' days'));
                                }
                                else {
                                    $package = $package[0];
                                    $date = date('d/m/y',strtotime(get_the_date('Y/m/d').' + '.intval(get_field('days','career-packages_'.$package->term_id)). ' days'));
                                }
                                $price = '';
                                if(get_field('price','career-packages_'.$package->term_id)){
                                    $price.= '$'.get_field('price','career-packages_'.$package->term_id);
                                    if(get_field('gst','career-packages_'.$package->term_id)) {
                                        $price.= ' + GST';
                                    }
                                }
                                $company =  '' ;
                                $status = ucfirst(implode(' ',explode('-',get_post_status())));
                                if($status == 'Publish') {
                                    $status = 'Active';
                                }
                                if(get_field('companies_career')){
                                    $company.= get_field('companies_career');
                                }
                                ?>
                                <tr>
                                    <td><?php the_title(); ?></td>
                                    <td><?php echo $company; ?></td>
                                    <td><?php echo $price; ?></td>
                                    <td><?php echo $date; ?></td>
                                    <td><?php echo $status; ?></td>
                                    <td><a class="btnEdit" href="<?php the_post_job_link(); ?>/?id=<?php the_ID(); ?>"><img src="<?php echo THEME_IMAGES; ?>/edit.svg"></a></td>
                                    <td><a class="btnDelete" data-post="<?php the_ID(); ?>"><img src="<?php echo THEME_IMAGES; ?>/close-black.svg"></a></td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>
                <?php endif; ?>
                <?php
                $args = array(
                    'post_type' => 'event',
                    'posts_per_page' => '-1',
                    'author' => get_current_user_id(),
                    'orderby'=> 'DATE',
                    'order' => 'DESC',
                    'post_status' => 'any',
                );
                $the_query = new WP_Query($args);
                ?>
                <?php if($the_query->have_posts()): ?>
                    <div class="relatedSection">
                        <table>
                            <tr>
                                <th>events</th>
                                <th>Brand</th>
                                <th>price</th>
                                <th>expiry date</th>
                                <th>status</th>
                                <th></th>
                                <th></th>
                            </tr>
                            <?php while($the_query->have_posts()): $the_query->the_post();?>
                                <?php
                                $package = wp_get_post_terms( get_the_ID(), 'event-packages' );
                                if(!count($package)){
                                    $package = get_term_by('id', get_field('priority'), 'event-packages');
                                    $date = date('d/m/y',strtotime(date('Y/m/d').' + '.intval(get_field('days','event-packages_'.$package->term_id)). ' days'));
                                }
                                else {
                                    $package = $package[0];
                                    $date = date('d/m/y',strtotime(get_the_date('Y/m/d').' + '.intval(get_field('days','event-packages_'.$package->term_id)). ' days'));
                                }
                                $price = '';
                                if(get_field('price','event-packages_'.$package->term_id)){
                                    $price.= '$'.get_field('price','event-packages_'.$package->term_id);
                                    if(get_field('gst','event-packages_'.$package->term_id)) {
                                        $price.= ' + GST';
                                    }
                                }
                                $brand =  '' ;
                                $status = ucfirst(implode(' ',explode('-',get_post_status())));
                                if($status == 'Publish') {
                                    $status = 'Active';
                                }
                                if(get_field('event_brand_name')){
                                    $brand = get_field('event_brand_name');
                                }
                                ?>
                                <tr>
                                    <td><?php the_title(); ?></td>
                                    <td><?php echo $brand; ?></td>
                                    <td><?php echo $price; ?></td>
                                    <td>08/08/17</td>
                                    <td><?php echo $status; ?></td>
                                    <td><?php echo '<a class="btnEdit" href="'.get_post_event_link().'/?id='.get_the_ID().'"><img src="'.THEME_IMAGES.'/edit.svg"></a>'; ?></td>
                                    <td><a class="btnDelete" data-post="<?php the_ID(); ?>"><img src="<?php echo THEME_IMAGES; ?>/close-black.svg"></a></td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>
                <?php endif; ?>
                <?php
                $args = array(
                    'post_type' => 'sale',
                    'posts_per_page' => '-1',
                    'author' => get_current_user_id(),
                    'orderby'=> 'DATE',
                    'order' => 'DESC',
                    'post_status' => 'any',
                );
                $the_query = new WP_Query($args);
                ?>
                <?php if($the_query->have_posts()): ?>
                    <div class="relatedSection">
                        <table>
                            <tr>
                                <th>Sales</th>
                                <th>Brand</th>
                                <th>price</th>
                                <th>expiry date</th>
                                <th>status</th>
                                <th></th>
                                <th></th>
                            </tr>
                            <?php while($the_query->have_posts()): $the_query->the_post();?>
                                <?php
                                 ;
                                $package = wp_get_post_terms( get_the_ID(), 'sale-packages' );
                                if(!count($package)){
                                    $package = get_term_by('id', get_field('priority'), 'sale-packages');
                                    $date = date('d/m/y',strtotime(date('Y/m/d').' + '.intval(get_field('days','sale-packages_'.$package->term_id)). ' days'));
                                }
                                else {
                                    $package = $package[0];
                                    $date = date('d/m/y',strtotime(get_the_date('Y/m/d').' + '.intval(get_field('days','sale-packages_'.$package->term_id)). ' days'));
                                }
                                $price = '';
                                if(get_field('price','sale-packages_'.$package->term_id)){
                                    $price.= '$'.get_field('price','sale-packages_'.$package->term_id);
                                    if(get_field('gst','sale-packages_'.$package->term_id)) {
                                        $price.= ' + GST';
                                    }
                                }
                                $brand =  '' ;
                                $status = ucfirst(implode(' ',explode('-',get_post_status())));
                                if($status == 'Publish') {
                                    $status = 'Active';
                                }
                                if(get_field('sale_brand_name')){
                                    $brand = get_field('sale_brand_name');
                                }
                                ?>
                                <tr>
                                    <td><?php the_title(); ?></td>
                                    <td><?php echo $brand; ?></td>
                                    <td><?php echo $price; ?></td>
                                    <td><?php echo $date; ?></td>
                                    <td><?php echo $status; ?></td>
                                    <td><?php echo '<a class="btnEdit" href="'.get_post_sale_link().'/?id='.get_the_ID().'"><img src="'.THEME_IMAGES.'/edit.svg"></a>'; ?></td>
                                    <td><a class="btnDelete" data-post="<?php the_ID(); ?>"><img src="<?php echo THEME_IMAGES; ?>/close-black.svg"></a></td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <div class="relatedDashboard">
                <h1 class="textH1">You don’t have any previously created listings.</h1>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php get_footer('page');?>
