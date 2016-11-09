<?php

include_once get_template_directory() . '/homepages/homepage-class.php';

class TCR extends Homepage {
	function __construct( $options=array() ) {
		$defaults = array(
			'template' 			=> get_stylesheet_directory() . '/homepages/templates/the-crime-report.php',
			'name' 				=> __( 'TCR Three Column', 'tcr' ),
			'type' 				=> 'tcr',
			'description' 		=> __( 'This layout has a skinny left sidebar, wide right sidebar and a list of stories in the middle column. This layout allows setting a homepage Top Story.', 'largo' ),
			'sidebars' => array(
				__( 'Homepage Left Rail (Appears at the bottom of the featured stories in the left column)', 'tcr' )
			),
			'prominenceTerms' 	=> array(
				array(
					'name' 			=> __( 'Top Story', 'largo' ),
					'description' 	=> __( 'If you are using a "Big story" homepage layout, add this label to a post to make it the top story on the homepage', 'largo' ),
					'slug' 			=> 'top-story'
				)
			),
			'assets' => array(
				array( 'legacy-three-column', get_template_directory_uri() . '/homepages/assets/css/legacy-three-column.css', array() ),
				array( 'tcr-homepage', get_stylesheet_directory_uri() . '/homepages/assets/css/homepage.css', array() )
			),
		);
		$options = array_merge( $defaults, $options );
		parent::__construct( $options );
	}

	/**
	 * The homepage top story, copied from largo/homepages/templates/top-stories.php
	 */
	function homepage_top_story() {
		global $shown_ids, $post;
		$post = largo_home_single_top();

		$shown_ids[] = $post->ID;
		setup_postdata( $post );
		ob_start();
		get_template_part('partials/content', 'home');
		wp_reset_postdata();
		return ob_get_clean();
	}

	/**
	 * get n posts from the Homepage Featured term
	 *
	 * Supplement with recent posts if not enough Homepage Featured posts exist.
	 */
	private function tcr_homepage_featured_get_many( $many ) {
		global $shown_ids, $post;
		$homepage_feature_term = get_term_by( 'name', __('Homepage Featured', 'largo'), 'prominence' );

		$args = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'prominence',
					'field' => 'term_id',
					'terms' => $homepage_feature_term->term_id
				)
			),
			'posts_per_page' => $many,
			'post__not_in' => $shown_ids
		);

		if (of_get_option('cats_home'))
			$args['cat'] = of_get_option('cats_home');

		$return = get_posts( $args );

		if ( count($return) < $many ) {
			$feat_ids = array();
			foreach ( $return as $f ) {
				$feat_ids[] = $f->ID;
			}
			
			$args = array(
				'posts_per_page' => ($many - count($return)),
				'post__not_in' => array_merge($feat_ids, $shown_ids)
			);
			
			if (of_get_option('cats_home'))
				$args['cat'] = of_get_option('cats_home');
				
			$recent = get_posts( $args );
			
			$return = array_merge( $return, $recent );
		}

		return $return;
	}

	/**
	 * given an array of posts and the second param of a get_template_part( 'foo', 'bar' ), render the posts.
	 *
	 * The template chosen is `partials/content` joined with the argument $partial.
	 * If you don't specify a $partial, it loads `partials/content.php`.
	 *
	 * @param Array $posts an array of WP_Post objects
	 * @param String $partial the template partial's name; see https://developer.wordpress.org/reference/functions/get_template_part/
	 */
	private function tcr_homepage_render( $posts, $partial = '' ) {
		global $shown_ids, $post;
		ob_start();

		foreach ( $posts as $post ) {
			setup_postdata( $post );
			get_template_part( 'partials/content', $partial );

			$shown_ids[] = get_the_ID();
		}

		wp_reset_postdata();
		return ob_get_clean();
	}

	/**
	 * Homepage ( count - 1 ) stories, from homepage featured
	 */
	function homepage_center_column_featured() {
		$num_posts_home = (int) of_get_option( 'num_posts_home' ) - 1; // so the total number of posts in the center column is 5, because there's the top story as well.
		$featured = $this->tcr_homepage_featured_get_many( $num_posts_home );
		return $this->tcr_homepage_render( $featured, 'home' );
	}

	// five stories with top term, headline and excerpt
	function homepage_left_column_featured() {
		global $shown_ids, $post;
		$featured = $this->tcr_homepage_featured_get_many( 5 );
		return $this->tcr_homepage_render( $featured, 'home-nano' );
	}

	// the four stories with headlines only, in the left column
	function homepage_left_column_headlines() {
		global $shown_ids, $post;
		$featured = $this->tcr_homepage_featured_get_many( 4 );
		return $this->tcr_homepage_render( $featured, 'home-pico' );
	}
}
