<?php 
	class ticket_model extends CI_Model {
		
		function __construct()
			{
				parent::__construct();
			}
		
		function LoadTickets()
		{
			$query = $this->db->query("select al.RequestId,al.Eid,al.Uid,al.Status,al.FName as CKName,al.SName as CKSName,al.Login as CKLogin,al.Rights as CKRights,us.Login as ULogin,us.FName as UFName,us.SName as USName,us.Rights as URights from (SELECT NTIRequests.Id as RequestId,NTIRequests.`ExpertId` as Eid,NTIRequests.`UserId` as Uid,NTIRequests.`Status` as Status,exp.* FROM `NTIRequests` join (select Id,Login,FName,SName,Rights from NTIUsers) as exp on NTIRequests.`ExpertId`=exp.Id) as al join (select Id,Login,FName,SName,Rights from NTIUsers) as us on al.`Uid`=us.Id " );
			if($query->num_rows()>0)
			{
			
				return $query->result_array();
			
			}
			else
			{
				return false;;
			}
		}
		
		
		
	}
