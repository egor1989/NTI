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
	
	
		public function getTotalStats($userid) {
		$usr=mysql_real_escape_string($userid);
		$q = $this->db->query("SELECT sum(`TotalBrake1Count`*0.1+`TotalBrake2Count`*0.25+`TotalBrake3Count`*0.65)/count(*) as BrakeK,sum(`TotalAcc1Count`*0.1+`TotalAcc2Count`*0.25+`TotalAcc3Count`*0.65)/count(*) as AccK,sum(`TotalSpeed1Count`*0.1+`TotalSpeed2Count`*0.25+`TotalSpeed3Count`*0.65)/count(*) as SpeedK,sum(`TotalTurn1Count`*0.1+`TotalTurn2Count`*0.25+`TotalTurn3Count`*0.65)/count(*) as TurnK  FROM `NTIUserDrivingTrack` ");
			foreach($q->result() as $row) {
				$BrakeK = $row->BrakeK;
			    $AccK = $row->AccK;
				$SpeedK = $row->SpeedK;
				$TurnK = $row->TurnK;
			}
		
		
		
		$q = $this->db->query("SELECT * FROM NTIUserDrivingTrack where UID=$userid");
		$n=0;
		$da['tscore']=0;
		//Если не было поездок за текущий период или не передано ни одной точки.
		if ($q->num_rows() > 0) {
			foreach($q->result() as $row) {
				$da[$n]['TimeStart'] = $row->TimeStart;
				$da[$n]['TimeEnd'] = $row->TimeEnd;
				$da[$n]['Id'] = $row->Id;
				$da[$n]['TotalAcc1Count'] = $row->TotalAcc1Count;
				$da[$n]['TotalAcc2Count'] = $row->TotalAcc2Count;
				$da[$n]['TotalAcc3Count'] = $row->TotalAcc3Count;
				$da[$n]['TotalBrake1Count'] = $row->TotalBrake1Count;
				$da[$n]['TotalBrake2Count'] = $row->TotalBrake2Count;
				$da[$n]['TotalBrake3Count'] = $row->TotalBrake3Count;
				$da[$n]['TotalSpeed1Count'] = $row->TotalSpeed1Count;
				$da[$n]['TotalSpeed2Count'] = $row->TotalSpeed2Count;
				$da[$n]['TotalSpeed3Count'] = $row->TotalSpeed3Count;
				$da[$n]['TotalTurn1Count'] = $row->TotalTurn1Count;
				$da[$n]['TotalTurn2Count'] = $row->TotalTurn2Count;
				$da[$n]['TotalTurn3Count'] = $row->TotalTurn3Count;
				$da[$n]['total_dist']=0;
				$da[$n]['total_acc_score'] =  100*($row->TotalAcc1Count*0.1+ $row->TotalAcc2Count*.025+ $row->TotalAcc3Count*0.65)/$AccK;
				$da[$n]['total_brk_score'] = 100*($row->TotalBrake1Count*0.1+ $row->TotalBrake2Count*.025+ $row->TotalBrake3Count*0.65)/$BrakeK;
				$da[$n]['total_crn_score'] = 100*($row->TotalTurn1Count*0.1+ $row->TotalTurn2Count*.025+ $row->TotalTurn3Count*0.65)/$TurnK;
				$da[$n]['total_spd_score'] =100*($row->TotalSpeed1Count*0.1+ $row->TotalSpeed2Count*.025+ $row->TotalSpeed3Count*0.65)/$AccK;
				$da[$n]['total_all_score'] =($row->TotalAcc1Count*0.1+ $row->TotalAcc2Count*.025+ $row->TotalAcc3Count*0.65)+($row->TotalBrake1Count*0.1+ $row->TotalBrake2Count*.025+ $row->TotalBrake3Count*0.65)+($row->TotalTurn1Count*0.1+ $row->TotalTurn2Count*.025+ $row->TotalTurn3Count*0.65)+($row->TotalSpeed1Count*0.1+ $row->TotalSpeed2Count*.025+ $row->TotalSpeed3Count*0.65);
				$da['tscore'] +=($row->TotalAcc1Count*0.1+ $row->TotalAcc2Count*.025+ $row->TotalAcc3Count*0.65)+($row->TotalBrake1Count*0.1+ $row->TotalBrake2Count*.025+ $row->TotalBrake3Count*0.65)+($row->TotalTurn1Count*0.1+ $row->TotalTurn2Count*.025+ $row->TotalTurn3Count*0.65)+($row->TotalSpeed1Count*0.1+ $row->TotalSpeed2Count*.025+ $row->TotalSpeed3Count*0.65);
				$n++;
			}
			return $da;
		} 
		else {

			return -1;
		}
	}
	
	
	
	
	
	public function getUserTravelStats($userid) {
		$usr=mysql_real_escape_string($userid);
		


		$q = $this->db->query("SELECT sum(`TotalBrake1Count`*0.1+`TotalBrake2Count`*0.25+`TotalBrake3Count`*0.65)/count(*) as BrakeK,sum(`TotalAcc1Count`*0.1+`TotalAcc2Count`*0.25+`TotalAcc3Count`*0.65)/count(*) as AccK,sum(`TotalSpeed1Count`*0.1+`TotalSpeed2Count`*0.25+`TotalSpeed3Count`*0.65)/count(*) as SpeedK,sum(`TotalTurn1Count`*0.1+`TotalTurn2Count`*0.25+`TotalTurn3Count`*0.65)/count(*) as TurnK  FROM `NTIUserDrivingTrack` ");
			foreach($q->result() as $row) {
				$BrakeK = $row->BrakeK;
			    $AccK = $row->AccK;
				$SpeedK = $row->SpeedK;
				$TurnK = $row->TurnK;
			}

		$q = $this->db->query("SELECT * FROM NTIUserDrivingTrack where UID=$userid");
		$n=0;
				$da['total_time']=0;
			    $da['total_trips']=0;
				$da['total_acc1']=0;
				$da['total_acc2']=0;
				$da['total_acc3']=0;
				$da['total_brake1']=0;
				$da['total_brake2']=0;
				$da['total_brake3']=0;
				$da['total_prev1']=0;
				$da['total_prev2']=0;
				$da['total_prev3']=0;
				$da['total_turn1']=0;
				$da['total_turn2']=0;
				$da['total_turn3']=0;
				$da['total_accs']=0;
				$da['total_brakes']=0;
				$da['total_excesses']=0;
				$da['total_turns']=0;
				$da['total_dist']=0;
				$da['total_acc_score']=0;
				$da['total_brk_score']=0;
				$da['total_crn_score']=0;
				$da['total_spd_score']=0;
				$da['total_all_score']=0;
				$da['is_set']=1;


		if ($q->num_rows() > 0) {
			foreach($q->result() as $row) {
				
				$da['total_time'] += $row->TimeEnd-$row->TimeStart;
			    $da['total_trips']++;
				$da['total_acc1'] += $row->TotalAcc1Count;
				$da['total_acc2'] += $row->TotalAcc2Count;
				$da['total_acc3'] += $row->TotalAcc3Count;
				$da['total_brake1'] += $row->TotalBrake1Count;
				$da['total_brake2'] += $row->TotalBrake2Count;
				$da['total_brake3'] += $row->TotalBrake3Count;
				$da['total_prev1'] += $row->TotalSpeed1Count;
				$da['total_prev2'] += $row->TotalSpeed2Count;
				$da['total_prev3'] += $row->TotalSpeed3Count;
				$da['total_turn1'] += $row->TotalTurn1Count;
				$da['total_turn2'] += $row->TotalTurn2Count;
				$da['total_turn3'] += $row->TotalTurn3Count;
				$da['total_accs'] += $row->TotalAcc1Count+$row->TotalAcc2Count+$row->TotalAcc3Count;
				$da['total_brakes'] += $row->TotalBrake1Count+$row->TotalBrake2Count+$row->TotalBrake3Count;
				$da['total_excesses'] += $row->TotalSpeed1Count+$row->TotalSpeed2Count+$row->TotalSpeed3Count;
				$da['total_turns'] += $row->TotalTurn1Count+$row->TotalTurn2Count+$row->TotalTurn3Count;
				$da['total_dist']=0;
				$da['total_acc_score'] +=  100*($row->TotalAcc1Count*0.1+ $row->TotalAcc2Count*.025+ $row->TotalAcc3Count*0.65)/$AccK;
				$da['total_brk_score'] += 100*($row->TotalBrake1Count*0.1+ $row->TotalBrake2Count*.025+ $row->TotalBrake3Count*0.65)/$BrakeK;
				$da['total_crn_score'] += 100*($row->TotalTurn1Count*0.1+ $row->TotalTurn2Count*.025+ $row->TotalTurn3Count*0.65)/$TurnK;
				$da['total_spd_score'] +=100*($row->TotalSpeed1Count*0.1+ $row->TotalSpeed2Count*.025+ $row->TotalSpeed3Count*0.65)/$AccK;
				$da['total_all_score'] +=($row->TotalAcc1Count*0.1+ $row->TotalAcc2Count*.025+ $row->TotalAcc3Count*0.65)+($row->TotalBrake1Count*0.1+ $row->TotalBrake2Count*.025+ $row->TotalBrake3Count*0.65)+($row->TotalTurn1Count*0.1+ $row->TotalTurn2Count*.025+ $row->TotalTurn3Count*0.65)+($row->TotalSpeed1Count*0.1+ $row->TotalSpeed2Count*.025+ $row->TotalSpeed3Count*0.65);
				$da['is_set']=1;
			}
			return $da;
		} 
		else {
$da['is_set']=-1;
			return $da;
		}
	}
	






	public function getTotalStatsByTime($userid,$time1,$time2) {
		$usr=mysql_real_escape_string($userid);
		
			/*
			$results['total_time'] 			= $total_time;
			$results['total_trips']			= $m;
			$results['total_turn1']			= $total_turn1;
			$results['total_turn2']			= $total_turn2;
			$results['total_turn3']			= $total_turn3;
			$results['total_acc1']			= $total_acc1;
			$results['total_acc2']			= $total_acc2;
			$results['total_acc3']			= $total_acc3;
			$results['total_brake1']		= $total_brake1;
			$results['total_brake2']		= $total_brake2;
			$results['total_brake3']		= $total_brake3;
			$results['total_prev1']			= $total_speed1;
			$results['total_prev2']			= $total_speed2;
			$results['total_prev3']			= $total_speed3;
			$results['total_turns'] 		= $total_turn1 + $total_turn2 + $total_turn3;
			$results['total_accs'] 			= $total_acc1 + $total_acc2 + $total_acc3; 
			$results['total_brakes'] 		= $total_brake1 + $total_brake2 + $total_brake3;
			$results['total_excesses'] 		= $total_speed1 + $total_speed2 + $total_speed3; 
			$results['total_dist']			= $total_dist;
			$results['total_acc_score'] 	= $total_acc_score;
			$results['total_brk_score'] 	= $total_brk_score;
			$results['total_crn_score'] 	= $total_crn_score;
			$results['total_spd_score'] 	= $total_spd_score;
			$results['total_all_score'] 	= $total_all_score;
			* $results['is_set'] = -1;
			* */
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		$q = $this->db->query("SELECT sum(`TotalBrake1Count`*0.1+`TotalBrake2Count`*0.25+`TotalBrake3Count`*0.65)/count(*) as BrakeK,sum(`TotalAcc1Count`*0.1+`TotalAcc2Count`*0.25+`TotalAcc3Count`*0.65)/count(*) as AccK,sum(`TotalSpeed1Count`*0.1+`TotalSpeed2Count`*0.25+`TotalSpeed3Count`*0.65)/count(*) as SpeedK,sum(`TotalTurn1Count`*0.1+`TotalTurn2Count`*0.25+`TotalTurn3Count`*0.65)/count(*) as TurnK  FROM `NTIUserDrivingTrack` ");
			foreach($q->result() as $row) {
				$BrakeK = $row->BrakeK;
			    $AccK = $row->AccK;
				$SpeedK = $row->SpeedK;
				$TurnK = $row->TurnK;
			}
			
			
					$time1 = strtotime($time1);
		$time2 = strtotime($time2);	

		$q = $this->db->query("SELECT * FROM NTIUserDrivingTrack where UID=$userid and ((TimeStart>=$time1 and TimeStart<=$time2) or (TimeStart<$time1 and TimeEnd>=$time1))");
		$n=0;
		$da['tscore']=0;
		//Если не было поездок за текущий период или не передано ни одной точки.
		if ($q->num_rows() > 0) {
			foreach($q->result() as $row) {
				$da[$n]['TimeStart'] = $row->TimeStart;
				$da[$n]['TimeEnd'] = $row->TimeEnd;
				$da[$n]['TotalAcc1Count'] = $row->TotalAcc1Count;
				$da[$n]['TotalAcc2Count'] = $row->TotalAcc2Count;
				$da[$n]['TotalAcc3Count'] = $row->TotalAcc3Count;
				$da[$n]['TotalBrake1Count'] = $row->TotalBrake1Count;
				$da[$n]['TotalBrake2Count'] = $row->TotalBrake2Count;
				$da[$n]['TotalBrake3Count'] = $row->TotalBrake3Count;
				$da[$n]['TotalSpeed1Count'] = $row->TotalSpeed1Count;
				$da[$n]['TotalSpeed2Count'] = $row->TotalSpeed2Count;
				$da[$n]['TotalSpeed3Count'] = $row->TotalSpeed3Count;
				$da[$n]['TotalTurn1Count'] = $row->TotalTurn1Count;
				$da[$n]['TotalTurn2Count'] = $row->TotalTurn2Count;
				$da[$n]['TotalTurn3Count'] = $row->TotalTurn3Count;
				$da[$n]['total_dist']=0;
				$da[$n]['total_acc_score'] =  100*($row->TotalAcc1Count*0.1+ $row->TotalAcc2Count*.025+ $row->TotalAcc3Count*0.65)/$AccK;
				$da[$n]['total_brk_score'] = 100*($row->TotalBrake1Count*0.1+ $row->TotalBrake2Count*.025+ $row->TotalBrake3Count*0.65)/$BrakeK;
				$da[$n]['total_crn_score'] = 100*($row->TotalTurn1Count*0.1+ $row->TotalTurn2Count*.025+ $row->TotalTurn3Count*0.65)/$TurnK;
				$da[$n]['total_spd_score'] =100*($row->TotalSpeed1Count*0.1+ $row->TotalSpeed2Count*.025+ $row->TotalSpeed3Count*0.65)/$AccK;
				$da[$n]['total_all_score'] =($row->TotalAcc1Count*0.1+ $row->TotalAcc2Count*.025+ $row->TotalAcc3Count*0.65)+($row->TotalBrake1Count*0.1+ $row->TotalBrake2Count*.025+ $row->TotalBrake3Count*0.65)+($row->TotalTurn1Count*0.1+ $row->TotalTurn2Count*.025+ $row->TotalTurn3Count*0.65)+($row->TotalSpeed1Count*0.1+ $row->TotalSpeed2Count*.025+ $row->TotalSpeed3Count*0.65);
				$da['tscore'] +=($row->TotalAcc1Count*0.1+ $row->TotalAcc2Count*.025+ $row->TotalAcc3Count*0.65)+($row->TotalBrake1Count*0.1+ $row->TotalBrake2Count*.025+ $row->TotalBrake3Count*0.65)+($row->TotalTurn1Count*0.1+ $row->TotalTurn2Count*.025+ $row->TotalTurn3Count*0.65)+($row->TotalSpeed1Count*0.1+ $row->TotalSpeed2Count*.025+ $row->TotalSpeed3Count*0.65);

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
	
	public function vck($i) {
		$q = $this->db->query("SELECT NTIUsers.Id, NTIUsers.Login, NTIUsers.FName, NTIUsers.SName, NTIRelations.ExpertId FROM NTIUsers INNER JOIN NTIRelations ON NTIUsers.Id=NTIRelations.UserId WHERE NTIRelations.ExpertId=$i AND NTIUsers.Deleted=0 AND NTIUsers.Rights<2 ORDER BY Login");
		$n = 0;
		if ($q->num_rows() > 0) {
			foreach($q->result() as $row) {
				$da[$n]['Id'] = $row->Id;
				$da[$n]['Login'] = $row->Login;
				$da[$n]['FName'] = $row->FName;
				$da[$n]['SName'] = $row->SName;
				$n++;
			}
			return $da;
		} 
		else {
			return -1;
		}
	}
	
	function unbind($i, $c) {
		$q = $this->db->query("DELETE FROM NTIRelations WHERE ExpertId=$c AND UserId=$i");
		return 1;
	}
	
	public function getById() {
		$q = $this->db->query("SELECT Id, Login, FName, SName FROM NTIUsers WHERE Rights<2 AND Deleted=0 ORDER BY Id");
		$n = 0;
		if ($q->num_rows() > 0) {
			foreach($q->result() as $row) {
				$da[$n]['Id'] = $row->Id;
				$da[$n]['Login'] = $row->Login;
				$da[$n]['FName'] = $row->FName;
				$da[$n]['SName'] = $row->SName;
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



