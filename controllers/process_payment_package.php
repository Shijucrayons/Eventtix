<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class process_payment_package extends CI_Controller
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
		//$this->load->model('email_model','',TRUE);
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
		
		
		
	//	$cart_session_id = $this->input->post('cart_session_id');
	
		$pre_user_id     = $this->uri->segment(3);
		$currency_id = $this->uri->segment(4);
		
		/*echo "User id ".$pre_user_id;
		echo "<br> Currency id ".$currency_id;
		echo "<br> cart sessin id ".$this->session->userdata('cart_session_id');	
		$user_id = $this->input->post('user_id');
		$currency_id = $this->input->post('currency_id');
		
		exit;*/
		
		//$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
		$cart_main_details = $this->sales_model->get_cart_main_details_package($this->session->userdata('cart_session_id'),$this->session->userdata('student_temp_id'));
		
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
				'return_URL' => site_url('process_payment_package/processpay/'.$pre_user_id), 
				'cancel_URL' => site_url('/home/package_check_out'), 
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
		
		$pre_user_id = $this->uri->segment(3);
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
						
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];						
						$paymantDetails['currency_id']=$currencyId;	
						if(isset($this->session->userdata['deals']['vCode']))
						{
							$paymantDetails['type']='deals_package';					
						}
						elseif($this->session->userdata('enrolling_rep_code'))
						{
							$paymantDetails['type']='rep_package';
						}
						else
						{
							$paymantDetails['type']='payment_package';
						}
						$paymantDetails['date']=$dateNow;
						$paymantDetails['sales_session_id']=$this->session->userdata('cart_session_id');
						$paymantDetails['discount_applied']='no';
						
						$new_registration_mail = true;
						
						
						$this->load->library('email');
						$this->load->model('email_model','',TRUE);
						$this->load->library('encrypt');
						
			if($this->session->userdata('cart_session_id'))
			{
				//$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));	
				$cart_main_details = $this->sales_model->get_cart_main_details_package($this->session->userdata('cart_session_id'),$this->session->userdata('student_temp_id'));
				
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
												
							foreach($cart_item_details as $cart_det)
							{								
								if($cart_det->product_type == 'course')
								{
									$this->load->model('course_model','',TRUE);
									$this->load->model('payment_model','',TRUE);									
									/*if(!$this->session->userdata["student_logged_in"])
									{*/												
										$tempArray = $this->user_model->get_student_temp($pre_user_id);									
										$course_ids = explode(',',$cart_det->selected_item_ids);									
										$langId = $this->course_model->get_lang_course($course_ids[0]);										
										foreach($tempArray as $row)
										{
											$studentdata  = array();
											$studentdata['first_name'] = $row->first_name;
											$studentdata['last_name'] =$row->last_name;
											$studentdata['email'] =$row->email;
											$studentdata['username'] = $row->username;
											$studentdata['password'] = $row->password;
											$studentdata['gender'] = $row->gender;
											$studentdata['contact_number'] = $row->contact_number;
											$studentdata['house_number'] = $row->house_number;		 
											$studentdata['address'] = $row->address;
											$studentdata['street'] = $row->street;
											$studentdata['zipcode'] = $row->zipcode;
											$studentdata['city'] = $row->city;
											$studentdata['country_id'] = $row->country_id;
											$studentdata['us_states'] = $row->us_states;
											$studentdata['reason_id'] = $row->reason_id;
											$studentdata['reg_date'] = $dateNow;
											$studentdata['lang_id'] = $langId;
											$studentdata['status']='1';
											if($this->session->userdata('enrolling_rep_code'))
											{
												$studentdata['reg_type'] = 'rep_code';
												
											}
											else if(isset($this->session->userdata['voucher_code']))
											{
												$studentdata['reg_type'] = 'voucher_home';	
											}
											else if(isset($this->session->userdata['deals']['vCode']))
											{
												$studentdata['reg_type'] = 'voucher_deals';
											}
											
											$content['coupon_code'] = $row->coupon_code;
											$content['redemption_code'] = $row->redemption_code;
											$content['redemption_pdf'] = $row->redemption_pdf;											
										}
										
										$repeate_stat = $this->gift_voucher_model->email_username_check($studentdata['email'],$studentdata['username'],$pre_user_id);
										if($repeate_stat==0)
										{										
											$user_id = $this->user_model->add_student($studentdata);	
										}
									
										
										$added_user_sess_array = array('added_user_id' => $user_id); 			
										$this->session->set_userdata($added_user_sess_array);	
										
										$cart_main_update_array = array("user_id"=>$user_id);
								
										$this->sales_model->main_cart_details_update_user_id($this->session->userdata('cart_session_id'),$pre_user_id,$cart_main_update_array);	
																
									/*}
									else
									{
										$user_id = $this->session->userdata["student_logged_in"];
									}*/
								
								 $paymantDetails['user_id'] = $user_id;	
								
								 $paymentId = $this->user_model->add_payment($paymantDetails);							
							
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
									if(isset($this->session->userdata['deals']['vCode']))
									{
									/*	if($this->session->userdata["student_logged_in"])
										{
											$student_courseData['enroll_type'] = 'deals_package_extra';
										}
										else
										{*/
											$student_courseData['enroll_type'] = 'deals_package';
										/*}*/
									}
									elseif($this->session->userdata('enrolling_rep_code'))
									{
										$student_courseData['enroll_type'] = 'rep_package';
									}
									else
									{
										/*if($this->session->userdata["student_logged_in"])
										{
											$student_courseData['enroll_type'] = 'payment_package_extra';
										}
										else
										{*/
											$student_courseData['enroll_type'] = 'payment_package';
										/*}*/
									}
									
									$student_courseData['course_status'] = '0';	
									
									
								//	print_r($student_courseData);
															
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
																				
										$ebook_id = $this->package_model->get_ebook_id_for_course_id($this->session->userdata('package_applying_course'),$studentdata['lang_id']);
										$ebook_subs_details['user_id'] 		= $user_id;
										$ebook_subs_details['product_id']	 = $eb_opt->id;
										$ebook_subs_details['ebook_id'] 	   = $ebook_id;
										$ebook_subs_details['date_purchased'] = $dateNow;
										$ebook_subs_details['payment_id'] 	 = $paymentId;									
										$ebook_subs_details_id = $this->ebook_model->addSubscription_user($ebook_subs_details);				
										
															
										$row_new = $this->email_model->getTemplateById('ebook_download_link',$studentdata['lang_id']);
										foreach($row_new as $row1)
										{
											
											$emailSubject = $row1->mail_subject;
											$mailContent = $row1->mail_content;
										}
											 
										if($studentdata['lang_id']==3)
										{
											$mailContent = str_replace ( "#firstname#",$studentdata['first_name'], $mailContent );
											$mailContent = str_replace ( "#click here#","<a href='".base_url()."home/ebookDownload/user'>clica aquí</a>", $mailContent );
										}									
										else
										{
											$mailContent = str_replace ( "#firstname#",$studentdata['first_name'], $mailContent );
											$mailContent = str_replace ( "#click here#","<a href='".base_url()."/home/ebookDownload/user'>click here</a>", $mailContent );
										}
										$to_mail = $studentdata['email'];
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
								
								
								if(isset($this->session->userdata['deals']['vCode']))
								{
								
								$vouchercode = $this->session->userdata['deals']['vCode'];	
								$voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);
								$webVoucherId = $this->gift_voucher_model->getVoucherWebIdByVcode($vouchercode);
					
								$couponDetails=array();
								$couponDetails['user_id']=$user_id;						
								$couponDetails['course_id']=$cart_det->selected_item_ids;							
								$couponDetails['coupon_code']=$content['coupon_code'];	
								if(isset($content['redemption_code']))
								{		
								$couponDetails['redemption_code']=$content['redemption_code'];
								$couponDetails['pdf_name']=$content['redemption_pdf'];							
								}
								$couponDetails['website_id']=$webVoucherId;
								$couponDetails['date']=$dateNow;							
								$redeemedCoupenId = $this->user_model->add_redeemedCoupon($couponDetails);
								
								$this->common_model->deactivate_voucher_code($this->session->userdata['deals']['vCode']);
									
								}
								else if(isset($this->session->userdata['voucher_code']))
								{
								
								$vouchercode = $this->session->userdata('voucher_code');	
								$voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);
								$webVoucherId = $this->gift_voucher_model->getVoucherWebIdByVcode($vouchercode);
					
								$couponDetails=array();
								$couponDetails['user_id']=$user_id;						
								$couponDetails['course_id']=$cart_det->selected_item_ids;							
								$couponDetails['coupon_code']=$content['coupon_code'];	
								if(isset($content['redemption_code']))
								{		
								$couponDetails['redemption_code']=$content['redemption_code'];
								$couponDetails['pdf_name']=$content['redemption_pdf'];							
								}
								$couponDetails['website_id']=$webVoucherId;
								$couponDetails['date']=$dateNow;							
								$redeemedCoupenId = $this->user_model->add_redeemedCoupon($couponDetails);
								
								$this->common_model->deactivate_voucher_code($this->session->userdata('voucher_code'));
									
								}
								
								elseif($this->session->userdata('enrolling_rep_code'))
								{
									$rep_Code = $this->session->userdata('enrolling_rep_code');						
									$rep_id= $this->user_model->get_rep_idBy_rep_code($rep_Code);
									//echo $rep_id;exit;
									$date_rep=date("Y-m-d");
									$rep_data['user_id']=$user_id;
									$rep_data['rep_id']=$rep_id;
									$rep_data['course_id']=$cart_det->selected_item_ids;
									$rep_data['rep_code']=$rep_Code;
									$rep_data['date_rep']=$date_rep;									
									$this->user_model->add_rep_details($rep_data);								
								}
								
									
									/*if(!$this->session->userdata["student_logged_in"])
									{	*/								
									if($new_registration_mail)
									{
									
									$en_studId = $this->encrypt->encode($user_id);//encoding student id
									$row_new = $this->email_model->getTemplateById('new_registration',$langId);									
									foreach($row_new as $row1)
									{										
										$emailSubject = $row1->mail_subject;
										$mailContent = $row1->mail_content;
									}
									if($langId==3)
									{
										$mailContent = str_replace ( "#firstname#",$studentdata['first_name'], $mailContent );									
										$mailContent = str_replace ( "#url#", "<a href='".base_url()."'>Eventtrix</a>", $mailContent );
										$mailContent = str_replace ( "#username#", $studentdata['username'], $mailContent );
										$mailContent = str_replace ( "#password#", $this->encrypt->decode($studentdata['password']), $mailContent );
									}
									else
									{
										$mailContent = str_replace ( "#firstname#",$studentdata['first_name'], $mailContent );										
										$mailContent = str_replace ( "#url#", "<a href=".base_url()."'>Eventtrix</a>", $mailContent );
										$mailContent = str_replace ( "#username#", $studentdata['username'], $mailContent );
										$mailContent = str_replace ( "#password#", $this->encrypt->decode($studentdata['password']), $mailContent );
									}								  
									  
										$tomail = $studentdata['email'];
										//$tomail = 'ajithupnp@gmail.com';
										
										$this->email->from('info@eventtrix.com', 'Team Eventtrix');
										$this->email->to($tomail); 
										$this->email->cc(''); 
										$this->email->bcc(''); 
										$this->email->subject($emailSubject);
										$this->email->message($mailContent);	
										  
										$this->email->send();
										$new_registration_mail = false;
									}
									/*}
									else
									{
										 $stud_details=$this->user_model->get_stud_details($userId);			 
										 foreach($stud_details as $row){
										  $first_name = $row->first_name;			  
										  $to_mail    = $row->email;										  
										 }
										 										 
										$course_name = $this->common_model->get_course_name($courseId); 
										$row_new = $this->email_model->getTemplateById('new_course',$langId);				
										foreach($row_new as $row1)
										{											
											$emailSubject = $row1->mail_subject;
											$mailContent = $row1->mail_content;
										}
											$mailContent = str_replace ( "#firstname#",$first_name, $mailContent );
											$mailContent = str_replace ( "#course_name#",$course_name, $mailContent );											
											$this->email->from('info@trendimi.com', 'Team Trendimi');
											$this->email->to($to_mail); 
											$this->email->cc(''); 
											$this->email->bcc(''); 
											$this->email->subject($emailSubject);
											$this->email->message($mailContent);
											$this->email->send();
									}*/
							}								
							if($cart_det->product_type == 'package')
							{									
								/*if($this->session->userdata["student_logged_in"])
								{
									$user_id = $this->session->userdata["student_logged_in"];
								}
								else
								{*/
									$user_id = $this->session->userdata('added_user_id');
								/*}*/
								$course_id = $this->session->userdata('package_applying_course');									
								$package_id = $cart_det->selected_item_ids;									
								$package_details = $this->package_model->fetch_package($package_id);															
								$paymentId = $this->user_model->add_payment($paymantDetails);									
								$today =date('Y-m-d');									
								$package_subscriptions['user_id'] = $user_id;
								$package_subscriptions['package_id'] = $package_id;
								$package_subscriptions['course_id'] = $course_id;
								if(isset($this->session->userdata['deals']['vCode']))
								{
									$package_subscriptions['source'] = 'deals_package';
								}
								elseif($this->session->userdata('enrolling_rep_code'))
								{
									$package_subscriptions['source'] = 'rep_package';
								}
								else
								{
									$package_subscriptions['source'] = 'payment_package';
								}
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
			
			}
			
		
			//	echo "User id ".$userId1=$this->session->userdata['student_logged_in']['id'];
						
			//$paymentId = $this->user_model->add_payment($paymantDetails);
			
			
			//exit;

			
			$this->session->unset_userdata('cart_session_id');
			$this->session->unset_userdata('package_applying_course');			
			$this->session->unset_userdata('added_user_id');
			$this->session->unset_userdata('enrolling_rep_code');
			
		//	redirect('home/after_course_package_pay/', 'refresh');
		//	redirect('home/paySuccess/', 'refresh');
		
		$en_user_id = urlencode($this->encrypt->encode($user_id));
		if(isset($this->session->userdata['home_enroll'])){
			if($this->session->userdata['home_enroll']=="home" || $this->session->userdata['home_enroll']=="rep")
			redirect('home/enroll_updation/'.$user_id,'refresh');
			else
			redirect('home/couponSuccess/'.$en_user_id,'refresh');
		}
		else{
		redirect('home/couponSuccess/'.$en_user_id,'refresh');
		}
			
				
				
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