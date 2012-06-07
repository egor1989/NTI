<?
$dblocation = "localhost";  
$dbname = "NTI";  
$dbuser = "steph";  
$dbpasswd = "trinitro"; 
 $dbcnx= mysql_connect($dblocation, $dbuser, $dbpasswd);
if (!$dbcnx)    {      return 0;  }  
if (!mysql_select_db($dbname,$dbcnx) )    {      return 0;    }  
mysql_query('SET NAMES cp1251'); 
$result=mysql_query("SELECT sum((`TotalBrake1Count`*0.1+`TotalBrake2Count`*0.25+`TotalBrake3Count`*0.65)/`TotalDistance`)/count(*) as BrakeK,sum((`TotalAcc1Count`*0.1+`TotalAcc2Count`*0.25+`TotalAcc3Count`*0.65)/`TotalDistance`)/count(*) as AccK,sum((`TotalSpeed1Count`*0.1+`TotalSpeed2Count`*0.25+`TotalSpeed3Count`*0.65)/`TotalDistance`)/count(*) as SpeedK,sum((`TotalTurn1Count`*0.1+`TotalTurn2Count`*0.25+`TotalTurn3Count`*0.65)/`TotalDistance`)/count(*) as TurnK  FROM `NTIUserDrivingTrack` ");
while ($row = mysql_fetch_array($result)) 
{
	$BrakeK = $row['BrakeK'];
	$AccK = $row['AccK'];
	$SpeedK = $row['SpeedK'];
	$TurnK = $row['TurnK'];
}
$result=mysql_query("Insert into NTICoef(TurnK,SpeedK,AccK,BrakeK) values($TurnK,$SpeedK,$AccK,$BrakeK)");
?>

