<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class tickets extends CI_Controller {
	
		//Отвечает за загрузку и мониторинг всех сообщений от пользователей
	public function index(){
		//Если пользователь не администратор,то послать его за сарай
		if($this->session->userdata('id')!=null && $this->session->userdata('rights')==3)
		{
			
				$this->load->model('ticket_model');
				$new_data['retdata']=$this->ticket_model->LoadTickets();
				$new_data['rights']=3;
				$this->load->view('header',$new_data);
				$this->load->view('tickets', $new_data);
				$this->load->view('footer');
		}	
		else
		{
			header("Location:http://nti.goodroads.ru/");
		}
		
	}
	

	
	
	
}
 
