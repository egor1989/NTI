<?php

class lays_model extends CI_Model {


		public function getMapData($id) {
			
		$id=mysql_real_escape_string($id);
		$q = $this->db->query("SELECT * FROM NTIUserDrivingTrack where Id='$id' order by Id DESC");
		//Если не было поездок за текущий период или не передано ни одной точки.
		if ($q->num_rows() > 0) {
			foreach($q->result() as $row) {
				$da['TimeStart'] = $row->TimeStart;
				$da['TimeEnd'] = $row->TimeEnd;
				$da['TotalAcc1Count'] = $row->TotalAcc1Count;
				$da['TotalAcc2Count'] = $row->TotalAcc2Count;
				$da['TotalAcc3Count'] = $row->TotalAcc3Count;
				$da['TotalBrake1Count'] = $row->TotalBrake1Count;
				$da['TotalBrake2Count'] = $row->TotalBrake2Count;
				$da['TotalBrake3Count'] = $row->TotalBrake3Count;
				$da['TotalSpeed1Count'] = $row->TotalSpeed1Count;
				$da['TotalSpeed2Count'] = $row->TotalSpeed2Count;
				$da['TotalSpeed3Count'] = $row->TotalSpeed3Count;
				$da['TotalTurn1Count'] = $row->TotalTurn1Count;
				$da['TotalTurn2Count'] = $row->TotalTurn2Count;
				$da['TotalTurn3Count'] = $row->TotalTurn3Count;
				$da['total_dist']=$row->TotalDistance;
				$da['total_acc_score'] =  $row->AccScore;
				$da['total_brk_score'] = $row->BrakeScore;
				$da['total_crn_score'] =$row->TurnScore;
				$da['total_spd_score'] =$row->SpeedScore;
				$da['total_all_score'] =$row->TotalScore;
				$da['tscore'] =$row->TotalScore;
				$da['tdst']=$row->TotalDistance;
							$da['SpeedK'] =  $row->SpeedK;
				$da['TurnK'] = $row->TurnK;
				$da['AccK'] =$row->AccK;
				$da['BrakeK'] =$row->BrakeK;
			}
			return $da;
		} 
		else {
			return -1;
		}
	}
	    function getByRide($uid)
	{			
		$k=0;
		$query = $this->db->query("SELECT * FROM `NTIUserDrivingEntry` where DrivingID='$uid'");
		
			if($query->num_rows()>0){
			
				foreach ($query->result() as $row)
                    {
	                         $ret_data[$k]['lat']=$row->lat;
	                         $ret_data[$k]['lng']=$row->lng;
	                         $ret_data[$k]['compass']=$row->compass;
	                         $ret_data[$k]['speed']=$row->speed;
	                         $ret_data[$k]['distance']=$row->distance;
	                         $ret_data[$k]['utimestamp']=$row->utimestamp;
	                         $ret_data[$k]['sevAcc']=$row->sevAcc;
	                         $ret_data[$k]['sevTurn']=$row->sevTurn;
	                         $ret_data[$k]['sevSpeed']=$row->sevSpeed;
	                         
	                         
	                         
	                         
	                         
	                         
	                         $ret_data[$k]['Info']="";
	                         if($row->sevAcc!=0)$ret_data[$k]['Info'].=$row->TypeAcc."<br/>";
	                         if($row->sevTurn!=0)$ret_data[$k]['Info'].=$row->TurnType."<br/>";
	                         if($row->sevSpeed!=0)$ret_data[$k]['Info'].=$row->TypeSpeed."<br/>";
	                         if($row->sevAcc==0 && $row->sevTurn==0 &&  $row->sevSpeed==0)
	                         	 	 	$ret_data[$k]['Info']="normal point";




					if($row->sevAcc!=0)
					{
						if($row->sevTurn==0)
						{
						if($row->sevAcc<0)
						{
							$ret_data[$k]['type']=2;
							$ret_data[$k]['weight']=$row->sevAcc*(-1);
						}
						else
						{
							$ret_data[$k]['type']=1;
							$ret_data[$k]['weight']=$row->sevAcc;
						}
					}
					else
					{
						if($row->sevAcc<0)
						{
							if($row->sevAcc*(-1)>=$row->sevTurn)
							{
									$ret_data[$k]['type']=2;
									$ret_data[$k]['weight']=$row->sevAcc*(-1);
							}
							else
							{
									$ret_data[$k]['type']=3;
									$ret_data[$k]['weight']=$row->sevAcc;
							}
						}
						else
						{
							if($row->sevAcc>=$row->sevTurn)
							{
									$ret_data[$k]['type']=1;
									$ret_data[$k]['weight']=$row->sevAcc;
							}
							else
							{
									$ret_data[$k]['type']=3;
									$ret_data[$k]['weight']=$row->sevTurn;
							}	
						}
					}
				}
				else if($row->sevAcc==0 && $row->sevSpeed==0)
				{
					
					if($row->sevTurn>0)
					{
						$ret_data[$k]['type']=3;
						$ret_data[$k]['weight']=$row->sevTurn;
					}
					else
					{
						$ret_data[$k]['type']=0;
						$ret_data[$k]['weight']=0;
					}
				}
				else
				{
					if($row->sevSpeed>0)
					{
						$ret_data[$k]['type']=4;
						$ret_data[$k]['weight']=$row->sevTurn;
					}
					else
					{
						$ret_data[$k]['type']=0;
						$ret_data[$k]['weight']=0;
					}
				}



							$k++;
					}
					
					return $ret_data;
			}
			else{
				return false;
			}
		
	}


		public function getTotalStats($userid) {
			
		$usr=mysql_real_escape_string($userid);

		
		$q = $this->db->query("SELECT * FROM NTIUserDrivingTrack where UID='$userid' order by Id DESC");
		$n=0;
		$da['tscore']=0;
		$da['tdst']=0;
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
				$da[$n]['total_dist']=$row->TotalDistance;
				$da[$n]['total_acc_score'] =  $row->AccScore;
				$da[$n]['total_brk_score'] = $row->BrakeScore;
				$da[$n]['total_crn_score'] =$row->TurnScore;
				$da[$n]['total_spd_score'] =$row->SpeedScore;
				$da[$n]['total_all_score'] =$row->TotalScore;
				$da['tscore'] +=$row->TotalScore;
				$da['tdst']=+$row->TotalDistance;
							$da[$n]['SpeedK'] =  $row->SpeedK;
				$da[$n]['TurnK'] = $row->TurnK;
				$da[$n]['AccK'] =$row->AccK;
				$da[$n]['BrakeK'] =$row->BrakeK;
				$n++;
			}
			$da['total_trips']=$n;
			return $da;
		} 
		else {

			return -1;
		}
	}
	

		public function LoadRawData($dataId) {
		$dataId=mysql_real_escape_string($dataId);
		$q = $this->db->query("SELECT * FROM `NTIUserDrivingEntry` WHERE `DrivingID`='$dataId' and (`TypeAcc` NOT LIKE 'normal point' or `TurnType` NOT LIKE 'normal point' or `TypeSpeed`  NOT LIKE  'normal point') order by `utimestamp` ");
		if ($q->num_rows() > 0) {
			return $q->result_array();
		} 
		else {

			return -1;
		}
	}




	
	
	
	
	public function getUserTravelStats($userid) {
		$usr=mysql_real_escape_string($userid);
	
		$q = $this->db->query("SELECT * FROM NTIUserDrivingTrack where UID='$userid' order by Id DESC");
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

$n=0;
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
				$da['total_dist']+=$row->TotalDistance;
				$da['total_acc_score'] +=  $row->AccScore;
				$da['total_brk_score'] += $row->BrakeScore;
				$da['total_crn_score'] +=$row->TurnScore;
				$da['total_spd_score'] +=$row->SpeedScore;
				$da['total_all_score'] +=$row->TotalScore;;
							$da['SpeedK'] =  $row->SpeedK;
				$da['TurnK'] = $row->TurnK;
				$da['AccK'] =$row->AccK;
				$da['BrakeK'] =$row->BrakeK;
				$da['is_set']=1;
				$n++;
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
	
		$time1 = strtotime($time1);
		$time2 = strtotime($time2);	

		$q = $this->db->query("SELECT * FROM NTIUserDrivingTrack where UID='$userid' and ((TimeStart>='$time1' and TimeStart<='$time2') or (TimeStart<'$time1' and TimeEnd>='$time1'))");
		$n=0;
		$da['tscore']=0;
		$da['tdst']=0;
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
				$da[$n]['total_dist']=$row->TotalDistance;
				$da[$n]['total_acc_score'] =  $row->AccScore;
				$da[$n]['total_brk_score'] = $row->BrakeScore;
				$da[$n]['total_crn_score'] =$row->TurnScore;
				$da[$n]['total_spd_score'] =$row->SpeedScore;
				$da[$n]['total_all_score'] =$row->TotalScore;;
							$da[$n]['SpeedK'] =  $row->SpeedK;
				$da[$n]['TurnK'] = $row->TurnK;
				$da[$n]['AccK'] =$row->AccK;
				$da[$n]['BrakeK'] =$row->BrakeK;
				$da['tscore'] +=$row->TotalScore;;
				$da['tdst']=+$row->TotalDistance;
				$n++;
			}
			return $da;
		} 
		else {

			return -1;
		}
	}


	public function cksearch($ckid) {
		$ckid=mysql_real_escape_string($ckid);
		$q = $this->db->query("SELECT * FROM (select tablet_user.* ,coalesce(`Status`,0) as Stat from (select allusers.* ,coalesce(`ExpertID`,0) as Bnd from (SELECT Id,Login,FName,SName FROM `NTIUsers` WHERE `Rights`<2 and Deleted=0) as allusers Left OUTER JOIN  (select * from NTIRelations where ExpertID='$ckid') as expert on allusers.Id=expert.UserID) as tablet_user Left OUTER JOIN (Select `Status`,`UserId` from NTIRequests where `ExpertId`='$ckid' and Status<=2) as request on tablet_user.Id=request.`UserId`) as prst ORDER BY Login");
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
		$i=mysql_real_escape_string($i);
		$q = $this->db->query("SELECT NTIUsers.Id, NTIUsers.Login, NTIUsers.FName, NTIUsers.SName, NTIRelations.ExpertId FROM NTIUsers INNER JOIN NTIRelations ON NTIUsers.Id=NTIRelations.UserId WHERE NTIRelations.ExpertId='$i' AND NTIUsers.Deleted=0 AND NTIUsers.Rights<2 ORDER BY Login");
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
		$i=mysql_real_escape_string($i);
		$c=mysql_real_escape_string($c);
		
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



