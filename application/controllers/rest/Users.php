<?php
require_once(APPPATH . 'libraries/REST_Controller.php');
require_once __DIR__ . '/../../../vendor/autoload.php';

/**
 * REST API for Users
 */
class Users extends API_Controller
{

    /**
     * Constructs Parent Constructor
     */
    function __construct()
    {
        parent::__construct('User');
    }

    /**
     * Default Query for API
     * @return [type] [description]
     */
    function default_conds()
    {
        $conds = array();

        if ($this->is_search) {
            if ($this->post('user_name') != "") {
                $conds['user_name']   = $this->post('user_name');
            }

            if ($this->post('keyword') != "") {
                $conds['keyword']   = trim($this->post('keyword'));
            }

            if ($this->post('overall_rating') != "") {
                $conds['overall_rating']   = $this->post('overall_rating');
            }

            if ($this->post('return_types') != "") {
                $conds['return_types']   = $this->post('return_types');
            }

            if ($this->get('login_user_id')) {
                $conds['from_block_user_id']   = $this->get('login_user_id');
            }

            $conds['order_by'] = 1;
            $conds['order_by_field']    = $this->post('order_by');
            $conds['order_by_type']     = $this->post('order_type');
        }

        return $conds;
    }


    /**
     * Convert Object
     */
    function convert_object(&$obj)
    {
        // call parent convert object
        parent::convert_object($obj);

        // convert customize category object
        $this->ps_adapter->convert_user($obj);
    }

    /**
     * Users Registration
     */
    function add_post()
    {
        // validation rules for user register
        $rules = array(
            array(
                'field' => 'user_name',
                'rules' => 'required'
            ),
            array(
                'field' => 'user_email',
                'rules' => 'required|valid_email|callback_email_check'
            ),
            array(
                'field' => 'user_password',
                'rules' => 'required'
            )

        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;
        $email_verified_enable = $this->Backend_config->get_one('be1')->email_verification_enabled;

        $code = generate_random_string(5);
        $added_date =  date("Y-m-d H:i:s");

        do {
            $unique_code = generate_random_string(5);
            $this->db->where('referral_code', $unique_code);
            $count = $this->db->count_all_results('core_users');
        } while ($count > 0);

        if ($email_verified_enable != 1) {

            $user_data = array(

                "user_name" => $this->post('user_name'),
                "user_email" => $this->post('user_email'),
                'user_password' => md5($this->post('user_password')),
                "device_token" => $this->post('device_token'),
                "code" =>  "",
                "email_verify" => 1,
                "status" => 1, //Need to verified status
                "referral_code" => $unique_code,
                "added_date" =>  $added_date,
                "added_date_timestamp" => strtotime($added_date)

            );
        } else {
            $user_data = array(

                "user_name" => $this->post('user_name'),
                "user_email" => $this->post('user_email'),
                'user_password' => md5($this->post('user_password')),
                "device_token" => $this->post('device_token'),
                "code" =>  $code,
                "email_verify" => 1,
                "status" => 2, //Need to verified status
                "referral_code" => $unique_code,
                "added_date" =>  $added_date,
                "added_date_timestamp" => strtotime($added_date)

            );
            $conds['status'] = 2;
        }

        $conds['user_email'] = $user_data['user_email'];

        $user_infos = $this->User->user_exists($conds)->result();

        if (empty($user_infos)) {

            if (!$this->User->save($user_data)) {

                $this->error_response(get_msg('err_user_register'), 500);
            } else {

                $noti_token = array(
                    "device_token" => $this->post('device_token')
                );

                $noti_count = $this->Noti->count_all_by($noti_token);
                //we need to nitify admin
                send_one_signal_notification('test');

                if ($noti_count == 1) {
                    if ($this->Noti->exists($noti_token)) {
                        $noti_id = $this->Noti->get_one_by($noti_token);
                        $push_noti_token_id = $noti_id->push_noti_token_id;
                        $noti_data = array(

                            "user_id" => $user_data['user_id']

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    } else {
                        $noti_data = array(

                            "user_id" => $user_data['user_id'],
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                } else {
                    $this->Noti->delete_by($noti_token);
                    $noti_data = array(

                        "user_id" => $user_data['user_id'],
                        "device_token" => $this->post('device_token')

                    );
                    $this->Noti->save($noti_data, $push_noti_token_id);
                }
                $hash_value = $this->post('hash_value');
                if (isset($hash_value) && !empty($hash_value)) {
                    $ciphertext = base64_decode($hash_value);
                    $secret_key = 'SLHChGUuN0';
                    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
                    $iv = substr($ciphertext, 0, $ivlen);
                    $hmac = substr($ciphertext, $ivlen, $sha2len = 32);
                    $ciphertext_raw = substr($ciphertext, $ivlen + $sha2len);
                    $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $secret_key, $options = OPENSSL_RAW_DATA, $iv);
                    $calcmac = hash_hmac('sha256', $ciphertext_raw, $secret_key, $as_binary = true);
                    if (hash_equals($hmac, $calcmac)) {
                        $user_item_arr = explode('||', $plaintext);
                        $userid = $user_item_arr[0];
                        $itemid = $user_item_arr[1];
                    }
                    $this->load->database();
                    $user_id =   $userid;
                    $selected_followers = $user_data['user_id'];
                    $event_id = $itemid;
                    $id = 'event_' . uniqid();

                    $data = array(
                        'id' => $id,
                        'user_id' => $user_id,
                        'follower_id' => $selected_followers,
                        'item_id' => $event_id,
                        'status' => 'pending'
                    );
                    $this->db->insert('bs_follower_invite', $data);
                }



                $subject = get_msg('user_acc_reg_label');

                if ($email_verified_enable != 1) {
                    if (!send_user_register_email_without_verify($user_data['user_id'], $subject)) {

                        $this->error_response(get_msg('user_register_success_but_email_not_send'), 503);
                    }
                } else {

                    if (!send_user_register_email($user_data['user_id'], $subject)) {

                        $this->error_response(get_msg('user_register_success_but_email_not_send'), 503);
                    }
                }
            }
        } else {

            //$this->error_response( get_msg( 'need_to_verify' ));
            $user_id = $user_infos[0]->user_id;
            $subject = get_msg('user_acc_reg_label');

            if ($email_verified_enable != 1) {
                if (!send_user_register_email_without_verify($user_id, $subject)) {

                    $this->error_response(get_msg('user_register_success_but_email_not_send'), 503);
                }
            } else {
                if (!send_user_register_email($user_id, $subject)) {

                    $this->error_response(get_msg('user_register_success_but_email_not_send'), 503);
                }
            }
            create_one_signal_player($user_id);
            $this->custom_response($this->User->get_one($user_id));
        }

        create_one_signal_player($user_data['user_id']);
        $this->custom_response($this->User->get_one($user_data["user_id"]));
    }


    /**
     * Email Checking
     *
     * @param      <type>  $email     The identifier
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    function email_check($email)
    {
        if ($this->User->exists(array('user_email' => $email, 'status' => 1))) {

            $this->form_validation->set_message('email_check', 'Email Exist');
            return false;
        }

        return true;
    }

    /**
     * Users Login
     */
    function login_post()
    {
        // validation rules for user register
        $rules = array(

            array(
                'field' => 'user_email',
                'rules' => 'required|valid_email'
            ),
            array(
                'field' => 'user_password',
                'rules' => 'required'
            )
        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        if ($this->User->exists(array('user_email' => $this->post('user_email'), 'user_password' => $this->post('user_password'), 'device_token' => $this->post('device_token')))) {

            //if ( $this->User->exists( array( 'user_email' => $this->post( 'user_email' ), 'user_password' => $this->post( 'user_password' )))) {

            $email = $this->post('user_email');
            $conds['user_email'] = $email;
            $is_banned = $this->User->get_one_by($conds)->is_banned;

            if ($is_banned == '1') {
                $this->error_response(get_msg('err_user_banned'), 500);
            } else {
                $user = $this->User->get_one_by(array("user_email" => $this->post('user_email')));

                $noti_token = array(
                    "device_token" => $this->post('device_token')
                );

                $noti_count = $this->Noti->count_all_by($noti_token);

                if ($noti_count == 1) {
                    if ($this->Noti->exists($noti_token)) {
                        $noti_id = $this->Noti->get_one_by($noti_token);
                        $push_noti_token_id = $noti_id->push_noti_token_id;
                        $noti_data = array(

                            "user_id" => $user->user_id

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    } else {
                        $noti_data = array(

                            "user_id" => $user->user_id,
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                } else {
                    $this->Noti->delete_by($noti_token);
                    $noti_data = array(

                        "user_id" => $user->user_id,
                        "device_token" => $this->post('device_token')

                    );
                    $this->Noti->save($noti_data, $push_noti_token_id);
                }

                create_one_signal_player($user->user_id);

                $this->custom_response($user);
            }
        } else {

            $this->error_response(get_msg('err_user_not_exist'), 404);
        }
    }


    function swap_profile_get()
    {
        $user_id = $this->get('user_id');

        if (!$user_id) {
            $this->custom_response(get_msg('user_id_required'));
        }
        // $flag="red";
        // $today_count=0;
        $user_data = array('date' => date('Y-m-d'), 'user_id' => $user_id);
        $check = $this->db->get_where('core_users_swap_profile', $user_data)->row();

        // if($check->num_rows()>0){
        //     $old_count=$check->row_array()['swap_count'];
        //     $today_count=$old_count+1;

        //     $this->db->where($user_data);
        //     $status=$this->db->update('core_users_swap_profile',array('swap_count'=>$today_count));
        //     if($status){
        //         $flag="green";
        //     }
        // }else{
        //     $today_count=1;
        //     $user_data['swap_count']=1;
        //     $status=$this->db->insert('core_users_swap_profile',$user_data);
        //     if($status){
        //         $flag="green";
        //     }
        // }

        // if($flag=="green"){
        $this->response(array('status' => 'success', 'message' => 'get today\'s count', 'today_count' => (int)$check->swap_count));
        // }
    }

    function swap_profile_post()
    {
        $user_id = $this->get('user_id');

        if (!$user_id) {
            $this->custom_response(get_msg('user_id_required'));
        }
        $flag = "red";
        $today_count = 0;
        $user_data = array('date' => date('Y-m-d'), 'user_id' => $user_id);
        $check = $this->db->get_where('core_users_swap_profile', $user_data);
        if ($check->num_rows() > 0) {
            $old_count = $check->row_array()['swap_count'];
            $today_count = $old_count + 1;

            $this->db->where($user_data);
            $status = $this->db->update('core_users_swap_profile', array('swap_count' => $today_count));
            if ($status) {
                $flag = "green";
            }
        } else {
            $today_count = 1;
            $user_data['swap_count'] = 1;
            $status = $this->db->insert('core_users_swap_profile', $user_data);
            if ($status) {
                $flag = "green";
            }
        }

        if ($flag == "green") {
            $this->response(array('status' => 'success', 'message' => 'count added', 'today_count' => $today_count));
        }
    }

    // reset spinn count
    function reset_spinn_count_get()
    {
        $user_id = $this->get('user_id');

        if (!$user_id) {
            $this->custom_response(get_msg('user_id_required'));
        }
        $flag = "red";
        $today_count = 0;
        $user_data = array('date' => date('Y-m-d'), 'user_id' => $user_id);
        $check = $this->db->get_where('core_users_swap_profile', $user_data);
        $this->db->where($user_data);
        $status = $this->db->update('core_users_swap_profile', array('swap_count' => $today_count));
        if ($status) {
            $flag = "green";
        }
        //    print_r($this->db->last_query());die;
        if ($flag == "green") {
            $this->response(array('status' => 'success', 'message' => 'count reset successfully', 'today_count' => $today_count));
        }
    }

    /**
     * User Reset Password
     */
    function reset_post()
    {
        // validation rules for user register
        $rules = array(
            array(
                'field' => 'user_email',
                'rules' => 'required|valid_email'
            )
        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        $user_info = $this->User->get_one_by(array("user_email" => $this->post('user_email')));

        if (isset($user_info->is_empty_object)) {
            // if user info is empty,

            $this->error_response(get_msg('err_user_not_exist'), 404);
        }

        // generate code
        $code = md5(time() . 'teamps');

        // insert to reset
        $data = array(
            'user_id' => $user_info->user_id,
            'code' => $code
        );

        if (!$this->ResetCode->save($data)) {
            // if error in inserting,

            $this->error_response(get_msg('err_model'), 500);
        }

        // Send email with reset code
        $to = $user_info->user_email;
        $subject = get_msg('pwd_reset_label');
        $hi = get_msg('hi_label');
        $sender_name = $this->Backend_config->get_one('be1')->sender_name;
        $msg = "<p>" . $hi . "," . $user_info->user_name . "</p>" .
            "<p>" . get_msg('pwd_reset_link') . "<br/>" .
            "<a href='" . site_url($this->config->item('reset_url') . '/' . $code) . "'>" . get_msg('reset_link_label') . "</a></p>" .
            "<p>" . get_msg('best_regards_label') . ",<br/>" . $sender_name . "</p>";
        // send email from admin
        if (!$this->ps_mail->send_from_admin($to, $subject, $msg)) {

            $this->error_response(get_msg('err_email_not_send'), 500);
        }

        $this->success_response(get_msg('success_email_sent'), 200);
    }

    /**
     * User Profile Update
     */

    function profile_update_post()
    {

        // validation rules for user register
        $rules = array(
            array(
                'field' => 'user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'user_name',
                'rules' => 'required'
            ),
            // array(
            //     'field' => 'user_email',
            //     'rules' => 'required|valid_email'
            // ),
            array(
                'field' => 'user_phone',
                'rules' => 'required'
            ),

            // array(
            //     'field' => 'user_about_me',
            //     'rules' => 'required'
            // ),

            array(
                'field' => 'is_show_email',
                'rules' => 'required'
            ),

            array(
                'field' => 'is_show_phone',
                'rules' => 'required'
            ),
            array(
                'field' => 'lat',
                'rules' => 'required'
            ),
            array(
                'field' => 'lng',
                'rules' => 'required'
            ),


        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        $user_id = $this->post('user_id');

        // user email checking
        $user_email = $this->User->get_one($user_id)->user_email;
        if ($user_email == $this->post('user_email')) {
            $email = $this->post('user_email');
        } else {
            $conds['user_email'] = $this->post('user_email');
            $conds['status'] = 1;
            $user_infos = $this->User->get_one_user_email($conds)->result();
            if (empty($user_infos)) {
                $email = $this->post('user_email');
            } else {

                $this->error_response(get_msg('err_user_email_exist'), 404);
            }
        }

        // user phone checking
        $user_phone = $this->User->get_one($user_id)->user_phone;
        if ($user_phone == $this->post('user_phone')) {
            $phone = $this->post('user_phone');
        } else {
            $conds['user_phone'] = $this->post('user_phone');
            $conds['status'] = 1;
            $user_infos = $this->User->get_one_user_phone($conds)->result();
            if (empty($user_infos)) {
                $phone = $this->post('user_phone');
            } else {

                $this->error_response(get_msg('err_user_phone_exist'), 404);
            }
        }


        $user_data = array(
            "user_name"     => $this->post('user_name'),
            "user_email"    => $this->post('user_email'),
            "user_phone"    => $this->post('user_phone'),
            "user_address"  => $this->post('user_address'),
            "city"            => $this->post('city'),
            "user_about_me" => $this->post('user_about_me'),
            "device_token" => $this->post('device_token'),
            "is_show_email" => $this->post('is_show_email'),
            "is_show_phone" => $this->post('is_show_phone'),
            "lat" => $this->post('lat'),
            "lng" => $this->post('lng'),
            "instagram_url" => $this->post('instagram_url'),
            "tiktok_url" => $this->post('tiktok_url'),
            "web_url" => $this->post('web_url'),
            "web_name" => $this->post('web_name'),
        );
        // print_r($user_data);die;

        if (!$this->User->save($user_data, $this->post('user_id'))) {

            $this->error_response(get_msg('err_user_update'), 500);
        }

        //$this->success_response( get_msg( 'success_profile_update' ));
        $this->custom_response($this->User->get_one($user_id));
    }


    function user_info_update_post()
    {
        // validation rules for user info update
        $rules = array(
            array(
                'field' => 'user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'user_name',
                'rules' => 'required'
            ),
            array(
                'field' => 'gender',
                'rules' => 'required'
            ),
            array(
                'field' => 'here_for',
                'rules' => 'required'
            ),
            array(
                'field' => 'interest',
                'rules' => 'required'
            )
        );

        // exit if there is an error in validation
        if (!$this->is_valid($rules)) exit;

        $user_id = $this->post('user_id');

        $user_data = array(
            "user_name"     => $this->post('user_name'),
            "gender" => $this->post('gender'),
            "here_for" => $this->post('here_for'),
            "interests" => $this->post('interest'),
            "interested_gender" => $this->post('interested_gender'),
            "talk_about" => $this->post('talk_about')
        );

        if (!$this->User->save($user_data, $this->post('user_id'))) {
            $this->error_response(get_msg('err_user_update'), 500);
        }

        $this->custom_response($this->User->get_one($user_id));
    }

    /**
     * User Profile Update
     */
    function password_update_put()
    {

        // validation rules for user register
        $rules = array(
            array(
                'field' => 'user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'user_password',
                'rules' => 'required'
            )
        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        $user_data = array(
            "user_password"     => md5($this->put('user_password'))
        );

        if (!$this->User->save($user_data, $this->put('user_id'))) {
            $this->error_response(get_msg('err_user_password_update'), 500);
        }

        $this->success_response(get_msg('success_profile_update'), 201);
    }


    /**
     * User Verified Code
     */
    function verify_post()
    {

        // validation rules for user register
        $rules = array(
            array(
                'field' => 'user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'code',
                'rules' => 'required'
            )
        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        $user_verify_data = array(
            "code"     => $this->post('code'),
            "user_id"  => $this->post('user_id'),
            "status"   => 2
        );


        $user_id = $this->User->get_one_by($user_verify_data)->user_id;

        if ($user_id  == $this->post('user_id')) {
            $user_data = array(
                "code"    => " ",
                "status"  => 1
            );
            $this->User->save($user_data, $user_id);
            $this->custom_response($this->User->get_one($user_id));
        } else {

            $this->error_response(get_msg('invalid_code'), 400);
        }
    }

    /**
     * Users Request Code
     */
    function request_code_post()
    {
        // validation rules for user register
        $rules = array(
            array(
                'field' => 'user_email',
                'rules' => 'required'
            )

        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        if (!$this->User->exists(array('user_email' => $this->post('user_email'), 'status' => 2))) {

            $this->error_response(get_msg('err_user_not_exist'), 404);
        } else {
            $email = $this->post('user_email');
            $conds['user_email'] = $email;
            $conds['status'] = 2;

            $user_id = $this->User->get_one_by($conds)->user_id;

            $code = $this->User->get_one_by($conds)->code;

            if ($code == " ") {

                $resend_code = generate_random_string(5);
                $user_data_code = array(
                    "code"    => $resend_code
                );
                $this->User->save($user_data_code, $user_id);
            }


            $user_data['user_id'] = $user_id;

            $subject = get_msg('verify_code_sent');

            if (!send_user_register_email($user_data['user_id'], $subject)) {

                $this->error_response(get_msg('user_register_success_but_email_not_send'), 503);
            }

            $this->success_response(get_msg('success_email_sent'), 200);
        }
    }


    /**
     * Users Registration with Facebook
     */
    function facebook_register_post()
    {
        $rules = array(
            array(
                'field' => 'facebook_id',
                'rules' => 'required'
            )
        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        $app_token = $this->Backend_config->get_one('be1')->app_token;

        //Need to check facebook_id is aleady exist or not?
        if (!$this->User->exists(array('facebook_id' => $this->post('facebook_id')))) {

            $rules = array(
                array(
                    'field' => 'user_name',
                    'rules' => 'required'
                )
            );

            //User not yet exist
            $fb_id = $this->post('profile_img_id');
            $url = "https://graph.facebook.com/$fb_id/picture?width=350&height=500&access_token=" . $app_token;
            $data = file_get_contents($url);
            $added_date = date("Y-m-d H:i:s");

            // for uploads

            $dir = "uploads/";
            $img = md5(time()) . '.jpg';
            $ch = curl_init($url);
            $fp = fopen('uploads/' . $img, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);

            //for thumbnail

            $dir = "uploads/thumbnail/";
            $ch = curl_init($url);
            $fp = fopen('uploads/thumbnail/' . $img, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);

            ////

            $user_data = array(
                "user_name"     => $this->post('user_name'),
                'user_email'    => $this->post('user_email'),
                "facebook_id"     => $this->post('facebook_id'),
                "user_profile_photo" => $img,
                "device_token" => $this->post('device_token'),
                "role_id"    => 4,
                "facebook_verify" => 1,
                "status"     => 1,
                "code"    => ' ',
                "added_date" =>  $added_date,
                "added_date_timestamp" => strtotime($added_date)
            );


            $user_email = $user_data['user_email'];
            //print_r($user_email);die;

            //if (!empty($user_email)) {
            //email exists
            if ($user_data['user_email'] != "") {
                $cond_user_existed['user_email'] = $user_data['user_email'];
                $cond_user_existed['phone_id'] = "";
                $user_infos = $this->User->get_email_phone($cond_user_existed)->result();
                $user_id = $user_infos[0]->user_id;
            }

            //}

            if ($user_id != "") {
                //user email alerady exist

                //for user name and user email
                $user_name = $this->post('user_name');
                $user_email = $this->post('user_email');

                if ($user_name == "" && $user_email == "") {
                    $user_data = array(
                        "user_name" => $user_infos[0]->user_name,
                        "user_email" => $user_infos[0]->user_email,
                        "device_token"  => $user_data['device_token'],
                        "facebook_id"     => $user_data['facebook_id'],
                        "facebook_verify" => $user_data['facebook_verify'],
                        'user_profile_photo' => $user_data['user_profile_photo'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                } else if ($user_name == "") {
                    $user_data = array(
                        "user_name" => $user_infos[0]->user_name,
                        "user_email"    => $user_email,
                        "device_token"  => $user_data['device_token'],
                        "facebook_id"     => $user_data['facebook_id'],
                        "facebook_verify" => $user_data['facebook_verify'],
                        'user_profile_photo' => $user_data['user_profile_photo'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                } else if ($user_email == "") {
                    $user_data = array(
                        "user_name"    => $user_name,
                        "user_email" => $user_infos[0]->user_email,
                        "device_token"  => $user_data['device_token'],
                        "facebook_id"     => $user_data['facebook_id'],
                        "facebook_verify" => $user_data['facebook_verify'],
                        'user_profile_photo' => $user_data['user_profile_photo'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                } else {
                    $user_data = array(
                        "user_name"    => $user_name,
                        "user_email"    => $user_email,
                        "device_token"  => $user_data['device_token'],
                        "facebook_id"     => $user_data['facebook_id'],
                        "facebook_verify" => $user_data['facebook_verify'],
                        'user_profile_photo' => $user_data['user_profile_photo'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                }

                $this->User->save($user_data, $user_id);

                $noti_token = array(
                    "device_token" => $this->post('device_token')
                );

                $noti_count = $this->Noti->count_all_by($noti_token);

                if ($noti_count == 1) {
                    if ($this->Noti->exists($noti_token)) {
                        $noti_id = $this->Noti->get_one_by($noti_token);
                        $push_noti_token_id = $noti_id->push_noti_token_id;
                        $noti_data = array(

                            "user_id" => $user_id

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    } else {
                        $noti_data = array(

                            "user_id" => $user_id,
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                } else {
                    $this->Noti->delete_by($noti_token);
                    $noti_data = array(

                        "user_id" => $user_id,
                        "device_token" => $this->post('device_token')

                    );
                    $this->Noti->save($noti_data, $push_noti_token_id);
                }
            } else {
                //user email not exist
                if (!$this->User->save($user_data)) {
                    $this->error_response(get_msg('err_user_register'), 500);
                }

                $noti_token = array(
                    "device_token" => $this->post('device_token')
                );

                $noti_count = $this->Noti->count_all_by($noti_token);

                if ($noti_count == 1) {
                    if ($this->Noti->exists($noti_token)) {
                        $noti_id = $this->Noti->get_one_by($noti_token);
                        $push_noti_token_id = $noti_id->push_noti_token_id;
                        $noti_data = array(

                            "user_id" => $user_data['user_id']

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    } else {
                        $noti_data = array(

                            "user_id" => $user_data['user_id'],
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                } else {
                    $this->Noti->delete_by($noti_token);
                    $noti_data = array(

                        "user_id" => $user_data['user_id'],
                        "device_token" => $this->post('device_token')

                    );
                    $this->Noti->save($noti_data, $push_noti_token_id);
                }


                $this->custom_response($this->User->get_one($user_data['user_id']));
            }

            $this->custom_response($this->User->get_one($user_infos[0]->user_id));
        } else {

            //User already exist in DB
            $conds['facebook_id'] = $this->post('facebook_id');
            $user_social_info_override = $this->Backend_config->get_one('be1')->user_social_info_override;


            if ($user_social_info_override == '1') {

                $conds1['facebook_id'] = $this->post('facebook_id');
                $user_profile_photo = $this->User->get_one_by($conds['facebook_id'])->user_profile_photo;

                //Delete existing image
                @unlink('./uploads/' . $user_profile_photo);
                @unlink('./uploads/thumbnail/' . $user_profile_photo);
                //Download again
                $fb_id = $this->post('profile_img_id');
                $url = "https://graph.facebook.com/$fb_id/picture?width=350&height=500&access_token=" . $app_token;
                $data = file_get_contents($url);

                // for uploads

                $dir = "uploads/";
                $img = md5(time()) . '.jpg';
                $ch = curl_init($url);
                $fp = fopen('uploads/' . $img, 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);

                // for thumbnail

                $dir = "uploads/thumbnail/";
                $ch = curl_init($url);
                $fp = fopen('uploads/thumbnail/' . $img, 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);

                $user_data = array(
                    'user_name'        => $this->post('user_name'),
                    'user_email'    => $this->post('user_email'),
                    'user_profile_photo' => $img,
                    'device_token'  => $this->post('device_token')
                );

                //for user name and user email
                $user_name = $this->post('user_name');
                $user_email = $this->post('user_email');

                if ($user_name == "" && $user_email == "") {
                    $user_data = array(
                        'device_token'  => $this->post('device_token'),
                        'user_profile_photo' => $user_data['user_profile_photo'],
                    );
                } else if ($user_name == "") {
                    $user_data = array(
                        'user_email'    => $user_data['user_email'],
                        'device_token'  => $user_data['device_token'],
                        'user_profile_photo' => $user_data['user_profile_photo'],
                    );
                } else if ($user_email == "") {
                    $user_data = array(
                        'user_name'    => $user_data['user_name'],
                        'device_token'  => $user_data['device_token'],
                        'user_profile_photo' => $user_data['user_profile_photo'],
                    );
                } else {
                    $user_data = array(
                        'user_name'    => $user_data['user_name'],
                        'user_email'    => $user_data['user_email'],
                        'device_token'  => $user_data['device_token'],
                        'user_profile_photo' => $user_data['user_profile_photo'],
                    );
                }


                $users_data = $this->User->get_one_by($conds1);
                $user_id = $users_data->user_id;

                $conds['facebook_id'] = $this->post('facebook_id');
                $user_datas = $this->User->get_one_by($conds);
                $user_id = $user_datas->user_id;

                if ($user_datas->is_banned == 1) {

                    $this->error_response(get_msg('err_user_banned'), 500);
                } else {
                    if (!$this->User->save($user_data, $user_id)) {
                        $this->error_response(get_msg('err_user_register'), 500);
                    }

                    $noti_token = array(
                        "device_token" => $this->post('device_token')
                    );

                    $noti_count = $this->Noti->count_all_by($noti_token);

                    if ($noti_count == 1) {
                        if ($this->Noti->exists($noti_token)) {
                            $noti_id = $this->Noti->get_one_by($noti_token);
                            $push_noti_token_id = $noti_id->push_noti_token_id;
                            $noti_data = array(

                                "user_id" => $user_id

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        } else {
                            $noti_data = array(

                                "user_id" => $user_id,
                                "device_token" => $this->post('device_token')

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        }
                    } else {
                        $this->Noti->delete_by($noti_token);
                        $noti_data = array(

                            "user_id" => $user_id,
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                }
            } else {
                $user_datas = $this->User->get_one_by($conds);
                $user_id = $user_datas->user_id;

                if ($user_datas->is_banned == 1) {

                    $this->error_response(get_msg('err_user_banned'), 500);
                } else {
                    if (!$this->User->save($user_datas, $user_id)) {
                        $this->error_response(get_msg('err_user_register'), 500);
                    }

                    $noti_token = array(
                        "device_token" => $this->post('device_token')
                    );

                    $noti_count = $this->Noti->count_all_by($noti_token);

                    if ($noti_count == 1) {
                        if ($this->Noti->exists($noti_token)) {
                            $noti_id = $this->Noti->get_one_by($noti_token);
                            $push_noti_token_id = $noti_id->push_noti_token_id;
                            $noti_data = array(

                                "user_id" => $user_id

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        } else {
                            $noti_data = array(

                                "user_id" => $user_id,
                                "device_token" => $this->post('device_token')

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        }
                    } else {
                        $this->Noti->delete_by($noti_token);
                        $noti_data = array(

                            "user_id" => $user_id,
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                }
            }

            $this->custom_response($this->User->get_one($user_datas->user_id));
        }
    }

    /**
     * User Profile Update
     */
    function password_update_post()
    {

        // validation rules for user register
        $rules = array(
            array(
                'field' => 'user_id',
                'rules' => 'required|callback_id_check[User]'
            ),
            array(
                'field' => 'user_password',
                'rules' => 'required'
            )
        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        $user_data = array(
            "user_password"     => md5($this->post('user_password'))
        );

        if (!$this->User->save($user_data, $this->post('user_id'))) {
            $this->error_response(get_msg('err_user_password_update'), 500);
        }

        $this->success_response(get_msg('success_profile_update'), 201);
    }

    /**
     * Trigger to delete user related data when user is deleted
     * delete user related data
     */

    function user_delete_post()
    {

        // validation rules for user register
        $rules = array(
            array(
                'field' => 'user_id',
                'rules' => 'required|callback_id_check[User]'
            )
        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        $id = $this->post('user_id');

        $conds['user_id'] = $id;

        // check user id

        $user_data = $this->User->get_one_by($conds);

        //print_r($user_data);die;


        if ($user_data->user_id == "") {

            $this->error_response(get_msg('invalid_user_id'), 400);
        } elseif ($user_data->user_is_sys_admin == 1) {
            $this->error_response(get_msg('user_not_able_delete'), 500);
        } else {
            $conds_user['user_id'] = $id;
            $conds_from_user['from_user_id'] = $id;
            $conds_to_user['to_user_id'] = $id;
            $conds_added_user['added_user_id'] = $id;
            $conds_followed_user['followed_user_id'] = $id;
            $conds_buyer_user['buyer_user_id'] = $id;
            $conds_seller_user['seller_user_id'] = $id;

            //delete User
            if (!$this->User->delete_by($conds_user)) {

                return false;
            }

            // delete Rating
            $this->Rate->delete_by($conds_from_user);
            $this->Rate->delete_by($conds_to_user);

            // delete push notification users
            if (!$this->Notireaduser->delete_by($conds_user)) {

                return false;
            }

            // delete push notification tokens
            if (!$this->Noti->delete_by($conds_user)) {

                return false;
            }

            // delete items and others related table

            $items_data     = $this->Item->get_one_by($conds_added_user);

            $item_data['item_id'] = $items_data->id;
            $img_data['img_parent_id'] = $items_data->id;

            $this->Chat->delete_by($item_data);
            $this->Paid_item->delete_by($item_data);
            $this->Favourite->delete_by($item_data);
            $this->Itemreport->delete_by($item_data);
            $this->Touch->delete_by($item_data);
            $this->Image->delete_by($img_data);

            if (!$this->Item->delete_by($conds_added_user)) {

                return false;
            }

            //delete follows

            $following_user        = $this->Userfollow->get_all_by($conds_user)->result();
            $follower_user        = $this->Userfollow->get_all_by($conds_followed_user)->result();

            foreach ($following_user as $following) {

                $conds_follower['user_id'] = $following->followed_user_id;

                $follower_user_data = $this->User->get_one_by($conds_follower);

                $follower_count = $follower_user_data->follower_count;

                $user_data = array(
                    "follower_count" => $follower_count - 1
                );

                $this->User->save($user_data, $follower_user_data->user_id);
            }


            foreach ($follower_user as $follower) {

                $conds_follow['user_id'] = $follower->user_id;

                $following_user_data = $this->User->get_one_by($conds_follow);

                $following_count = $following_user_data->following_count;

                $user_data = array(
                    "following_count" => $following_count - 1
                );

                $this->User->save($user_data, $following_user_data->user_id);
            }

            $this->Userfollow->delete_by($conds_user);
            $this->Userfollow->delete_by($conds_followed_user);


            // delete Favourite
            if (!$this->Favourite->delete_by($conds_user)) {

                return false;
            }

            // delete Chat History

            $this->Chat->delete_by($conds_buyer_user);
            $this->Chat->delete_by($conds_seller_user);

            $this->success_response(get_msg('success_delete'), 200);
        }
    }


    /**
     * Return User Unread Count For Chat Notification and Blog
     */

    function unread_count_post()
    {

        $rules = array(
            array(
                'field' => 'user_id',
                'rules' => 'required'
            )
        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        $blog_noti_unread_count = 0;

        //get all noti_id by user_id and device_token from bs_push_notification_users table

        $conds['user_id']        = $this->post('user_id');
        // $conds['device_token'] = $this->post('device_token');

        $blog_notis = $this->Notireaduser->get_all_by($conds)->result();



        foreach ($blog_notis as $blog_noti) {

            $id .= "'" . $blog_noti->noti_id . "',";
        }



        if ($id == "") {

            $obj = $this->Noti_message->get_all()->result();
            $blog_noti_unread_count = count($obj);
        } else {

            $result = rtrim($id, ',');

            $conds_blog_noti['$id'] = $result;



            $obj = $this->Noti_message->get_all_not_in_noti($conds_blog_noti)->result();



            $blog_noti_unread_count = count($obj);
        }


        //For buyer_unread_count

        $buyer_unread_count = 0;

        $conds_chat['buyer_user_id'] = $this->post('user_id');

        $chats_buyer_unread_records = $this->Chat->get_all_by($conds_chat)->result();


        foreach ($chats_buyer_unread_records as $chats_buyer_unread_record) {

            $buyer_unread_count += $chats_buyer_unread_record->buyer_unread_count;
        }

        //For seller_unread_count

        $seller_unread_count = 0;

        $conds_chat_seller['seller_user_id'] = $this->post('user_id');


        $chats_seller_unread_records = $this->Chat->get_all_by($conds_chat_seller)->result();

        foreach ($chats_seller_unread_records as $chats_seller_unread_record) {

            $seller_unread_count += $chats_seller_unread_record->seller_unread_count;
        }


        $count_object = new stdClass;
        $count_object->blog_noti_unread_count  = $blog_noti_unread_count;
        $count_object->buyer_unread_count      = $buyer_unread_count;
        $count_object->seller_unread_count     = $seller_unread_count;


        $final_data = $this->ps_security->clean_output($count_object);


        $this->response($final_data);
    }

    /**
     * Users Registration with Google
     */
    function google_register_post()
    {
        $rules = array(
            array(
                'field' => 'google_id',
                'rules' => 'required'
            )
        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        //Need to check google_id is aleady exist or not?
        if (!$this->User->exists(
            array(
                'google_id' => $this->post('google_id')
            )
        )) {

            $rules = array(
                array(
                    'field' => 'user_name',
                    'rules' => 'required'
                )
            );

            //User not yet exist
            $gg_id = $this->post('google_id');
            $url = $this->post('profile_photo_url');
            $added_date =  date("Y-m-d H:i:s");

            if ($url != "") {

                //for uploads

                $data = file_get_contents($url);
                $dir = "uploads/";
                $img = md5(time()) . '.jpg';
                $ch = curl_init($url);
                $fp = fopen('uploads/' . $img, 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);

                // for thumbnail

                $dir = "uploads/thumbnail/";
                $ch = curl_init($url);
                $fp = fopen('uploads/thumbnail/' . $img, 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);

                $user_data = array(
                    "user_name"     => $this->post('user_name'),
                    'user_email'    => $this->post('user_email'),
                    "google_id"     => $this->post('google_id'),
                    "user_profile_photo" => $img,
                    "device_token" => $this->post('device_token'),
                    "role_id" => 4,
                    "google_verify" => 1,
                    "status"     => 1,
                    "code"   => ' ',
                    "added_date" => $added_date,
                    "added_date_timestamp" => strtotime($added_date)
                );
            } else {

                $user_data = array(
                    "user_name"     => $this->post('user_name'),
                    'user_email'    => $this->post('user_email'),
                    "google_id"     => $this->post('google_id'),
                    "device_token" => $this->post('device_token'),
                    "role_id" => 4,
                    "google_verify" => 1,
                    "status"     => 1,
                    "code"   => ' ',
                    "added_date" => $added_date,
                    "added_date_timestamp" => strtotime($added_date)
                );
            }

            if ($user_data['user_email'] != "") {
                $cond_user_existed['user_email'] = $user_data['user_email'];
                $cond_user_existed['phone_id'] = "";
                $user_infos = $this->User->get_email_phone($cond_user_existed)->result();
                $user_id = $user_infos[0]->user_id;
            }

            if ($user_id != "") {
                //user email alerady exist

                //for user name and user email
                $user_name = $this->post('user_name');
                $user_email = $this->post('user_email');

                if ($user_name == "" && $user_email == "") {
                    $user_data = array(
                        "user_name" => $user_infos[0]->user_name,
                        "user_email" => $user_infos[0]->user_email,
                        "device_token"  => $user_data['device_token'],
                        "google_id"     => $user_data['google_id'],
                        "google_verify" => $user_data['google_verify'],
                        'user_profile_photo' => $user_data['user_profile_photo'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                } else if ($user_name == "") {
                    $user_data = array(
                        "user_name" => $user_infos[0]->user_name,
                        "user_email"    => $user_email,
                        "device_token"  => $user_data['device_token'],
                        "google_id"     => $user_data['google_id'],
                        "google_verify" => $user_data['google_verify'],
                        'user_profile_photo' => $user_data['user_profile_photo'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                } else if ($user_email == "") {
                    $user_data = array(
                        "user_name"    => $user_name,
                        "user_email" => $user_infos[0]->user_email,
                        "device_token"  => $user_data['device_token'],
                        "google_id"     => $user_data['google_id'],
                        "google_verify" => $user_data['google_verify'],
                        'user_profile_photo' => $user_data['user_profile_photo'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                } else {
                    $user_data = array(
                        "user_name"    => $user_name,
                        "user_email"    => $user_email,
                        "device_token"  => $user_data['device_token'],
                        "google_id"     => $user_data['google_id'],
                        "google_verify" => $user_data['google_verify'],
                        'user_profile_photo' => $user_data['user_profile_photo'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                }

                $this->User->save($user_data, $user_id);

                $noti_token = array(
                    "device_token" => $this->post('device_token')
                );

                $noti_count = $this->Noti->count_all_by($noti_token);

                if ($noti_count == 1) {
                    if ($this->Noti->exists($noti_token)) {
                        $noti_id = $this->Noti->get_one_by($noti_token);
                        $push_noti_token_id = $noti_id->push_noti_token_id;
                        $noti_data = array(

                            "user_id" => $user->user_id

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    } else {
                        $noti_data = array(

                            "user_id" => $user->user_id,
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                } else {
                    $this->Noti->delete_by($noti_token);
                    $noti_data = array(

                        "user_id" => $user->user_id,
                        "device_token" => $this->post('device_token')

                    );
                    $this->Noti->save($noti_data, $push_noti_token_id);
                }
            } else {
                //user email not exist
                if (!$this->User->save($user_data)) {
                    $this->error_response(get_msg('err_user_register'), 500);
                }

                $noti_token = array(
                    "device_token" => $this->post('device_token')
                );

                $noti_count = $this->Noti->count_all_by($noti_token);

                if ($noti_count == 1) {
                    if ($this->Noti->exists($noti_token)) {
                        $noti_id = $this->Noti->get_one_by($noti_token);
                        $push_noti_token_id = $noti_id->push_noti_token_id;
                        $noti_data = array(

                            "user_id" => $user_data['user_id']

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    } else {
                        $noti_data = array(

                            "user_id" => $user_data['user_id'],
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                } else {
                    $this->Noti->delete_by($noti_token);
                    $noti_data = array(

                        "user_id" => $user_data['user_id'],
                        "device_token" => $this->post('device_token')

                    );
                    $this->Noti->save($noti_data, $push_noti_token_id);
                }

                $this->custom_response($this->User->get_one($user_data['user_id']));
            }

            $this->custom_response($this->User->get_one($user_infos[0]->user_id));
        } else {

            //User already exist in DB
            $conds['google_id'] = $this->post('google_id');
            $user_social_info_override = $this->Backend_config->get_one('be1')->user_social_info_override;

            if ($user_social_info_override == '1') {
                $user_profile_photo = $this->User->get_one_by($conds)->user_profile_photo;

                //Delete existing image
                @unlink('./uploads/' . $user_profile_photo);
                @unlink('./uploads/thumbnail/' . $user_profile_photo);
                //Download again
                $gg_id = $this->post('google_id');
                $url = $this->post('profile_photo_url');

                if ($url != "") {
                    $data = file_get_contents($url);

                    // for uploads

                    $dir = "uploads/";
                    $img = md5(time()) . '.jpg';
                    $ch = curl_init($url);
                    $fp = fopen('uploads/' . $img, 'wb');
                    curl_setopt($ch, CURLOPT_FILE, $fp);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_exec($ch);
                    curl_close($ch);
                    fclose($fp);

                    // for thumbnail

                    $dir = "uploads/thumbnail/";
                    $ch = curl_init($url);
                    $fp = fopen('uploads/thumbnail/' . $img, 'wb');
                    curl_setopt($ch, CURLOPT_FILE, $fp);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_exec($ch);
                    curl_close($ch);
                    fclose($fp);

                    $user_data = array(
                        'user_name'        => $this->post('user_name'),
                        'user_email'    => $this->post('user_email'),
                        'device_token'  => $this->post('device_token'),
                        'user_profile_photo' => $img
                    );
                } else {

                    $user_data = array(
                        'user_name'        => $this->post('user_name'),
                        'user_email'    => $this->post('user_email'),
                        'device_token'  => $this->post('device_token')
                    );
                }

                //for user name and user email
                $user_name = $this->post('user_name');
                $user_email = $this->post('user_email');

                if ($user_name == "" && $user_email == "") {
                    $user_data = array(
                        'device_token'  => $this->post('device_token'),
                        'user_profile_photo' => $user_data['user_profile_photo'],
                    );
                } else if ($user_name == "") {
                    $user_data = array(
                        'user_email'    => $user_data['user_email'],
                        'device_token'  => $user_data['device_token'],
                        'user_profile_photo' => $user_data['user_profile_photo'],
                    );
                } else if ($user_email == "") {
                    $user_data = array(
                        'user_name'    => $user_data['user_name'],
                        'device_token'  => $user_data['device_token'],
                        'user_profile_photo' => $user_data['user_profile_photo'],
                    );
                } else {
                    $user_data = array(
                        'user_name'    => $user_data['user_name'],
                        'user_email'    => $user_data['user_email'],
                        'device_token'  => $user_data['device_token'],
                        'user_profile_photo' => $user_data['user_profile_photo'],
                    );
                }

                $conds['google_id'] = $this->post('google_id');
                $user_datas = $this->User->get_one_by($conds);
                $user_id = $user_datas->user_id;
                //print_r($user_id);die;

                if ($user_datas->is_banned == 1) {

                    $this->error_response(get_msg('err_user_banned'), 500);
                } else {
                    if (!$this->User->save($user_data, $user_id)) {
                        $this->error_response(get_msg('err_user_register'), 500);
                    }

                    $noti_token = array(
                        "device_token" => $this->post('device_token')
                    );

                    $noti_count = $this->Noti->count_all_by($noti_token);

                    if ($noti_count == 1) {
                        if ($this->Noti->exists($noti_token)) {
                            $noti_id = $this->Noti->get_one_by($noti_token);
                            $push_noti_token_id = $noti_id->push_noti_token_id;
                            $noti_data = array(

                                "user_id" => $user_id

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        } else {
                            $noti_data = array(

                                "user_id" => $user_id,
                                "device_token" => $this->post('device_token')

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        }
                    } else {
                        $this->Noti->delete_by($noti_token);
                        $noti_data = array(

                            "user_id" => $user_id,
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                }
            } else {
                $user_datas = $this->User->get_one_by($conds);
                $user_id = $user_datas->user_id;
                //print_r($user_id);die;

                if ($user_datas->is_banned == 1) {

                    $this->error_response(get_msg('err_user_banned'), 500);
                } else {
                    if (!$this->User->save($user_datas, $user_id)) {
                        $this->error_response(get_msg('err_user_register'), 500);
                    }

                    $noti_token = array(
                        "device_token" => $this->post('device_token')
                    );

                    $noti_count = $this->Noti->count_all_by($noti_token);

                    if ($noti_count == 1) {
                        if ($this->Noti->exists($noti_token)) {
                            $noti_id = $this->Noti->get_one_by($noti_token);
                            $push_noti_token_id = $noti_id->push_noti_token_id;
                            $noti_data = array(

                                "user_id" => $user_id

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        } else {
                            $noti_data = array(

                                "user_id" => $user_id,
                                "device_token" => $this->post('device_token')

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        }
                    } else {
                        $this->Noti->delete_by($noti_token);
                        $noti_data = array(

                            "user_id" => $user_id,
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                }
            }



            $this->custom_response($this->User->get_one($user_datas->user_id));
        }
    }

    /**
     * Users Registration with Phone
     */
    function phone_register_post()
    {
        $rules = array(
            array(
                'field' => 'phone_id',
                'rules' => 'required'
            )
        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        //Need to check phone_id is aleady exist or not?
        if (!$this->User->exists(
            //new
            array(
                'phone_id' => $this->post('phone_id')
            )
        )) {


            $rules = array(
                array(
                    'field' => 'user_name',
                    'rules' => 'required'
                )
            );
            if (!$this->is_valid($rules)) exit;

            do {
                $unique_code = generate_random_string(5);
                $this->db->where('referral_code', $unique_code);
                $count = $this->db->count_all_results('core_users');
            } while ($count > 0);

            $added_date = date("Y-m-d H:i:s");

            $user_data = array(
                "user_name"     => $this->post('user_name'),
                "user_phone"    => $this->post('user_phone'),
                "phone_id"        => $this->post('phone_id'),
                "device_token" => $this->post('device_token'),
                "role_id" => 4,
                "phone_verify" => 1,
                "status" => 1,
                "referral_code" => $unique_code,
                "added_date" =>  $added_date,
                "added_date_timestamp" => strtotime($added_date)
            );


            $conds_phone['user_phone'] = $user_data['user_phone'];
            $user_infos = $this->User->get_one_user_phone($conds_phone)->result();
            $user_id = $user_infos[0]->user_id;


            if ($user_id != "") {
                //user phone alerady exist

                //for user name and user email
                $user_name = $this->post('user_name');
                $user_phone = $this->post('user_phone');

                if ($user_name == "" && $user_phone == "") {
                    $user_data = array(
                        "user_name" => $user_infos[0]->user_name,
                        "user_phone" => $user_infos[0]->user_phone,
                        "device_token"  => $user_data['device_token'],
                        "phone_id"     => $user_data['phone_id'],
                        "phone_verify" => $user_data['phone_verify'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                } else if ($user_name == "") {
                    $user_data = array(
                        "user_name" => $user_infos[0]->user_name,
                        "user_phone"    => $user_phone,
                        "device_token"  => $user_data['device_token'],
                        "phone_id"     => $user_data['phone_id'],
                        "phone_verify" => $user_data['phone_verify'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                } else if ($user_phone == "") {
                    $user_data = array(
                        "user_name"    => $user_name,
                        "user_phone" => $user_infos[0]->user_phone,
                        "device_token"  => $user_data['device_token'],
                        "phone_id"     => $user_data['phone_id'],
                        "phone_verify" => $user_data['phone_verify'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                } else {
                    $user_data = array(
                        "user_name"    => $user_name,
                        "user_phone"    => $user_phone,
                        "device_token"  => $user_data['device_token'],
                        "phone_id"     => $user_data['phone_id'],
                        "phone_verify" => $user_data['phone_verify'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                }

                $this->User->save($user_data, $user_id);

                create_one_signal_player($user_id);


                $noti_token = array(
                    "device_token" => $this->post('device_token')
                );

                $noti_count = $this->Noti->count_all_by($noti_token);

                if ($noti_count == 1) {
                    if ($this->Noti->exists($noti_token)) {
                        $noti_id = $this->Noti->get_one_by($noti_token);
                        $push_noti_token_id = $noti_id->push_noti_token_id;
                        $noti_data = array(

                            "user_id" => $user_id

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    } else {
                        $noti_data = array(

                            "user_id" => $user_id,
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                } else {
                    $this->Noti->delete_by($noti_token);
                    $noti_data = array(

                        "user_id" => $user_id,
                        "device_token" => $this->post('device_token')

                    );
                    $this->Noti->save($noti_data, $push_noti_token_id);
                }
            } else {
                //user phone not exist
                if (!$this->User->save($user_data)) {
                    $this->error_response(get_msg('err_user_register'), 500);
                }

                $noti_token = array(
                    "device_token" => $this->post('device_token')
                );

                $noti_count = $this->Noti->count_all_by($noti_token);

                if ($noti_count == 1) {
                    if ($this->Noti->exists($noti_token)) {
                        $noti_id = $this->Noti->get_one_by($noti_token);
                        $push_noti_token_id = $noti_id->push_noti_token_id;
                        $noti_data = array(

                            "user_id" => $user_data['user_id']

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    } else {
                        $noti_data = array(

                            "user_id" => $user_data['user_id'],
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                } else {
                    $this->Noti->delete_by($noti_token);
                    $noti_data = array(

                        "user_id" => $user_data['user_id'],
                        "device_token" => $this->post('device_token')

                    );
                    $this->Noti->save($noti_data, $push_noti_token_id);
                }

              create_one_signal_player($user_data['user_id']);

                $this->custom_response($this->User->get_one($user_data['user_id']));
            }
            create_one_signal_player($user_data['user_id']);

            $this->custom_response($this->User->get_one($user_infos[0]->user_id));
        } else {

            $conds['phone_id'] = $this->post('phone_id');
            $user_social_info_override = $this->Backend_config->get_one('be1')->user_social_info_override;


            if ($user_social_info_override == '1') {

                //update
                //User already exist in DB
                $user_data = array(
                    "user_name"        => $this->post('user_name'),
                    "user_phone"    => $this->post('user_phone'),
                    "device_token" => $this->post('device_token')
                );

                //for user name and user email
                $user_name = $this->post('user_name');
                $user_phone = $this->post('user_phone');

                if ($user_name == "" && $user_phone == "") {
                    $user_data = array(
                        'device_token'  => $this->post('device_token'),
                    );
                } else if ($user_name == "") {
                    $user_data = array(
                        'user_phone'    => $user_data['user_phone'],
                        'device_token'  => $user_data['device_token'],
                    );
                } else if ($user_phone == "") {
                    $user_data = array(
                        'user_name'    => $user_data['user_name'],
                        'device_token'  => $user_data['device_token'],
                    );
                } else {
                    $user_data = array(
                        'user_name'    => $user_data['user_name'],
                        'user_phone'    => $user_data['user_phone'],
                        'device_token'  => $user_data['device_token'],
                    );
                }

                $conds['phone_id'] = $this->post('phone_id');
                $user_datas = $this->User->get_one_by($conds);
                $user_id = $user_datas->user_id;

                if ($user_datas->is_banned == 1) {

                    $this->error_response(get_msg('err_user_banned'), 500);
                } else {
                    if (!$this->User->save($user_data, $user_id)) {
                        $this->error_response(get_msg('err_user_register'), 500);
                    }

                    $noti_token = array(
                        "device_token" => $this->post('device_token')
                    );

                    $noti_count = $this->Noti->count_all_by($noti_token);

                    if ($noti_count == 1) {
                        if ($this->Noti->exists($noti_token)) {
                            $noti_id = $this->Noti->get_one_by($noti_token);
                            $push_noti_token_id = $noti_id->push_noti_token_id;
                            $noti_data = array(

                                "user_id" => $user_id

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        } else {
                            $noti_data = array(

                                "user_id" => $user_id,
                                "device_token" => $this->post('device_token')

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        }
                    } else {
                        $this->Noti->delete_by($noti_token);
                        $noti_data = array(

                            "user_id" => $user_id,
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                }
            } else {
                $user_datas = $this->User->get_one_by($conds);
                $user_id = $user_datas->user_id;

                if ($user_datas->is_banned == 1) {

                    $this->error_response(get_msg('err_user_banned'), 500);
                } else {
                    if (!$this->User->save($user_datas, $user_id)) {
                        $this->error_response(get_msg('err_user_register'), 500);
                    }

                    $noti_token = array(
                        "device_token" => $this->post('device_token')
                    );

                    $noti_count = $this->Noti->count_all_by($noti_token);

                    if ($noti_count == 1) {
                        if ($this->Noti->exists($noti_token)) {
                            $noti_id = $this->Noti->get_one_by($noti_token);
                            $push_noti_token_id = $noti_id->push_noti_token_id;
                            $noti_data = array(

                                "user_id" => $user_id

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        } else {
                            $noti_data = array(

                                "user_id" => $user_id,
                                "device_token" => $this->post('device_token')

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        }
                    } else {
                        $this->Noti->delete_by($noti_token);
                        $noti_data = array(

                            "user_id" => $user_id,
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                }
            }

            create_one_signal_player($user_datas->user_id);
            
            $this->custom_response($this->User->get_one($user_datas->user_id));
        }
    }

    function invite_plan_post()
    {
        $rules = array(
            array(
                'field' => 'sender_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'receiver_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'event_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'message',
                'rules' => 'trim'
            )
        );

        // Exit if validation fails
        if (!$this->is_valid($rules)) exit;

        $sender_id   = $this->post('sender_id');
        $receiver_id = $this->post('receiver_id');
        $event_id    = $this->post('event_id');
        $message     = $this->post('message');

        // Validate sender
        $this->db->where('user_id', $sender_id);
        $sender = $this->db->get('core_users')->row();

        if (empty($sender)) {
            $this->error_response(get_msg('Sender is not valid'), 400);
        }

        // Validate receiver
        $this->db->where('user_id', $receiver_id);
        $receiver = $this->db->get('core_users')->row();
        if (empty($receiver)) {
            $this->error_response(get_msg('Receiver is not valid'), 400);
        }

        // Check if an invite already exists
        $this->db->where('sender_id', $sender_id);
        $this->db->where('receiver_id', $receiver_id);
        $this->db->where('event_id', $event_id);
        $existing_invite = $this->db->get('core_invite_plan')->row();

        if (!empty($existing_invite)) {
            $this->error_response(get_msg('Invite already sent'), 400);
        }

        // Insert invite
        $data = [
            'sender_id'   => $sender_id,
            'receiver_id' => $receiver_id,
            'event_id'    => $event_id,
            'message'     => $message,
            'status'      => 'pending',
            'timestamp'   => date('Y-m-d H:i:s')
        ];
        $this->db->insert('core_invite_plan', $data);

        // Prepare notification for receiver
        $user_ids = [$receiver_id];
        $devices = $this->Noti->get_all_device_in($user_ids)->result();

        $device_ids = [];
        $platform_names = [];
        if (!empty($devices)) {
            foreach ($devices as $device) {
                $device_ids[] = $device->device_token;
                $platform_names[] = $device->platform_name;
            }
        }

        // Get sender's name
        $user_name = $this->User->get_one($sender_id)->user_name;

        // Notification data
        $notification_data = [
            'message'       => $user_name . ' has invited you to an event.',
            'sender_id'     => $sender_id,
            'receiver_id'   => $receiver_id,
            'event_id'      => $event_id,
            'sender_name'   => $user_name,
            'flag'          => 'invite',
            'chat_flag'     => 'CHAT_FROM_BUYER'
        ];

        // Send push notification
        send_android_fcm($device_ids, $notification_data, $platform_names);

        $this->success_response(get_msg('Invite sent successfully'), 200);
    }

    function accept_invite_post()
    {
        $rules = array(
            array(
                'field' => 'user_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'invite_id',
                'rules' => 'required'
            )
        );

        // Exit if validation fails
        if (!$this->is_valid($rules)) exit;

        $user_id   = $this->post('user_id');
        $invite_id = $this->post('invite_id');

        // Validate invite
        $this->db->where('id', $invite_id);
        $invite = $this->db->get('core_invite_plan')->row();
        if (empty($invite)) {
            $this->error_response(get_msg('Invite not found'), 400);
        }

        // Ensure only the receiver can accept
        if ($invite->receiver_id != $user_id) {
            $this->error_response(get_msg('Unauthorized action'), 403);
        }

        // Check invite status
        if ($invite->status !== 'pending') {
            $this->error_response(get_msg('Invite already processed'), 400);
        }

        // Update invite status to accepted
        $this->db->where('id', $invite_id);
        $this->db->update('core_invite_plan', ['status' => 'accepted']);

        // Prepare notification for sender
        $sender_id = $invite->sender_id;
        $receiver_id = $invite->receiver_id;
        $event_id = $invite->event_id;

        // Get sender's device tokens
        $user_ids = [$sender_id];
        $devices = $this->Noti->get_all_device_in($user_ids)->result();

        $device_ids = [];
        $platform_names = [];
        if (!empty($devices)) {
            foreach ($devices as $device) {
                $device_ids[] = $device->device_token;
                $platform_names[] = $device->platform_name;
            }
        }

        // Get receiver's name
        $user_name = $this->User->get_one($receiver_id)->user_name;

        // Notification data
        $notification_data = [
            'message'       => $user_name . ' accepted your invite. You both are going to X.',
            'sender_id'     => $sender_id,
            'receiver_id'   => $receiver_id,
            'event_id'      => $event_id,
            'sender_name'   => $user_name,
            'flag'          => 'invite',
            'chat_flag'     => 'CHAT_FROM_BUYER'
        ];

        // Send push notification
        send_android_fcm($device_ids, $notification_data, $platform_names);

        $this->success_response(get_msg('Invite accepted successfully'), 200);
    }

    function invite_status_get()
    {
        $rules = array(
            array(
                'field' => 'sender_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'receiver_id',
                'rules' => 'required'
            ),
        );

        // Exit if validation fails
        if (!$this->is_valid($rules)) exit;

        $invitation = $this->db->where('sender_id', $this->get('sender_id'))->where('receiver_id', $this->get('receiver_id'))->get('core_invite_plan')->row();

        if (empty($invitation)) {
            $this->error_response(get_msg('Invite not found'), 400);
        }

        $data = [
            'invite_id' => $invitation->id,
            'status' => $invitation->status
        ];

        $this->response($data);
    }


    function refer_user_post()
    {
        $rules = array(
            array(
                'field' => 'user_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'referal_code',
                'rules' => 'required'
            )
        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        $user_id = $this->post('user_id');
        $referal_code = $this->post('referal_code');
        $this->db->where('referral_code', $referal_code);
        $old_user = $this->db->get('core_users')->row();

        $this->db->where('user_id', $user_id);
        $new_user = $this->db->get('core_users')->row();

        $this->db->where('referred_id', $user_id);
        $already_refered = $this->db->get('core_user_referrals')->row();

        if (!empty($already_refered)) {
            $this->error_response(get_msg('Already Refered'), 400);
        }

        if (empty($new_user)) {
            $this->error_response(get_msg('User Is Not Valid'), 400);
        }

        if (empty($old_user)) {
            $this->error_response(get_msg('Referral Code Is Not Valid'), 400);
        }

        if (!empty($old_user) && $old_user->referral_type == 'limited') {

            $this->db->where('referrer_id', $old_user->user_id);
            $this->db->where('user_referral_code', $referal_code);
            $referral_count = $this->db->count_all_results('core_user_referrals');
            if ($referral_count >= 2) {
                $this->error_response(get_msg('Referral limit reached'), 400);
            }
        }

        $data = [
            'referrer_id' => $old_user->user_id,
            'referred_id' => $user_id,
            'user_referral_code' => $referal_code,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('core_user_referrals', $data);

        $this->success_response(get_msg('successfully refered'), 200);
    }

    function referral_records_get()
    {

        $user_id = $this->get('login_user_id');


        $this->db->select('user_is_sys_admin');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('core_users');
        $result = $query->row();

        if ($result->user_is_sys_admin == 1) {

            $this->db->select('core_user_referrals.*, referrer.user_name as referrer_name, referred.user_name as referred_name');
            $this->db->from('core_user_referrals');
            $this->db->join('core_users as referrer', 'referrer.user_id = core_user_referrals.referrer_id', 'left');
            $this->db->join('core_users as referred', 'referred.user_id = core_user_referrals.referred_id', 'left');
            $query = $this->db->get();

            $referral_record = $query->result();
        } else {

            $this->db->select('core_user_referrals.*, referrer.user_name as referrer_name, referred.user_name as referred_name');
            $this->db->from('core_user_referrals');
            $this->db->join('core_users as referrer', 'referrer.user_id = core_user_referrals.referrer_id', 'left');
            $this->db->join('core_users as referred', 'referred.user_id = core_user_referrals.referred_id', 'left');
            $this->db->where('core_user_referrals.referrer_id', $user_id);
            $query = $this->db->get();

            $referral_record = $query->result();
        }

        $formatted_records = [];

        foreach ($referral_record as $record) {

            $joined_date = date('d M, Y', strtotime($record->created_at)); // Format the date

            $message = "{$record->referred_name} has joined on {$joined_date}, Referred by {$record->referrer_name}";
            $formatted_records[] = $message;
        }

        $this->response($formatted_records);
    }

    function add_age_post()
    {
        $rules = array(
            array(
                'field' => 'user_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'age',
                'rules' => 'required'
            )
        );

        
        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        $user_id = $this->post('user_id');
        $age = $this->post('age');

        $this->db->where('user_id', $user_id);
        $user = $this->db->get('core_users')->row();

        if (empty($user)) {
            $this->error_response(get_msg('User Not Found'), 400);
        }

        // Update user's age
        $this->db->where('user_id', $user_id);
        $this->db->update('core_users', ['age' => $age]);

        $this->success_response(get_msg('successfully added'), 200);
    }

    function add_review_post()
    {
        $rules = array(
            array(
                'field' => 'from_user_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'to_user_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'title',
                'rules' => 'required|trim'
            ),
            array(
                'field' => 'rating',
                'rules' => 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[5]'
            ),
            array(
                'field' => 'description',
                'rules' => 'required|trim'
            ),
            array(
                'field' => 'date_posted',
                'rules' => 'required'
            )
        );

        // Exit if validation fails
        if (!$this->is_valid($rules)) {
            return;
        }

        $from_user_id = $this->post('from_user_id');
        $to_user_id   = $this->post('to_user_id');
        $title        = $this->post('title');
        $rating       = $this->post('rating');
        $description  = $this->post('description');
        $date_posted  = $this->post('date_posted');

        // Ensure from_user_id and to_user_id are not the same
        if ($from_user_id == $to_user_id) {
            return $this->error_response(get_msg('You cannot review yourself'), 400);
        }

        // Ensure rating is between 1 and 5
        if ($rating < 1 || $rating > 5) {
            return $this->error_response(get_msg('Rating must be between 1 and 5'), 400);
        }

        // Check if from_user exists
        $this->db->where('user_id', $from_user_id);
        $from_user = $this->db->get('core_users')->row();

        if (empty($from_user)) {
            return $this->error_response(get_msg('From User Not Found'), 400);
        }

        // Check if to_user exists
        $this->db->where('user_id', $to_user_id);
        $to_user = $this->db->get('core_users')->row();

        if (empty($to_user)) {
            return $this->error_response(get_msg('To User Not Found'), 400);
        }

        // Check if a review already exists from this user to the same user
        $this->db->where('from_user_id', $from_user_id);
        $this->db->where('to_user_id', $to_user_id);
        $existing_review = $this->db->get('core_ratings')->row();

        if (!empty($existing_review)) {
            return $this->error_response(get_msg('You already added a review to this User'), 400);
        }

        // Insert review into the core_ratings table
        $data = [
            'from_user_id' => $from_user_id,
            'to_user_id'   => $to_user_id,
            'rating'       => $rating,
            'title'        => $title,
            'description'  => $description,
            'date_posted'  => $date_posted,
        ];

        $this->db->insert('core_ratings', $data);

        return $this->success_response(get_msg('Review successfully added'), 200);
    }


    /**
     * Users Registration with Apple
     */

    function apple_register_post()
    {
        $rules = array(
            array(
                'field' => 'apple_id',
                'rules' => 'required'
            )
        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        //Need to check apple_id is aleady exist or not?
        if (!$this->User->exists(
            array(
                'apple_id' => $this->post('apple_id')
            )
        )) {

            $rules = array(
                array(
                    'field' => 'user_name',
                    'rules' => 'required'
                )
            );

            $added_date = date("Y-m-d H:i:s");

            $user_data = array(
                "user_name"     => $this->post('user_name'),
                "user_email"    => $this->post('user_email'),
                "apple_id"     => $this->post('apple_id'),
                "device_token" => $this->post('device_token'),
                "role_id" => 4,
                "apple_verify" => 1,
                "status"     => 1,
                "code"   => ' ',
                "added_date" =>  $added_date,
                "added_date_timestamp" => strtotime($added_date)
            );

            if ($user_data['user_email'] != "") {
                $cond_user_existed['user_email'] = $user_data['user_email'];
                $cond_user_existed['phone_id'] = "";
                $user_infos = $this->User->get_email_phone($cond_user_existed)->result();
                $user_id = $user_infos[0]->user_id;
            }

            if ($user_id != "") {
                //user email alerady exist

                //for user name and user email
                $user_name = $this->post('user_name');
                $user_email = $this->post('user_email');

                if ($user_name == "" && $user_email == "") {
                    $user_data = array(
                        "user_name" => $user_infos[0]->user_name,
                        "user_email" => $user_infos[0]->user_email,
                        "device_token"  => $user_data['device_token'],
                        "apple_id"     => $user_data['apple_id'],
                        "apple_verify" => $user_data['apple_verify'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                } else if ($user_name == "") {
                    $user_data = array(
                        "user_name" => $user_infos[0]->user_name,
                        "user_email"    => $user_email,
                        "device_token"  => $user_data['device_token'],
                        "apple_id"     => $user_data['apple_id'],
                        "apple_verify" => $user_data['apple_verify'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                } else if ($user_email == "") {
                    $user_data = array(
                        "user_name"    => $user_name,
                        "user_email" => $user_infos[0]->user_email,
                        "device_token"  => $user_data['device_token'],
                        "apple_id"     => $user_data['apple_id'],
                        "apple_verify" => $user_data['apple_verify'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                } else {
                    $user_data = array(
                        "user_name"    => $user_name,
                        "user_email"    => $user_email,
                        "device_token"  => $user_data['device_token'],
                        "apple_id"     => $user_data['apple_id'],
                        "apple_verify" => $user_data['apple_verify'],
                        "role_id" => $user_data['role_id'],
                        "status"     => $user_data['status']
                    );
                }

                $this->User->save($user_data, $user_id);

                $noti_token = array(
                    "device_token" => $this->post('device_token')
                );

                $noti_count = $this->Noti->count_all_by($noti_token);

                if ($noti_count == 1) {
                    if ($this->Noti->exists($noti_token)) {
                        $noti_id = $this->Noti->get_one_by($noti_token);
                        $push_noti_token_id = $noti_id->push_noti_token_id;
                        $noti_data = array(

                            "user_id" => $user_id

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    } else {
                        $noti_data = array(

                            "user_id" => $user_id,
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                } else {
                    $this->Noti->delete_by($noti_token);
                    $noti_data = array(

                        "user_id" => $user_id,
                        "device_token" => $this->post('device_token')

                    );
                    $this->Noti->save($noti_data, $push_noti_token_id);
                }
            } else {
                //user email not exist
                if (!$this->User->save($user_data)) {
                    $this->error_response(get_msg('err_user_register'), 500);
                }

                $noti_token = array(
                    "device_token" => $this->post('device_token')
                );

                $noti_count = $this->Noti->count_all_by($noti_token);

                if ($noti_count == 1) {
                    if ($this->Noti->exists($noti_token)) {
                        $noti_id = $this->Noti->get_one_by($noti_token);
                        $push_noti_token_id = $noti_id->push_noti_token_id;
                        $noti_data = array(

                            "user_id" => $user_data['user_id']

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    } else {
                        $noti_data = array(

                            "user_id" => $user_data['user_id'],
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                } else {
                    $this->Noti->delete_by($noti_token);
                    $noti_data = array(

                        "user_id" => $user_data['user_id'],
                        "device_token" => $this->post('device_token')

                    );
                    $this->Noti->save($noti_data, $push_noti_token_id);
                }

                $this->custom_response($this->User->get_one($user_data['user_id']));
            }

            $this->custom_response($this->User->get_one($user_infos[0]->user_id));
        } else {

            $conds['apple_id'] = $this->post('apple_id');
            $user_social_info_override = $this->Backend_config->get_one('be1')->user_social_info_override;

            if ($user_social_info_override == '1') {

                //User already exist in DB
                $user_data = array(
                    'user_name'        => $this->post('user_name'),
                    'user_email'    => $this->post('user_email'),
                    'device_token'  => $this->post('device_token')
                );

                //for user name
                $user_name = $this->post('user_name');
                $user_email = $this->post('user_email');

                if ($user_name == "" && $user_email == "") {
                    $user_data = array(
                        'device_token'  => $this->post('device_token'),
                    );
                } else if ($user_name == "") {
                    $user_data = array(
                        'user_email'    => $this->post('user_email'),
                        'device_token'  => $this->post('device_token'),
                    );
                } else if ($user_email == "") {
                    $user_data = array(
                        'user_name'    => $this->post('user_name'),
                        'device_token'  => $this->post('device_token'),
                    );
                } else {
                    $user_data = array(
                        'user_name'    => $this->post('user_name'),
                        'user_email'    => $this->post('user_email'),
                        'device_token'  => $this->post('device_token'),
                    );
                }


                $conds['apple_id'] = $this->post('apple_id');
                $user_datas = $this->User->get_one_by($conds);
                $user_id = $user_datas->user_id;

                if ($user_datas->is_banned == 1) {

                    $this->error_response(get_msg('err_user_banned'), 500);
                } else {
                    if (!$this->User->save($user_data, $user_id)) {
                        $this->error_response(get_msg('err_user_register'), 500);
                    }

                    $noti_token = array(
                        "device_token" => $this->post('device_token')
                    );

                    $noti_count = $this->Noti->count_all_by($noti_token);

                    if ($noti_count == 1) {
                        if ($this->Noti->exists($noti_token)) {
                            $noti_id = $this->Noti->get_one_by($noti_token);
                            $push_noti_token_id = $noti_id->push_noti_token_id;
                            $noti_data = array(

                                "user_id" => $user_id

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        } else {
                            $noti_data = array(

                                "user_id" => $user_id,
                                "device_token" => $this->post('device_token')

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        }
                    } else {
                        $this->Noti->delete_by($noti_token);
                        $noti_data = array(

                            "user_id" => $user_id,
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                }
            } else {
                $user_datas = $this->User->get_one_by($conds);
                $user_id = $user_datas->user_id;

                if ($user_datas->is_banned == 1) {

                    $this->error_response(get_msg('err_user_banned'), 500);
                } else {
                    if (!$this->User->save($user_datas, $user_id)) {
                        $this->error_response(get_msg('err_user_register'), 500);
                    }

                    $noti_token = array(
                        "device_token" => $this->post('device_token')
                    );

                    $noti_count = $this->Noti->count_all_by($noti_token);

                    if ($noti_count == 1) {
                        if ($this->Noti->exists($noti_token)) {
                            $noti_id = $this->Noti->get_one_by($noti_token);
                            $push_noti_token_id = $noti_id->push_noti_token_id;
                            $noti_data = array(

                                "user_id" => $user_id

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        } else {
                            $noti_data = array(

                                "user_id" => $user_id,
                                "device_token" => $this->post('device_token')

                            );
                            $this->Noti->save($noti_data, $push_noti_token_id);
                        }
                    } else {
                        $this->Noti->delete_by($noti_token);
                        $noti_data = array(

                            "user_id" => $user_id,
                            "device_token" => $this->post('device_token')

                        );
                        $this->Noti->save($noti_data, $push_noti_token_id);
                    }
                }
            }

            $this->custom_response($this->User->get_one($user_datas->user_id));
        }
    }

    /**
     * Get reported item list by login user id
     */
    function get_blocked_user_by_loginuser_get()
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
        $conds_block['from_block_user_id'] = $this->get('login_user_id');

        $blocked_datas = $this->Block->get_all_by($conds_block)->result();
        //print_r(count( $blocked_datas ));die;

        if (count($blocked_datas) > 0) {
            foreach ($blocked_datas as $blocked_data) {
                $result .= "'" . $blocked_data->to_block_user_id . "',";

                //"'" .$to_block_user_data->to_block_user_id . "',";

            }


            if (!empty($limit) && !empty($offset)) {
                // if limit & offset is not empty
                //$data = $this->model->get_wallpaper_delete_by_userid( $conds, $limit, $offset )->result();
                $data = $this->model->get_all_by($conds, $limit, $offset)->result();
            } else if (!empty($limit)) {
                // if limit is not empty

                //$data = $this->model->get_wallpaper_delete_by_userid( $conds, $limit )->result();
                $data = $this->model->get_all_by($conds, $limit)->result();
            } else {
                // if both are empty
                //$data = $this->model->get_wallpaper_delete_by_userid( $conds )->result();
                $data = $this->model->get_all_by($conds)->result();
            }


            $blocked_user = rtrim($result, ",");

            $conds['user_id'] = $blocked_user;

            $user_list = $this->User->get_all_in_blocked_user($conds, $limit, $offset)->result();
            //print_r($user_list);die;

            //$this->ps_adapter->convert_item( $user_list );
            $this->custom_response($user_list);
        } else {
            $this->error_response(get_msg('record_not_found'), 404);
        }
    }

    /**
     * Users Logout
     */
    function logout_post()
    {
        // validation rules for user register
        $rules = array(

            array(
                'field' => 'user_id',
                'rules' => 'required|callback_id_check[User]'
            )
        );

        // exit if there is an error in validation,
        if (!$this->is_valid($rules)) exit;

        $conds['user_id'] = $this->post('user_id');
        $this->Noti->delete_by($conds);

        $this->success_response(get_msg('success_logout'), 200);
    }

    /** user apply blue mark */

    // function apply_blue_mark_post()
    // {
    //     // validation rules for user register
    //     $rules = array(

    //         array(
    //             'field' => 'user_id',
    //             'rules' => 'required|callback_id_check[User]'
    //         )
    //     );

    //     // exit if there is an error in validation,
    //     if ( !$this->is_valid( $rules )) exit;

    //     $user_id = $this->post('user_id');
    //     $note = $this->post('note');

    //     $is_verify_blue_mark = $this->User->get_one($user_id)->is_verify_blue_mark;

    //     if ($is_verify_blue_mark == 0 || $is_verify_blue_mark == 3) {
    //         $user_data['user_id'] = $user_id;
    //         $conds['user_id'] = $user_id;
    //         $this->Blue_mark->delete_by($conds);

    //         if ($this->Blue_mark->save($user_data)) {
    //             // update at user table
    //             $usr_data['is_verify_blue_mark'] = 2 ;
    //             $usr_data['blue_mark_note'] = $note;
    //             $this->User->save($usr_data, $user_id);

    //             $this->success_response( get_msg( 'apply_success' ));
    //         } else {
    //             $this->error_response( get_msg( 'err_model' ));
    //         }
    //     } else if($is_verify_blue_mark == 2) {
    //         $this->error_response( get_msg( 'blue_mark_pending' ));
    //     } else {
    //         $this->error_response( get_msg( 'already_blue_mark' ));
    //     }



    // }


    function create_setup_intent_post()
    {
        $user_id = $this->post('user_id');
        $user = $this->db->get_where('core_users', ['user_id' => $user_id])->row();

        \Stripe\Stripe::setApiKey('sk_test_51QoMzlLgQ4wcmgSD7W91VBFgqa7IYXcO4gkDujuPwxLxewCf5rKZaGPlQgM7sOI7LciKDHZS5CSOHEpYXPTOotqR009EDQQTaO');

        if (empty($user->stripe_customer_id)) {
            $customer = \Stripe\Customer::create([
                'email' => $user->user_email
            ]);

            $this->db->update('core_users', [
                'stripe_customer_id' => $customer->id
            ], ['user_id' => $user_id]);
        } else {
            $customer = ['id' => $user->stripe_customer_id];
        }

        $intent = \Stripe\SetupIntent::create([
            'customer' => $customer['id']
        ]);

        return $this->success_response(['client_secret' => $intent->client_secret]);
    }

    function save_payment_method_post()
    {
        $user_id = $this->post('user_id');
        $payment_method = $this->post('payment_method'); 

        \Stripe\Stripe::setApiKey('sk_test_51QoMzlLgQ4wcmgSD7W91VBFgqa7IYXcO4gkDujuPwxLxewCf5rKZaGPlQgM7sOI7LciKDHZS5CSOHEpYXPTOotqR009EDQQTaO');

        try {
            $user = $this->db->get_where('core_users', ['user_id' => $user_id])->row();

            if (!$user) {
                return $this->error_response('User not found.', 404);
            }
           
            // Attach the payment method to the customer first
            \Stripe\PaymentMethod::attach($payment_method, [
                'customer' => $user->stripe_customer_id,
            ]);
            
            // Set it as default
            \Stripe\Customer::update($user->stripe_customer_id, [
                'invoice_settings' => ['default_payment_method' => $payment_method],
            ]);

            // Save to your local DB
            $this->db->update('core_users', [
                'default_payment_method' => $payment_method
            ], ['user_id' => $user_id]);

            return $this->success_response('Card saved and set as default successfully.');

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return $this->error_response('Stripe error: ' . $e->getMessage(), 500);
        } catch (Exception $e) {
            return $this->error_response('An error occurred: ' . $e->getMessage(), 500);
        }
    }



    function has_card_saved_post()
    {
        $user_id = $this->post('user_id');
        $user = $this->db->get_where('core_users', ['user_id' => $user_id])->row();

        return $this->success_response( ["saved" => !empty($user->default_payment_method)]);
    }

    function update_payment_status_post()
    {
        $user_id = $this->post('user_id');
        $follower_id = $this->post('follower_id');
        $item_id = $this->post('item_id');
        $who_paid = $this->post('who_paid'); // user / follower

       
        $existing = $this->db
            ->where(['user_id' => $user_id, 'item_id' => $item_id])
            ->get('core_follower_payment_status')->row();

        $update = [];
        
        if ($who_paid === 'user') {
            $update['user_payment_status'] = 1;
        } else {
            $update['follower_id'] = $follower_id;
            $update['follower_payment_status'] = 1;
        }

        if ($existing) {
            $this->db->update('core_follower_payment_status', $update, ['id' => $existing->id]);
        } else {
            $this->db->insert('core_follower_payment_status', [
                'user_id' => $user_id,
                'follower_id' => $follower_id,
                'item_id' => $item_id,
                'user_payment_status' => $who_paid === 'user' ? 1 : 0,
                'follower_payment_status' => $who_paid === 'follower' ? 1 : 0,
            ]);
        }

        return $this->success_response('Payment status updated.');
    }

    function charge_user_post()
    {
        try {

            $user_id = $this->post('user_id');
            $item_id = $this->post('item_id');
            $amount = $this->post('amount'); // in dollars

            $user = $this->db->get_where('core_users', ['user_id' => $user_id])->row();

            \Stripe\Stripe::setApiKey('sk_test_51QoMzlLgQ4wcmgSD7W91VBFgqa7IYXcO4gkDujuPwxLxewCf5rKZaGPlQgM7sOI7LciKDHZS5CSOHEpYXPTOotqR009EDQQTaO');
            
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount * 100, // convert to cents
                'currency' => 'usd',
                'customer' => $user->stripe_customer_id,
                'payment_method' => $user->default_payment_method,
                'off_session' => true,
                'confirm' => true,
            ]);

            // update payment status
            $this->db->where(['user_id' => $user_id, 'item_id' => $item_id])
                    ->update('core_follower_payment_status', ['user_payment_status' => 1]);

            return $this->success_response('User charged successfully.');

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return $this->error_response('Stripe error: ' . $e->getMessage(), 500);
        } catch (Exception $e) {
            return $this->error_response('An error occurred: ' . $e->getMessage(), 500);
        } catch (\Stripe\Exception\CardException $e) {
            return $this->error_response($e->getError()->message, 400);
        }
    }

}
