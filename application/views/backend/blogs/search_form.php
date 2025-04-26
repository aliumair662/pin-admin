<div class='row my-3'>

	<div class='col-6'>
	<?php
		$attributes = array('class' => 'form-inline');
		echo form_open( $module_site_url .'/search', $attributes);
	?>
		
		<div class="form-group" style="padding-right: 3px;">

			<?php echo form_input(array(
				'name' => 'searchterm',
				'value' => set_value( 'searchterm' ),
				'class' => 'form-control form-control-sm',
				'placeholder' => get_msg( 'btn_search' )
			)); ?>

	  	</div>
	  	<div class="form-group" style="padding-top: 3px;padding-right: 2px;">

			<?php
				$options=array();
				$options[1]=get_msg('all_location_label');
				
				$locations = $this->Itemlocation->get_all( );
				foreach($locations->result() as $location) {
					
					$options[$location->id]=$location->name;
				}
				
				echo form_dropdown(
					'item_location_id',
					$options,
					set_value( 'item_location_id', show_data( $item_location_id ), false ),
					'class="form-control form-control-sm mr-2" id="item_location_id"'
				);
			?> 

	  	</div>

		<div class="form-group" style="padding-right: 2px;">
		  	<button type="submit" class="btn btn-sm btn-primary">
		  		<?php echo get_msg( 'btn_search' )?>
		  	</button>
	  	</div>
	
		<div class="form-group">
		  	<a href='<?php echo $module_site_url .'/index';?>' class='btn btn-sm btn-primary'>
			<?php echo get_msg( 'btn_reset' )?>
		</a>
	  	</div>

	<?php echo form_close(); ?>

	</div>	

	<div class='col-6'>
		<a href='<?php echo $module_site_url .'/add';?>' class='btn btn-sm btn-primary pull-right'>
			<span class='fa fa-plus'></span> 
			<?php echo get_msg( 'blog_add' )?>
		</a>
	</div>

</div>