<?php

###########
# WIDGETS #
###########

// remove unneeded widgets and add some custom ones
function hnd_widgets_init() {

	unregister_sidebar('footer-one');
	unregister_sidebar('footer-two');
	unregister_sidebar('footer-three');
	unregister_sidebar('footer-four');

	// home page widget area
	register_sidebar( array(
		'name' => 'Home Page Widgets',
		'id' => 'home-page-widgets',
		'description' => __('Widgets in this area will be shown on the home page.','adapt'),
		'before_widget' => '<div class="sidebar-box clearfix">',
		'after_widget' => '</div>',
		'before_title' => '<h4><span>',
		'after_title' => '</span></h4>',
	) );

	// store page widget area
	register_sidebar( array(
		'name' => 'Store Page Widgets',
		'id' => 'store-page-widgets',
		'description' => __('Widgets in this area will be shown on the store page.','adapt'),
		'before_widget' => '<div class="sidebar-box clearfix">',
		'after_widget' => '</div>',
		'before_title' => '<h4><span>',
		'after_title' => '</span></h4>',
	) );

	// single page widget area
	register_sidebar( array(
		'name' => 'Single Page Widgets',
		'id' => 'single-page-widgets',
		'description' => __('Widgets in this area will be shown on single pages.','adapt'),
		'before_widget' => '<div class="sidebar-box clearfix">',
		'after_widget' => '</div>',
		'before_title' => '<h4><span>',
		'after_title' => '</span></h4>',
	) );
	
	// footer widget area
	register_sidebar( array(
		'name' => 'Footer Widgets',
		'id' => 'footer-widgets',
		'description' => __('Widgets in this area will be shown at the bottom of all pages.','adapt'),
		'before_widget' => '<div class="footer-box clearfix">',
		'after_widget' => '</div>',
		'before_title' => '<h4><span>',
		'after_title' => '</span></h4>',
	) );

	// remove some default Wordpress widgets
	unregister_widget('WP_Widget_Pages');
	unregister_widget('WP_Widget_Calendar');
	unregister_widget('WP_Widget_Archives');
	unregister_widget('WP_Widget_Links');
	unregister_widget('WP_Widget_Meta');
	#unregister_widget('WP_Widget_Search');
	unregister_widget('WP_Widget_Categories');
	unregister_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Widget_Recent_Comments');
	unregister_widget('WP_Widget_RSS');
	unregister_widget('WP_Widget_Tag_Cloud');
	unregister_widget('WP_Nav_Menu_Widget');
	
	// register custom Handstand widgets
	register_widget( 'HND_Carousel_Items_Widget' );
	register_widget( 'HND_Latest_News_Widget' );
	register_widget( 'HND_Latest_Release_Widget' );
	register_widget( 'HND_New_Arrivals_Widget' );
	register_widget( 'HND_Recent_Press_Widget' );
	register_widget( 'HND_Social_Media_Widget' );
	register_widget( 'HND_Upcoming_Events_Widget' );
	
	// NEW widgets
	register_widget( 'HND_Grid_Items_Widget' ); // *** new *** 
	register_widget( 'HND_Item_Slideshow_Widget' );
	register_widget( 'HND_Synchronized_Slideshow_Widget' ); // under construction
	
}
add_action( 'widgets_init', 'hnd_widgets_init' );

/* Carousel Items Widget
-------------------------------------------------------------- */

class HND_Carousel_Items_Widget extends WP_Widget {
	
	function HND_Carousel_Items_Widget() {  
		parent::WP_Widget( false, 'HND: Carousel Items' );
		
		parent::__construct(
			'HND_Carousel_Items_Widget',
			'HND: Carousel Items',
			array( 'description' => 'Displays swipe-able carousel items/posts' )
		);		
	}  
	
	function form( $instance ) {

		// outputs the options form on admin
		if( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'Carousel Items', 'text_domain' );
		}

		$link = $instance['link'];

		if( isset( $instance['carousel-option'] ) ) {
			$selected_option = $instance['carousel-option'];
		} else {
			$selected_option = 'new-arrivals';
		}

		// title input field
		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'title' ) . '"">Title:</label>';
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . esc_attr( $title ) . '">';
		echo '</p>';

		// title input field
		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'link' ) . '"">Link:</label>';
		echo '<input class="widefat" id="' . $this->get_field_id( 'link' ) . '" name="' . $this->get_field_name( 'link' ) . '" type="text" value="' . esc_url( $link ) . '">';
		echo '</p>';

		// options
		$options = array(
			'colored-vinyl',
			'item-press',
			'new-arrivals',
			'related-items',
			'sale-items',
			'merch',
			'cassette-tape',
		);

		// select box for carousel option
		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'carousel-option' ) . '">Carousel Option:</label><br>';
		echo '<select id="' . $this->get_field_id( 'carousel-option' ) . '" name="' . $this->get_field_name( 'carousel-option' ) . '">';
		
		foreach( $options as $option ) {
			$selected = ( $selected_option == $option ) ? 'selected' : '';
			echo '<option value="' . $option . '" ' . $selected . '>' . ucwords( str_replace( '-', ' ', $option ) ) . '</option>';
		}
		
		echo '</select>';
		echo '</p>'; 
	}  
	
	function update( $new_instance, $old_instance ) {

		// processes widget options to be saved
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['link'] = strip_tags( $new_instance['link'] );
		$instance['carousel-option'] = $new_instance['carousel-option'];
		
		return $instance; 
	}  
	
	function widget( $args, $instance ) {

		// get widget title and option
		$title = apply_filters( 'widget_title', $instance['title'] );
		$link = $instance['link'];
		$option = $instance['carousel-option'];
		
		$items_per_page = 24;
		$num_items = 25;
		$num_chunks = 5;
		
		// determine what items to show
		switch( $option ) {

			case 'colored-vinyl':
				$items = new WP_Query( array(
					'post_type' => 'items',
					'post_status' => 'publish',
					'posts_per_page' => $items_per_page,
					//'orderby' => 'rand',
					'meta_query' => array(
						array(
							'key' => 'stock',
							'value' => 0,
							'compare' => '>',
							'type' => 'numeric'
						),	
					),
					'tax_query' => array(
						array(
							'taxonomy' => 'features',
							'field' => 'slug',
							'terms' => 'colored-vinyl'
						),	
					)			
				) );
				break;

			case 'item-press':
				
				global $post;

				// ITEMS
				if( !is_single() && !hnd_is_item( $post->ID ) )
					return;

				$items = new WP_Query( array(
					'post_type' => 'press',
					'post_status' => 'publish',
					'posts_per_page' => $items_per_page,
					'orderby' => 'rand',
					'meta_query' => array(
						array(
							'key' => 'press_item',
							'value' => $post->ID,
							'compare' => '=',
						),						
					)		
				) );


				break;

			case 'new-arrivals':
				$items = new WP_Query( array(
					'post_type' => 'items',
					'post_status' => 'publish',
					'posts_per_page' => $items_per_page,
					'meta_query' => array(
						array(
							'key' => 'stock',
							'value' => 0,
							'compare' => '>',
							'type' => 'numeric'
						),	
					),
					'tax_query' => array(
						array(
							'taxonomy' => 'formats',
							'field' => 'slug',
							'terms' => 'package-deal',
							'operator' => 'NOT IN'
						)
					)
				) );
				break;

			case 'related-items':

				global $post;

				// ITEMS
				if( hnd_is_item( $post->ID ) ) {
				
					// get label ids for tax query
					$labels = get_the_terms( $post->ID, 'labels' );
					if( $labels ) {
						$label_ids = array();
						foreach( $labels as $label ) {
							$label_ids[] = $label->term_id;
						}			
					}

					// get genre ids for tax query
					$genres = get_the_terms( $post->ID, 'genres' );
					if( $genres ) {
						$genre_ids = array();
						foreach( $genres as $genre ) {
							$genre_ids[] = $genre->term_id;
						}			
					}

					# TODO: different comparison criteria for merch (shirts, buttons, etc)

					$args = array(
						'post_type' => 'items',
						//'order' => 'DESC',
						//'orderby' => 'rand',
						'posts_per_page' => $items_per_page,
						'post__not_in' => array( $post->ID ),
						'meta_query' => array(
							array(
								'key' => 'stock',
								'value' => 0,
								'compare' => '>',
								'type' => 'numeric'
							),	
						),
						'tax_query' => array(
							'relation' => 'OR',
							// labels
							array(
					           	'taxonomy' => 'labels',
					           	'terms' => $label_ids
				            ),
							// genres
							array(
					           	'taxonomy' => 'genres',
					           	'terms' => $genre_ids
				            ),
						)
					);
							
					// get related items	
					$items = new WP_Query( $args );
				
				// NEWS
						
				} elseif( hnd_is_news() ) {

					// get recently added items relative to post date
					$items = new WP_Query( array(
						'post_type' => 'items',
					//	'order' => 'DESC',
						'posts_per_page' => 16,
						'date_query' => array(
							array(
								'before'    => $post->post_date,
								'inclusive' => true,
							),
						),
					) );
				
				}

				break;

			case 'sale-items':
				$items = new WP_Query( array(
					'post_type' => 'items',
					'post_status' => 'publish',
					'posts_per_page' => $items_per_page,
					'orderby' => 'rand',
					'meta_query' => array(
						array(
							'key' => 'sale_price',
							'value' => 0,
							'compare' => '>',
							'type' => 'numeric'
						),
						array(
							'key' => 'stock',
							'value' => 0,
							'compare' => '>',
							'type' => 'numeric'
						),
					),
					'tax_query' => array(
						array(
							'taxonomy' => 'formats',
							'field' => 'slug',
							'terms' => 'package-deal',
							'operator' => 'NOT IN'
						)
					)			
				) );
				break;

			case 'merch':
				$items = new WP_Query( array(
					'post_type' => 'items',
					'post_status' => 'publish',
					'posts_per_page' => $items_per_page,
					//'orderby' => 'rand',
					'tax_query' => array(
						array(
							'taxonomy' => 'formats',
							'field'    => 'slug',
							'terms'    => 'merch',
						),
					),
					'meta_query' => array(
						array(
							'key' => 'stock',
							'value' => 0,
							'compare' => '>',
							'type' => 'numeric'
						),
					)			
				) );
				break;
			
			case 'cassette-tape':
				$items = new WP_Query( array(
					'post_type' => 'items',
					'post_status' => 'publish',
					'posts_per_page' => $items_per_page,
					//'orderby' => 'rand',
					'tax_query' => array(
						array(
							'taxonomy' => 'formats',
							'field'    => 'slug',
							'terms'    => 'cassette-tape',
						),
					),
					'meta_query' => array(
						array(
							'key' => 'stock',
							'value' => 0,
							'compare' => '>',
							'type' => 'numeric'
						),
					)			
				) );
				break;
				
		}

		// loop through results
		if( isset( $items ) && $items->have_posts() ) :

			if( hnd_is_news() && is_single() ) {
				$title = 'New Arrivals';
			}

			echo '<div id="' . $option . '-widget" class="sidebar-box carousel-widget full-width clearfix">';
			echo '<div class="widget-content clearfix">';
			
			if ( !empty( $title ) ) echo '<h4><span><a href="' . esc_url( $link ) .'">' . $title . '</a></span></h4>';
			
			/*
			if( $option == 'press' ) {
				
				// simple title for press
			
			} else {
			
				// BEGIN SYNC
				$new_post_ids = wp_list_pluck( $items->posts, 'ID' );
				shuffle( $new_post_ids );
				$new_post_chunks = array_chunk( $new_post_ids, $num_chunks );
				
				echo '<div id="sync-container">';
				if( !empty( $title ) ) {
					echo '<div class="sync-title"><a href="' . esc_url( $link ) .'">' . $title . '</a></div>';
				}
				for($i = 0; $i < $num_chunks; $i++ ) {
					$html = '';
					foreach( $new_post_chunks[$i] as $n ) $html .= hnd_get_featured_image( $n, 'medium', false );				
					echo '<div class="cycle-slideshow" data-cycle-fx=fade data-cycle-timeout=0 data-index=' . $i . '>' . $html . '</div>';
				}
				echo '</div><!--/#sync-container-->';
				// END SYNC
			
			}
			
			*/
			
			echo '<div class="textwidget">';
			
			while( $items->have_posts() ) : $items->the_post();
				get_template_part( 'loop', 'item' );
			endwhile;

			echo '</div><!--/.textwidget-->';
			echo '</div><!--/.widget-content-->';
			echo '</div><!--/#' . $option . '-widget-->';
		endif;
		
		wp_reset_query();	
	}	  
}

/* Grid Items Widget (NEW)
-------------------------------------------------------------- */

class HND_Grid_Items_Widget extends WP_Widget {
	
	function HND_Grid_Items_Widget() {  
		parent::WP_Widget( false, 'HND: Grid Items' );
		
		parent::__construct(
			'HND_Grid_Items_Widget',
			'HND: Grid Items',
			array( 'description' => 'Displays grid of newest arrivals' )
		);		
	}  
	
	function form( $instance ) {

		// outputs the options form on admin
		if( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'Carousel Items', 'text_domain' );
		}

		$link = $instance['link'];

		if( isset( $instance['carousel-option'] ) ) {
			$selected_option = $instance['carousel-option'];
		} else {
			$selected_option = 'new-arrivals';
		}

		// title input field
		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'title' ) . '"">Title:</label>';
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . esc_attr( $title ) . '">';
		echo '</p>';

		// title input field
		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'link' ) . '"">Link:</label>';
		echo '<input class="widefat" id="' . $this->get_field_id( 'link' ) . '" name="' . $this->get_field_name( 'link' ) . '" type="text" value="' . esc_url( $link ) . '">';
		echo '</p>';

		// options
		$options = array(
			'colored-vinyl',
			'item-press',
			'new-arrivals',
			'related-items',
			'sale-items',
		);

		// select box for carousel option
		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'carousel-option' ) . '">Carousel Option:</label><br>';
		echo '<select id="' . $this->get_field_id( 'carousel-option' ) . '" name="' . $this->get_field_name( 'carousel-option' ) . '">';
		
		foreach( $options as $option ) {
			$selected = ( $selected_option == $option ) ? 'selected' : '';
			echo '<option value="' . $option . '" ' . $selected . '>' . ucwords( str_replace( '-', ' ', $option ) ) . '</option>';
		}
		
		echo '</select>';
		echo '</p>'; 
	}  
	
	function update( $new_instance, $old_instance ) {

		// processes widget options to be saved
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['link'] = strip_tags( $new_instance['link'] );
		$instance['carousel-option'] = $new_instance['carousel-option'];
		
		return $instance; 
	}  
	
	function widget( $args, $instance ) {
	
		// get widget title and option
		$title = apply_filters( 'widget_title', $instance['title'] );
		$link = $instance['link'];
		$option = $instance['carousel-option'];

		// TODO: tweak criteria
		// maybe include package deals?
		
		//$post_status = ( is_user_logged_in() ) ? array('publish','draft') : array('publish');
		$post_status = array('publish');
		
		/*
		if( is_user_logged_in() ) {
			
			# TESTING new arrivals relative to news post date
			
			$year = get_the_date( 'Y' );
			$month = get_the_date( 'n' );
			$day = get_the_date( 'j' );

			$items = new WP_Query( array(
				'post_type' => 'items',
				'post_status' => $post_status,
				'posts_per_page' => 20,
				'date_query' => array(
					array(
						'year' => $year,
						'month' => $month,
						'day' => $day,
					),
				),
				'meta_query' => array(
					array(
						'key' => 'stock',
						'value' => 0,
						'compare' => '>',
						'type' => 'numeric'
					),	
				),
				'tax_query' => array(
					array(
						'taxonomy' => 'formats',
						'field' => 'slug',
						'terms' => 'package-deal',
						'operator' => 'NOT IN'
					)
				)
			) );
			
		}
		*/
		
		$items = new WP_Query( array(
			'post_type' => 'items',
			'post_status' => $post_status,
			'posts_per_page' => 20,
			'meta_query' => array(
				array(
					'key' => 'stock',
					'value' => 0,
					'compare' => '>',
					'type' => 'numeric'
				),	
			),
			'tax_query' => array(
				array(
					'taxonomy' => 'formats',
					'field' => 'slug',
					'terms' => 'package-deal',
					'operator' => 'NOT IN'
				)
			)
		) );

		// loop through results
		if( isset( $items ) && $items->have_posts() ) :
			$option = 'new-grid-items';
			echo '<div id="' . $option . '-widget" class="sidebar-box full-width clearfix">';
			echo '<div class="widget-content clearfix">';
			if ( !empty( $title ) ) echo '<h4><span><a href="' . esc_url( $link ) .'">' . $title . '</a></span></h4>';
			echo '<div class="textwidget">';
			
			while( $items->have_posts() ) : $items->the_post();
				get_template_part( 'loop', 'item' );
			endwhile;
			
			// TODO: add more links to shop pages?
			echo '<div align="center" style="padding:10px 0;"><strong>
				<a href="//handstandrecords.com/store/new-arrivals" class="button red">MORE NEW ARRIVALS &raquo;</a>
			</strong></div>';

			echo '</div><!--/.textwidget-->';
			echo '</div><!--/.widget-content-->';
			echo '</div><!--/#' . $option . '-widget-->';
		endif;
		
		wp_reset_query();	
	}	  
}

/* Item Slideshow Widget (NEW)
-------------------------------------------------------------- */

class HND_Item_Slideshow_Widget extends WP_Widget {
	
	function HND_Item_Slideshow_Widget() {  
		parent::WP_Widget( false, 'HND: Item Slideshow' );
		
		parent::__construct(
			'HND_Item_Slideshow_Widget',
			'HND: Item Slideshow',
			array( 'description' => 'Displays newest arrivals in a slideshow with thumbnails' )
		);		
	}  
	
	function form( $instance ) {  
		// outputs the options form on admin  
	}  
	
	function update( $new_instance, $old_instance ) {
		// processes widget options to be saved  
		return $new_instance;  
	}  
	
	function widget( $args, $instance ) {
		
		$new_items = new WP_Query( array(
			'post_type' => 'items',
			'post_status' => 'publish',
			'posts_per_page' => 18,
			'orderby' => 'menu_order',
			/*
			'meta_query' => array(
		        array( 'key' => '_thumbnail_id'), // Show only posts with featured images
		    ),
		    */
			'tax_query' => array(
				array(
					'taxonomy' => 'formats',
					'field' => 'slug',
					'terms' => array( 'package-deal' ),
					'operator' => 'NOT IN'
				),
			)
		) );
		
		if( $new_items->have_posts() ) :
			
			// define data array
			$item_slides = array();
			
			while( $new_items->have_posts() ) : $new_items->the_post();
				
				// compile data if images exists
				if( has_post_thumbnail() && hnd_has_display_image() ) {
					
					# TODO: add checks to make sure proper images exists
					
					$display_image = hnd_get_display_image_src( get_the_ID(), 'large' );
					
					// check for duplicates of multiple items
					if( !in_array( $display_image, $item_slides ) ) {
					
						$item_slides[] = array(
							'title' => hnd_get_nice_title( get_the_title() ),
							'link' => get_permalink(),
							'excerpt' => str_replace( 'More Info', '', strip_tags( get_the_excerpt() ) ),
							'thumbnail' => hnd_get_featured_image_src( get_the_ID(), 'thumbnail' ),
							'display' => $display_image,
						);
					}
				}
				
			endwhile;
			
			// shuffle data array
			#shuffle( $item_slides );
			
			?>
			
			<script>
				jQuery(document).ready(function($){
				
					var slideshows = $('.cycle-slideshow').on('cycle-next cycle-prev', function(e, opts) {
					    // advance the other slideshow
					    slideshows.not(this).cycle('goto', opts.currSlide);
					});
					
					$('#cycle-2 .cycle-slide').click(function(){
					    var index = $('#cycle-2').data('cycle.API').getSlideIndex(this);
					    slideshows.cycle('goto', index);
					});
					
				});
			</script>

			<?php
		
		endif;
		
		wp_reset_query();
		
		?>
		<div id="item-slideshow-widget" class="sidebar-box full-width clearfix">
			<div class="widget-content clearfix">
				<h4><span><a href="/store/new-arrivals/">New Arrivals</a></span></h4>
				<div class="textwidget">
		
					<div id="item-slideshow-1">
					    <div id="cycle-1" class="cycle-slideshow"
					        data-cycle-slides="> div"
					        data-cycle-timeout="5000"
					        >
					        <?php foreach( $item_slides as $item ) : ?>
						        <div><a href="<?php echo esc_url( $item['link'] ); ?>"><img src="<?php echo $item['display']; ?>" width=500 height=500></a></div>
					        <?php endforeach; ?>
					    </div>
					</div>
					
					<div id="item-slideshow-2">
					    <a href="#" class="cycle-prev">&lsaquo;</a>
					    <a href="#" class="cycle-next">&rsaquo;</a>
					    <div id="cycle-2" class="cycle-slideshow"<div id="cycle-2" class="cycle-slideshow"
					        data-cycle-slides="> div"
					        data-cycle-timeout="5000"
					        data-cycle-prev="#item-slideshow-2 .cycle-prev"
					        data-cycle-next="#item-slideshow-2 .cycle-next"
					        data-cycle-caption="#cycle-caption"
					        data-cycle-caption-template="{{cycleTitle}}<p>{{desc}}</p>"
							data-cycle-fx="carousel"
					        data-cycle-carousel-visible="5"
					        data-cycle-carousel-fluid="true"
					        data-allow-wrap="false"
					        >
					        <?php foreach( $item_slides as $item ) : ?>
						        <div data-cycle-title="<?php echo esc_attr( $item['title'] ); ?>" data-cycle-desc="<?php echo esc_attr( $item['excerpt'] ); ?>"><img src="<?php echo $item['thumbnail']; ?>" width=100 height=100></div>
					        <?php endforeach; ?>
					    </div>
					</div>
					<div id="cycle-caption"></div>
				
				</div><!--/.textwidget-->
			</div><!--/.widget-content-->
		</div><!--/#item-slideshow-widget-->
		
		<?php			
	}	  
}

/* Latest News Widget
-------------------------------------------------------------- */

class HND_Latest_News_Widget extends WP_Widget {
	
	function HND_Latest_News_Widget() {  
		parent::WP_Widget( false, 'HND: Latest News' );
		
		parent::__construct(
			'HND_Latest_News_Widget',
			'HND: Latest News',
			array( 'description' => 'Most recent news post' )
		);		
	}  
	
	function form( $instance ) {  
		// outputs the options form on admin  
	}  
	
	function update( $new_instance, $old_instance ) {
		// processes widget options to be saved  
		return $new_instance;  
	}  
	
	function widget( $args, $instance ) {  
		
		if( is_user_logged_in() ) {
			$ps = array( 'draft', 'publish' );
		} else {
			$ps = array( 'publish' );
		}
		
		$ps = array( 'publish' );
		#$ppp = 5;
		$ppp = 1;
		
		$recent_news = new WP_Query( array(
			'post_type' => 'post',
			'category__not_in' => array( 371 ), // notices
			'post_status' => $ps,
			'posts_per_page' => $ppp,
		) );
		
		if( $recent_news->have_posts() ) :
			echo '<div id="latest-news-widget" class="sidebar-box full-width clearfix">';
			echo '	<div class="widget-content clearfix">';
			echo '		<h4><span>Latest News</span></h4>';
			echo '		<div class="textwidget">';
			
			$i = 1;
			
			while( $recent_news->have_posts() ) : $recent_news->the_post();
				
				if( $i == 1 ) {
					
					echo '<div class="latest-news-post">';
					
					// check for gallery before displaying featured image
					$news_content = get_the_content();
					if( !has_shortcode( $news_content, 'gallery' ) ) {
						hnd_featured_image( get_the_ID(), $size = 'large' );
					}
					
					echo '<div class="post-date">' . get_the_date() . ' </div>';
					
					echo '<div class="title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></div>';
					
					if( is_front_page() ) {
						//echo '<div class="excerpt">' . get_the_excerpt() . '</div>';
						the_content();
					} else {
						echo '<div class="excerpt">' . get_the_excerpt() . '</div>';
					}
					
					echo '</div><!--/.latest-news-post-->';
					echo '<div class="clear"></div>';
					
					
				} else {
					get_template_part( 'loop', 'item' );
				}
				
				$i++;
				
			endwhile;
			
			echo '		</div><!--/.textwidget-->';
			echo '	</div><!--/.widget-content-->';
			echo '</div><!--/#latest-news-widget-->';
		endif;
		
		wp_reset_query();	
	}	  
}

/* Latest Release Widget
-------------------------------------------------------------- */

class HND_Latest_Release_Widget extends WP_Widget {
	
	function HND_Latest_Release_Widget() {  
		parent::WP_Widget( false, 'HND: Latest Release' );
		
		parent::__construct(
			'HND_Latest_Release_Widget',
			'HND: Latest Release',
			array( 'description' => 'Latest release from Handstand' )
		);		
	}  
	
	function form( $instance ) {  
		// outputs the options form on admin  
	}  
	
	function update( $new_instance, $old_instance ) {
		// processes widget options to be saved  
		return $new_instance;  
	}  
	
	function widget( $args, $instance ) {

		// dont display on discography page
		if( is_page( 'Discography' ) )
			return;

		$latest_release = new WP_Query( array(
			'post_type' => 'items',
			'post_status' => 'publish',
			'posts_per_page' => 2,
			//'p' => 5671 // mutant scum
			//'p'=> 3809 // flesh eating creeps
			'post__in'=> array(5671,3809), // mutant scum & flesh eating creeps
			'orderby' => 'ASC',
			/*
			'meta_query' => array(
				array(
					'key' => 'labels',
					'value' => hnd_get_label_id( 'Handstand Records' ),
					'compare' => '=',
				),	
			),
			// exclude package deals
			'tax_query' => array(
				array(
					'taxonomy' => 'formats',
					'field' => 'slug',
					'terms' => 'package-deal',
					'operator' => 'NOT IN'
				)
			)
			*/
		) );
		
		if( $latest_release->have_posts() ) :
			echo '<div id="latest-release-widget" class="sidebar-box clearfix">';
			echo '	<div class="widget-content clearfix">';
			echo '		<h4><span>Out Now!</span></h4>';
			echo '		<div class="textwidget">';
			
			while( $latest_release->have_posts() ) : $latest_release->the_post();
				echo '<div class="release">';
				echo hnd_get_featured_image( get_the_ID(), 'medium' );
				if( is_front_page() ) {
					echo hnd_get_disc_image( get_the_ID(), 'medium' );
				}
				echo do_shortcode( hnd_get_bandcamp_audio() );
				echo '<div class="title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></div>';
				echo '<div class="excerpt">' . get_the_excerpt() . '</div>';
				echo '<div class="clear"></div><br>';
				echo hnd_get_item_press();
				echo '</div>';
			endwhile;
		
			echo '		</div><!--/.textwidget-->';
			echo '	</div><!--/.widget-content-->';
			echo '</div><!--/#latest-release-widget-->';
		endif;
		
		wp_reset_query();	
	}	  
}

/* Synchronized Slideshow Widget
-------------------------------------------------------------- */

class HND_Synchronized_Slideshow_Widget extends WP_Widget {
	
	function HND_Synchronized_Slideshow_Widget() {  
		parent::WP_Widget( false, 'HND: Synchronized Slideshow' );
		
		parent::__construct(
			'HND_Synchronized_Slideshow_Widget',
			'HND: Synchronized Slideshow',
			array( 'description' => 'New arrivals in a fancy slideshow' )
		);		
	}  
	
	function form( $instance ) {  
		// outputs the options form on admin  
	}  
	
	function update( $new_instance, $old_instance ) {
		// processes widget options to be saved  
		return $new_instance;  
	}  
	
	function widget( $args, $instance ) {
	
		$num_items = 25; // should be divisible by 4 columns
		$num_chunks = 5; // should be divisible by $num_items
		$sync_title = 'New Arrivals';
		
		$new_arrivals = new WP_Query( array(
			'post_type' => 'items',
			'post_status' => 'publish',
			'posts_per_page' => $num_items,
			'tax_query' => array(
				array(
					'taxonomy' => 'formats',
					'field' => 'slug',
					'terms' => 'package-deal',
					'operator' => 'NOT IN'
				)
			)
		) );
		
		if( $new_arrivals->have_posts() ) :
			
			// BEGIN SYNC
			$new_post_ids = wp_list_pluck( $new_arrivals->posts, 'ID' );
			shuffle( $new_post_ids );
			$new_post_chunks = array_chunk( $new_post_ids, $num_chunks );
			
			echo '<div id="sync-container">';
			echo '<div class="sync-title">' . $sync_title . '</div>';
			for($i = 0; $i < $num_chunks; $i++ ) {
				$html = '';
				foreach( $new_post_chunks[$i] as $n ) $html .= hnd_get_featured_image( $n, 'medium', false );				
				echo '<div class="cycle-slideshow" data-cycle-fx=fade data-cycle-timeout=0 data-index=' . $i . '>' . $html . '</div>';
			}
			echo '</div><!--/#sync-container-->';
			// END SYNC
			
		endif;
		
		wp_reset_query();	
	}	  
}

/* New Arrivals Widget
-------------------------------------------------------------- */

class HND_New_Arrivals_Widget extends WP_Widget {
	
	function HND_New_Arrivals_Widget() {  
		parent::WP_Widget( false, 'HND: New Arrivals' );
		
		parent::__construct(
			'HND_New_Arrivals_Widget',
			'HND: New Arrivals',
			array( 'description' => 'Newly added distro items' )
		);		
	}  
	
	function form( $instance ) {  
		// outputs the options form on admin  
	}  
	
	function update( $new_instance, $old_instance ) {
		// processes widget options to be saved  
		return $new_instance;  
	}  
	
	function widget( $args, $instance ) {
		
		$new_arrivals = new WP_Query( array(
			'post_type' => 'items',
			'post_status' => 'publish',
			'posts_per_page' => 16,
			'tax_query' => array(
				array(
					'taxonomy' => 'formats',
					'field' => 'slug',
					'terms' => 'package-deal',
					'operator' => 'NOT IN'
				)
			)
		) );
		
		if( $new_arrivals->have_posts() ) :
			echo '<div id="new-arrivals-box-widget" class="sidebar-box full-width clearfix">';
			echo '	<div class="widget-content clearfix">';
			echo '		<h4><span>New Arrivals</span></h4>';
			echo '		<div class="textwidget">';
			
			while( $new_arrivals->have_posts() ) : $new_arrivals->the_post();
				echo hnd_get_featured_image( get_the_ID(), 'medium' );
				echo '<a class="tooltip" href="' . get_permalink() . '">' . get_the_title() . '</a>';
			endwhile;
		
			echo '		</div><!--/.textwidget-->';
			echo '	</div><!--/.widget-content-->';
			echo '</div><!--/#new-arrivals-widget-->';
		endif;
		
		wp_reset_query();	
	}	  
}

/* Recent Press Widget
-------------------------------------------------------------- */

class HND_Recent_Press_Widget extends WP_Widget {
	
	function HND_Recent_Press_Widget() {  
		parent::WP_Widget( false, 'HND: Recent Press' );
		
		parent::__construct(
			'HND_Recent_Press_Widget',
			'HND: Recent Press',
			array( 'description' => 'Most recent press post' )
		);		
	}  
	
	function form( $instance ) {  
		// outputs the options form on admin  
	}  
	
	function update( $new_instance, $old_instance ) {
		// processes widget options to be saved  
		return $new_instance;  
	}  
	
	function widget( $args, $instance ) {

		// dont display on press page
		if( get_post_type() == 'press' )
			return;

		// only retrieve press for a specific release
		
		$num_press = ( is_front_page() ) ? 2 : 1;
			
		$recent_press = new WP_Query( array(
			'post_type' => 'press',
			'post_status' => 'publish',
			'posts_per_page' => $num_press,
			/*
			'orderby' => 'rand',
			'meta_query' => array(
				array(
					'key' => 'press_item',
					'value' => array( 1130 ),
					'compare' => 'IN',
				),	
			),
			*/
		) );
		
		if( $recent_press->have_posts() ) :
			echo '<div id="recent-press-widget" class="sidebar-box clearfix">';
			echo '	<div class="widget-content clearfix">';
			echo '		<h4><span>Recent Press</span></h4>';
			echo '		<div class="textwidget">';
			
			while( $recent_press->have_posts() ) : $recent_press->the_post();
				$press_item = get_post_meta( get_the_ID(), 'press_item', false );
				
				/*
				if( is_front_page() ) {
					hnd_featured_image( $press_item[0]['ID'], 'large' );
				} else {				
					hnd_featured_image( get_the_ID(), 'large' );
				}
				*/
				
				echo '<div class="release">';
				hnd_featured_image( get_the_ID(), 'large' );
				
				echo '<div class="title"><a href="' . get_permalink() . '">' . get_the_title() . ' review:</a></div>';
				if( !is_front_page() ) echo '<div class="press-item">' . $press_item[0]['post_title'] . '</div>';
				echo get_the_excerpt();
				echo '<div class="small"><a href="' . get_permalink() . '">Read Full Review &#9654;</a></div>';
				echo '</div>';
				
			endwhile;
			
			echo '		</div><!--/.textwidget-->';
			echo '	</div><!--/.widget-content-->';
			echo '</div><!--/#recent-press-widget-->';
		endif;
		
		wp_reset_query();	
	}	  
}

/* Social Media Widget
-------------------------------------------------------------- */

class HND_Social_Media_Widget extends WP_Widget {
	
	function HND_Social_Media_Widget() {  
		parent::WP_Widget( false, 'HND: Social Media' );
		
		parent::__construct(
			'HND_Social_Media_Widget',
			'HND: Social Media',
			array( 'description' => 'Social media links' )
		);		
	}  
	
	function form( $instance ) {  
		// outputs the options form on admin  
	}  
	
	function update( $new_instance, $old_instance ) {
		// processes widget options to be saved  
		return $new_instance;  
	}  
	
	function widget( $args, $instance ) {

		$new_arrivals = new WP_Query( array(
			'post_type' => 'items',
			'post_status' => 'publish',
			'posts_per_page' => 9,
			'tax_query' => array(
				array(
					'taxonomy' => 'formats',
					'field' => 'slug',
					'terms' => 'package-deal',
					'operator' => 'NOT IN'
				)
			)
		) );
		
		if( $new_arrivals->have_posts() ) :
			echo '<div id="social-media-widget" class="sidebar-box clearfix">';
			echo '	<div class="widget-content clearfix">';
			echo '		<h4><span>Connect</span></h4>';
			echo '		<div class="textwidget">';
			
			echo hnd_social_media();
		
			echo '		</div><!--/.textwidget-->';
			echo '	</div><!--/.widget-content-->';
			echo '</div><!--/#social-media-widget-->';
		endif;
		
		wp_reset_query();	
	}	  
}

/* Upcoming Events Widget
-------------------------------------------------------------- */

class HND_Upcoming_Events_Widget extends WP_Widget {
	
	function HND_Upcoming_Events_Widget() {  
		parent::WP_Widget( false, 'HND: Upcoming Events' );
		
		parent::__construct(
			'HND_Upcoming_Events_Widget',
			'HND: Upcoming Events',
			array( 'description' => 'Upcoming events posts' )
		);		
	}  
	
	function form( $instance ) {  
		// outputs the options form on admin  
	}  
	
	function update( $new_instance, $old_instance ) {
		// processes widget options to be saved  
		return $new_instance;  
	}  
	
	function widget( $args, $instance ) {

		$upcoming_events = new WP_Query( array(
			'post_type' => 'events',
			'post_status' => array( 'publish', 'future' ),
			'posts_per_page' => 2,
			'date_query' => array(
				array(
					'after'     => date( 'Y/m/d' ),
					'inclusive' => true,
				),
			),
		) );
		
		if( $upcoming_events->have_posts() ) :
			
			echo '<div id="upcoming-events-widget" class="sidebar-box full-width clearfix">';
			echo '	<div class="widget-content clearfix">';
			echo '		<h4><span>Upcoming Events</span></h4>';
			echo '		<div class="textwidget">';
			
			while( $upcoming_events->have_posts() ) : $upcoming_events->the_post();
				
				$date = hnd_get_event_date();
				$venue = hnd_get_event_venue();
				$time = hnd_get_event_time();
				$price = hnd_get_event_price();
				$age_limit = hnd_get_event_age_limit();
				
				// conditional for img
				if( has_post_thumbnail() ) hnd_featured_image( get_the_ID(), 'large' );
				
				echo '<div class="events event">';
				echo '<h1 class="single-title"><span class="red">' . get_the_excerpt() . '</span></h1><br>';
				echo get_the_content() . '<br><br>';
				echo '<h1 class="single-title"><a href="' . get_permalink() . '">' . hnd_split_event_artists( get_the_title() ) . '</a></h1>';
				echo hnd_get_event_content( get_the_content() );
				echo '<br><a href="' . hnd_get_event_link() . '" class="button red" target="_blank">Facebook Event</a>';
				echo '</div>';

				/*
				
					echo '<div class="title">';
					if( $date ) echo $date;
					if( $venue ) echo ' @ ' . $venue;
					echo '</div><!--/.title-->';
					echo '<div class="event-details">';
					if( $time ) echo '<div>' . $time . '</div>';
					if( $price ) echo '<div>' . $price . '</div>';
					if( $age_limit ) echo '<div>' . $age_limit . '</div>';
					echo '<div class="small"><a href="' . get_permalink() . '">More Info &#9654;</a></div>';
					echo '</div><!--/.event-details-->';
					echo str_replace( ' / ', '<br>', get_the_title() );
					hnd_event_calendar_button();
										
				*/

			endwhile;
			
			echo '		</div><!--/.textwidget-->';
			echo '	</div><!--/.widget-content-->';
			echo '</div><!--/#upcoming-events-widget-->';
		endif;
		
		wp_reset_query();	
	}	  
}


/* Dashboard Widgets
-------------------------------------------------------------- */

function hnd_add_dashboard_widgets() {
	
	// new arrivals
	wp_add_dashboard_widget(
		'hnd_new_arrivals_dashboard_widget',         	// Widget slug.
		__( 'New Arrivals' ),         						// Title.
		'hnd_new_arrivals_dashboard_widget_function' 	// Display function.
	);

	// quick search
	wp_add_dashboard_widget(
		'hnd_quick_search_dashboard_widget',
		__( 'Quick Search' ),
		'hnd_quick_search_dashboard_widget_function'
	);
	
	// quick item
	wp_add_dashboard_widget(
		'hnd_quick_item_dashboard_widget',
		__( 'Quick Item' ),
		'hnd_quick_item_dashboard_widget_function'
	);
	
	if ( is_blog_admin() && current_user_can( get_post_type_object( 'post' )->cap->create_posts ) ) {
		
	}

}
add_action( 'wp_dashboard_setup', 'hnd_add_dashboard_widgets' );

// new arrivals
function hnd_new_arrivals_dashboard_widget_function() {
	
	// DRAFTS
	
	$new_drafts = new WP_Query( array(
		'post_type' => 'items',
		'post_status' => array( 'draft' ),
		'posts_per_page' => -1,
	) );
	
	if( $new_drafts->have_posts() ) :
		echo '<div style="width:50%; display:inline-block; vertical-align: text-top;">';
		echo '<h3>ON DECK (draft)</h3>';
		while( $new_drafts->have_posts() ) : $new_drafts->the_post();
			echo '<div class="item">';
			echo hnd_get_featured_image( get_the_ID(), 'tiny' );
			echo '<a href="' . get_edit_post_link() . '">' . get_the_title() . '</a>';
			echo '</div>';
		endwhile;
		echo '</div>';
	endif;
	
	wp_reset_query();
	
	// NEW ARRIVALS
	
	$new_arrivals = new WP_Query( array(
		'post_type' => 'items',
		'post_status' => array( 'publish' ),
		'posts_per_page' => 15,
	) );
	
	if( $new_arrivals->have_posts() ) :
		echo '<div style="width:50%; display:inline-block; vertical-align: text-top;">';
		echo '<h3>NEW SHIT (published)</h3>';
		while( $new_arrivals->have_posts() ) : $new_arrivals->the_post();
			echo '<div class="item">';
			echo hnd_get_featured_image( get_the_ID(), 'tiny' );
			echo '<a href="' . get_edit_post_link() . '">' . get_the_title() . '</a>';
			echo '</div>';
		endwhile;
		echo '</div>';
	endif;
	
	wp_reset_query();
	
	echo '<div class="clear"></div>';
}

// quick search
function hnd_quick_search_dashboard_widget_function() {
    ?>
    <form id="posts-filter" action="<?php echo get_bloginfo('url'); ?>/wp-admin/edit.php" method="get">
        <div class="search-box" style="height:50px">
            <input type="search" id="item-search-input" name="s" value="">
            <input type="hidden" name="post_type" value="items" /> 
            <input type="submit" name="" id="search-submit" class="button" value="Search Items"></p>
        </div>
	</form>

    <!--
    <form id="posts-filter" action="<?php echo get_bloginfo('url'); ?>/wp-admin/edit.php" method="get">
        <div class="search-box" style="height:50px">
            <input type="search" id="post-search-input" name="s" value="">
            <input type="submit" name="" id="search-submit" class="button" value="Search News"></p>
        </div>
	</form>

	<form id="pages-filter" action="<?php echo get_bloginfo('url'); ?>/wp-admin/edit.php" method="get">
        <div class="search-box" style="height:50px">
            <input type="search" id="page-search-input" name="s" value="">
            <input type="submit" name="" id="page-search-submit" class="button" value="Search Pages"></p>
        </div>
    </form>
    -->
    <?php
}







/* TO DO */

/* quickly add new item draft from the dashboard */

function hnd_quick_item_dashboard_widget_function( $error_msg = false ) {
	global $post_ID;
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}
	/* Check if a new auto-draft (= no new post_ID) is needed or if the old can be used */
	$last_post_id = (int) get_user_option( 'dashboard_quick_press_last_post_id' ); // Get the last post_ID
	if ( $last_post_id ) {
		$post = get_post( $last_post_id );
		if ( empty( $post ) || $post->post_status != 'auto-draft' ) { // auto-draft doesn't exists anymore
			$post = get_default_post_to_edit( 'post', true );
			update_user_option( get_current_user_id(), 'dashboard_quick_press_last_post_id', (int) $post->ID ); // Save post_ID
		} else {
			$post->post_title = ''; // Remove the auto draft title
		}
	} else {
		$post = get_default_post_to_edit( 'post' , true);
		$user_id = get_current_user_id();
		// Don't create an option if this is a super admin who does not belong to this site.
		if ( ! ( is_super_admin( $user_id ) && ! in_array( get_current_blog_id(), array_keys( get_blogs_of_user( $user_id ) ) ) ) )
			update_user_option( $user_id, 'dashboard_quick_press_last_post_id', (int) $post->ID ); // Save post_ID
	}
	$post_ID = (int) $post->ID;
?>

	<form name="post" action="<?php echo esc_url( admin_url( 'post.php' ) ); ?>" method="post" id="quick-item" class="initial-form hide-if-no-js">

		<?php if ( $error_msg ) : ?>
		<div class="error"><?php echo $error_msg; ?></div>
		<?php endif; ?>
		
		<p>UNDER CONSTRUCTION</p>
		
		<!--
		
		<div class="input-text-wrap" id="title-wrap">
			<label class="screen-reader-text prompt" for="title" id="title-prompt-text">Item</label>
			<input type="text" name="post_title" id="title" autocomplete="off" />
		</div>
		
		<p class="submit">
			<input type="hidden" name="action" id="quickpost-action" value="post-quickdraft-save" />
			<input type="hidden" name="post_ID" value="<?php echo $post_ID; ?>" />
			<input type="hidden" name="post_type" value="items" />
			<?php wp_nonce_field( 'add-post' ); ?>
			<?php submit_button( __( 'Save Draft' ), 'primary', 'save', false, array( 'id' => 'save-post' ) ); ?>
			<br class="clear" />
		</p>
		
		-->

	</form>
	<?php
}
