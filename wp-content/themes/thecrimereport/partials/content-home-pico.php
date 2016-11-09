<?php
/**
 * The template for displaying content in lists and other places where it's small
 * Typically will be wrapped in an <li> or ohter container
 */
 global $post;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix content-pico'); ?>>
	<h4><a href="<?php the_permalink(); ?>" title="Read: <?php esc_attr( the_title('','', FALSE) ); ?>"><?php the_title(); ?></a></h4>
</article>
