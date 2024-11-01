<?php
/*
 * Super Hero Slider functions for specific themes
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Prints the markup for the slider
 * @since 1.0.0
*/
if ( ! function_exists( 'shs_get_theme_template' ) ) {
	function shs_get_theme_template() {
		$theme = wp_get_theme();
		return $theme->get( 'Template' );
	}
}
add_action( 'plugins_loaded', 'shs_get_theme_template' );

/**
 * You need to add a filter to your child theme's version of print_thumbnail for this to work for Divi
 * @since 1.6.1
 */
function shs_filter_divi_featured_slider( $output ) {

	$post_id = get_the_ID();
	// Is image replacement enabled
	$replace = get_post_meta( $post_id, 'ctshs_replace_featured_image', true );
	if( ! $replace ) {
		return $output;
	}

	if( ! is_single() ) {
	//	return $output;
	}

	// If replacement is enabled then we need to get the ID of the selected slider
	$slider_id = get_post_meta( $post_id, 'ctshs_slide_id', true );
	if( $slider_id ) {
		$output = apply_filters( 'ctshs_open_featured_slider_wrapper', '<div class="ctshs_featured_image_wrapper">' );
		$output .= ctshs_super_hero_slider( $slider_id );
		$output .= apply_filters( 'ctshs_close_featured_slider_wrapper', '</div>' );
	}
	return $output; // Fallback
}
add_filter( 'shs_filter_divi_thumbnail', 'shs_filter_divi_featured_slider' );
