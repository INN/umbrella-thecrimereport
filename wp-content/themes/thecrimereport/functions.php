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
	wp_enqueue_style( 'oklahomawatch', get_stylesheet_directory_uri() . '/css/child' . $suffix . '.css?20170501' );

}
add_action( 'wp_enqueue_scripts', 'child_stylesheet', 20 );

/**
 * RCP Custom Enhancements
 */
require_once( dirname(__FILE__) . '/inc/rcp-term-restrictions-override.php' );
require_once( dirname(__FILE__) . '/inc/rcp-user-mailchimp-signup-display.php' );
