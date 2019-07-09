<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 * Template Name: Store - New Arrivals
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
		$status = ( is_user_logged_in() ) ? array('publish','draft') : array('publish');

		$items = new WP_Query( array(
			'post_type' => 'items',
			'post_status' => $status,
			//'order' => 'DESC',
			'paged' => $paged,
			'posts_per_page' => 48
		) );
		
		if( $items->have_posts() ) :
			while( $items->have_posts() ) : $items->the_post();
				get_template_part( 'loop', 'item' );
			endwhile;
		endif;

		hnd_pagination();
		
	?></div>
	<!-- /entry --> 

</article>
<!-- /post -->

<?php endwhile; ?>
<?php endif; ?>	  
<?php get_sidebar(); ?>
<?php get_footer(); ?>