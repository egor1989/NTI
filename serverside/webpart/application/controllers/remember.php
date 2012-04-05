<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Remember extends CI_Controller {
	
	public function index()
	{

					$new_data['map_type'] = 2;
					$new_data['rights']=0;
					$new_data['show_menu']=0;
					$this->load->view('header',$new_data);
					$this->load->view('remember_password',$new_data);
					$this->load->view('footer');
			
	}
	
	
	public function recover()
	{
		
		$email=$this->input->post('email');
		$new_data['rights']=0;
		
		
		if ($this->security->xss_clean($email, TRUE) === FALSE)
		{
					$new_data['map_type'] = 2;
					$new_data['rights']=0;
					$new_data['show_menu']=0;
					$new_data['specinfo'] = "Не верный формат поля";
					$this->load->view('header',$new_data);
					$this->load->view('remember_password',$new_data);
					$this->load->view('footer');
					
		}
		
		else
		{
		$this->load->model('userModel');
		$response = $this->userModel->passwordremember($email);
	
		
			if($response['result']==1)
			{
				$config['mailtype'] = 'html';
				$config['wordwrap'] = TRUE;
				$this->load->library('email');
				$this->email->initialize($config);
				$this->email->from('support@goodroads.ru', 'NTI');
				$this->email->to($email); 
				$this->email->subject('Password recovery');
				$emailLink=$response['Key'];
				$subject="<html>";
				$subject.="<head>";
				$subject.="</head>";
				$subject.="<body>";
				$subject.="Запрос на восстановление пароля<br/>";
				$subject.="Для восстановления пароля перейдите по ссылке ниже:<br/>";
				$subject.="<a href='http://nti.goodroads.ru/remember/passwordrecovery/$emailLink'>Восстановить</a><br/>";
				$subject.="</body>";
				$subject.="</html>";				
				$this->email->message($subject);	
				$this->email->send();
				header("Location: http://nti.goodroads.ru/user");
			}
			else
			{
					$new_data['map_type'] = 2;
					$new_data['specinfo'] = "Извините, но пользователя не существует";
					$this->load->view('header',$new_data);
					$this->load->view('remember_password',$new_data);
					$this->load->view('footer');
			}
	}
	}
		public function passwordrecovery()
		{
			//1 выделеем снача ключ пользваотеля , который нам пришел
			$this->load->helper('url');
			$password_recovery_key=$this->uri->segment(3);
			$new_data['map_type'] = 2;
			$new_data['rights']=0;
			$new_data['show_menu']=0;
			$new_data['hidden_info']=$password_recovery_key;
			
			$this->load->view('header',$new_data);
			$this->load->view('recovery',$new_data);
			$this->load->view('footer');

	}
	
	
	public function newpassword()
		{
			$password=$this->input->post('password');
			$userkey=$this->input->post('userkey');
			$this->load->model('userModel');
			$response = $this->userModel->make_new_password_by_ukey($password,$userkey);
			if($response==1)
			{
					$new_data['map_type'] = 2;
					$new_data['rights']=0;
					$new_data['show_menu']=0;
					$new_data['info']="Пароль был изменен";
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
