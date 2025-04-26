<script>
	function jqvalidate() {

		$(document).ready(function(){
			$('#app-form').validate({
				rules:{
					title:{
						required: true,
						minlength: 4
					},
					max_img_upload_of_item: {
						blankCheck:  "",
						indexCheck: ""
					},
					ad_type: {
						indexCheck: ""
					},
					promo_cell_interval_no: {
						blankCheck:  "",
						indexCheck: ""
					},
				},
				messages:{
					title:{
						required: "<?php echo get_msg( 'err_title' ) ;?>",
						minlength: "<?php echo get_msg( 'err_title_len' ) ;?>"
					}, 
					max_img_upload_of_item: {
						blankCheck: "<?php echo get_msg( 'err_max_img_blank_save' ) ;?>",
						indexCheck: "<?php echo get_msg( 'err_max_img_zero_save' ) ;?>"
					},
					ad_type: {
						indexCheck: "<?php echo get_msg( 'err_ad_post_type' ) ;?>"
					},
					promo_cell_interval_no: {
						blankCheck: "<?php echo get_msg( 'err_promo_cell_blank_save' ) ;?>",
						indexCheck: "<?php echo get_msg( 'err_promo_cell_zero_save' ) ;?>"
					},
				}
			});
		});
		
		// custom validation
		jQuery.validator.addMethod("blankCheck",function( value, element ) {
			
			if(value == "") {
					return false;
			} else {
						return true;
			}
			});

		jQuery.validator.addMethod("indexCheck",function( value, element ) {

			if(value == 0) {
					return false;
			} else {
						return true;
			}
		});
	}

	function runAfterJQ() {
		
		$('#ad_type').on('change', function() {

			var value = $('option:selected', this).text().replace(/Value\s/, '');

			var adType = $(this).val();

			if(adType != "1" && adType != "5" && adType != "0"){
				$("#promo_cell_interval_no_input").removeClass("d-none");
				$("#promo_cell_interval_no_input").addClass("fade show");
			}else{
				$("#promo_cell_interval_no_input").addClass("d-none");
			}

		});

		// check max_img_upload_of_item not to type chars and specail chars
		$('input[name="max_img_upload_of_item"]').keyup(function(e)
											{
			if (/[^\d.-]/g.test(this.value))
			{
			// Filter non-digits from input value.
			this.value = this.value.replace(/[^\d.-]/g, '');
			}
		});
		
		// check promo_cell_interval_no not to type chars and specail chars
		$('input[name="promo_cell_interval_no"]').keyup(function(e)
										{
			if (/[^\d.-]/g.test(this.value))
			{
			// Filter non-digits from input value.
			this.value = this.value.replace(/[^\d.-]/g, '');
			}
		});
	}
</script>