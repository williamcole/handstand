<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 * Template Name: Store - Featured Label
 */
?>

<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<header id="page-heading">
	<?php hnd_breadcrumb_nav(); ?>
</header>
<!-- /page-heading -->

<article class="post clearfix">
	<div class="entry clearfix"><?php
		
		// determine label based off title
		$label_slug = hnd_slugify( $post->post_title );
		
		// get featured image based off Contact with same label slug
		$label_contact = get_posts( array(
			'name' => $label_slug,
			'post_type' => 'contacts',
			'numberposts' => 1
		) );

		if( $label_contact ) {

			// label logo
			hnd_featured_image( $label_contact[0]->ID );

			// label name
			//echo '<h2>' . get_the_title() . '</h2>';
			
			// label bio
			echo '<div class="featured-label-bio">' . wpautop( $label_contact[0]->post_content ) . '</div>';
			
			// divider
			echo '<div class="clear"></div>';
			echo '<hr>';
		}

		// get items
		$items = new WP_Query( array(
			'post_status' => array( 'publish' ),
			'post_type' => 'items',
			
			// order by serial number descending
		   	'meta_key' => 'serial',
		   	'orderby'=> 'meta_value',
		   	'order' => 'DESC',

			'posts_per_page' => -1,
			'tax_query' => array(
				'relation' => 'AND',
				// Handstand Records release
				array(
					'taxonomy' => 'labels',
					'field' => 'slug',
					'terms' => $label_slug,
					'operator' => 'IN'
				)				
			)
		) );
		
		if( $items->have_posts() ) :
			while( $items->have_posts() ) : $items->the_post();
				get_template_part( 'loop', 'item' );
			endwhile;
		endif;

		wp_reset_query();
		
	?></div>
	<!-- /entry --> 

</article>
<!-- /post -->

<?php endwhile; ?>
<?php endif; ?>	  
<?php get_sidebar(); ?>
<?php get_footer(); ?>