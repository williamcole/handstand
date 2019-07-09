<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 * Template Name: Blog
 */
?>

<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<header id="page-heading">
	<h1><?php the_title(); ?></h1>		
</header>

<div class="post clearfix">
	<?php
	
	query_posts( array(
		'post_type'=> 'post',
		'paged'=>$paged
	));
	
	if( have_posts() ) :
		get_template_part( 'loop', 'entry' );
	endif;
	
	pagination();
	
	wp_reset_query();
	
	?>
</div>
<!-- /post -->

<?php endwhile; ?>
<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>