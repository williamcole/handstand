<?php

##############
# NAVIGATION #
##############

/* Menus
-------------------------------------------------------------- */

function hnd_register_menus() {
	register_nav_menus( array(
		//'store-menu' => __( 'Store Menu' ),	
		'footer-menu' => __( 'Footer Menu' ),
	) );
}
add_action( 'init', 'hnd_register_menus' );

/* Navigation
-------------------------------------------------------------- */

function hnd_breadcrumb_nav() {
	
	global $post;
	$post_type = get_post_type();
	
	// define breadcrumb nav array
	$nav = array();
	
	# SET FIRST ELEMENT
	
	// get post type and replace some text
	if( ( $post_type == 'items' ) || hnd_is_store_page() ) {
		$post_type = 'store';
	} elseif( $post_type == 'post' ) {
		$post_type = 'news';
	} elseif( $post_type == 'page' ) {
		$post_type = 'page';
	} else {
		$post_type = get_post_type();
	}
	
	# SET FIRST ELEMENT
	
	if( hnd_is_event() || hnd_is_press() ) {
		$first = 'News';
		$last = $post_type;
	} elseif( get_post_type() == 'page' ) {

		$title = get_the_title();

		// check for parent page
    	if( is_page() && $post->post_parent && ( get_the_title( $post->post_parent ) != the_title( ' ' , ' ', false ) ) ) {
			$parent = wp_list_pages('echo=0&title_li=&include='.$post->post_parent);
			$parent = str_replace('<li class="page_item current_page_parent">', '', $parent);
			$parent = str_replace('</li>', '', $parent);
			
			$first = $parent;
			$last = $title;

		} else {
			$first = $title;
		}

	} else {
		$first = $post_type;
	}
	
	# SET MIDDLE/LAST ELEMENTS
	
	// artists
	if( hnd_is_artist() && is_single() ) {
		$last = get_the_title();
	}
	
	// taxonomies (formats/genres/features)
	if( is_tax() ) {
		
		global $wp_query;
		$term =	$wp_query->queried_object;
		$taxonomy = $term->taxonomy;
		$name = $term->name;
		
		// spell out 'inch'
		if( $taxonomy == 'formats' ) {
			$name = str_replace( '"', '-inch', $name );
		}
		
		$middle = $taxonomy;
		$last = $name;
	
	}
	
	// add link to first element if there are subsequent elements
	if( $middle || $last || hnd_is_store_page() ) {
		if( hnd_is_event() || hnd_is_press() ) {
			$first = '<a href="' . get_bloginfo( 'url' ) . '/news">' . $first . '</a>';
		} else {
			$first = '<a href="' . get_bloginfo( 'url' ) . '/' . $post_type . '">' . $first . '</a>';
		}
	}

	// link to last element on single pages
	if( is_single() ) {
		if( hnd_is_news() ) $first = '<a href="' . get_bloginfo( 'url' ) . '/news/">' . $first . '</a>';
		if( hnd_is_event() ) $last = '<a href="' . get_bloginfo( 'url' ) . '/news/events/">' . $last . '</a>';
		if( hnd_is_press() ) $last = '<a href="' . get_bloginfo( 'url' ) . '/news/press/">' . $last . '</a>';
	}

	// combine the breadcrumbs
	if( $first ) $nav[] = $first;
	if( $middle ) $nav[] = $middle;
	if( $last ) $nav[] = '<span class="black">' . $last . '</span>';
	
	// output
	echo '<h1>' . implode( ' <span class="arrow">></span> ', $nav ) . '</h1>';

	// subnav
	hnd_subnav();

	echo '<div class="clear"></div>';
}

// secondary navigation for store and artist pages
function hnd_subnav() {

	if( hnd_is_artist() && is_single() )
		hnd_artist_nav();

	if( hnd_is_event() && is_archive() )
		hnd_events_nav();

	if( hnd_is_store_page() )
		hnd_store_nav();	
}

// add 'view' query var to global WP_query
function hnd_add_query_vars( $vars ) {
	$vars[] = 'artist';
	$vars[] = 'view';
	return $vars;
}
add_filter( 'query_vars', 'hnd_add_query_vars' );

// secondary navigation for artist pages
function hnd_artist_nav() {
	
	if( !hnd_is_artist() || !is_single() )
		return;

	$artist_links = array(
		'Bio',
		'Events',
		'Flyers',
		'Photos',
		'Store',
		'Videos',
	);

	$active_link = ( get_query_var('view') ) ? get_query_var('view') : 'bio';

	echo '<div id="artist-nav" class="subnav"><ul>';
	foreach( $artist_links as $link ) {
		$class = ( $active_link == strtolower( $link ) ) ? 'class="active"' : '';
		echo '<li ' . $class . '><a href="?view=' . strtolower( $link ) . '">' . $link . '</a></li>';
	}
	echo '</ul></div><!--/#artist-nav-->';	
}

// filter events by artist
function hnd_events_nav() {

	wp_enqueue_script( 'nav-filter', get_stylesheet_directory_uri() . '/js/nav-filter.js', array( 'jquery' ), null, true );
	
	echo '<div id="events-nav" class="nav-filter">';
	
	$artists = new WP_Query( array(
		'post_type' => 'artists',
		'order' => 'ASC',
		'orderby' => 'title',
		'posts_per_page' => -1,
	) );
	
	if( isset( $artists ) && $artists->have_posts() ) {
		
		echo '<div class="nav-filter-box">';
		echo '<select class="nav-filter-select">';
		echo '<option selected="selected" value="">SELECT ARTIST</option>';
		
		// get selected artist
		$artist_slug = ( get_query_var('artist') ) ? get_query_var('artist') : '';
		
		while( $artists->have_posts() ) : $artists->the_post();
			$option_slug = hnd_slugify( get_the_title() );
			$selected = ( $artist_slug == $option_slug ) ? 'selected' : '';
			echo '<option value="' . get_bloginfo('url') . '/news/events/?artist=' . $option_slug . '" ' . $selected .'>' . get_the_title() . '</option>';
		endwhile;

		echo '</select>';
		echo '</div>';
	}

	echo '</div><!--/.#events-nav-->';

	wp_reset_query();	

}

// secondary navigation for all store pages
function hnd_store_nav() {

	if( !hnd_is_store_page() )
		return;

	wp_enqueue_script( 'nav-filter', get_stylesheet_directory_uri() . '/js/nav-filter.js', array( 'jquery' ), null, true );
	
	echo '<div id="nav-filter" class="nav-filter">';
	
	// formats, labels, genres, features
	$item_taxonomies = array(
		'formats' => array( 'orderby' => 'term_order', 'hierarchical' => true ),
		'labels' => array( 'orderby' => 'name' ),
		'genres'=> array( 'orderby' => 'name' ),
		'features' => array( 'orderby' => 'name' )
	);
	
	foreach( $item_taxonomies as $taxonomy => $order ) {
		$terms = get_terms( $taxonomy, $order );	
		if( $terms ) {
			echo '<div class="nav-filter-box">';
			echo '<select class="nav-filter-select">';
			echo '<option selected="selected" value="">' . strtoupper( $taxonomy ) .'</option>';
				
			if( $taxonomy == 'formats' ) {
				hnd_taxonomy_terms( 0, 'formats' );	
			} else {
				foreach( $terms as $term ) {
					$link = get_term_link( $term, $taxonomy );
					if( is_wp_error( $link ) ) continue;
					echo '<option value="' . $link . '">' . $term->name . '</option>';
				}
			}	

			echo '</select>';
			echo '</div>';
		}
	}
	
	echo '</div><!--/#nav-filter-->';

	wp_reset_query();		
}

// get hierachical terms of a given taxonomy
// needed for hnd_store_nav() do that formats display in the proper nested order
function hnd_taxonomy_terms( $parent_id, $taxonomy, $child = false, $ids = false ) {
        
    $args = array(
    	'parent' => $parent_id,
    	'orderby' => 'term_order',
    	'hide_empty' => false,
    );

    $child_terms = get_terms( $taxonomy, $args );

    // return if no child terms found
    if( count( $child_terms ) < 1 )
    	return;

    // set class if child
    $child_pad = ( $child ) ? '&nbsp;&nbsp;&nbsp;' : '';

    foreach( $child_terms as $child_term ) {
		
		if( $ids ) {
			// return id values
			$value = $child_term->term_id;
		} else {
			// return link values
			$value = get_term_link( $child_term, $taxonomy );
		}

		if( is_wp_error( $value ) ) continue;
		
		// output select option
		echo '<option value="' . $value . '">' . $child_pad . $child_term->name . '</option>';

		// recursively process the child terms if they exist
		hnd_taxonomy_terms( $child_term->term_id, $taxonomy, $child = true, $ids );
	}
}

/* Pagination
-------------------------------------------------------------- */

function hnd_prev_next_nav() {

	if( !hnd_is_news() )
		return;
	
	?>
	<nav id="single-nav" class="clearfix"> 
		<?php next_post_link( '<div id="single-nav-left">%link</div>', '&#9664; Prev', true ); ?>
		<?php previous_post_link( '<div id="single-nav-right">%link</div>', 'Next &#9654;', true ); ?>
	</nav><!-- /single-nav -->
	<?php
}

// custom store pagination
function hnd_get_pagination() {

	global $wp_query, $items, $paged;

	$this_query = $wp_query->query;
	#print_r( $this_query );
	#print_r( $wp_query );
	
	// max number of pages
	if( isset( $items->max_num_pages ) ) {
		$max_pages = $items->max_num_pages;
	} else {
		$max_pages = $wp_query->max_num_pages;
	}

	if( $max_pages <= 1 )
		return;

	// link url
	if( isset( $this_query['pagename'] ) ) {
		
		// store page templates
		$href = get_bloginfo('url') . '/' . $this_query['pagename'];
	} else {
		
		// get current url
		$href = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		// get url parts
		$href_parts = explode( '/', $href );
		$num_parts = count( $href_parts );

		// remove empty element
		if( empty( $href_parts[$num_parts] ) ) {
			array_pop( $href_parts );
		}
		
		// remove page number
		if( is_numeric( end( $href_parts ) ) ) {
			array_pop( $href_parts );
		}

		// remove 'page'
		if( 'page' == end( $href_parts ) ) {
			array_pop( $href_parts );
		}

		// reconstruct url
		$href = 'http://' . implode( '/' , $href_parts );		
	}
	
	$html = '';
	$html .= '<div class="pagination clearfix">';
	
	for( $i = 1; $i <= $max_pages; $i++ ) {
		if( $i == $paged ) {
			$html .= '<span class="current">' . $paged . '</span>';
		} else {
			$html .= '<a class="inactive" href="' . $href . '/page/' . $i .'">' . $i . '</a>';
		}
	}
	
	$html .= '</div>';

	return $html;
}

function hnd_pagination() {
	echo hnd_get_pagination();
}