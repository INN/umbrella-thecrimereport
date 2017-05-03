<?php
/**
 * Show full, unrestricted content in RSS feeds
 *
 * @param WP_Query $query
 *
 * @return void
 */
function crimereport_rcp_show_in_rss_feeds( $query ) {
	if ( $query->is_feed() ) {
		remove_filter( 'the_content', 'rcp_filter_restricted_content', 100 );
	}
}
add_action('pre_get_posts', 'crimereport_rcp_show_in_rss_feeds');
