<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map extends CI_Controller {
	public function index()
	{	
		$new_data['map_type']=2;
		$new_data['menu']=1;
		$new_data['library']=2;
						$this->load->library('session');
		$this->session->set_userdata('map',"beta");
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
}


