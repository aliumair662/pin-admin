<script>

	<?php if ( $this->config->item( 'client_side_validation' ) == true ): ?>
	
	function jqvalidate() {
		$('#agent-form').validate({
			rules:{
				user_name:{
					required: true,
					minlength: 4
				},
				<?php if($agent->email_verify == 1): ?>
				user_email:{
					required: true,
					email: true,
					remote: '<?php echo $module_site_url ."/ajx_exists/". @$agent->user_id ; ?>'
				},
				<?php endif; ?>
				<?php if($agent->phone_verify == 1): ?>
				user_phone:{
					required: true,
					remote: '<?php echo $module_site_url ."/ajx_exists_phone/". @$agent->user_id ; ?>'
				},
				<?php endif; ?>
				<?php if ( !isset( $agent )): ?>
				user_password:{
					required: true,
					minlength: 4
				},
				conf_password:{
					required: true,
					equalTo: '#user_password'
				},
				<?php endif; ?>
				"permissions[]": { 
					required: true, 
					minlength: 1 
				}
			},
			messages:{
				user_name:{
					required: "<?php echo get_msg( 'err_user_name_blank' ); ?>",
					minlength: "<?php echo get_msg( 'err_user_name_len' ); ?>"
				},
				user_email:{
					required: "<?php echo get_msg( 'err_user_email_blank' ); ?>",
					email: "<?php echo get_msg( 'err_user_email_invalid' ); ?>",
					remote: "<?php echo get_msg( 'err_user_email_exist' ); ?>"
				},
				user_phone:{
					required: "<?php echo get_msg( 'err_user_phone_blank' ); ?>",
					remote: "<?php echo get_msg( 'err_user_phone_exist' ); ?>"
				},
				<?php if ( !isset( $agent )): ?>
				user_password:{
					required: "<?php echo get_msg( 'err_user_pass_blank' ); ?>",
					minlength: "<?php echo get_msg( 'err_user_pass_len' ); ?>"
				},
				conf_password:{
					required: "<?php echo get_msg( 'err_user_pass_conf_blank' ); ?>",
					equalTo: "<?php echo get_msg( 'err_user_pass_conf_not_match' ); ?>"
				},
				<?php endif; ?>
				"permissions[]": "<?php echo get_msg( 'err_permission_blank' ); ?>"
			},
			errorPlacement: function(error, element) {
				console.log( $(error).text());
				if (element.attr("name") == "permissions[]" ) {
					console.log( $(error).text());
					$("#perm_err label").html($(error).text());
					$("#perm_err").show();
				} else {
					error.insertAfter(element);
				}
			}
		});

	<?php endif; ?>

</script>

<?php 
	// replace cover photo modal
	$data = array(
		'title' => get_msg('upload_photo'),
		'img_type' => 'agent',
		'img_parent_id' => @$agent->user_id
	);

	$this->load->view( $template_path .'/components/photo_upload_modal', $data );

	// delete cover photo modal
	$this->load->view( $template_path .'/components/delete_cover_photo_modal' ); 
?>