<?php 
	class admin_functions extends CI_Model {
		
		function __construct()
			{
				parent::__construct();
			}
		
		
		
		
		
		//Функиця отвечает за добав
		function approve($relation_id)
		{
			//
			
			$data = array('Status' => 4);
			
			$this->db->where('Id', $relation_id);
			$this->db->update('NTIRequests', $data); 
			
		}
		
			
		
	}
