<?php

class lays extends CI_Controller {

	public function index() {
		$new_data['rights'] = 0;
		
		$this->load->view('header', $new_data);
		$this->load->view('lays_view');
		$this->load->view('footer');
	}

	public function search() {
		$new_data['rights'] = 0;
		$this->load->model('lays_model');
		$dt = array(
			't1' => $this->input->post('t1'),
			't2' => $this->input->post('t2')
		);
		if (strtotime($dt['t1']) === FALSE) {
			$derr['errortype'] = "Неверный формат начального времени (должно быть в формате ГГГГ-ММ-ДД, например 2011-06-10 для 10 июня 2011 года)";
			$this->load->view('header', $new_data);
			$this->load->view('lays_view', $derr);
			$this->load->view('footer');
			return;
		} else if (strtotime($dt['t2']) === FALSE) {
			$derr['errortype'] = "Неверный формат конечного времени (должно быть в формате ГГГГ-ММ-ДД, например 2011-06-10 для 10 июня 2011 года)";
			$this->load->view('header', $new_data);
			$this->load->view('lays_view', $derr);
			$this->load->view('footer');
			return;
		} else if (strtotime($dt['t1']) > strtotime($dt['t2'])) {
			$isDataOk = 0;
			$ertype = "Ошибка: начальная дата больше конечной.";			
			return;
		} else if (strtotime($dt['t2']) > time()) {
			$derr['errortype'] = "Ошибка: начальная дата позже текущего момента.";
			$this->load->view('header', $new_data);
			$this->load->view('lays_view', $derr);
			$this->load->view('footer');
			return;
		}
		
		
		
		$data = $this->lays_model->search($dt);
				
		if ($data != -1) {
			$k = 0;
			$m = 0;
			$grouped[$k][$m]=$data[0];
			$n = count($data)-1;
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
			//Конец группирования по поездкам
			
			
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
			
			for($m=0;$m<$k;$m++)
			{
				unset($data);
				$unfilteredData=$grouped[$m];
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
				$acc = 0;
			
				//Выкидываем одну из соседних не отличающихся по LNG точек
				$w =0;
				for ($v=1;$v<count($unfilteredData)-1;$v++) {
					if ($unfilteredData[$v]['lng'] != $unfilteredData[$v-1]['lng'] ) {
						$data[$w] = $unfilteredData[$v-1];
						$w++;
					}
				}
				//Закончили выкидывать.
			
				if ($w!=0) {
							$j=count($data);
													$dss = 0;
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
						
						if ( ($data[$i]['lng']-$data[$i-1]['lng']) != 0  )
						{
						
							$turn[$i] = atan(($data[$i]['lat']-$data[$i-1]['lat'])/($data[$i]['lng']-$data[$i-1]['lng']));
			
							$turn[0] = 0;
							$deltaTurn = $turn[$i] - $turn[$i-1];
							$wAcc = abs($deltaTurn/($deltaTime));
							//echo $wAcc."<br>";						
							//Высчитываем тип поворота через угловое ускорение.					
							if (($wAcc < 0.45) && ($wAcc >= 0)) {
								$sevTurn = 0;
								//echo $sevTurn." ";
							} else 	if (($wAcc >= 0.45) && ($wAcc < 0.6))	{
								$sevTurn = 1;
								//echo $sevTurn." ";
							} else 	if (($wAcc >= 0.6) && ($wAcc < 0.75)){
								$sevTurn = 2;
								//echo $sevTurn." ";
							} else if ($wAcc >= 0.75) {
								$sevTurn = 3;
								//echo $sevTurn." ";
							}
							
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
									$speed1 = $speed1 + floor($dss/3);
									$dss = 0;
								
								} else if ($sevSpeed == 1) {

									$typeSpeed[$i] = "s1";
									$dss += $deltaTime;
								} else if ($sevSpeed == 2) {
									$typeSpeed[$i] = "s2";
									$speed1 = $speed1 + floor($dss/3);
									$dss = 0;
								} else if ($sevSpeed == 3) {
									$typeSpeed[$i] = "s3";
									$speed1 = $speed1 + floor($dss/3);
									$dss = 0;
								}
							} else if ($typeSpeed[$i-1] == "s2") {
								if ($sevSpeed == 0) {
									$typeSpeed[$i] = "normal point";
									$speed2 = $speed2 + floor($dss/3);
									$dss = 0;
								} else if ($sevSpeed == 1) {
									$typeSpeed[$i] = "s1";
									$speed2 = $speed2 + floor($dss/3);
									$dss = 0;
								} else if ($sevSpeed == 2) {
									$typeSpeed[$i] = "s2";
									$dss += $deltaTime;
								} else if ($sevSpeed == 3) {
									$typeSpeed[$i] = "s3";
									$speed2 += $dss;
									$dss = 0;
								}
							} else if ($typeSpeed[$i-1] == "s3") {
								if ($sevSpeed == 0) {
									$typeSpeed[$i] = "normal point";
									$speed3 = $speed3 + floor($dss/3);
									$dss = 0;
								} else if ($sevSpeed == 1) {
									$typeSpeed[$i] = "s1";
									$speed3 = $speed3 + floor($dss/3);
									$dss = 0;
								} else if ($sevSpeed == 2) {
									$typeSpeed[$i] = "s2";
									$speed3 = $speed3 + floor($dss/3);
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
					if(isset($data[$j - 1]['utimestamp']))	{
						$fullTime = ($data[$j - 1]['utimestamp'] - $data[0]['utimestamp']);
						if($fullTime!=0) {
							$drivingScore = ($coef1 * ($speed1 + $turn1 + $acc1 + $brake1) + $coef2 * ($speed2 + $turn2 + $acc2 + $brake2) + $coef3 * ($speed3 + $turn3 + $acc3 + $brake3)) / ($fullTime/3600);
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
							$total_prev1	=	$total_prev1	+	$speed1;
							$total_prev2	=	$total_prev2	+	$speed2;
							$total_prev3	=	$total_prev3	+	$speed3;
						}
					}
				}
			}
			$results['is_set'] = 1;
			$results['rtitle'] = "<h3>Подробная статистика за период с ".$dt['t1']." по ".$dt['t2'].".</h3><br>";
			$results['total_turn1'] 	= 	$total_turn1;
			$results['total_turn2'] 	= 	$total_turn2;
			$results['total_turn3'] 	= 	$total_turn3;
			$results['total_acc1'] 		= 	$total_acc1;
			$results['total_acc2'] 		= 	$total_acc2;
			$results['total_acc3'] 		= 	$total_acc3;
			$results['total_brake1'] 	= 	$total_brake1;
			$results['total_brake2'] 	= 	$total_brake2;
			$results['total_brake3'] 	= 	$total_brake3;
			$results['total_prev1'] 	= 	$total_prev1;
			$results['total_prev2'] 	= 	$total_prev2;
			$results['total_prev3'] 	= 	$total_prev3;
			$results['total_turns']		=	$total_turn1	+	$total_turn2	+	$total_turn3;
			$results['total_accs']		=	$total_acc1		+	$total_acc2		+	$total_acc3;
			$results['total_brakes']	=	$total_brake1	+	$total_brake2	+	$total_brake3;
			$results['total_excesses']	=	$total_prev1	+	$total_prev2	+	$total_prev3;
			$results['total_trips']		=	$total_runs;
			$results['total_time']		=	round($total_time/3600,2);
			$results['total_score']		=	floor($total_score);
			
			
		}
		else
		{
			$results['is_set'] = 0;
		}
		$this->load->view('header', $new_data);
		$this->load->view('lays_view', $results);
		$this->load->view('footer');
		
	}

}

?>