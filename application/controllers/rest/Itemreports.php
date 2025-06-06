<?php
require_once( APPPATH .'libraries/REST_Controller.php' );

/**
 * REST API for News
 */
class Itemreports extends API_Controller
{

	/**
	 * Constructs Parent Constructor
	 */
	function __construct()
	{
		parent::__construct( 'Itemreport' );
	}

	/**
	 * Default Query for API
	 * @return [type] [description]
	 */
	function default_conds()
	{
		$conds = array();

		if ( $this->is_get ) {
		// if is get record using GET method

		}

		return $conds;
	}
        
        // function add_post() {

	// 	$rules = array(
	// 		array(
	//         	'field' => 'item_id',
	//         	'rules' => 'required|callback_id_check[Item]'
	//         ),
	//         array(
	//         	'field' => 'reported_user_id',
	//         	'rules' => 'required|callback_id_check[User]'
	// 		)
    //     );
	// 	if ( !$this->is_valid( $rules )) exit;

	// 	$item_id = $this->post('item_id');
	// 	$added_user_id = $this->Item->get_one($item_id)->added_user_id;
	// 	$reported_user_id = $this->post('reported_user_id');
		
	// 	if ($added_user_id == $reported_user_id) {
	// 		$this->success_response( get_msg( 'unable_to_report'), 200);
	// 	}else{
	// 		$report_data = array(
	// 			"item_id" => $item_id, 
	// 			"reported_user_id" => $this->post('reported_user_id'),
	// 			"text_note" => $this->post('text_note'),
	// 			"reported_status" => 1
	//   		);
	

  	// 		$report_data_existed = $this->Itemreport->get_all_by($report_data)->result();

	// 		if (empty($report_data_existed)) {
	// 			$this->Itemreport->save($report_data);

  	// 			$this->success_response( get_msg( 'success_item_report'), 201);
	// 		}else{
	// 			$this->error_response( get_msg( 'already_report'), 503);
	// 		}

	// 	}

	// }

	function add_post() {

		$rules = array(
			array(
				'field' => 'item_id',
				'rules' => 'required|callback_id_check[Item]'
			),
			array(
				'field' => 'reported_user_id',
				'rules' => 'required|callback_id_check[User]'
			)
		);
		if (!$this->is_valid($rules)) exit;
	
		$item_id = $this->post('item_id');
		$added_user_id = $this->Item->get_one($item_id)->added_user_id;
		$reported_user_id = $this->post('reported_user_id');
	
		if ($added_user_id == $reported_user_id) {
			$this->success_response(get_msg('unable_to_report'), 200);
		} else {
			$report_data = array(
				"item_id" => $item_id,
				"reported_user_id" => $this->post('reported_user_id'),
				"text_note" => $this->post('text_note'),
				"reported_status" => 1
			);
	
			$report_data_existed = $this->Itemreport->get_all_by($report_data)->result();
	
			if (empty($report_data_existed)) {
				$this->Itemreport->save($report_data);
	
				// Send email notification
				$user_id = $this->post('reported_user_id');
				$event_id = $item_id;
				if (send_report_email($user_id, $event_id)) {
					$this->success_response(get_msg('success_item_report'), 201);
				} else {
					$this->error_response(get_msg('error_sending_email'), 500); // Adjust the error code and message as per your need
				}
	
				$this->success_response(get_msg('success_item_report'), 201);
			} else {
				$this->error_response(get_msg('already_report'), 503);
			}
		}
	}
	



	/**
	 * Convert Object
	 */
	function convert_object( &$obj )
	{

		// call parent convert object
		parent::convert_object( $obj );

	}

}