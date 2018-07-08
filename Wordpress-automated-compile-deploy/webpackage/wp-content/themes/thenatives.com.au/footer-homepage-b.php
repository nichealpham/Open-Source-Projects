</div>
<footer id="footer">
    <div class="footer-main">
        <div class="container">
            <?php if (!is_404()): ?>
                <div class="followUs">
                    <div class="row">
                        <div class="followUsItem wrapperContact col-lg-3 col-md-3 col-sm-12 col-xs-12">
                            <h4 class="titleFollowUs">Follow us and feed your feed.</h4>
                            <p class="contactFollowUs">@FASHIONJOURNALMAGAZINE</p>
                        </div>
                        <div class="instagram col-lg-9 col-md-9 col-sm-12 col-xs-12">
                            <?php echo do_shortcode('[instashow id="1"]'); ?>
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