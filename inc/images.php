<?php

##########
# IMAGES #
##########

// fix http error on image upload
function hnd_change_graphic_lib( $array ) {
	return array( 'WP_Image_Editor_GD', 'WP_Image_Editor_Imagick' );
}
add_filter( 'wp_image_editors', 'hnd_change_graphic_lib' );


/* Featured Images
-------------------------------------------------------------- */

if (class_exists('MultiPostThumbnails')) {
    
    // items
    new MultiPostThumbnails(
        array(
            'label' => 'Back Image',
            'id' => 'back-image',
            'post_type' => 'items'
        )
    );
    
    new MultiPostThumbnails(
        array(
            'label' => 'Disc Image',
            'id' => 'disc-image',
            'post_type' => 'items'
        )
    );
    
    new MultiPostThumbnails(
        array(
            'label' => 'Display Image',
            'id' => 'display-image',
            'post_type' => 'items'
        )
    );
    
    new MultiPostThumbnails(
        array(
            'label' => 'Logo Image',
            'id' => 'logo-image',
            'post_type' => 'artists'
        )
    );
}

/* Image Sizes
-------------------------------------------------------------- */

add_image_size( 'tiny', 25, 25, true );
add_image_size( 'trade', 40, 40, true );
add_image_size( 'small', 100, 100, true );
add_image_size( 'related', 125, 125, true );
add_image_size( 'discography', 200, 200, false );
add_image_size( 'loop', 220, 9999, false );
add_image_size( 'medium', 300, 300, true );
add_image_size( 'news', 300, 9999, false );
add_image_size( 'events', 300, 300, false );
add_image_size( 'press', 300, 300, false );
#add_image_size( 'large', 480, 480, true );

/* Image Filters
-------------------------------------------------------------- */

/*
// search by file name
function hnd_image_guid_search( $search, $a_wp_query ) {
	
	global $wpdb, $pagenow;

	// Only Admin side && Only Media Library page
	if ( !is_admin() && 'upload.php' != $pagenow ) 
	//if ( !is_admin() )
		return $search;

	$string = $_GET['s'];
	//$string = $a_wp_query->query_vars['s'];

	// Original search string:
	// AND (((wp_posts.post_title LIKE '%search-string%') OR (wp_posts.post_content LIKE '%search-string%')))
	$search = str_replace(
		'AND ((', 
		'AND (((' . $wpdb->prefix . 'posts.guid LIKE \'%' . $string . '%\') OR ', 
		$search
	); 

	return $search;
}
add_filter( 'posts_search', 'hnd_image_guid_search', 10, 2 );
*/

/* Featured Image
-------------------------------------------------------------- */

function hnd_get_fallback_image( $post_id = null, $disc = false ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	// get fallback image for items, downloads only
	// no contacts, posts, events, press
	
	switch( get_post_type( $post_id ) ) {
		
		# TODO: add logic for fallback disc images
		
		case 'downloads':
			
			# TODO: zip box image with overlay?
			$fallback_img = hnd_get_featured_image_src( hnd_get_download_item_id() );
			
		break;

		case 'events':

			# TODO: create calendar background image with date inside
			
		break;

		case 'items':
			
			# TODO: get specific no-image depending on format (CD, Vinyl, etc)
			if( hnd_is_package_deal( $post_id ) ) {
				$fallback_img = get_stylesheet_directory_uri() . '/images/no-image-package.png';
			} else {
				if( $disc ) {
					$fallback_img = get_stylesheet_directory_uri() . '/images/no-image-vinyl.png';
				} else {
					$fallback_img = get_stylesheet_directory_uri() . '/images/no-image.png';
				}
			}

		break;
		
		default:
			$fallback_img = '';
		break;
			
	}
	
	return $fallback_img;
}

function hnd_get_event_fallback_image( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	// get event date
	$event_date = strtotime( hnd_get_event_date( $post_id ) );

	// TODO: add to calendar
	$calendar_link = ( is_single() ) ? 'Javscript://Add To Calendar' : get_permalink();

	// construct calendar html
	$fallback_img = '';
	$fallback_img .= '<div class="post-thumbnail events default">';
	$fallback_img .= '	<a href="' . $calendar_link . '" class="calendar">';
	$fallback_img .= '		<span class="calendar-year">' . date( 'M', $event_date ) . ' ' . date( 'Y', $event_date ) . '</span>';
	$fallback_img .= '		<span class="calendar-weekday long">' . date( 'l', $event_date ) . '</span>';
	$fallback_img .= '		<span class="calendar-weekday short">' . date( 'D', $event_date ) . '</span>';
	#$fallback_img .= '		<span class="calendar-month long">' . date( 'F', $event_date ) . '</span>';
	#$fallback_img .= '		<span class="calendar-month short">' . date( 'M', $event_date ) . '</span>';
	$fallback_img .= '		<span class="calendar-day">' . date( 'd', $event_date ) . '</span>';
	$fallback_img .= '	</a>';
	$fallback_img .= '</div>';

	return $fallback_img;
}

function hnd_get_featured_image_src( $post_id = null, $size = null ) {
	
	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	$size = ( $size ) ? $size : 'loop';

	$featured_img = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );	
	$fallback_img = hnd_get_fallback_image( $post_id );
	
	$img_src = ( $featured_img ) ? $featured_img[0] : $fallback_img;

	return $img_src;	
}

function hnd_get_featured_image( $post_id = null, $size = null, $link = true, $colorbox = false ) {
	
	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	$size = ( $size ) ? $size : 'loop';
	
	/*
	// kill the featured image if there is an image gallery
	if( hnd_is_news() && is_single() && has_shortcode( $post->post_content, 'gallery') ) {
		return;
	}
	*/

	// determine loop-entry image sizes
	if( in_the_loop() ) {
		if( hnd_is_event() ) {
			$size = 'events';
		}
		if( is_single() ) {
			$size = 'large';
		}
	}

	// append post type to image class
	$img_class = array();
	$img_class[] = 'post-thumbnail';
	$img_class[] = hnd_get_post_type();
	
	// append format slugs to image class
	if( hnd_is_item() ) {
		$formats = wp_get_post_terms( $post_id, 'formats' );
		if( count( $formats ) ) foreach( $formats as $format ) $img_class[] = $format->slug;
	}
		
	if( hnd_get_featured_image_src( $post_id, $size ) ) {

		$img = '<img src="' . hnd_get_featured_image_src( $post_id, $size ) . '" class="' . implode( ' ', $img_class ) . '">';
		
		if( $colorbox ) {
			$img = '<a href="' . hnd_get_featured_image_src( $post_id, 'full' ) . '" class="colorbox" rel="colorbox" title="' . hnd_get_attribute_title() . '">' . $img . '</a>';
		} elseif( $link ) {
			$img = '<a href="' . get_permalink( $post_id ) . '" title="' . hnd_get_attribute_title() .'">' . $img . '</a>';
		}
		
		return $img;
	
	} else {

		// custom fallback calendar image for events
		if( hnd_is_event() ) {
			return hnd_get_event_fallback_image( $post_id );
		}
	}
}

function hnd_featured_image( $post_id = null, $size = null, $link = true, $colorbox = false ) {
	echo hnd_get_featured_image( $post_id, $size, $link, $colorbox );
}

// TODO: something fancy for package deals
function hnd_get_package_deal_images( $post_id = null ) {
	
	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	if( !hnd_is_package_deal() )
		return;
	
	$package_items = get_post_meta( $post_id, 'package_items', false );
	
	if( !$package_items )
		return;
	
	$html = '';
	$images = array();
	$num_items = count( $package_items );
	
	foreach( $package_items as $item ) {
		$images[] = hnd_get_featured_image( $item['ID'], 'thumbnail', false );
	}
	
	$html .= '<div class="package-images num-' . $num_items . '">';
	$html .= implode( $images );
	$html .= '</div>';
	
	return $html;
}

/* Back Image
-------------------------------------------------------------- */

function hnd_has_back_image( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	if( class_exists( 'MultiPostThumbnails' ) ) {
		$back_img = MultiPostThumbnails::get_the_post_thumbnail( get_post_type(), 'back-image' );
	}	
	
	if( $back_img ) {
		return true;
	} else {
		return false;
	}

}

function hnd_get_back_image_src( $post_id = null, $size = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	$size = ( $size ) ? $size : 'loop';
	
	if( class_exists( 'MultiPostThumbnails' ) ) {
		$img_src = MultiPostThumbnails::get_post_thumbnail_url( get_post_type(), 'back-image', $post_id, $size );
	}

	$fallback_img = hnd_get_fallback_image( $post_id );
	$img_src = ( $img_src ) ? $img_src : $fallback_img;
	
	return $img_src;
}

function hnd_get_back_image( $post_id = null, $size = null, $link = true, $colorbox = false ) {
	
	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	$size = ( $size ) ? $size : 'loop';
	
	// append post type to image class
	$img_class = 'post-thumbnail ' . hnd_get_post_type();
	
	if( hnd_get_back_image_src( $post_id, $size ) ) {
		
		$img = '<img src="' . hnd_get_back_image_src( $post_id, $size ) . '" class="' . $img_class . '">';
		
		if( $link ) {
			$img = '<a href="' . get_permalink( $post_id ) . '" title="' . hnd_get_attribute_title() .'">' . $img . '</a>';
		} elseif( $colorbox ) {
			$img = '<a href="' . hnd_get_back_image_src( $post_id, 'full' ) . '" class="colorbox" rel="colorbox" title="' . hnd_get_attribute_title() . '">' . $img . '</a>';
		}
		
		return $img;
	}
}

function hnd_back_image( $post_id = null, $size = null, $link = true, $colorbox = false ) {
	echo hnd_get_back_image( $post_id, $size, $link, $colorbox );
}

/* Disc Image
-------------------------------------------------------------- */

function hnd_has_disc_image( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;

	if( class_exists( 'MultiPostThumbnails' ) ) {
		$disc_img = MultiPostThumbnails::get_the_post_thumbnail( get_post_type(), 'disc-image' );
	}	
	
	if( $disc_img ) {
		return true;
	} else {
		// check both disc image options
		$disc_img = get_post_meta( $post_id, 'disc_image', true );	
	
		if( $disc_img ) {
			return true;
		} else {
			return false;
		}	
	}
}

function hnd_get_disc_image_src( $post_id = null, $size = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	$size = ( $size ) ? $size : 'loop';

	if( class_exists( 'MultiPostThumbnails' ) ) {
		$img_src = MultiPostThumbnails::get_post_thumbnail_url( get_post_type(), 'disc-image', $post_id, $size );
	}

	// check both disc image options
	if( !$img_src ) {
		$disc_img = get_post_meta( $post_id, 'disc_image', true );	
		$img_src = $disc_img['guid'];
	}

	$fallback_img = hnd_get_fallback_image( $post_id, $disc = true );
	$img_src = ( $img_src ) ? $img_src : $fallback_img;
	
	return $img_src;
}

function hnd_get_disc_image( $post_id = null, $size = null, $link = true, $colorbox = false ) {
	
	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	$size = ( $size ) ? $size : 'loop';
	
	// append post type to image class
	$img_class = 'post-thumbnail disc-image ' . hnd_get_post_type();
	
	if( hnd_get_disc_image_src( $post_id, $size ) ) {
		
		$img = '<img src="' . hnd_get_disc_image_src( $post_id, $size ) . '" class="' . $img_class . '">';
		
		if( $link ) {
			$img = '<a href="' . get_permalink( $post_id ) . '" title="' . hnd_get_attribute_title() .'">' . $img . '</a>';
		} elseif( $colorbox ) {
			$img = '<a href="' . hnd_get_disc_image_src( $post_id, 'full' ) . '" class="colorbox" rel="colorbox" title="' . hnd_get_attribute_title() . '">' . $img . '</a>';
		}
		
		return $img;
	}
}

function hnd_disc_image( $post_id = null, $size = null, $link = true, $colorbox = false ) {
	echo hnd_get_disc_image( $post_id, $size, $link, $colorbox );
}

/* Display Image
-------------------------------------------------------------- */

function hnd_has_display_image( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;

	if( class_exists( 'MultiPostThumbnails' ) ) {
		$disc_img = MultiPostThumbnails::get_the_post_thumbnail( get_post_type(), 'display-image' );
	}	
	
	if( $disc_img ) {
		return true;
	} else {
		return false;
	}
}

function hnd_get_display_image_src( $post_id = null, $size = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	$size = ( $size ) ? $size : 'loop';

	if( class_exists( 'MultiPostThumbnails' ) ) {
		$img_src = MultiPostThumbnails::get_post_thumbnail_url( get_post_type(), 'display-image', $post_id, $size );
	}

	$fallback_img = hnd_get_fallback_image( $post_id, $disc = true );
	$img_src = ( $img_src ) ? $img_src : $fallback_img;
	
	return $img_src;
}

function hnd_get_display_image( $post_id = null, $size = null, $link = true, $colorbox = false ) {
	
	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	$size = ( $size ) ? $size : 'loop';
	
	// append post type to image class
	$img_class = 'post-thumbnail display-image ' . hnd_get_post_type();
	
	if( hnd_get_display_image_src( $post_id, $size ) ) {
		
		$img = '<img src="' . hnd_get_display_image_src( $post_id, $size ) . '" class="' . $img_class . '">';
		
		if( $link ) {
			$img = '<a href="' . get_permalink( $post_id ) . '" title="' . hnd_get_attribute_title() .'">' . $img . '</a>';
		} elseif( $colorbox ) {
			$img = '<a href="' . hnd_get_display_image_src( $post_id, 'full' ) . '" class="colorbox" rel="colorbox" title="' . hnd_get_attribute_title() . '">' . $img . '</a>';
		}
		
		return $img;
	}
}

function hnd_display_image( $post_id = null, $size = null, $link = true, $colorbox = false ) {
	echo hnd_get_display_image( $post_id, $size, $link, $colorbox );
}
