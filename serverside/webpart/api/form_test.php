<?php
 function testPassword($password)
	{
		if ( strlen( $password ) == 0 ) { return 1;}
		$strength = 0;
		$length = strlen($password);
		if(strtolower($password) != $password){$strength += 1;}
		if(strtoupper($password) == $password) {$strength += 1;}
		if($length >= 8 && $length <= 15){$strength += 1;}
		if($length >= 16 && $length <=35){$strength += 2;}
		if($length > 35){$strength += 3;}
		preg_match_all('/[0-9]/', $password, $numbers);
		$strength += count($numbers[0]);
		preg_match_all('/[|!@#$%&*\/=?,;.:\-_+~^\\\]/', $password, $specialchars);
		$strength += sizeof($specialchars[0]);
		$chars = str_split($password);
		$num_unique_chars = sizeof( array_unique($chars) );
		$strength += $num_unique_chars * 2;
		$strength = $strength > 99 ? 99 : $strength;
		$strength = floor($strength / 10 + 1);
		return $strength;
	}
	echo testPassword("123OLAcomrade");
?>
