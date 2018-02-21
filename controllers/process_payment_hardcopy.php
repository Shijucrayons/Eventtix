<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class process_payment_hardcopy extends CI_Controller
{	

	private $ec_action = 'Sale';
 	 
	function __construct()
	{

		parent::__construct();
		$paypal_details = array(
		
			'API_username' => 'paypal_api1.icoes.org', 
			'API_signature' => 'AwekVwebMNeVgVPsgKVUvwyV2pBOAIa-W484b7b-w0eZSqT3FFinvzmQ', 
			'API_password' => 'HWLC8F42PV5YJ38D',
		

			// test account
			/*'API_username' => 'bhagat_1322118867_biz_api1.yahoo.com', 
			'API_signature' => 'AHM6a5O0X5frOYSETt40CccvXK0eA0zLNcF63Xqt.YdkGubrA5xlDtXC', 
			'API_password' => '1322118928',*/
			
			 'sandbox_status' => false,
		);
		
		$this->load->library('session');
		$this->load->helper(array('form'));
		$this->load->model('user_model','',TRUE);
		$this->load->model('offer_model','',TRUE);
		
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
		
		
        $product_details =$this->common_model->getProductDetail($product_id,$currency_id);	
	
		
		switch ($product_details['item'])
		{
			
			case 'hardcopy':
			{
				/*echo "in Hardcopy";
				echo " : ".$product_details['item'];*/
				//exit;	
				
				if(isset($_POST['house_number']))
				{			
				$student_update_data['house_number'] = $apartment  = $this->input->post('house_number');
				$student_update_data['address'] = $address1  = $this->input->post('address1');
				//$address2  = $this->input->post('address2');	
				$student_update_data['country_id'] = $country  = $this->input->post('country');
				$student_update_data['zipcode'] = $zip_code  = $this->input->post('zip_code');
				$student_update_data['city'] = $city  = $this->input->post('city');
				
				$this->user_model->update_student_details($student_update_data,$student_id);
				}
								
				$payment_conf = array(
				'desc' => 'Hard copy',
				'currency' => $product_details['currency_code'], 
				'type' => $this->ec_action, 
				'return_URL' => site_url('process_payment_hardcopy/processpay/hardcopy/'.$student_id.'/'.$course_id.'/'.$product_id), 
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
			
			
			
		}
		
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
						
						
						redirect('coursemanager/after_hardcopy_pay_new_payment/'.$arg_1.'/'.$paymentId.'/'.$arg_2.'/'.$arg_3, 'refresh');
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