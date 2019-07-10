<?php

/* General helper functions for Discogs API */

#########
# ADMIN #
#########

function hnd_discogs_get_response( $url = null ) {

	if( !$url )
		return;

	//initialize the session 
	$ch = curl_init(); 

	//Set the User-Agent Identifier 
	curl_setopt($ch, CURLOPT_USERAGENT, 'HandstandRecords/0.1 +http://www.handstandrecords.com'); 

	//Set the URL of the page or file to download. 
	curl_setopt($ch, CURLOPT_URL, $url); 

	//Ask cURL to return the contents in a variable instead of simply echoing them 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

	//Execute the curl session 
	$output = curl_exec($ch); 

	//close the session 
	curl_close ($ch); 

	// convert repsonse to array
	$response = json_decode( $output );

	// DUMP
	//echo '<hr>' . '<pre>' . print_r( $response, true ) . '</pre>';

	return $response;
}

function hnd_discogs_get_inventory_listings() {
	
	$inventory = hnd_discogs_get_response( DISCOGS_INVENTORY_URL );
	
	if( !$inventory )
		return;

	$num_pages = $inventory->pagination->pages;
	
	$listings = array();

	for( $i = 1; $i <= $num_pages; $i++ ) {

		// get inventory in chunks
		$inventory_chunk = hnd_discogs_get_response( DISCOGS_INVENTORY_URL . '?page=' . $i . '&per_page=50' );

		foreach( $inventory_chunk->listings as $listing ) {
			$listings[] = $listing;
		}
	}
	
	return $listings;
}

// DO WE NEED THIS?
function hnd_discogs_get_inventory_ids() {

	$ids = array();

	$inventory = hnd_discogs_get_response( DISCOGS_INVENTORY_URL );

	$num_items = $inventory->pagination->items;
	echo '<p>' . $num_items . ' total items</p>';

	$num_pages = $inventory->pagination->pages;
	echo '<p>' . $num_pages . ' total pages</p>';

	for( $i = 1; $i <= $num_pages; $i++ ) {

		// get inventory in chunks
		$inventory_chunk = hnd_discogs_get_response( DISCOGS_INVENTORY_URL . '?page=' . $i . '&per_page=50' );

		foreach( $inventory_chunk->listings as $listing ) {
			
			// listing id
			$ids[] = $listing->id;

			// release id
			//$ids[] = $listing->release->id;
		}
	}

	return $ids;
}

function hnd_discogs_search( $query = null ) {

	if( !$query )
		return;

	$results = hnd_discogs_get_response( DISCOGS_SEARCH_URL . '?q=' . urlencode( $query ) . '&type=releases' );

	return $results;
}

function hnd_discogs_orders( $query = null ) {

	if( !$query )
		return;

	$orders = hnd_discogs_get_response( DISCOGS_ORDERS_URL . '?q=' . urlencode( $query ) . '&type=releases' );

	return $orders;
}


?> 