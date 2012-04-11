<?php
$dbcnx=0;

function connec_to_db()
{
	$dblocation = "localhost";  
	$dbname = "NTI";  
	$dbuser = "steph";  
	$dbpasswd = "trinitro"; 
	 $dbcnx= mysql_connect($dblocation, $dbuser, $dbpasswd);
	if (!$dbcnx)    {      return 0;  }  
	if (!mysql_select_db($dbname,$dbcnx) )    {      return 0;    }  
	mysql_query('SET NAMES cp1251'); 
	return 1;
}
		
if(connec_to_db()==0) {
	$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	
	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	
}
	
//////////////////////////////////////////////////////////////////////// 

function gCercle ($lon1, $lat1, $lon2, $lat2)
{	
	return 2 * 6367 * sin(sqrt(pow(sin(($lat1-$lat2)/2),2)+(cos($lat1)*cos($lat2)*pow(sin(($lon1-$lon2)/2),2))));
}
/*
$query = 'SELECT Id,	File FROM NTIFile';
$result = mysql_query($query);
$c = 0;
$n = 0;
while ($row = mysql_fetch_array($result)) 
{
	$someArr[$c] = json_decode($row['File'],true);
	$k = 0;	$fileid = $row['Id'];
	while ($someArr[$c][$k]) 
	{
		//$encData[$n]['accx'] = $someArr[$c][$k]['acc']['x'];
		//$encData[$n]['accy'] = $someArr[$c][$k]['acc']['y'];
		//$encData[$n]['lat'] = $someArr[$c][$k]['gps']['latitude'];
		//$encData[$n]['lng'] = $someArr[$c][$k]['gps']['longitude'];
		//$encData[$n]['compass'] = $someArr[$c][$k]['gps']['compass'];
		//$encData[$n]['speed'] = $someArr[$c][$k]['gps']['speed'];
		//$encData[$n]['distance'] = $someArr[$c][$k]['gps']['distance'];
		//$encData[$n]['utimestamp'] = $someArr[$c][$k]['timestamp'];
		//$encData[$n]['fileid'] = $k;
		$accx = $someArr[$c][$k]['acc']['x'];
		$accy = $someArr[$c][$k]['acc']['y'];
		$lat =  $someArr[$c][$k]['gps']['latitude'];
		$lng = $someArr[$c][$k]['gps']['longitude'];
		$direction = $someArr[$c][$k]['gps']['direction'];
		$compass = $someArr[$c][$k]['gps']['compass'];
		$speed = $someArr[$c][$k]['gps']['speed'];
		$distance = $someArr[$c][$k]['gps']['distance'];
		$utimestamp = $someArr[$c][$k]['timestamp']/1000;
	
		$str = "INSERT INTO NTIEntry (UID, accx, accy, distance, lat, lng, direction, compass, speed, utimestamp, FileId) VALUES (-3, $accx, $accy, $distance, $lat, $lng, $direction, $compass, $speed, $utimestamp, $fileid)";
		$k++;
		$n++;
		mysql_query($str);
		//echo date($utimestamp) . "<br>";
	}
	$c++;
}
*/
//$i = 0;
//while ($p[$i]) {
//	echo date('YYYY-mm-dd', $p[$i]['utimestamp']) . "<br>";
//	$i++;
//}


//Получаем последнюю поездку

$query = 'SELECT * FROM NTIEntry where utimestamp!=0 order by utimestamp';
$result = mysql_query($query);
$c = 0;
$n = 0;
while ($row = mysql_fetch_array($result)) 
{
		$encData[$n]['accx'] = $row['accx'];
		$encData[$n]['accy'] = $row['accy'];
		$encData[$n]['lat'] = $row['lat'];
		$encData[$n]['lng'] = $row['lng'];
		$encData[$n]['compass'] = $row['compass'];
		$encData[$n]['speed'] = $row['speed'];
		$encData[$n]['distance'] = $row['distance'];
		$encData[$n]['utimestamp'] = $row['utimestamp'];
		$n++;
}
//Начинаем группировку
$k=0;
$m=0;
$grouped[$k][0]=$encData[0];
for($i=0;$i<count($encData);$i++)
{
	if($encData[$i]['utimestamp']-$grouped[$k][$m]['utimestamp']<600)
	{$grouped[$k][$m+1]=$encData[$i];$m++;}
	else
	{
		$k++;
		$m=0;
		$grouped[$k][$m]=$encData[$i];
	}
}
echo $k;
$total_time=0;
$total_score=0;
$total_turn=0;
$total_acc=0;
$total_break=0;
for($m=0;$m<$k;$m++)
{
unset($encData);
$encData=$grouped[$m];
$drivingScore = 0;
$coef1 = 0.1;
$coef2 = 0.2;
$coef3 = 0.6;
$speedType = 0;
$deltaSpeed=0;
$speed1=0;
$speed2=0;
$speed3=0;
$acc1=0;
$acc2=0;
$acc3=0;
$brake1=0;
$brake2=0;
$brake3=0;
$turn1=0;
$turn2=0;
$turn3=0;
$acc = 0;
	


$j=count($encData);
for ($i = 1; $i < $j; $i++)
{
	$typeTurn[0] = 'normal point';
	$typeAcc[0] = 'normal point';
	$sevTurn = 0;
	$sevAcc = 0;
	$sevSpeed = 0;
	$speed = $encData[$i]['speed'];	
	$deltaTime = ($encData[$i]['utimestamp'] - $encData[$i-1]['utimestamp']);

	if ( ($encData[$i]['lng']-$encData[$i-1]['lng']) != 0  )
	{
		$turn[$i] = atan(($encData[$i]['lat']-$encData[$i-1]['lat'])/($encData[$i]['lng']-$encData[$i-1]['lng']));
		$turn[0] = 0;
		$deltaTurn = $turn[$i] - $turn[$i-1];
		//echo $deltaTurn."<br/>";
		$wAcc = abs($deltaTurn/$deltaTime);
		$radius = $speed/$wAcc;
		if ($speed < 90) {
			$speedType = 0; 
		} else if ($speed < 110) {
			$speed1++;
			$speedType = 1;
		} else if ($speed<130)	{
			$speed2++;
			$speedType = 2;
		} else	{
			$speed3++;
			$speedType = 3;
		}
		if ( ($wAcc < 4.5) || (!is_Numeric($wAcc)) ) {
			$sevTurn = 0;
		} else 	if ($wAcc < 6)	{
			$sevTurn = 1;
			$turn1++;
		} else 	if ($wAcc < 7.5) {
               $sevTurn = 2;
			$turn2++;
		} else {
			$sevTurn = 3;
			$turn3++;
		}
		if (($typeTurn[$i-1] == 'left turn finished') || ($typeTurn[$i-1] == 'right turn finished') || (!isset($typeTurn[$i-1])) || ($speed == 0) ){
			$typeTurn[$i] = 'normal point';
		} else 	if ($deltaTurn > 0.5)   {
		    if ($typeTurn[$i-1] == 'normal point') $typeTurn[$i] = 'left turn started';
			if (($typeTurn[$i-1] == 'left turn started')||($typeTurn[$i-1] == 'left turn continued')) $typeTurn[$i] = 'left turn continued';
			if (($typeTurn[$i-1] == 'right turn started')||($typeTurn[$i-1] == 'right turn continued')) $typeTurn[$i] = 'right turn finished';
		} else 	if ($deltaTurn < -0.5)	{
               if ($typeTurn[$i-1] == 'normal point') $typeTurn[$i] = 'right turn started';
			if (($typeTurn[$i-1] == 'right turn started')||($typeTurn[$i-1] == 'right turn continued')) $typeTurn[$i] = 'right turn continued';
			if (($typeTurn[$i-1] == 'left turn started')||($typeTurn[$i-1] == 'left turn continued')) $typeTurn[$i] = 'left turn finished';
		} else	{
			if ($typeTurn[$i-1] == 'normal point') $typeTurn[$i] = 'normal point';
			if (($typeTurn[$i-1] == 'left turn started')||($typeTurn[$i-1] == 'left turn continued')) $typeTurn[$i] = 'left turn finished';
			if (($typeTurn[$i-1] == 'right turn started')||($typeTurn[$i-1] == 'right turn continued')) $typeTurn[$i] = 'right turn finished';
		}
	}
	else 	
	{
		$typeTurn[$i] = 'normal point';
		$sevTurn[$i] = 0;
		$wAcc = 0;
		$radius = 0;
	}
	
	$timeSum = 0;
	$sumSpeed = 0;

	if ($deltaTime!=0){
		$deltaSpeed = $speed - $encData[$i-1]['speed'];
		$accel[$i] = $deltaSpeed/$deltaTime;
		if ($accel[$i]<-7.5) {
		  $sevAcc = -3;
		  $brake3++;
		} else if ($accel[$i]<-6){
		  $sevAcc = -2;
		  $brake2++;
		} else if ($accel[$i]<-4.5){
		  $sevAcc = -1;
		  $brake1++;
		} else if ($accel[$i]>5){
		  $sevAcc = 3;
		  $acc3++;
		} else if ($accel[$i]>4){
		  $sevAcc = 2;
		  $acc2++;
		} else if ($accel[$i]>3.5){
		  $sevAcc = 1;
		  $acc1++;
		} else {
		  $sevAcc = 0;
		}
	}

}


$fullTime = ($encData[$j - 1]['utimestamp'] - $encData[0]['utimestamp']) /1000 / 60 / 60;
$drivingScore = ($coef1 * ($speed1 + $turn1 + $acc1 + $brake1) + $coef2 * ($speed2 + $turn2 + $acc2 + $brake2) + $coef3 * ($speed3 + $turn3 + $acc3 + $brake3)) / $fullTime;
echo "Time ".$fullTime."<br/>";
echo "score ".$total_score."<br/>";
echo "turn ".$total_turn."<br/>";
echo "acc ".$total_acc."<br/>";
echo "break ".$total_break."<br/>";

$total_time=$total_time+$fullTime; 
$total_score=$total_score+$drivingScore; 
$total_turn=$total_turn+$turn1+$turn3+$turn2; 
$total_acc=$total_acc+$acc1+$acc2+$acc3; 
$total_break=$total_break+$brake1+$brake2+$brake3; 
}
echo "Total time ".$total_time."<br/>";
echo "Total score ".$total_score/$k."<br/>";
echo "Total turn ".$total_turn."<br/>";
echo "Total acc ".$total_acc."<br/>";
echo "Total break ".$total_break."<br/>";

?>
