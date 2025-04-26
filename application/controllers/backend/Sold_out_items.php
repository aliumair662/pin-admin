<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sold out item Controller
 */
class Sold_out_items extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'Sold_out_items' );
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
		$conds['is_sold_out'] = 1;

		// get rows count
		$this->data['rows_count'] = $this->Sold_out_item->count_all_by($conds);
		// get items
		$this->data['soldoutitems'] = $this->Sold_out_item->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );

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

			if($this->input->post('property_by_id') != ""  || $this->input->post('property_by_id') != '0') {
				$conds['property_by_id'] = $this->input->post('property_by_id');
				$this->data['property_by_id'] = $this->input->post('property_by_id');
				
				$this->session->set_userdata(array("property_by_id" => $this->input->post('property_by_id')));
				
			} else {
				$this->session->set_userdata(array("property_by_id" => NULL ));
			}

			if($this->input->post('posted_by_id') != ""  || $this->input->post('posted_by_id') != '0') {
				$conds['posted_by_id'] = $this->input->post('posted_by_id');
				$this->data['posted_by_id'] = $this->input->post('posted_by_id');
				
				$this->session->set_userdata(array("posted_by_id" => $this->input->post('posted_by_id')));
				
			} else {
				$this->session->set_userdata(array("posted_by_id" => NULL ));
			}
			
			if($this->input->post('item_price_type_id') != ""  || $this->input->post('item_price_type_id') != '0') {
				$conds['item_price_type_id'] = $this->input->post('item_price_type_id');
				$this->data['item_price_type_id'] = $this->input->post('item_price_type_id');
				
				$this->session->set_userdata(array("item_price_type_id" => $this->input->post('item_price_type_id')));
				
			} else {
				$this->session->set_userdata(array("item_price_type_id" => NULL ));
			}

			if($this->input->post('item_type_id') != ""  || $this->input->post('item_type_id') != '0') {
				$conds['item_type_id'] = $this->input->post('item_type_id');
				$this->data['item_type_id'] = $this->input->post('item_type_id');
				
				$this->session->set_userdata(array("item_type_id" => $this->input->post('item_type_id')));
				
			} else {
				$this->session->set_userdata(array("item_type_id" => NULL ));
			}

			if($this->input->post('item_currency_id') != ""  || $this->input->post('item_currency_id') != '0') {
				$conds['item_currency_id'] = $this->input->post('item_currency_id');
				$this->data['item_currency_id'] = $this->input->post('item_currency_id');
				
				$this->session->set_userdata(array("item_currency_id" => $this->input->post('item_currency_id')));
				
			} else {
				$this->session->set_userdata(array("item_currency_id" => NULL ));
			}

			$conds['date'] = $this->input->post( 'date' );


			if($this->input->post('date') != "") {
				$conds['date'] = $this->input->post('date');
				$this->data['date'] = $this->input->post('date');
				$this->session->set_userdata(array("date" => $this->input->post('date')));
			} else {
				
				$this->session->set_userdata(array("date" => NULL));
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

		} else {
			//read from session value
			if($this->session->userdata('searchterm') != NULL){
				$conds['searchterm'] = $this->session->userdata('searchterm');
				$this->data['searchterm'] = $this->session->userdata('searchterm');
			}

			if($this->session->userdata('property_by_id') != NULL){
				$conds['property_by_id'] = $this->session->userdata('property_by_id');
				$this->data['property_by_id'] = $this->session->userdata('property_by_id');
			}

			if($this->session->userdata('posted_by_id') != NULL){
				$conds['posted_by_id'] = $this->session->userdata('posted_by_id');
				$this->data['posted_by_id'] = $this->session->userdata('posted_by_id');
			}
			
			if($this->session->userdata('item_price_type_id') != NULL){
				$conds['item_price_type_id'] = $this->session->userdata('item_price_type_id');
				$this->data['item_price_type_id'] = $this->session->userdata('item_price_type_id');
			}

			if($this->session->userdata('item_type_id') != NULL){
				$conds['item_type_id'] = $this->session->userdata('item_type_id');
				$this->data['item_type_id'] = $this->session->userdata('item_type_id');
			}

			if($this->session->userdata('item_currency_id') != NULL){
				$conds['item_currency_id'] = $this->session->userdata('item_currency_id');
				$this->data['item_currency_id'] = $this->session->userdata('item_currency_id');
			}
			
			if($this->session->userdata('date') != NULL){
				$conds['date'] = $this->session->userdata('date');
				$this->data['date'] = $this->session->userdata('date');
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

		}

		$conds['status'] = "1";
		$conds['is_sold_out'] = 1;
		
		// pagination
		$this->data['rows_count'] = $this->Sold_out_item->count_all_by( $conds );

		// search data
		$this->data['soldoutitems'] = $this->Sold_out_item->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load add list
		parent::search();
	}
	/**
 	* Update the existing one
	*/
	function edit( $id ) {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'sold_out_item_view' );

		// load user
		$this->data['soldoutitem'] = $this->Item->get_one( $id );
		$this->data['item_id'] = $id;
		// call the parent edit logic
		parent::edit( $id );
	}

	//get all location townships when select category
	function get_all_location_townships( $city_id )
    {
    	$conds['city_id'] = $city_id;
    	
    	$townships = $this->Item_location_township->get_all_by($conds);
		echo json_encode($townships->result());
    }


}