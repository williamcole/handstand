<?php

#############
# FUNCTIONS #
#############

/* Includes
-------------------------------------------------------------- */

require_once( dirname( __FILE__ ) . '/inc/admin.php' );
require_once( dirname( __FILE__ ) . '/inc/artists.php' );
require_once( dirname( __FILE__ ) . '/inc/audio.php' );
require_once( dirname( __FILE__ ) . '/inc/cart.php' );

if( is_admin() && is_wbc3() ) {
	#require_once( dirname( __FILE__ ) . '/inc/discogs-oauth.php' );
	#require_once( dirname( __FILE__ ) . '/inc/discogs.php' );
}

require_once( dirname( __FILE__ ) . '/inc/downloads.php' );
require_once( dirname( __FILE__ ) . '/inc/events.php' );
require_once( dirname( __FILE__ ) . '/inc/filters.php' );
require_once( dirname( __FILE__ ) . '/inc/helpers.php' );
require_once( dirname( __FILE__ ) . '/inc/images.php' );
require_once( dirname( __FILE__ ) . '/inc/items.php' );
require_once( dirname( __FILE__ ) . '/inc/message-board.php' );
require_once( dirname( __FILE__ ) . '/inc/navigation.php' );
require_once( dirname( __FILE__ ) . '/inc/news.php' );
require_once( dirname( __FILE__ ) . '/inc/notices.php' );
require_once( dirname( __FILE__ ) . '/inc/press.php' );
require_once( dirname( __FILE__ ) . '/inc/shortcodes.php' );
require_once( dirname( __FILE__ ) . '/inc/trades.php' );
require_once( dirname( __FILE__ ) . '/inc/widgets.php' );

/* CSS and JS
-------------------------------------------------------------- */

function hnd_enqueue_scripts() { 
	
	// impact
	wp_register_style( 'bebas', get_stylesheet_directory_uri() . '/fonts/Bebas-fontfacekit/bebas_regular_macroman/stylesheet.css' );
	wp_enqueue_style( 'bebas' );
	wp_register_style( 'impact-label', get_stylesheet_directory_uri() . '/fonts/Impact-Label-fontfacekit/impactlabel_regular_macroman/stylesheet.css' );
	wp_enqueue_style( 'impact-label' );
	wp_register_style( 'impact-label-reversed', get_stylesheet_directory_uri() . '/fonts/Impact-Label-fontfacekit/impactlabelreversed_regular_macroman/stylesheet.css' );
	wp_enqueue_style( 'impact-label-reversed' );
	
	// gothic
	wp_register_style( 'cartogothic-book', get_stylesheet_directory_uri() . '/fonts/CartoGothic-Std-fontfacekit/cartogothicstd_book_macroman/stylesheet.css' );
	wp_enqueue_style( 'cartogothic-book' );
	wp_register_style( 'cartogothic-bold', get_stylesheet_directory_uri() . '/fonts/CartoGothic-Std-fontfacekit/cartogothicstd_bold_macroman/stylesheet.css' );
	wp_enqueue_style( 'cartogothic-bold' );
	wp_register_style( 'cartogothic-italic', get_stylesheet_directory_uri() . '/fonts/CartoGothic-Std-fontfacekit/cartogothicstd_italic_macroman/stylesheet.css' );
	wp_enqueue_style( 'cartogothic-italic' );

	// droid sans (buttons)
	wp_register_style( 'droid-sans', 'http://fonts.googleapis.com/css?family=Droid+Sans');
    wp_enqueue_style( 'droid-sans');
    
	// colorbox
	wp_register_style( 'jquery-colorbox-css', get_stylesheet_directory_uri() . '/css/colorbox.css' );
	wp_enqueue_style( 'jquery-colorbox-css' );
	
	// font awesome
	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' ); 
	
	// cycle scripts
	wp_enqueue_script( 'jquery-cycle2', get_stylesheet_directory_uri() . '/js/jquery.cycle2.js', array( 'jquery' ) );
	wp_enqueue_script( 'jquery-cycle2-carousel', get_stylesheet_directory_uri() . '/js/jquery.cycle2.carousel.js', array( 'jquery-cycle2' ), null, true );
	wp_enqueue_script( 'jquery-cycle2-scrollVert', get_stylesheet_directory_uri() . '/js/jquery.cycle2.scrollVert.js', array( 'jquery-cycle2' ), null, true );
	#wp_enqueue_script( 'jquery-cycle2-tile', get_stylesheet_directory_uri() . '/js/jquery.cycle2.tile.js', array( 'jquery' ), null, true );
	
	// carousel scripts ( NO LONGER NEEDED? )
	//if( is_front_page() || hnd_is_store_page() || is_single() || is_page( 'Noise' ) ) {
		wp_enqueue_style( 'jquery-owl-carousel-css', get_stylesheet_directory_uri() . '/css/owl.carousel.css' );
		wp_enqueue_style( 'jquery-owl-theme-css', get_stylesheet_directory_uri() . '/css/owl.theme.css' );
		wp_enqueue_style( 'jquery-owl-transitions-css', get_stylesheet_directory_uri() . '/css/owl.transitions.css' );
		#wp_enqueue_script( 'jquery-owl-carousel', get_stylesheet_directory_uri() . '/js/jquery.owl.carousel.js', array( 'jquery' ) );
		#wp_enqueue_script( 'carousel', get_stylesheet_directory_uri() . '/js/carousel.js', array( 'jquery' ) );
	//}

	// audio scripts
	if( is_page( 'Noise' ) || ( hnd_is_item() && is_single() ) ) {
		wp_enqueue_script( 'jquery-rotate', get_stylesheet_directory_uri() . '/js/jquery.rotate.js', array( 'jquery' )  );
		wp_enqueue_script( 'audio', get_stylesheet_directory_uri() . '/js/audio.js', array( 'jquery', 'jquery-rotate' ) );
		wp_enqueue_style( 'audio-css', get_stylesheet_directory_uri() . '/css/audio.css' );
		
		// in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
		#wp_enqueue_script( 'ajax-script', get_stylesheet_directory_uri() . '/js/audio.js', array( 'jquery' ) );
		#wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'item_id' => 1234 ) );
	}

	// sitewide scripts
	wp_enqueue_script( 'add-this-event', get_stylesheet_directory_uri() . '/js/add-this-event.js', array( 'jquery' ) );
	wp_enqueue_script( 'jquery-colorbox', get_stylesheet_directory_uri() . '/js/jquery.colorbox.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'jquery-tooltip', get_stylesheet_directory_uri() . '/js/jquery.tooltip.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'site', get_stylesheet_directory_uri() . '/js/site.js', array( 'jquery' ), null, true );

}
add_action( 'wp_enqueue_scripts', 'hnd_enqueue_scripts' );


// admin scripts
function hnd_admin_enqueue_scripts() { 
	wp_enqueue_script( 'jquery-tablesorter', get_stylesheet_directory_uri() . '/js/jquery.tablesorter.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'admin', get_stylesheet_directory_uri() . '/js/admin.js', array( 'jquery' ), null, true );

}
add_action( 'admin_enqueue_scripts', 'hnd_admin_enqueue_scripts' );

/* Redirects
-------------------------------------------------------------- */

// redirect media attachment pages
function hnd_redirect() {
	
	if( get_post_type() == 'attachment' ) {
	
		$attachment = get_queried_object();		
		
		if( $attachment->post_parent ) {
			// redirect to parent post
			$redirect_url = get_permalink( $attachment->post_parent );
		} else {
			// if unattached, redirect to home page
			$redirect_url = home_url();
		}
		
		wp_redirect( $redirect_url );
		exit;
	}
}
add_action( 'template_redirect', 'hnd_redirect' );
