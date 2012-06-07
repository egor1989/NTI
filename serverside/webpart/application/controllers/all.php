<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class All extends CI_Controller {
	
	public function index(){

			header("Location:http://nti.goodroads.ru/all/users");
		
		
	}
	
	public function ck(){

		if($this->session->userdata('id')!=null && $this->session->userdata('rights')==3)
		
		{
			//загружаем модель pagination
			//Он обеспечивает навигаюци 12345
				$this->load->model('userModel');
				$this->load->library('pagination');
				$config['base_url'] = '/all/ck'; 
				$config['total_rows'] =$this->userModel->get_ck_count();
				$config['per_page'] = 20;   
				$config['num_links'] = 10;    
				$config['uri_segment'] = 3;  
				$config['cur_tag_open'] = '';
				$config['cur_tag_close'] = '';
				$config['num_tag_open'] = '';
				$config['num_tag_close'] = '';
				$config['last_link'] =FALSE;
				$config['next_link'] =FALSE;
				$config['prev_link']=FALSE;
				$this->  pagination->  initialize($config);
				$new_data['pager']=$this->  pagination-> create_links();
				$offset=$this->uri->segment(3);
				if(!is_int($offset))$offset=0;
				$new_data['retdata']=$this->userModel->load_all_ck_users(20,$offset);
				$new_data['rights']=3;
				$this->load->view('header',$new_data);
				$this->load->view('allusertable', $new_data);
				$this->load->view('footer');
			}

		
		else{
			header("Location:http://nti.goodroads.ru/");
		}
		
	}
		public function users(){

		if($this->session->userdata('id')!=null && $this->session->userdata('rights')==3)
		
		{
			//загружаем модель pagination
			//Он обеспечивает навигаюци 12345
				$this->load->model('userModel');
				$this->load->library('pagination');
				$config['base_url'] = '/all/users'; 
				$config['total_rows'] =$this->userModel->get_users_count();
				$config['per_page'] =  20;   
				$config['num_links'] = 10;    
				$config['uri_segment'] = 3;  
				$config['cur_tag_open'] = '';
				$config['cur_tag_close'] = '';
				$config['num_tag_open'] = '';
				$config['num_tag_close'] = '';
				$config['last_link'] =FALSE;
				$config['next_link'] =FALSE;
				$config['prev_link']=FALSE;
				$this->  pagination->  initialize($config);
				$new_data['pager']=$this->  pagination-> create_links();
				$offset=$this->uri->segment(3);
				if(!is_numeric($offset))$offset=0;
				
				$new_data['retdata']=$this->userModel->load_all_simple_users(20,$offset);
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
