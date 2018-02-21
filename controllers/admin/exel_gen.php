<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class exel_gen extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->library('encrypt');
		$this->load->model('course_model','',TRUE);
		$this->load->model('common_model','',TRUE);
		$this->load->model('user_model','',TRUE);
		$this->load->model('sales_model','',TRUE);
	
		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
   		
   		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}
		if($err_msg = $this->session->flashdata('err_msg')){
          $this->err_msg =$err_msg;
   		}

   		 $this->load->helper(array('form'));
		$this->load->library('form_validation');
   		
	   	$this->load->library('Datatables');
        $this->load->library('table');
        $this->load->database();

    }
	function index()
	{

	}

	function newsletter_gen()
	{
		$this->load->helper(array('php-excel'));	
		  
        $this->db-> select('*');
		$this->db->distinct('email');
		$this->db-> from('users');
		$this->db->where('newsletter','yes');
		$this->db->order_by('first_name','asc');
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
		$sql = $query->result();
	   	foreach ($sql as $row)
			 {
				 $country = $this->user_model->get_country_name($row->country_id);
				if($country==false)
				$country = "N/A";
				$password = $this->encrypt->decode($row->password);
				$cur_code = $this->course_model->get_currency_by_country($row->country_id);
				 
		$data_array[] = array($row->email,$row->first_name,$row->last_name,$row->username,$password,$country,$cur_code,$row->reg_date);
			 }
		$field_array[] = array("Email","First Name","Last Name","Username","Password","Country","CurrencyCOde","Reg date");	 
		   $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "newsletter_subscribed" );
	
	}
	function makeup_old()
	{
		$this->load->helper(array('php-excel'));	
		
        $this->db-> select('*');
		//$this->db->distinct('email');
		$this->db-> from('course_enrollments');
		//$this->db->order_by('newsletter','desc');
		$this->db->where("(course_id=3 OR course_id=13) AND student_course_units LIKE '%a:9%'");
		//$this->db->or_where('course_id',13);	
		//$this->db->like('student_course_unitsd','a:9{i');		
        $query = $this->db->get();
        
		
		$result = $query->result();
        //echo "<pre>";print_r($result);exit;
        $data = array();
		   
        
        if($query -> num_rows() >0 )
		{
			foreach($result as $row)
			{
				
				$user_arr = $this->user_model->get_stud_details($row->user_id);
				//echo "<pre>";echo "<br>user = ".$row->user_id;print_r($user_arr);continue;
				if(!empty($user_arr))
				{
				//$country = $this->user_model->get_country_name($user_arr[0]->country_id);
				//if($country==false)
				//$country = "N/A";
				$password = $this->encrypt->decode($user_arr[0]->password);
				//$cur_code = $this->course_model->get_currency_by_country($user_arr[0]->country_id);
			//	$deal_site = $this->course_model->get_deal_site_name($user_arr[0]->user_id);
				//if($deal_site=="")
				//$deal_site = "N/A";
				
				
				 if($row->course_status==0)
				 $status = "Not Started";
				 else if($row->course_status==1)
				 $status = "Studying";
				 else if($row->course_status==2)
				 $status = "Completed";
				 else if($row->course_status==3)
				 $status = "Certificate Requested";
				 else if($row->course_status==4)
				 $status = "Certificate Issued";
				 else if($row->course_status==5)
				 $status = "Matirial Access";
				 else if($row->course_status==6)
				 $status = "Archived";
				 else if($row->course_status==7)
				 $status = "Expired";
				 
				 //$course_arr = $this->user_model->get_coursename($row->course_id);
				//$user_courses = $this->course_model(get_user_courses_names);
	
				 $data_array[]= array($user_arr[0]->email,$user_arr[0]->first_name,$user_arr[0]->last_name,$user_arr[0]->username,$password,$status,$user_arr[0]->reg_date,$user_arr[0]->newsletter);
				}
			
			}
		$field_array[] = array("Email","First Name","Last Name","Username","Password","Status","Reg date","Newsletter");	
		 $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "courseviselist" ); 	
		}
       
      
	
	}
	
	

	//*******************************************************//
	
function ICOES_certificates($post_id=0)
	{
		$data['postal_option'] = $post_id;
		$data['search_mode'] = 0;
		if(isset($this->flashmessage))
		$data['flashmessage'] = $this->flashmessage;
		if(isset($this->err_msg))
		$data['err_msg'] = $this->err_msg;
		if(isset($_POST['search']))
		{
			$btn = $this->input->post('search');
			$data['search_mode'] = 1;
			$data['lan'] = $this->input->post('lan');
			$data['course'] = $this->input->post('course');
			if($btn=='Genarate Exel')
			{
				//$this->session->set_flashdata('lan',$data['lan']);
				//$this->session->set_flashdata('course',$data['course']);
				//redirect('admin/report_gen/course_user_gen');
			}
		}
		$content['searchmode'] = true;
		$data['view'] = "ICOES_cert";
		$data['content'] = $data;
		$this->load->view('admin/template',$data);
	}
	
	
	function search_icoes()
	{
		$postage_option=$this->input->post('postage_option');
		$source = $this->input->post('source');
		$date_from=$this->input->post('date_from');
		$date_to=$this->input->post('date_to');
		
		
		
		$excel_name_date ='';
		if(isset($_POST['generate_excel_range']))		
		{
			
		$this->load->helper(array('php-excel'));	
        $this->db-> select('*');
		if($postage_option==1)
		{
			$this->db->where('postal_type','standard');
		}
		else if($postage_option==3)
		{
			$this->db->where('postal_type','express');
		}
		if($source == 1)
		{
			$this->db->where('source','sales');
		}
		else if($source == 2)
		{
			$this->db->where('source','other');
		}
		
				
		if($date_from!=''){
			$this->db->where('hardcopy_apply_date >=', $date_from); 			
				}
		if($date_to!=''){
			$this->db->where('hardcopy_apply_date <=', $date_to); 
		}
		$this->db-> from('certificate_hardcopy_applications');
		$this->db->order_by('student_certificate_id','desc');
        $query = $this->db->get();
        
		//$total_array = $query->result();
		//$result = array_slice($total_array,$pageStart,$rp);
        $result =  $query->result();
        //echo "<pre>";print_r($result);exit;
        if($query -> num_rows() >0 )
		{
			foreach($result as $row)
			{
				$user_arr = $this->user_model->get_stud_details($row->user_id);
				if($row->postal_type=='standard')
				{
					$postal_type = 'Standard';
				}
				else if($row->postal_type=='express')
				{
					$postal_type = 'Express';
				}
				else
				{
					$postal_type = '';
				}
				$country = $this->user_model->get_country_name($user_arr[0]->country_id);
				
				if($country==false)
				$country = "N/A";
				if($user_arr[0]->lang_id==3)
				$language = "Sapnish";
				else if ($user_arr[0]->lang_id==4)
				$language = "English";			
				$cur_code = $this->course_model->get_currency_by_country($user_arr[0]->country_id);
				
				 $course_arr = $this->user_model->get_coursename($row->course_id);
				 
				  if($row->payment_id!=0)
				 {				 
				  	$source = $this->sales_model->get_source_from_payment_id($row->payment_id);
				 }
				 else
				 {
					 $payment_id = $this->sales_model->get_payment_id_from_user_subscription('hardcopy',$row->user_id,$row->course_id);
					 $source = $this->sales_model->get_source_from_payment_id($payment_id);
				 }
				 
				 
				//$user_courses = $this->course_model(get_user_courses_names);
	 			$data_array[]= array(
				"Trendimi",
				$postal_type,
				$source,
				$course_arr[0]->course_name,
				$user_arr[0]->first_name,
				$user_arr[0]->last_name,
				$user_arr[0]->house_number,
				$user_arr[0]->street,
				$user_arr[0]->address,
				$user_arr[0]->zipcode,
				$user_arr[0]->city,
				$country,
				$row->grade,
				date('F - Y',strtotime($row->completion_date)),
				"100-".$row->student_certificate_id,
				$language);
			
			}
			
		$field_array[] = array("Entity","Postal type","Source","Course","First Name","Last Name","House number / name","Road / Street","Address 3","Postal Code","City","Country",
		"Grade","Completion Date","Certificate Number","Language");	
		//echo "<pre>";print_r($field_array);print_r($data_array);exit;
	//	exit;
		 $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		  $exel_ob = $xls->generateXML ("Eventtrix Certificates"); 
			
         // $path = '/public/ICOES_email/Trendimi Certificates: '.$query -> num_rows().' {/Trendimi/'.$query -> num_rows();
			
		}
		else
		{
			$data_array[]= array("Eventtrix","No certificate requests today.");
			$this->load->helper(array('php-excel'));	
				$field_array[] = array("Entity","No requests");	
                 $xls = new Excel_XML;
				 $xls->addArray ($field_array);
				 $xls->addArray ($data_array);
				 //$exel_ob = $xls->generateToSaveXML();
				 $exel_ob = $xls->generateXML ('Eventtrix Certificates: 0');
			//	$path ='/public/ICOES_email/Trendimi Certificates: 0.xls';
				
						  
		}
			
		}
		else
		{
		$content['postal_option'] = isset($postage_option)?$postage_option:'';
		$content['source'] = isset($source)?$source:'';
		$content['date_from'] = isset($date_from)?$date_from:'';
		$content['date_to'] = isset($date_to)?$date_to:'';
		
		
		$content['searchmode'] = true;		
		$data['view'] = 'ICOES_cert';
		
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);
		
		}
	}
	function fetch_ICOES_request($postage_option=0)
	{
		
        $page = 1;	// The current page
		$sortname = 'student_certificate_id';	 // Sort column
		$sortorder = 'desc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		
		$source =0;
		if(isset($_GET['postal_option']) && $_GET['postal_option']!=''){
			$postage_option = $_GET['postal_option'];
		}
		if(isset($_GET['source']) && $_GET['source']!=''){
			$source = $_GET['source'];
		}
		
		//$_GET['source']
		//$postage_option=$this->input->post('postage_option');
		
		
		/*if(isset($_GET['postage_option']))
		{
			$postage_option=$_GET['postage_option'];
		}*/
		/*echo $postage_option;
		exit;*/
	    if (isset($_POST['page'])) {
		$page = $_POST['page'];
		}
		if (isset($_POST['sortname'])) {
		$sortname = $_POST['sortname'];
		}
		if (isset($_POST['sortorder'])) {
		$sortorder = $_POST['sortorder'];
		}
		if (isset($_POST['rp'])) {
		$rp = $_POST['rp'];
		}
		
		// Setup paging SQL
		 $pageStart = ($page-1)*$rp;
       if(isset($_GET['lan']))
	   $lan = $_GET['lan'];
	   if(isset($_GET['course']))
	   {
		   $course = $_GET['course'];
		 
	   }
        
        $this->db-> select('*');
		if($postage_option==1)
		{
			$this->db->where('postal_type','standard');
		}
		else if($postage_option==3)
		{
			$this->db->where('postal_type','express');
		}
		
		if($source == 1)
		{
			$this->db->where('source','sales');
		}
		else if($source == 2)
		{
			$this->db->where('source','other');
		}
		
		if(isset($_GET['date_from']) && $_GET['date_from']!=''){
			$this->db->where('hardcopy_apply_date >=', $_GET['date_from']); 		
				}
		if(isset($_GET['date_to']) && $_GET['date_to']!=''){
			$this->db->where('hardcopy_apply_date <=', $_GET['date_to']); 

		}
		
		//$this->db->where('hardcopy_apply_date >',);
		$this->db-> from('certificate_hardcopy_applications');
		
		$this->db->order_by($sortname,$sortorder);
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
		$total_array = $query->result();
		$result = array_slice($total_array,$pageStart,$rp);
        
        $data = array();
		$data['page'] = $page;
		 $data['total'] = $query -> num_rows();
		$data['rows'] = array();
		
       
        
        if($query -> num_rows() >0 )
		{
			//$result = $query -> result();
			




			foreach($result as $row)
			{
				$user_arr = $this->user_model->get_stud_details($row->user_id);
				if($row->postal_type=='standard')
				{
					$postal_type = 'Standard';
				}
				else if($row->postal_type=='express')
				{
					$postal_type = 'Express';
				}
				else
				{
					$postal_type = '';
				}
				
				$country = $this->user_model->get_country_name($user_arr[0]->country_id);
				if($country==false)
				$country = "N/A";
			//	$password = $this->encrypt->decode($user_arr[0]->password);
				$cur_code = $this->course_model->get_currency_by_country($user_arr[0]->country_id);
				//$deal_site = $this->course_model->get_deal_site_name($user_arr[0]->user_id);
				//if($deal_site=="")
			//	$deal_site = "N/A";
				//echo $deal_site;continue;
				
				/* if($row->course_status==0)
				 $status = "Not Started";
				 else if($row->course_status==1)
				 $status = "Studying";
				 else if($row->course_status==2)
				 $status = "Completed";
				 else if($row->course_status==3)
				 $status = "Certificate Requested";
				 else if($row->course_status==4)
				 $status = "Certificate Issued";
				 else if($row->course_status==5)
				 $status = "Matirial Access";
				 else if($row->course_status==6)
				 $status = "Archived";
				 else if($row->course_status==7)
				 $status = "Expired";
				 */
				 $course_arr = $this->user_model->get_coursename($row->course_id);
				 
				 if($row->payment_id!=0)
				 {				 
				  	$source = $this->sales_model->get_source_from_payment_id($row->payment_id);
				 }
				 else
				 {
					 $payment_id = $this->sales_model->get_payment_id_from_user_subscription('hardcopy',$row->user_id,$row->course_id);
					 $source = $this->sales_model->get_source_from_payment_id($payment_id);
				 }
				//$user_courses = $this->course_model(get_user_courses_names);
	
				 $data['rows'][] = array(
				'id' => $row->student_certificate_id,
				'cell' => array(				
				$postal_type,
				$course_arr[0]->course_name,
				$user_arr[0]->first_name,
				$user_arr[0]->last_name,
				$row->source,
				/*$user_arr[0]->street,
				$user_arr[0]->address,
				$user_arr[0]->zipcode,
				$user_arr[0]->city,
				$country,*/
				$row->grade,
				date('d - m - Y',strtotime($row->hardcopy_apply_date)),
				"100-".$row->student_certificate_id)
			);
			}
			
		}
       
       echo json_encode($data); exit(); 
	
	}
	function ICOES_exel_gen($postage_option=0)
	{
		
		if(isset($_POST['search_gen']))
		{
			
			if($this->input->post('date_email')!="")
			{
				$date = $this->input->post('date_email');
			}			
			else
			{
				$date =  	date('Y-m-d');
			}
		}
		else
		{
			$date = date('Y-m-d');
		}
		$this->load->helper(array('php-excel'));	
        $this->db-> select('*');
		if($postage_option==1)
		{
			$this->db->where('postal_type','standard');
		}
		else if($postage_option==3)
		{
			$this->db->where('postal_type','express');
		}
		$this->db->where('hardcopy_apply_date',$date);
		$this->db-> from('certificate_hardcopy_applications');
		$this->db->order_by('student_certificate_id','desc');
        $query = $this->db->get();
        
		//$total_array = $query->result();
		//$result = array_slice($total_array,$pageStart,$rp);
        $result =  $query->result();
        //echo "<pre>";print_r($result);exit;
        if($query -> num_rows() >0 )
		{
			foreach($result as $row)
			{
				$user_arr = $this->user_model->get_stud_details($row->user_id);
				if($row->postal_type=='standard')
				{
					$postal_type = 'Standard';
				}
				else if($row->postal_type=='express')
				{
					$postal_type = 'Express';
				}
				else
				{
					$postal_type = '';
				}
				$country = $this->user_model->get_country_name($user_arr[0]->country_id);
				
				if($country==false)
				$country = "N/A";
				if($user_arr[0]->lang_id==3)
				$language = "Sapnish";
				else if ($user_arr[0]->lang_id==4)
				$language = "English";
			//	$password = $this->encrypt->decode($user_arr[0]->password);
				$cur_code = $this->course_model->get_currency_by_country($user_arr[0]->country_id);
				//$deal_site = $this->course_model->get_deal_site_name($user_arr[0]->user_id);
				//if($deal_site=="")
			//	$deal_site = "N/A";
				//echo $deal_site;continue;
				
				/* if($row->course_status==0)
				 $status = "Not Started";
				 else if($row->course_status==1)
				 $status = "Studying";
				 else if($row->course_status==2)
				 $status = "Completed";
				 else if($row->course_status==3)
				 $status = "Certificate Requested";
				 else if($row->course_status==4)
				 $status = "Certificate Issued";
				 else if($row->course_status==5)
				 $status = "Matirial Access";
				 else if($row->course_status==6)
				 $status = "Archived";
				 else if($row->course_status==7)
				 $status = "Expired";
				 */
				 $course_arr = $this->user_model->get_coursename($row->course_id);
				 
				  if($row->payment_id!=0)
				 {				 
				  	$source = $this->sales_model->get_source_from_payment_id($row->payment_id);
				 }
				 else
				 {
					 $payment_id = $this->sales_model->get_payment_id_from_user_subscription('hardcopy',$row->user_id,$row->course_id);
					 $source = $this->sales_model->get_source_from_payment_id($payment_id);
				 }
				 
				 
				//$user_courses = $this->course_model(get_user_courses_names);
	 $data_array[]= array(
				"Trendimi",
				$postal_type,
				$source,
				$course_arr[0]->course_name,
				$user_arr[0]->first_name,
				$user_arr[0]->last_name,
				$user_arr[0]->house_number,
				$user_arr[0]->street,
				$user_arr[0]->address,
				$user_arr[0]->zipcode,
				$user_arr[0]->city,
				$country,
				$row->grade,
				date('F - Y',strtotime($row->completion_date)),
				"100-".$row->student_certificate_id,
				$language);
			
			}
			
			//echo "<pre>";print_r($data_array);exit;
		$field_array[] = array("Entity","Postal type","Source","Course","First Name","Last Name","House number / name","Road / Street","Address 3","Postal Code","City","Country",
		"Grade","Completion Date","Certificate Number","Language");	
		 $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		  $exel_ob = $xls->generateXML ($date." Eventtrix Certificates: ".$query -> num_rows()." {".$date."/Eventtrix/".$query -> num_rows()."" ); 
			
          $path = '/public/ICOES_email/'.$date.' Eventtrix Certificates: '.$query -> num_rows().' {'.$date.'/Eventtrix/'.$query -> num_rows();
			
		}
		else
		{
			$data_array[]= array("Eventtrix","No certificate requests today.");
			$this->load->helper(array('php-excel'));	
				$field_array[] = array("Entity","No requests");	
                 $xls = new Excel_XML;
				 $xls->addArray ($field_array);
				 $xls->addArray ($data_array);
				 //$exel_ob = $xls->generateToSaveXML();
				 $exel_ob = $xls->generateXML ( $date.'Eventtrix Certificates: 0');
				$path ='/public/ICOES_email/'.$date.' Eventtrix Certificates: 0.xls';
				
						  
		}
		
				/*$this->load->helper('file');
				 if ( ! write_file($path,$exel_ob,"w+"))
				  {
					  echo 'Unable to write the file';
				  }
				  else
				  {
					  $mailContent = "Please find the attached excel of certificate requests today(".date('m-d-Y').")";

		$this->load->library('email');		
				

			//	$tomail = 'info@trendimi.com';
			 	$tomail = 'deeputg1992@gmail.com';
				$from = "mailer@trendimi.com";
				//$cc ="bhagath@crayonsweb.com";
				$cc = "";
				$subject = "Trendimi Hardcopy ".date('d-m-Y');
	
				//echo $tomail."<Br>";echo $from."<Br>";echo $subject."<Br>".$mailContent;
						   	
					  $this->email->from($from); 
					  $this->email->to($tomail); 
					 // $this->email->reply_to();
					  //$this->email->cc($cc); 
					  //$this->email->bcc(''); 
					  $this->email->attach($path);
					  $this->email->subject($subject);
					  $this->email->message($mailContent);	
					  $this->email->send();
				  }*/
       
	}
	
}
?>