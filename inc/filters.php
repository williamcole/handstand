<?php

###########
# FILTERS #
###########

/* Title
-------------------------------------------------------------- */

# TODO: move outside of title tags
# or kill altogether - no longer needed?

// prepend flag to post types in search
function hnd_flag_title( $title ) {
	
	if( in_the_loop() ) {
	
		if( is_search() ) {
			if( hnd_is_event() || hnd_is_news() || hnd_is_press() ) {
				$flag = hnd_get_post_type();
			} else {
				$flag = false;
			}
		}
		
		/*
		if( hnd_is_package_deal() ) {
			$flag = 'special';
		}
		*/
	
		if( $flag && !is_page() ) {
			$title = '<span class=flag-' . $flag . '>' . $flag . '</span>' . $title;
		}
	}

	return $title;	
}
add_filter( 'the_title', 'hnd_flag_title' );

/* Excerpt
-------------------------------------------------------------- */

function hnd_excerpt_length( $length ) {
	
	// default: 55

	if( hnd_is_item() ) {
		return 40;
	} else {
		return 100;
	}
}
add_filter( 'excerpt_length', 'hnd_excerpt_length', 999 );

/* Content
-------------------------------------------------------------- */

# TODO: figure out why this breaks single pages

// remove jetpack sharedaddy so we can customize location
function hnd_the_content( $content ) {
	
	/*
	if( is_single() && ( hnd_is_item() || hnd_is_news() ) ) {
		remove_filter( 'the_content', 'sharing_display', 19 );
		remove_filter( 'the_excerpt', 'sharing_display', 19 );
	}
	*/
	
	remove_filter( 'the_content', 'sharing_display', 19 );
	remove_filter( 'the_excerpt', 'sharing_display', 19 );

	return $content;
}
add_filter( 'the_content', 'hnd_the_content' );

/* Search
-------------------------------------------------------------- */

function hnd_pre_get_posts_filter( $query ) {
	
	if( !is_admin() ) {

		// get post type, default to 'any'
		$post_type = ( isset( $_GET['post_type'] ) ) ? $_GET['post_type'] : 'any';
		
		// filter search by post type, and allow future posts (upcoming events)
		if( $query->is_search ) {
			$query->set( 'post_type', $post_type );
			$query->set( 'post_status', array( 'publish', 'future' ) );
		};

	}

	return $query;
}
add_filter( 'pre_get_posts', 'hnd_pre_get_posts_filter' );

/* Taxonomies
-------------------------------------------------------------- */

// display taxonomies in order they were entered, not alphabetical ???

function hnd_set_the_terms_in_order ( $terms, $id, $taxonomy ) {
    $terms = wp_cache_get( $id, "{$taxonomy}_relationships_sorted" );
    if ( false === $terms ) {
        $terms = wp_get_object_terms( $id, $taxonomy, array( 'orderby' => 'term_order' ) );
        wp_cache_add($id, $terms, $taxonomy . '_relationships_sorted');
    }
    return $terms;
}
#add_filter( 'get_the_terms', 'hnd_set_the_terms_in_order' , 10, 4 );

function hnd_do_the_terms_in_order () {
    global $wp_taxonomies;  // fixed missing semicolon
    // the following relates to tags, but you can add more lines like this for any taxonomy
    $wp_taxonomies['post_tag']->sort = true;
    $wp_taxonomies['post_tag']->args = array( 'orderby' => 'term_order' );    
}
#add_action( 'init', 'hnd_do_the_terms_in_order');