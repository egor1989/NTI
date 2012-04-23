<div id="content" class="pageContent">
<h3 align="left">Введите границы интервала (в формате ГГГГ-ММ-ДД, например 2011-11-25)</h3>
	<br>
	<?php echo form_open('user/search/'.$linkid); ?>
	<p>
		<label for="time1">От:</label>
		<input type="text" name="t1" id="t1" />
	</p>
	<p>
		<label for="time2">До:</label>
		<input type="text" name="t2" id="t2" />
	</p>
	<p>
		<input type="submit" value="Поиск" />
	</p>
	<? echo form_close(); ?>
	<?
		if (isset($errortype)) {
			echo $errortype;
		}
	?>
	<hr>
	<br>
	<?
	
	if ((isset($is_set)) && ($is_set == 1)) {
		?><span style="color:red"><?echo $rtitle;?></span><br><?
		if ($total_turn1 != 0){?><span style="color:red">Легких поворотов: <?echo $total_turn1;?></span><br><?}
		if ($total_turn2 != 0){?><span style="color:red">Средних поворотов: <?echo $total_turn2;?></span><br><?}
		if ($total_turn3 != 0){?><span style="color:red">Крутых поворотов: <?echo $total_turn3;?></span><br><?}
		if ($total_acc1 != 0){?><span style="color:red">Легких ускорений: <?echo $total_acc1;?></span><br><?}
		if ($total_acc2 != 0){?><span style="color:red">Средних ускорений: <?echo $total_acc2;?></span><br><?}
		if ($total_acc3 != 0){?><span style="color:red">Резких ускорений: <?echo $total_acc3;?></span><br><?}
		if ($total_brake1 != 0){?><span style="color:red">Легких торможений: <?echo $total_brake1;?></span><br><?}
		if ($total_brake2 != 0){?><span style="color:red">Средних торможений: <?echo $total_brake2;?></span><br><?}
		if ($total_brake3 != 0){?><span style="color:red">Крутых торможений: <?echo $total_brake3;?></span><br><?}
		if ($total_prev1 != 0){?><span style="color:red">Слабых превышений: <?echo $total_prev1;?></span><br><?}
		if ($total_prev2 != 0){?><span style="color:red">Средних превышений: <?echo $total_prev2;?></span><br><?}
		if ($total_prev3 != 0){?><span style="color:red">Жестких превышений: <?echo $total_prev3;?></span><br><?}
		?><br><?
		if ($total_turns != 0){?><span style="color:red">Всего поворотов: <?echo $total_turns;?></span><br><?}
		if ($total_accs != 0){?><span style="color:red">Всего ускорений: <?echo $total_accs;?></span><br><?}
		if ($total_brakes != 0){?><span style="color:red">Всего торможений: <?echo $total_brakes;?></span><br><?}
		if ($total_excesses != 0){?><span style="color:red">Всего превышений: <?echo $total_excesses;?></span><br><?}
		?><br><?
		if ($total_trips != 0){?><span style="color:red">Всего поездок: <?echo $total_trips;?></span><br><?}
		if ($total_time != 0){?><span style="color:red">Затрачено времени: <?echo $total_time;?></span><br><?}
		if ($total_score != 0){?><span style="color:red">Общий счет: <?echo $total_score;?></span><br><?}
	}
	else {
		if ((isset($is_set)) && ($is_set == 0)) {?><span style="color:red">Данных за указанный период не найдено.</span><br><?}
	}
	?>
</div>
