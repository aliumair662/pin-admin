<?php
  $attributes = array( 'id' => 'disable-form', 'enctype' => 'multipart/form-data');
  echo form_open( '', $attributes);
?>

<section class="content animated fadeInRight">
        
  <div class="card card-info">
    <div class="card-header">
      <h3 class="card-title"><?php echo get_msg('prd_info')?></h3>
    </div>

    <form role="form">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label> <span style="font-size: 17px; color: red;">*</span>
                <?php echo get_msg('itm_title_label')?>
              </label>

              <?php echo form_input( array(
                'name' => 'title',
                'value' => set_value( 'title', show_data( @$disable->title), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('itm_title_label'),
                'id' => 'title',
                'readonly' => 'true'
              )); ?>

            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label> 
                <?php echo get_msg('item_description_label')?>
              </label>

              <?php echo form_textarea( array(
                'name' => 'description',
                'value' => set_value( 'description', show_data( @$disable->description), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('item_description_label'),
                'id' => 'description',
                'rows' => "3",
                'readonly' => 'true'
              )); ?>

            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>
                <?php echo get_msg('area_label')?>
              </label>

              <?php echo form_input( array(
                'name' => 'area  ',
                'value' => set_value( 'area  ', show_data( @$disable->area ), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('area_label'),
                'id' => 'area',
                'readonly' => 'true'
              )); ?>

            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>
                <?php echo get_msg('itm_address_label')?>
              </label>

              <?php echo form_textarea( array(
                'name' => 'address',
                'value' => set_value( 'address', show_data( @$disable->address), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('itm_address_label'),
                'id' => 'address',
                'rows' => "3",
                'readonly' => 'true'
              )); ?>
            </div>  

          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>
                <?php echo get_msg('configuration_label')?>
              </label>

              <?php echo form_input( array(
                'name' => 'configuration',
                'value' => set_value( 'configuration', show_data( @$disable->configuration ), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('configuration_label'),
                'id' => 'rooms',
                'readonly' => 'true'
              )); ?>

            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
                <label>
                  <?php echo get_msg('prd_high_info')?>
                </label>

                <?php echo form_textarea( array(
                  'name' => 'highlight_info',
                  'value' => set_value( 'info', show_data( @$disable->highlight_info), false ),
                  'class' => 'form-control form-control-sm',
                  'placeholder' => get_msg('pls_highlight_info'),
                  'id' => 'info',
                  'rows' => "3",
                  'readonly' => 'true'
                )); ?>

            </div>  
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>
                <?php echo get_msg('floor_label')?>
              </label>

              <?php echo form_input( array(
                'name' => 'floor_no',
                'value' => set_value( 'floor_no', show_data( @$disable->floor_no ), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('floor_label'),
                'id' => 'floor_no',
                'readonly' => 'true'
              )); ?>

            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>
                <?php echo get_msg('itm_select_posted')?>
              </label>

              <?php
              
                $options=array();
                $options[0]=get_msg('itm_select_posted');
                $posted = $this->Postedby->get_all();
                foreach($posted->result() as $post) {
                    $options[$post->id]=$post->name;
                }

                echo form_dropdown(
                  'posted_by_id',
                  $options,
                  set_value( 'posted_by_id', show_data( @$disable->posted_by_id), false ),
                  'class="form-control form-control-sm mr-3" disabled="disabled" id="posted_by_id"'
                );
              ?>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
                <label>
                  <?php echo get_msg('itm_select_property')?>
                </label>

                <?php
                
                  $options=array();
                  $options[0]=get_msg('itm_select_property');
                  $properties = $this->PropertyBy->get_all();
                  foreach($properties->result() as $property) {
                      $options[$property->id]=$property->name;
                  }

                  echo form_dropdown(
                    'property_by_id',
                    $options,
                    set_value( 'property_by_id', show_data( @$disable->property_by_id), false ),
                    'class="form-control form-control-sm mr-3" disabled="disabled" id="property_by_id"'
                  );
                ?>
            </div>
          </div> 

        <div class="col-md-6">
         <div class="form-group">
            <label>
              <?php echo get_msg('itm_select_price')?>
            </label>

            <?php
              $options=array();
              $conds['status'] = 1;
              $options[0]=get_msg('itm_select_price');
              $pricetypes = $this->Pricetype->get_all_by($conds);
              foreach($pricetypes->result() as $price) {
                  $options[$price->id]=$price->name;
              }

              echo form_dropdown(
                'item_price_type_id',
                $options,
                set_value( 'item_price_type_id', show_data( @$item->item_price_type_id), false ),
                'class="form-control form-control-sm mr-3" disabled="disabled" id="item_price_type_id"'
              );
            ?>
          </div>
        </div> 

          <div class="col-md-6">
            <div class="form-group" style="padding-top: 30px;">
              <div class="form-check">

                <label>
                
                  <?php echo form_checkbox( array(
                    'name' => 'status',
                    'id' => 'status',
                    'value' => 'accept',
                    'checked' => set_checkbox('status', 1, ( @$disable->status == 1 )? true: false ),
                    'class' => 'form-check-input',
                    'onclick' => 'return false'
                  )); ?>

                  <?php echo get_msg( 'status' ); ?>
                </label>
              </div>
            </div>  
          </div>

          <div class="col-md-6">
            <div class="form-group mt-3">
              <label> <span style="font-size: 17px; color: red;"></span>
                <?php echo get_msg('prd_dynamic_link_label') ." : ".$disable->dynamic_link; ?>
              </label>
            </div>
            <div class="form-group">
              <label>
                <?php echo get_msg('owner_of_item') ." : ".$this->User->get_one( $disable->added_user_id )->user_name; ?>
              </label>
            </div>
          </div>

            <!-- for price info -->
          <legend><?php echo get_msg('price_info_label')?></legend>
          <div class="col-md-6">
            <div class="form-group">
              <label>
                <?php echo get_msg('price')?>
              </label>

              <?php echo form_input( array(
                'name' => 'price',
                'value' => set_value( 'price', show_data( @$disable->price), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('price'),
                'id' => 'price',
                'readonly' => 'true'
                
              )); ?>

            </div>

            <div class="form-group">
              <label>
                <?php echo get_msg('price_unit_label')?>
              </label>

              <?php echo form_input( array(
                'name' => 'price_unit',
                'value' => set_value( 'price_unit', show_data( @$disable->price_unit), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('price_unit_label'),
                'id' => 'price_unit',
                'readonly' => 'true'
                
              )); ?>

            </div>

            <div class="form-group">
              <div class="form-check">
                <label>
                
                <?php echo form_checkbox( array(
                  'name' => 'is_negotiable',
                  'id' => 'is_negotiable',
                  'value' => 'accept',
                  'checked' => set_checkbox('is_negotiable', 1, ( @$disable->is_negotiable == 1 )? true: false ),
                  'class' => 'form-check-input',
                  'onclick' => 'return false'
                )); ?>

                <?php echo get_msg( 'is_negotiable' ); ?>

                </label>
              </div>
            </div>

          </div>

          <div class="col-md-6">

            <div class="form-group">
              <label>
                <?php echo get_msg('price_note_label')?>
              </label>

              <?php echo form_input( array(
                'name' => 'price_note',
                'value' => set_value( 'price_note', show_data( @$disable->price_note), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('price_note_label'),
                'id' => 'price_note',
                'readonly' => 'true'
                
              )); ?>

            </div>

            <div class="form-group">
              <label>
                <?php echo get_msg('itm_select_currency')?>
              </label>

              <?php
                $options=array();
                $conds['status'] = 1;
                $options[0]=get_msg('itm_select_currency');
                $currency = $this->Currency->get_all_by($conds);
                foreach($currency->result() as $curr) {
                    $options[$curr->id]=$curr->currency_short_form;
                }

                echo form_dropdown(
                  'item_currency_id',
                  $options,
                  set_value( 'item_currency_id', show_data( @$disable->item_currency_id), false ),
                  'class="form-control form-control-sm mr-3" disabled="disabled" id="item_currency_id"'
                );
              ?>
            </div>

            <div class="form-group">
              <div class="form-check">
                <label>
                
                <?php echo form_checkbox( array(
                  'name' => 'is_sold_out',
                  'id' => 'is_sold_out',
                  'value' => 'accept',
                  'checked' => set_checkbox('is_sold_out', 1, ( @$disable->is_sold_out == 1 )? true: false ),
                  'class' => 'form-check-input',
                  'onclick' => 'return false'
                )); ?>

                <?php echo get_msg( 'itm_is_sold_out' ); ?>

                </label>
              </div>
            </div>

          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
            <legend><?php echo get_msg('location_info_label'); ?></legend>
            <div class="form-group">
              <label> <span style="font-size: 17px; color: red;">*</span>
                <?php echo get_msg('itm_select_location_city')?>
              </label>

              <?php
              
                $options=array();
                $options[0]=get_msg('itm_select_location');
                $locations = $this->Itemlocation->get_all();
                foreach($locations->result() as $location) {
                    $options[$location->id]=$location->name;
                }

                echo form_dropdown(
                  'item_location_city_id',
                  $options,
                  set_value( 'item_location_city_id', show_data( @$disable->item_location_city_id), false ),
                  'class="form-control form-control-sm mr-3" disabled="disabled" id="item_location_city_id"'
                );
              ?>
          </div>

          <div class="form-group">
              <label> <span style="font-size: 17px; color: red;">*</span>
                <?php echo get_msg('itm_select_location_township')?>
              </label>

              <?php
                if(isset($disable)) {
                  $options=array();
                  $options[0]=get_msg('itm_select_location_township');
                  $conds['city_id'] = $disable->item_location_city_id;
                  $townships = $this->Item_location_township->get_all_by($conds);
                  foreach($townships->result() as $township) {
                    $options[$township->id]=$township->township_name;
                  }
                  echo form_dropdown(
                    'item_location_township_id',
                    $options,
                    set_value( 'item_location_township_id', show_data( @$disable->item_location_township_id), false ),
                    'class="form-control form-control-sm mr-3" disabled="disabled" id="item_location_township_id"'
                  );

                } else {
                  $conds['city_id'] = $selected_location_city_id;
                  $options=array();
                  $options[0]=get_msg('itm_select_location_township');

                  echo form_dropdown(
                    'item_location_township_id',
                    $options,
                    set_value( 'item_location_township_id', show_data( @$disable->item_location_township_id), false ),
                    'class="form-control form-control-sm mr-3" disabled="disabled" id="item_location_township_id"'
                  );
                }
                
              ?>
            </div>


            <?php if (  @$disable->lat !='0' && @$disable->lng !='0' ):?>
              <div id="disable_map" style="width: 100%; height: 400px;"></div>
                <div class="clearfix">&nbsp;</div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label><span style="font-size: 17px; color: red;">*</span>
                          <?php echo get_msg('itm_lat_label') ?>
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
                            'value' => set_value( 'lat', show_data( @$disable->lat ), false ),
                            'readonly' => 'true'
                          ));
                        ?>
                      </div>
                    </div>  
                    <div class="col-md-6">
                      <div class="form-group">
                          <label><span style="font-size: 17px; color: red;">*</span>
                            <?php echo get_msg('itm_lng_label') ?>
                            <a href="#" class="tooltip-ps" data-toggle="tooltip" 
                              title="<?php echo get_msg('city_lng_label')?>">
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
                            'value' =>  set_value( 'lng', show_data( @$disable->lng ), false ),
                            'readonly' => 'true'
                          ));
                        ?>
                      </div>
                    </div>
                  </div>
              <!-- form group -->
            <?php endif ?>

          </div>

          <div class="col-md-6">
            <legend><?php echo get_msg('amenities_info_label'); ?></legend> <br>

              <div id="perm_err" class="alert alert-danger fade in" style="display: none">
                <label for="amenities[]" class="error"></label>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              </div>


              <div class="form-group">
                  <?php 
                    if (!$disable) {
                      foreach($this->Amenity->get_all()->result() as $amenity): ?>
                      <div class="form-check">

                        <label class="form-check-label">
                        
                        <?php echo form_checkbox('amenities[]', $amenity->id, set_checkbox('amenities', $amenity->id)); ?> 

                        <?php echo $amenity->name; ?> 

                        </label>
                      </div>  
                      <br>
                  <?php endforeach; ?>
                  <?php 
                    } else { 
                      foreach($this->Amenity->get_all()->result() as $amenity): ?>
                     
                    <div class="form-check">
                      <label class="form-check-label">
                        <?php 

                          if($disable->id != "") {
                              $conds['item_id'] = $disable->id;
                            } else {
                              $conds['item_id'] = '0';
                            }
                            
                            $conds['amenity_id'] = $amenity->id;


                              
                            $amenity_item_id = $this->Item_amenity->get_one_by($conds)->amenity_id;
                            
                           $checked_value = "";
                            if($amenity_item_id == "" ) {
                              $checked_value = "";
                            } else {
                              $checked_value = "checked";
                            }
                         ?>

                      <?php echo form_checkbox('amenities[]', $amenity->id, set_checkbox('amenities', $amenity->id ),$checked_value); ?>

                      <?php echo $amenity->name; ?>

                      </label>
                    </div>
                    <br>
                   
                  <?php endforeach; ?>
                  <?php } ?>
              </div>
          </div>
        </div>  

        <hr>
         <div class="form-group" style="background-color: #edbbbb; padding: 20px;">
            <label>
              <strong><?php echo get_msg('select_status')?></strong>
            </label>

            <select id="item_is_published" name="item_is_published" class="form-control">
               <option value="1"><?php echo get_msg('approved_label'); ?></option>
               <option value="2"><?php echo get_msg('disable_label'); ?></option>
               <option value="3"><?php echo get_msg('reject_label'); ?></option>
            </select>
          </div>
        </div>



      <!-- Grid row -->
      <?php if ( isset( $disable )): ?>
      <div class="gallery" id="gallery" style="margin-left: 15px; margin-bottom: 15px;">
        <?php
            $conds = array( 'img_type' => 'item', 'img_parent_id' => $disable->id );
            $images = $this->Image->get_all_by( $conds )->result();
        ?>
        <?php $i = 0; foreach ( $images as $img ) :?>
          <!-- Grid column -->
          <div class="mb-3 pics animation all 2">
            <a href="#<?php echo $i;?>"><img class="img-fluid" src="<?php echo img_url('/' . $img->img_path); ?>" alt="Card image cap"></a>
          </div>
          <!-- Grid column -->
        <?php $i++; endforeach; ?>

        <?php $i = 0; foreach ( $images as $img ) :?>
          <a href="#_1" class="lightbox trans" id="<?php echo $i?>"><img src="<?php echo img_url('/' . $img->img_path); ?>"></a>
        <?php $i++; endforeach; ?>
      </div>
      <?php endif; ?>
      
      <!-- Grid row -->

      <div class="card-footer">
        <button type="submit" class="btn btn-sm btn-primary" style="margin-top: 3px;">
          <?php echo get_msg('btn_save')?>
        </button>

        <a href="<?php echo $module_site_url; ?>" class="btn btn-sm btn-primary" style="margin-top: 3px;">
          <?php echo get_msg('btn_cancel')?>
        </a>
      </div>
    </form>
  </div>
</section>

<script>
  $(document).ready(function() {
    $("input[type=checkbox]").attr("disabled", true);
  });
</script>


        <!-- disable map -->

        <script>

            <?php
                if (isset($disable)) {
                    $lat = $disable->lat;
                    $lng = $disable->lng;
            ?>
                    var disable_map = L.map('disable_map').setView([<?php echo $lat;?>, <?php echo $lng;?>], 5);
            <?php
                } else {
            ?>
                    var disable_map = L.map('disable_map').setView([0, 0], 5);
            <?php
                }
            ?>

            const disable_attribution =
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
            const disable_tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            const disable_tiles = L.tileLayer(disable_tileUrl, { disable_attribution });
            disable_tiles.addTo(disable_map);
            <?php if(isset($disable)) {?>
                var disable_marker = new L.Marker(new L.LatLng(<?php echo $lat;?>, <?php echo $lng;?>));
                disable_map.addLayer(disable_marker);
                // results = L.marker([<?php echo $lat;?>, <?php echo $lng;?>]).addTo(mymap);

            <?php } else { ?>
                var disable_marker = new L.Marker(new L.LatLng(0, 0));
                //mymap.addLayer(marker2);
            <?php } ?>
            var results = L.layerGroup().addTo(disable_map);
            
            var popup = L.popup();

            function onMapClick(e) {

                var disable = e.latlng.toString();
                var disable_res = disable.substring(disable.indexOf("(") + 1, disable.indexOf(")"));
                disable_map.removeLayer(disable_marker);
                results.clearLayers();
                results.addLayer(L.marker(e.latlng));   

                var disable_tmpArr = new Array();
                disable_tmpArr = disable_res.split(",");

                document.getElementById("lat").value = disable_tmpArr[0].toString(); 
                document.getElementById("lng").value = disable_tmpArr[1].toString();
            }

            disable_map.on('click', onMapClick);
        </script>
