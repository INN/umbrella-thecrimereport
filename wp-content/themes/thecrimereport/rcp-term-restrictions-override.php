<?php
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

// Bulk & Quick Edit Display - uses example from https://wpdreamer.com/2012/03/manage-wordpress-posts-using-bulk-edit-and-quick-edit/

/**
 * Since Bulk Edit and Quick Edit hooks are triggered by custom columns,
 * you must first add custom columns for the fields you wish to add, which are setup by
 * 'filtering' the column information.
 *
 * There are 3 different column filters: 'manage_pages_columns' for pages,
 * 'manage_posts_columns' which covers ALL post types (including custom post types),
 * and 'manage_{$post_type_name}_posts_columns' which only covers, you guessed it,
 * the columns for the defined $post_type_name.
 *
 * The 'manage_pages_columns' and 'manage_{$post_type_name}_posts_columns' filters only
 * pass $columns (an array), which is the column info, as an argument, but 'manage_posts_columns'
 * passes $columns and $post_type (a string).
 *
 * Note: Don't forget that it's a WordPress filter so you HAVE to return the first argument that's
 * passed to the function, in this case $columns. And for filters that pass more than 1 argument,
 * you have to specify the number of accepted arguments in your add_filter() declaration,
 * following the priority argument.
 *
 */
add_filter( 'manage_posts_columns', 'manage_wp_posts_be_qe_manage_posts_columns', 10, 2 );
function manage_wp_posts_be_qe_manage_posts_columns( $columns, $post_type ) {
	/**
	 * The first example adds our new columns at the end.
	 * Notice that we're specifying a post type because our function covers ALL post types.
	 *
	 * Uncomment this code if you want to add your column at the end
	 */
	/*if ( $post_type == 'movies' ) {
		$columns[ 'release_date' ] = 'Release Date';
		$columns[ 'coming_soon' ] = 'Coming Soon';
		$columns[ 'film_rating' ] = 'Film Rating';
	}

	return $columns;*/

	/**
	 * The second example adds our new column after the ÒTitleÓ column.
	 * Notice that we're specifying a post type because our function covers ALL post types.
	 */
	switch ( $post_type ) {

		case 'post':

			// building a new array of column data
			$new_columns = array();

			foreach( $columns as $key => $value ) {

				// default-ly add every original column
				$new_columns[ $key ] = $value;

				/**
				 * If currently adding the title column,
				 * follow immediately with our custom columns.
				 */
				if ( 'title' === $key ) {
					$new_columns[ 'rcp_override_term_restrictions' ] = 'Override Category Restrictions';
				}

			}

			return $new_columns;

	}

	return $columns;

}

/**
 * Now that we have a column, we need to fill our column with data.
 * The filters to populate your custom column are pretty similar to the ones
 * that added your column: 'manage_pages_custom_column', 'manage_posts_custom_column',
 * and 'manage_{$post_type_name}_posts_custom_column'. All three pass the same
 * 2 arguments: $column_name (a string) and the $post_id (an integer).
 *
 * Our custom column data is post meta so it will be a pretty simple case of retrieving
 * the post meta with the meta key 'release_date'.
 *
 * Note that we are wrapping our post meta in a div with an id of Òrelease_date-Ó plus the post id.
 * This will come in handy when we are populating our ÒQuick EditÓ row.
 */
add_action( 'manage_posts_custom_column', 'manage_wp_posts_be_qe_manage_posts_custom_column', 10, 2 );
function manage_wp_posts_be_qe_manage_posts_custom_column( $column_name, $post_id ) {
	switch( $column_name ) {
		case 'rcp_override_term_restrictions':
			$override_status = '1' === get_post_meta( $post_id, 'rcp_override_term_restrictions', true ) ? 'Yes' : '';
			echo '<div id="rcp_override_term_restrictions-' . $post_id . '">' . $override_status . '</div>';
			break;
	}

}

/**
 * Now that you have your custom column, it's bulk/quick edit showtime!
 * The filters are 'bulk_edit_custom_box' and 'quick_edit_custom_box'. Both filters
 * pass the same 2 arguments: the $column_name (a string) and the $post_type (a string).
 *
 * Your data's form fields will obviously vary so customize at will. For this example,
 * we're using an input. Also take note of the css classes on the <fieldset> and <div>.
 * There are a few other options like 'inline-edit-col-left' and 'inline-edit-col-center'
 * for the fieldset and 'inline-edit-col' for the div. I recommend studying the WordPress
 * bulk and quick edit HTML to see the best way to layout your custom fields.
 */
add_action( 'bulk_edit_custom_box', 'manage_wp_posts_be_qe_bulk_quick_edit_custom_box', 10, 2 );
add_action( 'quick_edit_custom_box', 'manage_wp_posts_be_qe_bulk_quick_edit_custom_box', 10, 2 );
function manage_wp_posts_be_qe_bulk_quick_edit_custom_box( $column_name, $post_type ) {
	switch ( $post_type ) {

		case 'post':

			switch( $column_name ) {

				case 'rcp_override_term_restrictions':

					?><fieldset class="inline-edit-col-left">
						<div class="inline-edit-col">
							<label for="rcp_restrict">
								<input type="checkbox" id="rcp_override_term_restrictions" name="rcp_override_term_restrictions" value="1" <?php checked( true, get_post_meta( $post->ID, 'rcp_override_term_restrictions', true ) ); ?>/>
								<?php _e( 'Check this box to ignore category restrictions on this post.', 'rcp-override-term-restrictions' ); ?>
							</label>
<?php /*
							<label>
								<span class="title">Override Term Restrictions</span>
								<span class="input-text-wrap">
									<input type="text" value="" name="release_date">
								</span>
							</label>
*/ ?>
						</div>
					</fieldset><?php
					break;
			}

			break;

	}

}
/**
 * When you click 'Quick Edit', you may have noticed that your form fields are not populated.
 * WordPress adds one 'Quick Edit' row which moves around for each post so the information cannot
 * be pre-populated. It has to be populated with JavaScript on a per-post 'click Quick Edit' basis.
 *
 * WordPress has an inline edit post function that populates all of their default quick edit fields
 * so we want to hook into this function, in a sense, to make sure our JavaScript code is run when
 * needed. We will 'copy' the WP function, 'overwrite' the WP function so we're hooked in, 'call'
 * the original WP function (via our copy) so WordPress is not left hanging, and then run our code.
 *
 * Remember where we wrapped our column data in a <div> in Step 2? This is where it comes in handy,
 * allowing our Javascript to retrieve the data by the <div>'s element ID to populate our form field.
 * There are other methods to retrieve your data that involve AJAX but this route is the simplest.
 *
 * Don't forget to enqueue your script and make sure it's dependent on WordPress's 'inline-edit-post' file.
 * Since we'll be using the jQuery library, we need to make sure 'jquery' is loaded as well.
 *
 * I have provided several scenarios for where you've placed this code. Simply uncomment the scenario
 * you're using. For all scenarios, make sure your javascript file is in the same folder as your code.
 */
add_action( 'admin_print_scripts-edit.php', 'manage_wp_posts_be_qe_enqueue_admin_scripts' );
function manage_wp_posts_be_qe_enqueue_admin_scripts() {
	// if code is in theme functions.php file
	//wp_enqueue_script( 'manage-wp-posts-using-bulk-quick-edit', trailingslashit( get_bloginfo( 'stylesheet_directory' ) ) . 'bulk_quick_edit.js', array( 'jquery', 'inline-edit-post' ), '', true );

	// if using code as plugin
	wp_enqueue_script( 'manage-wp-posts-using-bulk-quick-edit', '/wp-content/themes/thecrimereport/js/bulk_quick_edit.js', array( 'jquery', 'inline-edit-post' ), '', true );

}
/**
 * Saving your 'Quick Edit' data is exactly like saving custom data
 * when editing a post, using the 'save_post' hook. With that said,
 * you may have already set this up. If you're not sure, and your
 * 'Quick Edit' data is not saving, odds are you need to hook into
 * the 'save_post' action.
 *
 * The 'save_post' action passes 2 arguments: the $post_id (an integer)
 * and the $post information (an object).
 */
add_action( 'save_post', 'manage_wp_posts_be_qe_save_post', 10, 2 );
function manage_wp_posts_be_qe_save_post( $post_id, $post ) {
	// pointless if $_POST is empty (this happens on bulk edit)
	if ( empty( $_POST ) )
		return $post_id;

	// verify quick edit nonce
	if ( isset( $_POST[ '_inline_edit' ] ) && ! wp_verify_nonce( $_POST[ '_inline_edit' ], 'inlineeditnonce' ) )
		return $post_id;

	// don't save for autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	// dont save for revisions
	if ( isset( $post->post_type ) && $post->post_type == 'revision' )
		return $post_id;

	switch( $post->post_type ) {

		case 'post':

			/**
			 * Because this action is run in several places, checking for the array key
			 * keeps WordPress from editing data that wasn't in the form, i.e. if you had
			 * this post meta on your "Quick Edit" but didn't have it on the "Edit Post" screen.
			 */
			$custom_fields = array( 'rcp_override_term_restrictions' );

			foreach( $custom_fields as $field ) {

				if ( array_key_exists( $field, $_POST ) )
					update_post_meta( $post_id, $field, $_POST[ $field ] );

			}

			break;

	}

}

/**
 * Saving the 'Bulk Edit' data is a little trickier because we have
 * to get JavaScript involved. WordPress saves their bulk edit data
 * via AJAX so, guess what, so do we.
 *
 * Your javascript will run an AJAX function to save your data.
 * This is the WordPress AJAX function that will handle and save your data.
 */
add_action( 'wp_ajax_manage_wp_posts_using_bulk_quick_save_bulk_edit', 'manage_wp_posts_using_bulk_quick_save_bulk_edit' );
function manage_wp_posts_using_bulk_quick_save_bulk_edit() {
	// we need the post IDs
	$post_ids = ( isset( $_POST[ 'post_ids' ] ) && !empty( $_POST[ 'post_ids' ] ) ) ? $_POST[ 'post_ids' ] : NULL;

	// if we have post IDs
	if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {

		// get the custom fields
		$custom_fields = array( 'rcp_override_term_restrictions' );

		foreach( $custom_fields as $field ) {

			// if it has a value, doesn't update if empty on bulk
			if ( isset( $_POST[ $field ] ) && !empty( $_POST[ $field ] ) ) {

				// update for each post ID
				foreach( $post_ids as $post_id ) {
					update_post_meta( $post_id, $field, $_POST[ $field ] );
				}

			}

		}

	}

}
