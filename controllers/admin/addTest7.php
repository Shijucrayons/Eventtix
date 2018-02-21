<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class addTest7 extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('questionmodel','',TRUE);
		
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
        $this->load->database();

    }
	function index()
	{

	}


	function addquestion($task_id=NULL)
	{	
		$content = array();
		$content['task_id']=$task_id;
		
	  	if(isset($this->err_msg))
		$content['err_msg']=$this->err_msg;
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		
		// field name, error message, validation rules
		if(isset($_POST['save']))
		{
			
			$testdata  = array();
			
			
		  //$this->form_validation->set_rules('Image', 'Image', 'required');
		  $this->form_validation->set_rules('ans', 'Options', 'required');
		  $this->form_validation->set_rules('order', 'Answer', 'requered');
					
			if($this->form_validation->run())
			{
	
				$config['upload_path'] = 'public/admin/uploads/radioCompareImg';
				$config['allowed_types'] = 'gif|jpg|png';
				$config['max_size']	= '10000';
				//$config['max_width']  = '1024';
				//$config['max_height']  = '768';
				
				
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
		
				
				
				if ( $this->upload->do_upload('image'))
				{
					$testdata['question']=$this->input->post('ans');
					$testdata['task_id']=$task_id;	
					$optiondata['question_id'] = $this->questionmodel->add_7_question($testdata);
					
					$uploaded = array('upload_data' => $this->upload->data());
					$optiondata['image'] = $uploaded['upload_data']['file_name'];
					$optiondata['task_id']=$task_id;	
					$optiondata['answer_order']=$this->input->post('order');	
					$this->questionmodel->add_7_option($optiondata);
					
				}
				else
				{
					$error['upResult'] = array('error' => $this->upload->display_errors());
					echo "error while uploading <pre>: ";print_r( $error['upResult']);exit;
				}
				redirect('admin/addTest7/addquestion/'.$task_id, 'refresh');
			}
			else
			{
				echo "form validation failed";
			}

				
		}
		
/*...............................................................*/


        
        
			$this->db-> select('*');
			$this->db-> from('radio_image_question');
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
				 $content['action'][$q] = '<a href="'.base_url().'admin/addTest7/edit/'.$row->id.'/'.$row->task_id.'">Edit</a>';
				 $content['action'][$q] .=' | <a href="'.base_url().'admin/addTest7/delete/'.$row->id.'/'.$row->task_id.'">Delete</a>';
					
					$this->db-> select('*');
					$this->db-> from('radio_image');
					$this->db-> where('question_id',$row->id);
					$query1 = $this->db->get();
					
					if($query1 -> num_rows() >0 )
					{
						$result1 = $query1 -> result();
				
						foreach($result1 as $row1)
						{
							 $content['correct'][$q] = $row1->image;
						}
					}
			
			$q++;	
			}
			}
			
			
		
	
	
/*...............................................................*/
	
		$data['view'] = 'viewTest7';
		$content['mode']=0;
		$data['content'] = $content;
		
		$this->load->view('admin/template',$data);
	
	}
	
	function edit($id,$task_id=NULL)
	{	
		$content = array();
		
		if(isset($this->err_msg))
		$content['err_msg']=$this->err_msg;
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		
		$content['task_id']=$task_id;
		$content['id']=$id;
		$this->db-> select('*');
		$this->db-> from('radio_image_question');
		$this->db-> where('id',$id);
		$querys = $this->db->get();
			if($querys -> num_rows() >0 )
			{
				$results = $querys -> result();
				foreach($results as $rows)
				{
				$content['question']=$rows->question;
				$content['question_order']=$rows->question_order;
				}
			}
	  
		
		// field name, error message, validation rules
		if(isset($_POST['save']))
		{
			
			$testdata  = array();
			
			
		  //$this->form_validation->set_rules('Image', 'Image', 'required');
		  $this->form_validation->set_rules('ans', 'Options', 'required');
		  $this->form_validation->set_rules('order', 'Answer', 'requered');
					
			if($this->form_validation->run())
			{
	
				$config['upload_path'] = 'public/admin/uploads/radioCompareImg';
				$config['allowed_types'] = 'gif|jpg|png';
				$config['max_size']	= '10000';
				//$config['max_width']  = '1024';
				//$config['max_height']  = '768';
				
				
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
		
				if(!empty($_POST['ans']))
				{
				$testdata['question']=$this->input->post('ans');
				$testdata['task_id']=$task_id;	
				$this->questionmodel->update_7_question($id,$testdata);
				if ( $this->upload->do_upload('image'))
				{
					
					
					$optiondata['question_id'] = $id;
					$this->db-> select('*');
		            $this->db-> from('radio_image');
		            $this->db-> where('question_id',$id);
		            $querys1 = $this->db->get();
			        if($querys1 -> num_rows() >0 )
			           {
				          $results1 = $querys1 -> result();
				           foreach($results1 as $rows1)
				                 {	 
							   $content['id']=$rows1->id;
				               $content['image']=$rows1->image;
				               $content['answer_order']=$rows1->answer_order;
				                  }
			            }
					$uploaded = array('upload_data' => $this->upload->data());
					$optiondata['image'] = $uploaded['upload_data']['file_name'];
					$optiondata['task_id']=$task_id;	
					$optiondata['answer_order']=$this->input->post('order');	
					$this->questionmodel->update_7_option($id,$optiondata);
					
				}
				else
				{
					$error['upResult'] = array('error' => $this->upload->display_errors());
					//echo "error while uploading <pre>: ";print_r( $error['upResult']);exit;
					$this->session->set_flashdata('err_msg',$error['upResult']);
				}
				}
				else
				{
					$this->session->set_flashdata('err_msg','Please fill mandatory fields');
					redirect('','refresh');
				}
				redirect('admin/addTest7/addquestion/'.$task_id, 'refresh');
			}
			else
			{
				echo "form validation failed";
			}

				
		}
		
/*...............................................................*/


        
        
			$this->db-> select('*');
			$this->db-> from('radio_image_question');
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
				 $content['action'][$q] = '<a href="'.base_url().'admin/addTest7/edit/'.$row->id.'/'.$row->task_id.'">Edit</a>';
				 $content['action'][$q] .=' | <a href="'.base_url().'admin/addTest7/delete/'.$row->id.'/'.$row->task_id.'">Delete</a>';
					
					$this->db-> select('*');
					$this->db-> from('radio_image');
					$this->db-> where('question_id',$row->id);
					$query1 = $this->db->get();
					
					if($query1 -> num_rows() >0 )
					{
						$result1 = $query1 -> result();
				
						foreach($result1 as $row1)
						{
							 $content['correct'][$q] = $row1->image;
						}
					}
			
			$q++;	
			}
			}
			
			
		
	
	
/*...............................................................*/
	
		$data['view'] = 'viewTest7';
		$content['mode']=1;
		$data['content'] = $content;
		
		$this->load->view('admin/template',$data);
	
	}

	function delete($id,$task_id)
	{
		 
	  $this->questionmodel->radio_image_question_delete($id);
	  $this->questionmodel->radio_image_delete($id);
	  $this->session->set_flashdata('message', 'Question Deleted');
	  redirect('admin/addTest7/addquestion/'.$task_id, 'refresh');
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
	
	function valid_order($val,$task)
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