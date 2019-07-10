<?php

#################
# MESSAGE BOARD #
#################

function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}

// TODO: combine functions below?

function hnd_get_vinyl( $size = 'all' ) {

	$items = array();
	$sizes = array( 'small', 'medium', 'large', 'all' );
	
	if( !in_array( $size, $sizes ) )
		return;

	switch( $size ) {
		case 'small': $formats = array( '7-inch', '2x7-inch' ); break;
		case 'medium': $formats = array( '8-inch', '10-inch', '10-inch-cd' ); break;
		case 'large': $formats = array( '12-inch', '12-inch-7-inch', '12-inch-cd', '2x12-inch', '3x12-inch' ); break;
		default: $formats = array( 'vinyl' ); break;
	}

	$item_query = new WP_Query( array(
		'post_type' => 'items',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key' => 'stock',
				'value' => 0,
				'compare' => '>',
				'type' => 'numeric'
			),	
		),
		'tax_query' => array(
			array(
				'taxonomy' => 'formats',
				'field' => 'slug',
				'terms' => $formats
			)
		)
	) );
	
	if( isset( $item_query ) && $item_query->have_posts() ) {
		while( $item_query->have_posts() ) : $item_query->the_post();
			$items[] = array( 'id' => get_the_ID(), 'title' => get_the_title() );
		endwhile;
		aasort( $items, 'title' );
	} else {
		$items = false;
	}

	wp_reset_query();

	return $items;
}

function hnd_get_cds() {

	$item_query = new WP_Query( array(
		'post_type' => 'items',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key' => 'stock',
				'value' => 0,
				'compare' => '>',
				'type' => 'numeric'
			),	
		),
		'tax_query' => array(
			array(
				'taxonomy' => 'formats',
				'field' => 'slug',
				'terms' => array( 'compact disc' )
			)
		)
	) );
	
	if( isset( $item_query ) && $item_query->have_posts() ) {
		while( $item_query->have_posts() ) : $item_query->the_post();
			$items[] = array( 'id' => get_the_ID(), 'title' => get_the_title() );
		endwhile;
		aasort( $items, 'title' );
	} else {
		$items = false;
	}

	wp_reset_query();

	return $items;
}

function hnd_get_dvds() {

	$item_query = new WP_Query( array(
		'post_type' => 'items',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key' => 'stock',
				'value' => 0,
				'compare' => '>',
				'type' => 'numeric'
			),	
		),
		'tax_query' => array(
			array(
				'taxonomy' => 'formats',
				'field' => 'slug',
				'terms' => array( 'dvd' )
			)
		)
	) );
	
	if( isset( $item_query ) && $item_query->have_posts() ) {
		while( $item_query->have_posts() ) : $item_query->the_post();
			$items[] = array( 'id' => get_the_ID(), 'title' => get_the_title() );
		endwhile;
		aasort( $items, 'title' );
	} else {
		$items = false;
	}

	wp_reset_query();

	return $items;
}

function hnd_get_tapes() {

	$item_query = new WP_Query( array(
		'post_type' => 'items',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key' => 'stock',
				'value' => 0,
				'compare' => '>',
				'type' => 'numeric'
			),	
		),
		'tax_query' => array(
			array(
				'taxonomy' => 'formats',
				'field' => 'slug',
				'terms' => array( 'cassette-tape' )
			)
		)
	) );
	
	if( isset( $item_query ) && $item_query->have_posts() ) {
		while( $item_query->have_posts() ) : $item_query->the_post();
			$items[] = array( 'id' => get_the_ID(), 'title' => get_the_title() );
		endwhile;
		aasort( $items, 'title' );
	} else {
		$items = false;
	}

	wp_reset_query();

	return $items;
}

function hnd_get_merch( $type = 'all' ) {
	
	$items = array();
	$types = array( 't-shirt', 'all' );
	
	if( !in_array( $type, $types ) )
		return;

	switch( $type ) {
		case 't-shirt': $formats = array( 't-shirt' ); break;
		default: $formats = array( 'merch' ); break;
	}

	$item_query = new WP_Query( array(
		'post_type' => 'items',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key' => 'stock',
				'value' => 0,
				'compare' => '>',
				'type' => 'numeric'
			),	
		),
		'tax_query' => array(
			array(
				'taxonomy' => 'formats',
				'field' => 'slug',
				'terms' => $formats
			)
		)
	) );
	
	if( isset( $item_query ) && $item_query->have_posts() ) {
		while( $item_query->have_posts() ) : $item_query->the_post();
			$items[] = array( 'id' => get_the_ID(), 'title' => get_the_title() );
		endwhile;
		aasort( $items, 'title' );
	} else {
		$items = false;
	}

	wp_reset_query();

	return $items;       
}

function hnd_get_deals() {

	$item_query = new WP_Query( array(
		'post_type' => 'items',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key' => 'stock',
				'value' => 0,
				'compare' => '>',
				'type' => 'numeric'
			),	
		),
		'tax_query' => array(
			array(
				'taxonomy' => 'formats',
				'field' => 'slug',
				'terms' => array( 'package-deal' )
			)
		)
	) );
	
	if( isset( $item_query ) && $item_query->have_posts() ) {
		while( $item_query->have_posts() ) : $item_query->the_post();
			$items[] = array( 'id' => get_the_ID(), 'title' => get_the_title() );
		endwhile;
		aasort( $items, 'title' );
	} else {
		$items = false;
	}

	wp_reset_query();

	return $items;        
}

// TODO: fix the merge in this function
function hnd_get_misc() {

	// combine
	$misc = array();

	$dvds = hnd_get_dvds();
	if( $dvds ) $misc = array_merge( $misc, $dvds );

	#$tapes = hnd_get_tapes();
	#if( $tapes ) $misc = array_merge( $misc, $tapes );
	
	$merch = hnd_get_merch( 't-shirt' );
	if( $merch ) $misc = array_merge( $misc, $merch );
	
	natcasesort($misc);
	
	return $misc;
}

function hnd_message_board_list_category( $title = null, $format = 'Text' ) {

	if( !isset( $title ) )
		return;

	$divider = "-------------------------";
	$linebreak = "\n";
	$markup = '';
	$markup .= $linebreak . $divider . $linebreak . strtoupper( $title ) . $linebreak . $divider . $linebreak . $linebreak;

	return $markup;
}

function hnd_message_board_list_item( $id = null, $format = 'Text' ) {

	if( !isset( $id ) )
		return;

	$title = get_the_title( $id );
	$price = hnd_get_price( $id );
	$link = get_permalink( $id );
	$color = get_post_meta( $id, 'pressing_color', true );
	#$features = get_the_terms( $id, 'features' );

	$markup = '';
	
	// title
	$markup .= $title;

	// vinyl color
	if( hnd_is_vinyl( $id ) && ( $color !== '' ) ) $markup .= ' (' . $color . ')';

	$markup .= ' - $' . $price;
	$markup .= "\n";
	
	/*
	// features
	$markup .= print_r( $features, true );

	// price & buy link
	$markup .= '$' . $price . ' | ';
	
	switch( $format ) {
		case 'Shortcode':
			$markup .= '[url=' . $link . ']BUY[/url]';
			break;
		case 'HTML':
			$markup .= '<a href="' . $link . '" target="_blank">BUY</a>';
			break;
		default:
			$markup .= 'BUY: ' . $link;
			break;
	}

	$markup .= "\n\n";
	
	*/

	return $markup;
}