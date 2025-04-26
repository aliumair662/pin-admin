<style type="text/css">
  .search-container {
  display: flex;
  align-items: center;
  gap: 10px;
  max-width: 600px;
  margin: auto;
}

.search-box {
  flex: 1;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.import-span {
  background-color: #ff0000;
  color: white;
  border: none;
  padding: 10px 20px;
  font-size: 16px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.import-span:hover {
  background-color: #e60000;
}

</style>
<?php
  $attributes = array( 'id' => 'item-form', 'enctype' => 'multipart/form-data');
  echo form_open( '', $attributes);
?>
<section class="content animated fadeInRight" style="padding-bottom:10px;margin-top: -46px;padding-left: 459px;" >
  <div class="search-container">
    <input 
      id="place-search" 
      class="search-box" 
      type="text" 
      placeholder="Search places..." 
    />
    <span id="import-span" class="import-span">Import from G</span>
  </div>
</section>

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
                'value' => set_value( 'title', show_data( @$item->title), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('itm_title_label'),
                'id' => 'title'
              )); ?>

            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>  <span style="font-size: 17px; color: red;">*</span>
                <?php echo get_msg('item_description_label')?>
              </label>

              <?php echo form_textarea( array(
                'name' => 'description',
                'value' => set_value( 'description', show_data( @$item->description), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('item_description_label'),
                'id' => 'description',
                'rows' => "3"
              )); ?>

            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
                <label> <span style="font-size: 17px; color: red;">*</span>
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
                    set_value( 'property_by_id', show_data( @$item->property_by_id), false ),
                    'class="form-control form-control-sm mr-3" id="property_by_id"'
                  );
                ?>
            </div>
          </div>  

          <div class="col-md-6">
            <div class="form-group">
              <label> <span style="font-size: 17px; color: red;">*</span>
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
                  set_value( 'posted_by_id', show_data( @$item->posted_by_id), false ),
                  'class="form-control form-control-sm mr-3" id="posted_by_id"'
                );
              ?>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>
                <?php echo get_msg('area_label')?>
              </label>

              <?php echo form_input( array(
                'name' => 'area',
                'value' => set_value( 'area', show_data( @$item->area ), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('area_label'),
                'id' => 'area'
              )); ?>

            </div>
          </div>

          <div class="col-md-6">
         <div class="form-group">
            <label> <span style="font-size: 17px; color: red;"></span>
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
                'class="form-control form-control-sm mr-3" id="item_price_type_id"'
              );
            ?>
          </div>
        </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>
                <?php echo get_msg('configuration_label')?>
              </label>

              <?php echo form_input( array(
                'name' => 'configuration',
                'value' => set_value( 'configuration', show_data( @$item->configuration ), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('configuration_label'),
                'id' => 'rooms'
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
                'value' => set_value( 'floor_no', show_data( @$item->floor_no ), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('floor_label'),
                'id' => 'floor_no'
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
                'value' => set_value( 'address', show_data( @$item->address), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('itm_address_label'),
                'id' => 'address',
                'rows' => "3"
              )); ?>
            </div>  

          </div>
         <input type="text" hidden name="g_review_place_id" id="g_review_place_id" value="<?=isset($item->g_review_place_id) ? $item->g_review_place_id : '' ?>" >
         <input type="text" hidden name="g_review_score" id="g_review_score" value="<?=isset($item->g_review_score) ? $item->g_review_score : '' ?>" >
         <input type="text" hidden name="g_review_quantity" id="g_review_quantity" value="<?=isset($item->g_review_quantity) ? $item->g_review_quantity : '' ?>" >
         <input type="text" hidden name="g_review_link" id="g_review_link" value="<?=isset($item->g_review_link) ? $item->g_review_link : '' ?>" >
          
          <div class="col-md-6">
            <div class="form-group">
                <label>
                  <?php echo get_msg('prd_high_info')?>
                </label>

                <?php echo form_textarea( array(
                  'name' => 'highlight_info',
                  'value' => set_value( 'info', show_data( @$item->highlight_info), false ),
                  'class' => 'form-control form-control-sm',
                  'placeholder' => get_msg('pls_highlight_info'),
                  'id' => 'info',
                  'rows' => "3"
                )); ?>

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
                    'checked' => set_checkbox('status', 1, ( @$item->status == 1 )? true: false ),
                    'class' => 'form-check-input'
                  )); ?>

                  <?php echo get_msg( 'status' ); ?>
                </label>
              </div>

              <div class="form-group">
                <label for="repeat-on">Repeat on:</label>
                <div class="form-check form-check-inline">
                  <input 
                    type="radio" 
                    id="repeat-year" 
                    name="repeat_on" 
                    value="year" 
                    class="form-check-input" <?= (isset($item->repeat_on) && $item->repeat_on == 'year' )?'checked':''; ?> >
                  <label for="repeat-year" class="form-check-label">Year</label>
                </div>
                <div class="form-check form-check-inline">
                  <input 
                    type="radio" 
                    id="repeat-week" 
                    name="repeat_on" 
                    value="week" 
                    class="form-check-input" <?= (isset($item->repeat_on) && $item->repeat_on == 'week' )?'checked':''; ?> >
                  <label for="repeat-week" class="form-check-label">Week</label>
                </div>
              </div>

              <div class="form-group">
                <label for="repeat-on">Type:</label>
                <div class="form-check form-check-inline">
                  <input 
                    type="radio" 
                    id="repeat-event" 
                    name="event_type" 
                    value="event" 
                    class="form-check-input" <?= (isset($item->event_type) && $item->event_type == 'event' )?'checked':''; ?> >
                  <label for="repeat-event" class="form-check-label">Event</label>
                </div>
                <div class="form-check form-check-inline">
                  <input 
                    type="radio" 
                    id="repeat-place" 
                    name="event_type" 
                    value="place" 
                    class="form-check-input" <?= (isset($item->event_type) && $item->event_type == 'place' )?'checked':''; ?> >
                  <label for="repeat-place" class="form-check-label">Place</label>
                </div>
              </div>

            </div>  
          </div>

          <div class="col-md-6">
            <div class="form-group mt-3">
              <label> <span style="font-size: 17px; color: red;"></span>
                <?php echo get_msg('prd_dynamic_link_label') ." : ".$item->dynamic_link; ?>
              </label>
            </div>

            <div class="form-group">
              <label>
                <?php echo get_msg('owner_of_item') ." : ".$this->User->get_one( $item->added_user_id )->user_name; ?>
              </label>
            </div>
            <div id="start_date_stop_date" class="form-group-container" style="display: flex; gap: 10px; align-items: center; display: none;">
              <div class="form-group">
                <label>
                  <span style="font-size: 17px; color: red;">*</span>
                  Start Date
                </label>
                <input type="text" readonly id="start_date" name="start_date" value="<?=isset($item->start_date) ? $item->start_date : ''; ?>" >
              </div>

              <div class="form-group">
                <label>
                  <span style="font-size: 17px; color: red;">*</span>
                  Stop Date
                </label>
                <input type="text" readonly id="stop_date" name="stop_date" value="<?=isset($item->stop_date) ? $item->stop_date : ''; ?>" >
              </div>
            </div>
          </div>

            <!-- for price info -->
          <legend><?php echo get_msg('price_info_label')?></legend>
          <div class="col-md-6">
            <div class="form-group">
              <label> <span style="font-size: 17px; color: red;">*</span>
                <?php echo get_msg('price')?>
              </label>

              <?php echo form_input( array(
                'name' => 'price',
                'value' => set_value( 'price', show_data( @$item->price), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('price'),
                'id' => 'price'
                
              )); ?>

            </div>

            <div class="form-group">
              <label>
                <?php echo get_msg('price_unit_label')?>
              </label>

              <?php echo form_input( array(
                'name' => 'price_unit',
                'value' => set_value( 'price_unit', show_data( @$item->price_unit), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('price_unit_label'),
                'id' => 'price_unit'
                
              )); ?>

            </div>

            <div class="form-group">
              <div class="form-check">
                <label>
                
                <?php echo form_checkbox( array(
                  'name' => 'is_negotiable',
                  'id' => 'is_negotiable',
                  'value' => 'accept',
                  'checked' => set_checkbox('is_negotiable', 1, ( @$item->is_negotiable == 1 )? true: false ),
                  'class' => 'form-check-input'
                )); ?>

                <?php echo get_msg( 'is_negotiable' ); ?>

                </label>
              </div>
            </div>

          </div>

          <div class="col-md-6">

            <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
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
                set_value( 'item_currency_id', show_data( @$item->item_currency_id), false ),
                'class="form-control form-control-sm mr-3" id="item_currency_id"'
              );
            ?>
          </div>

          
          <div class="form-group" id="discount_rate_by_percentage">
            <label>
              <?php echo get_msg('discount_rate_by_percentage')?>
            </label>

            <?php echo form_input( array(
              'name' => 'discount_rate_by_percentage',
              'value' => set_value( 'discount_rate_by_percentage', show_data( @$item->discount_rate_by_percentage), false )?set_value( 'discount_rate_by_percentage', show_data( @$item->discount_rate_by_percentage), false ):'0',
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('discount_rate_by_percentage')                  
            )); ?>

          </div>

            <div class="form-group">
              <label>
                <?php echo get_msg('price_note_label')?>
              </label>

              <?php echo form_input( array(
                'name' => 'price_note',
                'value' => set_value( 'price_note', show_data( @$item->price_note), false ),
                'class' => 'form-control form-control-sm',
                'placeholder' => get_msg('price_note_label'),
                'id' => 'price_note'
                
              )); ?>

            </div>

            <div class="form-group">
              <div class="form-check">
                <label>
                
                <?php echo form_checkbox( array(
                  'name' => 'is_sold_out',
                  'id' => 'is_sold_out',
                  'value' => 'accept',
                  'checked' => set_checkbox('is_sold_out', 1, ( @$item->is_sold_out == 1 )? true: false ),
                  'class' => 'form-check-input'
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
                  set_value( 'item_location_city_id', show_data( @$item->item_location_city_id), false ),
                  'class="form-control form-control-sm mr-3" id="item_location_city_id"'
                );
              ?>
          </div>

          <div class="form-group">
              <label> <span style="font-size: 17px; color: red;">*</span>
                <?php echo get_msg('itm_select_location_township')?>
              </label>

              <?php
                if(isset($item)) {
                  $options=array();
                  $options[0]=get_msg('itm_select_location_township');
                  $conds['city_id'] = $item->item_location_city_id;
                  $townships = $this->Item_location_township->get_all_by($conds);
                  foreach($townships->result() as $township) {
                    $options[$township->id]=$township->township_name;
                  }
                  echo form_dropdown(
                    'item_location_township_id',
                    $options,
                    set_value( 'item_location_township_id', show_data( @$item->item_location_township_id), false ),
                    'class="form-control form-control-sm mr-3" id="item_location_township_id"'
                  );

                } else {
                  $conds['city_id'] = $selected_location_city_id;
                  $options=array();
                  $options[0]=get_msg('itm_select_location_township');

                  echo form_dropdown(
                    'item_location_township_id',
                    $options,
                    set_value( 'item_location_township_id', show_data( @$item->item_location_township_id), false ),
                    'class="form-control form-control-sm mr-3" id="item_location_township_id"'
                  );
                }
                
              ?>

            </div>


            <?php if (  @$item->lat !='0' && @$item->lng !='0' ):?>
              <div id="itm_location" style="width: 100%; height: 200px;"></div>
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
                            'value' => set_value( 'lat', show_data( @$item->lat ), false ),
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
                            'value' =>  set_value( 'lng', show_data( @$item->lng ), false ),
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
                    if (!$item) {
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

                          if($item->id != "") {
                              $conds['item_id'] = $item->id;
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
                  
                  <div class="form-group">
                    <label>
                      Item Gallery
                    </label>
                      <br/>
                    <input class="btn btn-sm" type="file" name="item_gallery[]" accept=".jpg,.jpeg,.png" multiple>
                  </div>

                  <?php if ( !isset( $item )): ?>

              <div class="form-group">
                <span style="font-size: 17px; color: red;">*</span>
                <label><?php echo get_msg('item_img')?>
                  <a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('item_img')?>">
                    <span class='glyphicon glyphicon-info-sign menu-icon'>
                  </a>
                </label>

                  <p class="mb-0 d-inline-block">
                      (<?php echo get_msg('recommended_size_img')?>)
                  </p>


                  <br/>

                <input class="btn btn-sm" type="file" name="cover" accept=".jpg,.jpeg,.png">
              </div>

              <?php else: ?>
              <span style="font-size: 17px; color: red;">*</span>
              <label><?php echo get_msg('item_img')?>
                <a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('cat_photo_tooltips')?>">
                  <span class='glyphicon glyphicon-info-sign menu-icon'>
                </a>
              </label> 
              
              <div class="btn btn-sm btn-primary btn-upload pull-right" data-toggle="modal" data-target="#uploadImage">
                <?php echo get_msg('btn_replace_photo')?>
              </div>

              <br>

              <p class="mb-0 d-inline-block">
                  <?php echo get_msg('recommended_size_img')?>
              </p>

              <hr/>
            
              <?php
                $conds = array( 'img_type' => 'item', 'img_parent_id' => $item->id, 'ordering' => '1' );
                $images = $this->Image->get_all_by( $conds )->result();
                $conds1 = array( 'img_type' => 'item', 'img_parent_id' => $item->id );
                $images1 = $this->Image->get_all_by( $conds1 )->result();
                
                if (!empty($images)) {
                  $img_path = $images[0]->img_path;
                } else if (!empty($images1)) {
                  $img_path = $images1[0]->img_path;
                } else {
                  $img_path = 'no_image.png';
                }
              ?>
                
                  <div class="col-md-4" style="height:100">

                    <div class="thumbnail">

                      <img src="<?php echo $this->ps_image->upload_thumbnail_url . $img_path; ?>">

                      <br/>
                      
                      <p class="text-center">
                        <?php if (!empty($images)) { ?>

                          <a data-toggle="modal" data-target="#deletePhoto" class="delete-img" id="<?php echo $images[0]->img_id; ?>"   
                            image="<?php echo $img->img_path; ?>">
                            <?php echo get_msg('remove_label'); ?>
                          </a>
                        <?php } else if (!empty($images1)) { ?>
                          <a data-toggle="modal" data-target="#deletePhoto" class="delete-img" id="<?php echo $images1[0]->img_id; ?>"   
                            image="<?php echo $img->img_path; ?>">
                            <?php echo get_msg('remove_label'); ?>
                          </a>
                        <?php } ?>    
                      </p>

                    </div>

                  </div>

            <?php endif; ?> 
            <!-- End Item default photo -->
            <!-- Item video upload -->
            <?php if ( !isset( $item )): ?>

              <div class="form-group">
                <span style="font-size: 17px; color: red;"></span>
                <label><?php echo get_msg('item_video_label')?>
                  <a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('item_video_label')?>">
                    <span class='fa fa-info-sign menu-icon'>
                  </a>
                </label>

                <br/>

                <input class="btn btn-sm" type="file" name="video" accept=".flv,.f4v,.f4p,.mp4">
              </div>

              <?php else: ?>
              <span style="font-size: 17px; color: red;"></span>
              <label><?php echo get_msg('item_video_label')?>
                <a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('cat_photo_tooltips')?>">
                  <span class='fa fa-info-sign menu-icon'>
                </a>
              </label> 
              
              <div class="btn btn-sm btn-primary btn-upload pull-right" data-toggle="modal" data-target="#uploadvideo">
                <?php echo get_msg('btn_replace_video_label')?>
              </div>
              
              <hr/>

              <?php
                  $conds = array( 'img_type' => 'video', 'img_parent_id' => $item->id );
                  $videos = $this->Image->get_all_by($conds)->result();
                ?>
            
                <?php if ( count($videos) > 0 ): ?>
              
                    <div class="row">

                      <?php $i = 0; foreach ( $videos as $video ) :?>

                        <?php if ($i>0 && $i%3==0): ?>
                            
                        </div><div class='row'>
                        
                        <?php endif; ?>
                        
                        <div class="col-md-4">

                          <video width="320" height="240" controls>
                              <source src="<?php echo $this->ps_image->upload_url . $video->img_path; ?>" type="video/mp4" / >
                              This text displays if the video tag isn't supported.
                          </video>

                          <br/>
                            
                            <p class="text-center">
                              
                              <a data-toggle="modal" data-target="#deleteVideo" class="delete-video" id="<?php echo $video->img_id; ?>"   
                                image="<?php echo $video->img_path; ?>">
                                <?php echo get_msg('remove_label'); ?>
                              </a>
                            </p>

                        </div>

                      <?php $i++; endforeach; ?>

                    </div>
            
                  <?php endif; ?>
                
            <?php endif; ?> 
            <!-- End Item video -->

            <!-- End item cover photo -->
        <?php if ( !isset( $item )): ?>

          <div class="form-group">
            <span style="font-size: 17px; color: red;"></span>
            <label>
              <?php echo get_msg('item_video_icon_label')?> 
            </label>

              <p class="mb-0 d-inline-block">
                  (<?php echo get_msg('recommended_size_icon')?>)
              </p>


              <br/>

            <input class="btn btn-sm" type="file" name="icon" id="icon" accept=".jpg,.jpeg,.png">
          </div>

        <?php else: ?>
          <span style="font-size: 17px; color: red;"></span>
          <label><?php echo get_msg('item_video_icon_label')?></label> 
          
          
          <div class="btn btn-sm btn-primary btn-upload pull-right" data-toggle="modal" data-target="#uploadIcon">
            <?php echo get_msg('btn_replace_icon')?>
          </div>

           <br>

            <p class="mb-0 d-inline-block">
                <?php echo get_msg('recommended_size_icon')?>
            </p>


            <hr/>
          
          <?php

            $conds = array( 'img_type' => 'video-icon', 'img_parent_id' => $item->id );
            
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
                    
                    <a data-toggle="modal" data-target="#deletePhoto" class="delete-img" id="<?php echo $img->img_id; ?>"   
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
          </div>
        </div>  


      <!-- Grid row -->
      <?php if ( isset( $item )): ?>
      <div class="gallery" id="gallery" style="margin-left: 15px; margin-bottom: 15px;">
        <?php
            $conds = array( 'img_type' => 'item', 'img_parent_id' => $item->id );
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
        </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-sm btn-primary" style="margin-top: 3px;">
          <?php echo get_msg('btn_save')?>
        </button>

        <button type="submit" name="gallery" id="gallery" class="btn btn-sm btn-primary" style="margin-top: 3px;">
          <?php echo get_msg('btn_save_gallery')?>
        </button>

        <button type="submit" name="promote" id="promote" class="btn btn-sm btn-primary" style="margin-top: 3px;">
          <?php echo get_msg('btn_promote')?>
        </button>

        <a href="<?php echo $module_site_url; ?>" class="btn btn-sm btn-primary" style="margin-top: 3px;">
          <?php echo get_msg('btn_cancel')?>
        </a>
      </div>
    </form>
  </div>
</section>

<script>
  $(document).ready(function () {
    // Show/hide date fields based on event type
    function toggleDateFields() {
      const eventType = $('input[name="event_type"]:checked').val();
      if (eventType === 'event') {
        $('#start_date_stop_date').show();
      } else {
        $('#start_date_stop_date').hide();
      }
    }

    // Trigger on page load (to handle pre-checked state)
    toggleDateFields();

    // Trigger on change of event type
    $('input[name="event_type"]').on('change', function () {
      toggleDateFields();
    });

  });
</script>

<script>
  $(function () {
    // Initialize Date Pickers
    $('#start_date').daterangepicker({
      singleDatePicker: true,
      autoUpdateInput: false,
      locale: { format: 'YYYY-MM-DD' }
    });

    $('#stop_date').daterangepicker({
      singleDatePicker: true,
      autoUpdateInput: false,
      locale: { format: 'YYYY-MM-DD' }
    });

    // Update minDate for stop_date dynamically based on start_date
    $('#start_date').on('apply.daterangepicker', function (ev, picker) {
      const selectedStartDate = picker.startDate.format('YYYY-MM-DD');
      $(this).val(selectedStartDate);
      
      // Disable dates in stop_date before the selected start_date
      const stopDatePicker = $('#stop_date').data('daterangepicker');
      stopDatePicker.minDate = picker.startDate;
      stopDatePicker.updateCalendars();
    });

    // Update maxDate for start_date dynamically based on stop_date
    $('#stop_date').on('apply.daterangepicker', function (ev, picker) {
      const selectedStopDate = picker.startDate.format('YYYY-MM-DD');
      $(this).val(selectedStopDate);

      // Disable dates in start_date after the selected stop_date
      const startDatePicker = $('#start_date').data('daterangepicker');
      startDatePicker.maxDate = picker.startDate;
      startDatePicker.updateCalendars();
    });
  });
</script>



        <!-- item map -->
        
        <script>
            <?php
                if (isset($item)) {
                    $lat = $item->lat;
                    $lng = $item->lng;
            ?>
                    var itm_map = L.map('itm_location').setView([<?php echo $lat;?>, <?php echo $lng;?>], 5);
            <?php
                } else {
            ?>
                    var itm_map = L.map('itm_location').setView([0, 0], 5);
            <?php
                }
            ?>

            const itm_attribution =
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
            const itm_tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            const itm_tiles = L.tileLayer(itm_tileUrl, { itm_attribution });
            itm_tiles.addTo(itm_map);
            <?php if(isset($item)) {?>
                var itm_marker = new L.Marker(new L.LatLng(<?php echo $lat;?>, <?php echo $lng;?>));
                itm_map.addLayer(itm_marker);
                // results = L.marker([<?php echo $lat;?>, <?php echo $lng;?>]).addTo(mymap);

            <?php } else { ?>
                var itm_marker = new L.Marker(new L.LatLng(0, 0));
                //mymap.addLayer(marker2);
            <?php } ?>
            var itm_searchControl = L.esri.Geocoding.geosearch().addTo(itm_map);
            var results = L.layerGroup().addTo(itm_map);

            itm_searchControl.on('results',function(data){
                results.clearLayers();

                for(var i= data.results.length -1; i>=0; i--) {
                    itm_map.removeLayer(itm_marker);
                    results.addLayer(L.marker(data.results[i].latlng));
                    var itm_search_str = data.results[i].latlng.toString();
                    var itm_search_res = itm_search_str.substring(itm_search_str.indexOf("(") + 1, itm_search_str.indexOf(")"));
                    var itm_searchArr = new Array();
                    itm_searchArr = itm_search_res.split(",");

                    document.getElementById("lat").value = itm_searchArr[0].toString();
                    document.getElementById("lng").value = itm_searchArr[1].toString(); 
                   
                }
            })
            var popup = L.popup();

            function onMapClick(e) {

                var itm = e.latlng.toString();
                var itm_res = itm.substring(itm.indexOf("(") + 1, itm.indexOf(")"));
                itm_map.removeLayer(itm_marker);
                results.clearLayers();
                results.addLayer(L.marker(e.latlng));   

                var itm_tmpArr = new Array();
                itm_tmpArr = itm_res.split(",");

                document.getElementById("lat").value = itm_tmpArr[0].toString(); 
                document.getElementById("lng").value = itm_tmpArr[1].toString();
            }

            itm_map.on('click', onMapClick);
        </script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDY9B2K8mOhMaB-LId-iXPw-YRvUowqJEE&amp;libraries=places"></script>

<script>
  let autocomplete;

  function initializeAutocomplete() {
    const input = document.getElementById('place-search');
    autocomplete = new google.maps.places.Autocomplete(input, {
      types: [],
    });

    autocomplete.addListener('place_changed', () => {
      const place = autocomplete.getPlace();
      console.log('place',place);
      if (!place.geometry) {
        alert("No details available for the selected place!");
        return;
      }

      const url = place.url || "";
      const placeId = place.place_id || "";
      const name = place.name || "";
      const address = place.formatted_address || "";
      const rating = place.rating || "0";
      const reviewCount = place.user_ratings_total || "0";
      const latitude = place.geometry.location.lat();
      const longitude = place.geometry.location.lng();
      const photos = place.photos || [];

      // Display details in console
      console.log(`placeId: ${placeId}`);
      console.log(`Place: ${name}`);
      console.log(`Url: ${url}`);
      console.log(`Address: ${address}`);
      console.log(`Rating: ${rating}`);
      console.log(`Reviews Count: ${reviewCount}`);
      console.log(`Latitude: ${latitude}, Longitude: ${longitude}`);
      
      if (photos.length > 0) {
        console.log("Photos:");
        photos.slice(0, 3).forEach((photo, index) => {
          console.log(`Photo ${index + 1}: ${photo.getUrl()}`);
        });
      } else {
        console.log("No photos available");
      }
       
       document.getElementById('g_review_place_id').value = placeId;
       document.getElementById('g_review_score').value = rating;
       document.getElementById('g_review_quantity').value = reviewCount;
       document.getElementById('g_review_link').value = url;
       document.getElementById('lat').value = latitude;
       document.getElementById('lng').value = longitude;
       document.getElementById('address').value = address;
       document.getElementById('title').value = name;

      // alert(`
      //   placeId: ${placeId}
      //   Place: ${name}
      //   Url: ${url}
      //   Address: ${address}
      //   Rating: ${rating}
      //   Reviews: ${reviewCount}
      //   Lat, Lng: ${latitude}, ${longitude}
      // `);
    });
  }

  window.onload = initializeAutocomplete;

  document.getElementById('import-button').addEventListener('click', () => {
    const place = autocomplete.getPlace();
    if (place && place.geometry) {
      alert(`Place ${place.name} has been imported!`);
      
    } else {
      alert("Please select a place first!");
    }
  });
</script>