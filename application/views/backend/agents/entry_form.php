<?php
	$attributes = array('id' => 'agent-form');
	echo form_open( '', $attributes );
?>
<section class="content animated fadeInRight">

	<div class="card card-info">
		<div class="card-header">
	      <h3 class="card-title"><?php echo get_msg('user_info')?></h3>
	    </div>

	   <div class="card-body">
    		<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label><?php echo get_msg('user_name'); ?></label>
						<?php echo form_input( array(
							'name' => 'user_name',
							'value' => set_value( 'user_name', show_data( @$agent->user_name ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'user_name' ),
							'id' => 'user_name',
							'readonly' => 'readonly'
						)); ?>
					</div>

					<?php if($agent->email_verify == 1): ?>
					<div class="form-group">
						<label><?php echo get_msg('user_email'); ?></label>
						<?php echo form_input( array(
							'name' => 'user_email',
							'value' => set_value( 'user_email', show_data( @$agent->user_email ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'user_email' ),
							'id' => 'user_email',
							'readonly' => 'readonly'
						)); ?>
					</div>
					<?php endif; ?>

					<?php if($agent->phone_verify == 1): ?>
					<div class="form-group">
						<label><?php echo get_msg('user_phone'); ?></label>
						<?php echo form_input( array(
							'name' => 'user_phone',
							'value' => set_value( 'user_phone', show_data( @$agent->user_phone ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'user_phone' ),
							'id' => 'user_phone',
							'readonly' => 'readonly'
						)); ?>
					</div>
					<?php endif; ?>
				</div>

				<div class="col-md-6">
					<div class="form-group">	
						<label><?php echo get_msg('user_address'); ?></label>
						<?php echo form_input( array(
							'name' => 'user_address',
							'value' => set_value( 'user_address', show_data( @$agent->user_address ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'user_address' ),
							'id' => 'user_address',
							'readonly' => 'readonly'
						)); ?>
					</div>
					<div class="form-group">	
						<label><?php echo get_msg('user_city'); ?></label>
						<?php echo form_input( array(
							'name' => 'city',
							'value' => set_value( 'city', show_data( @$agent->city ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'city' ),
							'id' => 'city',
							'readonly' => 'readonly'
						)); ?>
					</div>
					<div class="form-group">	
						<label><?php echo get_msg('about_me'); ?></label>
						<?php echo form_input( array(
							'name' => 'user_about_me',
							'value' => set_value( 'user_about_me', show_data( @$agent->user_about_me ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'about_me' ),
							'id' => 'user_about_me',
							'readonly' => 'readonly'
						)); ?>
					</div>
				</div>


		    </div>
		     <!-- row -->
	        <hr>
	        <div class="form-group" style="background-color: #edbbbb; padding: 20px;">
	          <label>
	            <strong><?php echo get_msg('select_status')?></strong>
	          </label>

	            <select class="form-control" name="application_status" id="application_status">
						<!-- <option value="0"><?php echo get_msg('select_status');?></option> -->

						<?php
						$array = array('Approved' => 1,'Reject' => 3);
	    					foreach ($array as $key=>$value) {
	    						
	    						if($value == $agent->application_status) {
		    						echo '<option value="'.$value.'" selected>'.$key.'</option>';
		    					} else {
		    						echo '<option value="'.$value.'">'.$key.'</option>';
		    					}
	    					}
						?>
					</select>
	        </div>
	      </div>
		</div>
		 <!-- /.card-body -->

		<div class="card-footer">
            <button type="submit" class="btn btn-sm btn-primary">
				<?php echo get_msg('btn_save')?>
			</button>

			<a href="<?php echo $module_site_url; ?>" class="btn btn-sm btn-primary">
				<?php echo get_msg('btn_cancel')?>
			</a>
        </div>
	</div>
</section>

<?php echo form_close(); ?>