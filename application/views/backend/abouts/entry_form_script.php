<script>
	function jqvalidate() {

		$(document).ready(function(){
			$('#about-form').validate({
				rules:{
					title:{
						required: true,
						minlength: 4
					}
				},
				messages:{
					title:{
						required: "<?php echo get_msg('err_title') ?>",
						minlength: "<?php echo get_msg('err_title_len') ?>"
					}
				}
			});
		});
	}

	

		$('.delete-img').click(function(e){
			e.preventDefault();

			// get id and image
			var id = $(this).attr('id');

			// do action
			var action = '<?php echo $module_site_url .'/delete_cover_photo/'; ?>' + id + '/<?php echo @$about->about_id; ?>';
			console.log( action );
			$('.btn-delete-image').attr('href', action);
		});

	function runAfterJQ() {
		CKEDITOR.editorConfig = function( config ) {
				config.toolbarGroups = [
					{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
					{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
					{ name: 'links', groups: [ 'links' ] },
					{ name: 'insert', groups: [ 'insert' ] },
					{ name: 'forms', groups: [ 'forms' ] },
					{ name: 'tools', groups: [ 'tools' ] },
					{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
					{ name: 'others', groups: [ 'others' ] },
					'/',
					{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
					{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
					{ name: 'styles', groups: [ 'styles' ] },
					{ name: 'colors', groups: [ 'colors' ] },
					{ name: 'about', groups: [ 'about' ] }
				];

				config.removeButtons = 'BGColor,Styles,Format,Anchor,Image,Table,HorizontalRule,SpecialChar,Source,NumberedList,BulletedList,Indent,Outdent';
			};

			CKEDITOR.replace( 'ckeditor' );
	}

</script>
<?php
	// replace cover photo modal
	$data = array(
		'title' => get_msg('upload_photo'),
		'img_type' => 'about',
		'img_parent_id' => @$about->about_id
	);

	$this->load->view( $template_path .'/components/photo_upload_modal', $data );

	// delete cover photo modal
	$this->load->view( $template_path .'/components/delete_cover_photo_modal' ); 

	// replace icon icon modal
	$data = array(
		'title' => get_msg('about_upload_icon'),
		'img_type' => 'nav',
		'img_parent_id' => @$about->about_id
	);
	$this->load->view( $template_path .'/components/sidebar_logo_upload', $data );

	$data = array(
		'title' => get_msg('about_upload_icon'),
		'img_type' => 'fav',
		'img_parent_id' => @$about->about_id
	);
	$this->load->view( $template_path .'/components/favicon_upload_modal', $data );
	// delete icon photo modal
	$this->load->view( $template_path .'/components/delete_icon_modal' ); 
?>>