<?php

#############
# DOWNLOADS #
#############

function hnd_download_title( $title ) {

	// split artist names on single Event posts
	if( hnd_is_download() && in_the_loop() && is_single() ) {
		$title = hnd_get_nice_title( $title );
	}
	
	return $title;	
}
add_filter( 'the_title', 'hnd_download_title' );

function hnd_download_excerpt( $output ) {
	
	if( hnd_is_download() ) {
		$output = '';
		$output .= '<div class="buttons">' . hnd_get_download_button() . '</div>';
	}
	
	return $output;
}
add_filter( 'get_the_excerpt', 'hnd_download_excerpt' );

function hnd_download_content( $content ) {
	
	if( hnd_is_download() && ( is_single() || in_the_loop() ) ) {
		
		// get item
		$item = get_post_meta( get_the_ID(), 'item', false );
		$item_id = $item[0]['ID'];
		$item_post = get_post( $item_id );
		
		$orig_content = $content;
		$content = '<p>' . $item_post->post_excerpt . '</p>';
	}
	
	return $content;
}
add_filter( 'the_content', 'hnd_download_content' );

function hnd_get_download_item_id() {
	
	global $post;
	
	$item = get_post_meta( $post->ID, 'item', false );
	
	return $item[0]['ID'];
}

function hnd_download_code_required() {
	
	global $post;

	if( !hnd_is_download() )
		return;
	
	// is this a free download
	$free = get_post_meta( $post->ID, 'free', true );
	$link = get_post_meta( $post->ID, 'link', true );
	
	# TODO: check if download codes exist
	
	if( !$free ) {
		return true;
	} else {
		return false;
	}
}

function hnd_get_download_button( $post_id = null, $code = false ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;

	// check post type: Download or Item
	if( hnd_is_item( $post_id ) ) {
		// ITEM - get postid of attached download
		$download = get_post_meta( $post_id, 'download', false );
		$download_id = $download[0]['ID'];
		$file = get_post_meta( $download_id, 'file', false );
	} else {
		// DOWNLOAD - get link to download file so we can determine size
		$file = get_post_meta( $post_id, 'file', false );
		$download_id = $post_id;
	}

	// do nothing if there is no file
	if( !$file ) 
		return;

	// get file location and size
	if( $file[0]['ID'] ) {
		$file_size = filesize( get_attached_file( $file[0]['ID'] ) );
		$file_size = ' - ZIP File (' . size_format( $file_size ) . ')';
		$file = $file[0]['guid'];
	} else {
		$file = false;
	}
	
	// get post meta
	$link = get_post_meta( $post_id, 'link', true );
	$free = get_post_meta( $post_id, 'free', true );
	$href = ( $free ) ? $file : get_permalink();
	
	// allow download if code entered
	if( $code ) {
		$free = true;
	}

	/*
	// dont show button
	if( ( hnd_is_download( $post_id ) && is_single() ) && !$free && !$link )
		return;
	
	// dont show button
	if( ( hnd_is_item( $post_id ) && is_single() ) && !$free && !$link )
		return;	
	*/

	// set link href and text depending on link
	if( $link ) {
		
		if( strpos( $link, 'itunes' ) !== false ) {
			$link_source = ' at iTunes';
		} elseif( strpos( $link, 'bandcamp' ) !== false ) {
			$link_source = ' at BandCamp';
		} else {
			$link_source = '';
		}

		$text = 'Download' . $link_source;
	}

	// short text for single items
	if( hnd_is_item( $post_id ) && is_single() ) {
		
		$href = get_permalink( $download_id );
		$text = 'Download';
	
	} else {
		if( $free ) {
		
			if( $code ) {
				$text = 'Download';
				$href = $file;
			} else {
				$text = 'Free Download';
			}

			// append file size on single downloads
			if( hnd_is_download( $post_id ) && is_single() ) {
				$text .= ' ' . $file_size;
			}

		} else {
			$href = ( is_single() ) ? 'Javascript://Enter Download Code' : get_permalink( $download_id );
			$text = 'Enter Download Code';
		}
	}

	// construct html
	$buttons = '';

	// detemine target
	$id = ( !$free && !$code && is_single() ) ? ' id="enter-download-code"' : '';
	$class = ( $free ) ? 'download' : '';
	$target = ( hnd_is_download( $post_id ) && ( $free || $link_source ) ) ? '_self' : '_blank';

	// download button
	if( $file ) {
		$buttons .= '<a target="' . $target . '" class="button ' . $class . '" ' . $id . ' href="' . $href . '">' . $text . '</a>';
	}

	// only on single downloads
	if( hnd_is_download( $post_id ) && $link && !$code ) {
	
		// external link
		$buttons .= '<a target="_blank" class="button download" href="' . $link . '">Download ' . $link_source . '</a>';
	}

	return $buttons;
}

function hnd_download_form() {

	if( !hnd_is_download() )
		return;

	if( hnd_download_code_required() ) {

		$is_valid_code = false;
		$code = ( isset( $_POST['code'] ) ) ? trim( $_POST['code'] ) : false;
		
		if( !empty( $code ) && has_term( $code, 'download_codes' ) ) {
			$is_valid_code = true;
			$num_downloads = get_post_meta( get_the_ID(), 'number_of_downloads', true );
			update_post_meta( get_the_ID(), 'number_of_downloads', $num_downloads += 1 );
		}

		if( $is_valid_code ) {

			echo '<br><h2 class="green">Thanks! Click the link below to download.</h2>';
			echo hnd_get_download_button( get_the_ID(), $code = true );
			
		} else {

			// display button
			echo '<div class="buttons">' . hnd_get_download_button(); '</div>';

			if( $code ) {
				echo '<br><br><h2 class="dark-red">Invalid download code. Please try again.</h2>';
			}

			// display download form
			echo hnd_get_download_code_form( $show = true );
		}

	} else {

		// display button
		echo '<div class="buttons">' . hnd_get_download_button(); '</div>';

		// display download form
		echo hnd_get_download_code_form();
	}

}

function hnd_get_download_code_form( $show = false ) {
	$class = ( $show ) ? 'show' : '';
	$html = '<form class="download-form ' . $class . '" name="download-form" method="post" action="' . $_SERVER['REQUEST_URI'] . '">
				<p><input type="text" name="code" value="" placeholder="Codes are CaSe SeNsItIve"></p>
				<p><input class="button" type="submit" name="Submit" value="Submit"></p>
			</form>';
	return $html;
}