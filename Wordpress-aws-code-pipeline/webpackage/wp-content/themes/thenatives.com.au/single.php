<?php get_header(); ?>
<?php
    if(have_posts()) {
        while (have_posts()) {
            the_post();
            $style = get_field('style') ? get_field('style') : 'article-a';
            if (!file_exists(THEME_DIR . '/content-' . $style . '.php')) {
                $style = 'article-a';
            }
            get_template_part('content', $style);
        }
    }
?>
<?php get_footer('homepage-a'); ?>