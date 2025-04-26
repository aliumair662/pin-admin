<?php
$attributes = array('id' => 'thumbnail-form','enctype' => 'multipart/form-data');
echo form_open( '', $attributes);
?>
<section class="content animated fadeInRight">
	<div class="col-md-12">
		<div class="card card-info">
			<div class="card-header">
		        <h3 class="card-title"><?php echo get_msg('demo_data_delete')?></h3>
		    </div>


			<div class="card-body">
				<div class="row px-3">
					<div class = "col-6" >
						<div class="form-group"><h5><b><?php echo get_msg('entry_group')?></b></h5></div>
						<?php foreach($entries->result() as $entry) : ?>
							<div class=" ml-2 form-group">
							<i class="fa fa-clipboard"></i><span for="">  <?php echo get_msg($entry->module_lang_key)?></span>
							</div>
						
						<?php endforeach ?>
						
						<div class="form-group mt-4"><h5><b><?php echo get_msg('user_mang_group')?></b></h5></div>
							<div class="ml-2 form-group">
							<i class="fa fa-clipboard"></i><span for="">  <?php echo get_msg('register_user_module')?></span>
						</div>
						<div class="form-group"><h5><b><?php echo get_msg('images')?></b></h5></div>
						<div class="ml-2 form-group">
							<i class="fa fa-clipboard"></i><span for="">  <?php echo get_msg('img_all_delete'); ?></span>
						</div>
					</div>
					<div class = "col-6" >
						<div class="form-group"><h5><b><?php echo get_msg('approval_group')?></b></h5></div>
						<?php foreach($approvals->result() as $approval) : ?>
						
							<div class="ml-2 form-group">
							<i class="fa fa-clipboard"></i><span for="">  <?php echo get_msg($approval->module_lang_key)?></span>
							</div>
						
						<?php endforeach ?>
						<div class="form-group mt-4"><h5><b><?php echo get_msg('reports_group')?></b></h5></div>
						<?php foreach($reports->result() as $report) : ?>
						
							<div class="ml-2 form-group">
							<i class="fa fa-clipboard"></i><span for="">  <?php echo get_msg($report->module_lang_key)?></span>
							</div>
						
						<?php endforeach ?>
						<div class="form-group mt-4"><h5><b><?php echo get_msg('miscellaneous_group')?></b></h5></div>
							<div class="ml-2 form-group">
							<i class="fa fa-clipboard"></i><span for="">  <?php echo get_msg('noti_info')?></span>
						</div>
						<div class="form-group mt-4"><h5><b><?php echo get_msg('setting_group')?></b></h5></div>
						<div class="ml-2 form-group">
							<i class="fa fa-clipboard"></i><span for="">  <?php echo get_msg('offline_payment')?></span>
						</div>
						<div class="ml-2 form-group">
							<i class="fa fa-clipboard"></i><span for="">  <?php echo get_msg('in_app_purchases')?></span>
						</div>
						
					</div>
					
				</div>
				<div class="row">
					<div class="col-12 mt-3">
						<p><b> <?php echo get_msg('demo_data_delete_note_label'); ?> </b></p>
					</div>
	        	</div>
		    </div>
			<div class="card-footer">
				<?php if($available == 1) : ?>
					<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#deleteDemoDataModal" >
						<?php echo get_msg('btn_delete')?>
					</button>
				<?php else: ?>
					<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#deleteDemoDataModal" disabled>
					<?php echo get_msg('btn_delete')?>
				</button>
				<?php endif; ?>
				<a href="<?php echo $module_site_url; ?>" class="btn btn-sm btn-primary">
					<?php echo get_msg('btn_cancel')?>
				</a>
		    </div>
		</div>
	</div>
</section>


<?php echo form_close(); ?>

<?php 
	// delete cover photo modal
	$this->load->view( $template_path .'/components/delete_demo_data_modal' ); 
?>