<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for Item table
 */
class Item extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		
		header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		parent::__construct( 'bs_items', 'id', 'itm_' );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array())
	{
		
		$date = date('Y-m-d');
		$day_name = date('l', strtotime($date));
		
		// $this->db->or_where('is_repetitive','false');
		// $this->db->or_where('is_repetitive','');
		// $this->db->or_where('bs_items.added_user_id',$conds['added_user_id']);

		//repetitive event condition
		// $arr = array('event_day'=>$day_name, 'is_repetitive'=>'true' );
		// $this->db->where($arr);

		// default where clause
		if (isset( $conds['status'] )) {
			$this->db->where( 'status', $conds['status'] );
		}

		// default where clause
		if (isset( $conds['is_sold_out'] )) {
			$this->db->where( 'is_sold_out', $conds['is_sold_out'] );
		}

		// is_paid condition
        if ( isset( $conds['is_paid'] )) {

            if ($conds['is_paid'] != "") {
                $this->db->where( 'is_paid', $conds['is_paid'] );
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

		// id condition
		if ( isset( $conds['id'] )) {
			
			$s = $this->db->where( 'id', $conds['id'] );
		}

		// title condition
		if ( isset( $conds['title'] )) {
			// $arr = array('title'=> $conds['title'], 'is_repetitive'=>'true');
			$this->db->where( 'title', $conds['title'] );
		}

		// payment_type condition
		if ( isset( $conds['payment_type'] )) {
			$this->db->where( 'payment_type', $conds['payment_type'] );
		}

		// id condition
		if ( isset( $conds['added_user_id'] )) {
			$this->db->where( 'added_user_id', $conds['added_user_id'] );
		}

		// Property type id
		if ( isset( $conds['property_by_id'] )) {
			
			if ($conds['property_by_id'] != "") {
				if($conds['property_by_id'] != '0'){
				
					$this->db->where( 'property_by_id', $conds['property_by_id'] );	
				}

			}			
		}

		// posted_by_id
		if ( isset( $conds['posted_by_id'] )) {
			
			if ($conds['posted_by_id'] != "") {
				if($conds['posted_by_id'] != '0'){
				
					$this->db->where( 'posted_by_id', $conds['posted_by_id'] );	
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

		// item_location_city_id
		if ( isset( $conds['item_location_city_id'] )) {
			
			if ($conds['item_location_city_id'] != "") {
				if($conds['item_location_city_id'] != '0'){
				
					$this->db->where( 'item_location_city_id', $conds['item_location_city_id'] );	
				}

			}			
		}

		// discount rate
		if ( isset( $conds['is_discount'] )) {
			if ($conds['is_discount'] == "1") {
				$this->db->where( 'discount_rate_by_percentage !=', '0' );				
				$this->db->where( 'discount_rate_by_percentage !=', '' );			
			}	
		}

		// item_location_township_id
		if ( isset( $conds['item_location_township_id'] )) {

			if ($conds['item_location_township_id'] != "") {
				if($conds['item_location_township_id'] != '0'){

					$this->db->where( 'item_location_township_id', $conds['item_location_township_id'] );
				}

			}
		}

		if( isset($conds['max_price']) ) {
			if( $conds['max_price'] != 0 ) {
				$this->db->where( 'price <=', $conds['max_price'] );
			}	

		}

		if( isset($conds['min_price']) ) {

			if( $conds['min_price'] != 0 ) {
				$this->db->where( 'price >=', $conds['min_price'] );
			}

		}


		// description condition
		if ( isset( $conds['description'] )) {
			$this->db->where( 'description', $conds['description'] );
		}

		// highlight_info condition 
		if ( isset( $conds['highlight_info'] )) {
			$this->db->where( 'highlight_info', $conds['highlight_info'] );
		}

		// searchterm --- improvement
		// if (isset($conds['searchterm'])) {
		// 	$search1 = explode(" ", trim($conds['searchterm']));
		// 	//print_r($search1);die;
		// 	for ($i=0; $i <count($search1) ; $i++) { 
		// 		$cond_name1 = $search1[$i];
		// 		$cond_name2 = substr($cond_name1, 0,3);
		// 		$cond_name3 = substr($cond_name1, -3);


		// 		$this->db->group_start();
		// 		$this->db->like( 'title', $cond_name1 );
		// 		$this->db->or_like( 'title', $cond_name2 );
		// 		$this->db->or_like( 'title', $cond_name3 );
		// 		$this->db->or_like( 'description', $conds['searchterm'] );
		// 		$this->db->or_like( 'condition_of_item_id', $conds['searchterm'] );
		// 		$this->db->or_like( 'highlight_info', $conds['searchterm'] );
		// 		$this->db->group_end();
		// 	}
		// }

		// SEARCHTERM --- IMPROVEMENT
		if (isset($conds['searchterm'])) {
			
			$search1 = explode(" ", trim($conds['searchterm']));

			$str = "";
			$str_rev = "";

			$this->db->group_start();
			for ($i = 0; $i < count($search1); $i++) {
				$cond_name1 = $search1[$i];

				if ($str == '') {
					$str = $cond_name1;
					$str_rev = $cond_name1;
				} else {
					$str = $str . "( \w*\s*?)*" . $cond_name1;
					$str_rev = $cond_name1 . "( \w*\s*?)*" . $str_rev;
				}
			}

			$this->db->where('title REGEXP', $str);
			$this->db->or_where('title REGEXP', $str_rev);
			$this->db->or_like('description', $conds['searchterm']);
			$this->db->or_like('condition_of_item_id', $conds['searchterm']);
			$this->db->or_like('highlight_info', $conds['searchterm']);
			$this->db->group_end();
			
			
		}

		



		
	}

	

}
