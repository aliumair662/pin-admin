<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Packages Controller
 */
class Packages extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {
		
		
		parent::__construct( MODULE_CONTROL, 'PACKAGES' );
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
		$conds['no_publish_filter'] = 1;
		$conds['order_by'] = 1;
		$conds['order_by_field'] = "added_date";
		$conds['order_by_type'] = "desc";
		// get rows count
		$this->data['rows_count'] = $this->Package->count_all_by( $conds );
		
		// get packages
		$this->data['packages'] = $this->Package->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );
		// load index logic
		parent::index();
	}

	/**
	 * Searches for the first match.
	 */
	function search() {
		

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'pkg_search' );
		
		// condition with search term
		$conds = array( 'searchterm' => $this->searchterm_handler( $this->input->post( 'searchterm' )) );

		// no publish filter
		$conds['no_publish_filter'] = 1;

		if ($conds['order_by_field'] == "" ){
			$conds['order_by_field'] = "added_date";
			$conds['order_by_type'] = "desc";
		}

		// pagination
		$this->data['rows_count'] = $this->Package->count_all_by( $conds );

		// search data
		$this->data['packages'] = $this->Package->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );
		
		// load add list
		parent::search();
	}

	/**
	 * Create new one
	 */
	function add() {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'pkg_add' );

		// call the core add logic
		parent::add();
	}

	/**
	 * Update the existing one
	 */
	function edit( $id ) {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'pkg_edit' );

		// load user
		$this->data['package'] = $this->Package->get_one( $id );

		// call the parent edit logic
		parent::edit( $id );
	}

	/**
	 * Saving Logic
	 * 1) save package
	 * 2) check transaction status
	 *
	 * @param      boolean  $id  The user identifier
	 */
	function save( $id = false ) {
		// start the transaction
		$this->db->trans_start();
		
		/** 
		 * Insert Package Records 
		 */
		$data = array();

		// prepare title
		if ( $this->has_data( 'title' )) {
			$data['title'] = $this->get_data( 'title' );
		}

        // prepare price
		if ( $this->has_data( 'price' )) {
			$data['price'] = $this->get_data( 'price' );
		}

		// prepare currency_id
		if ( $this->has_data( 'currency_id' )) {
			$data['currency_id'] = $this->get_data( 'currency_id' );
		}

        // prepare post_count
		if ( $this->has_data( 'post_count' )) {
			$data['post_count'] = $this->get_data( 'post_count' );
		}

	 	// prepare package_in_app_purchased_prd_id
		 if ( $this->has_data( 'package_in_app_purchased_prd_id' )) {
			$data['package_in_app_purchased_prd_id'] = $this->get_data( 'package_in_app_purchased_prd_id' );
		}

		// prepare type
		 if ( $this->has_data( 'type' )) {
			$data['type'] = $this->get_data( 'type' );
		}

		// save package
		if ( ! $this->Package->save( $data, $id )) {
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
				
				$this->set_flash_msg( 'success', get_msg( 'success_pkg_edit' ));
			} else {
			// if user id is false, show success_edit message

				$this->set_flash_msg( 'success', get_msg( 'success_pkg_add' ));
			}
		}

		redirect( $this->module_site_url());
	}

	/**
	 * Delete the record
	 * 1) delete package
	 * 2) check transactions
	 */
	function delete( $package_id ) {

		// start the transaction
		$this->db->trans_start();

		// check access
		$this->check_access( DEL );
		
		// delete package
		if ( !$this->ps_delete->delete_package( $package_id )) {

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
        	
			$this->set_flash_msg( 'success', get_msg( 'success_pkg_delete' ));
		}
		
		redirect( $this->module_site_url());
	}
	
	/**
	 * Determines if valid input.
	 *
	 * @return     boolean  True if valid input, False otherwise.
	 */
	function is_valid_input( $id = 0 ) {
		
		$rule = 'required|callback_is_valid_name['. $id  .']';

		$this->form_validation->set_rules( 'title', get_msg( 'title' ), $rule);

		if ( $this->form_validation->run() == FALSE ) {
		// if there is an error in validating,

			return false;
		}

		return true;
	}

	/**
	 * Determines if valid name.
	 *
	 * @param      <type>   $name  The  name
	 * @param      integer  $id     The  identifier
	 *
	 * @return     boolean  True if valid name, False otherwise.
	 */
	function is_valid_name( $name, $package_id = 0 )
	{		

		 $conds['title'] = $name;

			
		 	if( $package_id != "") {
				if ( strtolower( $this->Package->get_one( $id )->title ) == strtolower( $name )) {
				// if the name is existing name for that user id,
					return true;
				} 
			} else {
				if ( $this->Package->exists( ($conds ))) {
				// if the name is existed in the system,
					$this->form_validation->set_message('is_valid_name', get_msg( 'err_dup_name' ));
					return false;
				}
			}
			return true;
	}

	/**
	 * Check package title via ajax
	 *
	 * @param      boolean  $package_id  The cat identifier
	 */
	function ajx_exists( $package_id = false )
	{
		// get package title

		$title = $_REQUEST['title'];

		if ( $this->is_valid_name( $title, $package_id )) {

		// if the package title is valid,
			
			echo "true";
		} else {
		// if invalid package title,
			
			echo "false";
		}
	}

	/**
	 * Publish the record
	 *
	 * @param      integer  $package_id  The package identifier
	 */
	function ajx_publish( $package_id = 0 )
	{
		// check access
		$this->check_access( PUBLISH );
		
		// prepare data
		$package_data = array( 'status'=> 1 );
			
		// save data
		if ( $this->Package->save( $package_data, $package_id )) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	/**
	 * Unpublish the records
	 *
	 * @param      integer  $package_id  The category identifier
	 */
	function ajx_unpublish( $package_id = 0 )
	{
		// check access
		$this->check_access( PUBLISH );
		
		// prepare data
		$package_data = array( 'status'=> 0 );
			
		// save data
		if ( $this->Package->save( $package_data, $package_id )) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
}