<?php
$attributes = array('id' => 'app-form', 'enctype' => 'multipart/form-data');
echo form_open('', $attributes);
?>

<div class="content animated fadeInRight">
	<div class="">
		<div class="card card-info">
			<div class="card-header">
				<h3 class="card-title"><?php echo get_msg('app_config_info_lable') ?></h3>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row">

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('google_playstore_url') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('google_playstore_url') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'google_playstore_url',
								'id' => 'google_playstore_url',
								'class' => 'form-control',
								'placeholder' => get_msg('google_playstore_url'),
								'value' =>  set_value('google_playstore_url', show_data(@$app->google_playstore_url), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('apple_appstore_url') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('apple_appstore_url') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'apple_appstore_url',
								'id' => 'apple_appstore_url',
								'class' => 'form-control',
								'placeholder' => get_msg('apple_appstore_url'),
								'value' =>  set_value('apple_appstore_url', show_data(@$app->apple_appstore_url), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('ios_appstore_id') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('ios_appstore_id') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'ios_appstore_id',
								'id' => 'ios_appstore_id',
								'class' => 'form-control',
								'placeholder' => get_msg('ios_appstore_id'),
								'value' =>  set_value('ios_appstore_id', show_data(@$app->ios_appstore_id), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('fb_key') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('fb_key') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'fb_key',
								'id' => 'fb_key',
								'class' => 'form-control',
								'placeholder' => get_msg('fb_key'),
								'value' =>  set_value('fb_key', show_data(@$app->fb_key), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('price_format') ?><br> <small><?php echo get_msg('price_format_desc') ?></small>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('price_format') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'price_format',
								'id' => 'price_format',
								'class' => 'form-control',
								'placeholder' => get_msg('price_format'),
								'value' =>  set_value('price_format', show_data(@$app->price_format), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('date_format') ?><br> <small><?php echo get_msg('date_format_desc') ?></small>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('date_format') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'date_format',
								'id' => 'date_format',
								'class' => 'form-control',
								'placeholder' => get_msg('date_format'),
								'value' =>  set_value('date_format', show_data(@$app->date_format), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('blue_mark_size') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('blue_mark_size') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'blue_mark_size',
								'id' => 'blue_mark_size',
								'class' => 'form-control',
								'placeholder' => get_msg('blue_mark_size'),
								'value' =>  set_value('blue_mark_size', show_data(@$app->blue_mark_size), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('mile') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('mile') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'mile',
								'id' => 'mile',
								'class' => 'form-control',
								'placeholder' => get_msg('mile'),
								'value' =>  set_value('mile', show_data(@$app->mile), false),
							));
							?>
						</div>
					</div>


					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('video_duration') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('video_duration') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'video_duration',
								'id' => 'video_duration',
								'class' => 'form-control',
								'placeholder' => get_msg('video_duration'),
								'value' =>  set_value('video_duration', show_data(@$app->video_duration), false),
							));
							?>
						</div>
					</div>

					<!-- <div class="col-md-6">						
						<div class="form-group">
							<label><?php echo get_msg('default_order_time') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" 
									title="<?php echo get_msg('default_order_time') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'default_order_time',
								'id' => 'default_order_time',
								'class' => 'form-control',
								'placeholder' => get_msg('default_order_time'),
								'value' =>  set_value('default_order_time', show_data(@$app->default_order_time), false),
							));
							?>
						</div>
					</div> -->

					<div class="col-12">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<div class="form-check">
										<label class="form-check-label">
											<?php echo form_checkbox(array(
												'name' => 'is_show_token_id',
												'id' => 'is_show_token_id',
												'value' => 'accept',
												'checked' => set_checkbox('is_show_token_id', 1, (@$app->is_show_token_id == 1) ? true : false),
												'class' => 'form-check-input'
											));	?>
											<?php echo get_msg('is_show_token_id'); ?>
										</label>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<div class="form-check">
										<label class="form-check-label">
											<?php echo form_checkbox(array(
												'name' => 'is_use_googlemap',
												'id' => 'is_use_googlemap',
												'value' => 'accept',
												'checked' => set_checkbox('is_use_googlemap', 1, (@$app->is_use_googlemap == 1) ? true : false),
												'class' => 'form-check-input'
											));	?>
											<?php echo get_msg('is_use_googlemap'); ?>
										</label>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<div class="form-check">
										<label class="form-check-label">
											<?php echo form_checkbox(array(
												'name' => 'is_use_thumbnail_as_placeholder',
												'id' => 'is_use_thumbnail_as_placeholder',
												'value' => 'accept',
												'checked' => set_checkbox('is_use_thumbnail_as_placeholder', 1, (@$app->is_use_thumbnail_as_placeholder == 1) ? true : false),
												'class' => 'form-check-input'
											));	?>
											<?php echo get_msg('is_use_thumbnail_as_placeholder'); ?>
										</label>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<div class="form-check">
										<label class="form-check-label">
											<?php echo form_checkbox(array(
												'name' => 'no_filter_with_location_on_map',
												'id' => 'no_filter_with_location_on_map',
												'value' => 'accept',
												'checked' => set_checkbox('no_filter_with_location_on_map', 1, (@$app->no_filter_with_location_on_map == 1) ? true : false),
												'class' => 'form-check-input'
											));	?>
											<?php echo get_msg('no_filter_with_location_on_map'); ?>
										</label>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<div class="form-check">
										<label class="form-check-label">
											<?php echo form_checkbox(array(
												'name' => 'is_show_owner_info',
												'id' => 'is_show_owner_info',
												'value' => 'accept',
												'checked' => set_checkbox('is_show_owner_info', 1, (@$app->is_show_owner_info == 1) ? true : false),
												'class' => 'form-check-input'
											));	?>
											<?php echo get_msg('is_show_owner_info'); ?>
										</label>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<div class="form-check">
										<label class="form-check-label">
											<?php echo form_checkbox(array(
												'name' => 'is_force_login',
												'id' => 'is_force_login',
												'value' => 'accept',
												'checked' => set_checkbox('is_force_login', 1, (@$app->is_force_login == 1) ? true : false),
												'class' => 'form-check-input'
											));	?>
											<?php echo get_msg('is_force_login'); ?>
										</label>
									</div>
								</div>
							</div>
							
							<div class="col-md-6">
								<div class="form-group">
									<div class="form-check">
										<label class="form-check-label">
											<?php echo form_checkbox(array(
												'name' => 'is_language_config',
												'id' => 'is_language_config',
												'value' => 'accept',
												'checked' => set_checkbox('is_language_config', 1, (@$app->is_language_config == 1) ? true : false),
												'class' => 'form-check-input'
											));	?>
											<?php echo get_msg('is_language_config'); ?>
										</label>
									</div>
								</div>
							</div>

						</div>
					</div>
					<!-- 
					<hr width="100%" class="my-5">

					<legend class="mx-3 mb-4 font-weight-bold"><?php echo get_msg('loc_section') ?></legend>
					<div class="col-md-6">
					  	<div id="app_location" style="width: 100%; height: 250px;"></div>
            			<div class="clearfix">&nbsp;</div>
					</div>
					
		          	<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('shop_lat_label') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('shop_lat_label') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'lat',
								'id' => 'lat',
								'class' => 'form-control',
								'placeholder' => get_msg('shop_lat_label'),
								'value' => set_value('lat', show_data(@$app->lat), false),
							));
							?>
						</div>
						
						<div class="form-group">
							<label><?php echo get_msg('shop_lng_label') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" 
									title="<?php echo get_msg('shop_lng_label') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'lng',
								'id' => 'lng',
								'class' => 'form-control',
								'placeholder' => get_msg('shop_lng_label'),
								'value' =>  set_value('lng', show_data(@$app->lng), false),
							));
							?>
						</div>
					</div> -->
					<hr width="100%" class="my-5">

					<legend class="mx-3 mb-4 font-weight-bold"><?php echo get_msg('promote_section') ?></legend>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('promote_first_choice_day') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('promote_first_choice_day') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'promote_first_choice_day',
								'id' => 'promote_first_choice_day',
								'class' => 'form-control',
								'placeholder' => get_msg('promote_first_choice_day'),
								'value' =>  set_value('promote_first_choice_day', show_data(@$app->promote_first_choice_day), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('promote_second_choice_day') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('promote_second_choice_day') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'promote_second_choice_day',
								'id' => 'promote_second_choice_day',
								'class' => 'form-control',
								'placeholder' => get_msg('promote_second_choice_day'),
								'value' =>  set_value('promote_second_choice_day', show_data(@$app->promote_second_choice_day), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('promote_third_choice_day') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('promote_third_choice_day') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'promote_third_choice_day',
								'id' => 'promote_third_choice_day',
								'class' => 'form-control',
								'placeholder' => get_msg('promote_third_choice_day'),
								'value' =>  set_value('promote_third_choice_day', show_data(@$app->promote_third_choice_day), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('promote_fourth_choice_day') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('promote_fourth_choice_day') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'promote_fourth_choice_day',
								'id' => 'promote_fourth_choice_day',
								'class' => 'form-control',
								'placeholder' => get_msg('promote_fourth_choice_day'),
								'value' =>  set_value('promote_fourth_choice_day', show_data(@$app->promote_fourth_choice_day), false),
							));
							?>
						</div>
					</div>



					<hr width="100%" class="my-5">

					<legend class="mx-3 mb-4 font-weight-bold"><?php echo get_msg('image_section') ?></legend>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('profile_image_size') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('profile_image_size') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'profile_image_size',
								'id' => 'profile_image_size',
								'class' => 'form-control',
								'placeholder' => get_msg('profile_image_size'),
								'value' =>  set_value('profile_image_size', show_data(@$app->profile_image_size), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('upload_image_size') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('upload_image_size') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'upload_image_size',
								'id' => 'upload_image_size',
								'class' => 'form-control',
								'placeholder' => get_msg('upload_image_size'),
								'value' =>  set_value('upload_image_size', show_data(@$app->upload_image_size), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('chat_image_size') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('chat_image_size') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'chat_image_size',
								'id' => 'chat_image_size',
								'class' => 'form-control',
								'placeholder' => get_msg('chat_image_size'),
								'value' =>  set_value('chat_image_size', show_data(@$app->chat_image_size), false),
							));
							?>
						</div>
					</div>


					<hr width="100%" class="my-5">

					<legend class="mx-3 mb-4 font-weight-bold"><?php echo get_msg('admob_section') ?></legend>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('item_detail_view_count_for_ads') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('item_detail_view_count_for_ads') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'item_detail_view_count_for_ads',
								'id' => 'item_detail_view_count_for_ads',
								'class' => 'form-control',
								'placeholder' => get_msg('item_detail_view_count_for_ads'),
								'value' =>  set_value('item_detail_view_count_for_ads', show_data(@$app->item_detail_view_count_for_ads), false),
							));
							?>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
									<?php echo form_checkbox(array(
										'name' => 'is_show_admob',
										'id' => 'is_show_admob',
										'value' => 'accept',
										'checked' => set_checkbox('is_show_admob', 1, (@$app->is_show_admob == 1) ? true : false),
										'class' => 'form-check-input'
									));	?>
									<?php echo get_msg('is_show_admob'); ?>
								</label>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
									<?php echo form_checkbox(array(
										'name' => 'is_show_ads_in_item_detail',
										'id' => 'is_show_ads_in_item_detail',
										'value' => 'accept',
										'checked' => set_checkbox('is_show_ads_in_item_detail', 1, (@$app->is_show_ads_in_item_detail == 1) ? true : false),
										'class' => 'form-check-input'
									));	?>
									<?php echo get_msg('is_show_ads_in_item_detail'); ?>
								</label>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
									<?php echo form_checkbox(array(
										'name' => 'is_show_admob_inside_list',
										'id' => 'is_show_admob_inside_list',
										'value' => 'accept',
										'checked' => set_checkbox('is_show_admob_inside_list', 1, (@$app->is_show_admob_inside_list == 1) ? true : false),
										'class' => 'form-check-input'
									));	?>
									<?php echo get_msg('is_show_admob_inside_list'); ?>
								</label>
							</div>
						</div>
					</div>


					<hr width="100%" class="my-5">

					<legend class="mx-3 mb-4 font-weight-bold"><?php echo get_msg('lang_section') ?></legend>
					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('default_language') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('default_language') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>

							<?php
							$options[0] = get_msg('default_lang_select');
							foreach ($languages as $language) {
								$options[$language['language_code']] = $language['name'];
							}
							echo form_dropdown(
								'default_language',
								$options,
								set_value('default_language', show_data(@$app->default_language), false),
								'class="form-control form-control-sm mr-3" id="default_language"'
							);
							?>
						</div>
					</div>
					<div class="col-12 mt-3">
						<div class="form-group">
							<label><?php echo get_msg('exclude_language') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('exclude_language') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br><?php echo get_msg('exclude_lang_desc') ?>
						</div>

						<div class="row">
							<?php
							$exclude_language = explode(',', trim($app->exclude_language));
							foreach ($languages as $language) : ?>
								<div class="col-6">
									<div class="form-group">
										<div class="form-check">
											<label class="form-check-label">
												<?php echo form_checkbox(array(
													'name' => $language['language_code'],
													'id' => $language['language_code'],
													'value' => 'accept',
													'checked' => set_checkbox($language['language_code'], 1, (in_array($language['language_code'], $exclude_language)) ? false : true),
													'class' => 'form-check-input'
												));	?>
												<?php echo $language['name'] . "( " . $language['language_code'] . '_' . $language['country_code'] . " )"; ?>
											</label>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<hr width="100%" class="my-5">

					<legend class="mx-3 mb-4 font-weight-bold"><?php echo get_msg('default_currency_section') ?></legend>
					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('default_razor_currency') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('default_razor_currency') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'default_razor_currency',
								'id' => 'default_razor_currency',
								'class' => 'form-control',
								'placeholder' => get_msg('default_razor_currency'),
								'value' =>  set_value('default_razor_currency', show_data(@$app->default_razor_currency), false),
							));
							?>
						</div>
					</div>

					<!-- <div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('default_flutter_wave_currency') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" 
									title="<?php echo get_msg('default_flutter_wave_currency') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'default_flutter_wave_currency',
								'id' => 'default_flutter_wave_currency',
								'class' => 'form-control',
								'placeholder' => get_msg('default_flutter_wave_currency'),
								'value' =>  set_value('default_flutter_wave_currency', show_data(@$app->default_flutter_wave_currency), false),
							));
							?>
						</div>
		          	</div> -->

					<div class="col-md-6">
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
									<?php echo form_checkbox(array(
										'name' => 'is_razor_support_multi_currency',
										'id' => 'is_razor_support_multi_currency',
										'value' => 'accept',
										'checked' => set_checkbox('is_razor_support_multi_currency', 1, (@$app->is_razor_support_multi_currency == 1) ? true : false),
										'class' => 'form-check-input'
									));	?>
									<?php echo get_msg('is_razor_support_multi_currency'); ?>
								</label>
							</div>
						</div>
					</div>

					<hr width="100%" class="my-5">

					<legend class="mx-3 mb-4 font-weight-bold"><?php echo get_msg('login_section') ?></legend>
					<div class="col-md-6">
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
									<?php echo form_checkbox(array(
										'name' => 'show_facebook_login',
										'id' => 'show_facebook_login',
										'value' => 'accept',
										'checked' => set_checkbox('show_facebook_login', 1, (@$app->show_facebook_login == 1) ? true : false),
										'class' => 'form-check-input'
									));	?>
									<?php echo get_msg('show_facebook_login'); ?>
								</label>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
									<?php echo form_checkbox(array(
										'name' => 'show_phone_login',
										'id' => 'show_phone_login',
										'value' => 'accept',
										'checked' => set_checkbox('show_phone_login', 1, (@$app->show_phone_login == 1) ? true : false),
										'class' => 'form-check-input'
									));	?>
									<?php echo get_msg('show_phone_login'); ?>
								</label>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
									<?php echo form_checkbox(array(
										'name' => 'show_google_login',
										'id' => 'show_google_login',
										'value' => 'accept',
										'checked' => set_checkbox('show_google_login', 1, (@$app->show_google_login == 1) ? true : false),
										'class' => 'form-check-input'
									));	?>
									<?php echo get_msg('show_google_login'); ?>
								</label>
							</div>
						</div>
					</div>

					<hr width="100%" class="my-5">

					<!-- <legend class="mx-3 mb-4 font-weight-bold"><?php echo get_msg('dashboard_section') ?></legend>
					<div class="col-md-6">
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
									<?php echo form_checkbox(array(
										'name' => 'show_main_menu',
										'id' => 'show_main_menu',
										'value' => 'accept',
										'checked' => set_checkbox('show_main_menu', 1, (@$app->show_main_menu == 1) ? true : false),
										'class' => 'form-check-input'
									));	?>
									<?php echo get_msg('show_main_menu'); ?>
								</label>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
									<?php echo form_checkbox(array(
										'name' => 'show_special_collections',
										'id' => 'show_special_collections',
										'value' => 'accept',
										'checked' => set_checkbox('show_special_collections', 1, (@$app->show_special_collections == 1) ? true : false),
										'class' => 'form-check-input'
									));	?>
									<?php echo get_msg('show_special_collections'); ?>
								</label>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
									<?php echo form_checkbox(array(
										'name' => 'show_featured_items',
										'id' => 'show_featured_items',
										'value' => 'accept',
										'checked' => set_checkbox('show_featured_items', 1, (@$app->show_featured_items == 1) ? true : false),
										'class' => 'form-check-input'
									));	?>
									<?php echo get_msg('show_featured_items'); ?>
								</label>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<div class="form-check">
								<label class="form-check-label">
									<?php echo form_checkbox(array(
										'name' => 'show_best_choice_slider',
										'id' => 'show_best_choice_slider',
										'value' => 'accept',
										'checked' => set_checkbox('show_best_choice_slider', 1, (@$app->show_best_choice_slider == 1) ? true : false),
										'class' => 'form-check-input'
									));	?>
									<?php echo get_msg('show_best_choice_slider'); ?>
								</label>
							</div>
						</div>
					</div> -->

					<!-- <hr width="100%" class="my-5"> -->

					<legend class="mx-3 mb-4 font-weight-bold"><?php echo get_msg('default_limit_section') ?></legend>
					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('default_loading_limit') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('default_loading_limit') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'default_loading_limit',
								'id' => 'default_loading_limit',
								'class' => 'form-control',
								'placeholder' => get_msg('default_loading_limit'),
								'value' =>  set_value('default_loading_limit', show_data(@$app->default_loading_limit), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('category_loading_limit') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('category_loading_limit') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'category_loading_limit',
								'id' => 'category_loading_limit',
								'class' => 'form-control',
								'placeholder' => get_msg('category_loading_limit'),
								'value' =>  set_value('category_loading_limit', show_data(@$app->category_loading_limit), false),
							));
							?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('posted_by_loading_limit') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('posted_by_loading_limit') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'posted_by_loading_limit',
								'id' => 'posted_by_loading_limit',
								'class' => 'form-control',
								'placeholder' => get_msg('posted_by_loading_limit'),
								'value' =>  set_value('posted_by_loading_limit', show_data(@$app->posted_by_loading_limit), false),
							));
							?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('agent_loading_limit') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('agent_loading_limit') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'agent_loading_limit',
								'id' => 'agent_loading_limit',
								'class' => 'form-control',
								'placeholder' => get_msg('agent_loading_limit'),
								'value' =>  set_value('agent_loading_limit', show_data(@$app->agent_loading_limit), false),
							));
							?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('amenities_loading_limit') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('amenities_loading_limit') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'amenities_loading_limit',
								'id' => 'amenities_loading_limit',
								'class' => 'form-control',
								'placeholder' => get_msg('amenities_loading_limit'),
								'value' =>  set_value('amenities_loading_limit', show_data(@$app->amenities_loading_limit), false),
							));
							?>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('recent_item_loading_limit') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('recent_item_loading_limit') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'recent_item_loading_limit',
								'id' => 'recent_item_loading_limit',
								'class' => 'form-control',
								'placeholder' => get_msg('recent_item_loading_limit'),
								'value' =>  set_value('recent_item_loading_limit', show_data(@$app->recent_item_loading_limit), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('popular_item_loading_limit') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('popular_item_loading_limit') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'popular_item_loading_limit',
								'id' => 'popular_item_loading_limit',
								'class' => 'form-control',
								'placeholder' => get_msg('popular_item_loading_limit'),
								'value' =>  set_value('popular_item_loading_limit', show_data(@$app->popular_item_loading_limit), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('discount_item_loading_limit') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('discount_item_loading_limit') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'discount_item_loading_limit',
								'id' => 'discount_item_loading_limit',
								'class' => 'form-control',
								'placeholder' => get_msg('discount_item_loading_limit'),
								'value' =>  set_value('discount_item_loading_limit', show_data(@$app->discount_item_loading_limit), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('feature_item_loading_limit') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('feature_item_loading_limit') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'feature_item_loading_limit',
								'id' => 'feature_item_loading_limit',
								'class' => 'form-control',
								'placeholder' => get_msg('feature_item_loading_limit'),
								'value' =>  set_value('feature_item_loading_limit', show_data(@$app->feature_item_loading_limit), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('block_slider_loading_limit') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('block_slider_loading_limit') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'block_slider_loading_limit',
								'id' => 'block_slider_loading_limit',
								'class' => 'form-control',
								'placeholder' => get_msg('block_slider_loading_limit'),
								'value' =>  set_value('block_slider_loading_limit', show_data(@$app->block_slider_loading_limit), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('follower_item_loading_limit') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('follower_item_loading_limit') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'follower_item_loading_limit',
								'id' => 'follower_item_loading_limit',
								'class' => 'form-control',
								'placeholder' => get_msg('follower_item_loading_limit'),
								'value' =>  set_value('follower_item_loading_limit', show_data(@$app->follower_item_loading_limit), false),
							));
							?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo get_msg('block_item_loading_limit') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('block_item_loading_limit') ?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label><br>
							<?php
							echo form_input(array(
								'type' => 'text',
								'name' => 'block_item_loading_limit',
								'id' => 'block_item_loading_limit',
								'class' => 'form-control',
								'placeholder' => get_msg('block_item_loading_limit'),
								'value' =>  set_value('block_item_loading_limit', show_data(@$app->block_item_loading_limit), false),
							));
							?>
						</div>
					</div>
					</div>
				</div>
				<!-- /.card-body -->
				<div class="card-footer">
					<button type="submit" name="save" class="btn btn-sm btn-primary">
						<?php echo get_msg('btn_save') ?>
					</button>

					<a href="<?php echo $module_site_url; ?>" class="btn btn-sm btn-primary">
						<?php echo get_msg('btn_cancel') ?>
					</a>
				</div>
				<!-- /.card footer-->

			</div>
		</div>
	</div>

	<?php echo form_close(); ?>
