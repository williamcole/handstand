<?php

#########
# PRESS #
#########

// append Press post title with release info
function hnd_press_title( $title ) {
	
	if( hnd_is_press() && in_the_loop() ) {
		$press_item = get_post_meta( get_the_ID(), 'press_item', true );
		$item_title = $press_item['post_title'];
		if( $item_title ) {
			if( is_single() ) {
				$title .= ' review:<br><span class="press-item">' . $item_title . '</span>';
			} else {
				// TODO: temp fix
				$title .= ' review of ' . $item_title;
			}
		}	
	}

	return $title;	
}
add_filter( 'the_title', 'hnd_press_title' );

// add blockquote to press excerpt
function hnd_press_excerpt( $content ) {
	
	if( hnd_is_press() ) {
		$content = '<blockquote>' . $content . '</blockquote>';
	}

	return $content;
}
add_filter( 'get_the_excerpt', 'hnd_press_excerpt' );

// add blockquote to press content
function hnd_press_content( $content ) {
	
	if( hnd_is_press() ) {
		$content = '<blockquote>' . $content . '</blockquote>';
		$content .= hnd_get_press_item();
	}

	return $content;
}
add_filter( 'the_content', 'hnd_press_content' );

// get item attached to this press
function hnd_get_press_item() {

	if( !hnd_is_press() )
		return;

	$item = get_post_meta( get_the_ID(), 'press_item', true );

	$html = '';

	$press_items = new WP_Query( array(
		'post_type' => 'items',
		'posts_per_page' => 1,
		'post__in' => array( $item['ID'] ),
	) );
	
	if( isset( $press_items ) && $press_items->have_posts() ) {
		$html .= '<hr>';
		while( $press_items->have_posts() ) : $press_items->the_post();
			$html .= hnd_get_featured_image();
			$html .= '<h2>' . get_the_title() . '</h2>';
			$html .= get_the_excerpt();
		endwhile;
	}
	
	wp_reset_query();
	
	return $html;
}