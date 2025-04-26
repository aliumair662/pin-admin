<div class="table-responsive animated fadeInRight">
	<table class="table m-0 table-striped">
		<tr>
			<th><?php echo get_msg('no'); ?></th>
			<th><?php echo get_msg('package_name'); ?></th>
			<th><?php echo get_msg('bought_user'); ?></th>
			<th><?php echo get_msg('price'); ?></th>
			<th><?php echo get_msg('post_count'); ?></th>
			<th><?php echo get_msg('payment_status'); ?></th>
			
			<?php if ( $this->ps_auth->has_access( EDIT )): ?>
				
				<th><span class="th-title"><?php echo get_msg('btn_edit')?></span></th>
			
			<?php endif; ?>
			
			<?php if ( $this->ps_auth->has_access( DEL )): ?>
				
				<th><span class="th-title"><?php echo get_msg('btn_delete')?></span></th>
			
			<?php endif; ?>
			
		</tr>
		
	
	<?php $count = $this->uri->segment(4) or $count = 0; ?>

	<?php if ( !empty( $transactions ) && count( $transactions->result()) > 0 ): ?>

		<?php foreach($transactions->result() as $tran): ?>
			<?php $pkg = $this->Package->get_one($tran->package_id); ?>
			<tr>
				<td><?php echo ++$count;?></td>
				<td><?php echo $pkg->title?></td>
				<td><?php echo $this->User->get_one($tran->user_id)->user_name; ?></td>
				<td><?php echo $pkg->price; ?> <?php echo $this->Currency->get_one($pkg->currency_id)->currency_symbol; ?></td>
				<td><?php echo $pkg->post_count; ?></td>
				<td>
					<?php if ($tran->status == 0) { ?>
						<button class="btn btn-sm btn-warning">
                			<?php echo get_msg('waiting_for_payment_label'); ?></button>
					<?php } elseif ($tran->status == 1){ ?>
						<button class="btn btn-sm btn-success">
                			<?php echo get_msg('paid_label'); ?></button>
                	<?php } else{ ?>
                		<button class="btn btn-sm btn-danger">
                			<?php echo get_msg('paid_reject_label'); ?></button>
					<?php } ?>
				</td>


				<?php if ($tran->status == 0 || $tran->status == 2) : ?>
				
					<?php if ( $this->ps_auth->has_access( EDIT )): ?>
			
						<td>
							<a href='<?php echo $module_site_url .'/edit/'. $tran->id; ?>'>
								<i class='fa fa-pencil-square-o'></i>
							</a>
						</td>
					
					<?php endif; ?>
					
					<?php	if ( $this->ps_auth->has_access( DEL )): ?>
					
						<td>
							<a herf='#' class='btn-delete' data-toggle="modal" data-target="#myModal" id="<?php echo "$tran->id";?>">
								<i class='fa fa-trash-o'></i>
							</a>
						</td>
			
					<?php endif; ?>
				<?php endif; ?>	
				
			</tr>

		<?php endforeach; ?>

	<?php else: ?>
			
		<?php $this->load->view( $template_path .'/partials/no_data' ); ?>

	<?php endif; ?>

</table>
</div>

