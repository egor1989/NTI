
<h3 align="left">Введите границы интервала (в формате MM-DD-YYYY)</h3>
	<br>
	<?php echo form_open('user/search/'.$linkid); ?>
	<table>
	<tr>
		<td>
			<p>
				<label for="time1">От:</label>
				<input type="text" name="t1" id="t1" class="dateField" />
			</p>
		<td>
			<p>
				<label for="time2">До:</label>
				<input type="text" name="t2" id="t2" class="dateField" />
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<p>
				<input type="submit" value="Поиск" />
			</p>
		</td>
		<td></td>
	</tr>
	</table>
	<? echo form_close(); ?>
	<?
		if (isset($errortype)) {echo $errortype;}
	?>
	
	<br>
	<?
	
	if ((isset($is_set)) && ($is_set == 1)) {?>
			<table border="1">
			<tr>
				<th>
					Время поездки
				</th>
				<th colspan=5>

					Результат поездки
				</th>
			</tr>
		<?
		for ($c=0;$c<$total_trips;$c++) {?>
			<tr>
				<td>
					<br>
					<span style="color:black"> Начало поездки:<br><?echo date('d.m.Y H:i:s',$trr[$c]['TimeStart']); ?></span><br><br>
					<span style="color:black"> Конец  поездки:<br><?echo date('d.m.Y H:i:s',$trr[$c]['TimeEnd']);?></span>
				</td>
				<td>	
				    <span style="color:red">Легких поворотов: <?echo $trr[$c]['TotalTurn1Count'];?></span><br>
					<span style="color:red">Средних поворотов: <?echo $trr[$c]['TotalTurn2Count'];?></span><br>
					<span style="color:red">Крутых поворотов: <?echo $trr[$c]['TotalTurn3Count'];?></span><br>
				</td>
				<td>		
					<span style="color:red">Легких ускорений: <?echo $trr[$c]['TotalAcc1Count'];?></span><br>
					<span style="color:red">Средних ускорений: <?echo $trr[$c]['TotalAcc2Count'];?></span><br>
					<span style="color:red">Резких ускорений: <?echo $trr[$c]['TotalAcc3Count'];?></span><br>
				</td>
				<td>
				    <span style="color:red">Легких торможений: <?echo $trr[$c]['TotalBrake1Count'];?></span><br>
					<span style="color:red">Средних торможений: <?echo $trr[$c]['TotalBrake2Count'];?></span><br>
					<span style="color:red">Крутых торможений: <?echo $trr[$c]['TotalBrake3Count'];?></span><br>
				</td>
				<td>
					<span style="color:red">Слабых превышений: <?echo $trr[$c]['TotalSpeed1Count'];?></span><br>
					<span style="color:red">Средних превышений: <?echo $trr[$c]['TotalSpeed2Count'];?></span><br>
					<span style="color:red">Жестких превышений: <?echo $trr[$c]['TotalSpeed3Count'];?></span><br>
				</td>
				<td>
					<span style="color:red">Километраж поездки: <?echo round($trr[$c]['total_dist'],2);?> км.</span><br>
					<span style="color:red">Очки ускорений: <?echo ($trr[$c]['total_acc_score']);?> </span><br>
					<span style="color:red">Очки торможений: <?echo ($trr[$c]['total_brk_score']);?> </span><br>
					<span style="color:red">Очки поворотов: <?echo ($trr[$c]['total_crn_score']);?> </span><br>
					<span style="color:red">Очки превышений: <?echo ($trr[$c]['total_spd_score']);?> </span><br>
					<span style="color:red">Счет поездки: <?echo ($trr[$c]['total_all_score']);?> </span><br>
				</td>
			</tr>
		<?}?>
		</table>
		<br>
			<?if (($trr['tscore'] != 0)&&($total_trips > 1)) {?>
				<span style="color:red">Средний счет последних <?echo $total_trips; ?> поездок: <?echo floor($trr['tscore']/$total_trips);?> points</span>
				<br>
				<span style="color:red">Суммарный счет последних <?echo $total_trips; ?> поездок: <?echo floor($trr['tscore']);?> points</span>
				<?	
				}

		}
	else {
		if ((isset($is_set)) && ($is_set != 1)) {?><span style="color:red">Данных за указанный период не найдено.</span><br><?}
	}
	?>

