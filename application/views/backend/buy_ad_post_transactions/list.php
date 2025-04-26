<div class="table-responsive animated fadeInRight">
  <table class="table m-0 table-striped">
    <tr>
      <th><?php echo get_msg('no'); ?></th>
      <th><?php echo get_msg('user_name'); ?></th>
      <th><?php echo get_msg('package_name'); ?></th>
      <th><?php echo get_msg('amount_label'); ?></th>
      <th><?php echo get_msg('payment_method_label'); ?></th>
      <th><?php echo get_msg('date'); ?></th>
      <th><?php echo get_msg('lbl_view'); ?></th>
    </tr>
    
  
  <?php $count = $this->uri->segment(4) or $count = 0; ?>

  <?php if ( !empty( $transactions) && count( $transactions->result()) > 0 ): ?>

      <?php foreach($transactions->result() as $trans): ?>
        
        <tr>
          <td><?php echo ++$count;?></td>
          <td><?php echo $this->User->get_one( $trans->user_id )->user_name;?></td>
          <td><?php echo $this->Package->get_one( $trans->package_id )->title;?></td>
          <td><?php echo $trans->price.$this->Currency->get_one($this->Package->get_one($trans->package_id)->currency_id)->currency_symbol; ?></td>
          <td><?php echo $trans->payment_method; ?></td>
          <td><?php echo $trans->added_date; ?></td>
          <?php if ( $this->ps_auth->has_access( EDIT )): ?>
      
            <td>
              <a href='<?php echo $module_site_url .'/edit/'. $trans->id; ?>'>
                <i class='fa fa-eye'></i>
              </a>
            </td>
          
          <?php endif; ?>
          
        </tr>

      <?php endforeach; ?>

  <?php else: ?>
      
    <?php $this->load->view( $template_path .'/partials/no_data' ); ?>

  <?php endif; ?>

</table>
</div>