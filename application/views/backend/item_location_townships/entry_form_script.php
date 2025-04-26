<script>

	<?php if ( $this->config->item( 'client_side_validation' ) == true ): ?>

	function jqvalidate() {

		$('#location-township-form').validate({
			rules:{
				township_name:{
					blankCheck : "",
					minlength: 3
					
				},
				city_id: {
					indexCheck : ""
				},
				lat:{
                    blankCheck : "",
                    indexCheck : "",
                    validChecklat : ""
			    },
			    lng:{
			     	blankCheck : "",
			     	indexCheck : "",
			     	validChecklng : ""
			    },
			},
			messages:{
				township_name:{
					blankCheck : "<?php echo get_msg( 'err_township_name' ) ;?>",
					minlength: "<?php echo get_msg( 'err_township_len' ) ;?>"
					
				},
				city_id:{
					indexCheck: "<?php echo get_msg('f_item_city_required'); ?>"
				},
				lat:{
			     	blankCheck : "<?php echo get_msg( 'err_lat' ) ;?>",
			     	indexCheck : "<?php echo get_msg( 'err_lat_lng' ) ;?>",
			     	validChecklat : "<?php echo get_msg( 'lat_invlaid' ) ;?>"
			    },
			    lng:{
			     	blankCheck : "<?php echo get_msg( 'err_lng' ) ;?>",
			     	indexCheck : "<?php echo get_msg( 'err_lat_lng' ) ;?>",
			     	validChecklng : "<?php echo get_msg( 'lng_invlaid' ) ;?>"
			    },
			}
		});

		jQuery.validator.addMethod("indexCheck",function( value, element ) {
			
			if(value == 0) {
				 return false;
			} else {
				 return true;
			};

			
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

	function runAfterJQ() {

		  $('input[name="ordering"]').keyup(function(e)
                                {
		  if (/[^0-9\.]/g.test(this.value))
		  {
		    // Filter non-digits from input value.
		    //this.value = this.value.replace(/\D/g, '');
		    this.value = this.value.replace(/[^0-9\.]/g,'');
		  }
		});
	}	

</script>