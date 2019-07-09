<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 */
?>
<?php get_header(); ?>

<header id="page-heading">
    <h1 class="page-title"><?php _e('Page Not Found',''); ?></h1>		
</header>
<!-- END page-heading -->

<div class="post clearfix">

<div class="entry clearfix">		

	<img src="<?php echo get_stylesheet_directory_uri() . '/images/broken-record.jpg'; ?>" class="post-thumbnail" style="float:right">
	<p>Sorry, the page you were looking for could not be found. Try searching or check out the site map below.</p>
	
	<?php wp_nav_menu( array( 'menu' => 'Menu' ) ); ?>

</div><!-- END entry -->

</div>
<!-- END post -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>