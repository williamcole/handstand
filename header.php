<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 */
$options = get_option( 'adapt_theme_settings' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<!-- Mobile Specific
================================================== -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<!--[if lt IE 9]>
	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->

<!-- Meta Tags -->
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<meta name="description" content="<?php
	if ( is_single() ) {
		single_post_title('', true);
	} else {
		bloginfo('name'); echo " - "; bloginfo('description');
	}
?>" />
    
<!-- Title Tag
================================================== -->
<title><?php
	wp_title('');
	if( wp_title( '', false ) ) { echo ' | '; } 
	bloginfo('name');
?></title>
    
<?php if(!empty($options['favicon'])) { ?>

<!-- Favicon
================================================== -->
<!-- <link rel="icon" type="image/png" href="<?php echo $options['favicon']; ?>" /> -->
<?php } ?>

<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.ico" />
<link rel="apple-touch-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/header-logo.png" />
        
<!-- Main CSS
================================================== -->
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" />


<!-- Load HTML5 dependancies for IE
================================================== -->
<!--[if IE]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<!--[if lte IE 7]>
	<script src="js/IE8.js" type="text/javascript"></script><![endif]-->
<!--[if lt IE 7]>
	<link rel="stylesheet" type="text/css" media="all" href="css/ie6.css"/>
<![endif]-->

<!-- WP Head
================================================== -->
<?php if ( is_single() || is_page() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>

<div id="wrap" class="clearfix">

    <header id="masterhead" class="clearfix">

        <div id="header-logo">
            <div id="logo-image">
                <a href="<?php bloginfo( 'url' ); ?>/" title="<?php bloginfo( 'name' ); ?>"><img src="<?php echo get_stylesheet_directory_uri(). '/images/header-logo.png'; ?>" alt="<?php bloginfo( 'name' ); ?>" /></a>
            </div>
            <div id="logo-text">
                <!--
                <h1 id="site-title"><a href="<?php bloginfo( 'url' ); ?>/" title="<?php bloginfo( 'name' ) ?>"><?php echo hnd_get_header_text(); ?></a></h1>
                <h2 id="tagline"><?php bloginfo( 'description' ); ?></h2>
                -->
                <h1 id="site-title"><a href="<?php bloginfo( 'url' ); ?>/" title="<?php bloginfo( 'name' ) ?>"><img src="<?php echo get_stylesheet_directory_uri(). '/images/handstand-header-logo-title.png'; ?>" alt="<?php bloginfo( 'name' ); ?>" /></a></h1>
                <h2 id="tagline"><a href="<?php bloginfo( 'url' ); ?>/" title="<?php bloginfo( 'description' ) ?>"><img src="<?php echo get_stylesheet_directory_uri(). '/images/handstand-header-logo-tagline.png'; ?>" alt="<?php bloginfo( 'description' ); ?>" /></a></h2>
            </div>
        </div>

        <div id="header-nav">
            <nav id="mobile-nav">
                <div class="mobile-nav-box">
                    <a id="mobile-nav-menu-button" class="mobile-nav-button" href="Javascript://Menu">Menu</a>
                    <nav id="mobile-nav-menu" class="mobile-nav-dropdown navigation clearfix">
                        <?php wp_nav_menu( array(
                            'theme_location' => 'menu',
                            'sort_column' => 'menu_order',
                            'menu_class' => 'sf-menu',
                            'fallback_cb' => 'default_menu'
                        )); ?>
                    </nav>
                </div>
                <div class="mobile-nav-box">
                    <a id="mobile-nav-search-button" class="mobile-nav-button" href="Javascript://Search">Search</a>
                    <div id="mobile-nav-search" class="mobile-nav-dropdown"><?php get_search_form(); ?></div>
                </div>
                <div class="mobile-nav-box">
                    <a id="mobile-nav-cart-button" class="mobile-nav-button mobile-nav-button-exclude" href="' . home_url() . '/store/shopping-cart">
                        <span id="total-cart-items"><?php if( hnd_get_shopping_cart_total_items() > 0 ) hnd_shopping_cart_total_items(); ?></span>
                    </a>
                    <div id="mobile-nav-cart" class="mobile-nav-dropdown"><?php hnd_shopping_cart(); ?></div>
                </div>
            </nav>

            <div id="search-form" class="header-bar"><?php get_search_form(); ?></div>
            <div id="cart-button" class="header-bar"><?php hnd_shopping_cart(); ?></div>
        </div>

        <div class="clear"></div>
        
        <nav id="masternav" class="navigation clearfix">
            <?php wp_nav_menu( array(
                'theme_location' => 'menu',
                'sort_column' => 'menu_order',
                'menu_class' => 'sf-menu',
                'fallback_cb' => 'default_menu'
            )); ?>
        </nav>
        <!-- /masternav -->

    </header><!-- /masterhead -->
    
<div id="main" class="clearfix">
	<?php

    hnd_current_notices();

    // TODO: increase z-index, style better, change text to EDIT {post_type}
    // admin edit post link
    //edit_post_link( 'Edit' );
    
    ?>