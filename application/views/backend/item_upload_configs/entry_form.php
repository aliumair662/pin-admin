<?php
$attributes = array('id' => 'item-config-form','enctype' => 'multipart/form-data');
echo form_open( '', $attributes);
?>
<section class="content animated fadeInRight">
	<div class="card card-info">
		 <div class="card-header">
	        <h3 class="card-title"><?php echo get_msg('item_config_label')?></h3>
	    </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
				<div class="col-md-6">
                    <div class="form-group">
						<div class="form-check">
							<label class="form-check-label">
							<?php echo form_checkbox( array(
								'name' => 'configuration',
								'id' => 'configuration',
								'value' => 'accept',
								'checked' => set_checkbox('configuration', 1, ( @$item_config->configuration == 1 )? true: false ),
								'class' => 'form-check-input',
							));	?>
							<?php echo get_msg( 'configuration_label' ); ?>
							</label>
						</div>
					</div>
                    <div class="form-group">
						<div class="form-check">
							<label class="form-check-label">
							<?php echo form_checkbox( array(
								'name' => 'area',
								'id' => 'area',
								'value' => 'accept',
								'checked' => set_checkbox('area', 1, ( @$item_config->area == 1 )? true: false ),
								'class' => 'form-check-input',
							));	?>
							<?php echo get_msg( 'area_label' ); ?>
							</label>
						</div>
					</div>
					
                    <div class="form-group">
						<div class="form-check">
							<label class="form-check-label">
							<?php echo form_checkbox( array(
								'name' => 'floor_no',
								'id' => 'floor_no',
								'value' => 'accept',
								'checked' => set_checkbox('floor_no', 1, ( @$item_config->floor_no == 1 )? true: false ),
								'class' => 'form-check-input',
							));	?>
							<?php echo get_msg( 'floor_label' ); ?>
							</label>
						</div>
					</div>
                    <div class="form-group">
						<div class="form-check">
							<label class="form-check-label">
							<?php echo form_checkbox( array(
								'name' => 'amenities',
								'id' => 'amenities',
								'value' => 'accept',
								'checked' => set_checkbox('amenities', 1, ( @$item_config->amenities == 1 )? true: false ),
								'class' => 'form-check-input',
							));	?>
							<?php echo get_msg( 'amenities_info_label' ); ?>
							</label>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<div class="form-check">
							<label class="form-check-label">
							<?php echo form_checkbox( array(
								'name' => 'video',
								'id' => 'video',
								'value' => 'accept',
								'checked' => set_checkbox('video', 1, ( @$item_config->video == 1 )? true: false ),
								'class' => 'form-check-input',
							));	?>
							<?php echo get_msg( 'item_video_label' ); ?>
							</label>
						</div>
					</div>
					<div class="form-group">
						<div class="form-check">
							<label class="form-check-label">
							<?php echo form_checkbox( array(
								'name' => 'video_icon',
								'id' => 'video_icon',
								'value' => 'accept',
								'checked' => set_checkbox('video_icon', 1, ( @$item_config->video_icon == 1 )? true: false ),
								'class' => 'form-check-input',
							));	?>
							<?php echo get_msg( 'item_video_icon_label' ); ?>
							</label>
						</div>
					</div>
					<div class="form-group">
						<div class="form-check">
							<label class="form-check-label">
							<?php echo form_checkbox( array(
								'name' => 'condition_of_item_id',
								'id' => 'condition_of_item_id',
								'value' => 'accept',
								'checked' => set_checkbox('condition_of_item_id', 1, ( @$item_config->condition_of_item_id == 1 )? true: false ),
								'class' => 'form-check-input',
							));	?>
							<?php echo get_msg( 'condition_of_item' ); ?>
							</label>
						</div>
					</div>
					<div class="form-group">
						<div class="form-check">
							<label class="form-check-label">
							<?php echo form_checkbox( array(
								'name' => 'highlight_info',
								'id' => 'highlight_info',
								'value' => 'accept',
								'checked' => set_checkbox('highlight_info', 1, ( @$item_config->highlight_info == 1 )? true: false ),
								'class' => 'form-check-input',
							));	?>
							<?php echo get_msg( 'prd_high_info' ); ?>
							</label>
						</div>
					</div>
				</div>
				<div class="row col-12">
                    <legend class="mt-3"><?php echo get_msg('price')?></legend><hr class="col-12">
					<div class="col-6">
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
								<?php echo form_checkbox( array(
									'name' => 'price_unit',
									'id' => 'price_unit',
									'value' => 'accept',
									'checked' => set_checkbox('price_unit', 1, ( @$item_config->price_unit == 1 )? true: false ),
									'class' => 'form-check-input',
								));	?>
								<?php echo get_msg( 'itm_price_unit_label' ); ?>
								</label>
							</div>
						</div>
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
								<?php echo form_checkbox( array(
									'name' => 'price_note',
									'id' => 'price_note',
									'value' => 'accept',
									'checked' => set_checkbox('price_note', 1, ( @$item_config->price_note == 1 )? true: false ),
									'class' => 'form-check-input',
								));	?>
								<?php echo get_msg( 'price_note_label' ); ?>
								</label>
							</div>
						</div>
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
								<?php echo form_checkbox( array(
									'name' => 'item_price_type_id',
									'id' => 'item_price_type_id',
									'value' => 'accept',
									'checked' => set_checkbox('item_price_type_id', 1, ( @$item_config->item_price_type_id == 1 )? true: false ),
									'class' => 'form-check-input',
								));	?>
								<?php echo get_msg( 'price_type_label' ); ?>
								</label>
							</div>
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
								<?php echo form_checkbox( array(
									'name' => 'discount_rate_by_percentage',
									'id' => 'discount_rate_by_percentage',
									'value' => 'accept',
									'checked' => set_checkbox('discount_rate_by_percentage', 1, ( @$item_config->discount_rate_by_percentage == 1 )? true: false ),
									'class' => 'form-check-input',
								));	?>
								<?php echo get_msg( 'discount_rate_by_percentage' ); ?>
								</label>
							</div>
						</div>
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
								<?php echo form_checkbox( array(
									'name' => 'is_negotiable',
									'id' => 'is_negotiable',
									'value' => 'accept',
									'checked' => set_checkbox('is_negotiable', 1, ( @$item_config->is_negotiable == 1 )? true: false ),
									'class' => 'form-check-input',
								));	?>
								<?php echo get_msg( 'is_nagotiable_label' ); ?>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12">
                    <legend class="mt-3"><?php echo get_msg('location_info_label')?></legend><hr>
					<div class="form-group">
						<div class="form-check">
							<label class="form-check-label">
							<?php echo form_checkbox( array(
								'name' => 'address',
								'id' => 'address',
								'value' => 'accept',
								'checked' => set_checkbox('address', 1, ( @$item_config->address == 1 )? true: false ),
								'class' => 'form-check-input',
							));	?>
							<?php echo get_msg( 'itm_address_label' ); ?>
							</label>
						</div>
					</div>
				</div>
            </div>
            <div class="card-footer">
				<button type="submit" name="save" class="btn btn-sm btn-primary">
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