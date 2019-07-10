/* Audio JS
-------------------------------------------------------------- */

jQuery(document).ready(function($) {

	/***********************************************************************************/
	/* Configuration */
	/***********************************************************************************/
	
	// define some variables
	var id = null,
		loaded = false,
		paused = false,
		playing = false;
	
	// player variables
	var $item = $('.owl-carousel-noise .carousel-item a'),
		$track_list = $('.track-list div.track'),
		$track = $('.track-name'),
		$disc = $('.disc-button'),
		$play = $('.haiku-play'),
		$stop = $('.haiku-stop'),
		$pause = $('.haiku-pause'),
		$arm = $('.turntable-arm');

	// rotation variables
	var arm_degree_min = 8,
		arm_degree_max = 32, // 24 for 7-inch
		arm_degree_current = 0,
		disc_degree_min = 0,
		disc_degree_max = 360,
		disc_degree_current = 0;
	
	/***********************************************************************************/
	/* Init */
	/***********************************************************************************/
	
	load_first_item();
	
	/***********************************************************************************/
	/* Click Events */
	/***********************************************************************************/
	
	// ITEM
	$item.on( 'click', function(e) {
	
		e.preventDefault();
		
		// get disc id
		id = $(this).attr('item');
		
		// ignore click if item is already loaded
		if( $(".turntable img[item='"+id+"']").is(':visible') ) {
			return false;
		} else {			
			$item.removeClass('active');	
			$(this).toggleClass('active');
			stop_all_tracks();
			load_new_item();		
		}		
	});

	// DISC
	$disc.on( 'click', function(e) {
	
		e.preventDefault();
		
		// get disc id
		id = $(this).attr('item');
		
		// ignore click if item is already playing
		if( playing ) {
			stop_all_tracks();
			arm_degree_current = 0;
		} else {			
			load_first_track();
			first_track = $('.track:visible').find('.track-name:first');
			start_track( first_track );
		}		
	});

	// TRACK
	$track.click(function(e){
		
		e.preventDefault();
		
		// add css to track player
		show_track_controls( $(this) );
		
		if( $(this).hasClass('active') ) {
		
			// track is already playing
			// stop this track
			stop_track( $(this) );
			
		} else {
		
			// stop all tracks
			// play new track		
			stop_all_tracks();
			start_track( $(this) );
			
		}
	});	
		
	// BUTTONS
	 
	$play.click(function(){
		if( !paused && !playing ) turntable_arm_on(); // dont move arm if unpausing
		start_disc();
		paused = false;
		playing = true;
	});
	
	$stop.click(function(){
		turntable_arm_off();
		stop_disc();
		paused = false;
		playing = false;
	});
	
	$pause.click(function(){
		stop_disc();
		paused = true;
		playing = false;
	});
	
	
	/***********************************************************************************/
	/* Helper Functions */
	/***********************************************************************************/
	
	// ITEM
	
	function load_new_item() {
		turntable_arm_off();
		clear_turntable();
		load_item_disc();
		load_item_tracks();
		load_first_track();
		arm_degree_current = 0;
	}
	
	function load_first_item() {
		id = $track_list.first().attr('item');
		$item.first().addClass('active')
		load_item_disc();
		load_item_tracks();		
	}

	// TRACK
	
	function load_first_track() {
		first_track = $('.track:visible').find('.track-name:first');
		first_track.addClass('active');
		first_track.parent('li').children('.haiku-graphical-container').show().css('height', '32px');
		//start_track( first_track );
	}
	
	function show_track_controls( this_track ) {
		this_track.parent('li').find('.haiku-graphical-container').css('height', '32px');
	}
	
	function start_track( this_track ) {
		this_track.addClass('active');
		this_track.parent('li').find('.haiku-graphical-container').slideDown();
		this_track.parent('li').find('.haiku-play').trigger('click');
	}
	
	function stop_track( this_track ) {
		this_track.removeClass('active');
		this_track.parent('li').find('.haiku-graphical-container').slideUp();
		this_track.parent('li').find('.haiku-stop').trigger('click');
	}

	function stop_current_track() {
		stop_track( $('.track-name.active') );
	}
	
	function stop_all_tracks() {
		$('.track-name.active').each(function() {
			stop_track( $(this) );
		});
	}

	function get_number_of_tracks() {
		return $('.track:visible').find('.track-name:last').parent('li').attr('track');
	}
	
	function get_degree_from_track( track_number ) {
		
		if( !track_number )
			return arm_degree_min;

		// hack for Olde Ghost 7"
		if( id == '1128' ) arm_degree_max = 24;

		// calculate number of degrees per track to increment
		total_tracks = get_number_of_tracks();
		track_diff = total_tracks - 1;
		degree_diff = arm_degree_max - arm_degree_min;
		each_track = degree_diff / track_diff;
		track_degree = arm_degree_min + ( ( track_number - 1 ) * each_track );

		return track_degree;
	}
	
	// TURNTABLE
	
	function turntable_arm_on() {

		// get track number and degree
		track_number = $('.track-name.active').parent('li').attr('track');
		degree = get_degree_from_track( track_number );
		
		// start rotation
		$arm.rotate({
			angle: arm_degree_current,
			center: ["50%", "7%"], 
			animateTo: degree
        });

		// set durrent degree
        arm_degree_current = degree;
    }
	
	function turntable_arm_off() {
	
		// stop rotation
		$arm.rotate({
			angle: arm_degree_current,
			center: ["50%", "7%"], 
			animateTo: 0
	    });
	}
	
	function clear_turntable() {
		
		// stop rotation
		setTimeout( $('.turntable img:visible').stopRotate(), 1000 );
		
		// fade out for now
		setTimeout( $('.turntable img:visible').fadeOut(), 1000 );
		
		// TODO: put record back in sleeve		
	}
	
	function start_disc() {		
		$('.turntable img:visible').rotate({
			angle: disc_degree_current, 
			animateTo: disc_degree_max, 
			callback: rotation,
			easing: function (x,t,b,c,d){
				return c*(t/d)+b;
			}
		});
	}
	
	function stop_disc() {
		$('.turntable img:visible').stopRotate({
			callback: function() {
				//console.log('STOP DISC');
			}
		});

		// set current disc degree
		disc_degree_current = $('.turntable img:visible').getRotateAngle();
	}
	
	// endless rotation function
	var rotation = function() {
		$('.turntable img:visible').rotate({
			angle: disc_degree_min, 
			animateTo: disc_degree_max, 
			callback: rotation,
			easing: function (x,t,b,c,d){ // t: current time, b: begInnIng value, c: change In value, d: duration
				return c*(t/d)+b;
			}
		});
	}
	
	/***********************************************************************************/
	
	function load_item_tracks() {		
		if( !$(".track-list div.track[item='"+id+"']").is(':visible') ) {
			$track_list.hide();
			$(".track-list div.track[item='"+id+"']").fadeIn();
		}
	}
	
	function load_item_disc() {
		$(".turntable img[item='"+id+"']").fadeIn();
	}
	
	function hide_tracks() {
		$('.haiku-button').slideUp();
		$('.player-container').slideUp();
	}
	
	function hide_track_controls() {
		$('.haiku-button, .player-container').css('display', 'none');	
	}	
	
	/***********************************************************************************/
	/* UNUSED AJAX FUNCTIONALITY */
	/***********************************************************************************/
	
	/* 
	
	// determine action and data to post
	function ajax_update_audio_data( item_id ) {

		if( !item_id )
			return;

		var ajax_data = {
			action: 'hnd_ajax_get_audio',
			item_id: item_id
		};

		$.post( ajax_object.ajax_url, ajax_data, function( response ) {
			//console.log( 'Got this from the server: ' + response );
			$('.track-list').html( response );
		});

	}
	
	//ajax_update_audio_data( 1128 ) );
	
	*/
		
});