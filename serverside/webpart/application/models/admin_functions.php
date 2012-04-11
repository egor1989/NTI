<?php 
	class admin_functions extends CI_Model {
		
		function __construct()
			{
				parent::__construct();
			}
		//Функция отвечает за добав
		function approve($relation_id)
		{
			//1 получение данных о отношении
			$query = $this->db->get_where('NTIRequests', array('Id' => $relation_id));
			foreach($query->result() as $row)
				{
					$CKId = $row->ExpertId;
					$UserId = $row->UserId;
				}
			//Теперь проверяем, было ли создано такое отношение прежде
			$query = $this->db->get_where('NTIRelations', array('ExpertID' => $CKId,'UserID' => $UserId));
			if($query->num_rows()>0)
			{
			//Отношение было создано до нас
				return -1;
			}
			$this->db->insert('NTIRelations', array('ExpertID' => $CKId,'UserID' => $UserId)); 
			$data = array('Status' => 4);
			$this->db->where('Id', $relation_id);
			$this->db->update('NTIRequests', $data); 
			return 1;	
		}
		//Функция отвечает за добав
		function dismiss($relation_id)
		{
			$data = array('Status' => 3);
			$this->db->where('Id', $relation_id);
			$this->db->update('NTIRequests', $data); 
			return 1;	
		}
	}
