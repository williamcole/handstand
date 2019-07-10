<?php

########
# NEWS #
########

// make sure More... link is displayed on archive page
function hnd_news_excerpt( $output ) {
	
	if( !is_single() && hnd_is_news() && in_the_loop() ) {
		$output .= '... <a href="' . get_permalink() . '" class="small">Read More &#9654;</a>';
	}
		
	return $output;
}
add_filter( 'get_the_excerpt', 'hnd_news_excerpt' );

// append item info to new posts
function hnd_news_content( $content ) {
	
	if( !is_front_page() && hnd_is_news() ) {
		$content .= hnd_get_related_items( $list = true );
	}
	
	return $content;
}
#add_filter( 'the_content', 'hnd_news_content' );


// get most recent batch of new arrivals relative to post date
function hnd_news_get_recent_arrivals( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	if( !hnd_is_news( $post_id ) )
		return;

	$year = get_the_date( 'Y' );
	$month = get_the_date( 'n' );
	$day = get_the_date( 'j' );

	// get recently added items relative to post date
	$items = new WP_Query( array(
		'post_type' => 'items',
		'order' => 'DESC',
		'posts_per_page' => -1,
		'date_query' => array(
			array(
				'year' => $year,
				'month' => $month,
				'day' => $day,
			),
		),
	) );

	// loop through items
	if( $items->have_posts() ) :
		
		$html = '<div class="owl-carousel owl-theme owl-carousel-news">';
		
		while( $items->have_posts() ) : $items->the_post();
			$html .= '<div class="carousel-item">' . hnd_get_featured_image( get_the_ID(), $size = 'small' ) . '</div>';
		endwhile;

		$html .= '</div>';
	
	endif;
	
	//wp_reset_query();
	
	return $html . '----';	
}

function hnd_news_recent_arrivals( $post_id = null ) {
	echo hnd_news_get_recent_arrivals( $post_id );
}