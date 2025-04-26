<script>

	<?php if ( $this->config->item( 'client_side_validation' ) == true ): ?>

	function jqvalidate() {
		$('#check-admin-form').validate({
			rules:{
				password:{
					blankCheck : "",
					remote: "<?php echo $module_site_url .'/ajx_valid/'; ?>"
				}
			},
			messages:{
				password:{
					blankCheck : "<?php echo get_msg( 'err_blank_pwd' ) ;?>",
					remote: "<?php echo get_msg( 'err_pwd_invalid' ) ;?>."
				}
			}
		});

		// custom validation
		jQuery.validator.addMethod("blankCheck",function( value, element ) {
			
			   if(value == "") {
			    	return false;
			   } else {
			    	return true;
			   }
		})
	}

	<?php endif; ?>


</script>