<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class addTest12 extends CI_Controller {

	 
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
		if($up_errs = $this->session->flashdata('up_errs')){
          $this->up_errs =$up_errs;
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
		if(isset($this->up_errs))
		{
			$data['up_errs'] =$this->up_errs;
			/*$data['up_errs'] ='';
			for($up=0;$up<count($this->up_errs);$up++)
			$data['up_errs'] .= $arr[$up];*/
		}
		if(isset($this->err_msg))
		$data['err_msg'] = $this->err_msg;
		if(isset($this->flashmessage))
		$data['flashmessage'] = $this->flashmessage;
		
		if(isset($_POST['submit']))
		{
			$config['upload_path'] = 'public/admin/uploads/dressupradio';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '1000';
					
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
	        //echo "<pre>";print_r($_FILES);exit;

			foreach ($_FILES as $key => $value) {
			if (!$this->upload->do_upload($key)) {
				$errors = $this->upload->display_errors();
				echo "<pre><br>--------errors-----";print_r($errors);
				} else {
					
				   $uploaded[] = $this->upload->data();
				}
			}
			
			$question =$this->input->post('question1');
			$opt1=$this->input->post('opt_1');
			$opt2=$this->input->post('opt_2');
			//echo $opt1.'/'.$opt2;exit;
			$cur_1 = 1;
			$cur_2 = 1;
			$cur_3 = 1;
			$cur_4 = 1;
			if($opt1==2)
			$cur_1 = 0;
			else
			$cur_2 = 0;
			if($opt2==2)
			$cur_3 = 0;
			else
			$cur_4 = 0;
			 
			 $questionId = $this->questionmodel->add_12_question($question,$uploaded[0]['file_name'],$task_id);
			  if($questionId !=''){
				 $this->questionmodel->add_12_options($questionId,$uploaded[1]['file_name'],1,$cur_1);
				 $this->questionmodel->add_12_options($questionId,$uploaded[2]['file_name'],1,$cur_2);
				 $this->questionmodel->add_12_options($questionId,$uploaded[3]['file_name'],2,$cur_3);
				 $this->questionmodel->add_12_options($questionId,$uploaded[4]['file_name'],2,$cur_4);
			  }
				 redirect('admin/addTest12/addquestion/'.$task_id);
				
		}
		$this->db->select('*');
		$this->db->from('dressup_radio_questions');
		$this->db->where('tasks_idtasks',$task_id);
		$query = $this->db->get();
		$result = $query->result();
		$i=0;
		foreach($result as $row)
		{
			$data['questions']['question_id'][$i] = $row->id;
			$data['questions']['question_image'][$i]       = base_url().'/public/admin/uploads/dressupradio/'.$row->image_path;
			$data['questions']['question'][$i]    =$row->question;
			$data['questions']['action'][$i] = '<a href="'.base_url().'admin/addTest12/edit/'.$row->id.'/'.$task_id.'">Edit</a>';
			$data['questions']['action'][$i] .=' | <a href="'.base_url().'admin/addTest12/delete/'.$row->id.'/'.$task_id.'">Delete</a>';
				$this->db->select('*');
				$this->db->from('dressup_radio_questions_answers');
				$this->db->where('question_id',$row->id);
				$query_op = $this->db->get();
				$result_op = $query_op->result();
				$j=0;
				foreach($result_op as $row1)
				{
					$data['questions']['option_id'][$i][$j]    = $row1->id;
					$data['questions']['image_option'][$i][$j] = $row1->answer_image_path;
					$data['questions']['is_correct'][$i][$j] = $row1->correct;
				$j++;
			}
			$i++;
		}
		
		$data['task_id'] = $task_id;
		$data['view'] = 'viewTest12';
	
		$content['mode']=0;
		$data['content'] = $content;
		
		$this->load->view('admin/template',$data);
	
	}
	
		function edit($id,$task_id=NULL)
	{
		
		$content=array();
		
		if(isset($this->up_errs))
		{
			$arr =$this->up_errs;
			$data['up_errs'] ='';
			for($up=0;$up<count($this->up_errs);$up++)
			$data['up_errs'] .= $arr[$up];
		}
		if(isset($this->err_msg))
		$data['err_msg'] = $this->err_msg;
		if(isset($this->flashmessage))
		$data['flashmessage'] = $this->flashmessage;
		
		$this->db->select('*');
		$this->db->from('dressup_radio_questions');
		$this->db->where('task_id',$task_id);
		$querys = $this->db->get();
		$results = $querys->result();
		foreach($results as $rows)
		{
			
			$content['question_id']= $rows->id;
			$content['question_image']= base_url().'/public/admin/uploads/dressupradio/'.$rows->image_path;
			$content['question']=$rows->question;
			    $this->db->select('*');
				$this->db->from('dressup_radio_questions_answers');
				$this->db->where('question_id',$rows->id);
				$query_op_edit = $this->db->get();
				$result_op_edit = $query_op_edit->result();
				$j=0;
				foreach($result_op_edit as $row_edit)
				{
					$content['option_id'][$j]    = $row_edit->id;
					$content['image_option'][$j] = $row_edit->answer_image_path;
					$content['is_correct'][$j] = $row_edit->correct;
				$j++;
			}
			
		}
		
		if(isset($_POST['submit']))
		{
			$config['upload_path'] = 'public/admin/uploads/dressupradio';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '1000';
					
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
	       // echo "<pre>";print_r($_FILES);exit;
			
			$im=0;
			foreach ($_FILES as $key => $value) {
				if($_FILES[$key]['error']!=4)
				{
					if (!$this->upload->do_upload($key))
					{
						
					}
					else
					{
						$uploaded[$im] = $this->upload->data();
					}
					$errors = $this->upload->display_errors();
				}
				$im++;
			}
			if(isset($errors))
			$this->session->set_flashdata('up_errs',$errors);
			
			$question =$this->input->post('question1');
			$opt1=$this->input->post('opt_1');
			$opt2=$this->input->post('opt_2');
			//echo $opt1.'/'.$opt2;exit;
			$cur_1 = 1;
			$cur_2 = 1;
			$cur_3 = 1;
			$cur_4 = 1;
			if($opt1==2)
			$cur_1 = 0;
			else
			$cur_2 = 0;
			if($opt2==2)
			$cur_3 = 0;
			else
			$cur_4 = 0;
			
			if(isset($uploaded[0]['file_name']))
			$question_image = $uploaded[0]['file_name'];
			else
			$question_image = $results[0]->image;
			
			
			if(isset($uploaded[1]['file_name']))
			{
				$option_image[0] = $uploaded[1]['file_name'];
			}
			else
			{
				$option_image[0] = $content['image_option'][0];
			}
			if(isset($uploaded[2]['file_name']))
			{
				$option_image[1] = $uploaded[2]['file_name'];
			}
			else
			{
				$option_image[1] = $content['image_option'][1];
			}
			if(isset($uploaded[3]['file_name']))
			{
				$option_image[2] = $uploaded[3]['file_name'];
			}
			else
			{
				$option_image[2] = $content['image_option'][2];
			}
			if(isset($uploaded[4]['file_name']))
			{
				$option_image[3] = $uploaded[4]['file_name'];
			}
			else
			{
				$option_image[3] = $content['image_option'][3];
			}
			 
			  $this->questionmodel->update_add_12_question($id,$question,$question_image,$task_id);
			  if($id !=''){
				 $this->questionmodel->update_add_12_options($content['option_id'][0],$id,$option_image[0],1,$cur_1);
				 $this->questionmodel->update_add_12_options($content['option_id'][1],$id,$option_image[1],1,$cur_2);
				 $this->questionmodel->update_add_12_options($content['option_id'][2],$id,$option_image[2],2,$cur_3);
				 $this->questionmodel->update_add_12_options($content['option_id'][3],$id,$option_image[3],2,$cur_4);
			  }
				 redirect('admin/addTest12/addquestion/'.$task_id);
				
		}
		$this->db->select('*');
		$this->db->from('dressup_radio_questions');
		$this->db->where('task_id',$task_id);
		$query = $this->db->get();
		$result = $query->result();
		$i=0;
		foreach($result as $row)
		{
			$data['questions']['question_id'][$i] = $row->id;
			$data['questions']['question_image'][$i]       = base_url().'/public/admin/uploads/dressupradio/'.$row->image_path;
			$data['questions']['question'][$i]    =$row->question;
			$data['questions']['action'][$i] = '<a href="'.base_url().'admin/addTest12/edit/'.$row->id.'/'.$task_id.'">Edit</a>';
			$data['questions']['action'][$i] .=' | <a href="'.base_url().'admin/addTest12/delete/'.$row->id.'/'.$task_id.'">Delete</a>';
				$this->db->select('*');
				$this->db->from('dressup_radio_questions_answers');
				$this->db->where('question_id',$row->id);
				$query_op = $this->db->get();
				$result_op = $query_op->result();
				$j=0;
				foreach($result_op as $row1)
				{
					$data['questions']['option_id'][$i][$j]    = $row1->id;
					$data['questions']['image_option'][$i][$j] = $row1->answer_image_path;
					$data['questions']['is_correct'][$i][$j] = $row1->correct;
				$j++;
			}
			$i++;
		}
		
		$data['task_id'] = $task_id;
		$data['view'] = 'viewTest12';
	
		$content['mode']=1;
		$data['content'] = $content;
		
		$this->load->view('admin/template',$data);
	
	}

	function delete($id,$task_id){
	 
	  $this->questionmodel->template12_question_delete($id);
	  $this->questionmodel->template12_image_delete($id);
	  $this->session->set_flashdata('message', 'Question Deleted');
	  redirect('admin/addTest12/addquestion/'.$task_id, 'refresh');
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
	
	function isExist($val,$taskId)
	{
		if($val=='')
		{
			 $this->form_validation->set_message('option', 'Please fill the option field');
                return FALSE;
		}
		else if($this->questionmodel->getOptionInTask($val,$taskId)==TRUE)
		{
			$this->form_validation->set_message('Option', 'This option already used in this task');
                return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

}