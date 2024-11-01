<?php
/*
 * Super Hero Slider metaboxes
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin public class
 **/
if ( ! class_exists( 'CT_SHS_Metaboxes' ) ) {

	class CT_SHS_Metaboxes {

		public $metaboxes;

		public function __construct ( $metaboxes ) {
			$this -> metaboxes = $metaboxes;
		}

		/*
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.0
		 */
		public function init() {
			add_action ( 'admin_enqueue_scripts', array ( $this, 'enqueue_scripts' ) );
			add_action ( 'add_meta_boxes', array ( $this, 'add_meta_box' ) );
			add_action ( 'save_post', array ( $this, 'save_metabox_data' ) );
			add_action ( 'admin_footer', array ( $this, 'detect_slider_change' ) );
			add_action ( 'wp_ajax_ctshs_dismiss_notice', array ( $this, 'ctshs_dismiss_notice' ) );

			add_filter ( 'postbox_classes_slider_slider_products_feed_metabox', array ( $this, 'add_products_postbox_class' ) );
			add_filter ( 'postbox_classes_slider_slider_products_feed_content_metabox', array ( $this, 'add_products_postbox_class' ) );
			add_filter ( 'postbox_classes_slider_slide_products_feed_content_metabox', array ( $this, 'add_products_postbox_class' ) );
			add_filter ( 'postbox_classes_slider_slide_products_feed_animation_metabox', array ( $this, 'add_products_postbox_class' ) );
			add_filter ( 'postbox_classes_slider_slide_products_advanced_metabox', array ( $this, 'add_products_postbox_class' ) );

			add_filter ( 'postbox_classes_slider_slider_posts_feed_metabox', array ( $this, 'add_posts_postbox_class' ) );
			add_filter ( 'postbox_classes_slider_slider_posts_feed_content_metabox', array ( $this, 'add_posts_postbox_class' ) );
		}

		/*
		 * Register the metabox
		 * @since 1.0.0
		 */
		public function add_meta_box() {

			$screens = array ( 'slider' );

			// Slide order - this is special so we do it separately
			add_meta_box (
				'slider_metabox',
				__( 'Slides', 'super-hero-slider' ),
				array ( $this, 'slide_order_callback' ),
				$screens
			);

			// Preview - this is special so we do it separately
			add_meta_box (
				'slider_preview',
				__( 'Preview', 'super-hero-slider' ),
				array ( $this, 'slider_preview' ),
				$screens
			);

			$metaboxes = $this -> metaboxes;

			// Certain metaboxes will only display with the pro version
			$is_pro = false;
			if ( function_exists ( 'ctshs_pro_load_plugin_textdomain' ) ) {
				$is_pro = true;
			}

			foreach ( $metaboxes as $metabox ) {

				// And some metaboxes have other dependencies, e.g. require WooCommerce
				// @since 1.1.0
				$is_allowed = true;
				if ( isset ( $metabox['dependency'] ) ) {
					$is_allowed = false;
					switch ( $metabox['dependency'] ) {
						case 'is_woocommerce':
							$is_allowed = $this -> is_woocommerce();
							break;
					}
				}

				//	Is it a pro field?
				$pro_metabox = isset ( $metabox['pro'] );

				// Only display field if it's not a pro field or if pro is enabled
				if ( ( ! $pro_metabox || $is_pro ) && $is_allowed ) {

					add_meta_box (
						$metabox['ID'],
						$metabox['title'],
						array ( $this, $metabox['callback'] ),
						$metabox['screens'],
						$metabox['context'],
						$metabox['priority'],
						$metabox['fields']
					);

				}

			}

		}

		/*
		 * Add section to upgrade to Pro
		 * @since 1.0.0
		*/
		public function meta_box_go_pro_callback ( $post, $fields ) {
			if ( $fields['args'] ) {

				foreach ( $fields['args'] as $field ) {

						switch ( $field['type'] ) {
							case 'upgrade':
								$this -> metabox_upgrade_output ( $post, $field );
								break;
						}

				}
			}

		}

		/*
		 * Metabox callback for slide order
		 * @since 1.0.0
		*/
		public function meta_box_callback ( $post, $fields ) {

			wp_nonce_field ( 'save_metabox_data', 'ctshs_metabox_nonce' );

			// Certain fields will only display with the pro version
			$is_pro = false;
			if ( function_exists ( 'ctshs_pro_load_plugin_textdomain' ) ) {
				$is_pro = true;
			}

			if ( $fields['args'] ) {

				foreach ( $fields['args'] as $field ) {

					//	Is it a pro field?
					$pro_field = isset ( $field['pro'] );
					// Only display field if it's not a pro field or if pro is enabled
					if ( ! $pro_field || $is_pro ) {

						switch ( $field['type'] ) {

							case 'text':
								$this -> metabox_text_output ( $post, $field );
								break;
							case 'number':
								$this -> metabox_number_output ( $post, $field );
								break;
							case 'select':
								$this -> metabox_select_output ( $post, $field );
								break;
							case 'multi-select':
								$this -> metabox_multi_select_output ( $post, $field );
								break;
							case 'checkbox':
								$this -> metabox_checkbox_output ( $post, $field );
								break;
							case 'slide_image':
								$this -> metabox_slide_image_output ( $post, $field );
								break;
							case 'image':
								$this -> metabox_image_output ( $post, $field );
								break;
							case 'color':
								$this -> metabox_color_output ( $post, $field );
								break;
							case 'wysiwyg':
								$this -> metabox_wysiwyg_output ( $post, $field );
								break;
							case 'post':
								if ( isset ( $field['multi'] ) ) {
									$this -> metabox_post_multi_output ( $post, $field );
									break;
								} else {
									$this -> metabox_post_output ( $post, $field );
									break;
								}
							case 'taxonomy':
								if ( isset ( $field['multi'] ) ) {
									$this -> metabox_taxonomy_multi_output ( $post, $field );
									break;
								} else {
									$this -> metabox_taxonomy_output ( $post, $field );
									break;
								}
							case 'divider':
								$this -> metabox_divider_output();
								break;
							case 'html':
								$this -> metabox_html_output ( $field );
								break;

						}

					}

				}

			}

		}

		/*
		 * Metabox callback for text type
		 * @since 1.0.0
		 */
		public function metabox_upgrade_output( $post, $field ) {
			?>
			<div class="ctshs_metafield <?php echo $field['class']; ?>">
				<a href="https://catapultthemes.com/downloads/super-hero-slider-pro/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=super-hero-slider&utm_campaign=campaign"><img src="<?php echo SHS_PLUGIN_URL . 'assets/images/superherosliderpro.png'; ?>" alt="" ></a>
				<?php echo $field['content'];
				if ( ! empty ( $field['options'] ) ) {
					echo '<ul>';
					foreach ( $field['options'] as $option ) {
						echo '<li><a class="' . esc_attr ( $option['class'] ) . '" href="' . esc_url ( $option['link'] ) . '" target="' . esc_attr ( $option['target'] ) . '">' . $option['text'] . '</a></li>';
					}
					echo '</ul>';
				} ?>
				<script>
					jQuery(document).ready(function($){
						$('.dismiss-ctshs-upgrade').on('click',function(e){
							e.preventDefault();
							var data = {
								action: 'ctshs_dismiss_notice'
							};
							$.post (
								'<?php echo admin_url ( 'admin-ajax.php' ); ?>',
								data,
								function ( response ) {
									if(response=='updated'){
										$('#ctshs_go_pro_metabox').fadeOut();
									}
								}
							);
						});
					});
				</script>
			</div>
			<?php
		}

		/*
		 * Metabox callback for text type
		 * @since 1.0.0
		 */
		public function ctshs_dismiss_notice( ) {
			update_option ( 'ctshs_pro_dismissed', 1 );
			echo 'updated';
			wp_die();
		}

		/*
		 * Metabox callback for text type
		 * @since 1.0.0
		 */
		public function metabox_text_output( $post, $field ) {

			$value = get_post_meta ( $post -> ID, $field['ID'], true );

			?>
			<div class="ctshs_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<input type="text" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo esc_attr ( $value ); ?>" >
			</div>
			<?php
		}

		/*
		 * Metabox callback for wysiwyg type
		 * @since 1.0.0
		 */
		public function metabox_wysiwyg_output( $post, $field ) {

			$value = get_post_meta ( $post -> ID, $field['ID'], true );

			?>
			<div class="ctshs_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<?php wp_editor (
					htmlspecialchars_decode ( $value),
					$field['name'],
					array (
						"media_buttons" => false,
						"textarea_rows" => 5,
						"media_buttons" => true
					)
				); ?>
			</div>
			<?php
		}

		/*
		 * Metabox callback for color type
		 * @since 1.0.0
		 */
		public function metabox_color_output( $post, $field ) {

			$value = get_post_meta ( $post -> ID, $field['ID'], true );

			?>
			<div class="ctshs_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<input type="text" class="ctshs-color" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo esc_attr ( $value ); ?>" >
			</div>
			<?php
		}

		/*
		 * Callback for a divider
		 * @since 1.0.0
		 */
		public function metabox_divider_output() {
			?>
			<div class="divider"></div>
			<?php
		}

		/*
		 * Callback for html content
		 * @since 1.1.0
		 */
		public function metabox_html_output ( $field ) {
			?>
			<h3><?php echo $field['title']; ?></h3>
			<?php
		}

		/*
		 * Metabox callback for slide image type
		 * Allows previewing of text with slide
		 * @since 1.5.0
		 */
		public function metabox_slide_image_output( $post, $field ) {

			$value = get_post_meta ( $post -> ID, $field['ID'], true );

			?>
			<div class="ctshs_metafield <?php echo $field['class']; ?>">

				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<input type="hidden" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo esc_attr ( $value ); ?>" >

				<div class="ctshs-preview-wrapper">

					<div id="<?php echo $field['name']; ?>-image" class="ctshs-background-image">
						<?php if ( isset ( $value ) ) { ?>
							<?php echo wp_get_attachment_image ( intval ( $value ), 'medium' ); ?>
						<?php } ?>
					</div>

					<div id="ctshs-preview-captions-wrapper" data-position="" data-layout="">
						<div id="ctshs-caption-1">
						</div><!-- ctshs-caption-1 -->
						<div id="ctshs-caption-2">
						</div><!-- ctshs-caption-2 -->
						<div id="ctshs-caption-3">
						</div><!-- ctshs-caption-3 -->
					</div><!-- .ctshs-preview-captions-wrapper -->

				</div><!-- .ctshs-preview-wrapper -->

				<script>
					jQuery(document).ready(function($){
						// Store existing/old value in data attribute so that we can remove correct classes when something gets updated
						var wrapper = $('#ctshs-preview-captions-wrapper');
						var captionPosition = $('#ctshs_caption_position').val();
						wrapper.data('position',captionPosition);
						var oldPosition = wrapper.data('position');
						wrapper.removeClass(oldPosition);
						wrapper.addClass(captionPosition);

						$('#ctshs_caption_position').on('change',function(){
							oldPosition = wrapper.data('position');
							wrapper.removeClass(oldPosition);
							captionPosition = $(this).val();
							wrapper.data('position',captionPosition);
							wrapper.addClass(captionPosition);
						});

						// Caption layout
						var captionLayout = $('#ctshs_caption_layout').val();
						wrapper.data('layout',captionLayout);
						var oldLayout = wrapper.data('layout');
						wrapper.removeClass(oldLayout);
						wrapper.addClass(captionLayout);

						$('#ctshs_caption_layout').on('change',function(){
							oldLayout = wrapper.data('layout');
							wrapper.removeClass(oldLayout);
							captionLayout = $('#ctshs_caption_layout').val();
							wrapper.data('layout',captionLayout);
							wrapper.addClass(captionLayout);
						});

						// Caption width
						var captionWidth = $('#ctshs_caption_width').val();
						wrapper.css('width',captionWidth+"%");

						$('#ctshs_caption_width').on('change',function(){
							var captionWidth = $(this).val();
							wrapper.css('width',captionWidth+"%");
						});

						// @todo Listen for updates to captions
						var caption1 = $('#ctshs_slide_caption_1').val();
						$('#ctshs-caption-1').html(caption1);
						var caption2 = $('#ctshs_slide_caption_2').val();
						$('#ctshs-caption-2').html(caption2);
						var caption3 = $('#ctshs_slide_caption_3').val();
						$('#ctshs-caption-3').html(caption3);
					});
				</script>

				<input type="button" class="button ctshs-media-upload" data-metabox="<?php echo $field['name']; ?>" value="<?php _e( 'Add Image', 'super-hero-slider' )?>" />

				<input type="button" class="button ctshs-media-remove" data-metabox="<?php echo $field['name']; ?>" value="<?php _e( 'Remove Image', 'super-hero-slider' )?>" />

				<?php if ( isset ( $field['desc'] ) ) { ?>
					<p class="description"><?php echo esc_html ( $field['desc'] ); ?></p>
				<?php } ?>

			</div>
			<?php
		}

		/*
		 * Metabox callback for image type
		 * @since 1.0.0
		 */
		public function metabox_image_output( $post, $field ) {

			$value = get_post_meta ( $post -> ID, $field['ID'], true );

			?>
			<div class="ctshs_metafield <?php echo $field['class']; ?>">

				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<input type="hidden" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo esc_attr ( $value ); ?>" >

				<div id="<?php echo $field['name']; ?>-image" class="ctshs-background-image">
					<?php if ( isset ( $value ) ) { ?>
						<?php echo wp_get_attachment_image ( intval ( $value ), 'medium' ); ?>
					<?php } ?>
				</div>

				<input type="button" class="button ctshs-media-upload" data-metabox="<?php echo $field['name']; ?>" value="<?php _e( 'Add Image', 'super-hero-slider' )?>" />

				<input type="button" class="button ctshs-media-remove" data-metabox="<?php echo $field['name']; ?>" value="<?php _e( 'Remove Image', 'super-hero-slider' )?>" />

				<?php if ( isset ( $field['desc'] ) ) { ?>
					<p class="description"><?php echo esc_html ( $field['desc'] ); ?></p>
				<?php } ?>

			</div>
			<?php
		}

		/*
		 * Metabox callback for select
		 * @since 1.0.0
		 */
		public function metabox_select_output( $post, $field ) {

			$field_value = get_post_meta ( $post -> ID, $field['ID'], true );

			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if ( empty ( $field_value ) && ! empty ( $field['default'] ) ) {
				$field_value = $field['default'];
			}

			?>
			<div class="ctshs_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<select id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>">
					<?php if ( $field['options'] ) {
						foreach ( $field['options'] as $key => $value ) { ?>
							<option value="<?php echo $key; ?>" <?php selected ( $field_value, $key ); ?>><?php echo $value; ?></option>
						<?php }
					} ?>
				</select>
			</div>
			<?php
		}

		/*
		 * Metabox callback for number
		 * @since 1.0.0
		 */
		public function metabox_number_output( $post, $field ) {

			$field_value = get_post_meta ( $post -> ID, $field['ID'], true );

			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if ( empty ( $field_value ) && ! empty ( $field['default'] ) ) {
				// Check if we're on the post-new screen
				global $pagenow;
				if ( in_array ( $pagenow, array( 'post-new.php' ) ) ) {
					// This is a new post screen so we can apply the default value
					$field_value = $field['default'];
				}
			}

			?>
			<div class="ctshs_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<input type="number" min="<?php echo $field['min']; ?>" max="<?php echo $field['max']; ?>" step="<?php echo $field['step']; ?>" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo esc_attr ( $field_value ); ?>" >
			</div>
			<?php
		}

		/*
		 * Metabox callback for checkbox
		 * @since 1.0.0
		 */
		public function metabox_checkbox_output( $post, $field ) {

			$field_value = 0;

			// First check if we're on the post-new screen
			global $pagenow;
			if ( in_array ( $pagenow, array( 'post-new.php' ) ) ) {
				// This is a new post screen so we can apply the default value
				$field_value = $field['default'];
			} else {
				$custom = get_post_custom ( $post->ID );
				if ( isset ( $custom[$field['ID']][0] ) ) {
					$field_value = $custom[$field['ID']][0];
				}
			}
			?>
			<div class="ctshs_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<input type="checkbox" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="1" <?php checked ( 1, $field_value ); ?>>
				<?php if ( ! empty ( $field['label'] ) ) { ?>
					<?php echo $field['label']; ?>
				<?php } ?>
			</div>
			<?php
		}

		/*
		 * Metabox callback for multi select
		 * @since 1.0.0
		 */
		public function metabox_multi_select_output( $post, $field ) {

			$field_value = get_post_meta ( $post -> ID, $field['ID'], true );

			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if ( empty ( $field_value ) && ! empty ( $field['default'] ) ) {
				$field_value = $field['default'];
			}

			// Make an array for values
			$values = array();
			if ( $field_value ) {
				$values = explode ( ',', $field_value );
			}
			?>

			<div class="ctshs_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<select multiple id="<?php echo $field['name']; ?>-select" name="<?php echo $field['name']; ?>-select">
					<?php if ( $field['options'] ) {
						foreach ( $field['options'] as $key => $value ) {
							$selected = in_array ( $key, $values ); ?>
							<option value="<?php echo $key; ?>" <?php echo selected ( 1, $selected ); ?>><?php echo $value; ?></option>
						<?php }
					} ?>
				</select>
				<input type="hidden" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo esc_attr ( $field_value ); ?>" >
			</div>
			<script>
				jQuery(document).ready(function($){
					$('#<?php echo $field['name']; ?>-select').on('change',function(){
						$('#<?php echo $field['name']; ?>').val($('#<?php echo $field['name']; ?>-select').val());
					});
				});
			</script>

			<?php
		}

		/*
		 * Metabox callback for post types
		 * @since 1.0.0
		 */
		public function metabox_post_output( $post, $field ) {

			global $post;

			$field_value = get_post_meta ( $post -> ID, $field['ID'], true );

			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if ( empty ( $field_value ) && ! empty ( $field['default'] ) ) {
				$field_value = $field['default'];
			}
			$temp = $post;
			$args = array (
				'post_type'			=> $field['post-type'],
				'posts_per_page'	=> -1
			);
			$options = new WP_Query ( $args );

			?>
			<div class="ctshs_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<select id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>">
					<option value=""><?php _e( '-- Select --', 'super-hero-slider' ); ?></option>
					<?php if ( $options -> have_posts() ) {
						while ( $options -> have_posts() ) : $options -> the_post(); ?>
							<option value="<?php echo $post -> ID; ?>" <?php selected ( $field_value, $post -> ID ); ?>><?php the_title(); ?></option>
						<?php endwhile;
					}
					wp_reset_postdata();
					$post = $temp; ?>
				</select>
			</div>
			<?php
		}

		/*
		 * Metabox callback for post types
		 * @since 1.0.0
		 */
		public function metabox_post_multi_output( $post, $field ) {

			global $post;

			$field_value = get_post_meta ( $post -> ID, $field['ID'], true );

			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if ( empty ( $field_value ) && ! empty ( $field['default'] ) ) {
				$field_value = $field['default'];
			}

			// Make an array for values
			$values = array();
			if ( $field_value ) {
				$values = explode ( ',', $field_value );
			}
			$temp = $post;
			$args = array (
				'post_type'			=> $field['post-type'],
				'posts_per_page'	=> -1
			);
			$options = new WP_Query ( $args );
			?>

			<div class="ctshs_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<select multiple id="<?php echo $field['name']; ?>-select" name="<?php echo $field['name']; ?>-select">
					<?php if ( $options -> have_posts() ) {
						while ( $options -> have_posts() ) : $options -> the_post();
							$selected = in_array ( $post -> ID, $values ); ?>
							<option value="<?php echo $post -> ID; ?>" <?php echo selected ( 1, $selected ); ?>><?php the_title(); ?></option>
						<?php endwhile;
					}
					wp_reset_postdata();
					$post = $temp; ?>
				</select>
				<input type="hidden" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo esc_attr ( $field_value ); ?>" >
			</div>
			<script>
				jQuery(document).ready(function($){
					$('#<?php echo $field['name']; ?>-select').on('change',function(){
						$('#<?php echo $field['name']; ?>').val($('#<?php echo $field['name']; ?>-select').val());

						// Do an AJAX thing to remove slider IDs from slide metafields
						// Only on a Slide post type
						if ( $('#post_type').val() == 'slide' ) {
							var data = {
								'action': 'ctshs_update_slider_list',
								'slider_list': $('#ctshs_slider').val(),
								'slide_id': $("#post_ID").val(),
								'security': "<?php echo wp_create_nonce ( "update-slider-nonce" ); ?>"
							};
							$.post(ajaxurl, data, function(response){
							//	alert(response);
							});
						}

					});
				});
			</script>

			<?php
		}

		/*
		 * Metabox callback for single taxonomy select
		 * @since 1.0.0
		 */
		public function metabox_taxonomy_output( $post, $field ) {

			global $post;

			$field_value = get_post_meta ( $post -> ID, $field['ID'], true );

			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if ( empty ( $field_value ) && ! empty ( $field['default'] ) ) {
				$field_value = $field['default'];
			}
			$taxonomies = get_terms ( $field['taxonomy'] );

			?>
			<div class="ctshs_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<select id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>">
					<option value=""><?php _e( '-- Select --', 'super-hero-slider' ); ?></option>
					<?php if ( ! empty ( $taxonomies ) ) {
						foreach ( $taxonomies as $taxonomy ) { ?>
							<option value="<?php echo $taxonomy -> term_id; ?>" <?php selected ( $field_value, $taxonomy -> term_id ); ?>><?php echo $taxonomy -> name; ?></option>
						<?php }
					} ?>
				</select>
			</div>
			<?php
		}

		/*
		 * Metabox callback for taxonomies
		 * @since 1.0.0
		 */
		public function metabox_taxonomy_multi_output( $post, $field ) {

			global $post;

			$field_value = get_post_meta ( $post -> ID, $field['ID'], true );

			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if ( empty ( $field_value ) && ! empty ( $field['default'] ) ) {
				$field_value = $field['default'];
			}

			// Make an array for values
			$values = array();
			if ( $field_value ) {
				$values = explode ( ',', $field_value );
			}
			$taxonomies = get_terms ( $field['taxonomy'] );
			?>

			<div class="ctshs_metafield <?php echo $field['class']; ?>">
				<?php if ( ! empty ( $taxonomies ) ) { ?>
					<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
					<select multiple id="<?php echo $field['name']; ?>-select" name="<?php echo $field['name']; ?>-select">
							<?php foreach ( $taxonomies as $taxonomy ) {
								$selected = in_array ( $taxonomy -> term_id, $values ); ?>
								<option value="<?php echo $taxonomy -> term_id; ?>" <?php echo selected ( 1, $selected ); ?>><?php echo $taxonomy -> name; ?></option>
							<?php } ?>
					</select>
					<input type="hidden" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo esc_attr ( $field_value ); ?>" >
				<?php } ?>
			</div>
			<script>
				jQuery(document).ready(function($){
					$('#<?php echo $field['name']; ?>-select').on('change',function(){
						$('#<?php echo $field['name']; ?>').val($('#<?php echo $field['name']; ?>-select').val());
					});
				});
			</script>

			<?php
		}

		/*
		 * Metabox callback for slide order
		 * @since 1.0.0
		*/
		public function slide_order_callback ( $post ) {

			wp_nonce_field ( 'save_metabox_data', 'ctshs_metabox_nonce' );

			$order = ltrim ( get_post_meta ( $post -> ID, 'ctshs_slides_order', true ), ',' );

			// This is the field we save our slide order to
			echo '<input type="hidden" id="ctshs_slides_order" name="ctshs_slides_order" value="' . esc_attr ( $order ) . '" >';

			global $post;

			// This will be an array of all slides in this slider
			$all_slides = array();

			// If we already have a value here, then we've saved some slides
			if ( ! empty ( $order ) ) {
				// Create an array of existing slide IDs
				$all_slides_list = $order;
				$slide_ids = str_replace ( 'post_', '', $order );
				$all_slides = explode ( ',', $slide_ids );
				$has_slides = true;
			} else {
				$has_slides = false;
				$all_slides_list = '';
			}

			// Query all slides to check for any that have been added since we last saved this metabox
			$current_post = $post -> ID;
			$args = array (
				'post_type'			=> 'slide',
				'posts_per_page'	=> -1,
				// Find slides in this slider only
				'meta_query'		=> array (
					array (
						'key'		=> 'ctshs_slider',
						'value'		=> $current_post,
						'compare'	=> 'LIKE'
					)
				)
			);

			$slides = new WP_Query ( $args );

			if ( $slides -> have_posts() ) {

				while ( $slides -> have_posts() ): $slides -> the_post();

					$slider_list = get_post_meta ( $post -> ID, 'ctshs_slider', true );

					if ( ! empty ( $slider_list ) ) {

						$pluck_ids = str_replace ( 'post_', '', $slider_list );

						$pluck = explode ( ',', $pluck_ids );

						// Double check that the ID number is actually in the slider ID array
						if ( in_array ( $current_post, $pluck ) ) {
							// If the ID isn't in the array, add it
							if ( ! in_array ( $post -> ID, $all_slides ) ) {
								$all_slides[] = $post -> ID;
								$all_slides_list .= ',' . 'post_' . $post -> ID;
							}
						}

					}

				endwhile;

			} else {
				echo '<p>' . __( 'You don\'t have any slides yet.', 'super-hero-slider' ) . '</p>';
			}

			// If we've got slides, let's show them
			if ( $all_slides ) {
				$all_slides_list = trim ( $all_slides_list, ',' );
				?>

				<table class="wp-list-table widefat fixed striped slides">
					<thead>
						<tr>
							<th scope="col" id="drag" class="manage-column column-drag">&nbsp;</th>
							<th scope="col" id="image" class="manage-column column-image">Image</th>
							<th scope="col" id="title" class="manage-column column-title">Title</th>
							<th scope="col" id="caption" class="manage-column">&nbsp;</th>
							<th scope="col" id="position" class="manage-column">&nbsp;</th>
						</tr>
					</thead>

					<tbody id="slide-order">

						<?php foreach ( $all_slides as $slide_id ) { ?>

						<tr id="post_<?php echo $slide_id; ?>" class="hentry">

							<td class="drag-icon column-drag" data-colname="Drag">
								<span class="dashicons dashicons-sort"></span>
							</td>

							<td class="slide-image column-image" data-colname="Image">
								<?php $image_id = get_post_meta ( $slide_id, 'ctshs_slide_image', true );
								$img_src = wp_get_attachment_image_src ( $image_id, 'medium' );
								echo '<img src="' . $img_src[0] . '">'; ?>
							</td>

							<td class="title column-title column-title" data-colname="Title">

								<strong><a class="row-title" target="_blank" href="<?php echo admin_url(); ?>post.php?post=<?php echo $slide_id; ?>&amp;action=edit" title="Edit"><?php echo get_the_title ( $slide_id ); ?> <span class="dashicons dashicons-external"></span></a></strong>

								<div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>

								<div class="row-actions"><span class="edit">
									<a target="_blank" href="<?php echo admin_url(); ?>post.php?post=<?php echo $slide_id; ?>&amp;action=edit" title="Edit this item (opens new tab)">Edit slide</a> | </span><span class="trash"><a class="submitdelete" title="Remove this slide from this slider" data-remove="<?php echo $slide_id; ?>" href="#">Remove slide from slider</a></span>
								</div>

								<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
							</td>

							<td class="caption column-caption" data-colname="Caption">
								&nbsp;
							</td>

							<td class="caption column-position" data-colname="Position">
								&nbsp;
							</td>

						</tr>

						<?php } ?>

					</tbody>

					<tfoot>
						<tr>
						</tr>
					</tfoot>

				</table>

				<script>
					jQuery(document).ready(function($){
						// If we haven't got any slides in the order metafield, add the lot
						$('#ctshs_slides_order').val("<?php echo $all_slides_list; ?>");
						order = $('#ctshs_slides_order').val();
						$("#slide-order").sortable({
							create: function(event, ui) {
								container = $('#slide-order');
							},
							cursor: "move"
						});
						$("#slide-order").on("sortstop", function( event, ui  ) {
							var sorted = $("#slide-order").sortable("toArray");
							$('#ctshs_slides_order').val(sorted);
						});
						$(".submitdelete").on("click",function(e){
							e.preventDefault();
							var remove_id = $(this).data("remove");
							var remove = "post_"+remove_id;
							var order = $("#ctshs_slides_order").val();
							var newOrder = order.replace(remove, "");
							newOrder = newOrder.replace(",,", ",");
							if(newOrder.charAt( newOrder.length-1 ) == ",") {
								newOrder = newOrder.slice(0, -1)
							}
							$("#ctshs_slides_order").val(newOrder);
							// Remove the row from the slider
							$("#post_"+remove_id).remove();
							// Do an AJAX thing to remove slider IDs from slide metafields
							var data = {
								'action': 'ctshs_update_slide_list',
								'slide_id': remove_id,
								'slider_id': $("#post_ID").val(),
								'security': "<?php echo wp_create_nonce ( "update-slide-nonce" ); ?>"
							};
							$.post(ajaxurl, data, function(response){
							//	alert(response);
							});
						});
					});
				</script>
			<?php }
			wp_reset_query();
		}

		/*
		 * Preview Area
		 * @since 1.5.0
		 */
		public function slider_preview() {
			global $post;
			$post_id = $post->ID;
			if( isset( $_GET['post'] ) ) {
				$post_id = absint( $_GET['post'] );
			}
			// Is it a full-screen slider?
			$full_screen = get_post_meta( $post_id, 'ctshs_full_screen', true );
			if( ! empty( $full_screen ) ) { ?>
				<p><?php _e( 'Preview is not available for full screen sliders', 'super-hero-slider' ); ?></p>
			<?php } else { ?>
				<p><?php _e( 'Click Update to preview any changes', 'super-hero-slider' ); ?></p>
				<div class="ctshs-slider-preview">
					<?php super_hero_slider( $post_id ); ?>
				</div>
				<?php
			}
		}

		/*
		 * Save
		 * @since 1.0.0
		 */
		public function save_metabox_data( $post_id ) {

			// Check the nonce is set
			if ( ! isset ( $_POST['ctshs_metabox_nonce'] ) ) {
				return;
			}

			// Verify the nonce
			if ( ! wp_verify_nonce ( $_POST['ctshs_metabox_nonce'], 'save_metabox_data' ) ) {
				return;
			}

			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// Check the user's permissions.
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Save all our metaboxes
			$metaboxes = $this -> metaboxes;
			foreach ( $metaboxes as $metabox ) {
				if ( $metabox['fields'] ) {
					foreach ( $metabox['fields'] as $field ) {

						if ( $field['type'] != 'divider' ) {

							if ( isset ( $_POST[$field['name']] ) ) {
								if ( $field['type'] == 'wysiwyg' ) {
									$data = $_POST[$field['name']];
								} else {
									$data = sanitize_text_field ( $_POST[$field['name']] );
								}
								update_post_meta ( $post_id, $field['ID'], $data );
							} else {
								delete_post_meta ( $post_id, $field['ID'] );
							}
						}
					}
				}
			}

			// Make sure that the slide order is set.
			if ( isset( $_POST['ctshs_slides_order'] ) ) {
				$my_data = sanitize_text_field( $_POST['ctshs_slides_order'] );
				update_post_meta( $post_id, 'ctshs_slides_order', $my_data );
			}


		}

		/*
		 * Check if a slider has been deselected from a slide post
		 * @since 1.0.0
		 */
		public function detect_slider_change() {
			?>
			<script>
				jQuery(document).ready(function($){
					$('#ctshs_slider-select').on("change",function(){

					});
				});
			</script>
			<?php
		}

		/*
		 * Enqueue styles and scripts
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {

			wp_register_script( 'ctshs-admin', SHS_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ) );
			wp_localize_script( 'ctshs-admin', 'meta_image',
				array(
					'title' => __( 'Add Image', 'super-hero-slider' ),
					'button' => __( 'Select', 'super-hero-slider' ),
				)
			);

			wp_enqueue_script( 'ctshs-admin' );
			wp_enqueue_style( 'wp-color-picker' );
			// Don't enqueue all this unless it's needed
			$screen = get_current_screen();
			if( isset( $screen->post_type ) && ( $screen->post_type == 'slide' || $screen->post_type == 'slider' ) ) {
				wp_enqueue_script ( 'jquery-ui-sortable' );
				wp_enqueue_style ( 'ctshs-admin-style', SHS_PLUGIN_URL . 'assets/css/admin-style.css' );
				wp_enqueue_media();

				/*
				 * Enqueue styles and scripts from the front end for the Preview
				 * @since 1.5.0
				 */
			//	wp_enqueue_style ( 'mode-theme-style', get_stylesheet_uri() );

				wp_enqueue_style ( 'super-hero-style', SHS_PLUGIN_URL . 'assets/css/superherostyle.css' );
				wp_enqueue_style ( 'dashicons' );
				wp_register_script ( 'superhero-script',  SHS_PLUGIN_URL . 'assets/js/superhero.js', array ( 'jquery' ), '1.1.0', true );
				wp_register_script ( 'imagesloaded', SHS_PLUGIN_URL . 'assets/js/imagesloaded.pkgd.min.js', array ( 'jquery', 'superhero-script' ), '4.1.0', true );
				wp_enqueue_script ( 'superheroslider-script',  SHS_PLUGIN_URL . 'assets/js/superheroslider.js', array ( 'imagesloaded', 'superhero-script' ), '1.0.0', true );
			}
		}

		/*
		 * Detect if WooCommerce is activated
		 * @since 1.1.0
		 */
		public function is_woocommerce() {
			if ( class_exists ( 'WooCommerce' ) ) {
				return true;
			}
			return false;
		}

		/*
		 * Add class to specific metaboxes
		 * @since 1.1.0
		 */
		public function add_products_postbox_class ( $classes ) {
			array_push ( $classes, 'product-feeds-postbox' );
			return $classes;
		}

		/*
		 * Add class to specific metaboxes
		 * @since 1.1.0
		 */
		public function add_posts_postbox_class ( $classes ) {
			array_push ( $classes, 'post-feeds-postbox' );
			return $classes;
		}

	}

}
