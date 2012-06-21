<?error_reporting(0);
class UserTrack {
	
private $id;
private $timestart;
private $timeend;

 function UserTrack($Id,$TimeStart,$TimeEnd) {
	$this->id=$Id;
	$this->timestart=$TimeStart;
	$this->timeend=$TimeEnd;	
 }
 //Incapsulation
 public function setId($Id){$this->id=$Id;}
 public function setTimeStart($TimeStart) {$this->timestart=$TimeStart;}
 public function setTimeEnd($TimeEnd){	$this->timeend=$TimeEnd; }
 public function getId(){return($this->id);}
 public function getTimeStart() {return($this->timestart);}
 public function getTimeEnd(){	return($this->timeend); }


}
class EntryRide {
	
private $lat;
private $lng;
private $compass;
private $direction;
private $timestamp;
private $distance;
private $speed;
private $accx;
private $accy;

 function EntryRide() {
	//Constructor
 }
 //Incapsulation
 public function setLat($lat){$this->lat=$lat;}
 public function setLng($lng) {$this->lng=$lng;}
 public function setCompass($compass){	$this->compass=$compass; }
 public function setDirection($direction){	$this->direction=$direction; }
 public function setTimestamp($timestamp) {	$this->timestamp=$timestamp; }
 public function setDistance($distance) {	$this->distance=$distance; }
 public function setSpeed($speed) {	$this->speed=$speed; }
 public function setAccx($accx) {	$this->accx=$accx; }
 public function setAccy($accy) {	$this->accy=$accy; }
 
 public function getLat(){return($this->lat);}
 public function getLng() {return($this->lng);}
 public function getCompass(){	return($this->compass); }
 public function getDirection(){	return($this->direction); }
 public function getTimestamp() {	return($this->timestamp); }
 public function getDistance() {	return($this->distance); }
 public function getSpeed() {	return($this->speed); }
 public function getAccx() {	return($this->accx); }
 public function getAccy() {	return($this->accy); }

}
class UserEntry extends EntryRide {

private $sevAcc;
private $TypeAcc;
private $sevTurn;
private $TurnType;
private $TypeSpeed;
private $sevSpeed;
private $Turn;
private $Accel;
 function UserEntry() {
	//Constructor
 }
 //Incapsulation
 public function setsevAcc($sevAcc){$this->sevAcc=$sevAcc;}
 public function setTypeAcc($TypeAcc){$this->TypeAcc=$TypeAcc;}
 public function setsevTurn($sevTurn){$this->sevTurn=$sevTurn;}
 public function setTurnType($TurnType){$this->TurnType=$TurnType;}
 public function setTypeSpeed($TypeSpeed){$this->TypeSpeed=$TypeSpeed;}
 public function setsevSpeed($sevSpeed){$this->sevSpeed=$sevSpeed;}
 public function setTurn($Turn){$this->Turn=$Turn;}
 public function setAccel($Accel){$this->Accel=$Accel;}
  
 public function getsevAcc(){	return($this->sevAcc);}
 public function getTypeAcc(){	return($this->TypeAcc);}
 public function getsevTurn(){	return($this->sevTurn);}
 public function getTurnType(){	return($this->TurnType);}
 public function getTypeSpeed(){	return($this->TypeSpeed);}
 public function getsevSpeed(){	return($this->sevSpeed);}
 public function getTurn(){return($this->Turn);}
 public function getAccel(){return($this->Accel);}

}
$dblocation = "localhost";  
$dbname = "NTI";  
$dbuser = "steph";  
$dbpasswd = "trinitro"; 
 $dbcnx= mysql_connect($dblocation, $dbuser, $dbpasswd);
if (!$dbcnx)    {      return 0;  }  
if (!mysql_select_db($dbname,$dbcnx) )    {      return 0;    }
$userCount=0;
$sql_get_all_users=mysql_query("Select Id from NTIUsers");
while ($row = mysql_fetch_array($sql_get_all_users)) 
{
	$UID[$userCount]=$row['Id'];
	$userCount++;
}
//Получили всех пользователей
//Теперь перебираем все точки
for($z=0;$z<$userCount;$z++)
{
	$ID=$UID[$z];
	$sql_select_track=mysql_query("Select Id,TimeStart,TimeEnd from NTIUserDrivingTrack where UID=$ID order by TimeStart");
	$userTrackCount=0;
	while ($row = mysql_fetch_array($sql_select_track)) 
	{
		$tracking[$userTrackCount]=new UserTrack($row['Id'],$row['TimeStart'],$row['TimeEnd']);
		$userTrackCount++;
	}
	//Получили все поездки пользователя,
	$IdCount=0;
	//Объединяем по 2
	for($j=0;$j<$userTrackCount-1;$j++)
	{
		
		if((abs($tracking[$j]->getTimeStart()-$tracking[$j+1]->getTimeStart())<300) || (abs($tracking[$j]->getTimeStart()-$tracking[$j+1]->getTimeEnd)<300) || (abs($tracking[$j]->getTimeEnd()-$tracking[$j+1]->getTimeStart())<300))
		{
			$IdArray[$IdCount][0]=$tracking[$j]->getId();
			$IdArray[$IdCount][1]=$tracking[$j+1]->getId();
			$j++;
			$IdCount++;
		}
	}
	$InpID = $IdArray;
	if(count($InpID)>0)
	{
		for($j=0;$j<count($InpID);$j++)
		{
			$rId=$InpID[$j][0];
			$lId=$InpID[$j][1];
			$TypeAcc1Count =0;
			$TypeAcc2Count =0;
			$TypeAcc3Count =0; 
			$TypeTurn1Count=0;  
			$TypeTurn2Count=0;
			$TypeTurn3Count =0;
			$TypeSpeed1Count=0;
			$TypeSpeed2Count =0;
			$TypeSpeed3Count =0;
			$TypeBrake1Count =0;
			$TypeBrake2Count =0;
			$TypeBrake3Count =0;
			$TotalDistance=0;
			$TimeStart=2147483647;
			$TimeEnd=0;
			//Для начала получаем все данные 
			$sql_select_track=mysql_query("Select * from NTIUserDrivingTrack where Id=$rId or Id=$lId");
			while ($row = mysql_fetch_array($sql_select_track)) 
			{
				$TypeAcc1Count +=$row['TotalAcc1Count'];
				$TypeAcc2Count +=$row['TotalAcc2Count'];     
				$TypeAcc3Count +=$row['TotalAcc3Count']; 
				$TypeTurn1Count+=$row['TotalTurn1Count'];  
				$TypeTurn2Count+=$row['TotalTurn2Count']; 
				$TypeTurn3Count +=$row['TotalTurn3Count'];
				$TypeSpeed1Count +=$row['TotalSpeed1Count'];
				$TypeSpeed2Count +=$row['TotalSpeed2Count'];
				$TypeSpeed3Count +=$row['TotalSpeed3Count'];
				$TypeBrake1Count +=$row['TotalBrake1Count']; 
				$TypeBrake2Count +=$row['TotalBrake2Count'];
				$TypeBrake3Count +=$row['TotalBrake3Count'];
				$TotalDistance+=$row['TotalDistance'];
				if($TimeStart>$row['TimeStart'])$TimeStart=$row['TimeStart'];
				if($TimeEnd<$row['TimeEnd'])$TimeEnd=$row['TimeEnd'];
			}
			$result=mysql_query("Select 
					sum(Fa)/count(UID) as Fau,
					sum(Fb)/count(UID) as Fbu,
					sum(Fs)/count(UID)  as Fsu,
					sum(Ft)/count(UID)  as Ftu from
					(
					SELECT UID,
					sum((TotalAcc1Count*0.1+TotalAcc2Count*0.25+TotalAcc3Count*0.65)/(TimeEnd-TimeStart))/count(*) as Fa,
					sum((TotalBrake1Count*0.1+TotalBrake2Count*0.25+TotalBrake3Count*0.65)/(TimeEnd-TimeStart))/count(*) as Fb,
					sum((TotalSpeed1Count*0.1+TotalSpeed2Count*0.25+TotalSpeed3Count*0.65)/(TimeEnd-TimeStart))/count(*) as Fs,
					sum((TotalTurn1Count*0.1+TotalTurn2Count*0.25+TotalTurn3Count*0.65)/(TimeEnd-TimeStart))/count(*) as Ft
					FROM NTIUserDrivingTrack group by UID) as a");
			while ($row = mysql_fetch_array($result)) 
			{
				$Qa=$row['Fau'];
				$Qs=$row['Fsu'];
				$Qt=$row['Ftu'];
				$Qb=$row['Fbu'];
			}
			
			
			$DTime=$TimeEnd-$TimeStart;
			if($DTime==0)$DTime=1;
			$FAcc1=$TypeAcc1Count/$DTime;
			$FAcc2=$TypeAcc2Count/$DTime;
			$FAcc3=$TypeAcc3Count/$DTime;
			$Fturn1=$TypeTurn1Count/$DTime;
			$Fturn2=$TypeTurn2Count/$DTime;
			$Fturn3=$TypeTurn3Count/$DTime;
			$FSpeed1=$TypeSpeed1Count/$DTime;
			$FSpeed2=$TypeSpeed2Count/$DTime;
			$FSpeed3=$TypeSpeed3Count/$DTime;
			$FBrake1=$TypeBrake1Count/$DTime;
			$FBrake2=$TypeBrake2Count/$DTime;
			$FBrake3=$TypeBrake3Count/$DTime;
			$KvnA=($FAcc1*0.1+ $FAcc2*0.25 +$FAcc3*0.65);
			$KvnS=($FSpeed1*0.1+ $FSpeed2*0.25 +$FSpeed3*0.65);
			$KvnT=($Fturn1*0.1+ $Fturn2*0.25 +$Fturn3*0.65);
			$KvnB=($FBrake1*0.1+ $FBrake2*0.25 +$FBrake3*0.65);
			$Kua=1/sqrt(1+$KvnA/$Qa);
			$Kub=1/sqrt(1+$KvnB/$Qb);
			$Kus=1/sqrt(1+$KvnS/$Qs);
			$Kut=1/sqrt(1+$KvnT/$Qt);					
			$score=0.15*$Kua+0.35*$Kub+0.35*$Kus+0.25*$Kut;
			$score_speed = $Kus;
			$score_turn = $Kut;
			$score_brake =$Kub ;
			$score_acc = $Kua ;
			
			$sql_insert_str=mysql_query("Update NTIUserDrivingTrack set
			TotalAcc1Count=$TypeAcc1Count,
			TotalAcc2Count=$TypeAcc2Count,
			TotalAcc3Count=$TypeAcc3Count,
			TotalBrake1Count=$TypeBrake1Count,
			TotalBrake2Count=$TypeBrake2Count,
			TotalBrake3Count=$TypeBrake3Count,
			TotalSpeed1Count=$TypeSpeed1Count,
			TotalSpeed2Count=$TypeSpeed2Count,
			TotalSpeed3Count=$TypeSpeed3Count,
			TotalTurn1Count=$TypeTurn1Count,
			TotalTurn2Count=$TypeTurn2Count,
			TotalTurn3Count=$TypeTurn3Count,
			TimeStart=$TimeStart,
			TimeEnd=$TimeEnd,
			TotalDistance=$TotalDistance,
			SpeedScore=$score_speed,
			TurnScore=$score_turn,
			BrakeScore=$score_brake,
			AccScore=$score_acc,
			TotalScore=$score,
			SpeedK=$KvnS,
			AccK=$KvnA,
			BrakeK=$KvnB,
			TurnK=$KvnT where Id=$rId");
			$sql_select_track=mysql_query("Delete from NTIUserDrivingTrack where Id=$lId");
			$sql_select_track=mysql_query("Update NTIUserDrivingEntry set DrivingId=$rId where DrivingId=$lId");
		}
		
		
		
	}
	
}


?>

