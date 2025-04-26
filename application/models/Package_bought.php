<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for about table
 */
class Package_bought extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'bs_package_bought_transactions', 'id', 'pkg_trans' );
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

		// package_id condition
		if ( isset( $conds['package_id'] )) {
			$this->db->where( 'package_id', $conds['package_id'] );
		}

		// payment_method condition
		if ( isset( $conds['payment_method'] )) {
			$this->db->where( 'payment_method', $conds['payment_method'] );
		}

		// price condition
		if ( isset( $conds['price'] )) {
			$this->db->where( 'price', $conds['price'] );
		}

		// razor_id condition
		if ( isset( $conds['razor_id'] )) {
			$this->db->where( 'razor_id', $conds['razor_id'] );
		}

		// isPaystack condition
		if ( isset( $conds['isPaystack'] )) {
			$this->db->where( 'isPaystack', $conds['isPaystack'] );
		}

		// status condition
		if ( isset( $conds['status'] )) {
			$this->db->where( 'bs_package_bought_transactions.status', $conds['status'] );
		}

		// searchterm
		if ( isset( $conds['searchterm'] )) {
			$this->db->join('core_users', 'core_users.user_id =bs_package_bought_transactions.user_id');
			$this->db->join('bs_packages', 'bs_packages.package_id =bs_package_bought_transactions.package_id');
			$this->db->like( 'core_users.user_name', $conds['searchterm'] );
			$this->db->or_like( 'bs_packages.title', $conds['searchterm'] );
		}

		//for date
		if (isset( $conds['date'] )) {
			
			$dates = $conds['date'];
			
			if ($dates != "") {
				$vardate = explode('-',$dates,2);

				$temp_mindate = $vardate[0];
				$temp_maxdate = $vardate[1];		

				$temp_startdate = new DateTime($temp_mindate);
				$mindate = $temp_startdate->format('Y-m-d');

				$temp_enddate = new DateTime($temp_maxdate);
				$maxdate = $temp_enddate->format('Y-m-d');
			} else {
				$mindate = "";
			 	$maxdate = "";
			}
			if ($mindate != "" && $maxdate != "") {
				//got 2dates
				if ($mindate == $maxdate ) {
					$this->db->where("bs_package_bought_transactions.added_date BETWEEN DATE('".$mindate."' - INTERVAL 1 DAY) AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {
					$today_date = date('Y-m-d');
					if($today_date == $maxdate) {
						$current_time = date('H:i:s');
						$maxdate = $maxdate . " ". $current_time;
					}
					// die;
					$this->db->where( 'bs_package_bought_transactions.added_date >=', $mindate );
   					$this->db->where( 'bs_package_bought_transactions.added_date <=', $maxdate );
				}	
			}			 
	    }

		$this->db->order_by( 'bs_package_bought_transactions.added_date', 'desc' );
	}

}