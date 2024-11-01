<?php

/*
 * Called when we remove a slide from the slide list in the slider post type
 */
function ctshs_update_slide_list_callback() {
		
	$slide_id = intval ( $_POST['slide_id'] );
	$slider_id = intval ( $_POST['slider_id'] );
	
	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $slider_id ) ) {
		return;
	}
	
	check_ajax_referer ( 'update-slide-nonce', 'security' );
	
	$old_value = get_post_meta ( $slide_id, 'ctshs_slider', true ); // This is the list of sliders that the slide is assigned to
	// Iterate through this list to find the slider ID that we need to remove
	$response .= $old_value;
	$index = 0;
	$old_value = explode ( ',', $old_value );
	if ( $old_value ) {
		foreach ( $old_value as $k ) {
			$response .= $k . ',';
			if ( $k == $slider_id ) { // We have identified the slider we want to remove
				unset($old_value[$index]); // Remove it from the object
				$response .= $index;
			}
			$index++;
		}
	}
	// Reset the array keys
	$new_value = array_values ( $old_value );
	$new_value = join ( ',', $new_value );
	
	update_post_meta ( $slide_id, 'ctshs_slider', $new_value  );
	
	echo $response;
	
	wp_die();
	
}
add_action ( 'wp_ajax_ctshs_update_slide_list', 'ctshs_update_slide_list_callback' );

/*
 * Called when we remove a slider from the slider list in the slide post type
 */
function ctshs_update_slider_list_callback() {
		
	$slide_id = intval ( $_POST['slide_id'] );
	$slider_list = sanitize_text_field ( $_POST['slider_list'] );
	
	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $slide_id ) ) {
		return;
	}
	
	check_ajax_referer ( 'update-slider-nonce', 'security' );
	
	$old_value = get_post_meta ( $slide_id, 'ctshs_slider', true ); // This is the list of sliders that the slide is assigned to
	
	$old_array = explode ( ',', $old_value );
	$new_array = explode ( ',', $slider_list );
	// Gets the difference between the arrays and resets the element keys to be zero-based
	$difference = array_values ( array_diff ( $old_array, $new_array ) );
	$diff_id = $difference[0]; // This is the slider ID that was removed
	
	// From the slider ID, remove the slide from the list
	$slide_list = get_post_meta ( $diff_id, 'ctshs_slides_order', true );
	$slide_list_array = explode ( ',', $slide_list );
	$index = 0;
	if ( $slide_list_array ) {
		foreach ( $slide_list_array as $k ) {
		$response .= $k;
			if ( $k == 'post_' . $slide_id ) { // This is the slide to remove
				unset ( $slide_list_array[$index] );
				$response = $index;
			}
			$index++;
		}
		$new_slide_list = join ( ',', $slide_list_array ); // New list of slides
		update_post_meta ( $diff_id, 'ctshs_slides_order', $new_slide_list  );
	}
	
	echo $response;
	
	wp_die();
	
}
add_action ( 'wp_ajax_ctshs_update_slider_list', 'ctshs_update_slider_list_callback' );