<div class="table-responsive animated fadeInRight">
	<table class="table m-0 table-striped">
		<tr>
			<th><?php echo get_msg('no'); ?></th>
			<th><?php echo get_msg('img_name'); ?></th>
			
			<?php if ( $this->ps_auth->has_access( EDIT )): ?>
				
				<th><span class="th-title"><?php echo get_msg('thumbnail_generators_lng')?></span></th>
			
			<?php endif; ?>
			
			

		</tr>
		
	
	<?php $count = $this->uri->segment(4) or $count = 0; ?>

	<?php if ( !empty( $images ) && count( $images->result()) > 0 ): ?>

		<?php foreach($images->result() as $image): ?>
			
			<tr>
				<?php 
					$img_path_org = $image->img_path;
					$img_path_org = explode('.', $img_path_org);
					$img_path1 = $img_path_org[0];
					$img_path2 = $img_path_org[1];

					if (strlen($img_path1) > 20) {
						
						$img_path1 = substr($img_path1, 0, 20);
	                
	                    $img_path = $img_path1 . '.' . $img_path2;
					} else {

						$img_path = $image->img_path;
					}

				?>

				<td><?php echo ++$count;?></td>
				
				<td ><?php echo $img_path; ?></td>

				
				<?php if ( $this->ps_auth->has_access( EDIT )): ?>
			
					<td>
						<a href='<?php echo $module_site_url .'/createThumbs/'. $image->img_id; ?>'>
							<button type="submit" name="save" class="btn btn-sm btn-primary">
								<?php echo get_msg('btn_create_thumbnail')?>
							</button>
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
