<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Users crontroller for BE_USERS table
 */
class Agents extends BE_Controller {

	/**
	 * Constructs required variables
	 */
	function __construct() {
		parent::__construct( MODULE_CONTROL, 'Agents' );
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


		//no of filter
		$conds['no_publish_filter'] = 1;

		// get rows count
		$this->data['rows_count'] = $this->Agent->count_all($conds);

		// get users
		$this->data['agents'] = $this->Agent->get_all_by($conds, $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load index logic
		parent::index();
	}

	/**
	 * Searches for the first match in system users
	 */
	function search() {

		// breadcrumb urls
		$data['action_title'] = get_msg( 'agent_search' );
        
		// condition with search term
		// $conds = array( 'searchterm' => $this->searchterm_handler( $this->input->post( 'searchterm' )) );
		if($this->input->post('submit') != NULL ){

			if($this->input->post('searchterm') != "") {
				$conds['searchterm'] = $this->input->post('searchterm');
				$this->data['searchterm'] = $this->input->post('searchterm');
				$this->session->set_userdata(array("searchterm" => $this->input->post('searchterm')));
			} else {
				
				$this->session->set_userdata(array("searchterm" => NULL));
			}

			if($this->input->post('application_status') != "" || $this->input->post('application_status') != '0') {
				$conds['application_status'] = $this->input->post('application_status');
				$this->data['application_status'] = $this->input->post('application_status');
				$this->session->set_userdata(array("application_status" => $this->input->post('application_status')));
			} else {
				$this->session->set_userdata(array("application_status" => NULL ));
			}

		}else{
			//read from session value
			if($this->session->userdata('searchterm') != NULL){
				$conds['searchterm'] = $this->session->userdata('searchterm');
				$this->data['searchterm'] = $this->session->userdata('searchterm');
			}

			if($this->session->userdata('application_status') != NULL){
				$conds['application_status'] = $this->session->userdata('application_status');
				$this->data['application_status'] = $this->session->userdata('application_status');
			}
		}
      
        $conds['no_publish_filter'] = 1;

		// pagination
		$this->data['rows_count'] = $this->Agent->count_all_by( $conds );
  

		// search data
		$this->data['agents'] = $this->Agent->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load add list
		parent::search();
	}

	/**
	 * Create the user
	 */
	function add() {

		// breadcrumb
		$this->data['action_title'] = get_msg( 'user_add' );

		// call add logic
		parent::add();
	}

	/**
	 * Update the user
	 */
	function edit( $user_id ) {

		// breadcrumb
		$this->data['action_title'] = get_msg( 'agent_edit' );

		// load user
		$this->data['agent'] = $this->Agent->get_one( $user_id );
		// print_r($this->data['agent']);die;

		// call update logic
		parent::edit( $user_id );
	}


	/**
	 * Saving User Info logic
	 *
	 * @param      boolean  $user_id  The user identifier
	 */
	function save( $user_id = false ) {
		// prepare user object and permission objects
		$data = array();
		$user_name = $this->User->get_one($user_id)->user_name;
		// print_r($user_name);die;
		// save username
		if ( $this->has_data( 'user_name' )) {
			$data['user_name'] = $this->get_data( 'user_name' );
		}

		
		if( $this->has_data( 'user_email' )) {
			$data['user_email'] = $this->get_data( 'user_email' );
		}
		
		if( $this->has_data( 'user_phone' )) {
			$data['user_phone'] = $this->get_data( 'user_phone' );
		}


		// user_address
		if ( $this->has_data( 'user_address' )) {
			$data['user_address'] = $this->get_data( 'user_address' );
		}

		// save city
		if( $this->has_data( 'city' )) {
			$data['city'] = $this->get_data( 'city' );
		}

		// save user_about_me
		if( $this->has_data( 'user_about_me' )) {
			$data['user_about_me'] = $this->get_data( 'user_about_me' );
		}


		$data['apply_to'] = 1 ;
		//print_r($this->get_data('application_status'));die;

		// application_status
	    if ( $this->has_data( 'application_status' )) {
            $data['application_status'] = $this->get_data('application_status');
		}

		//user_type

		if ($this->get_data( 'application_status' ) == 1) {
			$data['user_type'] = 1;
		}else{
			$data['user_type'] = 0;
		}

		if ($this->get_data( 'application_status' ) == 0) {
			$data['user_type'] = $this->Agent->get_one($user_id)->user_type;
			$data['application_status'] = $this->Agent->get_one($user_id)->application_status;
			$data['apply_to'] = $this->Agent->get_one($user_id)->apply_to;
		}

		// save data
		// print_r($data);die;
		if ( ! $this->Agent->save( $data, $user_id )) {
		// if there is an error in inserting user data,	

			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} else {
		// if no eror in inserting

			if ( $user_id ) {
			// if user id is not false, show success_add message
				
				$this->set_flash_msg( 'success', get_msg( 'success_agent_edit' ));
			} else {
			// if user id is false, show success_edit message

				$this->set_flash_msg( 'success', get_msg( 'success_agent_add' ));
			}
		}

		/** 
		* Upload Image Records 
		*/
		
		if ( !$id ) {
		// if id is false, this is adding new record

			if ( ! $this->insert_agent_image( $_FILES, 'agent', $data['id'],'agent')) {
			// if error in saving image

			}
		}

		// send noti it user

		if ($data['application_status'] == '1') {
			$message = get_msg( 'apply_agent_noti_approve' ); 
		} else {
			$message = get_msg( 'apply_agent_noti_reject' ); 
		}
		$data['message'] = $message;
		$data['flag'] = 'verify_agent';

		$devices = $this->Noti->get_all_device_in($user_id)->result();

		$device_ids = array();
		if ( count( $devices ) > 0 ) {
			foreach ( $devices as $device ) {
				$device_ids[] = $device->device_token;
			}
		}

		$platform_names = array();
		if ( count( $devices ) > 0 ) {
			foreach ( $devices as $platform ) {
				$platform_names[] = $platform->platform_name;
			}
		}
		
		$status = send_android_fcm( $device_ids, $data, $platform_names );

		//// End - Send Noti /////

		if ( ! $this->check_trans()) {
        	
			// set flash error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} else {


			if ( !$status ) {
				$error_msg .= get_msg( 'noti_sent_fail' );
				$this->set_flash_msg( 'error', get_msg( 'noti_sent_fail' ) );
			}


			if ( $status ) {
				$this->set_flash_msg( 'success', get_msg( 'noti_sent_success' ) . ' ' . $user_name );
			}

		}

		// send email to user
		$this->load->library( 'PS_Mail' );

		if ($data['application_status'] == '1') {
			$subject = get_msg( 'apply_agent_noti_approve' ); 
		} else {
			$subject = get_msg( 'apply_agent_noti_reject' ); 
		}

		$is_apply_agent = $data['application_status'];

		if ( !send_user_apply_agent_email( $user_id, $subject, $is_apply_agent )) {

			$this->set_flash_msg( 'error', get_msg( 'verify_email_not_send_user' ) . ' ' . $user_name );
		
		}



		redirect( $this->module_site_url());
	}

	/**
	 * Determines if valid input.
	 *
	 * @return     boolean  True if valid input, False otherwise.
	 */
	function is_valid_input( $user_id = 0 ) {

		$email_verify = $this->Agent->get_one( $user_id )->email_verify;
		if ($email_verify == 1) {

			$rule = 'required|callback_is_valid_email['. $user_id  .']';

			$this->form_validation->set_rules( 'user_email', get_msg( 'user_email' ), $rule);

			if ( $this->form_validation->run() == FALSE ) {
			// if there is an error in validating,

				return false;
			}
		}

		return true;
	}

	/**
	 * Determines if valid email.
	 *
	 * @param      <type>   $email  The user email
	 * @param      integer  $user_id     The user identifier
	 *
	 * @return     boolean  True if valid email, False otherwise.
	 */
	function is_valid_email( $email, $user_id = 0 )
	{		

		if ( strtolower( $this->Agent->get_one( $user_id )->user_email ) == strtolower( $email )) {
		// if the email is existing email for that user id,
			
			return true;
		} else if ( $this->Agent->exists( array( 'user_email' => $_REQUEST['user_email'] ))) {
		// if the email is existed in the system,

			$this->form_validation->set_message('is_valid_email', get_msg( 'err_dup_email' ));
			return false;
		}

		return true;
	}

	function is_valid_phone( $phone, $user_id = 0 )
	{	
		if ( $this->Agent->get_one( $user_id )->user_phone  ==  $phone ) {
		// if the email is existing email for that user id,
			// echo "1";die;
			
			return true;
		} elseif ( $this->Agent->exists( array( 'user_phone' => $_REQUEST['user_phone'] ))) {
		// if the email is existed in the system,
			// echo "2";die;
			$this->form_validation->set_message('is_valid_phone', get_msg( 'err_dup_phone' ));
			return false;
		}
			
			return true;
	}

	/**
	 * Ajax Exists
	 *
	 * @param      <type>  $user_id  The user identifier
	 */
	function ajx_exists( $user_id = null )
	{
		$user_email = $_REQUEST['user_email'];
		
		if ( $this->is_valid_email( $user_email, $user_id )) {
		// if the user email is valid,
			
			echo "true";
		} else {
		// if the user email is invalid,

			echo "false";
		}
	}

	/**
	 * Ajax Exists
	 *
	 * @param      <type>  $user_id  The user identifier
	 */
	function ajx_exists_phone( $user_id = null )
	{
		$user_phone = $_REQUEST['user_phone'];
		
		if ( $this->is_valid_phone( $user_phone, $user_id )) {
		// if the user email is valid,
			
			echo "true";
		} else {
		// if the user email is invalid,

			echo "false";
		}
	}

	/**
	 * Delete the record
	 * 1) delete category
	 * 2) delete image from folder and table
	 * 3) check transactions
	 */
	function delete( $user_id ) {


		// start the transaction
		$this->db->trans_start();

		// check access
		$this->check_access( DEL );
		
		// delete categories and images
		// if ( !$this->ps_delete->delete_user( $user_id )) {

		// 	// set error message
		// 	$this->set_flash_msg( 'error', get_msg( 'err_model' ));

		// 	// rollback
		// 	$this->trans_rollback();

		// 	// redirect to list view
		// 	redirect( $this->module_site_url());
		// }

		//when delete agent, just update the all agent status in db

		$data['application_status'] = 0;
		$data['apply_to'] = 0;
		$data['user_type'] = 0;

		$this->Agent->save($data,$user_id);
			
		/**
		 * Check Transcation Status
		 */
		if ( !$this->check_trans()) {

			$this->set_flash_msg( 'error', get_msg( 'err_model' ));	
		} else {
        	
			$this->set_flash_msg( 'success', get_msg( 'success_agent_delete' ));
		}
		
		redirect( $this->module_site_url());
	}

	/**
	 * Delete all the news under category
	 *
	 * @param      integer  $category_id  The category identifier
	 */
	function delete_all( $user_id = 0 )
	{
		// start the transaction
		$this->db->trans_start();

		// check access
		$this->check_access( DEL );
		
		// delete categories and images

		/** Note: enable trigger will delete news under category and all news related data */
		if ( !$this->ps_delete->delete_user( $user_id )) {
		// if error in deleting category,

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
        	
			$this->set_flash_msg( 'success', get_msg( 'success_user_delete' ));
		}
		
		redirect( $this->module_site_url());
	}
}