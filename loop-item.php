<?php
	
	###########
	# CLASSES #
	###########
	
	// default classes
	$classes = array( 'loop-entry', 'grid', 'clearfix' );
	
	// post type class
	$classes[] = hnd_get_post_type();
	
	// upcoming event
	if( hnd_is_upcoming_event() || get_post_status() == 'draft' ) $classes[] = 'upcoming-event';
	
	###########
	# CONTENT #
	###########
	
	$date = $title = $content = $image = '';
	
	// determine image size
	switch( hnd_get_post_type() ) {
		case 'events': $image_size = 'events'; break;
		case 'press': $image_size = 'press'; break;
		default: $image_size = 'medium'; break;
	}

	// image
	if( has_post_thumbnail() || hnd_is_package_deal() || hnd_is_download() ) {
		
		// featured image
		$image = hnd_get_featured_image( get_the_ID(), $image_size );
		
		// package deals
		if( hnd_is_package_deal() ) {
			#$image .= hnd_get_package_deal_images();
		}
		
		// add disc image discography and featured labels
		if( hnd_is_item() && hnd_has_disc_image() && (
			is_page_template('template-discography.php') || is_page_template('template-store-featured-label.php')
		) ) {
			$image .= '<div class="disc-image">' . hnd_get_disc_image( get_the_ID(), $image_size ) . '</div>';
		}
		
	}

	// date
	if( hnd_is_news() || hnd_is_event() ) {
		
		$date = '<div class="post-date">';
		
		$date .= get_the_date();
		
		if( hnd_is_event() ) {
			
			$venue = hnd_get_event_venue();
			$details = hnd_get_event_details();

			if( $venue ) $date .= ' @ ' . $venue;
			if( $details && hnd_is_upcoming_event() ) $date .= '<br>' . $details;
									
		}

		$date .= '</div>';
	}

	// title
	if( hnd_is_item() || hnd_is_download() ) {
		$title = hnd_get_nice_title( get_the_title() );
	} else {
		$title_text = ( hnd_is_artist() ) ? hnd_get_artist_logo() : get_the_title();
		$title = '<h2><a href="' . get_permalink() . '" title="' . hnd_get_attribute_title() . '">' . $title_text . '</a></h2>';
	}
	
	// content
	if( hnd_is_item() ) {
		
		if( is_page_template('template-discography.php') || is_page_template('template-store-featured-label.php') ) {
			$content .= '<p class="release-specs">' . hnd_get_serial() . '</p>';
		}
		
		# TODO: fix price
		#$content .= hnd_get_buy_button();
		$content .= '<a class="green" href="' . get_permalink() . '">$' . hnd_get_price() . '</a>';
		
		# TESTING EXCERPT
		#if( hnd_is_press() ) {
			#$content = '<div class="nice-excerpt">' . hnd_press_excerpt() . '</div>';
			#$content = '<div class="nice-excerpt">' . get_the_excerpt() . '</div>';  //items
		#}
	}
	
	##########
	# MARKUP #
	##########
	
	// concatenate markup
	$markup = '<div class="grid-pad">' . $image . $date . $title . $content . '</div><!--/.grid-pad-->';
	
	// output
	echo '<article class="' . implode( ' ', $classes ) . '">' . $markup . '</article><!--/.loop-entry-->';
	
?>