<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 * Template Name: Store - Order Confirmation
 */

// get response from paypal
if( isset( $_POST ) && ( $_POST['processed'] !== 1 ) ) {

    // check paypal transaction id against DB
    $orders = new WP_Query( array(
        'post_type' => 'wpsc_cart_orders',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'wpsc_txn_id',
                'value' => $_POST['txn_id'],
                'compare' => '=',
            ),  
        )
    ) );
    
    if( $orders->have_posts() ) {

        // get the order id
        while( $orders->have_posts() ) : $orders->the_post();
            $order_id = get_the_ID();
        endwhile;

        // set order data
        $order = array(
            'total' => $_POST['mc_gross'],
            'shipping' => $_POST['mc_shipping'],
            'fee' => $_POST['mc_fee'],
            'num_items' => $_POST['num_cart_items'],
            'items' => array(),
        );
        
        // get items and quantities
        for( $i = 1; $i <= $_POST['num_cart_items']; $i++ ) {
            
            // check if item name and quantity exist
            if( isset( $_POST['item_name'.$i] ) && isset( $_POST['quantity'.$i] ) ) { 
                
                $order['items'][$i] = array(
                    'id' => $_POST['item_number'.$i],
                    'qty' => $_POST['quantity'.$i],
                    'title' => $_POST['item_name'.$i],
                );
            }
        }

        // update item quantities in WP if flag not set
        if( '1' !== get_post_meta( $order_id, 'processed', true ) ) {
        
            foreach( $order['items'] as $item ) {

                // get current qty
                $current_qty = get_post_meta( $item['id'], 'stock', true );
                $new_qty = $current_qty - $item['qty'];

                // send an email if there is not enough quantity in stock
                if( $new_qty < 0 ) {

                    $order_title = str_replace( '&#8243;', '-INCH', get_the_title( $item['id'] ) );

                    wp_mail(
                        'will@handstandrecords.com', // to
                        'OUT OF STOCK - ' . $order_title, // subject
                        'The following item was ordered but there are not enough copies in stock.
                        
                        ORDER: #' . $order_id . '
                        TITLE: ' . $order_title . '
                        STOCK: ' . $item['qty'] . ' ordered / ' . $current_qty . ' in stock

                        Please contact seller.'
                    );
                    
                } else {
                    // update the quantity if there is enough in stock
                    update_post_meta( $item['id'], 'stock', $new_qty );
                }
            }

            // update flag
            update_post_meta( $order_id, 'processed', '1' );
        }
    
    } else {
    
        //error_log('ORDER TRANSACTION ID ' . $_POST['txn_id'] . 'DOES NOT EXIST');
        
        // redirect to Store page
        wp_redirect( get_bloginfo( 'url' ) . '/store/' );
    }
    
    wp_reset_query();

} else {
    // redirect to Store page
    wp_redirect( get_bloginfo( 'url' ) . '/store/' );
}

?>

<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<header id="page-heading">
	<?php hnd_breadcrumb_nav(); ?>
</header>
<!-- /page-heading -->

<article class="post order-confirmation full-width clearfix">
    <div class="entry clearfix"><?php

        // content
        the_content();
        
        // TODO: animation of records getting added to a box
    	echo ' <div id="order-items">';
        foreach( $order['items'] as $item ) {
            hnd_featured_image( $item['id'], 'medium' );
        }
        echo '  </div><!--/#order-items-->';
        
        // social media
        echo '  <div id="order-social">';
        echo '      <h2>Follow Handstand Records</h2>';
        echo hnd_social_media();
        echo '  </div><!--/#order-social-->';
        
	?></div>
</article><!-- /post -->

<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>