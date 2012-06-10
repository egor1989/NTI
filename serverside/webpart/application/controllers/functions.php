<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class functions extends CI_Controller {
	public function get()
	{	
		if($this->session->userdata('rights')==3)
		{
			$this->load->model('lays_model');
			$this->load->library('session');
				$admin_data= $this->session->userdata('page');
				
			$new_data=$this->lays_model->getByRide($admin_data);	
			if(!isset($new_data[0][2]))
				{
				for($i=0;$i<count($new_data);$i++)
				{
					echo $new_data[$i]['lat'].",".$new_data[$i]['lng'].",".$new_data[$i]['compass'].",".$new_data[$i]['speed'].",".$new_data[$i]['distance'].",".$new_data[$i]['utimestamp'].",".$new_data[$i]['sevAcc'].",".$new_data[$i]['sevTurn'].",".$new_data[$i]['sevSpeed'].",".$new_data[$i]['Info'].",".$new_data[$i]['type'].",".$new_data[$i]['weight'].",\n";
				
				
				
				}
			}
			else
		 {
			 echo $new_data[0][1];
		 }
	 }
	 else
	 {
		 header("Location: http://nti.goodroads.ru/");
	 }
}
	
	


}
