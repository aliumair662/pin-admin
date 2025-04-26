<?php
	$attributes = array( 'id' => 'history-form', 'enctype' => 'multipart/form-data');
	echo form_open( '', $attributes);
?>
	
<section class="content animated fadeInRight">
	<div class="card card-info">
	    <div class="card-header">
	        <h3 class="card-title"><?php echo get_msg('trans_history_info')?></h3>
	    </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
            	<div class="col-md-6">
            		
					<div class="form-group">
                   		<label>
                   			<span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('amount_label') . ' ( '. $this->Currency->get_one($this->Package->get_one($trans->package_id)->currency_id)->currency_symbol . ' )' ; ?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('amount_label')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<?php echo form_input( array(
							'name' => 'price',
							'value' => set_value( 'price', show_data( @$trans->price ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'amount_label' ),
							'id' => 'price',
							'readonly' => 'true'
						)); ?>
              		</div>
						
            		<div class="form-group">
                   		<label>
                   			<span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('date')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('start_date_label')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<?php echo form_input( array(
							'name' => 'added_date',
							'value' => set_value( 'added_date', show_data( @$trans->added_date ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'date' ),
							'id' => 'added_date',
							'readonly' => 'true'
						)); ?>
              		</div>

            	</div>

            	<div class="col-md-6">
              		<div class="form-group">
                   		<label>
                   			<span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('payment_method_label')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('payment_method_label')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<?php echo form_input( array(
							'name' => 'payment_method',
							'value' => set_value( 'payment_method', show_data( @$trans->payment_method ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'payment_method_label' ),
							'id' => 'payment_method',
							'readonly' => 'true'
						)); ?>
              		</div>

					<div class="form-group">
                   		<label>
                   			<span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('user_name')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('user_name')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<?php echo form_input( array(
							'name' => 'user_name',
							'value' => set_value( 'user_name', show_data( @$user->user_name ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'user_name' ),
							'id' => 'user_name',
							'readonly' => 'true'
						)); ?>
              		</div>
            	</div>
            </div>
        </div>

        <hr>
        <div class="card-header">
	    	<h3 class="card-title"><?php echo get_msg('pkg_info')?></h3>
	  	</div>

	  	<div class="card-body">
	      	<div class="row">
	      		<div class="col-md-6">
					<div class="form-group">
					<label> <span style="font-size: 17px; color: red;">*</span>
						<?php echo get_msg('itm_title_label')?>
					</label>

					<?php echo form_input( array(
						'name' => 'title',
						'value' => set_value( 'title', show_data( @$package->title), false ),
						'class' => 'form-control form-control-sm',
						'placeholder' => get_msg('itm_title_label'),
						'id' => 'title',
						'readonly' => 'true'
					)); ?>

					</div>

					<div class="form-group">
					<label> <span style="font-size: 17px; color: red;">*</span>
						<?php echo get_msg('post_count')?>
					</label>

					<?php echo form_input( array(
						'name' => 'post_count',
						'value' => set_value( 'post_count', show_data( @$package->post_count), false ),
						'class' => 'form-control form-control-sm',
						'placeholder' => get_msg('post_count'),
						'id' => 'post_count',
						'readonly' => 'true'
					)); ?>

					</div>
	            <!-- form group -->
	          </div>

	          <div class="col-md-6">
	            <div class="form-group">
	              <label> <span style="font-size: 17px; color: red;">*</span>
	                <?php echo get_msg('price') . ' ( '. $this->Currency->get_one($this->Package->get_one($trans->package_id)->currency_id)->currency_symbol . ' )' ; ?>
	              </label>

	              <?php echo form_input( array(
	                'name' => 'price',
	                'value' => set_value( 'price', show_data( @$package->price), false ),
	                'class' => 'form-control form-control-sm',
	                'placeholder' => get_msg('price'),
	                'id' => 'price',
	                'readonly' => 'true'
	              )); ?>

	            </div>

	          </div>
	          
	        <!-- row -->
	    </div>
		
    </div>
	<div class="card-footer">
		<a href="<?php echo $module_site_url; ?>" class="btn btn-sm btn-primary"><?php echo get_msg('btn_back')?></a>
	</div>
    <!-- card info -->
</section>
				
<?php echo form_close(); ?>