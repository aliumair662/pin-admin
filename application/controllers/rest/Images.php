<?php
require_once(APPPATH . 'libraries/REST_Controller.php');

/**
 * REST API for News
 */
class Images extends API_Controller
{

	/**
	 * Constructs Parent Constructor
	 */
	function __construct()
	{
		parent::__construct('Image');
		$this->load->library('PS_Image');
		$this->load->library('PS_Delete');
	}

	function upload_post()
	{
		
		$platform_name = $this->post('platform_name');
		if (!$platform_name) {
			$this->custom_response(get_msg('required_platform'));
		}

		$user_id = $this->post('user_id');

		if ($platform_name == "ios") {


			if (!$user_id) {
				$this->custom_response(get_msg('user_id_required'));
			}

			$uploaddir = 'uploads/';
			if(!empty( $_FILES['file'] )){
				$path_parts = pathinfo($_FILES['file']['name']);
				$_FILES['file']['name']=time().rand(1,9999).rand(1,999).rand(1,99). '.' . $path_parts['extension'];
			}
			$path_parts = pathinfo($_FILES['file']['name']);
			//$filename = $path_parts['filename'] . date( 'YmdHis' ) .'.'. $path_parts['extension'];
			$filename = $path_parts['filename'] . '.' . $path_parts['extension'];

			//if (move_uploaded_file($_FILES['pic']['tmp_name'], $uploaddir . $filename)) {
			
				

			if ( !empty( $_FILES['file'] ) && $this->ps_image->upload($_FILES)) {
				//call to image reseize

				//    $this->image_resize_calculation( FCPATH. $uploaddir . $filename );

				$user_data = array('user_profile_photo' => $filename);
				if ($this->User->save($user_data, $user_id)) {

					$user = $this->User->get_one($user_id);

					$this->ps_adapter->convert_user($user);

					$this->custom_response($user);
				} else {
					$this->error_response(get_msg('file_na'), 500);
				}
				
			} else {
				$this->error_response(get_msg('file_na'), 500);
			}
		} else {

			$uploaddir = 'uploads/';
			if(!empty( $_FILES['file'] )){
				$path_parts = pathinfo($_FILES['file']['name']);
				$_FILES['file']['name']=time().rand(1,9999).rand(1,999).rand(1,99). '.' . $path_parts['extension'];
			}
			$path_parts = pathinfo($_FILES['file']['name']);
			//$filename = $path_parts['filename'] . date( 'YmdHis' ) .'.'. $path_parts['extension'];
			$filename = $path_parts['filename'] . '.' . $path_parts['extension'];


			//if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . $filename)) {
			$upload_data = $this->ps_image->upload($_FILES);

			$filename = $upload_data[0]["file_name"];


			if ( !empty( $_FILES['file'] ) && count($upload_data) > 0) {

				//call to image reseize

				//    $this->image_resize_calculation( FCPATH. $uploaddir . $filename );
				$user_data = array('user_profile_photo' => $filename);
				if ($this->User->save($user_data, $user_id)) {

					$user = $this->User->get_one($user_id);

					$this->ps_adapter->convert_user($user);

					$this->custom_response($user);
				} else {
					$this->error_response(get_msg('file_na'), 500);
				}

				

			} else {
				$this->error_response(get_msg('file_na'), 500);
			}
		}
	}

	function upload_multiprofile_post()
	{
		
		$platform_name = $this->post('platform_name');
		if (!$platform_name) {
			$this->custom_response(get_msg('required_platform'));
		}

		$user_id = $this->post('user_id');

		if ($platform_name == "ios") {


			if (!$user_id) {
				$this->custom_response(get_msg('user_id_required'));
			}

			$uploaddir = 'uploads/';
			if(!empty( $_FILES['file'] )){
				$path_parts = pathinfo($_FILES['file']['name']);
				$_FILES['file']['name']=time().rand(1,9999).rand(1,999).rand(1,99). '.' . $path_parts['extension'];
			}
			$path_parts = pathinfo($_FILES['file']['name']);
			//$filename = $path_parts['filename'] . date( 'YmdHis' ) .'.'. $path_parts['extension'];
			$filename = $path_parts['filename'] . '.' . $path_parts['extension'];

			//if (move_uploaded_file($_FILES['pic']['tmp_name'], $uploaddir . $filename)) {
			
				

			if ( !empty( $_FILES['file'] ) && $this->ps_image->upload($_FILES)) {
				//call to image reseize

				//    $this->image_resize_calculation( FCPATH. $uploaddir . $filename );

				// $user_data = array('user_profile_photo' => $filename);
				// if ($this->User->save($user_data, $user_id)) {

				// 	$user = $this->User->get_one($user_id);

				// 	$this->ps_adapter->convert_user($user);

				// 	$this->custom_response($user);
				// } else {
				// 	$this->error_response(get_msg('file_na'), 500);
				// }
				$user_data = array('user_profile_photo' => $filename,'user_id'=>$user_id);
				if ($this->db->insert('core_users_profiles_images',$user_data)) {

					$user = $this->User->get_one($user_id);

					$this->ps_adapter->convert_user($user);

					$this->custom_response($user);
				} else {
					$this->error_response(get_msg('file_na'), 500);
				}
			} else {
				$this->error_response(get_msg('file_na'), 500);
			}
		} else {

			$uploaddir = 'uploads/';
			if(!empty( $_FILES['file'] )){
				$path_parts = pathinfo($_FILES['file']['name']);
				$_FILES['file']['name']=time().rand(1,9999).rand(1,999).rand(1,99). '.' . $path_parts['extension'];
			}
			$path_parts = pathinfo($_FILES['file']['name']);
			//$filename = $path_parts['filename'] . date( 'YmdHis' ) .'.'. $path_parts['extension'];
			$filename = $path_parts['filename'] . '.' . $path_parts['extension'];


			//if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . $filename)) {
			$upload_data = $this->ps_image->upload($_FILES);

			$filename = $upload_data[0]["file_name"];


			if ( !empty( $_FILES['file'] ) && count($upload_data) > 0) {

				//call to image reseize

				//    $this->image_resize_calculation( FCPATH. $uploaddir . $filename );
				// $user_data = array('user_profile_photo' => $filename);
				// if ($this->User->save($user_data, $user_id)) {

				// 	$user = $this->User->get_one($user_id);

				// 	$this->ps_adapter->convert_user($user);

				// 	$this->custom_response($user);
				// } else {
				// 	$this->error_response(get_msg('file_na'), 500);
				// }

				$user_data = array('user_profile_photo' => $filename,'user_id'=>$user_id);
				if ($this->db->insert('core_users_profiles_images',$user_data)) {

					$user = $this->User->get_one($user_id);

					$this->ps_adapter->convert_user($user);

					$this->custom_response($user);
				} else {
					$this->error_response(get_msg('file_na'), 500);
				}

			} else {
				$this->error_response(get_msg('file_na'), 500);
			}
		}
	}
	
	function upload_item_post()
	{
		$id = $this->post('item_id');
		$login_user_id = $this->get('login_user_id');
		$owner_id = $this->Item->get_one($id)->added_user_id;
		if ($login_user_id == $owner_id) {

			$item_id = $this->post('item_id');
			$files = $this->post('file');
			$img_id = $this->post('img_id');

			if (trim($img_id) == "") {
				$max_img_upload_count = $this->App_setting->get_one('app1')->max_img_upload_of_item;

				$conds['img_parent_id'] = $item_id;
				$conds['img_type'] = 'item';
				$img_count = $this->Image->count_all_by($conds);

				if ($max_img_upload_count > $img_count) {
					$path_parts = pathinfo($_FILES['file']['name']);

					if (strtolower($path_parts['extension']) != "jpeg" && strtolower($path_parts['extension']) != "png" && strtolower($path_parts['extension']) != "jpg") {


						$uploaddir = 'uploads/';
						$uploaddir_thumb = 'uploads/thumbnail/';

						$path_parts = pathinfo($_FILES['file']['name']);

						$filename = $path_parts['filename'] . date('YmdHis') . '.' . $path_parts['extension'];



						// upload image to "uploads" folder
						if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . $filename)) {

							//move uploaded image to thumbnail folder
							if (copy($uploaddir . $filename, $uploaddir_thumb . $filename)) {
								//copy success file
								$item_img_data = array(
									'img_parent_id' => $item_id,
									'img_path' => $filename,
									'img_type' => "item",
									'img_width' => 0,
									'img_height' => 0,
									'ordering' => $this->post('ordering')
								);
							}
						}
					} else {
						//if image is JPG or PNG (Not heic format)	

						$upload_data = $this->ps_image->upload($_FILES);


						foreach ($upload_data as $upload) {
							$item_img_data = array(
								'img_parent_id' => $item_id,
								'img_path' => $upload['file_name'],
								'img_type' => "item",
								'img_width' => $upload['image_width'],
								'img_height' => $upload['image_height'],
								'ordering' => $this->post('ordering')
							);
						}
					}


					if ($this->Image->save($item_img_data)) {

						//for deeplinking image url update by PP
						$description = $this->Item->get_one($item_id)->description;
						$title = $this->Item->get_one($item_id)->title;
						$conds_img = array('img_type' => 'item', 'img_parent_id' => $item_id, 'ordering' => '1');
						$images = $this->Image->get_all_by($conds_img)->result();
						$img = $this->ps_image->upload_url . $images[0]->img_path;
						$deep_link = deep_linking_shorten_url($description, $title, $img, $item_id);
						$itm_data = array(
							'dynamic_link' => $deep_link
						);
						$this->Item->save($itm_data, $item_id);


						$conds['img_path'] = $item_img_data['img_path'];
						$img_id = $this->Image->get_one_by($conds)->img_id;
						$image = $this->Image->get_one($img_id);

						$this->ps_adapter->convert_image($image);

						$this->custom_response($image);
					} else {
						$this->error_response(get_msg('file_na'), 500);
					}
				} else {
					$max_img_upload_count = $this->App_setting->get_one('app1')->max_img_upload_count;
					$this->error_response(get_msg('err_max_img_upload') . $max_img_upload_count, 400);
				}
			} else {

				$path_parts = pathinfo($_FILES['file']['name']);

				if ($path_parts['extension'] == "heic" or $path_parts['extension'] == "HEIC") {

					$uploaddir = 'uploads/';
					$uploaddir_thumb = 'uploads/thumbnail/';

					$path_parts = pathinfo($_FILES['file']['name']);

					$filename = $path_parts['filename'] . date('YmdHis') . '.' . $path_parts['extension'];



					// upload image to "uploads" folder
					if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . $filename)) {

						//move uploaded image to thumbnail folder
						if (copy($uploaddir . $filename, $uploaddir_thumb . $filename)) {
							//copy success file
							$item_img_data = array(
								'img_parent_id' => $item_id,
								'img_path' => $filename,
								'img_type' => "item",
								'img_width' => 0,
								'img_height' => 0,
								'ordering' => $this->post('ordering')
							);
						}
					}
				} else {

					// upload images
					$upload_data = $this->ps_image->upload($_FILES);

					if (empty($_FILES)) {
						$img_path = $this->Image->get_one($img_id)->img_path;
						$img_width = $this->Image->get_one($img_id)->img_width;
						$img_height = $this->Image->get_one($img_id)->img_height;

						$item_img_data = array(
							'img_id' => $img_id,
							'img_parent_id' => $item_id,
							'img_path' => $img_path,
							'img_width' => $img_width,
							'img_height' => $img_height,
							'ordering' => $this->post('ordering')
						);
					} else {

						foreach ($upload_data as $upload) {
							$item_img_data = array(
								'img_id' => $img_id,
								'img_parent_id' => $item_id,
								'img_path' => $upload['file_name'],
								'img_width' => $upload['image_width'],
								'img_height' => $upload['image_height'],
								'ordering' => $this->post('ordering')
							);
						}
					}
				}



				if ($this->Image->save($item_img_data, $img_id)) {

					//for deeplinking image url update by PP
					$description = $this->Item->get_one($item_id)->description;
					$title = $this->Item->get_one($item_id)->title;
					$conds_img = array('img_type' => 'item', 'img_parent_id' => $item_id, 'ordering' => '1');
					$images = $this->Image->get_all_by($conds_img)->result();
					$img = $this->ps_image->upload_url . $images[0]->img_path;
					$deep_link = deep_linking_shorten_url($description, $title, $img, $item_id);
					$itm_data = array(
						'dynamic_link' => $deep_link
					);
					$this->Item->save($itm_data, $item_id);

					$image = $this->Image->get_one($img_id);

					$this->ps_adapter->convert_image($image);

					$this->custom_response($image);
				} else {
					$this->error_response(get_msg('file_na'), 500);
				}
			}
		} else {
			$this->error_response(get_msg('unauthorize_item_edit'), 403);
		}
	}


	/**
     * it updates the seller profile
     * if they have more then one request/chat
     * @method isDesired
     * @param seller_user_id
     * */
    private function isDesired($seller_user_id){

        $chk_seller_info = $this->User->get_one($seller_user_id);
        $chat_data_count = $this->Chat->count_all_by([
           'seller_user_id' => $seller_user_id
        ]);

        if ( $chat_data_count > 1 ){
           
           $this->load->database();
           $this->db->set('desired', '1', false);
           $this->db->where('user_id', $seller_user_id);
           $this->db->update('core_users');

        }

    }

	/** Chat image upload api */

	function chat_image_upload_post()
	{

		$sender_id = $this->post('sender_id');
		$type = $this->post('type');
		$chat_data = array(

			"item_id" => $this->post('item_id'),
			"buyer_user_id" => $this->post('buyer_user_id'),
			"seller_user_id" => $this->post('seller_user_id')

		);

		$chat_history_data = $this->Chat->get_one_by($chat_data);
		$is_user_online = $this->post('is_user_online');
		//////
		if ($chat_history_data->id == "") {
			if ($type == "to_buyer") {

				$buyer_unread_count = $chat_history_data->buyer_unread_count;

				if ($is_user_online == '1') {
					//if user is online, no need to send noti and no need to add unread count

					$chat_data = array(

						"item_id" => $this->post('item_id'),
						"buyer_user_id" => $this->post('buyer_user_id'),
						"seller_user_id" => $this->post('seller_user_id'),
						"buyer_unread_count" => $buyer_unread_count,
						"added_date" => date("Y-m-d H:i:s"),

					);
				} else {
					//if user is offline, send noti and add unread count

					$chat_data = array(

						"item_id" => $this->post('item_id'),
						"buyer_user_id" => $this->post('buyer_user_id'),
						"seller_user_id" => $this->post('seller_user_id'),
						"buyer_unread_count" => $buyer_unread_count + 1,
						"added_date" => date("Y-m-d H:i:s"),

					);

					//prepare data for noti
					$user_ids[] = $this->post('buyer_user_id');
					$devices = $this->Noti->get_all_device_in($user_ids)->result();

					$device_ids = array();
					if (count($devices) > 0) {
						foreach ($devices as $device) {
							$device_ids[] = $device->device_token;
						}
					}

					$platform_names = array();;
					if (count($devices) > 0) {
						foreach ($devices as $platform) {
							$platform_names[] = $platform->platform_name;
						}
					}

					$user_id = $this->post('seller_user_id');
					$user_name = $this->User->get_one($user_id)->user_name;

					$data['message'] = get_msg('image');
					$data['buyer_user_id'] = $this->post('buyer_user_id');
					$data['seller_user_id'] = $this->post('seller_user_id');
					$data['sender_name'] = $user_name;
					$data['item_id'] = $this->post('item_id');
					$data['flag'] = 'chat';
					$data['chat_flag'] = 'CHAT_FROM_BUYER';

					$status = send_android_fcm($device_ids, $data, $platform_names);
				}
			} elseif ($type == "to_seller") {


				$seller_unread_count = $chat_history_data->seller_unread_count;

				if ($is_user_online == '1') {
					//if user is online, no need to send noti and no need to add unread count

					$chat_data = array(

						"item_id" => $this->post('item_id'),
						"buyer_user_id" => $this->post('buyer_user_id'),
						"seller_user_id" => $this->post('seller_user_id'),
						"seller_unread_count" => $seller_unread_count,
						"added_date" => date("Y-m-d H:i:s"),

					);
				} else {
					//if user is offline, send noti and add unread count

					$chat_data = array(

						"item_id" => $this->post('item_id'),
						"buyer_user_id" => $this->post('buyer_user_id'),
						"seller_user_id" => $this->post('seller_user_id'),
						"seller_unread_count" => $seller_unread_count + 1,
						"added_date" => date("Y-m-d H:i:s"),

					);

					//prepare data for noti
					$user_ids[] = $this->post('seller_user_id');


					$devices = $this->Noti->get_all_device_in($user_ids)->result();
					//print_r($devices);die;

					$device_ids = array();
					if (count($devices) > 0) {
						foreach ($devices as $device) {
							$device_ids[] = $device->device_token;
						}
					}

					$platform_names = array();;
					if (count($devices) > 0) {
						foreach ($devices as $platform) {
							$platform_names[] = $platform->platform_name;
						}
					}

					$user_id = $this->post('buyer_user_id');
					$user_name = $this->User->get_one($user_id)->user_name;

					$data['message'] = get_msg('image');
					$data['buyer_user_id'] = $this->post('buyer_user_id');
					$data['seller_user_id'] = $this->post('seller_user_id');
					$data['sender_name'] = $user_name;
					$data['item_id'] = $this->post('item_id');
					$data['flag'] = 'chat';
					$data['chat_flag'] = 'CHAT_FROM_SELLER';

					$status = send_android_fcm($device_ids, $data, $platform_names);
				}
			}

			$this->Chat->Save($chat_data);
			$this->isDesired($this->post('seller_user_id'));
			if (!$sender_id) {
				$this->custom_response(get_msg('sender_id_required'));
			}

			//$sender_id = $this->post('sender_id');

			$path_parts = pathinfo($_FILES['file']['name']);

			if ($path_parts['extension'] == "heic" or $path_parts['extension'] == "HEIC") {

				$uploaddir = 'uploads/';
				$uploaddir_thumb = 'uploads/thumbnail/';

				$path_parts = pathinfo($_FILES['file']['name']);
				$filename = $path_parts['filename'] . date('YmdHis') . '.' . $path_parts['extension'];

				if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . $filename)) {

					$data = getimagesize($uploaddir . $filename);
					$width = $data[0];
					$height = $data[1];

					//call to image reseize

					//$this->image_resize_calculation( FCPATH. $uploaddir . $filename );

					$img_data = array(

						'img_parent_id' => $sender_id,
						'img_type'      => "chat",
						'img_path'      => $filename,
						'img_width'     => $width,
						'img_height'    => $height

					);
				}
			} else {
				//if image is JPG or PNG (Not heic format)	
				$upload_data = $this->ps_image->upload($_FILES);


				foreach ($upload_data as $upload) {
					$img_data = array(
						'img_parent_id' => $sender_id,
						'img_path' => $upload['file_name'],
						'img_type' => "chat",
						'img_width' => $upload['image_width'],
						'img_height' => $upload['image_height']
					);
				}
			}
			//	print_r($img_data); die;

			if ($this->Image->save($img_data)) {

				//print_r($img_data['img_id']);
				$conds['img_path'] = $img_data['img_path'];
				$img_id = $this->Image->get_one_by($conds)->img_id;
				$image = $this->Image->get_one($img_id);

				//$this->ps_adapter->convert_image( $image );
				$this->isDesired($this->post('seller_user_id'));
				$this->custom_response($image);
			} else {
				$this->error_response(get_msg('file_na'), 500);
			}
		} else {

			if ($type == "to_buyer") {
				$buyer_unread_count = $chat_history_data->buyer_unread_count;

				if ($is_user_online == '1') {

					//if user is online, no need to send noti and no need to add unread count

					$chat_data = array(

						"item_id" => $this->post('item_id'),
						"buyer_user_id" => $this->post('buyer_user_id'),
						"seller_user_id" => $this->post('seller_user_id'),
						"buyer_unread_count" => $buyer_unread_count,
						"added_date" => date("Y-m-d H:i:s"),

					);
				} else {
					//if user is offline, send noti and add unread count

					$chat_data = array(

						"item_id" => $this->post('item_id'),
						"buyer_user_id" => $this->post('buyer_user_id'),
						"seller_user_id" => $this->post('seller_user_id'),
						"buyer_unread_count" => $buyer_unread_count + 1,
						"added_date" => date("Y-m-d H:i:s"),

					);

					//prepare data for noti
					$user_ids[] = $this->post('buyer_user_id');


					$devices = $this->Noti->get_all_device_in($user_ids)->result();
					//print_r($devices);die;

					$device_ids = array();
					if (count($devices) > 0) {
						foreach ($devices as $device) {
							$device_ids[] = $device->device_token;
						}
					}

					$platform_names = array();;
					if (count($devices) > 0) {
						foreach ($devices as $platform) {
							$platform_names[] = $platform->platform_name;
						}
					}

					$user_id = $this->post('seller_user_id');
					$user_name = $this->User->get_one($user_id)->user_name;

					$data['message'] = get_msg('image');
					$data['buyer_user_id'] = $this->post('buyer_user_id');
					$data['seller_user_id'] = $this->post('seller_user_id');
					$data['sender_name'] = $user_name;
					$data['item_id'] = $this->post('item_id');
					$data['flag'] = 'chat';
					$data['chat_flag'] = 'CHAT_FROM_BUYER';

					$status = send_android_fcm($device_ids, $data, $platform_names);
				}
			} elseif ($type == "to_seller") {


				$seller_unread_count = $chat_history_data->seller_unread_count;

				if ($is_user_online == '1') {
					//if user is online, no need to send noti and add unread count

					$chat_data = array(

						"item_id" => $this->post('item_id'),
						"buyer_user_id" => $this->post('buyer_user_id'),
						"seller_user_id" => $this->post('seller_user_id'),
						"seller_unread_count" => $seller_unread_count,
						"added_date" => date("Y-m-d H:i:s"),

					);
				} else {
					//if user is offline, send noti and add unread count

					$chat_data = array(

						"item_id" => $this->post('item_id'),
						"buyer_user_id" => $this->post('buyer_user_id'),
						"seller_user_id" => $this->post('seller_user_id'),
						"seller_unread_count" => $seller_unread_count + 1,
						"added_date" => date("Y-m-d H:i:s"),

					);

					//prepare data for noti
					$user_ids[] = $this->post('seller_user_id');


					$devices = $this->Noti->get_all_device_in($user_ids)->result();
					//print_r($devices);die;

					$device_ids = array();
					if (count($devices) > 0) {
						foreach ($devices as $device) {
							$device_ids[] = $device->device_token;
						}
					}

					$platform_names = array();;
					if (count($devices) > 0) {
						foreach ($devices as $platform) {
							$platform_names[] = $platform->platform_name;
						}
					}

					$user_id = $this->post('buyer_user_id');
					$user_name = $this->User->get_one($user_id)->user_name;

					$data['message'] = get_msg('image');
					$data['buyer_user_id'] = $this->post('buyer_user_id');
					$data['seller_user_id'] = $this->post('seller_user_id');
					$data['sender_name'] = $user_name;
					$data['item_id'] = $this->post('item_id');
					$data['flag'] = 'chat';
					$data['chat_flag'] = 'CHAT_FROM_BUYER';

					$status = send_android_fcm($device_ids, $data, $platform_names);
				}
			}

			if (!$this->Chat->Save($chat_data, $chat_history_data->id)) {

				$this->error_response(get_msg('err_accept_update'));
			} else {

				if (!$sender_id) {
					$this->custom_response(get_msg('sender_id_required'));
				}

				//$sender_id = $this->post('sender_id');

				$path_parts = pathinfo($_FILES['file']['name']);

				//print_r($filename); die;

				if ($path_parts['extension'] == "heic" or $path_parts['extension'] == "HEIC") {

					$uploaddir = 'uploads/';

					$path_parts = pathinfo($_FILES['file']['name']);
					$filename = $path_parts['filename'] . date('YmdHis') . '.' . $path_parts['extension'];


					if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . $filename)) {

						$data = getimagesize($uploaddir . $filename);
						$width = $data[0];
						$height = $data[1];

						//call to image reseize

						//$this->image_resize_calculation( FCPATH. $uploaddir . $filename );

						$img_data = array(

							'img_parent_id' => $sender_id,
							'img_type'      => "chat",
							'img_path'      => $filename,
							'img_width'     => $width,
							'img_height'    => $height
						);
					}
				} else {
					//if image is JPG or PNG (Not heic format)	
					$upload_data = $this->ps_image->upload($_FILES);

					foreach ($upload_data as $upload) {
						$img_data = array(
							'img_parent_id' => $sender_id,
							'img_path' => $upload['file_name'],
							'img_type' => "item",
							'img_width' => $upload['image_width'],
							'img_height' => $upload['image_height'],
							'ordering' => $this->post('ordering')
						);
					}
				}

				//  print_r($img_data); die;

				if ($this->Image->save($img_data)) {

					//print_r($img_data['img_id']);

					$conds['img_path'] = $img_data['img_path'];
					$img_id = $this->Image->get_one_by($conds)->img_id;

					$image = $this->Image->get_one($img_id);

					//$this->ps_adapter->convert_image( $image );
					$this->isDesired($this->post('seller_user_id'));
					$this->custom_response($image);
				} else {
					$this->error_response(get_msg('file_na'), 500);
				}
			}
		}
	}

	/** Delete Item Image **/

	function delete_item_image_post()
	{

		$rules = array(
			array(
				'field' => 'img_id',
				'rules' => 'required'
			)
		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)) exit;

		$img_id = $this->post('img_id');

		$conds_img['img_id'] = $img_id;

		$conds_itm_img['img_parent_id'] = $this->Image->get_one_by($conds_img)->img_parent_id;

		$img_count = $this->Image->count_all_by($conds_itm_img);



		if (!$this->ps_delete->delete_images_by(array('img_id' => $img_id))) {

			$this->error_response(get_msg('err_model'), 500);
		} else {

			if ($img_count > 1) {
				$itm_images = $this->Image->get_all_by($conds_itm_img)->result();

				for ($i = 0; $i < $img_count; $i++) {
					$conds_itm_img['order_by'] = 1;

					$j = $i + 1;
					if ($itm_images[$i]->ordering != $j) {
						$img_data = array(
							"ordering" => $j
						);

						$this->Image->save($img_data, $itm_images[$i]->img_id);
					}
				}
			}

			$this->success_response(get_msg('success_img_delete'));
		}
	}

	function delete_profile_image_post()
	{

		$rules = array(
			array(
				'field' => 'id',
				'rules' => 'required'
			)
		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)){ 
			$this->custom_response(array('status'=>'error','message'=>"id is required for profile image delete"));
			exit;
		} 

		$user_id = $this->post('user_id');

		if (!$user_id) {
			$this->custom_response(get_msg('user_id_required'));
		}

		$id = $this->post('id');

		$check=$this->db->get_where('core_users_profiles_images',array('id'=>$id,'user_id'=>$user_id));

		if($check->num_rows()>0){

			$this->db->where('id',$id);
			$status=$this->db->delete('core_users_profiles_images');
			if($status){
				$this->success_response(get_msg('success_img_delete'));
			}else{
				$this->custom_response(array('status'=>'error','message'=>"try again"));
			}
			
		}else{
			// echo json_encode();
			$this->custom_response(array('status'=>'error','message'=>"user id and image id is not match"));
		}


		
	}
	/**
	 * Convert Object
	 */
	function convert_object(&$obj)
	{
		// call parent convert object
		parent::convert_object($obj);

		// convert customize category object
		$this->ps_adapter->convert_image($obj);
	}

	/** Get Item Gallery Image */

	function get_item_gallery_get()
	{
		// add flag for default query
		$this->is_get = true;

		// get limit & offset
		$limit = $this->get('limit');
		$offset = $this->get('offset');

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge($default_conds, $user_conds);
		$conds['order_by'] = 1;

		if ($limit) {
			unset($conds['limit']);
		}

		if ($offset) {
			unset($conds['offset']);
		}

		if (!empty($limit) && !empty($offset)) {
			// if limit & offset is not empty

			$data = $this->model->get_all_by($conds, $limit, $offset)->result();
		} else if (!empty($limit)) {
			// if limit is not empty

			$data = $this->model->get_all_by($conds, $limit)->result();
		} else {
			// if both are empty

			$data = $this->model->get_all_by($conds)->result();
		}

		$this->custom_response($data, $offset);
	}

	/** Upload Video */

	function video_upload_post()
	{

		$id = $this->post('item_id');
		$login_user_id = $this->get('login_user_id');
		$owner_id = $this->Item->get_one($id)->added_user_id;
		if ($login_user_id == $owner_id) {

			$item_id = $this->post('item_id');
			$uploaddir = 'uploads/';

			$path_parts = pathinfo($_FILES['file']['name']);

			//for space video file name
			$result_filename = preg_replace("/[^a-zA-Z0-9]+/", "", $path_parts['filename']);
			$res = $result_filename . '.' . $path_parts['extension'];

			//       $tmp_filename = explode('.',$res,2);

			//       $filename = $tmp_filename[0];

			// $imagename = $filename. date( 'YmdHis' ) .".png";

			// $image_thum_path = $uploaddir_thumb . $filename . date( 'YmdHis' ) .".png";

			$video = $res;

			if (isset($video)) {
				$img_id = $this->post('img_id');

				$_FILES['file']['name'] = $res;


				if (trim($img_id) == "") {

					if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . $video)) {
						$video_data = array(
							'img_parent_id' => $item_id,
							'img_path' => $video,
							'img_type' => "video",
							'img_width' => 0,
							'img_height' => 0
						);
						if (!$this->Image->save($video_data)) {
							$this->error_response(get_msg('file_na'), 500);
						}

						$video = $this->Image->get_one($video_data['img_id']);
						$this->custom_response($video);
					}
				} else {


					if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . $video)) {
						$video_data = array(
							'img_id' => $img_id,
							'img_parent_id' => $item_id,
							'img_path' => $video,
							'img_type' => "video",
							'img_width' => 0,
							'img_height' => 0
						);
						if (!$this->Image->save($video_data, $img_id)) {
							$this->error_response(get_msg('file_na'), 500);
						}

						$video = $this->Image->get_one($video_data['img_id']);

						$this->custom_response($video);
					}
				}
			} else {
				$this->error_response(get_msg('file_na'), 500);
			}
		} else {
			$this->error_response(get_msg('unauthorize_item_edit'), 403);
		}
	}

	/**
	 * Delete Video by id and type
	 *
	 * @param      <type>  $conds  The conds
	 */
	function delete_videos_by($conds)
	{
		/**
		 * Delete Video from folder
		 *
		 */

		$videos = $this->Image->get_all_by($conds);

		if (!empty($videos)) {

			foreach ($videos->result() as $vid) {

				if (!$this->ps_image->delete_images($vid->img_path)) {
					// if there is an error in deleting images

					$this->set_flash_msg('error', get_msg('err_del_image'));
					return false;
				}
			}
		}

		/**
		 * Delete images from database table
		 */
		if (!$this->Image->delete_by($conds)) {

			$this->set_flash_msg('error', get_msg('err_model'));
			return false;
		}

		return true;
	}

	/** delete item video */

	function delete_video_and_icon_post()
	{

		$rules = array(
			array(
				'field' => 'img_id',
				'rules' => 'required'
			)
		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)) exit;

		$img_id = $this->post('img_id');
		$img_data = $this->Image->get_one($img_id);
		//print_r($img_data->is_empty_object);die;

		$img_type = $this->Image->get_one($img_id)->img_type;

		if ($img_type == "video") {
			$success_delete = get_msg('success_video_delete');
		} else {
			$success_delete = get_msg('success_video_icon_delete');
		}

		if ($img_data->is_empty_object != 1) {
			if (!$this->ps_delete->delete_images_by(array('img_id' => $img_id))) {

				$this->error_response(get_msg('err_model'), 500);
			} else {

				$this->success_response($success_delete);
			}
		} else {
			$this->error_response(get_msg('invalid_img_id'), 400);
		}
	}

	/** upload video icon */

	function upload_video_icon_post()
	{

		$id = $this->post('item_id');
		$login_user_id = $this->get('login_user_id');
		$owner_id = $this->Item->get_one($id)->added_user_id;
		if ($login_user_id == $owner_id) {

			$item_id = $this->post('item_id');
			$files = $this->post('file');
			$img_id = $this->post('img_id');

			if (trim($img_id) == "") {

				$path_parts = pathinfo($_FILES['file']['name']);

				if (strtolower($path_parts['extension']) != "jpeg" && strtolower($path_parts['extension']) != "png" && strtolower($path_parts['extension']) != "jpg") {


					$uploaddir = 'uploads/';

					$path_parts = pathinfo($_FILES['file']['name']);

					$filename = $path_parts['filename'] . date('YmdHis') . '.' . $path_parts['extension'];



					// upload image to "uploads" folder
					if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . $filename)) {

						//move uploaded image to thumbnail folder
						$item_img_data = array(
							'img_parent_id' => $item_id,
							'img_path' => $filename,
							'img_type' => "video-icon",
							'img_width' => 0,
							'img_height' => 0,
							'ordering' => $this->post('ordering')
						);
					}
				} else {
					//if image is JPG or PNG (Not heic format)	
					$upload_data = $this->ps_image->upload($_FILES);


					foreach ($upload_data as $upload) {
						$item_img_data = array(
							'img_parent_id' => $item_id,
							'img_path' => $upload['file_name'],
							'img_type' => "video-icon",
							'img_width' => $upload['image_width'],
							'img_height' => $upload['image_height'],
							'ordering' => $this->post('ordering')
						);
					}
				}


				if ($this->Image->save($item_img_data)) {

					$image = $this->Image->get_one($item_img_data['img_id']);

					$this->ps_adapter->convert_image($image);

					$this->custom_response($image);
				} else {
					$this->error_response(get_msg('file_na'), 500);
				}
			} else {

				$path_parts = pathinfo($_FILES['file']['name']);

				if ($path_parts['extension'] == "heic" or $path_parts['extension'] == "HEIC") {

					$uploaddir = 'uploads/';

					$path_parts = pathinfo($_FILES['file']['name']);

					$filename = $path_parts['filename'] . date('YmdHis') . '.' . $path_parts['extension'];



					// upload image to "uploads" folder
					if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . $filename)) {

						//copy success file
						$item_img_data = array(
							'img_parent_id' => $item_id,
							'img_path' => $filename,
							'img_type' => "video-icon",
							'img_width' => 0,
							'img_height' => 0,
							'ordering' => $this->post('ordering')
						);
					}
				} else {

					// upload images
					$upload_data = $this->ps_image->upload($_FILES);

					foreach ($upload_data as $upload) {
						$item_img_data = array(
							'img_id' => $img_id,
							'img_parent_id' => $item_id,
							'img_path' => $upload['file_name'],
							'img_width' => $upload['image_width'],
							'img_height' => $upload['image_height'],
							'ordering' => $this->post('ordering')
						);
					}
				}



				if ($this->Image->save($item_img_data, $img_id)) {

					$image = $this->Image->get_one($item_img_data['img_id']);

					$this->ps_adapter->convert_image($image);

					$this->custom_response($image);
				} else {
					$this->error_response(get_msg('file_na'), 500);
				}
			}
		} else {
			$this->error_response(get_msg('unauthorize_item_edit'), 403);
		}
	}

	// reorder image
	function reorder_image_post()
	{

		$login_user_id = $this->get('login_user_id');

		$image_orders = $this->post();

		$index = 0;
		foreach ($image_orders as $image_order) {

			if (isset($image_order['img_id'])) {
				$img_id = $image_order['img_id'];

				$img_parent_id = $this->Image->get_one($img_id)->img_parent_id;
				$owner_id = $this->Item->get_one($img_parent_id)->added_user_id;

				if ($login_user_id == $owner_id) {

					$data['ordering'] = $image_order['ordering'];

					if (!$this->Image->save($data, $img_id)) {
						$this->error_response(get_msg('err_model'), 500);
					}
				} else {
					$this->error_response(get_msg('unauthorize_reorder_img_edit'), 403);
				}
			}
		}

		$this->success_response(get_msg('success_image_reorder'), 201);
	}
}
