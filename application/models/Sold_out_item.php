<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for product table
 */
class Sold_out_item extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'bs_items', 'id', 'itm_' );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array())
	{
		// print_r($conds);die;
		// default where clause
		
		//  status condition 
		if ( isset( $conds['status'] )) {
			if ($conds['status'] != "" ) {
				if ($conds['status'] != '0') {
					$this->db->where( 'bs_items.status', $conds['status'] );
				}
				
			}
			
		}
		if (isset( $conds['is_sold_out'] )) {
			$this->db->where( 'is_sold_out', $conds['is_sold_out'] );
		}else{
			$this->db->where( 'is_sold_out', 1 );
		}
		

		// id condition
		if ( isset( $conds['id'] )) {
			$this->db->where( 'id', $conds['id'] );
		}

		//  property by id condition 
		if ( isset( $conds['property_by_id'] )) {
			if ($conds['property_by_id'] != "" ) {
				if ($conds['property_by_id'] != '0') {
					$this->db->where( 'bs_items.property_by_id', $conds['property_by_id'] );
				}
				
			}
			
		}
		
		//  posted by id condition 
		if ( isset( $conds['posted_by_id'] )) {
			if ($conds['posted_by_id'] != "" ) {
				if ($conds['posted_by_id'] != '0') {
					$this->db->where( 'bs_items.posted_by_id', $conds['posted_by_id'] );
				}
				
			}
			
		}
		
		//  location city id condition 
		if ( isset( $conds['item_location_city_id'] )) {
			if ($conds['item_location_city_id'] != "" ) {
				if ($conds['item_location_city_id'] != '0') {
					$this->db->where( 'bs_items.item_location_city_id', $conds['item_location_city_id'] );
				}
				
			}
			
		}
		
		//  location township id condition 
		if ( isset( $conds['item_location_township_id'] )) {
			if ($conds['item_location_township_id'] != "" ) {
				if ($conds['item_location_township_id'] != '0') {
					$this->db->where( 'bs_items.item_location_township_id', $conds['item_location_township_id'] );
				}
				
			}
			
		}

		// Type id
		if ( isset( $conds['item_type_id'] )) {
			
			if ($conds['item_type_id'] != "") {
				if($conds['item_type_id'] != '0'){
				
					$this->db->where( 'item_type_id', $conds['item_type_id'] );	
				}

			}			
		}
	  
		// Price id
		if ( isset( $conds['item_price_type_id'] )) {
			
			if ($conds['item_price_type_id'] != "") {
				if($conds['item_price_type_id'] != '0'){
				
					$this->db->where( 'item_price_type_id', $conds['item_price_type_id'] );	
				}

			}			
		}
	   
		// Currency id
		if ( isset( $conds['item_currency_id'] )) {
			
			if ($conds['item_currency_id'] != "") {
				if($conds['item_currency_id'] != '0'){
				
					$this->db->where( 'item_currency_id', $conds['item_currency_id'] );	
				}

			}			
		}

		// condition_of_item id condition
		if ( isset( $conds['condition_of_item_id'] )) {
			$this->db->where( 'condition_of_item_id', $conds['condition_of_item_id'] );
		}

		// description condition
		if ( isset( $conds['description'] )) {
			$this->db->where( 'description', $conds['description'] );
		}

		// highlight_info condition
		if ( isset( $conds['highlight_info'] )) {
			$this->db->where( 'highlight_info', $conds['highlight_info'] );
		}

		// deal_option_id condition
		if ( isset( $conds['deal_option_id'] )) {
			$this->db->where( 'deal_option_id', $conds['deal_option_id'] );
		}

		// brand condition
		if ( isset( $conds['brand'] )) {
			$this->db->where( 'brand', $conds['brand'] );
		}

		// business_mode condition
		if ( isset( $conds['business_mode'] )) {
			$this->db->where( 'business_mode', $conds['business_mode'] );
		}

		//  added user id condition 
		if ( isset( $conds['added_user_id'] )) {
			if ($conds['added_user_id'] != "" ) {
				if ($conds['added_user_id'] != '0') {
					$this->db->where( 'bs_items.added_user_id', $conds['added_user_id'] );
				}
				
			}
			
		}
		
		if ( isset( $conds['searchterm'] ) || isset( $conds['date'] )) {
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
			
			if ($conds['searchterm'] == "" && $mindate != "" && $maxdate != "") {
				//got 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("bs_touches.added_date BETWEEN DATE('".$mindate."') AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {

					$today_date = date('Y-m-d');
					if($today_date == $maxdate) {
						$current_time = date('H:i:s');
						$maxdate = $maxdate . " ". $current_time;
					}

					$this->db->where( 'date(bs_touches.added_date) >=', $mindate );
   					$this->db->where( 'date(bs_touches.added_date) <=', $maxdate );

				}
				$this->db->like( '(title', $conds['searchterm'] );
				$this->db->or_like( 'title)', $conds['searchterm'] );
			} else if ($conds['searchterm'] != "" && $mindate != "" && $maxdate != "") {
				//got name and 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("bs_touches.added_date BETWEEN DATE('".$mindate."') AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {

					$today_date = date('Y-m-d');
					if($today_date == $maxdate) {
						$current_time = date('H:i:s');
						$maxdate = $maxdate . " ". $current_time;
					}

					$this->db->where( 'date(bs_touches.added_date) >=', $mindate );
   					$this->db->where( 'date(bs_touches.added_date) <=', $maxdate );

				}
				$this->db->group_start();
				$this->db->like( 'title', $conds['searchterm'] );
				$this->db->or_like( 'title', $conds['searchterm'] );
				$this->db->group_end();
			} else {
				//only name 
				$this->db->group_start();
				$this->db->like( 'title', $conds['searchterm'] );
				$this->db->or_like( 'title', $conds['searchterm'] );
				$this->db->group_end();
				
			}
			 
	    }
		if( isset($conds['max_price']) ) {
			if( $conds['max_price'] != 0 ) {
				$this->db->where( 'price >=', $conds['max_price'] );
			}	

		}

		if( isset($conds['min_price']) ) {

			if( $conds['min_price'] != 0 ) {
				$this->db->where( 'price <=', $conds['min_price'] );
			}

		}

		// order by
		if ( isset( $conds['order_by_field'] )) {
			$order_by_field = $conds['order_by_field'];
			$order_by_type = $conds['order_by_type'];
			
			$this->db->order_by( 'bs_items.'.$order_by_field, $order_by_type);
		} else {
			$this->db->order_by('added_date', 'desc' );
		}

		
	}

	/**
	 * Determines if filter feature.
	 *
	 * @return     boolean  True if filter feature, False otherwise.
	 */
	function is_filter_feature( $conds )
	{
		return ( isset( $conds['feature'] ) && $conds['feature'] == 1 );
	}

	/**
	 * Determines if filter discount.
	 *
	 * @return     boolean  True if filter discount, False otherwise.
	 */
	function is_filter_discount( $conds )
	{
		return ( isset( $conds['discount'] ) && $conds['discount'] == 1 );
	}

}