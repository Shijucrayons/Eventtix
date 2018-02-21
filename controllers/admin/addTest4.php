<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class addTest4 extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('questionmodel','',TRUE);
		
		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
   		
   		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}

   		$this->load->helper(array('form'));
		$this->load->library('form_validation');
        $this->load->database();

    }
	function index()
	{

	}


	function addquestion($task_id=NULL)
	{	
		$content = array();
		$content['task_id']=$task_id;
		
	  
		
		// field name, error message, validation rules
		if(isset($_POST['dropType']))
		{
			
			$testdata  = array();
			if($this->input->post('dropType')==0)
			{
				$this->form_validation->set_rules('question1', 'Question', 'required');
				$this->form_validation->set_rules('ans1', 'Options', 'required');
				$this->form_validation->set_rules('ansopt1', 'Correct option', 'required');
			}
			else if($this->input->post('dropType')==1)
			{
				$this->form_validation->set_rules('question2', 'Question and options', 'required');
			}
			else if($this->input->post('dropType')==2)
			{
				//$this->form_validation->set_rules('question3', 'Question', 'required');
				$this->form_validation->set_rules('ans3', 'Options', 'required');
			}
			else if($this->input->post('dropType')==3)
			{
				//$this->form_validation->set_rules('question4', 'Question', 'required');
				$this->form_validation->set_rules('ans4', 'Options', 'required');
				$this->form_validation->set_rules('ansopt4', 'Correct option', 'required');
			}
			else if($this->input->post('dropType')==4)
			{
				//$this->form_validation->set_rules('question5', 'Question', 'required');
				$this->form_validation->set_rules('ans5', 'Options', 'required');
				$this->form_validation->set_rules('ansopt5', 'Correct option', 'required');
			}
		
			$testdata['task_id']=$task_id;			
			//echo $this->input->post('dropType');
			
						
			if($this->form_validation->run())
			{	
				$dropType = $this->input->post('dropType');
				
			
				if($this->input->post('dropType')==0)//adding question ans options of single dropdown
			{
				$testdata['question'] = $content['question1'] = $this->input->post('question1');
				$testdata['type'] = $dropType;
			 	$optiondata['question_id'] = $this->questionmodel->add_4_question($testdata);
			 	
				$optiondata['answeropt']=$this->input->post('ansopt1');
				$optiondata['answer']=$this->input->post('ans1');
				$count=count($optiondata['answer']);
				
				for ($i=0; $i<$count; $i++) {
						$insert['question_id']=$optiondata['question_id'];
						$insert['answer']=$optiondata['answer'][$i];
						
						
						if(($i+1)==$optiondata['answeropt']){
							$insert['is_correct']='1';
						}
						else{
							$insert['is_correct']='0';
						}
						
						//echo $insert['is_correct'];
						$this->questionmodel->add_4_option($insert);

				}

			 	redirect('admin/addTest4/addquestion/'.$task_id.'/'.$dropType, 'refresh');
			
			
			}
			
			
          else if($this->input->post('dropType')==1)//adding question ans options of multiple dropdown
          {
	     $testdata['question'] = $content['question2'] = $this->input->post('question2');
	     $testdata['type'] = $dropType;
	     $optiondata['question_id'] = $this->questionmodel->add_4_question($testdata);
	
	    $en=array("{","}");
	    $summaryques=$this->input->post('question2');
	
	    $splitWords=explode("{",$summaryques);//print_r($splitWords);
	    for($word=0;$word<count($splitWords);$word++)
	    {
		  if(strstr($splitWords[$word],'}'))
		  { 
		  $gap=explode("}",$splitWords[$word]);
		  //$res=$objTest->addOptions_dropDown($gap[0] ,$answerFlag,$questionId);
		 
		  $insert['question_id']=$optiondata['question_id'];
		  $insert['answer']=addslashes($gap[0]);
		  $insert['is_correct']='1';
		  $this->questionmodel->add_4_option($insert);
		 }
    	}	
	
	redirect('admin/addTest4/addquestion/'.$task_id.'/'.$dropType, 'refresh');				

   }
			
							else if($this->input->post('dropType')==2)//adding question ans options of image left dropdown right(4)
							{
								//echo "entered in 2";exit;
								//pictue upload 
								
								$config['upload_path'] = 'public/uploads/coursecontent/uploads/imagedecade/';
								$config['allowed_types'] = 'gif|jpg|png';
								$config['max_size']	= '1000';
								//$config['max_width']  = '1024';
								//$config['max_height']  = '768';
								
								
								$this->load->library('upload', $config);
								$this->upload->initialize($config);
						
								
								if ( $this->upload->do_upload('question3'))
								{
									$uploaded = array('upload_data' => $this->upload->data());
									$testdata['question'] = $uploaded['upload_data']['file_name'];
									$testdata['type'] = $dropType;
									$optiondata['question_id'] = $this->questionmodel->add_4_question($testdata);
									
								}
								else
								{
									$error['upResult'] = array('error' => $this->upload->display_errors());
									//echo "err condition<pre>";var_dump($error['upResult']);exit;
								}
					
												
								$optiondata['answeropt']=$this->input->post('ansopt3');
								$optiondata['answer']=$this->input->post('ans3');
								$count=count($optiondata['answer']);
								
								for ($i=0; $i<$count; $i++) {
										$insert['question_id']=$optiondata['question_id'];
										$insert['answer']=$optiondata['answer'][$i];
										
										if(($i+1)==$optiondata['answeropt']){
											$insert['is_correct']='1';
										}
										else{
											$insert['is_correct']='0';
										}
														
										//echo $insert['is_correct'];
										$this->questionmodel->add_4_option($insert);
				
								}
				
								redirect('admin/addTest4/addquestion/'.$task_id, 'refresh');
							
							
							
							}
			
else if($this->input->post('dropType')==3)//adding question ans options of image left dropdown right(2)
{
	//echo "entered in 3";
			$config1['upload_path'] = 'public/uploads/coursecontent/uploads/imagedecade/';
			$config1['allowed_types'] = 'gif|jpg|png';
			$config1['max_size']	= '10000';
			//$config['max_width']  = '1024';
			//$config['max_height']  = '768';
			
			
			$this->load->library('upload', $config1);
			$this->upload->initialize($config1);
	
			
			if ( $this->upload->do_upload('question4'))
			{
				$uploaded = array('upload_data' => $this->upload->data());
				$testdata['question'] = $uploaded['upload_data']['file_name'];
				$testdata['type'] = $dropType;
				$optiondata['question_id'] = $this->questionmodel->add_4_question($testdata);
  
		
			$optiondata['answeropt']=$this->input->post('ansopt4');
			$optiondata['answer']=$this->input->post('ans4');
			$count=count($optiondata['answer']);
			
			for ($i=0; $i<$count; $i++) {
					$insert['question_id']=$optiondata['question_id'];
					$insert['answer']=$optiondata['answer'][$i];
					
					if(($i+1)==$optiondata['answeropt']){
						$insert['is_correct']='1';
					}
					else{
						$insert['is_correct']='0';
					}
									
					//echo $insert['is_correct'];
					$this->questionmodel->add_4_option($insert);
			
	}
	}
	else
	{
		$error['upResult'] = array('error' => $this->upload->display_errors());
		//echo "err condition<pre>";var_dump($error['upResult']);exit;
	}
	
					

	
	redirect('admin/addTest4/addquestion/'.$task_id, 'refresh');
	
	
}
			
								else if($this->input->post('dropType')==4)//adding question ans options of image top dropdown bottom type
								{
								//echo "entered in 2";exit;
								//pictue upload 
								
								$config2['upload_path'] = 'public/uploads/coursecontent/uploads/imagedecade/';
								$config2['allowed_types'] = 'gif|jpg|png|jpeg';
								$config2['max_size']	= '1000';
								//$config['max_width']  = '1024';
								//$config['max_height']  = '768';
								
								
								$this->load->library('upload', $config2);
								$this->upload->initialize($config2);
						
								
								if ( $this->upload->do_upload('question5'))
								{
									$uploaded = array('upload_data' => $this->upload->data());
									$testdata['question'] = $uploaded['upload_data']['file_name'];
									$testdata['type'] = $dropType;
									$optiondata['question_id'] = $this->questionmodel->add_4_question($testdata);
									
									$optiondata['answeropt']=$this->input->post('ansopt5');
									$optiondata['answer']=$this->input->post('ans5');
									$count=count($optiondata['answer']);
									
									for ($i=0; $i<$count; $i++) {
											$insert['question_id']=$optiondata['question_id'];
											$insert['answer']=$optiondata['answer'][$i];
											
											if(($i+1)==$optiondata['answeropt']){
												$insert['is_correct']='1';
											}
											else{
												$insert['is_correct']='0';
											}
															
											//echo $insert['is_correct'];
											$this->questionmodel->add_4_option($insert);
								}
									
								}
								else
								{
									$error['upResult'] = array('error' => $this->upload->display_errors());
									//echo "err condition<pre>";var_dump($error['upResult']);exit;
								}
					
												
								
								
				
								redirect('admin/addTest4/addquestion/'.$task_id, 'refresh');
							
							
							
							}
			
		}
		else
		{
			echo "form validation failed";exit;
		}
 
	}
	else if($_POST)
	{
		echo "no drop type";exit;
	}
	
/*...............................................................*/
		$this->db-> select('*');
		$this->db-> from(' dropdown_questions');
		$this->db-> where('task_id',$task_id);
		$query = $this->db->get();
		
		$data = array();
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			$q=0;
			foreach($result as $row)
			{
			 $content['questions'][$q] = $row->question;
			 $content['tasktype'] = $row->type;
			   $content['action'][$q] = '<a href="'.base_url().'admin/addTest4/edit/'.$row->id.'">Edit</a>';
			    $content['action'][$q] .=' | <a href="'.base_url().'admin/addTest4/delete/'.$row->id.'">Delete</a>';
				
				$this->db-> select('*');
				$this->db-> from(' dropdown_options');
				$this->db-> where('question_id',$row->id);
				$query1 = $this->db->get();
				
				if($query1 -> num_rows() >0 )
				{
					$result1 = $query1 -> result();
					$a=0;
					foreach($result1 as $row1)
					{
						 $content['answer'][$q][$a] = $row1->answer;
						 $content['correct'][$q][$a] = $row1->is_correct;
						 $a++;
					}
				}
		
		$q++;	
		}
		}
	
		$data['view'] = 'viewTest4';
		$content['mode']=0;
		$data['content'] = $content;
		//echo "<pre>";print_r($content);exit;
		$this->load->view('admin/template',$data);
	
	}

	function validate($val,$name)
	{
		if($val=='')
		{
			 $this->form_validation->set_message('validate', 'Please Select the '.$name.' field');
                return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	
}