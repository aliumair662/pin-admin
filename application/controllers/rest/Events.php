<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Events extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'form']);
        $this->load->library(['form_validation', 'upload']);
        header('Content-Type: application/json');
    }

    public function create() {
        
       // $user_token = $this->input->get_request_header('Authorization');
       // echo $user_token;
       // exit();
        $user_id = $this->get_authenticated_user($this);

        if (!$user_id) {
            echo json_encode(['status' => 'unauthorized', 'message' => 'Invalid or expired token']);
            return;
        }
        //$user_id = $this->input->post('user_id'); // in real app, get from auth token/session

      

        $data = [
            'user_id'      => $user_id,
            'event_title'  => $this->input->post('event_title'),
            'event_description'  => $this->input->post('event_description'),
            'currency'     => $this->input->post('currency'),
            'township'     => $this->input->post('township'),
            'city'         => $this->input->post('city'),
            'property_id'  => $this->input->post('property_id'),
            'price_unit'   => $this->input->post('price_unit'),
            'price_note'   => $this->input->post('price_note'),
            'start_date'   => $this->input->post('start_date'),
            'end_date'     => $this->input->post('end_date'),
        ];
       
        // Upload event picture if provided
        if (!empty($_FILES['event_picture']['name'])) {
           
            $upload_path = './uploads/events/';

            // Check if the directory exists, if not, create it
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true); // true allows recursive directory creation
            }

            $config['upload_path'] = './uploads/events/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = time() . '_' . $_FILES['event_picture']['name'];

            $this->upload->initialize($config);

            if ($this->upload->do_upload('event_picture')) {
                $upload_data = $this->upload->data();
                $data['event_picture'] = 'uploads/events/' . $upload_data['file_name'];
                log_message('debug', 'Uploaded file path: ' . $data['event_picture']);
            } else {
                echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                return;
            }
        }
       
        if ($this->db->insert('events', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Event created successfully']);
        } else {
            log_message('error', 'Database error: ' . $this->db->last_query() . ' | Error: ' . $this->db->_error_message());
            echo json_encode(['status' => 'error', 'message' => 'Failed to create event']);
        }
    }
    public function get_event_by_id($event_id)
    {
        
        // Authenticate user
        $user_id = $this->get_authenticated_user($this);
        
      
        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        // Get the event data for the authenticated user
        $event = $this->db->get_where('events', ['id' => $event_id, 'user_id' => $user_id])->row();

        if (!$event) {
            echo json_encode(['status' => 'error', 'message' => 'Event not found or unauthorized']);
            return;
        }

        echo json_encode(['status' => 'success', 'event' => $event]);
    }


    public function update($id) {
        // Authenticate the user
        $user_id = $this->get_authenticated_user($this);
    
        if (!$user_id) {
            echo json_encode(['status' => 'unauthorized', 'message' => 'Invalid or expired token']);
            return;
        }
    
        // Get the existing event data
        $event = $this->db->get_where('events', ['id' => $id, 'user_id' => $user_id])->row(); // Ensure it's the right user
    
        if (!$event) {
            echo json_encode(['status' => 'error', 'message' => 'Event not found or unauthorized']);
            return;
        }
    
        // Gather the updated data
        $updateData = [
            'user_id'      => $user_id, // Ensure user_id is set to the authenticated user
            'event_title'  => $this->input->post('event_title'),
            'event_description'  => $this->input->post('event_description'),
            'currency'     => $this->input->post('currency'),
            'township'     => $this->input->post('township'),
            'city'         => $this->input->post('city'),
            'property_id'  => $this->input->post('property_id'),
            'price_unit'   => $this->input->post('price_unit'),
            'price_note'   => $this->input->post('price_note'),
            'start_date'   => $this->input->post('start_date'),
            'end_date'     => $this->input->post('end_date'),
        ];
    
        // Check if a new event picture has been uploaded
        if (!empty($_FILES['event_picture']['name'])) {
            $upload_path = './uploads/events/';
    
            // Check if the directory exists, if not, create it
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true); // true allows recursive directory creation
            }
    
            // If there is a previous event picture, delete it
            if (!empty($event->event_picture) && file_exists($event->event_picture)) {
                unlink($event->event_picture); // Remove the old image
            }
    
            // Upload configuration
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = time() . '_' . $_FILES['event_picture']['name'];
    
            $this->upload->initialize($config);
    
            if ($this->upload->do_upload('event_picture')) {
                $upload_data = $this->upload->data();
                // Set the event picture path
                $updateData['event_picture'] = 'uploads/events/' . $upload_data['file_name'];
            } else {
                echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                return;
            }
        }
    
        // Update the event in the database
        $this->db->where('id', $id);
        if ($this->db->update('events', $updateData)) {
            echo json_encode(['status' => 'success', 'message' => 'Event updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Update failed']);
        }
    }
    

    public function get_user_events()
    {
        $user_id = $this->get_authenticated_user($this);
    
        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        // Get events for the authenticated user
        $events = $this->db->get_where('events', ['user_id' => $user_id])->result();

        // If events are found, add additional data
        if (!empty($events)) {
            foreach ($events as $event) {
                // Fetch township data
                $township = $this->db->get_where('bs_item_location_townships', ['id' => $event->township])->row();

                // Fetch city data
                $city = $this->db->get_where('bs_item_location_cities', ['id' => $event->city])->row();

                // Fetch property data
                $property = $this->db->get_where('bs_items_property_by', ['id' => $event->property_id])->row();

                // Add the additional data to the event object
                $event->township_data= $township ;
                $event->city_data = $city ;
                $event->property_data =$property;
            }

            // Return the events along with additional data
            echo json_encode(['status' => 'success', 'events' => $events]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No events found for this user']);
        }
    }




        function get_authenticated_user($CI) {
           
            // Retrieve request headers
            $headers = $CI->input->request_headers();
            
            // Get the Authorization header
            $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;
            
            if (!$authHeader) {
                // No Authorization header found, return null
                return null;
            }
            
            // Extract token from Bearer format
            $token = str_replace('Bearer ', '', $authHeader);
            
            // Check if token exists and is valid
            $query = $CI->db->get_where('auth_tokens', [
                'token' => $token,
                'expires_at >=' => date('Y-m-d H:i:s')
            ]);
            
            $auth = $query->row();
            
            if ($auth) {
                // Token is valid, return the user_id
                return $auth->user_id;
            } else {
                // Token is either invalid or expired
                return null;
            }
        }
    
}
