<script>

	<?php if ( $this->config->item( 'client_side_validation' ) == true ): ?>

	function jqvalidate() {

		$('#package-form').validate({
			rules:{
				title:{
					blankCheck : "",
					remote: "<?php echo $module_site_url .'/ajx_exists/'.@$package->package_id; ?>"
				},
				post_count:{
					blankCheck : ""
				},
				price:{
					blankCheck : ""
				},
				currency_id:{
					blankCheck : ""
				}
			},
			messages:{
				title:{
					blankCheck : "<?php echo get_msg( 'err_pkg_title' ) ;?>",
					remote: "<?php echo get_msg( 'err_pkg_exist' ) ;?>."
				},
				post_count:{
					blankCheck: "<?php echo get_msg( 'err_pkg_post_count' ) ;?>"
				},
				price:{
					blankCheck: "<?php echo get_msg( 'err_pkg_price' ) ;?>"
				},
				currency_id: {
					blankCheck: "<?php echo get_msg( 'err_pkg_currency' ) ;?>"
				}
			}
		});

		// custom validation
		jQuery.validator.addMethod("blankCheck",function( value, element ) {
			
			   if(value == "" || value== '0') {
			    	return false;
			   } else {
			    	return true;
			   }
		})
	}

	<?php endif; ?>
</script>
