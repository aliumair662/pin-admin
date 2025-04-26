<?php
  $attributes = array( 'id' => 'item-form', 'enctype' => 'multipart/form-data');
  echo form_open( '', $attributes);
?>

<section class="content animated fadeInRight">
<div class="col-md-6">
    <div class="card card-info">
    <div class="card-header">
      <h3 class="card-title"><?php echo get_msg('offline_paid_prd_info')?></h3>
    </div>

    <div class="card-body">
      <div class="form-group">
            <label>
              <?php echo get_msg('itm_title_label')?>:
              <input type="hidden" id="product_id" name="product_id" value="<?php echo $this->Item->get_one($item_id)->id; ?>">
              <?php 
                $name = $this->Item->get_one($item_id)->title;
                echo $name;
              ?>
            </label>
          </div>
      <div class="form-group">
        <label>
          <?php echo get_msg('date')?>
        </label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="fa fa-calendar"></i>
            </span>
          </div>

          <?php 

            //for start date


            $var_start_date = explode(' ',$start_date,2);
            $temp_start_date = $var_start_date[0];

            $new_start_date = date("m-d-Y", strtotime($temp_start_date));

            $new_start_date1 = explode('-',$new_start_date,3);

            $start_date = $new_start_date1[0] . "/" . $new_start_date1[1] . "/" . $new_start_date1[2];

            // for end date

            $var_end_date = explode(' ',$end_date,2);
            $temp_end_date = $var_end_date[0];

            $new_end_date = date("m-d-Y", strtotime($temp_end_date));

            $new_end_date1 = explode('-',$new_end_date,3);

            $end_date = $new_end_date1[0] . "/" . $new_end_date1[1] . "/" . $new_end_date1[2];
            //print_r($end_date);die;

           ?>

          <input type="text" size="23" name="date" id="date" readonly="readonly" value="<?php echo $start_date  . " - " . $end_date; ?> " />


        </div>
      </div>
        <div class="form-group" style="background-color: #edbbbb; padding: 20px;">
          <label>
            <strong><?php echo get_msg('select_status')?></strong>
          </label>

          <?php if ($item->is_paid == 0) { ?>
            <select id="is_paid" name="is_paid" class="form-control">
              <option value="0" selected><?php echo get_msg('waiting_approval_label'); ?></option>
              <option value="1"><?php echo get_msg('approved_label'); ?></option>
              <option value="2"><?php echo get_msg('reject_label'); ?></option>
            </select>
          <?php } elseif ($item->is_paid == 1) { ?>
            <select id="is_paid" name="is_paid" class="form-control">
              <option value="0"><?php echo get_msg('waiting_approval_label'); ?></option>
              <option value="1" selected><?php echo get_msg('approved_label'); ?></option>
              <option value="2"><?php echo get_msg('reject_label'); ?></option>
            </select>
          <?php } elseif($item->is_paid == 2) { ?>
            <select id="is_paid" name="is_paid" class="form-control">
              <option value="0"><?php echo get_msg('waiting_approval_label'); ?></option>
              <option value="1"><?php echo get_msg('approved_label'); ?></option>
              <option value="2" selected><?php echo get_msg('reject_label'); ?></option>
            </select> 
          <?php } ?>  
        </div>
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