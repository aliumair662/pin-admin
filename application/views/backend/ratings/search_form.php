<div class='row my-3'>
	<div class='col-9'>
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
	</div>
	<div class='col-3'>
		<a href='<?php echo $module_site_url .'/export_csv/' ;?>' class='btn btn-sm btn-primary pull-right'>
			<span class='fa fa-download'></span>
			<?php echo get_msg('csv_export') ?>
		</a>
	</div>
</div>