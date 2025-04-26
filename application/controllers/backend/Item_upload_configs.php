<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Likes Controller
 */

class Item_upload_configs extends BE_Controller {

		/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'ITEM_UPLOAD_CONFIGS' );
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

	function index( $id = "1" ) {

		if ( $this->is_POST()) {
		// if the method is post

			// server side validation
			//if ( $this->is_valid_input()) {

				// save user info
				$this->save( $id );
			//}
		}
		
		//Get About Object
		$this->data['item_config'] = $this->Item_upload_config->get_one( $id );

		$this->load_form($this->data);

	}

	/**
	 * Update the existing one
	 */
	function edit( $id = "1") {


		// load user
		$this->data['item_config'] = $this->Item_upload_config->get_one( $id );

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

		// if item_price_type_id is checked,
		if ( $this->has_data( 'item_price_type_id' )) {
			$data['item_price_type_id'] = 1;
		}else{
			$data['item_price_type_id'] = 0;
		}

		// if discount_rate_by_percentage is checked,
		if ( $this->has_data( 'discount_rate_by_percentage' )) {
			$data['discount_rate_by_percentage'] = 1;
		}else{
			$data['discount_rate_by_percentage'] = 0;
		}

		// if condition_of_item_id is checked,
		if ( $this->has_data( 'condition_of_item_id' )) {
			$data['condition_of_item_id'] = 1;
		}else{
			$data['condition_of_item_id'] = 0;
		}

		// if highlight_info is checked,
		if ( $this->has_data( 'highlight_info' )) {
			$data['highlight_info'] = 1;
		}else{
			$data['highlight_info'] = 0;
		}

		// if video is checked,
		if ( $this->has_data( 'video' )) {
			$data['video'] = 1;
		}else{
			$data['video'] = 0;
		}

		// if video_icon is checked,
		if ( $this->has_data( 'video_icon' )) {
			$data['video_icon'] = 1;
		}else{
			$data['video_icon'] = 0;
		}

		// if price_unit is checked,
		if ( $this->has_data( 'price_unit' )) {
			$data['price_unit'] = 1;
		}else{
			$data['price_unit'] = 0;
		}

        // if price_note is checked,
		if ( $this->has_data( 'price_note' )) {
			$data['price_note'] = 1;
		}else{
			$data['price_note'] = 0;
		}

		// if address is checked,
		if ( $this->has_data( 'address' )) {
			$data['address'] = 1;
		}else{
			$data['address'] = 0;
		}

        // if is_negotiable is checked,
		if ( $this->has_data( 'is_negotiable' )) {
			$data['is_negotiable'] = 1;
		}else{
			$data['is_negotiable'] = 0;
		}

        // if amenities is checked,
		if ( $this->has_data( 'amenities' )) {
			$data['amenities'] = 1;
		}else{
			$data['amenities'] = 0;
		}

        // if floor_no is checked,
		if ( $this->has_data( 'floor_no' )) {
			$data['floor_no'] = 1;
		}else{
			$data['floor_no'] = 0;
		}

        // if configuration is checked,
		if ( $this->has_data( 'configuration' )) {
			$data['configuration'] = 1;
		}else{
			$data['configuration'] = 0;
		}

        // if area is checked,
		if ( $this->has_data( 'area' )) {
			$data['area'] = 1;
		}else{
			$data['area'] = 0;
		}

		if ( ! $this->Item_upload_config->save( $data, $id )) {
		// if there is an error in inserting user data,	

			// set error message
			$this->data['error'] = get_msg( 'err_model' );
			
			return;
		}

		// commit the transaction
		if ( ! $this->check_trans()) {
        	
			// set flash error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} else {

			if ( $id ) {
			// if user id is not false, show success_add message
				
				$this->set_flash_msg( 'success', get_msg( 'success_item_config_edit' ));
			} else {
			// if user id is false, show success_edit message

				$this->set_flash_msg( 'success', get_msg( 'success_item_config_add' ));
			}
		}

		redirect( site_url('/admin/item_upload_configs') );
	}
}