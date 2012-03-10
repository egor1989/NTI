<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	
	public function index(){

		if($this->session->userdata('id')!=null && $this->session->userdata('id')>0 ){
			$this->load->model('userModel');
			$new_data['id'] = $this->session->userdata('id');
			$new_data['name'] = $this->session->userdata('name');
			$new_data['sname'] = $this->session->userdata('sname');
			$new_data['rights']= $this->session->userdata('rights');
			$new_data['map_type'] = 2;
			//Обработка экспертов
			if($this->session->userdata('rights')==2)
			{
				$new_data['retdata']=$this->userModel->get_all_users($this->session->userdata('id'));
			}
		
		
			$new_data['users']=1;
			$this->load->view('header',$new_data);
			$this->load->view('userInfoView', $new_data);
		}
		else{
			$new_data['rights']=0;
			$new_data['map_type'] = 2;
			$new_data['show_menu'] = -1;
			$this->load->view('header',$new_data);
			$this->load->view('loginView');
		}
		$this->load->view('footer');
	}
	
	public function logout(){
		$this->session->sess_destroy();
		header("Location:http://nti.goodroads.ru/");
	}
	
	public function authorization(){
			$this->load->model('userModel');
			$userLogPass = $this->input->post();
			$response = $this->userModel->authorization($userLogPass);
			$new_data['map_type'] = 2;
			$new_data['rights']=0;
			if($response != false){
				$this->session->set_userdata($response);
				header("Location: http://nti.goodroads.ru/user");
			}
			else{
				
							header("Location: http://nti.goodroads.ru/user");

	
				}
			}
	
	public function registration(){
		
		$user = $this->input->post();
		$fname=$this->input->post('fname');
		$sname=$this->input->post('sname');
		$login=$this->input->post('login');
		$password=$this->input->post('password');
		$email=$this->input->post('email');
		$new_data['rights']=0;
		if ($this->security->xss_clean($email, TRUE) === FALSE || $this->security->xss_clean($fname, TRUE) === FALSE || $this->security->xss_clean($sname, TRUE) === FALSE || $this->security->xss_clean($login, TRUE) === FALSE || $this->security->xss_clean($password, TRUE) === FALSE)
		{
					$new_data['map_type'] = 2;
					$new_data['specinfo'] = "Не верный формат одно из полей";
					$this->load->view('header',$new_data);
					$this->load->view('registrationView',$new_data);
					$this->load->view('footer');
					
		}
		else if(strlen($login)<3 || strlen($password)<3 || strlen($email)<3)
		{
					$new_data['map_type'] = 2;
					$new_data['specinfo'] = "Значения каждого поля должно быть заполнено и быть больше 3-х символов";
					$this->load->view('header',$new_data);
					$this->load->view('registrationView',$new_data);
					$this->load->view('footer');
		}
		else if(strlen($fname)>32 || strlen($sname)>32 || strlen($login)>32  || strlen($email)>32)
		{
					$new_data['map_type'] = 2;
					$new_data['specinfo'] = "Значения каждого поля должно быть меньше 32 символов";
					$this->load->view('header',$new_data);
					$this->load->view('registrationView',$new_data);
					$this->load->view('footer');
		}
		else
		{
		$this->load->model('userModel');
		$response = $this->userModel->registration($user);
		if($response){
			if($response['result']>0)
			{
				$responseAuth = $this->userModel->authorization($response);
				if($responseAuth!=false)
				{
					$this->session->set_userdata($responseAuth);
					header("Location: http://nti.goodroads.ru/user");
				}
			}
			else if($response['result']==-1)
			{
					$new_data['map_type'] = 2;
					$new_data['specinfo'] = "Пользователь с таким логином или адресом почты уже существует";
					$this->load->view('header',$new_data);
					$this->load->view('registrationView',$new_data);
					$this->load->view('footer');
			}
						else if($response['result']==-2)
			{
					$new_data['map_type'] = 2;
					$new_data['specinfo'] = "Неверный формат логина";
					$this->load->view('header',$new_data);
					$this->load->view('registrationView',$new_data);
					$this->load->view('footer');
			}
						else if($response['result']==-3)
			{
					$new_data['map_type'] = 2;
					$new_data['specinfo'] = "Неверный формат адреса почты";
					$this->load->view('header',$new_data);
					$this->load->view('registrationView',$new_data);
					$this->load->view('footer');
			}
		}
		else{
		header("Location: http://nti.goodroads.ru/");
		}
	}
	}
	
	public function registrationFormView(){
		$new_data['map_type'] = 2;
$new_data['rights']=0;
			$this->load->view('header',$new_data);
			$this->load->view('registrationView',$new_data);
			$this->load->view('footer');

	}
	//Просмотр экспертами страниц пользователей, юзер обыкновенный делать этого не может
	public function viewuser()
	{
		//
		if($this->session->userdata('rights')>=2)
		{
			$new_data['rights']=$this->session->userdata('rights');
			$new_data['map_type'] = 2;	
			$this->load->helper('url');
			$urls=$this->uri->segment(3);
			$this->load->model('userModel');
			//1) Check if he can see		
			$checker=$this->userModel->checkrealation($this->session->userdata('id'),$urls);
			if($checker==1)
			{
				$new_data['user_info']="You can see him";	
			}
			else
			{
				$new_data['user_info']="user doesnt bind to you";
			}
			$this->load->view('header',$new_data);
			$this->load->view('userstats',$new_data);
			$this->load->view('footer');
		}
		else
			header("Location: http://nti.goodroads.ru/");
		
	}

	
	//движок поиска и сбора статистики о пользователе
	//Отвечает за поиск пользователей 
	
	public function search()
	{
		if($this->session->userdata('rights')>=2)
		{
			$new_data['rights']=$this->session->userdata('rights');
			$new_data['map_type'] = 2;	
			$this->load->model('userModel');
			if($this->input->post())
			{
				$user = $this->input->post();
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
			$this->load->view('header',$new_data);
			$this->load->view('usersearch',$new_data);
			$this->load->view('footer');
		}
		else
		{
			header("Location: http://nti.goodroads.ru/");
		}
	}
	//Добавление тикета о запросе связки эксперта и пользователя
		public function add()
	{
		if($this->session->userdata('rights')>=2)
		{
			$new_data['rights']=$this->session->userdata('rights');
			$new_data['map_type'] = 2;	
			$this->load->helper('url');
			$urls=$this->uri->segment(3);
			$this->load->model('userModel');
			//1) Check if he can see		
			$checker=$this->userModel->AddRelationQuery($this->session->userdata('id'),$urls);
			if($checker==1)
			{
				$new_data['some_info']="Заявка создана";	
			}
			else
			{
				$new_data['some_info']="Заявка не может быть создана.";
			}
			$new_data['isfounded']=-3;
			$new_data['users']=$this->userModel->load_users_list(5);
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
