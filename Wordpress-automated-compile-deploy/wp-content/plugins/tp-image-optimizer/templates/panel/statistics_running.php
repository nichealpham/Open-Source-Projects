<div class='optimize-notice'>
	<div class='io-optimizer-wrapper'>
		<div class='io-notify-group <?php
			if ($cron): echo "active";
			endif;
		?>'>
			<ul>
				<li class="top-notice">
					<p class='label-statistic-optimizing'>
						<?php print esc_html__("Processing ", 'tp-image-optimizer'); ?>
					</p>
					<p class='optimized-number'>0</p> / <p
						class='total-number'><?php echo esc_html($total_file); ?></p>
					( <p class="compressed-image"
					     data-number-selected-size="<?php echo esc_html($total_selected_size); ?>">0</p> / <p
						class="total-compressed-images">0</p>
					<p><?php print esc_html__("images", 'tp-image-optimizer'); ?></p> )
				</li>
				<li>
					<p><?php print esc_html__("Error ", 'tp-image-optimizer'); ?></p>
					<p class='io-error'><?php echo esc_html($total_error); ?></p>
				</li>
			</ul>
		</div>
	</div>
</div>