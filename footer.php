<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 */
$options = get_option( 'adapt_theme_settings' );

?>
</div>
<!-- /main -->


<?php

// TESTING: footer widget areas
/*
if( is_user_logged_in() ) {
	?><div class="footer-widgets"><?php dynamic_sidebar('footer-widgets'); ?></div><?php
}
*/

?>

    <div id="footer" class="clearfix">
        
        <?php wp_nav_menu( array( 'menu' => 'Footer Menu' ) ); ?>
          
		<div id="footer-bottom" class="clearfix">
        
            <div id="copyright"><?php
                echo '&copy; 2000 - ' . date('Y') . ' ' . get_bloginfo( 'name' );
            ?></div>
            <!-- /copyright -->
            
            <div id="back-to-top">
                <a href="#toplink"><?php _e('BACDAFUCUP', ''); ?> &uarr;</a>
            </div>
            <!-- /back-to-top -->
        
        </div>
        <!-- /footer-bottom -->
        
	</div>
	<!-- /footer -->
    
</div>
<!-- wrap --> 

<!-- WP Footer -->
<?php wp_footer(); ?>
</body>
</html>