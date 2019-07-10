/* Site JS
-------------------------------------------------------------- */

jQuery(document).ready(function($) {
	
	/*********************/
	/* MOBILE NAVIGATION */
	/*********************/

	$('.mobile-nav-button').on('click', function() {
		if( !$(this).hasClass('mobile-nav-button-exclude') ) {
			
			$box = $(this).parent('.mobile-nav-box').children('.mobile-nav-dropdown');
			
			if( $(this).hasClass('active') ) {
				$(this).removeClass('active');
				$box.slideUp();
			} else {
				$('.mobile-nav-button').removeClass('active');
				$('.mobile-nav-dropdown').hide();
				$box.slideDown();
				$(this).addClass('active');
			}

		}
	});
	
	/**********/
	/* DESIGN */
	/**********/

	// maintain aspect ratio on certain elements
	function maintain_aspect_ratio() {
		$('.maintain-ratio').each(function(){
			// set height to match width
			w = $(this).width();
			$(this).height(w);
 		});
	}
	
	// maintain aspect ratio on resize
	$(window).resize(function() {
		maintain_aspect_ratio();
	});
	
	// adjust element sizes on load
	maintain_aspect_ratio();
	
	/***************/
	/* BUY BUTTONS */
	/***************/

	// append prices to Buy buttons
	$('.wp-cart-button-form input[type="submit"]').each(function() {
		price = $(this).parent('form').children('input[name="price"]').val();
		current = $(this).val();
		$(this).val( current + ' $' + price );
	});

	/****************/
	/* SINGLE ITEMS */
	/****************/

	// colorbox overlay
	$('.colorbox').colorbox({
		rel: 'colorbox',
		transition: 'fade',
		maxHeight: '90%',
		title: function() {
			return '<div class="colorbox-title">' + $(this).attr('title') + '</div>';
		}
	});
	
	/***********/
	/* WIDGETS */
	/***********/	
	
	// display item title on hover
	$('#new-arrivals-box-widget img').tooltip({
		effect: 'fade',
		//offset: [50, 0],
		opacity: 0.8,
		position: "top center",
	});
	
	/*
	// sync slideshow widget
	var sync_slides = $('.cycle-slideshow');
	
	// optional: sort the slideshow collection based on the value of the data-index attribute
	Array.prototype.sort.call( sync_slides, function(a, b) {
	    a = $(a).data('index'), b = $(b).data('index');
	    return a < b ? -1 : a > b ? 1 : 0;
	});
	
	// bind to cycle-after to trigger next slideshow's transition
	$('#sync-container').on('cycle-after', function(e) {
	    var index = sync_slides.index(e.target);
	    transitionNext(index);
	});
	
	// trigger the initial transition after 1 second
	setTimeout(transitionNext, 1000);
	
	function transitionNext( index ) {
	    if (index === undefined || index == sync_slides.length -1 )
	        index = 0;
	    else
	        index++;
	
	    sync_slides.eq(index).cycle('next');
	}
	*/

	/*****************/
	/* SHOPPING CART */
	/*****************/

	// adjust table cell headers
	/*$('.shopping-cart').find("th:eq(0)").html('Item');
	$('.shopping-cart').find("th:eq(1)").html('Qty');
	
	// shopping cart toggle
	$('#cart-button').on('click', function() {
		$(this).find('#masterhead .shopping-cart').toggleClass('active');
		$(this).find('#masterhead .cart-wrap').slideToggle();
	});

	// TODO: do this in php
	var cart_rows = $('#masterhead .shopping-cart').find('tr').length;
	cart_rows = cart_rows - 3; // adjust for header, total, and image rows
	
	// check if cart has items
	if( cart_rows > 0 ) {
		$('#masterhead .shopping-cart').addClass('has-items');
	} else {
		// cart is empty
		$('#masterhead .shopping-cart').removeClass('has-items');
	}*/

});