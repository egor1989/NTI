<?

function distance($lat1, $lon1, $lat2, $lon2) { 

  $theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  $dist = rad2deg($dist); 
  $miles = $dist * 60 * 1.1515;
    return ($miles * 1.609344); 
}

function dstBetweenPointsMet($lat1,$lon1,$lat2,$lon2)
{
	return 1000*(3958*3.1415926*sqrt(($lat2-$lat1)*($lat2-$lat1) + cos($lat2/57.29578)*cos($lat1/57.29578)*($lon2-$lon1)*($lon2-$lon1))/180);
}
$dblocation = "localhost";  
$dbname = "NTI";  
$dbuser = "goodroads";  
$dbpasswd = "123OLAcomrade"; 
 $dbcnx= mysql_connect($dblocation, $dbuser, $dbpasswd);
if (!$dbcnx)    {      return 0;  }  
if (!mysql_select_db($dbname,$dbcnx) )    {      return 0;    }
$IDD=mysql_real_escape_string($_GET["id"]);
$userCount=0;
	$sql_select_track=mysql_query("Select * from  NTIUserDrivingEntry  where DrivingID='$IDD' order by Id");
	$userTrackCount=0;
	$i=0;
	while ($row = mysql_fetch_array($sql_select_track)) 
	{
		$tracking[$i]['Id']=$row['Id'];
		$tracking[$i]['lat']=$row['lat'];
		$tracking[$i]['lng']=$row['lng'];
		$tracking[$i]['compass'] =$row['compass'];
		$tracking[$i]['utimestamp'] =$row['utimestamp'];
		$tracking[$i]['speed'] =$row['speed'];
		
		$tracking[$i]['turntype']=0;	

		$i++;
	}
	

					for($j=2;$j<$i;$j++)
					{
						//Сначала проверяем -  есть ли где-нибудь нули
						$dX1=$tracking[$j-1]['lat']-$tracking[$j-2]['lat'];
						$dY1=$tracking[$j-1]['lng']-$tracking[$j-2]['lng'];
						
						$dX2=$tracking[$j]['lat']-$tracking[$j-1]['lat'];
						$dY2=$tracking[$j]['lng']-$tracking[$j-1]['lng'];
						if($dX1==0)$k1=0;
						else
							$k1=$dY1/$dX1;
							
						if($dX2==0)$k2=0;
						else
							$k2=$dY2/$dX2;
							//Получили угол
						
						$Angle=rad2deg(atan(($k2-$k1)/(1+$k1*$k2)));
						$deltaTime=$tracking[$j]['utimestamp']-$tracking[$j-1]['utimestamp'];
						if(	$deltaTime==0)$deltaTime=1;
						
						
						
						$deltaTurn=$tracking[$j]['compass']-$tracking[$j-1]['compass'];
						if(abs($deltaTurn)<180);
						else if($deltaTurn<0)$deltaTurn=360+$deltaTurn;
						else
							$deltaTurn=360-$deltaTurn;
							$tracking[$j]['wAccG']=abs($Angle)/(1+abs($tracking[$j]['speed']-$tracking[$j-1]['speed']));
							$tracking[$j]['wAccC']=abs($deltaTurn)/(1+abs($tracking[$j]['speed']-$tracking[$j-1]['speed']));
						$tracking[$j]['Angle']=$Angle;
						$tracking[$j]['deltaTurn']=$deltaTurn;
						
							if(abs($deltaTurn)>12)
								if(abs($Angle)>10)$tracking[$j]['turntype']=1;
					}
					//Теперь добавим проверку на поворот
					for($j=2;$j<$i;$j++)
					{
						if($tracking[$j]['speed']<8 && $tracking[$j+1]['turntype']==1)
						{
							$tracking[$j]['turntype']=0;

						}

					    }
					for($j=2;$j<$i;$j++)
					{
						echo "S1=".$tracking[$j]['speed']."S2=".$tracking[$j-1]['speed']." WAccG=".$tracking[$j]['wAccG']." WaccC=".$tracking[$j]['wAccC']." GeomAngle=".$tracking[$j]['Angle'].".CompasDelta=".$tracking[$j]['deltaTurn']." compas1=".$tracking[$j]['compass']." compas2=".$tracking[$j-1]['compass']."==".$deltaTurn."<br/>";

						$id=$tracking[$j]['Id'];
					}
		

?>
