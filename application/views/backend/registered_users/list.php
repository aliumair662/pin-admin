<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
<div class="table-responsive animated fadeInRight">
	<table class="table m-0 table-striped">

		<tr>
			<th><?php echo get_msg('no')?></th>
			<th><?php echo get_msg('Profile Image')?></th>
			<th><?php echo get_msg('Swipe Hiden')?></th>
			<th><?php echo get_msg('user_name')?></th>
			<th><?php echo get_msg('user_email')?></th>
			<th><?php echo get_msg('user_phone')?></th>
			<th><?php echo get_msg('role')?></th>

			<?php if ( $this->ps_auth->has_access( EDIT )): ?>
				
				<th><?php echo get_msg('btn_edit')?></th>

			<?php endif;?>

			<?php if ( $this->ps_auth->has_access( BAN )): ?>
				
				<th><?php echo get_msg('user_ban')?></th>

			<?php endif;?>

			<?php if ( $this->ps_auth->has_access( DEL )): ?>
				
				<th><span class="th-title"><?php echo get_msg('btn_delete')?></span></th>
			
			<?php endif; ?>

		</tr>

		<?php $count = $this->uri->segment(4) or $count = 0; ?>

		<?php if ( !empty( $users ) && count( $users->result()) > 0 ): ?>
				
			<?php foreach($users->result() as $user): ?>
				
				<tr>
					<td><?php echo ++$count;?></td>
					<td>
					  <img style="width: 50px;height: 50px; border-radius: 50%;"  src="<?php echo $this->ps_image->upload_thumbnail_url . $user->user_profile_photo; ?>" onerror="this.src='<?php echo img_url( '/thumbnail/no_image.png' ); ?>'" >
					</td>
					<td>
						<label class="switch">
						  <input type="checkbox" <?= ($user->swipe_profile_hidden == 1) ? 'checked' : ''; ?> onchange="swipe_profile_hidden('<?=$user->user_id;?>')" >
						  <span class="slider round"></span>
						</label>
					</td>
					<td><?php echo $user->user_name;?></td>
					<td><?php echo $user->user_email;?></td>
					<td><?php echo $user->user_phone;?></td>
				
					<td><?php echo "Registered User";?></td>

					<?php if ( $this->ps_auth->has_access( EDIT )): ?>
					
					<td>
						<a href='<?php echo $module_site_url .'/edit/'. $user->user_id; ?>'>
							<i class='fa fa-pencil-square-o'></i>
						</a>
					</td>
				
				
					<?php endif; ?>
					
					<?php if ( $this->ps_auth->has_access( BAN )):?>
					
						<td>
							<?php if ( @$user->is_banned == 0 ): ?>
								
								<button class="btn btn-sm btn-primary-green ban" userid='<?php echo @$user->user_id;?>'>
									<?php echo get_msg( 'user_ban' ); ?>
								</button>
							
							<?php else: ?>
								
								<button class="btn btn-sm btn-danger unban" userid='<?php echo @$user->user_id;?>'>
									<?php echo get_msg( 'user_unban' ); ?>
								</button>
							
							<?php endif; ?>

						</td>

					<?php endif;?>

					<?php if ( $this->ps_auth->has_access( DEL )): ?>
					
					<td>
						<a herf='#' class='btn-delete' data-toggle="modal" data-target="#myModal" id="<?php echo $user->user_id;?>">
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
<script type="text/javascript">
	function swipe_profile_hidden(id){
		window.location = window.location +'?id='+id;
	}
</script>