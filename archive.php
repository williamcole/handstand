<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 */

get_header();

?>

<header id="page-heading">
	<?php $post = $posts[0]; ?>
	<?php if (is_category()) { ?>
		<h1><a href="<?php echo get_bloginfo( 'url' ) . '/news'; ?>">News</a> <span class="arrow">></span> <span class="black"><?php single_cat_title(); ?></span></h1>
	<?php } elseif( is_tag() ) { ?>
		<h1>Posts Tagged &quot;<?php single_tag_title(); ?>&quot;</h1>
	<?php } elseif (is_day()) { ?>
		<h1>Archive for <?php the_time('F jS, Y'); ?></h1>
	<?php } elseif (is_month()) { ?>
		<h1>Archive for <?php the_time('F, Y'); ?></h1>
	<?php } elseif (is_year()) { ?>
		<h1>Archive for <?php the_time('Y'); ?></h1>
	<?php } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h1>Blog Archives</h1>
	<?php } else { ?>
		<?php hnd_breadcrumb_nav(); ?>
	<?php } ?>
</header>
<!-- END page-heading -->

<?php

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

if( get_post_type() == 'events' ) {
	
	// get selected artist
	$artist_slug = ( get_query_var('artist') ) ? get_query_var('artist') : '';
	
	if( $artist_slug !== '' ) {

		// get artist title from slug
		$artists_found = get_posts( array(
			'name' => $artist_slug,
			'post_type' => 'artists',
			'post_status' => 'publish',
			'numberposts' => 1
		) );
		
		if( $artists_found ) {
			$artist_title = $artists_found[0]->post_title;
		}
	} else {
		$artist_title = '';
	}
	
	add_filter( 'posts_where', 'hnd_artists_posts_where', 10, 2 );
  	
	// allow future events to be displayed
	query_posts( array(
		'post_status'=> array( 'publish', 'future' ),
		'post_type' => get_post_type(),
		'paged' => $paged,
		'artist_title' => $artist_title,
	));

	remove_filter( 'posts_where', 'hnd_artists_posts_where', 10, 2 );
  	
} elseif( get_post_type() == 'items' ) {
	
	// get current taxonomy
	$current_query = $wp_query->query_vars;
	
	// show all items on Labels tax
	if( $current_query['taxonomy'] == 'labels' ) {
		$posts_per_page = -1;
		$meta_key = 'serial';

		// order by serial number descending
   		//$query->set( 'meta_key', 'serial' );
		//$query->set( 'orderby', 'meta_value' );
		//$query->set( 'order', 'DESC' );

	} else {
		$posts_per_page = get_option('posts_per_page');
	}

	query_posts( array(
		$current_query['taxonomy'] => $current_query['term'],
		'post_status'=> array( 'publish' ),
		'post_type' => get_post_type(),
		'posts_per_page' => $posts_per_page,
		'paged' => $paged,
		//'meta_key' => $meta_key,
		//'orderby' => 'meta_value',
	));

} else {

	// default paginated query
	query_posts( array(
		'post_type' => get_post_type(),
		'paged' => $paged,
	));

}

if (have_posts()) : ?>

	<div id="post" class="post clearfix"><?php
	
		get_template_part( 'loop' , 'entry' );
		hnd_pagination();
	
	?></div>
	<!-- END post -->

<?php endif; ?>

<?php get_sidebar(); ?>	  
<?php get_footer(); ?>