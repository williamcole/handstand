<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 * Template Name: Store - Sale Items
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
		
		the_content();
		
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		$items = new WP_Query( array(
			'post_type' => 'items',
			'post_status' => 'publish',
			'order' => 'ASC',
			'orderby' => 'title',
			'paged' => $paged,
			'meta_query' => array(
				array(
					'key' => 'sale_price',
					'value' => 0,
					'compare' => '>',
					'type' => 'NUMERIC'
				),	
			),
		) );
		
		if( $items->have_posts() ) :		
			while( $items->have_posts() ) : $items->the_post();
				get_template_part( 'loop', 'item' );
			endwhile;
		endif;

		hnd_pagination();

		wp_reset_query();
		
	?></div>
	<!-- /entry --> 

</article>
<!-- /post -->

<?php endwhile; ?>
<?php endif; ?>	  
<?php get_sidebar(); ?>
<?php get_footer(); ?>