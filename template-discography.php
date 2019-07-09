<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 * Template Name: Discography
 */
?>

<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<header id="page-heading">
    <h1><?php the_title(); ?></h1>
</header>
<!-- /page-heading -->

<article class="post clearfix">
	<div class="entry clearfix"><?php
		
		the_content();
		
		// discography
		$hnd_releases = hnd_get_handstand_releases();
		
		if( isset( $hnd_releases ) && $hnd_releases->have_posts() ) {
			$postcount = 0;
			while( $hnd_releases->have_posts() ) : $hnd_releases->the_post();
				get_template_part( 'loop', 'item' );
			endwhile;
		}

		echo '<br>';
		
		/*
		
		// merch
		$hnd_merch = hnd_get_handstand_merch();
		
		if( isset( $hnd_merch ) && $hnd_merch->have_posts() ) {
			echo '<div class="clear" style="height:1em"></div>';
			echo '<h1 class="page-title">Merch</h1>';
			echo '<hr>';
			$postcount = 0;
			while( $hnd_merch->have_posts() ) : $hnd_merch->the_post();
				get_template_part( 'loop', 'item' );
			endwhile;
		}

		echo '<br>';
		
		// upcoming releases
		$hnd_upcoming_releases = hnd_get_handstand_upcoming_releases();
		
		if( isset( $hnd_upcoming_releases ) && $hnd_upcoming_releases->have_posts() ) {
			echo '<div class="clear" style="height:1em"></div>';
			echo '<h1 class="page-title">Upcoming Releases</h1>';
			echo '<hr>';
			$postcount = 0;
			while( $hnd_upcoming_releases->have_posts() ) : $hnd_upcoming_releases->the_post();
				get_template_part( 'loop', 'item' );
			endwhile;
		}
		
		*/
		
	?></div>
	<!-- /entry -->    
</article>
<!-- /post -->

<?php endwhile; ?>
<?php endif; ?>	  
<?php get_sidebar(); ?>
<?php get_footer(); ?>