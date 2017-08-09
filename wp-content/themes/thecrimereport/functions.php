<?php
/**
 * Register a custom homepage layout
 *
 * @see "homepages/layouts/your_homepage_layout.php"
 */
function register_custom_homepage_layout() {
	include_once __DIR__ . '/homepages/layouts/TheCrimeReport.php';
	register_homepage_layout('TCR');
}
add_action( 'init', 'register_custom_homepage_layout', 0 );


/**
 * Include compiled style.css
 */
function child_stylesheet() {
	wp_dequeue_style( 'largo-child-styles' );

	$suffix = ( LARGO_DEBUG )? '' : '.min';
	wp_enqueue_style( 'oklahomawatch', get_stylesheet_directory_uri() . '/css/child' . $suffix . '.css?201707051' );

}
add_action( 'wp_enqueue_scripts', 'child_stylesheet', 20 );

/**
 * RCP Custom Enhancements
 */
require_once( dirname(__FILE__) . '/inc/rcp-term-restrictions-override.php' );
require_once( dirname(__FILE__) . '/inc/rcp-user-mailchimp-signup-display.php' );
require_once( dirname(__FILE__) . '/inc/rcp-feed-override.php' );

/**
 * extend crime report excerpt length to 80 words
 */
function crimereport_custom_excerpt_length( $length ) {
	return 80;
}
add_filter( 'excerpt_length', 'crimereport_custom_excerpt_length', 999 );

function crimereport_custom_rcp_excerpt( $excerpt, $post ) {
	$length = 80;
	$tags = '<a><em><strong><blockquote><ul><ol><li><p>';
	$extra = ' . . .';

	if ( is_int( $post ) ) {
		// get the post object of the passed ID
		$post = get_post( $post );
	} elseif ( ! is_object( $post ) ) {
		return false;
	}

	$the_excerpt = $post->post_content;

	$tags = apply_filters( 'rcp_excerpt_tags', $tags );

	if ( $more ) {
		$the_excerpt = strip_shortcodes( strip_tags( stripslashes( substr( $the_excerpt, 0, $length ) ), $tags ) );
	} else {
		$the_excerpt = strip_shortcodes( strip_tags( stripslashes( $the_excerpt ), $tags ) );
		$the_excerpt = preg_split( '/\b/', $the_excerpt, $length * 2 + 1 );
		$excerpt_waste = array_pop( $the_excerpt );
		$the_excerpt = implode( $the_excerpt );
		$the_excerpt .= $extra;
	}

	return $the_excerpt;
}
add_filter( 'tcr_rcp_excerpt', 'crimereport_custom_rcp_excerpt', 999, 2 );

// enable shortcodes in text widgets
global $wp_embed;
add_filter( 'widget_text', 'shortcode_unautop', 8 );
add_filter( 'widget_text', array( $wp_embed, 'autoembed'), 8 );
add_filter( 'widget_text', 'do_shortcode', 8 );
