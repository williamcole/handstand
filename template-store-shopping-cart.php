<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 * Template Name: Store - Shopping Cart
 */

get_header();

wp_enqueue_script( 'cart', get_stylesheet_directory_uri() . '/js/cart.js', array( 'jquery' ), null, true );

?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<header id="page-heading">
	<?php hnd_breadcrumb_nav(); ?>
</header>
<!-- /page-heading -->

<article class="post full-width clearfix">
    <div class="entry clearfix">
    	<?php
    	
        // cart
    	#echo '<div class="cart-left">';
        echo do_shortcode('[cart-page]');
    	#echo '</div>';
        
        /*
        // shipping rates
        echo '<div class="cart-right">';
        echo '<h2>SHIPPING RATES</h2>';
        echo do_shortcode('[shipping-rates]');
        echo '</div>';
        echo '<div class="clear"><br></div>';
		*/
		
        // content
        the_content();
        
        ?>
    </div>
</article><!-- /post -->

<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>