<?php 


function ctshs_metaboxes() {
	
	// Get all post types 
	$args = array(
		'public'   => true
	);
	$post_types = get_post_types ( $args, 'names' );
	$exclude = array ( 'slider', 'slide', 'attachment' );
	$available = array_diff ( $post_types, $exclude );
	
	// Certain fields will only display with the pro version
	$ctshs_pro_dismissed = get_option ( 'ctshs_pro_dismissed', 0 );
	$show_upgrade = false;
	if ( ! function_exists ( 'ctshs_pro_load_plugin_textdomain' ) && $ctshs_pro_dismissed == 0 ) {
		$show_upgrade = true;
	}
	
	$metaboxes = array (
		array (
			'ID'			=> 'slider_background_metabox',
			'title'			=> __( 'Design Settings', 'super-hero-slider' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> array ( 'slider' ),
			'context'		=> 'normal',
			'priority'		=> 'default',
			'pro'			=> true,
			'fields'		=> array (
				array (
					'ID'		=> 'ctshs_background_image',
					'name'		=> 'ctshs_background_image',
					'title'		=> __( 'Background Image', 'super-hero-slider' ),
					'type'		=> 'image',
					'class'		=> 'ctshs_image ctshs-half'
				),
				array (
					'ID'		=> 'ctshs_background_color',
					'name'		=> 'ctshs_background_color',
					'title'		=> __( 'Background Color', 'super-hero-slider' ),
					'type'		=> 'color',
					'class'		=> 'ctshs_color ctshs-half ctshs-last'
				),
				array (
					'type'		=> 'divider'
				),
				array (
					'ID'		=> 'ctshs_padding_vertical',
					'name'		=> 'ctshs_padding_vertical',
					'title'		=> __( 'Padding Vertical', 'super-hero-slider' ),
					'type'		=> 'number',
					'default'	=> 0,
					'min'		=> 0,
					'max'		=> 5,
					'step'		=> 0.25,
					'class'		=> 'ctshs-first ctshs-half'
				),
				array (
					'ID'		=> 'ctshs_padding_horizontal',
					'name'		=> 'ctshs_padding_horizontal',
					'title'		=> __( 'Padding Horizontal', 'super-hero-slider' ),
					'type'		=> 'number',
					'default'	=> 0,
					'min'		=> 0,
					'max'		=> 5,
					'step'		=> 0.25,
					'class'		=> 'ctshs-last ctshs-half'
				),
				array (
					'type'		=> 'divider'
				),
				array (
					'ID'		=> 'ctshs_full_screen',
					'name'		=> 'ctshs_full_screen',
					'title'		=> __( 'Go Full Screen', 'super-hero-slider' ),
					'type'		=> 'checkbox',
					'default'	=> 0,
					'class'		=> 'ctshs-first ctshs-third',
					'pro'		=> true
				),
				array (
					'ID'		=> 'ctshs_full_screen_mode',
					'name'		=> 'ctshs_full_screen_mode',
					'title'		=> __( 'Full Screen Mode', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array (
					//	'background'	=> 'Background',
						'block'			=> 'Block',
						'overlay'		=> 'Overlay'
					),
					'default'	=> 'overlay',
					'class'		=> 'ctshs-third',
					'pro'		=> true
				),
				array (
					'ID'		=> 'ctshs_parallax_scroll',
					'name'		=> 'ctshs_parallax_scroll',
					'title'		=> __( 'Allow Parallax Scroll', 'super-hero-slider' ),
					'type'		=> 'checkbox',
					'default'	=> 0,
					'class'		=> 'ctshs-last ctshs-third',
					'pro'		=> true
				),
				/*
				array (
					'ID'		=> 'ctshs_transparent_elements',
					'name'		=> 'ctshs_transparent_elements',
					'title'		=> __( 'Set Classes to Transparent', 'super-hero-slider' ),
					'type'		=> 'text',
					'default'	=> '',
					'class'		=> 'ctshs-last ctshs-half',
					'pro'		=> true
				), */
				array (
					'type'		=> 'divider'
				),
				array (
					'ID'		=> 'ctshs_theme_font_styles',
					'name'		=> 'ctshs_theme_font_styles',
					'title'		=> __( 'Use Theme Font Styles', 'super-hero-slider' ),
					'type'		=> 'checkbox',
					'default'	=> 0,
					'class'		=> 'ctshs-first ctshs-half',
					'pro'		=> true
				),
			)
		),
		array (
			'ID'			=> 'slide_content_metabox',
			'title'			=> __( 'Slide Content', 'super-hero-slider' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> array ( 'slide' ),
			'context'		=> 'normal',
			'priority'		=> 'default',
			'fields'		=> array (
				array (
					'ID'		=> 'ctshs_slide_image',
					'name'		=> 'ctshs_slide_image',
					'title'		=> __( 'Slide Image', 'super-hero-slider' ),
					'type'		=> 'slide_image',
					'class'		=> 'ctshs_image ctshs_preview_image',
					'desc'		=> __( 'Preview has limited functionality at the moment and might vary from front-end display depending on slider settings and screen sizes. Ideally, your theme will support add_editor_style to enable front-end styles in the editor.', 'super-hero-slider' )
				),
				array (
					'type'		=> 'divider'
				),
				array (
					'ID'		=> 'ctshs_slide_caption_1',
					'name'		=> 'ctshs_slide_caption_1',
					'title'		=> __( 'Slide Caption 1', 'super-hero-slider' ),
					'type'		=> 'wysiwyg',
					'class'		=> 'ctshs_wysiwyg ctshs-first ctshs-half'
				),
				array (
					'ID'		=> 'ctshs_animation_caption_1',
					'name'		=> 'ctshs_animation_caption_1',
					'title'		=> __( 'Animation Caption 1', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array (
						'bounceIn'		 		=> 'bounceIn',
						'bounceInDown' 			=> 'bounceInDown',
						'bounceInLeft' 			=> 'bounceInLeft',
						'bounceInRight'			=> 'bounceInRight',
						'bounceInUp'			=> 'bounceInUp',
						'fadeIn'		 		=> 'fadeIn',
						'fadeInDown'		 	=> 'fadeInDown',
						'fadeInDownBig'		 	=> 'fadeInDownBig',
						'fadeInLeft'		 	=> 'fadeInLeft',
						'fadeInLeftBig'		 	=> 'fadeInLeftBig',
						'fadeInRight'		 	=> 'fadeInRight',
						'fadeInRightBig'		=> 'fadeInRightBig',
						'fadeInUp'		 		=> 'fadeInUp',
						'fadeInUpBig'		 	=> 'fadeInUpBig',
						'flipInX'		 		=> 'flipInX',
						'flipInY'		 		=> 'flipInY',
						'rotateIn'		 		=> 'rotateIn',
						'rotateInDownLeft'		=> 'rotateInDownLeft',
						'rotateInDownRight'		=> 'rotateInDownRight',
						'rotateInUpLeft'		=> 'rotateInUpLeft',
						'rotateInUpRight'		=> 'rotateInUpRight',
						'slideInDown'			=> 'slideInDown',
						'slideInLeft'		 	=> 'slideInLeft',
						'slideInRight'			=> 'slideInRight',
						'slideInUp'		 		=> 'slideInUp',
						'spinIn'				=> 'spinIn',
						'zoomIn'		 		=> 'zoomIn',
						'zoomInDown'		 	=> 'zoomInDown',
						'zoomInLeft'		 	=> 'zoomInLeft',
						'zoomInRight'		 	=> 'zoomInRight',
						'zoomInUp'		 		=> 'zoomInUp'
					),
					'default'	=> 'slideInUp',
					'class'		=> 'ctshs-half'
				),
				array (
					'ID'		=> 'ctshs_animation_delay_1',
					'name'		=> 'ctshs_animation_delay_1',
					'title'		=> __( 'Animation Delay 1', 'super-hero-slider' ),
					'type'		=> 'number',
					'default'	=> 1000,
					'min'		=> 0,
					'max'		=> 50000,
					'step'		=> 100,
					'class'		=> 'ctshs-half'
				),
				array (
					'ID'		=> 'ctshs_animation_speed_1',
					'name'		=> 'ctshs_animation_speed_1',
					'title'		=> __( 'Animation Speed 1', 'super-hero-slider' ),
					'type'		=> 'number',
					'default'	=> 500,
					'min'		=> 0,
					'max'		=> 5000,
					'step'		=> 100,
					'class'		=> 'ctshs-half',
					'pro'		=> true
				),
				array (
					'type'		=> 'divider'
				),
				array (
					'ID'		=> 'ctshs_slide_caption_2',
					'name'		=> 'ctshs_slide_caption_2',
					'title'		=> __( 'Slide Caption 2', 'super-hero-slider' ),
					'type'		=> 'wysiwyg',
					'class'		=> 'ctshs_wysiwyg ctshs-last ctshs-half'
				),
				array (
					'ID'		=> 'ctshs_animation_caption_2',
					'name'		=> 'ctshs_animation_caption_2',
					'title'		=> __( 'Animation Caption 2', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array (
						'bounceIn'		 		=> 'bounceIn',
						'bounceInDown' 			=> 'bounceInDown',
						'bounceInLeft' 			=> 'bounceInLeft',
						'bounceInRight'			=> 'bounceInRight',
						'bounceInUp'			=> 'bounceInUp',
						'fadeIn'		 		=> 'fadeIn',
						'fadeInDown'		 	=> 'fadeInDown',
						'fadeInDownBig'		 	=> 'fadeInDownBig',
						'fadeInLeft'		 	=> 'fadeInLeft',
						'fadeInLeftBig'		 	=> 'fadeInLeftBig',
						'fadeInRight'		 	=> 'fadeInRight',
						'fadeInRightBig'		=> 'fadeInRightBig',
						'fadeInUp'		 		=> 'fadeInUp',
						'fadeInUpBig'		 	=> 'fadeInUpBig',
						'flipInX'		 		=> 'flipInX',
						'flipInY'		 		=> 'flipInY',
						'rotateIn'		 		=> 'rotateIn',
						'rotateInDownLeft'		=> 'rotateInDownLeft',
						'rotateInDownRight'		=> 'rotateInDownRight',
						'rotateInUpLeft'		=> 'rotateInUpLeft',
						'rotateInUpRight'		=> 'rotateInUpRight',
						'slideInDown'			=> 'slideInDown',
						'slideInLeft'		 	=> 'slideInLeft',
						'slideInRight'			=> 'slideInRight',
						'slideInUp'		 		=> 'slideInUp',
						'spinIn'				=> 'spinIn',
						'zoomIn'		 		=> 'zoomIn',
						'zoomInDown'		 	=> 'zoomInDown',
						'zoomInLeft'		 	=> 'zoomInLeft',
						'zoomInRight'		 	=> 'zoomInRight',
						'zoomInUp'		 		=> 'zoomInUp'
					),
					'default'	=> 'slideInUp',
					'class'		=> 'ctshs-half'
				),
				array (
					'ID'		=> 'ctshs_animation_delay_2',
					'name'		=> 'ctshs_animation_delay_2',
					'title'		=> __( 'Animation Delay 2', 'super-hero-slider' ),
					'type'		=> 'number',
					'default'	=> 1000,
					'min'		=> 0,
					'max'		=> 50000,
					'step'		=> 100,
					'class'		=> 'ctshs-half'
				),
				array (
					'ID'		=> 'ctshs_animation_speed_2',
					'name'		=> 'ctshs_animation_speed_2',
					'title'		=> __( 'Animation Speed 2', 'super-hero-slider' ),
					'type'		=> 'number',
					'default'	=> 500,
					'min'		=> 0,
					'max'		=> 5000,
					'step'		=> 100,
					'class'		=> 'ctshs-half',
					'pro'		=> true
				),
				array (
					'type'		=> 'divider'
				),
				array (
					'ID'		=> 'ctshs_slide_caption_3',
					'name'		=> 'ctshs_slide_caption_3',
					'title'		=> __( 'Slide Caption 3', 'super-hero-slider' ),
					'type'		=> 'wysiwyg',
					'class'		=> 'ctshs_wysiwyg ctshs-last ctshs-half'
				),
				array (
					'ID'		=> 'ctshs_animation_caption_3',
					'name'		=> 'ctshs_animation_caption_3',
					'title'		=> __( 'Animation Caption 3', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array (
						'bounceIn'		 		=> 'bounceIn',
						'bounceInDown' 			=> 'bounceInDown',
						'bounceInLeft' 			=> 'bounceInLeft',
						'bounceInRight'			=> 'bounceInRight',
						'bounceInUp'			=> 'bounceInUp',
						'fadeIn'		 		=> 'fadeIn',
						'fadeInDown'		 	=> 'fadeInDown',
						'fadeInDownBig'		 	=> 'fadeInDownBig',
						'fadeInLeft'		 	=> 'fadeInLeft',
						'fadeInLeftBig'		 	=> 'fadeInLeftBig',
						'fadeInRight'		 	=> 'fadeInRight',
						'fadeInRightBig'		=> 'fadeInRightBig',
						'fadeInUp'		 		=> 'fadeInUp',
						'fadeInUpBig'		 	=> 'fadeInUpBig',
						'flipInX'		 		=> 'flipInX',
						'flipInY'		 		=> 'flipInY',
						'rotateIn'		 		=> 'rotateIn',
						'rotateInDownLeft'		=> 'rotateInDownLeft',
						'rotateInDownRight'		=> 'rotateInDownRight',
						'rotateInUpLeft'		=> 'rotateInUpLeft',
						'rotateInUpRight'		=> 'rotateInUpRight',
						'slideInDown'			=> 'slideInDown',
						'slideInLeft'		 	=> 'slideInLeft',
						'slideInRight'			=> 'slideInRight',
						'slideInUp'		 		=> 'slideInUp',
						'spinIn'				=> 'spinIn',
						'zoomIn'		 		=> 'zoomIn',
						'zoomInDown'		 	=> 'zoomInDown',
						'zoomInLeft'		 	=> 'zoomInLeft',
						'zoomInRight'		 	=> 'zoomInRight',
						'zoomInUp'		 		=> 'zoomInUp'
					),
					'default'	=> 'slideInUp',
					'class'		=> 'ctshs-half'
				),
				array (
					'ID'		=> 'ctshs_animation_delay_3',
					'name'		=> 'ctshs_animation_delay_3',
					'title'		=> __( 'Animation Delay 3', 'super-hero-slider' ),
					'type'		=> 'number',
					'default'	=> 1000,
					'min'		=> 0,
					'max'		=> 50000,
					'step'		=> 100,
					'class'		=> 'ctshs-half'
				),
				array (
					'ID'		=> 'ctshs_animation_speed_3',
					'name'		=> 'ctshs_animation_speed_3',
					'title'		=> __( 'Animation Speed 3', 'super-hero-slider' ),
					'type'		=> 'number',
					'default'	=> 500,
					'min'		=> 0,
					'max'		=> 5000,
					'step'		=> 100,
					'class'		=> 'ctshs-half',
					'pro'		=> true
				),
			)
		),
		array (
			'ID'			=> 'slide_settings_metabox',
			'title'			=> __( 'Slide Settings', 'super-hero-slider' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> array ( 'slide' ),
			'context'		=> 'side',
			'priority'		=> 'default',
			'fields'		=> array (
				array (
					'ID'		=> 'ctshs_slider',
					'name'		=> 'ctshs_slider',
					'title'		=> __( 'Slider', 'super-hero-slider' ),
					'type'		=> 'post',
					'multi'		=> true,
					'post-type'	=> array ( 'slider' ),
					'class'		=> 'ctshs_text_sidebar'
				),
				array (
					'ID'		=> 'ctshs_caption_position',
					'name'		=> 'ctshs_caption_position',
					'title'		=> __( 'Caption Position', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array ( 
						'top-left' => 'Top Left',
						'top-center' => 'Top Center',
						'top-right' => 'Top Right',
						'center-left' => 'Center Left',
						'center-center' => 'Center',
						'center-right' => 'Center Right',
						'bottom-left' => 'Bottom Left',
						'bottom-center' => 'Bottom Center',
						'bottom-right' => 'Bottom Right'
					),
					'default'	=> 'center-center',
					'class'		=> 'ctshs_text_sidebar'
				),
				array (
					'ID'		=> 'ctshs_caption_layout',
					'name'		=> 'ctshs_caption_layout',
					'title'		=> __( 'Caption Layout', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array ( 
						'horizontal ' => 'Horizontal',
						'vertical' => 'Vertical'
					),
					'default'	=> 'vertical',
					'class'		=> 'ctshs_text_sidebar'
				),
				array (
					'ID'		=> 'ctshs_caption_width',
					'name'		=> 'ctshs_caption_width',
					'title'		=> __( 'Caption Width', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array ( 
						'30'	=> 'Third',
						'50'	=> 'Half',
						'90'	=> 'Full'
					),
					'default'	=> '90',
					'class'		=> 'ctshs_text_sidebar'
				),
				array (
					'ID'		=> 'ctshs_image_size',
					'name'		=> 'ctshs_image_size',
					'title'		=> __( 'Image Size', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array ( 
						'auto ' => 'Auto',
						'full-width' => 'Full Width'
					),
					'default'	=> 'full-width',
					'class'		=> 'ctshs_text_sidebar'
				),
				array (
					'ID'		=> 'ctshs_linked_page',
					'name'		=> 'ctshs_linked_page',
					'title'		=> __( 'Link Slide To', 'super-hero-slider' ),
					'type'		=> 'post',
					'post-type'	=> $available,
					'default'	=> '',
					'class'		=> 'ctshs_text_sidebar'
				),
				array (
					'ID'		=> 'ctshs_custom_link',
					'name'		=> 'ctshs_custom_link',
					'title'		=> __( 'Custom Link (overrides above)', 'super-hero-slider' ),
					'type'		=> 'text',
					'default'	=> '',
					'class'		=> 'ctshs_text_sidebar'
				),
			)
		),
		array (
			'ID'			=> 'slider_settings_metabox',
			'title'			=> __( 'Slider Settings', 'super-hero-slider' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> array ( 'slider' ),
			'context'		=> 'side',
			'priority'		=> 'default',
			'fields'		=> array (
				array (
					'ID'		=> 'ctshs_slider_type',
					'name'		=> 'ctshs_slider_type',
					'title'		=> __( 'Content Type', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array (
						'std'				=> 'Standard',
						'posts'				=> 'Posts',
						'products'			=> 'Products (requires WooCommerce)'
					),
					'default'	=> 'std',				
					'class'		=> 'ctshs_text_sidebar',
					'pro'		=> true
				),
				array (
					'ID'		=> 'ctshs_slide_duration',
					'name'		=> 'ctshs_slide_duration',
					'title'		=> __( 'Slide Duration (ms)', 'super-hero-slider' ),
					'type'		=> 'number',
					'default'	=> 0,
					'min'		=> 0,
					'max'		=> 60000,
					'step'		=> 100,
					'class'		=> 'ctshs_text_sidebar'
				),
				array (
					'ID'		=> 'ctshs_animation_in',
					'name'		=> 'ctshs_animation_in',
					'title'		=> __( 'Animation', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array (
						'backSlide'				=> 'BackSlide',
						'fade'					=> 'Fade',
						'fadeLeft'				=> 'FadeLeft',
						'fadeUp'				=> 'FadeUp',
						'goDown'				=> 'GoDown',
						'swapLeft'				=> 'SwapLeft',
						'zoom'					=> 'Zoom'
					),
					'default'	=> 'backSlide',				
					'class'		=> 'ctshs_text_sidebar'
				),
				array (
					'ID'		=> 'ctshs_transition_duration',
					'name'		=> 'ctshs_transition_duration',
					'title'		=> __( 'Transition Duration (ms)', 'super-hero-slider' ),
					'type'		=> 'number',
					'default'	=> 700,
					'min'		=> 100,
					'max'		=> 60000,
					'step'		=> 100,
					'class'		=> 'ctshs_text_sidebar',
					'pro'		=> true
				),
				array (
					'ID'		=> 'ctshs_carousel_items',
					'name'		=> 'ctshs_carousel_items',
					'title'		=> __( 'Carousel Items', 'super-hero-slider' ),
					'type'		=> 'number',
					'default'	=> 1,
					'min'		=> 1,
					'max'		=> 16,
					'step'		=> 1,
					'class'		=> 'ctshs_text_sidebar',
					'pro'		=> true
				),
				array (
					'ID'		=> 'ctshs_carousel_spacing',
					'name'		=> 'ctshs_carousel_spacing',
					'title'		=> __( 'Carousel Item Spacing (px)', 'super-hero-slider' ),
					'type'		=> 'number',
					'default'	=> 0,
					'min'		=> 0,
					'max'		=> 25,
					'step'		=> 1,
					'class'		=> 'ctshs_text_sidebar',
					'pro'		=> true
				),
				array (
					'ID'		=> 'ctshs_spinner',
					'name'		=> 'ctshs_spinner',
					'title'		=> __( 'Include Loading Spinner?', 'super-hero-slider' ),
					'type'		=> 'checkbox',
					'label'		=> __( 'Add spinner', 'super-hero-slider' ),
					'default'	=> 1,
					'class'		=> ''
				),
				array (
					'ID'		=> 'ctshs_loading_image',
					'name'		=> 'ctshs_loading_image',
					'title'		=> __( 'Loading Image', 'super-hero-slider' ),
					'type'		=> 'image',
					'class'		=> '',
					'pro'		=> true
				),
			)
		),
		array (
			'ID'			=> 'navigation_settings_metabox',
			'title'			=> __( 'Navigation Settings', 'super-hero-slider' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> array ( 'slider' ),
			'context'		=> 'side',
			'priority'		=> 'default',
			'fields'		=> array (
				array (
					'ID'		=> 'ctshs_navigation_position',
					'name'		=> 'ctshs_navigation_position',
					'title'		=> __( 'Navigation Position', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array (
						'navigation-below' 			=> 'Below Slider',
						'navigation-bottom-right'	=> 'Bottom Right',
						'navigation-center'			=> 'Centered',
						'no-navigation'				=> 'Hide Buttons'
					),
					'default'	=> 'navigation-center',
					'class'		=> 'ctshs_text_sidebar'
				),
				array (
					'ID'		=> 'ctshs_navigation_style',
					'name'		=> 'ctshs_navigation_style',
					'title'		=> __( 'Navigation Style', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array (
						'dark-on-light' 	=> 'Light on Dark',
						'light-on-dark'		=> 'Dark on Light'
					),
					'default'	=> 'dark-on-light',				
					'class'		=> 'ctshs_text_sidebar'
				),
				array (
					'ID'		=> 'ctshs_navigation_buttons',
					'name'		=> 'ctshs_navigation_buttons',
					'title'		=> __( 'Navigation Buttons', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array (
						'arrow' 		=> 'Arrow',
						'caret'			=> 'Caret',
						'chevron'		=> 'Chevron'
					),
					'default'	=> 'caret',				
					'class'		=> 'ctshs_text_sidebar'
				),
				array (
					'ID'		=> 'ctshs_button_style',
					'name'		=> 'ctshs_button_style',
					'title'		=> __( 'Buttons Style', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array (
						'no-background' 		=> 'No Background',
						'circle'				=> 'Circle',
						'square'				=> 'Square',
						'circle-large'			=> 'Circle Large',
						'square-large'			=> 'Square Large'
					),
					'default'	=> 'square',				
					'class'		=> 'ctshs_text_sidebar'
				),
				array (
					'ID'		=> 'ctshs_dots_navigation',
					'name'		=> 'ctshs_dots_navigation',
					'title'		=> __( 'Pagination', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array (
						'false'					=> 'Hide',
						'true'					=> 'Display'
					),
					'default'	=> 'hide',				
					'class'		=> 'ctshs_text_sidebar'
				),
				array (
					'ID'		=> 'ctshs_thumbnail_navigation',
					'name'		=> 'ctshs_thumbnail_navigation',
					'title'		=> __( 'Thumbnail Navigation', 'super-hero-slider' ),
					'type'		=> 'select',
					'options'	=> array (
						'disabled'	=> 'Disabled',
						'bottom'	=> 'Bottom',
						'top'		=> 'Top',
						'left'		=> 'Left',
						'right'		=> 'Right'
					),
					'default'	=> 'disabled',
					'value'		=> 'thumbnail-navigation',
					'class'		=> 'ctshs_text_sidebar',
					'pro'		=> true
				),
			)
		),
		array(
			'ID'			=> 'slider_id_metabox',
			'title'			=> __( 'Super Hero Slider', 'super-hero-slider' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> $available,
			'context'		=> 'side',
			'priority'		=> 'default',
			'fields'		=> array (
				array (
					'ID'		=> 'ctshs_slide_id',
					'name'		=> 'ctshs_slide_id',
					'title'		=> __( 'Slider', 'super-hero-slider' ),
					'type'		=> 'post',
					'post-type'	=> array ( 'slider' ),
					'class'		=> 'ctshs_text_sidebar'
				),
				array (
					'ID'		=> 'ctshs_replace_featured_image',
					'name'		=> 'ctshs_replace_featured_image',
					'title'		=> __( 'Replace featured image?', 'super-hero-slider' ),
					'type'		=> 'checkbox',
					'label'		=> __( 'Replace the featured image on this page with the selected slider above', 'super-hero-slider' ),
					'default'	=> 0,
					'class'		=> ''
				),
			)
		)
	);
	
	if ( $show_upgrade ) {
		$metaboxes[] = array (
			'ID'			=> 'ctshs_go_pro_metabox',
			'title'			=> __( 'Go Pro', 'super-hero-slider' ),
			'callback'		=> 'meta_box_go_pro_callback',
			'screens'		=> array ( 'slide', 'slider' ),
			'context'		=> 'side',
			'priority'		=> 'high',
			'fields'		=> array (
				array (
					'ID'		=> 'ctshs_go_pro',
					'name'		=> 'ctshs_slider',
					'title'		=> __( 'Go Pro', 'super-hero-slider' ),
					'type'		=> 'upgrade',
					'content'	=> __( 'Super Hero Slider Pro offers amazing extra features, such as carousels, thumbnail navigation, recent posts slider and carousel, and additional animation options.', 'super-hero-slider' ),
					'class'		=> 'ctshs_text_sidebar',
					'options'	=> array (
						array (
							'text'		=> __( 'Show me more', 'super-hero-slider' ),
							'link'		=> 'https://catapultthemes.com/downloads/super-hero-slider-pro/',
							'class'		=> '',
							'target'	=> '_blank'
						),
						array (
							'text'		=> __( 'Dismiss this notice', 'super-hero-slider' ),
							'link'		=> '#',
							'class'		=> 'dismiss-ctshs-upgrade',
							'target'	=> '_self'
						)
					)
				)
			)
		);
	}

	return $metaboxes;
	
}