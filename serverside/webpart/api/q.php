<?php
$dbcnx=0;

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
		
	if(connec_to_db()==0) {
		$errortype=array('info'=>"Cannot connect to DB",'code'=>  4);	
		$res=array('result'=>2,'error'=>  $errortype);	echo json_encode($res);	exit();	
	}

$k = 0;

$result = mysql_query("select Id,File from NTIFile where Id>35 and Id<45 order by Id");
while ($row = mysql_fetch_array($result)) {
	$data = $row['File'];
	echo $data;
}

/*
$entrys = json_decode($data,true);
for($i=0;$i<count($entrys);$i++)
{
	$ret_data[$k]['lat']=$entrys[$i]['gps']['latitude'];
	$ret_data[$k]['lng']=$entrys[$i]['gps']['longitude'];
	$ret_data[$k]['compass']=$entrys[$i]['gps']['compass'];
	$ret_data[$k]['speed']=$entrys[$i]['gps']['speed'];
	$ret_data[$k]['distance']=$entrys[$i]['gps']['distance'];
	$ret_data[$k]['utimestamp']=$entrys[$i]['timestamp'];
		
	
	$k++;
}
print_r($ret_data);
*/
?>