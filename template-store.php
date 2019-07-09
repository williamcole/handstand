<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 * Template Name: Store
 */
?>

<?php get_header(); ?>

<header id="page-heading">
	<?php hnd_breadcrumb_nav(); ?>
</header>
<!-- /page-heading -->

<div class="widget-page"><?php dynamic_sidebar('store-page-widgets'); ?></div>

<?php get_footer(); ?>