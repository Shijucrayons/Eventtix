<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class addTest10 extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('questionmodel','',TRUE);
		
		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
   		
   		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}
		if($message = $this->session->flashdata('err_msg')){
          $this->err_msg =$message;
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
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		if(isset($this->err_msg))
		$content['err_msg'] = $this->err_msg;
		
	  
		
		// field name, error message, validation rules
		if(isset($_POST['question']))
		{
			
			$testdata  = array();
			
			$testdata['task_id']=$task_id;
		    $testdata['question'] = $content['question'] = $this->input->post('question');
		    $testdata['answer_desc'] = $content['answer_desc'] = $this->input->post('answer_txt');
			
			$this->form_validation->set_rules('question', 'Question', 'required');
			$this->form_validation->set_rules('answer_txt', 'Answer', 'required');
			//$this->form_validation->set_rules('file_1', 'image', 'required');
			//$this->form_validation->set_rules('file_2', 'image', 'required');
			$this->form_validation->set_rules('ansopt', 'ansopt', 'required');
			
			if($this->form_validation->run())
			{	
			
			 $config['upload_path'] = 'public/uploads/taskimages/imageoptions/';
             $config['allowed_types'] = 'gif|jpg|png';
             $config['max_size'] = '10000';
             //$config['max_width']  = '1024';
             //$config['max_height']  = '768';
             $this->load->library('upload', $config);
		     $this->upload->initialize($config);
			//echo "<pre><br>--------FILE-----";print_r($_FILES);
			
		     foreach ($_FILES as $key => $value) 
			 {
			    if (!$this->upload->do_upload($key))
				{
				$errors = $this->upload->display_errors();
				$this->session->set_flashdata("err_msg",$errors);
				redirect('admin/addTest10/addquestion/'.$task_id, 'refresh');
				}
				else
				{
				   $uploaded[] = $this->upload->data();
				}
			}
				
				//echo $uploaded[0]['file_name'];exit;
			// echo "<pre>";print_r($uploaded);exit;
           
			 	$optiondata['question_id'] = $this->questionmodel->add_10_question($testdata);
				

				$optiondata['answeropt']=$this->input->post('ansopt');
				//echo $optiondata['answeropt'][0];exit;
				$count=count($uploaded);
				
				for ($i=0; $i<$count; $i++) {
						$insert['question_id']=$optiondata['question_id'];
						$insert['answer']=$uploaded[$i]['file_name'];
						$j=$i+1;
						//echo $optiondata['answeropt'][0];exit;
						if($j==$optiondata['answeropt'][0]){
							$insert['is_correct']='1';
						}
						else{
							$insert['is_correct']='0';
						}
						$this->questionmodel->add_10_option($insert);

				}

			 	redirect('admin/addTest10/addquestion/'.$task_id, 'refresh');
			
			}
			else
			{
				echo "here";exit;
			}
		}
/*...............................................................*/


        
        $this->db-> select('*');
		$this->db-> from(' imageoption_questions');
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
			   $content['action'][$q] = '<a href="'.base_url().'admin/addTest10/edit/'.$row->id.'/'.$task_id.'">Edit</a>';
			    $content['action'][$q] .=' | <a href="'.base_url().'admin/addTest10/delete/'.$row->id.'/'.$task_id.'">Delete</a>';
				
				$this->db-> select('*');
				$this->db-> from(' imageoption_options');
				$this->db-> where('question_id',$row->id);
				$query1 = $this->db->get();
				
				if($query1 -> num_rows() >0 )
				{
					$result1 = $query1 -> result();
					$a=0;
					foreach($result1 as $row1)
					{
						 $content['options'][$q][$a] = $row1->answer;
						 $content['correct'][$q][$a] = $row1->is_correct;
						
						 $a++;
					}
				}
		
		$q++;	
		}
		}
/*...............................................................*/
	
		$data['view'] = 'viewTest10';
		$content['mode']=0;
		$data['content'] = $content;
		
		$this->load->view('admin/template',$data);
	
	}
	function edit($id,$task_id=NULL)
	{	
	   
		
		$content = array();
		$content['task_id']=$task_id;
		$content['id']=$id;
		$this->db-> select('*');
		$this->db-> from('imageoption_questions');
		$this->db-> where('id',$id);
		$querys = $this->db->get();
			if($querys -> num_rows() >0 )
			{
				$results = $querys -> result();

				foreach($results as $rows)
				{

				$content['question_id']=$rows->id;
				$content['question']=$rows->question;
				$content['answer_txt']=$rows->answer_desc;
				$this->db-> select('*');
				$this->db-> from(' imageoption_options');
				$this->db-> where('question_id',$rows->id);
				$query3 = $this->db->get();
				
				if($query3 -> num_rows() >0 )
				{
					$result3 = $query3 -> result();
					$a=0;
					foreach($result3 as $row3)
					{
						 $content['option_id'][$a] = $row3->option_id;
						 $content['option_img'][$a] = $row3->answer;
						 $content['is_correct'][$a] = $row3->is_correct;
						
						 $a++;
					}
					

				}
		
			
		}
				
			
				
			}
	
		if(isset($_POST['question']))
		{
			
			$testdata  = array();
			
			$testdata['task_id']=$task_id;
		    $testdata['question'] = $content['question'] = $this->input->post('question');
		    $testdata['answer_desc'] = $content['answer_desc'] = $this->input->post('answer_txt');
			
			$this->form_validation->set_rules('question', 'Question', 'required');
			$this->form_validation->set_rules('answer_txt', 'Answer', 'required');
			//$this->form_validation->set_rules('file_1', 'image', 'required');
			//$this->form_validation->set_rules('file_2', 'image', 'required');
			$this->form_validation->set_rules('ansopt', 'ansopt', 'required');
			
			if($this->form_validation->run())
			{	
			
			 $config['upload_path'] = 'public/uploads/taskimages/imageoptions/';
             $config['allowed_types'] = 'gif|jpg|png';
             $config['max_size'] = '10000';
          
             $this->load->library('upload', $config);
		     $this->upload->initialize($config);
			
			$f=1;
		     foreach ($_FILES as $key => $value)
			 {
				if($_FILES['file_'.$f]['error']==0)	 
				{
				//echo $f;
					if (!$this->upload->do_upload($key))
					{
						$errors = $this->upload->display_errors();
						$this->session->set_flashdata('err_msg',$errors);
						echo "<pre>";print_r($errors);exit;
					}
					else
					{
						$uploaded[$f] = $this->upload->data();
					}
				}
				
				
				$f++;
			}
			//echo "<pre>";print_r($uploaded);
			$this->questionmodel->update_10_question($id,$testdata);
				
				$opt_count = count($content['option_id']);
				$total_count = count($_FILES);

				$optiondata['answeropt']=$this->input->post('ansopt');
				
				
				
				for ($i=0; $i<$total_count; $i++) {
					$insert = array();
					$j=$i+1;
					if($i<$opt_count)
					{
						//echo "<br>".$i;
						//echo "entered in i less than option count";
						if($_FILES['file_'.$j]['error']==0)	 
						{
						$insert['answer'] = $uploaded[$j]['file_name'];
						}
						if($j==$optiondata['answeropt'][0]){
							$insert['is_correct']='1';
						}
						else{
							$insert['is_correct']='0';
						}
					}
					else
					{
						//echo "<br>".$i." entered in i greater than option count";
						$insert['question_id']=$content['question_id'];
						$insert['answer']=$uploaded[$j]['file_name'];
						if($j==$optiondata['answeropt'][0]){
							$insert['is_correct']='1';
						}
						else{
							$insert['is_correct']='0';
						}
					}
					
						if($i<$opt_count)
						{
						$this->questionmodel->update_10_option($content['option_id'][$i],$insert);
						}
						else
						{
							$this->questionmodel->add_10_option($insert);
						}

				}
				if($opt_count>$total_count)
				{
					$new_count=$opt_count-$total_count;
					
					for($i=($new_count+1);$i<$opt_count;$i++)
					{
						$this->questionmodel->imageoption_deleteByID($content['option_id'][$i]);
						
					}
					
				}

			 	redirect('admin/addTest10/addquestion/'.$task_id, 'refresh');
			
			}
			
			
		}
/*...............................................................*/


        
        $this->db-> select('*');
		$this->db-> from(' imageoption_questions');
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
			 $content['action'][$q] = '<a href="'.base_url().'admin/addTest10/edit/'.$row->id.'/'.$task_id.'">Edit</a>';
			 $content['action'][$q] .=' | <a href="'.base_url().'admin/addTest10/delete/'.$row->id.'/'.$task_id.'">Delete</a>';
				
				$this->db-> select('*');
				$this->db-> from(' imageoption_options');
				$this->db-> where('question_id',$row->id);
				$query1 = $this->db->get();
				
				if($query1 -> num_rows() >0 )
				{
					$result1 = $query1 -> result();
					$a=0;
					foreach($result1 as $row1)
					{
						 $content['options'][$q][$a] = $row1->answer;
						 $content['correct'][$q][$a] = $row1->is_correct;
						
						 $a++;
					}
				}
		
		$q++;	
		}
		}
/*...............................................................*/
	
		$data['view'] = 'viewTest10';
		$content['mode']=1;
		$data['content'] = $content;
		
		$this->load->view('admin/template',$data);
	
	}
	function delete($id,$task_id){
	 
	  $this->questionmodel->imageoption_questions_delete($id);
	  $this->questionmodel->imageoption_options_delete($id);
	  $this->session->set_flashdata('message', 'Question Deleted');
	  redirect('admin/addTest10/addquestion/'.$task_id, 'refresh');
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

	function fetchdata($task_id){
		

	 //$lang = $_GET['lang'];
		
		/*$this->datatables->select('id,page_title,page_status')
		->add_column('action','<a href="'.base_url().'admin/cms/edit/$1">Edit</a> | <a href="'.base_url().'admin/cms/delete/$1">Delete</a>','id')
		->edit_column('page_status', '<a href="$1" >Disabled</a>' ,'page_status')
		->where('language',$lang)
        ->unset_column('id')
        ->from('cms');  
        echo $this->datatables->generate();
        */
        
        
        $page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		// Get posted data
	   if (isset($_POST['page'])) {
		$page = $_POST['page'];
		}
		if (isset($_POST['sortname'])) {
		$sortname = $_POST['sortname'];
		}
		if (isset($_POST['sortorder'])) {
		$sortorder = $_POST['sortorder'];
		}
		if (isset($_POST['rp'])) {
		$rp = $_POST['rp'];
		}
		
		// Setup paging SQL
		 $pageStart = ($page-1)*$rp;
       
        
        $this->db-> select('*');
		$this->db-> from(' multiple_choice_questions');
		$this->db-> where('task_id',$task_id);
		$this->db->order_by($sortname,$sortorder);
		$this->db->limit($rp,$pageStart);
        $query = $this->db->get();

        
        $data = array();
		$data['page'] = $page;
		 $data['total'] = $query -> num_rows();
		$data['rows'] = array();
		
       
        
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
		



			foreach($result as $row)
			{
			   //print_r($row);die;

				
			   $action = '<a href="'.base_url().'admin/multiplechoice/edit/'.$row->id.'">Edit</a>';
			    $action .=' | <a href="'.base_url().'admin/multiplechoice/delete/'.$row->id.'">Delete</a>';
				
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->question,$row->answer_desc,$action)
			);
			}
			
		}
        

       echo json_encode($data); exit(); 
        
       
           
	}

}