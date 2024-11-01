/*
 * version 1.1.0
 */
jQuery(document).ready(function($){
	"use strict";
});
	
	// @ToDo - check back on this when full-screen option is introduced
// Constrain image size
function shs_resize_slide( itemID, currentItem ){
	
	// Recalculate height and ensure images don\'t overflow
	slideH = currentItem.height() * .75;
	// Embedded thumbs in the product slider can have their height set manually
	jQuery(".not-carousel "+itemID+" .super-hero-caption img").not('.embedded-thumb').css({
		"max-height": slideH,
		"width": "auto"
	});
	
	// Add full-caption class if slider width is less than breakpoint
	// Check non-carousel items
	if(jQuery(currentItem.context).hasClass("not-carousel")){
		
		// Resize text and images to ensure we don't get any content cropped
		// Check the caption height against the slide height
		captionH = jQuery(itemID+" .super-hero-caption").height();
		
		// Is the caption bigger than the slide?
		if ( captionH > slideH ) {
			// Failsafe
			var whileCount = 0;
			imgH = jQuery(itemID+" .super-hero-caption img").height();
			h1FontSize = parseInt(jQuery(itemID+" .super-hero-caption h1").css("font-size"));
			h2FontSize = parseInt(jQuery(itemID+" .super-hero-caption h2").css("font-size"));
			h3FontSize = parseInt(jQuery(itemID+" .super-hero-caption h3").css("font-size"));
			h4FontSize = parseInt(jQuery(itemID+" .super-hero-caption h4").css("font-size"));
			pFontSize = parseInt(jQuery(itemID+" .super-hero-caption p").css("font-size"));
			while(whileCount<100){
				if(captionH>slideH){
					imgH = imgH - 25;
					// 18 is minimum size for the h1 element
					if(parseInt(h1FontSize)>18) {
						h1FontSize = parseInt(h1FontSize) - 0.25;
					} else {
						h1FontSize = 18;
					}
					// 18 is minimum size for the h2 element
					if(parseInt(h2FontSize)>18) {
						h2FontSize = parseInt(h2FontSize) - 0.25;
					} else {
						h2FontSize = 18;
					}
					// 16 is minimum size for the h3 element
					if(parseInt(h3FontSize)>16) {
						h3FontSize = parseInt(h3FontSize) - 0.25;
					} else {
						h3FontSize = 16;
					}
					// 13 is minimum size for the h4 element
					if(parseInt(h4FontSize)>13) {
						h4FontSize = parseInt(h4FontSize) - 0.25;
					} else {
						h4FontSize = 13;
					}
					// 12 is minimum size for the p element
					if(parseInt(pFontSize)>12) {
						pFontSize = parseInt(pFontSize) - 0.25;
					} else {
						pFontSize = 12;
					}
					jQuery(itemID+" .super-hero-caption img").css({
						"max-height": imgH
					});
					jQuery(itemID+" .super-hero-caption h1").css({"font-size":h1FontSize+"px"});
					jQuery(itemID+" .super-hero-caption h2").css({"font-size":h2FontSize+"px"});
					jQuery(itemID+" .super-hero-caption h3").css({"font-size":h3FontSize+"px"});
					jQuery(itemID+" .super-hero-caption h4").css({"font-size":h4FontSize+"px"});
					jQuery(itemID+" .super-hero-caption p").css({"font-size":pFontSize+"px"});
					captionH = jQuery(itemID+" .super-hero-caption").height();
				} else {
					whileCount = 100;
				}
				whileCount++;
			}
		}
	}
}