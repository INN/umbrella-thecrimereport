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
	wp_enqueue_style( 'oklahomawatch', get_stylesheet_directory_uri() . '/css/child' . $suffix . '.css?20170315' );

}
add_action( 'wp_enqueue_scripts', 'child_stylesheet', 20 );

/**
 * RCP Custom Enhancements
 */
function crimereport_term_restriction_override_display() {
	global $post;
	?>
	<div id="rcp-metabox-override-term-restrictions" class="rcp-metabox-field">
		<p>
			<label for="rcp_restrict">
				<input type="checkbox" id="rcp_override_term_restrictions" name="rcp_override_term_restrictions" value="1" <?php checked( true, get_post_meta( $post->ID, 'rcp_override_term_restrictions', true ) ); ?>/>
				<?php _e( 'Check this box to ignore category restrictions on this post.', 'rcp-override-term-restrictions' ); ?>
			</label>
		</p>
	</div>
	<?php
}
add_action( 'rcp_metabox_additional_options_before', 'crimereport_term_restriction_override_display' );

function crimereport_term_restriction_override_save( $post_id ) {

	$override_term_restrictions = isset( $_POST['rcp_override_term_restrictions'] );
	if ( $override_term_restrictions ) {
		update_post_meta( $post_id, 'rcp_override_term_restrictions', $override_term_restrictions );
	} else {
		delete_post_meta( $post_id, 'rcp_override_term_restrictions' );
	}
}
add_action( 'rcp_save_post_meta', 'crimereport_term_restriction_override_save' );

function crimereport_term_restriction_override_logic( $restricted, $post_id ) {
	$override_term_restrictions = crimereport_term_restriction_override_check( $post_id );
	if ( $override_term_restrictions ) {
		$restricted = false;
	}
	return $restricted;
}
add_action( 'rcp_is_restricted_content', 'crimereport_term_restriction_override_logic', 10, 2 );

/**
 * Checks to see if a given post has selected to override term restrictions.
 *
 * @param int $post_id The post ID to check for restrictions override.
 *
 * @since 2.8.6
 * @return bool True if the post has selected to override term restrictions.
 */
function crimereport_term_restriction_override_check( $post_id ) {
	if ( empty( $post_id ) || ! is_numeric( $post_id ) ) {
		return false;
	}
	$selected = false;
	$post_id = absint( $post_id );
	$selected = get_post_meta( $post_id, 'rcp_override_term_restrictions', true );
	return (bool) apply_filters( 'rcp_override_term_restrictions', $selected, $post_id );
}
