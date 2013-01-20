<?php

class Fback extends CI_Controller {
	
	public function index() {
		$newdata['rights'] = $this->session->userdata('rights');
		if (($newdata['rights'] == 0) || ($newdata['rights'] != 3)) {
			$newdata['id'] = $this->session->userdata('id');
			$newdata['derr'] = 0;
			$this->load->view('header', $newdata);
			$this->load->view('feedback', $newdata);
			$this->load->view('footer');
		} else {
			header("Location: http://nti.goodroads.ru");
		}
	}
	
	public function send() {
		$pname = mysql_real_escape_string($this->input->post('ndata'));
		$paddr = mysql_real_escape_string($this->input->post('mdata'));
		$ptext = mysql_real_escape_string($this->input->post('tdata'));
		$newdata['derr'] = 0;
		
		if ((strlen($pname)<4) || (strlen($pname)>32)) {
			$newdata['derr'] = 1; //"Пожалуйста, введите корректное имя от 4 до 32 символов.";
			$this->load->view('header', $newdata);
			$this->load->view('feedback', $newdata);
			$this->load->view('footer');

		} else if ((strlen($paddr)<6) || (strlen($paddr)>32) || (!filter_var($paddr, FILTER_VALIDATE_EMAIL))) {
			$newdata['derr'] = 2; //"Пожалуйста, введите корректный email-адрес от 6 до 32 символов.";
			$this->load->view('header', $newdata);
			$this->load->view('feedback', $newdata);
			$this->load->view('footer');

		} else {
			echo "Petition was sent";
		}
		
	}	
}


	
