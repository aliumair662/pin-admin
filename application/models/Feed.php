<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for feed table
 */
class Feed extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'bs_feeds', 'id', 'feed' );
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

		// feed_name condition
		if ( isset( $conds['name'] )) {
			$this->db->where( 'name', $conds['name'] );
		}
		// lcoation id condition
		if ( isset( $conds['item_location_id'] ) ) {
			
			if ($conds['item_location_id'] != "") {
                if($conds['item_location_id'] != '0'){
                    if($conds['item_location_id'] != '1'){

                    $this->db->where( 'item_location_id', $conds['item_location_id'] );
                    }
				}

			}			
		}

		// feed_desc condition
		if ( isset( $conds['description'] )) {
			$this->db->where( 'description', $conds['description'] );
		}

		// searchterm
		if ( isset( $conds['searchterm'] )) {
			$this->db->group_start();
			$this->db->like( 'name', $conds['searchterm'] );
			$this->db->or_like( 'name', $conds['searchterm'] );
			$this->db->group_end();
		}

		$this->db->order_by( 'added_date', 'desc' );
	}
}