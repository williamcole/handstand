/* Admin Toggle Custom Fields for Posts
-------------------------------------------------------------- */

jQuery(document).ready(function($) {
	
	// checkbox objects
	$events_box = $('#categorychecklist').find("label:contains('Events')").children('input:checkbox');
	$press_box = $('#categorychecklist').find("label:contains('Press')").children('input:checkbox');
	$notices_box = $('#categorychecklist').find("label:contains('Notices')").children('input:checkbox');
	
	// init
	toggle_event_fields();
	toggle_press_fields();
	toggle_notice_fields();
	
	// toogle custom field display
	$events_box.click(function() {
		toggle_event_fields();
	});	
	$press_box.click(function() {
		toggle_press_fields();
	});
	$notices_box.click(function() {
		toggle_notice_fields();
	});
	
	function toggle_event_fields() {
		if( $events_box.is(':checked') ) {
			$('#pods-meta-more-fields').find("label:contains('Event')").parent('th').parent('tr').show();
		} else {
			$('#pods-meta-more-fields').find("label:contains('Event')").parent('th').parent('tr').hide();
		}		
	}
	
	function toggle_press_fields() {
		if( $press_box.is(':checked') ) {
			$('#pods-meta-more-fields').find("label:contains('Press')").parent('th').parent('tr').show();
		} else {
			$('#pods-meta-more-fields').find("label:contains('Press')").parent('th').parent('tr').hide();
		}		
	}
	
	function toggle_notice_fields() {
		if( $notices_box.is(':checked') ) {
			$('#pods-meta-more-fields').find("label:contains('Notice')").parent('th').parent('tr').show();
		} else {
			$('#pods-meta-more-fields').find("label:contains('Notice')").parent('th').parent('tr').hide();
		}		
	}
	
});