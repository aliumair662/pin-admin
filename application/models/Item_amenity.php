<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for api table
 */
class Item_amenity extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'bs_item_amenities', 'id', 'itmamen' );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array())
	{
		// api_id condition
		if ( isset( $conds['id'] )) {
			
			$this->db->where( 'id', $conds['id'] );
		}

		// item_id condition
		if ( isset( $conds['item_id'] )) {
			
			$this->db->where( 'item_id', $conds['item_id'] );
		}

			// amenity_id condition
		if ( isset( $conds['amenity_id'] )) {
			
			$this->db->where( 'amenity_id', $conds['amenity_id'] );
		}

		// amenities_id condition
        if ( isset( $conds['amenities_id'] )) {

            $this->db->where_in( 'amenity_id', $conds['amenities_id'] );
        }
	}


	
}