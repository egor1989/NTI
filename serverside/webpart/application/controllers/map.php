<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map extends CI_Controller {
	public function index()
	{	
		if ($this->session->userdata('rights') != 3) {
			header("Location: http://nti.goodroads.ru/");
		} else {
			$new_data['map_type']=3;
			$new_data['menu']=1;
			$new_data['library']=2;
			$this->load->library('session');
			
			$new_data['rights'] = $this->session->userdata('rights');
			
			$this->load->model('lays_model');
			$new_data['userslist'] = $this->lays_model->getById();
			$this->session->set_userdata('map',"beta");
			$this->load->view('header',$new_data);
			$this->load->view('map_content',$new_data);
			$this->load->view('footer');
		}
	}
	public function full()
	{	
		$new_data['map_type']=3;
		$new_data['menu']=1;
		$new_data['library']=2;
		$this->load->library('session');
		$this->session->set_userdata('map',"beta");
		$this->load->view('fullheader',$new_data);
		$this->load->view('fullmap',$new_data);
		$this->load->view('fullfooter',$new_data);
    
	}
		public function viewdata()
	{	
		$new_data['map_type']=3;
		$new_data['menu']=1;
		$new_data['library']=2;
						$this->load->library('session');
		$this->session->set_userdata('map',"beta");
		
		
		$this->load->helper('url');
		$urls=$this->uri->segment(3);
	$this->session->set_userdata('page',$urls);

		$this->load->view('header',$new_data);
		$this->load->view('map_content',$new_data);
		$this->load->view('footer');
    
	}
	
	
	function load()
	{

		$this->output->set_header("Content-Type: application/json");
		$this->output->set_header("Accept-Charset: utf-8");

		$this->load->model('mapmodel');;
		
		$data=$this->mapmodel->load_map_data();
		$item=trim($this->input->post());
		echo json_encode($data);
	}
	
	function viewtrips() {
		echo "123"."<br>";
		if ($this->session->userdata('rights') != 3) {
			header("Location: http://nti.goodroads.ru/");
		} else {
			//$i = $this->input->post('userid');
		}
	}
	
}












































