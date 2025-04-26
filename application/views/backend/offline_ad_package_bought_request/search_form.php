<?php
	$attributes = array('id' => 'search-form', 'enctype' => 'multipart/form-data');
	echo form_open( $module_site_url .'/search', $attributes);
?>

<div class='row my-3'>
	<div class="col-12">
		<div class='form-inline'>
			<div class="form-group" style="padding-top: 3px;padding-right: 2px;">

				<?php echo form_input(array(
					'name' => 'searchterm',
					'value' => set_value( 'searchterm', $searchterm ),
					'class' => 'form-control form-control-sm mr-3',
					'placeholder' => get_msg( 'btn_search' )
				)); ?>

		  	</div>

		  	<div class="form-group" style="padding-top: 3px;">
						
				<select class="form-control form-control-sm mr-3" name="status" id="status">

					<option value="0"><?php echo get_msg('select_payment_status_label');?></option>
							
						<?php

							$array = array('Paid' => 1, 'Reject' => 2, 'Waiting For Pay' => 3);
	    					foreach ($array as $key=>$value) {
	    						if($value == $status) {
		    						echo '<option value="'.$value.'" selected>'.$key.'</option>';
		    					} else {
		    						echo '<option value="'.$value.'">'.$key.'</option>';
		    					}
	    					}
						?>
				</select> 
			</div>

			

		  	<div class="form-group" style="padding-top: 3px;padding-right: 5px;">
			  	<button type="submit" value="submit" name="submit" class="btn btn-sm btn-primary">
			  		<?php echo get_msg( 'btn_search' )?>
			  	</button>
		  	</div>
		
			<div class="form-group" style="padding-top: 3px;">
			  	<a href='<?php echo $module_site_url .'/index';?>' class='btn btn-sm btn-primary'>
					<?php echo get_msg( 'btn_reset' )?>
				</a>
		  	</div>

		</div>
	</div>

</div>

<?php echo form_close(); ?>

