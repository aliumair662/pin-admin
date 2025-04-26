<?php
	$attributes = array( 'id' => 'propertyby-form', 'enctype' => 'multipart/form-data');
	echo form_open( '', $attributes);
?>
	
<section class="content animated fadeInRight">
	<div class="card card-info">
	    <div class="card-header">
	        <h3 class="card-title"><?php echo get_msg('property_info')?></h3>
	    </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
             	<div class="col-md-6">
            		<div class="form-group">
                   		<label>
                   			<span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('property_name')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('cat_name_tooltips')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<?php echo form_input( array(
							'name' => 'name',
							'value' => set_value( 'name', show_data( @$propertyby->name ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'property_name' ),
							'id' => 'name'
						)); ?>
              		</div>

              	</div>

                <div class="col-md-6"  style="padding-left: 50px;">
	                <?php if ( !isset( $propertyby )): ?>

					<div class="form-group">
						<span style="font-size: 17px; color: red;">*</span>
						<label><?php echo get_msg('propertyby_img')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('cat_photo_tooltips')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

                        <p class="mb-0 d-inline-block">
                            (<?php echo get_msg('recommended_size_img')?>)
                        </p>

						<br/>

						<input class="btn btn-sm" type="file" name="cover" id="cover">
					</div>

						<?php else: ?>
						<span style="font-size: 17px; color: red;">*</span>
						<label><?php echo get_msg('propertyby_img')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('cat_photo_tooltips')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label> 
					
					<div class="btn btn-sm btn-primary btn-upload pull-right" data-toggle="modal" data-target="#uploadImage">
						<?php echo get_msg('btn_replace_photo')?>
					</div>

                    <br>

                        <p class="mb-0">
                            <?php echo get_msg('recommended_size_img')?>
                        </p>


                        <hr/>
				
					<?php
						$conds = array( 'img_type' => 'propertyby-cover', 'img_parent_id' => $propertyby->id );
						$images = $this->Image->get_all_by( $conds )->result();
					?>
						
					<?php if ( count($images) > 0 ): ?>
						
						<div class="row">

						<?php $i = 0; foreach ( $images as $img ) :?>

							<?php if ($i>0 && $i%3==0): ?>
									
							</div><div class='row'>
							
							<?php endif; ?>
								
							<div class="col-md-4" style="height:100">

								<div class="thumbnail">

									<img src="<?php echo $this->ps_image->upload_thumbnail_url . $img->img_path; ?>">

									<br/>
									
									<p class="text-center">
										
										<a data-toggle="modal" data-target="#deletePhoto" class="delete-img" id="<?php echo $img->img_id; ?>"   
											image="<?php echo $img->img_path; ?>">
											<?php echo get_msg('remove_label'); ?>
										</a>
									</p>

								</div>

							</div>

						<?php $i++; endforeach; ?>

						</div>
					
					<?php endif; ?>

				<?php endif; ?>	
				<!-- End Category cover photo -->
				<?php if ( !isset( $propertyby )): ?>

					<div class="form-group">
						<span style="font-size: 17px; color: red;">*</span>
						<label>
							<?php echo get_msg('propertyby_icon')?> 
						</label>

                        <p class="mb-0 d-inline-block">
                            (<?php echo get_msg('recommended_size_icon')?>)
                        </p>

                        <br/>

						<input class="btn btn-sm" type="file" name="icon" id="icon">
					</div>

				<?php else: ?>
					<span style="font-size: 17px; color: red;">*</span>
					<label><?php echo get_msg('propertyby_icon')?></label> 
					
					
					<div class="btn btn-sm btn-primary btn-upload pull-right" data-toggle="modal" data-target="#uploadIcon">
						<?php echo get_msg('btn_replace_icon')?>
					</div>

                    <br>

                    <p class="mb-0">
                        <?php echo get_msg('recommended_size_img')?>
                    </p>


                    <hr/>
					
					<?php

						$conds = array( 'img_type' => 'propertyby-icon', 'img_parent_id' => $propertyby->id );
						
						//print_r($conds); die;
						$images = $this->Image->get_all_by( $conds )->result();
					?>
						
					<?php if ( count($images) > 0 ): ?>
						
						<div class="row">

						<?php $i = 0; foreach ( $images as $img ) :?>

							<?php if ($i>0 && $i%3==0): ?>
									
							</div><div class='row'>
							
							<?php endif; ?>
								
							<div class="col-md-4" style="height:100">

								<div class="thumbnail">

									<img src="<?php echo $this->ps_image->upload_thumbnail_url . $img->img_path; ?>" width="200" height="200">

									<br/>
									
									<p class="text-center">
										
										<a data-toggle="modal" data-target="#deleteIcon" class="delete-img" id="<?php echo $img->img_id; ?>"   
											image="<?php echo $img->img_path; ?>">
											<?php echo get_msg('remove_label'); ?>
										</a>
									</p>

								</div>

							</div>

						<?php endforeach; ?>

						</div>
					
					<?php endif; ?>

				<?php endif; ?>	
		
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