<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for about table
 */
class User_bought extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'bs_user_bought', 'id', 'bou_' );
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

		// item_id condition
		if ( isset( $conds['item_id'] )) {
			$this->db->where( 'item_id', $conds['item_id'] );
		}

		// buyer_user_id condition
		if ( isset( $conds['buyer_user_id'] )) {
			$this->db->where( 'buyer_user_id', $conds['buyer_user_id'] );
		}

		// seller_user_id condition
		if ( isset( $conds['seller_user_id'] )) {
			$this->db->where( 'seller_user_id', $conds['seller_user_id'] );
		}

		
	}
}