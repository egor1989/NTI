<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller {

	
	public function index()
	{
		if($this->session->userdata('rights')>=2)
		{
			$new_data['rights']=$this->session->userdata('rights');
			$new_data['map_type'] = 2;	
			$this->load->model('userModel');
			if($this->input->post('name'))
			{
				$user = $this->input->post('name');
				$response = $this->userModel->search($user);
				if($response)
				{
					//Собираем статистику для красоты 
					for($i=0;$i<count($response);$i++)
					{
						$response[$i]['Relation'] = $this->userModel->GetUserStatistics( $this->session->userdata('id'),$response[$i]['Id']);
					}
					$new_data['search_result'] = $response;		
					$new_data['isfounded'] = 1;
			
				}
				else
				{
					$new_data['search_result'] = "";		
					$new_data['isfounded'] = 0;
				}
			}
			else
			{
				$new_data['search_result'] = "";		
				$new_data['isfounded'] = 0;
			}
			$new_data['users']=$this->userModel->load_users_list(5);
			$new_data['tickets']=$this->userModel->load_all_tickets($this->session->userdata('id'));
			$this->load->view('header',$new_data);
			$this->load->view('usersearch',$new_data);
			$this->load->view('footer');
		}
		else
		{
			header("Location: http://nti.goodroads.ru/");
		}
	}
	
	
	
	
	
}
