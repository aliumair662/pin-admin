<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Main Controller for API classes
 */
class API_Controller extends REST_Controller
{
	// model to access database
	protected $model;

	// validation rule for new record
	protected $create_validation_rules;

	// validation rule for update record
	protected $update_validation_rules;

	// validation rule for delete record
	protected $delete_validation_rules;

	// is adding record?
	protected $is_add;

	// is updating record?
	protected $is_update;

	// is deleting record?
	protected $is_delete;

	// is get record using GET method?
	protected $is_get;

	// is search record using GET method?
	protected $is_search;

	// login user id API parameter key name
	protected $login_user_key;

	// login user id
	protected $login_user_id;

	// if API allowed zero login user id,
	protected $is_login_user_nullable;

	// default value to ignore user id
	protected $ignore_user_id;

	/**
	 * construct the parent 
	 */
	function __construct( $model, $is_login_user_nullable = false )
	{
		header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

		parent::__construct();

		// set the model object
		$this->model = $this->{$model};

		// load security library
		$this->load->library( 'PS_Security' );

		// load the adapter library
		$this->load->library( 'PS_Adapter' );
		
		// set the login user nullable
		$this->is_login_user_nullable = $is_login_user_nullable;

		// login user id key
		$this->login_user_key = "login_user_id";

		// default value to ignore user id for API
		$this->ignore_user_id = "nologinuser";

		if ( $this->is_logged_in()) {
		// if login user id is existed, pass the id to the adapter

			$this->login_user_id = $this->get_login_user_id();

			if ( !$this->User->is_exist( $this->login_user_id ) && !$this->is_login_user_nullable ) {
			// if login user id not existed in system,

				$this->error_response( get_msg( 'invalid_login_user_id' ), 400);
			}

			$this->ps_adapter->set_login_user_id( $this->login_user_id );
		}

		// load the mail library
		$this->load->library( 'PS_Mail' );

		// if ( ! $this->is_valid_api_key()) {
		// // if invalid api key

		// 	$this->response( array(
		// 		'status' => 'error',
		// 		'message' => get_msg( 'invalid_api_key' )
		// 	), 404 );
		// }

		// default validation rules
		$this->default_validation_rules();
	}

	/**
	 * Determines if logged in.
	 *
	 * @return     boolean  True if logged in, False otherwise.
	 */
	function is_logged_in()
	{
		// it is login user if the GET login_user_id is not null and default key
		// it is login user if the POST login_user_id is not null
		// it is login user if the PUT login_user_id is not null
		return ( $this->get( $this->login_user_key ) != null && $this->get( $this->login_user_key ) != $this->ignore_user_id ) ||
			( $this->post( $this->login_user_key ) != null ) ||
			( $this->put( $this->login_user_key ) != null ) ;
	}

	/**
	 * Gets the login user identifier.
	 */
	function get_login_user_id()
	{
		/**
		 * GET['login_user_id'] will create POST['user_id']
		 * POST['login_user_id'] will create POST['user_id'] and remove POST['login_user_id']
		 * PUT['login_user_id'] will create PUT['user_id'] and remove PUT['login_user_id']
		 */
		// if exist in get variable,
		if ( $this->get( $this->login_user_key ) != null) {

			// get user id
			$login_user_id = $this->get( $this->login_user_key );

			// replace user_id
			$this->_post_args['user_id'] = $this->get( $this->login_user_key );
			
			return $this->get( $this->login_user_key );
		}

		// if exist in post variable,
		if ( $this->post( $this->login_user_key ) != null) {

			// get user id
			$login_user_id = $this->post( $this->login_user_key );

			// replace user_id
			$this->_post_args['user_id'] = $this->post( $this->login_user_key );
			unset( $this->_post_args[ $this->login_user_key ] );
			
			return $login_user_id;
		}

		// if exist in put variable,
		if ( $this->put( $this->login_user_key ) != null) {

			// get user id
			$login_user_id = $this->put( $this->login_user_key );

			// replace user_id
			$this->_put_args['user_id'] = $this->put( $this->login_user_key );
			unset( $this->_put_args[ $this->login_user_key ] );
			
			return $login_user_id;
		}
	}

	/**
	 * Convert logged in user id to user_id
	 */
	function get_similar_key( $actual, $similar )
	{
		if ( empty( parent::post( $actual )) && empty( parent::put( $actual ))) {
		// if actual key is not existed in POST and PUT, return similar

			return $similar;
		}

		// else, just return normal key
		return $actual;
	}

	/**
	 * Override Get variables
	 *
	 * @param      <type>  $key    The key
	 */
	function get( $key = NULL, $xss_clean = true )
	{
		return $this->ps_security->clean_input( parent::get( $key, $xss_clean ));
	}

	/**
	 * Override Post variables
	 *
	 * @param      <type>  $key    The key
	 */
	function post( $key = NULL, $xss_clean = true )
	{
		if ( $key == 'user_id' ) {
		// if key is user_id and user_id is not in variable, get the similar key

			$key = $this->get_similar_key( 'user_id', $this->login_user_key );
		}

		return $this->ps_security->clean_input( parent::post( $key, $xss_clean ));
	}

	/**
	 * Override Put variables
	 *
	 * @param      <type>  $key    The key
	 */
	function put( $key = NULL, $xss_clean = true )
	{
		if ( $key == 'user_id' ) {
		// if key is user_id and user_id is not in variable, get the similar key
			
			$key = $this->get_similar_key( 'user_id', $this->login_user_key );
		}

		return $this->ps_security->clean_input( parent::put( $key, $xss_clean ));
	}

	/**
	 * Determines if valid api key.
	 *
	 * @return     boolean  True if valid api key, False otherwise.
	 */
	function is_valid_api_key()
	{	
		$client_api_key = $this->get( 'api_key' );
		
		if ( $client_api_key == NULL ) {
		// if API key is null, return false;

			return false;
		}
		$conds['key'] = $client_api_key;

		$api_key = $this->Api_key->get_all_by( $conds)->result();
		$server_api_key = $api_key[0]->key;

		if ( $client_api_key != $server_api_key ) {
		// if API key is different with server api key, return false;

			return false;
		}

		return true;
	}

	/**
	 * Convert Object
	 */
	function convert_object( &$obj ) 
	{
		// convert added_date date string
		if ( isset( $obj->added_date )) {
			
			// added_date timestamp string
			$obj->added_date_str = ago( $obj->added_date );
		}
	}

	/**
	 * Gets the default photo.
	 *
	 * @param      <type>  $id     The identifier
	 * @param      <type>  $type   The type
	 */
	function get_default_photo( $id, $type )
	{
		$default_photo = "";

		// get all images
		$img = $this->Image->get_all_by( array( 'img_parent_id' => $id, 'img_type' => $type ))->result();

		if ( count( $img ) > 0 ) {
		// if there are images for news,
			
			$default_photo = $img[0];
		} else {
		// if no image, return empty object

			$default_photo = $this->Image->get_empty_object();
		}

		return $default_photo;
	}

	/**
	 * Response Error
	 *
	 * @param      <type>  $msg    The message
	 */
	function error_response( $msg, $code = false )
	{
		if(!$code) $code = 404;
		$this->response( array(
			'status' => 'error',
			'message' => $msg
		), $code );
	}

	/**
	 * Response Success
	 *
	 * @param      <type>  $msg    The message
	 */
	function success_response( $msg, $code = false)
	{ 
		if(!$code) $code = 200;
		$this->response( array(
			'status' => 'success',
			'message' => $msg
		), $code);
	}

	/**
	 * Custome Response return 404 if not data found
	 *
	 * @param      <type>  $data   The data
	 */
	function custom_response( $data, $offset = false,$require_convert = true )
	{
		if ( empty( $data )) {
		// if there is no data, return error
			if (empty( $data ) && $offset == 0) {
				$this->error_response(get_msg( 'record_not_found'), 404);
			} else if (empty( $data ) && $offset > 0) {
				$this->error_response(get_msg( 'record_no_pagination'), 404);
			}

		} else if ( $require_convert ) {
		// if there is data, return the list

			if ( is_array( $data )) {
			// if the data is array

				foreach ( $data as $obj ) {

					// convert object for each obj
					$this->convert_object( $obj );
				}
			} else {

				$this->convert_object( $data );
			}
		}

		$data = $this->ps_security->clean_output( $data );

		$this->response( $data );
	}

	/**
	 * Default Validation Rules
	 */
	function default_validation_rules()
	{
		// default rules
		$rules = array(
			array(
				'field' => $this->model->primary_key,
				'rules' => 'required|callback_id_check'
			)
		);

		// set to update validation rules
		$this->update_validation_rules = $rules;

		// set to delete_validation_rules
		$this->delete_validation_rules = $rules;
	}

	/**
	 * Id Checking
	 *
	 * @param      <type>  $id     The identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function id_check( $id, $model_name = false )
    {
    	$tmp_model = $this->model;

    	if ( $model_name != false) {
    		$tmp_model = $this->{$model_name};
    	}

        if ( !$tmp_model->is_exist( $id )) {
        
            $this->form_validation->set_message('id_check', 'Invalid {field}');
            return false;
        }

        return true;
    }

	/**
	 * { function_description }
	 *
	 * @param      <type>   $conds  The conds
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	function is_valid( $rules )
	{
		if ( empty( $rules )) {
		// if rules is empty, no checking is required
			
			return true;
		}

		// GET data
		$user_data = array_merge( $this->get(), $this->post(), $this->put() );

		$this->form_validation->set_data( $user_data );
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules( $rules );

		if ( $this->form_validation->run() == FALSE ) {
		// if there is an error in validating,

			$errors = $this->form_validation->error_array();

			if ( count( $errors ) == 1 ) {
			// if error count is 1, remove '\n'

				$this->error_response( trim(validation_errors()), 400);
			}

			$this->error_response( validation_errors(), 400);
		}

		return true;
	}

	/**
	 * Returns default condition like default order by
	 * @return array custom_condition_array
	 */
	function default_conds()
	{
		return array();
	}

	/**
	 * Get all or Get One
	 */
	function get_get()
	{
		// add flag for default query
		$this->is_get = true;

		// get id
		$id = $this->get( 'id' );

		if ( $id ) {
			
			// if 'id' is existed, get one record only
			$data = $this->model->get_one( $id );

			if ( isset( $data->is_empty_object )) {
			// if the id is not existed in the return object, the object is empty
				
				$data = array();
			}

			$this->custom_response( $data );
		}

		// get limit & offset
		$limit = $this->get( 'limit' );
		$offset = $this->get( 'offset' );

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge( $default_conds, $user_conds );
		$conds['get_user'] = 1;

		if ( $limit ) {
			unset( $conds['limit']);
		}

		if ( $offset ) {
			unset( $conds['offset']);
		}

		if ( count( $conds ) == 0 ) {
		// if 'id' is not existed, get all	
		
			if ( !empty( $limit ) && !empty( $offset )) {
			// if limit & offset is not empty
				
				$data = $this->model->get_all( $limit, $offset )->result();
			} else if ( !empty( $limit )) {
			// if limit is not empty
				
				$data = $this->model->get_all( $limit )->result();
			} else {
			// if both are empty

				$data = $this->model->get_all()->result();
			}

			$this->custom_response( $data , $offset );
		} else {

			if ( !empty( $limit ) && !empty( $offset )) {
			// if limit & offset is not empty

				$data = $this->model->get_all_by( $conds, $limit, $offset )->result();
			} else if ( !empty( $limit )) {
			// if limit is not empty

				$data = $this->model->get_all_by( $conds, $limit )->result();
			} else {
			// if both are empty

				$data = $this->model->get_all_by( $conds )->result();
			}

			$this->custom_response( $data , $offset );
		}
	}

	/**
	 * Get all or Get One
	 */
	function get_favourite_get()
	{
		
		// add flag for default query
		$this->is_get = true;

		// get limit & offset
		$limit = $this->get( 'limit' );
		$offset = $this->get( 'offset' );

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge( $default_conds, $user_conds );
		$conds['user_id'] = $this->get_login_user_id();

		/* For User Block */
		if($this->App_setting->get_one('app1')->is_block_user == "1"){
			//user block check with login_user_id
			$conds_login_block['from_block_user_id'] = $this->get_login_user_id();
			$login_block_count = $this->Block->count_all_by($conds_login_block);
			//print_r($login_block_count);die;

			// user blocked existed by login user
			if ($login_block_count > 0) {
				// get the blocked user by login user
				$to_block_user_datas = $this->Block->get_all_by($conds_login_block)->result();

				foreach ( $to_block_user_datas as $to_block_user_data ) {

					$to_block_user_id .= "'" .$to_block_user_data->to_block_user_id . "',";
			
				}

				// get block user's item

				$result_users = rtrim($to_block_user_id,',');
				$conds_user['added_user_id'] = $result_users;

				$item_users = $this->Item->get_all_in_item( $conds_user )->result();

				foreach ( $item_users as $item_user ) {

					$id .= $item_user->id .",";
				
				}

				// get all item without block user's item

				$result_items = rtrim($id,',');
				$item_id = explode(",", $result_items);
				//print_r($item_id);die;
				//$conds['id'] = $result_items;

			}	
		}
		/* For Item Report */

		//item report check with login_user_id
		$conds_report['reported_user_id'] = $this->get_login_user_id();
		$reported_data_count = $this->Itemreport->count_all_by($conds_report);

		// item reported existed by login user
		if ($reported_data_count > 0) {
			// get the reported item data
			$item_reported_datas = $this->Itemreport->get_all_by($conds_report)->result();

			foreach ( $item_reported_datas as $item_reported_data ) {

				$item_ids .= "'" .$item_reported_data->item_id . "',";
		
			}

			// get block user's item

			$result_reports = rtrim($item_ids,',');
			$conds_item['id'] = $result_reports;

			$item_reports = $this->Item->get_all_in_report( $conds_item )->result();

			foreach ( $item_reports as $item_report ) {

				$ids .= $item_report->id .",";
			
			}

			// get all item without block user's item

			$result_items = rtrim($ids,',');
			$reported_item_id = explode(",", $result_items);
			//$conds['id'] = $result_items;
		}

		$conds['item_id'] = $item_id;
		$conds['reported_item_id'] = $reported_item_id;
		$conds['status'] = 1;

		if ( $limit ) {
			unset( $conds['limit']);
		}

		if ( $offset ) {
			unset( $conds['offset']);
		}
		
		if ( !empty( $limit ) && !empty( $offset )) {
		// if limit & offset is not empty
			$data = $this->model->get_item_favourite( $conds, $limit, $offset )->result();
		} else if ( !empty( $limit )) {
		// if limit is not empty

			$data = $this->model->get_item_favourite( $conds, $limit )->result();
		} else {
		// if both are empty
			$data = $this->model->get_item_favourite( $conds )->result();
		}

		$this->custom_response( $data , $offset );
	}

	/**
	 * Get all or Get One
	 */
	function get_user_follow_get()
	{
		// add flag for default query
		$this->is_get = true;

		// get limit & offset
		$limit = $this->get( 'limit' );
		$offset = $this->get( 'offset' );

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge( $default_conds, $user_conds );
		$conds['user_id'] = $this->get( 'login_user_id' );

		$userfollow_data = $this->Userfollow->get_all_by($conds)->result();
		//print_r(count( $userfollow_data ));die;

		if ( count( $userfollow_data ) > 0 ) {
			foreach ($userfollow_data as $userfollow) {
		  	$result .= "'".$userfollow->followed_user_id ."'" .",";
		  
		}

		if ( !empty( $limit ) && !empty( $offset )) {
		// if limit & offset is not empty
			//$data = $this->model->get_wallpaper_delete_by_userid( $conds, $limit, $offset )->result();
			$data = $this->model->get_all_by( $conds, $limit, $offset )->result();
		} else if ( !empty( $limit )) {
		// if limit is not empty

			//$data = $this->model->get_wallpaper_delete_by_userid( $conds, $limit )->result();
			$data = $this->model->get_all_by( $conds, $limit )->result();
		} else {
		// if both are empty
			//$data = $this->model->get_wallpaper_delete_by_userid( $conds )->result();
			$data = $this->model->get_all_by( $conds )->result();
		}


		$followuser = rtrim($result,",");

		$conds['followuser'] = $followuser;

		$obj = $this->User->get_all_follower_by_user($conds, $limit, $offset)->result();

		if($obj == "") {
			$this->error_response(get_msg( 'record_not_found'), 404);
		} else {
			$this->ps_adapter->convert_follow_user_list( $obj );
			$this->custom_response( $obj , $offset );
		}
		
		
		} else {
			$this->error_response(get_msg( 'record_not_found'), 404);

		}
		
	}

	/**
	 * Get all or Get One
	 */
	function get_download_get()
	{
		// add flag for default query
		$this->is_get = true;

		// get limit & offset
		$limit = $this->get( 'limit' );
		$offset = $this->get( 'offset' );

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge( $default_conds, $user_conds );
		$conds['user_id'] = $this->get( 'login_user_id' );
		if ( $limit ) {
			unset( $conds['limit']);
		}

		if ( $offset ) {
			unset( $conds['offset']);
		}
		
		if ( !empty( $limit ) && !empty( $offset )) {
		// if limit & offset is not empty
			// echo "adfad";die;
			$data = $this->model->get_download_by_userid( $conds, $limit, $offset )->result();
		} else if ( !empty( $limit )) {
		// if limit is not empty

			$data = $this->model->get_download_by_userid( $conds, $limit )->result();
		} else {
		// if both are empty
			$data = $this->model->get_download_by_userid( $conds )->result();
		}

		$this->custom_response( $data , $offset );
	}

	function get_token_get()
	{

		$payment_info = $this->Paid_config->get_one('pconfig1');

		$environment = $payment_info->paypal_environment;
		$merchantId  = $payment_info->paypal_merchant_id;
		$publicKey   = $payment_info->paypal_public_key;
		$privateKey  = $payment_info->paypal_private_key;


		//echo ">>" . $environment . " - " . $merchantId . " - " . $publicKey . " - " . $privateKey; die;

		$gateway = new Braintree_Gateway([
		  'environment' => $environment,
		  'merchantId' => $merchantId,
		  'publicKey' => $publicKey,
		  'privateKey' => $privateKey
		]);

		$clientToken = $gateway->clientToken()->generate();

		//$this->custom_response( $clientToken );

		if($clientToken != "") {
			$this->response( array(
				'status' => 'success',
				'message' => $clientToken
			));
		} else {
			$this->error_response( get_msg( 'token_not_round' ), 404);
		}

	}

	/**
	 * Search API
	 */
	function search_post()
	{

		$item_id = $reported_item_id = [];
		// add flag for default query
		$this->is_search = true;


		// add default conds
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge( $default_conds, $user_conds );
		// check empty condition
		$final_conds = array();
		foreach( $conds as $key => $value ) {
    
		    if (isset($conds['is_sold_out'])) {
				$final_conds[$key] = $value;

			} else {

				if($key != "status") {
				    if ( !empty( $value )) {
				     $final_conds[$key] = $value;
				    }
			    }

			    if($key == "status") {
			    	$final_conds[$key] = $value;
			    }

			}


		}
		$conds = $final_conds;

		$limit = $this->get( 'limit' );
		$offset = $this->get( 'offset' );

		if (isset($conds['item_search']) && $conds['item_search']==1) {
			
		    // get item_id from junction table by amenity_id
			$item_ids = "";
            if($this->post('amenity_id') != "") {

                $amenity_id = $this->post('amenity_id');
                $amenity_id_to_arr = explode(",",$amenity_id);

                $conds_db['amenities_id'] = $amenity_id_to_arr;

//                print_r($conds_db['amenity_id']);

                $amenities = $this->Item_amenity->get_all_by($conds_db)->result();
//                print_r($amenities);die();

                //get all item_id by amenity_id from item table
                foreach ($amenities as $amenity) {
                    // $item_ids .= $amenity->item_id. ",";
					$item_ids .= "'" .$amenity->item_id . "'" . ",";
                }

                $item_ids = rtrim($item_ids,",");

                $ids_by_amenity = explode(",",$item_ids);

            }

			/* For User Block */
			if($this->App_setting->get_one('app1')->is_block_user == "1"){
				//user block check with login_user_id
				$conds_login_block['from_block_user_id'] = $this->get_login_user_id();
				$login_block_count = $this->Block->count_all_by($conds_login_block);
				//print_r($login_block_count);die;

				// user blocked existed by login user
				if ($login_block_count > 0) {
					// get the blocked user by login user
					$to_block_user_datas = $this->Block->get_all_by($conds_login_block)->result();
					$to_block_user_id = '';
					foreach ( $to_block_user_datas as $to_block_user_data ) {

						$to_block_user_id .= "'" .$to_block_user_data->to_block_user_id . "',";

					}

					// get block user's item

					$result_users = rtrim($to_block_user_id,',');
					$conds_user['added_user_id'] = $result_users;

					$item_users = $this->Item->get_all_in_item( $conds_user )->result();
					$id = "";
					foreach ( $item_users as $item_user ) {

						$id .= $item_user->id .",";

					}

					// get all item without block user's item

					$result_items = rtrim($id,',');
					$item_id = explode(",", $result_items);
					//print_r($item_id);die;
					//$conds['id'] = $result_items;

				}
			}

			/* For Item Report */

			//item report check with login_user_id
			$conds_report['reported_user_id'] = $this->get_login_user_id();
			$reported_data_count = $this->Itemreport->count_all_by($conds_report);

			// item reported existed by login user
			if ($reported_data_count > 0) {
				// get the reported item data
				$item_reported_datas = $this->Itemreport->get_all_by($conds_report)->result();

				foreach ( $item_reported_datas as $item_reported_data ) {

					$item_ids .= "'" .$item_reported_data->item_id . "',";

				}

				// get block user's item

				$result_reports = rtrim($item_ids,',');
				$conds_item['id'] = $result_reports;

				$item_reports = $this->Item->get_all_in_report( $conds_item )->result();
				$ids = "";
				foreach ( $item_reports as $item_report ) {

					$ids .= $item_report->id .",";

				}

				// get all item without block user's item

				$result_items = rtrim($ids,',');
				$reported_item_id = explode(",", $result_items);
				//$conds['id'] = $result_items;
			}

			// ad post type response
			if ($conds['ad_post_type'] == "only_paid_item") {
				$conds['item_id'] = $item_id;
				$conds['reported_item_id'] = $reported_item_id;
				$conds['is_paid'] = 1 ;
				
				if ( !empty( $limit ) && !empty( $offset )) {
					// if limit & offset is not empty
					$data = $this->model->get_all_item_by_paid( $conds, $limit, $offset );

				} else if ( !empty( $limit )) {
					// if limit is not empty
					$data = $this->model->get_all_item_by_paid( $conds, $limit );

				} else {
					// if both are empty
					$data = $this->model->get_all_item_by_paid( $conds );

				}
			} elseif ($conds['ad_post_type'] == "paid_item_first") {
				$result = "";

				$conds['item_id'] = $item_id;
				$conds['reported_item_id'] = $reported_item_id;
				$conds['is_paid'] = 1;
				
				if ( !empty( $limit ) && !empty( $offset )) {
					// if limit & offset is not empty
					$data = $this->model->get_all_item_by_paid_date( $conds, $limit, $offset );
				} else if ( !empty( $limit )) {
					// if limit is not empty
					$data = $this->model->get_all_item_by_paid_date( $conds, $limit );
				} else {
					// if both are empty
					$data = $this->model->get_all_item_by_paid_date( $conds );
				}

			} elseif ($conds['ad_post_type'] == "paid_item_first_with_google") {
				$result = "";

				$conds['item_id'] = $item_id;
				$conds['reported_item_id'] = $reported_item_id;
				$conds['is_paid'] = 1;
				
				if ( !empty( $limit ) && !empty( $offset )) {
					// if limit & offset is not empty
					$data = $this->model->get_all_item_by_paid_date_with_google( $conds, $limit, $offset );
				} else if ( !empty( $limit )) {
					// if limit is not empty
					$data = $this->model->get_all_item_by_paid_date_with_google( $conds, $limit );
				} else {
					// if both are empty
					$data = $this->model->get_all_item_by_paid_date_with_google( $conds );
				}

			} elseif ($conds['ad_post_type'] == "normal_ads_only") {
				
				$conds['item_id'] = $item_id;
				$conds['reported_item_id'] = $reported_item_id;
				
				if ( !empty( $limit ) && !empty( $offset )) {
					// if limit & offset is not empty
					$data = $this->model->get_all_item_by_normal_ads( $conds, $limit, $offset );
				} else if ( !empty( $limit )) {
					// if limit is not empty
					$data = $this->model->get_all_item_by_normal_ads( $conds, $limit );
				} else {
					// if both are empty
					$data = $this->model->get_all_item_by_normal_ads( $conds );
				}

			} elseif ($conds['ad_post_type'] == "google_ads_between") {
				
				$conds['item_id'] = $item_id;
				$conds['reported_item_id'] = $reported_item_id;
				
				if ( !empty( $limit ) && !empty( $offset )) {
					// if limit & offset is not empty
					$data = $this->model->get_all_item_by_google_ads( $conds, $limit, $offset );
				} else if ( !empty( $limit )) {
					// if limit is not empty
					$data = $this->model->get_all_item_by_google_ads( $conds, $limit );
				} else {
					// if both are empty
					$data = $this->model->get_all_item_by_google_ads( $conds );
				}

			} elseif ($conds['ad_post_type'] == "bumps_and_google_ads_between") {
				
				$conds['item_id'] = $item_id;
				$conds['reported_item_id'] = $reported_item_id;
				
				if ( !empty( $limit ) && !empty( $offset )) {
					// if limit & offset is not empty
					$data = $this->model->get_all_item_by_bumps_up_google_ads( $conds, $limit, $offset );
				} else if ( !empty( $limit )) {
					// if limit is not empty
					$data = $this->model->get_all_item_by_bumps_up_google_ads( $conds, $limit );
				} else {
					// if both are empty
					$data = $this->model->get_all_item_by_bumps_up_google_ads( $conds );
				}

			} elseif ($conds['ad_post_type'] == "bumps_ups_between") {
				
				$conds['item_id'] = $item_id;
				$conds['reported_item_id'] = $reported_item_id;
				
				if ( !empty( $limit ) && !empty( $offset )) {
					// if limit & offset is not empty
					$data = $this->model->get_all_item_by_bumps_up( $conds, $limit, $offset );
				} else if ( !empty( $limit )) {
					// if limit is not empty
					$data = $this->model->get_all_item_by_bumps_up( $conds, $limit );
				} else {
					// if both are empty
					$data = $this->model->get_all_item_by_bump_up( $conds );
				}

			} else {
				// if ad_post_type is null, get paid_ad_type from app_setting
				$ad_type = $this->App_setting->get_one('app1')->ad_type;
				$promo_cell_interval_no = $this->App_setting->get_one('app1')->promo_cell_interval_no;
				$ad_post_type = $this->Ad_post_type->get_one($ad_type)->key;

				$conds['item_id'] = $item_id;
				$conds['reported_item_id'] = $reported_item_id;

				if($ad_post_type == "paid_item_first"){
					// paid_item_first
					$conds['is_paid'] = 1;
					
					if ( !empty( $limit ) && !empty( $offset )) {
						// if limit & offset is not empty
						$data = $this->model->get_all_item_by_paid_date( $conds, $limit, $offset );
					} else if ( !empty( $limit )) {
						// if limit is not empty
						$data = $this->model->get_all_item_by_paid_date( $conds, $limit );
					} else {
						// if both are empty
						$data = $this->model->get_all_item_by_paid_date( $conds );
					}
			
				} elseif($ad_post_type == "paid_item_first_with_google"){
					// paid_item_first
					$conds['is_paid'] = 1;
					
					if ( !empty( $limit ) && !empty( $offset )) {
						// if limit & offset is not empty
						$data = $this->model->get_all_item_by_paid_date_with_google( $conds, $limit, $offset );
					} else if ( !empty( $limit )) {
						// if limit is not empty
						$data = $this->model->get_all_item_by_paid_date_with_google( $conds, $limit );
					} else {
						// if both are empty
						$data = $this->model->get_all_item_by_paid_date_with_google( $conds );
					}
			
				} elseif($ad_post_type == "bumps_ups_between") {
					// bumps_ups_between
					
					if ( !empty( $limit ) && !empty( $offset )) {
						// if limit & offset is not empty
						$data = $this->model->get_all_item_by_bumps_up( $conds, $limit, $offset );
					} else if ( !empty( $limit )) {
						// if limit is not empty
						$data = $this->model->get_all_item_by_bumps_up( $conds, $limit );
					} else {
						// if both are empty
						$data = $this->model->get_all_item_by_bump_up( $conds );
					}
				} elseif($ad_post_type == "google_ads_between") {
					// google_ads_between
						
					if ( !empty( $limit ) && !empty( $offset )) {
						// if limit & offset is not empty
						$data = $this->model->get_all_item_by_google_ads( $conds, $limit, $offset );
					} else if ( !empty( $limit )) {
						// if limit is not empty
						$data = $this->model->get_all_item_by_google_ads( $conds, $limit );
					} else {
						// if both are empty
						$data = $this->model->get_all_item_by_google_ads( $conds );
					}

				} elseif($ad_post_type == "bumps_and_google_ads_between") {
					// bumps_and_google_ads_between
					
					if ( !empty( $limit ) && !empty( $offset )) {
						// if limit & offset is not empty
						$data = $this->model->get_all_item_by_bumps_up_google_ads( $conds, $limit, $offset );
					} else if ( !empty( $limit )) {
						// if limit is not empty
						$data = $this->model->get_all_item_by_bumps_up_google_ads( $conds, $limit );
					} else {
						// if both are empty
						$data = $this->model->get_all_item_by_bumps_up_google_ads( $conds );
					}

				} elseif($ad_post_type == "normal_ads_only") {
					// normal_ads_only
					
					if ( !empty( $limit ) && !empty( $offset )) {
						// if limit & offset is not empty
						$data = $this->model->get_all_item_by_normal_ads( $conds, $limit, $offset );
					} else if ( !empty( $limit )) {
						// if limit is not empty
						$data = $this->model->get_all_item_by_normal_ads( $conds, $limit );
					} else {
						// if both are empty
						$data = $this->model->get_all_item_by_normal_ads( $conds );
					}
	
				}else{
					$data = $this->model->get_all_by_item( $conds, $limit, $offset )->result();
				}
				
			}

			
		} else {
			if ( !empty( $limit ) && !empty( $offset )) {
			// if limit & offset is not empty
			$data = $this->model->get_all_by( $conds, $limit, $offset )->result();


			} else if ( !empty( $limit )) {
				// if limit is not empty
				$data = $this->model->get_all_by( $conds, $limit )->result();

			} else {
				// if both are empty
				$data = $this->model->get_all_by( $conds )->result();

			}
		}

		$this->custom_response( $data );
	}

	/**
	 * Adds a post.
	 */
	function add_post()
	{
		// set the add flag for custom response
		$this->is_add = true;

		if ( !$this->is_valid( $this->create_validation_rules )) {
		// if there is an error in validation,
			
			return;
		}

		// get the post data
		$data = $this->post();

		if ( !$this->model->save( $data )) {
			$this->error_response( get_msg( 'err_model' ), 500);
		}

		// response the inserted object	
		$obj = $this->model->get_one( $data[$this->model->primary_key] );

		$this->custom_response( $obj );
	}

	/**
	 * Adds a post.
	 */
	/**
	 * Adds a post.
	 */
	function add_follow_post() 
	{
		
		// validation rules for create
		
		$rules = array(
			array(
	        	'field' => 'user_id',
	        	'rules' => 'required|callback_id_check[User]'
	        ),
	        array(
	        	'field' => 'followed_user_id',
	        	'rules' => 'required|callback_id_check[User]'
	        )
        );

		// validation
        if ( !$this->is_valid( $rules )) exit;

		$following_user_id = $this->post('user_id'); //Mary
		$followed_user_id = $this->post('followed_user_id');//Admin
		
		// prep data
        $data = array( 'user_id' => $following_user_id, 'followed_user_id' => $followed_user_id );


		if ( $this->Userfollow->exists( $data )) {
			
			if ( !$this->Userfollow->delete_by( $data )) {
				$this->error_response( get_msg( 'err_model' ), 500);
			} else {

			   	$conds_following['user_id'] = $following_user_id;
				$conds_followed['followed_user_id'] = $following_user_id;
				$conds_followed1['followed_user_id'] = $followed_user_id;

				//for user_id
				$total_follow_count = $this->Userfollow->count_all_by($conds_followed);
				$total_following_count = $this->Userfollow->count_all_by($conds_following);

				$user_data['follower_count'] = $total_follow_count;
				$user_data['following_count'] = $total_following_count;
				$user_id = $this->post('user_id');
				$this->User->save($user_data, $user_id);
				
				//for followed user_id
				$following_user['follower_count'] = $this->Userfollow->count_all_by($conds_followed1);
				$this->User->save($following_user, $followed_user_id);

			}

		} else {

			if ( !$this->Userfollow->save( $data )) {
				$this->error_response( get_msg( 'err_model' ), 500);
			} else {
				$conds_following['user_id'] = $following_user_id;
				$conds_followed['followed_user_id'] = $following_user_id;
				$conds_followed1['followed_user_id'] = $followed_user_id;

				//for user_id
				$total_follow_count = $this->Userfollow->count_all_by($conds_followed);
				$total_following_count = $this->Userfollow->count_all_by($conds_following);

				$user_data['follower_count'] = $total_follow_count;
				$user_data['following_count'] = $total_following_count;
				$user_id = $this->post('user_id');
				$this->User->save($user_data, $user_id);

				//for followed user_id
				$following_user['follower_count'] = $this->Userfollow->count_all_by($conds_followed1);
				$this->User->save($following_user, $followed_user_id);

			}

			$following_user_name = $this->User->get_one($following_user_id)->user_name;
			$message = $following_user_name . ' ' . get_msg('followed_you');

			$data['message'] = $message;
			$data['flag'] = 'follow';

			$user_ids = $this->post('followed_user_id');

			$devices = $this->Noti->get_all_device_in($user_ids)->result();

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

		}

		

		$obj = new stdClass;
		$obj->user_id = $followed_user_id;
		$user = $this->User->get_one( $obj->user_id );
		
		$user->followed_user_id = $followed_user_id;
		$user->following_user_id = $following_user_id;
		$this->ps_adapter->convert_user( $user );
		$this->custom_response( $user );
		

	}


	/**
	 * Adds a post.
	 */
	function update_put()
	{
		// set the add flag for custom response
		$this->is_update = true;

		if ( !$this->is_valid( $this->update_validation_rules )) {
		// if there is an error in validation,
			
			return;
		}

		// get the post data
		$data = $this->put();

		// get id
		$id = $this->get( $this->model->primary_key );

		if ( !$this->model->save( $data, $id )) {
		// error in saving, 
			
			$this->error_response( get_msg( 'err_model' ), 500);
		}

		// response the inserted object	
		$obj = $this->model->get_one( $id );

		$this->custom_response( $obj );
	}

	/**
	 * Delete the record
	 */
	function delete_delete()
	{
		// set the add flag for custom response
		$this->is_delete = true;

		if ( !$this->is_valid( $this->delete_validation_rules )) {
		// if there is an error in validation,
			
			return;
		}

		// get id
		$id = $this->get( $this->model->primary_key );

		if ( !$this->model->delete( $id )) {
		// error in saving, 
			
			$this->error_response( get_msg( 'err_model' ), 500);
		}

		$this->success_response( get_msg( 'success_delete' ), 200);
	}

	/**
	 * Claim Point From User
	 */
	function claim_point_post()
	{	
		$user_id = $this->post('user_id');
		$point = $this->post('point');

		if($user_id != "") {

			$user = $this->model->get_one($this->post('user_id'));

			//Get Existing Point
			$existing_total_point = $user->total_point;

			//Add Point to User
			$data['total_point'] = $existing_total_point + $point;

			//Point Update
			$this->model->save($data, $user_id);

			$obj = $this->model->get_one( $user_id );

			$this->custom_response( $obj );

		}
	}

	/**
	 * Custome Response return 404 if not data found
	 *
	 * @param      <type>  $data   The data
	 */
	function custom_response_noti( $data, $require_convert = true )
	{	
		if ( empty( $data )) {
		// if there is no data, return error

			$this->error_response($get_msg( 'record_not_found'), 404);

		} else if ( $require_convert ) {
		// if there is data, return the list
			if ( is_array( $data )) {
			// if the data is array
				foreach ( $data as $obj ) {
					// convert object for each obj
					if($this->get_login_user_id() != "") {
						$noti_user_data = array(
				        	"noti_id" => $obj->id,
				        	"user_id" => $this->get_login_user_id(),
				        	"device_token" => $this->post('device_token')
				    	);
						if ( !$this->Notireaduser->exists( $noti_user_data )) {
							$obj->is_read = 88;
						} else {
							$obj->is_read = 100;
						}
					} 

					$this->convert_object( $obj );
				}
			} else {
				if($this->get_login_user_id() != "") {
					$noti_user_data = array(
			        	"noti_id" => $data->id,
			        	"user_id" => $this->get_login_user_id(),
			        	"device_token" => $this->post('device_token')
			    	);
					if ( !$this->Notireaduser->exists( $noti_user_data )) {
						$data->is_read = 99;
					} else {
						$data->is_read = 100;
					}
				} 

				$this->convert_object( $data );
			}
		}
		$data = $this->ps_security->clean_output( $data );

		

		$this->response( $data );
	}


	/**
	* Download Item 
	*/
	function touch_item_post()
	{

		$user_id = $this->post('user_id');
		$item_id = $this->post('item_id');

		if($user_id != "") {

			$data['user_id'] 		= $user_id ;
			$data['item_id']   = $item_id ;

			$obj = $this->Item->get_one( $item_id  );
			$user_obj = $this->User->get_one( $user_id  );


			//if($user_obj->user_id != "") {

				if($obj->id != "") {

					if($this->Touch->save($data)) {
						
						//Need to update download_count at item table 
						$conds['item_id'] = $item_id;
						
						//Get Downlaod Count from Download Table
						$item_touch_count = $this->Touch->count_all_by($conds);

						//Update at Item Table
						$item_id = $conds['item_id'];
						$data_item['touch_count'] = $item_touch_count;
						$this->Item->save($data_item, $item_id);

						$this->success_response( get_msg( 'success_touch_count'), 200);


					} else {
						$this->error_response( get_msg( 'err_model' ), 500);
					}

				} else {
					$this->error_response( get_msg( 'invalid_item' ), 400);
				}
			// } else {
			// 	$this->error_response( get_msg( 'invalid_user' ));
			// }


		} else {
			$this->error_response( get_msg( 'user_id_required' ), 400);
		}

	}


	/**
  	* Get Delete History By Date Range.
  	*/
	function get_delete_history_post()
	{
	  	

		$start = $this->post('start_date');
		$end   = $this->post('end_date');
		$user_id = $this->post('user_id');
		  
		if ($start != "" && $start != '0') {
            $conds['start_date'] = $start;
        }

        if ($end != "" && $end != '0') {
            $conds['end_date']   = $end;
        }

		$conds['order_by'] = 1;
		$conds['order_by_field'] = "type_name";
		$conds['order_by_type'] = "desc";


		//$deleted_his_ids = $this->Delete_history->get_all_history_by($conds)->result();
		$deleted_his_ids = $this->Delete_history->get_all_by($conds)->result();

		$this->custom_response_history( $deleted_his_ids, $user_id, false );

	}

	/**
	 * Custome Response return 404 if not data found
	 *
	 * @param      <type>  $data   The data
	 */
	function custom_response_history( $data, $user_id, $require_convert = true )
	{

		$version_object = new stdClass; 
		$version_object->version_no           = $this->Version->get_one("1")->version_no; 
		$version_object->version_force_update = $this->Version->get_one("1")->version_force_update;
		$version_object->version_title        = $this->Version->get_one("1")->version_title;
		$version_object->version_message      = $this->Version->get_one("1")->version_message;
		$version_object->version_need_clear_data      = $this->Version->get_one("1")->version_need_clear_data;
		
		$app_object = new stdClass;
		$app_object->lat = $this->App_setting->get_one('app1')->lat;
		$app_object->lng = $this->App_setting->get_one('app1')->lng;
		$app_object->is_sub_location = $this->App_setting->get_one('app1')->is_sub_location;
		$app_object->is_thumb2x_3x_generate = $this->App_setting->get_one('app1')->is_thumb2x_3x_generate;
		$app_object->is_paid_app = $this->App_setting->get_one('app1')->is_paid_app;
		$app_object->is_block_user = $this->App_setting->get_one('app1')->is_block_user;
		$app_object->is_propertyby_subscription = $this->App_setting->get_one('app1')->is_propertyby_subscription;
		$app_object->max_img_upload_of_item = $this->App_setting->get_one('app1')->max_img_upload_of_item;	
		$app_object->ad_type = $this->App_setting->get_one('app1')->ad_type;
		$app_object->promo_cell_interval_no = $this->App_setting->get_one('app1')->promo_cell_interval_no;

		$item_config_object = new stdClass;
		$item_config = $this->Item_upload_config->get_one('1');
		$item_config_object->item_price_type_id = $item_config->item_price_type_id;
		$item_config_object->condition_of_item_id = $item_config->condition_of_item_id;
		$item_config_object->video = $item_config->video;
		$item_config_object->video_icon = $item_config->video_icon;
		$item_config_object->discount_rate_by_percentage = $item_config->discount_rate_by_percentage;
		$item_config_object->highlight_info = $item_config->highlight_info;
		$item_config_object->price_unit = $item_config->price_unit;
		$item_config_object->price_note = $item_config->price_note;
		$item_config_object->configuration = $item_config->configuration;
		$item_config_object->area = $item_config->area;
		$item_config_object->is_negotiable = $item_config->is_negotiable;
		$item_config_object->amenities = $item_config->amenities;
		$item_config_object->floor_no = $item_config->floor_no;
		$item_config_object->address = $item_config->address;

		$mobile_config_object = new stdClass;
		$mobile_config = $this->Mobile_config->get_one('mb1');
		$mobile_config_object->google_playstore_url = $mobile_config->google_playstore_url;
		$mobile_config_object->apple_appstore_url = $mobile_config->apple_appstore_url;
		$mobile_config_object->price_format = $mobile_config->price_format;
		$mobile_config_object->date_format = $mobile_config->date_format;
		$mobile_config_object->ios_appstore_id = $mobile_config->ios_appstore_id;
		$mobile_config_object->is_use_thumbnail_as_placeholder = $mobile_config->is_use_thumbnail_as_placeholder;
		$mobile_config_object->is_use_googlemap = $mobile_config->is_use_googlemap;
		$mobile_config_object->is_show_token_id = $mobile_config->is_show_token_id;
		$mobile_config_object->fb_key = $mobile_config->fb_key;
		$mobile_config_object->is_show_admob = $mobile_config->is_show_admob;
		$mobile_config_object->default_loading_limit = $mobile_config->default_loading_limit;
		$mobile_config_object->category_loading_limit = $mobile_config->category_loading_limit;
		$mobile_config_object->posted_by_loading_limit = $mobile_config->posted_by_loading_limit;
		$mobile_config_object->agent_loading_limit = $mobile_config->agent_loading_limit;
		$mobile_config_object->amenities_loading_limit = $mobile_config->amenities_loading_limit;
		$mobile_config_object->recent_item_loading_limit = $mobile_config->recent_item_loading_limit;
		$mobile_config_object->popular_item_loading_limit = $mobile_config->popular_item_loading_limit;
		$mobile_config_object->discount_item_loading_limit = $mobile_config->discount_item_loading_limit;
		$mobile_config_object->feature_item_loading_limit = $mobile_config->feature_item_loading_limit;
		$mobile_config_object->block_slider_loading_limit = $mobile_config->block_slider_loading_limit;
		$mobile_config_object->follower_item_loading_limit = $mobile_config->follower_item_loading_limit;
		$mobile_config_object->block_item_loading_limit = $mobile_config->block_item_loading_limit;
		$mobile_config_object->show_facebook_login = $mobile_config->show_facebook_login;
		$mobile_config_object->show_google_login = $mobile_config->show_google_login;
		$mobile_config_object->show_phone_login = $mobile_config->show_phone_login;
		$mobile_config_object->is_razor_support_multi_currency = $mobile_config->is_razor_support_multi_currency;
		$mobile_config_object->default_razor_currency = $mobile_config->default_razor_currency;
		$mobile_config_object->item_detail_view_count_for_ads = $mobile_config->item_detail_view_count_for_ads;
		$mobile_config_object->is_show_ads_in_item_detail = $mobile_config->is_show_ads_in_item_detail;
		$mobile_config_object->is_show_admob_inside_list = $mobile_config->is_show_admob_inside_list;
		$mobile_config_object->blue_mark_size = $mobile_config->blue_mark_size;
		$mobile_config_object->mile = $mobile_config->mile;
		$mobile_config_object->video_duration = $mobile_config->video_duration;
		$mobile_config_object->profile_image_size = $mobile_config->profile_image_size;
		$mobile_config_object->upload_image_size = $mobile_config->upload_image_size;
		$mobile_config_object->chat_image_size = $mobile_config->chat_image_size;
		$mobile_config_object->promote_first_choice_day = $mobile_config->promote_first_choice_day;
		$mobile_config_object->promote_second_choice_day = $mobile_config->promote_second_choice_day;
		$mobile_config_object->promote_third_choice_day = $mobile_config->promote_third_choice_day;
		$mobile_config_object->promote_fourth_choice_day = $mobile_config->promote_fourth_choice_day;
		$mobile_config_object->no_filter_with_location_on_map = $mobile_config->no_filter_with_location_on_map;
		$mobile_config_object->is_show_owner_info = $mobile_config->is_show_owner_info;
		$mobile_config_object->is_force_login = $mobile_config->is_force_login;
		$mobile_config_object->is_language_config = $mobile_config->is_language_config;

		$languages = array(
			array('language_code'=> 'en', 'country_code' => 'US', 'name' => 'English'),
			array('language_code'=> 'ar', 'country_code' => 'DZ', 'name' => 'Arabic'),
			array('language_code'=> 'hi', 'country_code' => 'IN', 'name' => 'Hindi'),
			array('language_code'=> 'de', 'country_code' => 'DE', 'name' => 'German'),
			array('language_code'=> 'es', 'country_code' => 'ES', 'name' => 'Spainish'),
			array('language_code'=> 'fr', 'country_code' => 'FR', 'name' => 'French'),
			array('language_code'=> 'id', 'country_code' => 'ID', 'name' => 'Indonesian'),
			array('language_code'=> 'it', 'country_code' => 'IT', 'name' => 'Italian'),
			array('language_code'=> 'ja', 'country_code' => 'JP', 'name' => 'Japanese'),
			array('language_code'=> 'ko', 'country_code' => 'KR', 'name' => 'Korean'),
			array('language_code'=> 'ms', 'country_code' => 'MY', 'name' => 'Malay'),
			array('language_code'=> 'pt', 'country_code' => 'PT', 'name' => 'Portuguese'),
			array('language_code'=> 'ru', 'country_code' => 'RU', 'name' => 'Russian'),
			array('language_code'=> 'th', 'country_code' => 'TH', 'name' => 'Thai'),
			array('language_code'=> 'tr', 'country_code' => 'TR', 'name' => 'Turkish'),
			array('language_code'=> 'zh', 'country_code' => 'CN', 'name' => 'Chinese'),
		);

		$default_language = array();
		$exclude_language = array();
		$exclude_lang = explode(',' ,trim($mobile_config->exclude_language));
		$default_lang = trim($mobile_config->default_language);

		foreach($languages as $language){
			if(in_array($language['language_code'], $exclude_lang)){
				$exclude_language[] = array('language_code' => $language['language_code'], 'country_code' => $language['country_code'], 'name' => $language['name']);
			}

			if($language['language_code'] == $default_lang){
				$default_language = array('language_code' => $language['language_code'], 'country_code' => $language['country_code'], 'name' => $language['name']);
			}
		}

		$mobile_config_object->default_language = $default_language;
		$mobile_config_object->exclude_language = $exclude_language;

		$is_banned = $this->User->get_one($user_id)->is_banned;
		$user_object = new stdClass;
		$user_object->user_status = $this->User->get_one($user_id)->status;

		$user_data = $this->User->get_one($user_id);
		//($user_data->status);die;

		if ($user_id == "nologinuser") {
			$user_object->user_status = "nologinuser";
		}elseif ($user_data->is_empty_object == 1 ) {
			$user_object->user_status = "deleted";
		}elseif ($is_banned == 1 ) {
			$user_object->user_status = "banned";
		}elseif ($user_object->user_status == 1) {
			$user_object->user_status = "active";
		}elseif ($user_object->user_status == 2) {
			$user_object->user_status = "pending";
		}elseif ($user_object->user_status == 0) {
			$user_object->user_status = "unpublished";
		}

		// for android type at in app purchase item promote
		$conds_android['type'] = "Android";
		$conds_android['status'] = "1";
		$app_purchased_count_android = $this->In_app_purchase->count_all_by($conds_android);

		if ($conds_android['type'] = "Android") {
			for ($i=0; $i <  $app_purchased_count_android ; $i++) { 
				$app_purchased_data_android = $this->In_app_purchase->get_all_by($conds_android)->result();
     				//print_r($app_purchased_data[0]->id);die;
     				$in_app_purchased_prd_id_android .= "" . $app_purchased_data_android[$i]->in_app_purchase_prd_id . "@@" . $app_purchased_data_android[$i]->day .  "##" ;

			}
				
		}

		// for ios type at in app purchase item promote
		$conds_ios['type'] = "IOS";
		$conds_ios['status'] = "1";
		$app_purchased_count_ios = $this->In_app_purchase->count_all_by($conds_ios);

		if ($conds_ios['type'] = "IOS") {
			for ($i=0; $i <  $app_purchased_count_ios ; $i++) { 
				$app_purchased_data_ios = $this->In_app_purchase->get_all_by($conds_ios)->result();
     				//print_r($app_purchased_data[0]->id);die;
     				$in_app_purchased_prd_id_ios .= "" . $app_purchased_data_ios[$i]->in_app_purchase_prd_id . "@@" . $app_purchased_data_ios[$i]->day .  "##" ;

			}
				
		}


		// for android type at in app purchase package
		$conds_pkg_android['type'] = "Android";
		$conds_pkg_android['status'] = "1";
		$package_app_purchased_count_android = $this->Package->count_all_by($conds_android);
		//print_r($package_app_purchased_count_android);die;

		if ($conds_pkg_android['type'] = "Android") {
			for ($i=0; $i <  $package_app_purchased_count_android ; $i++) { 

				$package_app_purchased_data_android = $this->Package->get_all_by($conds_pkg_android)->result();
     			
     				$package_in_app_purchased_prd_id_android .= "" . $package_app_purchased_data_android[$i]->package_in_app_purchased_prd_id .  "##" ;

			}
				
		}

		// for ios type at in app purchase package
		$conds_pkg_ios['type'] = "IOS";
		$conds_pkg_ios['status'] = "1";
		$package_app_purchased_count_ios = $this->Package->count_all_by($conds_pkg_ios);

		if ($conds_pkg_ios['type'] = "IOS") {
			for ($i=0; $i <  $package_app_purchased_count_ios ; $i++) { 
				$package_app_purchased_data_ios = $this->Package->get_all_by($conds_pkg_ios)->result();
     				//print_r($app_purchased_data[0]->id);die;
     				$package_in_app_purchased_prd_id_ios .= "" . $package_app_purchased_data_ios[$i]->package_in_app_purchased_prd_id .  "##" ;

			}
				
		}

		// for default currency

		$conds_cur['is_default'] = 1;

		$currency_object = new stdClass;
		$currency_object->id = $this->Currency->get_one_by($conds_cur)->id;
		$currency_object->currency_short_form = $this->Currency->get_one_by($conds_cur)->currency_short_form;
		$currency_object->currency_symbol = $this->Currency->get_one_by($conds_cur)->currency_symbol;
		$currency_object->status = $this->Currency->get_one_by($conds_cur)->status;
		$currency_object->is_default = $this->Currency->get_one_by($conds_cur)->is_default;
		$currency_object->added_date = $this->Currency->get_one_by($conds_cur)->added_date;

		$final_data = new stdClass;
		$final_data->version = $version_object;
		$final_data->app_setting = $app_object;
		$final_data->item_upload_config = $item_config_object;
		$final_data->mobile_config_setting = $mobile_config_object;
		$final_data->user_info = $user_object;
		$final_data->oneday = $this->Paid_config->get_one("pconfig1")->amount;
		$final_data->default_currency = $currency_object;
		$final_data->currency_symbol = $this->Paid_config->get_one("pconfig1")->currency_symbol;
		$final_data->currency_short_form = $this->Paid_config->get_one("pconfig1")->currency_short_form;
		$final_data->stripe_publishable_key = $this->Paid_config->get_one("pconfig1")->stripe_publishable_key;
		$final_data->stripe_enabled = $this->Paid_config->get_one("pconfig1")->stripe_enabled;
		$final_data->paypal_enabled = $this->Paid_config->get_one("pconfig1")->paypal_enabled;
		$final_data->razor_enabled = $this->Paid_config->get_one("pconfig1")->razor_enabled;
		$final_data->razor_key = $this->Paid_config->get_one("pconfig1")->razor_key;
		$final_data->offline_enabled = $this->Paid_config->get_one("pconfig1")->offline_enabled;
		$final_data->offline_message = $this->Paid_config->get_one("pconfig1")->offline_message;
		$final_data->paystack_enabled = $this->Paid_config->get_one("pconfig1")->paystack_enabled;
		$final_data->paystack_key = $this->Paid_config->get_one("pconfig1")->paystack_key;
		$final_data->in_app_purchased_enabled = $this->Paid_config->get_one("pconfig1")->in_app_purchased_enabled;
		$final_data->in_app_purchased_prd_id_android = $in_app_purchased_prd_id_android;
		$final_data->in_app_purchased_prd_id_ios = $in_app_purchased_prd_id_ios;
		$final_data->package_in_app_purchased_prd_id_android = $package_in_app_purchased_prd_id_android;
		$final_data->package_in_app_purchased_prd_id_ios = $package_in_app_purchased_prd_id_ios;
		$final_data->delete_history = $data;
		

		$final_data = $this->ps_security->clean_output( $final_data );


		$this->response( $final_data );
	}


	/**
	 * Get all or Get One
	 */
	function get_item_by_followuser_post()
	{

		// add flag for default query
		$this->is_get = true;
		// get limit & offset
		$limit = $this->get( 'limit' );
		$offset = $this->get( 'offset' );

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge( $default_conds, $user_conds );
		$conds['user_id'] = $this->get( 'login_user_id' );

		$userfollow_data = $this->Userfollow->get_all_by($conds)->result();
		//print_r(count( $userfollow_data ));die;

		if ( count( $userfollow_data ) > 0 ) {
			foreach ($userfollow_data as $userfollow) {
		  	$result .= $userfollow->followed_user_id .",";
		  
		}

		if ( !empty( $limit ) && !empty( $offset )) {
			// if limit & offset is not empty
			//$data = $this->model->get_wallpaper_delete_by_userid( $conds, $limit, $offset )->result();
			$data = $this->model->get_all_by( $conds, $limit, $offset )->result();
		} else if ( !empty( $limit )) {
			// if limit is not empty

			//$data = $this->model->get_wallpaper_delete_by_userid( $conds, $limit )->result();
			$data = $this->model->get_all_by( $conds, $limit )->result();
		} else {
			// if both are empty
			//$data = $this->model->get_wallpaper_delete_by_userid( $conds )->result();
			$data = $this->model->get_all_by( $conds )->result();
		}


		$resultfollow = rtrim($result,",");
		$followuser = explode(",", $resultfollow);

		
		$conds['followuser'] = $followuser;

		/* For User Block */
		if($this->App_setting->get_one('app1')->is_block_user == "1"){
			//user block check with login_user_id
			$conds_login_block['from_block_user_id'] = $this->get_login_user_id();
			$login_block_count = $this->Block->count_all_by($conds_login_block);
			//print_r($login_block_count);die;

			// user blocked existed by login user
			if ($login_block_count > 0) {
				// get the blocked user by login user
				$to_block_user_datas = $this->Block->get_all_by($conds_login_block)->result();

				foreach ( $to_block_user_datas as $to_block_user_data ) {

					$to_block_user_id .= "'" .$to_block_user_data->to_block_user_id . "',";
			
				}

				// get block user's item

				$result_users = rtrim($to_block_user_id,',');
				$conds_user['added_user_id'] = $result_users;

				$item_users = $this->Item->get_all_in_item( $conds_user )->result();

				foreach ( $item_users as $item_user ) {

					$id .= $item_user->id .",";
				
				}

				// get all item without block user's item

				$result_items = rtrim($id,',');
				$item_id = explode(",", $result_items);
				//print_r($item_id);die;
				//$conds['id'] = $result_items;

			}
		}
		/* For Item Report */

		//item report check with login_user_id
		$conds_report['reported_user_id'] = $this->get_login_user_id();
		$reported_data_count = $this->Itemreport->count_all_by($conds_report);

		// item reported existed by login user
		if ($reported_data_count > 0) {
			// get the reported item data
			$item_reported_datas = $this->Itemreport->get_all_by($conds_report)->result();

			foreach ( $item_reported_datas as $item_reported_data ) {

				$item_ids .= "'" .$item_reported_data->item_id . "',";
		
			}

			// get block user's item

			$result_reports = rtrim($item_ids,',');
			$conds_item['id'] = $result_reports;

			$item_reports = $this->Item->get_all_in_report( $conds_item )->result();

			foreach ( $item_reports as $item_report ) {

				$ids .= $item_report->id .",";
			
			}

			// get all item without block user's item

			$result_items = rtrim($ids,',');
			$reported_item_id = explode(",", $result_items);
			//$conds['id'] = $result_items;
		}

		$conds['item_id'] = $item_id;
		$conds['reported_item_id'] = $reported_item_id;
		$conds['status'] = 1;

		$conds['item_location_city_id'] = $this->post('item_location_id');
		$conds['item_location_township_id'] = $this->post('item_location_township_id');
		
		$item_list = $this->Item->get_all_item_by_followuser($conds, $limit, $offset)->result();
		
		if($item_list == "") {
			$this->error_response(get_msg( 'record_not_found'), 404);
		} else {
			// $this->ps_adapter->convert_item( $item_list );
			$this->custom_response( $item_list );
		}
		

		} else {
			$this->error_response(get_msg( 'record_not_found'), 404);

		}
		

		
	}

	/**
	 * Adds a post.
	 */
	function add_accept_offer_post()
	{
		// set the add flag for custom response
		$this->is_add = true;

		if ( !$this->is_valid( $this->create_validation_rules )) {
		// if there is an error in validation,
			
			return;
		}

		// get the post data
		$data = $this->post();
		$user_id = $data['from_user_id'];
		$users = global_user_check($user_id);

		$user_id = $data['to_user_id'];
		$users = global_user_check($user_id);
		
		if ( $this->model->exists( $data ) ) {

			//existing accept user
			$this->error_response( get_msg( 'already_accept_user' ), 503);
			
		} else {

			if ( $this->model->save( $data )) {
				// response the inserted object	
				$obj = $this->model->get_one( $data[$this->model->primary_key] );

				if ( $obj->item_id != " ") {
					$id = $obj->item_id;
					$item_data = array(
		        		"is_sold_out" => 1
		        	
		    		);

					$this->Item->save( $item_data, $id );

					$item_data = $this->Item->get_one($id);
					$this->ps_adapter->convert_item($item_data);
					$this->custom_response( $item_data );
				}
			
 			}
		
		}
	}

	/**
	 * Adds a post.
	 */
	function add_rating_post()
	{
		// set the add flag for custom response
		$this->is_add = true;

		if ( !$this->is_valid( $this->create_validation_rules )) {
		// if there is an error in validation,
			
			return;
		}

		// get the post data
		$data = $this->post();
		$from_user_id = $data['from_user_id'];
		$to_user_id = $data['to_user_id'];

		$user_id = $data['from_user_id'];
		$users = global_user_check($user_id);

		$user_id = $data['to_user_id'];
		$users = global_user_check($user_id);
		
		$conds['from_user_id'] = $from_user_id;
		$conds['to_user_id'] = $to_user_id;
		// print_r($conds);die;
		
		$id = $this->model->get_one_by($conds)->id;

		$rating = $data['rating'];
		if ( $id ) {

			$this->model->save( $data, $id );

			// response the inserted object	
			$obj = $this->model->get_one( $id );
		} else {
			$this->model->save( $data );

			// response the inserted object	
			$obj = $this->model->get_one( $data[$this->model->primary_key] );
		}

        //noti send to to_user_id when reviewed

		$devices = $this->Noti->get_all_device_in($to_user_id)->result();

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

		$data['message'] = htmlspecialchars_decode($this->post( 'title' ));
		$data['rating'] = $this->post('rating');
		$data['flag'] = 'review';
		$data['review_user_id'] = $this->get_login_user_id();

       	$status = send_android_fcm( $device_ids, $data, $platform_names );
		
		//Need to update rating value at product
		$conds_rating['to_user_id'] = $obj->to_user_id;

		$total_rating_count = $this->Rate->count_all_by($conds_rating);
		$sum_rating_value = $this->Rate->sum_all_by($conds_rating)->result()[0]->rating;

		if($total_rating_count > 0) {
			$total_rating_value = number_format((float) ($sum_rating_value  / $total_rating_count), 1, '.', '');
		} else {
			$total_rating_value = 0;
		}

		$user_data['overall_rating'] = $total_rating_value;
		$this->User->save($user_data, $obj->to_user_id);

		// send email to to_user_id
		$user_data = $this->User->get_one($to_user_id);
		$user_email = $user_data->user_email;
		$email_verify = $user_data->email_verify;


		if($user_email && $email_verify == 1){
			$this->load->library( 'PS_Mail' );

			$subject = htmlspecialchars_decode($this->post( 'title' ));
			
			if ( !send_rating_email( $to_user_id, $subject, $from_user_id )) {
				$this->set_flash_msg( 'error', get_msg( 'verify_email_not_send_user' ) . ' ' .  $user_data->user_name);
			}
		}
		
		//$obj_item = $this->Product->get_one( $obj->product_id );
		$obj_rating = $this->Rate->get_one( $obj->id );

		$this->ps_adapter->convert_rating( $obj_rating);
		$this->custom_response( $obj_rating );
	}

	// get rating list by user id

	function rating_user_post()
	{
		$this->is_add = true;

		if ( !$this->is_valid( $this->create_validation_rules )) {
		// if there is an error in validation,
			
			return;
		}

		// get the post data
		$data = $this->post();
		$user_id = $data['user_id'];
		$conds['to_user_id'] = $user_id;

		$users = $this->Rate->get_all_by($conds)->result();
		//print_r($users);die;
		if(count($users) > 0) {
			foreach ($users as $user) {
				$to_user_id = $user->to_user_id;
				$id .= "'" . $user->id . "',";
			}

			$result = rtrim($id,',');
			//print_r($result);die;

			if ($user_id == $to_user_id) {

				$conds1['id'] = $result;
				//print_r($conds1['id']);die;

				$limit = $this->get( 'limit' );
				$offset = $this->get( 'offset' );

				$obj = $this->Rate->get_all_in_rating( $conds1, $limit, $offset )->result();
				//print_r($obj);die;

				$this->ps_adapter->convert_rating( $obj );

				$this->custom_response( $obj );
			

			}
		} else {
		
			$this->error_response(get_msg( 'record_not_found'), 503);
		}
		
	}

	/**
	 * Get all or Get One
	 */
	function get_following_user_get()
	{

		// add flag for default query
		$this->is_get = true;

		// get limit & offset
		$limit = $this->get( 'limit' );
		$offset = $this->get( 'offset' );

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge( $default_conds, $user_conds );
		$conds['followed_user_id'] = $this->get( 'login_user_id' );

		$userfollow_data = $this->Userfollow->get_all_by($conds)->result();
		//print_r( $userfollow_data );die;

		if ( count( $userfollow_data ) > 0 ) {
			foreach ($userfollow_data as $userfollow) {
		  	$result .= "'".$userfollow->user_id ."'" .",";
		  
		}

		if ( !empty( $limit ) && !empty( $offset )) {
		// if limit & offset is not empty
			//$data = $this->model->get_wallpaper_delete_by_userid( $conds, $limit, $offset )->result();
			$data = $this->model->get_all_by( $conds, $limit, $offset )->result();
		} else if ( !empty( $limit )) {
		// if limit is not empty

			//$data = $this->model->get_wallpaper_delete_by_userid( $conds, $limit )->result();
			$data = $this->model->get_all_by( $conds, $limit )->result();
		} else {
		// if both are empty
			//$data = $this->model->get_wallpaper_delete_by_userid( $conds )->result();
			$data = $this->model->get_all_by( $conds )->result();
		}


		$followuser = rtrim($result,",");

		$conds['followuser'] = $followuser;

		$obj = $this->User->get_all_follower_by_user($conds, $limit, $offset)->result();
		
		$this->ps_adapter->convert_follow_user_list( $obj );
		$this->custom_response( $obj );

		} else {
			$this->error_response(get_msg( 'record_not_found'), 404);

		}
	}

	function get_offline_payment_get()
	{
		
		// add flag for default query
		$this->is_get = true;

		// get limit & offset
		$limit = $this->get( 'limit' );
		$offset = $this->get( 'offset' );

		if ( $limit ) {
			unset( $conds['limit']);
		}

		if ( $offset ) {
			unset( $conds['offset']);
		}
		
		if ( !empty( $limit ) && !empty( $offset )) {
		// if limit & offset is not empty
			$data = $this->model->get_all( $limit, $offset )->result();
		} else if ( !empty( $limit )) {
		// if limit is not empty

			$data = $this->model->get_all( $limit )->result();
		} else {
		// if both are empty
			$data = $this->model->get_all( )->result();
		}
		
		$this->custom_response_offline( $data );
	
	}

	/**
	 * Custome Response return 404 if not data found
	 *
	 * @param      <type>  $data   The data
	 */
	function custom_response_offline( $data, $require_convert = true )
	{
		$final_data = new stdClass;
		$final_data->message = $this->Paid_config->get_one("pconfig1")->offline_message;
		foreach ($data as $d) {
			//set default icon
			$d->default_icon = $this->get_default_photo( $d->id, 'offline_icon' );
		}
		$final_data->offline_payment = $data;
		$final_data = $this->ps_security->clean_output( $final_data );
		$this->response( $final_data );
	}

	/**
	 * Get reported item list by login user id
	 */
	function get_reported_item_by_loginuser_get()
	{
		// add flag for default query
		$this->is_get = true;

		// get limit & offset
		$limit = $this->get( 'limit' );
		$offset = $this->get( 'offset' );

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge( $default_conds, $user_conds );
		$conds_report['reported_user_id'] = $this->get( 'login_user_id' );

		$reported_datas = $this->Itemreport->get_all_by($conds_report)->result();
		//print_r(count( $reported_datas ));die;

		if ( count( $reported_datas ) > 0 ) {
			foreach ($reported_datas as $reported_data) {
		  	$result .=  "'" .$reported_data->item_id ."',";

		}

		if ( !empty( $limit ) && !empty( $offset )) {
		// if limit & offset is not empty
			//$data = $this->model->get_wallpaper_delete_by_userid( $conds, $limit, $offset )->result();
			$data = $this->model->get_all_by( $conds, $limit, $offset )->result();
		} else if ( !empty( $limit )) {
		// if limit is not empty

			//$data = $this->model->get_wallpaper_delete_by_userid( $conds, $limit )->result();
			$data = $this->model->get_all_by( $conds, $limit )->result();
		} else {
		// if both are empty
			//$data = $this->model->get_wallpaper_delete_by_userid( $conds )->result();
			$data = $this->model->get_all_by( $conds )->result();
		}


		$reported_item = rtrim($result,",");

		$conds['id'] = $reported_item;
		$conds['status'] = 1;

		$item_list = $this->Item->get_all_in_reported_item($conds, $limit, $offset)->result();
		// print_r($item_list);die;
		
		//$this->ps_adapter->convert_item( $item_list );
		$this->custom_response( $item_list );

		} else {
			$this->error_response(get_msg( 'record_not_found'), 404);

		}
		

		
	}

	
	/**
	 * Get sold out item list by login user id
	 */
	function get_sold_out_item_by_loginuser_get()
	{
		// add flag for default query
		$this->is_get = true;
		
		// get limit & offset
		$limit = $this->get( 'limit' );
		$offset = $this->get( 'offset' );

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge( $default_conds, $user_conds );
		$conds['added_user_id'] = $this->get( 'login_user_id' );
		
		$conds['is_sold_out'] = 1;
		$conds['status'] = 1;

		if ( !empty( $limit ) && !empty( $offset )) {
			// if limit & offset is not empty
			$data = $this->model->get_item_by( $conds, $limit, $offset )->result();
		} else if ( !empty( $limit )) {
			// if limit is not empty
			$data = $this->model->get_item_by( $conds, $limit )->result();
		} else {
			// if both are empty
			$data = $this->model->get_item_by( $conds )->result();
		}

		$this->custom_response( $data );
		
	}

	
}
