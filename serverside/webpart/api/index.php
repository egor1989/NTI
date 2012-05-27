<?php
//ErrorCodes
define(DB_CONNECTION_REFUSE_CODE, 88);
define(DB_CONNECTION_REFUSE_INFO, "Database connection error");
define(JSON_ERROR_DEPTH_CODE, 1);
define(JSON_ERROR_DEPTH_INFO, "Maximum stack depth exceeded");
define(JSON_ERROR_CTRL_CHAR_CODE, 2);
define(JSON_ERROR_CTRL_CHAR_INFO, "Unexpected control character found");
define(JSON_ERROR_SYNTAX_CODE, 3);
define(JSON_ERROR_SYNTAX_INFO, "Syntax error, malformed JSON");
define(NO_METHOD_SET_CODE, 4);
define(NO_METHOD_SET_INFO, "No difintion state set, or function is incorrect");
define(NO_FUNCTION_SET_CODE, 5);
define(NO_FUNCTION_SET_INFO, "No action set, or function is incorrect");




$dbcnx=0;

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
 public function getLat($lat){return($this->lat);}
 public function getLng($lng) {return($this->lng);}
 public function getCompass($compass){	return($this->compass); }
 public function getDirection($direction){	return($this->direction); }
 public function getTimestamp($timestamp) {	return($this->timestamp); }
 public function getDistance($distance) {	return($this->distance); }
 public function getSpeed($speed) {	return($this->speed); }
 public function getAccx($accx) {	return($this->accx); }
 public function getAccy($accy) {	return($this->accy); }

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


















function distance_between_points($lat1,$lat2,$lng1,$lng2)
{
	//Функция возвращает расстояние между 2-мя точками в километрах
	return acos(sin($lat1)*sin($lat2)+cos($lat1)*cos($lat2)*cos($lng2-$lng1))*111.2;
	
}



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
$zip=$_POST['zip'];

if($zip==1)
{
	$data=str_replace("<","",$data);
	$data=str_replace(">","",$data);
	$data=str_replace(" ","",$data);
	$data=gzdecodes(pack('H*',$data));
}

function gzdecodes($data) 
{ 
   return gzinflate(substr($data,10,-8)); 
} 
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
			$res=array('result'=>$data,'error'=> $errortype);
			echo json_encode($res);
			exit();
        break; 
    }
    
   
if(!isset($json['method'])){ $errortype=array('info'=>"No difintion state set, or function is incorrect",'code'=>  NO_METHOD_SET_CODE);
$res=array('result'=>-1,'error'=>  $errortype);echo json_encode($res);exit();}

if($json['method']=="NTIauth"){NTIauth($json['params']);}//-
else if($json['method']=="addNTIFile"){addNTIFile($json['params']);}//-
else if($json['method']=="NTIregister"){NTIregister($json['params']);}//-
else if($json['method']=="getStatistics"){getStatistics($json['params']);}//-
else if($json['method']=="getPath"){getPath($json['params']);}//-
else if($json['method']=="feedBack"){feedBack($json['params']);}//-
else if($json['method']=="addQuest"){feedBack($json['params']);}//-
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
				if($dt-$row['Creation_Date']>60000)
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

			if(strlen($email)<3)
	{
		mysql_close($dbcnx);
		$errortype=array('info'=>"Email is too short",'code'=>  7);
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
		$utmstamp=time();
		//Монго? - не, не слышал
		//Тк делаем , лучше пусть будет переносима с приемлемыми результатами
		
		$qq = json_decode($ins,true);	
		if ($qq != NULL) 
		{
			
			if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
			mysql_query("INSERT into NTIFile (UID,File) values ('$UID','$ins')");
			$fileid = mysql_insert_id();
			$k = 0;
			//Начинаем обрабатывать и разбивать
			$i=0;
			$n=0;
			while ($qq[$k]) 
			{
				if($i>0)
				{
					if($qq[$k]['timestamp']-$ArrayEntry[$n][$i-1]->getTimestamp<600)
					{
						$ArrayEntry[$n][$i]=new UserEntry();
						$ArrayEntry[$n][$i]->setLat($qq[$k]['gps']['latitude']);
						$ArrayEntry[$n][$i]->setLng($qq[$k]['gps']['longitude']);
						$ArrayEntry[$n][$i]->setAccx($qq[$k]['acc']['x']);
						$ArrayEntry[$n][$i]->setAccy($qq[$k]['acc']['y']);
						$ArrayEntry[$n][$i]->setDirection($qq[$k]['gps']['direction']);
						$ArrayEntry[$n][$i]->setDistance($qq[$k]['gps']['distance']);
						$ArrayEntry[$n][$i]->setSpeed($qq[$k]['gps']['speed']);
						$ArrayEntry[$n][$i]->setCompass($qq[$k]['gps']['compass']);
						$ArrayEntry[$n][$i]->setTimestamp($qq[$k]['timestamp']);
						$i++;
					}
					else
					{
						$n++;
						$i=0;
						$ArrayEntry[$n][$i]=new UserEntry();
						$ArrayEntry[$n][$i]->setLat($qq[$k]['gps']['latitude']);
						$ArrayEntry[$n][$i]->setLng($qq[$k]['gps']['longitude']);
						$ArrayEntry[$n][$i]->setAccx($qq[$k]['acc']['x']);
						$ArrayEntry[$n][$i]->setAccy($qq[$k]['acc']['y']);
						$ArrayEntry[$n][$i]->setDirection($qq[$k]['gps']['direction']);
						$ArrayEntry[$n][$i]->setDistance($qq[$k]['gps']['distance']);
						$ArrayEntry[$n][$i]->setSpeed($qq[$k]['gps']['speed']);
						$ArrayEntry[$n][$i]->setCompass($qq[$k]['gps']['compass']);
						$ArrayEntry[$n][$i]->setTimestamp($qq[$k]['timestamp']);
						$i++;
					}
				}
				else
				{
						$n++;
						$i=0;
						$ArrayEntry[$n][$i]=new UserEntry();
						$ArrayEntry[$n][$i]->setLat($qq[$k]['gps']['latitude']);
						$ArrayEntry[$n][$i]->setLng($qq[$k]['gps']['longitude']);
						$ArrayEntry[$n][$i]->setAccx($qq[$k]['acc']['x']);
						$ArrayEntry[$n][$i]->setAccy($qq[$k]['acc']['y']);
						$ArrayEntry[$n][$i]->setDirection($qq[$k]['gps']['direction']);
						$ArrayEntry[$n][$i]->setDistance($qq[$k]['gps']['distance']);
						$ArrayEntry[$n][$i]->setSpeed($qq[$k]['gps']['speed']);
						$ArrayEntry[$n][$i]->setCompass($qq[$k]['gps']['compass']);
						$ArrayEntry[$n][$i]->setTimestamp($qq[$k]['timestamp']);
						$i++;
				}

				
				//$str = "INSERT INTO NTIEntry (UID, accx, accy, distance, lat, lng, direction, compass, speed, utimestamp, FileId) VALUES ($UID, $accx, $accy, $distance, $lat, $lng, $direction, $compass, $speed, $utimestamp, $fileid)";
				//mysql_query($str);
				$k++;
			}
			//Разбили по времени
			//Теперь перебирем поездки и высчитываем данные 
			for($i=1;$i<$n;$i++)
			{
				
				
					$acc1=0;
					$acc2=0;       
					$acc3=0;       
					$turn1=0;       
					$turn2=0;       
					$turn3=0;      
					$speed1=0;      
					$speed2=0;     
					$speed3=0;     
					$brake1=0;     
				$brake2=0;    
					$brake3=0;    
				if(count($ArrayEntry[$i])>50)
				{
					$ArrayEntry[$i][0]->setsevAcc(0);
					$ArrayEntry[$i][0]->setsevTurn(0);
					$ArrayEntry[$i][0]->setsevSpeed(0);
					$ArrayEntry[$i][0]->setTurnType("normal point");
					$ArrayEntry[$i][0]->setTypeSpeed("normal point");
					$ArrayEntry[$i][0]->setTypeAcc("normal point");
					
					for($j=1;$j<count($ArrayEntry[$i]);$j++)
					{
						$ArrayEntry[$i][$j]->setsevAcc(0);
						$ArrayEntry[$i][$j]->setsevTurn(0);
						$ArrayEntry[$i][$j]->setsevSpeed(0);
						$ArrayEntry[$i][$j]->setTurnType("normal point");
						$ArrayEntry[$i][$j]->setTypeSpeed("normal point");
						$ArrayEntry[$i][$j]->setTypeAcc("normal point");
						$speed=$ArrayEntry[$i][$j]->getSpeed();
						$deltaTime=$ArrayEntry[$i][$j]->getTimestamp()-$ArrayEntry[$i][$j-1]->getTimestamp();
						if($ArrayEntry[$i][$j]->getLng()-$ArrayEntry[$i][$j-1]->getLng()!=0)
						{
								$ArrayEntry[$i][$j]->setTurn(atan(($ArrayEntry[$i][$j]->getLat()-$ArrayEntry[$i][$j-1]->getLat())/($ArrayEntry[$i][$j]->getLng()-$ArrayEntry[$i][$j-1]->getLng())));
								$deltaTurn = 	$ArrayEntry[$i][$j]->getTurn() - $ArrayEntry[$i][$j-1]->getTurn();
								$wAcc = abs($deltaTurn/($deltaTime));
														
								if (($wAcc < 0.45) && ($wAcc >= 0)) {$ArrayEntry[$i][$j]->setsevTurn(0);} 
								else if (($wAcc >= 0.45) && ($wAcc < 0.6))	{$ArrayEntry[$i][$j]->setsevTurn(1);} 
								else if (($wAcc >= 0.6) && ($wAcc < 0.75)){$ArrayEntry[$i][$j]->setsevTurn(2);} 
								else if ($wAcc >= 0.75) {$ArrayEntry[$i][$j]->setsevTurn(3);}
								$deltaSpeed = $speed - $ArrayEntry[$i][$j-1]->getSpeed();
								$accel = $deltaSpeed/$deltaTime;
								//Высчитываем тип неравномерного движения (ускорение-торможение) через ускорение.
								if ($accel<-7.5) $ArrayEntry[$i][$j]->setsevAcc(-3);
								else if (($accel>=-7.5)&&($accel<-6))$ArrayEntry[$i][$j]->setsevAcc(-2);
								else if (($accel>=-6)&&($accel<-4.5))$ArrayEntry[$i][$j]->setsevAcc(-1);
								else if ($accel>5)$ArrayEntry[$i][$j]->setsevAcc(3);
								else if (($accel>4)&&($accel<=5))$ArrayEntry[$i][$j]->setsevAcc(2);
								else if (($accel>3.5)&&($accel<=4))$ArrayEntry[$i][$j]->setsevAcc(1);
								else if (($accel>=-4.5)&&($accel<=3.5))$ArrayEntry[$i][$j]->setsevAcc(0);
								//Рассчитываем превышения скорости. Превышение (1,2,3 уровня) засчитывается, если движение осуществлялось на соответствующей скорости 5 секунд. 
								//И далее еще по очку превышения (1,2,3 уровня) за каждые ПОЛНЫЕ ТРИ секунд движения на превышенной скорости.
								if (($speed >= 0) && ($speed <= 80))$ArrayEntry[$i][$j]->setsevSpeed(0); 
								else if (($speed > 80) && ($speed <= 110))$ArrayEntry[$i][$j]->setsevSpeed(1); 
								else if (($speed > 110) && ($speed <= 130))	$ArrayEntry[$i][$j]->setsevSpeed(2); 
								else if ($speed > 130)$ArrayEntry[$i][$j]->setsevSpeed(3); 
								if ($ArrayEntry[$i][$j-1]->getTypeSpeed() == "normal point") {
									if ($ArrayEntry[$i][$j]->getsevSpeed() == 0) {
										$ArrayEntry[$i][$j]->setTypeSpeed("normal point");
									} else if ($ArrayEntry[$i][$j]->getsevSpeed() == 1) {
										$ArrayEntry[$i][$j]->setTypeSpeed("s1");
										$dss = $deltaTime;
									} else if ($ArrayEntry[$i][$j]->getsevSpeed() == 2) {
										$ArrayEntry[$i][$j]->setTypeSpeed("s2");
										$dss = $deltaTime;
									} else if ($ArrayEntry[$i][$j]->getsevSpeed() == 3) {
										$ArrayEntry[$i][$j]->setTypeSpeed("s3");
										$dss = $deltaTime;
									}
								} else if ($ArrayEntry[$i][$j-1]->getTypeSpeed()  == "s1") {

									if ($ArrayEntry[$i][$j]->getsevSpeed() == 0) {

										$ArrayEntry[$i][$j]->setTypeSpeed("normal point");
										$speed1 = $speed1 + floor($dss/3);
										$dss = 0;
									
									} else if ($ArrayEntry[$i][$j]->getsevSpeed() == 1) {
										$ArrayEntry[$i][$j]->setTypeSpeed("s1");
											$dss += $deltaTime;
									} else if ($ArrayEntry[$i][$j]->getsevSpeed() == 2) {
										$ArrayEntry[$i][$j]->setTypeSpeed("s2");
										$speed1 = $speed1 +floor($dss/3);
										$dss = 0;
									} else if ($ArrayEntry[$i][$j]->getsevSpeed() == 3) {
										$ArrayEntry[$i][$j]->setTypeSpeed("s3");
										$speed1 += floor($dss/3);
										$dss = 0;
									}
								} else if ($ArrayEntry[$i][$j-1]->getTypeSpeed()  == "s2") {
									if ($ArrayEntry[$i][$j]->getsevSpeed() == 0) {
										$ArrayEntry[$i][$j]->setTypeSpeed("normal point");
										$speed2 += floor($dss/3);
										$dss = 0;
									} else if ($ArrayEntry[$i][$j]->getsevSpeed() == 1) {
										$ArrayEntry[$i][$j]->setTypeSpeed("s1");
										$speed2 = $speed2 +floor($dss/3);
										$dss = 0;
									} else if ($ArrayEntry[$i][$j]->getsevSpeed() == 2) {
										$ArrayEntry[$i][$j]->setTypeSpeed("s2");
										$dss += $deltaTime;
									} else if ($ArrayEntry[$i][$j]->getsevSpeed() == 3) {
										$ArrayEntry[$i][$j]->setTypeSpeed("s3");
										$speed2 += floor($dss/3);
										$dss = 0;
									}
								} else if ($ArrayEntry[$i][$j-1]->getTypeSpeed()  == "s3") {
									if ($ArrayEntry[$i][$j]->getsevSpeed() == 0) {
										$ArrayEntry[$i][$j]->setTypeSpeed("normal point");
										$speed3 += floor($dss/3);
										$dss = 0;
									} else if ($ArrayEntry[$i][$j]->getsevSpeed() == 1) {
										$ArrayEntry[$i][$j]->setTypeSpeed("s1");
										$speed3 += floor($dss/3);
										$dss = 0;
									} else if ($ArrayEntry[$i][$j]->getsevSpeed() == 2) {
										$ArrayEntry[$i][$j]->setTypeSpeed("s2");
										$speed3 += floor($dss/3);
										$dss = 0;
									} else if ($ArrayEntry[$i][$j]->getsevSpeed() == 3) {
										$ArrayEntry[$i][$j]->setTypeSpeed("s3");
										$dss += $deltaTime;
									}
								}
								// Конец выявления превышения скорости.
								///////////////////////////////////////////////////////////////////////////////////////
								
								//Большое количество проверок условий соотношения ускорений в текущей и прошлой точках.
								
								
								if ($ArrayEntry[$i][$j-1]->getTypeAcc() == "normal point") {
									if ($ArrayEntry[$i][$j]->getsevAcc() == 0) {
										$ArrayEntry[$i][$j]->setTypeAcc("normal point");
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 1) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc1 started");
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 2) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc2 started");
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 3) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc3 started");
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -1) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake1 started");
									} else if ($ArrayEntry[$i][$j]->getsevAcc()== -2) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake2 started");
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -3) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake3 started");
									}
								} else	if (($ArrayEntry[$i][$j-1]->getTypeAcc()== "acc1 started") || ($ArrayEntry[$i][$j-1]->getTypeAcc() == "acc1 continued")) {
									if ($ArrayEntry[$i][$j]->getsevAcc() == 0) {
										$ArrayEntry[$i][$j]->setTypeAcc("normal point");
										$acc1++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 1) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc1 continued");
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 2) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc2 continued");
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 3) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc3 continued");
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -1) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake1 started");
										$acc1++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -2) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake2 started");
										$acc1++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -3) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake3 started");
										$acc1++;
									}
								} else	if (($ArrayEntry[$i][$j-1]->getTypeAcc()== "acc2 started") || ($ArrayEntry[$i][$j-1]->getTypeAcc() == "acc2 continued")) {
									if ($ArrayEntry[$i][$j]->getsevAcc() == 0) {

										$ArrayEntry[$i][$j]->setTypeAcc("normal point");
										$acc2++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc()== 1) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc1 started");
										$acc2++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 2) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc2 continued");
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 3) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc3 continued");
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -1) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake1 started");
										$acc2++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -2) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake2 started");
										$acc2++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -3) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake3 started");
										$acc2++;
									}
								} else	if (($ArrayEntry[$i][$j-1]->getTypeAcc() == "acc3 started") || ($ArrayEntry[$i][$j-1]->getTypeAcc() == "acc3 continued")) {
									if ($ArrayEntry[$i][$j]->getsevAcc() == 0) {
										$ArrayEntry[$i][$j]->setTypeAcc("normal point");
										$acc3++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 1) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc1 started");
										$acc3++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 2) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc2 started");
										$acc3++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 3) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc3 continued");
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -1) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake1 started");
										$acc3++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -2) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake2 started");
										$acc3++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -3) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake3 started");
										$acc3++;
									}
								} else	if (($ArrayEntry[$i][$j-1]->getTypeAcc() == "brake1 started") || ($ArrayEntry[$i][$j-1]->getTypeAcc() == "brake1 continued")) {
									if ($ArrayEntry[$i][$j]->getsevAcc() == 0) {
										$ArrayEntry[$i][$j]->setTypeAcc("normal point");
										$brake1++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 1) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc1 started");
										$brake1++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 2) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc2 started");
										$brake1++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 3) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc3 started");
										$brake1++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -1) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake1 continued");
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -2) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake2 started");
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -3) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake3 started");
									}
								} else if (($ArrayEntry[$i][$j-1]->getTypeAcc() == "brake2 started") || ($ArrayEntry[$i][$j-1]->getTypeAcc() == "brake2 continued")) {
									if ($ArrayEntry[$i][$j]->getsevAcc()== 0) {
										$ArrayEntry[$i][$j]->setTypeAcc("normal point");
										$brake2++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 1) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc1 started");
										$brake2++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 2) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc2 started");
										$brake2++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 3) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc3 started");
										$brake2++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc()== -1) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake1 started");
										$brake2++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -2) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake2 continued");
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -3) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake3 started");
									}
								} else	if (($ArrayEntry[$i][$j-1]->getTypeAcc() == "brake3 started") || ($ArrayEntry[$i][$j-1]->getTypeAcc() == "brake3 continued")) {
									if ($ArrayEntry[$i][$j]->getsevAcc() == 0) {
										$ArrayEntry[$i][$j]->setTypeAcc("normal point");
										$brake3++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 1) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc1 started");
										$brake3++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 2) {
										$ArrayEntry[$i][$j]->setTypeAcc( "acc2 started");
										$brake3++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == 3) {
										$ArrayEntry[$i][$j]->setTypeAcc("acc3 started");
										$brake3++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc()== -1) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake1 started");
										$brake3++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc() == -2) {
										$ArrayEntry[$i][$j]->setTypeAcc("brake2 started");
										$brake3++;
									} else if ($ArrayEntry[$i][$j]->getsevAcc()== -3) {

										$ArrayEntry[$i][$j]->setTypeAcc("brake3 continued");
									}
								}
						
								//После поворота - нормальная точка.
								
								if (($ArrayEntry[$i][$j-1]->getTurnType() == "left turn finished") || ($ArrayEntry[$i][$j-1]->getTurnType() == "right turn finished") || ($speed == 0) ) {
									$ArrayEntry[$i][$j]->setTurnType("normal point");
								// Отклонение > 0.5 - после нормальной точки начинаем поворот налево, либо продолжаем поворот налево после уже начатого, либо завершаем, если это был поворот направо.
								} else 	if ($deltaTurn > 0.5)   {
									if ($ArrayEntry[$i][$j-1]->getTurnType() == "normal point") $ArrayEntry[$i][$j]->setTurnType( "left turn started");
									if (($ArrayEntry[$i][$j-1]->getTurnType() == "left turn started")||($ArrayEntry[$i][$j-1]->getTurnType() == "left turn continued"))$ArrayEntry[$i][$j]->setTurnType("left turn continued");
									if (($ArrayEntry[$i][$j-1]->getTurnType() == "right turn started")||($ArrayEntry[$i][$j-1]->getTurnType() == "right turn continued"))$ArrayEntry[$i][$j]->setTurnType("right turn finished");
								// Отклонение > 0.5 - после нормальной точки начинаем поворот направо, либо продолжаем поворот направо после уже начатого, либо завершаем, если это был поворот налево.
								} else 	if ($deltaTurn < -0.5)	{
									if ($ArrayEntry[$i][$j-1]->getTurnType() == "normal point")$ArrayEntry[$i][$j]->setTurnType("right turn started");
									if (($ArrayEntry[$i][$j-1]->getTurnType() == "right turn started")||($ArrayEntry[$i][$j-1]->getTurnType() == "right turn continued"))$ArrayEntry[$i][$j]->setTurnType("right turn continued");
									if (($ArrayEntry[$i][$j-1]->getTurnType() == "left turn started")||($ArrayEntry[$i][$j-1]->getTurnType() == "left turn continued"))$ArrayEntry[$i][$j]->setTurnType("left turn finished");
								} else	{
								// Отклонение между -0.5 и 0.5 - после нормальной точки идет нормальная, а после начатых поворотов налево или направо - продолженные повороты соответственно налево и направо.
									if ($ArrayEntry[$i][$j-1]->getTurnType() == "normal point")$ArrayEntry[$i][$j]->setTurnType("normal point");
									if (($ArrayEntry[$i][$j-1]->getTurnType() == "left turn started")||($ArrayEntry[$i][$j-1]->getTurnType() == "left turn continued"))$ArrayEntry[$i][$j]->setTurnType("left turn finished");
									if (($ArrayEntry[$i][$j-1]->getTurnType() == "right turn started")||($typeTurn[$i-1] == "right turn continued"))$ArrayEntry[$i][$j]->setTurnType( "right turn finished");
								}
								
								
								
								
								if (($ArrayEntry[$i][$j]->getTurnType() == "left turn finished") || ($ArrayEntry[$i][$j]->getTurnType() == "right turn finished")) 
								{
									switch ($ArrayEntry[$i][$j]->getsevTurn()) {
											case 1: {$turn1++;break;}
											case 2: {$turn2++;break;}
											case 3: {$turn3++;break;}
											case 0: {break;}
										}
								}	
						}
						
						
						
						
						
					}
					$TimeStart=$ArrayEntry[$i][0];
					$TimeEnd=$ArrayEntry[$i][0];
					
					//Теперь ищем начало и конец поездки 
					for($j=0;$j<count($ArrayEntry[$i]);$j++)
					{
						if($ArrayEntry[$i][$j]<$TimeStart)$TimeStart=$ArrayEntry[$i][$j];
						if($ArrayEntry[$i][$j]>$TimeEnd)$TimeEnd=$ArrayEntry[$i][$j];
					}
					//Теперь ищем расстояние, которое проехали в поездке
					//Для этого не будем полагаться на мобильник и посчитаем всё руками
					$TotalDistance=0;
					for($j=0;$j<count($ArrayEntry[$i])-1;$j++)
					{
						$TotalDistance+=distance_between_points($ArrayEntry[$i][$j]->getLat(),$ArrayEntry[$i][$j+1]->getLat(),$ArrayEntry[$i][$j]->getLng(),$ArrayEntry[$i][$j+1]->getLng());
					}
					$TypeAcc1Count  =$acc1;
					$TypeAcc2Count =$acc2;       
					$TypeAcc3Count =$acc3;       
					$TypeTurn1Count=$turn1;       
					$TypeTurn2Count=$turn2;       
					$TypeTurn3Count =$turn3;      
					$TypeSpeed1Count =$speed1;      
					$TypeSpeed2Count =$speed2;     
					$TypeSpeed3Count =$speed3;     
					$TypeBrake1Count =$brake1;     
					$TypeBrake2Count =$brake2;    
					$TypeBrake3Count =$brake3;    
					//Перебираем все элементы для поиска конечного
					//посчитали i ую поездку
					//Теперь проверим , есть ли уже именно с этим пользователем поездки 
					//Для этого получаем все временнные метки 
					//Для того, чтобы поездка соединилась должны выполняться следующие условия
					//Лучше ебану пример запросом
					/*
					SELECT `TimeStart`,`TimeEnd`,`Id` FROM `NTIUserDrivingTrack` WHERE UID=8 and
					(`TimeStart`>=1334857038 and 1334857238 >=`TimeStart` and 1334857238<=`TimeEnd`)
					OR
					(`TimeStart`<=1334857038 and 1334857238<=`TimeEnd`)
					OR
					(`TimeStart`<=1334857038 and 1334857038 <=`TimeEnd` and 1334857238>=`TimeEnd`)
					OR
					(`TimeStart`>=1334857038 and 1334857238>=`TimeEnd`)
					 */ 
					
					
					$result = mysql_query("	SELECT * FROM `NTIUserDrivingTrack` WHERE UID=8 and (`TimeStart`>=$TimeStart and $TimeEnd >=`TimeStart` and $TimeEnd<=`TimeEnd`) OR	(`TimeStart`<=$TimeStart and $TimeEnd<=`TimeEnd`)	OR	(`TimeStart`<=$TimeStart and $TimeStart <=`TimeEnd` and $TimeEnd>=`TimeEnd`)OR	(`TimeStart`>=$TimeStart and $TimeEnd>=`TimeEnd`)OR	(`TimeStart`>=$TimeEnd and TimeStart-$TimeEnd<300)	OR	(`TimeEnd`<=$TimeStart and $TimeStart-`TimeEnd`<300)");
					while($row = mysql_fetch_array($result)) 
					{	//Для начала получаем все данные этих поездк
						$TypeAcc1Count  = $row['lat'];
						$TypeAcc2Count =$row['lat'];       
						$TypeAcc3Count =$row['lat'];       
						$TypeTurn1Count=$row['lat'];       
						$TypeTurn2Count=$row['lat'];       
						$TypeTurn3Count =$row['lat'];      
						$TypeSpeed1Count =$row['lat'];      
						$TypeSpeed2Count =$row['lat'];     
						$TypeSpeed3Count =$row['lat'];     
						$TypeBrake1Count =$row['lat'];     
						$TypeBrake2Count =$row['lat'];    
						$TypeBrake3Count =$row['lat'];    
						$TotalDistance=$row['lat'];	
					}
					
					
					
					
					
					
				}
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
		$errortype=array('info'=>"File is too small or empty",'code'=>  3);
		$res=array('result'=>1,'error'=> $errortype);
		echo json_encode($res);	
		exit();
	}
}







function getStatistics($param)
{
		if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
		$UID=NTI_Cookie_check();
		if($UID>0)
		{
		$result = mysql_query("SELECT * FROM NTIEntry where UID=$UID AND lat != 0 AND lng != 0 group by utimestamp order by utimestamp");
		$n=0;
		//Если не было поездок за текущий период или не передано ни одной точки.
			while($row = mysql_fetch_array($result)) {
		
				$data[$n]['lat'] = $row['lat'];
				$data[$n]['lng'] = $row['lng'];
				$data[$n]['compass'] = $row['compass'];
				$data[$n]['speed'] = $row['speed'];
				$data[$n]['distance'] = $row['distance'];
				$data[$n]['utimestamp'] = $row['utimestamp'];
				$n++;
			}

		$R = 111.2; //$R = 6371; // km *666*
		//Если дданные есть:
		if (mysql_num_rows($result)>0) {
			$k = 0;
			$m = 0;
			$grouped[$k][$m]=$data[0];
			$n = count($data)-1;
			for ($i=1;$i<$n-1;$i++) {
				if (($data[$i]['utimestamp'] - $grouped[$k][$m]['utimestamp'] < 300) && 
						((acos(sin($data[$i]['lat'])*sin($grouped[$k][$m]['lat']) + cos($data[$i]['lat'])*cos($grouped[$k][$m]['lat']) *  cos($grouped[$k][$m]['lng']-$data[$i]['lng'])) * $R)/($data[$i]['utimestamp'] - $grouped[$k][$m]['utimestamp']) < 180))
					{
						$m++;
						$grouped[$k][$m] = $data[$i];
					}
					else
					{


							$k++;
							$m = 0; 
							$grouped[$k][$m] = $data[$i];
					}
				
			}
			
			
			
				
				$w = 0;
				$n=0;
				for($i=0;$i<count($grouped);$i++) {
					$w=0;
					for ($v=1; $v<count($grouped[$i]); $v++) {
						if ($grouped[$i][$v]['lng'] != $grouped[$i][$v-1]['lng'] ) {
							$unfilteredData[$n][$w] = $grouped[$i][$v-1];
							$w++;
						}
					}
					$n++;
				}
				unset($grouped);
				$v=0;
			for($i=0;$i<$n;$i++)
			{
			if(isset($unfilteredData[$i]))
				if(count($unfilteredData[$i])>10)
				{
					if(($unfilteredData[$i][count($unfilteredData[$i])-1]['distance']-$unfilteredData[$i][0]['distance'])>0.1)
					{
						$notneed=0;
						for($g=0;$g<count($unfilteredData[$i]);$g++)if($unfilteredData[$i][$g]['speed']==0)$notneed++;
						if($notneed*2<count($unfilteredData[$i]))
						{
							$grouped[$v]=$unfilteredData[$i];
							$v++;
						}
					}
				}
			}
			
			

			$grouped=array_reverse($grouped);
			
			$z=count($grouped);
			if (isset($param['last']) && $param['last']>0) {
				
				if ($z >= 2)for ($i=1;$i<$z;$i++)unset($grouped[$i]);
			//$grouped=array_reverse($grouped);			
			}
						
			unset($results);
			$results['score']=0;
			$results['score_speed']=0;
			$results['score_turn']=0;
			$results['score_acc']=0;
			$results['score_brake']=0;
			//$results['score'] =0;				 
			$results['time'] =	0;
			$results['turn1']=	0;
			$results['turn2']=	0;
			$results['turn3']=0;
			$results['acc1']=0;
			$results['acc2']=	0;
			$results['acc3']=	0;
			$results['brake1']=	0;
			$results['brake2']	=	0;
			$results['brake3']=0;
			$results['prev1']=0;
			$results['prev2']=	0;
			$results['prev3']=0;
			$results['tscore'] = 0;
			$total_time=0;
			$total_score=0;
			$total_turn=0;
			$total_acc=0;
			$total_brake=0;
			$total_turn1=0;
			$total_turn2=0;
			$total_turn3=0;
			$total_acc1=0;
			$total_acc2=0;
			$total_acc3=0;
			$total_brake1=0;
			$total_brake2=0;
			$total_brake3=0;
			$total_prev1=0;
			$total_prev2=0;
			$total_prev3=0;
			$tt = 0;
			$ta = 0;
			$tp = 0;
			$tb = 0;
			$ttime = 0;
		
			$unfilteredData=$grouped;
			$d=0;
			for($m=0;$m<count($unfilteredData);$m++) {
				unset($data);
				$drivingScore = 0;
				$coef1 = 0.1;
				$coef2 = 0.2;
				$coef3 = 0.6;
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
				$data=$unfilteredData[$m];
					$j=count($data);
					$dss = 0;

					
					
					$last_time= $data[1]['utimestamp'];
						for ($i = 1; $i < $j-1; $i++)
						{
							$typeTurn[0] = "normal point";
							$typeAcc[0] = "normal point";
							$typeSpeed[0] = "normal point";
							
							$sevTurn = 0;
							$sevAcc = 0;
							$sevSpeed = 0;
							$speed = $data[$i]['speed'];	
							$deltaTime = $data[$i]['utimestamp'] - $data[$i-1]['utimestamp'];
							$d += acos(sin($data[$i]['lat'])*sin($data[$i-1]['lat']) + cos($data[$i]['lat'])*cos($data[$i-1]['lat']) *  cos($data[$i-1]['lng']-$data[$i]['lng'])) * 111.2;
	
							if ( ($data[$i]['lng']-$data[$i-1]['lng']) != 0  )
							{
							
								$turn[$i] = atan(($data[$i]['lat']-$data[$i-1]['lat'])/($data[$i]['lng']-$data[$i-1]['lng']));
								$turn[0] = 0;
								$deltaTurn = $turn[$i] - $turn[$i-1];
								$wAcc = abs($deltaTurn/($deltaTime));
														
								if (($wAcc < 0.45) && ($wAcc >= 0)) {$sevTurn = 0;} 
								else 	if (($wAcc >= 0.45) && ($wAcc < 0.6))	{$sevTurn = 1;} 
								else 	if (($wAcc >= 0.6) && ($wAcc < 0.75)){$sevTurn = 2;	} 
								else if ($wAcc >= 0.75) {$sevTurn = 3;}
								
								$deltaSpeed = $speed - $data[$i-1]['speed'];
								$accel[$i] = $deltaSpeed/$deltaTime;
								
								//Высчитываем тип неравномерного движения (ускорение-торможение) через ускорение.
								if ($accel[$i]<-7.5) {
									$sevAcc = -3;
								} else if (($accel[$i]>=-7.5)&&($accel[$i]<-6)) {
									$sevAcc = -2;
								} else if (($accel[$i]>=-6)&&($accel[$i]<-4.5)) {
									$sevAcc = -1;
								} else if ($accel[$i]>5) {
									$sevAcc = 3;
								} else if (($accel[$i]>4)&&($accel[$i]<=5)){
									$sevAcc = 2;
								} else if (($accel[$i]>3.5)&&($accel[$i]<=4)) {
									$sevAcc = 1;
								} else if (($accel[$i]>=-4.5)&&($accel[$i]<=3.5)) {
									$sevAcc = 0;
								}
								
								
								//Рассчитываем превышения скорости. Превышение (1,2,3 уровня) засчитывается, если движение осуществлялось на соответствующей скорости 5 секунд. 
								//И далее еще по очку превышения (1,2,3 уровня) за каждые ПОЛНЫЕ ТРИ секунд движения на превышенной скорости.
								if (($speed >= 0) && ($speed <= 80)) 
									$sevSpeed = 0;
								else if (($speed > 80) && ($speed <= 110))
									$sevSpeed = 1;
								else if (($speed > 110) && ($speed <= 130))
									$sevSpeed = 2;
								else if ($speed > 130)
									$sevSpeed = 3;
								
								//$typeSpeed[$i] = "normal point";
						
								if ($typeSpeed[$i-1] == "normal point") {
									if ($sevSpeed == 0) {
										$typeSpeed[$i] = "normal point";
									} else if ($sevSpeed == 1) {
										$typeSpeed[$i] = "s1";
										$dss = $deltaTime;
									} else if ($sevSpeed == 2) {
										$typeSpeed[$i] = "s2";
										$dss = $deltaTime;
									} else if ($sevSpeed == 3) {
										$typeSpeed[$i] = "s3";
										$dss = $deltaTime;
									}
								} else if ($typeSpeed[$i-1] == "s1") {

									if ($sevSpeed == 0) {
										$typeSpeed[$i] = "normal point";
										//if ($dss > 3) {}
										$speed1 = $speed1 + floor($dss/3);
										$dss = 0;
									
									} else if ($sevSpeed == 1) {

										$typeSpeed[$i] = "s1";
										$dss += $deltaTime;
									} else if ($sevSpeed == 2) {
										$typeSpeed[$i] = "s2";
										$speed1 = $speed1 +floor($dss/3);
										$dss = 0;
									} else if ($sevSpeed == 3) {
										$typeSpeed[$i] = "s3";
										$speed1 += floor($dss/3);
										$dss = 0;
									}
								} else if ($typeSpeed[$i-1] == "s2") {
									if ($sevSpeed == 0) {
										$typeSpeed[$i] = "normal point";
										$speed2 += floor($dss/3);
										$dss = 0;
									} else if ($sevSpeed == 1) {
										$typeSpeed[$i] = "s1";
										$speed2 = $speed2 +floor($dss/3);
										$dss = 0;
									} else if ($sevSpeed == 2) {
										$typeSpeed[$i] = "s2";
										$dss += $deltaTime;
									} else if ($sevSpeed == 3) {
										$typeSpeed[$i] = "s3";
										$speed2 += floor($dss/3);
										$dss = 0;
									}
								} else if ($typeSpeed[$i-1] == "s3") {
									if ($sevSpeed == 0) {
										$typeSpeed[$i] = "normal point";
										$speed3 += floor($dss/3);
										$dss = 0;
									} else if ($sevSpeed == 1) {
										$typeSpeed[$i] = "s1";
										$speed3 += floor($dss/3);
										$dss = 0;
									} else if ($sevSpeed == 2) {
										$typeSpeed[$i] = "s2";
										$speed3 += floor($dss/3);
										$dss = 0;
									} else if ($sevSpeed == 3) {
										$typeSpeed[$i] = "s3";
										$dss += $deltaTime;
									}
								}
								// Конец выявления превышения скорости.
								///////////////////////////////////////////////////////////////////////////////////////
								
								//Большое количество проверок условий соотношения ускорений в текущей и прошлой точках.
								if ($typeAcc[$i-1] == "normal point") {
									if ($sevAcc == 0) {
										$typeAcc[$i] = "normal point";
									} else if ($sevAcc == 1) {
										$typeAcc[$i] = "acc1 started";
									} else if ($sevAcc == 2) {
										$typeAcc[$i] = "acc2 started";
									} else if ($sevAcc == 3) {
										$typeAcc[$i] = "acc3 started";
									} else if ($sevAcc == -1) {
										$typeAcc[$i] = "brake1 started";
									} else if ($sevAcc == -2) {
										$typeAcc[$i] = "brake2 started";
									} else if ($sevAcc == -3) {
										$typeAcc[$i] = "brake3 started";
									}
								} else	if (($typeAcc[$i-1] == "acc1 started") || ($typeAcc[$i-1] == "acc1 continued")) {
									if ($sevAcc == 0) {
										$typeAcc[$i] = "normal point";
										$acc1++;
									} else if ($sevAcc == 1) {
										$typeAcc[$i] = "acc1 continued";
									} else if ($sevAcc == 2) {
										$typeAcc[$i] = "acc2 continued";
									} else if ($sevAcc == 3) {
										$typeAcc[$i] = "acc3 continued";
									} else if ($sevAcc == -1) {
										$typeAcc[$i] = "brake1 started";
										$acc1++;
									} else if ($sevAcc == -2) {
										$typeAcc[$i] = "brake2 started";
										$acc1++;
									} else if ($sevAcc == -3) {
										$typeAcc[$i] = "brake3 started";
										$acc1++;
									}
								} else	if (($typeAcc[$i-1] == "acc2 started") || ($typeAcc[$i-1] == "acc2 continued")) {
									if ($sevAcc == 0) {
										$typeAcc[$i] = "normal point";
										$acc2++;
									} else if ($sevAcc == 1) {
										$typeAcc[$i] = "acc1 started";
										$acc2++;
									} else if ($sevAcc == 2) {
										$typeAcc[$i] = "acc2 continued";
									} else if ($sevAcc == 3) {
										$typeAcc[$i] = "acc3 continued";
									} else if ($sevAcc == -1) {
										$typeAcc[$i] = "brake1 started";
										$acc2++;
									} else if ($sevAcc == -2) {
										$typeAcc[$i] = "brake2 started";
										$acc2++;
									} else if ($sevAcc == -3) {
										$typeAcc[$i] = "brake3 started";
										$acc2++;
									}
								} else	if (($typeAcc[$i-1] == "acc3 started") || ($typeAcc[$i-1] == "acc3 continued")) {
									if ($sevAcc == 0) {
										$typeAcc[$i] = "normal point";
										$acc3++;
									} else if ($sevAcc == 1) {
										$typeAcc[$i] = "acc1 started";
										$acc3++;
									} else if ($sevAcc == 2) {
										$typeAcc[$i] = "acc2 started";
										$acc3++;
									} else if ($sevAcc == 3) {
										$typeAcc[$i] = "acc3 continued";
									} else if ($sevAcc == -1) {
										$typeAcc[$i] = "brake1 started";
										$acc3++;
									} else if ($sevAcc == -2) {
										$typeAcc[$i] = "brake2 started";
										$acc3++;
									} else if ($sevAcc == -3) {
										$typeAcc[$i] = "brake3 started";
										$acc3++;
									}
								} else	if (($typeAcc[$i-1] == "brake1 started") || ($typeAcc[$i-1] == "brake1 continued")) {
									if ($sevAcc == 0) {
										$typeAcc[$i] = "normal point";
										$brake1++;
									} else if ($sevAcc == 1) {
										$typeAcc[$i] = "acc1 started";
										$brake1++;
									} else if ($sevAcc == 2) {
										$typeAcc[$i] = "acc2 started";
										$brake1++;
									} else if ($sevAcc == 3) {
										$typeAcc[$i] = "acc3 started";
										$brake1++;
									} else if ($sevAcc == -1) {
										$typeAcc[$i] = "brake1 continued";
									} else if ($sevAcc == -2) {
										$typeAcc[$i] = "brake2 started";
									} else if ($sevAcc == -3) {
										$typeAcc[$i] = "brake3 started";
									}
								} else if (($typeAcc[$i-1] == "brake2 started") || ($typeAcc[$i-1] == "brake2 continued")) {
									if ($sevAcc == 0) {
										$typeAcc[$i] = "normal point";
										$brake2++;
									} else if ($sevAcc == 1) {
										$typeAcc[$i] = "acc1 started";
										$brake2++;
									} else if ($sevAcc == 2) {
										$typeAcc[$i] = "acc2 started";
										$brake2++;
									} else if ($sevAcc == 3) {
										$typeAcc[$i] = "acc3 started";
										$brake2++;
									} else if ($sevAcc == -1) {
										$typeAcc[$i] = "brake1 started";
										$brake2++;
									} else if ($sevAcc == -2) {
										$typeAcc[$i] = "brake2 continued";
									} else if ($sevAcc == -3) {
										$typeAcc[$i] = "brake3 started";
									}
								} else	if (($typeAcc[$i-1] == "brake3 started") || ($typeAcc[$i-1] == "brake3 continued")) {
									if ($sevAcc == 0) {
										$typeAcc[$i] = "normal point";
										$brake3++;
									} else if ($sevAcc == 1) {
										$typeAcc[$i] = "acc1 started";
										$brake3++;
									} else if ($sevAcc == 2) {
										$typeAcc[$i] = "acc2 started";
										$brake3++;
									} else if ($sevAcc == 3) {
										$typeAcc[$i] = "acc3 started";
										$brake3++;
									} else if ($sevAcc == -1) {
										$typeAcc[$i] = "brake1 started";
										$brake3++;
									} else if ($sevAcc == -2) {
										$typeAcc[$i] = "brake2 started";
										$brake3++;
									} else if ($sevAcc == -3) {
										$typeAcc[$i] = "brake3 continued";
									}
								}
								
																	
								//После поворота - нормальная точка.
								if (($typeTurn[$i-1] == "left turn finished") || ($typeTurn[$i-1] == "right turn finished") || (!isset($typeTurn[$i-1])) || ($speed == 0) ) {
									$typeTurn[$i] = "normal point";
								// Отклонение > 0.5 - после нормальной точки начинаем поворот налево, либо продолжаем поворот налево после уже начатого, либо завершаем, если это был поворот направо.
								} else 	if ($deltaTurn > 0.5)   {
									if ($typeTurn[$i-1] == "normal point") 
										$typeTurn[$i] = "left turn started";
									if (($typeTurn[$i-1] == "left turn started")||($typeTurn[$i-1] == "left turn continued")) 
										$typeTurn[$i] = "left turn continued";
									if (($typeTurn[$i-1] == "right turn started")||($typeTurn[$i-1] == "right turn continued")) {
										$typeTurn[$i] = "right turn finished";
										///////
									}
								// Отклонение > 0.5 - после нормальной точки начинаем поворот направо, либо продолжаем поворот направо после уже начатого, либо завершаем, если это был поворот налево.
								} else 	if ($deltaTurn < -0.5)	{
									if ($typeTurn[$i-1] == "normal point") 
										$typeTurn[$i] = "right turn started";
									if (($typeTurn[$i-1] == "right turn started")||($typeTurn[$i-1] == "right turn continued")) 
										$typeTurn[$i] = "right turn continued";
									if (($typeTurn[$i-1] == "left turn started")||($typeTurn[$i-1] == "left turn continued")) {
										$typeTurn[$i] = "left turn finished";
										///////
									}
								} else	{
								// Отклонение между -0.5 и 0.5 - после нормальной точки идет нормальная, а после начатых поворотов налево или направо - продолженные повороты соответственно налево и направо.
									if ($typeTurn[$i-1] == "normal point") 
										$typeTurn[$i] = "normal point";
									if (($typeTurn[$i-1] == "left turn started")||($typeTurn[$i-1] == "left turn continued")) {
										$typeTurn[$i] = "left turn finished";
										///////
									}
									if (($typeTurn[$i-1] == "right turn started")||($typeTurn[$i-1] == "right turn continued")) {
										$typeTurn[$i] = "right turn finished";
										///////
									}
								}
								
								if (($typeTurn[$i] == "left turn finished") || ($typeTurn[$i] == "right turn finished")) {
									switch ($sevTurn) {
											case 1: {
												$turn1++;
												break;
											}
											case 2: {
												$turn2++;
												break;
											}
											case 3: {
												$turn3++;
												break;
											}
											case 0: {
												break;
											}
										}
								}
							}
							else 	
							{
								$typeTurn[$i] = "normal point";
								$typeAcc[$i] = "normal point";
								$sevTurn = 0;
								$wAcc = 0;
								$radius = 0;
								$turn[$i] = $turn[$i-1];
							}
						
							$timeSum = 0;
							$sumSpeed = 0;
						
							
				
						}
						if(isset($data[$j-2]['utimestamp']))	{
						
							$fullTime = ($data[$j-2]['utimestamp'] - $data[0]['utimestamp']);
							if($fullTime!=0) {					
								$results['score'] 		+= 		($coef1 * ($speed1 + $turn1 + $acc1 + $brake1) + $coef2 * ($speed2 + $turn2 + $acc2 + $brake2) + $coef3 * ($speed3 + $turn3 + $acc3 + $brake3)) / ($fullTime/3600);
								$results['score_speed'] 	+=($coef1 * ($speed1) + $coef2 * ($speed2) + $coef3 * ($speed3)) / ($fullTime/3600);
								$results['score_turn'] 	+=($coef1 * ($turn1) + $coef2 * ($turn2) + $coef3 * ($turn3)) / ($fullTime/3600);
								$results['score_acc'] 	+=($coef1 * ($acc1) + $coef2 * ($acc2) + $coef3 * ($acc3)) / ($fullTime/3600);
								$results['score_brake'] 	+=($coef1 * ($brake1) + $coef2 * ($brake2) + $coef3 * ($brake3)) / ($fullTime/3600);
								$results['distance']+=$d;				
							}
						}	
			} 
			
			$errortype=array('info'=>"",'code'=>  0);
			if(isset($param['last']) && $param['last']>0)
			$ret=array('total_score'=>floor($results['score']),'score_speed'=>floor($results['score_speed']),'score_turn'=>floor($results['score_turn']),'score_acc'=>floor($results['score_acc']),'score_brake'=>floor($results['score_brake']),'distance'=>floor($results['distance']),'time'=>$last_time);
			else
			{
					$ret=array('total_score'=>floor($results['score']),'score_speed'=>floor($results['score_speed']),'score_turn'=>floor($results['score_turn']),'score_acc'=>floor($results['score_acc']),'score_brake'=>floor($results['score_brake']),'distance'=>floor($results['distance']),'time'=>0);
		
				}
			
			$res=array('result'=>$ret,'error'=> $errortype);
			echo json_encode($res);	
			exit();	
			
			
		}
		else {
			$errortype=array('info'=>"",'code'=>  0);
			$ret=array('total_score'=>0,'score_speed'=>0,'score_turn'=>0,'score_acc'=>0,'score_brake'=>0,'distance'=>0,'time'=>0);
		
			$res=array('result'=>$ret,'error'=> $errortype);
			echo json_encode($res);	
			exit();
		}
		return $results;
}
else
{
		$errortype=array('info'=>"Connection broken or you are not authorized",'code'=>  33);
		$res=array('result'=>-1,'error'=> $errortype);
		echo json_encode($res);	
		exit();
}
	
}






function getPath($param)
{
	
	$time=$param['time'];
	$till=$param['till'];
	$day=$param['day'];
	
	$UID=NTI_Cookie_check();
	if($UID>0)
	{
		$last=0;//Отвечает за то , чтобы выбрать последнюю поезку
		if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
		if(isset($param['time']) && !isset($param['till']) and $param['time']>0)
		{
		
		$start=strtotime(date("D M j 00:00:00 T Y",$time));
		$end= strtotime(date("D M j 23:59:59 T Y",$time));
			$query = "select * from (SELECT * FROM NTIEntry where utimestamp<=$end and utimestamp>=$start and UID=$UID and (lat!=0 or lng!=0) and utimestamp!=0 group by utimestamp) as st group by `lat`,`lng` order by utimestamp";
		}
		else if(isset($param['time']) && isset($param['till'])  and $param['time']>0  and $param['till']>0)
		{
				$time=mysql_real_escape_string($time);
				$till=mysql_real_escape_string($till);
				$query = "SELECT * FROM NTIEntry where utimestamp>=$time and utimestamp<=$till and UID=$UID and (lat!=0 or lng!=0) and utimestamp!=0 group by utimestamp order by utimestamp";
		}
		else if(isset($param['day']) && $param['day']>0)
		{
				$day=time()-86400;//Все даты за день до этого 
				$query = "SELECT * FROM NTIEntry where utimestamp>=$day and UID=$UID and (lat!=0 or lng!=0) and utimestamp!=0 group by utimestamp order by utimestamp";
		}
		else
		{
				//иначе будем возвращать последнюю поездку
				$query = "Select * from (SELECT * FROM NTIEntry where UID=$UID and (lat!=0 or lng!=0) and utimestamp!=0 group by utimestamp) as st group by `lat`,`lng` order by utimestamp";
				$last=1;
		}
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
		//Начинаем группировку
		if($last==0)
		{
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
				$sevTurn[0] = 0;
				$sevAcc[0] = 0;
				$sevSpeed[0] = 0;
				$speed = $encData[$i]['speed'];	
				$deltaTime = ($encData[$i]['utimestamp'] - $encData[$i-1]['utimestamp']);
				if ( ($encData[$i]['lng']-$encData[$i-1]['lng']) != 0  )
				{
					$turn[$i] = atan(($encData[$i]['lat']-$encData[$i-1]['lat'])/($encData[$i]['lng']-$encData[$i-1]['lng']));
					$turn[0] = 0;
					$deltaTurn = $turn[$i] - $turn[$i-1];
					$wAcc = abs($deltaTurn/$deltaTime);
					$radius = $speed/$wAcc;
											
																			if (($speed >= 0) && ($speed <= 80)) 
									$sevSpeed[$i] = 0;
								else if (($speed > 80) && ($speed <= 110))
									$sevSpeed[$i] = 1;
								else if (($speed > 110) && ($speed <= 130))
									$sevSpeed[$i] = 2;
								else if ($speed > 130)
									$sevSpeed[$i] = 3;
								
											
													
								//Высчитываем тип поворота через угловое ускорение.					
								if (($wAcc < 0.45) && ($wAcc >= 0)) {
									$sevTurn[$i] = 0;									
								} else 	if (($wAcc >= 0.45) && ($wAcc < 0.6))	{
									$sevTurn[$i] = 1;
								} else 	if (($wAcc >= 0.6) && ($wAcc < 0.75)){
									$sevTurn[$i] = 2;
								} else if ($wAcc >= 0.75) {
									$sevTurn[$i] = 3;
								}
								
								$deltaSpeed = $speed - $encData[$i-1]['speed'];
								$accel[$i] = $deltaSpeed/$deltaTime;
								
								//Высчитываем тип неравномерного движения (ускорение-торможение) через ускорение.
								if ($accel[$i]<-7.5) {
									$sevAcc[$i] = -3;
								} else if (($accel[$i]>=-7.5)&&($accel[$i]<-6)) {
									$sevAcc[$i] = -2;
								} else if (($accel[$i]>=-6)&&($accel[$i]<-4.5)) {
									$sevAcc[$i] = -1;
								} else if ($accel[$i]>5) {
									$sevAcc[$i] = 3;
								} else if (($accel[$i]>4)&&($accel[$i]<=5)){
									$sevAcc[$i] = 2;
								} else if (($accel[$i]>3.5)&&($accel[$i]<=4)) {
									$sevAcc[$i] = 1;
								} else if (($accel[$i]>=-4.5)&&($accel[$i]<=3.5)) {
									$sevAcc[$i] = 0;
								}
					
					
				}
				else 	
				{	$sevAcc[$i] = 0;
					$sevTurn[$i] = 0;
					$wAcc = 0;
					$radius = 0;
				}
				$timeSum = 0;
				$sumSpeed = 0;



		

			}

		

		$k=0;
		$R = 6371; // km
		for ($i = 1; $i < $j; $i++)
		{
				$vg=1;
				$d = acos(sin($encData[$i]['lat'])*sin($encData[$i-1]['lat']) + cos($encData[$i]['lat'])*cos($encData[$i-1]['lat']) *  cos($encData[$i-1]['lng']-$encData[$i]['lng'])) * $R;
				if(($encData[$i]['utimestamp']-$encData[$i-1]['utimestamp']>300) || (($d)/($encData[$i]['utimestamp']-$encData[$i-1]['utimestamp']))>40)$vg=42;			
				$ret_arr[$k]['lat']=$encData[$i]['lat'];
				$ret_arr[$k]['lng']=$encData[$i]['lng'];
				
				//Вычисляем , что отправлять
				if($vg!=42)
				{
				if($sevAcc[$i]!=0)
				{
					if($sevTurn[$i]==0)
					{
						if($sevAcc[$i]<0)
						{
							$ret_arr[$k]['type']=2;
							$ret_arr[$k]['weight']=$sevAcc[$i]*(-1);
						}
						else
						{
							$ret_arr[$k]['type']=1;
							$ret_arr[$k]['weight']=$sevAcc[$i];
						}
					}
					else
					{
						if($sevAcc[$i]<0)
						{
							if($sevAcc[$i]*(-1)>=$sevTurn[$i])
							{
									$ret_arr[$k]['type']=2;
									$ret_arr[$k]['weight']=$sevAcc[$i]*(-1);
							}
							else
							{
									$ret_arr[$k]['type']=3;
									$ret_arr[$k]['weight']=$sevTurn[$i];
							}
						}
						else
						{
							if($sevAcc[$i]>=$sevTurn[$i])
							{
									$ret_arr[$k]['type']=1;
									$ret_arr[$k]['weight']=$sevAcc[$i];
							}
							else
							{
									$ret_arr[$k]['type']=3;
									$ret_arr[$k]['weight']=$sevTurn[$i];
							}	
						}
					}
				}
				else if($sevAcc[$i]==0 && $sevSpeed[$i]==0)
				{
					
					if($sevTurn[$i]>0)
					{
						$ret_arr[$k]['type']=3;
						$ret_arr[$k]['weight']=$sevTurn[$i];
					}
					else
					{
						$ret_arr[$k]['type']=0;
						$ret_arr[$k]['weight']=0;
					}
				}
				else
				{
					if($sevSpeed[$i]>0)
					{
						$ret_arr[$k]['type']=4;
						$ret_arr[$k]['weight']=$sevSpeed[$i];
					}
					else
					{
						$ret_arr[$k]['type']=0;
						$ret_arr[$k]['weight']=0;
					}
				}
			}
			else
			{
				
				$ret_arr[$k]['type']=42;
				$ret_arr[$k]['weight']=42;
			}
						
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
	}
	else
	{
		if($n<=1)
		{
			$errortype=array('info'=>"There is no data with such time",'code'=>  32);
			$res=array('result'=>-1,'error'=> $errortype);
			echo json_encode($res);	
			exit();
		}

		//Если нудно взять только последнюю поездку
		//Для начала берем группировку
		$R = 6371; // km
		//Если дданные есть:
		if (mysql_num_rows($result)>0) {
			$k = 0;
			$m = 0;
			$grouped[$k][$m]=$encData[0];
			$n = count($encData)-1;
			for ($i=1;$i<$n-1;$i++) {
				if (($encData[$i]['utimestamp'] - $grouped[$k][$m]['utimestamp'] < 300) && 
						((acos(sin($encData[$i]['lat'])*sin($grouped[$k][$m]['lat']) + cos($encData[$i]['lat'])*cos($grouped[$k][$m]['lat']) *  cos($grouped[$k][$m]['lng']-$encData[$i]['lng'])) * $R)/($encData[$i]['utimestamp'] - $grouped[$k][$m]['utimestamp']) < 180))
					{
						$m++;
						$grouped[$k][$m] = $encData[$i];
					}
					else
					{
      						$k++;
							$m = 0; 
							$grouped[$k][$m] = $encData[$i];
					}
				
			}
			
			
			
				//Теперь фильтруем данные 
				$w = 0;
				$n=0;
				for($i=0;$i<count($grouped);$i++) {
					$w=0;
					for ($v=1; $v<count($grouped[$i]); $v++) {
						if ($grouped[$i][$v]['lng'] != $grouped[$i][$v-1]['lng'] ) {
							$unfilteredData[$n][$w] = $grouped[$i][$v-1];
							$w++;
						}
					}
					$n++;
				}
				unset($grouped);
				$v=0;
				for($i=0;$i<$n;$i++)
				{
					if(isset($unfilteredData[$i]))
					if(count($unfilteredData[$i])>10)
					{
						if(($unfilteredData[$i][count($unfilteredData[$i])-1]['distance']-$unfilteredData[$i][0]['distance'])>0.1)
						{
							$notneed=0;
							for($g=0;$g<count($unfilteredData[$i]);$g++)//if($unfilteredData[$i][$g]['speed']==0)$notneed++;
							if($notneed*2<count($unfilteredData[$i]))
							{
								$grouped[$v]=$unfilteredData[$i];
								$v++;
							}
						}
					}
				}
			
			
			//Теперь берем только последнюю поездку
			$grouped=array_reverse($grouped);
			$z=count($grouped);
			if ($z >= 2)for ($i=1;$i<$z;$i++)unset($grouped[$i]);
			unset($encData);
			$encData=$grouped[0];
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
				$sevTurn[0] = 0;
				$sevAcc[0] = 0;
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
					
						//Высчитываем тип поворота через угловое ускорение.					
								if (($wAcc < 0.45) && ($wAcc >= 0)) {
									$sevTurn[$i] = 0;									
								} else 	if (($wAcc >= 0.45) && ($wAcc < 0.6))	{
									$sevTurn[$i] = 1;
								} else 	if (($wAcc >= 0.6) && ($wAcc < 0.75)){
									$sevTurn[$i] = 2;
								} else if ($wAcc >= 0.75) {
									$sevTurn[$i] = 3;
								}
								
								$deltaSpeed = $speed - $encData[$i-1]['speed'];
								$accel[$i] = $deltaSpeed/$deltaTime;
								
								//Высчитываем тип неравномерного движения (ускорение-торможение) через ускорение.
								if ($accel[$i]<-7.5) {
									$sevAcc[$i] = -3;
								} else if (($accel[$i]>=-7.5)&&($accel[$i]<-6)) {
									$sevAcc[$i] = -2;
								} else if (($accel[$i]>=-6)&&($accel[$i]<-4.5)) {
									$sevAcc[$i] = -1;
								} else if ($accel[$i]>5) {
									$sevAcc[$i] = 3;
								} else if (($accel[$i]>4)&&($accel[$i]<=5)){
									$sevAcc[$i] = 2;
								} else if (($accel[$i]>3.5)&&($accel[$i]<=4)) {
									$sevAcc[$i] = 1;
								} else if (($accel[$i]>=-4.5)&&($accel[$i]<=3.5)) {
									$sevAcc[$i] = 0;
								}
					
					
					
				}
				else 	
				{
					$sevAcc[$i] = 0;
					$sevTurn[$i] = 0;
					$wAcc = 0;
					$radius = 0;
				}
				$timeSum = 0;
				$sumSpeed = 0;


			}

		

		$k=0;
		$R = 6371; // km
		for ($i = 1; $i < $j; $i++)
		{
				$vg=1;
				$d = acos(sin($encData[$i]['lat'])*sin($encData[$i-1]['lat']) + cos($encData[$i]['lat'])*cos($encData[$i-1]['lat']) *  cos($encData[$i-1]['lng']-$encData[$i]['lng'])) * $R;
				if(($encData[$i]['utimestamp']-$encData[$i-1]['utimestamp']>300) || (($d)/($encData[$i]['utimestamp']-$encData[$i-1]['utimestamp']))>40)$vg=42;			
				$ret_arr[$k]['lat']=$encData[$i]['lat'];
				$ret_arr[$k]['lng']=$encData[$i]['lng'];
							if($vg!=42)
				{
				if($sevAcc[$i]!=0)
				{
					if($sevTurn[$i]==0)
					{
						if($sevAcc[$i]<0)
						{
							$ret_arr[$k]['type']=2;
							$ret_arr[$k]['weight']=$sevAcc[$i]*(-1);
						}
						else
						{
							$ret_arr[$k]['type']=1;
							$ret_arr[$k]['weight']=$sevAcc[$i];
						}
					}
					else
					{
						if($sevAcc[$i]<0)
						{
							if($sevAcc[$i]*(-1)>=$sevTurn[$i])
							{
									$ret_arr[$k]['type']=2;
									$ret_arr[$k]['weight']=$sevAcc[$i]*(-1);
							}
							else
							{
									$ret_arr[$k]['type']=3;
									$ret_arr[$k]['weight']=$sevTurn[$i];
							}
						}
						else
						{
							if($sevAcc[$i]>=$sevTurn[$i])
							{
									$ret_arr[$k]['type']=1;
									$ret_arr[$k]['weight']=$sevAcc[$i];
							}
							else
							{
									$ret_arr[$k]['type']=3;
									$ret_arr[$k]['weight']=$sevTurn[$i];
							}	
						}
					}
				}
				else if($sevAcc[$i]==0 && $sevSpeed[$i]==0)
				{
					
					if($sevTurn[$i]>0)
					{
						$ret_arr[$k]['type']=3;
						$ret_arr[$k]['weight']=$sevTurn[$i];
					}
					else
					{
						$ret_arr[$k]['type']=0;
						$ret_arr[$k]['weight']=0;
					}
				}
				else
				{
					if($sevSpeed[$i]>0)
					{
						$ret_arr[$k]['type']=4;
						$ret_arr[$k]['weight']=$sevSpeed[$i];
					}
					else
					{
						$ret_arr[$k]['type']=0;
						$ret_arr[$k]['weight']=0;
					}
				}
			}
			else
			{
				
				$ret_arr[$k]['type']=42;
				$ret_arr[$k]['weight']=42;
			}
						
				$k++;
		}
		if($k!=0)
		{
		
			$errortype=array('info'=>$k,'code'=>  0);
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

function feedBack($param)
{
	$title=$param['title'];
	$body=$param['body'];	
	$UID=NTI_Cookie_check();			
	if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
	$title=mysql_real_escape_string($title);
	$body=mysql_real_escape_string($body);
	if(strlen($title)>4 && strlen($body)>4)
	{
		mysql_query("INSERT into NTIFeedback (UID,Title,Body) values ('$UID','$title','$body')");
		$errortype=array('info'=>"Allok",'code'=>  0);
		$res=array('result'=>1,'error'=> $errortype);
		echo json_encode($res);	
		exit();
	}
	else
	{
		$errortype=array('info'=>"Transfered data is too short",'code'=>  51);
		$res=array('result'=>-1,'error'=> $errortype);
		echo json_encode($res);	
		exit();
	}	
}

function addQuest($param)
{
	$company=$param['company'];
	$age=$param['age'];
	$sex=$param['sex'];
	$stage=$param['skill'];
	$dtp=$param['dtp'];
	$autotype=$param['autotype'];
	$autopower=$param['autopower'];
	if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
	$UID=NTI_Cookie_check();		
	if($UID>0)
	{
		$company=mysql_real_escape_string($company);
		$age=mysql_real_escape_string($age);
		$sex=mysql_real_escape_string($sex);
		$stage=mysql_real_escape_string($stage);
		$dtp=mysql_real_escape_string($dtp);
		$autotype=mysql_real_escape_string($autotype);
		$autopower=mysql_real_escape_string($autopower);
		mysql_query("INSERT into NTIQuest (UID,Company,Age,Sex,Stage,Dtp,Autotype,Autopower) values ('$UID','$company','$age','$sex','$stage','$dtp','$autotype','$autopower')");
		
		$errortype=array('info'=>"Quest success",'code'=>  0);
		$res=array('result'=>1,'error'=> $errortype);
		echo json_encode($res);	
		exit();
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
