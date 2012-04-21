<?php
$dbcnx=0;
function rand_str($length = 64, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
{
    $chars_length = (strlen($chars) - 1);
    $string = $chars{rand(0, $chars_length)};
    for ($i = 1; $i < $length; $i = strlen($string))
    {
        $r = $chars{rand(0, $chars_length)};
        if ($r != $string{$i - 1}) $string .=  $r;
    }
    return $string;
}


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

	if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
		$time=mysql_real_escape_string($time);
		$query = "SELECT * FROM NTIEntry where utimestamp>=$time and UID=$UID and (lat!=0 or lng!=0) and utimestamp!=0 order by utimestamp";
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
			$drivingScore = 0;
			$coef1 = 0.1;
			$coef2 = 0.2;
			$coef3 = 0.6;
			$speedType = 0;
			$deltaSpeed=0;
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
					$wAcc = abs($deltaTurn/$deltaTime);
					$radius = $speed/$wAcc;
					if (($typeTurn[$i-1] == 'left turn finished') || ($typeTurn[$i-1] == 'right turn finished') || (!isset($typeTurn[$i-1])) || ($speed == 0) )
					{
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
	$vg[$i]=0;
		if ($sevAcc==1) $vg[$i]=1;
		if ($sevAcc==2) $vg[$i]=2;
		if ($sevAcc==3) $vg[$i]=3;
	    if ($sevAcc==-1) $vg[$i]=-1;
		if ($sevAcc==-2) $vg[$i]=-2;
		if ($sevAcc==-3) $vg[$i]=-3;
		

			}

		

		$vg[1]=42;
		$k=0;
		for ($i = 1; $i < $j; $i++)
		{
			if($encData[$i]['utimestamp']-$encData[$i-1]['utimestamp']!=0)
			{
				if(($encData[$i]['utimestamp']-$encData[$i-1]['utimestamp']>300) || ((sqrt(pow(($encData[$i]['lat']-$encData[$i-1]['lat']),2)+pow(($encData[$i]['lng']-$encData[$i-1]['lng']),2))/($encData[$i]['utimestamp']-$encData[$i-1]['utimestamp']))>180))$vg[$i]=42;			
				$ret_arr[$k]['lat']=$encData[$i]['lat'];
				$ret_arr[$k]['lng']=$encData[$i]['lng'];
				$ret_arr[$k]['type']=$vg[$i];
				$k++;
			}
		}

	

?>
