<?php

#########
# ADMIN #
#########

// test function for admin Will
function is_wbc3() {
	$bool = false;
	$user = wp_get_current_user();
	if( $user && isset( $user->user_login ) && ( 'handstand' == $user->user_login ) ) $bool = true;
	return $bool;	
}

// customize the admin menu to remove unused pages
function hnd_admin_remove_menu_pages() {
	remove_menu_page( 'link-manager.php' );
	//remove_menu_page( 'edit.php?post_type=feedback' );
	remove_menu_page( 'edit.php?post_type=portfolio' );
	remove_menu_page( 'edit.php?post_type=slides' );
	remove_menu_page( 'edit.php?post_type=hp_highlights' );	
}
add_action( 'admin_menu', 'hnd_admin_remove_menu_pages' );

// better way to add css to admin backend
function hnd_admin_styles() {
    
    // admin css
    wp_register_style( 'hnd_admin_stylesheet', get_stylesheet_directory_uri() . '/css/admin.css' );
    wp_enqueue_style( 'hnd_admin_stylesheet' );
    
    // admin js

}
add_action( 'admin_enqueue_scripts', 'hnd_admin_styles' );

// add custom editor style
function hnd_image_editor_mce_css( $mce_css ) {	
	$mce_css .= ', ' . get_stylesheet_directory_uri() . '/css/editor-style.css';
	return $mce_css;
}
//add_filter( 'mce_css', 'hnd_image_editor_mce_css' );
add_editor_style( 'css/editor-style.css' );

// toggle some custom fields depending on post type
function hnd_toggle_custom_fields( $post_type, $post ) {
	switch( $post_type ) {	
		case 'post': wp_enqueue_script( 'admin-toggle-post-custom-fields', get_stylesheet_directory_uri() . '/js/admin-toggle-post-custom-fields.js', array( 'jquery' ), null, false ); break;
		case 'items': wp_enqueue_script( 'admin-toggle-item-custom-fields', get_stylesheet_directory_uri() . '/js/admin-toggle-item-custom-fields.js', array( 'jquery' ), null, false ); break;	
	}    
}
add_action( 'add_meta_boxes', 'hnd_toggle_custom_fields', 10, 2 );

// add select box filters for all taxonomies
function hnd_restrict_manage_posts() {
    
    global $typenow;
        
    // enable filters for Items and Contacts
    $post_types = array(
    	'items',
    	'contacts',
    );

    if ( in_array( $typenow, $post_types ) ) {

    	$filters = get_object_taxonomies( $typenow );
		
		foreach( $filters as $tax_slug ) {
        
            $tax_obj = get_taxonomy( $tax_slug );
        	
        	wp_dropdown_categories( array(
                'show_option_all' => __( $tax_obj->label ),
                'taxonomy' => $tax_slug,
                'name' => $tax_obj->name,
                'orderby' => 'name',
                'selected' => $_GET[$tax_obj->query_var],
                'hierarchical' => $tax_obj->hierarchical,
                'show_count' => false,
                'hide_empty' => true
            ) );
        }
    }
}
add_action( 'restrict_manage_posts', 'hnd_restrict_manage_posts' );

function hnd_convert_restrict( $query ) {
    
    global $pagenow, $typenow;
    
    if( $pagenow == 'edit.php' ) {
        $filters = get_object_taxonomies( $typenow );
        foreach( $filters as $tax_slug ) {
            $var = &$query->query_vars[$tax_slug];
            if( isset( $var ) ) {
                $term = get_term_by( 'id', $var, $tax_slug );
                $var = $term->slug;
            }
        }
    }
    
    return $query;
}
add_filter( 'parse_query','hnd_convert_restrict' );

function hnd_action_row( $actions, $post ) {
    if( $post->post_type == 'items' ) {
        
        # TODO ???
     	//$actions['out_of_stock'] = '<a class="custom" href="http://www.google.com/?q=' . get_permalink( $post->ID ) . '">Out of Stock</a>';
    	
    	    	
    	############
    	# BANDCAMP #
    	############
    	
    	$actions['bandcamp'] = '<a class="custom find" target="_blank" href="http://www.bandcamp.com/search/?q=' . urlencode( $post->post_title ) . '&type=all">Bandcamp</a>';
    	
    	/*
    	$bandcamp_release_id = get_post_meta( $post->ID, 'bandcamp_release_id', true );
    	if( !empty( $bandcamp_release_id ) ) {
    		// if item has id
    		$actions['bandcamp'] = '<a class="custom view" target="_blank" href="http://www.bandcamp.com/release/' . $bandcamp_release_id . '">Bandcamp</a>';
    	} else {
    		// link to search
    		$actions['bandcamp'] = '<a class="custom find" target="_blank" href="http://www.bandcamp.com/search/?q=' . urlencode( $post->post_title ) . '&type=all">Bandcamp</a>';
    	}
    	*/
    	
    	###########
    	# DISCOGS #
    	###########
    	
    	$discogs_release_id = get_post_meta( $post->ID, 'discogs_release_id', true );
    	
    	if( !empty( $discogs_release_id ) ) {
    		// if item has id
    		$actions['discogs'] = '<a class="custom view" target="_blank" href="http://www.discogs.com/release/' . $discogs_release_id . '">Discogs</a>';
    	} else {
    		// link to search
    		$actions['discogs'] = '<a class="custom find" target="_blank" href="http://www.discogs.com/search/?q=' . urlencode( $post->post_title ) . '&type=all">Discogs</a>';
    	}
   	
   	}
    return $actions;
}
add_filter( 'post_row_actions', 'hnd_action_row', 10, 2 );

/* Facebook and Twitter Open Graph Tags
-------------------------------------------------------------- */

function hnd_open_graph_tags( $og_tags ) {
    
    // default image if no post thumbnail
    if( !has_post_thumbnail() ) {
    	$og_tags['og:image'] = get_bloginfo('url') . '/wp-content/uploads/2012/10/handstand-logo.png';
		$og_tags['og:image:alt'] = get_bloginfo('url') . '/wp-content/uploads/2012/10/handstand-logo.png';
    }
    
    // temporary custom image for Home page
    if( is_front_page() ) {
    	$og_tags['og:image'] = get_bloginfo('url') . '/wp-content/uploads/2012/10/handstand-logo.png';
    //	$og_tags['og:image'] = get_stylesheet_directory_uri() . '/images/HND-Responsive-Layout.jpg';
    }
    
    // custom image for Noise page
    if( is_page('Noise') ) {
    	$og_tags['og:image'] = get_stylesheet_directory_uri() . '/images/noise-page.jpg';
    }
    
    // default to display image for store items
    if( hnd_is_item() && hnd_has_display_image() ) {
	    $og_tags['og:image:alt'] = $og_tags['og:image'];
	    $og_tags['og:image'] = hnd_get_display_image_src( null, 'large' );
	    $og_tags['twitter:image'] = $og_tags['og:image'];
	}
    
    // logic for blank description
    if( hnd_is_item() && ( trim( $og_tags['og:description'] ) == '' ) ) {
    	$og_tags['og:description'] .= hnd_get_open_graph_description();
    	$og_tags['twitter:description'] .= hnd_get_open_graph_description();
	}
    
    // twitter cards
	if( hnd_is_item() ) {
		$og_tags['twitter:title'] = get_the_title();
		
		$og_tags['twitter:label1'] = 'Brand';
		$og_tags['twitter:data1'] = 'Handstand Records';
		
		#$og_tags['twitter:label2'] = 'Price';
		#$og_tags['twitter:data2'] = '$ 99.00 USD';
    }
    
    $og_tags['twitter:card'] = 'summary';
	$og_tags['twitter:site'] = '@HandstandRecord';
	$og_tags['twitter:creator'] = '@HandstandRecord';
	
	return $og_tags;
}
add_filter( 'jetpack_open_graph_tags', 'hnd_open_graph_tags', 11 );

// generate open graph description text if none is set
function hnd_get_open_graph_description( $post_id = null ) {

	global $post;
	$post_id = ( $post_id ) ? $post_id : $post->ID;

	$description = '';

	return $description;	
}

/* Item Meta
-------------------------------------------------------------- */



/*
function hnd_item_meta_inventory_admin_panel() {
	add_meta_box(
		'hnd_inventory_panel',
		__('Inventory & Price'),
		'hnd_item_meta_inventory_panel',
		'items',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'hnd_item_meta_inventory_admin_panel', 10 );
add_action( 'admin_init', 'hnd_item_meta_inventory_admin_panel', 10 );

function hnd_item_meta_inventory_panel() {

	global $post;
	
	// nonce field for sigfile checkbox
	wp_nonce_field( basename( __FILE__ ), 'item_meta_inventory_nonce' );

	// get post meta
	$stock = get_post_meta( $post->ID, 'qty', true );

	// markup
	echo '<input type="text" name="qty" id="qty" value="' . $stock . '">';
	
	?>
	<label for="wporg_field">Description for this field</label>
    <select name="wporg_field" id="wporg_field" class="postbox">
        <option value="">Select something...</option>
        <option value="something">Something</option>
        <option value="else">Else</option>
    </select>
    
    <?php
}

// generic save function for all item meta fields
function hnd_item_meta_save_post( $post_id ) {

	// verify the nonce before proceeding
	if ( !isset( $_POST['item_meta_inventory_nonce'] ) || !wp_verify_nonce( $_POST['item_meta_inventory_nonce'], basename( __FILE__ ) ) )
		return $post_id;
	
	// check autosave
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

    # TODO: make this work!!!
    
    // save/update the meta field in the database
	$stock = ( !empty( $_POST['qty'] ) ) ? $_POST['qty'] : 0;
	update_post_meta( $post_id, 'qty', $stock );

}
add_action( 'save_post', 'hnd_item_meta_save_post' );

*/


/** 
* Add my metaboxes for the Books pod 
* 
* $type (string)  
*   The type of object (post_type, taxonomy, media, user, or comment) 
* 
* $name (string) 
*   The name of the object (pod name, post type, taxonomy name,  
*   media, user, or comment) 
*/ 
function hnd_items_pods_metaboxes ( $type, $name ) { 
    
    pods_group_add( array( 'items' ), 'External IDs', 'bandcamp_release_id,discogs_release_id' ); 
	pods_group_add( array( 'items' ), 'Inventory', 'stock,price,sale_price,wholesale_cost' ); 
	pods_group_add( array( 'items' ), 'Details', 'serial,pressing_color,pressing_qty' );
	pods_group_add( array( 'items' ), 'Track List', 'track_list' ); 

} 

// hook into Pods Metaboxes 
add_action( 'pods_meta_groups', 'hnd_items_pods_metaboxes', 10, 2 );


/* Label Mapping
-------------------------------------------------------------- */

// create new Label taxonomy when new Label contact is added
function hnd_new_label_taxonomy_term( $post_id ) {

	// only run for Label contacts
	if( !hnd_is_label( $post_id ) )
		return;

	// dont run if Label contact is deleted
	if( get_post_status( $post_id ) == 'trash' )
		return;

	// check if already mapped to taxonomy term
	$label_map = get_post_meta( $post_id, 'map' );
	$label_map = $label_map[0];

	$post = get_post( $post_id );
	$title = $post->post_title;
	$slug = $post->post_name;

	if( $label_map ) {

		// term already exists, update it
		wp_update_term( $label_map['term_id'], 'labels', array(
			'name' => $title,
			'slug' => $slug
		) );

	} else {

		// create new label taxonomy term
		wp_insert_term( $title, 'labels' );

		// get the term we just created
		$label_term = get_term_by( 'name', $title, 'labels' );
		
		// map this Label contact to taxonomy term
		if( $label_term ) {
			update_post_meta( $post_id, 'map', $label_term->term_id );
		}

	}	
}
add_action( 'wp_insert_post', 'hnd_new_label_taxonomy_term', 99 );

// create new Label post type when new Label taxonomy term is added
function hnd_new_label_post_type( $term_id, $tt_id, $taxonomy ) {

	if( $taxonomy !== 'labels' )
		return;

	$label_term = get_term_by( 'id', $term_id, 'labels' );

	if( $label_term ) {

		// check against slug to see if Label contact already exists
		$label_exists = get_posts( array(
			'name' => $label_term->slug,
			'post_type' => 'items',
			'numberposts' => 1
		) );

		if( $label_exists ) {

			// create the Label contact
			$label_id = wp_insert_post( array(
				'post_title' => $label_term->name,
				'post_type' => 'contacts',
				'post_status' => 'publish',
			) );

			// update Label meta (map, contact_types)
			if( $label_id !== 0 ) {
				update_post_meta( $label_id, 'map', $term_id );
				wp_set_post_terms( $label_id, '382', 'contact_types' );
			}
		}
	}
}
add_action( 'create_term', 'hnd_new_label_post_type', 10, 3 );

/* Admin Menu
-------------------------------------------------------------- */

function hnd_get_admin_submenu_pages() {

	// config for submenu pages
	$submenu_pages = array(
		'Bulk Add',
		'Bulk Edit',
		'Discogs',
		'Download Code Generator',
		//'Financial',
		'Item Cleanup',
		'Label Mapping',
		'Message Board Code',
		'Migrate Content',
		'Trade List',
	);

	return $submenu_pages;
}


function hnd_admin_menu() {
	
	// top level admin menu
	add_menu_page( 'Handstand Admin', 'Handstand', 'manage_options', 'handstand-admin', 'hnd_admin_menu_function', get_stylesheet_directory_uri() . '/images/admin-menu-icon-handstand.png' );
	
	// get submenu pages
	// need to create individual functions below
	$submenu_pages = hnd_get_admin_submenu_pages();
	
	// create submenu pages
	foreach( $submenu_pages as $submenu ) {
		
		// create slug name
		$submenu_slug = strtolower( str_replace( ' ', '-', $submenu ) );
		$function_slug = strtolower( str_replace( ' ', '_', $submenu ) );
		
		// create submenu page
		add_submenu_page( 'handstand-admin', $submenu, $submenu, 'manage_options', 'handstand-admin-' . $submenu_slug, 'hnd_admin_' . $function_slug . '_function' );

	}
}
add_action( 'admin_menu', 'hnd_admin_menu' );

/* Handstand Admin Menu Page
-------------------------------------------------------------- */

function hnd_admin_menu_function() {
	
	// check that the user has the required capability
	if( !current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	
	// display settings edit screen
	?><div class="wrap">
		
		<h2 class="page-title">Handstand Admin Page</h2>

		<?php

		$submenu_pages = hnd_get_admin_submenu_pages();

		if( count( $submenu_pages ) ) {
			echo '<ul>';
			foreach( $submenu_pages as $submenu ) {
				$slug = hnd_slugify( $submenu );
				echo '<li><a href="' . get_admin_url() . 'admin.php?page=handstand-admin-' . $slug . '">' . $submenu . ' </a></li>';

			}
			echo '</ul>';
		}

		?>
		
	</div><?php
}

/* Bulk Add Menu Page
-------------------------------------------------------------- */

function hnd_admin_bulk_add_function() {
	
	// check that the user has the required capability 
    if( !current_user_can( 'manage_options' ) )
    	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    
    // if user has posted some information, set hidden field to 'Y'
    if( isset( $_POST[ HND_ADMIN_HIDDEN_FIELD ] ) && ( $_POST[ HND_ADMIN_HIDDEN_FIELD ] == 'Y' ) ) {
    	
    	// get attached label id
		$label_id = $_POST['label_id'];
		echo '<H4>LABEL ID = ' . $label_id . '</H4>';
		
		// get the text content
		$bulk_item_list = trim( $_POST['bulk_item_list'] );
		
		// create line items
		$bulk_item_lines = explode( PHP_EOL, $_POST['bulk_item_list'] );
		
		#print_r( $bulk_item_lines );
			
		// loop through trade items and add to inventory
		foreach( $bulk_item_lines as $item ) :
		
			// trim extra whitespace by default	
			$item = trim( $item );
			
			// ignore empty lines
			if( empty( $item ) ) continue;
			
			echo '<hr>';
			echo $item;
			
			$item_parts = explode( ' ', $item );
			
			// pre
			echo '<pre>';
			print_r( $item_parts );
			echo '</pre>';
			
			# QUANTITY
			
			// assume space (2 x)
			if( is_numeric( $item_parts[0] ) && ( $item_parts[1] == 'x' ) ) {
				$qty = (int) $item_parts[0];
				unset( $item_parts[1] );
			// no space (2x)
			} elseif( is_numeric( substr( $item_parts[0], 0, -1 ) ) && ( substr( $item_parts[0], -1 ) == 'x' ) ) {
				$qty = (int) substr( $item_parts[0], 0, -1 );
			} else {
				$qty = 1;
			}
			
			$item_parts[0] = $qty;
			
			// post
			echo '<pre>';
			print_r( $item_parts );
			echo '</pre>';
			
			# COST
			
			
			
			/*
			
			// determine format
			#$format = hnd_get_line_item_format( $item );
			 
			// determine title (strip qty)
			#$title = hnd_get_line_item_title( $item );
			
			*/
			
			
			/*
			
			# DISABLE THIS WHILE TESTING
			
			// add item
			$item_id = wp_insert_post( array(
				'post_type'      => 'items',
				'post_status'    => 'draft',
				'post_title'     =>  strip_tags( $title ),
			) );
	
			if( $item_id ) {
	
				# TODO
				
				// set formats
				#wp_set_post_terms( $item_id, $format, 'formats' );
	
				// set labels
				#wp_set_post_terms( $item_id, $label_slug, 'labels' );
	
				// set meta values
				if( $qty ) update_post_meta( $item_id, 'stock', $qty );
			}
			*/
			
		endforeach; 
			
	}
	
	// display settings edit screen
	?><div class="wrap">
		
		<h2>Bulk Add</h2>
		<p class="help">Add items in text list format (UNDER CONSTRUCTION)</p>
		<hr>
		
		<form name="bulk-add-items" method="post" action="">
			
			<input type="hidden" name="<?php echo HND_ADMIN_HIDDEN_FIELD; ?>" value="Y">
			
			<table class="admin-table">
				<tr>
					<td>
						<label for="item">Label / Distributor?</label>
					</td>
					<td>
						<select name="label_id" id="label_id">
							<option>Select Label</option>
							<?php
							
							$labels = get_terms( 'labels', array( 'hide_empty' => false ) );
							if( !empty( $labels ) && !is_wp_error( $labels ) ) {
							    foreach ( $labels as $label ) {
							        echo '<option value="' . $label->term_id . '">' . $label->name . '</option>';
							    }
							}
							
							?>
						</select>&nbsp;
						<a class="small" href="<?php echo admin_url( 'edit-tags.php?taxonomy=labels&post_type=items' ); ?>" target="_blank">Add Label</a>
					</td>
				</tr>
				<tr>
					<td>
						<label for="bulk_item_list">Bulk Item List</label>
						<br><br>
						<b>Example:</b><br>
						2 x Artist - Title LP (Color) $COST
					</td>
					<td>
						<textarea style="width: 100%; height: 200px;" type="text" name="bulk_item_list" id="bulk_item_list" value="<?php echo $bulk_item_list; ?>"></textarea>
					</td>
				</tr>
			</table>
			
			<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Bulk Add' ); ?>" /></p>
			
		</form>
		
	</div><?php	
}

// helper function to extract quantity from item line
function hnd_get_line_item_qty( $item ) {
	
	/*
	$item = trim( $item );
	
	$item_parts = explode( ' ', $item );
		
	// assume space (2 x)
	if( is_numeric( $item_parts[0] ) && ( $item_parts[1] == 'x' ) ) {
		$qty = (int) $item_parts[0];
	} elseif( is_numeric( substr( $item_parts[0], 0, -1 ) ) && ( substr( $item_parts[0], -1 ) == 'x' ) ) {
		$qty = (int) substr( substr( $item_parts[0], 0, -1 ) );
	} else {
		$qty = 1;
	}

	return $qty;
	*/
}

// helper function to extract title from line item
function hnd_get_line_item_title( $item ) {
	
	/*
	$title = trim( $item );

	// get qty to strip
	$qty = hnd_get_line_item_qty( $item );
	
	// strip qty from title
	if( $qty ) $title = str_replace( "$qty x ", '', $title );
	
	$title = trim( $title );

	return $title;
	
	*/
}

/* Bulk Edit Menu Page
-------------------------------------------------------------- */

function hnd_admin_bulk_edit_function() {
	
	// check that the user has the required capability 
    if( !current_user_can( 'manage_options' ) )
    	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    
    // display settings edit screen
	?><div class="wrap">
		
		<h2>Bulk Edit</h2>
		<p class="help">This page allows you to bulk edit item meta fields (stock, price, etc.)</p>
		
		<?php

		##########
		# FILTER #
		##########

		// nav filter options
		hnd_admin_nav_filter();

		// get the items
		$items = hnd_admin_get_items();

		##########
		# UPDATE #
		##########
		
		if( isset( $_POST[ HND_ADMIN_HIDDEN_FIELD ] ) && ( $_POST[ HND_ADMIN_HIDDEN_FIELD ] == 'Y' ) && ( $_POST['admin-action'] == 'bulk-edit-update' ) ) {
    
			if( isset( $_POST['update_items'] ) ) {
				
				$update_items = $_POST['update_items'];

				echo '<h3 style="color:green">Updating ' . count( $update_items ) . ' Items</h3>';
				
				foreach( $update_items as $item ) {
					foreach( $item as $key => $value ) {
						
						// ignore id field
						if( $key == 'id')
							continue;

						// update post meta
						update_post_meta( $item['id'], $key, $value );
					}					
				}
			}	
		}

		###########
		# DISPLAY #
		###########
			
		if( isset( $items ) && $items->have_posts() ) {
		
			?>

			<form id="bulk-edit-update" name="bulk-edit-update" method="post" action="">
				
				<input type="hidden" name="admin-action" value="bulk-edit-update">
				<input type="hidden" name="<?php echo HND_ADMIN_HIDDEN_FIELD; ?>" value="Y">
			
				<?php
				
					echo '<h3>' . $items->found_posts . ' Items</h3>';
					
					// table display
					echo '<table class="tablesorter">';
					echo '<thead>';	
					echo '<tr>';	
					echo '	<th>Title</th>';
					echo '	<th>Stock</th>';
					echo '	<th>Price</th>';
					echo '	<th>Sale Price</th>';
					echo '	<th>Wholesale Price</th>';
					echo '</tr>';
					echo '</thead>';	
					
					echo '<tbody>';	
					
					$i = 0;

					while( $items->have_posts() ) : $items->the_post();
						
						global $post;
						
						// get item meta
						$id = get_the_ID();
						$title = $post->post_title;
						$stock = ( get_post_meta( $id, 'stock', true ) ) ? get_post_meta( $id, 'stock', true ) : 0;
						$price = ( get_post_meta( $id, 'price', true ) ) ? get_post_meta( $id, 'price', true ) : 0;
						$sale_price = ( get_post_meta( $id, 'sale_price', true ) ) ? get_post_meta( $id, 'sale_price', true ) : 0;
						$wholesale_price = ( get_post_meta( $id, 'wholesale_price', true ) ) ? get_post_meta( $id, 'wholesale_price', true ) : 0;
						
						?>

						<tr>	
							<td width="50%">
								<div class="hide"><?php echo $title; ?></div>
								<a href="<?php echo get_edit_post_link(); ?>" target="_blank"><?php echo $title; ?></a>
							</td>
								<input type="hidden" name="update_items[<?php echo $i; ?>][id]" value="<?php echo $id; ?>">
							<td>
								<div class="hide"><?php echo $stock; ?></div>
								<input type="text" style="width:50px; margin-right: 10px;" name="update_items[<?php echo $i; ?>][stock]" value="<?php echo $stock; ?>">
							</td>
							<td>
								<div class="hide"><?php echo $price; ?></div>
								$<input type="text" style="width:50px; margin-right: 10px;" name="update_items[<?php echo $i; ?>][price]" value="<?php echo $price; ?>">
							</td>
							<td>
								<div class="hide"><?php echo $sale_price; ?></div>
								$<input type="text" style="width:50px; margin-right: 10px;" name="update_items[<?php echo $i; ?>][sale_price]" value="<?php echo $sale_price; ?>">
							</td>
							<td>
								<div class="hide"><?php echo $wholesale_price; ?></div>
								$<input type="text" style="width:50px; margin-right: 10px;" name="update_items[<?php echo $i; ?>][wholesale_price]" value="<?php echo $wholesale_price; ?>">
							</td>
						</tr>

						<?php
						$i++;
					endwhile;

					echo '</tbody>';				
					echo '</table>';				
				
				?>

				<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Update' ); ?>" /></p>

			</form>

			<?php 

		} else {

			echo '<h3>No Items Found</h3>';
		
		}

		wp_reset_query();

		?>
		
	</div><?php	
}

/* Discogs Menu Page
-------------------------------------------------------------- */

function hnd_admin_discogs_function() {
	
	// check that the user has the required capability 
    if( !current_user_can( 'manage_options' ) )
    	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    
 	wp_register_style( 'jquery-easytabs-css', get_stylesheet_directory_uri() . '/css/easytabs.css' );
    wp_enqueue_style( 'jquery-easytabs-css' );
    wp_enqueue_script( 'jquery-easytabs', get_stylesheet_directory_uri() . '/js/jquery.easytabs.js', array( 'jquery' ), null, false );
	wp_enqueue_script( 'tabs', get_stylesheet_directory_uri() . '/js/tabs.js', array( 'jquery', 'jquery-easytabs' ), null, false );
	
    // display settings edit screen
	?><div class="wrap">
		
		<h2>Discogs <span style="color:red">(UNDER CONSTRUCTION)</span></h2>
		<p class="help">This page interfaces with Discogs API.</p>

		<?php

		#require_once( dirname( __FILE__ ) . '/api/discogs-oauth.php' );

		# TESTING 
		#print_r( hnd_discogs_get_response( 'http://api.discogs.com/marketplace/orders' ) );


		?>
		
		<div id="tab-container" class="tab-container">
			
			<ul class="etabs">
				<li class="tab"><a href="#main">Main</a></li>
				<li class="tab"><a href="#inventory">Inventory</a></li>
				<li class="tab"><a href="#orders">Orders</a></li>
				<li class="tab"><a href="#search">Search</a></li>
				<li class="tab"><a href="#sync">Sync</a></li>
			</ul>

			<div id="main" class="tab-section">
				<h2>Main Settings</h2>
				<?php



				?>

			</div>

			<div id="inventory" class="tab-section">
				<?php

				
				##########
				# FILTER #
				##########

				// nav filter options
				#hnd_admin_nav_filter();
				/*
				// get Discogs inventory listing
				$listings = hnd_discogs_get_inventory_listings();

				if( $listings ) {

					echo '<h3>' . count( $listings ). ' Listings</h3>';

					// table display
					echo '<table class="tablesorter">';
					echo '<thead>';	
					echo '<tr>';	
					echo '	<th>Title</th>';
					echo '	<th>Price</th>';
					echo '	<th>Release ID</th>';
					echo '	<th>Listing ID</th>';
					//echo '	<th>External ID/th>';
					echo '</tr>';
					echo '</thead>';
					echo '<tbody>';	

					foreach( $listings as $item ) {

						$title = $item->release->description;
						$price = number_format( $item->price->value, 2 );
						
						$release_url = $item->release->resource_url;
						$listing_url = $item->resource_url;
						
						$release_id = $item->release->id;
						$listing_id = $item->id;
						
						// need to be authenticated
						//$external_id
						
						?>
						<tr>	
							<td width="50%">
								<div>
									<strong><?php echo $title; ?></strong>
								</div>
								<div class="small">
									<a href="<?php echo $release_url; ?>" target="_blank">RELEASE</a> | <a href="<?php echo $listing_url; ?>" target="_blank">LISTING</a>
								</div>
							</td>
							<td><?php echo '$' . $price; ?></td>
							<td><?php echo $release_id; ?></td>
							<td><?php echo $listing_id; ?></td>
							<!-- <td></td> -->
						</tr>
						<?php

					}

					echo '</tbody>';				
					echo '</table>';				
						
				}

				echo '<pre>';

				#echo print_r( hnd_discogs_get_inventory_ids(), true );
				#echo print_r( hnd_discogs_get_response( DISCOGS_INVENTORY_URL ), true );

				echo '</pre>';

				###########
				# DISPLAY #
				###########
					
				/*
				if( isset( $items ) && $items->have_posts() ) {
				
					?>

					<form id="discogs-update" name="discogs-update" method="post" action="">
						
						<input type="hidden" name="admin-action" value="discogs-update">
						<input type="hidden" name="<?php echo HND_ADMIN_HIDDEN_FIELD; ?>" value="Y">
					
						<?php
						
							echo '<h3>' . $items->found_posts . ' Items</h3>';
							
							// table display
							echo '<table class="tablesorter">';
							echo '<thead>';	
							echo '<tr>';	
							echo '	<th>Title</th>';
							echo '	<th>Stock</th>';
							echo '	<th>Price</th>';
							echo '	<th>Sale Price</th>';
							echo '	<th>Discogs</th>';
							echo '</tr>';
							echo '</thead>';	
							
							echo '<tbody>';	
							
							$i = 0;

							while( $items->have_posts() ) : $items->the_post();
								
								global $post;
								
								// get item meta
								$id = get_the_ID();
								$title = $post->post_title;
								$stock = ( get_post_meta( $id, 'stock', true ) ) ? get_post_meta( $id, 'stock', true ) : 0;
								$price = ( get_post_meta( $id, 'price', true ) ) ? get_post_meta( $id, 'price', true ) : 0;
								$sale_price = ( get_post_meta( $id, 'sale_price', true ) ) ? get_post_meta( $id, 'sale_price', true ) : 0;
								
								?>

								<tr>	
									<td width="50%">
										<div class="hide"><?php echo $title; ?></div>
										<a href="<?php echo get_edit_post_link(); ?>" target="_blank"><?php echo $title; ?></a>
									</td>
										<input type="hidden" name="update_items[<?php echo $i; ?>][id]" value="<?php echo $id; ?>">
									<td>
										<div class="hide"><?php echo $stock; ?></div>
										<input type="text" style="width:50px; margin-right: 10px;" name="update_items[<?php echo $i; ?>][stock]" value="<?php echo $stock; ?>">
									</td>
									<td>
										<div class="hide"><?php echo $price; ?></div>
										$<input type="text" style="width:50px; margin-right: 10px;" name="update_items[<?php echo $i; ?>][price]" value="<?php echo $price; ?>">
									</td>
									<td>
										<div class="hide"><?php echo $sale_price; ?></div>
										$<input type="text" style="width:50px; margin-right: 10px;" name="update_items[<?php echo $i; ?>][sale_price]" value="<?php echo $sale_price; ?>">
									</td>
									<td>
										<?php //print_r( hnd_discogs_search( $title ) );?>
									</td>
								</tr>

								<?php
								$i++;
							endwhile;

							echo '</tbody>';				
							echo '</table>';				
						
						?>

						<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Update' ); ?>" /></p>

					</form>

					<?php 

				} else {

					echo '<h3>No Items Found</h3>';
				
				}

				wp_reset_query();
				
				*/
			
				?>
			</div>

			<div id="orders" class="tab-section">
				orders go here
			</div>

			<div id="search" class="tab-section">

				<?php

				// retain query string
				$query = ( isset( $_POST['query'] ) ) ? $_POST['query'] : '';
				
				?>
				<form id="discogs-search" name="discogs-search" method="post" action="">
					<input type="hidden" name="admin-action" value="discogs-search">
					<input type="hidden" name="<?php echo HND_ADMIN_HIDDEN_FIELD; ?>" value="Y">
					<input type="text" name="query" value="<?php echo $query; ?>">
					<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Search' ); ?>" style="margin-left:10px" />
				</form>

				<?php
				
				/*
				
				// search action
				if( isset( $_POST[ HND_ADMIN_HIDDEN_FIELD ] ) && ( $_POST[ HND_ADMIN_HIDDEN_FIELD ] == 'Y' ) && ( $_POST['query'] ) ) {
					
					echo '<h3>Results for "' . $query . '"</h3>';
					echo '<pre>' . print_r( hnd_discogs_search( $query ), true ) . '</pre>';
				
				}

				*/

				?>

			</div>

			<div id="sync" class="tab-section">
				<p>Some day we'll be able to sync Discogs and WP items.</p>
			</div>

		</div><!--/#tab-container-->

	</div><?php	
}

/* Download Code Generator Menu Page
-------------------------------------------------------------- */

function hnd_admin_download_code_generator_function() {
	
	// check that the user has the required capability 
    if( !current_user_can( 'manage_options' ) )
    	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    
    // configure some vars for validation
    $is_valid_item = false;
    $is_valid_number = false;
    
    // if user has posted some information, set hidden field to 'Y'
    if( isset( $_POST[ HND_ADMIN_HIDDEN_FIELD ] ) && ( $_POST[ HND_ADMIN_HIDDEN_FIELD ] == 'Y' ) ) {
    	
    	// make sure an item is selected
    	if( intval( $_POST['item'] ) > 0 ) {
    		$is_valid_item = true;
    		$item = $_POST['item'];
    	} else {
    		$is_valid_item = false;
    		$item = 0;
    		echo '<div class="error">Please select an item</div>';
    	}
    	
    	// make sure number of codes is a valid integer
    	if( intval( $_POST['number_of_codes'] ) > 0 ) {
    		$is_valid_number = true;    		
    		$number_of_codes = $_POST['number_of_codes'];
    	} else {
    		$is_valid_number = false;
    		$number_of_codes = 0;
    		echo '<div class="error">Please enter a valid number of codes</div>';
    	}
		
		// add update message
        if( $is_valid_item && $is_valid_number ) {
        	
        	$c = 0;
        	
        	?><div class="updated">
			
				<div style="float: right; margin: 1em"><?php hnd_featured_image( $item ); ?></div>				
				<p><strong><?php echo $number_of_codes; ?> Codes Generated for <?php echo get_the_title( $item ); ?></strong></p>
				
				<?php
				
				$new_codes = array();
				
				for( $i = 1; $i <= $number_of_codes; $i++ ) {
				
					// generate 10-digit random number
					$new_code = substr( md5( rand( 1,999999 ) ), 0, 10 );
					
					// check against existing codes
					if( !term_exists( $new_code, 'download_codes' ) ) {
					
						// add code to db
						wp_insert_term( $new_code, 'download_codes' );
						
						$new_codes[] = $new_code;
						
						echo '<div>' . $new_code . '</div>';
					
					}					
					
				}
				
				// attach codes to download
				wp_set_post_terms( $item, $new_codes, 'download_codes', true );
				
				?>
				
			</div><?php
		}		
	}
	
	// display settings edit screen
	?><div class="wrap">
		
		<h2>Download Code Generator</h2>
		<p class="help">This page allows you to generate a defined number of download codes for a particular item.</p>
		<hr>

		<form name="download-code-generator" method="post" action="">
			
			<input type="hidden" name="<?php echo HND_ADMIN_HIDDEN_FIELD; ?>" value="Y">
			
			<table class="admin-table">
				<tr>
					<td>
						<label for="item">Select a download item</label>
					</td>
					<td>
						<select name="item" id="item">
							<option>Select Download Item</option>
							<?php
								
								$downloads = new WP_Query( array(
									'post_type' => 'downloads',
									'order' => 'DESC',
								) );
								
								if( $downloads->have_posts() ) :
									while( $downloads->have_posts() ) : $downloads->the_post();
										echo '<option value="' . get_the_ID() . '" ';
										if( $item == get_the_ID() ) echo ' selected="selected"';
										echo '>' . get_the_title() . '</option>';
									endwhile;						
								endif;
									
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<label for="number_of_codes">Number of download codes to create</label>
					</td>
					<td>
						<input type="text" name="number_of_codes" id="number_of_codes" value="<?php echo $number_of_codes; ?>">
					</td>
				</tr>
			</table>
			
			<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Generate Codes' ); ?>" /></p>
			
		</form>
	
	</div><?php
}

/* Item Cleanup Menu Page
-------------------------------------------------------------- */

function hnd_admin_item_cleanup_function() {
	
	// check that the user has the required capability 
    if( !current_user_can( 'manage_options' ) )
    	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    
    // display settings edit screen
	?><div class="wrap">
		
		<h2>Item Cleanup</h2>
		<p class="help">This page shows you inconsistencies in item meta data values.</p>
		<hr>

		<?php
			
		$items = new WP_Query( array(
			'post_status' => array('publish'),
			'post_type' => 'items',
			//'order' => 'ASC',
			//'orderby' => 'title',
			'posts_per_page' => -1,
		) );
		
		if( isset( $items ) && $items->have_posts() ) {
	
			echo '<h3>' . $items->found_posts . ' Items</h3>';
			echo '<table>';
			
			while( $items->have_posts() ) : $items->the_post();
				
				global $post;
				
				// get item meta
				$title = $post->post_title;
				$slug = $post->post_name;
				$excerpt = $post->post_excerpt;
				$format = get_the_term_list( get_the_ID(), 'formats', '', ', ' );
				$color = get_post_meta( get_the_ID(), 'pressing_color', true );
				$track_list = get_post_meta( get_the_ID(), 'track_list', true );
				
				// check if item needs to be edited
				$edit = false;
				$flags = array();
				
				// check if slug ends in a number
				if( is_numeric( end( explode( '-', $slug ) ) ) ) {
					$edit = true;
					$flags[] = 'Slug Number';
				}
				
				// check is excerpt is set
				if( empty( $excerpt ) ) {
					#$edit = true;
					#$flags[] = 'Excerpt';
				} 
				
				// check if vinyl has 'inch' in slug
				if( strpos( $format, '"' !== false ) && ( strpos( $slug, 'inch' ) == false ) ) {
					$edit = true;
					$flags[] = 'Slug Inch';
				}
				
				// check if vinyl color is written properly
				if( empty( $color ) ) {
				
					$edit = true;
					$flags[] = 'Vinyl Color';
					
				} else {
					
					# TODO
					/*
					// check tapes
					if( in_array( 'Cassette Tape', $format ) ) {
						$edit = true;
						$flags[] = 'Cassette tape!!!!';
						
					}
					*/
					
					// check vinyl
					if( strpos( $color, 'Vinyl' ) == false ) {
						$edit = true;
						$flags[] = 'Vinyl Color';
					}
					
				}
				
				// check if track list contains an ordered list
				if( !empty( $track_list ) && ( strpos( $track_list, '<li>' ) ) == false ) {
					$edit = true;
					$flags[] = 'Track List Order';
				}
				
				// check if track list contains an unordered list
				if( !empty( $track_list ) && ( strpos( $track_list, '<ul>' ) ) !== false ) {
					$edit = true;
					$flags[] = 'Unordered Track List';
				}
				
				// check if track list contains multiple ordered lists
				$num_matches = 0;
				$num_matches = preg_match_all( "~\<ol\>~", $track_list, $matches );
				if( $num_matches > 1 ) {
					$edit = true;
					$flags[] = 'Multiple &lt;ol&gt; Tags';
				}
				
				# TODO: featured image size
				
				// output
				echo '<tr';
				if( $edit ) echo ' style="background:yellow"';
				echo '>';	
				echo '	<td><a href="' . get_edit_post_link() . '" target="_blank">' . $title . '</a></td>';
				echo '	<td>' . $post->post_name . '</td>';
				echo '	<td>' . $post->post_excerpt . '</td>';
				echo '	<td>' . $format . '</td>';
				echo '	<td>' . implode( ' / ', $flags ) . '</td>';
				echo '</tr>';	

			endwhile;
			
			echo '</table>';
		}
		
		wp_reset_query();
		
		?>
		
	</div><?php	
}

/* Label Mapping Menu Page
-------------------------------------------------------------- */

function hnd_admin_label_mapping_function() {
	
	// check that the user has the required capability 
    if( !current_user_can( 'manage_options' ) )
    	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    
    // manual settings
	$convert = false;
	$update = false;

	// display settings edit screen
	?><div class="wrap">
		
		<h2>Label Mapping</h2>
		<p class="help">Labels exist as the post type "contacts" as well as a taxonomy "labels".</p>
		<hr>

		<div style="float: left; width: 50%;"><?php
			
			$convert_count = 0;
			$update_count = 0;
			
			$unmapped_posts = array();

			// post type
			$labels = new WP_Query( array(
				'post_type' => 'contacts',
				'order' => 'ASC',
				'orderby' => 'title',
				'posts_per_page' => -1,
				'tax_query' => array(
					array(
						'taxonomy' => 'contact_types',
						'field' => 'slug',
						'terms' => 'label'
					)
				)
			) );
			
			if( isset( $labels ) && $labels->have_posts() ) {
				echo '<h3>' . $labels->found_posts . ' Labels (Post Type)</h3>';
				echo '<textarea style="width: 75%; height: 200px;">';
				while( $labels->have_posts() ) : $labels->the_post();
					
					global $post;

					$id = get_the_ID();
					$title = get_the_title();
					
					echo $title . "\n";
					
					$label_term = get_term_by( 'name', $title, 'labels' );
					
					// check if label post is mapped
					if( !term_exists( $title, 'labels' ) ) {
						$unmapped_posts[] = $title;
					}

					if( $convert && !term_exists( $title, 'labels' ) ) {
					
						// create the term
						wp_insert_term( $title, 'labels' );
						
						// increase the counter
						$convert_count++;
					
					}
					
					// map label post type to taxonomy
					if( $update ) {
						$slug = $post->post_name;
						$label_slug = get_term_by( 'slug', $slug, 'labels' );
						$label_term_id = $label_slug->term_id;
						if( $label_term_id ) {
							update_post_meta( $id, 'map', $label_term_id );
							$update_count++;
						} else {
							echo '++++';
						}
					}

				endwhile;
				echo '</textarea>';

				echo '<br><h4>Unmapped Posts</h4>';
				print_r( $unmapped_posts );
			}
			
			wp_reset_query();
		
		?></div>
		
		<div style="float: right; width: 50%;"><?php

			$unmapped_terms = array();

			// taxonomy
			$terms = get_terms( 'labels', array( 'hide_empty' => false ) );	
			if( $terms ) {
				echo '<h3>' . count( $terms ) . ' Labels (Taxonomy)</h3>';
				echo '<textarea style="width: 75%; height: 200px;">';
				foreach( $terms as $term ) {
					
					echo $term->name;
					echo ' - ' . $term->term_id;
					echo "\n";
					
					// get post type based on slug
					$my_posts = get_posts( array(
						'name' => $term->slug,
						'post_type' => 'contacts',
						'numberposts' => 1
					) );
					
					if( $my_posts ) {
						
						/*
						// fix fuckup
						wp_update_post( array(
							'ID'           => $my_posts[0]->ID,
							'post_title' => $term->name
						) );
						echo ' - ' . $my_posts[0]->ID . ' - updated' . "\n";
						*/
					} else {
						$unmapped_terms[] = $term->name;
					}									
				}
				echo '</textarea>';

				echo '<br><h4>Unmapped Terms</h4>';
				print_r( $unmapped_terms );
			}
		
		?></div>
		
		<div class="clear"><br><hr></div>
		
		<?php
		
		if( $convert ) {
			if( count( $convert_count ) > 0 ) {
				echo '<h3 style="color: green">' . $convert_count . ' Labels Converted</h3>';
			} else {
				echo '<h3 style="color: red">All Labels Converted</h3>';
			}
		} else {
			echo '<h3 style="color: gray">Conversion Turned Off</h3>';
		}

		if( $update ) {
			if( count( $update_count ) > 0 ) {
				echo '<h3 style="color: green">' . $update_count . ' Label Mappings Updated</h3>';
			} else {
				echo '<h3 style="color: red">All Labels Mapped</h3>';
			}
		} else {
			echo '<h3 style="color: gray">Mapping Update Turned Off</h3>';
		}
			
		?>

	</div><?php	
}

/* Message Board Code Menu Page
-------------------------------------------------------------- */

function hnd_admin_message_board_code_function() {
	
	// check that the user has the required capability 
    if( !current_user_can( 'manage_options' ) )
    	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    
    wp_register_style( 'jquery-easytabs-css', get_stylesheet_directory_uri() . '/css/easytabs.css' );
    wp_enqueue_style( 'jquery-easytabs-css' );
    wp_enqueue_script( 'jquery-easytabs', get_stylesheet_directory_uri() . '/js/jquery.easytabs.js', array( 'jquery' ), null, false );
	wp_enqueue_script( 'tabs', get_stylesheet_directory_uri() . '/js/tabs.js', array( 'jquery', 'jquery-easytabs' ), null, false );
	
    // display settings edit screen
	?><div class="wrap">
		
		<h2>Message Board Code</h2>
		<p class="help">This page generates markup for message board posting for easy copy-and-paste.</p>
		<hr>

		<?php

		// post type
		$boards = new WP_Query( array(
			'post_type' => 'contacts',
			'order' => 'DESC',
			'orderby' => 'title',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'contact_types',
					'field' => 'slug',
					'terms' => 'message-board'
				)
			)
		) );
		
		if( isset( $boards ) && $boards->have_posts() ) {
			
			echo '<h3>' . $boards->found_posts . ' Message Boards</h3>';
			
			?>
			<div id="tab-container" class="tab-container">
				<ul class="etabs"><?php
					
					// tabs
					while( $boards->have_posts() ) : $boards->the_post();
						echo '<li class="tab"><a href="#' . hnd_slugify( get_the_title() ) . '">' . get_the_title() . '</a></li>';
					endwhile;

				?></ul>
				
				<?php

				// tab sections
				while( $boards->have_posts() ) : $boards->the_post();

					// get contact meta
					$id = get_the_ID();
					$board_title = get_the_title();
					$slug = hnd_slugify( $board_title );
					$content = apply_filters( 'the_content', get_the_content() );
					$board_link = get_post_meta( $id, 'website', true );
					$username = get_post_meta( $id, 'message_board_username', true );
					$format = get_post_meta( $id, 'message_board_format', true );
					$new_thread_link = get_post_meta( $id, 'message_board_new_thread_link', true );
					
					// loop through items and markup
					
					// item textarea
					echo '<div id="' . $slug . '" class="tab-section">';

					echo '<div style="float:right; width:66%;">';
					echo '<textarea style="width:100%; height:200px;">';
					
					// SMALL VINYL
					echo hnd_message_board_list_category( 'SMALL VINYL' );
					foreach( hnd_get_vinyl( 'small' ) as $item ) echo hnd_message_board_list_item( $item['id'], $format );

					// MEDIUM VINYL
					echo hnd_message_board_list_category( 'MEDIUM VINYL' );
					foreach( hnd_get_vinyl( 'medium' ) as $item ) echo hnd_message_board_list_item( $item['id'], $format );

					// LARGE VINYL
					echo hnd_message_board_list_category( 'LARGE VINYL' );
					foreach( hnd_get_vinyl( 'large' ) as $item ) echo hnd_message_board_list_item( $item['id'], $format );

					// CASSETTE TAPES
					echo hnd_message_board_list_category( 'CASSETTE TAPES' );
					foreach( hnd_get_tapes() as $item ) echo hnd_message_board_list_item( $item['id'], $format );

					// CDS
					echo hnd_message_board_list_category( 'COMPACT DISCS' );
					foreach( hnd_get_cds() as $item ) echo hnd_message_board_list_item( $item['id'], $format );

					// MISC
					echo hnd_message_board_list_category( 'MISCELLANEOUS' );
					foreach( hnd_get_misc() as $item ) echo hnd_message_board_list_item( $item['id'], $format );

					echo '	</textarea></div>';

					// message board content
					echo '<h2>' . $board_title . '</h2>';
					echo '<p><strong>USERNAME:</strong> ' . $username . '</p>';
					echo '<p><strong>FORMAT:</strong> ' . $format . '</p>';
					echo '<p><a class="button button-primary" href="' . $new_thread_link . '" target="_blank">Create New Thread</a></p>';
					echo $content;
					echo '<div class="clear"></div>';

					echo '</div><!--/.tab-section-->';
				endwhile;

				?>
			</div><!--/#tab-container-->

			<?php
		
		}

		wp_reset_query();

		?>

	</div><?php	
}

/* Migrate Content Menu Page
-------------------------------------------------------------- */

function hnd_admin_migrate_content_function() {
	
	// check that the user has the required capability 
    if( !current_user_can( 'manage_options' ) )
    	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    
    // on/off switch
	$migrate = false;

	// display settings edit screen
	?><div class="wrap">
		
		<h2>Migrate Content</h2>
		<p class="help">This page allows you to migrate content (post types, taxonomies, etc.)</p>
		<hr>

		<div style="background:#FFF; margin: 1em 0; padding:1em; border-radius:1em"><?php

			##############
			# POST TYPES #
			# ############
			
			// if user has posted some information, set hidden field to 'Y'
			if( isset( $_POST[ HND_ADMIN_HIDDEN_FIELD ] ) && ( $_POST[ HND_ADMIN_HIDDEN_FIELD ] == 'Y' ) ) {

				// get form action
				$action = ( isset( $_POST['admin-action'] ) ) ? $_POST['admin-action'] : false;
				
				switch( $action ) {

					case 'migrate-post-type':

						// check if both fields are filled out and they match
						if( isset( $_POST['migrate-from'] ) && isset( $_POST['migrate-to'] ) && ( $_POST['migrate-from'] == $_POST['migrate-to'] ) ) {

							echo '<h3 style="color:green">Migrating <em>' . $_POST['migrate-from'] . '</em> to <em>' . $_POST['migrate-to'] . '</em>...</h3>';
							$cnt = 0;

							// get posts
							$posts = new WP_Query( array(
								'post_type' => 'post',
								'category_name' => $_POST['migrate-from'],
								'order' => 'DESC',
								'posts_per_page' => -1,
							) );
							
							if( isset( $posts ) && $posts->have_posts() ) {
								while( $posts->have_posts() ) : $posts->the_post();

									$post_data = array();
									
									// get data to migrate
									$post_data['title'] = get_the_title();
									$post_data['excerpt'] = get_the_excerpt();
									$post_data['content'] = get_the_content();
									$post_data['date'] = get_the_date('Y-m-d H:i:s');
									$post_data['post_thumbnail_id'] = get_post_thumbnail_id();

									if( 'events' == $_POST['migrate-to'] ) {

										$post_data['event_venue'] = get_post_meta( get_the_ID(), 'event_venue', false );
										$post_data['event_venue'] = $post_data['event_venue'][0]['ID'];
										//$post_data['event_date'] = get_post_meta( get_the_ID(), 'event_date', true );
										$post_data['event_time'] = get_post_meta( get_the_ID(), 'event_time', true );
										$post_data['event_price'] = get_post_meta( get_the_ID(), 'event_price', true );
										$post_data['event_age_limit'] = get_post_meta( get_the_ID(), 'event_age_limit', true );
										
									} elseif( 'press' == $_POST['migrate-to'] ) {

										$post_data['press_item'] = get_post_meta( get_the_ID(), 'press_item', false );
										$post_data['press_item'] = $post_data['press_item'][0]['ID'];

										$post_data['press_source'] = get_post_meta( get_the_ID(), 'press_source', false );
										$post_data['press_source'] = $post_data['press_source'][0]['ID'];							

									}

									#echo '<pre>';
									#print_r( $post_data );
									#echo '</pre>';

									if( $migrate ) {

										// add post
										$post_id = wp_insert_post( array(
											'post_type'     => $_POST['migrate-to'],
											'post_status'   => 'publish',
											'post_title'    =>  $post_data['title'],
											'post_excerpt'	=> $post_data['excerpt'],
											'post_content'	=> $post_data['content'],
											'post_date'		=> $post_data['date'],
										) );

										if( $post_id ) {

											// update counter
											$cnt++;

											// update featured image
											if( $post_data['post_thumbnail_id'] ) set_post_thumbnail( $post_id, $post_data['post_thumbnail_id'] ); 

											// set meta values
											if( $post_data['event_venue'] ) update_post_meta( $post_id, 'event_venue', $post_data['event_venue'] );
											//if( $post_data['event_date'] ) update_post_meta( $post_id, 'event_date', $post_data['event_date'] );
											if( $post_data['event_time'] ) update_post_meta( $post_id, 'event_time', $post_data['event_time'] );
											if( $post_data['event_price'] && ( $post_data['event_price'] !== '0.00' ) ) update_post_meta( $post_id, 'event_price', $post_data['event_price'] );
											if( $post_data['event_age_limit'] ) update_post_meta( $post_id, 'event_age_limit', $post_data['event_age_limit'] );

											if( $post_data['press_item'] ) update_post_meta( $post_id, 'press_item', $post_data['press_item'] );
											if( $post_data['press_source'] ) update_post_meta( $post_id, 'press_source', $post_data['press_source'] );
											
										}
									}

								endwhile;

								if( $migrate ) {
									echo '<h4>' . $cnt . ' posts migrated</h4>';
									echo '<hr>';
								}		
							}
							
							wp_reset_query();
						}

					break;

					case 'migrate-taxonomy':
						echo 'taxonomy';
					break;

					case 'migrate-custom-field';
						
						// check if both fields are filled out and they match
						if( isset( $_POST['migrate-from-post-type'] ) && isset( $_POST['migrate-from-custom-field'] ) && isset( $_POST['migrate-to-taxonomy'] ) ) {

							#print_r( $_POST );

							echo '<h3 style="color:green">Migrating <em>' . $_POST['migrate-to-taxonomy'] . '</em></h3>';
							$cnt = 0;

							$colors = array();

							// get posts with a color value set
							$items = new WP_Query( array(
								'post_type' => 'items',
								'posts_per_page' => -1,
								'order' => 'DESC',
								'meta_query' => array(
									array(
										'key' => 'pressing_color',
										'compare' => 'EXISTS',
									)		
								),								
							) );
							
							// loop through items
							if( isset( $items ) && $items->have_posts() ) {
								while( $items->have_posts() ) : $items->the_post();
									
									// get color
									$color = get_post_meta( get_the_ID(), 'pressing_color', true );
									
									// add to array if it doesnt already exist
									if( !empty( $color ) && !in_array( $color, $colors ) ) {
										$colors[] = ucwords( $color );
									}

								endwhile;
							}

							sort( $colors );
							print_r( $colors );

							// add the new taxonomy foreachword
							foreach( $colors as $color ) {
								wp_insert_term( $color, 'colors' );
							}
							
						}

					break;
				}				
			}

			// end of processing, begin display

		?></div>

		<h3>Post Types</h3>
		<form name="migrate-post-type" method="post" action="">
			
			<input type="hidden" name="admin-action" value="migrate-post-type">
			<input type="hidden" name="<?php echo HND_ADMIN_HIDDEN_FIELD; ?>" value="Y">

			<div style="display:inline-block; width:auto; vertical-align:top; margin-right:20px;">
				<h4>Migrate From<br>Category</h4>
				<select id="migrate-from" name="migrate-from"><?php

					// get all post categories
					$categories = get_categories();
					
					// loop through and add selectable options
					foreach( $categories as $category ) {
						$selected = ( $category->slug == $_POST['migrate-from'] ) ? 'selected="selected"' : '';
						echo '<option value="' . $category->slug . '" ' . $selected . '>' . $category->name . '</option>';
					}

				?></select>

			</div>

			<div style="display:inline-block; width:auto; vertical-align:top">
				<h4>Migrate To<br>Post Type</h4>
				<select id="migrate-to" name="migrate-to"><?php
					
					// get all post types
					$post_types = get_post_types();
					sort( $post_types );

					// loop through and add selectable options
					foreach( $post_types as $post_type ) {
						$selected = ( $post_type == $_POST['migrate-to'] ) ? 'selected="selected"' : '';
						echo '<option value="' . $post_type . '" ' . $selected . '>' . $post_type . '</option>';
					}

				?></select>
			</div>

			<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Migrate' ); ?>" disabled="disabled" /></p>

		</form>

		<?php

		##############
		# TAXONOMIES #
		# ############
		
		#echo '<hr>';
		#echo '<h3>Taxonomies</h3>';
		#echo '<p>Under Construction</p>';

		?>
		
		<hr>
		<h3>Custom Fields</h3>
		<form name="migrate-custom-field" method="post" action="">
			
			<input type="hidden" name="admin-action" value="migrate-custom-field">
			<input type="hidden" name="<?php echo HND_ADMIN_HIDDEN_FIELD; ?>" value="Y">

			<div style="display:inline-block; width:auto; vertical-align:top; margin-right:20px;">
				<h4>Migrate From<br>Post Type</h4>
				<select id="migrate-from-post-type" name="migrate-from-post-type">
					<option value="items">items</option>
				</select>
			</div>

			<div style="display:inline-block; width:auto; vertical-align:top; margin-right:20px;">
				<h4>Migrate From<br>Custom Field</h4>
				<select id="migrate-from-custom-field" name="migrate-from-custom-field">
					<option value="pressing_color">pressing_color</option>
				</select>
			</div>

			<div style="display:inline-block; width:auto; vertical-align:top">
				<h4>Migrate To<br>Taxonomy</h4>
				<select id="migrate-to-taxonomy" name="migrate-to-taxonomy">
					<option value="colors">colors</option>
				</select>
			</div>

			<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Migrate' ); ?>" disabled="disabled" /></p>

		</form>

	</div><?php	
}


/* Trade List Menu Page
-------------------------------------------------------------- */

function hnd_admin_trade_list_function() {
	
	// check that the user has the required capability 
    if( !current_user_can( 'manage_options' ) )
    	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    
    // display settings edit screen
	?><div class="wrap">
		
		<h2>Trade List</h2>
		<p class="help">This page display a nice trade list for easy copy & paste action.</p>
		
		<?php

		##########
		# FILTER #
		##########

		// nav filter options
		hnd_admin_nav_filter();

		// get tradeable items
		$items = new WP_Query( array(
			'post_type' => 'items',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'order' => 'ASC',
			'meta_key' => 'serial',
			'orderby' => 'meta_value',
			'meta_query'  => array(
				array(
					'key'     => 'stock',
					'value'   => 5,
					'compare' => '>',
					'type' => 'numeric'
				)
			),
			'tax_query' => array(
	            array(
	            	'taxonomy' => 'formats',
	            	'field' => 'slug',
	            	'terms' => array( 'merch' ),
	            	'operator' => 'NOT IN'
	            )
	        )
		) );

		
		###########
		# DISPLAY #
		###########
			
		if( isset( $items ) && $items->have_posts() ) {
		
			?>

			<form id="bulk-edit-update" name="bulk-edit-update" method="post" action="">
				
				<input type="hidden" name="admin-action" value="bulk-edit-update">
				<input type="hidden" name="<?php echo HND_ADMIN_HIDDEN_FIELD; ?>" value="Y">
			
				<?php
				
					echo '<h3>' . $items->found_posts . ' Items</h3>';
					
					// table display
					echo '<table class="tablesorter">';
					echo '<thead>';	
					echo '<tr>';	
					echo '	<th>Title</th>';
					echo '	<th>Label</th>';
					echo '	<th>Stock</th>';
					echo '	<th>Price</th>';
					echo '	<th>Wholesale Price</th>';
					echo '</tr>';
					echo '</thead>';	
					
					echo '<tbody>';	
					
					$i = 0;

					while( $items->have_posts() ) : $items->the_post();
						
						global $post;
						
						// get item meta
						$id = get_the_ID();
						$title = $post->post_title;
						$label = get_taxonomy('label');
						$stock = ( get_post_meta( $id, 'stock', true ) ) ? get_post_meta( $id, 'stock', true ) : 0;
						$price = ( get_post_meta( $id, 'price', true ) ) ? get_post_meta( $id, 'price', true ) : 0;
						$wholesale_price = ( get_post_meta( $id, 'wholesale_price', true ) ) ? get_post_meta( $id, 'wholesale_price', true ) : 0;
						
						?>

						<tr>	
							<td width="50%">
								<div class="hide"><?php echo $title; ?></div>
								<a href="<?php echo get_edit_post_link(); ?>" target="_blank"><?php echo $title; ?></a>
							</td>
							
							<td>
								label = <?php echo $label; ?>
							</td>
								<input type="hidden" name="update_items[<?php echo $i; ?>][id]" value="<?php echo $id; ?>">
							<td>
								<div class="hide"><?php echo $stock; ?></div>
								<input type="text" style="width:50px; margin-right: 10px;" name="update_items[<?php echo $i; ?>][stock]" value="<?php echo $stock; ?>">
							</td>
							<td>
								<div class="hide"><?php echo $price; ?></div>
								$<input type="text" style="width:50px; margin-right: 10px;" name="update_items[<?php echo $i; ?>][price]" value="<?php echo $price; ?>">
							</td>
							<td>
								<div class="hide"><?php echo $wholesale_price; ?></div>
								$<input type="text" style="width:50px; margin-right: 10px;" name="update_items[<?php echo $i; ?>][wholesale_price]" value="<?php echo $wholesale_price; ?>">
							</td>
						</tr>

						<?php
						$i++;
					endwhile;

					echo '</tbody>';				
					echo '</table>';				
				
				?>

				<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Update' ); ?>" /></p>

			</form>

			<?php 

		} else {

			echo '<h3>No Items Found</h3>';
		
		}

		wp_reset_query();

		?>
		
	</div><?php	

}






/* Helper Functions
-------------------------------------------------------------- */

function hnd_admin_nav_filter() {
	?>
	<form class="nav-filter" name="nav-filter" method="post" action="">
			
		<input type="hidden" name="admin-action" value="item-filter">
		<input type="hidden" name="<?php echo HND_ADMIN_HIDDEN_FIELD; ?>" value="Y">

		<?php

		// formats, labels, genres, features
		$item_taxonomies = array(
			'formats' => array( 'orderby' => 'term_order', 'hierarchical' => true ),
			'labels' => array( 'orderby' => 'name' ),
			'genres'=> array( 'orderby' => 'name' ),
			'features' => array( 'orderby' => 'name' )
		);
		
		foreach( $item_taxonomies as $taxonomy => $order ) {
			
			// set hidden inout field
			echo '<input type="hidden" name="' . $taxonomy . '" value="">';
	
			// get terms
			$terms = get_terms( $taxonomy, $order );	
			
			if( $terms ) {
				echo '<div class="nav-filter-box">';
				echo '<select taxonomy="' . $taxonomy .'" class="nav-filter-select">';
				echo '<option selected="selected" value="">' . strtoupper( $taxonomy ) .'</option>';
					
				if( $taxonomy == 'formats' ) {
					// get taxonomy ids
					hnd_taxonomy_terms( 0, 'formats', false, true );	
				} else {
					foreach( $terms as $term ) {
						$link = get_term_link( $term, $taxonomy );
						if( is_wp_error( $link ) ) continue;
						echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
					}
				}	

				echo '</select>';
				echo '</div>';
			}
		}

		?>

		<div class="nav-filter-box">
			<input type="submit" name="Submit" class="button-primary" value="Filter" />
		</div>

	</form>

	<?php
}

function hnd_admin_get_items() {

	// default item args (get all)
	$args = array(
		'post_status' => array('publish','draft'),
		'post_type' => 'items',
		'order' => 'ASC',
		'orderby' => 'title',
		'posts_per_page' => -1,
	);

	// check POST data for filters
	if( isset( $_POST[ HND_ADMIN_HIDDEN_FIELD ] ) && ( $_POST[ HND_ADMIN_HIDDEN_FIELD ] == 'Y' ) && ( $_POST['admin-action'] == 'item-filter' ) ) {
    
		$taxes = array();

		if( $_POST['formats'] ) $taxes['formats'] = $_POST['formats'];
		if( $_POST['labels'] ) $taxes['labels'] = $_POST['labels'];
		if( $_POST['genres'] ) $taxes['genres'] = $_POST['genres'];
		if( $_POST['features'] ) $taxes['features'] = $_POST['features'];
		
		if( count( $taxes ) ) {

			$args['tax_query'] = array();

			// multiple taxonommies
			if( count( $taxes ) > 1 ) {
				$args['tax_query']['relation'] = 'AND';
			}

			foreach( $taxes as $key => $value ) {
				$args['tax_query'][] = array(
					'taxonomy' => $key,
					'field' => 'id',
					'terms' => array( $value )
				);
			}
		}
	}

	// display updated items
	if( isset( $_POST['update_items'] ) ) {
		
		$update_item_ids = array();
		$update_items = $_POST['update_items'];
		
		// add id to array
		foreach( $update_items as $item ) {
			$update_item_ids[] = $item['id'];
		}
	}

	// show the items we just updated
	if( count( $update_item_ids ) ) {
		$args['post__in'] = $update_item_ids;
	}	

	// get the items
	$items = new WP_Query( $args );

	return $items;
}