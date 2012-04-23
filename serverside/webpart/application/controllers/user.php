<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	

	public function index() {

		if($this->session->userdata('id')!=null && $this->session->userdata('id')>0) {
			
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
					//Получение всех пользователей системы, которые привязаны к данному пользователю
					$new_data['retdata']=$this->userModel->get_all_users($this->session->userdata('id'));
					//Получение всех открытых заявок относительно данного пользователя
					$new_data['tickets']=$this->userModel->load_all_tickets($this->session->userdata('id'));
					$new_data['users']=1;
					$this->load->view('header',$new_data);
					$this->load->view('userInfoView', $new_data);
					$this->load->view('footer');
					
				}
				else if($this->session->userdata('rights')==1)
				{
					$new_data['users']=1;
					$new_data['users']=1;
					$this->load->view('header',$new_data);
					$rs['trr'] = $this->userinfo();
					 
					$this->load->view('lasttrips_view',$rs);
					//$this->load->view('userInfoView', $new_data);
					$this->load->view('footer');
					
					//$this->load->view('lasttrips_view', $this->userinfo());
				//	
					//$this->load->view('lasttrips_view', $qqqq);
				}
				else if($this->session->userdata('rights')==0)
				{
					$new_data['users']=1;
					$this->load->view('header',$new_data);
					
					//$this->load->view('lasttrips_view', $qqqq);
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
		$userLogPass = $this->input->post();
		$response = $this->userModel->authorization($userLogPass);
		$new_data['map_type'] = 2;
		$new_data['rights']=0;
		if($response != false){
			$this->session->set_userdata($response);
			header("Location: http://nti.goodroads.ru/user");
		}
		else {
			header("Location: http://nti.goodroads.ru/user");
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
	
	//Просмотр экспертами страниц пользователей, юзер обыкновенный делать этого не может
	public function viewuser() {
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
		} else
			header("Location: http://nti.goodroads.ru/");
	}

	
	public function addaccept()	{
		
		if($this->session->userdata('rights')>=2)
		{
			$new_data['rights']=$this->session->userdata('rights');
			$new_data['map_type'] = 2;	
			$urls=$this->input->post('userid');
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
			return;
		}
		else
		{
			header("Location: http://nti.goodroads.ru");
		}
		
	}
	
	
	public function removeaccept()	{
		if($this->session->userdata('rights')>=2)
		{
			$new_data['rights']=$this->session->userdata('rights');
			$new_data['map_type'] = 2;	
			$urls=$this->input->post('userid');
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
	
	public function delete() {
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
	
	public function navigate()	{
		if($this->session->userdata('rights')>=2) {
			$new_data['rights']=$this->session->userdata('rights');
			$new_data['map_type'] = 2;	
			$this->load->model('userModel');
			//1) Check if he can see		
			$new_data['isfounded']=-3;
			$new_data['users']=$this->userModel->load_expert_users($this->session->userdata('id'));
			$this->load->view('header',$new_data);
			$this->load->view('usertable',$new_data);
			$this->load->view('footer');
		} else	{
			header("Location: http://nti.goodroads.ru/");
		}
	}
	
	
	public function changeinfo() {
		if($this->session->userdata('rights')>=2) {
			$new_data['rights']=$this->session->userdata('rights');
			$new_data['map_type'] = 2;	
			$this->load->model('userModel');
			//1) Check if he can see		
			$new_data['isfounded']=-3;
			$new_data['users']=$this->userModel->load_expert_users($this->session->userdata('id'));
			$this->load->view('header',$new_data);
			$this->load->view('usertable',$new_data);
			$this->load->view('footer');
		} else {
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
		
	
	//>>>>>>>>>>>>

	public function search() {
		
		
		if($this->session->userdata('id')!=null && $this->session->userdata('id')>0){
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
			$derr['errortype'] = "Неверный формат начального времени (должно быть в формате ГГГГ-ММ-ДД, например 2011-06-10 для 10 июня 2011 года)";
			$derr['linkid'] = $userid;
			$this->load->view('header', $new_data);
			$this->load->view('lays_view', $derr);
			$this->load->view('footer');
			return;
		} else if (strtotime($dt['t2']) === FALSE) {
			$derr['errortype'] = "Неверный формат конечного времени (должно быть в формате ГГГГ-ММ-ДД, например 2011-06-10 для 10 июня 2011 года)";
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
			$k = 0;
			$m = 0;
			$grouped[$k][$m]=$data[0];
			$n = count($data)-1;
			for ($i=1;$i<$n;$i++) {
				if ($data[$i]['utimestamp'] - $grouped[$k][$m]['utimestamp'] != 0) {
					if (($data[$i]['utimestamp'] - $grouped[$k][$m]['utimestamp'] < 300) && 
					   (((sqrt(pow(($data[$i]['lat']-$grouped[$k][$m]['lat']),2) + pow(($data[$i]['lng']-$grouped[$k][$m]['lng']),2)))*200)/($data[$i]['utimestamp'] - $grouped[$k][$m]['utimestamp']) < 180)) {
						$m++;
						$grouped[$k][$m] = $data[$i];
					}
					else
					{
						$m = 0;
						$k++;
						$grouped[$k][$m] = $data[$i];
					}
				}
			}
			$total_runs = $k - 1;
			//Конец группирования по поездкам
			
			
			$total_time=0;
			$total_score=0;
			$total_turn=0;
			$total_acc=0;
			$total_brake=0;
			$total_turn1=0;
			$total_turn2=0;
			$total_turn3=0;
			$total_acc1=0;
			$total_acc2=0;
			$total_acc3=0;
			$total_brake1=0;
			$total_brake2=0;
			$total_brake3=0;
			$total_prev1=0;
			$total_prev2=0;
			$total_prev3=0;
			$tt = 0;
			$ta = 0;
			$tp = 0;
			$tb = 0;
			$ttime = 0;
			
			for($m=0;$m<$k;$m++)
			{
				unset($data);
				$unfilteredData=$grouped[$m];
				$drivingScore = 0;
				$coef1 = 0.1;
				$coef2 = 0.2;
				$coef3 = 0.6;
				$deltaSpeed=0;
				$speed1=0;
				$speed2=0;
				$speed3=0;
				$acc1=0;
				$acc2=0;
				$acc3=0;
				$brake1=0;
				$brake2=0;
				$brake3=0;
				$turn1=0;
				$turn2=0;
				$turn3=0;
				$acc = 0;
			
				//Выкидываем одну из соседних не отличающихся по LNG точек
				$w =0;
				for ($v=1;$v<count($unfilteredData)-1;$v++) {
					if ($unfilteredData[$v]['lng'] != $unfilteredData[$v-1]['lng'] ) {
						$data[$w] = $unfilteredData[$v-1];
						$w++;
					}
				}
				//Закончили выкидывать.
			
				if ($w>10) {
							$j=count($data);
													$dss = 0;
					for ($i = 1; $i < $j-1; $i++)
					{
						$typeTurn[0] = "normal point";
						$typeAcc[0] = "normal point";
						$typeSpeed[0] = "normal point";
						
						$sevTurn = 0;
						$sevAcc = 0;
						$sevSpeed = 0;
						$speed = $data[$i]['speed'];	
						$deltaTime = $data[$i]['utimestamp'] - $data[$i-1]['utimestamp'];
						
						if ( ($data[$i]['lng']-$data[$i-1]['lng']) != 0  )
						{
						
							$turn[$i] = atan(($data[$i]['lat']-$data[$i-1]['lat'])/($data[$i]['lng']-$data[$i-1]['lng']));
			
							$turn[0] = 0;
							$deltaTurn = $turn[$i] - $turn[$i-1];
							$wAcc = abs($deltaTurn/($deltaTime));
													
							//Высчитываем тип поворота через угловое ускорение.					
							if (($wAcc < 0.45) && ($wAcc >= 0)) {
								$sevTurn = 0;
								
							} else 	if (($wAcc >= 0.45) && ($wAcc < 0.6))	{
								$sevTurn = 1;
							
							} else 	if (($wAcc >= 0.6) && ($wAcc < 0.75)){
								$sevTurn = 2;
							
							} else if ($wAcc >= 0.75) {
								$sevTurn = 3;
								
							}
							
							$deltaSpeed = $speed - $data[$i-1]['speed'];
							$accel[$i] = $deltaSpeed/$deltaTime;
							
							//Высчитываем тип неравномерного движения (ускорение-торможение) через ускорение.
							if ($accel[$i]<-7.5) {
								$sevAcc = -3;
							} else if (($accel[$i]>=-7.5)&&($accel[$i]<-6)) {
								$sevAcc = -2;
							} else if (($accel[$i]>=-6)&&($accel[$i]<-4.5)) {
								$sevAcc = -1;
							} else if ($accel[$i]>5) {
								$sevAcc = 3;
							} else if (($accel[$i]>4)&&($accel[$i]<=5)){
								$sevAcc = 2;
							} else if (($accel[$i]>3.5)&&($accel[$i]<=4)) {
								$sevAcc = 1;
							} else if (($accel[$i]>=-4.5)&&($accel[$i]<=3.5)) {
								$sevAcc = 0;
							}
							
							
							//Рассчитываем превышения скорости. Превышение (1,2,3 уровня) засчитывается, если движение осуществлялось на соответствующей скорости 5 секунд. 
							//И далее еще по очку превышения (1,2,3 уровня) за каждые ПОЛНЫЕ ТРИ секунд движения на превышенной скорости.
							if (($speed >= 0) && ($speed <= 80)) 
								$sevSpeed = 0;
							else if (($speed > 80) && ($speed <= 110))
								$sevSpeed = 1;
							else if (($speed > 110) && ($speed <= 130))
								$sevSpeed = 2;
							else if ($speed > 130)
								$sevSpeed = 3;
							
							//$typeSpeed[$i] = "normal point";
					
							if ($typeSpeed[$i-1] == "normal point") {
								if ($sevSpeed == 0) {
									$typeSpeed[$i] = "normal point";
								} else if ($sevSpeed == 1) {
									$typeSpeed[$i] = "s1";
									$dss = $deltaTime;
								} else if ($sevSpeed == 2) {
									$typeSpeed[$i] = "s2";
									$dss = $deltaTime;
								} else if ($sevSpeed == 3) {
									$typeSpeed[$i] = "s3";
									$dss = $deltaTime;
								}
							} else if ($typeSpeed[$i-1] == "s1") {

								if ($sevSpeed == 0) {
									$typeSpeed[$i] = "normal point";
									//if ($dss > 3) {}
								
									$speed1 = $speed1 + floor($dss/3);
									$dss = 0;
								
								} else if ($sevSpeed == 1) {

									$typeSpeed[$i] = "s1";
									$dss += $deltaTime;
								} else if ($sevSpeed == 2) {
									$typeSpeed[$i] = "s2";
									$speed1 = $speed1 +floor($dss/3);
									$dss = 0;
								} else if ($sevSpeed == 3) {
									$typeSpeed[$i] = "s3";
									$speed1 += floor($dss/3);
									$dss = 0;
								}
							} else if ($typeSpeed[$i-1] == "s2") {
								if ($sevSpeed == 0) {
									$typeSpeed[$i] = "normal point";
									$speed2 += floor($dss/3);
									$dss = 0;
								} else if ($sevSpeed == 1) {
									$typeSpeed[$i] = "s1";
									$speed2 = $speed2 +floor($dss/3);
									$dss = 0;
								} else if ($sevSpeed == 2) {
									$typeSpeed[$i] = "s2";
									$dss += $deltaTime;
								} else if ($sevSpeed == 3) {
									$typeSpeed[$i] = "s3";
									$speed2 += floor($dss/3);
									$dss = 0;
								}
							} else if ($typeSpeed[$i-1] == "s3") {
								if ($sevSpeed == 0) {
									$typeSpeed[$i] = "normal point";
									$speed3 += floor($dss/3);
									$dss = 0;
								} else if ($sevSpeed == 1) {
									$typeSpeed[$i] = "s1";
									$speed3 += floor($dss/3);
									$dss = 0;
								} else if ($sevSpeed == 2) {
									$typeSpeed[$i] = "s2";
									$speed3 += floor($dss/3);
									$dss = 0;
								} else if ($sevSpeed == 3) {
									$typeSpeed[$i] = "s3";
									$dss += $deltaTime;
								}
							}
							// Конец выявления превышения скорости.
							///////////////////////////////////////////////////////////////////////////////////////
							
							//Большое количество проверок условий соотношения ускорений в текущей и прошлой точках.
							if ($typeAcc[$i-1] == "normal point") {
								if ($sevAcc == 0) {
									$typeAcc[$i] = "normal point";
								} else if ($sevAcc == 1) {
									$typeAcc[$i] = "acc1 started";
								} else if ($sevAcc == 2) {
									$typeAcc[$i] = "acc2 started";
								} else if ($sevAcc == 3) {
									$typeAcc[$i] = "acc3 started";
								} else if ($sevAcc == -1) {
									$typeAcc[$i] = "brake1 started";
								} else if ($sevAcc == -2) {
									$typeAcc[$i] = "brake2 started";
								} else if ($sevAcc == -3) {
									$typeAcc[$i] = "brake3 started";
								}
							} else	if (($typeAcc[$i-1] == "acc1 started") || ($typeAcc[$i-1] == "acc1 continued")) {
								if ($sevAcc == 0) {
									$typeAcc[$i] = "normal point";
									$acc1++;
								} else if ($sevAcc == 1) {
									$typeAcc[$i] = "acc1 continued";
								} else if ($sevAcc == 2) {
									$typeAcc[$i] = "acc2 continued";
								} else if ($sevAcc == 3) {
									$typeAcc[$i] = "acc3 continued";
								} else if ($sevAcc == -1) {
									$typeAcc[$i] = "brake1 started";
									$acc1++;
								} else if ($sevAcc == -2) {
									$typeAcc[$i] = "brake2 started";
									$acc2++;
								} else if ($sevAcc == -3) {
									$typeAcc[$i] = "brake3 started";
									$acc3++;
								}
							} else	if (($typeAcc[$i-1] == "acc2 started") || ($typeAcc[$i-1] == "acc2 continued")) {
								if ($sevAcc == 0) {
									$typeAcc[$i] = "normal point";
									$acc2++;
								} else if ($sevAcc == 1) {
									$typeAcc[$i] = "acc1 started";
									$acc2++;
								} else if ($sevAcc == 2) {
									$typeAcc[$i] = "acc2 continued";
								} else if ($sevAcc == 3) {
									$typeAcc[$i] = "acc3 continued";
								} else if ($sevAcc == -1) {
									$typeAcc[$i] = "brake1 started";
									$acc2++;
								} else if ($sevAcc == -2) {
									$typeAcc[$i] = "brake2 started";
									$acc2++;
								} else if ($sevAcc == -3) {
									$typeAcc[$i] = "brake3 started";
									$acc2++;
								}
							} else	if (($typeAcc[$i-1] == "acc3 started") || ($typeAcc[$i-1] == "acc3 continued")) {
								if ($sevAcc == 0) {
									$typeAcc[$i] = "normal point";
									$acc3++;
								} else if ($sevAcc == 1) {
									$typeAcc[$i] = "acc1 started";
									$acc3++;
								} else if ($sevAcc == 2) {
									$typeAcc[$i] = "acc2 started";
									$acc3++;
								} else if ($sevAcc == 3) {
									$typeAcc[$i] = "acc3 continued";
								} else if ($sevAcc == -1) {
									$typeAcc[$i] = "brake1 started";
									$acc3++;
								} else if ($sevAcc == -2) {
									$typeAcc[$i] = "brake2 started";
									$acc3++;
								} else if ($sevAcc == -3) {
									$typeAcc[$i] = "brake3 started";
									$acc3++;
								}
							} else	if (($typeAcc[$i-1] == "brake1 started") || ($typeAcc[$i-1] == "brake1 continued")) {
								if ($sevAcc == 0) {
									$typeAcc[$i] = "normal point";
									$brake1++;
								} else if ($sevAcc == 1) {
									$typeAcc[$i] = "acc1 started";
									$brake1++;
								} else if ($sevAcc == 2) {
									$typeAcc[$i] = "acc2 started";
									$brake1++;
								} else if ($sevAcc == 3) {
									$typeAcc[$i] = "acc3 started";
									$brake1++;
								} else if ($sevAcc == -1) {
									$typeAcc[$i] = "brake1 continued";
								} else if ($sevAcc == -2) {
									$typeAcc[$i] = "brake2 started";
								} else if ($sevAcc == -3) {
									$typeAcc[$i] = "brake3 started";
								}
							} else if (($typeAcc[$i-1] == "brake2 started") || ($typeAcc[$i-1] == "brake2 continued")) {
								if ($sevAcc == 0) {
									$typeAcc[$i] = "normal point";
									$brake2++;
								} else if ($sevAcc == 1) {
									$typeAcc[$i] = "acc1 started";
									$brake2++;
								} else if ($sevAcc == 2) {
									$typeAcc[$i] = "acc2 started";
									$brake2++;
								} else if ($sevAcc == 3) {
									$typeAcc[$i] = "acc3 started";
									$brake2++;
								} else if ($sevAcc == -1) {
									$typeAcc[$i] = "brake1 started";
									$brake2++;
								} else if ($sevAcc == -2) {
									$typeAcc[$i] = "brake2 continued";
								} else if ($sevAcc == -3) {
									$typeAcc[$i] = "brake3 started";
								}
							} else	if (($typeAcc[$i-1] == "brake3 started") || ($typeAcc[$i-1] == "brake3 continued")) {
								if ($sevAcc == 0) {
									$typeAcc[$i] = "normal point";
									$brake3++;
								} else if ($sevAcc == 1) {
									$typeAcc[$i] = "acc1 started";
									$brake3++;
								} else if ($sevAcc == 2) {
									$typeAcc[$i] = "acc2 started";
									$brake3++;
								} else if ($sevAcc == 3) {
									$typeAcc[$i] = "acc3 started";
									$brake3++;
								} else if ($sevAcc == -1) {
									$typeAcc[$i] = "brake1 started";
									$brake3++;
								} else if ($sevAcc == -2) {
									$typeAcc[$i] = "brake2 started";
									$brake3++;
								} else if ($sevAcc == -3) {
									$typeAcc[$i] = "brake3 continued";
								}
							}
							
																
							//После поворота - нормальная точка.
							if (($typeTurn[$i-1] == "left turn finished") || ($typeTurn[$i-1] == "right turn finished") || (!isset($typeTurn[$i-1])) || ($speed == 0) ) {
								$typeTurn[$i] = "normal point";
							// Отклонение > 0.5 - после нормальной точки начинаем поворот налево, либо продолжаем поворот налево после уже начатого, либо завершаем, если это был поворот направо.
							} else 	if ($deltaTurn > 0.5)   {
								if ($typeTurn[$i-1] == "normal point") 
									$typeTurn[$i] = "left turn started";
								if (($typeTurn[$i-1] == "left turn started")||($typeTurn[$i-1] == "left turn continued")) 
									$typeTurn[$i] = "left turn continued";
								if (($typeTurn[$i-1] == "right turn started")||($typeTurn[$i-1] == "right turn continued")) {
									$typeTurn[$i] = "right turn finished";
									///////
								}
							// Отклонение > 0.5 - после нормальной точки начинаем поворот направо, либо продолжаем поворот направо после уже начатого, либо завершаем, если это был поворот налево.
							} else 	if ($deltaTurn < -0.5)	{
								if ($typeTurn[$i-1] == "normal point") 
									$typeTurn[$i] = "right turn started";
								if (($typeTurn[$i-1] == "right turn started")||($typeTurn[$i-1] == "right turn continued")) 
									$typeTurn[$i] = "right turn continued";
								if (($typeTurn[$i-1] == "left turn started")||($typeTurn[$i-1] == "left turn continued")) {
									$typeTurn[$i] = "left turn finished";
									///////
								}
							} else	{
							// Отклонение между -0.5 и 0.5 - после нормальной точки идет нормальная, а после начатых поворотов налево или направо - продолженные повороты соответственно налево и направо.
								if ($typeTurn[$i-1] == "normal point") 
									$typeTurn[$i] = "normal point";
								if (($typeTurn[$i-1] == "left turn started")||($typeTurn[$i-1] == "left turn continued")) {
									$typeTurn[$i] = "left turn finished";
									///////
								}
								if (($typeTurn[$i-1] == "right turn started")||($typeTurn[$i-1] == "right turn continued")) {
									$typeTurn[$i] = "right turn finished";
									///////
								}
							}
							
							if (($typeTurn[$i] == "left turn finished") || ($typeTurn[$i] == "right turn finished")) {
								switch ($sevTurn) {
										case 1: {
											$turn1++;
											break;
										}
										case 2: {
											$turn2++;
											break;
										}
										case 3: {
											$turn3++;
											break;
										}
										case 0: {
											break;
										}
									}
							}
						}
						else 	
						{
							$typeTurn[$i] = "normal point";
							$typeAcc[$i] = "normal point";
							$sevTurn = 0;
							$wAcc = 0;
							$radius = 0;
							$turn[$i] = $turn[$i-1];
						}
					
						$timeSum = 0;
						$sumSpeed = 0;
					
						
					
						$color = "white";
						if ($sevAcc==1) 
							$color = "#c3eb0d";
						if ($sevAcc==2) 
							$color = "#0deb12";
						if ($sevAcc==3) 
							$color = "#0deb88";
						if ($sevAcc==-1) 
							$color = "#ebc10d";
						if ($sevAcc==-2) 
							$color = "#eb610d";
						if ($sevAcc==-3) 
							$color = "#eb0d1b";
			
					}
					if(isset($data[$j - 1]['utimestamp']))	{
						$fullTime = ($data[$j - 1]['utimestamp'] - $data[0]['utimestamp']);
						if($fullTime!=0) {
							$drivingScore = ($coef1 * ($speed1 + $turn1 + $acc1 + $brake1) + $coef2 * ($speed2 + $turn2 + $acc2 + $brake2) + $coef3 * ($speed3 + $turn3 + $acc3 + $brake3)) / ($fullTime/3600);
							$total_time 	= 	$total_time 	+ 	$fullTime; 
							$total_score 	= 	$total_score 	+ 	$drivingScore;
							$total_turn1	=	$total_turn1	+	$turn1;
							$total_turn2	=	$total_turn2	+	$turn2;
							$total_turn3	=	$total_turn3	+	$turn3;
							$total_acc1		=	$total_acc1		+	$acc1;
							$total_acc2		=	$total_acc2		+	$acc2;
							$total_acc3		=	$total_acc3		+	$acc3;
							$total_brake1	=	$total_brake1	+	$brake1;
							$total_brake2	=	$total_brake2	+	$brake2;
							$total_brake3	=	$total_brake3	+	$brake3;
							$total_prev1	=	$total_prev1	+	$speed1;
							$total_prev2	=	$total_prev2	+	$speed2;
							$total_prev3	=	$total_prev3	+	$speed3;
						}
					}
				}
			}
			$results['is_set'] = 1;
			$results['rtitle'] = "<h3>Подробная статистика за период с ".$dt['t1']." по ".$dt['t2'].".</h3><br>";
			$results['total_turn1'] 	= 	$total_turn1;
			$results['total_turn2'] 	= 	$total_turn2;
			$results['total_turn3'] 	= 	$total_turn3;
			$results['total_acc1'] 		= 	$total_acc1;
			$results['total_acc2'] 		= 	$total_acc2;
			$results['total_acc3'] 		= 	$total_acc3;
			$results['total_brake1'] 	= 	$total_brake1;
			$results['total_brake2'] 	= 	$total_brake2;
			$results['total_brake3'] 	= 	$total_brake3;
			$results['total_prev1'] 	= 	$total_prev1;
			$results['total_prev2'] 	= 	$total_prev2;
			$results['total_prev3'] 	= 	$total_prev3;
			$results['total_turns']		=	$total_turn1	+	$total_turn2	+	$total_turn3;
			$results['total_accs']		=	$total_acc1		+	$total_acc2		+	$total_acc3;
			$results['total_brakes']	=	$total_brake1	+	$total_brake2	+	$total_brake3;
			$results['total_excesses']	=	$total_prev1	+	$total_prev2	+	$total_prev3;
			$results['total_trips']		=	$total_runs;
			$results['total_time']		=	round($total_time/3600,2);
			$results['total_score']		=	floor($total_score);
		}
		else {
			$results['is_set'] = 0;
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
	}
	
	//>>>>>>>>>>>>
	
	public function userinfo() {
		
		$userid = 8;
		$this->load->model('lays_model');
		$data = $this->lays_model->vie_wuser($userid);
		$R = 6371; // km
	
		if ($data != -1) {
			$k = 0;
			$m = 0;
			$grouped[$k][$m]=$data[0];
			$n = count($data)-1;
			for ($i=1;$i<$n-1;$i++) {
				if ($data[$i]['utimestamp'] - $grouped[$k][$m]['utimestamp'] != 0) {
					if (($data[$i]['utimestamp'] - $grouped[$k][$m]['utimestamp'] < 300) && 
						((acos(sin($data[$i]['lat'])*sin($grouped[$k][$m]['lat']) + cos($data[$i]['lat'])*cos($grouped[$k][$m]['lat']) *  cos($grouped[$k][$m]['lng']-$data[$i]['lng'])) * $R)/($data[$i]['utimestamp'] - $grouped[$k][$m]['utimestamp']) < 180))
					{
						$m++;
						$grouped[$k][$m] = $data[$i];
					}
					else
					{


							$k++;
							$m = 0;
							$grouped[$k][$m] = $data[$i];
					}
				}
			}
			
			
			
				
				$w = 0;
				$n=0;
				for($i=0;$i<count($grouped);$i++)
				{
				
				$w=0;
					for ($v=1; $v<count($grouped[$i]); $v++) {
						if ($grouped[$i][$v]['lng'] != $grouped[$i][$v-1]['lng'] ) {
							$unfilteredData[$n][$w] = $grouped[$i][$v-1];
							$w++;
						}
					}
			$n++;
				}
				unset($grouped);$v=0;
			for($i=0;$i<$n;$i++)
			{
			if(isset($unfilteredData[$i]))
				if(count($unfilteredData[$i])>10)
				{
					if(($unfilteredData[$i][count($unfilteredData[$i])-1]['distance']-$unfilteredData[$i][0]['distance'])>0.1)
					{
						$notneed=0;
						for($g=0;$g<count($unfilteredData[$i]);$g++)if($unfilteredData[$i][$g]['speed']==0)$notneed++;
						if($notneed*2<count($unfilteredData[$i]))
						{
							$grouped[$v]=$unfilteredData[$i];
							$v++;
						}
					}
				}
			}
			
			

			$grouped=array_reverse($grouped);
			
			$z=count($grouped);
			for($i=5;$i<$z;$i++)
				unset($grouped[$i]);
			
			unset($results);
			$results['tscore'] = 0;
			$total_time=0;
			$total_score=0;
			$total_turn=0;
			$total_acc=0;
			$total_brake=0;
			$total_turn1=0;
			$total_turn2=0;
			$total_turn3=0;
			$total_acc1=0;
			$total_acc2=0;
			$total_acc3=0;
			$total_brake1=0;
			$total_brake2=0;
			$total_brake3=0;
			$total_prev1=0;
			$total_prev2=0;
			$total_prev3=0;
			$tt = 0;
			$ta = 0;
			$tp = 0;
			$tb = 0;
			$ttime = 0;
		
			$unfilteredData=$grouped;
			
			for($m=0;$m<count($unfilteredData);$m++) {
				unset($data);
				$drivingScore = 0;
				$coef1 = 0.1;
				$coef2 = 0.2;
				$coef3 = 0.6;
				$deltaSpeed=0;
				$speed1=0;
				$speed2=0;
				$speed3=0;
				$acc1=0;
				$acc2=0;
				$acc3=0;
				$brake1=0;
				$brake2=0;
				$brake3=0;
				$turn1=0;
				$turn2=0;
				$turn3=0;
				$data=$unfilteredData[$m];
					$j=count($data);
					$dss = 0;
						for ($i = 1; $i < $j-1; $i++)
						{
							$typeTurn[0] = "normal point";
							$typeAcc[0] = "normal point";
							$typeSpeed[0] = "normal point";
							
							$sevTurn = 0;
							$sevAcc = 0;
							$sevSpeed = 0;
							$speed = $data[$i]['speed'];	
							$deltaTime = $data[$i]['utimestamp'] - $data[$i-1]['utimestamp'];
							
							if ( ($data[$i]['lng']-$data[$i-1]['lng']) != 0  )
							{
							
								$turn[$i] = atan(($data[$i]['lat']-$data[$i-1]['lat'])/($data[$i]['lng']-$data[$i-1]['lng']));
				
								$turn[0] = 0;
								$deltaTurn = $turn[$i] - $turn[$i-1];
								$wAcc = abs($deltaTurn/($deltaTime));
														
								//Высчитываем тип поворота через угловое ускорение.					
								if (($wAcc < 0.45) && ($wAcc >= 0)) {
									$sevTurn = 0;									
								} else 	if (($wAcc >= 0.45) && ($wAcc < 0.6))	{
									$sevTurn = 1;
								} else 	if (($wAcc >= 0.6) && ($wAcc < 0.75)){
									$sevTurn = 2;
								} else if ($wAcc >= 0.75) {
									$sevTurn = 3;
								}
								
								$deltaSpeed = $speed - $data[$i-1]['speed'];
								$accel[$i] = $deltaSpeed/$deltaTime;
								
								//Высчитываем тип неравномерного движения (ускорение-торможение) через ускорение.
								if ($accel[$i]<-7.5) {
									$sevAcc = -3;
								} else if (($accel[$i]>=-7.5)&&($accel[$i]<-6)) {
									$sevAcc = -2;
								} else if (($accel[$i]>=-6)&&($accel[$i]<-4.5)) {
									$sevAcc = -1;
								} else if ($accel[$i]>5) {
									$sevAcc = 3;
								} else if (($accel[$i]>4)&&($accel[$i]<=5)){
									$sevAcc = 2;
								} else if (($accel[$i]>3.5)&&($accel[$i]<=4)) {
									$sevAcc = 1;
								} else if (($accel[$i]>=-4.5)&&($accel[$i]<=3.5)) {
									$sevAcc = 0;
								}
								
								
								//Рассчитываем превышения скорости. Превышение (1,2,3 уровня) засчитывается, если движение осуществлялось на соответствующей скорости 5 секунд. 
								//И далее еще по очку превышения (1,2,3 уровня) за каждые ПОЛНЫЕ ТРИ секунд движения на превышенной скорости.
								if (($speed >= 0) && ($speed <= 80)) 
									$sevSpeed = 0;
								else if (($speed > 80) && ($speed <= 110))
									$sevSpeed = 1;
								else if (($speed > 110) && ($speed <= 130))
									$sevSpeed = 2;
								else if ($speed > 130)
									$sevSpeed = 3;
								
								//$typeSpeed[$i] = "normal point";
						
								if ($typeSpeed[$i-1] == "normal point") {
									if ($sevSpeed == 0) {
										$typeSpeed[$i] = "normal point";
									} else if ($sevSpeed == 1) {
										$typeSpeed[$i] = "s1";
										$dss = $deltaTime;
									} else if ($sevSpeed == 2) {
										$typeSpeed[$i] = "s2";
										$dss = $deltaTime;
									} else if ($sevSpeed == 3) {
										$typeSpeed[$i] = "s3";
										$dss = $deltaTime;
									}
								} else if ($typeSpeed[$i-1] == "s1") {

									if ($sevSpeed == 0) {
										$typeSpeed[$i] = "normal point";
										//if ($dss > 3) {}
									
										$speed1 = $speed1 + floor($dss/3);
										$dss = 0;
									
									} else if ($sevSpeed == 1) {

										$typeSpeed[$i] = "s1";
										$dss += $deltaTime;
									} else if ($sevSpeed == 2) {
										$typeSpeed[$i] = "s2";
										$speed1 = $speed1 +floor($dss/3);
										$dss = 0;
									} else if ($sevSpeed == 3) {
										$typeSpeed[$i] = "s3";
										$speed1 += floor($dss/3);
										$dss = 0;
									}
								} else if ($typeSpeed[$i-1] == "s2") {
									if ($sevSpeed == 0) {
										$typeSpeed[$i] = "normal point";
										$speed2 += floor($dss/3);
										$dss = 0;
									} else if ($sevSpeed == 1) {
										$typeSpeed[$i] = "s1";
										$speed2 = $speed2 +floor($dss/3);
										$dss = 0;
									} else if ($sevSpeed == 2) {
										$typeSpeed[$i] = "s2";
										$dss += $deltaTime;
									} else if ($sevSpeed == 3) {
										$typeSpeed[$i] = "s3";
										$speed2 += floor($dss/3);
										$dss = 0;
									}
								} else if ($typeSpeed[$i-1] == "s3") {
									if ($sevSpeed == 0) {
										$typeSpeed[$i] = "normal point";
										$speed3 += floor($dss/3);
										$dss = 0;
									} else if ($sevSpeed == 1) {
										$typeSpeed[$i] = "s1";
										$speed3 += floor($dss/3);
										$dss = 0;
									} else if ($sevSpeed == 2) {
										$typeSpeed[$i] = "s2";
										$speed3 += floor($dss/3);
										$dss = 0;
									} else if ($sevSpeed == 3) {
										$typeSpeed[$i] = "s3";
										$dss += $deltaTime;
									}
								}
								// Конец выявления превышения скорости.
								///////////////////////////////////////////////////////////////////////////////////////
								
								//Большое количество проверок условий соотношения ускорений в текущей и прошлой точках.
								if ($typeAcc[$i-1] == "normal point") {
									if ($sevAcc == 0) {
										$typeAcc[$i] = "normal point";
									} else if ($sevAcc == 1) {
										$typeAcc[$i] = "acc1 started";
									} else if ($sevAcc == 2) {
										$typeAcc[$i] = "acc2 started";
									} else if ($sevAcc == 3) {
										$typeAcc[$i] = "acc3 started";
									} else if ($sevAcc == -1) {
										$typeAcc[$i] = "brake1 started";
									} else if ($sevAcc == -2) {
										$typeAcc[$i] = "brake2 started";
									} else if ($sevAcc == -3) {
										$typeAcc[$i] = "brake3 started";
									}
								} else	if (($typeAcc[$i-1] == "acc1 started") || ($typeAcc[$i-1] == "acc1 continued")) {
									if ($sevAcc == 0) {
										$typeAcc[$i] = "normal point";
										$acc1++;
									} else if ($sevAcc == 1) {
										$typeAcc[$i] = "acc1 continued";
									} else if ($sevAcc == 2) {
										$typeAcc[$i] = "acc2 continued";
									} else if ($sevAcc == 3) {
										$typeAcc[$i] = "acc3 continued";
									} else if ($sevAcc == -1) {
										$typeAcc[$i] = "brake1 started";
										$acc1++;
									} else if ($sevAcc == -2) {
										$typeAcc[$i] = "brake2 started";
										$acc2++;
									} else if ($sevAcc == -3) {
										$typeAcc[$i] = "brake3 started";
										$acc3++;
									}
								} else	if (($typeAcc[$i-1] == "acc2 started") || ($typeAcc[$i-1] == "acc2 continued")) {
									if ($sevAcc == 0) {
										$typeAcc[$i] = "normal point";
										$acc2++;
									} else if ($sevAcc == 1) {
										$typeAcc[$i] = "acc1 started";
										$acc2++;
									} else if ($sevAcc == 2) {
										$typeAcc[$i] = "acc2 continued";
									} else if ($sevAcc == 3) {
										$typeAcc[$i] = "acc3 continued";
									} else if ($sevAcc == -1) {
										$typeAcc[$i] = "brake1 started";
										$acc2++;
									} else if ($sevAcc == -2) {
										$typeAcc[$i] = "brake2 started";
										$acc2++;
									} else if ($sevAcc == -3) {
										$typeAcc[$i] = "brake3 started";
										$acc2++;
									}
								} else	if (($typeAcc[$i-1] == "acc3 started") || ($typeAcc[$i-1] == "acc3 continued")) {
									if ($sevAcc == 0) {
										$typeAcc[$i] = "normal point";
										$acc3++;
									} else if ($sevAcc == 1) {
										$typeAcc[$i] = "acc1 started";
										$acc3++;
									} else if ($sevAcc == 2) {
										$typeAcc[$i] = "acc2 started";
										$acc3++;
									} else if ($sevAcc == 3) {
										$typeAcc[$i] = "acc3 continued";
									} else if ($sevAcc == -1) {
										$typeAcc[$i] = "brake1 started";
										$acc3++;
									} else if ($sevAcc == -2) {
										$typeAcc[$i] = "brake2 started";
										$acc3++;
									} else if ($sevAcc == -3) {
										$typeAcc[$i] = "brake3 started";
										$acc3++;
									}
								} else	if (($typeAcc[$i-1] == "brake1 started") || ($typeAcc[$i-1] == "brake1 continued")) {
									if ($sevAcc == 0) {
										$typeAcc[$i] = "normal point";
										$brake1++;
									} else if ($sevAcc == 1) {
										$typeAcc[$i] = "acc1 started";
										$brake1++;
									} else if ($sevAcc == 2) {
										$typeAcc[$i] = "acc2 started";
										$brake1++;
									} else if ($sevAcc == 3) {
										$typeAcc[$i] = "acc3 started";
										$brake1++;
									} else if ($sevAcc == -1) {
										$typeAcc[$i] = "brake1 continued";
									} else if ($sevAcc == -2) {
										$typeAcc[$i] = "brake2 started";
									} else if ($sevAcc == -3) {
										$typeAcc[$i] = "brake3 started";
									}
								} else if (($typeAcc[$i-1] == "brake2 started") || ($typeAcc[$i-1] == "brake2 continued")) {
									if ($sevAcc == 0) {
										$typeAcc[$i] = "normal point";
										$brake2++;
									} else if ($sevAcc == 1) {
										$typeAcc[$i] = "acc1 started";
										$brake2++;
									} else if ($sevAcc == 2) {
										$typeAcc[$i] = "acc2 started";
										$brake2++;
									} else if ($sevAcc == 3) {
										$typeAcc[$i] = "acc3 started";
										$brake2++;
									} else if ($sevAcc == -1) {
										$typeAcc[$i] = "brake1 started";
										$brake2++;
									} else if ($sevAcc == -2) {
										$typeAcc[$i] = "brake2 continued";
									} else if ($sevAcc == -3) {
										$typeAcc[$i] = "brake3 started";
									}
								} else	if (($typeAcc[$i-1] == "brake3 started") || ($typeAcc[$i-1] == "brake3 continued")) {
									if ($sevAcc == 0) {
										$typeAcc[$i] = "normal point";
										$brake3++;
									} else if ($sevAcc == 1) {
										$typeAcc[$i] = "acc1 started";
										$brake3++;
									} else if ($sevAcc == 2) {
										$typeAcc[$i] = "acc2 started";
										$brake3++;
									} else if ($sevAcc == 3) {
										$typeAcc[$i] = "acc3 started";
										$brake3++;
									} else if ($sevAcc == -1) {
										$typeAcc[$i] = "brake1 started";
										$brake3++;
									} else if ($sevAcc == -2) {
										$typeAcc[$i] = "brake2 started";
										$brake3++;
									} else if ($sevAcc == -3) {
										$typeAcc[$i] = "brake3 continued";
									}
								}
								
																	
								//После поворота - нормальная точка.
								if (($typeTurn[$i-1] == "left turn finished") || ($typeTurn[$i-1] == "right turn finished") || (!isset($typeTurn[$i-1])) || ($speed == 0) ) {
									$typeTurn[$i] = "normal point";
								// Отклонение > 0.5 - после нормальной точки начинаем поворот налево, либо продолжаем поворот налево после уже начатого, либо завершаем, если это был поворот направо.
								} else 	if ($deltaTurn > 0.5)   {
									if ($typeTurn[$i-1] == "normal point") 
										$typeTurn[$i] = "left turn started";
									if (($typeTurn[$i-1] == "left turn started")||($typeTurn[$i-1] == "left turn continued")) 
										$typeTurn[$i] = "left turn continued";
									if (($typeTurn[$i-1] == "right turn started")||($typeTurn[$i-1] == "right turn continued")) {
										$typeTurn[$i] = "right turn finished";
										///////
									}
								// Отклонение > 0.5 - после нормальной точки начинаем поворот направо, либо продолжаем поворот направо после уже начатого, либо завершаем, если это был поворот налево.
								} else 	if ($deltaTurn < -0.5)	{
									if ($typeTurn[$i-1] == "normal point") 
										$typeTurn[$i] = "right turn started";
									if (($typeTurn[$i-1] == "right turn started")||($typeTurn[$i-1] == "right turn continued")) 
										$typeTurn[$i] = "right turn continued";
									if (($typeTurn[$i-1] == "left turn started")||($typeTurn[$i-1] == "left turn continued")) {
										$typeTurn[$i] = "left turn finished";
										///////
									}
								} else	{
								// Отклонение между -0.5 и 0.5 - после нормальной точки идет нормальная, а после начатых поворотов налево или направо - продолженные повороты соответственно налево и направо.
									if ($typeTurn[$i-1] == "normal point") 
										$typeTurn[$i] = "normal point";
									if (($typeTurn[$i-1] == "left turn started")||($typeTurn[$i-1] == "left turn continued")) {
										$typeTurn[$i] = "left turn finished";
										///////
									}
									if (($typeTurn[$i-1] == "right turn started")||($typeTurn[$i-1] == "right turn continued")) {
										$typeTurn[$i] = "right turn finished";
										///////
									}
								}
								
								if (($typeTurn[$i] == "left turn finished") || ($typeTurn[$i] == "right turn finished")) {
									switch ($sevTurn) {
											case 1: {
												$turn1++;
												break;
											}
											case 2: {
												$turn2++;
												break;
											}
											case 3: {
												$turn3++;
												break;
											}
											case 0: {
												break;
											}
										}
								}
							}
							else 	
							{
								$typeTurn[$i] = "normal point";
								$typeAcc[$i] = "normal point";
								$sevTurn = 0;
								$wAcc = 0;
								$radius = 0;
								$turn[$i] = $turn[$i-1];
							}
						
							$timeSum = 0;
							$sumSpeed = 0;
						
							
						
							$color = "white";
							if ($sevAcc==1) 
								$color = "#c3eb0d";
							if ($sevAcc==2) 
								$color = "#0deb12";
							if ($sevAcc==3) 
								$color = "#0deb88";
							if ($sevAcc==-1) 
								$color = "#ebc10d";
							if ($sevAcc==-2) 
								$color = "#eb610d";
							if ($sevAcc==-3) 
								$color = "#eb0d1b";
				
						}
						if(isset($data[$j-2]['utimestamp']))	{
						
							$fullTime = ($data[$j-2]['utimestamp'] - $data[0]['utimestamp']);
							if($fullTime!=0) {
							
							//$number=$m;
						
								$results[$m]['score'] 		= 		($coef1 * ($speed1 + $turn1 + $acc1 + $brake1) + $coef2 * ($speed2 + $turn2 + $acc2 + $brake2) + $coef3 * ($speed3 + $turn3 + $acc3 + $brake3)) / ($fullTime/3600);
								$results[$m]['tstart']		=	 	$data[0]['utimestamp']; 
								$results[$m]['tfinish']		=	 	$data[$j-2]['utimestamp']; 				 				 
								$results[$m]['time'] 		=	 	$fullTime; 
								$results[$m]['turn1']		=		$turn1;
								$results[$m]['turn2']		=		$turn2;
								$results[$m]['turn3']		=		$turn3;
								$results[$m]['acc1']		=		$acc1;
								$results[$m]['acc2']		=		$acc2;
								$results[$m]['acc3']		=		$acc3;
								$results[$m]['brake1']		=		$brake1;
								$results[$m]['brake2']		=		$brake2;
								$results[$m]['brake3']		=		$brake3;
								$results[$m]['prev1']		=		$speed1;
								$results[$m]['prev2']		=		$speed2;
								$results[$m]['prev3']		=		$speed3;
								$results['tscore'] 			= 		$results['tscore'] + $results[$m]['score'];
								
							}
						}	
						
					
					$results['total_trips'] = $m+1;
			} //end of "$dt != -1" operators block.
		}
		else {
			$results['total_trips'] = -1;
		}
		return $results;
	}

}

?>
