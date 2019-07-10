<?php

###########
# NOTICES #
###########

function hnd_get_current_notices() {
	
	// temporarily kill on home page
	#if( is_front_page() ) return;
	
	$html = '';
	
	if( is_user_logged_in() ) {
		$ps = array( 'draft', 'publish' );
	} else {
		$ps = array( 'publish' );
	}
	
	// get notices with an end date later than today
	$notices = new WP_Query( array(
		'post_type' => 'post',
		'category_name' => 'notices',
		'post_status' => $ps,
		'posts_per_page' => 3,
		'meta_query'  => array(
			array(
				'key'     => 'notice_end_date',
				'value'   => date( 'Y/m/d' ),
				'compare' => '>=',
				'type'    => 'DATE'
			)
		)
	) );
	
	if( $notices->have_posts() ) :
		while( $notices->have_posts()) : $notices->the_post();
			$html .= '<div class="notice clearfix">';
			$html .= get_the_post_thumbnail( get_the_ID(), 'tiny', array( 'class' => 'alignleft' ) );
			$html .= '<div><strong>' . get_the_title() . '</strong></div>';
			$html .= '<div>' . get_the_content() . '</div>';
			$html .= '</div>';
		endwhile;
	endif;
	
	wp_reset_query();
	
	return $html;
}

function hnd_current_notices() {
	echo hnd_get_current_notices();
}