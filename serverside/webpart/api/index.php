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
	

}


function NTIregister($param)
{
	$username=$param['login'];
	$password=$param['password'];
	$email=$param['login'];
	$name=$param['name'];
	$surname=$param['surname'];
	
	if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
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
	
	$username=mysql_real_escape_string($username);
	$password=mysql_real_escape_string($password);
	$email=mysql_real_escape_string($email);
	$name=mysql_real_escape_string($name);
	$surname=mysql_real_escape_string($surname);
	
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
	
	mysql_query("INSERT into NTIKeys (UID,SID,Creation_Date,device,model,version) values ('$id','$sid','$tm','$device','$model','$version')");
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



?>
