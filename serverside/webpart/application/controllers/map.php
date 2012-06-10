<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map extends CI_Controller {

		public function viewdata()
	{	
		if($this->session->userdata('rights')==3)
		{
		$new_data['map_type']=3;
		$new_data['menu']=1;
		$new_data['library']=2;
		$this->load->model('lays_model');
		$new_data['rights']=$this->session->userdata('rights');
		$this->session->set_userdata('map',"beta");
		$this->load->helper('url');
		$urls=$this->uri->segment(3);
		$this->session->set_userdata('page',$urls);
		$new_data['trr'] = $this->lays_model->getMapData($urls);
		$this->load->view('header',$new_data);
		$this->load->view('map_content',$new_data);
		$this->load->view('footer');
	}
	else
	{
		header("Location: http://nti.goodroads.ru/");
	}
	
    
	}

	
}












































