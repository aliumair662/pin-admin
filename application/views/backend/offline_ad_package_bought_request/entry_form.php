<?php
	$attributes = array( 'id' => 'item-form', 'enctype' => 'multipart/form-data');
	echo form_open( '', $attributes);
?>

<section class="content animated fadeInRight">
<div class="">
    <div class="card card-info">
    <div class="card-header">
      <h3 class="card-title"><?php echo get_msg('offline_pkg_bought_info')?></h3>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('package_name')?>
            </label>

            <?php echo form_input( array(
              'name' => 'title',
              'value' => set_value( 'title', show_data( @$package->title), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('package_name'),
              'id' => 'title',
              'readonly' => 'true'
            )); ?>
          </div>

          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('post_count')?>
            </label>

            <?php echo form_input( array(
              'name' => 'post_count',
              'value' => set_value( 'post_count', show_data( @$package->post_count), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('post_count'),
              'id' => 'post_count',
              'readonly' => 'true'
            )); ?>

					</div>

          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('bought_user')?>
            </label>

            <?php echo form_input( array(
              'name' => 'user_id',
              'value' => set_value( 'user_id', show_data( @$user->user_name), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('bought_user'),
              'id' => 'user_id',
              'readonly' => 'true'
            )); ?>

					</div>


        </div><!-- ./col-md-6 -->
        
        <div class="col-md-6">
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('price'); ?>
            </label>

            <?php echo form_input( array(
              'name' => 'price',
              'value' => set_value( 'price', show_data( @$package->price), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('price'),
              'id' => 'price',
              'readonly' => 'true'
            )); ?>
	        </div>

          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('currency_label')?>
            </label>

            <?php
              $options=array();
              $conds['status'] = 1;
              $options[0]=get_msg('pkg_select_currency');
              $currency = $this->Currency->get_all_by($conds);
              foreach($currency->result() as $curr) {
                  $options[$curr->id]=$curr->currency_short_form;
              }

              echo form_dropdown(
                'currency_id',
                $options,
                set_value( 'currency_id', show_data( @$package->currency_id), false ),
                'class="form-control form-control-sm mr-3" disabled="disabled" id="currency_id"',
                
              );
            ?>
          </div>

        </div><!-- ./col-md-6 -->

      </div> <!-- ./row -->



        <div class="form-group mt-5" style="background-color: #edbbbb; padding: 20px;">
          <label>
            <strong><?php echo get_msg('select_status')?></strong>
          </label>

          <?php if ($trans->status == 0) { ?>
            <select id="status" name="status" class="form-control">
              <option value="0" selected><?php echo get_msg('waiting_approval_label') ?></option>
              <option value="1"><?php echo get_msg('approved_label') ?></option>
              <option value="2"><?php echo get_msg('reject_label') ?></option>
            </select>
          <?php } elseif ($trans->status == 1) { ?>
            <select id="status" name="status" class="form-control">
              <option value="0"><?php echo get_msg('waiting_approval_label') ?></option>
              <option value="1" selected><?php echo get_msg('approved_label') ?></option>
              <option value="2"><?php echo get_msg('reject_label') ?></option>
            </select>
          <?php } else { ?>
            <select id="status" name="status" class="form-control">
              <option value="0"><?php echo get_msg('waiting_approval_label') ?></option>
              <option value="1"><?php echo get_msg('approved_label') ?></option>
              <option value="2" selected><?php echo get_msg('reject_label') ?></option>
            </select> 
          <?php } ?>  
        </div>
      </div>
   
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