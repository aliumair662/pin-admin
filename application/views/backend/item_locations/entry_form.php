
<?php
	$attributes = array( 'id' => 'location-form', 'enctype' => 'multipart/form-data');
	echo form_open( '', $attributes);
?>
	
<section class="content animated fadeInRight">
	<div class="col-md-12">
		<div class="card card-info">
		    <div class="card-header">
		        <h3 class="card-title"><?php echo get_msg('location_info')?></h3>
		    </div>
	        <!-- /.card-header -->
	        <div class="card-body">
	            <div class="row">
	            	<div class="col-md-6">
	            		<div class="form-group">
	                   		<label>
	                   			<span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('location_name')?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('cat_name_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label>

							<?php echo form_input( array(
								'name' => 'name',
								'value' => set_value( 'name', show_data( @$location->name ), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg( 'type_name' ),
								'id' => 'name'
							)); ?>
	              		</div>

	              		<div class="form-group">
	                   		<label>
	                   			<span style="font-size: 17px; color: red;"></span>
								<?php echo get_msg('loc_ordering')?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('ordering_name_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label>

							<?php echo form_input( array(
								'name' => 'ordering',
								'value' => set_value( 'ordering', show_data( @$location->ordering ), false ),
								'class' => 'form-control form-control-sm ordering',
								'placeholder' => get_msg( 'loc_ordering' ),
								'id' => 'ordering'
							)); ?>
	              		</div>

	              	</div>

	              	<div class="col-md-6">

	              		<div id="location_map" style="width: 100%; height: 400px;"></div>
	              		
	        			<div class="clearfix">&nbsp;</div>

	        			<div class="form-group">
			              <label><span style="font-size: 17px; color: red;">*</span> <?php echo get_msg('itm_lat_label') ?>
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
			                  'value' => set_value( 'lat', show_data( @$location->lat ), false ),
			                ));
			              ?>
			            </div>

			            <div class="form-group">
			              <label><span style="font-size: 17px; color: red;">*</span> <?php echo get_msg('itm_lng_label') ?>
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
			                  'value' => set_value( 'lng', show_data( @$location->lng ), false ),
			                ));
			              ?>
			            </div>
			            <!-- form group -->
		            </div>
            		
	              		
	            <!-- /.row -->
	        	</div>
	        <!-- /.card-body -->
	   		</div>
	   		<?php 
				if ( isset( $location )) { 
			?>
				<input type="hidden" id="edit_location" name="edit_location" value="1">
			<?php		
				} else {
			?>
				<input type="hidden" id="edit_location" name="edit_location" value="0">
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
</section

<?php echo form_close(); ?>


<!-- location city map -->
        <script>
            <?php
                if (isset($location)) {
                    $lat = $location->lat;
                    $lng = $location->lng;
            ?>
                    var mymap = L.map('location_map').setView([<?php echo $lat;?>, <?php echo $lng;?>], 5);
            <?php
                } else {
            ?>
                    var mymap = L.map('location_map').setView([0, 0], 5);
            <?php
                }
            ?>

            const attribution =
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
            const tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            const tiles = L.tileLayer(tileUrl, { attribution });
            tiles.addTo(mymap);
            <?php if(isset($location)) {?>
                var marker2 = new L.Marker(new L.LatLng(<?php echo $lat;?>, <?php echo $lng;?>));
                mymap.addLayer(marker2);
                // results = L.marker([<?php echo $lat;?>, <?php echo $lng;?>]).addTo(mymap);

            <?php } else { ?>
                var marker2 = new L.Marker(new L.LatLng(0, 0));
                //mymap.addLayer(marker2);
            <?php } ?>
            var searchControl = L.esri.Geocoding.geosearch().addTo(mymap);
            var results = L.layerGroup().addTo(mymap);

            searchControl.on('results',function(data){
                results.clearLayers();

                for(var i= data.results.length -1; i>=0; i--) {
                    mymap.removeLayer(marker2);
                    results.addLayer(L.marker(data.results[i].latlng));
                    var search_str = data.results[i].latlng.toString();
                    var search_res = search_str.substring(search_str.indexOf("(") + 1, search_str.indexOf(")"));
                    var searchArr = new Array();
                    searchArr = search_res.split(",");

                    document.getElementById("lat").value = searchArr[0].toString();
                    document.getElementById("lng").value = searchArr[1].toString(); 
                   
                }
            })
            var popup = L.popup();

            function onMapClick(e) {

                var str = e.latlng.toString();
                var str_res = str.substring(str.indexOf("(") + 1, str.indexOf(")"));
                mymap.removeLayer(marker2);
                results.clearLayers();
                results.addLayer(L.marker(e.latlng));   

                var tmpArr = new Array();
                tmpArr = str_res.split(",");

                document.getElementById("lat").value = tmpArr[0].toString(); 
                document.getElementById("lng").value = tmpArr[1].toString();
            }

            mymap.on('click', onMapClick);
        </script>
