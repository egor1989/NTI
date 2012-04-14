<?php

class lays extends CI_Controller {

	function index() {
		$this->load->view('lays_view');
	}

	public function search() {
		$this->load->model('lays_model');
		$data = array(
			't1' => $this->input->post('t1'),
			't2' => $this->input->post('t2')
		);
		/*
		if (strtotime($data['t1']) === FALSE) {
			echo "Wrong data: check your fromTime. Should be in YYYY-mm-dd format. Click BACK in your browser and try again.";
			return;
		}
		if (strtotime($data['t2']) === FALSE) {
			echo "Wrong data: check your tillTime. Should be in YYYY-mm-dd format. Click BACK in your browser and try again.";
			return;
		}
		if (strtotime($data['t1']) > strtotime($data['t2'])) {
			echo "Wrong data: fromTime is bigger than tillTime. Click BACK in your browser and try again.";
			return;
		}
		if (strtotime($data['t2']) > time()) {
			echo "Wrong data: fromTime is bigger than current time. Click BACK in your browser and try again.";
			return;
		}
		*/
		$results = $this->lays_model->search($data);
		print_r($results);
		$this->load->view('lays_view', $results);
	}

}