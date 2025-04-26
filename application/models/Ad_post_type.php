<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for ad_post_type table
 */
class Ad_post_type extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'bs_ad_post_type', 'id', 'adpt_' );
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
	}
}