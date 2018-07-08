<?php get_header(); ?>
<section id="search">
    <?php
    $key = isset($_GET['s'])?$_GET['s']:'';
    $args = array(
        'post_type' => array('post'),
        'posts_per_page' => '-1',
        'post_status' => 'publish',
    );
    if(isset($_GET['s'])){
        $args['s'] = $_GET['s'];
        $args['sentence'] = true;
    }
    if(isset($_GET['tags'])){
        $tags = explode(',',$_GET['tags']);
        $args['tag_slug__in'] = $tags;
        unset($args['s']);
        unset($args['sentence']);
    }
    $cats = '';
    if(isset($_GET['cats'])){
        $args['category_name'] = $_GET['cats'];
        $cats = $_GET['cats'];
        unset($args['s']);
        unset($args['sentence']);
    }
    ?>
    <div class="groupPost search-results">
        <div class="container">
            <div class="row">
                <div class="sectionSearchResul">
                    <div class="searchResultsCount col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <p><?php echo thenative_posts_count($args); ?> search results</p>
                    </div>
                    <div class="titleSearch col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <h1><?php echo $key; ?></h1>
                    </div>
                    <div class="buttonSearch col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <button>search again</button>
                    </div>
                </div>
            </div>
            <?php
            $posts_per_page = 8;
            $args['posts_per_page'] = $posts_per_page;
            $the_query =  new WP_Query($args);
            ?>
            <?php if($the_query->have_posts()): ?>
                <?php
                $the_query =  new WP_Query($args);
                ?>
                <?php if($the_query->have_posts()): ?>
                    <div class="homepage">
                        <div class="imgPosts">
                            <div class="row">
                                <?php while($the_query->have_posts()): $the_query->the_post(); ?>
                                    <div class="col-sm-3 col-xs-6 colImages">
                                        <?php get_template_part('content', 'archive-medium'); ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                    <?php $args['posts_per_page'] = -1; ?>
                    <?php if(thenative_posts_count($args) > $posts_per_page): ?>
                        <div class="lazyLoad" data-time="<?php echo 1; ?>" data-offset="<?php echo $posts_per_page; ?>" data-search="<?php echo ($cats=='')?$_GET['s']:'';?>" data-tax="<?php echo $cats; ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" alt="Lazy Loading">
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php wp_reset_postdata(); ?>

                <?php /*
                    $args['post_type'] = 'event';
                    $the_query =  new WP_Query($args);
                ?>
                <?php if($the_query->have_posts()): ?>
                    <div class="Event">
                        <div class="wrapperVer">
                            <div class="row">
                                <?php while($the_query->have_posts()): $the_query->the_post(); ?>
                                    <div class="col-sm-3 col-xs-6 col-xs-12 colImages">
                                        <?php get_template_part('content', 'archive-event'); ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php wp_reset_postdata(); ?>

                <?php
                    $args['post_type'] = 'career';
                    $the_query =  new WP_Query($args);
                ?>
                <?php if($the_query->have_posts()): ?>
                    <div class="groupCareer">
                        <div class="wrapperVer">
                            <div class="row">
                                <?php while($the_query->have_posts()): $the_query->the_post(); ?>
                                    <div class="col-sm-3 col-xs-6 colImages">
                                        <?php get_template_part('content', 'archive-career'); ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php wp_reset_postdata(); */?>

            <?php endif; ?>
        </div>
    </div>
</section>
<?php get_footer(); ?>
