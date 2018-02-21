<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class processpayment extends CI_Controller
{	

	private $ec_action = 'Sale';
 	 
	function __construct()
	{


		parent::__construct();
		/*$paypal_details = array(

			
			/*'API_username' => 'paypal_api1.trendimi.com', 
			'API_signature' => 'AS0FOh49flQhVH0YxoNysc8ykjxQAGfWR5T2KjD.cKIttlpdDfFt38-X', 
			'API_password' => 'EUGBR86MJJKNLL24',
			
			// test account
			 'API_username' => 'bhagat_1322118867_biz_api1.yahoo.com', 
			'API_signature' => 'AHM6a5O0X5frOYSETt40CccvXK0eA0zLNcF63Xqt.YdkGubrA5xlDtXC', 
			'API_password' => '1322118928',
			//'sandbox_status' => false,
		);
		*/
				
		if($this->session->userdata['ip_address'] == '122.174.239.151')
		{
			$session_array = array('paid_from_sandbox_account'=>true);
			$this->session->set_userdata($session_array);
			$paypal_details = array(				
			
			// test account
			'API_username' => 'bhagat_1322118867_biz_api1.yahoo.com', 
			'API_signature' => 'AHM6a5O0X5frOYSETt40CccvXK0eA0zLNcF63Xqt.YdkGubrA5xlDtXC', 
			'API_password' => '1322118928',
			// 'sandbox_status' => false,
			);
		
		}
		else
		{
			$session_array = array('paid_from_sandbox_account'=>false);
			$this->session->set_userdata($session_array);
		
			$paypal_details = array(
		

			'API_username' => 'info_api1.eventtrix.com', 
			'API_signature' => 'AOMQMnv4ArM3RwJXLXErDgS2Xb9pAqDlkRiafuw05QR4DYntkelrbkWb', 
			'API_password' => 'CKTNTS93YME24ZVF',
			
			'sandbox_status' => false,
			);
		}
		
		$this->load->library('session');
		$this->load->helper(array('form'));
		$this->load->model('user_model','',TRUE);
		$this->load->model('offer_model','',TRUE);
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
		
		$product_id = $this->uri->segment(3);
		 
		
		if($product_id =='')
		{
			$product_id = $this->input->post('product_id');
		}
		
		//extra args used for resit options $taskId-$course_id-$coursePageId-$unit_id.-$slPage.-.$ref);
		$ar_1=	$this->uri->segment(4);
		$ar_2=	$this->uri->segment(5);
		$ar_3=	$this->uri->segment(6);
		$ar_4=	$this->uri->segment(7);
		$ar_5=	$this->uri->segment(8);
		$ar_6=	$this->uri->segment(9);

		$student_id = $this->input->post('stud_id');
		$currency_id = $this->input->post('currency_id');
		$course_id  = $this->input->post('course_id');
		
		
		$product_id_temp = $this->input->post('product_id');	
		if($product_id_temp !='')
		{
			$product_id = $product_id_temp;
		}
		
		
		
	/*	echo "<br>product id ".$product_id;		
		echo "<br>currency id ".$ar_2;		*/
		
		if($product_id==1)
		$product_details =$this->common_model->getProductDetail($product_id,$ar_2);
		else
		//$product_details =$this->common_model->getProductDetail($product_id,$currency_id);
		$product_details =$this->common_model->getProductDetail($product_id,$ar_1);
		
		
		//echo "<pre>"; print_r($product_details); exit;

		if($product_details['item']=='extend_voucher')
		{
		  if(isset($_POST['name'])&&isset($_POST['email']))
		  {

			  $in_array['name'] = $this->input->post('name');
			  $in_array['email']=$this->input->post('email');
			  $in_array['voucher']= $ar_1;
			  $in_array['date'] = date('Y-m-d');
			  $in_array['status'] = 'not_paid';
			  $this->db->insert('giftvoucher_extend_user',$in_array);
			  $gift_paid_id = $this->db->insert_id();
		  }
		  else
		  {
			  $this->session->set_flashdata('error_msg',"Please fill all mandatory fields.");
			redirect('sales/extend_coupon/'.$ar_1);
		  }
		}
		
		
		
		
		
		/*echo "In payment";
		echo "User id ".$student_id;
		echo "<br>Currency id ".$currency_id;
		echo "<br>Product id  ".$product_id;
		echo "<br>Course id  ".$course_id;
		exit;*/
		
		
		
		
		/*echo "<pre>arg_1 : ".$ar_1."<br>";
		print_r($product_details);*/
		
		
		switch ($product_details['item'])
		{
			
			case 'course':
			{
				$course_id = $product_details['itemId'];
				$payment_conf = array(
				'desc' => 'Course',
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('processpayment/processpay/course_withreg/'.$ar_1.'/'.$course_id), 
				'cancel_URL' => site_url('/home/enroll_2/'.$course_id.'/'.$ar_1), 
				);
				
				$product = array(
				'name' => 'Course:'.$product_details['itemName'], 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $product_details['amount']['amount']
				);
				/*echo "<pre>";print_r($product);print_r($payment_conf);exit;*/

				$payment_conf['products'][] = $product;
				
				break;
			}
			
			case 'extra_course':
			{
				//$course_id = $product_details['itemId'];
				$courseArr = $this->course_model->get_coursedetails($course_id);
				foreach($courseArr as $row2)
				{
					$course_deatails['itemName']=$row2->course_name;
				}
				
				if($this->session->userdata('coupon_applied'))
				{
					//echo "Session set";
					
					/*echo "<pre>";
					print_r($this->session->userdata('coupon_applied_details'));*/
					
					$payment_conf = array(
					'desc' => 'extra_course',
					'currency' =>  $this->session->userdata['coupon_applied_details']['currency_code'], 
					'type' => $this->ec_action, 
					'return_URL' => site_url('processpayment/processpay/extra_course_withreg/'.$student_id.'/'.$course_id), 
					'cancel_URL' => site_url('/home/buy_another_course/stud_id/'.$student_id.'/cour_id/'.$course_id), 
					);	
					
					$product = array(
					'name' => 'Course:'.$course_deatails['itemName'], 
					'quantity' => 1, // simple example -- fixed to 1
					'amount' => $this->session->userdata['coupon_applied_details']['amount']
					);
					
					
					//exit;
				}
				else
				{
				
				
				$payment_conf = array(
				'desc' => 'extra_course',
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('processpayment/processpay/extra_course_withreg/'.$student_id.'/'.$course_id), 
				'cancel_URL' => site_url('/home/buy_another_course/stud_id/'.$student_id.'/cour_id/'.$course_id), 
				);
				
				$product = array(
				'name' => 'Course:'.$course_deatails['itemName'], 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $product_details['amount']['amount']
				);
			     }
				$payment_conf['products'][] = $product;
				break;
				
			}
			
			case 'extension':
			{
				
				//$courseArr = $this->course_model->get_coursedetails($course_id);
				$courseArr = $this->course_model->get_coursedetails($ar_2);
				foreach($courseArr as $row2)
				{				
					$course_name = $row2->course_name;
				}
				
				
				//echo $ar_1.'/'.$ar_2 .'/'.$product_id.'/';exit;
				$payment_conf = array(
				'desc' => 'Course Extension',
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				
				'return_URL' => site_url('processpayment/processpay/course_extension/'.$ar_1.'/'.$ar_2.'/'.$product_id), 
				'cancel_URL' => site_url('/coursemanager/campus'), 
				); 
				$product = array(
				'name' => 'Course Extension :'.$product_details['period'], 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $product_details['amount']['amount']
				);
				
				$payment_conf['products'][] = $product;
				break;
			}
			
			case 'hardcopy':
			{
				/*echo "in Hardcopy";
				echo " : ".$product_details['item'];*/
				//exit;	
					 if(isset($_POST['house_number']))
				{			
				$student_update_data['house_number'] = $apartment  = $this->input->post('house_number');
				$student_update_data['address'] = $address1  = $this->input->post('address1');
				$student_update_data['street'] = $street  = $this->input->post('street');
				//$address2  = $this->input->post('address2');	
				$student_update_data['country_id'] = $country  = $this->input->post('country');
				$student_update_data['zipcode'] = $zip_code  = $this->input->post('zip_code');
				$student_update_data['city'] = $city  = $this->input->post('city');
				$student_update_data['us_states'] = $city  = $this->input->post('state');
				
				$this->user_model->update_student_details($student_update_data,$student_id);
				}									
				$payment_conf = array(
				'desc' => 'Hard copy',
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('processpayment/processpay/hardcopy/'.$student_id.'/'.$course_id.'/'.$product_id), 
				'cancel_URL' => site_url('/coursemanager/campus'), 
				); 
				$product = array(
				'name' => 'Hard copy', 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $product_details['amount']['amount']
				);
				
				$payment_conf['products'][] = $product;
				break;
			}
			case 'proof_completion_hard':
			{
				/*echo "in Hardcopy";
				echo " : ".$product_details['item'];*/
				//exit;	
				 if(isset($_POST['house_number']))
				{			
				$student_update_data['house_number'] = $apartment  = $this->input->post('house_number');
				$student_update_data['address'] = $address1  = $this->input->post('address1');
				$student_update_data['street'] = $street  = $this->input->post('street');
				//$address2  = $this->input->post('address2');	
				$student_update_data['country_id'] = $country  = $this->input->post('country');
				$student_update_data['zipcode'] = $zip_code  = $this->input->post('zip_code');
				$student_update_data['city'] = $city  = $this->input->post('city');
				$student_update_data['us_states'] = $city  = $this->input->post('state');
				
				$this->user_model->update_student_details($student_update_data,$student_id);
				}	
				//echo '<br>Student_id-'.$ar_3.'<br>Course_id-'.$ar_2.'<br>Product_id-'.$product_id; exit;				
				$payment_conf = array(
				'desc' => 'proof of completion',
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('processpayment/processpay/proof_completion_hard/'.$ar_3.'/'.$ar_2.'/'.$product_id), 
				'cancel_URL' => site_url('/coursemanager/campus'), 
				); 
				$product = array(
				'name' => 'proof of completion', 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $product_details['amount']['amount']
				);
				
				$payment_conf['products'][] = $product;
				break;
			}
			case 'ebooks':
			{
				
				/*echo "in ebboks";
				echo " : ".$product_details['item'];*/
			//	exit;
				//$course_id = $product_details['itemId'];
				$payment_conf = array(
				'desc' => 'Holly and Hugo Ebooks',
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('processpayment/processpay/ebooks/'.$student_id.'/'.$product_id), 
				'cancel_URL' => site_url('/home/EbookCart'),
				);
				
				$product = array(
				'name' => 'Ebooks:'.$product_details['itemName'], 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $product_details['amount']['amount']
				);
				
				$payment_conf['products'][] = $product;
				break;
			}
			case 'transcript':
			{
				/*echo "in Hardcopy";
				echo " : ".$product_details['item'];*/
				//exit;	
					 if(isset($_POST['house_number']))
				{			
				$student_update_data['house_number'] = $apartment  = $this->input->post('house_number');
				$student_update_data['address'] = $address1  = $this->input->post('address1');
				$student_update_data['street'] = $street  = $this->input->post('street');
				//$address2  = $this->input->post('address2');	
				$student_update_data['country_id'] = $country  = $this->input->post('country');
				$student_update_data['zipcode'] = $zip_code  = $this->input->post('zip_code');
				$student_update_data['city'] = $city  = $this->input->post('city');
				$student_update_data['us_states'] = $city  = $this->input->post('state');
				
				$this->user_model->update_student_details($student_update_data,$student_id);
				}									
				$payment_conf = array(
				'desc' => 'eTranscript',
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('processpayment/processpay/transcript/'.$student_id.'/'.$course_id.'/'.$product_id), 
				'cancel_URL' => site_url('/coursemanager/campus'), 
				); 
				$product = array(
				'name' => 'eTranscript', 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $product_details['amount']['amount']
				);
				
				$payment_conf['products'][] = $product;
				break;
			}	
			
			
			case 'proof_completion':
			{
				//echo "in Hardcopy";
				//echo " : ".$product_details['item'];
				//exit;	
						 if(isset($_POST['house_number']))
				{			
				$student_update_data['house_number'] = $apartment  = $this->input->post('house_number');
				$student_update_data['address'] = $address1  = $this->input->post('address1');
				$student_update_data['street'] = $street  = $this->input->post('street');
				//$address2  = $this->input->post('address2');	
				$student_update_data['country_id'] = $country  = $this->input->post('country');
				$student_update_data['zipcode'] = $zip_code  = $this->input->post('zip_code');
				$student_update_data['city'] = $city  = $this->input->post('city');
				$student_update_data['us_states'] = $city  = $this->input->post('state');
				
				$this->user_model->update_student_details($student_update_data,$student_id);
				}							
				$payment_conf = array(
				'desc' => 'proof of completion',
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('processpayment/processpay/proof_completion/'.$student_id.'/'.$course_id.'/'.$product_id), 
				'cancel_URL' => site_url('/coursemanager/campus'), 
				); 
				$product = array(
				'name' => 'proof of completion', 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $product_details['amount']['amount']
				);
				
				$payment_conf['products'][] = $product;
				break;
			}
			
			case 'poe_soft':
			{
				/*echo "in Hardcopy";
				echo " : ".$product_details['item'];*/
				//exit;	
					 if(isset($_POST['house_number']))
				{			
				$student_update_data['house_number'] = $apartment  = $this->input->post('house_number');
				$student_update_data['address'] = $address1  = $this->input->post('address1');
				$student_update_data['street'] = $street  = $this->input->post('street');
				//$address2  = $this->input->post('address2');	
				$student_update_data['country_id'] = $country  = $this->input->post('country');
				$student_update_data['zipcode'] = $zip_code  = $this->input->post('zip_code');
				$student_update_data['city'] = $city  = $this->input->post('city');
				$student_update_data['us_states'] = $city  = $this->input->post('state');
				
				$this->user_model->update_student_details($student_update_data,$student_id);
				}									
				$payment_conf = array(
				'desc' => 'proof of enrolment',
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('processpayment/processpay/poe_soft/'.$student_id.'/'.$course_id.'/'.$product_id), 
				'cancel_URL' => site_url('/coursemanager/campus'), 
				); 
				$product = array(
				'name' => 'proof of enrolment', 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $product_details['amount']['amount']
				);
				
				$payment_conf['products'][] = $product;
				break;
			}
			case 'poe_hard':
			{
				/*echo "in Hardcopy";
				echo " : ".$product_details['item'];*/
				//exit;	
				
					 if(isset($_POST['house_number']))
				{			
				$student_update_data['house_number'] = $apartment  = $this->input->post('house_number');
				$student_update_data['address'] = $address1  = $this->input->post('address1');
				$student_update_data['street'] = $street  = $this->input->post('street');
				//$address2  = $this->input->post('address2');	
				$student_update_data['country_id'] = $country  = $this->input->post('country');
				$student_update_data['zipcode'] = $zip_code  = $this->input->post('zip_code');
				$student_update_data['city'] = $city  = $this->input->post('city');
				$student_update_data['us_states'] = $city  = $this->input->post('state');
				
				$this->user_model->update_student_details($student_update_data,$student_id);
				}									
				$payment_conf = array(
				'desc' => 'proof of enrolment',
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('processpayment/processpay/poe_hard/'.$student_id.'/'.$course_id.'/'.$product_id), 
				'cancel_URL' => site_url('/coursemanager/campus'), 
				); 
				$product = array(
				'name' => 'proof of enrolment', 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $product_details['amount']['amount']
				);
				
				$payment_conf['products'][] = $product;
				break;
			}
			
			case 'access':
			{
				/*echo "in Hardcopy";
				echo " : ".$product_details['item'];*/
				//exit;	
								
				$payment_conf = array(
				'desc' => 'Course material access',
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('processpayment/processpay/access/'.$student_id.'/'.$course_id.'/'.$product_id), 
				'cancel_URL' => site_url('/coursemanager/campus'), 
				); 
				$product = array(
				'name' => 'Course material access', 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $product_details['amount']['amount']
				);
				
				$payment_conf['products'][] = $product;
				break;
			}
			
			
			case 'retask':
			{
				//$course_id = $product_details['itemId'];
				$payment_conf = array(
				'desc' => 'Resit task',
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('processpayment/processpay/resit_task/'.$student_id.'/'.$ar_1.'/'.$ar_2.'/'.$ar_3.'/'.$ar_4.'/'.$ar_5.'/'.$ar_6), 
				'cancel_URL' => site_url('/coursemanager/ResitConfirm/'.$student_id.'/'.$ar_1.'/'.$ar_2.'/'.$ar_3.'/'.$ar_4.'/'.$ar_5.'/'.$ar_6), 
				);
				
				$product = array(
				'name' => 'Re-sit:'."Resit a unit", 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $product_details['amount']['amount']
				);
				
				$payment_conf['products'][] = $product;
				break;
			}
			case 'offers':
			{
				$offer_id = $product_details['itemId'];
				$payment_conf = array(
				'desc' => 'Course Upgrade',
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('processpayment/processpay/course_upgrade/'.$offer_id.'/'.$student_id), 
				'cancel_URL' => site_url('/offers/paymentConfirm/'.$offer_id), 
				);
				
				$product = array(
				'name' => 'Course upgrade:'.$product_details['itemName'], 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $product_details['amount']['amount']
				);
				
				$payment_conf['products'][] = $product;
				break;
			}
			case 'extend_voucher':
			{
				$payment_conf = array(
				'desc' => $product_details['itemName'],
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('processpayment/processpay/extend_voucher/'.$product_id.'/'.$ar_1.'/'.$gift_paid_id), 
				'cancel_URL' => site_url('/home/extend_coupon/'.$ar_1),
				);
				
				$product = array(
				'name' => 'Extend:'.$product_details['itemName'], 
				'quantity' => 1, // simple example -- fixed to 1
				'amount' => $product_details['amount']['amount']
				);
				
				$payment_conf['products'][] = $product;
				break;
			}
			
		}
		/*echo "<pre>";
		print_r($payment_conf);exit;*/
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
		
	}
	
	function processpay() {
		
		$token = $_GET['token'];
		$payer_id = $_GET['PayerID'];
		
		$payment_for = $this->uri->segment(3);
		$arg_1 = $this->uri->segment(4);
		$arg_2 = $this->uri->segment(5);
		
		$arg_3 = $this->uri->segment(6);
		$products_id = $this->uri->segment(6);
		$arg_4 = $this->uri->segment(7);
		$arg_5 = $this->uri->segment(8);
		$arg_6 = $this->uri->segment(9);
		$arg_7 = $this->uri->segment(10);
		$arg_8 = $this->uri->segment(11);
		$arg_9 = $this->uri->segment(12);
		$arg_10 = $this->uri->segment(13);
		
		//echo $payment_for."<br>".$arg_1."<br>".$arg_2;exit;
		
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
				//echo "entered in 2<br>".$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
				

				//echo "\nGetExpressCheckoutDetails Data\n" . print_r($get_ec_return, true);
				//echo "\n\nDoExpressCheckoutPayment Data\n" . print_r($do_ec_return, true);
				
				switch ($payment_for){
					
					case 'course_withreg':
					{
						
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						$paymantDetails['product_id']=$this->common_model->getProdectId('course',$arg_2);
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;	
						//$paymantDetails['user_id']=$arg_1; need to update user id after 
						$paymantDetails['date']=$dateNow;
						
						$paymentId = $this->user_model->add_payment($paymantDetails);
						
						redirect('home/process_reg/'.$arg_1.'/'.$paymentId.'/'.$arg_2, 'refresh');
					}
					case 'extra_course_withreg':
					{
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						$paymantDetails['product_id']=$this->common_model->getProdectId('extra_course');
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;	
						//$paymantDetails['user_id']=$arg_1; need to update user id after 
						$paymantDetails['date']=$dateNow;
						
						if($this->session->userdata('coupon_applied'))
		  				{
							$paymantDetails['discount_applied'] ='yes';
							$paymantDetails['discount_id'] =$code_applied_details['discount_id'] = $this->session->userdata['coupon_applied_details']['discount_code_id']; 						
							$code_applied_details['source'] = 'campus';
						}						
						
						$paymentId = $this->user_model->add_payment($paymantDetails);
						if($this->session->userdata('coupon_applied'))
		  				{
							$code_applied_details['product_id'] = $this->common_model->getProdectId('extra_course');
							$code_applied_details['user_id'] = $arg_1;
							$code_applied_details['selected_item_id'] = $arg_2;		
							$code_applied_details['payment_id'] = $paymentId;							
							$this->discount_code_model->add_discount_applied_details($code_applied_details);														
						}
						
						
						redirect('home/process_add_course/'.$arg_1.'/'.$paymentId.'/'.$arg_2, 'refresh');
						
					}
					
					
					case 'course_extension':
					{
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						$paymantDetails['product_id']=$arg_3;
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;	
						//$paymantDetails['user_id']=$arg_1; need to update user id after 
						$paymantDetails['date']=$dateNow;
						
						$paymentId = $this->user_model->add_payment($paymantDetails);
						  
						  //echo "<pre>"; print_r($products_id); exit;
						
						redirect('coursemanager/after_extend_pay/'.$arg_1.'/'.$paymentId.'/'.$arg_2.'/'.$arg_3, 'refresh');
						
					}
					case 'ebooks':
					{
						
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						$paymantDetails['product_id']=$arg_2;
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;	
						//$paymantDetails['user_id']=$arg_1; need to update user id after 
						$paymantDetails['date']=$dateNow;
						
						$paymentId = $this->user_model->add_payment($paymantDetails);
						
						
					
						
						
						redirect('home/afterBuyEbook/'.$paymentId, 'refresh');
					}
					
					case 'hardcopy':
					{
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						$paymantDetails['product_id']=$arg_3;
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;	
						//$paymantDetails['user_id']=$arg_1; need to update user id after 
						$paymantDetails['date']=$dateNow;
						
						$paymentId = $this->user_model->add_payment($paymantDetails);				
						
						
						redirect('coursemanager/after_hardcopy_pay/'.$arg_1.'/'.$paymentId.'/'.$arg_2.'/'.$arg_3, 'refresh');
					}
					
					case 'transcript':
					{
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						$paymantDetails['product_id']=$arg_3;
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;	
						//$paymantDetails['user_id']=$arg_1; need to update user id after 
						$paymantDetails['date']=$dateNow;
						
						$paymentId = $this->user_model->add_payment($paymantDetails);				
						
						
						redirect('sales/after_transcript_pay/'.$arg_1.'/'.$paymentId.'/'.$arg_2.'/'.$arg_3, 'refresh');
					}
					
					case 'proof_completion':
					{
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						$paymantDetails['product_id']=$arg_3;
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;	
						//$paymantDetails['user_id']=$arg_1; need to update user id after 
						$paymantDetails['date']=$dateNow;
						
						$paymentId = $this->user_model->add_payment($paymantDetails);				
						
						
						redirect('sales/after_proof_completion_pay/'.$arg_1.'/'.$paymentId.'/'.$arg_2.'/'.$arg_3, 'refresh');
					}
					
					case 'proof_completion_hard':
					{
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						$paymantDetails['product_id']=$arg_3;
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;	
						$paymantDetails['user_id']=$arg_1; //need to update user id after 
						$paymantDetails['date']=$dateNow;
						/*if($this->session->userdata('paid_from_sandbox_account'))
						{
							$paymantDetails['live_pay']=0;
						}
						else
						{
							$paymantDetails['live_pay']=1;
						}*/
						
						$paymentId = $this->user_model->add_payment($paymantDetails);				
						//echo '<br>Student_id-'.$arg_1.'<br>Payment_id-'.$paymentId.'<br>Course_id-'.$arg_2.'<br>Product_id-'.$arg_3; exit;
						
						redirect('sales/after_proof_completion_hard_pay/'.$arg_1.'/'.$paymentId.'/'.$arg_2.'/'.$arg_3, 'refresh');
					}
					
					case 'poe_soft':
					{
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						$paymantDetails['product_id']=$arg_3;
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;	
						//$paymantDetails['user_id']=$arg_1; need to update user id after 
						$paymantDetails['date']=$dateNow;
						
						$paymentId = $this->user_model->add_payment($paymantDetails);				
						
						
						redirect('sales/after_proof_enrolment_soft_pay/'.$arg_1.'/'.$paymentId.'/'.$arg_2.'/'.$arg_3, 'refresh');
					}	
					case 'poe_hard':
					{
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						$paymantDetails['product_id']=$arg_3;
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;	
						//$paymantDetails['user_id']=$arg_1; need to update user id after 
						$paymantDetails['date']=$dateNow;
						
						
						$paymentId = $this->user_model->add_payment($paymantDetails);				
						
						
						redirect('sales/after_proof_enrolment_hard_pay/'.$arg_1.'/'.$paymentId.'/'.$arg_2.'/'.$arg_3, 'refresh');
					}	
					
					
					
					case 'access':
					{
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						$paymantDetails['product_id']=$arg_3;
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;	
						//$paymantDetails['user_id']=$arg_1; need to update user id after 
						$paymantDetails['date']=$dateNow;
						
						$paymentId = $this->user_model->add_payment($paymantDetails);				
						
						
						redirect('sales/after_course_access_pay/'.$arg_1.'/'.$paymentId.'/'.$arg_2.'/'.$arg_3, 'refresh');
					}
					
					case 'resit_task':
					{
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						$paymantDetails['product_id']=$arg_3;
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;	
						//$paymantDetails['user_id']=$arg_1; need to update user id after 
						$paymantDetails['date']=$dateNow;
						
						$paymentId = $this->user_model->add_payment($paymantDetails);				
						
						
						redirect('coursemanager/Resit_AfterPay/'.$paymentId.'/'.$arg_1.'/'.$arg_2.'/'.$arg_3.'/'.$arg_4.'/'.$arg_5.'/'.$arg_6.'/'.$arg_7.'/'.$arg_8.'/'.$arg_9.'/'.$arg_10, 'refresh');
					}
					case 'course_upgrade':
					{
						
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						$paymantDetails['product_id']=$this->common_model->getProdectId('offers',$arg_1);
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;	
						$paymantDetails['user_id']=$arg_2; 
						$paymantDetails['date']=$dateNow;
						
						$paymentId = $this->user_model->add_payment($paymantDetails);
						
						redirect('offers/upgrade_afterPay/'.$arg_1.'/'.$paymentId, 'refresh');
					}
					case 'extend_voucher':
					{
						
						$dateNow= date('Y-m-d H:i:s', strtotime($do_ec_return['TIMESTAMP']));
						$currCode=$do_ec_return['PAYMENTINFO_0_CURRENCYCODE'];
						$currencyId = $this->common_model->get_currencyId_byCode($currCode);
						$paymantDetails=array();
						$paymantDetails['product_id']=$arg_1;
						$paymantDetails['transaction_id']=$do_ec_return['PAYMENTINFO_0_TRANSACTIONID'];
						$paymantDetails['amount']=$do_ec_return['PAYMENTINFO_0_AMT'];			
						$paymantDetails['currency_id']=$currencyId;	
						//$paymantDetails['user_id']=$arg_1; need to update user id after 
						$paymantDetails['date']=$dateNow;
						
						$paymentId = $this->user_model->add_payment($paymantDetails);
						
						redirect('sales/afterExtendVoucher/'.$paymentId.'/'.$arg_2.'/'.$arg_3, 'refresh');
					}	
					
					
				
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

?>