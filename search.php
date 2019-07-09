<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 */

get_header();

$search_type = get_query_var('post_type');

if( $search_type == 'items' ) {
	$search_type = '<a href="' . get_bloginfo('url') . '/store">store</a>';
} else if( $search_type == 'any' ) {
	$search_type = '<a href="' . get_bloginfo('url') . '">site</a>';
}

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
query_posts( $query_string .'&posts_per_page=20&paged=' . $paged );

?>

	<header id="page-heading">
		<h1 id="archive-title"><?php _e('Search', 'adapt'); ?> <span class="arrow">></span> <?php echo $search_type; ?> <span class="arrow">></span> <span class="black"><?php the_search_query(); ?></span></h1>
	</header>
	<!-- /page-heading -->
	    
	<div id="post" class="post clearfix"><?php

		if( have_posts() ) {
			get_template_part( 'loop' , 'entry' );
			hnd_pagination();
		} else {
    		_e('No results found for that query.', 'adapt');
		}

	?></div>
	<!-- /post  -->
        
<?php get_sidebar(); ?>		  
<?php get_footer(); ?>