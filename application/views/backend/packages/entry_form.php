<?php
	$attributes = array( 'id' => 'package-form', 'enctype' => 'multipart/form-data');
	echo form_open( '', $attributes);
?>
	
<section class="content animated fadeInRight">
	<div class="card card-info">
	    <div class="card-header">
	        <h3 class="card-title"><?php echo get_msg('pkg_info')?></h3>
	    </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
             	<div class="col-md-6">
            		<div class="form-group">
                   		<label>
                   			<span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('title')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('title_tooltips')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<?php echo form_input( array(
							'name' => 'title',
							'value' => set_value( 'title', show_data( @$package->title ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'title' ),
							'id' => 'title'
						)); ?>
              		</div>

					<div class="form-group">
                   		<label>
                   			<span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('post_count')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" post_count="<?php echo get_msg('title_tooltips')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<?php echo form_input( array(
							'name' => 'post_count',
							'value' => set_value( 'post_count', show_data( @$package->post_count ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'post_count' ),
							'id' => 'post_count'
						)); ?>
              		</div>

					<div class="form-group">
						<label>
							<?php echo get_msg('package_app_purchase_product_id_label')?>
						</label>

						<?php echo form_input( array(
							'name' => 'package_in_app_purchased_prd_id',
							'value' => set_value( 'package_in_app_purchased_prd_id', show_data( @$package->package_in_app_purchased_prd_id), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('package_app_purchase_product_id_label'),
							'id' => 'package_in_app_purchased_prd_id'
							
						)); ?>

					</div>

              	</div>

                <div class="col-md-6"  style="padding-left: 50px;">
	               <div class="form-group">
                   		<label>
                   			<span style="font-size: 17px; color: red;">*</span>
							   <?php echo get_msg('price')?><?php echo get_msg('price_pkg_desc')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" price="<?php echo get_msg('title_tooltips')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<?php echo form_input( array(
							'name' => 'price',
							'value' => set_value( 'price', show_data( @$package->price ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'price' ),
							'id' => 'price'
						)); ?>
              		</div>

					<div class="form-group">
						<label> <span style="font-size: 17px; color: red;">*</span>
						<?php echo get_msg('pkg_select_currency')?><?php echo get_msg('currency_pkg_desc')?>
						</label>

						<?php
						$options=array();
						$conds['status'] = 1;
						$options[0]=get_msg('pkg_select_currency');
						$currency = $this->Currency->get_all_by($conds);
						foreach($currency->result() as $curr) {
							$options[$curr->id]=$curr->currency_short_form;
						}

						echo form_dropdown(
							'currency_id',
							$options,
							set_value( 'currency_id', show_data( @$package->currency_id), false ),
							'class="form-control form-control-sm mr-3" id="currency_id"'
						);
						?>
              		</div>

					<div class="form-group">
						<label>
						<?php echo get_msg('package_purchase_type_label')?>
						</label><br>

						<select class="form-control" name="type" id="type">
							<option value="0"><?php echo get_msg('package_purchase_type_label');?></option>

							<?php
								$array = array('IOS' => 'IOS', 'Android' => 'Android');
								foreach ($array as $key=>$value) {
									
									if($value == $package->type) {
									echo '<option value="'.$value.'" selected>'.$key.'</option>';
									} else {
									echo '<option value="'.$value.'">'.$key.'</option>';
									}
								}
							?>
            			</select>

            		</div>
		
              	</div>
              	<!--  col-md-6  -->

            </div>
            <!-- /.row -->
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
    <!-- card info -->
</section>
				
<?php echo form_close(); ?>