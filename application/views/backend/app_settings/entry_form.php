<?php
$attributes = array('id' => 'app-form','enctype' => 'multipart/form-data');
echo form_open( '', $attributes);
?>

<section class="content animated fadeInRight">
	<div class="card card-info">
		<div class="card-header">
	        <h3 class="card-title"><?php echo get_msg('app_setting_lable')?></h3>
	    </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
              	<div class="col-md-6">
              		<div id="app_location" style="width: 100%; height: 400px;"></div>
            			<div class="clearfix">&nbsp;</div>
							
							<div class="form-group">
								<label><?php echo get_msg('app_lat_label') ?>
					              	<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('city_lat_label')?>">
					              		<span class='glyphicon glyphicon-info-sign menu-icon'>
					              	</a>
				              	</label>

								<br>

								<?php 
									echo form_input( array(
										'type' => 'text',
										'name' => 'lat',
										'id' => 'lat',
										'class' => 'form-control',
										'placeholder' => '',
										'value' => set_value( 'lat', show_data( @$app->lat ), false ),
									));
								?>
							</div>

							<div class="form-group">
								<label><?php echo get_msg('app_lng_label') ?>
									<a href="#" class="tooltip-ps" data-toggle="tooltip" 
										title="<?php echo get_msg('city_lng_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>

								<br>

								<?php 
									echo form_input( array(
										'type' => 'text',
										'name' => 'lng',
										'id' => 'lng',
										'class' => 'form-control',
										'placeholder' => '',
										'value' =>  set_value( 'lng', show_data( @$app->lng ), false ),
									));
								?>
							</div>

							<div class="form-group">
								<div class="form-check">
									<label class="form-check-label">
									
									<?php echo form_checkbox( array(
										'name' => 'is_approval_enabled',
										'id' => 'is_approval_enabled',
										'value' => 'accept',
										'checked' => set_checkbox('is_approval_enabled', 1, ( @$app->is_approval_enabled == 1 )? true: false ),
										'class' => 'form-check-input'
									));	?>

									<?php echo get_msg( 'app_is_approval_enabled' ); ?>

									</label>
								</div>
							</div>

							<div class="form-group">
								<div class="form-check">
									<label class="form-check-label">
									
									<?php echo form_checkbox( array(
										'name' => 'is_sub_location',
										'id' => 'is_sub_location',
										'value' => 'accept',
										'checked' => set_checkbox('is_sub_location', 1, ( @$app->is_sub_location == 1 )? true: false ),
										'class' => 'form-check-input'
									));	?>

									<?php echo get_msg( 'is_sub_location' ); ?>

									</label>
								</div>
							</div>
							<div class="form-group">
								<div class="form-check">
									<label class="form-check-label">
									
									<?php echo form_checkbox( array(
										'name' => 'is_thumb2x_3x_generate',
										'id' => 'is_thumb2x_3x_generate',
										'value' => 'accept',
										'checked' => set_checkbox('is_thumb2x_3x_generate', 1, ( @$app->is_thumb2x_3x_generate == 1 )? true: false ),
										'class' => 'form-check-input'
									));	?>

									<?php echo get_msg( 'is_thumb2x_3x_generate' ); ?>

									</label>
								</div>
							</div>

							
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<div class="form-check">
							<label class="form-check-label">
							
							<?php echo form_checkbox( array(
								'name' => 'is_paid_app',
								'id' => 'is_paid_app',
								'value' => 'accept',
								'checked' => set_checkbox('is_paid_app', 1, ( @$app->is_paid_app == 1 )? true: false ),
								'class' => 'form-check-input',
							));	?>
							<?php echo get_msg( 'is_paid_app' ); ?>
							</label>
						</div>
					</div>
					<div class="form-group">
						<div class="form-check">
							<label class="form-check-label">
							
							<?php echo form_checkbox( array(
								'name' => 'is_block_user',
								'id' => 'is_block_user',
								'value' => 'accept',
								'checked' => set_checkbox('is_block_user', 1, ( @$app->is_block_user == 1 )? true: false ),
								'class' => 'form-check-input',
							));	?>
							<?php echo get_msg( 'is_block_user' ); ?>
							</label>
						</div>
					</div>
					<div class="form-group">
						<div class="form-check">
							<label class="form-check-label">
							
							<?php echo form_checkbox( array(
								'name' => 'is_propertyby_subscription',
								'id' => 'is_propertyby_subscription',
								'value' => 'accept',
								'checked' => set_checkbox('is_propertyby_subscription', 1, ( @$app->is_propertyby_subscription == 1 )? true: false ),
								'class' => 'form-check-input',
							));	?>
							<?php echo get_msg( 'is_subcat_subscription' ); ?>
							</label>
						</div>
					</div>
					
					<div class="form-group">
						<label><?php echo get_msg('max_img_upload_of_item')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('max_img_upload_of_item')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>
						<?php 
							echo form_input( array(
								'name' => 'max_img_upload_of_item',
								'id' => 'max_img_upload_of_item',
								'class' => 'form-control',
								'value' => set_value( 'max_img_upload_of_item', show_data( @$app->max_img_upload_of_item), false )?set_value( 'max_img_upload_of_item', show_data( @$app->max_img_upload_of_item), false ):'1',
								'placeholder' => get_msg('max_img_upload_of_item'),
							));
						?>
					</div>
				</div>
			</div>

			
			<legend class="ml-3 mt-5"><?php echo get_msg('ad_post_section')?></legend>
			<hr width="100%">
			<div class="col-md-6 mb-5">
				<div class="form-group">
					<label><?php echo get_msg('ad_post_type')?></label>

					<?php
					$options=array();
					$options[0]=get_msg('select_ad_post_type');
					$ad_post_types = $this->Ad_post_type->get_all();
					foreach($ad_post_types->result() as $ad_post_type) {
						$options[$ad_post_type->id]=$ad_post_type->value;
					}

					echo form_dropdown(
						'ad_type',
						$options,
						set_value( 'ad_type', show_data( @$app->ad_type), false ),
						'class="form-control mr-3" id="ad_type"'
					);
					?>
				</div>

				<div class="form-group <?php echo ($app->ad_type == "1" || $app->ad_type == "5" )  ? 'd-none' : '' ?>" id="promo_cell_interval_no_input">
					<label><?php echo get_msg('promo_cell_interval_no') . ' ' . get_msg('promo_cell_interval_desc')?>
						<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('promo_cell_interval_no')?>">
							<span class='glyphicon glyphicon-info-sign menu-icon'>
						</a>
					</label>
					<?php 
						echo form_input( array(
							'name' => 'promo_cell_interval_no',
							'id' => 'promo_cell_interval_no',
							'class' => 'form-control ' ,
							'value' => set_value( 'promo_cell_interval_no', show_data( @$app->promo_cell_interval_no), false )?set_value( 'promo_cell_interval_no', show_data( @$app->promo_cell_interval_no), false ):'1',
							'placeholder' => get_msg('promo_cell_interval_no'),
						));
					?>
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
     
</section>


        <!-- app location map -->

        <script>

            <?php
                if (isset($app)) {
                    $lat = $app->lat;
                    $lng = $app->lng;
            ?>
                    var app_map = L.map('app_location').setView([<?php echo $lat;?>, <?php echo $lng;?>], 5);
            <?php
                } else {
            ?>
                    var app_map = L.map('app_location').setView([0, 0], 5);
            <?php
                }
            ?>

            const app_attribution =
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
            const app_tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            const app_tiles = L.tileLayer(app_tileUrl, { app_attribution });
            app_tiles.addTo(app_map);
            <?php if(isset($app)) {?>
                var app_marker = new L.Marker(new L.LatLng(<?php echo $lat;?>, <?php echo $lng;?>));
                app_map.addLayer(app_marker);
                // results = L.marker([<?php echo $lat;?>, <?php echo $lng;?>]).addTo(mymap);

            <?php } else { ?>
                var app_marker = new L.Marker(new L.LatLng(0, 0));
                //mymap.addLayer(marker2);
            <?php } ?>
            var app_searchControl = L.esri.Geocoding.geosearch().addTo(app_map);
            var results = L.layerGroup().addTo(app_map);

            app_searchControl.on('results',function(data){
                results.clearLayers();

                for(var i= data.results.length -1; i>=0; i--) {
                    app_map.removeLayer(app_marker);
                    results.addLayer(L.marker(data.results[i].latlng));
                    var app_search_str = data.results[i].latlng.toString();
                    var app_search_res = app_search_str.substring(app_search_str.indexOf("(") + 1, app_search_str.indexOf(")"));
                    var app_searchArr = new Array();
                    app_searchArr = app_search_res.split(",");

                    document.getElementById("lat").value = app_searchArr[0].toString();
                    document.getElementById("lng").value = app_searchArr[1].toString(); 
                   
                }
            })
            var popup = L.popup();

            function onMapClick(e) {

                var app = e.latlng.toString();
                var app_res = app.substring(app.indexOf("(") + 1, app.indexOf(")"));
                app_map.removeLayer(app_marker);
                results.clearLayers();
                results.addLayer(L.marker(e.latlng));   

                var app_tmpArr = new Array();
                app_tmpArr = app_res.split(",");

                document.getElementById("lat").value = app_tmpArr[0].toString(); 
                document.getElementById("lng").value = app_tmpArr[1].toString();
            }

            app_map.on('click', onMapClick);
        </script>
