<?php

#########
# ITEMS #
#########

/* Title
-------------------------------------------------------------- */

// make single item titles nice
function hnd_item_title( $title ) {
	if( hnd_is_item() && is_single() && is_main_query() ) {
		$title = hnd_get_nice_title( $title );
	}
	return $title;	
}
//add_filter( 'the_title', 'hnd_item_title' );

/* Slug
-------------------------------------------------------------- */

// convert " to -inch in post slug
function hnd_item_post_slug( $slug, $post_ID, $post_status, $post_type ) {
    if( $post_type == 'items' ) {
        $slug = hnd_slugify( get_the_title( $post_ID ) );
    	$slug = str_replace( '215', 'x', $slug ); 		// replace weird shit
    	$slug = str_replace( '/', '-', $slug );    		// replace multiple dash ???    
    	$slug = str_replace( '8217', '', $slug ); 		// replace single quotes
    	$slug = str_replace( '8242', '', $slug ); 		// replace single quotes
    	$slug = str_replace( '8243', '-inch', $slug ); 	// replace double quotes
    	$slug = str_replace( '-8211-', '-', $slug );    // replace multiple dash ???    
		$slug = str_replace( '038', '', $slug ); 		// replace ampersand
    	$slug = str_replace( '--', '-', $slug ); 		// replace double dashes
    }
    return $slug;
}
add_filter( 'wp_unique_post_slug', 'hnd_item_post_slug', 10, 4 );

/* Content
-------------------------------------------------------------- */

function hnd_item_excerpt( $output ) {
	
	if( hnd_is_item() ) {
		
		if( hnd_is_package_deal() ) {
			$output .= '&nbsp;&nbsp;<div class="package-savings">SAVE $' . hnd_get_package_deal_savings() . '!</div>';
		}
		
		$output .= '<div class="buttons">';
		$output .= hnd_get_buy_button();
		$output .= hnd_get_more_info();
		$output .= '</div>';
		
		/*
		if( hnd_is_package_deal() ) {
			$output .= hnd_get_package_deal_items();
		}
		*/
	}
	
	return $output;
}
add_filter( 'get_the_excerpt', 'hnd_item_excerpt' );

function hnd_item_content( $content ) {
	
	// single item markup
	if( hnd_is_item() && is_single() ) {
		
		// orig content
		$orig_content = $content;
		$content = '';
		
		if( has_shortcode( $orig_content, 'gallery' ) ) {
		
			# TODO - move gallery to the end after various specs
			
		}
		
		$content .= hnd_get_pressing_specs();
		$content .= hnd_get_button_specs();
		$content .= hnd_get_item_meta();
		$content .= hnd_get_bandcamp_audio();
		$content .= $orig_content;
		
		// supplemental content
		$content .= hnd_get_release_notes( $markup = true );
		$content .= hnd_get_track_list();
		
		// press ???
		
		// sharing display (move elsewhere ???)
		if( function_exists('sharing_display') ) {
			$content .= '<div class="sharing-display">';
			$content .= '<h3 class="page-title">Share</h3>';
			$content .= sharing_display();
			$content .= '</div><!--/.sharing-display-->';
		}
	}
	
	return $content;
}
add_filter( 'the_content', 'hnd_item_content' );

function hnd_get_nice_title( $title = null, $link = true ) {

	global $post;
	$title = ( $title ) ? $title : get_the_title( $post->ID );
	
	if( !$title )
		return;

	$delimiter = ' &#8211; ';
	$title_parts = explode( $delimiter, $title );

	// no link on single item pages
	if( is_single() && in_the_loop() && hnd_is_item() ) $link = false;
	
	// artist
	$title1 = '<h1 class="nice-title">';
	if( $link ) $title1 .= '<a href="' . get_permalink() . '" title="' . $title . '">';
	$title1 .= $title_parts[0];
	if( $link ) $title1 .= '</a>';
	$title1 .= '</h1>';
	
	// title and format
	$title2 = '<h2 class="nice-title">';
	if( $link ) $title2 .= '<a href="' . get_permalink() . '" title="' . $title . '">';
	$title2 .= $title_parts[1];
	if( $title_parts[2] ) $title2 .= $delimiter . $title_parts[2];
	if( $link ) $title2 .= '</a>';
	$title2 .= '</h2>';
	
	$title = $title1 . $title2;
	
	return $title;
}

function hnd_nice_title( $title = null, $link = true ) {
	echo hnd_get_nice_title( $title, $link );
}

// consolidate Labels/Genres/Features taxonomy functions
function hnd_get_taxonomy( $taxonomy = null ) {

	// define available taxonomies
	$taxonomies = array( 'years', 'formats', 'labels', 'genres', 'features' );

	if( !$taxonomy || !in_array( $taxonomy, $taxonomies ) )
		return;

	$html = '';
	$terms = get_the_term_list( get_the_ID(), $taxonomy, null, ', ', null );
	$num_terms = count( wp_get_post_terms( get_the_ID(), $taxonomy ) );

	if( $terms ) {
		$tax_label = ( $num_terms > 1 && $taxonomy !== 'formats' ) ? $taxonomy : substr( $taxonomy, 0, -1 );
		$html .= '<div class="tax-spec ' . $taxonomy . '">';
		$html .= '<span class="label">' . ucwords( $tax_label ) . ':</span>';
		$html .= $terms;
		$html .= '</div>';
	}
	
	return $html;
}

function hnd_taxonomy( $taxonomy = null ) {
	echo hnd_get_taxonomy( $taxonomy );
}

function hnd_get_taxonomy_specs() {
	$content = '';
	$content .= '<div class="tax-specs">';
	$content .= hnd_get_taxonomy( 'formats' );
	$content .= hnd_get_taxonomy( 'years' );
	$content .= hnd_get_taxonomy( 'labels' );
	$content .= hnd_get_taxonomy( 'genres' );
	$content .= hnd_get_taxonomy( 'features' );
	$content .= '</div><!--/.tax-specs-->';
	return $content;
}

function hnd_taxonomy_specs() {
	echo hnd_get_taxonomy_specs();
}


/*******************************/

/* NEW FUNCTION FOR TAXONOMIES */
// to replace taxonomy_specs

function hnd_get_item_meta( $post_id = null ) {
    
    $post_id = ( !empty( $post_id ) ) ? $post_id : get_the_ID();
    
    $term_array = array(
        //'artists' => 'fa-tag',
        'formats' => 'fa-archive',
        'years' => 'fa-calendar',
        'labels' => 'fa-folder-open',
        //'venues' => 'fa-building',
        //'locations' => 'fa-map-marker',
		'genres' => 'fa-music',
		'features' => 'fa-tag',
    );

    $html_array = array();
    
    // loop through terms and add to array
    foreach( $term_array as $term => $class ) {
        
        $string = '';
		
		/*
        if( $term == 'locations' ) {
            
            // filter locations so they display more logically
            $locations = get_the_terms( $post_id, $term );
            $cities = array();
            $states = array();
            $state_strings = array();

            if( $locations ) {
                foreach( $locations as $location ) {
                    if( $location->parent ) {
                        $cities[] = $location;
                    } else {
                        $states[] = $location;
                    }
                }
            }
            
            if( $states ) {
                foreach( $states as $state ) {
                    $city_strings = array();
                    foreach( $cities as $city ) {
                        if( $city->parent == $state->term_id ) {
                            $city_strings[] = '<a href="' . get_term_link( $city->term_id, $term ) . '">' . $city->name . '</a>';
                        }
                    }
                    $city_strings[] = '<a href="' . get_term_link( $state->term_id, $term ) . '">' . $state->name . '</a>';
                    $state_strings[] = join( ', ', $city_strings );
                    reset( $city_strings );
                }
            }

            if( count( $state_strings ) ) {
                $string = '<i class="fa ' . $class . '"></i>' . join( ' / ', $state_strings );
            }

        } else {
            
            // simple term list works fine for most taxonomies
            $string = get_the_term_list( $post_id, $term, '<i class="fa ' . $class . '"></i>', ', ');
        }
        
        */
		
		// COPIED FROM ELSE ABOVE
	    // simple term list works fine for most taxonomies
        $string = get_the_term_list( $post_id, $term, '<i class="fa ' . $class . '"></i>', ', ');
    
        if( !empty( $string ) ) $html_array[] = $string;
    }    

    $output = '';

    if( !empty( $html_array ) ) {
        $output .= '<div class="item-meta">';
        foreach( $html_array as $html ) $output .= '<span class="tag">' . $html . '</span>';
        $output .= ' </div><!--/.item-meta-->';
    }

    return $output;
}

function hnd_item_meta( $post_id = null ) {
	echo hnd_get_item_meta( $post_id );
}


/* Labels
-------------------------------------------------------------- */

function hnd_get_labels() {

	$html = '';
	$labels = get_the_term_list( get_the_ID(), 'labels', null, ' / ', null );
	
	if( $labels ) {	
		$html .= '<div class="labels">';
		$html .= '<span class="label">Labels</span>';
		$html .= $labels;
		$html .= '</div>';
	}
	
	return $html;
}

function hnd_labels() {
	echo hnd_get_labels();
}

/* Genres
-------------------------------------------------------------- */

function hnd_get_genres() {
	
	$html = '';
	$genres = get_the_term_list( get_the_ID(), 'genres', null, ' / ', null );
	
	if( $genres ) {	
		$html .= '<div class="genres">';
		$html .= '<span class="label">Genres</span>';
		$html .= $genres;
		$html .= '</div>';
	}
	
	return $html;
}

function hnd_genres() {
	echo hnd_get_genres();
}

/* Features
-------------------------------------------------------------- */

function hnd_get_features() {
	
	$html = '';
	$features = get_the_term_list( get_the_ID(), 'features', null, ' / ', null );
	
	if( $features ) {	
		$html .= '<div class="features">';
		$html .= '<span class="label">Features</span>';
		$html .= $features;
		$html .= '</div>';
	}
	
	return $html;
}

function hnd_features() {
	echo hnd_get_features();
}


/* Bandcamp Audio
-------------------------------------------------------------- */

function hnd_get_bandcamp_audio( $art = false ) {
	
	$bandcamp_audio = '';
	$bandcamp_ids = get_post_meta( get_the_ID(), 'bandcamp_release_id', true );
	
	if( empty( $bandcamp_ids ) ) {
		return;
	}
	
	// check for multiple ids
	$bandcamp_array = explode( ',', $bandcamp_ids );
	
	$bandcamp_audio .= '<div class="bandcamp-audio">';
	
	foreach( $bandcamp_array as $bc ) {
		
		// generate shortcode(s)
		$bandcamp_audio .= '[bandcamp width=100% height=42 album=' . $bc . ' size=small bgcol=ffffff linkcol=de270f';
		
		// display art
		if( !$art ) $bandcamp_audio .= ' artwork=none';
		
		// close bracket
		$bandcamp_audio .= ']';
	}
	
	$bandcamp_audio .= '</div><!--/.bandcamp-audio-->';
		
	return $bandcamp_audio;
}

/* Pressing Info
-------------------------------------------------------------- */

function hnd_get_serial( $markup = false ) {
	$serial = get_post_meta( get_the_ID(), 'serial', true );
	return $serial;
}

function hnd_serial( $markup = false ) {
	echo hnd_get_serial( $markup );
}

function hnd_get_release_date() {
	$release_date = get_post_meta( get_the_ID(), 'release_date', true );
	return $release_date;
}

function hnd_release_date() {
	echo hnd_get_release_date();
}

function hnd_get_pressing_color() {
	$pressing_color = get_post_meta( get_the_ID(), 'pressing_color', true );
	return $pressing_color;
}

function hnd_pressing_color() {
	echo hnd_get_pressing_color();
}

function hnd_get_pressing_qty() {
	$pressing_qty = get_post_meta( get_the_ID(), 'pressing_qty', true );
	if( $pressing_qty > 0 ) {
		return $pressing_qty;
	}
}

function hnd_pressing_qty() {
	echo hnd_get_pressing_qty();
}

function hnd_get_pressing_specs() {
	
	#$serial = hnd_get_serial();
	$color = hnd_get_pressing_color();
	$pressing = hnd_get_pressing_qty();
	
	$specs = array();
	
	#if( $serial && is_single() ) $specs[] = $serial;
	if( $color ) $specs[] = $color;
	if( $pressing ) $specs[] = $pressing;
	
	if( count( $specs ) ) {
		$html .= '<div class="pressing-specs">';
		$html .= implode( ' | ', $specs );
		$html .= '</div>';
	}
	
	return $html;
	
}

function hnd_pressing_specs() {
	echo hnd_get_pressing_specs();
}

function hnd_get_release_notes( $markup = false ) {
	
	if( !hnd_is_item() || hnd_is_package_deal() )
		return;
	
	$release_notes = get_post_meta( get_the_ID(), 'release_notes', true );
	
	if( $release_notes ) {
		if( $markup ) {
		
			$html = '';
			$html .= '<div class="release-notes">';
			$html .= '<h3 class="page-title">Release Notes</h3>';
			$html .= $release_notes;
			$html .= '</div>';
			
			return $html;
			
		} else {			
			return $release_notes;
		}
	} 
}

function hnd_release_notes( $markup = false ) {
	echo hnd_get_release_notes( $markup );
}

/* Track List
-------------------------------------------------------------- */

function hnd_get_track_list( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	if( get_post_type() !== 'items' )
		return;
	
	$html = '';
	$track_list = get_post_meta( $post->ID, 'track_list', true );
	
	if( $track_list ) {	
		if( hnd_has_item_audio( $post_id ) ) {
			$html .= hnd_get_item_audio( $post_id );			
		} elseif( $track_list ) {
			$html .= '<div class="clear"></div>';
			$html .= '<div class="track-list"><h3 class="page-title">Track List</h3>' . $track_list . '</div>';	
		}
	}
	
	return $html;
}

function hnd_track_list() {
	echo hnd_get_track_list();
}

/* Press
-------------------------------------------------------------- */

function hnd_get_item_press() {
	
	if( get_post_type() !== 'items' )
		return;
	
	$html = '';
	$posts_per_page = ( is_front_page() ) ? 3 : -1;
	
	$press = new WP_Query( array(
		'post_type' => 'press',
		'post_status' => 'publish',
		'posts_per_page' => $posts_per_page,
		'meta_query' => array(
			array(
				'key' => 'press_item',
				'value' => get_the_ID(),
				'compare' => 'IN',
			),	
		)
	) );
	
	if( $press->have_posts() ) :
	
		global $post;
		
		$html .= '<div class="press-list">';
		if( !is_front_page() ) $html .= '<h3 class="page-title">Press</h3>';
		
		# TODO: build show/hide press meta field
		
		while( $press->have_posts() ) : $press->the_post();
			//$html .= hnd_get_featured_image();
			$html .= '<a href="' . get_permalink() . '"> ' . get_the_excerpt() . '</a>';
			$html .= '<h2><a href="' . get_permalink() . '">' . $post->post_title . '</a></h2>';
			$html .= '<hr>';
		endwhile;
		
		$html .= '</div>';
	
	endif;
	
	wp_reset_query();
	
	return $html;
}

function hnd_item_press() {
	echo hnd_get_item_press();
}

/* Discography
-------------------------------------------------------------- */

function hnd_is_upcoming_release() {
	if( ( get_post_status() == 'draft' ) || ( get_post_status() == 'future' ) ) {
		return true;
	} else {
		return false;
	}
}

function hnd_get_handstand_releases() {
	
	$post_status = ( is_user_logged_in() ) ? array('publish','future','draft') : array('publish','future');
	#$post_status = array('publish');
	
	$hnd_releases = new WP_Query( array(
		'post_status' => $post_status,
		'post_type' => 'items',
		
		// order by serial number descending
	   	'meta_key' => 'serial',
	   	'orderby'=> 'meta_value',
	   	'order' => 'DESC',

		'posts_per_page' => -1,
		'tax_query' => array(
			'relation' => 'AND',
			// Handstand Records release
			array(
				'taxonomy' => 'labels',
				'field' => 'slug',
				'terms' => 'handstand-records',
				'operator' => 'IN'
			),
			// exclude package deals
			array(
				'taxonomy' => 'formats',
				'field' => 'slug',
				'terms' => 'package-deal',
				'operator' => 'NOT IN'
			),
			// exclude merch
			array(
				'taxonomy' => 'formats',
				'field' => 'slug',
				'terms' => 'merch',
				'operator' => 'NOT IN'
			)
		)
	) );
	
	return $hnd_releases;
}

function hnd_get_handstand_merch() {
	
	$hnd_releases = new WP_Query( array(
		'post_status' => array( 'publish' ),
		'post_type' => 'items',
		
		// order by serial number descending
	   	#'meta_key' => 'serial',
	   	#'orderby'=> 'meta_value',
	   	#'order' => 'DESC',

		'posts_per_page' => -1,
		'tax_query' => array(
			'relation' => 'AND',
			// Handstand Records release
			array(
				'taxonomy' => 'labels',
				'field' => 'slug',
				'terms' => 'handstand-records',
				'operator' => 'IN'
			),
			// exclude package deals
			array(
				'taxonomy' => 'formats',
				'field' => 'slug',
				'terms' => 'package-deal',
				'operator' => 'NOT IN'
			),
			// exclude merch
			array(
				'taxonomy' => 'formats',
				'field' => 'slug',
				'terms' => 'merch',
				'operator' => 'IN'
			)
		)
	) );
	
	return $hnd_releases;
}

function hnd_get_handstand_upcoming_releases() {
	
	$hnd_upcoming_releases = new WP_Query( array(
		'post_status' => array( 'draft', 'future' ),
		'post_type' => 'items',
		'order' => 'ASC',
		'posts_per_page' => -1,
		'tax_query' => array(
			'relation' => 'AND',
			// Handstand Records release
			array(
				'taxonomy' => 'labels',
				'field' => 'slug',
				'terms' => 'handstand-records',
				'operator' => 'IN'
			),
			// exclude package deals
			array(
				'taxonomy' => 'formats',
				'field' => 'slug',
				'terms' => 'package-deal',
				'operator' => 'NOT IN'
			)
		)
	) );
	
	return $hnd_upcoming_releases;
}

function hnd_get_release_title() {

	$title = get_the_title();
	$title_parts = explode( ' &#8211; ', $title );
	
	$html = '';
	
	// artist
	$html .= '<h1><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . $title_parts[0] . '</a></h1>';
	
	// title and format
	$html .= '<h2><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . $title_parts[1] . '</a></h2>';
	
	return $html;
}

function hnd_release_title() {
	echo hnd_get_release_title();
}

function hnd_get_release_specs() {

	$serial = hnd_get_serial();
	$release_date = date( 'M  j, Y', strtotime( hnd_get_release_date() ) );
	
	$html = '';
	$specs = array();
	
	if( $serial ) $specs[] = $serial;
	if( $release_date ) $specs[] = 'Released ' . $release_date;
	
	if( count( $specs ) ) {
		$html .= '<div class="release-specs">';
		$html .= implode( ' | ', $specs );
		$html .= '</div>';
	}
	
	return $html;
}

function hnd_release_specs() {
	echo hnd_get_release_specs();
}

/* Store
-------------------------------------------------------------- */

function hnd_is_store_page() {
	$url = $_SERVER['REQUEST_URI'];
	if( strpos( $url, '/store' ) === false ) {
		return false;
	} else {
		return true;
	}
}

function hnd_get_button_specs( $inline = false ) {
	
	$html = '';

	$class = ( $inline ) ? 'inline' : '';
	
	if( get_post_type() == 'items' ) {
		$html .= '<div class="button-specs ' . $class . '">';
		$html .= hnd_get_buy_button();
		$html .= hnd_get_download_button();
		#$html .= sharing_display();
		$html .= '</div>';
	}
	
	return $html;
}

function hnd_button_specs( $inline = false ) {
	echo hnd_get_button_specs( $inline );
}

function hnd_get_paypal_title( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$title = get_the_title( $post_id ) . '(' . hnd_get_pressing_color( $post_id ) . ')';
	
	# TODO: for package deals, list all items
	
	// replace special characters
	$title = str_replace( '&#8211;', '-', $title ); // dash
	$title = str_replace( array( '&#8242;', '&#8217;' ), array( '\'', '\''), $title ); // single quotes
	$title = str_replace( '&#8243;', '-INCH', $title ); // double quotes
	
	// remove tags and text (flags)
	$title = preg_replace( "~(<(?:[^>]*?)>(?:.+?)<\/(?:[^>]*?)>)~", '', $title ); 

	return $title;	
}

function hnd_in_stock( $post_id = null ) {

	$bool = false;
	
	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$stock = get_post_meta( $post_id, 'stock', true );
	
	if( $stock > 0 ) {
		$bool = true;
	}
	
	return $bool;	
}

function hnd_get_price( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$price = get_post_meta( $post_id, 'price', true );
	$sale_price = get_post_meta( $post_id, 'sale_price', true );
	
	// check for sale price
	if( ( $sale_price > 0 ) && ( $sale_price < $price ) ) {
		$price = $sale_price;
	}
	
	return $price;
}

function hnd_on_sale( $post_id = null ) {

	$bool = false;
	
	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$price = get_post_meta( $post_id, 'price', true );
	$sale_price = get_post_meta( $post_id, 'sale_price', true );
	
	if( ( $sale_price > 0 ) && ( $sale_price < $price ) ) {
		$bool = true;
	}
	
	return $bool;	
}

function hnd_get_buy_button( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	if( !hnd_is_item( $post_id ) )
		return;
	
	$html = '';	
	$price = get_post_meta( $post_id, 'price', true );
	$sale_price = get_post_meta( $post_id, 'sale_price', true );
	
	if( hnd_in_stock() ) {
		
		// build shortcode
		
		// original
		#$shortcode_attrs = 'name="' . hnd_get_paypal_title() . '" price="' . hnd_get_price( $post_id ) . '" item_number="' . get_the_ID() . '" thumbnail="' . hnd_get_featured_image_src( $post_id ) . '"';
		
		// add base shipping cost
		$shortcode_attrs = 'name="' . hnd_get_paypal_title() . '" price="' . hnd_get_price( $post_id ) . '" shipping="0.0001" item_number="' . get_the_ID() . '" thumbnail="' . hnd_get_featured_image_src( $post_id ) . '"';
		
		// add size option to shirts
		if( has_term( 'T-Shirt', 'formats', $post_id ) ) {
		
			// get sizes
			$sizes = get_the_terms( $post_id, 'sizes' );
					
			// add size option
			if( count( $sizes ) ) {
				$size_array  = array();
				
				foreach( $sizes as $size ) {
					$size_array[] = $size->name;
				}
		
				$shortcode_attrs .= ' var1="SIZE|' . implode( '|', $size_array ) . '"';
			}			
		}

		// savings		 
		if( hnd_is_package_deal() && is_single() ) {
			$html .= '<div class="package-savings">SAVE $' . hnd_get_package_deal_savings() . '!</div>';
		}

		if( hnd_item_in_cart( $post_id ) ) {

			// item is in cart
			$html .= '<a class="button added-to-cart" href="' . get_bloginfo('url') . '/store/shopping-cart/">Added To Cart</a>';
	
		} else {

			// generate paypal button
			$html .= do_shortcode('[wp_cart_button ' . $shortcode_attrs . ']');
				
		}
		
	} else {

		$submit_text = ( hnd_is_upcoming_release() ) ? 'Out Soon' : 'Out Of Stock';
		$html .= '<div class="button ' . hnd_slugify( $submit_text ) . '">' . $submit_text . '</div>';
	
	}
	
	return $html;
}

function hnd_buy_button() {
	echo hnd_get_buy_button();
}

function hnd_get_more_info() {
	return '<a class="button more-info" href="' . get_permalink() . '" title="' . get_the_title() . '">More Info</a>';
}

function hnd_more_info() {
	echo hnd_get_more_info();
}

function hnd_item_in_cart( $item_id = null ) {

	$bool = false;

	if( $item_id && $_SESSION['simpleCart'] ) {
		foreach( $_SESSION['simpleCart'] as $item ) {
			if( $item_id == $item['item_number'] ) {
				$bool = true;
			}
		}
	}

	return $bool;
}

function hnd_get_list_item( $post_id = null ) {
	
	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	remove_filter( 'the_title', 'hnd_flag_title' );
	
	$html = '';
	$html .= hnd_get_featured_image( $post_id, $size = 'small', $link = false, $colorbox = false );
	$html .= '<h4>' . get_the_title( $post_id ) . '</h4>';
	
	add_filter( 'the_title', 'hnd_flag_title' );

	return $html;
}

function hnd_list_item() {
	echo hnd_get_list_item();
}

/* Shipping Weight
-------------------------------------------------------------- */

function hnd_get_format_weight( $format ) {

	if( !$format )
		return;

	// get the term by id
	$term = get_term_by( 'id', $format, 'formats' );

	if( $term->term_id ) {
		$weight = pods_field( 'formats', $term->term_id, 'weight' );
		return $weight;
	}
}

function hnd_format_weight( $format ) {
	echo hnd_get_format_weight( $format );
}

function hnd_get_item_weight( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	// get item format
	$format = wp_get_post_terms( $post_id, 'formats' );
	
	if( $format[0]->term_id ) {
		$weight = hnd_get_format_weight( $format[0]->term_id );
		return $weight;
	}
}

function hnd_item_weight( $post_id = null ) {
	echo hnd_get_item_weight( $post_id );
}


/* Package Deals
-------------------------------------------------------------- */

function hnd_get_package_deal_items() {
	
	if( !hnd_is_package_deal() )
		return;
	
	$package_items = get_post_meta( get_the_ID(), 'package_items', false );
	
	if( !$package_items )
		return;
	
	$num_items = count( $package_items );
	
	$html = '';
	$html .= '<div class="package-deal-items">';
	
	foreach( $package_items as $item ) {
		$html .= '<div class="package-deal-item">' . hnd_get_list_item( $item['ID'] ) . '</div>';
	}
	
	$html .= '</div>';
	
	return $html;
}

function hnd_get_package_deal_savings() {

	if( !hnd_is_package_deal() )
		return;
		
	$package_items = get_post_meta( get_the_ID(), 'package_items', false );
	
	if( !$package_items )
		return;
	
	$total = 0;
	
	foreach( $package_items as $item ) {
		$total += hnd_get_price( $item['ID'] );
	}
	
	$savings = number_format( $total - hnd_get_price(), 2 );
		
	return $savings;
}

/* Taxonomy Archives
-------------------------------------------------------------- */

function hnd_tax_archive( $query ) {

	if( $query->is_main_query() && hnd_is_store_page() ) {
	    
	    if( is_tax( 'labels' ) ) {
	   		
	    	// set higher posts per page limit so we can display all label releases
	   		$query->set( 'posts_per_page', 50 );
	   		
	   		// order by serial number descending
	   		$query->set( 'meta_key', 'serial' );
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'order', 'DESC' );
	   	
	    } else {

	    	// order alphabetically by title
	   		$query->set( 'orderby', 'title' );
			$query->set( 'order', 'ASC' );

	    }

	    return;
	}
}
add_action( 'pre_get_posts', 'hnd_tax_archive', 1 );


/* Related Items
-------------------------------------------------------------- */

function hnd_get_related_items( $list = true ) {

	$html = '';
	$related_items = get_post_meta( get_the_ID(), 'related_items' );
			
	if( !empty( $related_items ) ) {
		
		# TODO: add graphic layout version for when $list is false
		# TODO: different layout for just 1 item
		
		$html .= '<div class="related-items">';
		
		foreach( $related_items as $item ) {
			
			if( !empty( $item['ID'] ) ) {
				$html .= '<div class="related-item">';
			
				if( is_user_logged_in() ) {
					#echo print_r( $item ) . '<hr>';
				}
				
				if( !empty( $item['ID'] ) ) {
					$html .= '<a href="' . get_the_permalink( $item['ID'] ) . '">' . hnd_get_featured_image( $item['ID'], 'thumbnail' ) . '</a>';
				}
				
				if( !empty( $item['post_title'] ) ) {
					$html .= '<strong><a href="' . get_the_permalink( $item['ID'] ) . '">' . $item['post_title'] . '</a></strong>';
				}
				if( !empty( $item['post_excerpt'] ) ) {
					$html .= '<br>' . $item['post_excerpt'];
				}
				$html .= '</div>';
			}
		
		}
		
		$html .= '</div><!--/.related-items-->';
	}
	return $html;
}

function hnd_related_items() {
	echo hnd_get_related_items();
}