<?php
/**
 * Legacy three column homepage template
 */
?>
<div class="row-fluid">
	<div id="homepage-left" class="span8">
		<div id="homepage-featured" class="row-fluid clearfix">
			<div id="content-main" class="span8">
				<?php echo $homepage_top_story; ?>
				<?php echo $homepage_center_column_featured; ?>
			</div>

			<div id="left-rail" class="span4">
				<?php echo $homepage_left_column_featured; ?>
				<?php echo $homepage_left_column_headlines; ?>
				<?php dynamic_sidebar('homepage-left-rail'); ?>
			</div>
		</div>
	</div>
	<div id="sidebar" class="span4">
		<div class="widget-area">
			<?php if (!dynamic_sidebar('sidebar-main')) { ?>
				<p><?php _e('Please add widgets to this content area in the WordPress admin area under Appearance > Widgets.', 'largo'); ?></p>
			<?php } ?>
		</div>
	</div><!-- end of span4 sidebar -->
</div>
