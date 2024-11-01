<?php
/**
 * Functions for the featured image
 */

/**
 * Filter the post thumbnail html
 * @since 1.6.0
 * @return Array
 */
function ctshs_filter_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
	// Is image replacement enabled
	$replace = get_post_meta( $post_id, 'ctshs_replace_featured_image', true );
	if( ! $replace ) {
		return $html;
	}
	// If replacement is enabled then we need to get the ID of the selected slider
	$slider_id = get_post_meta( $post_id, 'ctshs_slide_id', true );
	if( $slider_id ) {
		$html = apply_filters( 'ctshs_open_featured_slider_wrapper', '<div class="ctshs_featured_image_wrapper">' );
		$html .= ctshs_super_hero_slider( $slider_id );
		$html .= apply_filters( 'ctshs_close_featured_slider_wrapper', '</div>' );
	}
	return $html; // Fallback
}
add_filter( 'post_thumbnail_html', 'ctshs_filter_post_thumbnail_html', 10, 5 );