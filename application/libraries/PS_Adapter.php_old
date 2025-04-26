<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PanaceaSoft Authentication
 */
class PS_Adapter {

	// codeigniter instance
	protected $CI;

	// login user
	protected $login_user_id;

	/**
	 * Constructor
	 */
	function __construct()
	{
		// get CI instance
		$this->CI =& get_instance();
	}

	/**
	 * Sets the login user.
	 */
	function set_login_user_id( $user_id )
	{
		$this->login_user_id = $user_id;
	}
	
	/**
	 * Gets the default photo.
	 *
	 * @param      <type>  $id     The identifier
	 * @param      <type>  $type   The type
	 */
	function get_default_photo( $id, $type )
	{
		$default_photo = "";

		// get all images
		$img = $this->CI->Image->get_all_by( array( 'img_parent_id' => $id, 'img_type' => $type ))->result();

		if ( count( $img ) > 0 ) {
		// if there are images for wallpaper,
			
			$default_photo = $img[0];
		} else {
		// if no image, return empty object

			$default_photo = $this->CI->Image->get_empty_object();
		}

		return $default_photo;
	}
	

	/**
	 * Customize wallpaper object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_image( &$obj )
	{

	}

	function convert_agent( &$obj )
	{
		$conds['user_id'] = $obj->user_id;
		// photo count
		$obj->photo_count = $this->CI->Image->count_all_by(array('img_parent_id' => $obj->user_id));
		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->id, 'agent' );
	}

	/**
	 * Customize package object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_package( &$obj )
	{
		// Itemcurrency Object
		if ( isset( $obj->currency_id )) {

			$tmp_currency = $this->CI->Currency->get_one( $obj->currency_id );
			$obj->currency = $tmp_currency;
		}
	}
	
	/**
	 * Customize buy ad post transaction history
	 */
	function convert_package_bought_history( $obj ){
		// user object
		if(isset($obj->user_id)){
			$tmp_user = $this->CI->User->get_one( $obj->user_id );

			$this->convert_user( $tmp_user );

			$obj->user = $tmp_user;
		}

		// package object
		if(isset($obj->package_id)){
			$tmp_package = $this->CI->Package->get_one( $obj->package_id );

			$this->convert_package( $tmp_package );

			$obj->package = $tmp_package;
		}

	}

	/**
	 * Customize category object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_banners( &$obj )
	{
		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->id, 'banner' );
	}


	/**
	 * Customize category object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_item( &$obj )
	{
		$conds['item_id'] = $obj->id;
		$conds['id'] = $obj->history_id;
		$paid_history = $this->CI->Paid_item->get_all_by($conds)->result();
		
		$today_date = date('Y-m-d H:i:s');

		// ad post type condition
		if($obj->is_paid == 1  && !isset($obj->ad_type)){
			$obj->ad_type = 'normal_ad';

			$ad_conds['item_id'] = $obj->id;

			$paid_data = $this->CI->Paid_item->get_all_by($ad_conds)->result();
			
			foreach($paid_data as $item){
				
				if($item->start_date<=$today_date && $item->end_date>=$today_date){
					$obj->ad_type = 'paid_ad'; continue;
				}
			}

		}elseif(!isset($obj->ad_type)){
			$obj->ad_type = 'normal_ad';
		}
		
		//paid status of offline payment
		$conds_id['id'] = $obj->id;
		$is_paid = $this->CI->Item->get_one_by($conds_id)->is_paid;


		if (count($paid_history) > 1) {
			foreach ($paid_history as $paid) {
				$tmp_result .= $paid->item_id .",";
				  
			}
			$paid_his_item_id = rtrim($tmp_result,",");
			$item_id = explode(",", $paid_his_item_id);
			
			$progress = $this->CI->Paid_item->get_item_by_paid_progress($item_id)->result();
			//for progress and finished
			if ( !empty($progress) ) {
				
				foreach ($progress as $pro) {
					if ($is_paid == 1) {
						if ($today_date >= $pro->start_date && $today_date <= $pro->end_date ) {
						$obj->paid_status = $this->CI->config->item('progress_label');
						} else {
							$obj->paid_status = $this->CI->config->item('not_yet_start_label');
						}
					}else{
						if ($is_paid == 0) {
							$obj->paid_status = $this->CI->config->item('waiting_for_approval_label');
						}else{
							$obj->paid_status = $this->CI->config->item('reject_label');
						}
						
					}
					
				}
			} else {
				
				$obj->paid_status = $this->CI->config->item('finished_label');
			}

		} elseif (count($paid_history)==1) {
			$start_date = $paid_history[0]->start_date;
			$end_date = $paid_history[0]->end_date;

			if ($is_paid == 1) {
				//print_r($pro->start_date);die;
				if ($today_date >= $start_date && $today_date <= $end_date ) {
				$obj->paid_status = $this->CI->config->item('progress_label');
				} elseif ($today_date > $start_date && $today_date > $end_date) {
				$obj->paid_status = $this->CI->config->item('finished_label');
				} else {
					$obj->paid_status = $this->CI->config->item('not_yet_start_label');
				}
			}else{
				if ($is_paid == 0) {
					$obj->paid_status = $this->CI->config->item('waiting_for_approval_label');
				}else{
					$obj->paid_status = $this->CI->config->item('reject_label');
				}
			}

		
		} else {
			$obj->paid_status = $this->CI->config->item('not_available');
		}

		// discounted price 
		if ( isset( $obj->discount_rate_by_percentage )) {
			$discount_price = (float) $obj->price - ( (float) $obj->price * ( (float) $obj->discount_rate_by_percentage / 100));
			$obj->discounted_price = $discount_price;
		}

		unset($obj->history_id);
		// photo count
		unset($obj->history_id);
 		// photo count
		$max_image_limit = $this->CI->App_setting->get_one('app1')->max_img_upload_of_item;
		$image_count = $this->CI->Image->count_all_by(array('img_parent_id' => $obj->id , 'img_type' => 'item'));
		$obj->photo_count = $image_count;
		if($image_count > $max_image_limit){
			$obj->photo_count = $max_image_limit;
		}
		// photo count
		$obj->video_count = $this->CI->Image->count_all_by(array('img_parent_id' => $obj->id , 'img_type' => 'video'));
		// set default photo
		$obj->default_photo = $this->get_item_default_photo( $obj->id, 'item' );
        // set video obj
        $obj->default_video = $this->get_default_photo( $obj->id, 'video' );
        // set video icon obj
        $obj->default_video_icon = $this->get_default_photo( $obj->id, 'video-icon' );

		// Postedby Object
		if ( isset( $obj->posted_by_id )) {

			$tmp_posted = $this->CI->Postedby->get_one( $obj->posted_by_id );
			// $obj->posted_by = $tmp_posted;
            $this->convert_color( $tmp_posted );
            $obj->post_type = $tmp_posted;
		}


		// Property type Object
		if ( isset( $obj->property_by_id )) {

			$tmp_property_type = $this->CI->Property_type->get_one( $obj->property_by_id );
			$this->convert_propertyby( $tmp_property_type );
			$obj->property_type = $tmp_property_type;
		}

		// Itemcurrency Object
		if ( isset( $obj->item_currency_id )) {

			$tmp_item_currency = $this->CI->Currency->get_one( $obj->item_currency_id );
			$obj->item_currency = $tmp_item_currency;
		}

		// Itempricetype Object
		if ( isset( $obj->item_price_type_id )) {

			$tmp_item_price_type = $this->CI->Pricetype->get_one( $obj->item_price_type_id );
			$obj->item_price_type = $tmp_item_price_type;
		}

		// Itemlocation Object
		if ( isset( $obj->item_location_city_id )) {

			$tmp_item_location_city= $this->CI->Itemlocation->get_one( $obj->item_location_city_id );
			$obj->item_location_city = $tmp_item_location_city;
		}

		// Item_location_township Object
		if ( isset( $obj->item_location_township_id )) {

			$tmp_item_location_township= $this->CI->Item_location_township->get_one( $obj->item_location_township_id );
			$obj->item_location_township = $tmp_item_location_township;
		}

		//amenities object
		$conds_itm['item_id'] = $obj->id;

		$amenities = $this->CI->Item_amenity->get_all_by( $conds )->result();

		foreach ($amenities as $amenity) {
			$tmp_result .= $amenity->amenity_id .",";
			  
		}
		$amenity_id = rtrim($tmp_result,",");
		$amen_id = explode(",", $amenity_id);
		if (empty($amen_id[0])) {
			$amenity_dummy = $this->CI->Amenity->get_empty_object();
			$this->convert_amenity( $amenity_dummy );
			$tmp_amenity[] = $amenity_dummy;
			$obj->amenities = $tmp_amenity;
		} else {
			$tmp_amenities = $this->CI->Amenity->get_all_amenity($amen_id)->result();
			foreach ($tmp_amenities as $tmp_amenity) {
				$this->CI->Amenity->get_one( $tmp_amenity->id );
				$this->convert_amenity( $tmp_amenity );
			}			
			$obj->amenities = $tmp_amenities;
		}


		//print_r($obj->added_user_id . "$$");die;
		// User Object
		if ( isset( $obj->added_user_id )) {
			$tmp_item_user = $this->CI->User->get_one( $obj->added_user_id );

			$this->convert_user( $tmp_item_user );
			$obj->user = $tmp_item_user;
		}

		//Need to check for Like and Favourite
		$obj->is_favourited = 0;
		$obj->is_owner = 0;

		if($this->get_login_user_id() != "") {
			//echo "1";
			//Need to check for Fav
			$conds['item_id'] = $obj->id;
			$conds['user_id']    = $this->get_login_user_id();
			$login_user_id = $conds['user_id'];


			if($obj->added_user_id == $login_user_id) {
				$obj->is_owner = 1;
			} else {
				$obj->is_owner = 0;
			}

			$fav_id = $this->CI->Favourite->get_one_by($conds)->id;
			$obj->is_favourited = 0;
			if($fav_id != "") {
				$obj->is_favourited = 1;
			} else {
				$obj->is_favourited = 0;
			}

		} else if($obj->login_user_id_post != "") {
			//echo "2";
			$conds['item_id'] 	= $obj->id;
			$conds['user_id']    = $obj->login_user_id_post;
			$login_user_id = $conds['user_id'];

			// checking for like product by user

			$obj->is_owner = 0;
			if($obj->added_user_id == $login_user_id) {
				$obj->is_owner = 1;
			} else {
				$obj->is_owner = 0;
			}
			
			$fav_id = $this->CI->Favourite->get_one_by($conds)->id;
			$obj->is_favourited = 0;
			if($fav_id != "") {
				$obj->is_favourited = 1;
			} else {
				$obj->is_favourited = 0;
			}

		}
		//die;

		//print_r($obj->added_user_id);die;
		//unset($obj->login_user_id_post);

		$obj->is_favourited = $obj->is_favourited;
		$obj->is_owner = $obj->is_owner;

	}

	function convert_color( &$obj )
	{
		$conds['post_id'] = $obj->id;
		// Colors Object 
		$color_count = $this->CI->Color->count_all_by( $conds );

		if ( $color_count > 0 ) {
			$tmp_colors = $this->CI->Color->get_all_by( $conds )->result();
			$obj->colors = $tmp_colors[0];
		} else {
			$color_dummy = $this->CI->Color->get_empty_object();
			$obj->colors = $color_dummy;
		}
	}

	/*
	 * Customize feed object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_paid_history( &$obj )
	{
		$today_date = date('Y-m-d H:i:s');
		
		$conds['id'] = $obj->item_id;

		$is_paid = $this->CI->Item->get_one_by($conds)->is_paid;

		if ($obj->payment_method == "offline" && $is_paid == "0") {
			$obj->paid_status = $this->CI->config->item('waiting_for_approval_label');
		} elseif ($obj->payment_method == "offline" && $is_paid == "2") {
			$obj->paid_status = $this->CI->config->item('reject_label');
		} elseif ($obj->payment_method == "offline" && $is_paid == "1") {
			if ($today_date >= $obj->start_date && $today_date <= $obj->end_date) {
			$obj->paid_status = $this->CI->config->item('progress_label');
			} elseif ($today_date > $obj->start_date && $today_date > $obj->end_date) {
				$obj->paid_status = $this->CI->config->item('finished_label');
			} elseif ($today_date < $obj->end_date && $today_date < $obj->end_date) {
				$obj->paid_status = $this->CI->config->item('not_yet_start_label');
			}
		} elseif ($today_date >= $obj->start_date && $today_date <= $obj->end_date) {
			$obj->paid_status = $this->CI->config->item('progress_label');
		} elseif ($today_date > $obj->start_date && $today_date > $obj->end_date) {
			$obj->paid_status = $this->CI->config->item('finished_label');
		} elseif ($today_date < $obj->end_date && $today_date < $obj->end_date) {
			$obj->paid_status = $this->CI->config->item('not_yet_start_label');
		} else {
			$obj->paid_status = $this->CI->config->item('not_available');
		}
		// item object
		if ( isset( $obj->item_id )) {
			$tmp_item = $this->CI->Item->get_one( $obj->item_id );
			$tmp_item->history_id = $obj->id;

			$this->convert_item( $tmp_item );

			$obj->item = $tmp_item;
		}

	}

	/**
	 * Customize chatitem object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_chatitem( &$obj )
	{
			
		// photo count
		$obj->photo_count = $this->CI->Image->count_all_by(array('img_parent_id' => $obj->id));
		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->id, 'item' );

		// Itemtype Object
		if ( isset( $obj->item_type_id )) {
			$tmp_item_type = $this->CI->Itemtype->get_one( $obj->item_type_id );

			$obj->item_type = $tmp_item_type;
		}

		// Itemprice Object
		if ( isset( $obj->item_price_type_id )) {
			$tmp_item_price_type = $this->CI->Pricetype->get_one( $obj->item_price_type_id );

			$obj->item_price_type = $tmp_item_price_type;
		}

		// Itemcurrency Object
		if ( isset( $obj->item_currency_id )) {
			$tmp_item_currency = $this->CI->Currency->get_one( $obj->item_currency_id );

			$obj->item_currency = $tmp_item_currency;
		}

		// Itemlocation Object
		if ( isset( $obj->item_location_id )) {
			$tmp_item_location= $this->CI->Itemlocation->get_one( $obj->item_location_id );

			$obj->item_location = $tmp_item_location;
		}

		// condition of item id Object
		if ( isset( $obj->condition_of_item_id )) {
			$tmp_condition_item= $this->CI->Condition->get_one( $obj->condition_of_item_id );

			$obj->condition_of_item = $tmp_condition_item;
		}

		// Chat User Object
		if ( isset( $obj->chat_user_id )) {
			$conds['chat_user_id'] = $obj->chat_user_id;

			$tmp_item_user = $this->CI->User->get_all_in_user($conds)->result();
			//print_r($tmp_item_user);die;

			$this->convert_user( $tmp_item_user );

			$obj->chat_user = $tmp_item_user;
		}


		//Need to check for Like and Favourite
		$obj->is_favourited = 0;
		$obj->is_owner = 0;
		
		if($this->get_login_user_id() != "") {
			//Need to check for Fav
			$conds['item_id'] = $obj->id;
			$conds['user_id'] = $this->get_login_user_id();
			$login_user_id    = $conds['user_id'];
		
			// checking for item by user
			if($obj->added_user_id == $login_user_id) {
				$obj->is_owner = 1;
			} else {
				$obj->is_owner = 0;
			}


			$fav_id = $this->CI->Favourite->get_one_by($conds)->fav_id;

			$obj->is_favourited = 0;
			if($fav_id != "") {
				$obj->is_favourited = 1;
			} else {
				$obj->is_favourited = 0;
			}

		} else if($obj->login_user_id_post != "") {
			$conds['item_id'] = $obj->id;
			$conds['user_id'] = $obj->login_user_id_post;
			$login_user_id    = $conds['user_id'];

			// checking for like product by user
			$obj->is_owner = 0;
			if($obj->added_user_id == $login_user_id) {
				$obj->is_owner = 1;
			} else {
				$obj->is_owner = 0;
			}

			$fav_id = $this->CI->Favourite->get_one_by($conds)->id;
			$obj->is_favourited = 0;
			if($fav_id != "") {
				$obj->is_favourited = 1;
			} else {
				$obj->is_favourited = 0;
			}

		}

		
		unset($obj->login_user_id_post);

		$obj->is_favourited = $obj->is_favourited;
		$obj->is_owner = $obj->is_owner;

	}


	/**
	 * Sets the login user.
	 */
	function get_login_user_id()
	{
		return $this->login_user_id;
	}


	
	/**
	 * Customize follower user object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_user_follow( &$obj )
	{
		// user object
		if ( isset( $obj->followed_user_id )) {
			$tmp_user = $this->CI->User->get_one( $obj->followed_user_id );

			$this->convert_user( $tmp_user );

			$obj->user_followed = $tmp_user;
		}
	}



	/**
	 * Customize about object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_about( &$obj )
	{
		$obj->privacypolicy = $this->CI->Privacy_policy->get_one('privacy1')->content;
		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->about_id, 'about' );

		//user data deletion
		$obj->datadelection = $this->CI->Data_deletion->get_one('datadelete1')->content;

	}

	/**
	 * Customize user object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_user( &$obj , $need_return = false)
	{

		if($obj->role_id == 4){
			$obj->post_count = 'limited';
		}else{
			$obj->post_count = 'unlimited';
		}
		
		$conds['added_user_id'] = $obj->user_id;
		//print_r($obj->user_id);die;
		$obj->rating_count = $this->CI->Rate->count_all_by(array('to_user_id' => $obj->user_id));

        //is follower or not

		$obj->is_followed = 0;
		$obj->is_blocked = 0;
		
		if($obj->followed_user_id != "") {
			
			$conds1['user_id']    = $obj->following_user_id;
			$conds1['followed_user_id'] = $obj->followed_user_id;
			
			$conds2['from_block_user_id'] = $obj->following_user_id;
			$conds2['to_block_user_id'] = $obj->followed_user_id;
		}

		if($this->get_login_user_id() != "" && $obj->user_id != $this->get_login_user_id()) {
			$conds1['user_id'] = $this->get_login_user_id();
			$conds1['followed_user_id'] = $obj->user_id;
			
			$conds2['from_block_user_id'] = $this->get_login_user_id();
			$conds2['to_block_user_id'] = $obj->user_id;
		}

		if(!empty($conds1)){
			// checking follower
			$followed_user_id = $this->CI->Userfollow->get_one_by($conds1)->id;
			$obj->is_followed = 0;
			if($followed_user_id != "") {
				$obj->is_followed = 1;
			} else {
				$obj->is_followed = 0;
			}
			
			// checking block user
			$blocked_user_id = $this->CI->Block->get_one_by($conds2)->id;
			
			$obj->is_blocked = 0;
			if($blocked_user_id != "") {
				$obj->is_blocked = 1;
			} else {
				$obj->is_blocked = 0;
			}

		}
		
		unset($obj->following_user_id);
		unset($obj->followed_user_id);
		unset($obj->blocked_user_id);
		unset($obj->user_password);
		unset($conds1);
		unset($conds2);

		$obj->is_followed = $obj->is_followed;

        //rating details 
		
		$total_rating_count = 0;
		$total_rating_value = 0;

		$five_star_count = 0;
		$five_star_percent = 0;

		$four_star_count = 0;
		$four_star_percent = 0;

		$three_star_count = 0;
		$three_star_percent = 0;

		$two_star_count = 0;
		$two_star_percent = 0;

		$one_star_count = 0;
		$one_star_percent = 0;


		

		//Rating Total how much ratings for this product
		$conds_rating['to_user_id'] = $obj->user_id;
		$total_rating_count = $this->CI->Rate->count_all_by($conds_rating);
		$sum_rating_value = $this->CI->Rate->sum_all_by($conds_rating)->result()[0]->rating;


		//Rating Value such as 3.5, 4.3 and etc
		if($total_rating_count > 0) {
			$total_rating_value = number_format((float) ($sum_rating_value  / $total_rating_count), 1, '.', '');
		} else {
			$total_rating_value = 0;
		}

		//For 5 Stars rating
		$conds_five_star_rating['rating'] = 5;
		$conds_five_star_rating['to_user_id'] = $obj->user_id;
		$five_star_count = $this->CI->Rate->count_all_by($conds_five_star_rating);
		if($total_rating_count > 0) {
			$five_star_percent = number_format((float) ((100 / $total_rating_count) * $five_star_count), 1, '.', '');
		} else {
			$five_star_percent = 0;
		}

		//For 4 Stars rating
		$conds_four_star_rating['rating'] = 4;
		$conds_four_star_rating['to_user_id'] = $obj->user_id;
		$four_star_count = $this->CI->Rate->count_all_by($conds_four_star_rating);
		if($total_rating_count > 0) {
			$four_star_percent = number_format((float) ((100 / $total_rating_count) * $four_star_count), 1, '.', '');
		} else {
			$four_star_percent = 0;
		}


		//For 3 Stars rating
		$conds_three_star_rating['rating'] = 3;
		$conds_three_star_rating['to_user_id'] = $obj->user_id;
		$three_star_count = $this->CI->Rate->count_all_by($conds_three_star_rating);
		if($total_rating_count > 0) {
			$three_star_percent = number_format((float) ((100 / $total_rating_count) * $three_star_count), 1, '.', '');
		} else {
			$three_star_percent = 0;
		}


		//For 2 Stars rating
		$conds_two_star_rating['rating'] = 2;
		$conds_two_star_rating['to_user_id'] = $obj->user_id;
		$two_star_count = $this->CI->Rate->count_all_by($conds_two_star_rating);

		if($total_rating_count > 0) {
			$two_star_percent = number_format((float) ((100 / $total_rating_count) * $two_star_count), 1, '.', '');
		} else {
			$two_star_percent = 0;
		}

		//For 1 Stars rating
		$conds_one_star_rating['rating'] = 1;
		$conds_one_star_rating['to_user_id'] = $obj->user_id;
		$one_star_count = $this->CI->Rate->count_all_by($conds_one_star_rating);

		if($total_rating_count > 0) {
		$one_star_percent = number_format((float) ((100 / $total_rating_count) * $one_star_count), 1, '.', '');
		} else {
			$one_star_percent = 0;
		}


		$rating_std = new stdClass();
		$rating_std->five_star_count = $five_star_count; 
		$rating_std->five_star_percent = $five_star_percent;

		$rating_std->four_star_count = $four_star_count;
		$rating_std->four_star_percent = $four_star_percent;

		$rating_std->three_star_count = $three_star_count;
		$rating_std->three_star_percent = $three_star_percent;

		$rating_std->two_star_count = $two_star_count;
		$rating_std->two_star_percent = $two_star_percent;

		$rating_std->one_star_count = $one_star_count;
		$rating_std->one_star_percent = $one_star_percent;

		//print_r($total_rating_count . "##" . $total_rating_value);die;

		$rating_std->total_rating_count = $total_rating_count;
		$rating_std->total_rating_value = $total_rating_value;


		$obj->rating_details = $rating_std;

		if($need_return)
		{
			return $obj;
		} 

	}

	/**
	 * Customize noti object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_noti_message( &$obj )
	{
		
		
		if($this->get_login_user_id() != "") {
			$noti_user_data = array(
	        	"noti_id" => $obj->id,
	        	"user_id" => $this->get_login_user_id()
	    	);
			if ( !$this->CI->Notireaduser->exists( $noti_user_data )) {
				$obj->is_read = 0;
			} else {
				$obj->is_read = 1;
			}
		} 
		


		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->id, 'noti' );
	}

	/**
	 * Customize noti object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_noti( &$obj )
	{
		
		
		if($this->get_login_user_id() != "") {
			$noti_user_data = array(
	        	"noti_id" => $obj->id,
	        	"user_id" => $this->get_login_user_id()
	    	);
			if ( !$this->CI->Notireaduser->exists( $noti_user_data )) {
				$obj->is_read = 0;
			} else {
				$obj->is_read = 1;
			}
		} 
		


		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->id, 'noti' );
	}

	/*
	 * Customize feed object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_feed( &$obj )
	{
		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->id, 'blog' );
		// location Object
		if ( isset( $obj->item_location_id )) {

			$tmp_blog_city= $this->CI->Itemlocation->get_one( $obj->item_location_id );
			$obj->blog_city = $tmp_blog_city;
		}

	}

	/**
	 * Customize tag object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_rating( &$obj )
	{
		// set user object

		if ( is_array( $obj )) {
			
			for ($i=0; $i < count($obj) ; $i++) { 
				if ( isset( $obj[$i]->from_user_id )) {

					$tmp_from_user_id = $this->CI->User->get_one( $obj[$i]->from_user_id );
					//print_r($tmp_from_user_id);die;
					$this->convert_user( $tmp_from_user_id );
					//print_r($a);die;
					$obj[$i]->from_user = $tmp_from_user_id;
				}


				// set user object
				if ( isset( $obj[$i]->to_user_id )) {
					$tmp_to_user_id = $this->CI->User->get_one( $obj[$i]->to_user_id );

					$this->convert_user( $tmp_to_user_id );

					$obj[$i]->to_user = $tmp_to_user_id;
				}
			}
			

		}else {

			if ( isset( $obj->from_user_id )) {

					$tmp_from_user_id = $this->CI->User->get_one( $obj->from_user_id );
					//print_r($tmp_from_user_id);die;
					$this->convert_user( $tmp_from_user_id );
					//print_r($a);die;
					$obj->from_user = $tmp_from_user_id;
				}


				// set user object
				if ( isset( $obj->to_user_id )) {
					$tmp_to_user_id = $this->CI->User->get_one( $obj->to_user_id );

					$this->convert_user( $tmp_to_user_id );

					$obj->to_user = $tmp_to_user_id;
				}

		}
	}

	/**
	 * Customize chatitem object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_chathistory( &$obj )
	{
		if ( is_array( $obj )) {
			for ($i=0; $i < count($obj) ; $i++) { 

				$is_sold_out = $this->CI->Item->get_one( $obj[$i]->item_id )->is_sold_out;
				//print($is_sold_out);die;

				if ( $obj[$i]->offer_status == '2' || $is_sold_out == '1' ) {
					//hide
					$obj[$i]->is_offer = 1;
				} else {
					//show
					$obj[$i]->is_offer = 0;
				}

				$obj[$i]->offer_amount = $obj[$i]->nego_price;

				// if ( $obj[$i]->nego_price == '0') {
				// 	$obj[$i]->is_offer = 0;
				// 	$obj[$i]->offer_amount = 0;
					
				// } else {
				// 	$obj[$i]->is_offer = 1;
				// 	$obj[$i]->offer_amount = $obj[$i]->nego_price;

				// }
				// item object
				if ( isset( $obj[$i]->item_id )) {
					$tmp_item = $this->CI->Item->get_one( $obj[$i]->item_id );

					$this->convert_item( $tmp_item );

					$obj[$i]->item = $tmp_item;
				}

				//set users objects
				if ( isset( $obj[$i]->buyer_user_id )) {

					$tmp_buyer_user_id = $this->CI->User->get_one( $obj[$i]->buyer_user_id );
					$this->convert_user( $tmp_buyer_user_id );
					$obj[$i]->buyer = $tmp_buyer_user_id;
				}

				if ( isset( $obj[$i]->seller_user_id )) {
					$tmp_seller_user_id = $this->CI->User->get_one( $obj[$i]->seller_user_id );

					$this->convert_user( $tmp_seller_user_id );

					$obj[$i]->seller = $tmp_seller_user_id;
				}
			}
		} else {

			$is_sold_out = $this->CI->Item->get_one( $obj->item_id )->is_sold_out;

			if ( $obj->offer_status == '2' || $is_sold_out == '1' ) {
				$obj->is_offer = 1;
			} else {
				$obj->is_offer = 0;
			}
			
			$obj->offer_amount = $obj->nego_price;

			// if ( $obj->nego_price == '0') {
			// 	$obj->is_offer = 0;
			// 	$obj->offer_amount = 0;
				
			// } else {
			// 	$obj->is_offer = 1;
			// 	$obj->offer_amount = $obj->nego_price;

			// }

			if ( isset( $obj->item_id )) {
				$tmp_item = $this->CI->Item->get_one( $obj->item_id );

				$this->convert_item( $tmp_item );

				$obj->item = $tmp_item;
			}

			// set users objects
			
			if ( isset( $obj->buyer_user_id )) {

				$tmp_buyer_user_id = $this->CI->User->get_one( $obj->buyer_user_id );
				$this->convert_user( $tmp_buyer_user_id );
				$obj->buyer = $tmp_buyer_user_id;
			}


			if ( isset( $obj->seller_user_id )) {
				$tmp_seller_user_id = $this->CI->User->get_one( $obj->seller_user_id );

				$this->convert_user( $tmp_seller_user_id );

				$obj->seller = $tmp_seller_user_id;
			}

		}	
	}


	/**
	 * Customize follwing user object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_follow_user( &$obj )
	{
		// user object
		if ( isset( $obj->user_id )) {
			$tmp_user = $this->CI->User->get_one( $obj->user_id );

			$this->convert_user( $tmp_user );

			$obj->user_followed = $tmp_user;
		}
	}

	function convert_follow_user_list( &$obj, $followed_user_id )
	{

		if ( is_array( $obj )) {

			for ($i=0; $i < count($obj) ; $i++) { 

				$obj[$i]->is_followed = 0;
			
				 if($followed_user_id != "") {
					
					// $conds1['user_id']    = $following_user_id;
					// $conds1['followed_user_id'] = $obj[$i]->user_id;
					$conds1['user_id']    = $obj[$i]->user_id;
					$conds1['followed_user_id'] = $followed_user_id;
					// checking follower
					
					$followed_user_id = $this->CI->Userfollow->get_one_by($conds1)->id;
					//print_r($followed_user_id);die;
					$obj[$i]->is_followed = 0;
					if($followed_user_id != "") {
						$obj[$i]->is_followed = 1;
					} else {
						$obj[$i]->is_followed = 0;
					}

					// checking block user
					$conds2['from_block_user_id'] = $obj[$i]->user_id;
					$conds2['to_block_user_id'] = $followed_user_id;

					$blocked_user_id = $this->CI->Block->get_one_by($conds2)->id;
					
					$obj->is_blocked = 0;
					if($blocked_user_id != "") {
						$obj->is_blocked = 1;
					} else {
						$obj->is_blocked = 0;
					}

				}
				
				unset($followed_user_id);
				unset($blocked_user_id);

				$obj[$i]->is_followed = $obj[$i]->is_followed;

				if ( isset( $obj[$i]->user_id )) {

					$obj[$i]->rating_count = $this->CI->Rate->count_all_by(array('to_user_id' => $obj[$i]->user_id));

						//rating details 
					$total_rating_count = 0;
					$total_rating_value = 0;

					$five_star_count = 0;
					$five_star_percent = 0;

					$four_star_count = 0;
					$four_star_percent = 0;

					$three_star_count = 0;
					$three_star_percent = 0;

					$two_star_count = 0;
					$two_star_percent = 0;

					$one_star_count = 0;
					$one_star_percent = 0;

					//Rating Total how much ratings for this product
					$conds_rating['to_user_id'] = $obj[$i]->user_id;
					
					$total_rating_count = $this->CI->Rate->count_all_by($conds_rating);
					$sum_rating_value = $this->CI->Rate->sum_all_by($conds_rating)->result()[0]->rating;


					//Rating Value such as 3.5, 4.3 and etc
					if($total_rating_count > 0) {
						$total_rating_value = number_format((float) ($sum_rating_value  / $total_rating_count), 1, '.', '');
					} else {
						$total_rating_value = 0;
					}

					//For 5 Stars rating

					$conds_five_star_rating['rating'] = 5;
					$conds_five_star_rating['to_user_id'] = $obj->id;
					$five_star_count = $this->CI->Rate->count_all_by($conds_five_star_rating);
					if($total_rating_count > 0) {
						$five_star_percent = number_format((float) ((100 / $total_rating_count) * $five_star_count), 1, '.', '');
					} else {
						$five_star_percent = 0;
					}

					//For 4 Stars rating
					$conds_four_star_rating['rating'] = 4;
					$conds_four_star_rating['to_user_id'] = $obj->id;
					$four_star_count = $this->CI->Rate->count_all_by($conds_four_star_rating);
					if($total_rating_count > 0) {
						$four_star_percent = number_format((float) ((100 / $total_rating_count) * $four_star_count), 1, '.', '');
					} else {
						$four_star_percent = 0;
					}


					//For 3 Stars rating
					$conds_three_star_rating['rating'] = 3;
					$conds_three_star_rating['to_user_id'] = $obj->id;
					$three_star_count = $this->CI->Rate->count_all_by($conds_three_star_rating);
					if($total_rating_count > 0) {
						$three_star_percent = number_format((float) ((100 / $total_rating_count) * $three_star_count), 1, '.', '');
					} else {
						$three_star_percent = 0;
					}


					//For 2 Stars rating
					$conds_two_star_rating['rating'] = 2;
					$conds_two_star_rating['to_user_id'] = $obj->id;
					$two_star_count = $this->CI->Rate->count_all_by($conds_two_star_rating);

					if($total_rating_count > 0) {
						$two_star_percent = number_format((float) ((100 / $total_rating_count) * $two_star_count), 1, '.', '');
					} else {
						$two_star_percent = 0;
					}

					//For 1 Stars rating
					$conds_one_star_rating['rating'] = 1;
					$conds_one_star_rating['to_user_id'] = $obj->id;
					$one_star_count = $this->CI->Rate->count_all_by($conds_one_star_rating);

					if($total_rating_count > 0) {
					$one_star_percent = number_format((float) ((100 / $total_rating_count) * $one_star_count), 1, '.', '');
					} else {
						$one_star_percent = 0;
					}


					$rating_std = new stdClass();
					$rating_std->five_star_count = $five_star_count; 
					$rating_std->five_star_percent = $five_star_percent;

					$rating_std->four_star_count = $four_star_count;
					$rating_std->four_star_percent = $four_star_percent;

					$rating_std->three_star_count = $three_star_count;
					$rating_std->three_star_percent = $three_star_percent;

					$rating_std->two_star_count = $two_star_count;
					$rating_std->two_star_percent = $two_star_percent;

					$rating_std->one_star_count = $one_star_count;
					$rating_std->one_star_percent = $one_star_percent;

					$rating_std->total_rating_count = $total_rating_count;
					$rating_std->total_rating_value = $total_rating_value;

					$obj[$i]->rating_details = $rating_std;	
					
				}

			
			}

		} else {
			$obj->is_followed = 0;
			
			 if($followed_user_id != "") {
				
				// $conds1['user_id']    = $following_user_id;
				// $conds1['followed_user_id'] = $obj[$i]->user_id;
				$conds1['user_id']    = $obj[$i]->user_id;
				$conds1['followed_user_id'] = $followed_user_id;
				// checking follower
				
				$followed_user_id = $this->CI->Userfollow->get_one_by($conds1)->id;
				//print_r($followed_user_id);die;
				$obj->is_followed = 0;
				if($followed_user_id != "") {
					$obj->is_followed = 1;
				} else {
					$obj->is_followed = 0;
				}

				// checking block user
				$conds2['from_block_user_id'] = $obj[$i]->user_id;
				$conds2['to_block_user_id'] = $followed_user_id;

				$blocked_user_id = $this->CI->Block->get_one_by($conds2)->id;
				
				$obj->is_blocked = 0;
				if($blocked_user_id != "") {
					$obj->is_blocked = 1;
				} else {
					$obj->is_blocked = 0;
				}

			}
			
			unset($followed_user_id);
			unset($blocked_user_id);

			$obj->is_followed = $obj->is_followed;
			$obj->rating_count = $this->CI->Rate->count_all_by(array('to_user_id' => $obj->user_id));
			//rating details 
			$total_rating_count = 0;
			$total_rating_value = 0;

			$five_star_count = 0;
			$five_star_percent = 0;

			$four_star_count = 0;
			$four_star_percent = 0;

			$three_star_count = 0;
			$three_star_percent = 0;

			$two_star_count = 0;
			$two_star_percent = 0;

			$one_star_count = 0;
			$one_star_percent = 0;

			//Rating Total how much ratings for this product
			$conds_rating['to_user_id'] = $obj->to_user_id;
			$total_rating_count = $this->CI->Rate->count_all_by($conds_rating);
			$sum_rating_value = $this->CI->Rate->sum_all_by($conds_rating)->result()[0]->rating;

			//Rating Value such as 3.5, 4.3 and etc
			if($total_rating_count > 0) {
				$total_rating_value = number_format((float) ($sum_rating_value  / $total_rating_count), 1, '.', '');
			} else {
				$total_rating_value = 0;
			}

			//For 5 Stars rating

			$conds_five_star_rating['rating'] = 5;
			$conds_five_star_rating['to_user_id'] = $obj->id;
			$five_star_count = $this->CI->Rate->count_all_by($conds_five_star_rating);
			if($total_rating_count > 0) {
				$five_star_percent = number_format((float) ((100 / $total_rating_count) * $five_star_count), 1, '.', '');
			} else {
				$five_star_percent = 0;
			}

			//For 4 Stars rating
			$conds_four_star_rating['rating'] = 4;
			$conds_four_star_rating['to_user_id'] = $obj->id;
			$four_star_count = $this->CI->Rate->count_all_by($conds_four_star_rating);
			if($total_rating_count > 0) {
				$four_star_percent = number_format((float) ((100 / $total_rating_count) * $four_star_count), 1, '.', '');
			} else {
				$four_star_percent = 0;
			}


			//For 3 Stars rating
			$conds_three_star_rating['rating'] = 3;
			$conds_three_star_rating['to_user_id'] = $obj->id;
			$three_star_count = $this->CI->Rate->count_all_by($conds_three_star_rating);
			if($total_rating_count > 0) {
				$three_star_percent = number_format((float) ((100 / $total_rating_count) * $three_star_count), 1, '.', '');
			} else {
				$three_star_percent = 0;
			}


			//For 2 Stars rating
			$conds_two_star_rating['rating'] = 2;
			$conds_two_star_rating['to_user_id'] = $obj->id;
			$two_star_count = $this->CI->Rate->count_all_by($conds_two_star_rating);

			if($total_rating_count > 0) {
				$two_star_percent = number_format((float) ((100 / $total_rating_count) * $two_star_count), 1, '.', '');
			} else {
				$two_star_percent = 0;
			}

			//For 1 Stars rating
			$conds_one_star_rating['rating'] = 1;
			$conds_one_star_rating['to_user_id'] = $obj->id;
			$one_star_count = $this->CI->Rate->count_all_by($conds_one_star_rating);

			if($total_rating_count > 0) {
			$one_star_percent = number_format((float) ((100 / $total_rating_count) * $one_star_count), 1, '.', '');
			} else {
				$one_star_percent = 0;
			}


			$rating_std = new stdClass();
			$rating_std->five_star_count = $five_star_count; 
			$rating_std->five_star_percent = $five_star_percent;

			$rating_std->four_star_count = $four_star_count;
			$rating_std->four_star_percent = $four_star_percent;

			$rating_std->three_star_count = $three_star_count;
			$rating_std->three_star_percent = $three_star_percent;

			$rating_std->two_star_count = $two_star_count;
			$rating_std->two_star_percent = $two_star_percent;

			$rating_std->one_star_count = $one_star_count;
			$rating_std->one_star_percent = $one_star_percent;

			$rating_std->total_rating_count = $total_rating_count;
			$rating_std->total_rating_value = $total_rating_value;


			$obj[$i]->rating_details = $rating_std;	

		}


	}

	function convert_propertyby( &$obj )
	{
		// propertyby subscription
		if($this->CI->App_setting->get_one('app1')->is_propertyby_subscription == 1){
			
			$obj->is_subscribed_mb = 0;
			$obj->is_subscribed_fe = 0;
			if($this->get_login_user_id() != "") {
				$conds_mb['user_id'] = $this->get_login_user_id();
				$conds_mb['property_by_id_mb'] = $obj->id;
				$propertyby_subscribe_mb = $this->CI->Property_by_subscribe->get_one_by($conds_mb);
				
				if($propertyby_subscribe_mb->id != ''){
					$obj->is_subscribed_mb = 1;
				}

				$conds_fe['user_id'] = $this->get_login_user_id();
				$conds_fe['property_by_id_fe'] = $obj->id;
				$propertyby_subscribe_fe = $this->CI->Property_by_subscribe->get_one_by($conds_fe);
				
				if($propertyby_subscribe_fe->id != ''){
					$obj->is_subscribed_fe = 1;
				}
			}
		}

		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->id, 'propertyby-cover' );

		// set default icon 
		$obj->default_icon = $this->get_default_photo( $obj->id, 'propertyby-icon' );
	}
			
   function convert_township( &$obj )
	{
		$conds['city_id'] = $obj->id;
		// Colors Object 
		$count = $this->CI->Item_location_township->count_all_by( $conds );

		if ( $count > 0 ) {
			$township = $this->CI->Item_location_township->get_all_by( $conds )->result();
			$obj->townships = $township[0];
		} else {
			$color_dummy = $this->CI->Item_location_township->get_empty_object();
			$obj->townships = $color_dummy;
		}
	}

	/**
	 * amenity object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_amenity( &$obj )
	{
		

	}

	/**
	 * Gets the default photo for item
	 *
	 * @param      <type>  $id     The identifier
	 * @param      <type>  $type   The type
	 */
	function get_item_default_photo( $id, $type )
	{
		$default_photo = "";

		// get all images
		$img = $this->CI->Image->get_all_by( array( 'img_parent_id' => $id, 'img_type' => $type, 'ordering' => 1))->result();
		//print_r($img);die;

		if (!empty($img)) {
			$default_photo = $img[0];
		} else {
			$img = $this->CI->Image->get_all_by( array( 'img_parent_id' => $id, 'img_type' => $type ))->result();

			if ( count( $img ) > 0 ) {
			// if there are images for news,
				
				$default_photo = $img[0];
			} else {
			// if no image, return empty object

				$default_photo = $this->CI->Image->get_empty_object();
			}
		}

		

		return $default_photo;
	}

}