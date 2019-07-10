/* Trades JS
-------------------------------------------------------------- */

jQuery(document).ready(function($) {
	
	// define some object variables
	$type_radio = $('#trade-form input[name="contact_type"]');
	$item_checkbox = $('#trade-list input[type="checkbox"]');
	$trade_item = $('#trade-form .trade-item');
	$jewel_case = $('#trade-form .exclude-jewel-cases');
	
	toggle_jewel_case_checkbox();

	$type_radio.on('change', function() {
		toggle_contact_name_label();
	});

	$item_checkbox.on('change', function() {
		toggle_jewel_case_checkbox();
	});

	/*
	$trade_item.hover(function() {
		$(this).find('.excerpt').slideDown();
	}, function() {
		$('.excerpt').hide();
	});
	*/

	function toggle_contact_name_label() {
		
		var name = '';
		var placeholder = new Array();
			placeholder['Label'] = 'Awesome Records';
			placeholder['Distributor'] = 'Crucial Distribution';
			placeholder['Artist'] = 'The Punk Rockers';
			placeholder['Other'] = '???';

		$type_radio.each(function() {
			if( $(this).is(':checked') ) {
				name = $(this).val();
			}
		});

		if( name ) {
			$('label[for="contact_name"]').html( name + ' Name');
			$('input[name="contact_name"]').attr( 'placeholder', placeholder[name] );
		}
	}

	function toggle_jewel_case_checkbox() {

		var trade_items = '';
		
		// get the checked items
		$item_checkbox.each(function(){
			if( $(this).is(':checked') ) {
				trade_items += $(this).attr('item');
			}
		});

		// display checkbox if any CD items are checked
		if( trade_items.indexOf("CD") !== -1 ) {
			$jewel_case.show();
		} else {
			$jewel_case.hide();
		}
	}



	/*
	$contact_form = $('#contact-form-1213');
	$interested_in = $('#contact-form-comment-g1213-interestedin');
	$submit_btn = $('.contact-submit').find('input');

	// add some element styles
	$interested_in.attr('disabled', 'diabled').css('background', '#EEE');
	$submit_btn.addClass('button');

	

	$submit_btn.on('click', function(){
		
		// remove disabled attribute
		$interested_in.removeAttr('disabled');
	});

	

	*/

});