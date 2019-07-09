<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 */

get_header();

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
//$posts_per_page = get_option('posts_per_page');

query_posts( array(
	'cat' => -371, // exclude notices (371)
	'paged' => $paged,
	//'posts_per_page' => $posts_per_page,
	'post_type' => 'post'
));

?>

<header id="page-heading">
	<h1>News</h1>
</header>

<div id="post" class="post clearfix"><?php
	
	get_template_part( 'loop-entry' );
	hnd_pagination();

?></div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>