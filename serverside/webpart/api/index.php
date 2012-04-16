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
$data=$_POST['data'];
if(!isset($_POST))
{
	$data = file_get_contents("php://input");

	if(!$data)
	{
		$res=array('result'=>0,'error'=> "Error while getting data");
		echo json_encode($res);
		exit();
	}

}

$json = json_decode($data,true);
switch(json_last_error())
    {
        case JSON_ERROR_DEPTH:
            
			$errortype=array('info'=>" - Maximum stack depth exceeded",'code'=>  666);
			$res=array('result'=>1,'error'=> $errortype);
			echo json_encode($res);
			exit();
        break;
        case JSON_ERROR_CTRL_CHAR:
		$errortype=array('info'=>"  - Unexpected control character found",'code'=>  666);
		$res=array('result'=>1,'error'=> $errortype);
		echo json_encode($res);
			exit();
        break;
        case JSON_ERROR_SYNTAX:
			$errortype=array('info'=>" - Syntax error, malformed JSON",'code'=>  666);
			$res=array('result'=>1,'error'=> $errortype);
			echo json_encode($res);
			exit();
        break; 
    }
    
   
if(!isset($json['method'])){ $errortype=array('info'=>"No difintion state set, or function is incorrect",'code'=>  1);
$res=array('result'=>2,'error'=>  $errortype);echo json_encode($res);exit();}

if($json['method']=="NTIauth"){NTIauth($json['params']);}//-
else if($json['method']=="addNTIFile"){addNTIFile($json['params']);}//-
else if($json['method']=="NTIregister"){NTIregister($json['params']);}//-
else if($json['method']=="getStatistics"){getStatistics($json['params']);}//-
else if($json['method']=="getPath"){getPath($json['params']);}//-

else
{
	   $errortype=array('info'=>"No action set, or function is incorrect",'code'=>  1);
    if(!isset($json['action'])){$res=array('result'=>2,'error'=>  $errortype);echo json_encode($res);exit();}
}

function NTI_Cookie_check()
{
		$cooks=$_COOKIE['NTIKeys'];
		if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
		$cooks=mysql_real_escape_string($cooks);
		$result = mysql_query("SELECT * from NTIKeys where SID='$cooks' and Deleted=0");
		$cnt=mysql_num_rows($result);
		if($cnt==0)
		{
		
			mysql_close($dbcnx);
			return -3;
		}
		else
		{
		
			$dt=time();
			while($row = mysql_fetch_array($result))
			{
				if($dt-$row['Creation_Date']>6000)
				{
					mysql_query("UPDATE NTIKeys SET Deleted=1 where SID='$cooks'");
					return -2;
				}
				mysql_close($dbcnx);
				return $row['UID'];
			} 
		}
	return -1;

}


function NTIregister($param)
{
	$username=$param['login'];
	$password=$param['password'];
	$email=$param['login'];
	$name=$param['name'];
	$surname=$param['surname'];
	
	if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
	
	$username=mysql_real_escape_string($username);
	$password=mysql_real_escape_string($password);
	$email=mysql_real_escape_string($email);
	$name=mysql_real_escape_string($name);
	$surname=mysql_real_escape_string($surname);
		if(!isset($username) || !isset($password))
	{
		mysql_close($dbcnx);
		$errortype=array('info'=>"You dont set mail,password,login",'code'=>  2);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		exit();
	}
	$result = mysql_query("SELECT Id from NTIUsers where Login='$username'");
	$cnt=mysql_num_rows($result);
	if(!$cnt==0)
	{
		mysql_close($dbcnx);
		$errortype=array('info'=>"User name already exists",'code'=>  3);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		
		exit();
	}
	$result = mysql_query("SELECT Id from NTIUsers where Email='$email'");
	$cnt=mysql_num_rows($result);
	if(!$cnt==0)
	{
		mysql_close($dbcnx);
		$errortype=array('info'=>"User mail already exists",'code'=>  4);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		exit();
	}
	if(strlen($password)<64)
	{
		mysql_close($dbcnx);
		$errortype=array('info'=>"Password check failed, seem to be not sha",'code'=>  5);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		exit();
	}
	
		if(strlen($username)>32 || strlen($email)>32 || strlen($name)>32 || strlen($surname)>32 )
	{
		mysql_close($dbcnx);
		$errortype=array('info'=>"Fields are too long. Must be less than 32 bytes",'code'=>  6);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		exit();
	}

	
	mysql_query("INSERT into NTIUsers (Login,Password,Email,FName,SName) values ('$username','$password','$email','$name','$surname')");
	
	$id=mysql_insert_id();
	$tm=time(); 
	$sid=rand_str();
	setcookie("NTIKey", $sid,time()+6000);
	mysql_query("INSERT into NTIKeys (UID,SID,Creation_Date) values ('$id','$sid','$tm')");

	
	$errortype=array('info'=>"User was registered",'code'=>  0);
	$res=array('result'=>$sid ,'error'=>  $errortype);
	echo json_encode($res);
	exit();
}



function NTIauth($param)
{
	$username=$param['login'];
	$secret=$param['secret'];
	
    $device=$param['device'];
	$model=$param['model'];
	$version=$param['version'];
	$carrier=$param['carrier'];
	if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
		if(!isset($secret) || !isset($username))
	{
		mysql_close($dbcnx);
		$errortype=array('info'=>"Bad secret (it doesnt set)",'code'=>  10);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		exit();
	}
	$username=mysql_real_escape_string($username);
	$secret=mysql_real_escape_string($secret);
	
	$device=mysql_real_escape_string($device);
	$model=mysql_real_escape_string($model);
	$version=mysql_real_escape_string($version);
	$carrier=mysql_real_escape_string($carrier);
	
	$result = mysql_query("SELECT Id from NTIUsers where Login='$username'");
	$cnt=mysql_num_rows($result);
	if($cnt==0)
	{
		mysql_close($dbcnx);
		$errortype=array('info'=>"User doesnt exist",'code'=>  11);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		exit();
	}
	
	
	$result = mysql_query("SELECT Id from NTIUsers where Login='$username' and Password='$secret'");
	$cnt=mysql_num_rows($result);
	if($cnt==0)
	{
		mysql_close($dbcnx);
		$errortype=array('info'=>"Mismatch",'code'=>  12);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		exit();
	}
	else
	{
		$id=mysql_result($result,0);
	}

	//session_start();
	$tm=time(); 
	//$sid=session_id();
	$sid=rand_str();
	mysql_query("UPDATE NTIKeys SET Deleted=1 where UID=$id");
	setcookie("NTIKeys", $sid,time()+6000);
	
	mysql_query("INSERT into NTIKeys (UID,SID,Creation_Date,device,model,version,carrier) values ('$id','$sid','$tm','$device','$model','$version','$carrier')");
	$errortype=array('info'=>"Al akey",'code'=>  0);
	$res=array('result'=>$sid ,'error'=>  $errortype);
	echo json_encode($res);
	exit();
}


function addNTIFile($param)
{
	$ntifile=$param['ntifile'];
	$ins=json_encode($ntifile);
	if (strlen($ins) > 10) 
	{
		$UID=NTI_Cookie_check();
		$m = new Mongo(); 
		$db = $m->NTI;
		$NTIInfo=$db->NTIInfo;
		$utmstamp=time();
		
		
		$qq = json_decode($ins,true);	
		if ($qq != NULL) 
		{
			$insert_data=array(
				'UID'=>$UID,
				'UnixTimeStamp'=>$utmstamp,
				'NTIFile'=>$ntifile
				);
			$NTIInfo->insert($insert_data);		
			
			if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
			mysql_query("INSERT into NTIFile (UID,File) values ('$UID','$ins')");
			$fileid = mysql_insert_id();
			$k = 0;
			while ($qq[$k]) 
			{
				$accx = $qq[$k]['acc']['x'];
				$accy = $qq[$k]['acc']['y'];
				$lat =  $qq[$k]['gps']['latitude'];
				$lng = $qq[$k]['gps']['longitude'];
				$direction = $qq[$k]['gps']['direction'];
				$compass = $qq[$k]['gps']['compass'];
				$speed = $qq[$k]['gps']['speed'];
				$distance = $qq[$k]['gps']['distance'];
				$utimestamp = $qq[$k]['timestamp'];
					$accx = mysql_real_escape_string($accx);
					$accy = mysql_real_escape_string($accy);
					$lat = mysql_real_escape_string($lat);
					$lng = mysql_real_escape_string($lng); 
					$direction = mysql_real_escape_string($direction);
					$compass = mysql_real_escape_string($compass);
					$speed = mysql_real_escape_string($speed);
					$distance = mysql_real_escape_string($distance);
					$utimestamp = mysql_real_escape_string($utimestamp);
					$str = "INSERT INTO NTIEntry (UID, accx, accy, distance, lat, lng, direction, compass, speed, utimestamp, FileId) VALUES ($UID, $accx, $accy, $distance, $lat, $lng, $direction, $compass, $speed, $utimestamp, $fileid)";
					mysql_query($str);
				//echo $str;
				$k++;
			}
			$errortype=array('info'=>"All okey",'code'=>  0);
			$res=array('result'=>1,'error'=> $errortype);
			echo json_encode($res);	
	
			exit();
		}
		else
		{
			$errortype=array('info'=>"Data is not in json",'code'=>  4);
			$res=array('result'=>1,'error'=> $errortype);
			echo json_encode($res);	
			exit();	
		}
	}
	else
	{						
		$errortype=array('info'=>"File is too small or empty(".strlen($ins).")",'code'=>  3);
		$res=array('result'=>1,'error'=> $errortype);
		echo json_encode($res);	
		exit();
	}
}







function getStatistics($param)
{
	$last=$param['last'];
	if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
	$total_time=0;
	$total_score=0;
	$total_turn=0;
	$total_acc=0;
	$total_break=0;
	if(isset($last))
	{
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
			{
				$grouped[$k][$m+1]=$encData[$i];$m++;
			}
			else
			{
				$k++;
				$m=0;
				$grouped[$k][$m]=$encData[$i];
			}
		}


		for($m=$k-1;$m<$k;$m++)
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
					$wAcc = abs($deltaTurn/$deltaTime);
					$radius = $speed/$wAcc;
					if ($speed < 90) 
					{
						$speedType = 0; 
					} 
					else if ($speed < 110) 
					{
						$speed1++;
						$speedType = 1;
					} 
					else if ($speed<130)	
					{
						$speed2++;
						$speedType = 2;
					} 
					else	
					{
						$speed3++;
						$speedType = 3;
					}
					if ( ($wAcc < 4.5) || (!is_Numeric($wAcc)) ) 
					{
						$sevTurn = 0;
					}
					else 	if ($wAcc < 6)	
					{
						$sevTurn = 1;
						$turn1++;
					}
					else 	if ($wAcc < 7.5) 
					{
						$sevTurn = 2;
						$turn2++;
					} 
					else 
					{
						$sevTurn = 3;
						$turn3++;
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
				if ($deltaTime!=0)
				{
					$deltaSpeed = $speed - $encData[$i-1]['speed'];
					$accel[$i] = $deltaSpeed/$deltaTime;
					if ($accel[$i]<-7.5) 
					{
						$sevAcc = -3;
						$brake3++;
					} 
					else if ($accel[$i]<-6)
					{
						$sevAcc = -2;
						$brake2++;
					}
					else if ($accel[$i]<-4.5)
					{
						$sevAcc = -1;
						$brake1++;
					} 
					else if ($accel[$i]>5)
					{
						$sevAcc = 3;
						$acc3++;
					} 
					else if ($accel[$i]>4)
					{
						$sevAcc = 2;
						$acc2++;
					} 
					else if ($accel[$i]>3.5)
					{
						$sevAcc = 1;
						$acc1++;
					} 
					else 
					{
						$sevAcc = 0;
					}
				}

			}
			$fullTime = ($encData[$j - 1]['utimestamp'] - $encData[0]['utimestamp']) /1000 / 60 / 60;
			$drivingScore = ($coef1 * ($speed1 + $turn1 + $acc1 + $brake1) + $coef2 * ($speed2 + $turn2 + $acc2 + $brake2) + $coef3 * ($speed3 + $turn3 + $acc3 + $brake3)) / $fullTime;
			$total_time+=$fullTime; 
			$total_score+=$drivingScore; 
			$total_turn+=$turn1+$turn3+$turn2; 
			$total_acc+=$acc1+$acc2+$acc3; 
			$total_break+=$brake1+$brake2+$brake3; 
		}
	}
	else
	{
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
			{
				$grouped[$k][$m+1]=$encData[$i];$m++;
			}
			else
			{
				$k++;
				$m=0;
				$grouped[$k][$m]=$encData[$i];
			}
		}


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
					$wAcc = abs($deltaTurn/$deltaTime);
					$radius = $speed/$wAcc;
					if ($speed < 90) 
					{
						$speedType = 0; 
					} 
					else if ($speed < 110) 
					{
						$speed1++;
						$speedType = 1;
					} 
					else if ($speed<130)	
					{
						$speed2++;
						$speedType = 2;
					} 
					else	
					{
						$speed3++;
						$speedType = 3;
					}
					if ( ($wAcc < 4.5) || (!is_Numeric($wAcc)) ) 
					{
						$sevTurn = 0;
					}
					else 	if ($wAcc < 6)	
					{
						$sevTurn = 1;
						$turn1++;
					}
					else 	if ($wAcc < 7.5) 
					{
						$sevTurn = 2;
						$turn2++;
					} 
					else 
					{
						$sevTurn = 3;
						$turn3++;
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
				if ($deltaTime!=0)
				{
					$deltaSpeed = $speed - $encData[$i-1]['speed'];
					$accel[$i] = $deltaSpeed/$deltaTime;
					if ($accel[$i]<-7.5) 
					{
						$sevAcc = -3;
						$brake3++;
					} 
					else if ($accel[$i]<-6)
					{
						$sevAcc = -2;
						$brake2++;
					}
					else if ($accel[$i]<-4.5)
					{
						$sevAcc = -1;
						$brake1++;
					} 
					else if ($accel[$i]>5)
					{
						$sevAcc = 3;
						$acc3++;
					} 
					else if ($accel[$i]>4)
					{
						$sevAcc = 2;
						$acc2++;
					} 
					else if ($accel[$i]>3.5)
					{
						$sevAcc = 1;
						$acc1++;
					} 
					else 
					{
						$sevAcc = 0;
					}
				}

			}
			$fullTime = ($encData[$j - 1]['utimestamp'] - $encData[0]['utimestamp']) /1000 / 60 / 60;
			$drivingScore = ($coef1 * ($speed1 + $turn1 + $acc1 + $brake1) + $coef2 * ($speed2 + $turn2 + $acc2 + $brake2) + $coef3 * ($speed3 + $turn3 + $acc3 + $brake3)) / $fullTime;
			$total_time+=$fullTime; 
			$total_score+=$drivingScore; 
			$total_turn+=$turn1+$turn3+$turn2; 
			$total_acc+=$acc1+$acc2+$acc3; 
			$total_break+=$brake1+$brake2+$brake3; 
		}		
	}
							
		$ret=array('total_score'=>$total_score,'total_time'=>$total_time,'total_turn'=>$total_turn,'total_acc'=>$total_acc,'total_break'=>$total_break);				
		$errortype=array('info'=>"",'code'=>  0);
		$res=array('result'=>$ret,'error'=> $errortype);
		echo json_encode($res);	
		exit();
	
}






function getPath($param)
{
	$time=$param['time'];
	$UID=NTI_Cookie_check();
	if($UID>0)
	{
	if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
	if(isset($time))
	{
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

		
	}
	else
	{
		$errortype=array('info'=>"No time set",'code'=>  31);
		$res=array('result'=>-1,'error'=> $errortype);
		echo json_encode($res);	
		exit();
	}		
		$vg[1]=42;
		$k=0;
		for ($i = 1; $i < $j; $i++)
		{
			if($encData[$i]['utimestamp']-$encData[$i-1]['utimestamp']!=0)
			{
				if(($encData[$i]['utimestamp']-$encData[$i-1]['utimestamp']>300) || ((sqrt(pow(($encData[$i]['lat']-$encData[$i-1]['lat']),2)+pow(($encData[$i]['lng']-$encData[$i-1]['lng']),2))/($encData[$i]['utimestamp']-$encData[$i-1]['utimestamp']))<200))$vg[$i]=42;			
				$ret_arr[$k]['lat']=$encData[$i]['lat'];
				$ret_arr[$k]['lng']=$encData[$i]['lng'];
				$ret_arr[$k]['type']=$vg[$i];
				$k++;
			}
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
			$errortype=array('info'=>"There is no data with such time",'code'=>  32);
			$res=array('result'=>-1,'error'=> $errortype);
			echo json_encode($res);	
			exit();

		}
	}
	else
	{
		$errortype=array('info'=>"Connection broken or you are not authorized",'code'=>  33);
		$res=array('result'=>-1,'error'=> $errortype);
		echo json_encode($res);	
		exit();
	}
	
}
?>
