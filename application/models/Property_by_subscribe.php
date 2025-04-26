<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for touch table
 */
class Property_by_subscribe extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'bs_propertyby_subscribes', 'id', 'scribe_' );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array())
	{
        // id condition
		if ( isset( $conds['id'] )) {
			$this->db->where( 'id', $conds['id'] );
		}

		// user_id condition
		if ( isset( $conds['user_id'] )) {
			$this->db->where( 'user_id', $conds['user_id'] );
		}

		// property_by_id condition
		if ( isset( $conds['property_by_id'] )) {
			$this->db->where( 'property_by_id', $conds['property_by_id']);
		}

		// property_by_id_fe condition
		if ( isset( $conds['property_by_id_fe'] )) {
			$this->db->where( 'property_by_id', $conds['property_by_id_fe'] . '_FE' );
		}
		
		// property_by_id_mb condition
		if ( isset( $conds['property_by_id_mb'] )) {
			$this->db->where( 'property_by_id', $conds['property_by_id_mb'] . '_MB' );
		}

	}
}