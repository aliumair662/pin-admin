<?php
require_once( APPPATH .'libraries/REST_Controller.php' );

/**
 * REST API for News
 */
class Item_location_townships extends API_Controller
{

	/**
	 * Constructs Parent Constructor
	 */
	function __construct()
	{
		parent::__construct( 'Item_location_township' );
	}

	/**
	 * Default Query for API
	 * @return [type] [description]
	 */
	function default_conds()
	{
		
		$conds = array();

		if ( $this->is_search ) {

			//$setting = $this->Api->get_one_by( array( 'api_constant' => SEARCH_WALLPAPERS ));

			if($this->post('keyword') != "") {
				$conds['keyword']   = trim($this->post('keyword'));
			}

			if($this->post('lat') != "" && $this->post('lng') != "" && $this->post('miles') != "" && $this->post('city_id') != "") {
				$conds['city_id']   = $this->post('city_id');
			} if($this->post('lat') != "" && $this->post('lng') != "" && $this->post('miles') != "" && $this->post('city_id') == "") {
				$conds['city_id']   ="";
			} else {
				if($this->post('city_id') != "") {
					$conds['city_id']   = $this->post('city_id');
				}
			}

			$conds['order_by'] = 1;
			$conds['order_by_field']    = $this->post('order_by');
			$conds['order_by_type']     = $this->post('order_type');
				
		}

		return $conds;
	}
		/**
	 * Determines if valid input.
	 */
	function add_post()
	{
		// validation rules for create
		$rules = array(
			array(
	        	'field' => 'city_id',
	        	'rules' => 'required'
	        )
        );

       	$city_id = $this->post('city_id');

       	if ($city_id != "") {

       		$conds['city_id'] = $city_id;
       		// print_r($conds['city_id']);die;
       	
       		$townships = $this->Item_location_township->get_all_by($conds)->result();	

       	}
			$this->custom_response( $townships );
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