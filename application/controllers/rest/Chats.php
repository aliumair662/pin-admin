<?php
require_once( APPPATH .'libraries/REST_Controller.php' );

/**
 * REST API for Notification
 */
class Chats extends API_Controller
{
    /**
     * Constructs Parent Constructor
     */
    function __construct()
    {
        // call the parent
        parent::__construct( 'Chat' );

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

    private function send_onesignal_notification($external_user_ids, $message, $data = []) {
        $content = array(
            "en" => $message
        );

        if (!is_array($external_user_ids)) {
            $external_user_ids = [$external_user_ids];
        }

        $payload = [
            "app_id" => "0b24b754-04bf-4f12-88a5-4963071e6982",
            "include_external_user_ids" => $external_user_ids,
            "headings" => ["en" => "New Message"],
            'contents' => $content,
            "data" => $data
        ];
    
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.onesignal.com/notifications",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic os_v2_app_bmslovaex5hrfcffjfrqohtjqibdcb7kowee2c4f7haslnzzj7sqx3ax2ynhkm6zxxs7y2zifresqimxofkkmdmcl2eiykpfvpalmxi",
                "Accept: application/json",
                "Content-Type: application/json"
            ],
        ]);
        
        $response = curl_exec($curl);

        curl_close($curl);
    
        return $response;
    }
   
    /**
     * Add Chat History
     */
    function add_post()
    {
        // validation rules for chat history
        $rules = array(
            
            array(
                'field' => 'buyer_user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'seller_user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'is_user_online',
                'rules' => 'required'
            )

        );
        
        if(!empty($this->post('item_id'))){
            $item_id = $this->post('item_id');
        }else{
            $item_id = '';
        }
        
        $seller_user_id = $this->post('seller_user_id');
        $buyer_user_id = $this->post('buyer_user_id');
        $is_user_online = $this->post('is_user_online');

        $status = $this->Item->get_one($item_id)->status;
        $chk_seller_info = $this->User->get_one($seller_user_id);
        $chk_buyer_info = $this->User->get_one($buyer_user_id);

        if(!empty($this->post('item_id'))){
        if ($status == -1 || $chk_seller_info->is_empty_object == 1 || $chk_buyer_info->is_empty_object == 1) {
            $this->error_response( get_msg( 'cannot_chat' ), 503);
        }
        }else{
            if ($chk_seller_info->is_empty_object == 1 || $chk_buyer_info->is_empty_object == 1) {
                $this->error_response( get_msg( 'cannot_chat' ), 503);
            }   
        }


        // exit if there is an error in validation,
        if ( !$this->is_valid( $rules )) exit;
        $type = $this->post('type');

        $chat_data = array(

            "item_id" => $this->post('item_id'),
            "buyer_user_id" => $this->post('buyer_user_id'),
            "seller_user_id" => $this->post('seller_user_id')

        );

        $chat_data_count = $this->Chat->count_all_by($chat_data);

        if ($chat_data_count > 1) {
            $this->Chat->delete_by($chat_data);
        }

        $chat_history_data = $this->Chat->get_one_by($chat_data);


        if($chat_history_data->id == "") {

            if ( $type == "to_buyer" ) {

                $buyer_unread_count = $chat_history_data->buyer_unread_count;

                if ($is_user_online == '1') {
                    //if user is online, no need to send noti and no need to add unread count

                    $chat_data = array(

                        "item_id" => $this->post('item_id'),
                        "buyer_user_id" => $this->post('buyer_user_id'),
                        "seller_user_id" => $this->post('seller_user_id'),
                        "buyer_unread_count" => $buyer_unread_count,
                        "added_date" => date("Y-m-d H:i:s"),
                        "offer_status" => 1

                    );
                } else {
                    //if user is offline, send noti and add unread count
                    $chat_data = array(

                        "item_id" => $this->post('item_id'),
                        "buyer_user_id" => $this->post('buyer_user_id'),
                        "seller_user_id" => $this->post('seller_user_id'),
                        "buyer_unread_count" => (int)$buyer_unread_count + 1,
                        "added_date" => date("Y-m-d H:i:s"),
                        "offer_status" => 1

                    );

                    $user_ids[] = $buyer_user_id;

                    $devices = $this->Noti->get_all_device_in($user_ids)->result();

                    $device_ids = array();
                    if ( count( $devices ) > 0 ) {
                        foreach ( $devices as $device ) {
                            $device_ids[] = $device->device_token;
                        }
                    }

                    $platform_names = array();
					if ( count( $devices ) > 0 ) {
						foreach ( $devices as $platform ) {
							$platform_names[] = $platform->platform_name;
						}
					}

                    $user_id = $seller_user_id;
                    $user_name = $this->User->get_one($user_id)->user_name;
                    $user_profile_photo = $this->User->get_one($user_id)->user_profile_photo;
                    
                    $data['message'] = $this->post('message');
                    $data['buyer_user_id'] = $buyer_user_id;
                    $data['seller_user_id'] = $seller_user_id;
                    $data['sender_name'] = $user_name;
                    $data['item_id'] = $item_id;
                    $data['sender_profle_photo'] = $user_profile_photo;
                    $data['flag'] = 'chat';
			    	$data['chat_flag'] = 'CHAT_FROM_BUYER';

                    $status = send_android_fcm( $device_ids, $data, $platform_names );

                }



            } elseif ( $type == "to_seller" ) {

                $seller_unread_count = $chat_history_data->seller_unread_count;

                if ($is_user_online == '1') {
                    //if user is online, no need to send noti and no need to add unread count

                    $chat_data = array(

                        "item_id" => $this->post('item_id'),
                        "buyer_user_id" => $this->post('buyer_user_id'),
                        "seller_user_id" => $this->post('seller_user_id'),
                        "seller_unread_count" => $seller_unread_count,
                        "added_date" => date("Y-m-d H:i:s"),
                        "offer_status" => 1

                    );
                } else {
                    //if user is offline, send noti and add unread count
                    $chat_data = array(

                        "item_id" => $this->post('item_id'),
                        "buyer_user_id" => $this->post('buyer_user_id'),
                        "seller_user_id" => $this->post('seller_user_id'),
                        "seller_unread_count" => (int)$seller_unread_count + 1,
                        "added_date" => date("Y-m-d H:i:s"),
                        "offer_status" => 1

                    );

                    $user_ids[] = $seller_user_id;

                    $devices = $this->Noti->get_all_device_in($user_ids)->result();

                    $device_ids = array();
                    if ( count( $devices ) > 0 ) {
                        foreach ( $devices as $device ) {
                            $device_ids[] = $device->device_token;
                        }
                    }

                    $platform_names = array();
					if ( count( $devices ) > 0 ) {
						foreach ( $devices as $platform ) {
							$platform_names[] = $platform->platform_name;
						}
					}

                    $user_id = $buyer_user_id;
                    $user_name = $this->User->get_one($user_id)->user_name;
                    $user_profile_photo = $this->User->get_one($user_id)->user_profile_photo;

                    $data['message'] = $this->post('message');
                    $data['buyer_user_id'] = $buyer_user_id;
                    $data['seller_user_id'] = $seller_user_id;
                    $data['sender_name'] = $user_name;
                    $data['item_id'] = $item_id;
                    $data['sender_profle_photo'] = $user_profile_photo;
                    $data['flag'] = 'chat';
			    	$data['chat_flag'] = 'CHAT_FROM_BUYER';

                    $status = send_android_fcm( $device_ids, $data, $platform_names );
                }



            }

            if ( !$this->Chat->save($chat_data)) {

                $this->error_response( get_msg( 'err_chat_history_save' ), 500);

            } else {

                $obj = $this->Chat->get_one_by($chat_data);
                $this->ps_adapter->convert_chathistory( $obj );
                $this->isDesired($seller_user_id); // to update seller profile
                $this->custom_response( $obj );

            }

        } else {

            if ( $type == "to_buyer" ) {

                $buyer_unread_count = $chat_history_data->buyer_unread_count;
                $is_accept = $chat_history_data->is_accept;
                $offer_status = $chat_history_data->offer_status;

                if ($is_user_online == '1') {
                    //if user is online, no need to send noti and no need to add unread count

                    $chat_data = array(

                        "item_id" => $this->post('item_id'),
                        "buyer_user_id" => $this->post('buyer_user_id'),
                        "seller_user_id" => $this->post('seller_user_id'),
                        "buyer_unread_count" => $buyer_unread_count,
                        "added_date" => date("Y-m-d H:i:s"),
                        "offer_status" => $offer_status,
                        "is_accept" => $is_accept

                    );
                } else {
                    //if user is offline, send noti and add unread count

                    $chat_data = array(

                        "item_id" => $this->post('item_id'),
                        "buyer_user_id" => $this->post('buyer_user_id'),
                        "seller_user_id" => $this->post('seller_user_id'),
                        "buyer_unread_count" => (int)$buyer_unread_count + 1,
                        "added_date" => date("Y-m-d H:i:s"),
                        "offer_status" => $offer_status,
                        "is_accept" => $is_accept

                    );

                    $user_ids[] = $buyer_user_id;

                    $devices = $this->Noti->get_all_device_in($user_ids)->result();

                    $device_ids = array();
                    if ( count( $devices ) > 0 ) {
                        foreach ( $devices as $device ) {
                            $device_ids[] = $device->device_token;
                        }
                    }

					$platform_names = array();
					if ( count( $devices ) > 0 ) {
						foreach ( $devices as $platform ) {
							$platform_names[] = $platform->platform_name;
						}
					}

                    $user_id = $seller_user_id;
                    $user_name = $this->User->get_one($user_id)->user_name;
                    $user_profile_photo = $this->User->get_one($user_id)->user_profile_photo;

                    $data['message'] = $this->post('message');
                    $data['buyer_user_id'] = $buyer_user_id;
                    $data['seller_user_id'] = $seller_user_id;
                    $data['sender_name'] = $user_name;
                    $data['item_id'] = $item_id;
                    $data['sender_profle_photo'] = $user_profile_photo;
                    $data['flag'] = 'chat';
			    	$data['chat_flag'] = 'CHAT_FROM_BUYER';
                    $status = send_android_fcm( $device_ids, $data, $platform_names );

                    $this->send_onesignal_notification($buyer_user_id, $this->post('message'), $data);

                }



            } elseif ( $type == "to_seller" ) {

                $seller_unread_count = $chat_history_data->seller_unread_count;
                $is_accept = $chat_history_data->is_accept;
                $offer_status = $chat_history_data->offer_status;

                if ($is_user_online == '1') {
                    //if user is online, no need to send noti and no need to add unread count
                    $chat_data = array(

                        "item_id" => $this->post('item_id'),
                        "buyer_user_id" => $this->post('buyer_user_id'),
                        "seller_user_id" => $this->post('seller_user_id'),
                        "seller_unread_count" => $seller_unread_count,
                        "added_date" => date("Y-m-d H:i:s"),
                        "offer_status" => $offer_status,
                        "is_accept" => $is_accept

                    );

                } else {
                    //if user is offline, need to send noti and add unread count
                    $chat_data = array(

                        "item_id" => $this->post('item_id'),
                        "buyer_user_id" => $this->post('buyer_user_id'),
                        "seller_user_id" => $this->post('seller_user_id'),
                        "seller_unread_count" => (int)$seller_unread_count + 1,
                        "added_date" => date("Y-m-d H:i:s"),
                        "offer_status" => $offer_status,
                        "is_accept" => $is_accept

                    );

                    $user_ids[] = $seller_user_id;

                    $devices = $this->Noti->get_all_device_in($user_ids)->result();

                    $device_ids = array();
                    if ( count( $devices ) > 0 ) {
                        foreach ( $devices as $device ) {
                            $device_ids[] = $device->device_token;
                        }
                    }

					$platform_names = array();
					if ( count( $devices ) > 0 ) {
						foreach ( $devices as $platform ) {
							$platform_names[] = $platform->platform_name;
						}
					}

                    $user_id = $buyer_user_id;
                    $user_name = $this->User->get_one($user_id)->user_name;
                    $user_profile_photo = $this->User->get_one($user_id)->user_profile_photo;

                    $data['message'] = $this->post('message');
                    $data['buyer_user_id'] = $buyer_user_id;
                    $data['seller_user_id'] = $seller_user_id;
                    $data['sender_name'] = $user_name;
                    $data['item_id'] = $item_id;
                    $data['sender_profle_photo'] = $user_profile_photo;
                    $data['flag'] = 'chat';
			    	$data['chat_flag'] = 'CHAT_FROM_SELLER';

                    $status = send_android_fcm( $device_ids, $data, $platform_names );

                    $this->send_onesignal_notification($seller_user_id, $this->post('message'), $data);
                }


            }


            if ( $this->Chat->save($chat_data,$chat_history_data->id)) {

                $obj = $this->Chat->get_one_by($chat_data);
                $this->ps_adapter->convert_chathistory( $obj );
                $this->isDesired($seller_user_id); // to update seller profile
                $this->custom_response( $obj );

            }

        }

    }

    function create_id_post()
    {
        $rules = array(
            array(
                'field' => 'user_id',
                'rules' => 'required'
            ),
        );
        
        if (!$this->is_valid($rules)) exit;

        $payload = json_encode([
            "properties" => [
                "language" => "en",
                "timezone_id" => "UTC",
                "lat" => 0,
                "long" => 0,
                "country" => "US",
                "first_active" => time(),
                "last_active" => time()
            ],
            "identity" => [
                "external_id" => $this->post('user_id'),
            ]
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.onesignal.com/apps/0b24b754-04bf-4f12-88a5-4963071e6982/users");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic os_v2_app_bmslovaex5hrfcffjfrqohtjqibdcb7kowee2c4f7haslnzzj7sqx3ax2ynhkm6zxxs7y2zifresqimxofkkmdmcl2eiykpfvpalmxi'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        if ($response) {
            $data = json_decode($response, true); // Convert JSON to PHP array
            if (isset($data['identity']['onesignal_id'])) {
                $onesignal_id = $data['identity']['onesignal_id'];

                // Store onesignal_id in users table (using CodeIgniter Query Builder)
                $this->db->where('user_id', $this->post('user_id'))->update('core_users', ['onesignal_id' => $onesignal_id]);

                return true;
            }
        }
    }

    /**
     * Update Price
     */
    function update_price_post()
    {
        // validation rules for chat history
        $rules = array(
            array(
                'field' => 'item_id',
                'rules' => 'required|callback_id_check[Item]'
            ),
            array(
                'field' => 'buyer_user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'seller_user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'nego_price',
                'rules' => 'required'
            ),
            array(
                'field' => 'is_user_online',
                'rules' => 'required'
            )
        );

        // exit if there is an error in validation,
        if ( !$this->is_valid( $rules )) exit;

        $type = $this->post('type');
        $is_user_online = $this->post('is_user_online');

        $chat_data = array(

            "item_id" => $this->post('item_id'),
            "buyer_user_id" => $this->post('buyer_user_id'),
            "seller_user_id" => $this->post('seller_user_id')

        );

        $price = $this->post('nego_price');
        $is_sold_out = $this->Item->get_one($chat_data['item_id'])->is_sold_out;
        
        if ($is_sold_out == '1' && $price!= 0) {
        	//make offer to sold_out item
        	$this->error_response( get_msg( 'already_sold_out' ), 503);
        } else {

            $chat_history_data = $this->Chat->get_one_by($chat_data);
           
            if( $chat_history_data->offer_status != '2' &&  $chat_history_data->is_accept != '0' && $price == 0) {
				
				// checking reject offer chat
				$this->error_response( get_msg( 'need_make_offer' ), 503);

			}else if(($chat_history_data->offer_status == '2' || $chat_history_data->offer_status == '3') &&  $is_sold_out != '0' && $price != 0){
			
				// checking make offer chat
				$this->error_response( get_msg( 'already_accept_offer' ), 503);
				
			}else{

                if($chat_history_data->id == "") {

                    if ( $type == "to_buyer" ) {

                        $price = $this->post('nego_price');
                        if ( $price == 0) {
                            $data['message'] = get_msg('offer_rejected');
                            $offer_status = 4;
                        } else {
                            $data['message'] = get_msg('make_offer');
                            $offer_status = 2;
                        }

                        $buyer_unread_count = $chat_history_data->buyer_unread_count;

                        if ($is_user_online == '1') {
                            //if user is online, no need to send noti and no need to add unread count

                            $chat_data = array(

                                "item_id" => $this->post('item_id'),
                                "buyer_user_id" => $this->post('buyer_user_id'),
                                "seller_user_id" => $this->post('seller_user_id'),
                                "buyer_unread_count" => $buyer_unread_count,
                                "added_date" => date("Y-m-d H:i:s"),
                                "nego_price" => $this->post('nego_price'),
                                "offer_status" => $offer_status,
                                "is_accept" => 0

                            );


                        } else {
                            //if user is offline, send noti and add unread count

                            $chat_data = array(

                                "item_id" => $this->post('item_id'),
                                "buyer_user_id" => $this->post('buyer_user_id'),
                                "seller_user_id" => $this->post('seller_user_id'),
                                "buyer_unread_count" => (int)$buyer_unread_count + 1,
                                "added_date" => date("Y-m-d H:i:s"),
                                "nego_price" => $this->post('nego_price'),
                                "offer_status" => $offer_status,
                                "is_accept" => 0

                            );

                            $user_ids[] = $this->post('buyer_user_id');


                            $devices = $this->Noti->get_all_device_in($user_ids)->result();
                            //print_r($devices);die;

                            $device_ids = array();
                            if ( count( $devices ) > 0 ) {
                                foreach ( $devices as $device ) {
                                    $device_ids[] = $device->device_token;
                                }
                            }

                            $platform_names = array();
							if ( count( $devices ) > 0 ) {
								foreach ( $devices as $platform ) {
									$platform_names[] = $platform->platform_name;
								}
							}

                            $user_id = $this->post('seller_user_id');
                            $user_name = $this->User->get_one($user_id)->user_name;
	        		        $user_profile_photo = $this->User->get_one($user_id)->user_profile_photo;

                            $data['buyer_user_id'] = $this->post('buyer_user_id');
                            $data['seller_user_id'] = $this->post('seller_user_id');
                            $data['sender_name'] = $user_name;
                            $data['item_id'] = $this->post('item_id');
                            $data['sender_profle_photo'] = $user_profile_photo;
                            $data['flag'] = 'chat';
							$data['chat_flag'] = 'CHAT_FROM_BUYER';

                            $status = send_android_fcm( $device_ids, $data, $platform_names );

                        }



                    } elseif ( $type == "to_seller" ) {
                        $seller_unread_count = $chat_history_data->seller_unread_count;

                        $price = $this->post('nego_price');
                        if ( $price == 0) {
                            $data['message'] = get_msg('offer_rejected');
                            $offer_status = 4;
                        } else {
                            $data['message'] = get_msg('make_offer');
                            $offer_status = 2;
                        }

                        if ($is_user_online == '1') {
                            //if user is online, no need to send noti and no need to add unread count
                            $chat_data = array(

                                "item_id" => $this->post('item_id'),
                                "buyer_user_id" => $this->post('buyer_user_id'),
                                "seller_user_id" => $this->post('seller_user_id'),
                                "seller_unread_count" => $seller_unread_count,
                                "added_date" => date("Y-m-d H:i:s"),
                                "nego_price" => $this->post('nego_price'),
                                "offer_status" => $offer_status,
                                "is_accept" => 0

                            );
                        } else {
                            //if user is offline, send noti and add unread count



                            $chat_data = array(

                                "item_id" => $this->post('item_id'),
                                "buyer_user_id" => $this->post('buyer_user_id'),
                                "seller_user_id" => $this->post('seller_user_id'),
                                "seller_unread_count" => (int)$seller_unread_count + 1,
                                "added_date" => date("Y-m-d H:i:s"),
                                "nego_price" => $this->post('nego_price'),
                                "offer_status" => $offer_status,
                                "is_accept" => 0

                            );

                            $user_ids[] = $this->post('seller_user_id');


                            $devices = $this->Noti->get_all_device_in($user_ids)->result();
                            //print_r($devices);die;

                            $device_ids = array();
                            if ( count( $devices ) > 0 ) {
                                foreach ( $devices as $device ) {
                                    $device_ids[] = $device->device_token;
                                }
                            }

                            $platform_names = array();
							if ( count( $devices ) > 0 ) {
								foreach ( $devices as $platform ) {
									$platform_names[] = $platform->platform_name;
								}
							}

                            $user_id = $this->post('buyer_user_id');
                            $user_name = $this->User->get_one($user_id)->user_name;
                            $user_profile_photo = $this->User->get_one($user_id)->user_profile_photo;

                            $data['buyer_user_id'] = $this->post('buyer_user_id');
                            $data['seller_user_id'] = $this->post('seller_user_id');
                            $data['sender_name'] = $user_name;
                            $data['item_id'] = $this->post('item_id');
                            $data['flag'] = 'chat';
							$data['chat_flag'] = 'CHAT_FROM_SELLER';

                            $status = send_android_fcm( $device_ids, $data, $platform_names );


                        }

                    }

                    $this->Chat->save($chat_data);
                    $obj = $this->Chat->get_one_by($chat_data);
                    $this->ps_adapter->convert_chathistory( $obj );
                    $this->custom_response( $obj );


                } else {

                    if ( $type == "to_buyer" ) {

                        $buyer_unread_count = $chat_history_data->buyer_unread_count;

                        $price = $this->post('nego_price');
                        if ( $price == 0) {
                            $data['message'] = get_msg('offer_rejected');
                            $offer_status = 4;
                        } else {
                            $data['message'] = get_msg('make_offer');
                            $offer_status = 2;
                        }

                        if ($is_user_online == '1') {
                            //if user is online, no need to send noti and no need to add unread count

                            $chat_data = array(

                                "item_id" => $this->post('item_id'),
                                "buyer_user_id" => $this->post('buyer_user_id'),
                                "seller_user_id" => $this->post('seller_user_id'),
                                "buyer_unread_count" => $buyer_unread_count,
                                "added_date" => date("Y-m-d H:i:s"),
                                "nego_price" => $this->post('nego_price'),
                                "offer_status" => $offer_status,
                                "is_accept" => 0

                            );
                        } else {
                            //if user is offline,send noti and add unread count


                            $chat_data = array(

                                "item_id" => $this->post('item_id'),
                                "buyer_user_id" => $this->post('buyer_user_id'),
                                "seller_user_id" => $this->post('seller_user_id'),
                                "buyer_unread_count" => (int)$buyer_unread_count + 1,
                                "added_date" => date("Y-m-d H:i:s"),
                                "nego_price" => $this->post('nego_price'),
                                "offer_status" => $offer_status,
                                "is_accept" => 0

                            );

                            //prepare data for noti
                            $user_ids[] = $this->post('buyer_user_id');


                            $devices = $this->Noti->get_all_device_in($user_ids)->result();
                            //print_r($devices);die;

                            $device_ids = array();
                            if ( count( $devices ) > 0 ) {
                                foreach ( $devices as $device ) {
                                    $device_ids[] = $device->device_token;
                                }
                            }

                            $platform_names = array();
							if ( count( $devices ) > 0 ) {
								foreach ( $devices as $platform ) {
									$platform_names[] = $platform->platform_name;
								}
							}

                            $user_id = $this->post('seller_user_id');
                            $user_name = $this->User->get_one($user_id)->user_name;
	        		        $user_profile_photo = $this->User->get_one($user_id)->user_profile_photo;

                            $data['buyer_user_id'] = $this->post('buyer_user_id');
                            $data['seller_user_id'] = $this->post('seller_user_id');
                            $data['sender_name'] = $user_name;
                            $data['item_id'] = $this->post('item_id');
                            $data['flag'] = 'chat';
							$data['chat_flag'] = 'CHAT_FROM_BUYER';

                            $status = send_android_fcm( $device_ids, $data, $platform_names );


                        }



                    } elseif ( $type == "to_seller" ) {



                        $seller_unread_count = $chat_history_data->seller_unread_count;

                        $price = $this->post('nego_price');
                        if ( $price == 0) {
                            $data['message'] = get_msg('offer_rejected');
                            $offer_status = 4;
                        } else {
                            $data['message'] = get_msg('make_offer');
                            $offer_status = 2;
                        }

                        if ($is_user_online == '1') {
                            //if user is online, no need to send noti and no need to add unread count
                            $chat_data = array(

                                "item_id" => $this->post('item_id'),
                                "buyer_user_id" => $this->post('buyer_user_id'),
                                "seller_user_id" => $this->post('seller_user_id'),
                                "seller_unread_count" => $seller_unread_count,
                                "added_date" => date("Y-m-d H:i:s"),
                                "nego_price" => $this->post('nego_price'),
                                "offer_status" => $offer_status,
                                "is_accept" => 0

                            );

                        } else {
                            //if user is offline, send noti and add unread count

                            $chat_data = array(

                                "item_id" => $this->post('item_id'),
                                "buyer_user_id" => $this->post('buyer_user_id'),
                                "seller_user_id" => $this->post('seller_user_id'),
                                "seller_unread_count" => (int)$seller_unread_count + 1,
                                "added_date" => date("Y-m-d H:i:s"),
                                "nego_price" => $this->post('nego_price'),
                                "offer_status" => $offer_status,
                                "is_accept" => 0

                            );

                            $user_ids[] = $this->post('seller_user_id');

                            $devices = $this->Noti->get_all_device_in($user_ids)->result();



                            $device_ids = array();
                            if ( count( $devices ) > 0 ) {
                                foreach ( $devices as $device ) {
                                    $device_ids[] = $device->device_token;
                                }
                            }

							$platform_names = array();
							if ( count( $devices ) > 0 ) {
								foreach ( $devices as $platform ) {
									$platform_names[] = $platform->platform_name;
								}
							}

                            $user_id = $this->post('buyer_user_id');
                            $user_name = $this->User->get_one($user_id)->user_name;
	        		        $user_profile_photo = $this->User->get_one($user_id)->user_profile_photo;

                            $data['buyer_user_id'] = $this->post('buyer_user_id');
                            $data['seller_user_id'] = $this->post('seller_user_id');
                            $data['sender_name'] = $user_name;
                            $data['item_id'] = $this->post('item_id');
                            $data['flag'] = 'chat';
							$data['chat_flag'] = 'CHAT_FROM_SELLER';

                            $status = send_android_fcm( $device_ids, $data, $platform_names );
                        }

                    }



                    if( !$this->Chat->Save( $chat_data,$chat_history_data->id )) {

                        $this->error_response( get_msg( 'err_price_update' ), 500);


                    } else {


                        $obj = $this->Chat->get_one_by($chat_data);
                        $this->ps_adapter->convert_chathistory( $obj );
                        $this->custom_response( $obj );
                    }
                }
            }
        }
    }

    /**
     * Update count
     */
    function reset_count_post()
    {
        // validation rules for chat history
        $rules = array(
            
            array(
                'field' => 'buyer_user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'seller_user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'type',
                'rules' => 'required'
            )
        );


        // exit if there is an error in validation,
        if ( !$this->is_valid( $rules )) exit;

        if(!empty($this->post('item_id'))){
            $item_id = $this->post('item_id');
        }else{
            $item_id = '';
        }

        $chat_data = array(

            "item_id" => $item_id,
            "buyer_user_id" => $this->post('buyer_user_id'),
            "seller_user_id" => $this->post('seller_user_id')

        );

        $chat_history_data = $this->Chat->get_one_by($chat_data);


        if($chat_history_data->id == "") {

            $this->error_response( get_msg( 'err_chat_history_not_exist' ), 500);


        } else {

            if($this->post('type') == "to_seller") {

                $chat_data_update = array(

                    "item_id" =>  $item_id,
                    "buyer_user_id" => $this->post('buyer_user_id'),
                    "seller_user_id" => $this->post('seller_user_id'),
                    "seller_unread_count" => 0

                );

            } else if($this->post('type') == "to_buyer") {

                $chat_data_update = array(

                    "item_id" =>  $item_id,
                    "buyer_user_id" => $this->post('buyer_user_id'),
                    "seller_user_id" => $this->post('seller_user_id'),
                    "buyer_unread_count" => 0

                );
            }

            if( !$this->Chat->Save( $chat_data_update,$chat_history_data->id )) {

                $this->error_response( get_msg( 'err_count_update' ), 500);


            } else {

                $obj = $this->Chat->get_one_by($chat_data);
                $this->ps_adapter->convert_chathistory( $obj );
                $this->custom_response( $obj );

            }


        }


    }

    /* Update accept or not
    */


    function update_accept_post()
    {
        // validation rules for chat history
        $rules = array(
            array(
                'field' => 'item_id',
                'rules' => 'required|callback_id_check[Item]'
            ),
            array(
                'field' => 'buyer_user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'seller_user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'nego_price',
                'rules' => 'required'
            ),
            array(
                'field' => 'is_user_online',
                'rules' => 'required'
            )
        );

        // exit if there is an error in validation,
        if ( !$this->is_valid( $rules )) exit;
        $type = $this->post('type');
        $is_user_online = $this->post('is_user_online');

        $chat_data = array(

            "item_id" => $this->post('item_id'),
            "buyer_user_id" => $this->post('buyer_user_id'),
            "seller_user_id" => $this->post('seller_user_id')

        );

        $chat_history_data = $this->Chat->get_one_by($chat_data);
        //print_r($chat_history_data);die;

        if($chat_history_data->offer_status == 4){
            $this->error_response( get_msg( 'already_rejected' ), 503);
        }else{
            if( $chat_history_data->offer_status == '2' &&  $chat_history_data->is_accept == '0') {
                            

                // is_accept checking by seller_id and item_id
                $accept_checking_data = array(

                    "item_id" => $this->post('item_id'),
                    "buyer_user_id" => $this->post('buyer_user_id'),
                    "seller_user_id" => $this->post('seller_user_id'),

                );
                //print_r($accept_checking_data);die;
                $accept_checking_result = $this->Chat->get_all_by($accept_checking_data)->result();
                //print_r($accept_checking_result);die;


                $accept_result_flag = 0;

                foreach ($accept_checking_result as $rst) {

                    if ($rst->is_accept == 1) {
                        $accept_result_flag = 1;
                        break;
                    }


                }
                //print_r($accept_result_flag);die;

                if( $accept_result_flag == 1 ) {
                    $this->error_response( get_msg( 'err_accept_offer' ), 500);
                } else {


                    if($chat_history_data->id == "") {

                        if ( $type == "to_buyer" ) {



                            $buyer_unread_count = $chat_history_data->buyer_unread_count;

                            if ($is_user_online == '1') {
                                //if user is online, no need to send noti and no need to add unread count

                                $chat_data = array(

                                    "item_id" => $this->post('item_id'),
                                    "buyer_user_id" => $this->post('buyer_user_id'),
                                    "seller_user_id" => $this->post('seller_user_id'),
                                    "buyer_unread_count" => $buyer_unread_count,
                                    "added_date" => date("Y-m-d H:i:s"),
                                    "nego_price" => $this->post('nego_price'),
                                    "is_accept" => 1
                                    //"offer_status" => 3

                                );

                            } else {

                                $chat_data = array(

                                    "item_id" => $this->post('item_id'),
                                    "buyer_user_id" => $this->post('buyer_user_id'),
                                    "seller_user_id" => $this->post('seller_user_id'),
                                    "buyer_unread_count" => (int)$buyer_unread_count + 1,
                                    "added_date" => date("Y-m-d H:i:s"),
                                    "nego_price" => $this->post('nego_price'),
                                    "is_accept" => 1
                                    //"offer_status" => 3

                                );

                                //prepare data for noti
                                $user_ids[] = $this->post('buyer_user_id');


                                $devices = $this->Noti->get_all_device_in($user_ids)->result();
                                //print_r($devices);die;

                                $device_ids = array();
                                if ( count( $devices ) > 0 ) {
                                    foreach ( $devices as $device ) {
                                        $device_ids[] = $device->device_token;
                                    }
                                }

								$platform_names = array();
								if ( count( $devices ) > 0 ) {
									foreach ( $devices as $platform ) {
										$platform_names[] = $platform->platform_name;
									}
								}

                                $user_id = $this->post('seller_user_id');
                                $user_name = $this->User->get_one($user_id)->user_name;
                                $price = $this->post('nego_price');
                                
	        		            $user_profile_photo = $this->User->get_one($user_id)->user_profile_photo;

                                $data['message'] = get_msg('offer_accepted');
                                $data['buyer_user_id'] = $this->post('buyer_user_id');
                                $data['seller_user_id'] = $this->post('seller_user_id');
                                $data['sender_name'] = $user_name;
                                $data['item_id'] = $this->post('item_id');
                                $data['flag'] = 'chat';
								$data['chat_flag'] = 'CHAT_FROM_BUYER';

                                $status = send_android_fcm( $device_ids, $data, $platform_names );

                            }


                        } elseif ( $type == "to_seller" ) {



                            $seller_unread_count = $chat_history_data->seller_unread_count;

                            if ($is_user_online == '1') {
                                // if user is online, no need to send noti and no need to add noti count

                                $chat_data = array(

                                    "item_id" => $this->post('item_id'),
                                    "buyer_user_id" => $this->post('buyer_user_id'),
                                    "seller_user_id" => $this->post('seller_user_id'),
                                    "seller_unread_count" => $seller_unread_count,
                                    "added_date" => date("Y-m-d H:i:s"),
                                    "nego_price" => $this->post('nego_price'),
                                    "is_accept" => 1
                                    //"offer_status" => 3


                                );
                            } else {
                                // if user is offline,send noti and add noti count

                                $chat_data = array(

                                    "item_id" => $this->post('item_id'),
                                    "buyer_user_id" => $this->post('buyer_user_id'),
                                    "seller_user_id" => $this->post('seller_user_id'),
                                    "seller_unread_count" => (int)$seller_unread_count + 1,
                                    "added_date" => date("Y-m-d H:i:s"),
                                    "nego_price" => $this->post('nego_price'),
                                    "is_accept" => 1
                                    //"offer_status" => 3


                                );

                                //prepare data for noti
                                $user_ids[] = $this->post('seller_user_id');


                                $devices = $this->Noti->get_all_device_in($user_ids)->result();
                                //print_r($devices);die;

                                $device_ids = array();
                                if ( count( $devices ) > 0 ) {
                                    foreach ( $devices as $device ) {
                                        $device_ids[] = $device->device_token;
                                    }
                                }

								$platform_names = array();
								if ( count( $devices ) > 0 ) {
									foreach ( $devices as $platform ) {
										$platform_names[] = $platform->platform_name;
									}
								}

                                $user_id = $this->post('buyer_user_id');
                                $user_name = $this->User->get_one($user_id)->user_name;
	        		            $user_profile_photo = $this->User->get_one($user_id)->user_profile_photo;

                                $data['message'] = get_msg('offer_accepted');
                                $data['buyer_user_id'] = $this->post('buyer_user_id');
                                $data['seller_user_id'] = $this->post('seller_user_id');
                                $data['sender_name'] = $user_name;
                                $data['item_id'] = $this->post('item_id');
                                $data['flag'] = 'chat';
								$data['chat_flag'] = 'CHAT_FROM_SELLER';

                                $status = send_android_fcm( $device_ids, $data, $platform_names );

                            }



                        }


                        $this->Chat->save($chat_data);
                        $obj = $this->Chat->get_one_by($chat_data);
                        $this->ps_adapter->convert_chathistory( $obj );
                        $this->custom_response( $obj );


                    } else {


                        //print_r($chat_history_data->is_accept);die;
                        $conds_chat['buyer_user_id'] = $chat_history_data->buyer_user_id;
                        $conds_chat['seller_user_id'] = $chat_history_data->seller_user_id;
                        $conds_chat['item_id'] = $chat_history_data->item_id;

                        $chats = $this->Chat->get_all_by($conds_chat)->result();

                        //print_r($chats);die;

                        $accept_flag = 0;

                        foreach ($chats as $chat) {

                            if ($chat->is_accept == 1) {
                                $accept_flag = 1;
                                break;
                            }


                        }

                        if( $accept_flag == 1 ) {

                            $this->error_response( get_msg( 'err_accept_offer' ), 500);
                        } else {

                            if ( $type == "to_buyer" ) {

                                $buyer_unread_count = $chat_history_data->buyer_unread_count;

                                if ($is_user_online == '1') {

                                    //if user is online, no need to send noti and no need to add unread count

                                    $chat_data = array(

                                        "item_id" => $this->post('item_id'),
                                        "buyer_user_id" => $this->post('buyer_user_id'),
                                        "seller_user_id" => $this->post('seller_user_id'),
                                        "buyer_unread_count" => $buyer_unread_count,
                                        "added_date" => date("Y-m-d H:i:s"),
                                        "nego_price" => $this->post('nego_price'),
                                        "is_accept"	 => 1
                                        //"offer_status" => 3

                                    );
                                } else {
                                    //if user is offline, send noit and add unread count

                                    $chat_data = array(

                                        "item_id" => $this->post('item_id'),
                                        "buyer_user_id" => $this->post('buyer_user_id'),
                                        "seller_user_id" => $this->post('seller_user_id'),
                                        "buyer_unread_count" => (int)$buyer_unread_count + 1,
                                        "added_date" => date("Y-m-d H:i:s"),
                                        "nego_price" => $this->post('nego_price'),
                                        "is_accept"	 => 1
                                        //"offer_status" => 3

                                    );

                                    //prepare data for noti
                                    $user_ids[] = $this->post('buyer_user_id');


                                    $devices = $this->Noti->get_all_device_in($user_ids)->result();
                                    //print_r($devices);die;

                                    $device_ids = array();
                                    if ( count( $devices ) > 0 ) {
                                        foreach ( $devices as $device ) {
                                            $device_ids[] = $device->device_token;
                                        }
                                    }

									$platform_names = array();
									if ( count( $devices ) > 0 ) {
										foreach ( $devices as $platform ) {
											$platform_names[] = $platform->platform_name;
										}
									}

                                    $user_id = $this->post('seller_user_id');
                                    $user_name = $this->User->get_one($user_id)->user_name;
                                    
	        		                $user_profile_photo = $this->User->get_one($user_id)->user_profile_photo;   

                                    $data['message'] = get_msg('offer_accepted');
                                    $data['buyer_user_id'] = $this->post('buyer_user_id');
                                    $data['seller_user_id'] = $this->post('seller_user_id');
                                    $data['sender_name'] = $user_name;
                                    $data['item_id'] = $this->post('item_id');
                                    $data['flag'] = 'chat';
									$data['chat_flag'] = 'CHAT_FROM_BUYER';

                                    $status = send_android_fcm( $device_ids, $data, $platform_names );

                                }




                            } elseif ( $type == "to_seller" ) {



                                $seller_unread_count = $chat_history_data->seller_unread_count;

                                if ($is_user_online == '1') {
                                    //if user is online, no need to send noti and no need to add unread count

                                    $chat_data = array(

                                        "item_id" => $this->post('item_id'),
                                        "buyer_user_id" => $this->post('buyer_user_id'),
                                        "seller_user_id" => $this->post('seller_user_id'),
                                        "seller_unread_count" => $seller_unread_count,
                                        "added_date" => date("Y-m-d H:i:s"),
                                        "nego_price" => $this->post('nego_price'),
                                        "is_accept"	 => 1
                                        //"offer_status" => 3

                                    );
                                } else {
                                    //if user is offline, send noti and add unread count

                                    $chat_data = array(

                                        "item_id" => $this->post('item_id'),
                                        "buyer_user_id" => $this->post('buyer_user_id'),
                                        "seller_user_id" => $this->post('seller_user_id'),
                                        "seller_unread_count" => (int)$seller_unread_count + 1,
                                        "added_date" => date("Y-m-d H:i:s"),
                                        "nego_price" => $this->post('nego_price'),
                                        "is_accept"	 => 1
                                        //"offer_status" => 3

                                    );

                                    //prepare data for noti
                                    $user_ids[] = $this->post('seller_user_id');


                                    $devices = $this->Noti->get_all_device_in($user_ids)->result();
                                    //print_r($devices);die;

                                    $device_ids = array();
                                    if ( count( $devices ) > 0 ) {
                                        foreach ( $devices as $device ) {
                                            $device_ids[] = $device->device_token;
                                        }
                                    }

									$platform_names = array();
									if ( count( $devices ) > 0 ) {
										foreach ( $devices as $platform ) {
											$platform_names[] = $platform->platform_name;
										}
									}

                                    $user_id = $this->post('buyer_user_id');
                                    $user_name = $this->User->get_one($user_id)->user_name;
                                    
	        		                $user_profile_photo = $this->User->get_one($user_id)->user_profile_photo;

                                    $data['message'] = get_msg('offer_accepted');
                                    $data['buyer_user_id'] = $this->post('buyer_user_id');
                                    $data['seller_user_id'] = $this->post('seller_user_id');
                                    $data['sender_name'] = $user_name;
                                    $data['item_id'] = $this->post('item_id');
                                    $data['flag'] = 'chat';
									$data['chat_flag'] = 'CHAT_FROM_SELLER';

                                    $status = send_android_fcm( $device_ids, $data, $platform_names );	
                                }



                            }
                        }

                        if( !$this->Chat->Save( $chat_data,$chat_history_data->id )) {

                            $this->error_response( get_msg( 'err_accept_update' ), 500);


                        } else {

                            $obj = $this->Chat->get_one_by($chat_data);
                            $this->ps_adapter->convert_chathistory( $obj );
                            $this->custom_response( $obj );

                        }
                    }
                }

            }
        }

    }

    /**
     * Update Price
     */
    function item_sold_out_post()
    {
        // validation rules for chat history
        $rules = array(
            array(
                'field' => 'item_id',
                'rules' => 'required|callback_id_check[Item]'
            ),
            array(
                'field' => 'buyer_user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'seller_user_id',
                'rules' => 'required|callback_id_check[User]'
            )
        );




        // exit if there is an error in validation,
        if ( !$this->is_valid( $rules )) exit;

        //chat data already user bought checking

        $bought_data = array(

            "item_id" => $this->post('item_id'),
            "buyer_user_id" => $this->post('buyer_user_id'),
            "seller_user_id" => $this->post('seller_user_id')

        );

        $user_bought_data = $this->User_bought->get_one_by($bought_data);

		$chat_history_data = $this->Chat->get_one_by($bought_data);

        if($chat_history_data->offer_status == 4){
            $this->error_response( get_msg( 'already_reject_offer' ));
        }else{
            if( $chat_history_data->offer_status == '3') {
                        
                if ($user_bought_data->is_empty_object == 1) {
                    $this->error_response( get_msg( 'cannot_sold_out' ), 503);
                } else {
                    $item_id = $this->post('item_id');
                    $buyer_user_id = $this->post('buyer_user_id');
                    $seller_user_id = $this->post('seller_user_id');
                    $item_sold_out = array(

                        "is_sold_out" => 1,

                    );

                    $this->Item->save($item_sold_out,$item_id);
                    $conds['item_id'] = $item_id;
                    $conds['buyer_user_id'] = $buyer_user_id;
                    $conds['seller_user_id'] = $seller_user_id;

                    $obj = $this->Chat->get_one_by($conds);

                    $this->ps_adapter->convert_chathistory( $obj );
                    $this->custom_response($obj);
                }
            }
        }
        
    }


    /**
     * Reset is_accept
     */
    function reset_accept_post()
    {
        // validation rules for chat history
        $rules = array(
            array(
                'field' => 'item_id',
                'rules' => 'required|callback_id_check[Item]'
            ),
            array(
                'field' => 'buyer_user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'seller_user_id',
                'rules' => 'required|callback_id_check[User]'
            )
        );


        // exit if there is an error in validation,
        if ( !$this->is_valid( $rules )) exit;

        $chat_data = array(

            "item_id" => $this->post('item_id'),
            "buyer_user_id" => $this->post('buyer_user_id'),
            "seller_user_id" => $this->post('seller_user_id')

        );

        $chat_history_data = $this->Chat->get_one_by($chat_data);


        if($chat_history_data->id == "") {

            $this->error_response( get_msg( 'err_chat_history_not_exist' ), 500);


        } else {

            $chat_data = array(

                "item_id" => $this->post('item_id'),
                "buyer_user_id" => $this->post('buyer_user_id'),
                "seller_user_id" => $this->post('seller_user_id'),
                "is_accept" => 0

            );

            if( !$this->Chat->Save( $chat_data,$chat_history_data->id )) {

                $this->error_response( get_msg( 'err_accept_update' ), 500);


            } else {

                $this->success_response( get_msg( 'accept_reset_success' ), 200);


            }


        }


    }

    /**
     * Delete All Chat History
     */
    function delete_chat_history_post()
    {

        // delete categories and images
        if ( !$this->Chat->delete_all()) {

            // set error message
            $this->error_response( get_msg( 'error_delete_chat_history' ), 500);
            // rollback


        }

        $this->success_response( get_msg( 'success_delete_chat_history' ), 200);

    }

    /**
     * Reset Soldout
     */

    function reset_sold_out_post()
    {
        // validation rules for chat history
        $rules = array(
            array(
                'field' => 'item_id',
                'rules' => 'required|callback_id_check[Item]'
            ),
            array(
                'field' => 'buyer_user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'seller_user_id',
                'rules' => 'required|callback_id_check[User]'
            )
        );


        // exit if there is an error in validation,
        if ( !$this->is_valid( $rules )) exit;

        $chat_data = array(

            "item_id" => $this->post('item_id'),
            "buyer_user_id" => $this->post('buyer_user_id'),
            "seller_user_id" => $this->post('seller_user_id')

        );

        $chat_history_data = $this->Chat->get_one_by($chat_data);


        if($chat_history_data->id == "") {

            $this->error_response( get_msg( 'err_chat_history_not_exist' ), 500);


        } else {

            $chat_data = array(

                "item_id" => $this->post('item_id'),
                "buyer_user_id" => $this->post('buyer_user_id'),
                "seller_user_id" => $this->post('seller_user_id'),
                "is_accept" => 0

            );

            if( !$this->Chat->Save( $chat_data,$chat_history_data->id )) {

                $this->error_response( get_msg( 'err_accept_update' ), 500);


            } else {

                $item_data = array(
                    "is_sold_out" => 0
                );

                if( !$this->Item->Save( $item_data, $this->post('item_id') )) {

                    $this->error_response( get_msg( 'err_soldout_reset' ), 500);

                } else {

                    $this->success_response( get_msg( 'soldout_reset_success' ), 200);

                }


            }


        }

    }


    /**
     * get chat history
     */

    function get_chat_history_post()
    {
        // validation rules for chat history
        $rules = array(
            
            array(
                'field' => 'buyer_user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'seller_user_id',
                'rules' => 'required|callback_id_check[User]'
            )
        );


        if(!empty($this->post('item_id'))){
            $item_id = $this->post('item_id');
        }else{
            $item_id = '';
        }


        // exit if there is an error in validation,
        if ( !$this->is_valid( $rules )) exit;

        $chat_data = array(

            "item_id" => $item_id,
            "buyer_user_id" => $this->post('buyer_user_id'),
            "seller_user_id" => $this->post('seller_user_id')

        );

        $obj = $this->Chat->get_one_by($chat_data);

        $this->ps_adapter->convert_chathistory( $obj );
        $this->custom_response( $obj );

    }

    /**
     * Offer list Api
     */

    function offer_list_post()
    {
        // validation rules for chat history
        // $rules = array(
        //        array(
        //        	'field' => 'seller_user_id',
        //        	'rules' => 'required'
        //        )
        //       );


        // // exit if there is an error in validation,
        //       if ( !$this->is_valid( $rules )) exit;
        //       $chat_data = array(

        //       	"seller_user_id" => $this->post('seller_user_id')

        //       );
        //       $chats = $this->Chat->get_all_by($chat_data);
        //       foreach ($chats->result() as $ch) {
        //       	$nego_price = $ch->nego_price;
        //       	$is_accept = $ch->is_accept;
        //       	if ($nego_price != 0 && $is_accept != 0) {
        //       		$result .= $ch->id .",";
        //        }
        //       }
        //       $id_from_his = rtrim($result,",");
        // $result_id = explode(",", $id_from_his);
        // $obj = $this->Chat->get_multi_info($result_id)->result();

        // $this->ps_adapter->convert_chathistory( $obj );
        // $this->custom_response( $obj );

        // add flag for default query
        $this->is_get = true;

        // get the post data
        $user_id = $this->post('user_id');
        $return_type 	 = $this->post('return_type');

        $users = global_user_check($user_id);

        $limit = $this->get( 'limit' );
        $offset = $this->get( 'offset' );



        // get limit & offset

        if ( $return_type == "buyer") {

            //pph modified @ 22 June 2019

            /* For User Block */
            if($this->App_setting->get_one('app1')->is_block_user == "1"){
                //user block check with user_id
                $conds_login_block['from_block_user_id'] = $user_id;
                $login_block_count = $this->Block->count_all_by($conds_login_block);

                // user blocked existed by user id
                if ($login_block_count > 0) {
                    // get the blocked user by user id
                    $to_block_user_datas = $this->Block->get_all_by($conds_login_block)->result();

                    foreach ( $to_block_user_datas as $to_block_user_data ) {

                        $to_block_user_id .= "'" .$to_block_user_data->to_block_user_id . "',";

                    }

                    // get block user's chat list

                    $result_users = rtrim($to_block_user_id,',');
                    $conds_user['buyer_user_id'] = $result_users;

                    $chat_users = $this->Chat->get_all_in_chat_buyer( $conds_user )->result();


                    foreach ( $chat_users as $chat_user ) {

                        $chat_ids .= $chat_user->id .",";

                    }

                    // get all chat id without block user's list

                    $results = rtrim($chat_ids,',');
                    $chat_id = explode(",", $results);
                    $conds['chat_id'] = $chat_id;


                }
            }

            $conds['seller_user_id'] = $user_id;
            $conds['nego_price'] = '0' ;



            if ( !empty( $limit ) && !empty( $offset )) {
                // if limit & offset is not empty

                $chats = $this->Chat->get_all_chat($conds,$limit, $offset)->result();
            } else if ( !empty( $limit )) {
                // if limit is not empty


                $chats = $this->Chat->get_all_chat($conds, $limit )->result();
            } else {
                // if both are empty

                $chats = $this->Chat->get_all_chat($conds)->result();
            }
            //print_r($chats);die;
            if (!empty($chats)) {
                foreach ( $chats as $chat ) {

                    $id .= "'" .$chat->id . "',";

                }
            }

            if ($id == "") {
                $this->error_response(get_msg( 'record_not_found'), 404);
            } else {

                $result = rtrim($id,',');
                $conds['$id'] = $result;

                $obj = $this->Chat->get_all_in_chat($conds)->result();
                $this->ps_adapter->convert_chathistory( $obj );
                $this->custom_response( $obj );

            }



        } else if ( $return_type == "seller") {

            //$conds['seller_user_id'] = $user_id;
            //pph modified @ 22 June 2019

            /* For User Block */
            if($this->App_setting->get_one('app1')->is_block_user == "1"){
                //user block check with user_id
                $conds_login_block['from_block_user_id'] = $user_id;
                $login_block_count = $this->Block->count_all_by($conds_login_block);

                // user blocked existed by user id
                if ($login_block_count > 0) {
                    // get the blocked user by user id
                    $to_block_user_datas = $this->Block->get_all_by($conds_login_block)->result();

                    foreach ( $to_block_user_datas as $to_block_user_data ) {

                        $to_block_user_id .= "'" .$to_block_user_data->to_block_user_id . "',";

                    }

                    // get block user's chat list

                    $result_users = rtrim($to_block_user_id,',');
                    $conds_user['seller_user_id'] = $result_users;

                    $chat_users = $this->Chat->get_all_in_chat_seller( $conds_user )->result();


                    foreach ( $chat_users as $chat_user ) {

                        $chat_ids .= $chat_user->id .",";

                    }

                    // get all chat id without block user's list

                    $results = rtrim($chat_ids,',');
                    $chat_id = explode(",", $results);
                    $conds['chat_id'] = $chat_id;

                }
            }

            /* For Item Report */

            //item report check with login_user_id
            $conds_report['reported_user_id'] = $user_id;
            $reported_data_count = $this->Itemreport->count_all_by($conds_report);

            // item reported existed by login user
            if ($reported_data_count > 0) {
                // get the reported item data
                $item_reported_datas = $this->Itemreport->get_all_by($conds_report)->result();

                foreach ( $item_reported_datas as $item_reported_data ) {

                    $item_ids .= "'" .$item_reported_data->item_id . "',";

                }

                // get block user's item

                $result_reports = rtrim($item_ids,',');
                $conds_item['item_id'] = $result_reports;

                $item_reports = $this->Chat->get_all_in_chat_item( $conds_item )->result();

                foreach ( $item_reports as $item_report ) {

                    $ids .= $item_report->id .",";

                }

                // get all item without block user's item

                $result_items = rtrim($ids,',');
                $reported_item_id = explode(",", $result_items);
                $conds['item_id'] = $reported_item_id;
            }

            $conds['buyer_user_id'] = $user_id;
            $conds['nego_price'] = '0' ;

            //print_r($conds);die;

            if ( !empty( $limit ) && !empty( $offset )) {
                // if limit & offset is not empty

                $chats = $this->Chat->get_all_chat($conds,$limit, $offset)->result();
            } else if ( !empty( $limit )) {
                // if limit is not empty


                $chats = $this->Chat->get_all_chat($conds, $limit )->result();
            } else {
                // if both are empty

                $chats = $this->Chat->get_all_chat($conds)->result();
            }

            if (!empty($chats)) {
                foreach ( $chats as $chat ) {

                    $id .= "'" .$chat->id . "',";

                }
            }

            if ($id == "") {
                $this->error_response(get_msg( 'record_not_found'), 404);
            } else {

                $result = rtrim($id,',');
                $conds['$id'] = $result;

                $obj = $this->Chat->get_all_in_chat($conds)->result();
                $this->ps_adapter->convert_chathistory( $obj );
                $this->custom_response( $obj );

            }

        }


    }

    /**
    is user bought
     */
    function is_user_bought_post()
    {
        // validation rules for chat history
        $rules = array(
            array(
                'field' => 'item_id',
                'rules' => 'required|callback_id_check[Item]'
            ),
            array(
                'field' => 'buyer_user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'seller_user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'is_user_online',
                'rules' => 'required'
            )

        );

        $item_id = $this->post('item_id');
        $seller_user_id = $this->post('seller_user_id');
        $buyer_user_id = $this->post('buyer_user_id');
        $is_user_online = $this->post('is_user_online');

        // exit if there is an error in validation,
        if ( !$this->is_valid( $rules )) exit;

        /** save bought data */

        $bought_data = array(

            "item_id" => $this->post('item_id'),
            "buyer_user_id" => $this->post('buyer_user_id'),
            "seller_user_id" => $this->post('seller_user_id'),
            "added_date" => date("Y-m-d H:i:s"),

        );

        /** update accept offer status */

        $conds_chat['buyer_user_id'] = $this->post('buyer_user_id');
        $conds_chat['seller_user_id'] = $this->post('seller_user_id');
        $conds_chat['item_id'] = $this->post('item_id');

        //print_r($conds_chat);
        $chat_history_data = $this->Chat->get_one_by($conds_chat);
		if($chat_history_data->offer_status == 4){
            $this->error_response( get_msg( 'already_reject_offer' ), 503);
        }else{
            if( $chat_history_data->offer_status == '2' &&  $chat_history_data->is_accept == '1') {
				

                $id = $this->Chat->get_one_by($conds_chat)->id;
                $buyer_unread_count = $this->Chat->get_one_by($conds_chat)->buyer_unread_count;

                if ($is_user_online == '1') {
                    //if user is online, no need to send noti and no need to add unread count

                    $chat_data = array(
                        "offer_status" => 3

                    );
                } else {
                    //if user is offline, send noti and add noti count

                    /** send noti to buyer */

                    $user_name = $this->User->get_one($seller_user_id)->user_name;
                    $user_profile_photo = $this->User->get_one($seller_user_id)->user_profile_photo;

                    $message = get_msg('you_bought') . ' ' . $item_name ;
                    $data['message'] = $message;
                    $data['buyer_user_id'] = $buyer_user_id;
                    $data['seller_user_id'] = $seller_user_id;
                    $data['item_id'] = $item_id;
                    $data['sender_name'] = $user_name;
                    $data['sender_profle_photo'] = $user_profile_photo;
                    $data['flag'] = 'chat';
					$data['chat_flag'] = 'CHAT_FROM_SELLER';

                    $devices = $this->Noti->get_all_device_in($buyer_user_id)->result();

                    $device_ids = array();
                    if ( count( $devices ) > 0 ) {
                        foreach ( $devices as $device ) {
                            $device_ids[] = $device->device_token;
                        }
                    }

                    $platform_names = array();
					if ( count( $devices ) > 0 ) {
						foreach ( $devices as $platform ) {
							$platform_names[] = $platform->platform_name;
						}
					}

                    $status = send_android_fcm( $device_ids, $data, $platform_names );

                    //add buyer unread count

                    $chat_data = array(
                        "offer_status" => 3,
                        "buyer_unread_count" => (int)$buyer_unread_count + 1

                    );
                }



                $this->Chat->save($chat_data,$id);

                //save user bought data
                $this->User_bought->save($bought_data);

                $obj = $this->Chat->get_one($id);
                $this->ps_adapter->convert_chathistory( $obj );
                $this->custom_response( $obj );
            }
        }

    }
}