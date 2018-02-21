<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class maintenance extends CI_Controller
{


	function launching()
	{		
		
 		$this->load->view('launching/index','');
	}	
}