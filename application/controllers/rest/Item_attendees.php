<?php
require_once(APPPATH . 'libraries/REST_Controller.php');

/**
 * REST API for About
 */
class Item_attendees extends API_Controller
{
    /**
     * Constructs Parent Constructor
     */
    function __construct()
    {
        parent::__construct('Item_attendee');
        $this->load->model('Item_attendee');
    }
    public function attende_post()
    {
        $rules = array(
            array(
                'field' => 'user_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'item_id',
                'rules' => 'required'
            ),
        );
        if (!$this->is_valid($rules)) exit;

        $item_id = $this->post('item_id');
        $user_id = $this->post('user_id');
        $clicked_on = date('Y-m-d H:i:s');
        try {            
            $saved_data = $this->Item_attendee->add_attendee($item_id, $user_id, $clicked_on);
            $this->response(array('status' => 'success', 'message' => 'Attendee added', 'data' => $saved_data));
        } catch (Exception $e) {
            $this->response(array('status' => 'error', 'message' => 'Unable to add attendee'));
        }
    }

    /*
        sending push notification 1day before event start
    */

    public function run_cron_get(){
            
        $today_date = date('Y-m-d');
        // $today_date = '2024-04-11';  // add static date for testing

        $select = array('id','title');
        $this->db->select($select);
        $this->db->from('bs_items');
        $this->db->where('start_date', "DATE_ADD('$today_date', INTERVAL 1 DAY)", FALSE); 
        $query = $this->db->get()->result_array(); // get all the event which is started on tomorrow 
        
        $item_title = $query[0]['title'];
        
        $item_ids = array_map(function($item) {
            return $item['id'];
        }, $query);
        
        if(!empty($item_ids)){
            $user_ids = [];
            $users = $this->db->where_in('item_id', $item_ids)->get('bs_item_attendees')->result(); // get all user id which is enroll in events
            if ( count( $users ) > 0 ) {
                foreach ( $users as $user ) {
                    $user_ids[] = $user->user_id;
                }
            }
            
            if(!empty($user_ids)){
                $devices = $this->Noti->get_all_device_in($user_ids)->result();  // get all user device token for sending notifications
                $device_ids = array();
                $platform_names = array();
                
                if ( count( $devices ) > 0 ) {
                    foreach ( $devices as $key=>$device ) {
                        
                        $device_ids[] = $device->device_token;
                        $platform_names[] = $device->platform_name;

                        $data = [];
                        $lang_name = get_user_lang($device->user_id);
                        
                        if($lang_name=='Spanish'){
                            $data['message'] = "Tu proximo evento en 1 dia";
                            $data['flag'] = 'event_reminder';
                            $data['title'] = 'Recordatorio de Pinn';
                        }else{
                            $data['message'] = "Your event in 1 day";
                            // $data['sender_name'] = "Dhruv";
                            // $data['item_id'] = $item_id;
                            // $data['sender_profle_photo'] = '';//$user_profile_photo;
                            $data['flag'] = 'event_reminder';
                            $data['title'] = 'Pinn Reminder';
                            // $data['chat_flag'] = 'CHAT_FROM_BUYER';
                        }
                        // $data['message'] = "Upcoming event ".$item_title." is tomorrow";
                        // // $data['sender_name'] = "Dhruv";
                        // // $data['item_id'] = $item_id;
                        // // $data['sender_profle_photo'] = '';//$user_profile_photo;
                        // $data['flag'] = 'event_reminder';
                        // $data['title'] = 'Pinn Reminder';
                        // $data['chat_flag'] = 'CHAT_FROM_BUYER';
                        $status = send_android_fcm( explode(" ",$device->device_token), $data, explode(" ",$device->platform_name) ); // helper function to send notification
                        if($status){
                            print_r($status);
                        }else{
                            echo json_encode("Somthing Wrong! Please try again");
                        }
                    }
                }
                
            }else{
                echo json_encode("No User Found");
            }
        }else{
            echo json_encode("No event Found");
        }
            
    }
    public function attendees_item_get()
    {
        $rules = array(
            array(
                'field' => 'item_id',
                'rules' => 'required'
            ),
        );
        if (!$this->is_valid($rules)) exit;
        try {
            $item_id = $this->get('item_id');
            $item = $this->db->get_where('bs_items', array('id' => $item_id))->row();
            $attendees = $this->Item_attendee->get_attendees_by_item($item_id);
            $response = array(
                'status' => 'success',
                'message' => 'Attendees retrieved successfully againts item',
                'data' => array(
                    'attendees' => $attendees,
                    'item' => $item,
                )
            );
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null
            );
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }
    public function attendees_user_get()
    {
        $rules = array(
            array(
                'field' => 'userid',
                'rules' => 'required'
            ),
        );
        if (!$this->is_valid($rules)) exit;
        try {
            $user_id = $this->get('userid');
            $user = $this->db->get_where('core_users', array('user_id' => $user_id))->row();
            $attendees = $this->Item_attendee->get_attendees_by_user($user_id);
            $response = array(
                'status' => 'success',
                'message' => 'Attendees retrieved successfully againts user',
                'data' => array(
                    'attendees' => $attendees,
                    'user' => $user,
                )
            );
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null
            );
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }
}