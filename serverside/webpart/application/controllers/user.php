<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	
	public function index(){

		if($this->session->userdata('id')!=null && $this->session->userdata('id')>0){
			
			$this->load->model('userModel');
			$new_data['id'] = $this->session->userdata('id');
			$new_data['name'] = $this->session->userdata('name');
			$new_data['sname'] = $this->session->userdata('sname');
			$new_data['rights']= $this->session->userdata('rights');
			$new_data['map_type'] = 2;
			//Обработка экспертов
			if($this->session->userdata('rights')!=3)
			{
				if($this->session->userdata('rights')==2)
				{
					$new_data['retdata']=$this->userModel->get_all_users($this->session->userdata('id'));
					$new_data['unregistered_data']=$this->userModel->get_all_unregdata();
				}
				$new_data['users']=1;
				$this->load->view('header',$new_data);
				$this->load->view('userInfoView', $new_data);
			}
			else
			{
				//Здесь формируется данные предоставляемые администратору
				//Для начала получаем список пользователей
				$new_data['users']=$this->userModel->LoadUsers();
				//Теперь получаем список всех экспертов
				$new_data['experts']=$this->userModel->LoadExperts();
				//Получаем список запросов экспертов
				$new_data['tickets']=$this->userModel->LoadTickets();
				$this->load->view('header',$new_data);
				//Загружаем его собственную вивку 
				$this->load->view('admin', $new_data);
			}
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
		$password=$this->input->post('password');
		$email=$this->input->post('email');
		$new_data['rights']=0;
		
		
		if ($this->security->xss_clean($email, TRUE) === FALSE || $this->security->xss_clean($fname, TRUE) === FALSE || $this->security->xss_clean($sname, TRUE) === FALSE || $this->security->xss_clean($password, TRUE) === FALSE)
		{
					$new_data['map_type'] = 2;
					$new_data['specinfo'] = "Не верный формат одно из полей";
					$this->load->view('header',$new_data);
					$this->load->view('registrationView',$new_data);
					$this->load->view('footer');
					
		}
		else if(strlen($password)<3 || strlen($email)<3)
		{
					$new_data['map_type'] = 2;
					$new_data['specinfo'] = "Значения каждого поля должно быть заполнено и быть больше 3-х символов";
					$this->load->view('header',$new_data);
					$this->load->view('registrationView',$new_data);
					$this->load->view('footer');
		}
		else if(strlen($fname)>32 || strlen($sname)>32 ||  strlen($email)>32)
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
				$config['mailtype'] = 'html';
				$config['wordwrap'] = TRUE;

				$this->load->library('email');
				$this->email->initialize($config);
				
				$this->email->from('support@goodroads.ru', 'NTI');
				$this->email->to($response['login']); 
				$this->email->subject('Registration form');
				$emailLink=$response['emailkey'];
				
				$subject="<html>";
				$subject.="<head>";
				$subject.="</head>";
				$subject.="<body>";
				$subject.="Вы зарегистрировались на сайте nti.goodroads.ru<br/>";
				$subject.="Для подтверждения регистрации перейдите по ссылке:<br/>";
				$subject.="<a href='http://nti.goodroads.ru/user/approve/$emailLink'>http://nti.goodroads.ru/user/continue/$emailLink</a><br/>";
				$subject.="</body>";
				$subject.="</html>";				
				$this->email->message($subject);	
				$this->email->send();
				header("Location: http://nti.goodroads.ru/user");
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
			$new_data['show_menu']=0;
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
	
	
	public function removeaccept()
	{
		if($this->session->userdata('rights')>=2)
		{
			$new_data['rights']=$this->session->userdata('rights');
			$new_data['map_type'] = 2;	
			$this->load->helper('url');
			$urls=$this->uri->segment(3);
			$this->load->model('userModel');
			//1) Check if he can see		
			$checker=$this->userModel->RemoveRelationQuery($this->session->userdata('id'),$urls);
			if($checker==1)
			{
				$new_data['some_info']="Заявка удалена";	
			}
			else
			{
				$new_data['some_info']="Заявка не может быть удалена.";
			}
			$new_data['isfounded']=-3;
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
	
		public function delete()
	{
		if($this->session->userdata('rights')>=2)
		{
			$new_data['rights']=$this->session->userdata('rights');
			$new_data['map_type'] = 2;	
			$this->load->helper('url');
			$urls=$this->uri->segment(3);
			$this->load->model('userModel');
			//1) Check if he can see		
			$checker=$this->userModel->DeleteRelation($this->session->userdata('id'),$urls);
			if($checker==1)
			{
				$new_data['some_info']="Заявка на удаление подана";	
			}
			else
			{
				$new_data['some_info']="Заявка не может быть удалена.";
			}
			$new_data['isfounded']=-3;
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
	
		public function navigate()
		{
			if($this->session->userdata('rights')>=2)
			{
			$new_data['rights']=$this->session->userdata('rights');
			$new_data['map_type'] = 2;	
			$this->load->model('userModel');
			//1) Check if he can see		
			$new_data['isfounded']=-3;
			$new_data['users']=$this->userModel->load_expert_users($this->session->userdata('id'));
			$this->load->view('header',$new_data);
			$this->load->view('usertable',$new_data);
			$this->load->view('footer');
		}
		else
		{
			header("Location: http://nti.goodroads.ru/");
		}
	}
	
	
			public function changeinfo()
		{
			if($this->session->userdata('rights')>=2)
			{
			$new_data['rights']=$this->session->userdata('rights');
			$new_data['map_type'] = 2;	
			$this->load->model('userModel');
			//1) Check if he can see		
			$new_data['isfounded']=-3;
			$new_data['users']=$this->userModel->load_expert_users($this->session->userdata('id'));
			$this->load->view('header',$new_data);
			$this->load->view('usertable',$new_data);
			$this->load->view('footer');
		}
		else
		{
			header("Location: http://nti.goodroads.ru/");
		}
	}
	
				public function block()
		{
			if($this->session->userdata('rights')==3)
			{
				$this->load->helper('url');
				$urls=$this->uri->segment(3);
				$new_data['users']=$this->userModel->BlockUser($urls);
				header("Location: http://nti.goodroads.ru/");
			}
			else
			{
				header("Location: http://nti.goodroads.ru/");
			}
	}
					public function unblock()
		{
			if($this->session->userdata('rights')==3)
			{
				$this->load->helper('url');
				$urls=$this->uri->segment(3);
				$new_data['users']=$this->userModel->UnBlockUser($urls);
				header("Location: http://nti.goodroads.ru/");
			}
			else
			{
				header("Location: http://nti.goodroads.ru/");
			}
	}
	
	//Функция отвечает за продолжение регистрации
	
	public function approve(){
			//1 выделеем снача ключ пользваотеля , который нам пришел
			$this->load->helper('url');
			$user_key=$this->uri->segment(3);
			$this->load->model('userModel');
			//1) Check if he can see		
			$checker=$this->userModel->CheckRelation($user_key);
			if($checker==1)
			{
						$new_data['map_type'] = 2;
			$new_data['rights']=0;
			$new_data['show_menu']=0;
					$new_data['info']="Спасибо за подтверждение регистрации.";
					$this->load->view('header',$new_data);
					$this->load->view('temp_page',$new_data);
					$this->load->view('footer');
	
			}
			else
			{
					$new_data['map_type'] = 2;
					$new_data['rights']=0;
					$new_data['show_menu']=0;
					$new_data['info']="Извините, но ссылка, по которой Вы перешли, не существует.";
					$this->load->view('header',$new_data);
					$this->load->view('temp_page',$new_data);
					$this->load->view('footer');
			
			}
			

	}
	
	
	
	
}
