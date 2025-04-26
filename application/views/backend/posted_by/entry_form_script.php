<script>

	<?php if ( $this->config->item( 'client_side_validation' ) == true ): ?>

	function jqvalidate() {

		$('#post-form').validate({
			rules:{
				name:{
					blankCheck : "",
					minlength: 3,
					remote: "<?php echo $module_site_url .'/ajx_exists/'.@$postedby->id; ?>"
				}
			},
			messages:{
				name:{
					blankCheck : "<?php echo get_msg( 'err_post_name' ) ;?>",
					minlength: "<?php echo get_msg( 'err_post_len' ) ;?>",
					remote: "<?php echo get_msg( 'err_post_exist' ) ;?>."
				}
			}
		});
	}

	<?php endif; ?>

	function runAfterJQ() {

		// colorpicker
		$('.my-colorpicker2').colorpicker();

		$(document).ready(function () {

     		var edit_product_check = $('#edit_product').val();


     		if(edit_product_check == 0) {
     			//new product
     			var counter = 2;
     		} else {
     			//edit product
     			var counter =  parseInt($('#color_total_existing').val())+2;
     		}
     		$('#color_total_existing').val(counter);
     		$("#addColor").click(function () {
      			
				
      		 	var newTextBoxDiv = $(document.createElement('div'))
	     		.attr("id", "colorvalue"+counter)
	     		.attr("class", 'input-group my-colorpicker2 colorpicker-element');

	     		newTextBoxDiv.after().html(
	      		'<input class="form-control form-control-sm mt-1" type="text" name="colorvalue' + counter + 
	      		'" id="colorvalue' + counter + '" value="" ><div class="input-group-addon mt-1"><i></i></div>');

	      		newTextBoxDiv.appendTo("#color-picker-group");
				$('#colorvalue'+counter).colorpicker({});
				counter++;

				$( ".CounterTextBoxDiv" ).remove();
				var newCounterTextBoxDiv = $(document.createElement('div'))
	     		.attr("id", 'CounterTextBoxDiv' + counter);

	     		newCounterTextBoxDiv.after().html(
	      		'<input type="hidden" name="color_total" id="color_total" value=" '+ counter +'" >');

	      		newCounterTextBoxDiv.appendTo(".my-colorpicker2");

	      		

      		});
         });
	}


</script>