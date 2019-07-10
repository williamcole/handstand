<?php

#########
# AUDIO #
#########

function hnd_has_item_audio( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;
	
	if( get_post_type( $post_id ) !== 'items' )
		return;

	$audio = get_post_meta( $post_id, 'audio' );

	if( is_array( $audio[0] ) && count( $audio ) ) {
		return true;
	} else {
		return false;
	}
}

function hnd_get_item_audio( $post_ids = null ) {

	global $post;
	$post_ids = ( $post_ids ) ? $post_ids : $post->ID;
	if( !is_array( $post_ids ) ) $post_ids = array( $post_ids );
	
	// build the markup
	$html = '';
	$html .= '
		<div class="audio-wrap">

			<div class="track-list-wrap maintain-ratio">
				<div class="track-list">
	';

	// loop through item audio
	foreach( $post_ids as $post_id ) {
		
		// make sure audio array has data
		if( hnd_has_item_audio( $post_id ) ) {		
			
			if( count( $post_ids ) > 1 ) {
				// multiple post ids
				$track_list_title = hnd_get_nice_title( get_the_title( $post_id ) );
			} else {
				// single post id 
				$track_list_title = '<h3 class="page-title">Track List</h3><hr>';
			}

			$audio = get_post_meta( $post_id, 'audio' );
			
			$html .= '<div class="track" item="' . $post_id . '">';
			$html .= $track_list_title;
			$html .= '	<ol class="audio">';
			
			$i = 1;
			
			// loops through tracks
			foreach( $audio as $track ) {
				$html .= '<li track="' . $i . '">';
				$html .= '	<a href="Javascript://' . $track['post_title'] .'" class="track-name" track="' . $i . '">' . $track['post_title'] . '</a>';
				$html .= 	do_shortcode( '[haiku url="' . $track['guid'] . '" title="' . $track['post_title'] . '"]' );
				$html .= '</li>';
				$i++;
			}
			
			$html .= '	</ol>';
			
			// buttons
			if( !is_single() ) { 
				$html .= '<div class="buttons">';
				$html .= hnd_get_buy_button( $post_id );
				$html .= hnd_get_more_info( $post_id );
				$html .= '</div>';
			}
			
			$html .= '</div>';
		
		}
	}
					
	$html .= '
				</div>
			</div><!--/.track-list-wrap-->
			
			<div class="turntable-wrap maintain-ratio">
				<div class="turntable-box">
					<div class="turntable-arm">
						<div class="turntable-base" class="maintain-ratio"></div>
						<div class="turntable-lever"></div>
						<div class="turntable-head"></div>
					</div>
					<div class="turntable">
	';

	// loop through item images
	foreach( $post_ids as $post_id ) {
		$html .= '<a class="disc-button" href="Javascript://Play" item="' . $post_id . '"><img class="turntable-disc" item="' . $post_id . '" src="' . hnd_get_disc_image_src( $post_id ) . '" title="' . get_the_title( $post_id ) . '"></a>';
	}						
	
	$html .= '
					</div>
				</div>
			</div><!--/.turntable-wrap-->

			<div class="clear"></div>

		</div><!--/.audio-wrap-->
	';			
	
	return $html;
}

function hnd_item_audio( $post_id = null ) {
	echo hnd_get_item_audio( $post_id );
}

/* NOT IN USE

// ajax function callback to get item audio markup
function hnd_ajax_get_audio_callback() {
	echo hnd_item_get_audio( intval( $_POST['item_id'] ) );
	die();
}
add_action( 'wp_ajax_nopriv_hnd_ajax_get_audio', 'hnd_ajax_get_audio_callback' );
add_action( 'wp_ajax_hnd_ajax_get_audio', 'hnd_ajax_get_audio_callback' );

*/