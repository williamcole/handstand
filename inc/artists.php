<?php

###########
# ARTISTS #
###########

function hnd_artist_content( $content ) {
	
	// artists
	if( hnd_is_artist() && is_single() ) {
		
		$orig_content = $content;
		$content = '';

		$active_link = ( get_query_var('view') ) ? get_query_var('view') : 'bio';
		
		switch( $active_link ) {

			case 'events': hnd_artist_events(); break;
			case 'flyers': hnd_artist_flyers(); break;			
			case 'photos': hnd_artist_photos(); break;
			case 'store': hnd_artist_items(); break;
			case 'videos': hnd_artist_videos(); break;
			default:			
				// bio tab
				$content .= hnd_get_artist_logo();
				$content .= hnd_get_artist_years();	
				$content .= hnd_get_artist_location();
				$content .= '<div class="artist-content">';
				$content .= hnd_get_artist_members();
				$content .= $orig_content;
				$content .= hnd_get_artist_related_bands();
				$content .= hnd_get_artist_links();
				$content .= '</div><!--/.artist-content-->';
			break;	
		}		
	}
	
	return $content;
}
add_filter( 'the_content', 'hnd_artist_content' );

function hnd_get_artist_logo( $post_id = null ) {
	
	global $post;
	
	$post_id = ( $post_id ) ? $post_id : $post->ID;	
	$logo = get_post_meta( $post_id, 'logo_image', true );
	
	if( !empty( $logo['guid']  ) ) {
		return '<img class="artist-logo " src="' . $logo['guid'] . '">';
	} else {
		return '<h1>' . strtoupper( get_the_title( $post_id ) ) . '</h1>';
	}
}

function hnd_artist_logo() {
	echo hnd_get_artist_logo();
}

function hnd_get_artist_years( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$html = '';
	
	$years_active = get_post_meta( $post_id, 'years_active', true );
	if( $years_active ) {
		$years_active = str_replace( '-', '&ndash;', $years_active );
		$html .= '<div class="artist-years">' . $years_active . '</div>';
	}
	
	return $html;
}

function hnd_artist_years( $post_id = null ) {
	echo hnd_get_artist_years( $post_id );
}

function hnd_get_artist_location( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$html = '';
	
	$location = get_post_meta( $post_id, 'location', true );
	if( $location ) {
		$html .= '<div class="artist-location">' . $location . '</div>';
	}
	
	return $html;
}

function hnd_artist_location( $post_id = null ) {
	echo hnd_get_artist_location( $post_id );
}

function hnd_get_artist_members( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$html = '';
	
	$members = get_post_meta( $post_id, 'members', true );
	
	// members
	if( $members ) {
		$html .= '<div class="artist-members">' . $members . '</div>';
	}
	
	return $html;
}

function hnd_artist_members( $post_id = null ) {
	echo hnd_get_artist_members( $post_id );
}

function hnd_get_artist_related_bands( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	$html = '';
	
	$pre_bands = get_post_meta( $post_id, 'pre_bands', true );
	$post_bands = get_post_meta( $post_id, 'post_bands', true );
	
	if( $pre_bands || $post_bands ) {

		// featuring members of
		if( $pre_bands ) {
			$html .= '<p class="artist-pre-bands">Featuring members of ' . $pre_bands . '. </p>';
		}

		// members went on to play in
		if( $post_bands ) {
			$html .= '<p class="artist-post-bands">Members went on to play in ' . $post_bands . '. </p>';
		}

	}
	
	return $html;
}

function hnd_artist_related_bands( $post_id = null ) {
	echo hnd_get_artist_related_bands( $post_id );
}

function hnd_get_artist_links() {
	
	$links = get_post_meta( get_the_ID(), 'links', true );
	
	if( $links ) { 
		// only apply content filters to photo content
		remove_filter( 'the_content', 'hnd_artist_content' );
		$html = '<div class="artist-links">' . apply_filters( 'the_content', $links ) . '</div>';
		add_filter( 'the_content', 'hnd_artist_content' );
	}

	return $html;	
}

function hnd_artist_links() {
	echo hnd_get_artist_links();
}

// adjust query to return posts with the artist in the title
function hnd_artists_posts_where( $where, $wp_query ) {
    global $wpdb;
    if( $artist_title = $wp_query->get( 'artist_title' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $artist_title ) ) . '%\'';
    }
    return $where;
}

function hnd_artist_items() {

	$artist_title = get_the_title();

	// get items with this artist name in the title
	add_filter( 'posts_where', 'hnd_artists_posts_where', 10, 2 );
  	$artist_items = new WP_Query( array(
		'post_type' => 'items',
		'posts_per_page' => -1,
		
		// order by serial number descending
	   	'meta_key' => 'serial',
	   	'orderby'=> 'meta_value',
	   	'order' => 'DESC',

		'artist_title' => $artist_title,
	) );
	remove_filter( 'posts_where', 'hnd_artists_posts_where', 10, 2 );
  	
	if( isset( $artist_items ) && $artist_items->have_posts() ) {
		while( $artist_items->have_posts() ) : $artist_items->the_post();
			get_template_part( 'loop', 'item' );
		endwhile;
	} else {
		echo '<h2>No Items Available</h2><hr>';
	}
	
	wp_reset_query();
}

// get events associated with current artist
function hnd_artist_events() {

	$artist_title = get_the_title();

	add_filter( 'posts_where', 'hnd_artists_posts_where', 10, 2 );
  	$artist_events = new WP_Query( array(
		'post_type' => 'events',
		'order' => 'DESC',
		'posts_per_page' => -1,
		'artist_title' => $artist_title,
	) );
	remove_filter( 'posts_where', 'hnd_artists_posts_where', 10, 2 );
  	
	# TODO: split into upcoming and past events

	if( isset( $artist_events ) && $artist_events->have_posts() ) {
		while( $artist_events->have_posts() ) : $artist_events->the_post();
			get_template_part( 'loop', 'item' );
		endwhile;
	} else {
		echo '<h2>No Upcoming Events</h2><hr>';
	}
	
	wp_reset_query();
}

function hnd_artist_flyers() {
	
	// get events associated with this artist
	$artist_title = get_the_title();
	add_filter( 'posts_where', 'hnd_artists_posts_where', 10, 2 );
  	$artist_events = new WP_Query( array(
		'post_type' => 'events',
		'order' => 'DESC',
		'posts_per_page' => -1,
		'artist_title' => $artist_title,
	) );
	remove_filter( 'posts_where', 'hnd_artists_posts_where', 10, 2 );

	if( isset( $artist_events ) && $artist_events->have_posts() ) {
		
		$flyer_ids = array();
		
		// build array of flyer ids
		while( $artist_events->have_posts() ) : $artist_events->the_post();
			if( has_post_thumbnail() ) $flyer_ids[] = get_post_thumbnail_id();
		endwhile;

		// flyer gallery
		if( count( $flyer_ids ) ) {
			
			// default gallery
			#$shortcode = '[gallery ids="' . implode( ',', $flyer_ids ) . '"]';			
			
			// tiled columns
			#$shortcode = '[gallery type="columns" link="file" ids="' . implode( ',', $flyer_ids ) . '"]';			
			
			// tiled mosaic
			$shortcode = '[gallery type="rectangular" link="file" ids="' . implode( ',', $flyer_ids ) . '"]';			
			
			
			
			// only apply content filters to flyers
			remove_filter( 'the_content', 'hnd_event_content' );
			echo apply_filters( 'the_content', $shortcode );
			add_filter( 'the_content', 'hnd_event_content' );
		}

	} else {
		echo '<h2>No Flyers Available</h2><hr>'; 
		echo '<p>Free free to <a href="mailto:will@handstandrecords.com">send us any flyers</a> we don\'t already have!</p>';
	}
	
	wp_reset_query();	
}


function hnd_artist_photos() {
	
	$photos = get_post_meta( get_the_ID(), 'photos', true );
	
	if( $photos ) { 
		// only apply content filters to photo content
		remove_filter( 'the_content', 'hnd_artist_content' );
		echo apply_filters( 'the_content', $photos );
		add_filter( 'the_content', 'hnd_artist_content' );
	} else {
		echo '<h2>No Photos Available</h2><hr>'; 
		echo '<p>Free free to <a href="mailto:will@handstandrecords.com">send us any photos</a> we don\'t already have!</p>';
	}	
}

function hnd_artist_videos() {
	
	$videos = get_post_meta( get_the_ID(), 'videos', true );
	
	// TODO: still needed ????
	
	if( $videos ) { 
		// only apply content filters to video content
		remove_filter( 'the_content', 'hnd_artist_content' );
		echo apply_filters( 'the_content', $videos );
		add_filter( 'the_content', 'hnd_artist_content' );
	} else {
		echo '<h2>No Videos Available</h2><hr>'; 
		echo '<p>Free free to <a href="mailto:will@handstandrecords.com">send us any videos</a> we don\'t already have!</p>';
	}	
}