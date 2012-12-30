<?

$dblocation = "localhost";  
$dbname = "NTI";  
$dbuser = "goodroads";  
$dbpasswd = "123OLAcomrade"; 
 $dbcnx= mysql_connect($dblocation, $dbuser, $dbpasswd);
if (!$dbcnx)    {      return 0;  }  
if (!mysql_select_db($dbname,$dbcnx) )    {      return 0;    }
$userCount=0;
	$sql_select_track=mysql_query("Select * from NTIUserDrivingTrack order by TimeStart");
	$userTrackCount=0;
	while ($row = mysql_fetch_array($sql_select_track)) 
	{
		$tracking[$userTrackCount]['Id']=$row['Id'];
		$tracking[$userTrackCount]['TimeStart']=$row['TimeStart'];
		$tracking[$userTrackCount]['TimeEnd']=$row['TimeEnd'];
		$tracking[$userTrackCount]['TypeAcc1Count'] =$row['TotalAcc1Count'];
		$tracking[$userTrackCount]['TypeAcc2Count'] =$row['TotalAcc2Count'];     
		$tracking[$userTrackCount]['TypeAcc3Count'] =$row['TotalAcc3Count']; 
		$tracking[$userTrackCount]['TypeTurn1Count']=$row['TotalTurn1Count'];  
		$tracking[$userTrackCount]['TypeTurn2Count']=$row['TotalTurn2Count']; 
		$tracking[$userTrackCount]['TypeTurn3Count'] =$row['TotalTurn3Count'];
		$tracking[$userTrackCount]['TypeSpeed1Count'] =$row['TotalSpeed1Count'];
		$tracking[$userTrackCount]['TypeSpeed2Count'] =$row['TotalSpeed2Count'];
		$tracking[$userTrackCount]['TypeSpeed3Count'] =$row['TotalSpeed3Count'];
		$tracking[$userTrackCount]['TypeBrake1Count'] =$row['TotalBrake1Count']; 
		$tracking[$userTrackCount]['TypeBrake2Count'] =$row['TotalBrake2Count'];
		$tracking[$userTrackCount]['TypeBrake3Count'] =$row['TotalBrake3Count'];
		$userTrackCount++;
	}
	for($j=0;$j<$userTrackCount;$j++)
	{
		echo $j;
		$times=$tracking[$j]['TimeStart'];
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
					FROM NTIUserDrivingTrack where TimeStart<=$times group by UID) as a");
			while ($row = mysql_fetch_array($result)) 
			{
				$Qa=$row['Fau'];
				$Qs=$row['Fsu'];
				$Qt=$row['Ftu'];
				$Qb=$row['Fbu'];
			}
			
			
			$DTime=$tracking[$j]['TimeEnd']-$tracking[$j]['TimeStart'];
			if($DTime==0)$DTime=1;
			$FAcc1=$tracking[$j]['TypeAcc1Count']/$DTime;
			$FAcc2=$tracking[$j]['TypeAcc2Count']/$DTime;
			$FAcc3=$tracking[$j]['TypeAcc3Count']/$DTime;
			$Fturn1=$tracking[$j]['TypeTurn1Count']/$DTime;
			$Fturn2=$tracking[$j]['TypeTurn2Count']/$DTime;
			$Fturn3=$tracking[$j]['TypeTurn3Count']/$DTime;
			$FSpeed1=$tracking[$j]['TypeSpeed1Count']/$DTime;
			$FSpeed2=$tracking[$j]['TypeSpeed2Count']/$DTime;
			$FSpeed3=$tracking[$j]['TypeSpeed3Count']/$DTime;
			$FBrake1=$tracking[$j]['TypeBrake1Count']/$DTime;
			$FBrake2=$tracking[$j]['TypeBrake2Count']/$DTime;
			$FBrake3=$tracking[$j]['TypeBrake3Count']/$DTime;
			$KvnA=($FAcc1*0.1+ $FAcc2*0.25 +$FAcc3*0.65);
			$KvnS=($FSpeed1*0.1+ $FSpeed2*0.25 +$FSpeed3*0.65);
			$KvnT=($Fturn1*0.1+ $Fturn2*0.25 +$Fturn3*0.65);
			$KvnB=($FBrake1*0.1+ $FBrake2*0.25 +$FBrake3*0.65);
			$Kua=1/(1+$KvnA/$Qa);
			$Kub=1/(1+$KvnB/$Qb);
			$Kus=1/(1+$KvnS/$Qs);
			$Kut=1/(1+$KvnT/$Qt);					
			$score=0.10*$Kua+0.35*$Kub+0.30*$Kus+0.25*$Kut;
			$score_speed = $Kus;
			$score_turn = $Kut;
			$score_brake =$Kub ;
			$score_acc = $Kua ;
			$rId=$tracking[$j]['Id'];
			$sql_insert_str=mysql_query("Update NTIUserDrivingTrack set 
				SpeedScore=$score_speed,
				TurnScore=$score_turn,
				BrakeScore=$score_brake,
				AccScore=$score_acc,
				TotalScore=$score
				where Id=$rId");
		}

?>


