<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hosts extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
    }

    public function check_user_by_phone() {
        $phone = $this->input->post('user_phone');

        if (!$phone) {
            echo json_encode(['status' => 'error', 'message' => 'Phone number is required']);
            return;
        }

        $query = $this->db->get_where('core_users', ['user_phone' => $phone]);
        $user = $query->row();

        if ($user) {
            //print_r($user->user_id);
            //exit();
            // Generate token
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+7 days'));

            // Save to auth_tokens table
            $this->db->insert('auth_tokens', [
                'user_id'    => $user->user_id,
                'token'      => $token,
                'expires_at' => $expires_at
            ]);

            // Return user + token
            echo json_encode([
                'status' => 'success',
                'user'   => $user,
                'token'  => $token,
                'expires_at' => $expires_at
            ]);
        } else {
            echo json_encode(['status' => 'not_found', 'message' => 'User not found']);
        }
    }
}
