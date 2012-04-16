<?php 
	class usermodel extends CI_Model {
		
		function __construct()
			{
				parent::__construct();
			}
		
		
		function authorization($user){
			$login = $user['login'];
			$password=hash('sha256', $user['password']);
			$query = $this->db->get_where('NTIUsers', array('Login' => $login, 'Password' => $password));
			if($query->num_rows()>0){
			
				foreach($query->result() as $row)
				{
					$userData['id'] = $row->Id;
					$Id=$row->Id;
					$userData['login'] = $row->Login;
					$userData['name'] = $row->FName;
					$userData['sname'] = $row->SName;
					$userData['rights'] = $row->Rights;
				}
				return $userData;
			}
			else
			{
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
		//Загружает всех пользователей системы 
		// Нужно для администратора
		function load_all_users(){
			
			$query = $this->db->query("SELECT* from NTIUsers Where Rights!=3");
			if($query->num_rows()>0){
			
				return $query->result_array();
			}
			else{
				return false;
			}
		}
		
		
		function get_all_unregdata()
		{
			$query = $this->db->query("Select Id,Insert_Time from NTIFile where UID=-3 group by Insert_Time");
			if($query->num_rows()>0)
			{
			
			   return $query->result_array();
			}
			else
			{
				return 0;
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
		function search($name)
		{
				
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
							$user[$j]=$temp;
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
			$query = $this->db->query("Select * from NTIRequests where UserId=$userid and ExpertId=$id and (Status=1 or Status=2)");
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
		
			
		function RemoveRelationQuery($id,$username)
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
			$query = $this->db->query("Select * from NTIRequests where UserId=$userid and ExpertId=$id and Status=1");
			if($query->num_rows()==0)return -3;
		
			//Отлично , значит заявка у нас не создана и отношения нет
			//Вставляем новую запись
			$data = array(
				'Status' => 4
						);
$this->db->where('UserId', $userid);
$this->db->where('ExpertId', $id);

				$this->db->update('NTIRequests', $data); 
				
				return 1;
			
		}
		
		
		function DeleteRelation($id,$username)
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
			//Теперь проверяем , есть ли такая 
			$query = $this->db->query("Select * from NTIRelations where UserID=$userid and ExpertID=$id");
			if($query->num_rows()==0)return -2;
			
			//Теперь проверяем на возможнось создания повторной заявки 
			$query = $this->db->query("Select * from NTIRequests where UserId=$userid and ExpertId=$id and (Status=1 or Status=2)");
			if($query->num_rows()>0)return -3;
		
			//Отношение есть
			//Запросов нет
			//Вставляем новую запись
				$data = array(
				'UserId' => $userid ,
				'ExpertId' => $id  ,
				'Status' => 2
						);

				$this->db->insert('NTIRequests', $data); 
				
				return 1;
			
		}
		//Загружает все активные тикеты пользователя
		function load_all_tickets($id)
		{
			$query = $this->db->query("SELECT * FROM `NTIRequests`  Join NTIUsers on NTIRequests.UserId=NTIUsers.Id where NTIRequests.Status<3 and  NTIRequests.ExpertID=$id order by Insert_time" );
			if($query->num_rows()>0)
			{
			
				return $query->result_array();
			
			}
			else
			{
				return false;;
			}
		}
		
		function load_expert_users($id)
		{
			$query = $this->db->query("SELECT * FROM `NTIRelations`  Join NTIUsers on NTIRelations.UserId=NTIUsers.Id where NTIRelations.ExpertID=$id order by Login" );
			if($query->num_rows()>0)
			{
			
				return $query->result_array();
			
			}
			else
			{
				return false;;
			}
		}
		
		function LoadUsers()
		{
			$query = $this->db->query("SELECT * FROM `NTIUsers` where Rights=0 order by Id" );
			if($query->num_rows()>0)
			{
			
				return $query->result_array();
			
			}
			else
			{
				return false;;
			}
		}
		function LoadExperts()
		{
			$query = $this->db->query("SELECT * FROM `NTIUsers` where Rights=2 order by Id" );
			if($query->num_rows()>0)
			{
			
				return $query->result_array();
			
			}
			else
			{
				return false;;
			}
		}
		function LoadTickets()
		{
			$query = $this->db->query("select al.RequestId,al.Eid,al.Uid,al.Status,al.FName as CKName,al.SName as CKSName,al.Login as CKLogin,al.Rights as CKRights,us.Login as ULogin,us.FName as UFName,us.SName as USName,us.Rights as URights from (SELECT NTIRequests.Id as RequestId,NTIRequests.`ExpertId` as Eid,NTIRequests.`UserId` as Uid,NTIRequests.`Status` as Status,exp.* FROM `NTIRequests` join (select Id,Login,FName,SName,Rights from NTIUsers) as exp on NTIRequests.`ExpertId`=exp.Id) as al join (select Id,Login,FName,SName,Rights from NTIUsers) as us on al.`Uid`=us.Id where Status<=2" );
			if($query->num_rows()>0)
			{
			
				return $query->result_array();
			
			}
			else
			{
				return false;;
			}
		}
		//Блокирует пользователя
		function BlockUser($username)
		{
						$data = array(
								'Deleted' => 1
									 );
						$this->db->where('Login', $username);

				$this->db->update('NTIUsers', $data); 
				return 1;
		}
	//Разблокировка  пользователя
		function UnBlockUser($username)
		{
						$data = array(
								'Deleted' => 0
									 );
						$this->db->where('Login', $username);

				$this->db->update('NTIUsers', $data); 
				return 1;
		}
		//registration
		function registration($userData){
				extract($userData);
				$email=mysql_real_escape_string($email);
				$name=mysql_real_escape_string($fname);
				$surname=mysql_real_escape_string($sname);
				$emailcheck = array('Email' => $email);
				
				$query = $this->db->get_where('NTIUsers',$emailcheck);

				if ($query->num_rows() > 0)
				{
					return array('login' => -1, 'password' => -1,'result'=>-1);
					
				}
				
				$userData = array('Login' => $email ,'Password' => hash('sha256', $password),'FName' => $name ,'SName' => $surname,'Email'=>$email);
				
				$query=$this->db->insert('NTIUsers', $userData);
				//Получение ID пользователя, которого только что вставили
				$userID=$this->db->insert_id(); 
				//Генерируем рандомную строку 
				$email_key= random_string('alnum', 64);
				$unixtimestamp=time();
				$userEmailCheck = array('UserId' => $userID ,'Unixtimestamp' => $unixtimestamp,'Key' => $email_key);
				$query=$this->db->insert('EmailApproveKey', $userEmailCheck); 
				
				
				
				return array('login' => $email, 'password' => $password,'result'=>1,'emailkey'=>$email_key);
						
		}
	
		
		
		function CheckRelation($key){
			//Здесь также происходит с помощью Codeigniter фильтрация на инъекции
				$emailcheck = array('Key' => $key,'Deleted'=>0);	
				$query = $this->db->get_where('EmailApproveKey',$emailcheck);

				if ($query->num_rows() > 0)
				{
						//Нашли значение 
						//Теперь проверяем его на жизнеспособность
						foreach($query->result() as $row)
						{
									$userid = $row->UserId;
									$rowID = $row->Id;
									$Live = $row->Unixtimestamp;
						}
						if(time()-$Live>1000000)
						{
							//Удаляем
							$data = array('Deleted' => 1);
							$this->db->where('Id', $rowID);
							$this->db->update('EmailApproveKey', $data); 
							return -1;
						}
						else
						{
							$data = array('Deleted' => 1);
							$this->db->where('Id', $rowID);
							$this->db->update('EmailApproveKey', $data); 
							
							$data = array('Rights' => 1);
							$this->db->where('Id', $userid);
							$this->db->update('NTIUsers', $data); 
							return 1;
						}
						
				}
				else
				{
					return -1;
				}
						
		}
		function passwordremember($email)
	{
				$password= random_string('alnum', 64);
				$emailcheck = array('Login' => $email);
				
				$query = $this->db->get_where('NTIUsers',$emailcheck);

				if ($query->num_rows() == 0)
				{
					return array('result'=>-1);
					
				}
			   foreach($query->result() as $row)
				{
									$userid = $row->Id;
				}
				$unixtime=time();
				
				$userData = array('Key' => $password ,'UserId' => $userid ,'UnixTimeStamp' => $unixtime);
				
				$query=$this->db->insert('PasswordRecovery', $userData);
					
				return array('Key' => $password, 'result' => 1);
			
			
			
			
	}
			function make_new_password_by_ukey($password,$userkey)
	{
			
		//Для начала проверяем существование ключа
				$data=array('Key' => $userkey,'Deleted'=>0);
				$query = $this->db->get_where('PasswordRecovery',$data);

				if ($query->num_rows() >0)
				{
						foreach($query->result() as $row)
						{
									$userid = $row->UserId;
									$rowID = $row->Id;
									$Live = $row->UnixTimeStamp;
						}
						if(time()-$Live>1000000)
						{
							//Удаляем
							$data = array('Deleted' => 1);
							$this->db->where('Id', $rowID);
							$this->db->update('PasswordRecovery', $data); 
							return -1;
						}
						else
						{
							$data = array('Deleted' => 1);
							$this->db->where('Id', $rowID);
							$this->db->update('PasswordRecovery', $data); 
							
							$password=hash('sha256', $password);
							$data = array('Password' => $password);
							$this->db->where('Id', $userid);
							$this->db->update('NTIUsers', $data); 
							return 1;
						}
					
				}
				else
				{
					return -1;
					
				}
				
			  
				
	
			
				
			
			
	}
		
	}
