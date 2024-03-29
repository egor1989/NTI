<?php 
	class admin_functions extends CI_Model {
		
		function __construct()
			{
				parent::__construct();
			}
		//Функция отвечает за добав
		function approve($relation_id,$rtype)
		{
			//1 получение данных о отношении
			$query = $this->db->get_where('NTIRequests', array('Id' => $relation_id,'Type'=>$rtype));
			foreach($query->result() as $row)
				{
					$CKId = $row->ExpertId;
					$UserId = $row->UserId;
				}
			//Теперь проверяем, было ли создано такое отношение прежде
			if($rtype==0)
			{
				$query = $this->db->get_where('NTIRelations', array('ExpertID' => $CKId,'UserID' => $UserId));
				if($query->num_rows()>0)
				{
				//Отношение было создано до нас
					return -1;
				}
			
				$this->db->insert('NTIRelations', array('ExpertID' => $CKId,'UserID' => $UserId,'Type'=>$rtype)); 
				$data = array('Status' => 4);
				$this->db->where('Id', $relation_id);
				$this->db->where('Type', $rtype);
				$this->db->update('NTIRequests', $data); 
			}
			else
			{
				$query = $this->db->get_where('NTIRelations', array('ExpertID' => $CKId,'UserID' => $UserId));
				if($query->num_rows()>0)
				{
						$data = array('Status' => 4);
						$this->db->where('Id', $relation_id);
						$this->db->where('Type', $rtype);
						$this->db->update('NTIRequests', $data); 
						
						
						$data = array('Type' => 1);
						$this->db->where('ExpertID', $CKId);
						$this->db->where('UserID', $UserId);
						$this->db->update('NTIRelations', $data); 
						
						
						
				}
			}
			return 1;	
		}
		//Функция отвечает за добав
		function dismiss($relation_id,$rtype)
		{
			$data = array('Status' => 3);
			$this->db->where('Id', $relation_id);
			$this->db->where('Type', $rtype);
			$this->db->update('NTIRequests', $data); 
			return 1;	
		}
		
		//Блокирует пользователя
		function banuser($userid)
		{
			$data = array('Deleted' => 1);
			$this->db->where('Id', $userid);
			$this->db->update('NTIUsers', $data); 
			return 1;	
		}
		//Разблокирует пользователя
		function unbanuser($userid)
		{
			$data = array('Deleted' => 0);
			$this->db->where('Id', $userid);
			$this->db->update('NTIUsers', $data); 
			return 1;	
		}
		//Применяет к пользователю новые права (0,1,2)
		function chrights($data) {
			$d = array(
				'Rights' => mysql_real_escape_string($data['r'])
            );
			$this->db->where('Id', $data['i']);
			$this->db->update('NTIUsers', $d);
			return 1;		
		}
		//Изменяет пароль другого пользователя
		function chpassword($data) {
			$d = array(
				'Password' => hash('sha256', $data['p'])
            );
			$this->db->where('Id', $data['i']);
			$this->db->update('NTIUsers', $d);
			return 1;		
		}		
	}
?>
