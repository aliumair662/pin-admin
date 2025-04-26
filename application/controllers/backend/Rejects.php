<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Items Controller
 */
class Rejects extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'REJECTS' );
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

		$conds['status'] = 3;
		
		// get rows count
		$this->data['rows_count'] = $this->Reject->count_all_by( $conds );

		// get categories
		$this->data['rejects'] = $this->Reject->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );


		// load index logic
		parent::index();
	}

	/**
	 * Searches for the first match.
	 */
	function search() {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'prd_search' );

		// condition with search term
		$conds = array( 'searchterm' => $this->searchterm_handler( $this->input->post( 'searchterm' )) );
		$conds['status'] = 3;

		// pagination
		$this->data['rows_count'] = $this->Reject->count_all_by( $conds );

		// search data
		$this->data['rejects'] = $this->Reject->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load add list
		parent::search();
	}

	/**
	 * Saving Logic
	 * 1) upload image
	 * 2) save category
	 * 3) save image
	 * 4) check transaction status
	 *
	 * @param      boolean  $id  The user identifier
	 */
	function save( $id = false ) {
		
			$logged_in_user = $this->ps_auth->get_user_info();

			// if 'status' is checked,
			if ( $this->has_data( 'item_is_published' )) {
				$data['status'] = $this->get_data( 'item_is_published' );
			} 

			// set timezone

			if($id == "") {
				//save
				$data['added_date'] = date("Y-m-d H:i:s");
				$data['added_user_id'] = $logged_in_user->user_id;

			} else {
				//edit
				unset($data['added_date']);
				$data['updated_date'] = date("Y-m-d H:i:s");
				$data['updated_user_id'] = $logged_in_user->user_id;
			}
			//save item
			if ( ! $this->Reject->save( $data, $id )) {
			// if there is an error in inserting user data,	

				// rollback the transaction
				$this->db->trans_rollback();

				// set error message
				$this->data['error'] = get_msg( 'err_model' );
				
				return;
			}

			
		//get inserted item id	
		$id = ( !$id )? $data['id']: $id ;
		$title = $this->Reject->get_one($id)->title;
		//// Start - Send Noti /////
		if($data['status'] == 1) {
			//approve so change status to publish (1)
			$message = get_msg( 'approve_message_1' ) . $title . get_msg( 'approve_message_2' );
			
			// to send subscribe noti who subscribe the category - start

			$updated_flag = $this->Reject->get_one($id)->updated_flag;

			if($updated_flag == 0){

				$property_by_id =  $this->Item->get_one($id)->property_by_id;
				$propertyby_name = $this->PropertyBy->get_one($property_by_id)->name;
				$subscribe_msg = get_msg( 'new_item_upload_label' ) . ' ' . $propertyby_name;

				$data['message'] = $subscribe_msg;
				$data['subscribe'] = 1;
				$data['push'] = 0;
				$data['property_by_id'] = $property_by_id;
				$data['item_id'] = $id;

				// Push Notification for Mobile and FE

				$dyn_link_deep_url = $this->Backend_config->get_one('be1')->dyn_link_deep_url;

				$prj_url = explode('/', $dyn_link_deep_url);
				$i = count($prj_url)-3;
				$prj_name = $prj_url[$i];

				$status = send_android_fcm_topics_subscribe( $data );
				if ( !$status ) $error_msg .= get_msg('fail_push_devices') . "<br/>";

				$status_fe = send_android_fcm_topics_subscribe_fe( $data, $prj_name );
				if ( !$status_fe ) $error_msg .= get_msg('fail_push_websites') . "<br/>";
			}
			
			// update the updated_flag 1
			$itm_data = array(
				'updated_flag' => 1
			);
			$this->Reject->save($itm_data,$id);	
			// end

		} else if ($data['status'] == 2) {
			//disable so change status to publish (2)
			$message = get_msg( 'disable_message_1' ) . $title . get_msg( 'disable_message_2' );
		} else {
			//reject so change status to reject (3)
			$message = get_msg( 'reject_message_1' ) . $title . get_msg( 'reject_message_2' );
		}

		$data['message'] = $message;
		$data['flag'] = 'approval';
		$data['item_id'] = $id;

		$error_msg = "";
		$success_device_log = "";

		$user_ids = $this->Reject->get_one($id)->added_user_id;

		//for user name
		$user_id = $this->Reject->get_one($id)->added_user_id;
		$user_data = $this->User->get_one($user_id);
		$user_name = $user_data->user_name;
		$user_email = $user_data->user_email;
		$email_verify = $user_data->email_verify;
		
		//echo $user_device_token; die;

		$devices = $this->Noti->get_all_device_in($user_ids)->result();

        $device_ids = array();
        if ( count ( $devices ) > 0 ) {
        	foreach ($devices as $device) {
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
				$this->set_flash_msg( 'success', get_msg( 'noti_sent_success' ) . $user_name );
			}

		}
		
		// send email to user
		// if($user_email && $email_verify == 1){
			$this->load->library( 'PS_Mail' );

			// print_r("hello");die;
			if($data['status'] == 1) {
				//approve so change status to publish (1)
				$subject = get_msg( 'approve_message_1' ) . $title . get_msg( 'approve_message_2' );
			} else if ($data['status'] == 2) {
				//disable so change status to publish (2)
				$subject = get_msg( 'disable_message_1' ) . $title . get_msg( 'disable_message_2' );
			} else {
				//reject so change status to reject (3)
				$subject = get_msg( 'reject_message_1' ) . $title . get_msg( 'reject_message_2' );
			}
	
			$item_status = $data['status'];
			
			if ( !send_item_approval_email( $user_id, $subject, $item_status )) {
				$this->set_flash_msg( 'error', get_msg( 'verify_email_not_send_user' ) . ' ' . $user_name );
			}
		// }

		// redirect to list view
		redirect( $this->module_site_url() );
		
	}

    /**
	 * Show Gallery
	 *
	 * @param      <type>  $id     The identifier
	 */
	function gallery( $id ) {
		// breadcrumb urls
		$edit_item = get_msg('prd_edit');

		$this->data['action_title'] = array( 
			array( 'url' => 'edit/'. $id, 'label' => $edit_item ), 
			array( 'label' => get_msg( 'item_gallery' ))
		);
		
		$_SESSION['parent_id'] = $id;
		$_SESSION['type'] = 'item';
    	    	
    	$this->load_gallery();
    }


	/**
 	* Update the existing one
	*/
	function edit( $id ) 
	{
		
		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'reject_edit' );

		// load user
		$this->data['reject'] = $this->Reject->get_one( $id );

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
		
		$rule = 'required|callback_is_valid_name['. $id  .']';

		$this->form_validation->set_rules( 'title', get_msg( 'name' ), $rule);
		
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
	function is_valid_name( $name, $id = 0 )
	{		
		 $conds['title'] = $name;
		
		if ( strtolower( $this->Item->get_one( $id )->title ) == strtolower( $name )) {
		// if the name is existing name for that user id,
			return true;
		} else if ( $this->Item->exists( ($conds ))) {
		// if the name is existed in the system,
			$this->form_validation->set_message('is_valid_name', get_msg( 'err_dup_name' ));
			return false;
		}
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

	    if ( ! $this->ps_delete->delete_reject( $id, $enable_trigger )) {

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
 }