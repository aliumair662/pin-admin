<div class="table-responsive animated fadeInRight">
	<table class="table m-0 table-striped">
		<tr>
			<th><?php echo get_msg('no'); ?></th>
			<th><?php echo get_msg('item_name'); ?></th>
			<th><?php echo get_msg('user_name'); ?></th>
			<th><?php echo get_msg('status_label'); ?></th>
			
			<?php if ( $this->ps_auth->has_access( EDIT )): ?>
				
				<th><span class="th-title"><?php echo get_msg('btn_edit')?></span></th>
			
			<?php endif; ?>

			<?php if ( $this->ps_auth->has_access( DEL )): ?>
				
				<th><span class="th-title"><?php echo get_msg('btn_delete')?></span></th>
			
			<?php endif; ?>
			
		</tr>
		
	
	<?php $count = $this->uri->segment(4) or $count = 0; ?>

	<?php if ( !empty( $reports ) && count( $reports->result()) > 0 ): ?>

		<?php foreach($reports->result() as $report): ?>
			
			<tr>
				<td><?php echo ++$count;?></td>
				<td><?php echo $this->Item->get_one( $report->item_id )->title; ?></td>
				<td><?php echo $this->User->get_one( $report->reported_user_id )->user_name; ?></td>
				<td style="width: 10%;">
                    <?php
					
                    if ( $this->Reported_item_status->get_one($report->reported_status)->id == 1) { ?>
                        <span class="badge badge-success">
				                  <?php echo get_msg("open"); ?>
				                </span>
                    <?php } elseif ( $this->Reported_item_status->get_one($report->reported_status)->id == 2) { ?>
                        <span class="badge badge-info">
				                  <?php echo get_msg("in_progress"); ?>
				                </span>
                    <?php } elseif ( $this->Reported_item_status->get_one($report->reported_status)->id == 3) { ?>
                        <span class="badge badge-warning">
				                  <?php echo get_msg("close"); ?>
				                </span>
                    <?php } ?>
                </td>
				<?php if ( $this->ps_auth->has_access( EDIT )): ?>
			
					<td>
						<a href='<?php echo $module_site_url .'/edit/'. $report->id; ?>'>
							<i style='font-size: 18px;' class='fa fa-pencil-square-o'></i>
						</a>
					</td>
				
				<?php endif; ?>

				<?php if ( $this->ps_auth->has_access( DEL )): ?>
					
					<td>
						<a herf='#' class='btn-delete' data-toggle="modal" data-target="#myModal" id="<?php echo "$report->id";?>">
							<i class='fa fa-trash-o'></i>
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

