<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 */
?>
<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<header id="page-heading">
	<?php hnd_breadcrumb_nav(); ?>
</header>

<article class="post <?php echo hnd_get_post_type(); ?> clearfix">

	<?php
	
	// set larger image size on single pages
	$image_size = 'large';

	if( hnd_is_item() ) {
		
		echo '<div class="image-specs">';

		// wrap in slideshow div if there are multiple images
		if( !hnd_is_package_deal() && has_post_thumbnail() && ( hnd_has_disc_image() || hnd_has_back_image() || hnd_has_display_image() ) ) {
			?>
			<div class="cycle-slideshow" 
			    data-cycle-timeout=0
			    data-cycle-pager="#pager1"
			    data-cycle-pager-template="<a href=# ><img src='{{firstChild.firstChild.src}}'></a>"
			    data-cycle-slides=">div"
			    data-cycle-update-view=-1
			    >
			    <?php
			    
			    if( has_post_thumbnail() ) {
			    	echo '<div>'. hnd_get_featured_image( $post->ID, $image_size, $link = false, $colorbox = true ) . '</div>';
			    }
			    if( hnd_has_disc_image() ) {
			    	echo '<div style="display:none">'. hnd_get_disc_image( $post->ID, $image_size, $link = false, $colorbox = true ) . '</div>';
			    }
			    if( hnd_has_back_image() ) {
			    	echo '<div style="display:none">'. hnd_get_back_image( $post->ID, $image_size, $link = false, $colorbox = true ) . '</div>';
			    }
			    if( hnd_has_display_image() ) {
			    	echo '<div style="display:none">'. hnd_get_display_image( $post->ID, $image_size, $link = false, $colorbox = true ) . '</div>';
			    }
			    
		   		?>
			</div>
			<div class="cycle-pager" id="pager1"></div>
			<?php
		} else {
			hnd_featured_image( $post->ID, $image_size, $link = false, $colorbox = true );
		}
		
		echo '</div>';

	} else {

		if( hnd_is_artist() ) {
			// only show featured artist image on Bio tab
			$active_link = ( get_query_var('view') ) ? get_query_var('view') : 'bio';
			
			if( hnd_is_artist() && ( $active_link == 'bio' ) ) {
				hnd_featured_image( $post->ID, $image_size, $link = false, $colorbox = true );
			}
		} else {
			// check for gallery before displaying featured image
			$single_content = get_the_content();
			if( !has_shortcode( $single_content, 'gallery' ) ) {
				hnd_featured_image( $post->ID, $image_size, $link = false, $colorbox = true );
			}
		}
	}

	?>
	
	<header><?php
		
		// display date on news posts
		if( hnd_is_news() ) {
			echo '<div class="post-date">' . get_the_date() . '</div>';
		}

		// title
		if( !hnd_is_artist() ) {
			if( hnd_is_item() ) {
				echo hnd_get_nice_title( get_the_title() );
			} else {
				echo '<h1 class="single-title">' . get_the_title() .'</h1>';
			}
		}

	?></header>
	
	<div class="entry clearfix"><?php
		the_content();
		hnd_download_form();
	?></div><!-- /entry -->
	
	<?php

	// widget area
	echo '<div class="widget-page">';
	dynamic_sidebar('single-page-widgets');
	echo '</div>';

	// nav buttons
	hnd_prev_next_nav();

	?>
	
</article>
<!-- /post -->

<?php endwhile; ?>
<?php endif; ?>
             
<?php get_sidebar(); ?>
<?php get_footer(); ?>