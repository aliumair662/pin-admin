<script>
	function jqvalidate() {

		$(document).ready(function(){
			$('#app-form').validate({
				rules:{
				
					default_language:{
						indexCheck : "",
					},
					default_loading_limit:{
						blankCheck : "",
						indexCheck : ""
					},
					posted_by_loading_limit:{
						blankCheck : "",
						indexCheck : ""
					},
					agent_loading_limit:{
						blankCheck : "",
						indexCheck : ""
					},
					amenities_loading_limit:{
						blankCheck : "",
						indexCheck : ""
					},
					category_loading_limit:{
						blankCheck : "",
						indexCheck : ""
					},
					block_item_loading_limit:{
						blankCheck : "",
						indexCheck : ""
					},
					follower_item_loading_limit:{
						blankCheck : "",
						indexCheck : ""
					},
					recent_item_loading_limit:{
						blankCheck : "",
						indexCheck : ""
					},
					block_slider_loading_limit:{
						blankCheck : "",
						indexCheck : ""
					},
					feature_item_loading_limit:{
						blankCheck : "",
						indexCheck : ""
					},
					discount_item_loading_limit:{
						blankCheck : "",
						indexCheck : ""
					},
					popular_item_loading_limit:{
						blankCheck : "",
						indexCheck : ""
					},
					google_playstore_url:{
						blankCheck : ""
					},
					apple_appstore_url:{
						blankCheck : ""
					},
					ios_appstore_id:{
						blankCheck : ""
					},
					fb_key:{
						blankCheck : ""
					},
					date_format:{
						blankCheck : ""
					},
					price_format:{
						blankCheck : ""
					},
					item_detail_view_count_for_ads:{
						blankCheck : "",
						indexCheck : ""
					},
					profile_image_size:{
						blankCheck : "",
						indexCheck : ""
					},
					upload_image_size:{
						blankCheck : "",
						indexCheck : ""
					},
					chat_image_size:{
						blankCheck : "",
						indexCheck : ""
					},
					promote_first_choice_day:{
						blankCheck : "",
						indexCheck : ""
					},
					promote_second_choice_day:{
						blankCheck : "",
						indexCheck : ""
					},
					promote_third_choice_day:{
						blankCheck : "",
						indexCheck : ""
					},
					promote_fourth_choice_day:{
						blankCheck : "",
						indexCheck : ""
					},
					mile:{
						blankCheck : "",
						indexCheck : ""
					},
					blue_mark_size:{
						blankCheck : "",
						indexCheck : ""
					},
					video_duration:{
						blankCheck : "",
						indexCheck : ""
					},

				
				},
				messages:{
					default_language:{
						indexCheck : "<?php echo get_msg( 'err_default_lang' ) ;?>",
					},
					blue_mark_size:{
						indexCheck : "<?php echo get_msg( 'err_blue_mark_size_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_blue_mark_size' ) ;?>",
					},
					video_duration:{
						indexCheck : "<?php echo get_msg( 'err_video_duration_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_video_duration' ) ;?>",
					},
					mile:{
						indexCheck : "<?php echo get_msg( 'err_mile_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_mile' ) ;?>",
					},
					item_detail_view_count_for_ads:{
						indexCheck : "<?php echo get_msg( 'err_item_detail_view_count_for_ads_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_item_detail_view_count_for_ads' ) ;?>",
					},
					promote_first_choice_day:{
						indexCheck : "<?php echo get_msg( 'err_promote_first_choice_day_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_promote_first_choice_day' ) ;?>",
					},
					promote_second_choice_day:{
						indexCheck : "<?php echo get_msg( 'err_promote_second_choice_day_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_promote_second_choice_day' ) ;?>",
					},
					promote_third_choice_day:{
						indexCheck : "<?php echo get_msg( 'err_promote_third_choice_day_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_promote_third_choice_day' ) ;?>",
					},
					promote_fourth_choice_day:{
						indexCheck : "<?php echo get_msg( 'err_promote_fourth_choice_day_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_promote_fourth_choice_day' ) ;?>",
					},
					chat_image_size:{
						indexCheck : "<?php echo get_msg( 'err_chat_image_size_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_chat_image_size' ) ;?>",
					},
					upload_image_size:{
						indexCheck : "<?php echo get_msg( 'err_upload_image_size_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_upload_image_size' ) ;?>",
					},
				
					profile_image_size:{
						indexCheck : "<?php echo get_msg( 'err_profile_image_size_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_profile_image_size' ) ;?>",
					},
					default_loading_limit:{
						indexCheck : "<?php echo get_msg( 'err_default_loading_limit_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_default_loading_limit' ) ;?>",
					},
					category_loading_limit:{
						indexCheck : "<?php echo get_msg( 'err_category_loading_limit_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_category_loading_limit' ) ;?>",
					},
					posted_by_loading_limit:{
						indexCheck : "<?php echo get_msg( 'err_posted_by_loading_limit_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_posted_by_loading_limit' ) ;?>",
					},
					agent_loading_limit:{
						indexCheck : "<?php echo get_msg( 'err_agent_loading_limit_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_agent_loading_limit' ) ;?>",
					},
					amenities_loading_limit:{
						indexCheck : "<?php echo get_msg( 'err_amenities_loading_limit_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_amenities_loading_limit' ) ;?>",
					},
					block_item_loading_limit:{
						indexCheck : "<?php echo get_msg( 'err_block_item_loading_limit_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_block_item_loading_limit' ) ;?>",
					},
					follower_item_loading_limit:{
						indexCheck : "<?php echo get_msg( 'err_follower_item_loading_limit_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_follower_item_loading_limit' ) ;?>",
					},
					block_slider_loading_limit:{
						indexCheck : "<?php echo get_msg( 'err_block_slider_loading_limit_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_block_slider_loading_limit' ) ;?>",
					},
					feature_item_loading_limit:{
						indexCheck : "<?php echo get_msg( 'err_feature_item_loading_limit_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_feature_item_loading_limit' ) ;?>",
					},
					discount_item_loading_limit:{
						indexCheck : "<?php echo get_msg( 'err_discount_item_loading_limit_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_discount_item_loading_limit' ) ;?>",
					},
					popular_item_loading_limit:{
						indexCheck : "<?php echo get_msg( 'err_popular_item_loading_limit_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_popular_item_loading_limit' ) ;?>",
					},
					recent_item_loading_limit:{
						indexCheck : "<?php echo get_msg( 'err_recent_item_loading_limit_zero' ) ;?>",
						blankCheck : "<?php echo get_msg( 'err_recent_item_loading_limit' ) ;?>",
					},
					google_playstore_url:{
						blankCheck : "<?php echo get_msg( 'err_google_playstore_url' ) ;?>",
					},
					apple_appstore_url:{
						blankCheck : "<?php echo get_msg( 'err_apple_appstore_url' ) ;?>",
					},
					ios_appstore_id:{
						blankCheck : "<?php echo get_msg( 'err_ios_appstore_id' ) ;?>",
					},
					price_format:{
						blankCheck : "<?php echo get_msg( 'err_price_format' ) ;?>",
					},
					date_format:{
						blankCheck : "<?php echo get_msg( 'err_date_format' ) ;?>",
					},
					fb_key:{
						blankCheck : "<?php echo get_msg( 'err_fb_key' ) ;?>",
					},
				
				}
			});

			// default language is selected, available language is auto on
			var default_language = $('#default_language').val();
			const language_codes = ['en', 'ar', 'hi', 'de', 'es', 'fr', 'id', 'it', 'ja', 'ko', 'ms', 'pt', 'ru', 'th', 'tr', 'zh'];

			for(let x in language_codes){
				
				if(language_codes[x] == default_language){
					$('#'+default_language).attr('checked', true);
					$('#'+default_language).attr('disabled', true);
				}else{
					$('#'+language_codes[x]).attr('disabled', false);
				}
			}
		});

		jQuery.validator.addMethod("indexCheck",function( value, element ) {
			if(value == 0) {
					return false;
			} else {
					return true;
			};
	 	});

		jQuery.validator.addMethod("blankCheck",function( value, element ) {
			if(value == "") {
				return false;
			} else {
				return true;
			}
		});
		
		jQuery.validator.addMethod("validChecklat",function( value, element ) {
			if (value < -90 || value > 90) {
				return false;
			} else {
				return true;
			}
		});

		jQuery.validator.addMethod("validChecklng",function( value, element ) {
			if (value < -180 || value > 180) {
				return false;
			} else {
				return true;
			}
		});
	}

	function runAfterJQ() {

		$('#default_language').on('change', function() {

			var default_language = $(this).val();

			const language_codes = ['en', 'ar', 'hi', 'de', 'es', 'fr', 'id', 'it', 'ja', 'ko', 'ms', 'pt', 'ru', 'th', 'tr', 'zh'];

			for(let x in language_codes){
				
				if(language_codes[x] == default_language){
					$('#'+default_language).attr('checked', true);
					$('#'+default_language).attr('disabled', true);
				}else{
					$('#'+language_codes[x]).attr('disabled', false);
				}
			}
		});
	}

	$('input[name="ios_appstore_id"]').keyup(function(e) {
		if (/[^\d.-]/g.test(this.value))
		{
		// Filter non-digits from input value.
		this.value = this.value.replace(/[^\d.-]/g, '');
		}
	});

	$('input[name="fb_key"]').keyup(function(e) {
		if (/[^\d.-]/g.test(this.value))
		{
		// Filter non-digits from input value.
		this.value = this.value.replace(/[^\d.-]/g, '');
		}
	});
	
	$('input[name="default_order_time"]').keyup(function(e) {
		if (/[^\d.-]/g.test(this.value))
		{
		// Filter non-digits from input value.
		this.value = this.value.replace(/[^\d.-]/g, '');
		}
	});

	$('input[name="default_loading_limit"]').keyup(function(e) {
		if (/[^\d.-]/g.test(this.value))
		{
		// Filter non-digits from input value.
		this.value = this.value.replace(/[^\d.-]/g, '');
		}
	});

	$('input[name="category_loading_limit"]').keyup(function(e) {
		if (/[^\d.-]/g.test(this.value))
		{
		// Filter non-digits from input value.
		this.value = this.value.replace(/[^\d.-]/g, '');
		}
	});

	$('input[name="block_item_loading_limit"]').keyup(function(e) {
		if (/[^\d.-]/g.test(this.value))
		{
		// Filter non-digits from input value.
		this.value = this.value.replace(/[^\d.-]/g, '');
		}
	});

	$('input[name="follower_item_loading_limit"]').keyup(function(e) {
		if (/[^\d.-]/g.test(this.value))
		{
		// Filter non-digits from input value.
		this.value = this.value.replace(/[^\d.-]/g, '');
		}
	});

	$('input[name="recent_item_loading_limit"]').keyup(function(e) {
		if (/[^\d.-]/g.test(this.value))
		{
		// Filter non-digits from input value.
		this.value = this.value.replace(/[^\d.-]/g, '');
		}
	});
	$('input[name="block_slider_loading_limit"]').keyup(function(e) {
		if (/[^\d.-]/g.test(this.value))
		{
		// Filter non-digits from input value.
		this.value = this.value.replace(/[^\d.-]/g, '');
		}
	});

	$('input[name="feature_item_loading_limit"]').keyup(function(e) {
		if (/[^\d.-]/g.test(this.value))
		{
		// Filter non-digits from input value.
		this.value = this.value.replace(/[^\d.-]/g, '');
		}
	});

	$('input[name="discount_item_loading_limit"]').keyup(function(e) {
		if (/[^\d.-]/g.test(this.value))
		{
		// Filter non-digits from input value.
		this.value = this.value.replace(/[^\d.-]/g, '');
		}
	});

	$('input[name="popular_item_loading_limit"]').keyup(function(e) {
		if (/[^\d.-]/g.test(this.value))
		{
		// Filter non-digits from input value.
		this.value = this.value.replace(/[^\d.-]/g, '');
		}
	});

</script>