<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Deeplink_generators Controller
 */

class Deeplink_generators extends BE_Controller {
		/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'Deeplink Generators' );
		///start allow module check 
		$conds_mod['module_name'] = $this->router->fetch_class();
		
		$module_id = $this->Module->get_one_by($conds_mod)->module_id;
		
		$logged_in_user = $this->ps_auth->get_user_info();

		$user_id = $logged_in_user->user_id;
		if(empty($this->User->has_permission( $module_id,$user_id )) && $logged_in_user->user_is_sys_admin!=1){
			return redirect( site_url('/admin/') );
		}
		///end check
	}

	/**
	 * Load About Entry Form
	 */

	function index( ) {

		if ( $this->is_POST()) {
		// if the method is post

			// save user info
			$this->generateDeeplink( );
			
		}

		//Get Paid_config Object
		$this->data['pconfig'] = $this->Paid_config->get_one( $id );

		$this->load_form($this->data);

	}

	function generateDeeplink() {

		$item_data = $this->Item->get_all()->result();
		
		for ($i=0; $i <count($item_data) ; $i++) { 
			$title = $item_data[$i]->title;
			$description = $item_data[$i]->description;
			$id = $item_data[$i]->id;

			$conds_img = array( 'img_type' => 'item', 'img_parent_id' => $id );
        	$images = $this->Image->get_all_by( $conds_img )->result();
			$img = $this->ps_image->upload_url . $images[0]->img_path;

			$deep_link = deep_linking_shorten_url($description,$title,$img,$id);
			//print_r($deep_link);die;
			$itm_data = array(
				'dynamic_link' => $deep_link
			);

			$this->Item->save($itm_data,$id);
		}

		$this->set_flash_msg( 'success', get_msg( 'success_deeplink_generate' ));
	}

}