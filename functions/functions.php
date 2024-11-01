<?php
/*
 * Super Hero Slider functions
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Prints the markup for the slider
 * @since 1.0.0
*/
if ( ! function_exists ( 'super_hero_slider' ) ) {
	function super_hero_slider ( $slider_id=null ) {
		echo ctshs_super_hero_slider ( $slider_id );
	}
}

/*
 * Returns the markup for the slider
 * @since 1.0.0
*/
// @ToDo Save output to transient
// Refresh transient on post edit
function ctshs_super_hero_slider ( $slider_id=null ) {

	global $post;

	$output = '';
	$filtered_output = '';

	// If the slider ID isn't set, we can look to see if there's one in the page/post meta field
	if ( ! isset ( $slider_id ) || $slider_id == '' ) {
		$slider_id = get_post_meta ( get_the_ID(), 'ctshs_slide_id', true );
	}

	// If we still can't find a slider ID: well, at least we tried
	if ( empty ( $slider_id ) || "slider" != get_post_type ( $slider_id ) ) {
		$output = __( 'You haven\'t specified a valid slider ID.', 'super-hero-slider' );
		return $output;
	}

	// Get our array of slides
	$all_slides = ctshs_slide_order ( $slider_id );

	// Now we should be able to build our slider, assuming we actually have some slides in it
	if ( count ( $all_slides ) > 0 ) {

		// Get classes and options for the slider
		$wrapper_styles = ctshs_wrapper_styles ( $slider_id );
		$slider_classes = ctshs_slider_classes ( $slider_id );
		$slider_styles = ctshs_slider_styles ( $slider_id );

		// Add inline style declarations
		$style_declarations = ctshs_style_declarations ( $slider_id );
		if ( $style_declarations ) {
			$output .= '<style type="text/css">';
			foreach ( $style_declarations as $style ) {
				$output .= esc_attr ( $style );
			}
			$output .= '</style>';
		}
		// Check for a loading spinner
		$has_spinner = get_post_meta ( $slider_id, 'ctshs_spinner', true );
		$wrapper_class = 'super-hero-big-wrapper';
		if ( $has_spinner == 1 ) {
			$wrapper_class .= ' super-hero-spinning';
		}

		$output .= '<div class="' . $wrapper_class . '" style="' . esc_attr ( join ( ';', $wrapper_styles ) ) . '">';

		if ( $has_spinner == 1 ) {
			$output .= ctshs_loading_spinner ( $slider_id );
		}

		// @ToDo Add styles here, set negative margins on carousels to compensate for carousel item spacing
		$output .= '<div id="slider-' . $slider_id . '" class="' . esc_attr ( join ( ' ', $slider_classes ) ) . '" style="' . esc_attr ( join ( ';', $slider_styles ) ) . '">';

		foreach ( $all_slides as $slide ) {

			// Get permalink for the slide
			$slide_permalink = ctshs_slide_permalink ( $slider_id, $slide );
			$close_a = '';

			// Get slide-specific classes
			$slide_classes = ctshs_slide_classes ( $slider_id, $slide );
			$slide_styles = ctshs_slide_styles ( $slider_id, $slide );

			$output .= '<div id="slide-'  . $slider_id . '-' . $slide . '" class="' . esc_attr ( join ( ' ', $slide_classes ) ) . '" style="' . esc_attr ( join ( ';', $slide_styles ) ) . '">';

			if ( $slide_permalink != '' ) {
				$output .= '<a class="super-hero-link" href="' . esc_url ( $slide_permalink ) . '">';
				$close_a = '</a>'; // We'll add this before closing the wrapper
			}

			// Add the image
			// Set image size, @since 1.5.1
			$media_options = get_option( 'ctshs_media_settings' );
			$image_size = 'large';
			if( isset( $media_options['default_image_size'] ) ) {
				$image_size = esc_attr( $media_options['default_image_size'] );
			}
			$output .= ctshs_image ( $slider_id, $slide, $image_size );

			$output .= $close_a;

			// Add the caption
			$output .= ctshs_captions ( $slider_id, $slide );

			$output .= '</div><!-- .super-hero-slide -->';

		}

		$output .= '</div><!-- .super-hero-slider -->';
		$output .= '</div><!-- .big -->';

		// We can filter this content here
		$filtered_output = apply_filters ( 'ctshs_after_slider_output', $output, $all_slides, $slider_id );

		// Add the script
		$filtered_output .= ctshs_slider_script ( $slider_id );


	} else {

		$filtered_output = __( 'There are no slides in your slider.', 'super-hero-slider' );

	}

	return $filtered_output;

}

/*
 * Return an array of slide IDs for the slider
 * @since 1.0.0
 */
function ctshs_slide_order ( $slider_id ) {

	// Get the slide order for the slide
	$slides = get_post_meta ( $slider_id, 'ctshs_slides_order', true );

	$all_slides = ctshs_slide_ids ( $slides, $slider_id );
	// Tidy up the array, remove empty elements
	$all_slides = array_filter ( $all_slides );

	// Make a list of slides to save as meta data
	$all_slides_list = '';
	if ( ! empty ( $all_slides ) ) {
		foreach ( $all_slides as $slide ) {
			$all_slides_list .= 'post_' . $slide . ',';
		}
	}

	// Save our list of slides to the slider
	update_post_meta ( $slider_id, 'ctshs_slides_order', trim ( $all_slides_list, ',' ) );

	$all_slides = apply_filters ( 'super_hero_slider_slide_order', $all_slides, $slider_id );

	return $all_slides;

}

/*
 * Return an array of slide IDs for the slider
 * @since 1.0.0
 */
function ctshs_slide_ids ( $slides, $slider_id ) {

	global $post;

	if ( ! empty ( $slides ) ) {
		$slide_ids = str_replace ( 'post_', '', $slides );
		$all_slides = explode ( ',', $slide_ids );
		$all_slides_list = $slides;
	} else {
		$slide_ids = '';
		$all_slides = array();
		$all_slides_list = '';
	}

	// Check if slides have been allocated but the slider hasn't been saved
	$args = array (
		'post_type'			=> 'slide',
		'posts_per_page'	=> -1,
		// Find slides in this slider only
		'meta_query'		=> array (
			array (
				'key'		=> 'ctshs_slider',
				'value'		=> $slider_id,
				'compare'	=> 'LIKE'
			)
		)
	);

	$missing_slides = new WP_Query ( $args );

	if ( $missing_slides -> have_posts() ) {
		while ( $missing_slides -> have_posts() ): $missing_slides -> the_post();

			$slider_list = get_post_meta ( $post -> ID, 'ctshs_slider', true );

			if ( ! empty ( $slider_list ) ) {

				$pluck_ids = str_replace ( 'post_', '', $slider_list );
				$pluck = explode ( ',', $pluck_ids );

				// Double check that the ID number is actually in the slider ID array
				if ( in_array ( $slider_id, $pluck ) ) {

					if ( ! in_array ( $post -> ID, $all_slides ) ) {
						$all_slides[] = $post -> ID; // Add the slide ID to our array of slides
					}

				}
			}

		endwhile;
	}

	wp_reset_query();

	$all_slides = apply_filters ( 'super_hero_slider_slide_ids', $all_slides );

	return $all_slides;

}

/*
 * Add the image
 * @since 1.0.0
 */
function ctshs_image ( $slider_id, $slide, $size ) {

	// 	@ToDo : set image as background?
	//	$output .= '<div class="super-hero-image" style="background-image:url(' . ctshs_slider_image_url ( $slide ) . ');" >';
	$image = '';

	$image .= '<div class="super-hero-image">';
		$image .= ctshs_slider_image ( $slide, $size );
	$image .= '</div><!-- .super-hero-image -->';


	// Add the image
	$image = apply_filters ( 'super_hero_slider_image', $image, $slider_id, $slide, $size );

	return $image;

}

/*
 * Return the markup for the slide image
 * @since 1.0.0
 */
function ctshs_slider_image ( $slide, $size='large' ) {

	if ( ! isset ( $slide ) ) return;

	$image_id = get_post_meta ( $slide, 'ctshs_slide_image', true );
	$img_src = wp_get_attachment_image_src ( $image_id, $size );
	// Check that image size exists
	// If not return 'large' size
	// @since 1.5.1
	if( ! $img_src[3] ) {
		$img_src = wp_get_attachment_image_src ( $image_id, 'large' );
	}
	$image = '<img src="' . esc_url ( $img_src[0] ) . '">';

	return $image;

}

/*
 * Return the URL for the slide image
 * @since 1.0.0
 * NOT USED
 */
function ctshs_slider_image_url ( $slide ) {

	if ( ! isset ( $slide ) ) return;

	$image_id = get_post_meta ( $slide, 'ctshs_slide_image', true );
	$img_src = wp_get_attachment_image_src ( $image_id, 'large' );
	$image_url = $img_src[0];

	return $image_url;

}

/*
 * Return the navigation options for the slider
 * @since 1.0.0
 */
function ctshs_navigation_options ( $slider_id ) {

	if ( ! isset ( $slider_id ) ) return;

	$navigation_options = array();

	// Theme
	$navigation_options['theme'] = array (
		'type'			=> 'string',
		'option'		=> 'theme',
		'value'			=> 'shs-theme'
	);
	// Base class
	$navigation_options['baseClass'] = array (
		'type'			=> 'string',
		'option'		=> 'baseClass',
		'value'			=> 'shs-carousel'
	);
	$navigation_options['singleItem'] = array (
		'type'			=> 'boolean',
		'option'		=> 'singleItem',
		'value'			=> 'true'
	);
	$navigation_options['addClassActive'] = array (
		'type'			=> 'boolean',
		'option'		=> 'addClassActive',
		'value'			=> 'true'
	);

	// Animations
	$navigation_options['transitionStyle'] = array (
		'type'			=> 'string',
		'option'		=> 'transitionStyle',
		'value'			=> get_post_meta ( $slider_id, 'ctshs_animation_in', true )
	);

	// Auto Play
	$slide_duration = get_post_meta ( $slider_id, 'ctshs_slide_duration', true );
	if ( empty ( $slide_duration ) || $slide_duration == 0 ) {
		$navigation_options['autoPlay'] = array (
			'type'			=> 'boolean',
			'option'		=> 'autoPlay',
			'value'			=> 'false'
		);
	} else {
		$navigation_options['autoPlay'] = array (
			'type'			=> 'int',
			'option'		=> 'autoPlay',
			'value'			=> get_post_meta ( $slider_id, 'ctshs_slide_duration', true )
		);
	}

	// Get the icon
	$icons = array (
		'arrow'			=> array (
			'"<span class=\'dashicons dashicons-arrow-left-alt\'></span>"',
			'"<span class=\'dashicons dashicons-arrow-right-alt\'></span>"'
		),
		'caret'			=> array (
			'"<span class=\'dashicons dashicons-arrow-left\'></span>"',
			'"<span class=\'dashicons dashicons-arrow-right\'></span>"'
		),
		'chevron'		=> array (
			'"<span class=\'dashicons dashicons-arrow-left-alt2\'></span>"',
			'"<span class=\'dashicons dashicons-arrow-right-alt2\'></span>"'
		)

	);

	// Show navigation
	$navigation_options['navigation'] = array (
		'type'			=> 'boolean',
		'option'		=> 'navigation',
		'value'			=> 'true'
	);

	if ( get_post_meta ( $slider_id, 'ctshs_navigation_position', true ) == 'no-navigation' ) {
		$navigation_options['navigation'] = array (
			'type'			=> 'boolean',
			'option'		=> 'navigation',
			'value'			=> 'false'
		);
	}

	// Style of navigation button
	$nav_button = get_post_meta ( $slider_id, 'ctshs_navigation_buttons', true );
	$icon = $icons[$nav_button];
	$navigation_options['navigationText'] = array (
		'type'			=> 'array',
		'option'		=> 'navigationText',
		'value'			=> array (
			$icon[0],
			$icon[1]
		)
	);

	// Show pagination
	$navigation_options['pagination'] = array (
		'type'			=> 'boolean',
		'option'		=> 'pagination',
		'value'			=> get_post_meta ( $slider_id, 'ctshs_dots_navigation', true )
	);

	// Add the callbacks
	$navigation_options['beforeInit'] = array (
		'type'			=> 'callback',
		'option'		=> 'beforeInit',
		'value'			=> 'function(el){
						var slides = el.find(".super-hero-slide").length;
					}'
	);
	// Add the callbacks
	$navigation_options['afterInit'] = array (
		'type'			=> 'callback',
		'option'		=> 'afterInit',
		'value'			=> 'function(el){
						var currentItem = el.find(".shs-item.active");
						var itemID = "#"+currentItem.find(".super-hero-slide").attr("ID");
						currentItem.find(".super-hero-caption-wrapper").addClass("fadeIn animated");
						var currentCaption1 = currentItem.find(".caption-1");
						var captionAnimation1 = currentCaption1.data("animation");
						currentCaption1.addClass(captionAnimation1 + " animated");
						var currentCaption2 = currentItem.find(".caption-2");
						var captionAnimation2 = currentCaption2.data("animation");
						currentCaption2.addClass(captionAnimation2 + " animated");
						var currentCaption3 = currentItem.find(".caption-3");
						var captionAnimation3 = currentCaption3.data("animation");
						currentCaption3.addClass(captionAnimation3 + " animated");
						// Clear any styles
						shs_resize_slide( itemID, currentItem );
					}'
	);
	$navigation_options['beforeMove'] = array (
		'type'			=> 'callback',
		'option'		=> 'beforeMove',
		'value'			=> 'function(el){
						var oldItem = el.find(".shs-item.active");
						var itemID = "#"+oldItem.find(".super-hero-slide").attr("ID");
						oldItem.find(".super-hero-caption-wrapper").removeClass("fadeIn animated");
						var oldCaption1 = oldItem.find(".caption-1");
						var oldCaption2 = oldItem.find(".caption-2");
						var oldCaption3 = oldItem.find(".caption-3");
						var captionAnimation1 = oldCaption1.data("animation");
						oldCaption1.removeClass(captionAnimation1);
						oldCaption1.removeClass("animated");
						var captionAnimation2 = oldCaption2.data("animation");
						oldCaption2.removeClass(captionAnimation2);
						oldCaption2.removeClass("animated");
						var captionAnimation3 = oldCaption3.data("animation");
						oldCaption3.removeClass(captionAnimation3);
						oldCaption3.removeClass("animated");
						// Remove any styles we might have added in afterMove
						$(itemID+" .super-hero-image img").removeAttr("style");
					}'
	);
	$navigation_options['afterMove'] = array (
		'type'			=> 'callback',
		'option'		=> 'afterMove',
		'value'			=> 'function(el){
						var newItem = el.find(".shs-item.active");
						var itemID = "#"+newItem.find(".super-hero-slide").attr("ID");
						newItem.find(".super-hero-caption-wrapper").addClass("fadeIn animated").css("animation-duration",5);
						var newCaption1 = newItem.find(".caption-1");
						var newAnimation1 = newCaption1.data("animation");
						newCaption1.addClass(newAnimation1 + " animated");
						var newCaption2 = newItem.find(".caption-2");
						var newAnimation2 = newCaption2.data("animation");
						newCaption2.addClass(newAnimation2 + " animated");
						var newCaption3 = newItem.find(".caption-3");
						var newAnimation3 = newCaption3.data("animation");
						newCaption3.addClass(newAnimation3 + " animated");
						// Clear any styles
						shs_resize_slide( itemID, newItem );
					}'
	);

	// Apply filters
	$navigation_options = apply_filters ( 'super_hero_slider_navigation_options', $navigation_options, $slider_id );

	return $navigation_options;

}

/*
 * Return the navigation classes for the slider
 * @since 1.0.0
 */
function ctshs_slider_classes ( $slider_id ) {

	if ( ! isset ( $slider_id ) ) return;

	$slider_classes = array();

	$slider_classes['super-hero-slider'] = 'super-hero-slider';
	$slider_classes['shs-carousel'] = 'shs-carousel';

	// Get the background style
	$slider_classes['background'] = 'button-' . get_post_meta ( $slider_id, 'ctshs_button_style', true );
	$slider_classes['position'] = get_post_meta ( $slider_id, 'ctshs_navigation_position', true );
	$slider_classes['style'] = get_post_meta ( $slider_id, 'ctshs_navigation_style', true );

	$slider_classes = apply_filters ( 'super_hero_slider_slider_classes', $slider_classes, $slider_id );

	return $slider_classes;

}

/*
 * Return the navigation styles for the slider wrapper
 * @since 1.0.0
 * @renamed from ctshs_slider_styles to ctshs_wrapper_styles 1.3.0
 */
function ctshs_wrapper_styles ( $slider_id ) {

	if ( ! isset ( $slider_id ) ) return;

	$wrapper_styles = array();

	$wrapper_styles = apply_filters ( 'super_hero_slider_wrapper_styles', $wrapper_styles, $slider_id );

	return $wrapper_styles;

}


/*
 * Return the navigation styles for the slider
 * @since 1.3.0
 */
function ctshs_slider_styles ( $slider_id ) {

	if ( ! isset ( $slider_id ) ) return;

	$slider_styles = array();

	$slider_styles = apply_filters ( 'super_hero_slider_slider_styles', $slider_styles, $slider_id );

	return $slider_styles;

}

/*
 * Return classes for each slide
 * @since 1.0.0
 */
function ctshs_slide_classes ( $slider_id, $slide ) {

	if ( ! isset ( $slide ) ) return;

	$slide_classes = array();

	$slide_classes['super-hero-slide'] = 'super-hero-slide';

	if ( get_post_meta ( $slide, 'ctshs_image_size', true ) == 'full-width' ) {
		$slide_classes['full-width'] = 'full-width';
	}

	// If we've picked horizontal layout
	if ( get_post_meta ( $slide, 'ctshs_caption_layout', true ) == 'horizontal' ) {
		$slide_classes['layout'] = 'horizontal-layout';
	}

	$slide_classes = apply_filters ( 'super_hero_slider_slide_classes', $slide_classes, $slider_id, $slide );

	return $slide_classes;

}

/*
 * Return styles for each slide
 * @since 1.0.0
 */
function ctshs_slide_styles ( $slider_id, $slide ) {

	if ( ! isset ( $slide ) ) return;

	$slide_styles = array();

	$slide_styles = apply_filters ( 'super_hero_slider_slide_styles', $slide_styles, $slider_id, $slide );

	return $slide_styles;

}

/*
 * Return the linked page for each slide
 * @since 1.0.0
 */
function ctshs_slide_permalink ( $slider_id, $slide_id ) {

	$permalink = '';

	$page = get_post_meta ( $slide_id, 'ctshs_linked_page', true );
	$custom = get_post_meta ( $slide_id, 'ctshs_custom_link', true );
	if ( isset ( $custom ) && $custom != '' ) {
		$permalink = esc_url ( $custom );
	} else if ( isset ( $page ) && $page != '' ) {
		$permalink = get_permalink ( $page );
	}

	// Apply filters
	$permalink = apply_filters ( 'ctshs_slide_permalink', $permalink, $slider_id, $slide_id );

	return $permalink;

}

/*
 * Return the options for the captions
 * @since 1.0.0
 */
function ctshs_captions ( $slider_id, $slide ) {

	$captions = '';

	$slide_caption_1 = get_post_meta ( $slide, 'ctshs_slide_caption_1', true );
	$slide_caption_2 = get_post_meta ( $slide, 'ctshs_slide_caption_2', true );
	$slide_caption_3 = get_post_meta ( $slide, 'ctshs_slide_caption_3', true );

	// Allow us to filter the captions
	$slide_caption_1 = apply_filters ( 'super_hero_slider_caption_1', $slide_caption_1, $slider_id, $slide );
	$slide_caption_2 = apply_filters ( 'super_hero_slider_caption_2', $slide_caption_2, $slider_id, $slide );
	$slide_caption_3 = apply_filters ( 'super_hero_slider_caption_3', $slide_caption_3, $slider_id, $slide );

	if ( ! empty ( $slide_caption_1 ) || ! empty ( $slide_caption_2 ) || ! empty ( $slide_caption_3 ) ) {

		$caption_styles = ctshs_caption_styles ( $slider_id, $slide );
		$caption_classes = ctshs_caption_classes ( $slider_id, $slide );
		$caption_data = ctshs_caption_data ( $slider_id, $slide );

		$captions .= '<div class="' . esc_attr ( join ( ' ', $caption_classes['wrapper'] ) ) . '" style="' . esc_attr ( join ( ';',  $caption_styles['wrapper'] ) ) . '">';
			$captions .= '<div class="super-hero-caption">';
				// Caption 1
				if ( ! empty ( $slide_caption_1 ) ) {
					$data = '';
					if ( ! empty ( $caption_data[0] ) ) {
						foreach ( $caption_data[0] as $key => $value ) {
							$data .= 'data-' . $key . '="' . $value . '"';
						}
					}
					$captions .= '<div class="' . esc_attr ( join ( ' ', $caption_classes['caption_1'] ) ) . '" style="' . esc_attr ( join ( ';', $caption_styles['caption_1'] ) ) . '" data-animation="' . esc_attr ( $caption_styles['animation_1'] ) . '" ' . $data . '>' . do_shortcode ( $slide_caption_1 ) . '</div>';
				}

				// Caption 2
				if ( ! empty ( $slide_caption_2 ) ) {
					$data = '';
					if ( ! empty ( $caption_data[1] ) ) {
						foreach ( $caption_data[1] as $key => $value ) {
							$data .= 'data-' . $key . '="' . $value . '"';
						}
					}
					$captions .= '<div class="' . esc_attr ( join ( ' ', $caption_classes['caption_2'] ) ) . '" style="' . esc_attr ( join ( ';', $caption_styles['caption_2'] ) ) . '" data-animation="' . esc_attr ( $caption_styles['animation_2'] ) . '" ' . $data . '>' . do_shortcode ( $slide_caption_2 ) . '</div>';
				}

				// Caption 3
				if ( ! empty ( $slide_caption_3 ) ) {
					$data = '';
					if ( ! empty ( $caption_data[2] ) ) {
						foreach ( $caption_data[2] as $key => $value ) {
							$data .= 'data-' . $key . '="' . $value . '"';
						}
					}
					$captions .= '<div class="' . esc_attr ( join ( ' ', $caption_classes['caption_3'] ) ) . '" style="' . esc_attr ( join ( ';', $caption_styles['caption_3'] ) ) . '" data-animation="' . esc_attr ( $caption_styles['animation_3'] ) . '" ' . $data . '>' . do_shortcode ( $slide_caption_3 ) . '</div>';
				}

			$captions .= '</div><!-- super-hero-caption -->';
		$captions .= '</div><!-- super-hero-caption-wrapper -->';

	}

	$captions = apply_filters ( 'super_hero_slider_captions', $captions );

	return $captions;

}

/*
 * Return the classes for the captions
 * @since 1.0.0
 */
function ctshs_caption_classes ( $slider_id, $slide ) {

	$caption_classes = array();
	$caption_classes['wrapper'] = array (
		'super-hero-caption-wrapper',
		get_post_meta( $slide, 'ctshs_caption_position', true ),
		get_post_meta( $slide, 'ctshs_caption_layout', true )
	);
	$caption_classes['caption_1'] = array (
		'super-hero-caption',
		'caption-1'
	);
	$caption_classes['caption_2'] = array (
		'super-hero-caption',
		'caption-2'
	);
	$caption_classes['caption_3'] = array (
		'super-hero-caption',
		'caption-3'
	);

	$caption_classes = apply_filters ( 'super_hero_slider_caption_classes', $caption_classes, $slider_id, $slide );

	return $caption_classes;

}

/*
 * Return data for the captions
 * @since 1.1.0
 */
function ctshs_caption_data ( $slider_id, $slide ) {

	$caption_data = array();

	$caption_data = apply_filters ( 'super_hero_slider_caption_data', $caption_data, $slider_id, $slide );

	return $caption_data;

}

/*
 * Return the options for the captions
 * @since 1.0.0
 */
function ctshs_caption_styles ( $slider, $slide ) {

	$caption_styles = array();
	$caption_styles['wrapper'] = array();
	$caption_styles['caption_1'] = array();
	$caption_styles['caption_2'] = array();
	$caption_styles['caption_3'] = array();
	$caption_styles['animation_1'] = get_post_meta ( $slide, 'ctshs_animation_caption_1', true );
	$caption_styles['animation_2'] = get_post_meta ( $slide, 'ctshs_animation_caption_2', true );
	$caption_styles['animation_3'] = get_post_meta ( $slide, 'ctshs_animation_caption_3', true );

	$animation_delay_1 = get_post_meta ( $slide, 'ctshs_animation_delay_1', true );
	if ( isset ( $animation_delay_1 ) ) {
		// We need to add the same animation delay to the wrapper in order to ensure the fade is synced
		$caption_styles['wrapper']['webkit-animation-delay'] = '-webkit-animation-delay:' . $animation_delay_1 . 'ms';
		$caption_styles['wrapper']['animation-delay'] = 'animation-delay:' . $animation_delay_1 . 'ms';
		$caption_styles['caption_1']['webkit-animation-delay'] = '-webkit-animation-delay:' . $animation_delay_1 . 'ms';
		$caption_styles['caption_1']['animation-delay'] = 'animation-delay:' . $animation_delay_1 . 'ms';
	}

	$animation_delay_2 = get_post_meta ( $slide, 'ctshs_animation_delay_2', true );
	if ( isset ( $animation_delay_2 ) ) {
		$caption_styles['caption_2']['webkit-animation-delay'] = '-webkit-animation-delay:' . $animation_delay_2 . 'ms';
		$caption_styles['caption_2']['animation-delay'] = 'animation-delay:' . $animation_delay_2 . 'ms';
	}

	$animation_delay_3 = get_post_meta ( $slide, 'ctshs_animation_delay_3', true );
	if ( isset ( $animation_delay_3 ) ) {
		$caption_styles['caption_3']['webkit-animation-delay'] = '-webkit-animation-delay:' . $animation_delay_3 . 'ms';
		$caption_styles['caption_3']['animation-delay'] = 'animation-delay:' . $animation_delay_3 . 'ms';
	}

	$caption_width = get_post_meta ( $slide, 'ctshs_caption_width', true );
	if ( isset ( $caption_width ) ) {
		$caption_styles['wrapper']['width'] = 'width:' . $caption_width . '%';
		$caption_styles['wrapper']['max-width'] = 'max-width:' . $caption_width . '%';
	}

	$caption_styles = apply_filters ( 'super_hero_slider_caption_styles', $caption_styles, $slider, $slide );

	return $caption_styles;

}

/*
 * Prints the script for the slider
 * @since 1.0.0
*/
function ctshs_slider_script ( $slider_id ) {

	$navigation_options = ctshs_navigation_options ( $slider_id );
	$options = '';

	// Build the options
	foreach ( $navigation_options as $option ) {

		if ( $option['type'] == 'array' ) {
			$options .= $option['option'] . ':[';
			$options .= join ( ',', $option['value'] );
			$options .= '],';
		} else if ( $option['type'] == 'boolean' || $option['type'] == 'int' ) {
			$options .= $option['option'] . ':' . $option['value'] . ',';
		} else if ( $option['type'] == 'callback' ) {
			$options .= $option['option'] . ':' . $option['value'] . ',';
		} else {
			$options .= $option['option'] . ':"' . $option['value'] . '",';
		}

	}

	$script = '<script>jQuery(document).ready(function($){';

	// @since 1.0.2 Added imagesloaded
	$script .= 'var shs' . $slider_id . ' = $("#slider-' . $slider_id . '");
			shs' . $slider_id . '.imagesLoaded(function(){
				shs' . $slider_id . '.superHeroSlider({' . $options . '});
				$(".super-hero-big-wrapper").removeClass("super-hero-spinning");
				$(".shs-spinner").fadeOut(150,function(){
					$(this).remove();
				});
				$("body").addClass("shs-loaded");
			});
			$(window).resize(function(){
				shs' . $slider_id . '.data("superHeroSlider").destroy();
				shs' . $slider_id . '.superHeroSlider({' . $options . '});
			});';

	// We can add some extra script in here if we want
	$filtered_script = apply_filters ( 'super_hero_slider_script', $script, $slider_id );

	$filtered_script .= '});</script>';

	return $filtered_script;

}

/*
 * Add a loading spinner
 * @since 1.2.0
*/
function ctshs_loading_spinner ( $slider_id ) {

	$spinner = '<div class="shs-spinner-wrapper"><div class="shs-spinner"></div></div>';

	$spinner = apply_filters ( 'super_hero_slider_spinner', $spinner, $slider_id );

	return $spinner;
}


/*
 * Add style declarations to header
 * @since 1.1.0
 */
function ctshs_style_declarations ( $slider_id ) {

	$styles = array();

	$styles = apply_filters ( 'super_hero_slider_style_declarations', $styles, $slider_id );

	return $styles;

}
