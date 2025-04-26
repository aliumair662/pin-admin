<?php
	$attributes = array('user_id' => 'search-form', 'enctype' => 'multipart/form-data');
	echo form_open( $module_site_url .'/search', $attributes);
?>

<div class='row my-3'>
	<div class='form-inline'>
			<div class="form-group mr-3">
				
				<?php echo form_input(array(
					'name' => 'searchterm',
					'value' => set_value( 'searchterm', $searchterm ),
					'class' => 'form-control form-control-sm',
					'placeholder' => get_msg( 'btn_search' ),
					'id' => ''
				)); ?>

		  	</div>

	        <div class="form-group" style="padding-top: 3px;padding-right: 2px;">
						
				<select class="form-control form-control-sm mr-3" name="application_status" id="application_status">
							
					<option value="0"><?php echo get_msg('select_status_label');?></option>

						<?php
							$array = array('Pending' => 2, 'Approved' => 1 ,'Reject' => 3);
	    					foreach ($array as $key=>$value) {

	    						if($value == $application_status) {
		    						echo '<option value="'.$value.'" selected>'.$key.'</option>';
		    					} else {
		    						echo '<option value="'.$value.'">'.$key.'</option>';
		    					}
	    					}
						?>
				</select> 
			</div>

			<div class="form-group mr-3">
			  	<button type="submit" class="btn btn-sm btn-primary" name="submit" value="submit">
			  		<?php echo get_msg( 'btn_search' ); ?>
			  	</button>
		  	</div>

		  	<div class="form-group">
			  	<a href="<?php echo $module_site_url ; ?>" class="btn btn-sm btn-primary">
			  		<?php echo get_msg( 'btn_reset' ); ?>
			  	</a>
		  	</div>
	
	</div>
</div>

<?php echo form_close(); ?>