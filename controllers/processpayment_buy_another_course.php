<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class processpayment_buy_another_course extends CI_Controller
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
		$this->load->model('sales_model','',TRUE);	
		$this->load->model('discount_code_model','',TRUE);
		$this->load->model('ebook_model','',TRUE);
		
		$this->load->model('package_model','',TRUE);
		$this->load->model('gift_voucher_model','',TRUE);
		
		
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
	
	
		$user_id     = $this->uri->segment(3);
		$currency_id = $this->uri->segment(4);
	
		
		$cart_main_details = $this->sales_model->get_cart_main_details_packageby_userid($this->session->userdata('cart_session_id'),$user_id);
		
		foreach($cart_main_details as $cart_main)
		{
			$amount = $cart_main->total_cart_amount;
		
		}
		
		$currency_details = $this->common_model->get_currency_details($currency_id);

				$payment_conf = array(
				'desc' => 'Sales cart',
				'currency' => $currency_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('processpayment_buy_another_course/processpay/'.$user_id), 
				'cancel_URL' => site_url('/home/buy_another_course_check_out'.$user_id), 
				);
				
				$product = array(
				'name' => 'Course + Pacakge', 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $amount
				);
				
				$payment_conf['products'][] = $product;			
		
		
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
		$student_details=$this->user_model->get_stud_details($user_id);
		foreach($student_details as $row){
			$email=$row->email;
			$first_name=$row->first_name;
		}
		$langId=$this->session->userdata('language');
		$get_ec_return = $this->paypal_ec->get_ec($token);
		if (isset($get_ec_return['ec_status']) && ($get_ec_return['ec_status'] === true)) {
	
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
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];						
						$paymantDetails['currency_id']=$currencyId;	
						$paymantDetails['type']='extra_course_package';					
						$paymantDetails['date']=$dateNow;
						$paymantDetails['sales_session_id']=$this->session->userdata('cart_session_id');
						
					
						$this->load->library('email');
						$this->load->model('email_model','',TRUE);
						$this->load->library('encrypt');
						
			if($this->session->userdata('cart_session_id'))
			{
				
				$cart_main_details = $this->sales_model->get_cart_main_details_packageby_userid($this->session->userdata('cart_session_id'),$user_id);
				foreach($cart_main_details as $cart_main)
				{		
					$cart_main_id = $cart_main->id;			
					$products_in_cart = $this->sales_model->get_cart_items($cart_main->id);
					foreach($products_in_cart as $prod)
					{
						
						$paymantDetails['amount'] = $prod->item_amount;
						$product_id  = $prod->product_id;
						$paymantDetails['product_id'] = $prod->product_id;
						$cart_item_details = $this->sales_model->get_cart_items_details($cart_main_id,$prod->id);
												
							foreach($cart_item_details as $cart_det)
							{								
								if($cart_det->product_type == 'extra_course')
								{
									$this->load->model('course_model','',TRUE);
									$this->load->model('payment_model','',TRUE);									
																
										$course_ids = explode(',',$cart_det->selected_item_ids);									
										$langId = $this->course_model->get_lang_course($course_ids[0]);										
									    $course_name=$this->common_model->get_course_name($course_ids[0]);
										$added_user_sess_array = array('added_user_id' => $user_id); 			
										$this->session->set_userdata($added_user_sess_array);	
										
								
								 $paymantDetails['user_id'] = $user_id;	
								if($this->session->userdata('coupon_applied'))
		  				{
							$paymantDetails['discount_applied'] ='yes';
							$paymantDetails['discount_id'] =$code_applied_details['discount_id'] = $this->session->userdata['coupon_applied_details']['discount_code_id']; 						
					    }	
						else{
							$paymantDetails['discount_applied']='no';
						}
								 $paymentId = $this->user_model->add_payment($paymantDetails);							
							if($this->session->userdata('coupon_applied'))
		  				{
							
							$code_applied_details['source'] = 'extra_course_package';
							$code_applied_details['product_id'] = $prod->product_id;
							$code_applied_details['user_id'] = $user_id;
							$code_applied_details['selected_item_id'] = $this->session->userdata('package_applying_course');		
							$code_applied_details['payment_id'] = $paymentId;							
							$this->discount_code_model->add_discount_applied_details($code_applied_details);	
						}	
						
							//	print_r($course_ids);
								//exit;
								for($cr=0;$cr<count($course_ids);$cr++)
								{								
									$courseId = $course_ids[$cr];	
									//echo "<br> ".($cr+1)." Course Id ".$courseId;							
									$langId = $this->course_model->get_lang_course($courseId);
									$dateNow =date('Y-m-d');								
									$expirityDate = $this->user_model->findExpirityDate($courseId,$dateNow);								
									$usersUnit = $this->user_model->get_courseunits_id($courseId);
									$un = array();
									foreach($usersUnit as $row)
									{
										$un[$row->units_order] = $row->course_units;
									}
									$student_courseData['student_course_units'] = serialize($un);
									
									
									$student_courseData['course_id'] = $courseId;
									$student_courseData['user_id'] = $user_id;
									$student_courseData['date_enrolled'] = $dateNow;
									$student_courseData['date_expiry'] = $expirityDate;
									$student_courseData['enroll_type'] = 'extra_course_package';
									$student_courseData['course_status'] = '0';	
									$courseEnrId = $this->user_model->add_course_student($student_courseData);								
									$resumeLinkArr['user_id']=$user_id;
									$resumeLinkArr['course_id']=$courseId;
									$resumeLinkArr['resume_link']='coursemanager/studentcourse/'.$courseId;
									$this->user_model->addResumeLink($resumeLinkArr);							
									
								/* Check if any ebook is included in added pacakge */
								
								if($courseId == $this->session->userdata('package_applying_course'))
								{
								
								$package_cart_contents = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'package');							
								if(!empty($package_cart_contents))
								{			
									$added_pack_id = $package_cart_contents[0]->selected_item_ids;				
									$products_in_package = explode(',',$this->package_model->get_products_in_package($added_pack_id));												
									$ebook_options = $this->common_model->get_product_by_type('ebooks');									
									foreach($ebook_options as $eb_opt)
									{
										if(in_array($eb_opt->id,$products_in_package))
										{
																				
										$ebook_id = $this->package_model->get_ebook_id_for_course_id($this->session->userdata('package_applying_course'),$langId);
										$ebook_subs_details['user_id'] 		= $user_id;
										$ebook_subs_details['product_id']	 = $eb_opt->id;
										$ebook_subs_details['ebook_id'] 	   = $ebook_id;
										$ebook_subs_details['date_purchased'] = $dateNow;
										$ebook_subs_details['payment_id'] 	 = $paymentId;									
										$ebook_subs_details_id = $this->ebook_model->addSubscription_user($ebook_subs_details);				
										
															
										$row_new = $this->email_model->getTemplateById('ebook_download_link',$langId);
										foreach($row_new as $row1)
										{
											
											$emailSubject = $row1->mail_subject;
											$mailContent = $row1->mail_content;
										}
											 
										if($langId==3)
										{
											$mailContent = str_replace ( "#firstname#",$first_name, $mailContent );
											$mailContent = str_replace ( "#click here#","<a href='".base_url()."home/ebookDownload/user'>clica aquí</a>", $mailContent );
										}									
										else
										{
											$mailContent = str_replace ( "#firstname#",$first_name, $mailContent );
											$mailContent = str_replace ( "#click here#","<a href='".base_url()."/home/ebookDownload/user'>click here</a>", $mailContent );
										}
										$to_mail = $email;
										//$to_mail = 'ajithupnp@gmail.com';									
										$this->email->from('info@eventtrix.com', 'Team Eventtrix');						
										$this->email->to($to_mail); 
										$this->email->cc(''); 
										$this->email->bcc(''); 
										$this->email->subject($emailSubject);
										$this->email->message($mailContent);	
										  
										$this->email->send();				
											
																					
										}
									}
								  }
								}									
							
							  }										
		
							}								
								if($cart_det->product_type == 'package')
								{									
								$user_id = $this->session->userdata('added_user_id');
								$course_id = $this->session->userdata('package_applying_course');									
								$package_id = $cart_det->selected_item_ids;									
								$package_details = $this->package_model->fetch_package($package_id);															
								$paymentId = $this->user_model->add_payment($paymantDetails);									
								$today =date('Y-m-d');									
								$package_subscriptions['user_id'] = $user_id;
								$package_subscriptions['package_id'] = $package_id;
								$package_subscriptions['course_id'] = $course_id;
								$package_subscriptions['source'] = 'extra_course_package';
								$package_subscriptions['date'] = $today;
								$package_subscriptions['payment_id'] = $paymentId;
								
								$pack_sub_id = $this->package_model->add_pacakge_subscriptions($package_subscriptions);
								$products_in_package = explode(",",$package_details[0]->products);
								for($k=0;$k<count($products_in_package);$k++)
								{																			
									$package_subscriptions_details['package_sub_id']= $pack_sub_id;
									$package_subscriptions_details['user_id']= $user_id;
									$package_subscriptions_details['course_id'] = $course_id;
									$package_subscriptions_details['product_id']= $products_in_package[$k];										
									$product_type = $this->user_model->get_type_ByID($products_in_package[$k]);
									$package_subscriptions_details['product_type']= $product_type;	
									if($product_type=='ebooks')
									{
										$package_subscriptions_details['status']= 0;
									}
									else
									{																		
										$package_subscriptions_details['status']= 1;											
									}
							$this->package_model->add_pacakge_subscription_details($package_subscriptions_details);								
							}
						}
					}
				}
				
				
				
			$update_array = array("transaction_id"=>$paymantDetails['transaction_id']);					
			$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);
			
			
				$this->load->library('email');
				$this->load->model('email_model');
				$this->load->library('encrypt');
				
				$en_studId = $this->encrypt->encode($user_id);//encoding student id
				$to_mail = $email;
				
				$row_new = $this->email_model->getTemplateById('new_course',$langId);
				
				foreach($row_new as $row1)
				{
					
					$emailSubject = $row1->mail_subject;
					$mailContent = $row1->mail_content;
					$mailing_template_id=$row1->id;
				}
				
				 	$mailContent = str_replace ( "#firstname#",$first_name, $mailContent );
					$mailContent = str_replace ( "#course_name#",$course_name, $mailContent );
														  
				  
				
					
					$this->email->from('mailer@eventtrix.com', 'Team Eventrix');
					$this->email->to($to_mail); 
					$this->email->cc(''); 
					$this->email->bcc(''); 
					$this->email->subject($emailSubject);
					$this->email->message($mailContent);	
					  
					$sent=$this->email->send();
				  if($sent==TRUE){
				  $mailing_histrory=array();
				  $mailing_histrory['email_id']=$to_mail;
				  $mailing_histrory['user_id']=$user_id;
				  $mailing_histrory['template_id']=$mailing_template_id;
				  $mailing_histrory['mailing_date']=date("Y-m-d");
				  $this->common_model->add_email_history($mailing_histrory);
				  }				
			
			}
			
		if($this->session->userdata('coupon_applied')){
			$this->session->unset_userdata('coupon_applied');
		}
		if($this->session->userdata('coupon_applied_details')){
			$this->session->unset_userdata('coupon_applied_details');
		}
		if($this->session->userdata('cart_session_type')){
			$this->session->unset_userdata('cart_session_type');
		}
			if($this->session->userdata('cart_source')){
			$this->session->unset_userdata('cart_source');
		}
		
			$this->session->unset_userdata('cart_session_id');
			$this->session->unset_userdata('package_applying_course');			
			$this->session->unset_userdata('added_user_id');
			
		
		$en_user_id = urlencode($this->encrypt->encode($user_id));
		
		redirect('home/buy_another_course_success','refresh');
		
			
				
				
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
	}
}