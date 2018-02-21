<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class deeps_home extends CI_Controller
{
 	 
	function __construct()
	{

		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form'));
    //$this->load->helper(array('language'));
		$this->load->library('form_validation');
		$this->load->model('user_model','',TRUE);
		$this->load->model('student_model','',TRUE);
		//echo $this->input->ip_address();
		$this->load->library('geoip_lib');
		$ip = $this->input->ip_address();
    	$this->geoip_lib->InfoIP($ip);
    	$this->code3= $this->geoip_lib->result_country_code3();
     	$this->con_name = $this->geoip_lib->result_country_name();
      
		if(isset($_POST['username'])&&isset($_POST['password']))
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['username'] = $this->input->post('username');
			if($this->form_validation->run() == TRUE)
			{//Go to private area
				redirect('coursemanager/campus/'.$this->session->userdata['student_logged_in']['id'], 'refresh');
      }
		}
   
		if(isset($_GET['lang_id'])){
			$newdata = array(
                   'language'  => $_GET['lang_id']
               );
			$this->session->set_userdata($newdata);
		}
		elseif(!$this->session->userdata('language')){
			$newdata = array(
                   'language'  => '4'
               );
			$this->session->set_userdata($newdata);
		} 
		
		 $curr_code=$this->user_model->get_currency_id($this->con_name);

      if($curr_code!==1)
	  {
		foreach ($curr_code as $value)
		{
		 $this->currId= $value->currency_idcurrency;
		 $this->currencyCode=$value->currencyCode;
		}
	  }
    else {
      $this->currId=1;
    	$this->currencyCode='EUR';
		}
		

		$this->language = $this->session->userdata('language');
		$this->student_status = $this->session->userdata('student_logged_in');
		$this->course=$this->user_model->get_courses($this->language);
		$this->menu['top_menu']=$this->user_model->get_top_menu($this->language);
    }
	
	function currTest()
	{
		echo   $this->currId=1;
 echo    	$this->currencyCode='EUR';
	}

function mailTest(){
	
		$this->load->library('email');
	$tomail = 'deeputg1992@gmail.com';
					
					  $emailSubject = "<h2>Password Reset</h2>";
					  $mailContent = "<p>Plese <a href= 'http://staging.trendimi.com/newversion/user/usermanager/resetPassword/123'>click here</a> to reset your account password<p>";
						   	
					  $this->email->from('ajithupnp@gmail.com', 'Team Trendimi');
					  $this->email->to($tomail); 
					  $this->email->cc(''); 
					  $this->email->bcc(''); 
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					 $this->email->send();
					 echo "done";
					  

	
	}
	function ajaxTest()
	{
		$this->load->view('user/ajaxTest');
	}
	function getText()
	{
		echo 'deepu';
	}
	
	function Ebooks()
	{
		$this->load->model('ebook_model');
		
		if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$type = 'public';
			$user_id='';
		}
		else
		{
			$type = 'user';
			$user_id = $this->session->userdata['student_logged_in']['id'];
			$ebDetails['userId'] =$user_id;
		}
		
		//echo $ebDetails['userId'];exit;
		$ebArray = $this->ebook_model->fetchEbookByLang($this->language);
		if(!empty($ebArray))
		{
			$i = 0;
			foreach($ebArray as $row)
			{
				
				$ebDetails['ebId'][$i] = $row->ebid;
				$ebDetails['ebName'][$i] = $row->ebookName;
				$ebDetails['language'][$i] = $row->language;
				$ebDetails['description'][$i] = $row->description;
				$ebDetails['fileName'][$i] = $row->fileName;
				$ebDetails['sample_ebook_name'][$i] = $row->sample_ebook_name;
				$ebDetails['courseId'][$i] = $row->courseId;
				$ebDetails['picPath'][$i] = $row->image_name;
				
				
				$prodectId = $this->common_model->getProdectId('ebooks');	
				$ebookPrice =$this->common_model->getProductFee($prodectId,$this->currId);
				$ebDetails['fake_amount'][$i] =$ebookPrice['fake_amount'];
				$ebDetails['amount'][$i] =$ebookPrice['amount'];
				
				
					
				
				
				
			$i++;
			}
		}
		//echo "<pre>";print_r($ebDetails);print_r($ebookPrice);exit;
		
 		$data['view'] = 'trendimiebooks_test';
        $data['content'] = $ebDetails;
		if($user_id=='')
        $this->load->view('user/outerTemplate',$data);
		else
		$this->load->view('user/innerTemplate',$data);
		
	}
	
	
	function addEbookToCart($ebId)
	{
		$this->load->model('ebook_model');
		$sessionData['sessionId'] = session_id();
		$this->session->set_userdata($sessionData);
		/*echo $sessionData['sessionId'];
		echo "<br>sessionId in session".$this->session->userdata('sessionId');*/
		$tempDetails['session_id']=$this->session->userdata('sessionId');
		$tempDetails['ebook_id']=$ebId;
		if(isset($this->session->userdata['student_logged_in']['id']))
		$tempDetails['user_id']=$this->session->userdata['student_logged_in']['id'];
		
		
		
		$ebookTempId = $this->ebook_model->addEbookCart($tempDetails);
		if(isset($ebookTempId))
		echo $ebookTempId;
		else
		echo 0;
		
	}
	function removeEbookFromCart($ebId)
	{
		$this->load->model('ebook_model');
		$sessionData['sessionId'] = session_id();
		$this->session->set_userdata($sessionData);
		
		$tempDetails['session_id']=$this->session->userdata('sessionId');
		$tempDetails['ebook_id']=$ebId;
		
		$ebookTempId = $this->ebook_model->removeEbookCart($tempDetails);
		if(isset($ebookTempId))
		echo $ebookTempId;
		else
		echo 0;
		
	}
	function checkEbookTemp()
	{
		$this->load->model('ebook_model');
		$sessionData['sessionId'] = session_id();
		$this->session->set_userdata($sessionData);
		$this->db->select('ebook_id');
		$this->db->from('ebook_purchase_temp');
		$this->db->where('session_id',$this->session->userdata('sessionId'));
		
		
		$query = $this->db->get();
		if(!empty($query))
		{
			$i=0;
			$data =array();
			foreach($query->result() as $row)
			{
				$data['ebid'][$i]= $row->ebook_id;
				$i++;
			}
			$data['count']=$i;
		
			echo json_encode($data);  
		}
		else
		{
			$ebid['count']=0;
			echo json_encode($data);
			
		}
		
		
		
			
	}
	
	
//-------------------- selected ebooks listing here
function EbookCart()
{
	
		$sessionData['sessionId'] = session_id();
		$this->session->set_userdata($sessionData);
		
		$this->load->model('ebook_model');
		
		if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$type = 'public';
			$user_id ='';
		}
		else
		{
			$type = 'user';
			$user_id = $this->session->userdata['student_logged_in']['id'];
			//echo "entered here";exit;
		}
		//echo $user_id;exit;
		
		
		$ebArray = $this->ebook_model->fetchEbookTemp($user_id,$this->session->userdata('sessionId'));
		//echo "<pre>";print_r($ebArray);exit;
		if(!empty($ebArray))
		{
			$i = 0;
			foreach($ebArray as $row)
			{
				
				$ebDetails['tempId'][$i] = $row->id;
				$ebDetails['ebId'][$i] = $row->ebook_id;
				$ebDetails['session_id'][$i] = $row->session_id;
				$ebDetails['user_id'][$i] = $row->user_id;
				
				$ebDetails['ebookName'][$i] = $this->ebook_model->fetchEbookById($ebDetails['ebId'][$i]);
				
				
			$i++;
			}
			$prodectId = $this->common_model->getProdectId('ebooks','',$i);	
				$ebookPrice =$this->common_model->getProductFee($prodectId,$this->currId);
				$ebDetails['fake_amount']=$ebookPrice['fake_amount'];
				$ebDetails['amount']=$ebookPrice['amount'];
			
		}
		//echo "<pre>";print_r($ebDetails);print_r($ebookPrice);exit;
		
 		$data['view'] = 'eBookCartList';
        $data['content'] = $ebDetails;
		
        $this->load->view('user/outerTemplate',$data);
	
}
	
	function deleteEbookFromCart($tempId)
	{
		$this->load->model('ebook_model');
						
		$ebookTempId = $this->ebook_model->deleteEbookCart($tempId);
		
		redirect('deeps_home/EbookCart','refresh');
		
	
	
	}
	
	
	function afterBuyEbook()
	{
		$this->load->model('ebook_model');
		
		//echo "<pre>";
		//print_r($_REQUEST);exit;
		$success=1;
		if($success)
		{
		if(isset($this->session->userdata['student_logged_in']['id']))
		$userId1=$this->session->userdata['student_logged_in']['id'];
		else
		$userId1='';
		
			
			$tempArray = $this->ebook_model->fetchEbookTemp($userId1,$this->session->userdata('sessionId'));
			//$newArr = $tempArray->row();
			$i=0;
			$ebookids ='';
			$ebData  = array();
			foreach($tempArray as $row)
			{
				
				
				if($ebookids=='')
				$ebookids =$row->ebook_id;
				else
				{
				$ebookids .= ','.$row->ebook_id;
				}
				
				$ebData['user_id'] = $row->user_id;
				
				
			$i++;	
			}
			$prdctId = $this->common_model->getProdectId('ebooks','',$i);
			$dateNow =date('Y-m-d');
						
			$subscriDetails['user_id'] =$ebData['user_id'];
			$subscriDetails['product_id'] = $prdctId;
			$subscriDetails['ebook_id'] = $ebookids;
			$subscriDetails['date_purchased'] = $dateNow;
			
			if($subscriDetails['user_id']!=0)
			$subscriptionId = $this->ebook_model->addSubscription_user($subscriDetails);
			else
			$subscriptionId = $this->ebook_model->addSubscription_public($subscriDetails);
									
			$currCode='EUR';
			$paymantDetails=array();
			$paymantDetails['product_id']=$prdctId;
			$paymantDetails['transaction_id']=$transaction_id=104644;
			$paymantDetails['amount']=$transAmount=200;			
			$paymantDetails['currency_id']=$this->common_model->get_currencyId_byCode($currCode);
			if($subscriDetails['user_id']!=0)
			$paymantDetails['user_id']=$subscriDetails['user_id'];
			$paymantDetails['date']=$dateNow;
			
			$paymentId = $this->user_model->add_payment($paymantDetails);
			
			if(isset($paymentId))
			{/*
				$this->load->library('email');
				$this->load->model('email_model');
				
				$row_new = $this->email_model->getTemplateById(8);
				foreach($row_new as $row1)
				{
					
					$emailSubject = $row1->mail_subject;
					$mailContent = $row1->mail_content;
				}
					if($subscriDetails['user_id']!=0)
					{
					$studArr = $this->student_model->get_student_name($subscriDetails['user_id']);
					foreach($studArr as row2)
					{
						$studentdata['first_name'] = $row2->first_name;
						$studentdata['email'] = $row2->email;
					}
				 	$mailContent = str_replace ( "#firstname#",$studentdata['first_name'], $mailContent );
					}
					else
					$mailContent = str_replace ( "#firstname#","User", $mailContent );
					
					
					$liks ="";
					for($z=0;$z<$i;$z++)
					{
						$this->ebook_model->fetchEbookpathById($mailData['ebIOds'][$i]);
						
						if($liks=="")
						$liks="<a href=".base_url()."public/user/ebooks/".$eBooksDetails['path']> ".$eBooksDetails['name']." </a>";
						else
						$liks .="<a href=".base_url()."public/user/ebooks/".$eBooksDetails['path']>, ".$eBooksDetails['name']." </a>";
						
					}
					
					$mailContent = str_replace ( "#click here#",$liks, $mailContent );
					
							  
				  
					$tomail = $studentdata['email'];
					
					$this->email->from('info@trendimi.com', 'Team Trendimi');
					$this->email->to($tomail); 
					$this->email->cc(''); 
					$this->email->bcc(''); 
					$this->email->subject($emailSubject);
					$this->email->message($mailContent);	
					  
					$this->email->send();*/
			redirect('home/EbookBought','refresh');
		}
				
			
		}
		
	
	}
	
	function prodDetails($prodectId)
	{
		$this->load->model('course_model');
		$prod = $this->common_model->getProductDetail($prodectId,$this->currId);
		echo "<pre>";print_r($prod);
	}

//*** generate pdf script ******//
	function pdf()
	{
	 //$this->load->helper('file');
     $this->load->helper(array('dompdf', 'file'));
     // page info here, db calls, etc.     
     //$html = $this->load->view('controller/viewfile', $data, true);
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>certificate</title>
		<link href="public/certificate/css/certificate-style.css" type="text/css" rel="stylesheet" />
		</head>
		
		<body>
		<div class="raper">
		<div id="certificate">
		<h1> Certificate in Style Me</h1>
		<h2> Bhagath Prasad</h2>
		<h3>Style Me</h3>
		<h4> Pass </h4>
		<h4> 12-03-2013</h4>
		<h4> 12345-100-1</h4>
		<div style="clear:both"></div>
		</div>
		</div>
		<div style="clear:both"></div>
		</body>
		</html>
		';

     // case1 : thisone used for download pdf file************************
	 
	 	 //$data = pdf_create($html, 'certicate_name');
	  
	 // case2 : thisone used saving the pdf file on disc (or for email attachment)************************ 
    
     	$data = pdf_create($html, 'cert', false);	
		
		echo "<pre>";
		print_r($data);
		exit;
		
		$this->path = "public/certificate/hardcopy/cert.pdf";
		write_file($this->path, $data);
     
		
		
		
		// end case2 ******************************	
		$sendemail = true;
		
		if($sendemail)
		{
			$this->load->library('email');
			$tomail = 'ajithupnp@gmail.com';
					
					  $emailSubject = "certificate is attached";
					  $mailContent = "<p>Check if there is an attachemnt with mail. If its there you are lucky ;) <p>";
						   	
					  $this->email->from('info@trendimi.com', 'Team Trendimi');
					  $this->email->to($tomail); 
					  $this->email->attach($this->path);
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					 $this->email->send();
			}
	}
	
	//*** generate excel script ******//
	
	function generateExcel(){
		$this->load->library('export');
		//$this->load->model('user_model');
		$sql = $this->user_model->get_courses(4);
		/*echo "<pre>";
		print_r($sql);
		exit;*/
		
		$this->export->to_excel($sql, 'test_excel'); 
	}
	
	//*** generate excel script 2 we will use this ******//
	function excelExport(){
		$this->load->helper(array('php-excel'));
		$sql = $this->user_model->get_courses(4);
	   	$fields = (	$field_array[] = array ("ID", "Course Name", "Summary")  );
	   
	   	foreach ($sql as $row)
			 {
			 $data_array[] = array( $row->course_id, $row->course_name, $row->course_summary );
			 }
			 
		   $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "course_list" );
	}
	
	// **** encode and decode ********
	function excelExport2(){
		$this->load->helper(array('php-excel'));
		$sql = $this->student_model->student_excel();
	   	$fields = (	$field_array[] = array ("Sl No", "Student Name","Email","Course name","Date enrolled","Date expiry")  );
	  // $i=1;
	   	foreach ($sql as $row)
			 {
				 
			 $data_array[] = array($row->first_name,$row->first_name, $row->email, $row->course_name,$row->date_enrolled,$row->date_expiry );
			 //$i++;
			 }
			 
			 /*echo "<pre>";
			 print_r($field_array);
			 
			 echo "<pre>";
			 print_r($data_array);
			 exit;*/
			 
		   $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "course_list" );
	}
	
	function encodetext()
	{
		
		$this->load->library('encrypt');
		$text = $this->uri->segment(3);	
		echo "encoding ' ".$text; echo " '<br>";	
		
		echo "encoded value -";
		
		echo $encoded = $this->encrypt->encode($text);echo "<br>"; 
		
		echo "decoding ' ".$encoded; echo " '<br>";	
		
		echo "decoded value - ' ";
		
		echo $encoded = $this->encrypt->decode($encoded); echo " ' <br>";
		
	}
	
	
	
	//**** import records from excel sheet ******/
	// this one works !! :)
	function phpexcel(){
		
		$this->load->helper(array('phpexcel'));
		
		$excelrecords = excelReader('public/admin/uploads/couponcodes/vouchersample.xlsx');
		echo "<pre>";
		print_r($excelrecords); 
		
		 for($i=0;$i<count($excelrecords);$i++)
				{	
					$voucher_data[] 	 =$excelrecords[$i][0];
				}
			echo "<pre>";
		print_r($voucher_data); 
	}
	
	function apply_certificate($course_id)
  	{
  		$user_id = $this->session->userdata['student_logged_in']['id'];	
			
		$this->user_model->insert_certificate_request($course_id,$user_id);
		
		$data=array("course_status"=>'4'); // change status to Certificate applied
		
		$this->user_model->update_student_enrollments($course_id,$user_id,$data);
		
		
		
		 $stud_details=$this->user_model->get_stud_details($user_id);	
		 
		  foreach($stud_details as $val2)
		  {
			 $user_email= $row->email;
			 $user_name = $row->first_name;
			 $lang_id = $row->lang_id;
		  }
		
		   $course_name = $this->common_model->get_course_name($course_id);
		   
		   $mail_for = "cerificate_approved";
			$email_details = $this->email_model->getTemplateById($mail_for,$lang_id);
			foreach($email_details as $row)
			{
				
				$email_subject = $row->mail_subject;
				$mail_content = $row->mail_content;
			}
			
			
			$tomail = $user_email;
			
		
	    	$mail_content = str_replace ( "#first_name#", $user_name, $mail_content );
			$mail_content .= str_replace ( "#course_name#", $course_name, $mail_content );
			//$mailContent = str_replace ( "#Details#", $path, $mailContent );
			
			$this->load->library('email');
		
			$this->email->from('info@trendimi.com', 'Team Trendimi');
			$this->email->to($tomail); 
			$this->email->cc(''); 
			$this->email->bcc(''); 
			
			$this->email->subject($email_subject);
			$this->email->message($mail_content);	
			
			$this->email->send();
		
		 redirect('coursemanager/certificate_pre', 'refresh');
  
  	}
	
}
	