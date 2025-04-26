<?php
require_once(APPPATH . 'libraries/REST_Controller.php');

/**
 * REST API for Colors
 */
class Follower_invite extends API_Controller
{

    /**
     * Constructs Parent Constructor
     */
    function __construct()
    {
        parent::__construct('event_user');
    }

    public function invite_post()
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
            array(
                'field' => 'follower_id',
                'rules' => 'required'
            ),
        );
        if (!$this->is_valid($rules)) exit;
        $this->load->database();
        $user_id = $this->post('user_id');
        $selected_followers = $this->post('follower_id');
        $event_id = $this->post('item_id');
        $id = 'event_' . uniqid();

        $data = array(
            'id' => $id,
            'user_id' => $user_id,
            'follower_id' => $selected_followers,
            'item_id' => $event_id,
            'status' => 'pending',
            'is_read' => 0,
        );
        $this->db->insert('bs_follower_invite', $data);
        if ($this->db->affected_rows() > 0) {
            $response = array(
                'success' => true,
                'message' => 'Item invite send to your follower',
                'data' => $data
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Error',
            );
        }
        return $this->response($response);
    }

    public function invitations_get()
    {
        $this->load->database();
        $follower_id = $this->get('follower_id');
        $query = $this->db->get_where('bs_follower_invite', array('follower_id' => $follower_id));

        if ($query->num_rows() > 0) {
            $invitations = $query->result();
            $response = array(
                'success' => true,
                'message' => 'Invitations found',
                'data' => $invitations,
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'No invitations found',
            );
        }
        return $this->response($response);
    }

    public function user_inivitation_get()
    {
        $user_id = $this->get('user_id');
        $this->load->database();
        $query = $this->db->get_where('bs_follower_invite', array('user_id' => $user_id));

        if ($query->num_rows() > 0) {
            $invitations = $query->result();
            $response = array(
                'success' => true,
                'message' => 'All Invite follower',
                'data' => $invitations,
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'No Follower Invite',
            );
        }
        return $this->response($response);
    }

    public function invitation_action_post()
    {
        $rules = array(
            array(
                'field' => 'inivitation_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'action',
                'rules' => 'required'
            ),
        );
        if (!$this->is_valid($rules)) exit;
        $invitation_id = $this->post('inivitation_id');
        $action = $this->post('action');

        $this->load->database();

        // Check if the invitation exists
        $invitation = $this->db->get_where('bs_follower_invite', array('id' => $invitation_id))->row();
        if (!$invitation) {
            $response = array(
                'success' => false,
                'message' => 'Invitation not found',
            );
            return $this->response($response);
        }

        // Check if the action is valid
        if ($action != 'accept' && $action != 'reject') {
            $response = array(
                'success' => false,
                'message' => 'Invalid action',
            );
            return $this->response($response);
        }

        // Update the status of the invitation
        $data = array(
            'status' => ($action == 'accept' ? 'accepted' : 'rejected'),
        );
        $this->db->where('id', $invitation_id);
        $this->db->update('bs_follower_invite', $data);
        $invitation = $this->db->get_where('bs_follower_invite', array('id' => $invitation_id))->row();

        $response = array(
            'success' => true,
            'message' => 'Invitation ' . $action . 'ed',
            'data' => $invitation,
        );
        return $this->response($response);
    }

    public function iniviation_read_post()
    {
        $rules = array(
            array(
                'field' => 'inivitation_id',
                'rules' => 'required'
            ),
        );
        if (!$this->is_valid($rules)) exit;
        $invitation_id = $this->post('inivitation_id');
        $this->load->database();

        // Check if the invitation exists
        $invitation = $this->db->get_where('bs_follower_invite', array('id' => $invitation_id))->row();
        if (!$invitation) {
            $response = array(
                'success' => false,
                'message' => 'Invitation not found',
            );
            return $this->response($response);
        }
        $data = array(
            'is_read' => 1,
        );
        $this->db->where('id', $invitation_id);
        $this->db->update('bs_follower_invite', $data);
        $invitation = $this->db->get_where('bs_follower_invite', array('id' => $invitation_id))->row();

        $response = array(
            'success' => true,
            'message' => 'Is_Read Value Updated Successfully',
            'data' => $invitation,
        );
        return $this->response($response);
    }

    public function share_link_post()
    {
        $base_url = base_url();
        $userid = $this->post('userid');
        $itemid = $this->post('itemid');
        $secret_key = 'SLHChGUuN0';
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $text = $userid . '||' . $itemid;
        $ciphertext_raw = openssl_encrypt($text, $cipher, $secret_key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $secret_key, $as_binary = true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
        $share_link = $base_url . 'join/' . $ciphertext; // Construct share link with encrypted token

        $response = array(
            'success' => true,
            'message' => 'Share link generated',
            'share_link' => $share_link,
            'encrypted_key' => $ciphertext,
        );
        return $this->response($response);
    }
    
    public function UsersForEvent_get()
    {
        try {
            $event_id = $this->get('event_id');
            if (empty($event_id)) {
                throw new Exception("Event ID is missing.");
            }

            $this->db->select('f.item_id, f.id, f.is_read, f.follower_id, f.status, fu.user_id AS user_id, fu.user_name AS user_name, fu.user_email AS user_email, fu.user_phone AS user_phone,fu.user_profile_photo AS user_photo, fuf.user_name AS follower_name, fuf.user_email AS follower_email, fuf.user_phone AS follower_phone, fuf.user_profile_photo AS follower_profile_photo');
            $this->db->from('bs_follower_invite AS f');
            $this->db->join('core_users AS fu', 'f.user_id = fu.user_id');
            $this->db->join('core_users AS fuf', 'f.follower_id = fuf.user_id');
            $this->db->where('f.item_id', $event_id);
            // $this->db->where('f.status', 'accepted');
            $query = $this->db->get();

            if (!$query) {
                throw new Exception("Database query error: " . $this->db->error()['message']);
            }

            $results = $query->result_array();

            $this->db->select('item_att.user_id,item_att.item_id,item_att.id,fu.user_id AS user_id, fu.user_name AS user_name, fu.user_email AS user_email, fu.user_phone AS user_phone,fu.user_profile_photo AS user_photo');
            $this->db->from('bs_item_attendees AS item_att');
            $this->db->join('core_users AS fu', 'item_att.user_id = fu.user_id');
            $this->db->where('item_id', $event_id);
            $attendees_query = $this->db->get();

            if (!$attendees_query) {
                throw new Exception("Database query error: " . $this->db->error()['message']);
            }

            $attendees = $attendees_query->result_array();

            $response = array();

            if (!empty($results)) {
                $response['invite'] = $results;
            } else {
                $response['invite'] = array();
                $response['message'] = 'No invited to this event.';
            }

            if (!empty($attendees)) {
                $response['attendees'] = $attendees;
            } else {
                $response['attendees'] = array();
                $response['message'] = 'No users attending this event.';
            }

            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        } catch (Exception $e) {
            $errorResponse = array('error' => $e->getMessage());
            $this->output->set_content_type('application/json')->set_output(json_encode($errorResponse));
        }
    }

     public function isUserGoingToEvent_get()
    {
        $event_id = $this->get('event_id');
        $user_id = $this->get('user_id');
        try {
            if (empty($event_id) || empty($user_id)) {
                throw new Exception("Event ID or User ID is missing.");
            }
            $this->db->from('bs_item_attendees');
            $this->db->where('item_id', $event_id);
            $this->db->where('user_id', $user_id);
            // $this->db->where('status', 'accepted');
            $query = $this->db->get();
            if (!$query) {
                throw new Exception("Database query error: " . $this->db->error()['message']);
            }
            $isGoing = $query->num_rows() > 0;
            $this->output->set_content_type('application/json')->set_output(json_encode(['is_going' => $isGoing]));
        } catch (Exception $e) {
            $errorResponse = array('error' => $e->getMessage());
            $this->output->set_content_type('application/json')->set_output(json_encode($errorResponse));
        }
    }

}
