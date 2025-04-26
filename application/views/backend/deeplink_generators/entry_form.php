<?php
$attributes = array('id' => 'thumbnail-form','enctype' => 'multipart/form-data');
echo form_open( '', $attributes);
?>
<section class="content animated fadeInRight">
	<div class="col-md-12">
		<div class="card card-info">
			<div class="card-header">
		        <h3 class="card-title"><?php echo get_msg('deeplink_generator_label')?></h3>
		    </div>

		    <?php
		    	$backend = $this->Backend_config->get_one('be1');
		    ?>

			<div class="card-body">


		        <div class="row">

		        <legend class="ml-3"><?php echo get_msg('deeplink_section')?></legend>
		        <hr width="100%">
	            <div class="col-md-6">
	            	<div class="form-group">
						<label><?php echo get_msg('dyn_link_key_label')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('dyn_link_key_label')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>
						<?php 
							echo form_input( array(
								'type' => 'text',
								'name' => 'dyn_link_key',
								'id' => 'dyn_link_key',
								'class' => 'form-control',
								'placeholder' => get_msg('dyn_link_key_label'),
								'value' => set_value( 'dyn_link_key', show_data( @$backend->dyn_link_key ), false ),
								'readonly' => 'true'
							));
						?>
					</div>

					<div class="form-group">
						<label><?php echo get_msg('dyn_link_url_label')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('dyn_link_url_label')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>
						<?php 
							echo form_input( array(
								'type' => 'text',
								'name' => 'dyn_link_url',
								'id' => 'dyn_link_url',
								'class' => 'form-control',
								'placeholder' => get_msg('dyn_link_url_label'),
								'value' => set_value( 'dyn_link_url', show_data( @$backend->dyn_link_url ), false ),
								'readonly' => 'true'
							));
						?>
					</div>

					<div class="form-group">
						<label><?php echo get_msg('dyn_link_package_name_label')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('dyn_link_package_name_label')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>
						<?php 
							echo form_input( array(
								'type' => 'text',
								'name' => 'dyn_link_package_name',
								'id' => 'dyn_link_package_name',
								'class' => 'form-control',
								'placeholder' => get_msg('dyn_link_package_name_label'),
								'value' => set_value( 'dyn_link_package_name', show_data( @$backend->dyn_link_package_name ), false ),
								'readonly' => 'true'
							));
						?>
					</div>
	            </div>

	            <div class="col-md-6">
	            	<div class="form-group">
						<label><?php echo get_msg('dyn_link_domain_label')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('dyn_link_domain_label')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>
						<?php 
							echo form_input( array(
								'type' => 'text',
								'name' => 'dyn_link_domain',
								'id' => 'dyn_link_domain',
								'class' => 'form-control',
								'placeholder' => get_msg('dyn_link_domain_label'),
								'value' => set_value( 'dyn_link_domain', show_data( @$backend->dyn_link_domain ), false ),
								'readonly' => 'true'
							));
						?>
					</div>

					<div class="form-group">
						<label><?php echo get_msg('dyn_link_deep_url_label')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('dyn_link_deep_url_label')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>
						<?php 
							echo form_input( array(
								'type' => 'text',
								'name' => 'dyn_link_deep_url',
								'id' => 'dyn_link_deep_url',
								'class' => 'form-control',
								'placeholder' => get_msg('dyn_link_deep_url_label'),
								'value' => set_value( 'dyn_link_deep_url', show_data( @$backend->dyn_link_deep_url ), false ),
								'readonly' => 'true'
							));
						?>
					</div>

					<div class="form-group">
						<label><?php echo get_msg('dyn_link_package_name_ios')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('dyn_link_package_name_ios')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>
						<?php 
							echo form_input( array(
								'type' => 'text',
								'name' => 'ios_boundle_id',
								'id' => 'ios_boundle_id',
								'class' => 'form-control',
								'placeholder' => get_msg('dyn_link_package_name_ios'),
								'value' => set_value( 'ios_boundle_id', show_data( @$backend->ios_boundle_id ), false ),
								'readonly' => 'true'
							));
						?>
					</div>

					<div class="form-group">
						<label><?php echo get_msg('dyn_link_ios_appstore_id')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('dyn_link_ios_appstore_id')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>
						<?php 
							echo form_input( array(
								'type' => 'text',
								'name' => 'ios_appstore_id',
								'id' => 'ios_appstore_id',
								'class' => 'form-control',
								'placeholder' => get_msg('dyn_link_ios_appstore_id'),
								'value' => set_value( 'ios_appstore_id', show_data( @$backend->ios_appstore_id ), false ),
								'readonly' => 'true'
							));
						?>
					</div>
	            </div>

	            <p> <i> <b> <?php echo get_msg('deeplink_note_label'); ?> </b> <a href="<?php echo site_url('admin/backend_configs'); ?>" target="_blank">  <?php echo get_msg('go_to_be_setting_label'); ?></a> </i></p>
	            <br>

	        	<button type="submit" name="save" class="btn btn-primary">
					<?php echo get_msg('btn_deeplink_generator')?>
				</button>
		        </div>
		    </div>
		</div>
	</div>
</section>

<?php echo form_close(); ?>