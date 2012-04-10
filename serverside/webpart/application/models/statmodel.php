<?php

class statmodel extends CI_Model {

	function display() {
		//selects data from last 7 days.
		$this->db->where('utimestamp >', time()-7*60*60*24);
		$query = $this->db->get('NTIEntry');
		$i = 0;
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$ret[$i]['accx'] = $row->accx;
				$ret[$i]['accy'] = $row->accy;
				$ret[$i]['speed'] = $row->speed;
				$ret[$i]['lat'] = $row->lat;
				$ret[$i]['lng'] = $row->lng;
				$ret[$i]['compass'] = $row->compass;
				$ret[$i]['distance'] = $row->distance;
				$ret[$i]['direction'] = $row->direction;
				$ret[$i]['utimestamp'] = $row->utimestamp;
				$i++;
			}
			return $ret;
		}
		else {
			return false;
		}
	}
}


?>