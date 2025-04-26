<div class="table-responsive animated fadeInRight">
	<table class="table m-0 table-striped">
		<tr>
			<th><?php echo get_msg('no'); ?></th>
			<th><?php echo get_msg('user_name'); ?></th>
			<th><?php echo get_msg('overall_rating'); ?></th>
			<th><?php echo get_msg('added_date'); ?></th>
			<th><?php echo get_msg('verify_agent'); ?></th>
			<th><?php echo get_msg('view'); ?></th>
		</tr>
		
		<?php $count = $this->uri->segment(4) or $count = 0; ?>

		<?php if ( !empty( $ratings ) && count( $ratings->result()) > 0 ): ?>

			<?php foreach($ratings->result() as $rate): ?>
				<tr>
					<td><?php echo ++$count;?></td>
					<td><?php echo $rate->user_name; ?></td>
					<td>
					<?php 
						$i = 1;
						while($i<=5){
							if($rate->overall_rating>=1)
								echo '<i class="fa fa-star"></i>';
							else if($rate->overall_rating == 0.5)
								echo '<i class="fa fa-star-half-o"></i>';
							else
								echo '<i class="fa fa-star-o"></i>';
							$rate->overall_rating -= 1;
							$i += 1;
						}
					?>
					</td>
					<td><?php echo $rate->added_date; ?></td>					
					<td><?php echo $rate->apply_to == 1? "Yes" : "No" ;?></td>	
									
					<?php if ( $this->ps_auth->has_access( EDIT )): ?>
					<td>
						<a href='<?php echo $module_site_url .'/edit/'. $rate->user_id; ?>'>
							<i style='font-size: 18px;' class='fa fa-eye'></i>
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