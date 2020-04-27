<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class HomeController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        ini_set("display_errors", 0);
        error_reporting(0);
        date_default_timezone_set("UTC");
    }

    public function index() {
        $this->load->view('privacy_policy');
    }
    
    public function HelpAndSupport() {
        $this->load->view('help_support');
    }
    
    public function TermOfServices() {
        $this->load->view('term_services');
    }

}
