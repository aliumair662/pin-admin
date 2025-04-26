
<?php
	$attributes = array( 'id' => 'post-form', 'enctype' => 'multipart/form-data');
	echo form_open( '', $attributes);
?>
	
<section class="content animated fadeInRight">
	<div class="col-md-6">
		<div class="card card-info">
		    <div class="card-header">
		        <h3 class="card-title"><?php echo get_msg('post_info')?></h3>
		    </div>
	        <!-- /.card-header -->
	        <div class="card-body">
	            <div class="row">
	            	<div class="col-md-12">
	            		<div class="form-group">
	                   		<label>
	                   			<span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('post_name')?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('cat_name_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label>

							<?php echo form_input( array(
								'name' => 'name',
								'value' => set_value( 'name', show_data( @$postedby->name ), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg( 'post_name' ),
								'id' => 'name'
							)); ?>
	              		</div>
							
						<div class="form-group" id="color-picker-group">
		                    <label><?php echo get_msg('color')?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('color')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label>
							<!-- product color for edit -->
							<?php if ( isset( $postedby )) { ?>

								<?php 

									$colors = $this->Color->get_all_by( array( 'post_id' => @$postedby->id ))->result(); 
									$color_count = count($colors);
								?>

									

								<?php if ( !empty( $colors )){ ?>

									<?php 
										$i = 0;
										foreach( $colors as $color ): 
										$i++;
									?>
										<div id="<?php echo 'colorvalue' . $i ?>" class="input-group my-colorpicker2">
											<?php echo form_input(array(
												'name' => 'colorvalue' . $i,
												'value' => $color->color_code,
												'class' => 'form-control form-control-sm mt-1',
												'placeholder' => "",
												'id' => 'colorvalue' .$i,
											)); ?>
											<div class="input-group-addon mt-1"><i></i></div>
										</div>
											<?php endforeach; ?>

									<?php }else{ ?>
										<div id="colorvalue1" class="input-group my-colorpicker2">
											<?php echo form_input(array(
												'name' => 'colorvalue1',
												'value' => set_value( 'colorvalue1', show_data( @$postedby->colorvalue1), false ),
												'class' => 'form-control form-control-sm mt-1',
												'placeholder' => "",
												'id' => 'colorvalue1',
											)); ?>
										<div class="input-group-addon mt-1"><i></i></div>
									</div>
									<?php } ?>
								<!-- product color for save -->
								<?php } else { ?>
									<div id="colorvalue1" class="input-group my-colorpicker2">
										<?php echo form_input(array(
											'name' => 'colorvalue1',
											'value' => set_value( 'colorvalue1', show_data( @$postedby->colorvalue1), false ),
											'class' => 'form-control form-control-sm mt-1',
											'placeholder' => "",
											'id' => 'colorvalue1',
										)); ?>
										<div class="input-group-addon mt-1"><i></i></div>
									</div>
								<?php } ?>
		              		</div>	
		              		<div class="mt-2">
							<a id="addColor" class="pull-right">
							</a>
			   		</div>
	              	</div>		
	            <!-- /.row -->
	        	</div>
	        <!-- /.card-body -->
	   		</div>

	   		<?php 
			if (isset($color_count)) {
				$color_count = $color_count;
			} else {
				$color_count = 0;
			} 
		   ?>
		   <input type="hidden" id="color_total_existing" name="color_total_existing" value="<?php echo $color_count; ?>">

	   		<?php 
			if ( isset( $postedby )) { 
			?>
				<input type="hidden" id="edit_product" name="edit_product" value="1">
			<?php		
				} else {
			?>
				<input type="hidden" id="edit_product" name="edit_product" value="0">
			<?php } ?> 
			
			<div class="card-footer">
	            <button type="submit" class="btn btn-sm btn-primary">
					<?php echo get_msg('btn_save')?>
				</button>

				<a href="<?php echo $module_site_url; ?>" class="btn btn-sm btn-primary">
					<?php echo get_msg('btn_cancel')?>
				</a>
	        </div>
	       
		</div>

	</div>
</section>
				

	
	

<?php echo form_close(); ?>