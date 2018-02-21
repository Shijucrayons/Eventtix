<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class grid extends CI_Controller 
{

    public function __construct() 
	{
        parent::__construct();
        $this->load->library('Datatables');
        $this->load->library('table');
        $this->load->database();
    }
    function index()
    {

       
    }
	function cmsenglish()
	{
		 //set table id in table open tag
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl); 
        
        $this->table->set_heading('Language','Page Title','Page Status');

        $this->load->view('admin/cmsenglish');
	}
	function cmsspanish()
	{
		 //set table id in table open tag
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl); 
        
        $this->table->set_heading('Language','Page Title','Page Status');

        $this->load->view('admin/cmsspanish');
	}
	function cmschineese()
	{
		 //set table id in table open tag
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl); 
        
        $this->table->set_heading('Language','Page Title','Page Status');

        $this->load->view('admin/cmschineese');
	}
    //function to handle callbacks
    function datatable_eng()
    {
        $this->datatables->select('id,language,page_title,page_status')
		->where('language',1)
        ->unset_column('id')
        ->from('cms');
        
        echo $this->datatables->generate();
    }
	
	 function datatable_span()
    {
        $this->datatables->select('id,language,page_title,page_status')
		->where('language',2)
        ->unset_column('id')
        ->from('cms');
        
        echo $this->datatables->generate();
    }
	
	 function datatable_chin()
    {
        $this->datatables->select('id,language,page_title,page_status')
		->where('language',3)
        ->unset_column('id')
        ->from('cms');
        
        echo $this->datatables->generate();
    }
}