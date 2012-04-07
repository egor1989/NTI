<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class functions extends CI_Controller {
	public function get()
	{	
			$this->load->model('entry');
			$this->load->library('session');
				$admin_data= $this->session->userdata('page');
				
			$new_data=$this->entry->getbyid($admin_data);	
			if(!isset($new_data[0][2]))
			{
			for($i=0;$i<count($new_data);$i++)
			{
				echo $new_data[$i]['lat'].",".$new_data[$i]['lng'].",".$new_data[$i]['compass'].",".$new_data[$i]['speed'].",".$new_data[$i]['distance'].",".$new_data[$i]['utimestamp'].",\n";
			}
		 }
		 else
		 {
			 echo $new_data[0][1];
		 }
		}
	
	


}
