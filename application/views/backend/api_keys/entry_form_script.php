<script>
	function jqvalidate() {

		$(document).ready(function(){
			$('#apikey-form').validate({
				rules:{
					key:{
						required: true,
						minlength: 4
					}
				},
				messages:{
					key:{
						required: "<?php echo get_msg('err_key') ?>",
						minlength: "<?php echo get_msg('err_key_len') ?>"
					}
				}
			});
		});
	}

</script>