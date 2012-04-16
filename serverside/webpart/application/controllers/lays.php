<?php

class lays extends CI_Controller {

	public function index() {
		$this->load->view('lays_view');
	}

	public function search() {
		$isDataOk = 0;
		$this->load->model('lays_model');
		$data = array(
			't1' => $this->input->post('t1'),
			't2' => $this->input->post('t2')
		);
		if (strtotime($data['t1']) === FALSE) {
			$isDataOk = 0;
			$this->index();
			echo "Wrong data: check your fromTime. Should be in YYYY-mm-dd format.";
		} else if (strtotime($data['t2']) === FALSE) {
			$isDataOk = 0;
			$this->index();
			echo "Wrong data: check your tillTime. Should be in YYYY-mm-dd format.";			
		} else if (strtotime($data['t1']) > strtotime($data['t2'])) {
			$isDataOk = 0;
			$this->index();
			echo "Wrong data: fromTime is bigger than tillTime.";			
		} else if (strtotime($data['t2']) > time()) {
			$isDataOk = 0;
			$this->index();
			echo "Wrong data: fromTime is bigger than current time.";			
		} else {
			$isDataOk = 1;
		}
		
		if ($isDataOk = 1)
		{
			$results = $this->lays_model->search($data);
			//echo "data is okay";
			//$this->load->view('lays_view', $results);
		}
		
	}

}