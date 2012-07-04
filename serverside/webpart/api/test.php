<?error_reporting(0);
$dblocation = "localhost";  
$dbname = "NTI";  
$dbuser = "steph";  
$dbpasswd = "trinitro"; 
 $dbcnx= mysql_connect($dblocation, $dbuser, $dbpasswd);
if (!$dbcnx)    {      return 0;  }  
if (!mysql_select_db($dbname,$dbcnx) )    {      return 0;    }
function distance($lat1, $lon1, $lat2, $lon2) { 

  $theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  $dist = rad2deg($dist); 
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);
    return ($miles * 1.609344); 
}

$userCount=0;
$sql_get_all_users=mysql_query("Select Id from NTIUserDrivingTrack");
while ($row = mysql_fetch_array($sql_get_all_users)) 
{
	$UID[$userCount]=$row['Id'];
	$userCount++;
}
//Получили всех пользователей
//Теперь перебираем все точки
for($z=0;$z<$userCount;$z++)
{
	$ID=$UID[$z];
	$sql_select_track=mysql_query("Select Id,Lat,Lng,speed,utimestamp  from NTIUserDrivingEntry where DrivingID=$ID group by utimestamp order by utimestamp");
$Tot=0;
$k=0;
	while ($row = mysql_fetch_array($sql_select_track)) 
	{
	      $r[$k]['lat']=$row['Lat'];
	      $r[$k]['lng']=$row['Lng'];
	      $r[$k]['id']=$row['Id'];
	      $r[$k]['speed']=$row['speed'];
	      $r[$k]['utimestamp']=$row['utimestamp'];
	      $r[$k]['trn']=0;
          $k++;

	}

   for($i=1;$i<$k;$i++)
{
	$speed=$r[$i]['speed'];					
	$deltaTime=$r[$i]['utimestamp']-$r[$i-1]['utimestamp'];
	if($r[$i]['lng']!=$r[$i-1]['lng'])
	{
		$r[$i]['trn']=atan(( $r[$i]['lat']-$r[$i-1]['lat'])/($r[$i]['lng']-$r[$i-1]['lng']));
		$deltaTurn = 	$r[$i]['trn'] - $r[$i-1]['trn'];
		$wAcc = abs($deltaTurn/($deltaTime));
		$deltaSpeed = $speed -  $r[$i-1]['speed'];
		$accel = $deltaSpeed/$deltaTime;
		$idd= $r[$i]['id'];
		$sql_select_track=mysql_query("Update NTIUserDrivingEntry set Accel= $accel where Id=$idd");
	}
}

			
		}
		



?>
