<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model class for touch table
 */
class Item_ticket extends PS_Model
{

    /**
     * Constructs the required data
     */
    function __construct()
    {
        parent::__construct('bs_item_tickets', 'id', 'ticket_');
    }


    public function createTicket($ticketData)
    {
        if ($this->db->insert('bs_item_tickets', $ticketData)) {
            return true;
        }

        return false;
    }

    public function getTicketById($ticketId)
    {
        $query = $this->db->get_where('bs_item_tickets', array('ticket_id' => $ticketId));
        return $query->row_array();
    }
    public function updateTicket($ticketId, $ticketData)
    {
        $this->db->where('ticket_id', $ticketId);
        $this->db->update('bs_item_tickets', $ticketData);
    }

    public function getItemData($itemId)
    {
        $query = $this->db->get_where('bs_items', array('id' => $itemId));
        return $query->row_array();
    }

    public function getTicketsByItemId($itemId)
    {
        $query = $this->db->get_where('bs_item_tickets', array('item_id' => $itemId));
        return $query->result_array();
    }

    public function getTotalTicketsByItemId($item_id)
    {
        $this->db->select('COUNT(*) as total_tickets, SUM(quantity) as total_quantity');
        $this->db->from('bs_item_tickets');
        $this->db->where('item_id', $item_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->row();
            return array(
                'total_tickets' => $result->total_tickets,
                'total_quantity' => $result->total_quantity
            );
        } else {
            return false;
        }
    }

    public function getCurrencyData($currency_id)
    {
        $query = $this->db->get_where('bs_items_currency', array('id' => $currency_id));
        return $query->row_array();
    }
}
