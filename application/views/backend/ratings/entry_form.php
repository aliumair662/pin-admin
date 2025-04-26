<?php
	$attributes = array('id' => 'rating-form');
	echo form_open( '', $attributes );
?>
<div class="card card-info  animated fadeInRight">
	<div class="card-header">
        <h3 class="card-title"><?php echo get_msg('user_info')?></h3>
    </div>

	<div id="perm_err" class="alert alert-danger fade in" style="display: none">
		<label for="permissions[]" class="error"></label>
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	</div>
	<!-- /.card-header -->
    <div class="card-body">	
      <div class="row">
        <div class="col-6">
            <div class="form-group">
              <label><?php echo get_msg('user_name')?></label>

              <?php echo form_input(array(
                'name' => 'user_name',
                'value' => set_value( 'user_name', show_data( @$rating->user_name ), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('user_name'),
                'id' => 'name',
                'readonly' => 'true'
              )); ?>

            </div>
            
            <div class="form-group">
              <label><?php echo get_msg('user_email')?></label>

              <?php echo form_input(array(
                'name' => 'user_email',
                'value' => set_value( 'user_email', show_data( @$rating->user_email ), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('user_email'),
                'id' => 'user_email',
                'readonly' => 'true'
              )); ?>

            </div>
            <div class="form-group">
              <label>
                <?php echo get_msg('overall_rating') ." : " ;?>
                <?php 
						$i = 1;
						while($i<=5){
							if($rating->overall_rating>=1)
								echo '<i class="fa fa-star"></i>';
							else if($rating->overall_rating == 0.5)
								echo '<i class="fa fa-star-half-o"></i>';
							else
								echo '<i class="fa fa-star-o"></i>';
							$rating->overall_rating -= 1;
							$i += 1;
						}
					?>
              </label>
            </div>
            
            <div class="form-group">
            <label>
                <?php echo get_msg('verify_agent') . ' : ' ?> <?php echo @$rating->apply_to ==1? 'Yes': 'No';?>
              </label>
            </div>
            
          </div>
          <div class="col-6">
          <div class="form-group">
              <label><?php echo get_msg('user_phone')?></label>

              <?php echo form_input(array(
                'name' => 'user_phone',
                'value' => set_value( 'user_phone', show_data( @$rating->user_phone ), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('user_email'),
                'id' => 'user_phone',
                'readonly' => 'true'
              )); ?>

            </div>

            <div class="form-group">	
						<label><?php echo get_msg('user_address'); ?></label>
						<?php echo form_input( array(
							'name' => 'user_address',
							'value' => set_value( 'user_address', show_data( @$rating->user_address ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'user_address' ),
							'id' => 'user_address',
              'readonly' => 'true'
						)); ?>
					</div>
          <div class="form-group">
              <label><?php echo get_msg('added_date')?></label>

              <?php echo form_input(array(
                'name' => 'added_date',
                'value' => set_value( 'added_date', show_data( @$rating->added_date ), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('user_email'),
                'id' => 'added_date',
                'readonly' => 'true'
              )); ?>

            </div>
        </div>
      </div>
    </div>
	
	<div class="card-footer">
		<a href="<?php echo $module_site_url; ?>" class="btn btn-sm btn-primary"><?php echo get_msg('btn_back')?></a>
	</div>
</div>
<?php echo form_close(); ?>