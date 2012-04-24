<?php

class lays_model extends CI_Model {

	public function search($d,$userid) {
	
		$time1 = strtotime($d['t1']);
		$time2 = strtotime($d['t2']);	
		$usr=mysql_real_escape_string($userid);
		$time1=mysql_real_escape_string($time1);
		$time2=mysql_real_escape_string($time2);
		$q = $this->db->query("SELECT * FROM NTIEntry where UID=$usr and utimestamp >= $time1 AND utimestamp <= $time2 AND lat != 0 AND lng != 0 group by utimestamp order by utimestamp");
		$n=0;
		//Если не было поездок за текущий период или не передано ни одной точки.
		if ($q->num_rows() > 0) {
			foreach($q->result() as $row) {
				$da[$n]['lat'] = $row->lat;
				$da[$n]['lng'] = $row->lng;
				$da[$n]['compass'] = $row->compass;
				$da[$n]['speed'] = $row->speed;
				$da[$n]['distance'] = $row->distance;
				$da[$n]['utimestamp'] = $row->utimestamp;
				$n++;
			}
			return $da;
		} 
		else {

			return -1;
		}
	}
	
	
	public function getall($userid) {
	
		$usr=mysql_real_escape_string($userid);
		$q = $this->db->query("SELECT * FROM NTIEntry where UID=$usr AND lat != 0 AND lng != 0 group by utimestamp order by utimestamp");
		$n=0;
		//Если не было поездок за текущий период или не передано ни одной точки.
		if ($q->num_rows() > 0) {
			foreach($q->result() as $row) {
				$da[$n]['lat'] = $row->lat;
				$da[$n]['lng'] = $row->lng;
				$da[$n]['compass'] = $row->compass;
				$da[$n]['speed'] = $row->speed;
				$da[$n]['distance'] = $row->distance;
				$da[$n]['utimestamp'] = $row->utimestamp;
				$n++;
			}
			return $da;
		} else {
				return -1;
		}
	}
	
		
	
	public function vie_wuser($userid) {
		
		$usr=mysql_real_escape_string($userid);
		$q = $this->db->query("SELECT * FROM NTIEntry where UID=$usr AND lat != 0 AND lng != 0 group by utimestamp order by utimestamp");
		$n=0;
		//Если не было поездок за текущий период или не передано ни одной точки.
		if ($q->num_rows() > 0) {
			foreach($q->result() as $row) {
				$da[$n]['lat'] = $row->lat;
				$da[$n]['lng'] = $row->lng;
				$da[$n]['compass'] = $row->compass;
				$da[$n]['speed'] = $row->speed;
				$da[$n]['distance'] = $row->distance;
				$da[$n]['utimestamp'] = $row->utimestamp;
				$n++;
			}
			return $da;
		} 
		else {

			return -1;
		}
	}
}

?>



