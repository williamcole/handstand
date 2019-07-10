<?php

#################
# SHOPPING CART #
#################

function hnd_shopping_cart_page( $atts = null ) {

	if( !is_plugin_active( 'wordpress-simple-paypal-shopping-cart/wp_shopping_cart.php' ) )
		return;

	$html = '';
	$html .= '<div id="cart-page" class="form-wrap">';
	$html .= '<div class="shopping-cart">';
	$html .= '	<div class="header-options cart-top">';	
	$html .= '		<h2>' . hnd_get_shopping_cart_item_details() . '</h2>';
	$html .= '	</div><!--/.cart-top-->';
	$html .= '	<div class="cart-wrap">';	
	$html .= 		do_shortcode( '[show_wp_shopping_cart]' );
	$html .= '	</div><!--/.cart-wrap-->';
	$html .= '	</div><!--/.shopping-cart-->';
	$html .= '</div><!--/#cart-page-->';

	return $html;
}
add_shortcode( 'cart-page', 'hnd_shopping_cart_page' );


function hnd_get_shopping_cart() {

	if( !is_plugin_active( 'wordpress-simple-paypal-shopping-cart/wp_shopping_cart.php' ) )
		return;

	$html = '';
	$html .= '<a href="' . home_url() . '/store/shopping-cart" class="shopping-cart">';
	$html .= '	<div class="header-options cart-top">';	
	$html .= '		<div class="header-cell"><h4>Cart</h4></div>';
	$html .= '		<div class="header-cell cart-items">' . hnd_get_shopping_cart_item_details() . '</div>';
	$html .= '		<div class="header-cell icon">&nbsp;</div>';
	$html .= '	</div><!--/.cart-top-->';
	$html .= '	<div class="cart-wrap">';	
	$html .= 		do_shortcode( '[show_wp_shopping_cart]' );
	$html .= '	</div><!--/.cart-wrap-->';
	$html .= '</a><!--/.shopping-cart-->';

	return $html;
}

function hnd_shopping_cart() {
	echo hnd_get_shopping_cart();
}

function hnd_get_shopping_cart_total_items() {
	return count( $_SESSION['simpleCart'] );
}

function hnd_shopping_cart_total_items() {
	echo hnd_get_shopping_cart_total_items();
}

function hnd_get_shopping_cart_total_price() {
	if( $_SESSION['simpleCart'] ) {
		$total = 0;
		foreach( $_SESSION['simpleCart'] as $item ) {
			$total += ( $item['quantity'] * $item['price'] );
		}	
		return number_format( $total, 2 );
	}
}

function hnd_shopping_cart_total_price() {
	echo hnd_get_shopping_cart_total_price();
}

function hnd_get_shopping_cart_item_details() {
	
	$items = hnd_get_shopping_cart_total_items();
	$total = hnd_get_shopping_cart_total_price();
	
	if( $items && $total ) {
		$item_text = ( $items == 1 ) ? 'item' : 'items';
		return $items . ' ' . $item_text . ' ($' . $total . ')'; 
	} else {
		return '(Cart is empty)';
	}
}

function hnd_shopping_cart_item_details() {
	echo hnd_get_shopping_cart_item_details();
}

# DEPRECATED ?
function hnd_shopping_cart_adjust_item_links() {
	if( $_SESSION['simpleCart'] ) {		
		foreach( $_SESSION['simpleCart'] as $key => $item ) {
			
			// remove size from shirt titles
			if( strpos( $item['name'], 'Shirt') !== false ) {
				$title_parts = explode( ' (', $item['name'] );
				$title = $title_parts[0];
			} else {
				$title = $item['name'];
			}

			// check cartLink to make sure it is the correct item permalink
			$post = get_page_by_title( $title, ARRAY_A, 'items' );
			
			# TODO: fix
			# 
			// replace cartLink if it doesnt match the permalink
			if( get_permalink( $post['ID'] ) !== $item['cartLink'] ) {
				$_SESSION['simpleCart'][$key]['cartLink'] = get_permalink( $post['ID'] );
			}
			
			// set thumbnail image
			$_SESSION['simpleCart'][$key]['thumbnail'] = $post['ID'];
		}
	}
}
#add_action( 'init', 'hnd_shopping_cart_adjust_item_links' );

/****************************/
/* FUTURE CART ENHANCEMENTS */
/****************************/

function hnd_usps_rate( $weight = null, $destination = null ) {

	// This script was written by Mark Sanborn at http://www.marksanborn.net  
	// If this script benefits you are your business please consider a donation  
	// You can donate at http://www.marksanborn.net/donate.  

	$userName = USPS_API_USER;
	$url = USPS_API_PROD_URL;
	$origin = '11211';
	
	// PACKAGE WEIGHT AND SIZE
	$weight_oz = ( isset( $weight ) ) ? $weight : 5;
	$weight_lb = 0;
	$size_w = 13;
	$size_l = 13;
	$size_h = 1;
	$size_g = 0;

	// DESTINATION
	$destination = ( isset( $destination ) ) ? $destination : '90210';
	
	// make sure zip code is valid 5-digit number
	if( preg_match( "/^\d{5}([\-]?\d{4})?$/i", $destination ) ) {
		
		echo '<h3>DOMESTIC</h3>';

		// set default domestic service
		$service = 'media-mail';

		if( $weight_oz <= 10 ) {
			// anything 5 oz and under should be sent First Class (cheaper)
			$service = 'first-class';
		} else {
			// anything 10 oz and over should be sent Media Mail (cheaper)
			// anything over 13 ounces MUST be sent Media Mail
			$service = 'media-mail';
		}
			
	} else {
		
		echo '<h4>INTERNATIONAL</h4>';
		
		// only option for international
		$service = 'first-class-international';

	}	

	// here's where the magic happens
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url ); // set the target url
	curl_setopt( $ch, CURLOPT_HEADER, 1 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_POST, 1 ); // parameters to post
	
	switch( $service ) {

		// first class
		case 'first-class':
			$response = 'RATEV4RESPONSE';
			$response_code = 0;
			$data = "API=RateV4&XML=<RateV4Request USERID=\"$userName\">
			    <Revision/>
			    <Package ID=\"1ST\">
			        <Service>FIRST CLASS</Service>
					<FirstClassMailType>PARCEL</FirstClassMailType>
			        <ZipOrigination>$origin</ZipOrigination>
			        <ZipDestination>$destination</ZipDestination>
			        <Pounds>$weight_lb</Pounds>
			        <Ounces>$weight_oz</Ounces>
			        <Container/>
			        <Size>REGULAR</Size>
			        <Machinable>true</Machinable>
			    </Package>
			</RateV4Request>";
		break;

		// first class international
		case 'first-class-international':
			$response = 'INTLRATEV2RESPONSE';
			$response_code = 15;
			$data = "API=IntlRateV2&XML=<IntlRateV2Request USERID=\"$userName\">
				<Package ID=\"1ST\">
					<Pounds>$weight_lb</Pounds>
			        <Ounces>$weight_oz</Ounces>
			        <Machinable>True</Machinable>
					<MailType>Package</MailType>
					<GXG>
						<POBoxFlag>N</POBoxFlag>
						<GiftFlag>Y</GiftFlag>
					</GXG>
					<ValueOfContents>50</ValueOfContents>
					<Country>$destination</Country>
					<Container>RECTANGULAR</Container>
					<Size>LARGE</Size>
					<Width>13</Width>
					<Length>13</Length>
					<Height>1</Height>
					<Girth>0</Girth>
					<CommercialFlag>N</CommercialFlag>
				</Package>
			</IntlRateV2Request>";
		break;

		// media mail
		default :
			$response = 'RATEV4RESPONSE';
			$response_code = 6;
			$data = "API=RateV4&XML=<RateV4Request USERID=\"$userName\">
			    <Revision/>
			    <Package ID=\"1ST\">
			        <Service>MEDIA</Service>
			        <ZipOrigination>$origin</ZipOrigination>
			        <ZipDestination>$destination</ZipDestination>
			        <Pounds>$weight_lb</Pounds>
			        <Ounces>$weight_oz</Ounces>
			        <Container/>
			        <Size>REGULAR</Size>
			        <Machinable>true</Machinable>
			    </Package>
			</RateV4Request>";
		break;
	}

	// send the POST values to USPS
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	$result = curl_exec( $ch );
	$data = strstr( $result, '<?' );
	$xml_parser = xml_parser_create();
	xml_parse_into_struct( $xml_parser, $data, $vals, $index );
	xml_parser_free( $xml_parser );
	$params = array();
	$level = array();

	foreach( $vals as $xml_elem ) {
	    if( $xml_elem['type'] == 'open' ) {
	        if( array_key_exists( 'attributes', $xml_elem ) ) {
	            list( $level[$xml_elem['level']], $extra ) = array_values( $xml_elem['attributes'] );
	        } else {
	        	$level[$xml_elem['level']] = $xml_elem['tag'];
	        }
	    }
	    if( $xml_elem['type'] == 'complete' ) {
		    $start_level = 1;
		    $php_stmt = '$params';
		    while( $start_level < $xml_elem['level'] ) {
		        $php_stmt .= '[$level['.$start_level.']]';
		        $start_level++;
		    }
		    $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
		    eval( $php_stmt );
	    }
	}
	curl_close( $ch );
	
	// DEBUG - Uncomment to see xml tags
	if( is_wbc3() ) {
		//echo '<pre>'; print_r( $params ); echo '</pre>';
	}
	
	// RESPONSE
	$usps_response = $params[$response];

	if( $service == 'first-class-international' ) {
		$usps_service = $usps_response['1ST'][$response_code]['SVCDESCRIPTION'];
		$usps_price = $usps_response['1ST'][$response_code]['POSTAGE'];
	} else {
		$usps_service = $usps_response['1ST'][$response_code]['MAILSERVICE'];
		$usps_price = $usps_response['1ST'][$response_code]['RATE'];
	}

	echo '<p><b>DESTINATION:</b> ' . $destination . '</p>';
	echo '<p><b>METHOD:</b> ' . $usps_service . '</p>';
	echo '<p><b>WEIGHT:</b> ' . $weight_oz . ' oz.</p>';
	echo '<p><b>PRICE:</b> $' . $usps_price . '</p>';

	#return $usps_price;	
}

function hnd_usps_get_country_list() {
	
	$countries = hnd_usps_get_countries();

	if( count( $countries ) ) {

		$html = '<select id="country" name="country">';
		
		foreach( $countries as $id => $country ) {
			$html .= '<option value="' . $id . '">' . $country . '</option>';
		} 

		$html .= '</select>';

		// output
		return $html;
	}
}

function hnd_usps_country_list() {
	echo hnd_usps_get_country_list();
}

function hnd_usps_get_countries() {

	$countries = array(		
		'00000' => 'United States (Domestic and APO/FPO/DPO Mail)',
		'10440' => 'Abu Dhabi (United Arab Emirates)',
		'10345' => 'Admiralty Islands (Papua New Guinea)',
		'10000' => 'Afghanistan',
		'10314' => 'Aitutaki, Cook Islands (New Zealand)',
		'10441' => 'Ajman (United Arab Emirates)',
		'10110' => 'Aland Island (Finland)',
		'10001' => 'Albania',
		'12000' => 'Alberta (Canada)',
		'10140' => 'Alderney (Channel Islands) (Great Britain and Northern Ireland)',
		'10002' => 'Algeria',
		'10388' => 'Alhucemas (Spain)',
		'10309' => 'Alofi Island (New Caledonia)',
		'10450' => 'American Samoa',
		'10193' => 'Andaman Islands (India)',
		'10003' => 'Andorra',
		'10004' => 'Angola',
		'10005' => 'Anguilla',
		'10068' => 'Anjouan (Comoros)',
		'10097' => 'Annobon Island (Equatorial Guinea)',
		'12140' => 'Antigua (Antigua and Barbuda)',
		'10006' => 'Antigua and Barbuda',
		'10009' => 'Argentina',
		'10010' => 'Armenia',
		'10011' => 'Aruba',
		'10012' => 'Ascension',
		'10152' => 'Astypalaia (Greece)',
		'10505' => 'Atafu (Western Samoa)',
		'12026' => 'Atiu, Cook Islands (New Zealand)',
		'10013' => 'Australia',
		'10026' => 'Austria',
		'10315' => 'Avarua (New Zealand)',
		'10027' => 'Azerbaijan',
		'10359' => 'Azores (Portugal)',
		'10028' => 'Bahamas',
		'10029' => 'Bahrain',
		'10389' => 'Balearic Islands (Spain)',
		'10342' => 'Baluchistan (Pakistan)',
		'10030' => 'Bangladesh',
		'10496' => 'Banks Island (Vanuatu)',
		'10031' => 'Barbados',
		'10007' => 'Barbuda (Antigua and Barbuda)',
		'10171' => 'Barthelemy (Guadeloupe)',
		'10032' => 'Belarus',
		'10033' => 'Belgium',
		'10034' => 'Belize',
		'10036' => 'Benin',
		'10038' => 'Bermuda',
		'10039' => 'Bhutan',
		'10346' => 'Bismark Archipelago (Papua New Guinea)',
		'10040' => 'Bolivia',
		'10299' => 'Bonaire (Curacao)',
		'10118' => 'Borabora (French Polynesia)',
		'10200' => 'Borneo (Indonesia)',
		'10041' => 'Bosnia-Herzegovina',
		'10042' => 'Botswana',
		'10347' => 'Bougainville (Papua New Guinea)',
		'10366' => 'Bourbon (Reunion)',
		'10043' => 'Brazil',
		'12001' => 'British Columbia (Canada)',
		'10184' => 'British Guiana (Guyana)',
		'10035' => 'British Honduras (Belize)',
		'10044' => 'British Virgin Islands',
		'10045' => 'Brunei Darussalam',
		'10348' => 'Buka (Papua New Guinea)',
		'10046' => 'Bulgaria',
		'10047' => 'Burkina Faso',
		'10048' => 'Burma',
		'10050' => 'Burundi',
		'10434' => 'Caicos Islands (Turks and Caicos Islands)',
		'10051' => 'Cambodia',
		'10053' => 'Cameroon',
		'10054' => 'Canada',
		'10390' => 'Canary Islands (Spain)',
		'10225' => 'Canton Island (Kiribati)',
		'10057' => 'Cape Verde',
		'10058' => 'Cayman Islands',
		'10059' => 'Central African Republic',
		'10391' => 'Ceuta (Spain)',
		'10397' => 'Ceylon (Sri Lanka)',
		'10060' => 'Chad',
		'10392' => 'Chaferinas Islands (Spain)',
		'10153' => 'Chalki (Greece)',
		'10141' => 'Channel Islands (Jersey, Guernsey, Alderney and Sark) (Great Britain and Northern Ireland)',
		'10062' => 'Chile',
		'10063' => 'China',
		'10452' => 'Christiansted, US Virgin Islands',
		'10014' => 'Christmas Island (Australia)',
		'10226' => 'Christmas Island (Kiribati)',
		'10453' => 'Chuuk, Micronesia',
		'10015' => 'Cocos Island (Australia)',
		'10067' => 'Colombia',
		'10069' => 'Comoros',
		'10073' => 'Congo, Democratic Republic of the',
		'10072' => 'Congo, Republic of the',
		'10316' => 'Cook Islands (New Zealand)',
		'10098' => 'Corisco Island (Equatorial Guinea)',
		'10112' => 'Corsica (France)',
		'10080' => 'Costa Rica',
		'10081' => 'Cote d Ivoire',
		'10154' => 'Crete (Greece)',
		'10082' => 'Croatia',
		'10083' => 'Cuba',
		'10280' => 'Cumino Island (Malta)',
		'10300' => 'Curacao',
		'10243' => 'Cyjrenaica (Libya)',
		'10085' => 'Cyprus',
		'10086' => 'Czech Republic',
		'10037' => 'Dahomey (Benin)',
		'10194' => 'Damao (India)',
		'10317' => 'Danger Islands (New Zealand)',
		'10087' => 'Denmark',
		'10172' => 'Desirade Island (Guadeloupe)',
		'10195' => 'Diu (India)',
		'10088' => 'Djibouti',
		'10155' => 'Dodecanese Islands (Greece)',
		'10363' => 'Doha (Qatar)',
		'10091' => 'Dominica',
		'10092' => 'Dominican Republic',
		'10442' => 'Dubai (United Arab Emirates)',
		'10201' => 'East Timor (Timor-Leste, Democratic Republic of)',
		'10456' => 'Ebeye, Marshall Islands',
		'10093' => 'Ecuador',
		'10094' => 'Egypt',
		'10209' => 'Eire (Ireland)',
		'10095' => 'El Salvador',
		'10436' => 'Ellice Islands (Tuvalu)',
		'10099' => 'Elobey Islands (Equatorial Guinea)',
		'10227' => 'Enderbury Island (Kiribati)',
		'10142' => 'England (Great Britain and Northern Ireland)',
		'10100' => 'Equatorial Guinea',
		'10103' => 'Eritrea',
		'10104' => 'Estonia',
		'10105' => 'Ethiopia',
		'10506' => 'Fakaofo (Western Samoa)',
		'10106' => 'Falkland Islands',
		'10228' => 'Fanning Island (Kiribati)',
		'10108' => 'Faroe Islands',
		'10101' => 'Fernando Po (Equatorial Guinea)',
		'10244' => 'Fezzan (Libya)',
		'10109' => 'Fiji',
		'10111' => 'Finland',
		'10414' => 'Formosa (Taiwan)',
		'10113' => 'France',
		'10458' => 'Frederiksted, US Virgin Islands',
		'10117' => 'French Guiana',
		'10119' => 'French Oceania (French Polynesia)',
		'10120' => 'French Polynesia',
		'10089' => 'French Somaliland (Djibouti)',
		'10090' => 'French Territory of the Afars and Issas (Djibouti)',
		'10173' => 'French West Indies (Guadeloupe)',
		'12002' => 'French West Indies (Martinique)',
		'10426' => 'Friendly Islands (Tonga)',
		'10443' => 'Fujairah (United Arab Emirates)',
		'10503' => 'Futuna (Wallis and Futuna Islands)',
		'10134' => 'Gabon',
		'10135' => 'Gambia',
		'10121' => 'Gambier (French Polynesia)',
		'10136' => 'Georgia, Republic of',
		'10137' => 'Germany',
		'10138' => 'Ghana',
		'10139' => 'Gibraltar',
		'10229' => 'Gilbert Islands (Kiribati)',
		'10196' => 'Goa (India)',
		'10281' => 'Gozo Island (Malta)',
		'10070' => 'Grand Comoro (Comoros)',
		'10143' => 'Great Britain and Northern Ireland',
		'10156' => 'Greece',
		'10169' => 'Greenland',
		'10170' => 'Grenada',
		'10407' => 'Grenadines (Saint Vincent and the Grenadines)',
		'10174' => 'Guadeloupe',
		'10459' => 'Guam',
		'10181' => 'Guatemala',
		'10144' => 'Guernsey (Channel Islands) (Great Britain and Northern Ireland)',
		'10182' => 'Guinea',
		'10183' => 'Guinea-Bissau',
		'10185' => 'Guyana',
		'10064' => 'Hainan Island (China)',
		'10186' => 'Haiti',
		'10220' => 'Hashemite Kingdom (Jordan)',
		'10318' => 'Hervey, Cook Islands (New Zealand)',
		'10122' => 'Hivaoa (French Polynesia)',
		'10297' => 'Holland (Netherlands)',
		'10187' => 'Honduras',
		'10189' => 'Hong Kong',
		'10123' => 'Huahine (French Polynesia)',
		'10310' => 'Huan Island (New Caledonia)',
		'10191' => 'Hungary',
		'10192' => 'Iceland',
		'10197' => 'India',
		'10202' => 'Indonesia',
		'10206' => 'Iran',
		'10208' => 'Iraq',
		'10210' => 'Ireland',
		'10203' => 'Irian Barat (Indonesia)',
		'10145' => 'Isle of Man (Great Britain and Northern Ireland)',
		'10311' => 'Isle of Pines (New Caledonia)',
		'10084' => 'Isle of Pines, West Indies (Cuba)',
		'10211' => 'Israel',
		'12077' => 'Issas (Djibouti)',
		'10212' => 'Italy',
		'12312' => 'Ivory Coast (Cote d Ivoire)',
		'10213' => 'Jamaica',
		'10214' => 'Japan',
		'10146' => 'Jersey (Channel Islands) (Great Britain and Northern Ireland)',
		'10259' => 'Johore (Malaysia)',
		'10221' => 'Jordan',
		'10157' => 'Kalymnos (Greece)',
		'10052' => 'Kampuchea (Cambodia)',
		'10158' => 'Karpathos (Greece)',
		'10159' => 'Kassos (Greece)',
		'10160' => 'Kastellorizon (Greece)',
		'10223' => 'Kazakhstan',
		'10260' => 'Kedah (Malaysia)',
		'10016' => 'Keeling Islands (Australia)',
		'10261' => 'Kelantan (Malaysia)',
		'10224' => 'Kenya',
		'10462' => 'Kingshill, US Virgin Islands',
		'10230' => 'Kiribati',
		'10232' => 'Korea, Democratic Peoples Republic of (North Korea)',
		'10234' => 'Korea, Republic of (South Korea)',
		'10463' => 'Koror (Palau)',
		'10161' => 'Kos (Greece)',
		'12317' => 'Kosovo, Republic of',
		'10464' => 'Kosrae, Micronesia',
		'10190' => 'Kowloon (Hong Kong)',
		'10236' => 'Kuwait',
		'10465' => 'Kwajalein, Marshall Islands',
		'10237' => 'Kyrgyzstan',
		'10055' => 'Labrador (Canada)',
		'10262' => 'Labuan (Malaysia)',
		'10238' => 'Laos',
		'10239' => 'Latvia',
		'10240' => 'Lebanon',
		'10162' => 'Leipsos (Greece)',
		'10163' => 'Leros (Greece)',
		'10175' => 'Les Saints Island (Guadeloupe)',
		'10241' => 'Lesotho',
		'10242' => 'Liberia',
		'10245' => 'Libya',
		'10247' => 'Liechtenstein',
		'10248' => 'Lithuania',
		'10017' => 'Lord Howe Island (Australia)',
		'10312' => 'Loyalty Islands (New Caledonia)',
		'10249' => 'Luxembourg',
		'10250' => 'Macao',
		'10251' => 'Macau (Macao)',
		'10252' => 'Macedonia, Republic of',
		'10253' => 'Madagascar',
		'10360' => 'Madeira Islands (Portugal)',
		'10466' => 'Majuro, Marshall Islands',
		'10263' => 'Malacca (Malaysia)',
		'10254' => 'Malagasy Republic (Madagascar)',
		'10256' => 'Malawi',
		'10264' => 'Malaya (Malaysia)',
		'10265' => 'Malaysia',
		'10278' => 'Maldives',
		'10279' => 'Mali',
		'10282' => 'Malta',
		'10319' => 'Manahiki (New Zealand)',
		'10065' => 'Manchuria (China)',
		'12006' => 'Manitoba (Canada)',
		'10467' => 'Manua Islands, American Samoa',
		'10176' => 'Marie Galante (Guadeloupe)',
		'10124' => 'Marquesas Islands (French Polynesia)',
		'10468' => 'Marshall Islands, Republic of the',
		'10283' => 'Martinique',
		'10284' => 'Mauritania',
		'10285' => 'Mauritius',
		'10115' => 'Mayotte (France)',
		'10393' => 'Melilla (Spain)',
		'10287' => 'Mexico',
		'10469' => 'Micronesia, Federated States of',
		'10404' => 'Miquelon (Saint Pierre and Miquelon)',
		'10071' => 'Moheli (Comoros)',
		'10288' => 'Moldova',
		'10116' => 'Monaco (France)',
		'10289' => 'Mongolia',
		'12316' => 'Montenegro',
		'10290' => 'Montserrat',
		'10125' => 'Moorea (French Polynesia)',
		'10291' => 'Morocco',
		'10292' => 'Mozambique',
		'10340' => 'Muscat (Oman)',
		'10049' => 'Myanmar (Burma)',
		'10293' => 'Namibia',
		'10215' => 'Nansil Islands (Japan)',
		'10295' => 'Nauru',
		'10266' => 'Negri Sembilan (Malaysia)',
		'10296' => 'Nepal',
		'10298' => 'Netherlands',
		'10399' => 'Nevis (Saint Christopher and Nevis)',
		'10349' => 'New Britain (Papua New Guinea)',
		'12011' => 'New Brunswick (Canada)',
		'10313' => 'New Caledonia',
		'10350' => 'New Hanover (Papua New Guinea)',
		'10497' => 'New Hebrides (Vanuatu)',
		'10351' => 'New Ireland (Papua New Guinea)',
		'10018' => 'New South Wales (Australia)',
		'10324' => 'New Zealand',
		'10056' => 'Newfoundland (Canada)',
		'10335' => 'Nicaragua',
		'10336' => 'Niger',
		'10337' => 'Nigeria',
		'10164' => 'Nissiros (Greece)',
		'10325' => 'Niue (New Zealand)',
		'10019' => 'Norfolk Island (Australia)',
		'10267' => 'North Borneo (Malaysia)',
		'10233' => 'North Korea (Korea, Democratic People\'s Republic of)',
		'10147' => 'Northern Ireland (Great Britain and Northern Ireland)',
		'10473' => 'Northern Mariana Islands, Commonwealth of',
		'12012' => 'Northwest Territory (Canada)',
		'10338' => 'Norway',
		'12013' => 'Nova Scotia (Canada)',
		'10126' => 'Nukahiva (French Polynesia)',
		'10507' => 'Nukunonu (Western Samoa)',
		'10257' => 'Nyasaland (Malawi)',
		'10231' => 'Ocean Island (Kiribati)',
		'10217' => 'Okinawa (Japan)',
		'10341' => 'Oman',
		'12014' => 'Ontario (Canada)',
		'10474' => 'Pago Pago, American Samoa',
		'10268' => 'Pahang (Malaysia)',
		'10343' => 'Pakistan',
		'10475' => 'Palau',
		'10326' => 'Palmerston, Avarua (New Zealand)',
		'10344' => 'Panama',
		'10352' => 'Papua New Guinea',
		'10353' => 'Paraguay',
		'10327' => 'Parry, Cook Islands (New Zealand)',
		'10165' => 'Patmos (Greece)',
		'10420' => 'Pemba (Tanzania)',
		'10269' => 'Penang (Malaysia)',
		'10415' => 'Penghu Islands (Taiwan)',
		'10394' => 'Penon de Velez de la Gomera (Spain)',
		'10328' => 'Penrhyn, Tongareva (New Zealand)',
		'10270' => 'Perak (Malaysia)',
		'10271' => 'Perlis (Malaysia)',
		'10207' => 'Persia (Iran)',
		'10354' => 'Peru',
		'10416' => 'Pescadores Islands (Taiwan)',
		'10177' => 'Petite Terre (Guadeloupe)',
		'10355' => 'Philippines',
		'10356' => 'Pitcairn Island',
		'10477' => 'Pohnpei, Micronesia',
		'10357' => 'Poland',
		'10362' => 'Portugal',
		'12015' => 'Prince Edward Island (Canada)',
		'10272' => 'Province Wellesley (Malaysia)',
		'10478' => 'Puerto Rico',
		'10329' => 'Pukapuka (New Zealand)',
		'10364' => 'Qatar',
		'12016' => 'Quebec (Canada)',
		'10020' => 'Queensland (Australia)',
		'10417' => 'Quemoy (Taiwan)',
		'10127' => 'Raiatea (French Polynesia)',
		'10330' => 'Rakaanga (New Zealand)',
		'10128' => 'Rapa (French Polynesia)',
		'10331' => 'Rarotonga, Cook Islands (New Zealand)',
		'10444' => 'Ras al Kaimah (United Arab Emirates)',
		'10008' => 'Redonda (Antigua and Barbuda)',
		'10367' => 'Reunion',
		'10515' => 'Rhodesia (Zimbabwe)',
		'10102' => 'Rio Muni (Equatorial Guinea)',
		'10166' => 'Rodos (Greece)',
		'10286' => 'Rodrigues (Mauritius)',
		'10368' => 'Romania',
		'10480' => 'Rota, Northern Mariana Islands',
		'10369' => 'Russia',
		'10370' => 'Rwanda',
		'10303' => 'Saba (Curacao)',
		'10273' => 'Sabah (Malaysia)',
		'12019' => 'Saint Barthelemy (Guadeloupe)',
		'10178' => 'Saint Bartholomew (Guadeloupe)',
		'10400' => 'Saint Christopher and Nevis',
		'10481' => 'Saint Croix, US Virgin Islands',
		'10402' => 'Saint Helena',
		'10482' => 'Saint John, US Virgin Islands',
		'10401' => 'Saint Kitts (Saint Christopher and Nevis)',
		'10403' => 'Saint Lucia',
		'10179' => 'Saint Martin (French) (Guadeloupe)',
		'10405' => 'Saint Pierre and Miquelon',
		'10483' => 'Saint Thomas, US Virgin Islands',
		'10406' => 'Saint Vincent and the Grenadines',
		'12017' => 'Sainte Marie de Madagascar (Madagascar)',
		'10484' => 'Saipan, Northern Mariana Islands',
		'10096' => 'Salvador (El Salvador)',
		'10451' => 'Samoa, American',
		'10371' => 'San Marino',
		'10381' => 'Santa Cruz Islands (Solomon Island)',
		'10372' => 'Sao Tome and Principe',
		'10274' => 'Sarawak (Malaysia)',
		'10148' => 'Sark (Channel Islands) (Great Britain and Northern Ireland)',
		'12018' => 'Saskatchewan (Canada)',
		'10373' => 'Saudi Arabia',
		'10332' => 'Savage Island, Niue (New Zealand)',
		'10508' => 'Savaii Island (Western Samoa)',
		'10149' => 'Scotland (Great Britain and Northern Ireland)',
		'10275' => 'Selangor (Malaysia)',
		'10374' => 'Senegal',
		'12313' => 'Serbia, Republic of',
		'10376' => 'Seychelles',
		'10445' => 'Sharja (United Arab Emirates)',
		'10218' => 'Shikoku (Japan)',
		'10423' => 'Siam (Thailand)',
		'10377' => 'Sierra Leone',
		'10198' => 'Sikkim (India)',
		'10378' => 'Singapore',
		'10304' => 'Sint Eustatius (Curacao)',
		'10305' => 'Sint Maarten (Dutch)',
		'10379' => 'Slovak Republic (Slovakia)',
		'10380' => 'Slovenia',
		'10129' => 'Society Islands (French Polynesia)',
		'10382' => 'Solomon Islands',
		'10383' => 'Somali Democratic Republic (Somalia)',
		'10384' => 'Somalia',
		'10385' => 'Somaliland (Somalia)',
		'10386' => 'South Africa',
		'10021' => 'South Australia (Australia)',
		'10107' => 'South Georgia (Falkland Islands)',
		'10235' => 'South Korea (Korea, Republic of)',
		'10294' => 'South-West Africa (Namibia)',
		'10395' => 'Spain',
		'10339' => 'Spitzbergen (Norway)',
		'10398' => 'Sri Lanka',
		'10408' => 'Sudan',
		'10409' => 'Suriname',
		'10333' => 'Suwarrow Islands (New Zealand)',
		'10486' => 'Swain\'s Island, American Samoa',
		'10188' => 'Swan Islands (Honduras)',
		'10410' => 'Swaziland',
		'10411' => 'Sweden',
		'10412' => 'Switzerland',
		'10167' => 'Symi (Greece)',
		'10413' => 'Syrian Arab Republic (Syria)',
		'10130' => 'Tahaa (French Polynesia)',
		'10131' => 'Tahiti (French Polynesia)',
		'10418' => 'Taiwan',
		'10419' => 'Tajikistan',
		'10421' => 'Tanzania',
		'10022' => 'Tasmania (Australia)',
		'10061' => 'Tchad (Chad)',
		'10424' => 'Thailand',
		'10023' => 'Thursday Island (Australia)',
		'10066' => 'Tibet (China)',
		'10168' => 'Tilos (Greece)',
		'10204' => 'Timor (Indonesia)',
		'10517' => 'Timor-Leste, Democratic Republic of',
		'10487' => 'Tinian, Northern Mariana Islands',
		'10428' => 'Tobago (Trinidad and Tobago)',
		'10425' => 'Togo',
		'10509' => 'Tokelau (Union Group) (Western Samoa)',
		'10427' => 'Tonga',
		'10334' => 'Tongareva (New Zealand)',
		'10219' => 'Tori Shima (Japan)',
		'10498' => 'Torres Island (Vanuatu)',
		'10222' => 'Trans-Jordan, Hashemite Kingdom (Jordan)',
		'10387' => 'Transkei (South Africa)',
		'10276' => 'Trengganu (Malaysia)',
		'10429' => 'Trinidad and Tobago',
		'10246' => 'Tripolitania (Libya)',
		'10430' => 'Tristan da Cunha',
		'10446' => 'Trucial States (United Arab Emirates)',
		'10132' => 'Tuamotou (French Polynesia)',
		'10133' => 'Tubuai (French Polynesia)',
		'10431' => 'Tunisia',
		'10432' => 'Turkey',
		'10433' => 'Turkmenistan',
		'10435' => 'Turks and Caicos Islands',
		'10489' => 'Tutuila Island, American Samoa',
		'10437' => 'Tuvalu',
		'10438' => 'Uganda',
		'10439' => 'Ukraine',
		'10447' => 'Umm al Quaiwain (United Arab Emirates)',
		'10365' => 'Umm Said (Qatar)',
		'10510' => 'Union Group (Western Samoa)',
		'10448' => 'United Arab Emirates',
		'10150' => 'United Kingdom (Great Britain and Northern Ireland)',
		'10491' => 'United Nations, New York',
		'00000' => 'United States (Domestic and APO/FPO/DPO Mail)',
		'10511' => 'Upolu Island (Western Samoa)',
		'10449' => 'Uruguay',
		'10495' => 'Uzbekistan',
		'10499' => 'Vanuatu',
		'10500' => 'Vatican City',
		'10501' => 'Venezuela',
		'10024' => 'Victoria (Australia)',
		'10502' => 'Vietnam',
		'12023' => 'Virgin Islands (British)',
		'10492' => 'Virgin Islands (US)',
		'10151' => 'Wales (Great Britain and Northern Ireland)',
		'10504' => 'Wallis and Futuna Islands',
		'10277' => 'Wellesley, Province (Malaysia)',
		'10205' => 'West New Guinea (Indonesia)',
		'10025' => 'Western Australia (Australia)',
		'10512' => 'Western Samoa',
		'10494' => 'Yap, Micronesia',
		'10513' => 'Yemen',
		'12024' => 'Yukon Territory (Canada)',
		'10396' => 'Zafarani Islands (Spain)',
		'10514' => 'Zambia',
		'10422' => 'Zanzibar (Tanzania)',
		'10516' => 'Zimbabwe',
	);

	return $countries;
}