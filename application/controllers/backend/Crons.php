<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crons extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load the database library
        $this->load->database();
        $this->load->library('email');
    }

    public function updateItemStartDate()
    {   
        //trans_start
        $this->db->trans_start(); 

        // Update for 'week' - Adds 7 days to start_date if repeat_on is 'week'
        $this->db->set('start_date', 'DATE_ADD(CURDATE(), INTERVAL 6 DAY)', false);
        $this->db->set('updated_date', 'NOW()', false);
        $this->db->where('repeat_on', 'week');
        $this->db->where('start_date <', 'CURDATE()', false);
        $this->db->update('bs_items');

        // Update for 'year' - Adds 1 year to start_date if repeat_on is 'year'
        $this->db->set('start_date', 'DATE_ADD(CURDATE(), INTERVAL 1 YEAR)', false);
        $this->db->set('updated_date', 'NOW()', false);
        $this->db->where('repeat_on', 'year');
        $this->db->where('start_date <', 'CURDATE()', false);
        $this->db->update('bs_items');


        // Update for 'week' - Adds 7 days to stop_date if repeat_on is 'week'
        $this->db->set('start_date', 'DATE_ADD(CURDATE(), INTERVAL 6 DAY)', false);
        $this->db->set('stop_date', 'DATE_ADD(CURDATE(), INTERVAL 6 DAY)', false);
        $this->db->set('updated_date', 'NOW()', false);
        $this->db->where('repeat_on', 'week');
        $this->db->where('stop_date <', 'CURDATE()', false);
        $this->db->update('bs_items');

        // Update for 'year' - Adds 1 year to stop_date if repeat_on is 'year'
        $this->db->set('start_date', 'DATE_ADD(CURDATE(), INTERVAL 1 YEAR)', false);
        $this->db->set('stop_date', 'DATE_ADD(CURDATE(), INTERVAL 1 YEAR)', false);
        $this->db->set('updated_date', 'NOW()', false);
        $this->db->where('repeat_on', 'year');
        $this->db->where('stop_date <', 'CURDATE()', false);
        $this->db->update('bs_items');
        
        //trans_complete
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            // Transaction failed, send an email
            $this->sendFailureEmail();
        } else {
            // Transaction success, send an email
            $this->sendSuccessEmail();
            die('Cron job executed successfully: start_date and stop_date updated based on repeat_on.');
        }

    }

    private function sendFailureEmail()
    {
        // Email configuration
        $this->email->from('juan@pinn.app', 'Pinn.app');
        $this->email->to('juan@pinn.app');
        $this->email->cc('letsdoperfect212@gmail.com');
        $this->email->subject('Cron Job Failed: updateItemStartDate');
        $this->email->message('The updateItemStartDate cron job encountered an error. Please check the logs for more details.');

        if ($this->email->send()) {
            die('Failure notification email sent successfully.');
        } else {
            die('Failed to send failure notification email.');
        }
    }

    private function sendSuccessEmail()
    {
        // Email configuration
        $this->email->from('juan@pinn.app', 'Pinn.app');
        $this->email->to('letsdoperfect212@gmail.com');
        $this->email->subject('Cron Job Success: updateItemStartDate');
        $this->email->message('The updateItemStartDate cron job completed. Please check the logs for more details.');

        if ($this->email->send()) {
            die('Success notification email sent successfully.');
        } else {
            die('Failed to send Success notification email.');
        }
    }
}