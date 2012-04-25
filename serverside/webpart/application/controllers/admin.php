<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller 
{
	
	public function approve()
	{
		if(!$this->input->post('relation'))
		{
			header("Location:http://nti.goodroads.ru/");
		}
		else
		{
			//Если тот , кото вызвал функцию является администратором
			//то ебашим approve
			if($this->session->userdata('id')!=null && $this->session->userdata('id')>0 && $this->session->userdata('rights')==3)
			{
					$this->load->model('admin_functions');
					$request_approve_id=$this->input->post('relation');
					$response = $this->admin_functions->approve($request_approve_id);
					//echo $request_approve_id;
					header("Location:http://nti.goodroads.ru/");				
			} 
		}
	}
	public function dismiss()
	{
		if(!$this->input->post('relation'))
		{
			header("Location:http://nti.goodroads.ru/");
		}
		else
		{
			//Если тот , кото вызвал функцию является администратором
			//то ебашим approve
			if($this->session->userdata('id')!=null && $this->session->userdata('id')>0 && $this->session->userdata('rights')==3)
			{
					$this->load->model('admin_functions');
					$request_approve_id=$this->input->post('relation');
					$response = $this->admin_functions->dismiss($request_approve_id);
					//echo $request_approve_id;
					header("Location:http://nti.goodroads.ru/");					
			} 		
		}
	}	
	
	
		public function banuser()
	{
		if(!$this->input->post('userid'))
		{
			header("Location:http://nti.goodroads.ru/");
		}
		else
		{

			if($this->session->userdata('id')!=null && $this->session->userdata('id')>0 && $this->session->userdata('rights')==3)
			{
					$this->load->model('admin_functions');
					$request_approve_id=$this->input->post('userid');
					$response = $this->admin_functions->banuser($request_approve_id);
					//echo $request_approve_id;
					header("Location:http://nti.goodroads.ru/all");					
			} 		
		}
	}	
	
	
			public function unbanuser()
	{
		if(!$this->input->post('userid'))
		{
			header("Location:http://nti.goodroads.ru/");
		}
		else
		{
			//Если тот , кото вызвал функцию является администратором
			//то ебашим approve
			if($this->session->userdata('id')!=null && $this->session->userdata('id')>0 && $this->session->userdata('rights')==3)
			{
					$this->load->model('admin_functions');
					$request_approve_id=$this->input->post('userid');
					$response = $this->admin_functions->unbanuser($request_approve_id);
					//echo $request_approve_id;
					header("Location:http://nti.goodroads.ru/all");					
			} 		
		}
	}	
	
	
	public function chrights() {
		if(!$this->input->post('userid'))
		{
			header("Location:http://nti.goodroads.ru/");
		} else {
			if($this->session->userdata('id')!=null && $this->session->userdata('id')>0 && $this->session->userdata('rights')==3)
			{
					
					$this->load->model('admin_functions');
					$dt = array (
						'i' => $this->input->post('userid'),
						'r' => $this->input->post('nrights')
					);
					if (($dt['r'] == 0) || ($dt['r'] == 1) || ($dt['r'] == 2)) 
						$response = $this->admin_functions->chrights($dt);
					header("Location:http://nti.goodroads.ru/all");	
			}
		}
	}
	
	public function chpassword() {
		if(!$this->input->post('userid'))
		{
			header("Location:http://nti.goodroads.ru/");
		} else {
			if($this->session->userdata('id')!=null && $this->session->userdata('id')>0 && $this->session->userdata('rights')==3)
			{
					$this->load->model('admin_functions');
					$dt = array (
						'i' => $this->input->post('userid'),
						'p' => $this->input->post('npassword')
					);
					if (strlen($dt['p'])>=3)
						$response = $this->admin_functions->chpassword($dt);
					header("Location:http://nti.goodroads.ru/all");					
			}
		}
	}
	
}

?>