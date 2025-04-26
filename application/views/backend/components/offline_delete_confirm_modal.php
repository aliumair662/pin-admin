<div class="modal fade"  id="myModal">

	<div class="modal-dialog">
		
		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title"><?php echo $title; ?></h4>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
			</div>
			<!-- <?php
    		$attributes = array('id' => 'crediental-form','method' => 'POST');
    		//echo form_open(site_url('crediental'), $attributes);
    		?> -->

			<div class="modal-body">
				<label>Email :</label>
				<input type="hidden" name="orgemail" id="orgemail" value="admin@ps.com">
				<input type="email" name="email" id="email">
				<br>
				<label>Password :</label>
				<input type="hidden" name="orgpw" id="orgpw" value="admin">
				<input type="password" name="password" id="password">
			</div>

			<div class="modal-footer">

				<a class="btn btn-sm btn-primary btn-no" href='<?php echo $module_site_url ."/offline_delete/";?>'>
					<?php echo get_msg('btn_yes'); ?>
				</a>
				

				<a href='#' class="btn btn-sm btn-primary" data-dismiss="modal">
				<?php echo get_msg( 'btn_cancel' )?></a>
			</div>


		</div><!-- /.modal-content -->

	</div><!-- /.modal-dialog -->

</div><!-- /.modal -->
