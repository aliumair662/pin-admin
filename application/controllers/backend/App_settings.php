<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Likes Controller
 */

class App_settings extends BE_Controller {

		/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'APPSETTINGS' );
		///start allow module check 
		$conds_mod['module_name'] = $this->router->fetch_class();
		$module_id = $this->Module->get_one_by($conds_mod)->module_id;
		
		$logged_in_user = $this->ps_auth->get_user_info();

		$user_id = $logged_in_user->user_id;
		if(empty($this->User->has_permission( $module_id,$user_id )) && $logged_in_user->user_is_sys_admin!=1){
			return redirect( site_url('/admin') );
		}
		///end check
	}

	/**
	 * Load About Entry Form
	 */

	function index( $id = "app1" ) {

		if ( $this->is_POST()) {
		// if the method is post

			// server side validation
			//if ( $this->is_valid_input()) {

				// save user info
				$this->save( $id );
			//}
		}

		$this->data['action_title'] = get_msg('app_setting');
		
		//Get About Object
		$this->data['app'] = $this->App_setting->get_one( $id );

		$this->load_form($this->data);

	}

	/**
	 * Update the existing one
	 */
	function edit( $id = "app1") {


		// load user
		$this->data['app'] = $this->App_setting->get_one( $id );

		// call the parent edit logic
		parent::edit( $id );
	}

	/**
	 * Saving Logic
	 * 1) save about data
	 * 2) check transaction status
	 *
	 * @param      boolean  $id  The about identifier
	 */
	function save( $id = false ) {

		
		// prepare data for save
		$data = array();

		// id
		if ( $this->has_data( 'id' )) {
			$data['id'] = $this->get_data( 'id' );
		}

		// lat
		if ( $this->has_data( 'lat' )) {
			$data['lat'] = $this->get_data( 'lat' );
		}

		// lng
		if ( $this->has_data( 'lng' )) {
			$data['lng'] = $this->get_data( 'lng' );
		}

		// if is_approval_enabled is checked,
		if ( $this->has_data( 'is_approval_enabled' )) {
			$data['is_approval_enabled'] = 1;
		} else {
			$data['is_approval_enabled'] = 0;
		}

		// if is_sub_location is checked,
		if ( $this->has_data( 'is_sub_location' )) {
			$data['is_sub_location'] = 1;
		} else {
			$data['is_sub_location'] = 0;
		}
		
		// if is_paid_app is checked,
		if ( $this->has_data( 'is_paid_app' )) {
			$data['is_paid_app'] = 1;
		} else {
			$data['is_paid_app'] = 0;
			
		}

		// if is_thumb2x_3x_generate is checked,
		if ( $this->has_data( 'is_thumb2x_3x_generate' )) {
			$data['is_thumb2x_3x_generate'] = 1;
		} else {
			$data['is_thumb2x_3x_generate'] = 0;
		}

		// if is_block_user is checked,
		if ( $this->has_data( 'is_block_user' )) {
			$data['is_block_user'] = 1;
		} else {
			$data['is_block_user'] = 0;
		}

		// if is_propertyby_subscription is checked,
		if ( $this->has_data( 'is_propertyby_subscription' )) {
			$data['is_propertyby_subscription'] = 1;
		} else {
			$data['is_propertyby_subscription'] = 0;
		}
		
		// max_img_upload_of_item
		if ( $this->has_data( 'max_img_upload_of_item' )) {
			$data['max_img_upload_of_item'] = $this->get_data( 'max_img_upload_of_item' );
		}

		// ad_type
		if ( $this->has_data( 'ad_type' )) {
			$data['ad_type'] = $this->get_data( 'ad_type' );
		}

		// promo_cell_interval_no
		if ( $this->has_data( 'promo_cell_interval_no' )) {
			$data['promo_cell_interval_no'] = $this->get_data( 'promo_cell_interval_no' );
		}
		
		//print_r($data);die;
		if ( ! $this->App_setting->save( $data, $id )) {
		// if there is an error in inserting user data,	

			// set error message
			$this->data['error'] = get_msg( 'err_model' );
			
			return;
		} else{
			// if app is limited, show package module
			if($data['is_paid_app'] == '1'){
				$is_show_menu = array(
					'is_show_on_menu'=>1
				);
				$this->Module->save($is_show_menu,53);
			}

			// if app is free, hide package module
			if($data['is_paid_app'] == '0'){
				$is_show_menu = array(
					'is_show_on_menu'=>0
				);
				$this->Module->save($is_show_menu,53);
			}
		} 

		// commit the transaction
		if ( ! $this->check_trans()) {
        	
			// set flash error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} else {

			if ( $id ) {
			// if user id is not false, show success_add message
				
				$this->set_flash_msg( 'success', get_msg( 'success_app_setting_edit' ));
			} else {
			// if user id is false, show success_edit message

				$this->set_flash_msg( 'success', get_msg( 'success_app_setting_add' ));
			}
		}

		redirect( site_url('/admin/app_settings') );

	}
}