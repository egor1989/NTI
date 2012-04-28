<?php

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


	$time=1335328400;

	

		$last=0;//Отвечает за то , чтобы выбрать последнюю поезку
		if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
	
		
		$start=strtotime(date("D M j 00:00:00 T Y",$time));
		$end= strtotime(date("D M j 23:59:59 T Y",$time));
		$query = "Select * from (SELECT * FROM NTIEntry where utimestamp<=1335394799  and utimestamp>=1335308400 and UID=13 and (lat!=0 or lng!=0) and utimestamp!=0 group by utimestamp order by utimestamp) as st group by `lat`,`lng`";

		$result = mysql_query($query);
		$c = 0;
		$n = 0;
		while ($row = mysql_fetch_array($result)) 
		{
			$encData[$n]['lat'] = $row['lat'];
			$encData[$n]['lng'] = $row['lng'];
			$encData[$n]['compass'] = $row['compass'];
			$encData[$n]['speed'] = $row['speed'];
			$encData[$n]['distance'] = $row['distance'];
			$encData[$n]['utimestamp'] = $row['utimestamp'];
			$n++;
		}

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
					
				}
				else 	
				{
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
		$R = 6371; // km
		for ($i = 1; $i < $j; $i++)
		{
				
				$d = acos(sin($encData[$i]['lat'])*sin($encData[$i-1]['lat']) + cos($encData[$i]['lat'])*cos($encData[$i-1]['lat']) *  cos($encData[$i-1]['lng']-$encData[$i]['lng'])) * $R;
				if(($encData[$i]['utimestamp']-$encData[$i-1]['utimestamp']>300) || (($d)/($encData[$i]['utimestamp']-$encData[$i-1]['utimestamp']))>40)$vg[$i]=42;			
				$ret_arr[$k]['lat']=$encData[$i]['lat'];
				$ret_arr[$k]['lng']=$encData[$i]['lng'];
				$ret_arr[$k]['type']=$vg[$i];
				$ret_arr[$k]['waht']=$accel[$i];			
				$k++;
		}
		
		if($k!=0)
		{
		
			$errortype=array('info'=>"",'code'=>  0);
			$res=array('result'=>$ret_arr,'error'=> $errortype);
			echo json_encode($res);	
			exit();
		}	
		else
		{
			$errortype=array('info'=>"There is no data with such time($end $start)",'code'=>  32);
			$res=array('result'=>-1,'error'=> $errortype);
			echo json_encode($res);	
			exit();

		}
	



	





?>
