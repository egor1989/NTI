<?php

class stat extends CI_Controller {

	function index() {
		
		$this->load->model('statmodel');
		$t = $this->statmodel->display();
	
	}

}

?>