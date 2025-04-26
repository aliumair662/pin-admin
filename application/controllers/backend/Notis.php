<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Notis Controller
 */
class Notis extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'NOTIS' );
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
	* Load Notification Sending Form
	*/
	function index() {
		$this->data['action_title'] = get_msg('push_noti_module');
		// get rows count
		$this->data['rows_count'] = $this->Noti_message->count_all_by( $conds );

		// get notimsgs
		$this->data['notimsgs'] = $this->Noti_message->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );

		parent::index();
	}

	/**
	 * Searches for the first match.
	 */
	function search() {
		

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'noti_search' );
		
		// condition with search term
		$conds = array( 'searchterm' => $this->searchterm_handler( $this->input->post( 'searchterm' )) );
		// no publish filter
		$conds['no_publish_filter'] = 1;

		// pagination
		$this->data['rows_count'] = $this->Noti_message->count_all_by( $conds );

		// search data
		$this->data['notimsgs'] = $this->Noti_message->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );
		
		// load add list
		parent::search();
	}

	/**
	 * Create new one
	 */
	function add() {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'noti_add' );

		// call the core add logic
		parent::add();
	}
	/**
	* Sending Push Notification for flutter
	*/
	function push_message_flutter() { 

		if ( $this->input->server( 'REQUEST_METHOD' ) == "POST" ) {
			
			$desc = htmlspecialchars_decode($this->input->post( 'description' ));
			$message = htmlspecialchars_decode($this->input->post( 'message' ));


			//$noti_message = array('desc' => $desc, 'message' => $message);


			$error_msg = "";
			$success_device_log = "";

			// Push Notification for FE

			$dyn_link_deep_url = $this->Backend_config->get_one('be1')->dyn_link_deep_url;

			$prj_url = explode('/', $dyn_link_deep_url);
			$i = count($prj_url)-3;
			$prj_name = $prj_url[$i];

			$data['subscribe'] = 0;
			$data['push'] = 1;
			$data['desc'] = $desc;
			$data['message'] = $message;
			

			$status = send_android_fcm_topics_subscribe( $data );
			if ( !$status ) $error_msg .= get_msg('fail_push_devices') . "<br/>";

			$status_fe = send_android_fcm_topics_subscribe_fe( $data, $prj_name );
			if ( !$status_fe ) $error_msg .= get_msg('fail_push_websites') . "<br/>";

			$this->db->trans_start();
			$logged_in_user = $this->ps_auth->get_user_info();
			/** 
			 * Insert Notification Records 
			 */
			$data = array();

			// prepare noti name zawgyi
			if ( $this->has_data( 'description' )) {
				$data['description'] = $this->get_data( 'description' );
			}

			// prepare message zawgyi
			if ( $this->has_data( 'message' )) {
				$data['message'] = $this->get_data( 'message' );
			}

			$data['added_user_id'] = $logged_in_user->user_id;
			if($id == "") {
				//save
				$data['added_date'] = date("Y-m-d H:i:s");
			  } 
			// save notification
			if ( ! $this->Noti_message->save( $data, $id )) {
			// if there is an error in inserting user data,	

				// rollback the transaction
				$this->db->trans_rollback();

				// set error title
				$this->data['error'] = get_msg( 'err_model' );
				
				return;
			}
			/** 
			 * Upload Image Records 
			*/
		
			if ( !$id ) {
			// if id is false, this is adding new record

				if ( ! $this->insert_images( $_FILES, 'noti', $data['id'] )) {
				
				}

				
			}
				

				// commit the transaction
			if ( ! $this->check_trans()) {
	        	
				// set flash error title
				$this->set_flash_msg( 'error', get_msg( 'err_model' ));
			} else {

				if ( $id ) {
				// if user id is not false, show success_add title
					
					//$this->set_flash_msg( 'success', get_msg( 'success_cat_edit' ));
				} else {
				// if user id is false, show success_edit title

					$this->set_flash_msg( 'success', get_msg( 'success_noti_add' ));
				}
			}

		}

		// $this->data['action_title'] = get_msg('push_noti_module);
		redirect( $this->module_site_url());
	}

	/**
	 * Delete the record
	 * 1) delete notification
	 * 2) delete image from folder and table
	 * 3) check transactions
	 */
	function delete( $id ) {

		// start the transaction
		$this->db->trans_start();

		// check access
		$this->check_access( DEL );
		
		// delete categories and images
		if ( !$this->ps_delete->delete_noti( $id )) {

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
        	
			$this->set_flash_msg( 'success', get_msg( 'success_noti_delete' ));
		}
		
		redirect( $this->module_site_url());
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
	* Sending Message From FCM For Android & iOS By using topics subscribe
	*/
	function send_android_fcm_topics_subscribe( $noti_message = array() ) 
    {
    	//Google cloud messaging GCM-API url
    	

    	$url = 'https://fcm.googleapis.com/fcm/send';
    	// $fields = array(
    	//     'registration_ids' => $registatoin_ids,
    	//     'data' => $message,
    	// );

    	$noti_arr = array(
    		'title' => $noti_message['message'],
+    		'body' => $noti_message['desc'],
    		'sound' => 'default',
    		'flag' => 'broadcast'
    	);

    	

    	$noti_data = array(
    		'message' => $noti_message['message'],
    		'flag' => 'broadcast',
    		'sound' => 'default',
    		'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
    	);
    	
    	$fields = array(
    		'sound' => 'default',
    		'flag' => 'broadcast',
    		'notification' => $noti_arr,
    		'data' => $noti_data,
    	    'to' => '/topics/' . $this->Backend_config->get_one('be1')->topics
    	);


    	define("GOOGLE_API_KEY", $this->Backend_config->get_one('be1')->fcm_api_key);  	
    		
    	$headers = array(
    	    'Authorization: key=' . GOOGLE_API_KEY,
    	    'Content-Type: application/json'
    	);
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);	
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    	$result = curl_exec($ch);				
    	if ($result === FALSE) {
    	    die('Curl failed: ' . curl_error($ch));
    	}
    	curl_close($ch);
    	return $result;
    }
}