<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model class for touch table
 */
class Item_attendee extends PS_Model
{

    /**
     * Constructs the required data
     */
    function __construct()
    {
        parent::__construct('bs_item_attendees', 'id', 'atten');
    }

    public function add_attendee($item_id, $user_id, $clicked_on)
    {
        $id = 'atten_' . uniqid();
        $data = array(
            'id' => $id,
            'item_id' => $item_id,
            'user_id' => $user_id,
            'clicked_on' => $clicked_on
        );
        try {
            $this->db->insert('bs_item_attendees', $data);
            $saved_data = $this->db->get_where('bs_item_attendees', array('id' => $id))->row_array();
            return $saved_data;
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }

    public function get_attendees_by_item($item_id)
    {
        $this->db->where('item_id', $item_id);
        $attendees_query = $this->db->get('bs_item_attendees')->result();
        $data = array();
        foreach ($attendees_query as $attendee) {
            $this->db->where('user_id', $attendee->user_id);
            $user_query = $this->db->get('core_users')->result();
            if (!empty($user_query)) {
                $data[] = array(
                    'attendee' => $attendee,
                    'user' => $user_query[0]
                );
            }
        }

        return $data;
    }





    public function get_attendees_by_user($user_id)
    {
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('bs_item_attendees');
        return $query->result();
    }
}
