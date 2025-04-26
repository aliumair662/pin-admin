<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Item Itemtype Controller
 */
class Item_location_townships extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'ITEM LOCATION TOWNSHIPS' );
		$this->load->library('uploader');
		$this->load->library('csvimport');
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
		$this->data['rows_count'] = $this->Item_location_township->count_all_by( $conds );
		
		// get Item locations
		$this->data['item_location_townships'] = $this->Item_location_township->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );
		// load index logic
		parent::index();
	}

	/**
	 * Searches for the first match.
	 */
	function search() {
		

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'township_search' );
		
		if($this->input->post('submit') != NULL ){

			if($this->input->post('searchterm') != "") {
				$conds['searchterm'] = $this->input->post('searchterm');
				$this->data['searchterm'] = $this->input->post('searchterm');
				$this->session->set_userdata(array("searchterm" => $this->input->post('searchterm')));
			} else {
				
				$this->session->set_userdata(array("searchterm" => NULL));
			}

			if($this->input->post('city_id') != ""  || $this->input->post('city_id') != '0') {
				$conds['city_id'] = $this->input->post('city_id');
				$this->data['city_id'] = $this->input->post('city_id');
				
				$this->session->set_userdata(array("city_id" => $this->input->post('city_id')));
				
			} else {
				$this->session->set_userdata(array("city_id" => NULL ));
			}
			

		} else {

			//read from session value
			if($this->session->userdata('searchterm') != NULL){
				$conds['searchterm'] = $this->session->userdata('searchterm');
				$this->data['searchterm'] = $this->session->userdata('searchterm');
			}

			if($this->session->userdata('city_id') != NULL){
				$conds['city_id'] = $this->session->userdata('city_id');
				$this->data['city_id'] = $this->session->userdata('city_id');
			}

		}

		// // condition with search term
		// $conds = array( 'keyword' => $this->searchterm_handler( $this->input->post( 'searchterm' )),
		// 				'city_id' => $this->searchterm_handler( $this->input->post('city_id')) );
		// no publish filter
		$conds['no_publish_filter'] = 1;
		$conds['order_by'] = 1;
		$conds['order_by_field'] = "added_date";
		$conds['order_by_type'] = "desc";


		// pagination
		$this->data['rows_count'] = $this->Item_location_township->count_all_by( $conds );

		// search data
		$this->data['item_location_townships'] = $this->Item_location_township->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );
		
		// load add list
		parent::search();
	}

	/**
	 * Create new one
	 */
	function add() {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'location_township_add' );

		// call the core add logic
		parent::add();
	}

	/**
	 * Update the existing one
	 */
	function edit( $id ) {
		// print_r($id);die;

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'township_edit' );

		// load user
		$this->data['item_location_township'] = $this->Item_location_township->get_one( $id );

		// call the parent edit logic
		parent::edit( $id );
	}

	/**
	 * Saving Logic
	 * 1) upload image
	 * 2) save Itemlocation
	 * 3) save image
	 * 4) check transaction status
	 *
	 * @param      boolean  $id  The user identifier
	 */
	function save( $id = false ) {
		// start the transaction
		$this->db->trans_start();
		
		/** 
		 * Insert Itemlocation Records 
		 */
		$data = array();

		//city id
		if ($this->has_data( 'city_id' )) {
			$data['city_id'] = $this->get_data('city_id');
		}
        
		// prepare name
		if ( $this->has_data( 'township_name' )) {
			$data['township_name'] = $this->get_data( 'township_name' );
		}

		// prepare ordering
		if ( $this->has_data( 'ordering' )) {
			$data['ordering'] = $this->get_data( 'ordering' );
		}

		// prepare lat
		if ( $this->has_data( 'lat' )) {
			$data['lat'] = $this->get_data( 'lat' );
		}

		// prepare lng
		if ( $this->has_data( 'lng' )) {
			$data['lng'] = $this->get_data( 'lng' );
		}

		$data['status'] = 1;
       // print_r($data);die;
		// save Itemlocation
		if ( ! $this->Item_location_township->save( $data, $id )) {
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
				
				$this->set_flash_msg( 'success', get_msg( 'success_township_edit' ));
			} else {
			// if user id is false, show success_edit message

				$this->set_flash_msg( 'success', get_msg( 'success_township_add' ));
			}
		}

		redirect( $this->module_site_url());
	}

	/**
	 * Delete the record
	 * 1) delete Itemtype
	 * 2) delete image from folder and table
	 * 3) check transactions
	 */
	function delete( $id ) {

		// start the transaction
		$this->db->trans_start();

		// check access
		$this->check_access( DEL );
		
		// delete location and images
		if ( !$this->ps_delete->delete_location_township( $id )) {

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
        	
			$this->set_flash_msg( 'success', get_msg( 'success_location_delete' ));
		}
		
		redirect( $this->module_site_url());
	}

	
	/**
	 * upload csv file
	 */
	function upload() {
		
		if ( $this->is_POST()) {
		
			$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
			
        	if(!empty($_FILES['file']['name']) && strtolower($extension) == "csv") {
				
        	 	$upload_data = $this->uploader->upload($_FILES);
				
				if (!isset($upload_data['error'])) {
					foreach ($upload_data as $upload) {
						
						$file_data = $this->upload->data();
		            	$file_path =  './uploads/'.$file_data['file_name'];

						// Open file in read mode
						$csvFile = fopen($_FILES['file']['tmp_name'], 'r');
						$data = fgetcsv($csvFile); // Skipping header row
						
		            	if ($this->csvimport->get_array($file_path)) {
			                $i = 0; $s = 0; $f=0; $fail_records = "";

			                while (($row = fgetcsv($csvFile)) !== FALSE) {
								
			                    if( $row[0] != '') {
			                     	//Get location township data 
									$township_name =  ucwords(trim($row[0]));
									$city_name =  ucwords(trim($row[1]));
									$ordering =  $row[2];
									$lat =  $row[3];
									$lng =  $row[4];
									$status =  $row[5];
									
									// get city id
									$conds1['name'] = htmlspecialchars_decode($city_name);
									$city_id = $this->Itemlocation->get_one_by($conds1)->id;

									if($city_id != ''){
										$data = array(
											'city_id'=> htmlspecialchars_decode($city_id),
											'township_name'=> htmlspecialchars_decode($township_name),
											'ordering' => htmlspecialchars_decode($ordering),
											'lat' => htmlspecialchars_decode($lat),
											'lng' => htmlspecialchars_decode($lng),
											'status' => htmlspecialchars_decode($status) 
										   );
	
										// check township name is existed or not
										$conds['township_name'] = htmlspecialchars_decode($township_name);
										$conds['city_id'] = htmlspecialchars_decode($city_id);
										$conds['no_publish_filter'] = 1;
										
										if( $this->Item_location_township->count_all_by($conds) > 0 ) {
											$f++; $i++;
											$fail_records .= " - <b>" . $row[0] . '</b> ' . get_msg( 'township_existed' ) . ", ";
											continue;
										}
	
										if($this->Item_location_township->save($data)) {
												$s++;
										} else {
											$f++;
											$fail_records .= " - <b>" . $row[0] . '</b> ' . get_msg( 'because_db_err' ) . ", ";
										}
									}else{
										$f++;
										$fail_records .= " - <b>" . $row[1] . '</b> - '. $row[1] . get_msg( 'because_noexit_city' ) . ", ";
									}
									
			               		}else {
									//location city Missing
									$f++;
									$fail_records .= " - <b>" . $row[0] . '</b> ' . get_msg( 'because_miss_township' ) . ", ";
								}
								$i++;
			            	}
							$result_str = get_msg( 'total_township' ) . ' ' . $i . "<br>";
							$result_str .= get_msg( 'success_total_township' ) . ' ' . $s . "<br>";
							$result_str .= get_msg( 'fail_total_township' ) . ' ' . $f . " " . $fail_records;

							$this->data['message'] = $result_str;
							$this->set_flash_msg( 'success', $result_str);

							redirect( $this->module_site_url());

						}else {
			            	$this->set_flash_msg( 'error', get_msg( 'something_wrong_upload' ));
			            	redirect( $this->module_site_url());
			            }
					}
				}else {
					$this->set_flash_msg( 'error', $upload_data['error']);
					redirect( $this->module_site_url());
				}
			} else {
				$this->set_flash_msg( 'error',  get_msg( 'pls_upload_csv' ));
				redirect( $this->module_site_url());
			}
        }else{
			redirect( $this->module_site_url());
		}		
	}
	
	/**
	 * Determines if valid input.
	 *
	 * @return     boolean  True if valid input, False otherwise.
	 */
	function is_valid_input( $id = 0 ) {

		$rule = 'required|callback_is_valid_name['. $id  .']';

		$this->form_validation->set_rules( 'township_name', get_msg( 'township_name' ), $rule);

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
	function is_valid_name( $id = 0 )
	{		
		 $conds['township_name'] = $township_name;

		 	if ( trim(strtolower( $this->Item_location_township->get_one( $id )->township_name ) ) == trim(strtolower( $township_name )) ) {
			// if the name is existing name for that user id,
				return true;
			} else if ( $this->Item_location_township->exists( ($conds ))) {
			// if the name is existed in the system,
				$this->form_validation->set_message('is_valid_name', get_msg( 'err_dup_name' ));
				return false;
			}
			return true;
	}

	/**
	 * Check location name via ajax
	 *
	 * @param      boolean  $cat_id  The cat identifier
	 */
	function ajx_exists( $id = false )
	{

		// get location name

		$township_name = $_REQUEST['township_name'];

		if ( $this->is_valid_name( $township_name, $id )) {

		// if the location name is valid,
			
			echo "true";
		} else {
		// if invalid location name,
			
			echo "false";
		}
	}
	/**
	 * Publish the record
	 *
	 * @param      integer  Itemlocation  The location identifier
	 */
	function ajx_publish( $id = 0 )
	{
		// check access
		$this->check_access( PUBLISH );
		
		// prepare data
		$type_data = array( 'status'=> 1 );
			
		// save data
		if ( $this->Item_location_township->save( $type_data, $id )) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	/**
	 * Unpublish the records
	 *
	 * @param      integer  Itemlocation  The Itemlocation identifier
	 */
	function ajx_unpublish( $id = 0 )
	{
		// check access
		$this->check_access( PUBLISH );
		
		// prepare data
		$type_data = array( 'status'=> 0 );
		// save data
		if ( $this->Item_location_township->save( $type_data, $id )) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
}