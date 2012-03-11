<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class functions extends CI_Controller {
	public function get()
	{	
			$this->load->model('entry');
			$new_data=$this->entry->getbyuid(-3);	
			for($i=0;$i<count($new_data);$i++)
			{
				echo $new_data[$i]['lat'].",".$new_data[$i]['lng'].",".$new_data[$i]['compass'].",".$new_data[$i]['speed'].",".$new_data[$i]['distance'].",".$new_data[$i]['utimestamp'].",\n";
			}
		}
	
	


}
