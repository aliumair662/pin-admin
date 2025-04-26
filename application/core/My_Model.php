<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Empty Class
 */
class My_Model {}

/**
 * PanaceaSoft Base Model
 */
class PS_Model extends CI_Model {
	
	// name of the database table
	protected $table_name;

	// name of the ID field
	public $primary_key;

	// name of the key prefix
	protected $key_prefix;

	/**
	 * constructs required data
	 */
	function __construct( $table_name, $primary_key = false, $key_prefix = false )
	{
		parent::__construct();

		// set the table name
		$this->table_name = $table_name;
		$this->primary_key = $primary_key;
		$this->key_prefix = $key_prefix;
	}

	/**
	 * Empty class to be extended
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array()) {

	}

	/**
	 * Generate the TeamPS Unique Key
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function generate_key()
	{
		return $this->key_prefix . md5( $this->key_prefix . microtime() . uniqid() . 'teamps' );
	}

    /**
     * Determines if exist.
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  True if exist, False otherwise.
     */
    function is_exist( $id ) {
    	
    	// from table
    	$this->db->from( $this->table_name );

    	// where clause
		$this->db->where( $this->primary_key, $id );
		
		// get query
		$query = $this->db->get();

		// return the result
		return ($query->num_rows()==1);
    }

    /**
     * Save the data if id is not existed
     *
     * @param      <type>   $data   The data
     * @param      boolean  $id     The identifier
     */
	function save( &$data, $id = false ) {
		
		if ( !$id ) {
		// if id is not false and id is not yet existed,
			if ( !empty( $this->primary_key ) && !empty( $this->key_prefix )) {
			// if the primary key and key prefix is existed,
			
				// generate the unique key
				$data[ $this->primary_key ] = $this->generate_key();
			}

			// insert the data as new record
			return $this->db->insert( $this->table_name, $data );

			// print_r($this->db->last_query());die;
		} else {
			// else
			// where clause
			$this->db->where( $this->primary_key, $id);

			// update the data
			return $this->db->update($this->table_name,$data);
			//print_r($this->db->last_query());die;
		}
	}

	/**
	 * Returns all the records
	 *
	 * @param      boolean  $limit   The limit
	 * @param      boolean  $offset  The offset
	 */
	function get_all( $limit = false, $offset = false ) {

		// where clause
		$this->custom_conds();

		// from table
		$this->db->from($this->table_name);


		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}
		return $this->db->get();
		// print_r($this->db->last_query($sql));die;
	}

	/**
	 * Returns the total count
	 */
	function count_all() {
		// from table
		$this->db->from( $this->table_name );

		// where clause
		$this->custom_conds();

		// return the count all results
		return $this->db->count_all_results();
		// print_r($this->db->last_query());die;
	}

	/**
	 * Return the info by Id
	 *
	 * @param      <type>  $id     The identifier
	 */
	function get_one( $id ) {
		// query the record
		
		$query = $this->db->get_where( $this->table_name, array( $this->primary_key => $id ));
		if ( $query->num_rows() == 1 ) {
		// if there is one row, return the record
			
			return $query->row();
		} else {
		// if there is no row or more than one, return the empty object
			
			return $this->get_empty_object( $this->table_name );
		}
	}

	/**
	 * Returns the multiple Info by Id
	 *
	 * @param      array  $ids    The identifiers
	 */
	function get_multi_info( $ids = array()) {
		
		// from table
		$this->db->from( $this->table_name );

		// where clause
		$this->db->where_in( $this->primary_key, $ids );

		// returns
		return $this->db->get();
	}

	/**
	 * Delete the records by Id
	 *
	 * @param      <type>  $id     The identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function delete( $id )
	{
		// where clause
		$this->db->where( $this->primary_key, $id );

		// delete the record
		return $this->db->delete( $this->table_name );
 	}

 	/**
 	 * Delete the records by ids
 	 *
 	 * @param      array   $ids    The identifiers
 	 *
 	 * @return     <type>  ( description_of_the_return_value )
 	 */
 	function delete_list( $ids = array()) {
 		
 		// where clause
		$this->db->where_in( $this->primary_key, $ids );

		// delete the record
		return $this->db->delete( $this->table_name );
 	}

	/**
	 * returns the object with the properties of the table
	 *
	 * @return     stdClass  The empty object.
	 */
     function get_empty_object()
    {   
        $obj = new stdClass();
        
        $fields = $this->db->list_fields( $this->table_name );

        foreach ( $fields as $field ) {
            $obj->$field = '';
        }

        //for img ordering 0
        if ($obj->img_id == "") {
        	$obj->ordering = 0 ;
        	
        }
        $obj->is_empty_object = true;

        return $obj;
    }

   	/**
   	 * Execute The query
   	 *
   	 * @param      <type>   $sql     The sql
   	 * @param      <type>   $params  The parameters
   	 *
   	 * @return     boolean  ( description_of_the_return_value )
   	 */
	function exec_sql( $sql, $params = false )
	{
		if ( $params ) {
		// if the parameter is not false

			// bind the parameter and run the query
			return $this->db->query( $sql, $params );	
		}

		// if there is no parameter,
		return $this->db->query( $sql );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function conditions( $conds = array())
	{
		// if condition is empty, return true
		if ( empty( $conds )) return true;
	}

	/**
	 * Check if the key is existed,
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function exists( $conds = array()) {
		// where clause
		$this->custom_conds( $conds );
		
		// from table
		$this->db->from( $this->table_name );

		// get query
		$query = $this->db->get();

		// return the result
		return ($query->num_rows() == 1);
		// print_r($this->db->last_query());die;
	}

	/**
	 * Gets all by the conditions
	 *
	 * @param      array    $conds   The conds
	 * @param      boolean  $limit   The limit
	 * @param      boolean  $offset  The offset
	 *
	 * @return     <type>   All by.
	 */
	function get_all_by( $conds = array(), $limit = false, $offset = false ) {

		if($conds['lat'] != "" && $conds['lng'] != "") {
			$this->db->select('*,( 3959
		      * acos( cos( radians('. $conds['lat'] .') )
		              * cos(  radians( lat )   )
		              * cos(  radians( lng ) - radians('. $conds['lng'] .') )
		            + sin( radians('. $conds['lat'] .') )
		              * sin( radians( lat ) )
		            )
		    ) as distance');

		    if ($conds['miles'] == "") {
		    	$conds['miles'] = 0;
		    	$this->db->having('distance < ' .  $conds['miles'] );
		    } else {
		    	$this->db->having('distance < ' .  $conds['miles'] );

		    }

		   
		}

		// where clause
		$this->custom_conds( $conds );

		// from table
		$this->db->from( $this->table_name );

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}
		
	 	return $this->db->get();
		// print_r($this->db->last_query());die;
	
	}

	function get_all_by1( $conds = array(), $limit = false, $offset = false ) {

		if($conds['lat'] != "" && $conds['lng'] != "") {
			$this->db->select('*,( 3959
		      * acos( cos( radians('. $conds['lat'] .') )
		              * cos(  radians( lat )   )
		              * cos(  radians( lng ) - radians('. $conds['lng'] .') )
		            + sin( radians('. $conds['lat'] .') )
		              * sin( radians( lat ) )
		            )
		    ) as distance');

		    if ($conds['miles'] == "") {
		    	$conds['miles'] = 0;
		    	$this->db->having('distance < ' .  $conds['miles'] );
		    } else {
		    	$this->db->having('distance < ' .  $conds['miles'] );

		    }

		   
		}

		// where clause
		$this->custom_conds( $conds );

		// from table
		$this->db->from( $this->table_name );

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}
		
	 	return $this->db->get();
		//  print_r($this->db->last_query());die;
	
	}

	
	/**
	 * Counts the number of all by the conditions
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  Number of all by.
	 */
	function count_all_by( $conds = array()) {

		// where clause
		$this->custom_conds( $conds );
		
		// from table
		$this->db->from( $this->table_name );

		// return the count all results
		return $this->db->count_all_results();
		 //print_r($this->db->last_query());die;
	}

	/**
	 * Sum the number of all by the conditions
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  Number of all by.
	 */
	function sum_all_by( $conds = array()) {
		
		// where clause
		$this->custom_conds( $conds );
		
		$this->db->select_sum('rating');
		// from table
		$this->db->from( $this->table_name );

		// return the count all results
		//return $this->db->count_all_results();
		return $this->db->get();
	}

	/**
	 * Gets the information by.
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  The information by.
	 */
	function get_one_by( $conds = array()) {
		
		// where clause
		$this->custom_conds( $conds );
		// query the record
		$query = $this->db->get( $this->table_name );

		if ( $query->num_rows() == 1 ) {
		// if there is one row, return the record
			return $query->row();
		} else {
		// if there is no row or more than one, return the empty object
			 return $this->get_empty_object( $this->table_name );
			
		}

	}


	/**
	 * Delete the records by condition
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function delete_by( $conds = array() )
	{
		// where clause
		$this->custom_conds( $conds );

		// delete the record
		return $this->db->delete( $this->table_name );
		 //print_r($this->db->last_query());die;
 	}

 	/**
	 * Delete the records
	 */
	function delete_all()
	{
		// where clause
		//$this->custom_conds( $conds );
		// delete the records
		//$this->db->delete( $this->table_name );
		//print_r($this->db->last_query());die;

		return $this->db->query('Delete from bs_chat_history');
 	}



 	/**
	 * Delete the records by condition
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function get_rating_count( $conds = array() )
	{

		$this->db->select(' sum(rating) as t_count'); 
		$this->db->from('bs_rating');

		if(isset($conds['wallpaper_id'])) {

			if ($conds['wallpaper_id'] != "" || $conds['wallpaper_id'] != 0) {
					
					$this->db->where( 'wallpaper_id', $conds['wallpaper_id'] );	

			}

		}

	
	 	return $this->db->get();
		// print_r($this->db->last_query());die;
 	}

 

 	function get_total_earning_point($conds = array()) {

 		$this->db->select(' sum(earn_point) as total'); 
		$this->db->from('bs_earning_points');

		if(isset($conds['user_id'])) {

			if ($conds['user_id'] != "" || $conds['user_id'] != 0) {
					
					$this->db->where( 'user_id', $conds['user_id'] );	

			}

		}

	 	return $this->db->get();
		// print_r($this->db->last_query());die;
 	}


 	/** get popular item */

 	function get_item_count( $conds = array() )
	{
		$this->db->select('bs_items.*, count(bs_touches.touch_id) as t_count,bs_items.title'); 
		$this->db->from('bs_touches');
		$this->db->join('bs_items', 'bs_touches.item_id = bs_items.id');
	
		$this->db->limit(5);

		if(isset($conds['item_id'])) {

			if ($conds['item_id'] != "" || $conds['item_id'] != 0) {
					
					$this->db->where( 'item_id', $conds['item_id'] );	

			}

		}
		$this->db->group_by("bs_touches.item_id");
		$this->db->order_by("count(DISTINCT bs_touches.touch_id)", "DESC");
		return $this->db->get();
	
 	}

 	/** get reported item */

 	function get_item_report($conds = array())
	{
		//print_r("asdfasd" .$conds);die;
		$this->db->select('bs_items_report.*');    
  		$this->db->from('bs_items_report');
  		$this->db->where('month(added_date)',$conds['added_date']);

		return $this->db->get();
		 // print_r($this->db->last_query());die;
	}

 /**
  * Gets all by the conditions
  *
  * @param      array    $conds   The conds
  * @param      boolean  $limit   The limit
  * @param      boolean  $offset  The offset
  *
  * @return     <type>   All by.
  */
	function get_all_device_in( $conds = array(), $limit = false, $offset = false ) {

	  // where clause
	  $this->db->where_in('user_id', $conds);

	  // from table
	  $this->db->from( $this->table_name );

	  if ( $limit ) {
	  // if there is limit, set the limit
	   
	   $this->db->limit($limit);
	  }
	  
	  if ( $offset ) {
	  // if there is offset, set the offset,
	   
	   $this->db->offset($offset);
	  }
	  
	  return $this->db->get();

	 }

	 
 	/**
	Returns Item list by followuser
	*/
	function get_all_item_by_followuser( $conds = array() , $limit = false, $offset = false)
	{
		$is_block_user = $this->db->get_where( 'bs_app_settings', array( 'id' => 'app1' ))->row()->is_block_user;
		$this->db->select('bs_items.*'); 
		$this->db->from('bs_items');
		//follow user
       // print_r($conds['followuser']);die;
		if ( isset( $conds['followuser'] )) {
			if ($conds['followuser'] != "" ) {
				if ($conds['followuser'] != '0') {
					$this->db->where_in( 'bs_items.added_user_id', $conds['followuser'] );	
				} 
				
			}
		}

		// item id (id) check for user block condition
		if($is_block_user == "1"){
			if ( isset( $conds['item_id'] )) {
				
				if ($conds['item_id'] != "") {
					if($conds['item_id'] != '0'){
					
						$this->db->where_not_in( 'bs_items.id', $conds['item_id'] );	
					}

				}			
			}
		}
		// item id (id) check for item reported condition
		if ( isset( $conds['reported_item_id'] )) {
			
			if ($conds['reported_item_id'] != "") {
				if($conds['reported_item_id'] != '0'){
				
					$this->db->where_not_in( 'bs_items.id', $conds['reported_item_id'] );	
				}

			}			
		}

		$this->custom_conds($conds);

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}

  		 return $this->db->get();
  		//  print_r($this->db->last_query());die;
		
	}

	/**
	Returns popular Items count
	*/
	function count_item_by($conds = array()){
		$this->custom_conds();
		//where clause
		$this->db->select('bs_items.*, count(bs_touches.item_id) as t_count');    
  		$this->db->from('bs_items');
  		$this->db->join('bs_touches', 'bs_items.id = bs_touches.item_id');

		if ( isset( $conds['is_sold_out'] )) {
			if ($conds['is_sold_out'] != "" ) {
				$this->db->where( 'bs_items.is_sold_out', $conds['is_sold_out'] );					
			}
		}

		//  item type id condition 
		if ( isset( $conds['item_type_id'] )) {
			if ($conds['item_type_id'] != "" ) {
				if ($conds['item_type_id'] != '0') {
					$this->db->where( 'bs_items.item_type_id', $conds['item_type_id'] );
				}
				
			}
			
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
		
		//  item price type id condition 
		if ( isset( $conds['item_price_type_id'] )) {
			if ($conds['item_price_type_id'] != "" ) {
				if ($conds['item_price_type_id'] != '0') {
					$this->db->where( 'bs_items.item_price_type_id', $conds['item_price_type_id'] );
				}
				
			}
			
		}

		//  item currency id condition 
		if ( isset( $conds['item_currency_id'] )) {
			if ($conds['item_currency_id'] != "" ) {
				if ($conds['item_currency_id'] != '0') {
					$this->db->where( 'bs_items.item_currency_id', $conds['item_currency_id'] );
				}
				
			}
			
		}

		//  status condition 
		if ( isset( $conds['status'] )) {
			if ($conds['status'] != "" ) {
				if ($conds['status'] != '0') {
					$this->db->where( 'bs_items.status', $conds['status'] );
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

  		$this->db->group_by('bs_touches.item_id');
  		$this->db->order_by('t_count', "DESC");
  		$this->db->order_by( 'bs_touches.added_date', "desc" );
  		

  		return $this->db->count_all_results();
	}

	/**
	* Gets popular Items record
	*/
	function get_item_by ( $conds = array(), $limit = false, $offset = false ){

		
		//where clause
		$this->db->select('bs_items.*, count(bs_touches.item_id) as t_count');    
  		$this->db->from('bs_items');
  		$this->db->join('bs_touches', 'bs_items.id = bs_touches.item_id');
		
		
		
		if ( isset( $conds['is_sold_out'] )) {
			if ($conds['is_sold_out'] != "" ) {
				$this->db->where( 'bs_items.is_sold_out', $conds['is_sold_out'] );					
			}
		}

		//  item type id condition 
		if ( isset( $conds['item_type_id'] )) {
			if ($conds['item_type_id'] != "" ) {
				if ($conds['item_type_id'] != '0') {
					$this->db->where( 'bs_items.item_type_id', $conds['item_type_id'] );
				}
				
			}
			
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

		//  item price type id condition 
		if ( isset( $conds['item_price_type_id'] )) {
			if ($conds['item_price_type_id'] != "" ) {
				if ($conds['item_price_type_id'] != '0') {
					$this->db->where( 'bs_items.item_price_type_id', $conds['item_price_type_id'] );
				}
				
			}
			
		}

		//  item currency id condition 
		if ( isset( $conds['item_currency_id'] )) {
			if ($conds['item_currency_id'] != "" ) {
				if ($conds['item_currency_id'] != '0') {
					$this->db->where( 'bs_items.item_currency_id', $conds['item_currency_id'] );
				}
				
			}
			
		}

		//  status condition 
		if ( isset( $conds['status'] )) {
			if ($conds['status'] != "" ) {
				if ($conds['status'] != '0') {
					$this->db->where( 'bs_items.status', $conds['status'] );
				}
				
			}
			
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
		//repetitive event condition
		// $date = date('Y-m-d');
		// $day_name = date('l', strtotime($date));
		// $arr = array('bs_items.event_day'=>$day_name, 'bs_items.is_repetitive'=>'true');
		// $this->db->where($arr);
		// $this->db->or_where('bs_items.is_repetitive','false');
		// $this->db->or_where('bs_items.is_repetitive','');

  		$this->db->group_by('bs_touches.item_id');
  		$this->db->order_by('t_count', "DESC");
  		$this->db->order_by( 'bs_touches.added_date', "desc" );
  		
  		


  		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}

  		return $this->db->get();
  		//   print_r($this->db->last_query());die;

	}

	//get where in user

	function get_all_in_user( $conds = array() ) {

	  $sql = "SELECT * FROM `core_users` WHERE `user_id` IN(". $conds['chat_user_id'] .")";

		$query = $this->db->query($sql);

		return $query;

	}

	//get where in rating

	function get_all_in_rating( $conds1 = array() , $limit = false, $offset = false ) {

	  	$sql = "SELECT * FROM `bs_ratings` WHERE `id` IN(". $conds1['id'] .") ORDER BY added_date DESC LIMIT $offset,$limit ";

		$query = $this->db->query($sql);

		

		return $query;

	}		

	function get_all_in_chat( $conds = array() ) {

	  	$sql = "SELECT * FROM `bs_chat_history` WHERE `id` IN(". $conds['$id'] .") order by added_date desc";

		$query = $this->db->query($sql);

		return $query;

	}

	function get_all_not_in_noti( $conds = array() ) {

	  	$sql = "SELECT * FROM `bs_push_notification_messages` WHERE `id` NOT IN(". $conds['$id'] .") order by added_date desc";
		$query = $this->db->query($sql);

		return $query;

	}

	/**
	Returns user follow list
	*/
	function get_all_follower_by_user( $conds = array() , $limit = false, $offset = false)
	{
		//print_r($limit . "$offset");die;
		$sql = "SELECT *  FROM core_users WHERE  user_id in (". $conds['follower_and_following_user'] ." ) ORDER BY added_date DESC LIMIT $offset,$limit";
		//print_r($sql);die;
		$query = $this->db->query($sql);

		return $query;
		  //print_r($this->db->last_query());die;
		
	}

	/* Delete the records by condition
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 to get item favourite list
	 */
	function get_item_favourite( $conds = array(), $limit = false, $offset = false  )
	{
		
		$is_block_user = $this->db->get_where( 'bs_app_settings', array( 'id' => 'app1' ))->row()->is_block_user;
		//repetitive event condition
		// get by day -04-05-24 comment
		// $date = date('Y-m-d');
		// $day_name = date('l', strtotime($date));
		// $arr = array('bs_items.event_day'=>$day_name, 'bs_items.is_repetitive'=>'true' );
		// $this->db->where($arr);
		// $this->db->or_where('bs_items.is_repetitive','false');
		// $this->db->or_where('bs_items.is_repetitive','');
		// $this->db->or_where('bs_items.added_user_id',$conds['login_user_id']);
		// get by day -04-05-24
		$this->db->select('bs_items.*'); 
		$this->db->from('bs_items');
		$this->db->join('bs_favourite', 'bs_favourite.item_id = bs_items.id');

		if(isset($conds['user_id'])) {

			if ($conds['user_id'] != "" || $conds['user_id'] != 0) {
					
					$this->db->where( 'user_id', $conds['user_id'] );	

			}

		}

		// item id (id) check for user block condition
		if($is_block_user == "1"){
			if ( isset( $conds['item_id'] )) {
				
				if ($conds['item_id'] != "") {
					if($conds['item_id'] != '0'){
					
						$this->db->where_not_in( 'bs_items.id', $conds['item_id'] );	
					}

				}			
			}
		}

		// item id (id) check for item reported condition
		if ( isset( $conds['reported_item_id'] )) {
			
			if ($conds['reported_item_id'] != "") {
				if($conds['reported_item_id'] != '0'){
				
					$this->db->where_not_in( 'bs_items.id', $conds['reported_item_id'] );	
				}

			}			
		}

		// status
		if (isset( $conds['status'] )) {
			$this->db->where( 'bs_items.status', $conds['status'] );
		}
		

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			$this->db->offset($offset);
		}
		
		return $this->db->get();
		// print_r($this->db->last_query());die;
 	}

 	// get user with status 2 for request code

	function user_exists( $conds = array()) {

		$sql = "SELECT * FROM core_users WHERE `user_email` = '" . $conds['user_email'] . "' AND `status` = '" . $conds['status'] . "' ";

		$query = $this->db->query($sql);

		return $query;
	}

	// get user with email conds

	function get_one_user_email( $conds = array()) {

		$sql = "SELECT * FROM core_users WHERE `user_email` = '" . $conds['user_email'] . "' ";

		$query = $this->db->query($sql);

		return $query;
	}

	function get_all_not_in_lang( $id, $limit = false, $offset = false ) {
	  // where clause
	  $this->db->where_not_in('id', $id);

	  // from table
	  $this->db->from( $this->table_name );

	  if ( $limit ) {
	  // if there is limit, set the limit
	   
	   $this->db->limit($limit);
	  }
	  
	  if ( $offset ) {
	  // if there is offset, set the offset,
	   
	   $this->db->offset($offset);
	  }
	  
	  return $this->db->get();
	  // print_r($this->db->last_query());die;
	}

	
	function get_language_string( $conds = array() ){

		// from table
	  	$this->db->from( $this->table_name );

	  	if(isset($conds['language_id'])) {

			if ($conds['language_id'] != "" || $conds['language_id'] != 0) {
					
					$this->db->where( 'language_id', $conds['language_id'] );	

			}

		}

		if(isset($conds['key'])) {

			if ($conds['key'] != "" || $conds['key'] != 0) {
					
					$this->db->where( 'key', $conds['key'] );	

			}

		}

		if(isset($conds['value'])) {

			if ($conds['value'] != "" || $conds['value'] != 0) {
					
					$this->db->where( 'value', $conds['value'] );	

			}

		}

		return $this->db->get();

	}

	function get_all_item_by_paid ( $conds = array() , $limit = false, $offset = false ) {
		$is_block_user = $this->db->get_where( 'bs_app_settings', array( 'id' => 'app1' ))->row()->is_block_user;
		// $this->db->distinct('bs_items.id');
		$this->db->select('bs_items.*'); 
		$this->db->from('bs_items');
		$this->db->join('bs_paid_items_history', 'bs_paid_items_history.item_id = bs_items.id');
		$today_date = date('Y-m-d H:i:s');
		$this->db->where( 'bs_paid_items_history.start_date <= ', $today_date );
   		$this->db->where( 'bs_paid_items_history.end_date >= ', $today_date );

		// get by day -04-05-24 comment
		// $date = date('Y-m-d');
		// $day_name = date('l', strtotime($date));
		// $arr = array('bs_items.event_day'=>$day_name, 'bs_items.is_repetitive'=>'true' );
		// $this->db->where($arr);
		// $this->db->or_where('bs_items.is_repetitive','false');
		// $this->db->or_where('bs_items.is_repetitive','');
		// get by day -04-05-24

		if($conds['lat'] != "" && $conds['lng'] != "") {
			$this->db->select('bs_items.*,( 3959
		      * acos( cos( radians('. $conds['lat'] .') )
		              * cos(  radians( lat )   )
		              * cos(  radians( lng ) - radians('. $conds['lng'] .') )
		            + sin( radians('. $conds['lat'] .') )
		              * sin( radians( lat ) )
		            )
		    ) as distance');

		    if ($conds['miles'] == "") {
		    	$conds['miles'] = 0;
		    	$this->db->having('distance < ' .  $conds['miles'] );
		    } else {
		    	$this->db->having('distance < ' .  $conds['miles'] );

		    }

		    $this->db->order_by('distance' );
		}
		
   		// item id (id) check for user block condition
		if($is_block_user == "1"){
			if ( isset( $conds['item_id'] )) {
				
				if ($conds['item_id'] != "") {
					if($conds['item_id'] != '0'){
					
						$this->db->where_not_in( 'bs_items.id', $conds['item_id'] );	
					}

				}			
			}
		}

		// item id (id) check for item reported condition
		if ( isset( $conds['reported_item_id'] )) {
			
			if ($conds['reported_item_id'] != "") {
				if($conds['reported_item_id'] != '0'){
				
					$this->db->where_not_in( 'bs_items.id', $conds['reported_item_id'] );	
				}

			}			
		}

        // item id (id) check for item id by amenity condition
        if ( isset( $conds['item_id_by_amenity'] )) {

            if ($conds['item_id_by_amenity'] != "") {
                if($conds['item_id_by_amenity'] != '0'){

                    $this->db->where_in( 'bs_items.id', $conds['item_id_by_amenity'] );
                }

            }
        }


		//is_paid
		if ( isset( $conds['is_paid'] )) {
			
			if ($conds['is_paid'] != "") {
				if($conds['is_paid'] != '0'){
				
					$this->db->where( 'bs_items.is_paid', $conds['is_paid'] );	
				}

			}			
		}


		// location id

		if ( isset( $conds['item_location_city_id'] )) {

			//repetitive event condition
			// $date = date('Y-m-d');
			// $day_name = date('l', strtotime($date));
			// $arr = array('bs_items.event_day'=>$day_name, 'bs_items.is_repetitive'=>'true');
			// $this->db->where($arr);
			
			if ($conds['item_location_city_id'] != "") {
				if($conds['item_location_city_id'] != '0'){
				
					$this->db->where( 'bs_items.item_location_city_id', $conds['item_location_city_id'] );	
				}

			}			
		}

		// township id

		if ( isset( $conds['item_location_township_id'] )) {
			
			if ($conds['item_location_township_id'] != "") {
				if($conds['item_location_township_id'] != '0'){
				
					$this->db->where( 'bs_items.item_location_township_id', $conds['item_location_township_id'] );	
				}

			}			
		}

		// is_sold_out id condition
		if ( isset( $conds['is_sold_out'] )) {
			$this->db->where( 'bs_items.is_sold_out', $conds['is_sold_out'] );
		}

        // Property type id
		if ( isset( $conds['property_by_id'] )) {
			
			if ($conds['property_by_id'] != "") {
				if($conds['property_by_id'] != '0'){
				
					$this->db->where( 'bs_items.property_by_id', $conds['property_by_id'] );	
				}

			}			
		}

        // posted_by_id
        if ( isset( $conds['posted_by_id'] )) {

            if ($conds['posted_by_id'] != "") {
                if($conds['posted_by_id'] != '0'){

                    $this->db->where( 'bs_items.posted_by_id', $conds['posted_by_id'] );
                }

            }
        }


		// added_user_id
		if ( isset( $conds['added_user_id'] )) {
			
			if ($conds['added_user_id'] != "") {
				if($conds['added_user_id'] != '0'){
				
					$this->db->where( 'bs_items.added_user_id', $conds['added_user_id'] );	
				}

			}			
		}

		if(isset($conds['min_price'])) {

			if ($conds['min_price'] != "" || $conds['min_price'] != 0) {
					
					$this->db->where( 'bs_items.price>=', $conds['min_price'] );	

			}

		}

		if(isset($conds['max_price'])) {

			if ($conds['max_price'] != "" || $conds['max_price'] != 0) {
					
					$this->db->where( 'bs_items.price<=', $conds['max_price'] );	

			}

		}

		// Price id
		if ( isset( $conds['item_price_type_id'] )) {
			
			if ($conds['item_price_type_id'] != "") {
				if($conds['item_price_type_id'] != '0'){
				
					$this->db->where( 'bs_items.item_price_type_id', $conds['item_price_type_id'] );	
				}

			}			
		}
		
		// Currency id
		if ( isset( $conds['item_currency_id'] )) {
			
			if ($conds['item_currency_id'] != "") {
				if($conds['item_currency_id'] != '0'){
				
					$this->db->where( 'bs_items.item_currency_id', $conds['item_currency_id'] );	
				}

			}			
		}

		// condition_of_item id condition
		if ( isset( $conds['condition_of_item_id'] )) {
			$this->db->where( 'bs_items.condition_of_item_id', $conds['condition_of_item_id'] );
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
		// 		$this->db->like( 'bs_items.title', $cond_name1 );
		// 		$this->db->or_like( 'bs_items.title', $cond_name2 );
		// 		$this->db->or_like( 'bs_items.title', $cond_name3 );
		// 		$this->db->or_like( 'bs_items.description', $conds['searchterm'] );
		// 		$this->db->or_like( 'bs_items.condition_of_item_id', $conds['searchterm'] );
		// 		$this->db->or_like( 'bs_items.highlight_info', $conds['searchterm'] );
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

			$this->db->where('bs_items.title REGEXP', $str);
			$this->db->or_where('bs_items.title REGEXP', $str_rev);
			$this->db->or_like('bs_items.description', $conds['searchterm']);
			$this->db->or_like('bs_items.condition_of_item_id', $conds['searchterm']);
			$this->db->or_like('bs_items.highlight_info', $conds['searchterm']);
			$this->db->group_end();
		}

		if ( $limit ) {
			// if there is limit, set the limit
		 
			 $this->db->limit($limit);
		}
	
		if ( $offset ) {
			// if there is offset, set the offset,
		 
			 $this->db->offset($offset);
		}
		
		// order by
		if ( isset( $conds['order_by_field'] )) {
			$order_by_field = $conds['order_by_field'];
			$order_by_type = $conds['order_by_type'];
			
			$this->db->order_by( 'bs_items.'.$order_by_field, $order_by_type); 
		} else {
			$this->db->order_by('added_date', 'desc' );
		}
		// $this->db->order_by('bs_paid_items_history.start_date' , 'asc');
		
   		$query = $this->db->get();
   		return ( $query ) ? $query->result() : [] ;
		//return $query->result();
   		// print_r($this->db->last_query());die;
	}

	function get_item_by_paid_progress ( $item_id= array() ) {
		

		$this->db->select('bs_paid_items_history.*'); 
		$this->db->from('bs_paid_items_history');
		$this->db->where_in('item_id', $item_id);
		$today_date = date('Y-m-d H:i:s');
		$this->db->group_start();
		$this->db->where( 'bs_paid_items_history.start_date <= ', $today_date );
   		$this->db->where( 'bs_paid_items_history.end_date >= ', $today_date );
   		$this->db->or_where( 'bs_paid_items_history.end_date >= ', $today_date );
   		$this->db->group_end();
   		return $this->db->get();
   		//print_r($this->db->last_query());die;
	}
	

	// function get_all_item_by_paid_date ( $conds = array(), $limit = false, $offset = false ) {
	// 	$is_block_user = $this->db->get_where( 'bs_app_settings', array( 'id' => 'app1' ))->row()->is_block_user;
	// 	$this->db->select('bs_items.*'); 

	// 	//Start - Modify By PPH @ 12 May 2020
	// 	if($conds['lat'] != "" && $conds['lng'] != "") {
	// 		$this->db->select('( 3959
	// 	      * acos( cos( radians('. $conds['lat'] .') )
	// 	              * cos(  radians( lat )   )
	// 	              * cos(  radians( lng ) - radians('. $conds['lng'] .') )
	// 	            + sin( radians('. $conds['lat'] .') )
	// 	              * sin( radians( lat ) )
	// 	            )
	// 	    ) as distance');

	// 	    if ($conds['miles'] == "") {
	// 	    	$conds['miles'] = 0;
	// 	    	$this->db->having('distance < ' .  $conds['miles'] );
	// 	    } else {
	// 	    	$this->db->having('distance < ' .  $conds['miles'] );

	// 	    }
	// 	    $this->db->order_by('distance' ); //
	// 	}
	// 	//End - Modify By PPH @ 12 May 2020


	// 	$this->db->from('bs_items');
	// 	$this->db->join('bs_paid_items_history', 'bs_paid_items_history.item_id = bs_items.id');
	// 	$today_date = date('Y-m-d H:i:s');
	// 	$this->db->where( 'bs_items.status',$conds['status']);
	// 	$this->db->where( 'bs_items.is_paid',$conds['is_paid']);
	// 	$this->db->where( 'bs_paid_items_history.start_date <= ', $today_date );
   	// 	$this->db->where( 'bs_paid_items_history.end_date >= ', $today_date );
   		
   	// 	// location id
	// 	if ( isset( $conds['item_location_city_id'] )) {
			
	// 		if ($conds['item_location_city_id'] != "") {
	// 			if($conds['item_location_city_id'] != '0'){
				
	// 				$this->db->where( 'item_location_city_id', $conds['item_location_city_id'] );	
	// 			}

	// 		}			
	// 	}

	// 	// township id
	// 	if ( isset( $conds['item_location_township_id'] )) {
	
	// 		if ($conds['item_location_township_id'] != "") {
	// 			if($conds['item_location_township_id'] != '0'){
				
	// 				$this->db->where( 'item_location_township_id', $conds['item_location_township_id'] );	
	// 			}

	// 		}			
	// 	}

	// 	if ( isset( $conds['is_sold_out'] )) {
	// 		$this->db->where( 'is_sold_out', $conds['is_sold_out'] );
	// 	}

	// 	//Start - Modify By PPH @ 12 May 2020
	// 	// if ( isset( $conds['cat_id'] )) {
			
	// 	// 	if ($conds['cat_id'] != "") {
	// 	// 		if($conds['cat_id'] != '0'){
				
	// 	// 			$this->db->where( 'cat_id', $conds['cat_id'] );	
	// 	// 		}

	// 	// 	}			
	// 	// }
	// 	//End - Modify By PPH @ 12 May 2020
        	

	// 	// item id (id) check for user block condition
	// 	if($is_block_user == "1"){
	// 		if ( isset( $conds['item_id'] )) {
				
	// 			if ($conds['item_id'] != "") {
	// 				if($conds['item_id'] != '0'){
					
	// 					$this->db->where_not_in( 'bs_items.id', $conds['item_id'] );	
	// 				}

	// 			}			
	// 		}
	// 	}

	// 	// item id (id) check for item reported condition
	// 	if ( isset( $conds['reported_item_id'] )) {
			
	// 		if ($conds['reported_item_id'] != "") {
	// 			if($conds['reported_item_id'] != '0'){
				
	// 				$this->db->where_not_in( 'bs_items.id', $conds['reported_item_id'] );	
	// 			}

	// 		}			
	// 	}

    //     // item id (id) check for item id by amenity condition
    //     if ( isset( $conds['item_id_by_amenity'] )) {

    //         if ($conds['item_id_by_amenity'] != "") {
    //             if($conds['item_id_by_amenity'] != '0'){

    //                 $this->db->where_in( 'bs_items.id', $conds['item_id_by_amenity'] );
	// 				// print_r($this->db->last_query());
    //             }

    //         }
    //     }
        

    //     // Property type id
	// 	if ( isset( $conds['property_by_id'] )) {
			
	// 		if ($conds['property_by_id'] != "") {
	// 			if($conds['property_by_id'] != '0'){
				
	// 				$this->db->where( 'property_by_id', $conds['property_by_id'] );	
	// 			}

	// 		}			
	// 	}

    //     // posted_by_id
    //     if ( isset( $conds['posted_by_id'] )) {

    //         if ($conds['posted_by_id'] != "") {
    //             if($conds['posted_by_id'] != '0'){

    //                 $this->db->where( 'posted_by_id', $conds['posted_by_id'] );
    //             }

    //         }
    //     }


	// 	// added_user_id
	// 	if ( isset( $conds['added_user_id'] )) {
			
	// 		if ($conds['added_user_id'] != "") {
	// 			if($conds['added_user_id'] != '0'){
				
	// 				$this->db->where( 'bs_items.added_user_id', $conds['added_user_id'] );	
	// 			}

	// 		}			
	// 	}

	// 	if(isset($conds['min_price'])) {

	// 		if ($conds['min_price'] != "" || $conds['min_price'] != 0) {
					
	// 				$this->db->where( 'price>=', $conds['min_price'] );	

	// 		}

	// 	}

	// 	if(isset($conds['max_price'])) {

	// 		if ($conds['max_price'] != "" || $conds['max_price'] != 0) {
					
	// 				$this->db->where( 'price<=', $conds['max_price'] );	

	// 		}

	// 	}

	// 	// searchterm --- improvement
	// 	if (isset($conds['searchterm'])) {
	// 		$search1 = explode(" ", trim($conds['searchterm']));
	// 		//print_r($search1);die;
	// 		for ($i=0; $i <count($search1) ; $i++) { 
	// 			$cond_name1 = $search1[$i];
	// 			$cond_name2 = substr($cond_name1, 0,3);
	// 			$cond_name3 = substr($cond_name1, -3);

				
	// 			$this->db->group_start();
	// 			$this->db->like( 'title', $cond_name1 );
	// 			$this->db->or_like( 'title', $cond_name2 );
	// 			$this->db->or_like( 'title', $cond_name3 );
	// 			$this->db->or_like( 'description', $conds['searchterm'] );
	// 			$this->db->or_like( 'condition_of_item_id', $conds['searchterm'] );
	// 			$this->db->or_like( 'highlight_info', $conds['searchterm'] );
	// 			$this->db->group_end();
	// 		}
	// 	}
		
   	// 	if ( $limit ) {
	// 	  	// if there is limit, set the limit
		   
	// 	   	$this->db->limit($limit);
	//   	}
	  
	//   	if ( $offset ) {
	// 	  	// if there is offset, set the offset,
		   
	// 	   	$this->db->offset($offset);
	//   	}
   	// 	$query1 = $this->db->get_compiled_select();
   		
	//   	// from table
	//   	$this->db->from( $this->table_name );

	//   	//Start - Modify By PPH @ 12 May 2020

	//   	if($conds['lat'] != "" && $conds['lng'] != "") {
	// 		$this->db->select('*,( 3959
	// 	      * acos( cos( radians('. $conds['lat'] .') )
	// 	              * cos(  radians( lat )   )
	// 	              * cos(  radians( lng ) - radians('. $conds['lng'] .') )
	// 	            + sin( radians('. $conds['lat'] .') )
	// 	              * sin( radians( lat ) )
	// 	            )
	// 	    ) as distance');

	// 	    if ($conds['miles'] == "") {
	// 	    	$conds['miles'] = 0;
	// 	    	$this->db->having('distance < ' .  $conds['miles'] );
	// 	    } else {
	// 	    	$this->db->having('distance < ' .  $conds['miles'] );

	// 	    }
	// 	    $this->db->order_by('distance' ); //
	// 	}

	// 	// default where clause
	// 	if (isset( $conds['status'] )) {
	// 		$this->db->where( 'status', $conds['status'] );
	// 	}


	// 	// default where clause
	// 	if (isset( $conds['is_sold_out'] )) {
	// 		$this->db->where( 'is_sold_out', $conds['is_sold_out'] );
	// 	}

		
	// 	if(isset($conds['min_price'])) {

	// 		if ($conds['min_price'] != "" || $conds['min_price'] != 0) {
					
	// 				$this->db->where( 'price>=', $conds['min_price'] );	

	// 		}

	// 	}

	// 	if(isset($conds['max_price'])) {

	// 		if ($conds['max_price'] != "" || $conds['max_price'] != 0) {
					
	// 				$this->db->where( 'price<=', $conds['max_price'] );	

	// 		}

	// 	}

	// 	if(isset($conds['deal_option_id'])) {

	// 		if ($conds['deal_option_id'] != "" || $conds['deal_option_id'] != 0) {
					
	// 				$this->db->where( 'deal_option_id', $conds['deal_option_id'] );	

	// 		}

	// 	}

		

	// 	//End - Modify By PPH @ 12 May 2020

	// 	// item id (id) check for user block condition
	// 	if($is_block_user == "1"){
	// 		if ( isset( $conds['item_id'] )) {
				
	// 			if ($conds['item_id'] != "") {
	// 				if($conds['item_id'] != '0'){
					
	// 					$this->db->where_not_in( 'bs_items.id', $conds['item_id'] );	
	// 				}

	// 			}			
	// 		}
	// 	}

	// 	// item id (id) check for item reported condition
	// 	if ( isset( $conds['reported_item_id'] )) {
			
	// 		if ($conds['reported_item_id'] != "") {
	// 			if($conds['reported_item_id'] != '0'){
				
	// 				$this->db->where_not_in( 'bs_items.id', $conds['reported_item_id'] );	
	// 			}

	// 		}			
	// 	}

    //     // item id (id) check for item id by amenity condition
    //     if ( isset( $conds['item_id_by_amenity'] )) {

    //         if ($conds['item_id_by_amenity'] != "") {
    //             if($conds['item_id_by_amenity'] != '0'){

    //                 $this->db->where_in( 'bs_items.id', $conds['item_id_by_amenity'] );
	// 				// print_r($this->db->last_query());
    //             }

    //         }
    //     }

	// 	// id condition
	// 	if ( isset( $conds['added_user_id'] )) {
	// 		if($conds['added_user_id'] != '0'){
				
	// 				$this->db->where( 'added_user_id', $conds['added_user_id'] );
	// 			}
			
	// 	}

	// 	// Type id
	// 	if ( isset( $conds['item_type_id'] )) {
			
	// 		if ($conds['item_type_id'] != "") {
	// 			if($conds['item_type_id'] != '0'){
				
	// 				$this->db->where( 'item_type_id', $conds['item_type_id'] );	
	// 			}

	// 		}			
	// 	}

	// 	// Property type id
	// 	if ( isset( $conds['property_by_id'] )) {
			
	// 		if ($conds['property_by_id'] != "") {
	// 			if($conds['property_by_id'] != '0'){
				
	// 				$this->db->where( 'property_by_id', $conds['property_by_id'] );	
	// 			}

	// 		}			
	// 	}

    //     // posted_by_id
    //     if ( isset( $conds['posted_by_id'] )) {

    //         if ($conds['posted_by_id'] != "") {
    //             if($conds['posted_by_id'] != '0'){

    //                 $this->db->where( 'posted_by_id', $conds['posted_by_id'] );
    //             }

    //         }
    //     }
	  
	// 	// Price id
	// 	if ( isset( $conds['item_price_type_id'] )) {
			
	// 		if ($conds['item_price_type_id'] != "") {
	// 			if($conds['item_price_type_id'] != '0'){
				
	// 				$this->db->where( 'item_price_type_id', $conds['item_price_type_id'] );	
	// 			}

	// 		}			
	// 	}

	// 	// location id
	// 	if ( isset( $conds['item_location_city_id'] )) {
			
	// 		if ($conds['item_location_city_id'] != "") {
	// 			if($conds['item_location_city_id'] != '0'){
				
	// 				$this->db->where( 'item_location_city_id', $conds['item_location_city_id'] );	
	// 			}

	// 		}			
	// 	}

	// 	// township id
	// 	if ( isset( $conds['item_location_township_id'] )) {
	
	// 		if ($conds['item_location_township_id'] != "") {
	// 			if($conds['item_location_township_id'] != '0'){
				
	// 				$this->db->where( 'item_location_township_id', $conds['item_location_township_id'] );	
	// 			}

	// 		}			
	// 	}
	   
	// 	// Currency id
	// 	if ( isset( $conds['item_currency_id'] )) {
			
	// 		if ($conds['item_currency_id'] != "") {
	// 			if($conds['item_currency_id'] != '0'){
				
	// 				$this->db->where( 'item_currency_id', $conds['item_currency_id'] );	
	// 			}

	// 		}			
	// 	}

	// 	// condition_of_item id condition
	// 	if ( isset( $conds['condition_of_item_id'] )) {
	// 		$this->db->where( 'condition_of_item_id', $conds['condition_of_item_id'] );
	// 	}
        
    //     		// added_user_id id
	// 	if ( isset( $conds['added_user_id'] )) {
			
	// 		if ($conds['added_user_id'] != "") {
	// 			if($conds['added_user_id'] != '0'){
				
	// 				$this->db->where( 'bs_items.added_user_id', $conds['added_user_id'] );	
	// 			}

	// 		}			
	// 	}

	// 	// searchterm --- improvement
	// 	if (isset($conds['searchterm'])) {
	// 		$search1 = explode(" ", trim($conds['searchterm']));
	// 		//print_r($search1);die;
	// 		for ($i=0; $i <count($search1) ; $i++) { 
	// 			$cond_name1 = $search1[$i];
	// 			$cond_name2 = substr($cond_name1, 0,3);
	// 			$cond_name3 = substr($cond_name1, -3);

				
	// 			$this->db->group_start();
	// 			$this->db->like( 'title', $cond_name1 );
	// 			$this->db->or_like( 'title', $cond_name2 );
	// 			$this->db->or_like( 'title', $cond_name3 );
	// 			$this->db->or_like( 'description', $conds['searchterm'] );
	// 			$this->db->or_like( 'condition_of_item_id', $conds['searchterm'] );
	// 			$this->db->or_like( 'highlight_info', $conds['searchterm'] );
	// 			$this->db->group_end();
	// 		}
	// 	}

	//   	if ( $limit ) {
	// 	  	// if there is limit, set the limit
		   
	// 	   	$this->db->limit($limit);
	//   	}
	  
	//   	if ( $offset ) {
	// 	  	// if there is offset, set the offset,
		   
	// 	   	$this->db->offset($offset);
	//   	}


	  
	  	
	//   	// order by
	// 	if ( isset( $conds['order_by_field'] )) {
	// 		$order_by_field = $conds['order_by_field'];
	// 		$order_by_type = $conds['order_by_type'];
			
	// 		$this->db->order_by( 'bs_items.'.$order_by_field, $order_by_type);
	// 	} else {
	// 		$this->db->order_by('added_date', 'desc' );
	// 	}
	//     $query2 = $this->db->get_compiled_select();

	    

	//     $query = $this->db->query('( '. $query1 . ' ) UNION DISTINCT (' . $query2 .') ');

	//     //print_r('( '. $query1 . ' ) UNION DISTINCT (' . $query2 .') ');die;
	 	
	//   	return $query;
	// }

	/**
	 * Get all items by paid item first
	 */
	function get_all_item_by_paid_date ( $conds = array(), $limit = false, $offset = false ) {
		
		$conds_paid = $conds;
		$conds_paid['is_paid'] = 1;
		$paid_data = $this->get_all_item_by_paid($conds_paid);

		$conds_normal = $conds;
		$normal_data = $this->get_all_item_by_normal_ads($conds_normal);

		$data = array();

		foreach($paid_data as $item){
			$data[] = $item;
		}

		foreach($normal_data as $item){
			$data[] = $item;
		}

		// print_r($data);die;

		$result = array();
	
		if($limit) {
			foreach($data as $item){
				if($offset == 0){
					if($limit>0){
						$result[] = $item;
						$limit--;
					}
				}else{
					if($offset>0){
						$offset--;
						continue;
					}
				}
				
			}
		}else{
			$result = $data;
		}

		return $result;
	}

	/**
	 * Sponsored Ad first with google ads and normal ads
	 * paid_item_first_with_google
	 */
	function get_all_item_by_paid_date_with_google ( $conds = array(), $limit = false, $offset = false ) {
		
		$promo_cell_interval_no = $this->db->get_where( 'bs_app_settings', array( 'id' => 'app1' ))->row()->promo_cell_interval_no;
		
		$google_data = $this->get_empty_object( 'bs_items' );
		unset($google_data->is_empty_object);
		$google_data->ad_type = 'google_ad';

		$conds_paid = $conds;
		$conds_paid['is_paid'] = 1;
		$paid_data = $this->get_all_item_by_paid($conds_paid);

		$conds_normal = $conds;
		$normal_data = $this->get_all_item_by_normal_ads($conds_normal);

		$paid_count = count($paid_data);
		$normal_count = count($normal_data);
		
		$j = 0; $k = 0;
		$data = array();
		$extra = 0;

		// Traverse both array
		while ($j < $paid_count)
		{
			
			$interval = 0;
			while($interval < $promo_cell_interval_no && $j < $paid_count){
				$data[$k++] = $paid_data[$j++];
				$interval++;
			}

			if($interval % $promo_cell_interval_no == 0){
				$data[$k++] = $google_data;
			}else{
				$extra = $interval;
			}			
		}
			
		$j = 0;
		// Traverse both array
		while ($j < $normal_count)
		{
			if($extra>0){
				while($extra < $promo_cell_interval_no && $j < $normal_count){
					$data[$k++] = $normal_data[$j++];
					$extra++;
				}
				$data[$k++] = $google_data;
				$extra = 0;
			}

			$interval = 0;
			while($interval < $promo_cell_interval_no && $j < $normal_count){
				$data[$k++] = $normal_data[$j++];
				$interval++;
			}

			if($interval % $promo_cell_interval_no == 0){
				$data[$k++] = $google_data;
			}
		}

		$result = array();
	
		if($limit) {
			foreach($data as $item){
				if($offset == 0){
					if($limit>0){
						$result[] = $item;
						$limit--;
					}
				}else{
					if($offset>0){
						$offset--;
						continue;
					}
				}
				
			}
		}else{
			$result = $data;
		}

		return $result;
	}
	
	/** 
	 * Get all normal ads only
	 */
	function get_all_item_by_normal_ads ( $conds, $limit = false, $offset = false  ) {
		
		$is_block_user = $this->db->get_where( 'bs_app_settings', array( 'id' => 'app1' ))->row()->is_block_user;
		
		$this->db->select('bs_items.*'); 
		$this->db->from( $this->table_name );
		$this->db->join('bs_paid_items_history', 'bs_paid_items_history.item_id = bs_items.id');
		$today_date = date('Y-m-d H:i:s');
   		$this->db->where( 'bs_paid_items_history.start_date <= ', $today_date );
   		$this->db->where( 'bs_paid_items_history.end_date >= ', $today_date );
		// get by day -04-05-24 comment
		//repetitive event condition
		// $date = date('Y-m-d');
		// $day_name = date('l', strtotime($date));
		// $arr = array('bs_items.event_day'=>$day_name, 'bs_items.is_repetitive'=>'true' );
		// $this->db->where($arr);
		// $this->db->or_where('bs_items.is_repetitive','false');
		// $this->db->or_where('bs_items.is_repetitive','');
		// $this->db->or_where('bs_items.added_user_id',$conds['login_user_id']);
		// get by day -04-05-24
		
		if($conds['lat'] != "" && $conds['lng'] != "") {
			$this->db->select('bs_items.*,( 3959
		      * acos( cos( radians('. $conds['lat'] .') )
		              * cos(  radians( lat )   )
		              * cos(  radians( lng ) - radians('. $conds['lng'] .') )
		            + sin( radians('. $conds['lat'] .') )
		              * sin( radians( lat ) )
		            )
		    ) as distance');

		    if ($conds['miles'] == "") {
		    	$conds['miles'] = 0;
		    	$this->db->having('distance < ' .  $conds['miles'] );
		    } else {
		    	$this->db->having('distance < ' .  $conds['miles'] );

		    }

		    $this->db->order_by('distance' );
		}
		
		// default where clause
		if (isset( $conds['status'] )) {
			$this->db->where( 'bs_items.status', $conds['status'] );
		}
		
		if(isset($conds['min_price'])) {

			if ($conds['min_price'] != "" || $conds['min_price'] != 0) {
					
					$this->db->where( 'price>=', $conds['min_price'] );	

			}

		}

		if(isset($conds['max_price'])) {

			if ($conds['max_price'] != "" || $conds['max_price'] != 0) {
					
					$this->db->where( 'price<=', $conds['max_price'] );	

			}

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

		// location id
		if ( isset( $conds['item_location_city_id'] )) {
			
			if ($conds['item_location_city_id'] != "") {
				if($conds['item_location_city_id'] != '0'){
				
					$this->db->where( 'item_location_city_id', $conds['item_location_city_id'] );	
				}

			}			
		}

		// township id
		if ( isset( $conds['item_location_township_id'] )) {
			
			if ($conds['item_location_township_id'] != "") {
				if($conds['item_location_township_id'] != '0'){
				
					$this->db->where( 'item_location_township_id', $conds['item_location_township_id'] );	
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

		// item id (id) check for user block condition
		if($is_block_user == "1"){
    if (isset($conds['item_id']) && !empty($conds['item_id'])) { 
        $this->db->where_not_in('bs_items.id', $conds['item_id']);  
    }
}

		// item id (id) check for item reported condition
		if (isset($conds['reported_item_id']) && !empty($conds['reported_item_id'])) {
    $this->db->where_not_in('bs_items.id', $conds['reported_item_id']);  
}

		// item id (id) check for item id by amenity condition
		if ( isset( $conds['item_id_by_amenity'] )) {

			if ($conds['item_id_by_amenity'] != "") {
				if($conds['item_id_by_amenity'] != '0'){

					$this->db->where_in( 'bs_items.id', $conds['item_id_by_amenity'] );
				}

			}
		}

		// added_user_id id
		if ( isset( $conds['added_user_id'] )) {
			
			if ($conds['added_user_id'] != "") {
				if($conds['added_user_id'] != '0'){
				
					$this->db->where( 'bs_items.added_user_id', $conds['added_user_id'] );	
				}

			}			
		}

		// is_sold_out id condition
		if ( isset( $conds['is_sold_out'] )) {
			$this->db->where( 'is_sold_out', $conds['is_sold_out'] );
		}

		// discount rate
		if ( isset( $conds['is_discount'] )) {
			if ($conds['is_discount'] == "1") {
				$this->db->where( 'discount_rate_by_percentage !=', '0' );				
				$this->db->where( 'discount_rate_by_percentage !=', '' );			
			}	
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
	  	
	  	// order by
		if ( isset( $conds['order_by_field'] )) {
			
			$order_by_field = $conds['order_by_field'];
			$order_by_type = $conds['order_by_type'];
			
			$this->db->order_by( 'bs_items.'.$order_by_field, $order_by_type);
		} else {
			$this->db->order_by('bs_items.added_date', 'desc' );
		}

		$query1 = $this->db->get();
	  	// from table
	  	$this->db->from( $this->table_name );
		

		  if($conds['lat'] != "" && $conds['lng'] != "") {
			$this->db->select('*,( 3959
		      * acos( cos( radians('. $conds['lat'] .') )
		              * cos(  radians( lat )   )
		              * cos(  radians( lng ) - radians('. $conds['lng'] .') )
		            + sin( radians('. $conds['lat'] .') )
		              * sin( radians( lat ) )
		            )
		    ) as distance');

		    if ($conds['miles'] == "") {
		    	$conds['miles'] = 0;
		    	$this->db->having('distance < ' .  $conds['miles'] );
		    } else {
		    	$this->db->having('distance < ' .  $conds['miles'] );

		    }

		    $this->db->order_by('distance' );
		}
		
		// default where clause
		if (isset( $conds['status'] )) {
			$this->db->where( 'bs_items.status', $conds['status'] );
		}
		
		if(isset($conds['min_price'])) {

			if ($conds['min_price'] != "" || $conds['min_price'] != 0) {
					
					$this->db->where( 'price>=', $conds['min_price'] );	

			}

		}

		if(isset($conds['max_price'])) {

			if ($conds['max_price'] != "" || $conds['max_price'] != 0) {
					
					$this->db->where( 'price<=', $conds['max_price'] );	

			}

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

		// location id
		if ( isset( $conds['item_location_city_id'] )) {
			
			if ($conds['item_location_city_id'] != "") {
				if($conds['item_location_city_id'] != '0'){
				
					$this->db->where( 'item_location_city_id', $conds['item_location_city_id'] );	
				}

			}			
		}

		// township id
		if ( isset( $conds['item_location_township_id'] )) {
			
			if ($conds['item_location_township_id'] != "") {
				if($conds['item_location_township_id'] != '0'){
				
					$this->db->where( 'item_location_township_id', $conds['item_location_township_id'] );	
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

		// item id (id) check for user block condition
		if($is_block_user == "1"){
    if (isset($conds['item_id']) && !empty($conds['item_id'])) { 
        $this->db->where_not_in('bs_items.id', $conds['item_id']);  
    }
}

		// item id (id) check for item reported condition
		if (isset($conds['reported_item_id']) && !empty($conds['reported_item_id'])) {
    $this->db->where_not_in('bs_items.id', $conds['reported_item_id']);  
}

		// item id (id) check for item id by amenity condition
	if (isset($conds['item_id_by_amenity']) && !empty($conds['item_id_by_amenity'])) {
    $this->db->where_in('bs_items.id', $conds['item_id_by_amenity']);
}

		// added_user_id id
		if ( isset( $conds['added_user_id'] )) {
			
			if ($conds['added_user_id'] != "") {
				if($conds['added_user_id'] != '0'){
				
					$this->db->where( 'bs_items.added_user_id', $conds['added_user_id'] );	
				}

			}			
		}

		// is_sold_out id condition
		if ( isset( $conds['is_sold_out'] )) {
			$this->db->where( 'is_sold_out', $conds['is_sold_out'] );
		}

		// discount rate
		if ( isset( $conds['is_discount'] )) {
			if ($conds['is_discount'] == "1") {
				$this->db->where( 'discount_rate_by_percentage !=', '0' );				
				$this->db->where( 'discount_rate_by_percentage !=', '' );			
			}	
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
		
	  	// order by
		if ( isset( $conds['order_by_field'] )) {
			$order_by_field = $conds['order_by_field'];
			$order_by_type = $conds['order_by_type'];
			
			$this->db->order_by( 'bs_items.'.$order_by_field, $order_by_type);
		} else {
			$this->db->order_by('bs_items.added_date', 'desc' );
		}
		//repetitive event condition
		// get by day -04-05-24 comment
		// $date = date('Y-m-d');
		// $day_name = date('l', strtotime($date));
		// $arr = array('bs_items.event_day'=>$day_name, 'bs_items.is_repetitive'=>'true' );
		// $this->db->where($arr);
		// $this->db->or_where('bs_items.is_repetitive','false');
		// $this->db->or_where('bs_items.is_repetitive','');
		// $this->db->or_where('bs_items.added_user_id',$conds['login_user_id']);
		
		// get by day end - -04-05-24
		$query2 = $this->db->get();
		// echo $this->db->last_query($query1);
		// echo $this->db->last_query($query2); die;
		$query = array();
		if (!$query1) {
    print_r($this->db->error()); // Will show SQL error details
}
		$q1 = $query1->result();
		$q2 = $query2->result();
		if($limit) $limit=$limit+1; 
		foreach($q2 as $item){
			if(in_array($item, $q1)){
				continue;
			}
			 
			if($limit){
				if($offset == 0 ){
					if($limit > 1){
						$query[] = $item;
						$limit = $limit-1;
					}else{ 
						break;
					}
				}else{
					$offset = $offset-1;
				}
				
			}else{
				$query[] = $item;
			}
		}

	    return $query;
		// echo $this->db->last_query($query); die;

	}

	/**
	 * Get all items by bumps ups
	 */
	function get_all_item_by_bumps_up ( $conds = array(), $limit = false, $offset = false ) {
		
		$promo_cell_interval_no = $this->db->get_where( 'bs_app_settings', array( 'id' => 'app1' ))->row()->promo_cell_interval_no;
		
		$conds_paid = $conds;
		$conds_paid['is_paid'] = 1;
		$paid_data = $this->get_all_item_by_paid($conds_paid);

		$conds_normal = $conds;
		$normal_data = $this->get_all_item_by_normal_ads($conds_normal);

		$paid_count = count($paid_data);
		$normal_count = count($normal_data);


		$i = 0; $j = 0; $k = 0;
		$data = array();
		
		// Traverse both array
		while ($i < $paid_count && $j < $normal_count)
		{

			$interval = 0;
			while($interval < $promo_cell_interval_no && $j < $normal_count){
				$data[$k++] = $normal_data[$j++];
				$interval++;
			}
			
			if($interval % $promo_cell_interval_no == 0){
				$data[$k++] = $paid_data[$i++];
			}
		}

		// Store remaining elements
		// of first array (paid_data)
		while ($i < $paid_count)
			$data[$k++] = $paid_data[$i++];

		// Store remaining elements
		// of second array (normal_data)
		while($j < $normal_count)
			$data[$k++] = $normal_data[$j++];
		
	  	
		$result = array();
	
		if($limit) {
			foreach($data as $item){
				if($offset == 0){
					if($limit>0){
						$result[] = $item;
						$limit--;
					}
				}else{
					if($offset>0){
						$offset--;
						continue;
					}
				}
				
			}
		}else{
			$result = $data;
		}

		return $result;
	}

	/**
	 * Get all items by bumps ups and google ads post
	 */
	function get_all_item_by_bumps_up_google_ads ( $conds = array(), $limit = false, $offset = false ) {
		$google_data = $this->get_empty_object( 'bs_items' );
		unset($google_data->is_empty_object);
		$google_data->ad_type = 'google_ad';
		
		$promo_cell_interval_no = $this->db->get_where( 'bs_app_settings', array( 'id' => 'app1' ))->row()->promo_cell_interval_no;
		
		$conds_paid = $conds;
		$conds_paid['is_paid'] = 1;
		$paid_data = $this->get_all_item_by_paid($conds_paid);

		$conds_normal = $conds;
		$normal_data = $this->get_all_item_by_normal_ads($conds_normal);

		$paid_count = count($paid_data);
		$normal_count = count($normal_data);
		
		$i = 0; $j = 0; $k = 0; $g = 0;
		$data = array();
		
		// Traverse both array
		while ($i < $paid_count && $j < $normal_count)
		{
			
			$interval = 0;
			while($interval < $promo_cell_interval_no && $j < $normal_count ){
				$data[$k++] = $normal_data[$j++];
				$interval++;
			}

			if($interval % $promo_cell_interval_no == 0){
				if($g % 2 == 0){
					$data[$k++] = $paid_data[$i++];
					$g++;
				}else{
					$data[$k++] = $google_data;
					$g++;
				}
			}
		}

		if($i < $paid_count && $g%2 != 0){
			$data[$k++] = $google_data;
		}

		// Store remaining elements of first array (paid_data)
		while ($i < $paid_count){
			$data[$k++] = $paid_data[$i++];
			$data[$k++] = $google_data;
		}		

		// Store remaining elements of second array (normal_data)
		while($j < $normal_count){
			$interval = 0;
			while($interval < $promo_cell_interval_no && $j < $normal_count ){
				$data[$k++] = $normal_data[$j++];
				$interval++;
			}
			if($interval % $promo_cell_interval_no == 0){
				$data[$k++] = $google_data;	
			}
			
		}			
		
		$result = array();
		
		if($limit) {
			foreach($data as $item){
				if($offset == 0){
					if($limit>0){
						$result[] = $item;
						$limit--;
					}
				}else{
					if($offset>0){
						$offset--;
						continue;
					}
				}
				
			}
		}else{
			$result = $data;
		}

	  	return $result;
	}

	/**
	 * Get all items by google ads post
	 */
	function get_all_item_by_google_ads ( $conds = array(), $limit = false, $offset = false ) {
		$google_data = $this->get_empty_object( 'bs_items' );
		unset($google_data->is_empty_object);
		$google_data->ad_type = 'google_ad';

		$promo_cell_interval_no = $this->db->get_where( 'bs_app_settings', array( 'id' => 'app1' ))->row()->promo_cell_interval_no;

		$conds_normal = $conds;
		$normal_data = $this->get_all_item_by_normal_ads($conds_normal);

		$normal_count = count($normal_data);

		$j = 0; $k = 0;
		$data = array();
		
		// Traverse both array
		while ($j < $normal_count)
		{
			
			$interval = 0;
			while($interval < $promo_cell_interval_no && $j < $normal_count){
				$data[$k++] = $normal_data[$j++];
				$interval++;
			}

			if($interval % $promo_cell_interval_no == 0){
				$data[$k++] = $google_data;
			}
		}

	  	
		$result = array();
		
		if($limit) {
			foreach($data as $item){
				if($offset == 0){
					if($limit>0){
						$result[] = $item;
						$limit--;
					}
				}else{
					if($offset>0){
						$offset--;
						continue;
					}
				}
				
			}
		}else{
			$result = $data;
		}

	  	return $result;
	}

	// function get_not_correct_date ( $date_correct ) {
	// 	// where clause
	//   	$this->db->where_not_in('item_id', $date_correct);

	//   	// from table
	//   	$this->db->from( $this->table_name );

	//   	return $this->db->get();
	//   	// print_r($this->db->last_query());die;
	   	
	// }

	// get name with status 0 or 1

	function lang_exists( $conds = array()) {

		$sql = "SELECT * FROM bs_language WHERE `name` = '" . $conds['name'] . "' ";


		$query = $this->db->query($sql);

		return $query;
	}
	
	// get symbol with status 0 or 1

	function symbol_exists( $conds = array()) {

		$sql = "SELECT * FROM bs_language WHERE `symbol` = '" . $conds['symbol'] . "' ";
		

		$query = $this->db->query($sql);

		return $query;
	}

	// get user with phone conds

	function get_one_user_phone( $conds = array()) {

		$sql = "SELECT * FROM core_users WHERE `user_phone` = '" . $conds['user_phone'] . "' ";

		$query = $this->db->query($sql);

		return $query;
	}

	/**
	Returns module
	*/
	function get_all_module( )
	{
	
		$this->db->select('core_modules.*');    
  		$this->db->from('core_modules');
  		$this->db->where('is_show_on_menu',1);
  		$this->db->order_by('group_id','AESC');
		return $this->db->get();
		
	}

	/**
	get all chat not nego price zero
	*/
	function get_all_chat( $conds = array()) {

		$is_block_user = $this->db->get_where( 'bs_app_settings', array( 'id' => 'app1' ))->row()->is_block_user;
		$this->db->select('bs_chat_history.*');    
  		$this->db->from('bs_chat_history');

  		if(isset($conds['buyer_user_id'])) {

			if ($conds['buyer_user_id'] != "" || $conds['buyer_user_id'] != 0) {
					
					$this->db->where( 'buyer_user_id', $conds['buyer_user_id'] );	

			}

		}

		if(isset($conds['seller_user_id'])) {

			if ($conds['seller_user_id'] != "" || $conds['seller_user_id'] != 0) {
					
					$this->db->where( 'seller_user_id', $conds['seller_user_id'] );	

			}

		}

		// item id (id) check for user block condition
		if($is_block_user == "1"){
			if ( isset( $conds['item_id'] )) {
				
				if ($conds['item_id'] != "") {
					if($conds['item_id'] != '0'){
					
						$this->db->where_not_in( 'id', $conds['item_id'] );	
					}

				}			
			}
		}

		// item id (id) check for item reported condition
		if ( isset( $conds['chat_id'] )) {
			
			if ($conds['chat_id'] != "") {
				if($conds['chat_id'] != '0'){
				
					$this->db->where_not_in( 'id', $conds['chat_id'] );	
				}

			}			
		}

		$this->db->where( 'nego_price!=', $conds['nego_price'] );
		return $this->db->get();
		//print_r($this->db->last_query());die;
	}

    //get all chat when there is user blocked

	function get_all_chat_history( $conds = array(), $limit = false, $offset = false) {
		//print_r($conds['id']);die;
		$is_block_user = $this->db->get_where( 'bs_app_settings', array( 'id' => 'app1' ))->row()->is_block_user;
		$this->db->select('bs_chat_history.*');    
  		$this->db->from('bs_chat_history');

  		if(isset($conds['buyer_user_id'])) {

			if ($conds['buyer_user_id'] != "" || $conds['buyer_user_id'] != 0) {
					
					$this->db->where( 'buyer_user_id', $conds['buyer_user_id'] );	

			}

		}

		if(isset($conds['seller_user_id'])) {

			if ($conds['seller_user_id'] != "" || $conds['seller_user_id'] != 0) {
					
					$this->db->where( 'seller_user_id', $conds['seller_user_id'] );	

			}

		}

		// item id (id) check for user block condition
		if($is_block_user == "1"){
			if ( isset( $conds['item_id'] )) {
				
				if ($conds['item_id'] != "") {
					if($conds['item_id'] != '0'){
					
						$this->db->where_not_in( 'id', $conds['item_id'] );	
					}

				}			
			}
		}

		// item id (id) check for item reported condition
		if ( isset( $conds['chat_id'] )) {
			
			if ($conds['chat_id'] != "") {
				if($conds['chat_id'] != '0'){
				
					$this->db->where_not_in( 'id', $conds['chat_id'] );	
				}

			}			
		}

		if ( $limit ) {
		// if there is limit, set the limit
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			$this->db->offset($offset);
		}


		return $this->db->get();
		// print_r($this->db->last_query());die;
	}

	//get where in buyer user id at chat history

	function get_all_in_chat_buyer( $conds = array() ) {


		$sql = "SELECT * FROM `bs_chat_history` WHERE `buyer_user_id` IN(". $conds['buyer_user_id'] .")";

		$query = $this->db->query($sql);
		//print_r($this->db->last_query());die;
		return $query;


	}

	//get where in seller user id at chat history

	function get_all_in_chat_seller( $conds = array() ) {


		$sql = "SELECT * FROM `bs_chat_history` WHERE `seller_user_id` IN(". $conds['seller_user_id'] .")";

		$query = $this->db->query($sql);
		//print_r($this->db->last_query());die;
		return $query;


	}

	//get where in id at item table

	function get_all_in_report( $conds = array() ) {


		$sql = "SELECT * FROM `bs_items` WHERE `id` IN(". $conds['id'] .")";

		$query = $this->db->query($sql);
		///print_r($this->db->last_query());die;
		return $query;



	}

	//get where in item id at chat history

	function get_all_in_chat_item( $conds = array() ) {


		$sql = "SELECT * FROM `bs_chat_history` WHERE `item_id` IN(". $conds['item_id'] .")";

		$query = $this->db->query($sql);
		//print_r($this->db->last_query());die;
		return $query;



	}

	function get_all_by_item( $conds = array(), $limit = false, $offset = false ) {
		//print_r($conds);die;
		$is_block_user = $this->db->get_where( 'bs_app_settings', array( 'id' => 'app1' ))->row()->is_block_user;
		if($conds['lat'] != "" && $conds['lng'] != "") {
			$this->db->select('*,( 3959
		      * acos( cos( radians('. $conds['lat'] .') )
		              * cos(  radians( lat )   )
		              * cos(  radians( lng ) - radians('. $conds['lng'] .') )
		            + sin( radians('. $conds['lat'] .') )
		              * sin( radians( lat ) )
		            )
		    ) as distance');

		    if ($conds['miles'] == "") {
		    	$conds['miles'] = 0;
		    	$this->db->having('distance < ' .  $conds['miles'] );
		    } else {
		    	$this->db->having('distance < ' .  $conds['miles'] );

		    }
		    $this->db->order_by('distance' ); //

		   
		}

		// item id (id) check for user block condition
		if($is_block_user == "1"){
			if ( isset( $conds['item_id'] )) {

				if ($conds['item_id'] != "") {
					if($conds['item_id'] != '0'){

						$this->db->where_not_in( 'bs_items.id', $conds['item_id'] );
					}

				}
			}
		}

		// item id (id) check for item reported condition
		if ( isset( $conds['reported_item_id'] )) {

			if ($conds['reported_item_id'] != "") {
				if($conds['reported_item_id'] != '0'){

					$this->db->where_not_in( 'bs_items.id', $conds['reported_item_id'] );
				}

			}
		}

        // item id (id) check for item id by amenity condition
        if ( isset( $conds['item_id_by_amenity'] )) {

            if ($conds['item_id_by_amenity'] != "") {
                if($conds['item_id_by_amenity'] != '0'){

                    $this->db->where_in( 'bs_items.id', $conds['item_id_by_amenity'] );
//                    print_r($this->db->last_query());
                }

            }
        }

		// where clause
		$this->custom_conds( $conds );

		// from table
		$this->db->from( $this->table_name );

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}
		
	 	return $this->db->get();
		// print_r($this->db->last_query());die;
	
	}

	//get where in blocked user

	function get_all_in_blocked_user( $conds = array(), $limit = false, $offset = false ) {

	  	$sql = "SELECT * FROM `core_users` WHERE `user_id` IN(". $conds['user_id'] .") LIMIT $limit OFFSET $offset ";

		$query = $this->db->query($sql);

		return $query;

	}

	//get where in reported item

	function get_all_in_reported_item( $conds = array(), $limit = false, $offset = false ) {

	  	$sql = "SELECT * FROM `bs_items` WHERE `id` IN(". $conds['id'] .") AND `status` = '" . $conds['status'] . "'  LIMIT $limit OFFSET $offset ";

		$query = $this->db->query($sql);
		// print_r($this->db->last_query());die;

		return $query;

	}

	//get user with email and phone

	function get_email_phone( $conds = array()) {

		$sql = "SELECT * FROM core_users WHERE `user_email` = '" . $conds['user_email'] . "' AND `phone_id` = '" . $conds['phone_id'] . "' ";

		$query = $this->db->query($sql);

		return $query;
	}

	//get where in added_user_id at item table

	function get_all_in_item( $conds = array() ) {


		$sql = "SELECT * FROM `bs_items` WHERE `added_user_id` IN(". $conds['added_user_id'] .")";

		$query = $this->db->query($sql);
		///print_r($this->db->last_query());die;
		return $query;



	}

	//get all amenities

	function get_all_amenity($amen_id= array())
	{
		$this->db->select('bs_amenities.*'); 
		$this->db->from('bs_amenities');
		$this->db->where_in('id', $amen_id);
		return $this->db->get();
	}

	// get all not in currency (for default)
	function get_all_not_in_currency( $id, $limit = false, $offset = false ) {
	  // where clause
	  $this->db->where_not_in('id', $id);

	  // from table
	  $this->db->from( $this->table_name );

	  if ( $limit ) {
	  // if there is limit, set the limit
	   
	   $this->db->limit($limit);
	  }
	  
	  if ( $offset ) {
	  // if there is offset, set the offset,
	   
	   $this->db->offset($offset);
	  }
	  
	  return $this->db->get();
	  // print_r($this->db->last_query());die;
	}

	/**
	 * Delete the all records from table
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function delete_table( $table )
	{
		// delete the all records 
		return $this->db->empty_table( $table);
 	}

	 /**
	 * Delete the all records from table
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function delete_table_by( $conds, $table )
	{
		
		if(isset($conds['user_is_sys_admin'])){
			$this->db->where('user_is_sys_admin',$conds['user_is_sys_admin']);
		}
		
		// delete the all records 
		return $this->db->empty_table( $table);
 	}


	 /**
	 * Returns the total count
	 */
	function count_all_record($table) {
		// from table
		$this->db->from( $table );

		// return the count all results
		return $this->db->count_all_results();
		// print_r($this->db->last_query());die;
	}

	 /**
	 * Returns the total count
	 */
	function count_all_record_by($conds, $table) {
		
		// from table
		$this->db->from( $table );

		if(isset($conds['user_is_sys_admin'])){
			$this->db->where('user_is_sys_admin',$conds['user_is_sys_admin']);
		}

		// return the count all results
		return $this->db->count_all_results();
		// print_r($this->db->last_query());die;
	}
}