<?php 
	class usermodel extends CI_Model {
		
		function __construct()
			{
				parent::__construct();
			}
		
		
		function authorization($user){
			$login = $user['login'];
				if(!preg_match("/^[a-zA-Z0-9]+$/",$login))return false;
				$login= mysql_real_escape_string($login);


			$password=hash('sha256', $user['password']);
			$query = $this->db->get_where('NTIUsers', array('Login' => $login, 'Password' => $password));
			if($query->num_rows()>0){
			
				foreach($query->result() as $row){
					$userData['id'] = $row->Id;
					$Id=$row->Id;
					$userData['login'] = $row->Login;
					$userData['name'] = $row->FName;
					$userData['sname'] = $row->SName;
					$userData['rights'] = $row->Rights;
				}
				return $userData;
			}
			else{
				return false;
			}
		}
		
			function get_all_users($id){
			
			$query = $this->db->query("SELECT NTIUsers.* from NTIUsers,(Select UserID from NTIRelations where ExpertID=$id) as ExpertRel where NTIUsers.Id=ExpertRel.UserID");
			if($query->num_rows()>0){
			
				return $query->result_array();
			}
			else{
				return false;
			}
		}
		
		
		
		function checkrealation($id,$username)
		{
		$query = $this->db->query("Select UserID from NTIRelations,(Select * from NTIUsers where Login=".$this->db->escape($username).") as UserRel where UserRel.ID=NTIRelations.UserID and NTIRelations.ExpertID=$id");
			if($query->num_rows()>0){
			
			return 1;
			}
			else{
				return 0;
			}
		}
		
		
		//Получение последних count пользователей
		function load_users_list($count)
		{
			$query = $this->db->query("Select * from NTIUsers where Rights=0 order by Id limit $count");
			if($query->num_rows()>0){
			
			return $query->result_array();
			}
			else{
				return false;
			}
		}
		
		//Функция отвечает за поиск пользователя по параметрам
		//Также относительно пользователя собирает инфу о его отногениях
		function search($userData)
		{
				extract($userData);
				$name=$this->db->escape($name);
				//Методом n-грамма ищем похожих
				$query = $this->db->query("Select * from NTIUsers where Rights=0");
				//Получили все данные 
				if($query->num_rows()>0)
				{
					$i=0;
					foreach($query->result() as $row)
					{
						$user[$i]['Id']= $row->Id;
						$user[$i]['Login']= $row->Login;
						$user[$i]['FName']= $row->FName;
						$user[$i]['SName']= $row->SName;
						$user[$i]['need']= 0;
						$user[$i]['priority']= 0;
						$i++;
					}
				}
				for($i=0;$i<count($user);$i++)
				{
					similar_text($name,$user[$i]['Login'],$ps);
					if($ps>=60){$user[$i]['need']=1;$user[$i]['priority']=$ps;}
					similar_text($name,$user[$i]['FName'],$ps);
					if($ps>=60)
					{
						$user[$i]['need']=1;
						if($ps>$user[$i]['priority'])$user[$i]['priority']=$ps;
					}
					
					similar_text($name,$user[$i]['SName'],$ps);
					if($ps>=60)
					{
						$user[$i]['need']=1;
						if($ps>$user[$i]['priority'])$user[$i]['priority']=$ps;
					}
					
					
				}
				//Отлично , мы получили отсортированный массив похожестей
				//Теперь сортируем его !
				$k=0;
				//Для начала удалим всё, что содержит приоритет 0
				for($i=0;$i<count($user);$i++)
				{
						for($j=0;$j<count($user)-1;$j++)
					{
						if($user[$j]['priority']>$user[$j+1]['priority'])
						{
							$temp=$user[$j+1];
							$user[$j+1]=$user[$j];
							$user[$j]=$user[$j+1];
						}
						
					
					}					
				}
				//Отсортировали пузырьком 
				//Формируем новый массив, в котором только самые нужные значения 
				$j=0;
				
				for($i=0;$i<count($user);$i++)
				{
					if($user[$i]['need']==1)
					{
						$ret_arr[$j]=$user[$i];
						$j++;	
					}
					
				}
				echo "array";
				print_r($ret_arr);
				if(empty($ret_arr))return false;
				else
				 return $ret_arr;

		}
		
		
		function GetUserStatistics($ExpertId,$UserId)
		{
			$query = $this->db->query("Select * from NTIRelations where ExpertID=$ExpertId and UserID=$UserId");
				if($query->num_rows()>0)
				{
			
					return 1;//Отношение существует
				}
			$query = $this->db->query("Select * from NTIRequests where ExpertId=$ExpertId and UserId=$UserId and Status<3");
				if($query->num_rows()>0)
				{
			
					return 2;//Заявка уже подана 
				}
				return 3;//Ничего нет, можно добавлять 
		}
		
		
		
		
		
		//Функиця отвечает за проверку и добавление заявки пользователя
		
		function AddRelationQuery($id,$username)
		{
			//Сначала получаем id пользователя относительно его имени
			$query = $this->db->query("Select * from NTIUsers where Login=".$this->db->escape($username));
			if($query->num_rows()>0){
			
				foreach($query->result() as $row){
					$userid= $row->Id;
					
				}
			}
			else
			{
				return -1;
			}
			//Теперь проверяем , может уже была создано отношение?
			$query = $this->db->query("Select * from NTIRelations where UserID=$userid and ExpertID=$id");
			if($query->num_rows()>0)return -2;
			
			//Теперь проверяем на возможнось создания повторной заявки 
			$query = $this->db->query("Select * from NTIRequests where UserId=$userid and ExpertId=$id and Status<3");
			if($query->num_rows()>0)return -3;
		
			//Отлично , значит заявка у нас не создана и отношения нет
			//Вставляем новую запись
			$data = array(
				'UserId' => $userid ,
				'ExpertId' => $id  ,
				'Status' => 1
						);

				$this->db->insert('NTIRequests', $data); 
				
				return 1;
			
		}
		
		
		//registration
		function registration($userData){
			extract($userData);
				if(!preg_match("/^[a-zA-Z0-9]+$/",$login)){return array('login' => -1, 'password' => -1,'result'=>-2);}
				if(!preg_match("/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/",$email))return array('login' => -1, 'password' => -1,'result'=>-3);
				$login=mysql_real_escape_string($login);
				$name=mysql_real_escape_string($fname);
				$surname=mysql_real_escape_string($sname);
				
				$email=mysql_real_escape_string($email);
				
				$usercheck = array('Login' => $login);
				$emailcheck = array('Email' => $email);
				
				$query = $this->db->get_where('NTIUsers',$usercheck);

				if ($query->num_rows() > 0)
				{
					return array('login' => -1, 'password' => -1,'result'=>-1);
					
				}
				
								$query = $this->db->get_where('NTIUsers',$emailcheck);

				if ($query->num_rows() > 0)
				{
					return array('login' => -1, 'password' => -1,'result'=>-1);
					
				}
				
				$userData = array('Login' => $login ,'Password' => hash('sha256', $password),'FName' => $name ,'SName' => $surname,'Email'=>$email);
				
				$query=$this->db->insert('NTIUsers', $userData); 
				if($query){
						return array('login' => $login, 'password' => $password,'result'=>1);
				}
			
			return false;
		}
	
		
	}
