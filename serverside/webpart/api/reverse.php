<html>
<head></head>
<body>
<?php
	
	
	$dd[0] = array(
			'd11' => "11",
			'd12' => "12",
			'd13' => "13"
		
		);
	$dd[1] = array(
			'd21' => "21",
			'd22' => "22",
			'd23' => "23"
		
		);
	$dd[2] = array(
			'd31' => "31",
			'd32' => "32",
			'd33' => "33"
		
		);
	print_r($dd);
	echo "<br>";
	$dd[2] = array_reverse($dd[2]);
	print_r($dd);
	echo "<br>";
	$dd = array_reverse($dd);
	print_r($dd);
	
?>
</body>
</html>