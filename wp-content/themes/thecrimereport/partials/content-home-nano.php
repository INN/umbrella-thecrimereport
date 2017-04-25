<?php
/**
 * The template for displaying content in lists and other places where it's small
 * Typically will be wrapped in an <li> or ohter container
 */
 global $post;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix content-nano'); ?>>
	<h5 class="top-term"><?php largo_top_term(); ?></h5>
	<h4><a href="<?php the_permalink(); ?>" title="Read: <?php esc_attr( the_title('','', FALSE) ); ?>"><?php the_title(); ?></a></h4>
	<?php the_excerpt(); ?>
</article>
