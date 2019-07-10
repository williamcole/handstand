/* Shopping Cart JS
-------------------------------------------------------------- */

jQuery(document).ready(function($) {
		
	/*
	
	// KILL ALL THIS, NO LONGER NEEDED

	// add note about shipping rates
	$('.shopping_cart').find('.wpspsc_checkout_form').before('<tr><td colspan="4"><div align="center" class="small">Shipping will be added on checkout.</td></td></tr>');

	// get current total
	$total_label_cell = $('.shopping_cart').find("td:contains('Total:')");
	$total_value_cell = $total_label_cell.parent('tr').children("td:contains('$')");
	
	// make sure value is numeric so we can calculate stuff
	var total = Number( $total_value_cell.html().replace( /^\D+/g, '') );
	//console.log( 'total ' + total );

	// get shipping rate values
	var rates = new Array();
	$('.shipping-rates').children('tbody').children('tr').each(function() {
		
		var range = $(this).children('td:first').text();
		var range_parts = range.split(" — ");
		
		if( range_parts.length > 1 ) {

			var range1 = range_parts[0];
			range1 = Number( range1.replace( /^\D+/g, '') );
			
			var range2 = range_parts[1];
			range2 = Number( range2.replace( /^\D+/g, '') );
			
			// compare total to shipping range
			if( range1 && range2 && ( total >= range1 ) && ( total <= range2 ) ) {

				// highlight corresponding row
				$('.shipping-rates').children('tbody').children('tr').last().removeClass('in-range');
				$(this).addClass('in-range');
			}
		} else if( total > 50 ) {
			$('.shipping-rates').children('tbody').children('tr').last().addClass('in-range');
		}
		
	});

	// TODO: add shipping price
	//$('.shopping_cart').find('.wpspsc_checkout_form').before('<tr><td colspan="2" style="font-weight:bold; text-align:right;">Shipping:</td><td style="text-align:center">$XX.XX</td><td></td></tr>');
	
	*/

});