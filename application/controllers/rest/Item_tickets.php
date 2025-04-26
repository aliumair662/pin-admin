<?php
require_once(APPPATH . 'libraries/REST_Controller.php');

/**
 * REST API for About
 */
class Item_tickets extends API_Controller
{
    /**
     * Constructs Parent Constructor
     */
    function __construct()
    {
        // Call the parent
        parent::__construct('Item_ticket');
        $this->load->model('Item_ticket');
    }

    public function createTicket_post()
    {
        $rules = array(
            array(
                'field' => 'item_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'currency_id',
                'rules' => 'required'
            ),

        );
        if (!$this->is_valid($rules)) exit;
        $ticketsData = $this->post('tickets');
        $currency_id = $this->post('currency_id');
        $item_id = $this->post('item_id');
        $user_id= $this->post('user_id');



        if (empty($ticketsData)) {
            $response = array('status' => 'error', 'message' => 'No ticket data provided.');
        } else {
            $validationErrors = array();
            $successCount = 0;
            $createdTickets = array();

            foreach ($ticketsData as $ticketData) {
                $this->form_validation->set_data($ticketData);
                $this->form_validation->set_rules('category', 'Category', 'required');
                $this->form_validation->set_rules('description', 'Description', 'required');
                $this->form_validation->set_rules('price', 'Price', 'required|numeric');
                $this->form_validation->set_rules('quantity', 'Quantity', 'required|integer');
                $this->form_validation->set_rules('expiration', 'Expiration', 'required');

                if ($this->form_validation->run() == true) {
                    $id = 'ticket_' . uniqid();

                    $ticket = array(
                        'ticket_id' => $id,
                        'item_id' => $item_id,
                        'category' => $ticketData['category'],
                        'description' => $ticketData['description'],
                        'price' => $ticketData['price'],
                        'quantity' => $ticketData['quantity'],
                        'expiration' => $ticketData['expiration'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'currency_id' => $currency_id,
                        'user_id' => $user_id,
                    );

                    try {
                        $ticketId = $this->Item_ticket->createTicket($ticket);
                        if ($ticketId) {
                            $successCount++;
                            $createdTickets[] = array(
                                'ticket_data' => $ticket
                            );
                        }
                    } catch (Exception $e) {
                        $validationErrors[] = array(
                            'ticket_data' => $ticketData,
                            'error_message' => $e->getMessage()
                        );
                    }
                } else {
                    $validationErrors[] = array(
                        'ticket_data' => $ticketData,
                        'validation_errors' => validation_errors()
                    );
                }
            }

            if ($successCount > 0) {
                $response = array('status' => 'success', 'message' => 'Tickets created successfully.', 'created_tickets' => $createdTickets);
            } else {
                $errorMessages = array();
                foreach ($validationErrors as $error) {
                    $errorMessages[] = array(
                        'ticket_data' => $error['ticket_data'],
                        'validation_errors' => validation_errors()
                    );
                }
                $response = array('status' => 'error', 'message' => 'Failed to create tickets.', 'validation_errors' => $validationErrors, 'error_messages' => $errorMessages);
            }
        }

        $statusCode = !empty($response['status']) && $response['status'] === 'success' ? 200 : 400;
        $this->output->set_status_header($statusCode)->set_content_type('application/json')->set_output(json_encode($response));
    }


    public function tickets_item_get()
    {
        $rules = array(
            array(
                'field' => 'item_id',
                'rules' => 'required'
            ),

        );
        if (!$this->is_valid($rules)) exit;
        $item_id = $this->get('item_id');

        $itemData = $this->Item_ticket->getItemData($item_id);

        $ticketsByItemId = $this->Item_ticket->getTicketsByItemId($item_id);

        $ticketsWithCurrencyData = array();
        foreach ($ticketsByItemId as $ticket) {
            $currency_id = $ticket['currency_id'];
            $currencyData = $this->Item_ticket->getCurrencyData($currency_id);
            $ticket['ticket_currency'] = $currencyData;
            $ticketsWithCurrencyData[] = $ticket;
        }

        if (!empty($itemData)) {
            $statusCode = 200;
            $response = array('status' => 'success', 'message' => 'Data retrieved successfully.', 'item_data' => $itemData, 'tickets' => $ticketsWithCurrencyData);
        } else {
            $statusCode = 400;
            $response = array('status' => 'error', 'message' => 'No data found for the given item ID.');
        }

        $this->output->set_status_header($statusCode)->set_content_type('application/json')->set_output(json_encode($response));
    }


    public function editTicket_post()
    {
        $rules = array(
            array(
                'field' => 'item_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'currency_id',
                'rules' => 'required'
            ),
            array(
                'field' => 'ticket_id',
                'rules' => 'required'
            ),

        );
        if (!$this->is_valid($rules)) exit;
        $ticketId = $this->post('ticket_id');
        $ticketData = $this->post('ticket');
        $currency_id = $this->post('currency_id');
        $item_id = $this->post('item_id');
        $user_id = $this->post('user_id');

        if (empty($ticketId) || empty($ticketData)) {
            $response = array('status' => 'error', 'message' => 'Ticket ID or data not provided.');
            $status = 400;
        } else {
            $this->form_validation->set_data($ticketData);
            $this->form_validation->set_rules('category', 'Category', 'required');
            $this->form_validation->set_rules('description', 'Description', 'required');
            $this->form_validation->set_rules('price', 'Price', 'required|numeric');
            $this->form_validation->set_rules('quantity', 'Quantity', 'required|integer');
            $this->form_validation->set_rules('expiration', 'Expiration', 'required');

            if ($this->form_validation->run() == true) {
                $existingTicket = $this->Item_ticket->getTicketById($ticketId);

                if (!empty($existingTicket)) {
                    $updatedTicket = array(
                        'item_id' => $item_id,
                        'currency_id' => $currency_id,
                        'category' => $ticketData['category'],
                        'description' => $ticketData['description'],
                        'price' => $ticketData['price'],
                        'user_id' => $user_id,
                        'quantity' => $ticketData['quantity'],
                        'expiration' => $ticketData['expiration'],
                        'created_at' => $existingTicket['created_at']
                    );

                    try {
                        $this->Item_ticket->updateTicket($ticketId, $updatedTicket);
                        $updatedTicket['ticket_id'] = $ticketId; // Include the ticket_id in the response
                        $response = array('status' => 'success', 'message' => 'Ticket updated successfully.', 'updated_ticket' => $updatedTicket);
                        $status = 200;
                    } catch (Exception $e) {
                        $status = 400;
                        $response = array('status' => 'error', 'message' => 'Failed to update ticket.', 'error_message' => $e->getMessage());
                    }
                } else {
                    $status = 400;
                    $response = array('status' => 'error', 'message' => 'Ticket not found.');
                }
            } else {
                $status = 400;
                $response = array('status' => 'error', 'message' => validation_errors());
            }
        }
        
        $this->output->set_status_header($status)->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function totalTicketsByItemId_get()
    {
        $rules = array(
            array(
                'field' => 'item_id',
                'rules' => 'required'
            ),
        );
        if (!$this->is_valid($rules)) exit;

        $item_id = $this->get('item_id');

        $itemData = $this->Item_ticket->getItemData($item_id);
        if ($itemData) {
            $totalTickets = $this->Item_ticket->getTotalTicketsByItemId($item_id);

            if ($totalTickets !== false) {
                $response = array('status' => 'success', 'message' => 'Total tickets retrieved successfully.', 'total_tickets' => $totalTickets);
            } else {
                $response = array('status' => 'error', 'message' => 'No data found for the given item ID.');
            }
        } else {
            $response = array('status' => 'error', 'message' => 'No data found for the given item ID.');
        }

        $statusCode = !empty($response['status']) && $response['status'] === 'success' ? 200 : 400;
        $this->output->set_status_header($statusCode)->set_content_type('application/json')->set_output(json_encode($response));
    }
}
