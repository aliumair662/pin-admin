<script>

	<?php if ( $this->config->item( 'client_side_validation' ) == true ): ?>

	function jqvalidate() {

		$('#propertyby-form').validate({
			rules:{
				name:{
					blankCheck : "",
					minlength: 3,
					remote: "<?php echo $module_site_url .'/ajx_exists/'.@$propertyby->id; ?>"
				}
			},
			messages:{
				name:{
					blankCheck : "<?php echo get_msg( 'err_type_name' ) ;?>",
					minlength: "<?php echo get_msg( 'err_type_len' ) ;?>",
					remote: "<?php echo get_msg( 'err_type_exist' ) ;?>."
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

	function runAfterJQ() {

		$('.delete-img').click(function(e){
			e.preventDefault();

			// get id and image
			var id = $(this).attr('id');

			// do action
			var action = '<?php echo $module_site_url .'/delete_cover_photo/'; ?>' + id + '/<?php echo @$propertyby->id; ?>';
			console.log( action );
			$('.btn-delete-image').attr('href', action);
		});
	}


</script>

<?php 
	// replace cover photo modal
	$data = array(
		'title' => get_msg('upload_photo'),
		'img_type' => 'propertyby-cover',
		'img_parent_id' => @$propertyby->id
	);

	$this->load->view( $template_path .'/components/photo_upload_modal', $data );

	// delete cover photo modal
	$this->load->view( $template_path .'/components/delete_cover_photo_modal' ); 

	// replace cover icon modal
	$data = array(
		'title' => get_msg('about_upload_icon'),
		'img_type' => 'propertyby-icon',
		'img_parent_id' => @$propertyby->id
	);
		$this->load->view( $template_path .'/components/icon_upload_modal', $data );

	// delete cover photo modal
	$this->load->view( $template_path .'/components/delete_icon_modal' ); 
?>