<?php

##########
# EVENTS #
##########

function hnd_event_title( $title ) {

	// split artist names on single Event posts
	if( hnd_is_event() && in_the_loop() ) {
		$title = hnd_split_event_artists( $title );
	}
	
	return $title;	
}
add_filter( 'the_title', 'hnd_event_title' );

function hnd_split_event_artists( $title ) {

	$artists = explode( ' / ', $title );
	$title = '';

	foreach( $artists as $key => $value ) {
		$class = ( $key % 2 == 0 ) ? 'odd' : 'even';
		$title .= '<span class=' . $class . '>';
		
		// treatment for parenthesis
		$regex = '#(\((?:[^\)]*?)(?:.+?)\))#';
		$replacement = '<span class="thin">\1</span>';
		$value = preg_replace($regex, $replacement, $value );

		// treatment for 'cancelled'
		$value = str_replace( '<span class="thin">(CANCELLED)</span>', '</span><span class="cancelled">(CANCELLED)</span>', $value );

		$title .= $value;
		$title .= '</span>';
	}

	return $title;
}

function hnd_event_excerpt( $output ) {
	
	if( hnd_is_event() && in_the_loop() ) {
	
		global $post;
		
		// get event meta
		$excerpt = $post->post_excerpt;
		$date = hnd_get_event_date();
		$venue = hnd_get_event_venue();
		$time = hnd_get_event_time();
		$price = hnd_get_event_price();
			
		$output = '';

		if( hnd_event_is_cancelled() ) {
			$output .= '<div class="cancelled-event">CANCELLED</div>';
		}

		if( is_single() ) {
			
			$output .= '<div class="event-details">';
			
			// date and venue
			if( $date ) $output .= hnd_get_event_date();
			if( $venue ) $output .= ' @ ' . $venue;

			// only show time and price on upcoming events
			if( hnd_is_upcoming_event( $date ) ) {
				$output .= hnd_get_event_details();
			}
			
			// excerpt
			if( $excerpt ) $output .= '<div class="event-excerpt">' . $excerpt . '</div>';

			$output .= '</div><!--/.event-details-->';

		}

		// add to calendar button
		if( hnd_is_upcoming_event( $date ) ) {
			$output .= hnd_event_get_calendar_button();
		}
	}
		
	return $output;
}
add_filter( 'get_the_excerpt', 'hnd_event_excerpt' );

function hnd_event_content( $content ) {
	
	if( hnd_is_event() && ( is_single() || in_the_loop() ) ) {
	
		$content = hnd_get_event_content( $content );
	
		if( hnd_event_is_cancelled() ) {
			$content .= '<div class="cancelled-event">CANCELLED</div>';
		}
		
	}
	
	return $content;
}
add_filter( 'the_content', 'hnd_event_content' );

function hnd_get_event_content( $content ) {

	$orig_content = $content;

	$content = '';
	
	// event date
	$content .= '<div class="event-date">';
	$content .= hnd_get_event_date( get_the_ID(), $full = true );

	// time and price
	if( hnd_is_upcoming_event() ) {
		if( hnd_get_event_time() || hnd_get_event_price() ) $content .= '<br>';
		if( hnd_get_event_time() ) {
			$content .= hnd_get_event_time();
		}
		if( hnd_get_event_price() ) {
			$content .= ' &mdash; ' . hnd_get_event_price();
		}
	}

	$content .= '</div><!--/.event-date-->';
	
	// venue details
	$content .= '<div class="event-venue">';
	
	// venue name
	$content .= hnd_get_event_venue();
	
	if( hnd_get_event_location() ) {
		$content .= hnd_get_event_location();
		$content .= '<p><a href="https://www.google.com/maps/preview/place/' . hnd_get_event_location_map() . '" target="_blank">MAP</a></p>';
	}
	$content .= '</div>';

	// age limit
	if( hnd_get_event_age_limit() ) {
		$content .= '<span class="event-age-limit">' . hnd_get_event_age_limit() . '</span>';
	}

	// add to calendar button
	$content .= hnd_event_get_calendar_button();

	if( !is_front_page() )
		$content .= '<div class="event-content">' . $orig_content . '</div>';

	return $content;
}

function hnd_is_upcoming_event( $date = null ) {
	
	$date = ( !empty( $date ) ) ? $date : hnd_get_event_date();
	if( empty( $date ) )
		return;

	// compare event date with todays date
	if( strtotime( $date ) > strtotime( date( 'M j, Y' ) ) ) {
		return true;
	} else {
		return false;
	}
}

function hnd_get_event_details( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;

	if( !hnd_is_upcoming_event( $post_id ) )
		return;
	
	$time = hnd_get_event_time( $post_id );
	$price = hnd_get_event_price( $post_id );
	$age_limit = hnd_get_event_age_limit( $post_id );
	
	$details = array();

	if( $time ) $details[] = $time;
	if( $price && ( $price > 0 ) ) $details[] = $price;
	if( $age_limit ) $details[] = $age_limit;

	return implode( ' | ', $details );
}

function hnd_event_details( $post_id = null ) {
	echo hnd_get_event_details( $post_id );
}

function hnd_get_event_date( $post_id = null, $full = false ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	// custom field (OLD)
	//$date = strtotime( get_post_meta( get_the_ID(), 'event_date', true ) );
	
	// post date (NEW)
	$date = strtotime( get_the_date() );

	if( $full ) {
		$date = date( 'D M j, Y', $date );	
	} else {
		$date = date( 'M j, Y', $date );
	}

	if( $date ) {
		return $date;
	}
}

function hnd_event_date( $post_id = null ) {
	echo hnd_get_event_date( $post_id );
}

function hnd_get_event_venue( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$venue_array = get_post_meta( $post_id, 'event_venue', true );
	$venue = $venue_array['post_title'];
	
	return $venue;
}

function hnd_event_venue( $post_id = null ) {
	echo hnd_get_event_venue( $post_id );
}

function hnd_get_event_location( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$venue_array = get_post_meta( $post_id, 'event_venue', true );
	$location = get_post_meta( $venue_array['ID'], 'address', true );
	
	return $location;
}

function hnd_event_location( $post_id = null ) {
	echo hnd_get_event_location( $post_id );
}

function hnd_get_event_location_map() {
	$location = hnd_get_event_location();
	$map_location = str_replace( array( '<p>', '</p>', ',', '<br>', '<br />', ' ' ), array( '', '', '', '+', '+', '+' ), $location );
	return $map_location;
}

function hnd_get_event_time( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$time = get_post_meta( $post_id, 'event_time', true );
	
	if( $time ) {
		return $time;
	}
}

function hnd_event_time( $post_id ) {
	echo hnd_get_event_time( $post_id );
}

function hnd_get_event_price( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$price = get_post_meta( $post_id, 'event_price', true );
	
	if( $price && ( $price > 0 ) ) {
		return $price;
	}
}

function hnd_event_price( $post_id = null ) {
	echo hnd_get_event_price( $post_id );
}

function hnd_get_event_age_limit( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$age_limit = get_post_meta( $post_id, 'event_age_limit', true );
	
	if( $age_limit ) {
		return $age_limit;
	}
}

function hnd_event_age_limit( $post_id = null ) {
	echo hnd_get_event_age_limit( $post_id );
}

function hnd_get_event_link( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$link = get_post_meta( $post_id, 'event_link', true );
	
	if( $link ) {
		return $link;
	}
}

function hnd_event_link( $post_id = null ) {
	echo hnd_get_event_link( $post_id );
}

function hnd_event_is_cancelled( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$cancelled = get_post_meta( $post_id, 'event_cancelled', true );
	
	if( $cancelled ) {
		return true;
	} else {
		return false;
	}
}

function hnd_event_get_calendar_button( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;

	if( !hnd_is_upcoming_event() )
		return;

	// construct event content
	$description = hnd_get_event_details( $post_id ) . $post->post_content;
	
	$html = '<div class="add-to-calendar"><a href="' . get_permalink( $post_id ) . ' title="Add to Calendar" class="addthisevent">
	    Add to Calendar
		<span class="_date_format">DD/MM/YYYY</span>
		<span class="_start">10-05-2014 11:38:46</span>
		<span class="_end">11-05-2014 11:38:46</span>
		<span class="_zonecode">15</span>
		<span class="_summary">' . $post->post_title . '</span>
		<span class="_description">' . $description . '</span>
		<span class="_location">' . hnd_get_event_venue( $post_id ) . '</span>
		<span class="_organizer">Organizer</span>
		<span class="_organizer_email">Organizer e-mail</span>
		<span class="_facebook_event">' . hnd_get_event_link( $post_id ) . '</span>
		<span class="_all_day_event">false</span>
	</a></div>';

	return $html;
}

function hnd_event_calendar_button( $post_id = null ) {
	echo hnd_event_get_calendar_button( $post_id );
}

// How to show "future" articles (events) to all visitors
function hnd_show_upcoming_events( $posts ) {
	global $wp_query, $wpdb;
	if( is_single() && is_main_query() && $wp_query->post_count == 0 ) {
		$posts = $wpdb->get_results( $wp_query->request );
	}
	return $posts;
}
add_filter( 'the_posts', 'hnd_show_upcoming_events' );