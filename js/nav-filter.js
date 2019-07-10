/* Navigation Filter JS
-------------------------------------------------------------- */

jQuery(document).ready(function($) {
	
	// redirect nav links
	$('.nav-filter-select').on('change', function() {
		link = $(this).find('option:selected').val();
		if( link !== '' ) {
			window.location.href = link;
		}
	});

});