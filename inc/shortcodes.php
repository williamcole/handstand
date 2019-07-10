<?php

##############
# SHORTCODES #
##############

/* Shipping Rates
-------------------------------------------------------------- */

// deprecated???
// not sure this is being used
// data is hardcoded on Shopping Cart page

function hnd_shipping_rates() {
	
	$html = '';
	$html .= '<table class="shipping-rates">';
	
	$html .= '	<tr>';
	$html .= '		<th width="50%">DOMESTIC (USA)</th>';
	$html .= '		<th width="50%">INTERNATIONAL</th>';
	$html .= '	</tr>';

	$html .= '	<tr>';
	$html .= '		<td>
						<div>* $4.75 FLAT RATE for any amount of items</div>
						<div>* FREE SHIPPING on orders over $50</div>
					</td>';
	$html .= '		<td>
						<div>* Buyer pays shipping based on weight</div>
						<div>* Please message to get a shipping quote</div>
					</td>';
	$html .= '	</tr>';

	$html .= '</table><!--/.shipping-rates-->';

	return $html;  
}
add_shortcode( 'shipping-rates', 'hnd_shipping_rates' );

/* Social Media
-------------------------------------------------------------- */

function hnd_social_media( $atts = null ) {
	
	$html = '';

	// get social media items with links
	$items = new WP_Query( array(
		'post_type' => 'social_media',
		'posts_per_page' => -1,
		'order' => 'ASC',
		'orderby' => 'title',
		'meta_query'  => array(
			array(
				'key'     => 'link',
				'compare' => 'EXISTS',
			)
		)
	) );
	
	if( $items->have_posts() ) :
		$html .= '<div class="social-media"><ul>';
		while( $items->have_posts()) : $items->the_post();

			$link = get_post_meta( get_the_ID(), 'link', true );

			if( $link ) {
				$html .= '<li>';
				$html .= '<a href="' . $link . '" target="_blank">';
				
				if( has_post_thumbnail() ) {
					$html .= hnd_get_featured_image( get_the_ID(), 'small', $link = false );
				} else {
					$html .= get_the_title();
				}
				$html .= '</a>';
				$html .= '</li>';
			}

		endwhile;			
		$html .= '</ul><div class="clear"></div></div><!--/#social-media-->';
	endif;
	
	wp_reset_query();

	return $html;  
}
add_shortcode( 'social-media', 'hnd_social_media' );