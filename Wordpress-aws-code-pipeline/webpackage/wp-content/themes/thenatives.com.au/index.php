<?php get_header(); ?>
<div id="body-wrapper">
	<div class="container">
		<div id="main-content">
			<?php if(have_posts()): ?>
				<div id="post-list">
				<?php 
					while(have_posts()) {
						the_post(); 
						//get_template_part('content', get_post_format());
					}
				?>
				</div>
			<?php else: ?>
				<?php get_template_part('content', 'none'); ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>