<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class All extends CI_Controller {
	
	public function index(){

		if($this->session->userdata('id')!=null && $this->session->userdata('rights')==3){
			
				$this->load->model('userModel');
				$new_data['retdata']=$this->userModel->load_all_users();
				$new_data['rights']=3;
				$this->load->view('header',$new_data);
				$this->load->view('allusertable', $new_data);
				$this->load->view('footer');
			}

		
		else{
			header("Location:http://nti.goodroads.ru/");
		}
		
	}
	

	
	
	
}
