
jQuery(document).ready(function($){

	// Hide or show slide order if slider type is standard
	var sliderType = $('#ctshs_slider_type').val();
	ctshs_toggle_panels(sliderType);
	$('#ctshs_slider_type').on('change',function(){
		sliderType = $('#ctshs_slider_type').val();
		ctshs_toggle_panels(sliderType);
	});
	// Hide and show different panels
	function ctshs_toggle_panels(sliderType){
		if ( sliderType == 'std' || sliderType == null ) {
			$(".post-feeds-postbox").fadeOut();
		//	$("#slider_posts_feed_content_metabox").fadeOut();
			$(".product-feeds-postbox").fadeOut();
		//	$("#slider_products_feed_content_metabox").fadeOut();
			$("#slider_metabox").fadeIn();
		} else if ( sliderType == 'posts' ) {
			$(".post-feeds-postbox").fadeIn();
		//	$("#slider_posts_feed_content_metabox").fadeIn();
			$(".product-feeds-postbox").fadeOut();
		//	$("#slider_products_feed_content_metabox").fadeOut();
			$("#slider_metabox").fadeOut();
		} else if ( sliderType == 'products' ) {
			$(".product-feeds-postbox").fadeIn();
		//	$("#slider_products_feed_content_metabox").fadeIn();
			$(".post-feeds-postbox").fadeOut();
		//	$("#slider_posts_feed_content_metabox").fadeOut();
			$("#slider_metabox").fadeOut();
		}
		ctshs_toggle_carousel();
	}
	// Check carousel items
	var carouselItems = $('#ctshs_carousel_items').val();
	ctshs_toggle_carousel();
	$('#ctshs_carousel_items').on('change',function(){
		carouselItems = $('#ctshs_carousel_items').val();
		ctshs_toggle_carousel();
	});
	// Hide and show carousel layout panel
	function ctshs_toggle_carousel(){
		if(carouselItems>1&&sliderType=='std'){
			$('#carousel_layout_metabox').fadeIn();
		} else {
			$('#carousel_layout_metabox').fadeOut();
		}
	}
	// Hide or show panel background color option
	var captionDesign = $('#ctshs_posts_feed_caption_design').val();
	ctshs_toggle_panel_bg(captionDesign);
	$('#ctshs_posts_feed_caption_design').on('change',function(){
		captionDesign = $('#ctshs_posts_feed_caption_design').val();
		ctshs_toggle_panel_bg(captionDesign);
	});
	function ctshs_toggle_panel_bg(captionDesign){
		if ( captionDesign == 'ctshs-panel-std' || captionDesign == null ) {
			$("#ctshs_posts_feed_caption_design").parent().next().fadeOut();
		} else {
			$("#ctshs_posts_feed_caption_design").parent().next().fadeIn();
		}
	}

	/*
	* Attaches the image uploader to the input field
	*/
	$('.ctshs-color').wpColorPicker();

    // Instantiates the variable that holds the media library frame.
    var meta_image_frame;

    // Runs when the image button is clicked.
    $('.ctshs-media-upload').click(function(e){

			console.log('metabox_id');

        // Prevents the default action from occurring.
        e.preventDefault();

		// The metabox id
		var metabox_id = $(this).attr('data-metabox');

        // If the frame already exists, re-open it.
        if ( meta_image_frame ) {
            meta_image_frame.open();
            return;
        }

        // Sets up the media library frame
        meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
            title: meta_image.title,
            button: { text:  meta_image.button },
            library: { type: 'image' }
        });

        // Runs when an image is selected.
        meta_image_frame.on('select', function(){

            // Grabs the attachment selection and creates a JSON representation of the model.
            var media_attachment = meta_image_frame.state().get('selection').first().toJSON();

            // Sends the attachment URL to our custom image input field.
            $('#'+metabox_id).val(media_attachment.id);
            $('#'+metabox_id+"-image").html('<img src="'+media_attachment.sizes.medium.url+'">');
        });

        // Opens the media library frame.
        meta_image_frame.open();
    });
	// Remove the image
	$('.ctshs-media-remove').click(function(e){
		e.preventDefault();
		// The metabox id
		var metabox_id = $(this).data('metabox');
		$('#'+metabox_id).val('');
		$('#'+metabox_id+"-image").html('');

	});
});
