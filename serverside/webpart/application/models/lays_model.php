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
	
	public function cksearch($ckid) {
		$q = $this->db->query("SELECT * FROM (select tablet_user.* ,coalesce(`Status`,0) as Stat from (select allusers.* ,coalesce(`ExpertID`,0) as Bnd from (SELECT Id,Login,FName,SName FROM `NTIUsers` WHERE `Rights`<2 and Deleted=0) as allusers Left OUTER JOIN  (select * from NTIRelations where ExpertID=$ckid) as expert on allusers.Id=expert.UserID) as tablet_user Left OUTER JOIN (Select `Status`,`UserId` from NTIRequests where `ExpertId`=$ckid and Status<=2) as request on tablet_user.Id=request.`UserId`) as prst ORDER BY Login");
		$n = 0;
		if ($q->num_rows() > 0) {
			foreach($q->result() as $row) {
				$da[$n]['Id'] = $row->Id;
				$da[$n]['Login'] = $row->Login;
				$da[$n]['FName'] = $row->FName;
				$da[$n]['SName'] = $row->SName;
				$da[$n]['Bnd'] = $row->Bnd;
				$da[$n]['Stat'] = $row->Stat;
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



