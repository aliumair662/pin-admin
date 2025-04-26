<script>

	<?php if ( $this->config->item( 'client_side_validation' ) == true ): ?>

	function jqvalidate() {
		
		$('#amenity-form').validate({
			rules:{
				name:{
					blankCheck : "",
					minlength: 3
					
				},
				
				cover:{
					required : true
				}
			},
			messages:{
				name:{
					blankCheck : "<?php echo get_msg( 'err_amenity_name' ) ;?>",
					minlength: "<?php echo get_msg( 'err_amenity_len' ) ;?>"
				},
				
				cover:{
					required: "<?php echo get_msg('err_icon'); ?>"
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

	

		$(".delete-img").click(function(e){
			e.preventDefault();

			// get id and image
			var id = $(this).attr("id");

			// do action
			var action = "<?php echo $module_site_url ."/delete_cover_photo/"; ?>" + id + "/<?php echo @$amenity->id; ?>";
			console.log( action );
			$(".btn-delete-image").attr("href", action);
			
		});

	
</script>

<?php 
	// replace cover photo modal
	$data = array(
		"title" => get_msg("upload_photo"),
		"img_type" => "amenity",
		"img_parent_id" => @$amenity->id
	);
	
	$this->load->view( $template_path ."/components/photo_upload_modal", $data );
	// delete cover photo modal
	$this->load->view( $template_path ."/components/delete_cover_photo_modal" ); 

?>