<div class="table-responsive animated fadeInRight">
	<table class="table m-0 table-striped">

		<tr>
			<th><?php echo get_msg('no')?></th>
			<th><?php echo get_msg('user_name')?></th>
			<th><?php echo get_msg('user_email')?></th>
			<th><?php echo get_msg('user_phone')?></th>
			<th><?php echo get_msg('status_label')?></th>

			<?php if ( $this->ps_auth->has_access( EDIT )): ?>
				
				<th><?php echo get_msg('btn_edit')?></th>

			<?php endif;?>

			<?php if ( $this->ps_auth->has_access( DEL )): ?>
				
				<th><span class="th-title"><?php echo get_msg('btn_delete')?></span></th>
			
			<?php endif; ?>

		</tr>

		<?php $count = $this->uri->segment(4) or $count = 0; ?>

		<?php if ( !empty( $agents ) && count( $agents->result()) > 0 ): ?>
				
			<?php foreach($agents->result() as $agent): ?>
				
				<tr>
					<td><?php echo ++$count;?></td>
					<td><?php echo $agent->user_name;?></td>
					<td><?php echo $agent->user_email;?></td>
					<td><?php echo $agent->user_phone;?></td>
					
					<td style="width: 10%;">
						<?php 
							
							if ( $agent->application_status == 1) { ?>
				                <span class="badge badge-success">
				                  <?php echo get_msg('approved_label'); ?>
				                </span>
				            <?php } elseif ( $agent->application_status == 2) { ?>
				                <span class="badge badge-warning">
				                  <?php echo get_msg('pending'); ?>
				                </span>
				            <?php } else { ?>
				                <span class="badge badge-danger">
				                  <?php echo get_msg('reject_label'); ?>
				                </span>
			            	<?php } ?>
					</td>

					<?php if ( $this->ps_auth->has_access( EDIT )): ?>
					
					<td>
						<a href='<?php echo $module_site_url .'/edit/'. $agent->user_id; ?>'>
							<i class='fa fa-pencil-square-o'></i>
						</a>
					</td>
				
				
					<?php endif; ?>
					
					<?php if ( $this->ps_auth->has_access( DEL )): ?>
					
					<td>
						<a herf='#' class='btn-delete' data-toggle="modal" data-target="#myModal" id="<?php echo $agent->user_id;?>">
							<i style='font-size: 18px;' class='fa fa-trash-o'></i>
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