<?php
/**
 * The homepage template used on The Crime Report for the main thing
 */
 global $post;

		$thumbnail = get_the_post_thumbnail($post->ID, 'full');
		$excerpt = largo_excerpt($post, 2, false, '', false);
		if ( ! empty($thumbnail)) {
			?>
				<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
					<a href="<?php echo esc_attr(get_permalink($post->ID)); ?>">
						<?php echo $thumbnail; ?>
					</a>
					<div class="has-thumbnail">
						<div class="has-thumbnail-inner">
							<h5 class="top-term"><?php largo_top_term(); ?></h5>
							<h2><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></h2>
							<h5 class="byline"><?php largo_byline(true, false, $post); ?></h5>
							<section class="excerpt">
								<?php echo $excerpt; ?>
							</section>
						</div>
					</div>
				</article>
			<?php
		} else {
			?>
				<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
					<h5 class="top-term"><?php largo_top_term(); ?></h5>
					<div class="">
						<h2><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></h2>
						<h5 class="byline"><?php largo_byline(true, false, $post); ?></h5>
						<section class="excerpt">
							<?php echo $excerpt; ?>
						</section>
					</div>
				</article>
			<?php
		}
