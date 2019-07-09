<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 * Template Name: Trades
 */

// process form submission
if( isset( $_POST['trade-form-nonce'] ) && wp_verify_nonce( $_POST['trade-form-nonce'], 'trade-form-submit' ) ) {

	// success
	$success = true;

	/* CREATE CONTACT */

	// check if contact already exists
	$contact_slug = hnd_slugify( $_POST['contact_name'] );

	// check against slug to see if Label contact already exists
	$contact_exists = get_posts( array(
		'name' => $contact_slug,
		'post_type' => 'contacts',
		'numberposts' => 1
	) );

	if( $contact_exists ) {

		$contact_id = $contact_exists[0]->ID;

	} else {

		// create new label contact
		$contact_id = wp_insert_post( array(
			'post_type'      => 'contacts',
			'post_status'    => 'publish',
			'post_title'     =>  ucwords( $_POST['contact_name'] ),
		) );

		if( $contact_id ) {

			// set contact type
			$contact_type = get_term_by( 'name', $_POST['contact_type'], 'contact_types' );
			
			if( $contact_type ) {
				wp_set_post_terms( $contact_id, $contact_type->term_id, 'contact_types' );
			}

			// update contact meta fields
			update_post_meta( $contact_id, 'contact_person', ucwords( $_POST['contact_person'] ) );
			update_post_meta( $contact_id, 'address', ucwords( $_POST['address'] ) );
			update_post_meta( $contact_id, 'email', $_POST['email'] );
			update_post_meta( $contact_id, 'website', $_POST['website'] );
		
			// update label mapping
			hnd_new_label_taxonomy_term( $contact_id );
		}
	}

	/* CREATE TRADE */

	// set content
	$content = '';
	$content .= $_POST['trade_list'];
	$content .= '<h1 class="trade-for">~ TRADE FOR ~</h1>';
	
	// parse items
	if( !empty( $_POST['trade_items'] ) ) {
		
		$item_ids = $_POST['trade_items'];
		//$item_count = count( $item_ids );
		$items = array();

		// create array of item titles/serials
		foreach( $item_ids as $id ) {
			$items[] = get_the_title( $id ) . ' [' . get_post_meta( $id, 'serial', true ) . ']';
		}

		// add items to post content for easy copy/paste
		foreach( $items as $item ) {
			$content .= $item . '<br>';
		}
	
		// keep it all in post content area and use JS to parse, calculate, and add items
	
	}

	// create new trade
	$trade_id = wp_insert_post( array(
		'post_type'      => 'trades',
		'post_status'    => 'pending',
		'post_title'     => 'Trade with ' . ucwords( $_POST['contact_name'] ),
		'post_content'	 => $content,
	) );

	// update trade meta fields
	if( $trade_id ) {
		update_post_meta( $trade_id, 'contact', $contact_id );
		update_post_meta( $trade_id, 'exclude_jewel_cases', $_POST['exclude_jewel_cases'] );
		update_post_meta( $trade_id, 'trade_list', $_POST['trade_list'] );
	}

} else {

	// failure
	$success = false;
	$error = 'There was an error submitting the form. Please try again.';

}

?>

<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php

// load scripts
wp_enqueue_style( 'trades-css', get_stylesheet_directory_uri() . '/css/trades.css' );
wp_enqueue_script( 'trades', get_stylesheet_directory_uri() . '/js/trades.js', array( 'jquery' ) );

?>

<header id="page-heading">
    <h1><?php the_title(); ?></h1>		
</header>
<!-- /page-heading -->

<article class="post clearfix">
	<div class="entry clearfix">

		<?php the_content(); ?>

		<?php if( !empty( $_POST ) && $error ) { ?>
			<h3 class="red"><?php echo $error; ?></h3>
		<?php } ?>

		<?php if( $success ) { ?>
		
			<h3 class="green">Thanks for your trade proposal. We will get back to shortly.</h3>

		<?php } else { ?>

			<h3>Please fill out the form to propose a trade.</h3>

			<div id="trade-form-wrap" class="form-wrap">
				<form id="trade-form" name="trade-form" action="<?php the_permalink(); ?>" method="post">

					<h2>Contact Information</h2>
					
					<fieldset>
						<label for="contact_type">How Would You Describe Yourself?</label>
						<input name="contact_type" type="radio" value="Label" checked="checked"> Label 
						<input name="contact_type" type="radio" value="Distributor"> Distributor 
						<input name="contact_type" type="radio" value="Artist"> Artist						
		            	<input name="contact_type" type="radio" value="Other"> Other						
		            </fieldset>
					
					<fieldset>
						<label for="contact_name">Label Name</label>
						<input name="contact_name" type="text" placeholder="Awesome Records" required="required">
		            </fieldset>

		            <fieldset>
						<label for="contact_person">Your Name</label>
						<input name="contact_person" type="text" placeholder="John Doe" required="required">
		            </fieldset>

					<fieldset>
						<label for="email">Email</label>
						<input name="email" type="email" placeholder="name@email.com" required="required">
		            </fieldset>

		            <fieldset>
						<label for="website">Website</label>
						<input name="website" type="text" placeholder="http://www.website.com" required="required">
		            </fieldset>

		            <fieldset>
		            	<label for="address">Shipping Address</label>
		    			<textarea name="address" required="required"></textarea>
		            </fieldset>

		            <br>

		    		<h2>Trade Information</h2>

					<fieldset>
		            	<label for="trade_list">Enter your trade list or comments</label>
		    			<textarea name="trade_list"></textarea>
		            </fieldset>

		            <fieldset>
		            	<label for="trade_items">Select the items you're interested in</label>
		    			<?php echo do_shortcode( '[trade-list]' ); ?>
		            </fieldset>

		            <br>

		            <fieldset class="exclude-jewel-cases">
		    			<input name="exclude_jewel_cases" type="checkbox" value="1"> Check if you want to exclude CD jewel cases (we will do the same). This prevents damage during shipping and saves money on postage.
		            </fieldset>

		            <?php wp_nonce_field( 'trade-form-submit', 'trade-form-nonce' ); ?>

		            <fieldset>
		            	<input id="submit" name="submit" type="submit" class="button submit" value="Submit">
		            </fieldset>

				</form>
			</div><!--/#trade-form-wrap-->

		<?php } ?>

	</div>
	<!-- /entry -->    
</article>
<!-- /post -->

<?php endwhile; ?>
<?php endif; ?>	  
<?php get_sidebar(); ?>
<?php get_footer(); ?>