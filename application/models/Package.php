<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for package table
 */
class Package extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'bs_packages', 'package_id', 'pkg_' );
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


		// package_id condition
		if ( isset( $conds['package_id'] )) {
			$this->db->where( 'package_id', $conds['package_id'] );
		}

		// title condition
		if ( isset( $conds['title'] )) {
			$this->db->where( 'title', $conds['title'] );
		}

        // price condition
		if ( isset( $conds['price'] )) {
			$this->db->where( 'price', $conds['price'] );
		}

        // post_count condition
		if ( isset( $conds['post_count'] )) {
			$this->db->where( 'post_count', $conds['post_count'] );
		}

		// package_in_app_purchased_prd_id condition
		if ( isset( $conds['package_in_app_purchased_prd_id'] )) {
			$this->db->where( 'package_in_app_purchased_prd_id', $conds['package_in_app_purchased_prd_id'] );
		}

		// type condition
		if ( isset( $conds['type'] )) {
			$this->db->where( 'type', $conds['type'] );
		}
		
		// searchterm
		if ( isset( $conds['searchterm'] )) {
			$this->db->like( 'title', $conds['searchterm'] );
		}

		
		// order_by
		if ( isset( $conds['order_by_field'] )) {

			$order_by_field = $conds['order_by_field'];
			$order_by_type = $conds['order_by_type'];

			$this->db->order_by( 'bs_packages.'.$order_by_field, $order_by_type );
		} else {


			$this->db->order_by( 'added_date' );
		}
	}
}