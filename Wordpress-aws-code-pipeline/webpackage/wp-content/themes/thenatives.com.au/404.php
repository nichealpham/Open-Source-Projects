<?php get_header() ?>
<main class="page-404">
    <section class="page-404-header">
        <div class="container">
            <h1 class="page-404-title">Uh oh, that page doesn’t appear to exist.<br/>Try our <a class="SheArcH" href="#">search</a> or explore our latest articles below.</h1>
        </div>
    </section>
    <section class="groupPost">
        <div class="container">
            <div class="titleOla">
                <p>Ola.</p>
            </div>
            <?php
            global $wp_query;
            $args = array(
                'post_type'				=> 'post',
                'post_status'			=> 'publish',
                'posts_per_page' 		=> 8,
                'offset'                => 2,
                'orderby' 				=> 'date',
                'order'                 => 'DESC',
            );
            $wp_query = new WP_Query( $args );
            ?>
            <?php if (have_posts()) : ?>
                <div class="row imgPosts">
                    <?php while(have_posts()): the_post();?>
                        <div class="col-sm-3 col-xs-6 colImages">
                            <?php get_template_part('content', 'archive-medium'); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
            <?php wp_reset_query(); ?>
        </div>
    </section>
</main>
<?php get_footer() ?>