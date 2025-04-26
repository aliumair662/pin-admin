<?php
	$attributes = array('id' => 'search-form', 'enctype' => 'multipart/form-data');
	echo form_open( $module_site_url .'/search', $attributes);
?>

<div class='row'>
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
			
			<div class="form-group">
				<?php
				
					echo get_msg( 'status_label');
					$options=array();
					$options[0]=get_msg('select_status_label');
					$status = $this->Reported_item_status->get_all( );
					foreach($status->result() as $sat) {
						$options[$sat->id]=$sat->title;
					}
					
					echo form_dropdown(
						'reported_status',
						$options,
						set_value( 'reported_status', show_data( $reported_status ), false ),
						'class="form-control form-control-sm mr-3 ml-2" id="reported_status"'
					);
				
				?>
			</div>

			<div class="input-group" style="padding-top: 5px;">
				<div class="input-group-prepend">
					<span class="input-group-text">
						<i class="fa fa-calendar"></i>
					</span>
				</div>
				<?php 
					echo form_input(array(
						'name' => 'date',
						'value' => set_value( 'date' , $date),
						'class' => 'form-control',
						'placeholder' => '',
						'id' => 'reservation',
						'size' => '21',
						'readonly' => 'readonly'
					)); ?>

			</div>

			<div class="form-group" style="padding-top: 3px;padding-right: 5px; padding-left:10px;">
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
