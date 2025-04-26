<?php
require_once(APPPATH . 'libraries/REST_Controller.php');

/**
 * REST API for News
 */
class TicketBuys extends API_Controller
{
    function __construct()
    {
        // Call the parent
        parent::__construct('TicketBuys');
    }
    public function ticket_purchase_post()
    {

        $rules = array(
            array(
                'field' => 'item_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'ticket_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'selected_quantity',
                'rules' => 'required'
            ),
            array(
                'field' => 'payment_method',
                'rules' => 'required'
            ),
        );
        if (!$this->is_valid($rules)) exit;
        try {
            $item_id = $this->post('item_id');
            $ticket_id = $this->post('ticket_id');
            $selected_quantity = $this->post('selected_quantity');
            $payment_method = $this->post('payment_method');
            $payment_method_nonce = $this->post('payment_method_nonce');
            $category = $this->post('category');
            $user_id = $this->post('user_id');
            $tickets = array();
            $this->db->where('user_id', $user_id);
            $query = $this->db->get('core_users');
            if ($query->num_rows() > 0) {
                $user_data = $query->row_array();
            }


            for ($i = 1; $i <= $selected_quantity; $i++) {
                $ticket_number = $this->generate_ticket_number();
                $id = 'purchase_' . uniqid();
                $qr_code_filename = $this->generate_qr_code([$id]);

                // Save ticket purchase data to the database
                $this->db->insert('bs_ticket_purchase', array(
                    'purchase_id' => $id,
                    'item_id' => $item_id,
                    'ticket_id' => $ticket_id,
                    'selected_quantity' => 1,
                    'user_id' => $user_id,
                    'category' => $category,
                    'payment_method' => $payment_method,
                    'payment_method_nonce' => $payment_method_nonce,
                    'ticket_number' => $ticket_number,
                    'qr_code' => $qr_code_filename
                ));
                $this->db->select('buy_ticket');
                $this->db->where('ticket_id', $ticket_id);
                $query = $this->db->get('bs_item_tickets');

                if ($query->num_rows() > 0) {
                    $current_buy_ticket = $query->row_array()['buy_ticket'];
                }
                $new_buy_ticket = $current_buy_ticket + 1;

                $this->db->set('buy_ticket', $new_buy_ticket);
                $this->db->where('ticket_id', $ticket_id);
                $this->db->update('bs_item_tickets');
                $tickets[] = array(
                    'purchase_id' => $id,
                    'item_id' => $item_id,
                    'ticket_id' => $ticket_id,
                    'user_id' => $user_id,
                    'selected_quantity' => 1,
                    'category' => $category,
                    'payment_method' => $payment_method,
                    'payment_method_nonce' => $payment_method_nonce,
                    'ticket_number' => $ticket_number,
                    'qr_code_url' => base_url('qr_codes/' . $qr_code_filename),
                );
            }

            $response = array(
                'tickets' => $tickets,
                'user_data' => $user_data,
                'message' => 'Tickets purchase successful!',
            );

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
        } catch (Exception $e) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(array('error' => 'Ticket purchase failed. Please try again later.')));
        }
    }

    private function generate_ticket_number()
    {
        return 'TICKET_' . rand(10000, 99999);
    }


    private function generate_qr_code($data)
    {
        $qr_code_data = http_build_query($data);


        $api_url = 'https://chart.googleapis.com/chart';
        $size = '256x256';

        $qr_code_url = "{$api_url}?cht=qr&chs={$size}&chl={$qr_code_data}";


        $qr_code_folder = FCPATH . 'qr_codes/';


        if (!file_exists($qr_code_folder)) {
            mkdir($qr_code_folder, 0777, true);
        }


        $qr_code_filename = 'qr_code_' . uniqid() . '.png';
        $qr_code_filepath = $qr_code_folder . $qr_code_filename;

        try {
            $ch = curl_init($qr_code_url);
            $fp = fopen($qr_code_filepath, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);

            if (curl_errno($ch) !== 0) {
                throw new Exception('Failed to download QR code image: ' . curl_error($ch));
            }


            curl_close($ch);
            fclose($fp);
        } catch (Exception $e) {
            $this->output->set_status_header(500);
            $response = array('error' => 'Failed to generate QR code. Please try again later.');
            echo json_encode($response);
            return;
        }

        return $qr_code_filename;
    }

    public function ticket_purchase_data_get()
    {

        $rules = array(
            array(
                'field' => 'user_id',
                'rules' => 'required'
            ),
        );
        if (!$this->is_valid($rules)) exit;
        $user_id = $this->get('user_id');
        $is_admin = $this->check_admin($user_id);
        if (!$is_admin) {
            $response = array(
                'status' => 'error',
                'message' => 'You are not authorized to access this data.'
            );
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
            return;
        } else {
            $ticket_purchase_data = $this->get_ticket_purchase_data_from_db();
            foreach ($ticket_purchase_data as &$purchase) {
                $user_info = $this->get_user_info_from_db($purchase['user_id']);
                $purchase['user_data'] = $user_info;

                $item_info = $this->get_item_info_from_db($purchase['item_id']);
                $purchase['item_data'] = $item_info;

                $ticket_info = $this->get_ticket_info_from_db($purchase['ticket_id']);
                $purchase['ticket_data'] = $ticket_info;
            }

            $response = array(
                'status' => 'success',
                'data' => $ticket_purchase_data
            );

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    }
    private function check_admin($user_id)
    {
        $this->db->select('user_is_sys_admin');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('core_users');

        if ($query->num_rows() > 0) {
            $user_info = $query->row_array();
            return $user_info['user_is_sys_admin'] == 1;
        }

        return false;
    }
    private function get_ticket_purchase_data_from_db()
    {
        $query = $this->db->get('bs_ticket_purchase');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return array();
    }
    private function get_user_info_from_db($user_id)
    {
        $this->db->select('user_name');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('core_users');

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return array();
    }

    private function get_item_info_from_db($item_id)
    {
        $this->db->select('title');
        $this->db->where('id', $item_id);
        $query = $this->db->get('bs_items');

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return array();
    }
    private function get_ticket_info_from_db($ticket_id)
    {
        $this->db->where('ticket_id', $ticket_id);
        $query = $this->db->get('bs_item_tickets');

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return array();
    }
}
