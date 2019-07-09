<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 * Template Name: Noise
 */

get_header();

if( have_posts() ) : while( have_posts() ) : the_post();

	?>
	<header id="page-heading">
	    <h1><?php the_title(); ?></h1>		
	</header>
	<!-- /page-heading -->
	
	<article class="post clearfix">
	    <div class="entry clearfix"><?php
	    	
	    	// page content
	    	the_content();
	    	
	    	// TODO
	    	//echo '<h4>[LABEL FILTERS]</h4>';
	    	
	    	// custom query for items with attached audio
			$items = new WP_Query( array(
				'post_type' => 'items',
				'order' => 'DESC',
				'meta_query' => array(
					array(
						'key' => 'audio',
						'compare' => 'EXISTS',
					)		
				),
				'meta_key' => 'serial',
			   	'orderby'=> 'meta_value',
			   	'order' => 'DESC',
			) );
			
			if( $items->have_posts() ) :

				$noise_item_ids = array();

				echo '<div class="owl-carousel owl-carousel-audio owl-carousel-noise owl-theme">';	
				
				// loop
				while( $items->have_posts() ) : $items->the_post();
					$noise_item_ids[] = get_the_ID();
					echo '<div class="carousel-item"><a href="#' . get_permalink() . '" item="' . get_the_ID() . '"><img src="' . hnd_get_featured_image_src() . '" title="' . get_the_title() . '"></a></div>';
				endwhile;
			
				echo '</div><!--/.owl-carousel-->';
				
				// audio player				
				hnd_item_audio( $noise_item_ids );
				
			endif;
			
			wp_reset_query();
				
		?></div>
		<!-- /entry -->    
	</article>
	<!-- /post -->
	<?php

endwhile; endif;
	  
get_sidebar();
get_footer();

?>