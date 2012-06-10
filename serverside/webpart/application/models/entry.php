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
	

	
	    
	


	
    
}
