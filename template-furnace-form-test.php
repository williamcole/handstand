<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 * Template Name: Furnace Form Test
 */
?>

<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<link href="https://fonts.googleapis.com/css?family=Lato:400,900" rel="stylesheet">

<style>
	
	/* RESET HND STYLING */
	
	body {
		background: #f0911d;
	}
	
	#wrap {
		border: 0;
		box-shadow: 0;
		margin: 0;
		padding: 0;
		max-width: 100%;
		width: 100%;
	}
	
	#masterhead,
	#footer {	
		background: #f0911d;
		height: 60px;
	}
	
	#masterhead {
		background: #f0911d url('http://handstandrecords.com/wp-content/themes/handstand-2014/images/furnace/furnace-logo.png');
		background-position: center center;
		background-repeat: no-repeat;
	}
	
	#masterhead *,
	#footer * {
		display: none;
	}
	
	#masternav,
	#page-heading {
		display: none;
	}
	
	#main {
		background: #FFF;
		margin: 0 auto;
		padding: 2em 0;
		max-width: 50%;
	}
	
	@media only screen and (max-width: 800px) {
		#main {
			max-width: 80%;
		}
		.frm_section {
			width: 75%;
		}
	}
	
	
	/* CUSTOM */
	
	
	#frm_form_3_container {
		text-align: center;
	}
	
	.frm_section {
		background: #fff url('http://handstandrecords.com/wp-content/themes/handstand-2014/images/furnace/furnace-waveform.png');
		background-position: 50% 100%;
		background-repeat: no-repeat;
		border: 0;
		color: #2c2a2b;
		clear: both;
		display: block;
		font-family: 'Lato', sans-serif;
		font-size: 18px;
		font-weight: bold;
		letter-spacing: 1px;
		margin: 0 auto 1em;
		padding: 1.5em 0;
		text-align: center;
		text-transform: uppercase;
	}
	
	.frm_style_formidable-style.with_frm_style .frm_half {
		border: 0;
		margin: 0 auto;
		padding: 1em 0;
		max-width: 100%;
		width: 100%;
	}
	
	.frm_style_formidable-style.with_frm_style label.frm_primary_label,
	.frm_style_formidable-style.with_frm_style.frm_login_form label {
		color: #2a2829;
		font-family: 'Lato', sans-serif;
		font-size: 14px;
		font-weight: bold;
		letter-spacing: 1px;
		text-align: left;
		margin: 0;
		padding: 0 0 3px 0;
		width: auto;
		display: block;
	}
	
	.frm_style_formidable-style.with_frm_style .frm_required {
		color: #e03223;
		font-weight: bold;
	}
	
	.frm_style_formidable-style.with_frm_style .frm_checkbox,
	.frm_style_formidable-style.with_frm_style .frm_radio {
		text-align: left;
	}
	
	.frm_style_formidable-style.with_frm_style input[type=text], .frm_style_formidable-style.with_frm_style input[type=password], .frm_style_formidable-style.with_frm_style input[type=email], .frm_style_formidable-style.with_frm_style input[type=number], .frm_style_formidable-style.with_frm_style input[type=url], .frm_style_formidable-style.with_frm_style input[type=tel], .frm_style_formidable-style.with_frm_style input[type=file], .frm_style_formidable-style.with_frm_style input[type=search], .frm_style_formidable-style.with_frm_style select, .frm_style_formidable-style.with_frm_style textarea {
		border: 1px solid #CCC;
		font-family: 'Lato', sans-serif;
		line-height: 1.3;
	}
	
	.frm_grid_first .frm_radio label, .frm_grid .frm_radio label, .frm_grid_odd .frm_radio label, .frm_grid_first .frm_checkbox label, .frm_grid .frm_checkbox label, .frm_grid_odd .frm_checkbox label {
		visibility: visible;
	}
	
	.frm_style_formidable-style.with_frm_style .frm_submit button {
		background: #f2f2f2;
		background: -webkit-linear-gradient(#f2f2f2, #FFFFFA);
		background: -o-linear-gradient(#f2f2f2, #FFFFFA);
		background: -moz-linear-gradient(#f2f2f2, #FFFFFA);
		background: linear-gradient(#f2f2f2, #FFFFFA);

		border: 1px solid #CCC;
		border-radius: 4px;
		color: #666;
		font-family: 'Lato', sans-serif;
		font-size: 18px;
		font-weight: bold;
		letter-spacing: 1px;
		margin: 1em auto;
		padding: 0.5em 1em;
		text-align: center;
		text-transform: uppercase;
		-webkit-transition: background 0.5s;
		transition: background 0.5s;
	}
	.frm_style_formidable-style.with_frm_style .frm_submit button:hover {
		background: #FFFFFA;
		background: -webkit-linear-gradient(#FFFFFA, #f2f2f2);
		background: -o-linear-gradient(#FFFFFA, #f2f2f2);
		background: -moz-linear-gradient(#FFFFFA, #f2f2f2);
		background: linear-gradient(#FFFFFA, #f2f2f2);

		border: 1px solid #CCC;
		color: #666;
	}
	
	/* hide by default */
	#frm_field_14_container,
	#frm_field_15_container,
	#frm_field_16_container,
	#frm_field_17_container,
	#frm_field_18_container,
	#frm_field_19_container,
	#frm_field_20_container,
	#frm_field_21_container,
	#frm_field_22_container,
	#frm_field_23_container,
	#frm_field_24_container,
	#frm_field_25_container,
	#frm_field_26_container,
	#frm_field_27_container,
	#frm_field_28_container,
	#frm_field_29_container,
	#frm_field_30_container,
	#frm_field_31_container,
	#frm_field_32_container,
	#frm_field_33_container,
	#frm_field_34_container,
	#frm_field_35_container,
	#frm_field_36_container {
		/* display: none; */
	}
	

	
</style>

<script>

/* Custom javascript functionality for Custom Quote Form
-------------------------------------------------------------- */

jQuery(document).ready(function($) {
	
	/* DEFINE VARIABLES */
	
	// form id
	$form = $('#form_custom-quote');
	
	// basic data
	$quantity = $('#field_6md3x');
	$records_in_set = $('#field_wc3wv');
	$vinyl_format = $('#field_n43ua');
	
	// colors
	// $vinyl_color = $('#field_rky6x');
	
	
	
	
	// # //
	
	$color_selection = $('#frm_field_14_container');
	$color_variety = $('#frm_field_15_container');
	
	$sleeve_12inch_180g = $('#frm_field_17_container');
	$sleeve_12inch_150g = $('#frm_field_18_container');
	$sleeve_10inch = $('#frm_field_19_container');
	$sleeve_7inch = $('#frm_field_20_container');
	
	$packaging_12inch = $('#frm_field_21_container');
	$packaging_10inch = $('#frm_field_22_container');
	$packaging_7inch = $('#frm_field_23_container');
	
	$jacket_12inch = $('#frm_field_24_container');
	$jacket_7inch = $('#frm_field_25_container');
	$jacket_finishing = $('#frm_field_26_container');
	$jacket_board = $('#frm_field_27_container');
	
	$insert_12inch = $('#frm_field_28_container');
	$insert_7inch = $('#frm_field_29_container');
	$insert_printing = $('#frm_field_30_container');
	
	$center_label = $('#frm_field_16_container');
	$wrap_type = $('#frm_field_31_container');
	
	$extra_options = $('#frm_field_32_container');
	$sticker_options = $('#frm_field_33_container');
	
	$preprinted_sticker_checkbox = $('#field_ai2st-1');
	$preprinted_sticker_option = $('#frm_field_34_container');
	
	$custom_sticker_checkbox = $('#field_ai2st-0');
	$custom_sticker_option1 = $('#frm_field_35_container');
	$custom_sticker_option2 = $('#frm_field_36_container');
	
	$quantity_section = $('#frm_field_11_container');
	$contact_section = $('#frm_field_37_container');
	
	
	/* DEFINE HELPER FUNCTIONS */
	
	function test_function() {
		/*
		id = $track_list.first().attr('item');
		$item.first().addClass('active')
		load_item_disc();
		load_item_tracks();
		*/
	}
	
	
	/* FUNCTIONALITY */
	
	// form section titles
	$quantity_section.before('<div class="frm_section">Contact us for a custom quote</div>');
	$contact_section.before('<div class="frm_section">Tell us more about yourself</div>');
		
	// global form change logic
	$form.on('change', function() {
		
		/*
		
		// get format
		var vinyl_format = $vinyl_format.val();
		//console.log('FORMAT = ' + vinyl_format );
		
		if( vinyl_format == '' || vinyl_format == 'Choose One' ) {
			
			// hide a bunch of fields until basic parameters are selected
			
			$sleeve_12inch_180g.hide();
			$sleeve_12inch_150g.hide();
			$sleeve_10inch.hide();
			$sleeve_7inch.hide();
			
			$packaging_12inch.hide();
			$packaging_10inch.hide();
			$packaging_7inch.hide();
			
			$jacket_12inch.hide();
			$jacket_7inch.hide();
			$jacket_finishing.hide();
			$jacket_board.hide();
			
			$insert_12inch.hide();
			$insert_7inch.hide();
			
			$center_label.hide();
			$wrap_type.hide();
			$extra_options.hide();
			$sticker_options.hide();
			
			$preprinted_sticker_option.hide();
			$custom_sticker_option1.hide();
			$custom_sticker_option2.hide();
			
		} else {
		
			// show global options that aren't format-dependent
			
			$center_label.show();
			$wrap_type.show();
			$extra_options.show();
			$sticker_options.show();
		
		}
		
		
		// preprinted sticker
		if( $preprinted_sticker_checkbox.is(':checked') ) {
			$preprinted_sticker_option.show();
		} else {
			$preprinted_sticker_option.hide();
		}
		
		// custom sticker
		if( $custom_sticker_checkbox.is(':checked') ) {
			$custom_sticker_option1.show();
			$custom_sticker_option2.show();
		} else {
			$custom_sticker_option1.hide();
			$custom_sticker_option2.hide();
		}
		
		*/
	
	});
	
	// quantity
	$quantity.on('change', function() {
		//console.log('quantity = ' + $quantity.val() );
	});
	
	// vinyl format
	$vinyl_format.on('change', function() {
		
		/*
		var vinyl_format = $vinyl_format.val();
		//console.log('vinyl_format = ' + $vinyl_format.val() );
		
		// do this on form change
		if( vinyl_format == '' ) {
			
			$sleeve_12inch_180g.hide();
			$sleeve_12inch_150g.hide();
			$sleeve_10inch.hide();
			$sleeve_7inch.hide();
			
			$packaging_12inch.hide();
			$packaging_10inch.hide();
			$packaging_7inch.hide();
			
			$jacket_12inch.hide();
			$jacket_7inch.hide();
			$jacket_finishing.hide();
			$jacket_board.hide();
			
			$insert_12inch.hide();
			$insert_7inch.hide();
			
		} else if( vinyl_format == '10" - Standard weight' ) {
			
			$sleeve_12inch_180g.hide();
			$sleeve_12inch_150g.hide();
			$sleeve_10inch.show();
			$sleeve_7inch.hide();
			
			$packaging_12inch.hide();
			$packaging_10inch.show();
			$packaging_7inch.hide();
			
			$jacket_12inch.show();
			$jacket_7inch.hide();
			$jacket_finishing.show();
			$jacket_board.show();
			
			$insert_12inch.hide();
			$insert_7inch.hide();
			
		} else if( vinyl_format == '12" - 180g' ) {
			
			$sleeve_12inch_180g.show();
			$sleeve_12inch_150g.hide();
			$sleeve_10inch.hide();
			$sleeve_7inch.hide();
			
			$packaging_12inch.show();
			$packaging_10inch.hide();
			$packaging_7inch.hide();
		
			$jacket_12inch.show();
			$jacket_7inch.hide();
			$jacket_finishing.show();
			$jacket_board.show();
			
			$insert_12inch.show();
			$insert_7inch.hide();
		
		} else if( vinyl_format == '12" - 150g' ) {
		
			$sleeve_12inch_180g.hide();
			$sleeve_12inch_150g.show();
			$sleeve_10inch.hide();
			$sleeve_7inch.hide();
			
			$packaging_12inch.show();
			$packaging_10inch.hide();
			$packaging_7inch.hide();
		
			$jacket_12inch.show();
			$jacket_7inch.hide();
			$jacket_finishing.show();
			$jacket_board.show();
			
			$insert_12inch.show();
			$insert_7inch.hide();
		
		} else {
		
			$sleeve_12inch_180g.hide();
			$sleeve_12inch_150g.hide();
			$sleeve_10inch.hide();
			$sleeve_7inch.show();
			
			$packaging_12inch.hide();
			$packaging_10inch.hide();
			$packaging_7inch.show();
		
			$jacket_12inch.hide();
			$jacket_7inch.show();
			$jacket_finishing.show();
			$jacket_board.show();
			
			$insert_12inch.hide();
			$insert_7inch.show();
		
		}
		
		*/
		
	});
	
	/*
	
	// vinyl color
	$vinyl_color.on('change', function() {
		
		var vinyl_color = $vinyl_color.val();
		//console.log('vinyl_color = ' + $vinyl_color.val() );
		
		if( vinyl_color == 'All Black' ) {
			$color_variety.hide();
			$color_selection.hide();
		} else if( vinyl_color == 'All Color' ) {
			$color_variety.hide();
			$color_selection.show();
		} else if( vinyl_color == 'Color Variety' ) {
			$color_variety.show();
			$color_selection.hide();
		}
		
	});
	
	*/
	

});

</script>

<article>
    <header id="page-heading">
        <h1><?php the_title(); ?></h1>		
    </header>
    <!-- /page-heading -->
    
    <div class="post full-width clearfix">
   		<?php the_content(); ?>
    </div>
    <!-- /post -->
</article>
<?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>