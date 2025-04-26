<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Paid_items Controller
 */
class Paid_items extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'PAID ITEMS' );
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
	 * List down the paid item
	 */
	function index() {

		$this->session->unset_userdata('item_id');
		$conds['is_paid'] = 1;

		// get rows count
		$this->data['rows_count'] = $this->Item->count_all_by( $conds );

		// get paid_items
		$this->data['paid_items'] = $this->Item->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );

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
		if($this->input->post('submit') != NULL ){

			if($this->input->post('searchterm') != "") {
				$conds['searchterm'] = $this->input->post('searchterm');
				$this->data['searchterm'] = $this->input->post('searchterm');
				$this->session->set_userdata(array("searchterm" => $this->input->post('searchterm')));
			} else {
				
				$this->session->set_userdata(array("searchterm" => NULL));
			}
			
//			if($this->input->post('cat_id') != ""  || $this->input->post('cat_id') != '0') {
//				$conds['cat_id'] = $this->input->post('cat_id');
//				$this->data['cat_id'] = $this->input->post('cat_id');
//				$this->data['selected_cat_id'] = $this->input->post('cat_id');
//				$this->session->set_userdata(array("cat_id" => $this->input->post('cat_id')));
//				$this->session->set_userdata(array("selected_cat_id" => $this->input->post('cat_id')));
//			} else {
//				$this->session->set_userdata(array("cat_id" => NULL ));
//			}

			if($this->input->post('property_by_id') != ""  || $this->input->post('property_by_id') != '0') {
				$conds['property_by_id'] = $this->input->post('property_by_id');
				$this->data['property_by_id'] = $this->input->post('property_by_id');
				$this->session->set_userdata(array("property_by_id" => $this->input->post('property_by_id')));
			} else {
				$this->session->set_userdata(array("property_by_id" => NULL ));
			}

            if($this->input->post('item_price_type_id') != ""  || $this->input->post('item_price_type_id') != '0') {
                $conds['item_price_type_id'] = $this->input->post('item_price_type_id');
                $this->data['item_price_type_id'] = $this->input->post('item_price_type_id');
                $this->session->set_userdata(array("item_price_type_id" => $this->input->post('item_price_type_id')));
            } else {
                $this->session->set_userdata(array("item_price_type_id" => NULL ));
            }

            if($this->input->post('posted_by_id') != ""  || $this->input->post('posted_by_id') != '0') {
                $conds['posted_by_id'] = $this->input->post('posted_by_id');
                $this->data['posted_by_id'] = $this->input->post('posted_by_id');

                $this->session->set_userdata(array("posted_by_id" => $this->input->post('posted_by_id')));

            } else {
                $this->session->set_userdata(array("posted_by_id" => NULL ));
            }

            if($this->input->post('item_currency_id') != ""  || $this->input->post('item_currency_id') != '0') {
                $conds['item_currency_id'] = $this->input->post('item_currency_id');
                $this->data['item_currency_id'] = $this->input->post('item_currency_id');

                $this->session->set_userdata(array("item_currency_id" => $this->input->post('item_currency_id')));

            } else {
                $this->session->set_userdata(array("item_currency_id" => NULL ));
            }

            if($this->input->post('item_location_city_id') != ""  || $this->input->post('item_location_city_id') != '0') {
                $conds['item_location_city_id'] = $this->input->post('item_location_city_id');
                $this->data['item_location_city_id'] = $this->input->post('item_location_city_id');
                $this->data['selected_location_city_id'] = $this->input->post('item_location_city_id');
                $this->session->set_userdata(array("item_location_city_id" => $this->input->post('item_location_city_id')));
                $this->session->set_userdata(array("selected_location_city_id" => $this->input->post('item_location_city_id')));
            } else {
                $this->session->set_userdata(array("item_location_city_id" => NULL ));
            }

            if($this->input->post('item_location_township_id') != ""  || $this->input->post('item_location_township_id') != '0') {
                $conds['item_location_township_id'] = $this->input->post('item_location_township_id');
                $this->data['item_location_township_id'] = $this->input->post('item_location_township_id');
                $this->session->set_userdata(array("item_location_township_id" => $this->input->post('item_location_township_id')));
            } else {
                $this->session->set_userdata(array("item_location_township_id" => NULL ));
            }

			if($this->input->post('status') != "0") {
				
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

			if($this->session->userdata('cat_id') != NULL){
				$conds['cat_id'] = $this->session->userdata('cat_id');
				$this->data['cat_id'] = $this->session->userdata('cat_id');
				$this->data['selected_cat_id'] = $this->session->userdata('cat_id');
			}

			if($this->session->userdata('property_by_id') != NULL){
				$conds['property_by_id'] = $this->session->userdata('property_by_id');
				$this->data['property_by_id'] = $this->session->userdata('property_by_id');
				$this->data['selected_cat_id'] = $this->session->userdata('cat_id');
			}

            if($this->session->userdata('item_price_type_id') != NULL){
                $conds['item_price_type_id'] = $this->session->userdata('item_price_type_id');
                $this->data['item_price_type_id'] = $this->session->userdata('item_price_type_id');
            }

			if($this->session->userdata('posted_by_id') != NULL){
				$conds['posted_by_id'] = $this->session->userdata('posted_by_id');
				$this->data['posted_by_id'] = $this->session->userdata('posted_by_id');
			}

            if($this->session->userdata('item_currency_id') != NULL){
                $conds['item_currency_id'] = $this->session->userdata('item_currency_id');
                $this->data['item_currency_id'] = $this->session->userdata('item_currency_id');
            }

			if($this->session->userdata('item_type_id') != NULL){
				$conds['item_type_id'] = $this->session->userdata('item_type_id');
				$this->data['item_type_id'] = $this->session->userdata('item_type_id');
			}

            if($this->session->userdata('item_location_city_id') != NULL){
                $conds['item_location_city_id'] = $this->session->userdata('item_location_city_id');
                $this->data['item_location_city_id'] = $this->session->userdata('item_location_city_id');
                $this->data['selected_location_city_id'] = $this->session->userdata('item_location_city_id');
            }

            if($this->session->userdata('item_location_township_id') != NULL){
                $conds['item_location_township_id'] = $this->session->userdata('item_location_township_id');
                $this->data['item_location_township_id'] = $this->session->userdata('item_location_township_id');
                $this->data['selected_location_city_id'] = $this->session->userdata('item_location_city_id');
            }


            if($this->session->userdata('status') != 0){
				$conds['status'] = $this->session->userdata('status');
				$this->data['status'] = $this->session->userdata('status');
			}
			

		}

		if ($conds['status'] == "Select Status") {
			$conds['status'] = "1";
		}

		$conds['is_paid'] = "1";
		// pagination
		$this->data['rows_count'] = $this->Item->count_all_by( $conds );

		// search data
		$this->data['paid_items'] = $this->Item->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load add list
		parent::search();
	}

	/**
	 * Create new one
	 */
	function add($item_id=0) {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'paid_prd_add' );
		$this->data['item_id'] = $item_id;

		// call the core add logic
		parent::paid_add($item_id);
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
	function save( $id = false,$item_id = false ) {

		$user_id = $this->Item->get_one($item_id)->added_user_id;
		/* update item is paid 1 */
		$item_data = array(
			"is_paid" => "1"
		);
		$this->Item->save($item_data,$item_id);
		/* save paid item history data*/
	   	$added_user_id = $user_id;
		$dates = $this->get_data( 'date' );
		$vardate = explode('-',$dates,2);

		$temp_mindate = $vardate[0];
		$temp_maxdate = $vardate[1];		

		$temp_startdate = new DateTime($temp_mindate);
		$start_date = $temp_startdate->format('Y-m-d');

		$temp_enddate = new DateTime($temp_maxdate);
		$end_date = $temp_enddate->format('Y-m-d');

		$d = DateTime::createFromFormat('Y-m-d', $start_date);
		$start_timestamp = $d->getTimestamp();

	  	$paid_data = array(
	  		"item_id" => $item_id,
	  		"start_date" => $start_date,
	  		"start_timestamp" => $start_timestamp,
	  		"end_date" => $end_date,
	  		"amount" => 0,
	  		"payment_method" => 'NA',
	  		"added_user_id" => $added_user_id
	  	);
		//save item
		if ( ! $this->Paid_item->save( $paid_data, $id )) {
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

				$this->set_flash_msg( 'success', get_msg( 'success_paid_add' ));
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

	function save_edit( $id = false ) {

		$logged_in_user = $this->ps_auth->get_user_info();

		// Item id
	   	if ( $this->has_data( 'id' )) {
			$data['id'] = $this->get_data( 'id' );

		}

		//title
	   	if ( $this->has_data( 'title' )) {
			$data['title'] = $this->get_data( 'title' );
		}

		//description
	   	if ( $this->has_data( 'description' )) {
			$data['description'] = $this->get_data( 'description' );
		}
		//print_r($this->has_data( 'area' ));die;
		//area
	   	if ( $this->has_data( 'area' )) {
			$data['area'] = $this->get_data( 'area' );
		}

		// address
	   	if ( $this->has_data( 'address' )) {
			$data['address'] = $this->get_data( 'address' );
		}

		// configuration_label
	   	if ( $this->has_data( 'configuration_label' )) {
			$data['configuration_label'] = $this->get_data( 'configuration_label' );
		}

		// highlight_info
	   	if ( $this->has_data( 'highlight_info' )) {
			$data['highlight_info'] = $this->get_data( 'highlight_info' );
		}

		// posted_by_id
	   	if ( $this->has_data( 'posted_by_id' )) {
			$data['posted_by_id'] = $this->get_data( 'posted_by_id' );
		}

		// property_by_id
	   	if ( $this->has_data( 'property_by_id' )) {
			$data['property_by_id'] = $this->get_data( 'property_by_id' );
		}

		// price
	   	if ( $this->has_data( 'price' )) {
			$data['price'] = $this->get_data( 'price' );
		}

		// price_unit
	   	if ( $this->has_data( 'price_unit' )) {
			$data['price_unit'] = $this->get_data( 'price_unit' );
		}

		// price_note
	   	if ( $this->has_data( 'price_note' )) {
			$data['price_note'] = $this->get_data( 'price_note' );
		}

		// item_currency_id
	   	if ( $this->has_data( 'item_currency_id' )) {
			$data['item_currency_id'] = $this->get_data( 'item_currency_id' );
		}

		// item_location_city_id
	   	if ( $this->has_data( 'item_location_city_id' )) {
			$data['item_location_city_id'] = $this->get_data( 'item_location_city_id' );
		}

		// item_location_township_id
	   	if ( $this->has_data( 'item_location_township_id' )) {
			$data['item_location_township_id'] = $this->get_data( 'item_location_township_id' );
		}

		// prepare Item lat
		if ( $this->has_data( 'lat' )) {
			$data['lat'] = $this->get_data( 'lat' );
		}

		// prepare Item lng
		if ( $this->has_data( 'lng' )) {
			$data['lng'] = $this->get_data( 'lng' );
		}

		// if 'is_negotiable' is checked,
		if ( $this->has_data( 'is_negotiable' )) {
			$data['is_negotiable'] = 1;
		} else {
			$data['is_negotiable'] = 0;
		}

		// if 'is_sold_out' is checked,
		if ( $this->has_data( 'is_sold_out' )) {
			$data['is_sold_out'] = 1;
		} else {
			$data['is_sold_out'] = 0;
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

		//print_r($data);die;
		
		//save item
		if ( ! $this->Item->save( $data, $id )) {
		// if there is an error in inserting user data,	

			// rollback the transaction
			$this->db->trans_rollback();

			// set error message
			$this->data['error'] = get_msg( 'err_model' );
			
			return;
		}

		//amenities

		$data['amenities'] = ( $this->get_data( 'amenities' ) != false )? $this->get_data( 'amenities' ): array();

		$id = ( !$id )? $data['id']: $id ;

		if(!$this->ps_delete->delete_item_amenity( $id )) {
			if (count($data['amenities']) > 0) {
				for ($i=0; $i <count($data['amenities']) ; $i++) { 
					
					$select_data['amenity_id'] = $data['amenities'][$i];
					$select_data['item_id'] = $id;

					$this->Item_amenity->save($select_data);
				}

			}
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

	}


    function edit_paid( $id )
    {
    	$this->session->set_userdata(array("item_id" => $id ));
		
		redirect( site_url('admin/paid_items/paid_item_edit/') );
    }
	/**
 	* Update the existing one
	*/
	function paid_item_edit( ) 
	{
		$id = $this->session->userdata('item_id');
		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'prd_edit' );

		// load item
		$this->data['item'] = $this->Item->get_one( $id );

		// load history
		$conds['item_id'] = $id;
		
		// get rows count
		$this->data['rows_count'] = $this->Paid_item->count_all_by( $conds );
		// get paid history
		$this->data['paid_histories'] = $this->Paid_item->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ));

		// call the parent edit logic
		parent::paid_edit( $id );

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
	 * Check Item name via ajax
	 *
	 * @param      boolean  $Item_id  The cat identifier
	 */
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

 }