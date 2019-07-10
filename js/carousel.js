/* Carousel JS
-------------------------------------------------------------- */

jQuery(document).ready(function($) {
	
	// items
	$('.owl-carousel-items').owlCarousel({
		items : 5, // 5 items above 1000px browser width
		itemsDesktop : [1000,5], // 5 items between 1000px and 901px
		itemsDesktopSmall : [900,5], // betweem 900px and 601px
		itemsTablet: [600,5], // 2 items between 600 and 0
		itemsMobile : [480,3],
		navigation: true,
		navigationText: [
			"<i class='icon-chevron-left icon-white'>&#9664;</i>",
			"<i class='icon-chevron-right icon-white'>&#9654;</i>"
		]
	});

	// items
	$('.owl-carousel-news').owlCarousel({
		items : 10, // 5 items above 1000px browser width
		itemsDesktop : [1000,10], // 5 items between 1000px and 901px
		itemsDesktopSmall : [900,10], // betweem 900px and 601px
		itemsTablet: [600,5], // 2 items between 600 and 0
		itemsMobile : [480,5],
		navigation: true,
		navigationText: [
			"<i class='icon-chevron-left icon-white'>&#9664;</i>",
			"<i class='icon-chevron-right icon-white'>&#9654;</i>"
		]
	});

	// items
	$('.owl-carousel-audio').owlCarousel({
		items : 5, // 5 items above 1000px browser width
		itemsDesktop : [1000,5], // 5 items between 1000px and 901px
		itemsDesktopSmall : [900,5], // betweem 900px and 601px
		itemsTablet: [600,5], // 2 items between 600 and 0
		itemsMobile : [480,5],
		navigation: true,
		navigationText: [
			"<i class='icon-chevron-left icon-white'>&#9664;</i>",
			"<i class='icon-chevron-right icon-white'>&#9654;</i>"
		]
	});

	// item press
	$('.owl-carousel-press').owlCarousel({
		items : 1, // 1 item above 1000px browser width
		itemsDesktop : [1000,1], // 1 item between 1000px and 901px
		itemsDesktopSmall : [900,1], // betweem 900px and 601px
		itemsTablet: [600,1], // 1 item between 600 and 0
		itemsMobile : false, // itemsMobile disabled - inherit from itemsTablet option
		navigation: true,
		navigationText: [
			"<i class='icon-chevron-left icon-white'>&#9664;</i>",
			"<i class='icon-chevron-right icon-white'>&#9654;</i>"
		]
	});
	
});