<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for Itemreport table
 */
class Itemreport extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'bs_items_report', 'id', 'itm_report' );
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

		// reported_user_id
		if ( isset( $conds['reported_user_id'] )) {
			$this->db->like( 'reported_user_id', $conds['reported_user_id'] );
		}
		
		// text_note
		if ( isset( $conds['text_note'] )) {
			$this->db->like( 'text_note', $conds['text_note'] );
		}

		// reported_status
		if ( isset( $conds['reported_status'] )) {
			$this->db->join('bs_reported_item_status', 'bs_items_report.reported_status =bs_reported_item_status.id');
			$this->db->where( 'bs_reported_item_status.id', $conds['reported_status'] );
		}
		
		
		// searchterm
		if ( isset( $conds['searchterm'] )) {
			$this->db->join('bs_items', 'bs_items.id =bs_items_report.item_id');
			$this->db->like( 'bs_items.title', $conds['searchterm'] );
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
					$this->db->where("bs_items_report.added_date BETWEEN DATE('".$mindate."' - INTERVAL 1 DAY) AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {
					$today_date = date('Y-m-d');
					if($today_date == $maxdate) {
						$current_time = date('H:i:s');
						$maxdate = $maxdate . " ". $current_time;
					}
					// die;
					$this->db->where( 'bs_items_report.added_date >=', $mindate );
   					$this->db->where( 'bs_items_report.added_date <=', $maxdate );
				}	
			}			 
	    }

		$this->db->order_by( 'bs_items_report.added_date', 'desc' );
	}
}