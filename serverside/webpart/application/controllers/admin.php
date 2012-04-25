<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller 
{
	
	public function approve()
	{
		if(!$this->input->post('relation'))
		{
			header("Location:http://nti.goodroads.ru/");
		}
		else
		{
			//Если тот , кото вызвал функцию является администратором
			//то ебашим approve
			if($this->session->userdata('id')!=null && $this->session->userdata('id')>0 && $this->session->userdata('rights')==3)
			{
					$this->load->model('admin_functions');
					$request_approve_id=$this->input->post('relation');
					$response = $this->admin_functions->approve($request_approve_id);
					//echo $request_approve_id;
					header("Location:http://nti.goodroads.ru/");				
			} 
		}
	}
	/* Not need in this further. Updated to non-ticket (permanent) unbinding.
	public function dismiss()
	{
		if(!$this->input->post('relation'))
		{
			header("Location:http://nti.goodroads.ru/");
		}
		else
		{
			//Если тот , кото вызвал функцию является администратором
			//то ебашим approve
			if($this->session->userdata('id')!=null && $this->session->userdata('id')>0 && $this->session->userdata('rights')==3)
			{
					$this->load->model('admin_functions');
					$request_approve_id=$this->input->post('relation');
					$response = $this->admin_functions->dismiss($request_approve_id);
					//echo $request_approve_id;
					header("Location:http://nti.goodroads.ru/");					
			} 		
		}
	}
	*/	
	
	
		public function banuser()
	{
		if(!$this->input->post('userid'))
		{
			header("Location:http://nti.goodroads.ru/");
		}
		else
		{

			if($this->session->userdata('id')!=null && $this->session->userdata('id')>0 && $this->session->userdata('rights')==3)
			{
					$this->load->model('admin_functions');
					$request_approve_id=$this->input->post('userid');
					$response = $this->admin_functions->banuser($request_approve_id);
					//echo $request_approve_id;
					header("Location:http://nti.goodroads.ru/all");					
			} 		
		}
	}	
	
	
			public function unbanuser()
	{
		if(!$this->input->post('userid'))
		{
			header("Location:http://nti.goodroads.ru/");
		}
		else
		{
			//Если тот , кото вызвал функцию является администратором
			//то ебашим approve
			if($this->session->userdata('id')!=null && $this->session->userdata('id')>0 && $this->session->userdata('rights')==3)
			{
					$this->load->model('admin_functions');
					$request_approve_id=$this->input->post('userid');
					$response = $this->admin_functions->unbanuser($request_approve_id);
					//echo $request_approve_id;
					header("Location:http://nti.goodroads.ru/all");					
			} 		
		}
	}	
	
	
	public function chrights() {
		if(!$this->input->post('userid'))
		{
			header("Location:http://nti.goodroads.ru/");
		} else {
			if($this->session->userdata('id')!=null && $this->session->userdata('id')>0 && $this->session->userdata('rights')==3)
			{
					
					$this->load->model('admin_functions');
					$dt = array (
						'i' => $this->input->post('userid'),
						'r' => $this->input->post('nrights')
					);
					if (($dt['r'] == 0) || ($dt['r'] == 1) || ($dt['r'] == 2)) 
						$response = $this->admin_functions->chrights($dt);
					header("Location:http://nti.goodroads.ru/all");	
			}
		}
	}
	
	public function chpassword() {
		if(!$this->input->post('userid'))
		{
			header("Location:http://nti.goodroads.ru/");
		} else {
			if($this->session->userdata('id')!=null && $this->session->userdata('id')>0 && $this->session->userdata('rights')==3)
			{
					$this->load->model('admin_functions');
					$dt = array (
						'i' => $this->input->post('userid'),
						'p' => $this->input->post('npassword')
					);
					if (strlen($dt['p'])>=3)
						$response = $this->admin_functions->chpassword($dt);
					header("Location:http://nti.goodroads.ru/all");					
			}
		}
	}
		public function viewck()
	{
		if($this->session->userdata('rights')==3)
		{	
			$this->load->helper('url');
			$ckid=$this->uri->segment(3);
			$new_data['rights']=$this->session->userdata('rights');
			$new_data['map_type'] = 2;	
			$new_data['ckid'] = $ckid;	
			$this->load->model('userModel');
			$new_data['ept'] = $this->cksearch($ckid);
			$this->load->view('header',$new_data);
			$this->load->view('ckmanagment',$new_data);
			$this->load->view('footer');
		}
		else
		{
			header("Location: http://nti.goodroads.ru/");
		}
	}
	
	function cksearch($id) {
	
		$this->load->model('lays_model');
		$data = $this->lays_model->cksearch($id);
		
		if ($data != -1) {
			for ($i=0;$i<count($data);$i++) {
				if ($data[$i]['Bnd']==0) {
					$data[$i]['Button'] = 1; //add user
				} else {
					$data[$i]['Button'] = 2; //drop user
				}
			}
		} else {
			$data['Users'] = -1;
		}
		
		return $data;
	}
	
	
	
			public function add()
	{
		if($this->session->userdata('rights')==3)
		{	
			$ckid=$this->input->post('ckid');
			$userid=$this->input->post('userid');
				$this->load->model('userModel');
				$this->userModel->AddRelation($ckid,$userid);
			header("Location: http://nti.goodroads.ru/admin/viewck/$ckid");
		}
		else
		{
			header("Location: http://nti.goodroads.ru/");
		}
	}
	
			public function delete()
	{
		if($this->session->userdata('rights')==3)
		{	
			$ckid=$this->input->post('ckid');
			$userid=$this->input->post('userid');
				$this->load->model('userModel');
				$this->userModel->DelRelation($ckid,$userid);
			header("Location: http://nti.goodroads.ru/admin/viewck/$ckid");
		}
		else
		{
			header("Location: http://nti.goodroads.ru/");
		}
	}
	
	
}

?>
