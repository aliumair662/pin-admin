<div class="table-responsive animated fadeInRight">
	<table class="table m-0 table-striped">
		<tr>
			<th><?php echo get_msg('no'); ?></th>
			<th><?php echo get_msg('item_name'); ?></th>
			<th><?php echo get_msg('property_name'); ?></th>
			<th><?php echo get_msg('payment_status'); ?></th>
			
			<?php if ( $this->ps_auth->has_access( EDIT )): ?>
				
				<th><span class="th-title"><?php echo get_msg('btn_edit')?></span></th>
			
			<?php endif; ?>
			
			<?php if ( $this->ps_auth->has_access( DEL )): ?>
				
				<th><span class="th-title"><?php echo get_msg('btn_delete')?></span></th>
			
			<?php endif; ?>
			
		</tr>
		
	
	<?php $count = $this->uri->segment(4) or $count = 0; ?>

	<?php if ( !empty( $items ) && count( $items->result()) > 0 ): ?>

		<?php foreach($items->result() as $item): ?>
			
			<tr>
				<td><?php echo ++$count;?></td>
				<td><?php echo $item->title;?></td>
				<td><?php echo $this->Property_type->get_one( $item->property_by_id )->name; ?></td>
				<td>
					<?php if ($item->is_paid == 0) { ?>
						<button class="btn btn-sm btn-warning">
                			<?php echo get_msg('waiting_for_payment_label'); ?></button>
					<?php } elseif ($item->is_paid == 1){ ?>
						<button class="btn btn-sm btn-success">
                			<?php echo get_msg('paid_label'); ?></button>
                	<?php } elseif ($item->is_paid == 2) { ?>
                		<button class="btn btn-sm btn-danger">
                			<?php echo get_msg('paid_reject_label'); ?></button>
					<?php } ?>
				</td>

				<?php if ( $this->ps_auth->has_access( EDIT )): ?>
			
					<td>
						<a href='<?php echo $module_site_url .'/edit/'. $item->id; ?>'>
							<i class='fa fa-pencil-square-o'></i>
						</a>
					</td>
				
				<?php endif; ?>
				
				<?php if ($item->is_paid == 0 || $item->is_paid == 2) : ?>
				
					<?php	if ( $this->ps_auth->has_access( DEL )): ?>
					
						<td>
							<a herf='#' class='btn-delete' data-toggle="modal" data-target="#myModal" id="<?php echo "$item->id";?>">
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

