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
				if($qq[$k]['gps']['latitude']!=0 && $qq[$k]['gps']['longitude']!=0)
				{
				if($i>0)
				{
					
					if($qq[$k]['timestamp']-$ArrayEntry[$n][$i-1]->getTimestamp()<600)
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
			}
				$lat=mysql_real_escape_string($qq[$k]['gps']['latitude']);
				$lng=mysql_real_escape_string($qq[$k]['gps']['longitude']);
				$accx=mysql_real_escape_string($qq[$k]['acc']['x']);
				$accy=mysql_real_escape_string($qq[$k]['acc']['y']);
				$direction=mysql_real_escape_string($qq[$k]['gps']['direction']);
				$distance=mysql_real_escape_string($qq[$k]['gps']['distance']);
				$speed=mysql_real_escape_string($qq[$k]['gps']['speed']);
				$compass=mysql_real_escape_string($qq[$k]['gps']['compass']);
				$utimestamp=mysql_real_escape_string($qq[$k]['timestamp']);
				$str = "INSERT INTO NTIEntry (UID, accx, accy, distance, lat, lng, direction, compass, speed, utimestamp, FileId) VALUES ($UID, $accx, $accy, $distance, $lat, $lng, $direction, $compass, $speed, $utimestamp, $fileid)";
				mysql_query($str);
				$k++;
			}
			//print_r($ArrayEntry);
			//Разбили по времени
			//Теперь перебирем поездки и высчитываем данные 
			for($i=1;$i<=$n;$i++)
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
					$TimeStart=$ArrayEntry[$i][0]->getTimestamp();//Подходит под определение ближайшей
					$TimeEnd=$ArrayEntry[$i][0]->getTimestamp();//Хз может быть и перемешанно , пусть поищет
					$TotalDistance=0;
					//Теперь ищем начало и конец поездки 
					for($j=0;$j<count($ArrayEntry[$i]);$j++)
					{
						if($ArrayEntry[$i][$j]->getTimestamp()<$TimeStart)$TimeStart=$ArrayEntry[$i][$j]->getTimestamp();
						if($ArrayEntry[$i][$j]->getTimestamp()>$TimeEnd)$TimeEnd=$ArrayEntry[$i][$j]->getTimestamp();
						if($TotalDistance<$ArrayEntry[$i][$j]->getDistance())$TotalDistance=$ArrayEntry[$i][$j]->getDistance();
					}
					//Теперь ищем расстояние, которое проехали в поездке
					$result=mysql_query("SELECT * FROM `NTICoef` order by ID desc limit 1");
					while ($row = mysql_fetch_array($result)) 
					{
						$BrakeK = $row['BrakeK'];
						$AccK = $row['AccK'];
						$SpeedK = $row['SpeedK'];
						$TurnK = $row['TurnK'];
						$CoefID=$row['Id'];
					}
		
					if($TotalDistance<=0)$TotalDistance=1;
					$TypeAcc1Count =$acc1;
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
					$score_speed = 0.35*100*($TypeSpeed1Count*0.1+ $TypeSpeed2Count*0.25 +$TypeSpeed3Count*0.65)/($TotalDistance*$SpeedK) ;
					$score_turn =0.25*100*($TypeTurn1Count*0.1+ $TypeTurn2Count*0.25 +$TypeTurn3Count*0.65)/($TotalDistance*$TurnK) ;
					$score_brake =0.35*100*($TypeBrake1Count*0.1+ $TypeBrake2Count*0.25 +$TypeBrake3Count*0.65)/($TotalDistance*$BrakeK) ;
					$score_acc = 0.15*100*($TypeAcc1Count*0.1+ $TypeAcc2Count*0.25 +$TypeAcc3Count*0.65)/($TotalDistance*$AccK) ;
			
					$sql_insert_str="insert into NTIUserDrivingTrack(UID,TotalAcc1Count,TotalAcc2Count,TotalAcc3Count,TotalBrake1Count,TotalBrake2Count,TotalBrake3Count,TotalSpeed1Count,TotalSpeed2Count,TotalSpeed3Count,TotalTurn1Count,TotalTurn2Count,TotalTurn3Count,TimeStart,TimeEnd,TotalDistance,SpeedScore,	TurnScore,BrakeScore,AccScore,CurrentKCoefID) values ('$UID','$TypeAcc1Count','$TypeAcc2Count','$TypeAcc3Count','$TypeBrake1Count','$TypeBrake2Count','$TypeBrake3Count','$TypeSpeed1Count','$TypeSpeed2Count','$TypeSpeed3Count','$TypeTurn1Count','$TypeTurn2Count','$TypeTurn3Count','$TimeStart','$TimeEnd','$TotalDistance','$score_speed','$score_turn','$score_brake','$score_acc','$CoefID')";
					
					mysql_query($sql_insert_str);
					$TrackID = mysql_insert_id();
					//Теперь заносим эти же данные в EntrRide
					
					for($j=0;$j<count($ArrayEntry[$i]);$j++)
					{
						$accx=$ArrayEntry[$i][$j]->getAccx();
						$accy=$ArrayEntry[$i][$j]->getAccy();					
						$distance=$ArrayEntry[$i][$j]->getDistance();
						$lat=$ArrayEntry[$i][$j]->getLat();
						$lng=$ArrayEntry[$i][$j]->getLng();
						$direction=$ArrayEntry[$i][$j]->getDirection();
						$compass=$ArrayEntry[$i][$j]->getCompass();
						$speed=$ArrayEntry[$i][$j]->getSpeed();
						$utimestamp=$ArrayEntry[$i][$j]->getTimestamp();
						$DrivingID=$TrackID;
						$Blat=0;
						$Blng=0;
						$sevAcc=$ArrayEntry[$i][$j]->getsevAcc();
						$TypeAcc=$ArrayEntry[$i][$j]->getTypeAcc();
						$sevTurn=$ArrayEntry[$i][$j]->getsevTurn();
						$TurnType=$ArrayEntry[$i][$j]->getTurnType();
						$TypeSpeed=$ArrayEntry[$i][$j]->getTypeSpeed();
						$sevSpeed=$ArrayEntry[$i][$j]->getsevSpeed();				
						$sql_insert_str="insert into NTIUserDrivingEntry(UID,accx,accy,distance,lat,lng,direction,compass,speed,utimestamp,DrivingID,Blat,Blng,sevAcc,TypeAcc,sevTurn,TurnType,TypeSpeed,sevSpeed) values ('$UID','$accx','$accy','$distance','$lat','$lng','$direction','$compass','$speed','$utimestamp','$DrivingID','$Blat','$Blng','$sevAcc','$TypeAcc','$sevTurn','$TurnType','$TypeSpeed','$sevSpeed')";
						mysql_query($sql_insert_str);

					}

				}
				//Если же ментше 50 - нахуй за борт
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
			
			//Для начала высчитываем общую статистику по поездкам региона	
			$result=mysql_query("SELECT sum((`TotalBrake1Count`*0.1+`TotalBrake2Count`*0.25+`TotalBrake3Count`*0.65)/`TotalDistance`)/count(*) as BrakeK,sum((`TotalAcc1Count`*0.1+`TotalAcc2Count`*0.25+`TotalAcc3Count`*0.65)/`TotalDistance`)/count(*) as AccK,sum((`TotalSpeed1Count`*0.1+`TotalSpeed2Count`*0.25+`TotalSpeed3Count`*0.65)/`TotalDistance`)/count(*) as SpeedK,sum((`TotalTurn1Count`*0.1+`TotalTurn2Count`*0.25+`TotalTurn3Count`*0.65)/`TotalDistance`)/count(*) as TurnK  FROM `NTIUserDrivingTrack` ");
			while ($row = mysql_fetch_array($result)) 
			{
				$BrakeK = $row['BrakeK'];
			        $AccK = $row['AccK'];
				$SpeedK = $row['SpeedK'];
				$TurnK = $row['TurnK'];
			}
			 $score_speed=0;
			 $score_turn=0;
			 $score_brake=0;
			 $score_acc=0;
			 $distance=0;
			 $total_score=0;
			 $time=0;
			if(isset($param['last']) && $param['last']>0)
			{
				$result=mysql_query("SELECT * FROM `NTIUserDrivingTrack` where UID=$UID order by Id DESC Limit 1");
				while ($row = mysql_fetch_array($result)) 
				{
					$score_speed = 0.35*100*($row['TotalSpeed1Count']*0.1+ $row['TotalSpeed2Count']*0.25 +$row['TotalSpeed3Count']*0.65)/($row['TotalDistance']*$SpeedK) ;
					$score_turn =0.25*100*($row['TotalTurn1Count']*0.1+ $row['TotalTurn2Count']*0.25 +$row['TotalTurn3Count']*0.65)/($row['TotalDistance']*$TurnK) ;
					$score_brake =0.35*100*($row['TotalBrake1Count']*0.1+ $row['TotalBrake2Count']*0.25 +$row['TotalBrake3Count']*0.65)/($row['TotalDistance']*$BrakeK) ;
					$score_acc = 0.15*100*($row['TotalAcc1Count']*0.1+ $row['TotalAcc2Count']*0.25 +$row['TotalAcc3Count']*0.65)/($row['TotalDistance']*$AccK) ;
					$distance= $row['TotalDistance'];
					$total_score = $score_speed +$score_turn+$score_brake+$score_acc;
					$time=$row['TimeStart'];
				}
				
			}
			else
			{
			
				$result=mysql_query("SELECT * FROM `NTIUserDrivingTrack` where UID=$UID ");
				$n=0;
				while ($row = mysql_fetch_array($result)) 
				{
					$score_speed += 0.35*100*($row['TotalSpeed1Count']*0.1+ $row['TotalSpeed2Count']*0.25 +$row['TotalSpeed3Count']*0.65)/($row['TotalDistance']*$SpeedK) ;
					$score_turn +=0.25*100*($row['TotalTurn1Count']*0.1+ $row['TotalTurn2Count']*0.25 +$row['TotalTurn3Count']*0.65)/($row['TotalDistance']*$TurnK) ;
					$score_brake +=0.35*100*($row['TotalBrake1Count']*0.1+ $row['TotalBrake2Count']*0.25 +$row['TotalBrake3Count']*0.65)/($row['TotalDistance']*$BrakeK) ;
					$score_acc += 0.15*100*($row['TotalAcc1Count']*0.1+ $row['TotalAcc2Count']*0.25 +$row['TotalAcc3Count']*0.65)/($row['TotalDistance']*$AccK) ;
					$distance+= $row['TotalDistance'];
					$time+=$row['TimeEnd']-$row['TimeStart'];
					$total_score += ($score_speed+$score_turn+$score_brake+$score_acc)*$distance;
					$n++;
				}
				if($n>0)
				{
					$score_speed=$score_speed;
					$score_turn=$score_turn;
					$score_brake=$score_brake;
					$score_acc=$score_acc;
					$total_score = ($total_score/$distance)/$n;
				}
				
			}
			$errortype=array('info'=>"",'code'=>  0);
			$score_speed=floor($score_speed);
			$score_turn=floor( $score_turn);
			$score_brake=floor($score_brake);
			$score_acc=floor($score_acc);
			$distance=floor($distance);
			$total_score=floor( $total_score);
			$ret=array('total_score'=>$total_score,'score_speed'=> $score_speed,'score_turn'=>$score_turn,'score_acc'=>$score_acc,'score_brake'=>$score_brake,'distance'=>$distance,'time'=>$time);
			$res=array('result'=>$ret,'error'=> $errortype);
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






function getPath($param)
{
	
	$time=$param['time'];
	$till=$param['till'];
	$day=$param['day'];
	$UID=NTI_Cookie_check();
	if($UID>0)
	{
		if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
		if(isset($param['time']) && !isset($param['till']) and $param['time']>0 && !isset($param['day']))
		{
		
			$start=mysql_real_escape_string($time);
			$query = "Select NTIUserDrivingEntry.* from (SELECT `Id` FROM `NTIUserDrivingTrack` WHERE UID=$UID and `TimeStart`>=$start) as Driving,NTIUserDrivingEntry where NTIUserDrivingEntry.`DrivingID`=Driving.Id group by `utimestamp`";
		}
		else if(isset($param['time']) && isset($param['till'])  and $param['time']>0  and $param['till']>0)
		{
				$start=mysql_real_escape_string($time);
				$end=mysql_real_escape_string($till);
				$query = "Select NTIUserDrivingEntry.* from (SELECT `Id` FROM `NTIUserDrivingTrack` WHERE UID=$UID and (`TimeStart`>=$start and $end >=`TimeStart` and $end<=`TimeEnd`) OR (`TimeStart`<=$start and $end<=`TimeEnd`) OR 	(`TimeStart`<=$start and $start <=`TimeEnd` and $end>=`TimeEnd`) OR (`TimeStart`>=$start and $end>=`TimeEnd`)) as Driving,NTIUserDrivingEntry where NTIUserDrivingEntry.`DrivingID`=Driving.Id group by `utimestamp`";
		}
		else if(isset($param['time']) && !isset($param['till'])  and $param['time']>0  && isset($param['day']) && $param['day']==1)
		{
			$start=strtotime(date("D M j 00:00:00 T Y",$time));
			$end= strtotime(date("D M j 23:59:59 T Y",$time));
			$query = "Select NTIUserDrivingEntry.* from (SELECT `Id` FROM `NTIUserDrivingTrack` WHERE UID=$UID and (`TimeStart`>=$start and $end >=`TimeStart` and $end<=`TimeEnd`) OR (`TimeStart`<=$start and $end<=`TimeEnd`) OR 	(`TimeStart`<=$start and $start <=`TimeEnd` and $end>=`TimeEnd`) OR (`TimeStart`>=$start and $end>=`TimeEnd`)) as Driving,NTIUserDrivingEntry where NTIUserDrivingEntry.`DrivingID`=Driving.Id group by `utimestamp`";

		}
		else
		{
				//иначе будем возвращать последнюю поездку
				$query = "Select NTIUserDrivingEntry.* from (SELECT `Id` FROM `NTIUserDrivingTrack` WHERE UID=$UID order by Id DESC Limit 1) as Driving,NTIUserDrivingEntry where NTIUserDrivingEntry.`DrivingID`=Driving.Id group by `utimestamp`";
		}
		$result = mysql_query($query);
		$n = 0;
		$curDrivingId=0;
		while ($row = mysql_fetch_array($result)) 
		{
			$ret_arr[$n]['lat'] = $row['lat'];
			$ret_arr[$n]['lng'] = $row['lng'];
				if($row['sevAcc']!=0)
				{
					if($row['sevTurn']==0)
					{
						if($row['sevAcc']<0)
						{
							$ret_arr[$n]['type']=2;
							$ret_arr[$n]['weight']=$row['sevAcc']*(-1);
						}
						else
						{
							$ret_arr[$n]['type']=1;
							$ret_arr[$n]['weight']=$row['sevAcc'];
						}
					}
					else
					{
						if($row['sevAcc']<0)
						{
							if($row['sevAcc']*(-1)>=$row['sevTurn'])
							{
									$ret_arr[$n]['type']=2;
									$ret_arr[$n]['weight']=$row['sevAcc']*(-1);
							}
							else
							{
									$ret_arr[$n]['type']=3;
									$ret_arr[$n]['weight']=$row['sevTurn'];
							}
						}
						else
						{
							if($row['sevAcc']>=$row['sevTurn'])
							{
									$ret_arr[$n]['type']=1;
									$ret_arr[$n]['weight']=$row['sevAcc'];
							}
							else
							{
									$ret_arr[$n]['type']=3;
									$ret_arr[$n]['weight']=$row['sevTurn'];
							}	
						}
					}
				}
				else if($row['sevAcc']==0 && $row['sevSpeed']==0)
				{
					
					if($row['sevTurn']>0)
					{
						$ret_arr[$n]['type']=3;
						$ret_arr[$n]['weight']=$row['sevTurn'];
					}
					else
					{
						$ret_arr[$n]['type']=0;
						$ret_arr[$n]['weight']=0;
					}
				}
				else
				{
					if($row['sevSpeed']>0)
					{
						$ret_arr[$n]['type']=4;
						$ret_arr[$n]['weight']=$row['sevTurn'];
					}
					else
					{
						$ret_arr[$n]['type']=0;
						$ret_arr[$n]['weight']=0;
					}
				}
		if(		$curDrivingId!=$row['DrivingId'])
		{
					$ret_arr[$n]['type']=42;
					$ret_arr[$n]['weight']=42;
					$curDrivingId=$row['DrivingId'];
		}
			$n++;
			
		}
	

		if($n!=0)
		{
		
			$errortype=array('info'=>$query,'code'=>  0);
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
