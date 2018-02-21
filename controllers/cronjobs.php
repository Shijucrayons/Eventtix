<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cronjobs extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		//$this->load->library('encrypt');
		$this->load->model('course_model','',TRUE);
		//$this->load->model('common_model','',TRUE);
		$this->load->model('user_model','',TRUE);
	

   		
	   	$this->load->library('Datatables');
        $this->load->library('table');
        $this->load->database();

    }
	function index()
	{

	}

	
	function mail_hardcopy_ICOES()
	{
		$date_yes = date('Y-m-d',strtotime(date('Y-m-d').' - 1 day'));
		$this->load->helper(array('php-excel'));	
        $this->db-> select('*');
		$this->db->where('hardcopy_apply_date',$date_yes);
		$this->db-> from('certificate_hardcopy_applications');
		$this->db->order_by('student_certificate_id','desc');
        $query = $this->db->get();
        
		//$total_array = $query->result();
		//$result = array_slice($total_array,$pageStart,$rp);
        $result =  $query->result();
       // echo "<pre>";print_r($result);exit;
        if($query -> num_rows() >0 )
		{
			foreach($result as $row)
			{
				$user_arr = $this->user_model->get_stud_details($row->user_id);
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
				//$user_courses = $this->course_model(get_user_courses_names);
				
				 $course_id = $row->course_id;
				 
				 	/* Style Me course */
		
				if($course_id==1)
				{		
					$coursename= 'Style Me - Personal Stylist';
				}
				else if($course_id==11 )
				{		
					$coursename='Autoimagen';
				}
				/* End Style Me course */	
				
				/* Style You course */
		
				else if( $course_id==2 )
				{ 	
				$coursename = 'Style You - Personal Shopper';
				}
				else if($course_id==12 )
				{	
				$coursename = 'Personal Shopper';
				}
				
				/* End Style You course */
				
				/* Make Up course */
		
				else if($course_id==3 )
				{	
				$coursename = 'Make Up Artist';
				}
				else if($course_id==13)
				{		
				$coursename = 'Maquillaje';
				}
				
				/* End Make Up course */
				/* Wedding Planner course */
		
				else if($course_id==4 )
				{		
				$coursename='Wedding Planner';
				}
				
				else if($course_id==14 )
				{		
				$coursename='Wedding Planner';
				}
				
				/* End Wedding Planner course */
				
				
				/* Nail artist course */
				else if($course_id==5)
				{	
				$coursename='Nail Artist';
				}
				else if($course_id==15 )
				{		
				$coursename='Nails Art';
				}
				
				/* End Nail artist course */
				
				
				
				/* Hair Stylist course */
		
				else if($course_id==6 )
				{		
				$coursename='Hair Stylist';
				}
				else if($course_id==16 )
				{
						$coursename='Hair Stylist';
				}
				/* End Hair Stylist course */
				
				
	 $data_array[]= array(
				"Trendimi",
				//$course_arr[0]->course_name,
				$coursename,
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
			
			
		$field_array[] = array("Entity","Course","First Name","Last Name","House number / name","Road / Street","Address 3","Postal Code","City","Country",
		"Grade","Completion Date","Certificate Number","Language");	
		 $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $exel_ob = $xls->generateToSaveXML();
		  //$exel_ob = $xls->generateXML ( "1902014 Trendimi Certificates: ".$query -> num_rows()." {".date('Y-m-d')."/Trendimi/".$query -> num_rows()."" ); 
			
			$path = 'public/ICOES_email/'.$date_yes.' Trendimi Certificates: '.$query -> num_rows().'.xls';
			
		}
		else
		{
			$data_array[]= array("Trendimi","No certificate requests today.");
			$this->load->helper(array('php-excel'));	
				$field_array[] = array("Entity","No requests");	
                 $xls = new Excel_XML;
				 $xls->addArray ($field_array);
				 $xls->addArray ($data_array);
				 $exel_ob = $xls->generateToSaveXML();
				 //$exel_ob = $xls->generateXML ( 'Trendimi Certificates: 0');exit;
				$path ='public/ICOES_email/'.$date_yes.' Trendimi Certificates: 0.xls';
				
						  
		}
		
				$this->load->helper('file');
				 if ( ! write_file($path,$exel_ob,"w+"))
				  {
					  echo 'Unable to write the file';
				  }
				  else
				  {
					  $mailContent = "Please find the attached excel of certificate requests today(".$date_yes.")";

		$this->load->library('email');		
				

			//	$tomail = 'jane@trendimi.net';
			 	//$tomail = 'certificates@icoes.org';
				$tomail = 'certificates@trendimi.com';
				$from = "mailer@trendimi.com";
				//$cc ="bhagath@crayonsweb.com";
				//$cc="certificates@trendimi.com";
				 $cc = 'certificates@icoes.org';
				//$cc = "";
				$subject = "Trendimi Hardcopy ".$date_yes;
	
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
				  }
       
	}
	
	
	
	
}
?>