<?php
/*
 * Super Hero Slider admin class
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Plugin public class
 **/
if ( ! class_exists( 'CT_SHS_Admin' ) ) {

	class CT_SHS_Admin {
		public function __construct() {
		}
		
		/*
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.4
		 */
		public function init() {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
			add_action( 'admin_init', array( $this, 'register_options_media_init' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_filter( 'manage_slide_posts_columns', array( $this, 'add_thumb_column' ) );
			add_action( 'manage_slide_posts_custom_column', array(  $this, 'thumb_column_content' ), 5, 2 );
			add_filter( 'manage_slider_posts_columns', array( $this, 'add_id_column' ) );
			add_action( 'manage_slider_posts_custom_column', array(  $this, 'id_column_content' ), 5, 2 );
			
			add_action( 'after_setup_theme', array( $this, 'add_image_size' ) );
		}
		
		public function enqueue_scripts() {
			wp_enqueue_style ( 'ctcc-admin-style', SHS_PLUGIN_URL . 'assets/css/admin-style.css' );
		}
		
		/**
		 * We save this artificially to let the tracker know that we're allowed to export this option's data
		 */
		public function save_registered_setting() {
			$options = get_option( 'ctshs_media_settings' );
			$options['wisdom_registered_setting'] = 1;
			update_option( 'ctshs_media_settings', $options );
		}
		
		// Add the menu item
		public function add_admin_menu() {
			add_options_page ( __( 'Super Hero Slider', 'super-hero-slider' ), __( 'Super Hero Slider', 'super-hero-slider' ), 'manage_options', 'super-hero-slider', array( $this, 'options_page' ) );
		}
		
		public function register_options_media_init() {
			register_setting ( 'ctshs_media', 'ctshs_media_settings' );
			
			add_settings_section (
				'ctshs_media_section', 
				__( 'Media settings', 'super-hero-slider' ), 
				array ( $this, 'media_settings_section_callback' ), 
				'ctshs_media'
			);
			
			add_settings_field ( 
				'image_width', 
				__( 'Image width', 'super-hero-slider' ), 
				array ( $this, 'image_width_render' ),
				'ctshs_media', 
				'ctshs_media_section'
			);
			
			add_settings_field ( 
				'default_image_size', 
				__( 'Default image size', 'super-hero-slider' ), 
				array ( $this, 'default_image_size_render' ),
				'ctshs_media', 
				'ctshs_media_section'
			);
			
			add_settings_field ( 
				'wisdom_opt_out', 
				__( 'Opt out of tracking', 'super-hero-slider' ), 
				array ( $this, 'wisdom_opt_out_render' ),
				'ctshs_media', 
				'ctshs_media_section'
			);
			
			// Set default options
			$options = get_option ( 'ctshs_media_settings' );
			if ( false === $options ) {
				// Get defaults
				$defaults = $this -> get_default_media_settings();
				update_option ( 'ctshs_media_settings', $defaults );
			}
			
		}
		
		/*
		 * Defaults
		 */
		public function get_default_media_settings() {
			$defaults = array (
				'image_width'			=> 1920,
				'default_image_size'	=> 'large'
			);
			return $defaults;
		}
		
		public function image_width_render() {
			$options = get_option( 'ctshs_media_settings' );
			?>
			<input type='number' min="0" name='ctshs_media_settings[image_width]' value='<?php echo $options['image_width']; ?>'>
			<p class="description"><?php _e( 'Set the image width in pixels for slider images. Enter 0 to disable this image size. Use the setting below to specify which image size to use.', 'super-hero-slider' ); ?></p>
			<?php
		}
		
		public function default_image_size_render() {
			$options = get_option( 'ctshs_media_settings' );
			$sizes = get_intermediate_image_sizes();
			if( ! empty( $sizes ) ) { ?>
				<select name='ctshs_media_settings[default_image_size]'>
					<?php foreach( $sizes as $size ) { ?>
						<option value='<?php echo $size; ?>' <?php selected( $options['default_image_size'], $size ); ?>><?php echo $size; ?></option>
					<?php } ?>
				</select>
			<p class="description"><?php _e( 'Set the default image size to use in your sliders.', 'super-hero-slider' ); ?></p>
			<?php }
		}
		
		public function wisdom_opt_out_render() {
			$options = get_option( 'ctshs_media_settings' );
			$selected = ! empty( $options['wisdom_opt_out'] ) ? 1 : 0; ?>
			<input type='checkbox' name='ctshs_media_settings[wisdom_opt_out]' value='1' <?php checked( 1, $selected, true ); ?>>
			<p class="description"><?php _e( 'If you have previously opted into anonymous tracking data, you can opt out here', 'super-hero-slider' ); ?></p>
			<?php
		}
		
		// Callback for General settings
		public function media_settings_section_callback() { ?>
			<p>
				<?php echo __( 'Media options for your sliders.', 'super-hero-slider' ); ?>
			</p>
		<?php
		}
		
		public function options_page() {
	
			$current = isset ( $_GET['tab'] ) ? $_GET['tab'] : 'media';
			$title =  __( 'Super Hero Slider', 'super-hero-slider' );
			$tabs = array (
				'media'			=>	__( 'Media', 'super-hero-slider' ),
			//	'structure'		=>	__( 'Structure', 'super-hero-slider' ),
			//	'styles'		=>	__( 'Styles', 'super-hero-slider' )
			); 
			$tabs = apply_filters( 'ctshs_admin_tabs', $tabs );
			?>
		
			<div class="wrap">
				<h1><?php echo $title; ?></h1>
				<div class="ctdb-outer-wrap">
					<div class="ctdb-inner-wrap">
						<h2 class="nav-tab-wrapper">
							<?php foreach( $tabs as $tab => $name ) {
								$class = ( $tab == $current ) ? ' nav-tab-active' : '';
								echo "<a class='nav-tab $class' href='?page=super-hero-slider&tab=$tab'>$name</a>";
							} ?>
						</h2>
						<form action='options.php' method='post'>
							<?php if( $current != 'license' ) {
								settings_fields( 'ctshs_' . $current );
								do_settings_sections( 'ctshs_' . $current );
								submit_button();
							} else {
								if( function_exists( 'ct_shs_pro_license_page' ) ) {
									ct_shs_pro_license_page();
								}
							}
							?>
						</form>
					</div><!-- .ctdb-inner-wrap -->
					<div class="ctdb-banners">
						<div class="ctdb-banner ctshs-banner-ad">
							<a href="http://superheroslider.catapultthemes.com/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=super-hero-slider&utm_campaign=campaign"><img src="<?php echo SHS_PLUGIN_URL . 'assets/images/superherosliderpro.png'; ?>" alt="" ></a>
						</div>
						<div class="ctdb-banner">
							<a target="_blank" href="https://catapultthemes.com/downloads/showcase/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=super-hero-slider&utm_campaign=showcase"><img src="<?php echo SHS_PLUGIN_URL . 'assets/images/showcase-banner-ad.jpg'; ?>" alt="" ></a>
						</div>
						<div class="ctdb-banner hide-dbpro">
							<a target="_blank" href="https://discussionboard.pro/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=super-hero-slider&utm_campaign=dbpro"><img src="<?php echo SHS_PLUGIN_URL . 'assets/images/discussion-board-banner-ad.png'; ?>" alt="" ></a>
						</div>
						<div class="ctdb-banner">
							<a target="_blank" href="https://catapultthemes.com/downloads/bookings-for-woocommerce/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=super-hero-slider&utm_campaign=bookings"><img src="<?php echo SHS_PLUGIN_URL . 'assets/images/bookings-for-woocommerce-banner-ad.jpg'; ?>" alt="" ></a>
						</div>
					</div>
				</div><!-- .ctdb-outer-wrap -->
			</div><!-- .wrap -->
			<?php
		}
		
		
		public function add_thumb_column( $columns ) {
		   $columns['thumb'] = __( 'Image', 'super-hero-slider' );
		   return $columns;
		}
		
		function thumb_column_content( $column, $post_id ) {
			switch ( $column ) {
				case 'thumb' :
					$thumb = wp_get_attachment_image_src( get_post_meta( $post_id, 'ctshs_slide_image', true ) );
					if( ! empty( $thumb ) ) {
						echo '<img height="75" src="' . esc_url( $thumb[0] ) . '">';
					}
					break;
			}
		}
		
		public function add_id_column( $columns ) {
		   $columns['id'] = __( 'Slider ID', 'super-hero-slider' );
		   return $columns;
		}
		
		public function id_column_content( $column, $post_id ) {
			switch ( $column ) {
				case 'id' :
					echo $post_id;
					break;
			}
		}
		
		/*
		 * Set super-hero-image size
		 * @since 1.5.1
		 */
		public function add_image_size() {
			$options = get_option( 'ctshs_media_settings' );
			if( isset( $options['image_width'] ) && $options['image_width'] > 0 ) {
				add_image_size( 'super-hero-image', absint( $options['image_width'] ), 9999, false );
			}
		}
		
	}
	
}