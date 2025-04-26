<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hosts extends PS_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function login()
    {
        $this->load->view('hosts/login');
    }

    public function verify()
    {
        $this->load->view('hosts/verify');
    }

    public function dashboard()
    {
        $this->load->view('hosts/dashboard');
    }
}