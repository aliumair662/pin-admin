<div class="modal fade"  id="deleteDemoDataModal">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header bg-secondary" style="opacity:0.8;">
			
				<div class="modal-title ml-2"><h4 class="pt-2"><?php echo get_msg('delete_demo_data'); ?></h4></div>
				
				<button class="close" data-dismiss="modal">				
					<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
				</button>

			</div>

			<?php
				$attributes = array('id' => 'check-admin-form','enctype' => 'multipart/form-data');
				echo form_open( $module_site_url ."/delete_demo_data/", $attributes);
			?>

				<div class="modal-body px-5 pb-4">
					<div class="form-group">
						<div class="row text-center d-flex py-4">
							<div class="col-1 justify-content-center align-self-center"><span><i class="fa fa-exclamation-triangle text-warning " style="font-size: 30px;"></i></span></div>
							<h5 class="col text-center"><?php echo get_msg('admin_confirm') ?></h5>
						</div>
						<div class="input-group">
							<?php echo form_input(array(
								'type' => 'password',
								'name' => 'password',
								'class' => 'form-control',
								'placeholder' => get_msg( 'admin_pwd_label' ),
								'id' => 'password'
							)); ?>
						</div>
					</div>

				</div>

				<div class="modal-footer">

					<input type="submit" value="<?php echo get_msg('btn_submit') ?>" class="btn btn-sm btn-primary"/>

					<a href="" class="btn btn-sm btn-primary" data-dismiss="modal"><?php echo get_msg('btn_cancel')?></a>

				</div>
			
				<?php echo form_close(); ?>

		</div>

	</div>

</div>
