<?php
//ErrorCodes
define('DB_CONNECTION_REFUSE_CODE', 88);
define('DB_CONNECTION_REFUSE_INFO', "Database connection error");
define('JSON_ERROR_DEPTH_CODE', 1);
define('JSON_ERROR_DEPTH_INFO', "Maximum stack depth exceeded");
define('JSON_ERROR_CTRL_CHAR_CODE', 2);
define('JSON_ERROR_CTRL_CHAR_INFO', "Unexpected control character found");
define('JSON_ERROR_SYNTAX_CODE', 3);
define('JSON_ERROR_SYNTAX_INFO', "Syntax error, malformed JSON");
define('NO_METHOD_SET_CODE', 4);
define('NO_METHOD_SET_INFO', "No difintion state set, or function is incorrect");
define('NO_FUNCTION_SET_CODE', 5);
define('NO_FUNCTION_SET_INFO', "No action set, or function is incorrect");

$dbcnx=0;

class UserTrack {
	
private $id;
private $timestart;
private $timeend;

 function UserTrack($Id,$TimeStart,$TimeEnd) {
	$this->id=$Id;
	$this->timestart=$TimeStart;
	$this->timeend=$TimeEnd;	
 }
 //Incapsulation
 public function setId($Id){$this->id=$Id;}
 public function setTimeStart($TimeStart) {$this->timestart=$TimeStart;}
 public function setTimeEnd($TimeEnd){	$this->timeend=$TimeEnd; }
 public function getId(){return($this->id);}
 public function getTimeStart() {return($this->timestart);}
 public function getTimeEnd(){	return($this->timeend); }


}


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
 
 public function getLat(){return($this->lat);}
 public function getLng() {return($this->lng);}
 public function getCompass(){	return($this->compass); }
 public function getDirection(){	return($this->direction); }
 public function getTimestamp() {	return($this->timestamp); }
 public function getDistance() {	return($this->distance); }
 public function getSpeed() {	return($this->speed); }
 public function getAccx() {	return($this->accx); }
 public function getAccy() {	return($this->accy); }

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
private $wAcc;
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
  public function setwAcc($wAcc){$this->wAcc=$wAcc;}
 public function setAccel($Accel){$this->Accel=$Accel;}
  
 public function getsevAcc(){	return($this->sevAcc);}
 public function getTypeAcc(){	return($this->TypeAcc);}
 public function getsevTurn(){	return($this->sevTurn);}
 public function getTurnType(){	return($this->TurnType);}
 public function getTypeSpeed(){	return($this->TypeSpeed);}
 public function getsevSpeed(){	return($this->sevSpeed);}
 public function getTurn(){return($this->Turn);}
  public function getwAcc(){return($this->wAcc);}
 public function getAccel(){return($this->Accel);}

}


function distance($lat1, $lon1, $lat2, $lon2) { 

  $theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  $dist = rad2deg($dist); 
  $miles = $dist * 60 * 1.1515;
    return ($miles * 1.609344); 
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
	$dbuser = "goodroads";  
	$dbpasswd = "123OLAcomrade"; 
 $dbcnx= mysql_connect($dblocation, $dbuser, $dbpasswd);
if (!$dbcnx)    {      return 0;  }  
if (!mysql_select_db($dbname,$dbcnx) )    {      return 0;    }  
mysql_query('SET NAMES cp1251'); 
return 1;
}


$data=$_POST['data'];

if(isset($_POST['zip']))
{
	$zip=$_POST['zip'];
if($zip==1)
{
	$data=str_replace("<","",$data);
	$data=str_replace(">","",$data);
	$data=str_replace(" ","",$data);
	$data=gzdecodes(pack('H*',$data));
}
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
$data= iconv('cp1251', 'utf-8', $data);


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
else if($json['method']=="rememberPassword"){remember($json['params']);}//-
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

		$errortype=array('info'=>"You dont set mail,password,login",'code'=>  2);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		exit();
	}
	$result = mysql_query("SELECT Id from NTIUsers where Login='$username'");
	$cnt=mysql_num_rows($result);
	if(!$cnt==0)
	{

		$errortype=array('info'=>"User name already exists",'code'=>  3);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		
		exit();
	}
	$result = mysql_query("SELECT Id from NTIUsers where Email='$email'");
	$cnt=mysql_num_rows($result);
	if(!$cnt==0)
	{

		$errortype=array('info'=>"User mail already exists",'code'=>  4);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		exit();
	}
	if(strlen($password)<64)
	{
		
		$errortype=array('info'=>"Password check failed, seem to be not sha",'code'=>  5);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		exit();
	}
	
		if(strlen($username)>32 || strlen($email)>32 || strlen($name)>32 || strlen($surname)>32 )
	{
		
		$errortype=array('info'=>"Fields are too long. Must be less than 32 bytes",'code'=>  6);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		exit();
	}

			if(strlen($email)<3)
	{
		
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
		
		$errortype=array('info'=>"User doesnt exist",'code'=>  11);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		exit();
	}
	
	
	$result = mysql_query("SELECT Id from NTIUsers where Login='$username' and Password='$secret'");
	$cnt=mysql_num_rows($result);
	if($cnt==0)
	{
		
		$errortype=array('info'=>"Mismatch",'code'=>  12);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		exit();
	}
	else
	{
		$id=mysql_result($result,0);
	}

	$tm=time(); 
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
		//print_r($qq);
		//exit();
		if ($qq != NULL) 
		{

			if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
			mysql_query("INSERT into NTIFile (UID,File) values ('$UID','$ins')");
			$fileid = mysql_insert_id();
			$k = 0;
			//Начинаем обрабатывать и разбивать
			$i=0;
			$n=0;
			$json_size=count($qq);
			//echo "jsonsize;".$json_size;
			for($k=0;$k<$json_size;$k++)
			{
				if($qq[$k]['gps']['latitude']!=0 && $qq[$k]['gps']['longitude']!=0 && $qq[$k]['gps']['speed']>0)
				{
					//echo $k." ok";
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
			}
			//print_r($ArrayEntry);
			//Разбили по времени
			//Теперь перебирем поездки и высчитываем данные 
			//echo "\n\ncount:".$n."   ".count($ArrayEntry[1]);
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
				if(count($ArrayEntry[$i])>30)
				{
					$ArrayEntry[$i][0]->setsevAcc(0);
					$ArrayEntry[$i][0]->setsevTurn(0);
					$ArrayEntry[$i][0]->setsevSpeed(0);
					$ArrayEntry[$i][0]->setTurn(0);
					$ArrayEntry[$i][0]->setwAcc(0);
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
						$speed=($ArrayEntry[$i][$j]->getSpeed());

						$deltaTime=$ArrayEntry[$i][$j]->getTimestamp()-$ArrayEntry[$i][$j-1]->getTimestamp();
						if(	$deltaTime==0)$deltaTime=1;
						$deltaTurn=0;
						if($ArrayEntry[$i][$j]->getLng()-$ArrayEntry[$i][$j-1]->getLng()!=0)
						{
							$ArrayEntry[$i][$j]->setTurn(atan(($ArrayEntry[$i][$j]->getLat()-$ArrayEntry[$i][$j-1]->getLat())/($ArrayEntry[$i][$j]->getLng()-$ArrayEntry[$i][$j-1]->getLng())));
							$deltaTurn = 	$ArrayEntry[$i][$j]->getTurn() - $ArrayEntry[$i][$j-1]->getTurn();
							$wAcc = abs($deltaTurn/($deltaTime));
							if (($wAcc < 0.25) && ($wAcc >= 0)) {$ArrayEntry[$i][$j]->setsevTurn(0);} 
							else if (($wAcc >= 0.25) && ($wAcc < 0.5))	{$ArrayEntry[$i][$j]->setsevTurn(1);} 
							else if (($wAcc >= 0.5) && ($wAcc < 0.75)){$ArrayEntry[$i][$j]->setsevTurn(2);} 
							else if ($wAcc >= 0.75) {$ArrayEntry[$i][$j]->setsevTurn(3);}
							$ArrayEntry[$i][$j]->setwAcc($wAcc);
						}
						else
						{
							$ArrayEntry[$i][$j]->setTurn($ArrayEntry[$i][$j-1]->getTurn());		
							$ArrayEntry[$i][$j]->setwAcc($ArrayEntry[$i][$j-1]->getwAcc());	
						}
								$deltaSpeed = $speed/3.6 - ($ArrayEntry[$i][$j-1]->getSpeed())/3.6;
								
								$accel = $deltaSpeed/$deltaTime;
								if($accel==0)
								{
									$accel=sqrt($ArrayEntry[$i][$j]->getAccx()*$ArrayEntry[$i][$j]->getAccx()+$ArrayEntry[$i][$j]->getAccy()*$ArrayEntry[$i][$j]->getAccy())*9.8;
									if($ArrayEntry[$i][$j]->getCompass()>=180)$accel=(-1)*$accel;
								}
								$ArrayEntry[$i][$j]->setAccel($accel);
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
									if (($ArrayEntry[$i][$j-1]->getTurnType() == "right turn started")||($ArrayEntry[$i][$j-1]->getTurnType()== "right turn continued"))$ArrayEntry[$i][$j]->setTurnType( "right turn finished");
								}
								//if (($ArrayEntry[$i][$j]->getTurnType() == "left turn finished") || ($ArrayEntry[$i][$j]->getTurnType() == "right turn finished")) 
								//{
									switch ($ArrayEntry[$i][$j]->getsevTurn()) {
											case 1: {$turn1++;break;}
											case 2: {$turn2++;break;}
											case 3: {$turn3++;break;}
											case 0: {break;}
										}
								//}	
						




					}
					$TimeStart=$ArrayEntry[$i][0]->getTimestamp();//Подходит под определение ближайшей
					$TimeEnd=$ArrayEntry[$i][0]->getTimestamp();//Хз может быть и перемешанно , пусть поищет
					$TotalDistance=0;
					//Теперь ищем начало и конец поездки 
					$mid_speed=0;
					$preCount=count($ArrayEntry[$i])-1;
					for($j=0;$j<$preCount;$j++)
					{
						$mid_speed+=$ArrayEntry[$i][$j]->getSpeed();
						if($ArrayEntry[$i][$j]->getTimestamp()<$TimeStart)$TimeStart=$ArrayEntry[$i][$j]->getTimestamp();
						if($ArrayEntry[$i][$j]->getTimestamp()>$TimeEnd)$TimeEnd=$ArrayEntry[$i][$j]->getTimestamp();
						if($TotalDistance<$ArrayEntry[$i][$j]->getDistance())$TotalDistance=$ArrayEntry[$i][$j]->getDistance();
						$TotalDistance+=distance($ArrayEntry[$i][$j]->getLat(),$ArrayEntry[$i][$j]->getLng(),$ArrayEntry[$i][$j+1]->getLat(),$ArrayEntry[$i][$j+1]->getLng());
					}
					if(count($ArrayEntry[$i])>0)
					{
						$mid_speed=$mid_speed/count($ArrayEntry[$i]);
					}
					if($TotalDistance<=0)$TotalDistance=1;
					$DTime=$TimeEnd-$TimeStart;
					if($DTime==0)$DTime=1;
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
					//Calculation Fi
					$FAcc1=$acc1/$DTime;
					$FAcc2=$acc2/$DTime;
					$FAcc3=$acc3/$DTime;
					$Fturn1=$turn1/$DTime;
					$Fturn2=$turn2/$DTime;
					$Fturn3=$turn3/$DTime;
					$FSpeed1=$speed1/$DTime;
					$FSpeed2=$speed2/$DTime;
					$FSpeed3=$speed3/$DTime;
					$FBrake1=$brake1/$DTime;
					$FBrake2=$brake2/$DTime;
					$FBrake3=$brake3/$DTime;
					//Kvn
					$KvnA=($FAcc1*0.1+ $FAcc2*0.25 +$FAcc3*0.65);
					$KvnS=($FSpeed1*0.1+ $FSpeed2*0.25 +$FSpeed3*0.65);
					$KvnT=($Fturn1*0.1+ $Fturn2*0.25 +$Fturn3*0.65);
					$KvnB=($FBrake1*0.1+ $FBrake2*0.25 +$FBrake3*0.65);
					//Теперь считаем Q польз
					//
					$Qa=0;
					$Qs=0;
					$Qt=0;
					$Qb=0;
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
					FROM NTIUserDrivingTrack group by UID) as a");
					while ($row = mysql_fetch_array($result)) 
					{
						$Qa=$row['Fau'];
						$Qs=$row['Fsu'];
						$Qt=$row['Ftu'];
						$Qb=$row['Fbu'];
					}
					if($Qa==0)$Qa=1;
					if($Qs==0)$Qs=1;
					if($Qt==0)$Qt=1;
					if($Qb==0)$Qb=1;
					
					$Kua=1/sqrt(1+$KvnA/$Qa);
					$Kub=1/sqrt(1+$KvnB/$Qb);
					$Kus=1/sqrt(1+$KvnS/$Qs);
					$Kut=1/sqrt(1+$KvnT/$Qt);
					if(($acc1+$acc2+$acc3)==0)
					{
						$Kua=0;
					}	
					if(($brake1+$brake2+$brake3)==0)
					{
						$Kub=0;
					}	
					if(($speed1+$speed2+$speed3)==0)
					{
						$Kus=0;
					}	
					if(($turn1+$turn2+$turn3)==0)
					{
						$Kut=0;
					}			
					$score=0.10*$Kua+0.35*$Kub+0.30*$Kus+0.25*$Kut;
					$score_speed =$Kus;
					$score_turn = $Kut;
					$score_brake =$Kub ;
					$score_acc = $Kua ;
					$rt=mysql_query("SELECT * FROM `NTIUserDrivingTrack` where UID=$UID and TimeStart<=$TimeStart and TimeEnd>=$TimeEnd");
					//Убираем этим запрос дубляж
					
					$cnt=mysql_num_rows($rt);
					if($cnt>0)
					{
							$errortype=array('info'=>"Already exist",'code'=>  6);
							$res=array('result'=>-1,'error'=> $errortype);
							echo json_encode($res);	

							exit();
					}
					
                     if(($KvnA>0  && $KvnB>0) || ($mid_speed>10))
					{
						//Отлично значит поездка нормальна
						//Попробуем её объеденить с поездками этого же пользователя.
						//Поездки объединяются , если время между началом и стартом их не более 10 мин, либо одна лежит внутри другой.
						$sql_insert_str="insert into NTIUserDrivingTrack(UID,TotalAcc1Count,TotalAcc2Count,TotalAcc3Count,TotalBrake1Count,TotalBrake2Count,TotalBrake3Count,TotalSpeed1Count,TotalSpeed2Count,TotalSpeed3Count,TotalTurn1Count,TotalTurn2Count,TotalTurn3Count,TimeStart,TimeEnd,TotalDistance,SpeedScore,	TurnScore,BrakeScore,AccScore,TotalScore,SpeedK,AccK,BrakeK,TurnK) values ('$UID','$TypeAcc1Count','$TypeAcc2Count','$TypeAcc3Count','$TypeBrake1Count','$TypeBrake2Count','$TypeBrake3Count','$TypeSpeed1Count','$TypeSpeed2Count','$TypeSpeed3Count','$TypeTurn1Count','$TypeTurn2Count','$TypeTurn3Count','$TimeStart','$TimeEnd','$TotalDistance','$score_speed','$score_turn','$score_brake','$score_acc','$score','$KvnS','$KvnA','$KvnB','$KvnT')";
						mysql_query($sql_insert_str);
						$TrackID = mysql_insert_id();

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
							$accl=$ArrayEntry[$i][$j]->getAccel();	
							$waccl=$ArrayEntry[$i][$j]->getwAcc();
							$sql_insert_str="insert into NTIUserDrivingEntry(Accel,UID,accx,accy,distance,lat,lng,direction,compass,speed,utimestamp,DrivingID,Blat,Blng,sevAcc,TypeAcc,sevTurn,TurnType,TypeSpeed,sevSpeed,wAcc) values ('$accl','$UID','$accx','$accy','$distance','$lat','$lng','$direction','$compass','$speed','$utimestamp','$DrivingID','$Blat','$Blng','$sevAcc','$TypeAcc','$sevTurn','$TurnType','$TypeSpeed','$sevSpeed','$waccl')";
							mysql_query($sql_insert_str);
						}
					}
				}
				//Если же ментше 50 - нахуй за борт
			}

			$errortype=array('info'=>"all ok",'code'=>  0);
			$res=array('result'=>1,'error'=> $errortype);
			echo json_encode($res);	

			exit();
		}
		else
		{
			$errortype=array('info'=>"Data is not in json",'code'=>  4);
			$res=array('result'=>-1,'error'=> $errortype);
			echo json_encode($res);	
			exit();	
		}
	}
	else
	{						
		$errortype=array('info'=>"File is too small or empty",'code'=>  5);
		$res=array('result'=>-1,'error'=> $errortype);
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
					$score_speed = 100*$row['SpeedScore'];
					$score_turn =100*$row['TurnScore'];
					$score_brake =100*$row['BrakeScore'];
					$score_acc = 100*$row['AccScore'];
					$distance= $row['TotalDistance'];
					$total_score = 100*$row['TotalScore'];
					$time=$row['TimeStart'];
				}
				
			}
			else
			{
			
				$result=mysql_query("SELECT * FROM `NTIUserDrivingTrack` where UID=$UID ");
				$n=0;
				while ($row = mysql_fetch_array($result)) 
				{
					$score_speed += 100*$row['SpeedScore'];
					$score_turn +=100*$row['TurnScore'];
					$score_brake +=100*$row['BrakeScore'];
					$score_acc += 100*$row['AccScore'];
						$distance+= $row['TotalDistance'];
					$time+=$row['TimeEnd']-$row['TimeStart'];
					$total_score += 100*$row['TotalScore'];
					$n++;
				}
				if($n>0)
				{
					$score_speed=$score_speed/$n;
					$score_turn=$score_turn/$n;
					$score_brake=$score_brake/$n;
					$score_acc=$score_acc/$n;
					$total_score = ($total_score)/$n;
				}
				
			}
			$errortype=array('info'=>"",'code'=>  0);
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




function trimUTF8BOM($data){
    if(substr($data, 0, 3) == pack('CCC', 239, 187, 191)) {
        return substr($data, 3);
    }
    return $data;
}

function getPath($param)
{
	
	$time=$param['time'];
	$till=$param['till'];
	$day=$param['day'];

	$UID=NTI_Cookie_check();

        if($UID>0)
	{
		//if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
		if(isset($param['time']) && !isset($param['till']) and $param['time']>0 && !isset($param['day']))
		{
		
			$start=mysql_real_escape_string($time);
			$query = "Select NTIUserDrivingEntry.* from (SELECT `Id` FROM `NTIUserDrivingTrack` WHERE UID=$UID and `TimeStart`>=$start) as Driving,NTIUserDrivingEntry where NTIUserDrivingEntry.`DrivingID`=Driving.Id group by `utimestamp`";
		}
		else if(isset($param['time']) && isset($param['till'])  and $param['time']>0  and $param['till']>0)
		{
				$start=mysql_real_escape_string($time);
				$end=mysql_real_escape_string($till);
				$query = "Select NTIUserDrivingEntry.* from (SELECT `Id` FROM `NTIUserDrivingTrack` WHERE UID=$UID and ((`TimeStart`>=$start and $end >=`TimeStart` and $end<=`TimeEnd`) OR (`TimeStart`<=$start and $end<=`TimeEnd`) OR 	(`TimeStart`<=$start and $start <=`TimeEnd` and $end>=`TimeEnd`) OR (`TimeStart`>=$start and $end>=`TimeEnd`))) as Driving,NTIUserDrivingEntry where NTIUserDrivingEntry.`DrivingID`=Driving.Id group by `utimestamp`";
		}
		else if(isset($param['time']) && !isset($param['till'])  and $param['time']>0  && isset($param['day']) && $param['day']>0)
		{
			$start=strtotime(date("D M j 00:00:00 T Y",$time));
			$end= strtotime(date("D M j 23:59:59 T Y",$time));
			$query = "Select NTIUserDrivingEntry.* from (SELECT `Id` FROM `NTIUserDrivingTrack` WHERE UID=$UID and ((`TimeStart`>=$start and $end >=`TimeStart` and $end<=`TimeEnd`) OR (`TimeStart`<=$start and $end<=`TimeEnd`) OR 	(`TimeStart`<=$start and $start <=`TimeEnd` and $end>=`TimeEnd`) OR (`TimeStart`>=$start and $end>=`TimeEnd`))) as Driving,NTIUserDrivingEntry where NTIUserDrivingEntry.`DrivingID`=Driving.Id group by `utimestamp`";

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
				if(	$curDrivingId!=$row['DrivingID'])
				{

					$ret_arr[$n]['type']=42;
					$ret_arr[$n]['weight']=42;

					$curDrivingId=$row['DrivingID'];
				}
				$n++;
			
		}
	

		if($n!=0)
		{


			$errortype=array('info'=>"",'code'=>  0);
			$res=array('result'=>$ret_arr,'error'=> $errortype);

			echo gzencode(json_encode($res));
			exit();
		}	
		else
		{

			$errortype=array('info'=>"There is no data with such time($end $start)",'code'=>  32);
			$res=array('result'=>-1,'error'=> $errortype);
			echo gzencode(json_encode($res));	
			exit();

		}
	}
	else
	{

		$errortype=array('info'=>"Connection broken or you are not authorized",'code'=>  33);
		$res=array('result'=>-1,'error'=> $errortype);
		echo gzencode(json_encode($res));		
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


function remember($params)
{
	if(connec_to_db()==0){$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	}
	$login=mysql_real_escape_string($params['login']);
	$result = mysql_query("SELECT Email,Id from NTIUsers where Login='$login'");
	$cnt=mysql_num_rows($result);
	if($cnt==0)
	{
		$errortype=array('info'=>"User doesnt exist",'code'=>  62);
		$res=array('result'=>0,'error'=>  $errortype);
		echo json_encode($res);
		exit();
	}
	$row=mysql_fetch_row($result);
	$email=$row[0];
	$uid=$row[1];
	
	$errortype=array('info'=>"",'code'=>  0);
	$res=array('result'=>1,'error'=>  $errortype);
	
	$result = mysql_query("Update PasswordRecovery set Deleted=1 where UserId=$uid");
	$key=rand_str();
	$unixtime=time();
	$result = mysql_query("INSERT into PasswordRecovery (Key,UserId,Deleted,UnixTimeStamp) VALUES ('$key','$uid',0,'$unixtime')");	
	$to      = $email;
	$subject = 'Password recovery';
	$message = 'Link for password recovery';
	$message .="<a href=\"http://nti.goodroads.ru/remember/passwordrecovery/$key\">Nti.goodroads.ru</a>";
	$headers = 'From: pr@goodroads.ru' . "\r\n" .
    'Reply-To: pr@goodroads.ru' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
	mail($to, $subject, $message, $headers);
	echo json_encode($res);
	exit();
		
}

?>
