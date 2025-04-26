<?php
	$attributes = array('id' => 'search-form', 'enctype' => 'multipart/form-data');
	echo form_open( $module_site_url .'/search', $attributes);
?>

<div class='row my-3'>
	<div class="col-12">
		<div class='form-inline'>
			<div class="form-group" style="padding-top: 3px;padding-right: 2px;">

				<?php echo form_input(array(
					'name' => 'searchterm',
					'value' => set_value( 'searchterm', $searchterm ),
					'class' => 'form-control form-control-sm mr-2',
					'placeholder' => get_msg( 'btn_search' )
				)); ?>

		  	</div>

		  	<div class="form-group" style="padding-top: 3px;padding-right: 2px;">

				<?php
					$options=array();
					$options[0]=get_msg('itm_select_property');
					
					$propeties = $this->PropertyBy->get_all( );
					foreach($propeties->result() as $property) {
						
						$options[$property->id]=$property->name;
					}
					
					echo form_dropdown(
						'property_by_id',
						$options,
						set_value( 'property_by_id', show_data( $property_by_id ), false ),
						'class="form-control form-control-sm mr-2" id="property_by_id"'
					);
				?> 

		  	</div>

		  	<div class="form-group" style="padding-top: 3px;padding-right: 2px;">

				<?php
					$options=array();
					$options[0]=get_msg('itm_select_price');
					
					$pricetypes = $this->Pricetype->get_all( );
					foreach($pricetypes->result() as $price) {
						
						$options[$price->id]=$price->name;
					}
					
					echo form_dropdown(
						'item_price_type_id',
						$options,
						set_value( 'item_price_type_id', show_data( $item_price_type_id ), false ),
						'class="form-control form-control-sm mr-3" id="item_price_type_id"'
					);
				?> 

		  	</div>

		  	<div class="form-group" style="padding-top: 3px;padding-right: 2px;">

				<?php
					$options=array();
					$options[0]=get_msg('itm_select_postedby');
					
					$posted = $this->Postedby->get_all( );
					foreach($posted->result() as $post) {
						
						$options[$post->id]=$post->name;
					}
					
					echo form_dropdown(
						'posted_by_id',
						$options,
						set_value( 'posted_by_id', show_data( $posted_by_id ), false ),
						'class="form-control form-control-sm mr-2" id="posted_by_id"'
					);
				?> 

		  	</div>

		  	<div class="form-group" style="padding-top: 3px;padding-right: 2px;">

				<?php
					$options=array();
					$options[0]=get_msg('itm_select_currency');
					
					$currencies = $this->Currency->get_all( );
					foreach($currencies->result() as $currency) {
						
						$options[$currency->id]=$currency->currency_short_form;
					}
					
					echo form_dropdown(
						'item_currency_id',
						$options,
						set_value( 'item_currency_id', show_data( $item_currency_id ), false ),
						'class="form-control form-control-sm mr-2" id="item_currency_id"'
					);
				?> 

		  	</div>

			<div class="form-group" style="padding-top: 3px;padding-right: 2px;">

				<?php
					$options=array();
					$options[0]=get_msg('itm_select_location');
					
					$locations = $this->Itemlocation->get_all( );
					foreach($locations->result() as $location) {
						
						$options[$location->id]=$location->name;
					}
					
					echo form_dropdown(
						'item_location_city_id',
						$options,
						set_value( 'item_location_city_id', show_data( $item_location_city_id ), false ),
						'class="form-control form-control-sm mr-2" id="item_location_city_id"'
					);
				?> 

		  	</div>

		  	<div class="form-group" style="padding-top: 3px;">

				<?php
					if($selected_location_city_id != "") {
						$options=array();
						$options[0]=get_msg('Prd_search_location_township');
						$conds['city_id'] = $selected_location_city_id;
						$townships = $this->Item_location_township->get_all_by($conds);
						foreach($townships->result() as $township) {
							$options[$township->id]=$township->township_name;
						}
						echo form_dropdown(
							'item_location_township_id',
							$options,
							set_value( 'item_location_township_id', show_data( $item_location_township_id ), false ),
							'class="form-control form-control-sm mr-2" id="item_location_township_id"'
						);

					} else {

						$conds['city_id'] = $selected_location_city_id;
						$options=array();
						$options[0]=get_msg('Prd_search_location_township');

						echo form_dropdown(
							'item_location_township_id',
							$options,
							set_value( 'item_location_township_id', show_data( $item_location_township_id ), false ),
							'class="form-control form-control-sm mr-2" id="item_location_township_id"'
						);
					}
				?>

		  	</div>

		  	<div class="form-group" style="padding-top: 3px;padding-right: 2px;">
						
				<select class="form-control form-control-sm mr-3" name="is_paid" id="is_paid">

					<option value="0"><?php echo get_msg('select_payment_status_label');?></option>
							
						<?php

							$array = array('Paid' => 1, 'Reject' => 2, 'Waiting For Approval' => 3);
	    					foreach ($array as $key=>$value) {
	    						if($value == $is_paid) {
		    						echo '<option value="'.$value.'" selected>'.$key.'</option>';
		    					} else {
		    						echo '<option value="'.$value.'">'.$key.'</option>';
		    					}
	    					}
						?>
				</select> 
			</div>

		  	<div class="form-group" style="padding-top: 3px;padding-right: 5px;">
			  	<button type="submit" value="submit" name="submit" class="btn btn-sm btn-primary">
			  		<?php echo get_msg( 'btn_search' )?>
			  	</button>
		  	</div>
		
			<div class="form-group" style="padding-top: 3px;">
			  	<a href='<?php echo $module_site_url .'/index';?>' class='btn btn-sm btn-primary'>
					<?php echo get_msg( 'btn_reset' )?>
				</a>
		  	</div>

		</div>
	</div>

</div>


<?php echo form_close(); ?>

<script>
	
	$('#item_location_city_id').on('change', function() {

		var value = $('option:selected', this).text().replace(/Value\s/, '');

		var city_id = $(this).val();

		$.ajax({
			url: '<?php echo $module_site_url . '/get_all_location_townships/';?>' + city_id,
			method: 'GET',
			dataType: 'JSON',
			success:function(data){
				$('#item_location_township_id').html("");
				$.each(data, function(i, obj){
				    $('#item_location_township_id').append('<option value="'+ obj.id +'">' + obj.township_name+ '</option>');
				});
				$('#township_name').val($('#township_name').val() + " ").blur();
				$('#item_location_township_id').trigger('change');
			}
		});
	});
	
</script>