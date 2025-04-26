<?php
require_once(APPPATH . 'libraries/REST_Controller.php');

/**
 * REST API for Favourites
 */
class Favourites extends API_Controller
{

	/**
	 * Constructs Parent Constructor
	 */
	function __construct()
	{
		$is_login_user_nullable = false;

		// call the parent
		parent::__construct('Favourite', $is_login_user_nullable);

		// set the validation rules for create and update
		$this->validation_rules();
	}

	/**
	 * Determines if valid input.
	 */
	function validation_rules()
	{
		// validation rules for create
		$this->create_validation_rules = array(
			array(
				'field' => 'item_id',
				'rules' => 'required|callback_id_check[Item]'
			),
			array(
				'field' => 'user_id',
				'rules' => 'required|callback_id_check[User]'
			)
		);
	}


	/**
	 * When user press favourite button from app
	 */
	function press_post()
	{

		// validation rules for create

		$rules = array(
			array(
				'field' => 'item_id',
				'rules' => 'required|callback_id_check[Item]'
			),
			array(
				'field' => 'user_id',
				'rules' => 'required|callback_id_check[User]'
			)
		);

		// validation
		if (!$this->is_valid($rules)) exit;

		$item_id = $this->post('item_id');
		$user_id = $this->post('user_id');

		$users = global_user_check($user_id);

		// prep data
		$data = array('item_id' => $item_id, 'user_id' => $user_id);

		if ($this->Favourite->exists($data)) {

			if (!$this->Favourite->delete_by($data)) {
				$this->error_response(get_msg('err_model'), 500);
			} else {
				$conds_fav['item_id'] = $item_id;

				$total_fav_count = $this->Favourite->count_all_by($conds_fav);

				$item_data['favourite_count'] = $total_fav_count;
				$this->Item->save($item_data, $item_id);
			}
		} else {

			if (!$this->Favourite->save($data)) {
				$this->error_response(get_msg('err_model'), 500);
			} else {
				$conds_fav['item_id'] = $item_id;

				$total_fav_count = $this->Favourite->count_all_by($conds_fav);

				$item_data['favourite_count'] = $total_fav_count;
				$this->Item->save($item_data, $item_id);
			}
		}

		$obj = new stdClass;
		$obj->id = $item_id;
		$item = $this->Item->get_one($obj->id);

		$item->login_user_id_post = $user_id;

		$this->ps_adapter->convert_item($item);
		$this->custom_response($item);
	}



	function getLikedEventsByLocation_post()
	{
		try {
			$rules = array(
				array(
					'field' => 'location',
					'rules' => 'required'
				)
			);
	
			if (!$this->is_valid($rules)) {
				return $this->error_response('Invalid input.', 400);
			}
	
			$location = $this->post('location');
	
			// Fetch location ID
			$this->db->select('id');
			$this->db->from('bs_item_location_townships');
			$this->db->where('township_name', $location);
			$locationResult = $this->db->get()->row();
	
			if (empty($locationResult)) {
				log_message('error', 'Location not found: ' . $location);
				return $this->custom_response(array());
			}
	
			$locationId = $locationResult->id;
	
			// Fetch all items from the location with user details who liked them
			$this->db->select('bs_items.*, bs_favourite.added_date, core_users.user_name, core_users.user_id, 
							   core_users.user_profile_photo, core_users.interests, core_users.gender,
							   core_users.age, 
							   core_users.talk_about, core_users.interested_gender');
			$this->db->from('bs_items');
			$this->db->join('bs_favourite', 'bs_favourite.item_id = bs_items.id');
			$this->db->join('core_users', 'core_users.user_id = bs_favourite.user_id');
			$this->db->where('bs_items.item_location_township_id', $locationId);
			$this->db->where('core_users.swipe_profile_hidden', 0);
			$this->db->order_by('bs_favourite.added_date', 'DESC');
			$itemsResult = $this->db->get()->result();
	
			if (empty($itemsResult)) {
				log_message('error', 'No items found in location: ' . $location);
				return $this->custom_response(array());
			}
	
			$response = array();
	
			foreach ($itemsResult as $item) {
				$itemDetails = array(
					'item' => $item,
					'country' => null,
					'township' => null
				);
	
				// Check if item contains a coupon
				$this->db->from('bs_coupons');
				$this->db->where('item_id', $item->id);
				$couponExists = $this->db->count_all_results() > 0;
	
				// Get city location data
				$cityResult = $this->db->select('*')
									   ->from('bs_item_location_cities')
									   ->where('id', $item->item_location_city_id)
									   ->get()
									   ->row();
	
				if (!empty($cityResult)) {
					$itemDetails['country'] = $cityResult;
				}
	
				// Get township location data
				$townshipResult = $this->db->select('*')
										   ->from('bs_item_location_townships')
										   ->where('id', $item->item_location_township_id)
										   ->get()
										   ->row();
	
				if (!empty($townshipResult)) {
					$itemDetails['township'] = $townshipResult;
				}
	
				// Get item image
				$imageResult = $this->db->select('*')
										->from('core_images')
										->where('img_type', 'item')
										->where('img_parent_id', $item->id)
										->order_by('ordering', 'asc')
										->get()
										->row();
	
				$itemDetails['default_photo'] = !empty($imageResult) ? array(
					'img_id' => $imageResult->img_id,
					'img_path' => $imageResult->img_path,
					'img_width' => $imageResult->img_width,
					'img_height' => $imageResult->img_height,
					'img_desc' => $imageResult->img_desc,
					'img_parent_id' => $imageResult->img_parent_id,
					'img_type' => $imageResult->img_type
				) : array();
	
				// Fetch profile photos for the specific user_id
				$profilePhotos = $this->db->select('user_profile_photo')
										  ->from('core_users_profiles_images')
										  ->where('user_id', $item->user_id)
										  ->get()
										  ->result();
	
				$user_profile_photos = array_column($profilePhotos, 'user_profile_photo');
	
				$user = array(
					'name' => $item->user_name,
					'id' => $item->user_id,
					'picture' => $item->user_profile_photo,
					'gender' => $item->gender,
					'age' => $item->age,
					'interests' => $item->interests,
					'talk_about' => $item->talk_about,
					'interested_gender' => $item->interested_gender,
					'profile_photos' => $user_profile_photos
				);
	
				$response[] = array(
					'user' => $user,
					'item_details' => $itemDetails,
					'is_contains_coupon' => $couponExists
				);
			}
	
			return $this->custom_response($response);
		} catch (Exception $e) {
			return $this->error_response('An error occurred: ' . $e->getMessage());
		}
	}
	
}
