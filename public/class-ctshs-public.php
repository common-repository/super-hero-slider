<?php
/*
 * Super Hero Slider public class
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin public class
 **/
if ( ! class_exists( 'CT_SHS_Public' ) ) {

	class CT_SHS_Public {

		public function __construct() {
			//
		}
		
		/*
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.0
		 */
		public function init() {
			add_action ( 'init', array ( $this, 'register_post_type' ) );
			add_action ( 'wp_enqueue_scripts', array ( $this, 'enqueue_scripts' ) );
		}
		
		/*
		 * Register the post types and taxonomies
		 * @since 1.0.0
		 */
		public function register_post_type() {
			// Slider post type
			$labels = array (
				'name'					=> _x( 'Sliders', 'post type general name', 'ctshs' ),
				'singular_name'			=> _x( 'Slider', 'post type singular name', 'ctshs' ),
				'menu_name'				=> _x( 'Sliders', 'admin menu', 'ctshs' ),
				'name_admin_bar'		=> _x( 'Slider', 'add new on admin bar', 'ctshs' ),
				'add_new'				=> _x( 'Add New', 'Slider', 'ctshs' ),
				'add_new_item'			=> __( 'Add New Slider', 'ctshs' ),
				'new_item'				=> __( 'New Slider', 'ctshs' ),
				'edit_item'				=> __( 'Edit Slider', 'ctshs' ),
				'view_item'				=> __( 'View Slider', 'ctshs' ),
				'all_items'				=> __( 'All Sliders', 'ctshs' ),
				'search_items'			=> __( 'Search Sliders', 'ctshs' ),
				'parent_item_colon'		=> __( 'Parent Slider:', 'ctshs' ),
				'not_found'				=> __( 'No sliders found.', 'ctshs' ),
				'not_found_in_trash'	=> __( 'No sliders found in Trash.', 'ctshs' )
			);
			$args = array(
				'labels'				=> $labels,
				'description'			=> __( 'Description.', 'ctshs' ),
				'public'				=> false,
				'publicly_queryable'	=> false,
				'show_ui'				=> true,
				'show_in_menu'			=> true,
				'query_var'				=> true,
				'rewrite'				=> array( 'slug' => 'slider' ),
				'capability_type'		=> 'post',
				'menu_icon'				=> 'dashicons-format-gallery',
				'has_archive'			=> true,
				'hierarchical'			=> false,
				'menu_position'			=> 35,
				'supports'				=> array ( 'title' )
			);
			register_post_type( 'slider', $args );
			
			// Slide post type
			$labels = array (
				'name'					=> _x( 'Slides', 'post type general name', 'ctshs' ),
				'singular_name'			=> _x( 'Slide', 'post type singular name', 'ctshs' ),
				'menu_name'				=> _x( 'Slides', 'admin menu', 'ctshs' ),
				'name_admin_bar'		=> _x( 'Slide', 'add new on admin bar', 'ctshs' ),
				'add_new'				=> _x( 'Add New', 'Slide', 'ctshs' ),
				'add_new_item'			=> __( 'Add New Slide', 'ctshs' ),
				'new_item'				=> __( 'New Slide', 'ctshs' ),
				'edit_item'				=> __( 'Edit Slide', 'ctshs' ),
				'view_item'				=> __( 'View Slide', 'ctshs' ),
				'all_items'				=> __( 'Slides', 'ctshs' ),
				'search_items'			=> __( 'Search Slides', 'ctshs' ),
				'parent_item_colon'		=> __( 'Parent Slide:', 'ctshs' ),
				'not_found'				=> __( 'No slides found.', 'ctshs' ),
				'not_found_in_trash'	=> __( 'No slides found in Trash.', 'ctshs' )
			);
			$args = array(
				'labels'				=> $labels,
				'description'			=> __( 'Description.', 'ctshs' ),
				'public'				=> false,
				'publicly_queryable'	=> false,
				'show_ui'				=> true,
				'show_in_menu' 			=> true,
				'query_var'				=> true,
				'rewrite'				=> array( 'slug' => 'slide' ),
				'capability_type'		=> 'post',
				'menu_icon'				=> 'dashicons-format-image',
				'has_archive'			=> true,
				'hierarchical'			=> false,
				'menu_position'			=> 35,
				'supports'				=> array ( 'title' )
			);
			register_post_type( 'slide', $args );

		}
	
		/*
		 * Enqueue styles and scripts
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_style ( 'super-hero-style', SHS_PLUGIN_URL . 'assets/css/superherostyle.css' );
			wp_enqueue_style ( 'dashicons' );
			wp_register_script ( 'superhero-script',  SHS_PLUGIN_URL . 'assets/js/superhero.js', array ( 'jquery' ), '1.1.0', true );
			wp_register_script ( 'imagesloaded', SHS_PLUGIN_URL . 'assets/js/imagesloaded.pkgd.min.js', array ( 'jquery', 'superhero-script' ), '4.1.0', true );
			wp_enqueue_script ( 'superheroslider-script',  SHS_PLUGIN_URL . 'assets/js/superheroslider.js', array ( 'imagesloaded', 'superhero-script' ), '1.0.0', true );
		}
		
		/*
		 * Register the widgets
		 * @since 1.0.0
		 */
		public function widgets_init() {
			//	register_widget ( '' );
		}

	}
	
}