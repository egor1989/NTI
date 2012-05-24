<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	
	//Тестовый контроллер для функционала
	public function viewuser() {
		if($this->session->userdata('rights')>=2)
		{
			$this->load->model('userModel');
			$this->load->model('lays_model');
			$new_data['rights']=$this->session->userdata('rights');
			$this->load->helper('url');
			$urls=$this->uri->segment(3);
			$checker=$this->userModel->checkrealation($this->session->userdata('id'),$urls);
				if($checker==1 || $this->session->userdata('rights')==3)
				{
						
					$this->load->view('header',$new_data);
					$rs['trr'] = $this->lays_model->getTotalStats($urls);//Получеие статистики по пользователю
					$rs['total_trips']=count($rs['trr'])-1;
					$this->load->view('lasttrips_view',$rs);
					$this->load->view('footer');
		}
		else
			header("Location: http://nti.goodroads.ru/");
		} else
			header("Location: http://nti.goodroads.ru/");
	}
	
	
	

	public function index() {
		if($this->session->userdata('id')!=null && $this->session->userdata('id')>0) {
			
			$this->load->model('userModel');
			$this->load->model('lays_model');
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
					//Получение всех пользователей системы, которые привязаны к данному пользователю
					$new_data['retdata']=$this->userModel->get_all_users($this->session->userdata('id'));
					//Получение всех открытых заявок относительно данного пользователя
					for($i=0;$i<count($new_data['retdata']);$i++)
					{//Занесенение общей ствтистики для каждого пользователя.

						$new_data['retdata'][$i]['stats']=$this->lays_model->getUserTravelStats($new_data['retdata'][$i]['Id']);

					}
					$new_data['tickets']=$this->userModel->load_all_tickets($this->session->userdata('id'));
					$new_data['users']=1;
					$this->load->view('header',$new_data);
					$this->load->view('userInfoView', $new_data);
					$this->load->view('footer');
					
				}
				else if($this->session->userdata('rights')==1)
				{
				
						$new_data['users']=1;
						$this->load->view('header',$new_data);
					$rs['trr'] = $this->lays_model->getTotalStats($this->session->userdata('id'));//Получеие статистики по пользователю
					$rs['total_trips']=count($rs['trr'])-1;
					$this->load->view('lasttrips_view',$rs);
					$this->load->view('footer');
					
					
					
				
				}
				else if($this->session->userdata('rights')==0)
				{
						$new_data['users']=1;
						$this->load->view('header',$new_data);
					$rs['trr'] = $this->lays_model->getTotalStats($this->session->userdata('id'));//Получеие статистики по пользователю
					$rs['total_trips']=count($rs['trr'])-1;
					$this->load->view('lasttrips_view',$rs);
					$this->load->view('footer');
				}
				
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
		$userLogPass = $this->input->post('login');
		$userPass = $this->input->post('password');
		
		$response = $this->userModel->authorization($userLogPass,$userPass);
		$new_data['map_type'] = 2;
		$new_data['rights']=0;
		
		if($response != false){
		$this->session->set_userdata($response);


		header("Location: http://nti.goodroads.ru/user");
	}
		else {
$new_data['rights']=0;
			$new_data['map_type'] = 2;
			$new_data['show_menu'] = -1;
			$new_data['temp_info']="<span style=\"color:#FF0000;\"><b>Авторизация не удалась</b></span>";
			
			$this->load->view('header',$new_data);
			$this->load->view('loginView',$new_data);
			$this->load->view('footer');
		}
	}
	
	public function registration() {
		
		$fname=$this->input->post('fname');
		$sname=$this->input->post('sname');
		$password=$this->input->post('password');
		$email=$this->input->post('email');

		$new_data['rights']=0;
			
		//Для начала посчитаем сложность пароля
		$strength = 0;
		$length = strlen($password);
		if(strtolower($password) != $password){$strength += 1;}
		if(strtoupper($password) == $password) {$strength += 1;}
		if($length >= 8 && $length <= 15){$strength += 1;}
		if($length >= 16 && $length <=35){$strength += 2;}
		if($length > 35){$strength += 3;}
		preg_match_all('/[0-9]/', $password, $numbers);
		$strength += count($numbers[0]);
		preg_match_all('/[|!@#$%&*\/=?,;.:\-_+~^\\\]/', $password, $specialchars);
		$strength += sizeof($specialchars[0]);
		$chars = str_split($password);
		$num_unique_chars = sizeof( array_unique($chars) );
		$strength += $num_unique_chars * 2;
		$strength = $strength > 99 ? 99 : $strength;
		$strength = floor($strength / 10 + 1);
		if ($this->security->xss_clean($email, TRUE) === FALSE || $this->security->xss_clean($fname, TRUE) === FALSE || $this->security->xss_clean($sname, TRUE) === FALSE || $this->security->xss_clean($password, TRUE) === FALSE)	{
			$new_data['map_type'] = 2;
			$new_data['show_menu'] = -1;
			$new_data['specinfo'] = "Не верный формат одно из полей<br/>";
			$this->load->view('header',$new_data);
			$this->load->view('registrationView',$new_data);
			$this->load->view('footer');
			return;
		} else if(strlen($password)<3 || strlen($email)<3)	{
			$new_data['map_type'] = 2;
			$new_data['show_menu'] = -1;
			$new_data['specinfo'] = "Значения каждого поля должно быть заполнено и быть больше 3-х символов<br/>";
			$this->load->view('header',$new_data);
			$this->load->view('registrationView',$new_data);
			$this->load->view('footer');
			return;
		} else if(strlen($fname)>32 || strlen($sname)>32 ||  strlen($email)>32)	{
			$new_data['map_type'] = 2;
			$new_data['show_menu'] = -1;
			$new_data['specinfo'] = "Значения каждого поля должно быть меньше 32 символов<br/>";
			$this->load->view('header',$new_data);
			$this->load->view('registrationView',$new_data);
			$this->load->view('footer');
			return;
		}
		if($strength<4)	{
			$new_data['map_type'] = 2;
			$new_data['show_menu'] = -1;
			$new_data['specinfo'] = "Пароль слишком простой<br/>";
			$this->load->view('header',$new_data);
			$this->load->view('registrationView',$new_data);
			$this->load->view('footer');	
			return;
		}
		else {
			$this->load->model('userModel');
			$response = $this->userModel->registration($fname,$sname,$email,$password);
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
				} else if($response['result']==-1) {
					$new_data['map_type'] = 2;
					$new_data['show_menu'] = -1;
					$new_data['specinfo'] = "Пользователь с таким логином или адресом почты уже существует";
					$this->load->view('header',$new_data);
					$this->load->view('registrationView',$new_data);
					$this->load->view('footer');
					return;
				} else if($response['result']==-2) {
					$new_data['map_type'] = 2;
					$new_data['show_menu'] = -1;
					$new_data['specinfo'] = "Неверный формат логина";
					$this->load->view('header',$new_data);
					$this->load->view('registrationView',$new_data);
					$this->load->view('footer');
					return;
				} else if($response['result']==-3)	{
					$new_data['map_type'] = 2;
					$new_data['show_menu'] = -1;
					$new_data['specinfo'] = "Неверный формат адреса почты";
					$this->load->view('header',$new_data);
					$this->load->view('registrationView',$new_data);
					$this->load->view('footer');
					return;
				} else if($response['result']==-4)	{
					$new_data['map_type'] = 2;
					$new_data['show_menu'] = -1;
					$new_data['specinfo'] = "Пароль слишком короткий";
					$this->load->view('header',$new_data);
					$this->load->view('registrationView',$new_data);
					$this->load->view('footer');
					return;
				} else if($response['result']==-5)	{
					$new_data['map_type'] = 2;
					$new_data['show_menu'] = -1;
					$new_data['specinfo'] = "Адрес почты слишком короткий";
					$this->load->view('header',$new_data);
					$this->load->view('registrationView',$new_data);
					$this->load->view('footer');
					return;
				}
			} else {
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
	


	
	public function addaccept()	{
		
		if($this->session->userdata('rights')>=2)
		{
			$urls=$this->input->post('userid');
			$this->load->model('userModel');
			//1) Check if he can see		
			$checker=$this->userModel->AddRelationQuery($this->session->userdata('id'),$urls);
			header("Location: http://nti.goodroads.ru/search");
		}
		else
		{
			header("Location: http://nti.goodroads.ru");
		}
		
	}
	
	
	public function removeaccept()	{
		if($this->session->userdata('rights')>=2)
		{

			$urls=$this->input->post('userid');
			$this->load->model('userModel');
			//1) Check if he can see		
			$checker=$this->userModel->RemoveRelationQuery($this->session->userdata('id'),$urls);
		header("Location: http://nti.goodroads.ru/search");
		}
		else
		{
			header("Location: http://nti.goodroads.ru/");
		}
	}
	
	public function deleteaccept() {
		if($this->session->userdata('rights')>=2)
		{
			$urls=$this->input->post('userid');	
			$this->load->model('userModel');
			//1) Check if he can see		
			$checker=$this->userModel->DeleteRelation($this->session->userdata('id'),$urls);
			header("Location: http://nti.goodroads.ru/search");
		}
		else
		{
			header("Location: http://nti.goodroads.ru/");
		}
	}
	
	public function block()	{
		if($this->session->userdata('rights')==3) {
			$this->load->helper('url');
			$urls=$this->uri->segment(3);
			$new_data['users']=$this->userModel->BlockUser($urls);
			header("Location: http://nti.goodroads.ru/");
		} else {
			header("Location: http://nti.goodroads.ru/");
		}
	}
	
	public function unblock() {
		if($this->session->userdata('rights')==3) {
			$this->load->helper('url');
			$urls=$this->uri->segment(3);
			$new_data['users']=$this->userModel->UnBlockUser($urls);
			header("Location: http://nti.goodroads.ru/");
		} else	{
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
		if($checker==1) {
			$new_data['map_type'] = 2;
			$new_data['rights']=0;
			$new_data['show_menu']=0;
			$new_data['info']="Спасибо за подтверждение регистрации.";
			$this->load->view('header',$new_data);
			$this->load->view('temp_page',$new_data);
			$this->load->view('footer');
		} else {
			$new_data['map_type'] = 2;
			$new_data['rights']=0;
			$new_data['show_menu']=0;
			$new_data['info']="Извините, но ссылка, по которой Вы перешли, не существует.";
			$this->load->view('header',$new_data);
			$this->load->view('temp_page',$new_data);
			$this->load->view('footer');
		}
	}
		
	public function search() {
		
		$Kcr = 0.8; //Средний коэффициент по региону. Должен считаться как сумма ochkov всех водителей, прикрепленных к данному региону, поделенная на количество водил в этом регионе. Условно пока что взят за 0.8 //Arti
		
		if($this->session->userdata('id')!=null && $this->session->userdata('id')>0) {
			//Теперь проверям следующие предположения
			//1 - пользователь является сам собой
			$this->load->model('userModel');
			
			//Получаем для начала id пользователя
			$userid = $this->uri->segment(3);
			//Переменная отвечает за возможность просмотра польователем данных
			$can_see=0;
			if(!is_numeric($userid))$userid=$this->session->userdata('id');
			if($this->session->userdata('id')==$userid){$can_see=1;}//Сам пользователь смотри себя
			else if($this->session->userdata('id')!=$userid && $this->session->userdata('rights')==2)
			{
				$checker=$this->userModel->checkrealation($this->session->userdata('id'),$userid);
				if($checker==1)$can_see=1;
				
			}
			else if($this->session->userdata('rights')==3)
			{
				$can_see=1;
			}
			
			if($can_see==1)
			{
			
			$new_data['rights'] = 0;
			$this->load->model('lays_model');
			$dt = array(
				't1' => $this->input->post('t1'),
				't2' => $this->input->post('t2')
			);
			
			if (strtotime($dt['t1']) === FALSE) 
			{
			$derr['errortype'] = "Неверный формат начального времени (должно быть в формате ДД-ММ-ГГГГ, например 07-04-2012 для 7 апреля 2012 года)";
			$derr['linkid'] = $userid;
			$this->load->view('header', $new_data);
			$this->load->view('lays_view', $derr);
			$this->load->view('footer');
			return;
		} else if (strtotime($dt['t2']) === FALSE) {
			$derr['errortype'] = "Неверный формат конечного времени (должно быть в формате ДД-ММ-ГГГГ, например 07-04-2012 для 7 апреля 2012 года)";
			$derr['linkid'] = $userid;
			$this->load->view('header', $new_data);
			$this->load->view('lays_view', $derr);
			$this->load->view('footer');
			return;
		} else if (strtotime($dt['t1']) > strtotime($dt['t2'])) {
			$derr['errortype'] = "Ошибка: начальная дата больше конечной.";
			$derr['linkid'] = $userid;
			$this->load->view('header', $new_data);
			$this->load->view('lays_view', $derr);
			$this->load->view('footer');
			return;
	
	
	
		} else if (strtotime($dt['t2']) > time()) {
			$derr['errortype'] = "Ошибка: начальная дата позже текущего момента.";
			$derr['linkid'] = $userid;
			$this->load->view('header', $new_data);
			$this->load->view('lays_view', $derr);
			$this->load->view('footer');
			return;
		}
		//Теперь проверяем на отношения пользователя
		$data = $this->lays_model->search($dt,$userid);
		if ($data != -1) {
				$results['trr'] = $this->lays_model->getTotalStatsByTime($userid,$dt['t1'],$dt['t2']);//Получеие статистики по пользователю
				$time1=$dt['t1'];
				$time2=$dt['t2'];
				$results['total_trips']=count($results['trr'])-1;
				$results['is_set']=1;
		}
		else {
			$results['is_set'] = -1;
		}	

		$results['linkid'] = $userid;
		$this->load->view('header', $new_data);
		$this->load->view('lays_view', $results);
		$this->load->view('footer');
		}
		else {
			$new_data['rights']=$this->session->userdata('rights');
			$new_data['info']="У вас нет доступа к этой странице";
			$this->load->view('header',$new_data);
			$this->load->view('temp_page',$new_data);
			$this->load->view('footer');
			//return ;
		}
		}
		else {
		header("Location: http://nti.goodroads.ru/");	
		}
		return $results;
	} //end of search() method.	

	function deleterel() {
		if($this->session->userdata('rights')==2)
		{
			$usri=$this->input->post('userid');
			$cki=$this->session->userdata('id');
			$this->load->model('lays_model');
			$suckmyfuck = $this->lays_model->unbind($usri,$cki);
			header("Location: http://nti.goodroads.ru/search");
		}
		else
		{
			header("Location: http://nti.goodroads.ru/");
		}
		return 1;
	}
	
	function viewck() {
		$rg = $this->session->userdata('Rights');
		if ($rg==3) {
			$this->load->model('lays_model');
			$i = $this->uri->segment(3);
			$data = $this->lays_model->vck($i);
			return $data;
		
		} else {
			header("Location: http://nti.goodroads.ru/");
			return -1;
		}
	}
		
	
	
} //end of controller
?>
