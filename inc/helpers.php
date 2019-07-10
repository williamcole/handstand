<?php

###########
# HELPERS #
###########

function hnd_get_header_text() {
	return 'Handstand <span class="header-records">Records</span>';
}

// convert any string into a hypen-separated slug
setlocale( LC_ALL, 'en_US.UTF8' );
function hnd_slugify( $str, $replace = array(), $delimiter = '-' ) {
	if( !empty( $replace ) ) $str = str_replace( (array) $replace, ' ', $str );
	$clean = iconv( 'UTF-8', 'ASCII//TRANSLIT', $str );
	$clean = preg_replace( "/[^a-zA-Z0-9\/_|+ -]/", '', $clean );
	$clean = strtolower( trim( $clean, '-' ) );
	$clean = preg_replace( "/[\/_|+ -]+/", $delimiter, $clean );
	return $clean;
}

function hnd_slash_list( $array ) {
	return implode( ' / ', $array );
}

/* Post Types
-------------------------------------------------------------- */

// may not need this
function hnd_get_post_type( $plural = false ) {
	
	$post_type = get_post_type();
	
	// TODO: might not need this at all
	if( $post_type == 'post' ) {
		$post_type = 'news';
	}

	// depluralize
	/*if( $plural && ( $post_type !== 'press' ) && ( $post_type !== 'news' ) ) {
		$post_type = substr( $post_type, 0, -1 );
	}*/

	return $post_type;	
}

function hnd_is_artist() {
	if( get_post_type() == 'artists' ) {
		return true;
	} else {
		return false;
	}	
}

function hnd_is_download() {
	if( get_post_type() == 'downloads' ) {
		return true;
	} else {
		return false;
	}	
}

function hnd_is_event() {
	if( get_post_type() == 'events' ) {
		return true;
	} else {
		return false;
	}	
}

function hnd_is_item() {
	if( get_post_type() == 'items' ) {
		return true;
	} else {
		return false;
	}	
}

function hnd_is_label( $post_id = null ) {

	global $post;
	$post_id = ( isset( $post_id ) ) ? $post_id : $post->ID;

	if( ( get_post_type( $post_id ) == 'contacts' ) && ( has_term( 'Label', 'contact_types', $post_id ) ) ) {
		return true;
	} else {
		return false;
	}	
}

function hnd_is_news() {
	//if( in_the_loop() && !is_page() && ( get_post_type() == 'post' ) ) {
	if( get_post_type() == 'post' ) {
		return true;
	} else {
		return false;
	}	
}

function hnd_is_package_deal() {
	if( ( get_post_type() == 'items' ) && ( has_term( 'Package Deal', 'formats' ) ) ) {
		return true;
	} else {
		return false;
	}	
}

function hnd_is_press() {
	if( get_post_type() == 'press' ) {
		return true;
	} else {
		return false;
	}	
}

function hnd_is_venue() {
	if( ( get_post_type() == 'contacts' ) && ( has_term( 'Venue', 'contact_types' ) ) ) {
		return true;
	} else {
		return false;
	}	
}

function hnd_is_vinyl( $post_id ) {

	global $post;
	$post_id = ( isset( $post_id ) ) ? $post_id : $post->ID;

	if( get_post_type() !== 'items' )
		return;

	$formats = get_the_terms( $post_id, 'formats' );

	if ( $formats && !is_wp_error( $formats ) ) {
		
		$text = '';
		
		foreach( $formats as $format ) {
			$text .= $format->name;
		}

		if( strpos( $text, '"' ) ) {
			return true;
		} else {
			return false;
		}
	}	
}

function hnd_is_cd( $post_id ) {

	global $post;
	$post_id = ( isset( $post_id ) ) ? $post_id : $post->ID;

	if( get_post_type() !== 'items' )
		return;

	$formats = get_the_terms( $post_id, 'formats' );

	if ( $formats && !is_wp_error( $formats ) ) {
		
		$text = '';
		
		foreach( $formats as $format ) {
			$text .= $format->name;
		}

		if( strpos( $text, 'CD' ) ) {
			return true;
		} else {
			return false;
		}
	}	
}

function hnd_get_label_id( $title ) {
	$label = get_page_by_title( $title, null, 'contacts' );
	return $label->ID;
}

function hnd_get_attribute_title() {
	
	global $post;
	
	$title = $post->post_title;

	// append date and venue to event title
	if( hnd_is_event() ) {
		$title .= ' &mdash; ' . hnd_get_event_date() . ' @ ' . hnd_get_event_venue();
	}

	// replace quotes
	$title = str_replace( '"', '&quot;', $title );

	return $title;
}

/* Content
-------------------------------------------------------------- */

// prepend post title with flag
function hnd_get_title_flag() {
	
	global $post;
	
	$html = '';
	
	if( is_search() && in_the_loop() ) {
		if( hnd_is_package_deal() ) {
			$flag = 'special';
		} elseif( !hnd_is_item() ) {
			$flag = hnd_get_post_type();
		} else if( !is_page() ) {
			$flag = false;
		}
		if( $flag ) {
			$html = '<span class="flag ' . $flag . '">' . $flag . '</span>';
		}
	}

	return $html;
}

function hnd_title_flag() {
	echo hnd_get_title_flag();
}

function hnd_get_buttons( $post_id = null ) {

	global $post;
	$post_id = ( isset( $post_id ) ) ? $post_id : $post->ID;

	// only display on Downloads, Items, News
	if( !hnd_is_download() && !hnd_is_item() && !hnd_is_news() )
		return;

	// construct markup
	$html = '';
	$html .= '<div class="buttons">';

	// get post type
	switch( get_post_type() ) {

		case 'downloads':

		break;

		case 'items':

		break;

		case 'post':

		break;

	}
	

	// more info
	if( !is_single() ) {
		$html .= '<a class="button more-info" href="' . get_permalink() . '" title="' . hnd_get_attribute_title() . '">More Info</a>';
	}
	 
	
	
	$html .= '</div><!--/.buttons-->';

	return $html;
}

function hnd_buttons( $post_id = null ) {
	echo hnd_get_buttons( $post_id );
}


/* Truncation
-------------------------------------------------------------- */

function hnd_truncate_text($string, $your_desired_width = 300) {
	$parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
	$parts_count = count($parts);
	$length = 0;
	$last_part = 0;
	for (; $last_part < $parts_count; ++$last_part) {
		$length += strlen($parts[$last_part]);
		if ($length > $your_desired_width) break;
	}
	
	$string = implode(array_slice($parts, 0, $last_part)) . '...';
	
	return $string;
}

function hnd_truncate_html( $text, $length = 300, $ending = '', $exact = true, $consider_html = true ) {
	
	if( $consider_html ) {
	
		// if the plain text is shorter than the maximum length, return the whole text
		if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
			return $text;
		}
		
		// define some vars	
		$total_length = strlen($ending);
		$open_tags = array();
		$full_tags = array();
		$truncate = '';

		// no-break spaces can wreak havoc, let's remove them
		$text = preg_replace('/\x{00A0}/u', ' ', $text);
		
		// split all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
		
		// loop through lines
		foreach ($lines as $line_matchings) {
		
			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1])) {
				
				// if it's an "empty element" with or without xhtml-conform closing slash
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
				
					// do nothing
					
				// if tag is a closing tag
				} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
				
					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);
					if ($pos !== false) {
						unset($open_tags[$pos]);
						unset($full_tags[$pos]);
					}
				
				// if tag is an opening tag
				} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
				
					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, $tag_matchings[1]);
					array_unshift($full_tags, $tag_matchings[0]);
				
				}
				
				$truncate .= $line_matchings[1];
			}
			
			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			
			if ($total_length + $content_length > $length) {
			
				// the number of characters which are left
				$left = $length - $total_length;
				$entities_length = 0;
				
				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
					
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity) {
						if ($entity[1]+1-$entities_length <= $left) {
							$left--;
							$entities_length += strlen($entity[0]);
						} else {
							// no more characters left
							break;
						}
					}
				}
				
				$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
			
				// maximum length is reached, so get off the loop
				break;
				
			} else {
				
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			
			}
			
			// if the maximum length is reached, get off the loop
			if($total_length >= $length) {
				break;
			}
		
		}
		
	} else {
		
		// dont consider html
		if( strlen( $text ) <= $length) {
			return $text;
		} else {
			$truncate = substr( $text, 0, $length - strlen( $ending ) );
		}
		
	}
	
	// if the words shouldn't be cut in the middle...
	if( !$exact ) {
		
		// ...search the last occurance of a space...
		$spacepos = strrpos($truncate, ' ');
		
		if (isset($spacepos)) {
			// $line_matchings[2]
			// ...and cut the text in this position
			$truncate = substr($truncate, 0, $spacepos);
		}
		
	}

	// make amends if we truncate in the middle of a tag
	if ( strrpos( $truncate, '<' ) > strrpos( $truncate, '>' ) )
		$truncate .= '>';

	// add the defined ending to the text
	$truncate .= $ending;
	$added_len = 0;
	$added_open_tag = '';
	$arr_pos = 0;
	
	if($consider_html) {
		
		// close all unclosed html-tags
		foreach ($open_tags as $tag) {
			
			$added_len += strlen($tag) + 3;
			$truncate .= '</' . $tag . '>';
			
			if($tag !== 'a' ){
				$added_open_tag = '<' . $tag . '>' . $added_open_tag;
			} else {
				$added_open_tag = $full_tags[$arr_pos] . $added_open_tag;
			}
			
			$arr_pos++;
			
		}
		
	}
	
	// construct array_text
	$array_text = array();
	$array_text[0] = $truncate;
	$array_text[1] = $added_open_tag . ( substr($text, strlen($truncate) - $added_len ) );
	
	// trim teaser text and remove ending
	$array_text[0] = trim( $array_text[0] );
	$array_text[0] = str_replace( $ending, '', $array_text[0] );
	
	// get last piece of text
	$array_pieces = explode( ' ', $array_text[0] );
	$last_piece = end( $array_pieces );
	
	// if last piece contains a tag, remove it and rebuild the text
	if( preg_match( '/</', $last_piece ) && false === strpos( $last_piece, '</' ) ) {
		array_pop( $array_pieces );
		$array_text[0] = implode( ' ', $array_pieces );
	}
	
	// re-add ending
	$array_text[0] .= $ending;
	
	return $array_text;
}