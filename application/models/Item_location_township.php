<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for Itemlocation table
 */
class Item_location_township extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'bs_item_location_townships', 'id', 'itm_town' );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array())
	{
		// default where clause
		if ( !isset( $conds['no_publish_filter'] )) {
			$this->db->where( 'status', 1 );
		}

		// id condition
		if ( isset( $conds['id'] )) {
			$this->db->where( 'id', $conds['id'] );
		}

		// city_id condition
		if ( isset( $conds['city_id'] )) {
			
			if ($conds['city_id'] != "") {
				if($conds['city_id'] != '0'){
				
					$this->db->where( 'city_id', $conds['city_id'] );	
				}

			}			
		}

		// township_name condition
		if ( isset( $conds['township_name'] )) {
			$this->db->where( 'township_name', $conds['township_name'] );
		}

		// keyword
		if ( isset( $conds['searchterm'] )) {
			$this->db->like( 'township_name', $conds['searchterm'] );
		}

		// keyword
		if ( isset( $conds['keyword'] )) {
			$this->db->like( 'township_name', $conds['keyword'] );
		}

		// name condition
		if ( isset( $conds['ordering'] )) {
			$this->db->where( 'ordering', $conds['ordering'] );
		}

		// print_r($conds);die;

		// order_by
		if ( isset( $conds['order_by'] )) {

			$order_by_field = $conds['order_by_field'];
			$order_by_type = $conds['order_by_type'];

			$this->db->order_by( 'bs_item_location_townships.'.$order_by_field, $order_by_type );
		} else {

			$this->db->order_by( 'added_date' , 'desc' );
		}
		
	}
}