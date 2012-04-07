<?php
class Entry extends CI_Model {
  function __construct()
    {
        parent::__construct();
    }
    function getbyuid($uid)
	{			
		
			$query = $this->db->get_where('NTIEntry',array('UID' => $uid));
			if($query->num_rows()>0){
			
				return $query->result_array();
			}
			else{
				return false;
			}
		
	}
	
	    function getbyid($uid)
	{			
		$k=0;
		$query = $this->db->query("SELECT * FROM `NTIFile`,(select Insert_Time from NTIFile where Id=$uid) as Ps where NTIFile.Insert_Time=Ps.Insert_Time");
		
			if($query->num_rows()>0){
			
				foreach ($query->result() as $row)
                    {
		
					if($row->Type=='1')
					{

						$entrys = json_decode($row->File,true);
							for($i=0;$i<count($entrys);$i++)
	                         {

	                         $ret_data[$k]['lat']=$entrys[$i]['gps']['latitude'];
	                         $ret_data[$k]['lng']=$entrys[$i]['gps']['longitude'];
	                         $ret_data[$k]['compass']=$entrys[$i]['gps']['compass'];
	                         $ret_data[$k]['speed']=$entrys[$i]['gps']['speed'];
	                         $ret_data[$k]['distance']=$entrys[$i]['gps']['distance'];
	                         $ret_data[$k]['utimestamp']=$entrys[$i]['timestamp'];
	                         
							$k++;

	                         }
						 }
						 else
						 {
							$ret_data[0][1]=$row->File;
							$ret_data[0][2]=1;
						 }
	                         
					}
					
					return $ret_data;
			}
			else{
				return false;
			}
		
	}
	
	    
	


	
    
}
