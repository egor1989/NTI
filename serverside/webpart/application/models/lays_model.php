<?php

class lays_model extends CI_Model {

	public function search($d) {

		$time1 = strtotime($d['t1']);
		$time2 = strtotime($d['t2']);	
		$total_runs = 0;
	
		$q = $this->db->query("SELECT * FROM NTIEntry where utimestamp > $time1 AND utimestamp < $time2 AND lat != 0 AND lng != 0 order by utimestamp");
		$n=0;
		//≈сли не было поездок за текущий период или не передано ни одной точки.
		if ($q->num_rows() > 0) {
			foreach($q->result() as $row) {
				$data[$n]['accx'] = $row->accx;
				$data[$n]['accy'] = $row->accy;
				$data[$n]['lat'] = $row->lat;
				$data[$n]['lng'] = $row->lng;
				$data[$n]['compass'] = $row->compass;
				$data[$n]['speed'] = $row->speed;
				$data[$n]['distance'] = $row->distance;
				$data[$n]['utimestamp'] = $row->utimestamp;
				$n++;
			}
		}
		else {
			echo "No data found.";
			return -1;
		}
		
		//√руппирование по поездкам
		
		$k = 0;
		$m = 0;
		$grouped[$k][$m]=$data[0];
		for ($i=1;$i<$n;$i++) {
			if ($data[$i]['utimestamp'] - $grouped[$k][$m]['utimestamp'] != 0) {
				if (($data[$i]['utimestamp'] - $grouped[$k][$m]['utimestamp'] < 300) && 
					(((sqrt(pow(($data[$i]['lat']-$grouped[$k][$m]['lat']),2) + pow(($data[$i]['lng']-$grouped[$k][$m]['lng']),2)))*200)/($data[$i]['utimestamp'] - $grouped[$k][$m]['utimestamp']) < 180)) {
					$m++;
					$grouped[$k][$m] = $data[$i];
				}
				else
				{
					$m = 0;
					$k++;
					$grouped[$k][$m] = $data[$i];
				}
			}
		}
		$total_runs = $k - 1;
		// онец группировани€ по поездкам
		
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
		
		for($m=0;$m<=$k;$m++)
		{
			echo "***"."<br>";
			unset($data);
			$data=$grouped[$m];
			$drivingScore = 0;
			$coef1 = 0.1;
			$coef2 = 0.2;
			$coef3 = 0.6;
			//$speedType = 0;
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
			$acc = 0;
			$j=count($data);
					
			for ($i = 1; $i < $j-1; $i++)
			{
				$typeTurn[0] = "normal point";
				$typeAcc[0] = "normal point";
				$sevTurn = 0;
				$sevAcc = 0;
				$sevSpeed = 0;
				$speed = $data[$i]['speed'];	
				$deltaTime = ($data[$i]['utimestamp'] - $data[$i-1]['utimestamp'])/1000;
			
				if ( ($data[$i]['lng']-$data[$i-1]['lng']) != 0  )
				{
				
					$turn[$i] = atan(($data[$i]['lat']-$data[$i-1]['lat'])/($data[$i]['lng']-$data[$i-1]['lng']));
	
					$turn[0] = 0;
					$deltaTurn = $turn[$i] - $turn[$i-1];
					$wAcc = abs($deltaTurn/(1000*$deltaTime));
										
					//¬ысчитываем тип поворота через угловое ускорение.					
					if (($wAcc < 4.5) && ($wAcc >= 0)) {
						$sevTurn = 0;
						echo $sevTurn." ";
					} else 	if (($wAcc >= 4.5) && ($wAcc < 6))	{
						$sevTurn = 1;
						echo $sevTurn." ";
					} else 	if (($wAcc >= 6) && ($wAcc < 7.5)){
			            $sevTurn = 2;
			            echo $sevTurn." ";
					} else if ($wAcc >= 7.5) {
						$sevTurn = 3;
						echo $sevTurn." ";
					}
					
					
					if (($speed > 90) && ($speed < 110)) {
						$speed1++;
					} else if (($speed > 110) && ($speed<130))	{
						$speed2++;
					} else	if ($speed > 130)	{
						$speed3++;
					}
					
					/*					
					$deltaSpeed = $speed - $data[$i-1]['speed'];
					$accel[$i] = $deltaSpeed/$deltaTime;
					if ($accel[$i]<-7.5) {
//						$sevAcc = -3;
//					  	$brake3++;
					} else if (($accel[$i]<-6)&&($accel[$i]>=-7.5)) {
//						$sevAcc = -2;
//						$brake2++;
					} else if (($accel[$i]<-4.5)&&($accel[$i]>=-6)) {
//						$sevAcc = -1;
//						$brake1++;
					} else if ($accel[$i]>5){
//						$sevAcc = 3;
//						$acc3++;
					} else if (($accel[$i]>4)&&($accel[$i]<=5)){
//						$sevAcc = 2;
//						$acc2++;
					} else if (($accel[$i]>3.5)&&($accel[$i]<=4)) {
//						$sevAcc = 1;
//						$acc1++;
					} else if (($accel[$i]>=-4.5)&&($accel[$i]<=3.5)){
//						$sevAcc = 0;
					}
					*/					
					
					$deltaSpeed = $speed - $data[$i-1]['speed'];
					$accel[$i] = $deltaSpeed/$deltaTime;
					//¬ысчитываем тип неравномерного движени€ (ускорение-торможение) через ускорение.
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
					
					//Ѕольшое количество проверок условий соотношени€ ускорений в текущей и прошлой точках.
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
							$acc2++;
						} else if ($sevAcc == -3) {
							$typeAcc[$i] = "brake3 started";
							$acc3++;
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
					
														
					//ѕосле поворота - нормальна€ точка.
					if (($typeTurn[$i-1] == "left turn finished") || ($typeTurn[$i-1] == "right turn finished") || (!isset($typeTurn[$i-1])) || ($speed == 0) ) {
						$typeTurn[$i] = "normal point";
					// ќтклонение > 0.5 - после нормальной точки начинаем поворот налево, либо продолжаем поворот налево после уже начатого, либо завершаем, если это был поворот направо.
					} else 	if ($deltaTurn > 0.5)   {
					    if ($typeTurn[$i-1] == "normal point") 
							$typeTurn[$i] = "left turn started";
						if (($typeTurn[$i-1] == "left turn started")||($typeTurn[$i-1] == "left turn continued")) 
							$typeTurn[$i] = "left turn continued";
						if (($typeTurn[$i-1] == "right turn started")||($typeTurn[$i-1] == "right turn continued")) {
							$typeTurn[$i] = "right turn finished";
							///////
						}
					// ќтклонение > 0.5 - после нормальной точки начинаем поворот направо, либо продолжаем поворот направо после уже начатого, либо завершаем, если это был поворот налево.
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
					// ќтклонение между -0.5 и 0.5 - после нормальной точки идет нормальна€, а после начатых поворотов налево или направо - продолженные повороты соответственно налево и направо.
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
			
				
			
				$color = "white";
				if ($sevAcc==1) 
					$color = "#c3eb0d";
				if ($sevAcc==2) 
					$color = "#0deb12";
				if ($sevAcc==3) 
					$color = "#0deb88";
				if ($sevAcc==-1) 
					$color = "#ebc10d";
				if ($sevAcc==-2) 
					$color = "#eb610d";
				if ($sevAcc==-3) 
					$color = "#eb0d1b";
	
			}
			//echo $acc1." ".$acc2." ".$acc3." ".$brake1." ".$brake2." ".$brake3." ".$turn1." ".$turn2." ".$turn3."<br>";
			$fullTime = ($data[$j - 1]['utimestamp'] - $data[0]['utimestamp']);
			$drivingScore = ($coef1 * ($speed1 + $turn1 + $acc1 + $brake1) + $coef2 * ($speed2 + $turn2 + $acc2 + $brake2) + $coef3 * ($speed3 + $turn3 + $acc3 + $brake3)) / ($fullTime);
			$total_time 	= 	$total_time 	+ 	$fullTime; 
			$total_score 	= 	$total_score 	+ 	$drivingScore;
			$total_turn1	=	$total_turn1	+	$turn1;
			$total_turn2	=	$total_turn2	+	$turn2;
			$total_turn3	=	$total_turn3	+	$turn3;
			$total_acc1		=	$total_acc1		+	$acc1;
			$total_acc2		=	$total_acc2		+	$acc2;
			$total_acc3		=	$total_acc3		+	$acc3;
			$total_brake1	=	$total_brake1	+	$brake1;
			$total_brake2	=	$total_brake2	+	$brake2;
			$total_brake3	=	$total_brake3	+	$brake3;
		}
		
		echo "<h3>ѕодробна€ статистика за период с ".$d['t1']." по ".$d['t2'].".</h3>";
		echo "<br>";
		if ($total_turn1 != 0)
			echo "Ћегких поворотов: ".$total_turn1."<br/>";
		if ($total_turn2 != 0)
			echo "Ќормальных поворотов: ".$total_turn2."<br/>";
		if ($total_turn3 != 0)			
			echo " рутых поворотов: ".$total_turn3."<br/>";
		if ($total_acc1 != 0)	
			echo "—лабых ускорений: ".$total_acc1."<br/>";
		if ($total_acc2 != 0)
			echo "—редних ускорений: ".$total_acc2."<br/>";
		if ($total_acc3 != 0)	
			echo "ћощных рывков: ".$total_acc3."<br/>";
		if ($total_brake1 != 0)	
			echo "—лабых торможений ".$total_brake1."<br/>";
		if ($total_brake2 != 0)			
			echo "Ќормальных торможений ".$total_brake2."<br/>";
		if ($total_brake3 != 0)		
			echo " рутых торможений ".$total_brake3."<br/>";
		echo "<hr>";
		$tt = $total_turn1+$total_turn2+$total_turn3;
		$ta = $total_acc1+$total_acc2+$total_acc3;
		$tb = $total_brake1+$total_brake2+$total_brake3;
		echo "¬сего поворотов: ".$tt."<br/>";
		echo "¬сего ускорений: ".$ta."<br/>";
		echo "¬сего торможений: ".$tb."<br/>";
		echo "<hr>";
		$ttime = $total_time/3600;
		echo "—овершено поездок: ".$total_runs."<br>";
		echo "«атрачено времени: ".$ttime." часов.<br/>";
		echo "¬аш общий счет : ".$total_score." очков.<br/>";
		
	}
}

?>
