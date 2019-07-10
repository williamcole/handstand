<?php

##########
# TRADES #
##########

// hook to create new items after Trade is published
function hnd_add_trade_items( $post ) {

	if( get_post_type() !== 'trades' )
		return;

	// get attached label
	$label = get_post_meta( $post->ID, 'contact', true );
	$label_slug = $label['post_name'];
	
	// get the trade content
	$trade_parts = explode( '<h1', $post->post_content );
	$trade_content = wpautop( $trade_parts[0] );
	$trade_items = explode( '<p>', $trade_content );
	$trade_items = hnd_clean_array( $trade_items );
	
	// loop through trade items and add to inventory
	foreach( $trade_items as $item ) {

		// set default title
		$title = trim( $item );
		
		// determine qty
		$qty = hnd_get_trade_item_qty( $item );

		// determine serial
		$serial = hnd_get_trade_item_serial( $item );
		
		// determine format
		#$format = hnd_get_trade_item_format( $item );
		 
		// determine title (strip qty and serial)
		$title = hnd_get_trade_item_title( $item );

		// add item
		$item_id = wp_insert_post( array(
			'post_type'      => 'items',
			'post_status'    => 'draft',
			'post_title'     =>  strip_tags( $title ),
		) );

		if( $item_id ) {

			# TODO: set formats
			#wp_set_post_terms( $item_id, $format, 'formats' );

			// set labels
			wp_set_post_terms( $item_id, $label_slug, 'labels' );

			// set meta values
			if( $qty ) update_post_meta( $item_id, 'stock', $qty );
			if( $serial ) update_post_meta( $item_id, 'serial', $serial );		
		}
	}

	// get inventory content
	$stock_parts = explode( '/h1>', $post->post_content );
	$stock_content = wpautop( end( $stock_parts ) );
	$stock_items = explode( '<p>', $stock_content );
	$stock_items = hnd_clean_array( $stock_items );

	// loop through Handstand items and update inventory
	foreach( $stock_items as $item ) {

		// determine qty
		$qty = hnd_get_trade_item_qty( $item );

		// determine serial
		$serial = hnd_get_trade_item_serial( $item );

		if( $qty && $serial ) {

			// get item from serial
			$hnd_items = new WP_Query( array(
				'post_type' => 'items',
				'posts_per_page' => 1,
				'meta_query'  => array(
					array(
						'key'     => 'serial',
						'value'   => $serial,
						'compare' => '=',
					)
				)				
			) );

			if( $hnd_items->have_posts() ) :
				while( $hnd_items->have_posts()) : $hnd_items->the_post();
					
					// get existing stock and reduce 
					$current_qty = get_post_meta( get_the_ID(), 'stock', true );
					$new_qty = $current_qty - $qty;
					update_post_meta( get_the_ID(), 'stock', $new_qty );

				endwhile;			
			endif;

			wp_reset_query();
		}		
	}
}
add_action( 'pending_to_publish', 'hnd_add_trade_items' );
add_action( 'draft_to_publish', 'hnd_add_trade_items' );

// helper function to extract title from item line
function hnd_get_trade_item_title( $item ) {

	// default title
	$title = $item;

	// get qty and serial to strip
	$qty = hnd_get_trade_item_qty( $item );
	$serial = hnd_get_trade_item_serial( $item );

	// strip qty from title
	if( $qty ) $title = str_replace( "$qty x ", '', $title );
	
	// strip serial from title
	
	# TODO: figure out why the hell this isnt working
	# if( $serial ) $title_sans_serial = str_replace( "[$serial]", '', $title_sans_qty );
	
	// hacky workaround to strip serial
	if( strpos( $title, '[' ) !== 0 ) {
		$serial_parts = explode( '[', $title );
		$title = $serial_parts[0];
	}

	$title = trim( $title );

	return $title;
}

// helper function to extract quantity from item line
function hnd_get_trade_item_qty( $item ) {
	
	$item_parts = explode( ' ', $item );
		
	// assume space (2 x)
	if( is_numeric( $item_parts[0] ) && ( $item_parts[1] == 'x' ) ) {
		$qty = (int) $item_parts[0];
	} elseif( is_numeric( substr( $item_parts[0], 0, -1 ) ) && ( substr( $item_parts[0], -1 ) == 'x' ) ) {
		$qty = (int) substr( substr( $item_parts[0], 0, -1 ) );
	} else {
		$qty = 1;
	}

	return $qty;
}

// helper function to extract format from item line
function hnd_get_trade_item_format( $item ) {
	
	$title = hnd_get_trade_item_title( $item );

	// TODO:
	$format = 'test';
	
	return $format;
}

// helper function to extract serial from item line
function hnd_get_trade_item_serial( $item ) {
	
	$item_parts = explode( ' ', $item );
	$last = end( $item_parts );

	if( substr( $last, 0, 1 ) == '[' ) {
		$serial = strip_tags( str_replace( array( '[', ']' ), array( '', '' ), $last ) );
	} else {
		$serial = false;
	}

	return $serial;
}

function hnd_clean_array( $array = null ) {

	if( !is_array( $array ) || empty( $array ) )
		return;

	$array = array_filter( $array );
	
	// remove blank lines from trade items
	foreach( $array as $key => $item ) {

		// remove closing paragraph tags
		$array[$key] = strip_tags( $item );

		// account for spaces
		$array[$key] = trim( str_replace( '&nbsp;', ' ', $item ) );

		// remove empty items
		if( empty( $item ) || ( $item == '' ) || ( $item == ' ' ) || ( $item == '&nbsp;' ) ) {
			unset( $array[$key] );
		}
	}

	return $array;
}

/* Trade List
-------------------------------------------------------------- */

function hnd_trade_list( $atts = null ) {
	
	$html = '';

	// get tradeable items
	$items = new WP_Query( array(
		'post_type' => 'items',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'order' => 'ASC',
		'meta_key' => 'serial',
		'orderby' => 'meta_value',
		'meta_query'  => array(
			array(
				'key'     => 'stock',
				'value'   => 10,
				'compare' => '>',
				'type' => 'numeric'
			)
		),
		'tax_query' => array(
            array(
            	'taxonomy' => 'formats',
            	'field' => 'slug',
            	'terms' => array( 't-shirt', 'button', 'sticker', 'digital', 'package-deal' ),
            	'operator' => 'NOT IN'
            )
        )
	) );
	
	if( $items->have_posts() ) :
		$html .= '<div id="trade-list">';
		while( $items->have_posts()) : $items->the_post();
			$html .= '<div class="trade-item">';
			//$html .= '	<input type="checkbox" name="trade_items[]" value="' . get_the_ID() . '" item="' . hnd_get_attribute_title() . '">';
			$html .= hnd_get_featured_image( get_the_ID(), 'trade', $link = false );
			$html .= '	<span class="title">' . get_the_title() . '</span>';
			$html .= '	<span class="serial">[' . get_post_meta( get_the_ID(), 'serial', true ) . ']</span>';
			$html .= '	<div class="excerpt">'. get_the_excerpt() . '</div>';
			$html .= '	<div class="clear"></div>';
			$html .= '</div>';
		endwhile;			
		$html .= '</div><!--/#trade-list-->';
	endif;
	
	wp_reset_query();

	return $html;  
}
add_shortcode( 'trade-list', 'hnd_trade_list' );