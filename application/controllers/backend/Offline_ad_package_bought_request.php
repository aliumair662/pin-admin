<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Offline_ad_package_bought_request Controller
 */
class Offline_ad_package_bought_request extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'OFFLINE_AD_PACKAGE_BOUGHT_REQUEST' );
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
		$conds['payment_method'] = 'offline';
		
		// get rows count
		$this->data['rows_count'] = $this->Package_bought->count_all_by( $conds );

		// get transactions
		$this->data['transactions'] = $this->Package_bought->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load index logic
		parent::index();
	}

	/**
	 * Searches for the first match.
	 */
	function search() {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'offline_pkg_bought_search' );

		// condition with search term
		if($this->input->post('submit') != NULL ){

			if($this->input->post('searchterm') != "") {
				$conds['searchterm'] = $this->input->post('searchterm');
				$this->data['searchterm'] = $this->input->post('searchterm');
				$this->session->set_userdata(array("searchterm" => $this->input->post('searchterm')));
			} else {
				
				$this->session->set_userdata(array("searchterm" => NULL));
			}
			

			if($this->input->post('status') != "") {
				$conds['status'] = $this->input->post('status');
				$this->data['status'] = $this->input->post('status');
				$this->session->set_userdata(array("status" => $this->input->post('status')));
			
			} else {
				$this->session->set_userdata(array("status" => NULL ));
			}

		} else {
			//read from session value
			if($this->session->userdata('searchterm') != NULL){
				$conds['searchterm'] = $this->session->userdata('searchterm');
				$this->data['searchterm'] = $this->session->userdata('searchterm');
			}

			if($this->session->userdata('status') != NULL){
				$conds['status'] = $this->session->userdata('status');
				$this->data['status'] = $this->session->userdata('status');
			}

		}

		$conds['payment_method'] = 'offline';


		if ($conds['status'] == 3) {
			$conds['status'] = 0 ;
		}
		
		// pagination
		$this->data['rows_count'] = $this->Package_bought->count_all_by( $conds );

		// search data
		$this->data['transactions'] = $this->Package_bought->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load add list
		parent::search();
	}

	/**
	 * Saving Logic
	 * 1) save offline transaction status 
	 * 2) update user remaining post count
	 *
	 * @param      boolean  $id  The user identifier
	 */
	function save( $id = false ) {
		
		$logged_in_user = $this->ps_auth->get_user_info();
		
		// if 'status' is checked,
		if ( $this->has_data( 'status' )) {
			$data['status'] = $this->get_data( 'status' );
		}
		
		//save package bought transaction
		if ( ! $this->Package_bought->save( $data, $id )) {
		// if there is an error in inserting user data,	

			// rollback the transaction
			$this->db->trans_rollback();

			// set error message
			$this->data['error'] = get_msg( 'err_model' );
			
			return;
		}
			
		// update user remaining post count
		if($data['status'] == 1){
			$package_id = $this->Package_bought->get_one($id)->package_id;
			$user_id = $this->Package_bought->get_one($id)->user_id;
			// get post count by package id
			$post_count = $this->Package->get_one($package_id)->post_count;
			$remaining_post = $this->User->get_one($user_id)->remaining_post;

			// set post count to package buyer user
			$user_data = array(
				"remaining_post" => (int)$remaining_post + (int)$post_count
			);

			$this->User->save($user_data, $user_id);
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
				
				$this->set_flash_msg( 'success', get_msg( 'success_offline_pkg_edit' ));
			} else {
			// if user id is false, show success_edit message

				$this->set_flash_msg( 'success', get_msg( 'success_offline_pkg_add' ));
			}
		}

		
		// redirect to list view
		redirect( $this->module_site_url() );
		
	}

	/**
 	* Update the existing one
	*/
	function edit( $id ) 
	{
		
		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'offline_pkg_bought_edit' );
		
		// load user
		$this->data['trans'] = $this->Package_bought->get_one( $id );
		// load item
		$package_id = $this->Package_bought->get_one( $id )->package_id;
		$user_id = $this->Package_bought->get_one( $id )->user_id;
		$this->data['package'] = $this->Package->get_one( $package_id );
		$this->data['user'] = $this->User->get_one( $user_id );

		// call the parent edit logic
		parent::edit( $id );

	}

	/**
	 * Determines if valid input.
	 *
	 * @return     boolean  True if valid input, False otherwise.
	 */
	function is_valid_input( $id = 0 ) 
	{
		
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
		
		if ( !$this->Package_bought->delete($id)) {

			$this->set_flash_msg( 'error', get_msg( 'err_model' ));	
		} else {
        	
			$this->set_flash_msg( 'success', get_msg( 'success_offline_pkg_delete' ));
		}
		
		
		redirect( $this->module_site_url());
	}


	/**
	 * Check Item name via ajax
	 *
	 * @param      boolean  $Item_id  The cat identifier
	 */
	function ajx_exists( $id = false )
	{
		
		// get Item name
		$name = $_REQUEST['title'];
		
		if ( $this->is_valid_name( $name, $id )) {
		// if the Item name is valid,
			
			echo "true";
		} else {
		// if invalid Item name,
			
			echo "false";
		}
	}
	/**
	 * Publish the record
	 *
	 * @param      integer  $prd_id  The Item identifier
	 */
	function ajx_publish( $item_id = 0 )
	{
		// check access
		$this->check_access( PUBLISH );
		
		// prepare data
		$prd_data = array( 'status'=> 1 );
			
		// save data
		if ( $this->Item->save( $prd_data, $item_id )) {
			//Need to delete at history table because that wallpaper need to show again on app
			$data_delete['item_id'] = $item_id;
			$this->Item_delete->delete_by($data_delete);
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	/**
	 * Unpublish the records
	 *
	 * @param      integer  $prd_id  The category identifier
	 */
	function ajx_unpublish( $item_id = 0 )
	{
		// check access
		$this->check_access( PUBLISH );
		
		// prepare data
		$prd_data = array( 'status'=> 0 );
			
		// save data
		if ( $this->Item->save( $prd_data, $item_id )) {

			//Need to save at history table because that wallpaper no need to show on app
			$data_delete['item_id'] = $item_id;
			$this->Item_delete->save($data_delete);
			echo 'true';
		} else {
			echo 'false';
		}
	}

	//get all location townships when select location

	function get_all_location_townships( $city_id )
    {
    	$conds['city_id'] = $city_id;
    	
    	$townships = $this->Item_location_township->get_all_by($conds);
		echo json_encode($townships->result());
    }

    //get all subcategories when select category

	function get_all_sub_categories( $cat_id )
    {
    	$conds['cat_id'] = $cat_id;
    	
    	$sub_categories = $this->Subcategory->get_all_by($conds);
		echo json_encode($sub_categories->result());
    }

 }