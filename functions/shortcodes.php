<?php
/*
 * Super Hero Slider shortcode
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Prints the markup for the slider
 * @since 1.0.0
*/
function ctshs_super_hero_slider_shortcode ( $atts ) {
	
	$atts = shortcode_atts ( array (
		'slider'	=>	''
	), $atts );
	
	$output = ctshs_super_hero_slider ( $atts['slider'] );
	
	return $output;
	
}
add_shortcode ( 'super_hero_slider', 'ctshs_super_hero_slider_shortcode' );