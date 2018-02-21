<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class process_payment_sales extends CI_Controller
{	

	private $ec_action = 'Sale';
 	 
	function __construct()
	{

		parent::__construct();
		
		
		
		$paypal_details = array(		
		
			// test account
			'API_username' => 'bhagat_1322118867_biz_api1.yahoo.com', 
			'API_signature' => 'AHM6a5O0X5frOYSETt40CccvXK0eA0zLNcF63Xqt.YdkGubrA5xlDtXC', 
			'API_password' => '1322118928',
			// 'sandbox_status' => false,
		);
		
		
		
		$this->load->library('session');
		$this->load->helper(array('form'));
		$this->load->model('user_model','',TRUE);
		$this->load->model('offer_model','',TRUE);	
		$this->load->model('sales_model','',TRUE);	
		//$this->load->model('email_model','',TRUE);
		$this->load->model('ebook_model','',TRUE);
		$this->load->model('certificate_model','',TRUE);
		$this->load->model('discount_code_model','',TRUE);
		$this->load->library('paypal_ec', $paypal_details);
		
    }
	
 	public function index()
 	{
 		
 	}
 	
	public function prepay()
	{
				
		$this->load->model('common_model','',TRUE);
		$this->load->model('course_model','',TRUE);
		$this->load->model('sales_model','',TRUE);
		
	//	$cart_session_id = $this->input->post('cart_session_id');
	
		$user_id     = $this->uri->segment(3);
		$currency_id = $this->uri->segment(4);
		
		/*echo "User id ".$user_id;
		echo "<br> Currency id ".$currency_id;
		echo "<br> cart sessin id ".$this->session->userdata('cart_session_id');	*/
		/*$user_id = $this->input->post('user_id');
		$currency_id = $this->input->post('currency_id');*/
		
		
		$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
		
		foreach($cart_main_details as $cart_main)
		{
			$amount = $cart_main->total_cart_amount;
		
		}
		
		$currency_details = $this->common_model->get_currency_details($currency_id);
		
		/*echo "<pre>";
		print_r($cart_main_details);
		
		echo "<pre>";
		print_r($currency_details);*/
			
				
				$payment_conf = array(
				'desc' => 'Sales cart',
				'currency' => $currency_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('process_payment_sales/processpay/'.$user_id), 
				'cancel_URL' => site_url('/home/sales_check_out'), 
				);
				
				$product = array(
				'name' => 'Sales cart', 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $amount
				);
				
				$payment_conf['products'][] = $product;
				
			/*	echo "<pre>";
		print_r($product);
		
		
		echo "<pre>";
		print_r($payment_conf);*/
		
		
		
			$set_ec_return = $this->paypal_ec->set_ec($payment_conf);
		if (isset($set_ec_return['ec_status']) && ($set_ec_return['ec_status'] === true)) {
			// redirect to Paypal
			$this->paypal_ec->redirect_to_paypal($set_ec_return['TOKEN']);
			// You could detect your visitor's browser and redirect to Paypal's mobile checkout
			// if they are on a mobile device. Just add a true as the last parameter. It defaults
			// to false
			// $this->paypal_ec->redirect_to_paypal( $set_ec_return['TOKEN'], true);
		} else {
			$this->_error($set_ec_return);
		}	
				
			
		} // end prepay()
		
	function processpay() {
		
		$token = $_GET['token'];
		$payer_id = $_GET['PayerID'];
		
		$user_id = $this->uri->segment(3);
		$arg_1 = $this->uri->segment(4);
		$arg_2 = $this->uri->segment(5);
		
		
		
	//	echo "User id ".$user_id."<br>";
		
		// GetExpressCheckoutDetails
		$get_ec_return = $this->paypal_ec->get_ec($token);
		/*echo "<pre>";
		print_r($get_ec_return);exit;
		
*/		if (isset($get_ec_return['ec_status']) && ($get_ec_return['ec_status'] === true)) {
	//echo "entered in 1<br>";
			
			$ec_details = array(
				'token' => $token, 
				'payer_id' => $payer_id, 
				'currency' => $get_ec_return['PAYMENTREQUEST_0_CURRENCYCODE'], 
				'amount' => $get_ec_return['PAYMENTREQUEST_0_AMT'], 
				'IPN_URL' => site_url('processpayment/ipn'), 
				// in case you want to log the IPN, and you
				// may have to in case of Pending transaction
				'type' => $this->ec_action);
				
			// DoExpressCheckoutPayment
			$do_ec_return = $this->paypal_ec->do_ec($ec_details);
			if (isset($do_ec_return['ec_status']) && ($do_ec_return['ec_status'] === true)) {
				
					
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						//$paymantDetails['product_id']=$this->common_model->getProdectId('course',$arg_2);
						
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						//$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;
						$paymantDetails['discount_id'] =''; 
						$paymantDetails['discount_applied'] ='no';	
						$paymantDetails['type']='sales';	
						//$paymantDetails['user_id']=$arg_1; need to update user id after 
						$paymantDetails['date']=$dateNow;
						$paymantDetails['sales_session_id']=$this->session->userdata('cart_session_id');
						
						
			if($this->session->userdata('cart_session_id'))
			{
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));	
				
				/*echo "<pre>";
				print_r($cart_main_details);*/
				
				foreach($cart_main_details as $cart_main)
				{		
					$cart_main_id = $cart_main->id;			
					$products_in_cart = $this->sales_model->get_cart_items($cart_main->id);
					
					/*echo "<pre>";
					print_r($products_in_cart);
					*/
					
					foreach($products_in_cart as $prod)
					{
						/*$product_details = $this->common_model->get_product_details($prod->product_id);
						$product_name[$q] =  $product_details[0]->type;*/
						
						$paymantDetails['amount'] = $prod->item_amount;
						$product_id  = $prod->product_id;
						$paymantDetails['product_id'] = $prod->product_id;
						$cart_item_details = $this->sales_model->get_cart_items_details($cart_main_id,$prod->id);
						
						$user_id = $this->session->userdata['student_logged_in']['id'];
						$student_data = $this->user_model->get_student_details($user_id);									
						$lang_id = $student_data[0]->lang_id;
						
						$paymantDetails['user_id'] = $user_id;
						/*echo "Payment details";
						echo "<pre>";
						print_r($paymantDetails);	
						exit;*/
						
							foreach($cart_item_details as $cart_det)
							{
								
								$ebookids ='';
								if($cart_det->product_type == 'extension')
								{
									//echo ($cart_det->product_type);
									//echo "<br>Extension";
																		 
									 $course_id = $cart_det->selected_item_ids;	
									 
									/* echo "<br>Course id ".$course_id;
									 exit;*/
										
									 $extension_id = $this->user_model->get_extension_id($product_id);
										
										$extension_period = $this->user_model->get_extension_details($extension_id);
										foreach($extension_period as $key =>$row)
										{
											$period = $row->extension_days;
										}
										$today = date("Y-m-d");
										// $accessdate=date("Y-m-d", strtotime("+$period days"));
									//	 $status = '1'; // studying
										
										 
										 $userCoursesArr=$this->user_model->getcourses_student_expiry($user_id,$course_id);
										 
										 
										 foreach($userCoursesArr as $det)
										 {
										 
											$cur_expiry_date = $det->date_expiry;
											$course_status = $det->course_status;
										 }
										
										 
										 if($cur_expiry_date > $today)
										 {
											
											 $accessdate=date('Y-m-d', strtotime($cur_expiry_date. ' + '.$period.' days'));
											
										 }
										 else
										 {
											
											  $accessdate=date('Y-m-d', strtotime($today. ' + '.$period.' days'));
											 $accessdate=date("Y-m-d", strtotime("+$period days")); 
											
										 }
										
										
										  $status = '0';  // Change expired filed to 0
										  
									if($course_status=='7')
									{
										$cr_status = '1';
									$update_data=array("date_expiry"=>$accessdate,"course_status"=>$cr_status,"expired"=>$status);	
									}
									else
									{
										 
										$update_data=array("date_expiry"=>$accessdate,"expired"=>$status);
									}
									/*echo "<br>Upadting array";
									echo "<pre>";
									print_r($update_data);*/
									
									  if($this->session->userdata('coupon_applied'))
				                      {					
					               if($this->session->userdata['coupon_applied_details']['coupon_applied_product']=='extension')
					                 {	
						$paymantDetails['discount_id'] =$code_applied_details['discount_id'] = $this->session->userdata['coupon_applied_details']['discount_code_id']; 
						$paymantDetails['discount_applied'] ='yes';
					                 }				
				                      }	
									
									
									  $paymentId = $this->user_model->add_payment($paymantDetails);
									   if($this->session->userdata('coupon_applied'))
				{					
					if($this->session->userdata['coupon_applied_details']['coupon_applied_product']=='extension')
					{		
							$code_applied_details['user_id'] = $user_id;
							$code_applied_details['source'] = 'sales';							
							$code_applied_details['sales_source'] = $this->sales_model->get_cart_source_from_session_id_discount($this->session->userdata('cart_session_id'));
							$code_applied_details['selected_item_id'] = $cart_det->selected_item_ids;	
							$code_applied_details['product_id'] = $product_id;																					
							$code_applied_details['payment_id'] = $paymentId;							
							$this->discount_code_model->add_discount_applied_details($code_applied_details);
					}				
				}
									   
									
									 $this->user_model->update_student_enrollments($course_id,$user_id,$update_data);					  
								
									 $today = date("Y-m-d");
							  $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>$cart_det->product_type,"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$paymentId);	 
							  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
									
									
									
									
								}
								
								
								if($cart_det->product_type == 'colour_wheel_hard' || $cart_det->product_type == 'colour_wheel_soft' )
								{
									/*echo ($cart_det->product_type);
									echo "<br>Colour wheelllll";*/
								
							  $this->load->library('email');
									
							
							  
							  $paymentId = $this->user_model->add_payment($paymantDetails);
							  
							   $today = date("Y-m-d");
							  $insert_data=array("user_id"=>$user_id,"course_id"=>'',"type"=>$cart_det->product_type,"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$paymentId);	 
							  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
							  
							  if($cart_det->product_type == 'colour_wheel_soft')
							  {
							 	$colour_wheel_type ='downloadable';
							  }
							  else
							  {
								  $colour_wheel_type ='downloadable and hard';
							  }
							  
							  $user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);
									
									  $to_mail = $student_data[0]->email;
									//  $to_mail = 'ajithupnp@gmail.com';
									 // $to_mail = 'jane@trendimi.net';
									  $emailSubject = "Successfully purchased colour wheel ".$colour_wheel_type." copy";
									  $mailContent = "Hi, ".$student_data[0]->first_name;
									  
						    $mailContent .= "<p>Thanks you for your order.</p>";
							$mailContent .= "<p>You have successfully purchased colour wheel ".$colour_wheel_type." copy</p>";
							$mailContent .= "<p>You can download your colour wheel from this link <a href='".base_url()."/download_products'>click here</a></p>";
							$mailContent .= "<br>Happy styling! <br>Thanks,<br><strong>Team Eventtrix</strong>";


											
									  $this->email->from('info@eventtrix.com', 'Team Eventtrix');
									  $this->email->to($to_mail); 
									 // $this->email->attach($this->path);
									  
									  $this->email->subject($emailSubject);
									  $this->email->message($mailContent);	
									  
									 $this->email->send();
									 $this->email->clear(TRUE);
							  
							  
							  
							  if($cart_det->product_type == 'colour_wheel_hard')
							  {
								  
								  
								  
								  
								 
									$to_mail = 'info@eventtrix.com';									
									//$to_mail = 'ajithupnp@gmail.com';
								   $to_mail = 'certificates@eventtrix.com';
									$user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);
									
									  $emailSubject = "Colour wheel Hard copy request : ".$student_data[0]->email;
									  $mailContent = "<p>User details of colour wheel hard copy applied, <p>";
									  
									  $mailContent .= "<p>User name  : ".$student_data[0]->first_name." ".$student_data[0]->last_name."</p>";
									  $mailContent .= "<p>House Number :  ".$student_data[0]->house_number."</p>";
									  $mailContent .= "<p>Address :  ".$student_data[0]->address."</p>";					  
									  $mailContent .= "<p>City :  ".$student_data[0]->city."</p>";
									  $mailContent .= "<p>Zip code :  ".$student_data[0]->zipcode."</p>";
									  $mailContent .= "<p>Country : ".$user_country_name."</p>";
											
									  $this->email->from('info@eventtrix.com', 'Team eventtrix');
									  $this->email->to($to_mail); 
									 // $this->email->attach($this->path);
									  
									  $this->email->subject($emailSubject);
									  $this->email->message($mailContent);	
									  
									 $this->email->send();
									 $this->email->clear(TRUE);
							  }
									
									
									
									
								}
								
								
								if($cart_det->product_type == 'course')
								{						
									
									$this->load->model('course_model','',TRUE);
									$this->load->model('payment_model','',TRUE);
									
									$course_ids = explode(',',$cart_det->selected_item_ids);
									
									for($cr=0;$cr<(count($course_ids));$cr++)
									{
										$courseId = $course_ids[$cr];
										//echo "<br> Course id ".$course_id;	
																										
										$langId = $this->course_model->get_lang_course($courseId);
										$dateNow =date('Y-m-d');
										
										//$user_name = $this->common_model->get_user_name($userId);
										//$course_name = $this->common_model->get_course_name($courseId); 										
									
										$expirityDate = $this->user_model->findExpirityDate($courseId,$dateNow);
										$usersUnit = $this->user_model->get_courseunits_id($courseId);
										foreach($usersUnit as $row)
										{
											$un[$row->units_order] = $row->course_units;
										}
										$student_courseData['student_course_units'] = serialize($un);
										
										
										$student_courseData['course_id'] = $row->course_id;
										$student_courseData['user_id'] = $user_id;
										$student_courseData['date_enrolled'] = $dateNow;
										$student_courseData['date_expiry'] = $expirityDate;
										$student_courseData['enroll_type'] = 'payment';
										$student_courseData['course_status'] = '0';
										
										$courseEnrId = $this->user_model->add_course_student($student_courseData);
										
										$resumeLinkArr['user_id']=$user_id;
										$resumeLinkArr['course_id']=$courseId;
										$resumeLinkArr['resume_link']='coursemanager/studentcourse/'.$courseId;
										$this->user_model->addResumeLink($resumeLinkArr);
										
									}
										
										if($this->session->userdata('coupon_applied'))
				{					
					if($this->session->userdata['coupon_applied_details']['coupon_applied_product']=='course')
					{	
						$paymantDetails['discount_id'] =$code_applied_details['discount_id'] = $this->session->userdata['coupon_applied_details']['discount_code_id']; 
						$paymantDetails['discount_applied'] ='yes';
					}				
				}	
										$paymentId = $this->user_model->add_payment($paymantDetails);
									
									
									 if($this->session->userdata('coupon_applied'))
				{					
					if($this->session->userdata['coupon_applied_details']['coupon_applied_product']=='course')
					{		
							$code_applied_details['user_id'] = $user_id;
							$code_applied_details['source'] = 'sales';							
							$code_applied_details['sales_source'] = $this->sales_model->get_cart_source_from_session_id_discount($this->session->userdata('cart_session_id'));
							$code_applied_details['selected_item_id'] = $cart_det->selected_item_ids;	
							$code_applied_details['product_id'] = $product_id;																					
							$code_applied_details['payment_id'] = $paymentId;							
							$this->discount_code_model->add_discount_applied_details($code_applied_details);
					}				
				}
									
									
										// update user id in payment table
										/*$upArray['user_id']=$user_id ;
										$this->payment_model->userId_updation($upArray,$paymentId);*/
																			
											$this->load->library('email');
											$this->load->model('email_model');
											$this->load->library('encrypt');
											
											$en_studId = $this->encrypt->encode($user_id);//encoding student id
											
											$course_name = '';
											for($cr=0;$cr<(count($course_ids));$cr++)
											{
												$courseId = $course_ids[$cr];									
												if($course_name == '')
												{
													$course_name = $this->common_model->get_course_name($courseId); 
												}
												elseif($cr==((count($course_ids))-1))
												{
													$course_name .='&nbsp;and &nbsp;'.$this->common_model->get_course_name($courseId); 
												}
												else
												{
													$course_name .=','.$this->common_model->get_course_name($courseId); 
												}
													
											}
											
											
											$row_new = $this->email_model->getTemplateById('new_course',$langId);
											
											foreach($row_new as $row1)
											{
												
												$emailSubject = $row1->mail_subject;
												$mailContent = $row1->mail_content;
											}
												$mailContent = str_replace ( "#firstname#",$student_data[0]->first_name, $mailContent );
												$mailContent = str_replace ( "#course_name#",$course_name, $mailContent );
																					  
											  
												$to_mail = $student_data['0']->email;
											//	$to_mail = 'ajithupnp@gmail.com';	
												// $to_mail = 'jane@trendimi.net';
												$this->email->from('info@eventtrix.com', 'Team eventtrix');
												$this->email->to($to_mail); 
												$this->email->cc(''); 
												$this->email->bcc(''); 
												$this->email->subject($emailSubject);
												$this->email->message($mailContent);	
												  
												$this->email->send();						
												$this->email->clear(TRUE);
									
								}
								
								if($cart_det->product_type == 'ebooks')
								{
									$this->load->library('email');
									$this->load->model('email_model');
									
									//echo "<br>Ebook";
								//	echo "<br>Ebook ids ".$cart_det->selected_item_ids;
									
									
									$ebook_ids = $cart_det->selected_item_ids;
									
									if($this->session->userdata('coupon_applied'))
				{					
					if($this->session->userdata['coupon_applied_details']['coupon_applied_product']=='ebooks')
					{	
						$paymantDetails['discount_id'] =$code_applied_details['discount_id'] = $this->session->userdata['coupon_applied_details']['discount_code_id']; 
						$paymantDetails['discount_applied'] ='yes';
					}				
				}	
									
									
									
									
									$paymentId = $this->user_model->add_payment($paymantDetails);
									
									 if($this->session->userdata('coupon_applied'))
				{					
					if($this->session->userdata['coupon_applied_details']['coupon_applied_product']=='ebooks')
					{		
							$code_applied_details['user_id'] = $user_id;
							$code_applied_details['source'] = 'sales';							
							$code_applied_details['sales_source'] = $this->sales_model->get_cart_source_from_session_id_discount($this->session->userdata('cart_session_id'));
							$code_applied_details['selected_item_id'] = $cart_det->selected_item_ids;	
							$code_applied_details['product_id'] = $product_id;																					
							$code_applied_details['payment_id'] = $paymentId;	
							$this->discount_code_model->add_discount_applied_details($code_applied_details);
					}				
				}
									
									
									
									
									
									$ebook_subs_details['user_id'] 		= $user_id;
									$ebook_subs_details['product_id']	 = $product_id;
									$ebook_subs_details['ebook_id'] 	   = $ebook_ids;
									$ebook_subs_details['date_purchased'] = $dateNow;
									$ebook_subs_details['payment_id'] = $paymentId;
									
									
									$ebook_subs_details_id = $this->ebook_model->addSubscription_user($ebook_subs_details);
									
									
									$row_new = $this->email_model->getTemplateById('ebook_download_link',$lang_id);
									foreach($row_new as $row1)
									{
										
										$emailSubject = $row1->mail_subject;
										$mailContent = $row1->mail_content;
									}
										 
									if($lang_id==3)
									{
										$mailContent = str_replace ( "#firstname#",$student_data[0]->first_name, $mailContent );
										$mailContent = str_replace ( "#click here#","<a href='".base_url()."home/ebookDownload/user'>clica aquí</a>", $mailContent );
									}									
									else
									{
										$mailContent = str_replace ( "#firstname#",$student_data[0]->first_name, $mailContent );
										$mailContent = str_replace ( "#click here#","<a href='".base_url()."/home/ebookDownload/user'>click here</a>", $mailContent );
									}
									
									$to_mail = $student_data['0']->email;
								//	$to_mail = 'ajithupnp@gmail.com';
									
									$this->email->from('info@eventtrix.com', 'Team eventtrix');
									
									
									$this->email->to($to_mail); 
									$this->email->cc(''); 
									$this->email->bcc(''); 
									$this->email->subject($emailSubject);
									$this->email->message($mailContent);	
									  
									$this->email->send();
									$this->email->clear(TRUE);									
									
									
								}
								if($cart_det->product_type == 'hardcopy')
								{
								  $this->load->library('email');
								  
								  $course_id = $cart_det->selected_item_ids;
										
								  $today = date("Y-m-d");
								  
								  if($this->session->userdata('coupon_applied'))
				{					
					if($this->session->userdata['coupon_applied_details']['coupon_applied_product']=='hardcopy')
					{	
						$paymantDetails['discount_id'] =$code_applied_details['discount_id'] = $this->session->userdata['coupon_applied_details']['discount_code_id']; 
						$paymantDetails['discount_applied'] ='yes';
					}				
				}	
								  
								  
								  
								  $paymentId = $this->user_model->add_payment($paymantDetails);
								  
								   if($this->session->userdata('coupon_applied'))
				{					
					if($this->session->userdata['coupon_applied_details']['coupon_applied_product']=='hardcopy')
					{		
							$code_applied_details['user_id'] = $user_id;
							$code_applied_details['source'] = 'sales';							
							$code_applied_details['sales_source'] = $this->sales_model->get_cart_source_from_session_id_discount($this->session->userdata('cart_session_id'));
							$code_applied_details['selected_item_id'] = $cart_det->selected_item_ids;	
							$code_applied_details['product_id'] = $product_id;																					
							$code_applied_details['payment_id'] = $paymentId;							
							$this->discount_code_model->add_discount_applied_details($code_applied_details);
					}				
				}
					
								  
								  
								  
								 $certificate_details =  $this->certificate_model->get_certificate_details($user_id,$course_id);
		foreach($certificate_details as $key => $value)
		{
			$applied_date = $value->applied_on;	
			$certificate_id = $value->id;	
		}
		
		 $user_lang_id  = $this->common_model->get_user_lang_id($user_id);
		 
		 if($user_lang_id == 3)
			  {
				   setlocale(LC_TIME, 'es_ES');
			  }
			  else
			  {
				  setlocale(LC_TIME, 'en_EN');
			  }
		
		
		
		 $today = date("Y-m-d");
		 $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'hardcopy',"product_id"=>$product_id,"date_applied"=>$today,'payment_id'=>$paymentId);
		 
		 $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		 
		
		 
		 
		 
		
	 	 $mark_details = $this->get_student_progress($course_id);
	 
	 
	 //progressPercnt
	/* 
	 echo "<pre>";
	 print_r($mark_details);
	 exit;*/
	 $grade='falied';
	 	if($mark_details['progressPercnt'] >= 55 && $mark_details['progressPercnt'] <= 64.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_pass');
		}
		else if($mark_details['progressPercnt'] >= 65 && $mark_details['progressPercnt'] <= 74.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_pass_plus');
		}
		else if($mark_details['progressPercnt'] >= 75 && $mark_details['progressPercnt'] <= 84.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_merit');
		}
		else if($mark_details['progressPercnt'] >= 85 )
	 	{
	 		$grade = $this->user_model->translate_('mark_dist');
		}
		
		if($product_id == 20)
		{
			$postal_type='standard';
		}
		else if($product_id == 22)
		{
			$postal_type = 'express';
		}
			
			 $insert_data_hardcopy =array("student_certificate_id"=>$certificate_id,"user_id"=>$user_id,"course_id"=>$course_id,"hardcopy_apply_date"=>$today,"grade"=>$grade,"completion_date"=>$applied_date,"postal_type"=>$postal_type,"source"=>'sales',"post_status"=>'pending','payment_id'=>$paymentId);
	
	/*echo "<pre>";
	print_r($insert_data_hardcopy);*/
	
	$this->user_model->insertQuerys("certificate_hardcopy_applications",$insert_data_hardcopy);
	
		$sendemail = true;	
    	$stud_details=$this->user_model->get_stud_details($user_id);
		
		$course_name = $this->common_model->get_course_name($course_id); 	
		 
		  foreach($stud_details as $val2)
		  {
			  $certificate_user_name = $val2->first_name.' '.$val2->last_name;
			  $user_first_name = trim($val2->first_name);		
			 $user_country_name = $this->user_model->get_country_name($val2->country_id);
			 $user_house_number = $val2->house_number;
			 $user_address = $val2->address;
			 $user_city = $val2->city;
			 $user_zip_code = $val2->zipcode;
			 $user_mail = $val2->email;
			 
		  }		
	 
    $certificate_user_name = strtolower($certificate_user_name);
	$certificate_user_name = ucwords($certificate_user_name);
		
		
		
		if($product_id == 20)
		{
			$postal_type='Standard Posting';
		}
		else if($product_id == 22)
		{
			$postal_type = 'Express Posting';
		}
		
		
		
		
			/* Style Me course */
		
		if($course_id==1)
		{		
			$coursename= 'Style Me - Personal Stylist';
		}
		else if($course_id==11 )
		{		
			$coursename='Autoimagen';
		}
		else if($course_id==31 )
		{		
			$coursename='Mon Style';
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
		else if($course_id==33 )
		{		
			$coursename='Maquillaje';
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
		
		
		
		//$user_lang_id =3;
		
			// $cssLink = "/newversion/public/certificate/icoes_letter/english-letter.css";
			
			// $cssLink = "http://trendimi.net/public/letters/css/proof_letters.css";
			// $cssLink = "http://staging.trendimi.net/newversion/public/certificate/icoes_letter/english-letter.css";
		
			
		if($product_id == 22)
		{
			
			$this->load->helper(array('dompdf', 'file'));
			  if($user_lang_id == 4)
			  {
				  
			$cssLink = "public/certificate/icoes_letter/english-letter.css";
			   
			  $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Letter</title>
<link rel="stylesheet" href="'.$cssLink.'" type="text/css" />
</head>

<body>
<div class="wrapper">
<div class="inner">
<div class="title">
<ul>
<li>ICOES Foundation</li>
<li>Asterweg 113</li>
<li>1031 HM, AMSTERDAM</li>
<li>The Netherlands</li>
</ul>
</div>
<div class="clear"></div>
<div class="content">
<ul>
<li>'.$certificate_user_name.'</li>
<li>'.$user_house_number.'</li>
<li>'.$user_address.'</li>
<li>'.$user_zip_code.' '.$user_country_name.'</li>
</ul>
<div class="clear"></div>
<p>Dear '.$user_first_name.',</p>
<p>We enclose your certificate of completion for your recently studied '.$coursename.' course with eventtrix.</p>
<p>This course is accredited by ICOES. This accreditation means the course meets strict standards with regard to
high quality content and measurable learning outcomes.</p>
<p>We would like to congratulate you on your success and wish you a very favourable outcome in your career as a
result. We support continuous professional development and would like to take this opportunity to encourage
you to integrate your new training as soon as possible.</p>
<p>Online education is an exciting and effective way to build your skills, and therefore opportunities, in the
marketplace. Relying on accredited institutions guarantees you a high standard of education, all conveniently
structured to suit your life.</p>
<p>We see this method of study as the education of the future. There is a wide range of choices now available in
online training courses, allowing you to keep your skill set competitive, attractive and up to date.</p>
<p>We wish you continued success in your future.</p>
<p>Kind regards,</p>
</div>
</div>
<div class="clear"></div>
<div class="bottom">
<p>ICOES Foundation - Asterweg 113 - 1031 HM - AMSTERDAM - The Netherlands</p>
<p>Registered at the Amsterdam Chamber of Commerce under number 59322373</p>
<p>certificates@icoes.org; www.icoes.org</p>
</div>
</div>
</body>
</html>

	';
    }
    else if($user_lang_id == 3)
    {
			$cssLink = "public/certificate/icoes_letter/spanish-letter.css";
		 $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Letter</title>
<link rel="stylesheet" href="'.$cssLink.'" type="text/css" />
</head>

<body>
<div class="wrapper">
<div class="inner">
<div class="title">
<ul>
<li>ICOES Foundation</li>
<li>Asterweg 113</li>
<li>1031 HM, AMSTERDAM</li>
<li>The Netherlands</li>
</ul>
</div>
<div class="clear"></div>
<div class="content">
<ul>
<li>'.$certificate_user_name.'</li>
<li>'.$user_house_number.'</li>
<li>'.$user_address.'</li>
<li>'.$user_zip_code.' '.$user_country_name.'</li>
</ul>
<div class="clear"></div>
<p>Querido '.$user_first_name.',</p>
<p>Adjuntamos el certificado de su curso de '.$coursename.' con eventtrix.</p>
<p>El curso que ha realizado está acreditado por ICOES. Esta acreditación certificado que el curso cumple con los
estrictos estándares en materia de contenido de alta calidad y con los resultados de aprendizaje reconocidos.</p>

<p>Queremos felicitarle por la realización con éxito del curso y le deseamos una carrera profesional llena de
satisfacciones. Desde ICOES apoyamos el desarrollo profesional continuo, por lo que nos gustaría aprovechar
esta oportunidad para animarle a seguir con su formación tan pronto como le sea posible.</p>

<p>La educación en línea es una manera emocionante y eficaz para obtener una formación de calidad, lo que
facilita el acceso al mercado laboral. ICOES garantiza un alto nivel de educación, conocimiento que se adquiere
gracias a una estructura de formación que se adapta a la vida de los estudiantes.</p>

<p>Vemos este método de estudio como la educación del futuro al facilitar, gracias a una gran variedad de
posibilidades, el aprendizaje continuo y el mantenimiento de las habilidades adquiridas para que sigan siendo
competitivas y atractivas.</p>

<p>Le deseamos mucho éxito en su futuro.</p>
<p>Saludos cordiales,</p>
</div>
</div>
<div class="clear"></div>
<div class="bottom">
<p>ICOES Foundation - Asterweg 113 - 1031 HM - AMSTERDAM - The Netherlands</p>
<p>Registered at the Amsterdam Chamber of Commerce under number 59322373</p>
<p>certificates@icoes.org; www.icoes.org</p>
</div>
</div>
</body>
</html>
	';
		
		  
    }	
	else if($user_lang_id == 6)
    {
			$cssLink = "public/certificate/icoes_letter/french-letter.css";
		 $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Letter</title>
<link rel="stylesheet" href="'.$cssLink.'" type="text/css" />
</head>

<body>
<div class="wrapper">
<div class="inner">
<div class="title">
<ul>
<li>ICOES Foundation</li>
<li>Asterweg 113</li>
<li>1031 HM, AMSTERDAM</li>
<li>The Netherlands</li>
</ul>
</div>
<div class="clear"></div>
<div class="content">
<ul>
<li>'.$certificate_user_name.'</li>
<li>'.$user_house_number.'</li>
<li>'.$user_address.'</li>
<li>'.$user_zip_code.' </li>
<li>'.$user_country_name.'</li>
</ul>
<div class="clear"></div>
<p>Cher  '.trim($user_first_name).',</p>
<p>Vous trouverez ci-joint votre certificat de fin d’étude pour le cours  '.$coursename.' de Trendimi.</p>
<p>Ce cours est accrédité par ICOES. Cette accréditation signifie que le cours est à la hauteur des plus hauts standards en qualité d’enseignement et des résultats d’apprentissage qui en découlent.</p>

<p>Nous souhaitons vous féliciter pour votre réussite et vous souhaitons de rencontrer le succès dans votre future carrière. Nous sommes partisans du développement personnel continu et nous encourageons à cet effet à vous inscrire à de nouvelles formations dès que possible.</p>

<p>L’éducation en ligne est un moyen excitant et très efficace pour développer vos compétences et vous ouvrir à de nouvelles opportunités dans le monde du travail. Un diplôme d’un organisme accrédité vous garanti une formation de qualité utile pour votre évolution professionnelle.</p>

<p>Nous percevons cette méthode d’apprentissage comme la formation du futur. De nombreux cours en ligne sont disponibles, vous permettant de rester compétitif et à jour de ce qu’il se fait.</p>

<p>Nous vous souhaitons de réussir dans votre carrière.</p>
<p>Cordialement,</p>
<p>La fondation ICOES</p>
</div>
</div>
<div class="clear"></div>
<div class="bottom">
<p>ICOES Foundation - Asterweg 113 - 1031 HM - AMSTERDAM - The Netherlands</p>
<p>Registered at the Amsterdam Chamber of Commerce under number 59322373</p>
<p>certificates@icoes.org; www.icoes.org</p>
</div>
</div>
</body>
</html>
	';
		
		  
    }
		  
	/*	echo $html;
		exit;  */
		
		
		
		 		  
		  	$data = pdf_create($html, 'icoes_letter'.$user_id.'_'.$course_id,false);	   		
			$this->path = "public/certificate/icoes_letter_pdfs/icoes_letter_".$user_id."_".$course_id.".pdf";		
			write_file($this->path, $data);
	 
	 
		//	$this->email->clear(TRUE);			
	
	
	
	$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$course_id);
		foreach($certificate_details as $key => $value)
		{
			$applied_date = $value->applied_on;	
			$certificate_id = $value->id;	
		}
		
		$values = explode('-', $applied_date);
		
	if($values[1]=='1')
   {
       $month=$this->user_model->translate_('month_1');
   }
   else if($values[1]=='2')
   {
      $month=$this->user_model->translate_('month_2');
   }
   else if($values[1]=='3')
   {
      $month=$this->user_model->translate_('month_3');
   }else if($values[1]=='4')
   {
      $month=$this->user_model->translate_('month_4');
   }else if($values[1]=='5')
   {
      $month=$this->user_model->translate_('month_5');
   }else if($values[1]=='6')
   {
      $month=$this->user_model->translate_('month_6');
   }else if($values[1]=='7')
   {
      $month=$this->user_model->translate_('month_7');
   }else if($values[1]=='8')
   {
      $month=$this->user_model->translate_('month_8');
   }else if($values[1]=='9')
   {
      $month=$this->user_model->translate_('month_9');
   }else if($values[1]=='10')
   {
      $month=$this->user_model->translate_('month_10');
   }else if($values[1]=='11')
   {
      $month=$this->user_model->translate_('month_11');
   }else
   {
      $month=$this->user_model->translate_('month_12');
   }
	$year=$values[0];
		
		
		
		
	//	$cssLink = '';
		if($user_lang_id==3)
		{
			//$cssLink = base_url()."public/certificate/icoes_certificate/icoes_certificate_spanish.css";
			$cssLink = "public/certificate/icoes_certificate/icoes_certificate_spanish_pdf.css";
		}
		else if($user_lang_id==4)
		{
			//$cssLink = base_url()."public/certificate/icoes_certificate/icoes_certificate.css";
			$cssLink = "public/certificate/icoes_certificate/icoes_certificate_pdf.css";
		}
		if($user_lang_id==6)
		{
			//$cssLink = base_url()."public/certificate/icoes_certificate/icoes_certificate_spanish.css";
			$cssLink = "public/certificate/icoes_certificate/icoes_certificate_french_pdf.css";
		}


$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>certificate</title>
<link href="'.$cssLink.'" type="text/css" rel="stylesheet" />
</head>
<body>
<div class="outer">
<div class="innnr">
<div style="clear:both"></div>
<h2 id="name">'.$certificate_user_name.'</h2>
<h3 class="for">'.$this->user_model->translate_('for_success_completion').'</h3>
<h2 class="course">'.$coursename.'</h2>
<h3 class="with">'.$this->user_model->translate_('course_with').'<span> eventtrix</span> </h3>
<p class="top"><span>'.$this->user_model->translate_('icoes_grade').':</span> ' .$grade.'</p>
<p><span>'.$this->user_model->translate_('date_of_completion').':</span> '.$month.' '.$year.'</h4>
<p><span>'.$this->user_model->translate_('cert_no').':</span> 100-'.$certificate_id.'</p>
</div></div>
</body>
</html>';



/*echo $html;
		exit;
	 $data = pdf_create_align($html, 'TrendimiCertificate_'.$user_id.'_'.$course_id,true,'a4','landscape');   
     write_file('name', $data);	*/
	
			$data = pdf_create_align($html,'icoes_certificate_'.$user_id.'_'.$course_id,false,'a4','landscape');  
			$this->path_2 = "public/certificate/icoes_certificate_pdfs/icoes_certificate_".$user_id."_".$course_id.".pdf";		
			write_file($this->path_2, $data);
			
			
			 $stud_details=$this->user_model->get_stud_details($user_id);	
						 
						  foreach($stud_details as $val2)
						  {
							 $user_country_name = $this->user_model->get_country_name($val2->country_id);
							 $user_house_number = $val2->house_number;
							 $user_address = $val2->address;
							 $user_street = $val2->street;
							 $user_city = $val2->city;
							 $user_zip_code = $val2->zipcode;
							 $user_mail = $val2->email;
							 
							  $first_name = $val2->first_name;
							  $last_name =$val2->last_name;
							 
						  }
						  		
										if($this->session->userdata['ip_address'] == '117.242.194.73')
										{
											$to_mail = 'ajithupnp@gmail.com';
										}
										else
										{
											$to_mail = 'certificates@eventtrix.com';
										}
	 
									//$to_mail = 'info@trendimi.net';
									
								 //  $to_mail = 'ajithupnp@gmail.com';
									//$to_mail = 'bhagathindian@gmail.com';
								//	$to_mail = 'deeputg1992@gmail.com';
									
									//$to_mail = 'jane@trendimi.com';	
										
									  $emailSubject = "Express Certificate Request - ".$first_name." ".$last_name ;
									  $mailContent = "<p>Please find the attachments of hard copy certificate here with it. <p>";
									  
									   $mailContent .= "<p>User name  : ".$certificate_user_name."</p>";
									   $mailContent .= "<p>House Number :  ".$user_house_number."</p>";
									   $mailContent .= "<p>Street :  ".$user_street."</p>";
									   $mailContent .= "<p>Additional Address Line(if any) :  ".$user_address."</p>";				  
									   $mailContent .= "<p>City :  ".$user_city."</p>";
									   $mailContent .= "<p>Zip code :  ".$user_zip_code."</p>";
									   $mailContent .= "<p>Country : ".$user_country_name."</p>";
									   
									   
									  	
	
	
									 $this->email->from('info@eventtrix.com', 'Team eventtrix');
									  $this->email->to($to_mail); 
									  $this->email->attach($this->path);
									  $this->email->attach($this->path_2);
									  
									  $this->email->subject($emailSubject);
									  $this->email->message($mailContent);	
									  
									 $this->email->send();		
									 $this->email->clear(TRUE);
									 // usleep(50);		
									 
									// echo "<br> Mail sent to ---  ".$to_mail."........<br>";	
	
							}
		
		
		
								 $postal_id = $this->certificate_model->get_postal_id($product_id); 
								 $postage_details = $this->certificate_model->get_postage_details($postal_id); 
							
							  foreach($postage_details as $row2)
							  {
								  $postal_name = $row2->postage_type;
							  }
							  
							  $content['postal_name'] = $postal_name;
							
							  foreach ($postage_details as $value) {	
									$postal_estimate_time=  str_replace("&#8226;","",$value->delivery_time);			
								}
								
								 $user_lang_id  = $this->common_model->get_user_lang_id($user_id);
								 
								 /*if($product_id==20)// standard mail
								 {
						*/			 
								 
								 $this->load->library('email');
								 $this->load->model('email_model');
							
								 $row_new = $this->email_model->getTemplateById('iceoes_hardcopy',$user_lang_id);
								foreach($row_new as $row1)
								{
							   
								   $emailSubject = $row1->mail_subject;
								   $mailContent = $row1->mail_content;
								}
							  $mailContent = str_replace ( "#firstname#",$stud_details[0]->first_name, $mailContent );
							 
							  $mailContent = str_replace ( "#courseName#",$course_name,$mailContent); 
							 
							  
							  $mailContent = str_replace ( "#postal_option#", $postal_type, $mailContent );
							  $mailContent = str_replace ( "#delivery_period#", $postal_estimate_time, $mailContent );
						 
							  
						
							//$user_mail = 'ajithupnp@gmail.com';
							//  $user_mail = 'bhagathindian@gmail.com';
							//$user_mail = 'jane@trendimi.com';
						     //$user_mail = $stud_details[0]->email;
							 if($this->session->userdata['ip_address'] == '117.242.194.73')
										{
											$user_mail = 'ajithupnp@gmail.com';
										}
						   
						   
							 
								$this->email->from('info@eventtrix.net', 'Team eventtrix');
							   $this->email->to($user_mail); 
							   $this->email->cc(''); 
							   $this->email->bcc(''); 
							   
							   $this->email->subject($emailSubject);
							   $this->email->message($mailContent); 
							   
							  $this->email->send();
										 
							  $this->email->clear(TRUE);
																
																
							/*if($this->session->userdata['ip_address'] == '117.242.193.126')
							{
								exit;		
							}
								*/	
									
								}
								//transcript
								if($cart_det->product_type == 'transcript' || $cart_det->product_type == 'transcript_hard')
								{
									
								  $this->load->library('email');
								  
								   $course_id = $cart_det->selected_item_ids;
										
								  $today = date("Y-m-d");
																  
								  $paymentId = $this->user_model->add_payment($paymantDetails);
								  
								    $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>$cart_det->product_type,"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$paymentId);	 
							  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
								  
								  if($cart_det->product_type == 'transcript')
								  {
									$certificate_type ='downloadable';
								  }
								  else
								  {
									  $certificate_type ='downloadable and hard';
								  }
								  
								  $user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);
									
									  $to_mail = $student_data[0]->email;
									//  $to_mail = 'ajithupnp@gmail.com';
									 // $to_mail = 'jane@trendimi.net';
									  $emailSubject = "Successfully purchased eTranscript ".$certificate_type." copy";
									  $mailContent = "Hi, ".$student_data[0]->first_name;
									  
									$mailContent .= "<p>Thanks you for your order.</p>";
									$mailContent .= "<p>You have successfully purchased eTranscript ".$certificate_type." copy</p>";
									$mailContent .= "<p>You can download your eTranscript from this link <a href='".base_url()."download_products/'>click here</a></p>";
									$mailContent .= "<br>Happy styling! <br>Thanks,<br><strong>Team eventtrix</strong>";


											
									  $this->email->from('info@eventtrix.com', 'Team eventtrix');
									  $this->email->to($to_mail); 
									 // $this->email->attach($this->path);
									  
									  $this->email->subject($emailSubject);
									  $this->email->message($mailContent);	
									  
									  $this->email->send();
									  $this->email->clear(TRUE);
							  
							  
							  
							  		if($cart_det->product_type == 'transcript_hard')
							 	 	{
								  								 
									//  $to_mail = 'info@trendimi.net';									
									  $to_mail = 'certificates@eventtrix.com';
									 // $to_mail = 'jane@trendimi.net';
									  $user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);
									
									  $emailSubject = "eTranscript Hard copy request : ".$student_data[0]->email;
									  $mailContent = "<p>User details of eTranscript hard copy applied, <p>";
									  
									  $mailContent .= "<p>User name  : ".$student_data[0]->first_name." ".$student_data[0]->last_name."</p>";
									  $mailContent .= "<p>House Number :  ".$student_data[0]->house_number."</p>";
									  $mailContent .= "<p>Address :  ".$student_data[0]->address."</p>";					  
									  $mailContent .= "<p>City :  ".$student_data[0]->city."</p>";
									  $mailContent .= "<p>Zip code :  ".$student_data[0]->zipcode."</p>";
									  $mailContent .= "<p>Country : ".$user_country_name."</p>";
											
									  $this->email->from('info@eventtrix.com', 'Team eventtrix');
									  $this->email->to($to_mail); 
									 // $this->email->attach($this->path);
									  
									  $this->email->subject($emailSubject);
									  $this->email->message($mailContent);	
									  
									 $this->email->send();
									 $this->email->clear(TRUE);
							 	 }
									
									
									
								}
								
								
								
							    //	poe_soft
								if($cart_det->product_type == 'poe_soft' || $cart_det->product_type == 'poe_hard')
								{
									
								  $this->load->library('email');
								  $course_id = $cart_det->selected_item_ids;	
								  $today = date("Y-m-d");
								 
								  
								  $paymentId = $this->user_model->add_payment($paymantDetails);
								  
								    $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>$cart_det->product_type,"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$paymentId);	 
							  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
								  
								  if($cart_det->product_type == 'poe_soft')
								  {
									$certificate_type ='downloadable';
								  }
								  else
								  {
									  $certificate_type ='downloadable and hard';
								  }
								  
								  $user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);
									
									  $to_mail = $student_data[0]->email;
									//  $to_mail = 'ajithupnp@gmail.com';
									 // $to_mail = 'jane@trendimi.net';
									  $emailSubject = "Successfully purchased proof of enroll ".$certificate_type." copy";
									  $mailContent = "Hi, ".$student_data[0]->first_name;
									  
									$mailContent .= "<p>Thanks you for your order.</p>";
									$mailContent .= "<p>You have successfully purchased proof of enroll ".$certificate_type." copy</p>";
									$mailContent .= "<p>You can download your proof of enroll from this link <a href='".base_url()."download_products/'>click here</a></p>";
									$mailContent .= "<br>Happy styling! <br>Thanks,<br><strong>Team eventtrix</strong>";


											
									  $this->email->from('info@eventtrix.com', 'Team eventtrix');
									  $this->email->to($to_mail); 
									 // $this->email->attach($this->path);
									  
									  $this->email->subject($emailSubject);
									  $this->email->message($mailContent);	
									  
									  $this->email->send();
									  $this->email->clear(TRUE);
							  
							  
							  
							  		if($cart_det->product_type == 'poe_hard')
							 	 	{
								  		
										
										
										
									/* Certifcata creation */
									
								  $this->load->helper(array('dompdf', 'file'));
								  $user_id = $this->session->userdata['student_logged_in']['id'];	
								  $user_name = $this->common_model->get_user_name($user_id);	
								  
								  $user_name = strtolower($user_name);
		 						  $user_name = ucwords($user_name);
								  
								  $name = explode('&nbsp;',$user_name);
								
								  $course_name = $this->common_model->get_course_name($course_id); 		 
								  
								  $course_hours  = $this->user_model->get_course_hours($course_id);		 
								  
								  $course_deatails = $this->user_model->getcourses_student_expiry($user_id,$course_id);
								  
								  
								  $stud_details=$this->user_model->get_stud_details($user_id);
		  
								  $gender_pronoun = '';
								  $gender_pronoun_2 = ''; 
								  
								  if($stud_details[0]->gender == 1)
								  {
									 $gender_pronoun = 'him'; 
									 $gender_pronoun_2 = 'his'; 
									 
								  }
								  else if($stud_details[0]->gender == 2)
								  {
									 $gender_pronoun = 'her';
									 $gender_pronoun_2 = 'her'; 
								  }
								  
								  
								  foreach($course_deatails as $details)
								  {
									$expiry_date_date = $details->date_expiry;  
								  }
								  
								  $course_expiry_date = explode('-',$expiry_date_date);
								  
								   
									 $user_lang_id  = $this->common_model->get_user_lang_id($user_id);
								  /*------ send mail to admin */
								  
									  if($user_lang_id == 3)
									  {
										   setlocale(LC_TIME, 'es_ES');
									  }
									  else
									  {
										  setlocale(LC_TIME, 'en_EN');
									  }
								   
								  
								  $expiry_year  = $course_expiry_date[0];
								  $expiry_month = $course_expiry_date[1];
								  $expiry_date  = $course_expiry_date[2];
								  
								 // $expiry_date =1;
								  $month_name  = date("F",$expiry_date);
								  $date_suffix = date("S",strtotime($expiry_date));
								
								  
								   
		  $course_topics = '';
		  
		 if($course_id == 1)
			  {
				  $course_name='Personal Image & Beauty Expert';
				   $course_topics = 'Topics covered by the course include: Personal image, Personal care, Colour, skin tones and head shapes analysis, Make-up, Morphology, Professional products, Fashion.';
			  }
			  elseif($course_id == 2)
			  {
				  $course_name='Personal Shopper';
				  $course_topics = 'Topics covered by the course include: The profession of a personal shopper, Working with client, Colour and body shapes analysis, Hair and make-up, Wardrobe analysis:basics of wardrobe and complimentary accessories, Fashion and trends, Communication and protocol, Career guidance.';
			  }
			  elseif($course_id == 3)
			  {
				  $course_name='Make-up Artist';
				  $course_topics = 'Topics covered by the course include: Different skin types, Foundations and corrections, Colour, light and shade and head shapes analysis, Professional tools and make-up kits, Types of make-up, Eye and eyebrow make-up, False eyelashes, Lip make-up ,Make-up for different ages ,Health and safety .';
			  }
			  elseif($course_id == 4)
			  {
				  $course_name='Wedding Planner';
				  $course_topics = 'Topics covered by the course include: The profession of a wedding planner and types of ceremonies, Working with client, Engagement & bachelor parties, Getting the look and feel right  – venues, music, roles, invitations, guests lists, speeches, Dressing the wedding party, Perfecting essential details – décor, the banquet, gifts, menu, floral arrangements, honeymoon, Summing up – final budget, assessment, Starting and marketing your wedding planner business, Wedding planning resources.';
			  }
			  elseif($course_id == 5)
			  {
				  $course_name='Gel Manicure & Nail Artist';
				  $course_topics = 'The course content outlines the knowledge skills, and dos and don’ts necessary to become a professional nail artist, including manicure, pedicure, correcting problems and latest decoration techniques.';
			  }
			  elseif($course_id == 6)
			  {
				  $course_name='Hair Stylist';
				  $course_topics = 'The course content outlines the knowledge skills, and dos and don’ts necessary to become a professional hair stylist, including hair and scalp analysis, chemical structure of hair, choosing products & tools and styling techniques.';
			  }
			   elseif($course_id == 7)
			  {
				  $course_name='Event Planner';
				  $course_topics = 'Topics covered by the course include: Principles of event management & roles of event manager, Types of events, Working with clients incl. understanding client needs, preparing event proposals,signing contracts, Steps for planning an event incl. budgets, venues, food and beverages,transportation, speakers, General etiquette and protocol incl. invitations, dress codes, table settings andseating arrangements, greeting etiquette, Day of the event and post event evaluation.';
			  }
			  elseif($course_id == 8)
			  {
				  $course_name='Starting Your Business';
				  $course_topics = 'Topics covered by the course include: Market research and competitors analysis, funding and available help, Introduction to marketing, Business structures, legislation and regulations, registering your business, Budget and cash flows, accounting and finance, Insurance, premises, suppliers, staff, Home based businesses, Business plan, Launching your business.';
			  }
			   elseif($course_id == 9)
			  {
				  $course_name='Marketing Your Business';
				  $course_topics = 'Topics covered by the course include: Introduction to marketing, Marketing plan, Low cost marketing techniques, Developing your brand, Setting up and managing website, Social media and online marketing, Public relations and advertising, Sales campaigns and leads generation.';
			  }
			  
			  elseif($course_id == 11)
			  {
				  $course_name='Autoimagen';
				  $course_topics = 'Con este curso se adquiere el conocimiento para poder convertirse en un profesional en estilismo personal. El temario se centra en la importancia de conseguir una buena imagen de sí mismo, el cuidado personal, la optimización de la morfología individual y en cómo utilizar la moda para conseguir los mejores resultados.';
			  }
			  elseif($course_id == 12)
			  {
				  $course_name='Personal Shopper';
				  $course_topics = 'Con este curso se adquiere el conocimiento para poder convertirse en un asesor de imagen profesional. El temario se centra en las salidas profesionales de una personal shopper, la planificación de una asesoría, la moda y las tendencias, la comunicación y el protocolo.';
			  }
			  elseif($course_id == 13)
			  {
				  $course_name= 'Maquillaje';
				  $course_topics = 'Con este curso se adquiere el conocimiento para poder convertirse en un profesional en el mundo del maquillaje. El mismo se centra en un estudio en profundidad de los tipos de piel, la colorimetría, el maquillaje dependiendo de la forma del rostro, además de diferentes técnicas de maquillaje adaptadas a cada ocasión. El curso incluye tutoriales en formato vídeo de modelos maquilladas por un profesional que ayudan a reforzar el contenido del curso y a entender las técnicas con mayor precisión.';
			  }
			  elseif($course_id == 14)
			  {
				  $course_name='Wedding Planner';
				  $course_topics = 'Con este curso se adquiere el conocimiento para poder convertirse en un organizador de bodas. El temario incluye los diferentes tipos de ceremonia, todas las gestiones necesarias para organizar el evento y la preparación del presupuesto.';
			  }
			  elseif($course_id == 15)
			  {
				  $course_name='Nail Artist';
				  $course_topics = 'Con este curso se adquiere el conocimiento para poder convertirse en un profesional en estilismo de uñas. El cursose focaliza en el proceso para realizar la manicura, la pedicura, la corrección de problemas e imperfecciones y en las últimas tendencias en maquillaje de uñas. Tutoriales en formato vídeo ayudan a reforzar el contenido del curso y a entenderlo con mayor precisión.';
			  }
			  elseif($course_id == 16)
			  {
				  $course_name='Hair Stylist';
				  $course_topics = 'Con Hair Stylist se adquiere el conocimiento para poder convertirse en un estilista del cabello profesional. Se aprenden las técnicas de los peinados de tendencia, el análisis del cuero cabelludo, la estructura química del cabello, y la elección de productos y herramientas, entre otros.';
			  }
			  
			 // setlocale(LC_TIME, 'en_EN');
			  
			//  $cssLink = "http://trendimi.net/public/letters/css/proof_letters.css";
			  
			  
			  if($user_lang_id == 4)
			  {
				  
			  
			  $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Proof of enrolment</title>
	
	
	</head>
	
	<body>
	<style>
	.outer{height:940px; width:720px; margin:0 auto; line-height:20px}
.header{height:26px; text-align:right; padding:74px 20px 20px 20px; background:url(/public/letters/css/logoj.jpg) 485px 15px no-repeat}
.logotxt, .letterNme, .courseNme{font-family: "Great Vibes", cursive;}
.logotxt{font-size:22pt; font-weight:normal; margin:-0.5em 1.8em 0 0; padding:0; color:#df3e8e}
.letterNme{font-size:32pt; font-weight:normal; margin:0; padding:0; color:#a5218c; position:absolute; left:1em; top:-0.6em}
.content{background:#a9dde1 url(/public/letters/css/signature.jpg) 6% 67% no-repeat; padding:4em 2em 2em 2em; border-radius:20px; width:656px; float:left; height:696px; position:relative}
.text{height:500px}
.clear{clear:both}
p{font-size:11pt; color:#555; margin:0; padding:0.5em 0; font-family: "Andada", serif; line-height:1em}
ul{list-style:none; display:block; font-size:12pt; color:#555; margin:0; padding:0; font-family: "Andada", serif; line-height:1.4em}
	
	</style>
	<div class="outer">
	<div class="header">
	<h2 class="logotxt">- online learning -</h2>
	</div>
	<div class="clear"></div>	
	<div class="content">
	<h2 class="letterNme">Proof of enrolement</h2>
	<div class="clear"></div>
	<div class="text">
	<p>To whom it may concern,</p>
	<p>We confirm that '.$user_name.' has registered with Trendimi online learning institution and has enrolled to study our '.$course_name.' course. </p>
	<p>
	The content, exercises and exams in '.$course_name.' compile to '.$course_hours.' online hours study. '.$name[0].'\'s expected date of 
	completion is '.$expiry_date.' '.$month_name.'  '.$expiry_year.'. This date may be extended if extra time is needed to complete study.
	</p>
	<p>
	'.$course_topics.'
	</p>
	<p>
	We wish '.$name[0].' ever success in completing '.$course_name.' course and in '.$gender_pronoun_2.' future career. 
	</p>
	<p>Kind regards,</p>
	</div>
	<div class="clear"></div>
	<ul>
	<li>Francisca Tomàs</li>
	<li>Managing Director</li>
	<li>Trendimi Ltd</li>
	<li>T: UK + 44(0) 20 32904209</li>
	<li>T: Ireland +353(0) 21 234 0285</li>
	<li>w: www.trendimi.com</li>
	<li>e: info@trendimi.com</li>
	</ul>
	</div>
	</div>
	</body>
	</html>
	';
    }
    else if($user_lang_id == 3)
    {
		 $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Certificado de inscripción</title>	
	</head>
	
	<body>
	
	<style>
	.outer{height:940px; width:720px; margin:0 auto; line-height:20px}
.header{height:26px; text-align:right; padding:74px 20px 20px 20px; background:url(/public/letters/css/logoj.jpg) 485px 15px no-repeat}
.logotxt, .letterNme, .courseNme{font-family: "Great Vibes", cursive;}
.logotxt{font-size:22pt; font-weight:normal; margin:-0.5em 1.8em 0 0; padding:0; color:#df3e8e}
.letterNme{font-size:32pt; font-weight:normal; margin:0; padding:0; color:#a5218c; position:absolute; left:1em; top:-0.6em}
.content{background:#a9dde1 url(/public/letters/css/signature.jpg) 6% 67% no-repeat; padding:4em 2em 2em 2em; border-radius:20px; width:656px; float:left; height:696px; position:relative}
.text{height:500px}
.clear{clear:both}
p{font-size:11pt; color:#555; margin:0; padding:0.5em 0; font-family: "Andada", serif; line-height:1em}
ul{list-style:none; display:block; font-size:12pt; color:#555; margin:0; padding:0; font-family: "Andada", serif; line-height:1.4em}
	
	</style>
	
	<div class="outer">
	<div class="header">
	<h2 class="logotxt">- formación online -</h2>
	</div>
	<div class="clear"></div>
	<div class="content">
	<h2 class="letterNme">Certificado de inscripción</h2>
	<div class="clear"></div>
	<div class="text">
	<p>A quien corresponda,</p>
	<p>Confirmamos que '.$user_name.' se ha registrado en la institución de formación online Trendimi y se ha matriculado para estudiar nuestro curso de '.$course_name.'. </p>
	<p>
	El curso está compuesto por una primera parte de teoría, otra de ejercicios y una última de exámenes, y el mismo tiene una duración de
	'.$course_hours.' horas.La fecha prevista para que '.$name[0].' finalice el curso es día el '.$expiry_date.' de '.$month_name.' de '.$expiry_year.'. Esta fecha puede ser extendida si se necesita más tiempo para completar el curso.
	</p>
	<p>'.$course_topics.'</p>
	
	<p>Deseamos a '.$name[0].' mucha suerte para completar el curso '.$course_name.' y para su futura carrera profesional.</p>
	<p>Un cordial saludo,</p>
	</div>
	<div class="clear"></div>
	<ul>
	<li>Francisca Tomàs</li>
	<li>Directora General</li>
	<li>Trendimi Ltd</li>
	<li>T Reino Unido: + 44(0) 20 32904209</li>
	<li>T Irlana: +353(0) 21 234 0285</li>
	<li>w: www.trendimi.com</li>
	<li>e: info@trendimi.com</li>
	</ul>
	</div>
	</div>
	</body>
	</html>
	';
		
		  
    }
								  
								/*echo $html;
								exit;  
								*/
							
							
							
							
							$data = pdf_create($html, 'proof_enrol_'.$user_id.'_'.$course_id,false);		
	
							//$data = pdf_create($html, '', false);	
							$this->path = "public/certificate/proof_study/proof_enrol_".$user_id."_".$course_id.".pdf";
							write_file($this->path, $data);
						 
							
							// end case2 ******************************	
							$sendemail = true;		
							
								if($sendemail)
								{
									
									
									/*Ends here*/	
										
										
																 
									  //$to_mail = 'info@trendimi.net';									
									 // $to_mail = 'ajithupnp@gmail.com';
									  $to_mail = 'certificates@trendimi.com';
									  $user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);
									
									  $emailSubject = "Proof of enroll Hard copy request : ".$student_data[0]->email;
									  $mailContent = "<p>Please find the attachment of proof of enroll certificate  here with it. <p>";
									  $mailContent .= "<p>User details of Proof of enroll hard copy applied, <p>";
									  
									  $mailContent .= "<p>User name  : ".$student_data[0]->first_name." ".$student_data[0]->last_name."</p>";
									  $mailContent .= "<p>House Number :  ".$student_data[0]->house_number."</p>";
									  $mailContent .= "<p>Address :  ".$student_data[0]->address."</p>";					  
									  $mailContent .= "<p>City :  ".$student_data[0]->city."</p>";
									  $mailContent .= "<p>Zip code :  ".$student_data[0]->zipcode."</p>";
									  $mailContent .= "<p>Country : ".$user_country_name."</p>";
											
									  $this->email->from('info@eventtrix.com', 'Team eventtrix');
									  $this->email->to($to_mail); 
									  $this->email->attach($this->path);
									  
									  $this->email->subject($emailSubject);
									  $this->email->message($mailContent);	
									  
									 $this->email->send();
									 $this->email->clear(TRUE);
							 	 }
							}
							
								//$this->email->clear(TRUE);	
							
						}
								
							
								
							    //	proof_completion
								if($cart_det->product_type == 'proof_completion' || $cart_det->product_type == 'proof_completion_hard')
								{
									$this->load->library('email');
										
								  $today = date("Y-m-d");
								   $course_id = $cart_det->selected_item_ids;
								 
								  
								  $paymentId = $this->user_model->add_payment($paymantDetails);
								  
								    $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>$cart_det->product_type,"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$paymentId);	 
							  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
								  
								  
								  if($cart_det->product_type == 'proof_completion')
								  {
									$certificate_type ='downloadable';
								  }
								  else
								  {
									  $certificate_type ='downloadable and hard';
								  }
								  
								  $user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);
									
									  $to_mail = $student_data[0]->email;
									  
									   if($this->session->userdata['ip_address'] == '117.242.194.73')
										{
											$to_mail = 'ajithupnp@gmail.com';
										}
						   
									//  $to_mail = 'ajithupnp@gmail.com';
									 // $to_mail = 'jane@trendimi.net';
									  $emailSubject = "Successfully purchased proof of completion ".$certificate_type." copy";
									  $mailContent = "Hi, ".$student_data[0]->first_name;
									  
									$mailContent .= "<p>Thanks you for your order.</p>";
									$mailContent .= "<p>You have successfully purchased proof of completion ".$certificate_type." copy</p>";
									$mailContent .= "<p>You can download your proof of completion from this link <a href='".base_url()."download_products'>click here</a></p>";
									$mailContent .= "<br>Happy styling! <br>Thanks,<br><strong>Team eventtrix</strong>";


											
									  $this->email->from('info@eventtrix.com', 'Team eventtrix');
									  $this->email->to($to_mail); 
									 // $this->email->attach($this->path);
									  
									  $this->email->subject($emailSubject);
									  $this->email->message($mailContent);	
									  
									  $this->email->send();
									  $this->email->clear(TRUE);
							  
							  
							  
							  		if($cart_det->product_type == 'proof_completion_hard')
							 	 	{
								  		
										/*  certficate proof comletion */
										
										
										
									  $this->load->helper(array('dompdf', 'file'));
									  $user_id = $this->session->userdata['student_logged_in']['id'];	
									  $user_name = $this->common_model->get_user_name($user_id);	
									  
									  $name = explode('&nbsp;',$user_name);
									
									  $course_name = $this->common_model->get_course_name($course_id); 
									  $slNo=0;
									  
									  $course_hours  = $this->user_model->get_course_hours($course_id);
									  $stud_details=$this->user_model->get_stud_details($user_id);
									  
									  if($stud_details[0]->gender == 1)
									  {
										 $gender_pronoun = 'him'; 
										 $gender_pronoun_2 = 'his'; 
										 
									  }
									  else if($stud_details[0]->gender == 2)
									  {
										 $gender_pronoun = 'her';
										 $gender_pronoun_2 = 'her'; 
									  }
									 
									  $user_first_name = trim($stud_details[0]->first_name);	
									 /* $certficate_details = $this->user_model->get_certficate_details($user_id,$course_id);
									  
									 
									   $completed_date_date = $certficate_details['applied_on'];
									   */
									     $user_lang_id  = $this->common_model->get_user_lang_id($user_id);
										  /*------ send mail to admin */
										  
											  if($user_lang_id == 3)
											  {
												   setlocale(LC_TIME, 'es_ES');
											  }
											  elseif($user_lang_id == 4)
											  {
												  setlocale(LC_TIME, 'en_EN');
											  }
											  elseif($user_lang_id == 6)
											  {
												   setlocale(LC_TIME, 'fr_FR');
											  }
			  
									   
									  $certficate_details = $this->user_model->get_proof_of_completion_details($user_id,$course_id);
									  if(empty($certficate_details))
										{
											$certficate_details = $this->user_model->get_certficate_details($user_id,$course_id);
										}			
										
									  if(!empty($certficate_details))
									  {
										   $completed_date_date = $certficate_details['applied_on'];
									  }
									  else
									  {
										  $completed_date_date = date("Y-m-d");
									  }
										  
									//  $completed_date_date = $certficate_details['applied_on'];
									  
									  $course_completed_date = explode('-',$completed_date_date);
									  
									  $completed_year  = $course_completed_date[0];
									 // $completed_month = $course_completed_date[1];
									  $completed_date  = $course_completed_date[2];
									  
									   //$date_in_time_frmt = strtotime($completed_date_date);
									 // $completed_date =2;
									  //$month_name  = date('F', $date_in_time_frmt);
									  
									  $month_name = ucwords(strftime('%B',strtotime($completed_date_date)));
									  
									
									  $date_suffix = date("S",strtotime($completed_date_date));									  
									  
									  $module_list = $this->user_model->check_user_registered($user_id,$course_id);
									  
									  foreach($module_list as $unit)
									  {
										  $modules = unserialize($unit->student_course_units);
									  }
												
									  $module_count = count($modules);
									  
									  
										$mark_details = $this->get_student_progress($course_id);
									 
									
										 $grade='falied';
										if($mark_details['coursePercentage'] >= 55 && $mark_details['coursePercentage'] <= 64.99)
										{
											$grade = $this->user_model->translate_('mark_pass');
										}
										else if($mark_details['coursePercentage'] >= 65 && $mark_details['coursePercentage'] <= 74.99)
										{
											$grade = $this->user_model->translate_('mark_pass_plus');
										}
										else if($mark_details['coursePercentage'] >= 75 && $mark_details['coursePercentage'] <= 84.99)
										{
											$grade = $this->user_model->translate_('mark_merit');
										}
										else if($mark_details['coursePercentage'] >= 85 )
										{
											$grade = $this->user_model->translate_('mark_dist');
										}
										 $course_hours  = $this->user_model->get_course_hours($course_id);	
									 
									  
									 /* if($completed_date == 1)
									  {
										  $completed_date = $completed_date.'st';
									  }
									  else if($completed_date == 2)
									  {
										  $completed_date = $completed_date.'nd';
									  }
									  else if($completed_date == 3)
									  {
										  $completed_date = $completed_date.'rd';
									  }
									  else
									  {
										  $completed_date = $completed_date.'th';
									  }*/
									  $course_topics = '';
		  
							  if($course_id == 1)
							  {
								  $course_name='Personal Image & Beauty Expert';
				                 $course_topics = 'Topics covered by the course include: Personal image, Personal care, Colour, skin tones and head shapes analysis, Make-up, Morphology, Professional products, Fashion.';
							  }
							  elseif($course_id == 2)
							  {
								   $course_name='Personal Shopper';
				                   $course_topics = 'Topics covered by the course include: The profession of a personal shopper, Working with client, Colour and body shapes analysis, Hair and make-up, Wardrobe analysis:basics of wardrobe and complimentary accessories, Fashion and trends, Communication and protocol, Career guidance.';
							  }
							  elseif($course_id == 3)
							  {
								  $course_name='Make-up Artist';
				                  $course_topics = 'Topics covered by the course include: Different skin types, Foundations and corrections, Colour, light and shade and head shapes analysis, Professional tools and make-up kits, Types of make-up, Eye and eyebrow make-up, False eyelashes, Lip make-up ,Make-up for different ages ,Health and safety .';
							  }
							  elseif($course_id == 4)
							  {
								   $course_name='Wedding Planner';
				                   $course_topics = 'Topics covered by the course include: The profession of a wedding planner and types of ceremonies, Working with client, Engagement & bachelor parties, Getting the look and feel right  – venues, music, roles, invitations, guests lists, speeches, Dressing the wedding party, Perfecting essential details – décor, the banquet, gifts, menu, floral arrangements, honeymoon, Summing up – final budget, assessment, Starting and marketing your wedding planner business, Wedding planning resources.';
							  }
							  elseif($course_id == 5)
							  {
								 $course_name='Gel Manicure & Nail Artist';
				                 $course_topics = 'The course content outlines the knowledge skills, and dos and don’ts necessary to become a professional nail artist, including manicure, pedicure, correcting problems and latest decoration techniques.';
							  }
							  elseif($course_id == 6)
							  {
								  $course_name='Hair Stylist';
				               $course_topics = 'The course content outlines the knowledge skills, and dos and don’ts necessary to become a professional hair stylist, including hair and scalp analysis, chemical structure of hair, choosing products & tools and styling techniques.';
							  }
							  
							   elseif($course_id == 7)
			                   {
				  $course_name='Event Planner';
				  $course_topics = 'Topics covered by the course include: Principles of event management & roles of event manager, Types of events, Working with clients incl. understanding client needs, preparing event proposals,signing contracts, Steps for planning an event incl. budgets, venues, food and beverages,transportation, speakers, General etiquette and protocol incl. invitations, dress codes, table settings andseating arrangements, greeting etiquette, Day of the event and post event evaluation.';
			                   }
			  elseif($course_id == 8)
			                  {
				  $course_name='Starting Your Business';
				  $course_topics = 'Topics covered by the course include: Market research and competitors analysis, funding and available help, Introduction to marketing, Business structures, legislation and regulations, registering your business, Budget and cash flows, accounting and finance, Insurance, premises, suppliers, staff, Home based businesses, Business plan, Launching your business.';
			                 }
			   elseif($course_id == 9)
			                 {
				  $course_name='Marketing Your Business';
				  $course_topics = 'Topics covered by the course include: Introduction to marketing, Marketing plan, Low cost marketing techniques, Developing your brand, Setting up and managing website, Social media and online marketing, Public relations and advertising, Sales campaigns and leads generation.';
			                   }
							  elseif($course_id == 11)
							  {
								  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en el estudio sobre la importancia de conseguir una buena imagen de sí mismo, el cuidado personal, la optimización de la morfología individual y en cómo utilizar la moda para conseguir los mejores resultados.';
							  }
							  elseif($course_id == 12)
							  {
								  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en las salidas profesionales de una personal shopper, la planificación de una asesoría, la moda, las tendencias, la comunicación y el protocolo. Tutoriales en formato vídeo ayudan a reforzar el contenido del curso y a entenderlo con mayor precisión.';
							  }
							  elseif($course_id == 13)
							  {
								  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en un estudio en profundidad de los tipos de piel, la colorimetría, el maquillaje dependiendo de la forma del rostro, además de diferentes técnicas de maquillaje adaptadas a cada ocasión, entre otros. El curso incluye tutoriales en formato vídeo de modelos maquilladas por un profesional que ayudan a reforzar el contenido del curso y a entender las técnicas con mayor precisión.';
							  }
							  elseif($course_id == 14)
							  {
								  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en los diferentes tipos de ceremonia, todas las gestiones necesarias para organizar el evento y la preparación del presupuesto.';
							  }
							  elseif($course_id == 15)
							  {
								  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en el estudio sobre el masaje de pies y manos, la manicura, la pedicura, la corrección de problemas y las últimas tendencias en maquillaje de uñas.';
							  }
							  elseif($course_id == 16)
							  {
								  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. El curso se focaliza en las técnicas de los peinados de tendencia, el análisis del cuero cabelludo, la estructura química del cabello, y la elección de productos y herramientas, entre otros.';
							  }
							   elseif($course_id == 31)
							  {								  
								   $course_name ='Personal Stylist';
								  $course_topics = 'Les sujets abordés dans le cours incluent: L’image de soi, Soins personnels, Couleur, teint et visagisme, Maquillage, Morphologie, Produits professionnels, Mode';
							  }
							   elseif($course_id == 33)
							  {
								  $course_name ='Artiste du Maquillage';
								  $course_topics = 'Les sujets abordés dans le cours incluent: Les types de peau, Fond de teint et corrections, Couleur, lumière, fards et visagisme, Outils professionnels et trousses de maquillage, Types de maquillage, Maquillage des yeux et des sourcils, Faux cils, Maquillage des lèvres, Le maquillage selon l’âge, Santé et sécurité';
							  }
		  
							  
							  
							  
							 
							 $lang_id  = $this->common_model->get_user_lang_id($user_id); 
							//  $cssLink = "http://trendimi.net/public/user/css/proof_letters.css";
							
							if($lang_id==4)
							{
							  
							  $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml">
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<title>certificate</title>
					
					</head>
					
					<body>
					<style>
						.outer{height:940px; width:720px; margin:0 auto; line-height:20px}
					.header{height:26px; text-align:right; padding:74px 20px 20px 20px; background:url(/public/letters/css/logoj.jpg) 485px 15px no-repeat}
					.logotxt, .letterNme, .courseNme{font-family: "Great Vibes", cursive;}
					.logotxt{font-size:22pt; font-weight:normal; margin:-0.5em 1.8em 0 0; padding:0; color:#df3e8e}
					.letterNme{font-size:26pt; font-weight:normal; margin:0; padding:0; color:#a5218c; position:absolute; left:1em; top:-0.7em}
					.content{background:#a9dde1 url(/public/letters/css/signature.jpg) 6% 67% no-repeat; padding:4em 2em 2em 2em; border-radius:20px; width:656px; float:left; height:696px; position:relative}
					.text{height:500px}
					.clear{clear:both}
					p{font-size:11pt; color:#555; margin:0; padding:0.5em 0; font-family: "Andada", serif; line-height:1em}
					ul{list-style:none; display:block; font-size:12pt; color:#555; margin:0; padding:0; font-family: "Andada", serif; line-height:1.4em}
						
						</style>
					
					<div class="outer">
					<div class="header">
					<h2 class="logotxt">- online learning -</h2>
					</div>
					<div class="clear"></div>
					<div class="content">
					<h2 class="letterNme">Proof of course completion</h2>
					<div class="clear"></div>
					<div class="text">
					<p>To whom it may concern,</p>
<p>We are pleased to confirm that, on '.$completed_date.' '.$month_name.' '.$completed_year.' , '.$user_name.' successfully completed our '.$course_name.' e-learning course. '.$user_first_name.' graduated with a '.$grade.' grade.</p>
<p>
The course consists of '.$course_hours.' study hours and is part of the Trendimi suite of e-learning opportunities. The course educational excellence is assured through accreditation from the International Council for Online Educational Standards. 
</p>
<p>
The course includes study content with practical examples, video tutorials, exercises and exams. There are a total of '.$module_count.' modules. 
</p>
<p>
'.$course_topics.'
</p>
<p>We congratulate '.$user_first_name.' on completing our '.$course_name.' course and wish '.$gender_pronoun.' every success in '.$gender_pronoun_2.' future career.</p>
					<p>Kind regards,</p>
					</div>
					<div class="clear"></div>
					<ul>
					<li>Francisca Tomàs</li>
					<li>Managing Director</li>
					<li>Trendimi Ltd</li>
					<li>T: UK + 44(0) 20 32904209</li>
					<li>T: Ireland +353(0) 21 234 0285</li>
					<li>w: www.trendimi.com</li>
					<li>e: info@trendimi.com</li>
					</ul>
					</div>
					</div>
					</body>
					</html>
					';
							}
							elseif($lang_id==3)
							{
								  
							  $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml">
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<title>certificate</title>
					
					</head>
					
					<body>
					<style>
						.outer{height:940px; width:720px; margin:0 auto; line-height:20px}
					.header{height:26px; text-align:right; padding:74px 20px 20px 20px; background:url(/public/letters/css/logoj.jpg) 485px 15px no-repeat}
					.logotxt, .letterNme, .courseNme{font-family: "Great Vibes", cursive;}
					.logotxt{font-size:22pt; font-weight:normal; margin:-0.5em 1.8em 0 0; padding:0; color:#df3e8e}
					.letterNme{font-size:26pt; font-weight:normal; margin:0; padding:0; color:#a5218c; position:absolute; left:1em; top:-0.7em}
					.content{background:#a9dde1 url(/public/letters/css/signature.jpg) 6% 67% no-repeat; padding:4em 2em 2em 2em; border-radius:20px; width:656px; float:left; height:696px; position:relative}
					.text{height:500px}
					.clear{clear:both}
					p{font-size:11pt; color:#555; margin:0; padding:0.5em 0; font-family: "Andada", serif; line-height:1em}
					ul{list-style:none; display:block; font-size:12pt; color:#555; margin:0; padding:0; font-family: "Andada", serif; line-height:1.4em}
						
						</style>
					
					<div class="outer">
					<div class="header">
					<h2 class="logotxt">- formación online -</h2>
					</div>
					<div class="clear"></div>
					<div class="content">
					<h2 class="letterNme">Certificado de finalización de curso</h2>
					<div class="clear"></div>
					<div class="text">
					<p>A quien corresponda,</p>
					<p>Confirmamos que el día '.$completed_date.' de '.$month_name.' de '.$completed_year.' , '.$user_name.' completó con éxito el curso online	 de '.$course_hours.' horas '.$course_name.', uno de los curso de formación online ofrecidos por Trendimi.</p>
					<p>
					'.$course_topics.'
					</p>
					<p>Felicitamos a '.$user_first_name.' por haber completado con éxito el curso de '.$course_name.' y le deseamos mucho éxito en su futura carrera.</p>
					<p>Un cordial saludo,</p>
					</div>
					<div class="clear"></div>
					<ul>
					<li>Francisca Tomàs</li>
					<li>Directora General</li>
					<li>Trendimi Ltd</li>
					<li>T Reino Unido : + 44(0) 20 32904209</li>
					<li>T Irlana: +353(0) 21 234 0285</li>
					<li>w: www.trendimi.com</li>
					<li>e: info@trendimi.com</li>
					</ul>
					</div>
					</div>
					</body>
					</html>
					';
							}
							
					elseif($lang_id==6)
					{
					  
					  $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>certificate</title>
			
			</head>
			
			<body>
			<style>
				.outer{height:940px; width:720px; margin:0 auto; line-height:20px}
			.header{height:26px; text-align:right; padding:74px 20px 20px 20px; background:url(/public/letters/css/logoj.jpg) 485px 15px no-repeat}
			.logotxt, .letterNme, .courseNme{font-family: "Great Vibes", cursive;}
			.logotxt{font-size:22pt; font-weight:normal; margin:-0.5em 1.8em 0 0; padding:0; color:#df3e8e}
			.letterNme{font-size:26pt; font-weight:normal; margin:0; padding:0; color:#a5218c; position:absolute; left:1em; top:-0.7em}
			.content{background:#a9dde1 url(/public/letters/css/signature.jpg) 6% 68% no-repeat; padding:1.5em 2em 2em 2em; border-radius:20px; width:656px; float:left; height:696px; position:relative}
			.text{height:500px}
			.clear{clear:both}
			p{font-size:11pt; color:#555; margin:0; padding:0.5em 0; font-family: "Andada", serif; line-height:1em}
			ul{list-style:none; display:block; font-size:12pt; color:#555; margin:0; padding:7em 0 0 0; font-family: "Andada", serif; line-height:1.4em}
				
				</style>
			
			<div class="outer">
			<div class="header">
			<h2 class="logotxt">- online learning -</h2>
			</div>
			<div class="clear"></div>
			<div class="content">
			<h2 class="letterNme">Preuve d’Accomplissement</h2>
			<div class="clear"></div>
			<p>À qui de droit,</p>
			
			<p>Nous avons le plaisir de certifier que '.$user_name.' a validé avec succès le cours de formation en ligne '.$course_name.', le
			 '.$completed_date.' '.$month_name.' '.$completed_year.' . '.$user_first_name.' a obtenu son diplôme avec le niveau '.$grade.'.
			 </p>  
			 <p>Cette formation représente un temps d’étude de '.$course_hours.' heures et fait partie du programme de formations en  ligne proposées par Trendimi. La renommée du cours est certifiée par l’accréditation de l’International Council for Online Educational Standards. 
			 </p>
			 <p>La formation comprend du matériel pédagogique illustré d’exemples pratiques, des tutoriels vidéos, des exercices et une évaluation finale. Le cours comporte au total '.$module_count.' modules.
			 </p>
			 <p>
			'.$course_topics.'
			</p>
			<p>
			Nous félicitons '.$user_first_name.' d’avoir validé le cours '.$course_name.' et lui souhaitons de réussir dans sa future carrière.
			</p>
			<p>Meilleures salutations,</p>
			<div class="clear"></div>
			<ul>
			<li>Francisca Tomàs</li>
			<li>Directrice Générale</li>
			<li>eventtrix Ltd</li>
			<li>w: www.eventtrix.com</li>
			<li>e: info@eventtrix.com</li>
			</ul>
			</div>
			</div>
			</body>
			</html>
			';
					}
					
									/*echo $html;
									exit;  */
									
								/* $data = pdf_create($html, 'proof_completion_'.$user_id.'_'.$course_id);   
								 write_file('name', $data);*/
										
										
								  $data = pdf_create($html, 'proof_completion_'.$user_id.'_'.$course_id,false);		
	
								//$data = pdf_create($html, '', false);	
								$this->path = "public/certificate/proof_completion/proof_completion_".$user_id."_".$course_id.".pdf";
								write_file($this->path, $data);
							 
								
								// end case2 ******************************	
								$sendemail = true;
								
								
								
								if($sendemail)
								{
									  //$to_mail = 'info@trendimi.net';	
									    if($this->session->userdata['ip_address'] == '117.242.193.226')
										{								
											  $to_mail = 'ajithupnp@gmail.com';
										}
										else
										{
									  		$to_mail = 'certificates@eventtrix.com';
										}
									  $user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);
									
									  $emailSubject = "Proof of completion Hard copy request : ".$student_data[0]->email;
									  $mailContent = "<p>User details of Proof of completion hard copy applied, <p>";
									  
									  $mailContent .= "<p>User name  : ".$student_data[0]->first_name." ".$student_data[0]->last_name."</p>";
									  $mailContent .= "<p>House Number :  ".$student_data[0]->house_number."</p>";
									  $mailContent .= "<p>Address :  ".$student_data[0]->address."</p>";					  
									  $mailContent .= "<p>City :  ".$student_data[0]->city."</p>";
									  $mailContent .= "<p>Zip code :  ".$student_data[0]->zipcode."</p>";
									  $mailContent .= "<p>Country : ".$user_country_name."</p>";
											
									  $this->email->from('info@eventtrix.com', 'Team eventtrix');
									  $this->email->to($to_mail); 
									  $this->email->attach($this->path);
									  
									  $this->email->subject($emailSubject);
									  $this->email->message($mailContent);	
									  
									 $this->email->send();
									 $this->email->clear(TRUE);
									// usleep(50);
								}	
									
									
							}
							//$this->email->clear(TRUE);
							
						}/*end proof_completion */
								
							 
							 
							 if($cart_det->product_type == 'access')
								{
									$this->load->library('email');
									$this->load->model('email_model');
									
									//echo "<br>Ebook";
								//	echo "<br>Ebook ids ".$cart_det->selected_item_ids;
									
									
									$selected_months = $cart_det->selected_item_ids;
									
									
									 $today = date("Y-m-d");
								   $course_id = $cart_det->selected_item_ids;
								
									
									
									
									
									// $subcription_id = $this->user_model->get_extension_id($product_id);
									
									$product_details = $this->common_model->get_product_details($product_id);
										
										
											$period = $product_details[0]->item_id;
										
										$today = date("Y-m-d");
										// $accessdate=date("Y-m-d", strtotime("+$period days"));
										// $status = '1'; // studying
										
										 $course_status = $this->user_model->get_student_course_status($course_id,$user_id);
										 
										 $userCoursesArr=$this->user_model->getcourses_student_expiry($user_id,$course_id);
										 foreach($userCoursesArr as $det)
										 {
										 
											$cur_expiry_date = $det->date_expiry;
										 }
										
										 
										 if($cur_expiry_date > $today)
										 {
											
											 $accessdate=date('Y-m-d', strtotime($cur_expiry_date. ' + '.$period.' months'));
											
										 }
										 else
										 {
											
											  $accessdate=date('Y-m-d', strtotime($today. ' + '.$period.' months'));
											 $accessdate=date("Y-m-d", strtotime("+$period months")); 
											
										 }
										
										
									//	 echo "<br>Current acces date ".$cur_expiry_date;
		 		  
										
									
									 $course_status = $this->user_model->get_student_course_status($course_id,$user_id);
									// echo "<br> Course status ".$course_status;
									 
									 if($course_status == 7 || $course_status==6) // if expired or archived change status to completed
									 {
										
										
										 $mark_details = $this->get_student_progress($course_id); 
										
											if($mark_details['progressPercnt']==100)
											{
												
												$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$course_id);
												if(empty($certificate_details))
												{
													$update_data=array("date_expiry"=>$accessdate,"course_status"=>'2');
												}
												else
												{
													$update_data=array("date_expiry"=>$accessdate,"course_status"=>'4');
												}
											}
											else 
											{
													$update_data=array("date_expiry"=>$accessdate,"course_status"=>'1');
											}
													 
											
										
									 }
									 else
									 {
										  $update_data=array("date_expiry"=>$accessdate);
									 }
									 
									 $update_data['expired'] = '0';
									$this->user_model->update_student_enrollments($course_id,$user_id,$update_data);
									
									
										
									
									$paymentId = $this->user_model->add_payment($paymantDetails);
									
									
									  if($product_id == 46)
									  {
										  $type = 'access_6';
									  }
									  else if($product_id == 47)
									  {
										   $type = 'access_12';
									  }
									  
									  $today = date("Y-m-d");
									  $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>$type,"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$paymentId);		 
									  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
									
									
									 $to_mail = $student_data[0]->email;
									//  $to_mail = 'ajithupnp@gmail.com';
									 // $to_mail = 'jane@trendimi.net';
									  $emailSubject = "Successfully subscribed to course access";
									  $mailContent = "Hi, ".$student_data[0]->first_name;
									  
									$mailContent .= "<p>Thanks you for your order.</p>";
									$mailContent .= "<p>You have successfully subscribed course access</p>";
									$mailContent .= "<p>You will have access to all your course work and material to review and revise. Click on the option thats suite you and proceed to the payment page</p>";
									$mailContent .= "<br>Happy styling! <br>Thanks,<br><strong>Team eventtrix</strong>";


											
									  $this->email->from('info@eventtrix.com', 'Team eventtrix');
									  $this->email->to($to_mail); 
									 // $this->email->attach($this->path);
									  
									  $this->email->subject($emailSubject);
									  $this->email->message($mailContent);	
									  
									  $this->email->send();	
									  $this->email->clear(TRUE);				
									
									
								}
							 
							 
														
							}
						
							/*	echo "<pre>";
						print_r($paymantDetails);*/
						
					}
				}
				
				
				
			$update_array = array("transaction_id"=>$paymantDetails['transaction_id']);	
		
				
			$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);				
			
			}
			
		
			//	echo "User id ".$userId1=$this->session->userdata['student_logged_in']['id'];
						
			//$paymentId = $this->user_model->add_payment($paymantDetails);
			
			
			//exit;
				if($this->session->userdata('coupon_applied'))
		  	{				
				$this->session->unset_userdata('coupon_applied');
				$this->session->unset_userdata('coupon_applied_details');	
			}
			
			
			redirect('home/after_sales_pay/', 'refresh');
				
				
			} else {
				$this->_error($do_ec_return);
			}
		} else {
			$this->_error($get_ec_return);
		}
	}


	function ipn() {
		$logfile = 'ipnlog/' . uniqid() . '.html';
		$logdata = "<pre>\r\n" . print_r($_POST, true) . '</pre>';
		file_put_contents($logfile, $logdata);
	}
	
	
	function _error($ecd) {
		echo "<br>error at Express Checkout<br>";
		echo "<pre>" . print_r($ecd, true) . "</pre>";
		echo "<br>CURL error message<br>";
		echo 'Message:' . $this->session->userdata('curl_error_msg') . '<br>';
		echo 'Number:' . $this->session->userdata('curl_error_no') . '<br>';
	}

	function get_student_progress($course_id)
	{
		if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
		$stud_id=$this->session->userdata['student_logged_in']['id'];
		$course_status = $this->user_model->get_student_course_status($course_id,$stud_id); 	
		$course_name = $this->common_model->get_course_name($course_id); 
					
		 /*-----------------Start marks,progress calculations------------------------*/
					
					
		if($course_status!=0) // course started
		{		 
		  $courseUnitArray= $this->user_model->getCourseUnitListing($course_id,1); 
		 // 	echo $this->session->userdata['student_logged_in']['id']."<br>-------------------<br><pre>";print_r( $courseUnitArray);echo "</pre>";
		  $total_module = count($courseUnitArray);
		  if(!empty($courseUnitArray)) {
			$unitSlno            = 0;     
			$completedMarks1     = 0;
			$completedMarks2     = 0; 
			$countCompleted      = 0;
			$countTotal          = 0; 
			$completedPercentage = 0;   
			foreach($courseUnitArray as $key=> $courseUnitArr) { 
			  $percentage    = 0;
			 
			  $unitId        = $courseUnitArr['course_units_idcourse_units'];			 
			  
			  //whether the unit is completed or not by checking the pages in the unit
			  $unitComplete  = $this->user_model->getUnitCompleteByUser_unit($stud_id,$unitId,$course_id); 
			  //  total tasks in the unit
			  $taskArray     = $this->user_model->getTasksInUnit($unitId);
			  //echo "<br>-------------------<br><pre>";print_r( $taskArray);echo "</pre>";
			  $totalTask     = count($taskArray);
			  //  tasks in the unit which is attended by user
			  $userTaskArray = $this->user_model->getTasksForUserInUnit($stud_id,$unitId,$course_id); 
			 
			  $totalTaskUser = count($userTaskArray);
			  //the marks obtained by user in a particular unit in a course
			  $marksDetails  = $this->user_model->getUnitMarksForTasks($stud_id,$unitId,$course_id); 
			  //echo "<br>-------------------<br><pre>";print_r( $marksDetails);echo "</pre>";
			                 
			  if(!empty($marksDetails)) {
				  $totalMarks      = $marksDetails['totalMarks'];
				  $totalQuestions  = $marksDetails['totalQuestions'];
				$completedMarks1 = $completedMarks1+$totalMarks ;
				$completedMarks2 = $completedMarks2+$totalQuestions ;
				$markPerc        = @($totalMarks/$totalQuestions)*100;
				if($markPerc!=''){
				  $percentage=@round($markPerc,2);
				}
				
			  } 
			  if($unitComplete==1) {
				$countCompleted++;
			  }
			  //make array contain details for the unit with the unit id as index
			  $unitMarkArray[$unitId]['percentage']          = $percentage;
			  $unitMarkArray[$unitId]['complete']            = $unitComplete;
			  $unitMarkArray[$unitId]['totalTask']           = $totalTask;        
			  $unitMarkArray[$unitId]['totalTaskUser']       = $totalTaskUser;  
			  $unitMarkArray[$unitId]['totalTaskArray']      = $taskArray;        
			  $unitMarkArray[$unitId]['totalTaskUserArray']  = $userTaskArray;          
			  $countTotal++;  
			}
		  }
		 $coursePercentage2=@($completedMarks1/$completedMarks2)*100;
		  //$coursePercentage1 = $coursePercentage2/$total_module;
		  $coursePercentage=@round($coursePercentage2,2);
		  ////
		  $unitsIdArr = $this->user_model->getCourseUnitListing($course_id,1); 
		  
		  $totalUnit=count($unitsIdArr);
		  $valueArr=array();         
		  for ($unitCnt=0;$unitCnt<count($unitsIdArr);$unitCnt++) {
			$unitDetailsArr[] = $this->user_model->get_courseunits($unitsIdArr[$unitCnt]['course_units_idcourse_units']	);
			$pageIdsArr = array();
			$pageIdsArr = $this->user_model->getPageIdsForUnits($unitsIdArr[$unitCnt]['course_units_idcourse_units']);
			$studentPageIdsArr = $this->user_model->getStudentProgressPageIds($stud_id,$unitsIdArr[$unitCnt]['course_units_idcourse_units'],$course_id);
			$pageDiffArr = array();
			$progressPercnt=0;
			if (is_array($pageIdsArr) && is_array($studentPageIdsArr)) {
			  $pageDiffArr = array_diff($pageIdsArr,$studentPageIdsArr); 
			  $pageDiffCnt = count($pageDiffArr); 

			  $totalPageCnt = count($pageIdsArr);
			  $progressPercnt = round( ($totalPageCnt - $pageDiffCnt) * 100 / $totalPageCnt );
			}   
			else {
			  $progressPercnt = "0";
			}
			$valueArr[]=$progressPercnt;
		  }
		  
		  $total=array_sum($valueArr); 
		$totalpageattended=round($total/$totalUnit); 
		$x=0;
		$y=count($valueArr);
		foreach($valueArr as $val){
			$x=$x+$val;
		}
		$progressPercnt=$x/$y;
		
		}
		else // course not started
		{
			$progressPercnt=0;
			$coursePercentage=0;
		}
		  /*-----------------End marks,progress calculations------------------------*/
		  
		  /*----------------- Remaining days calculations------------------------*/
		  
		  $userCoursesArr=$this->user_model->getcourses_student_expiry($stud_id,$course_id);

		 $content['coursename'] = $course_name; 
		  if(!empty($userCoursesArr)){
			$courseDetails1 = $this->user_model->getstudent_courseaccess($stud_id,$course_id); 
			$now = time(); // or your date as well
			if($courseDetails1==''){
			  $accessdate_exp='';
			}
			else{
			  $accessdate_exp=$courseDetails1[0]->access_date_expiry;
			}
	
			if($accessdate_exp=='')
			{      
			  $your_date = strtotime($userCoursesArr[0]->date_expiry);
			  $datediff = $your_date - $now;
			  $numberOfDaysRemaining =  ceil($datediff/(60*60*24)); 
			}
			else
			{
			  $your_date = strtotime($accessdate_exp);
			  $datediff = $your_date - $now;
			  $numberOfDaysRemaining =  ceil($datediff/(60*60*24));
			}
			if($numberOfDaysRemaining < 0)
			{
			  $numberOfDaysRemaining = 0;
			}
		  }
		
		 /*----------------- End remaining days calculations------------------------*/
		
		$progressPercnt=@round($progressPercnt,0);
		$progress['course_id']        = $course_id;
		$progress['course_name']      = $course_name;
		$progress['coursePercentage'] = $coursePercentage;
		$progress['progressPercnt']   = $progressPercnt;
		$progress['daysremaining']    = $numberOfDaysRemaining;
		
		
		
		return $progress;
		
	
	
	}
	
}