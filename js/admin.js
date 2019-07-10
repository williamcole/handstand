/* Admin Scripts
-------------------------------------------------------------- */

jQuery(document).ready(function($) {
	
	/* Bulk Edit */
	$('.nav-filter-select').on('change', function() {
		
		// get taxonomy
		tax = $(this).attr('taxonomy');
		//console.log('tax ' + tax);
		
		// get value
		value = $(this).find('option:selected').val();
		console.log('value ' + value);

		$form = $(this).parent('div').parent('form');
		
		// set the value of the corresponding hidden input
		if( value !== '' ) {
			$form.find('input[name="' + tax + '"]').val( value );
		}

	});

	/*
	
	// parser that allows us to sort fields with input values
	$.tablesorter.addParser({ 
        id: "input",
        is: function(s) { 
            return false; 
        }, 
        format: function(s, t, node) {
            return $(node).children("input[type=checkbox]").is(':checked') ? 1 : 0;
        }, 
        type: "numeric" 
    });
	
	*/

	// table sorter
	$(".tablesorter").tablesorter({
		
		// default sort to first column
		sortList : [[0,0]],
		//headers : { "0": {"sorter" : "input" } }

		// sort on the first column and second column in ascending order
		//sortList: [[0,0],[1,0]]
	});
	

});