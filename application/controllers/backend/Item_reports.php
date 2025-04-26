<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Item Itemreport Controller
 */
class Item_reports extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'ITEM report' );
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
	 * List down the registered users
	 */
	function index() {
		
		// no publish filter
		$conds['status'] = 1;
		
		// get rows count
		$this->data['rows_count'] = $this->Itemreport->count_all_by( $conds );
		
		// get Item reports
		$this->data['reports'] = $this->Itemreport->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );
		// load index logic
		parent::index();
	}


	/**
	 * Searches for the first match.
	 */
	function search() {
		

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'report_search' );
		
		// condition with date search 
		// if($this->input->post('date') != "") {
		// 	$conds['date'] = $this->input->post('date');
		// }


		if($this->input->post('submit') != NULL ){
			// condition with search term
			if($this->input->post('searchterm') != "") {
				$conds['searchterm'] = $this->input->post('searchterm');
				$this->data['searchterm'] = $this->input->post('searchterm');
				$this->session->set_userdata(array("searchterm" => $this->input->post('searchterm')));
			} else {
				$this->session->set_userdata(array("searchterm" => NULL));
			}
			
			if($this->input->post('reported_status') != ""  && $this->input->post('reported_status') != '0') {
				$conds['reported_status'] = $this->input->post('reported_status');
				$this->data['reported_status'] = $this->input->post('reported_status');
	
				$this->session->set_userdata(array("reported_status" => $this->input->post('reported_status')));
			} else {
				$this->session->set_userdata(array("reported_status" => NULL ));
			}

			if($this->input->post('date') != "") {
				$conds['date'] = $this->input->post('date');
				$this->data['date'] = $this->input->post('date');
				$this->session->set_userdata(array("date" => $this->input->post('date')));
			} else {
				$this->session->set_userdata(array("date" => NULL ));
			}
		} else {
			//read from session value
			if($this->session->userdata('searchterm') != NULL){
				$conds['searchterm'] = $this->session->userdata('searchterm');
				$this->data['searchterm'] = $this->session->userdata('searchterm');
			}

			if($this->session->userdata('reported_status') != NULL){
				$conds['reported_status'] = $this->session->userdata('reported_status');
				$this->data['reported_status'] = $this->session->userdata('reported_status');
			}

			if($this->session->userdata('date') != NULL){
				$conds['date'] = $this->session->userdata('date');
				$this->data['date'] = $this->session->userdata('date');
			}
		}
		// no publish filter
		$conds['status'] = 1;
		$conds['order_by'] = 1;
		$conds['order_by_field'] = "bs_items_report.added_date";
		$conds['order_by_report'] = "desc";


		// pagination
		$this->data['rows_count'] = $this->Itemreport->count_all_by( $conds );

		// search data
		$this->data['reports'] = $this->Itemreport->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );
		
		// load add list
		parent::search();
	}

	/**
	 * Create new one
	 */
	function add() {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'report_add' );

		// call the core add logic
		parent::add();
	}

	/**
	 * Update the existing one
	 */
	function edit( $id ) {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'report_edit' );

		// load user
		$report = $this->Itemreport->get_one( $id );

		$this->data['item_id'] = $report->item_id;

		$item_id = $this->data['item_id'];

		$this->data['report'] = $this->Item->get_one( $item_id );
		//print_r($this->data['item']);die;

		// call the parent edit logic
		parent::edit( $id );
	}

	/**
	 * Saving Logic
	 * 1) upload image
	 * 2) save Itemreport
	 * 3) save image
	 * 4) check transaction status
	 *
	 * @param      boolean  $id  The user identifier
	 */
	 
	function save( $id = false ) {
		//echo "2";die;
		
			$logged_in_user = $this->ps_auth->get_user_info();

			$report = $this->Itemreport->get_one( $id );

			$item_id = $report->item_id;

			$user_id = $report->reported_user_id;
			//print_r($user_id);die;

			$conds['item_id'] = $item_id;

			$conds['user_id'] = $user_id;


			// if( isset($item_id) && isset($user_id) ){
			// 	$this->Itemreport->delete_by( $conds );
			// }

			// $data['status'] = 2;

			//print_r($data);die;
			//save item
			// if ( ! $this->Item->save( $data, $item_id )) {
			// // if there is an error in inserting user data,	

			// 	// rollback the transaction
			// 	$this->db->trans_rollback();

			// 	// set error message
			// 	$this->data['error'] = get_msg( 'err_model' );
				
			// 	return;
			// }

			// prepare reported_status
			if ( $this->has_data( 'reported_status' )) {
				$report_data['reported_status'] = $this->get_data( 'reported_status' );
			}

			$report_data['updated_date'] = date("Y-m-d H:i:s");

			//save reported_item
			if ( ! $this->Itemreport->save( $report_data, $report->id )) {
				// if there is an error in inserting user data,	
	
				// rollback the transaction
				$this->db->trans_rollback();

				// set error message
				$this->data['error'] = get_msg( 'err_model' );
				
				return;
			}

			/** 
			 * Check Transactions 
			 */

			// commit the transaction
			if ( ! $this->check_trans()) {
	        	
				// set flash error message
				$this->set_flash_msg( 'error', get_msg( 'err_model' ));
			} else {

				if ( $id ) {
				// if user id is not false, show success_add message
					
					$this->set_flash_msg( 'success', get_msg( 'success_prd_edit' ));
				} else {
				// if user id is false, show success_edit message

					$this->set_flash_msg( 'success', get_msg( 'success_prd_add' ));
				}
			}


		// Item Id Checking 
		if ( $this->has_data( 'gallery' )) {
		// if there is gallery, redirecti to gallery
			redirect( $this->module_site_url( 'gallery/' .$id ));
		}
		else {
		// redirect to list view
			redirect( $this->module_site_url() );
		}
	}

	
	
	/**
	 * Determines if valid input.
	 *
	 * @return     boolean  True if valid input, False otherwise.
	 */
	function is_valid_input( $id = 0 ) {

		return true;
	}

	/**
	 * Determines if valid name.
	 *
	 * @param      <report>   $name  The  name
	 * @param      integer  $id     The  identifier
	 *
	 * @return     boolean  True if valid name, False otherwise.
	 */
	function is_valid_name( $name, $id = 0 )
	{		
		return true;
	}

	/**
	 * Delete the record
	 * 1) delete Item
	 * 2) delete image from folder and table
	 * 3) check transactions
	 */
	function delete( $id ) 
	{
		// start the transaction
		$this->db->trans_start();

		// check access
		$this->check_access( DEL );

		// enable trigger to delete all products related data
	    $enable_trigger = true;

	    if ( ! $this->ps_delete->delete_report( $id, $enable_trigger )) {

			// set error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));

			// rollback
			$this->trans_rollback();

			// redirect to list view
			redirect( $this->module_site_url());
		}
		/**
		 * Check Transcation Status
		 */
		if ( !$this->check_trans()) {

			$this->set_flash_msg( 'error', get_msg( 'err_model' ));	
		} else {
        	
			$this->set_flash_msg( 'success', get_msg( 'success_prd_delete' ));
		}
		
		redirect( $this->module_site_url());
	}

	
}