		</div>
		<footer id="footer">
            <div class="footer-main">
                <div class="container">
                    <?php if(!is_404()): ?>
                        <div class="retailTherapy">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <h2 class="headerRetailTherapy">Retail Therapy</h2>
                                        <div class="wrapperBodyRetailTherapy">
                                            <?php
                                            if (get_field('retail_therapy', $post->ID)) {
                                                while (the_flexible_field('retail_therapy', $post->ID)) { ?>
                                                    <?php if (get_row_layout() == "retail_") { ?>
                                                        <div class="hrefThumbnails col-lg-15 col-md-15 col-sm-6 col-xs-12">
                                                            <a target="_blank" href="<?php the_sub_field("link_footer"); ?>" class="imageThumbnails">
                                                                <img class="img-responsive image-sm"
                                                                     src="<?php the_sub_field("image_footer"); ?>"
                                                                     alt="<?php the_sub_field("text_footer"); ?>">
                                                            </a>
                                                            <a target="_blank" href="<?php the_sub_field("link_footer"); ?>"><span class="nameModel"><?php the_sub_field("text_footer"); ?></span></a>
                                                            <span class="priceProduct"><?php the_sub_field("price_footer"); ?></span>
                                                            <div class="clearfix"></div>
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                            }

                                            ?>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
		</footer>
	</div>
    <?php wp_footer(); ?>
</body>
</html>