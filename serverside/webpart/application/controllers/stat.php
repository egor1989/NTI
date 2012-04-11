<?php

class stat extends CI_Controller {

	function index() {
		
		$this->load->model('statmodel');
		$t = $this->statmodel->display();
		$i = 0;
		$accel = 0;
		$isAccel = 0;
		$brake = 0;
		$isBrake = 0;
		$turns = 0;
		$isTurn = 0;
		
		for ($i = 0; $i < count($t)-1; $i++) {
			if (($t[$i+1]['utimestamp'] != $t[$i]['utimestamp']) || (abs($t[$i+1]['utimestamp']-$t[$i]['utimestamp']) > 10)) {
				if ( ($t[$i+1]['speed']*1000/3600 - $t[$i]['speed']*1000/3600)/($t[$i+1]['utimestamp']-$t[$i]['utimestamp']) >= 3.0 ) { 	//Если ускоряемся
					if ($isAccel == 0) 
					{
						$isBrake = 0;
						$isAccel = 1;
					}
				} else if (($t[$i+1]['speed']*1000/3600 - $t[$i]['speed']*1000/3600)/($t[$i+1]['utimestamp']-$t[$i]['utimestamp']) <= -3.0) { 	//Если тормозим
					if ($isBrake == 0) 
					{
						$isAccel = 0;
						$isBrake = 1;
					}
				} else {	//Если ровно едем (-3 < acc < 3)
					if ($isAccel == 1) { $isAccel = 0; $accel++; } //Если ускорялись, то прекращаем засекать ускорение  и увеличиваем счетчик ускорений .
					if ($isBrake == 1) { $isBrake = 0; $brake++; } //Если тормозили , то прекращаем засекать торможение и увеличиваем счетчик торможений.
				} 
			}
		}
		
		$udata['accels'] = $accel;
		$udata['brakes'] = $brake;
		$udata['turns'] = $turns;
		$this->load->view('trololo', $udata);
	}

}

?>