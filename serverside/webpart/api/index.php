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
    
   
if(!isset($json['method'])){ $errortype=array('info'=>"No difintion state set, or function is incorrect",'code'=>  1);$res=array('result'=>2,'error'=>  $errortype);echo json_encode($res);exit();}

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
		if(isset($_COOKIE['NTIKey']))
	{
		$cooks=$_COOKIE['NTIKey'];
		$cooks=mysql_real_escape_string($cooks);
		if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
		$result = mysql_query("SELECT * from NTIKeys where SID='$cooks' and Deleted=0");
		$cnt=mysql_num_rows($result);
		if($cnt==0)
		{
			mysql_close($dbcnx);
			return -1;
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
return -3;

}


function NTIregister($param)
{
	$username=$param['login'];
	$password=$param['password'];
	$email=$param['email'];
	$name=$param['name'];
	$surname=$param['surname'];
	
	if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
		if(!isset($username) || !isset($password) || !isset($email))
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
	
	mysql_query("INSERT into NTIKeys (UID,SID,Creation_Date) values ('$id','$sid','$tm')");
	$errortype=array('info'=>"Al akey",'code'=>  0);
	$res=array('result'=>$sid ,'error'=>  $errortype);
	echo json_encode($res);
	exit();
}


function addNTIFile($param)
{
	$ntifile=$param['ntifile'];
	$UID=NTI_Cookie_check();
	$m = new Mongo(); 
	$db = $m->NTI;
	$NTIInfo=$db->NTIInfo;
	$utmstamp=time();
	$ins=json_encode($ntifile);
	$insert_data=array(
		'UID'=>$UID,
		'UnixTimeStamp'=>$utmstamp,
		'NTIFile'=>$ntifile
		);
	$NTIInfo->insert($insert_data);		
	if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
	mysql_query("INSERT into NTIFile (UID,File) values ('$UID','$ins')");
	
    $entrys = json_decode($ntifile,true);
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

	for($i=0;$i<count($entrys);$i++)
	{
	

		mysql_query("Insert into NTIEntry (accx,accy,distance,lat,lng,direction,compass,speed,utimestamp) values (".$entrys[$i]['acc']['x'].",".$entrys[$i]['acc']['y'].",".$entrys[$i]['gps']['distance'].",".$entrys[$i]['gps']['latitude'].",".$entrys[$i]['gps']['longitude'].",".$entrys[$i]['gps']['direction'].",".$entrys[$i]['gps']['compass'].",".$entrys[$i]['gps']['speed'].",".$entrys[$i]['timestamp'].")");

	}

		$errortype=array('info'=>"",'code'=>  0);
		$res=array('result'=>1,'error'=> $errortype);
		echo json_encode($res);	
		exit();
}


?>