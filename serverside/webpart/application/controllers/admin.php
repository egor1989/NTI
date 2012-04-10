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
	
}
	
	
	
	
	

