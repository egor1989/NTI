<div id="content" class="pageContent">
<?


	
	if ((isset($trr['total_trips']))&&($trr['total_trips'] > 0)) {
		switch($trr['total_trips']) {
			case 1: 
				echo "<h3>Последняя поездка:  </h3>"."<br>";
				break;
			
			default: 
				echo "<h3>Последние ".$trr['total_trips']." поездок:  </h3>"."<br>";
				break;
		}
		?>	
		<table border="1">
			<tr>
				<th>
					Время поездки
				</th>
				<th colspan=5>

					Результат поездки
				</th>
			</tr>
		<? for ($c=0;$c<$trr['total_trips'];$c++) {?>
			<tr>
				<td>
					<br>
					<span style="color:black"> Начало поездки:<br><?echo date('m.d.Y H:i:s',$trr[$c]['tstart']); ?></span><br><br>
					<span style="color:black"> Конец  поездки:<br><?echo date('m.d.Y H:i:s',$trr[$c]['tfinish']);?></span>
				</td>
				<td>
					
				    <span style="color:red">Легких поворотов: <?echo $trr[$c]['turn1'];?></span><br>
					<span style="color:red">Средних поворотов: <?echo $trr[$c]['turn2'];?></span><br>
					<span style="color:red">Крутых поворотов: <?echo $trr[$c]['turn3'];?></span><br>
				</td><td>		
						
						
					
						<span style="color:red">Легких ускорений: <?echo $trr[$c]['acc1'];?></span><br>
						<span style="color:red">Средних ускорений: <?echo $trr[$c]['acc2'];?></span><br>
						<span style="color:red">Резких ускорений: <?echo $trr[$c]['acc3'];?></span><br>
						</td><td>
						
						
						
					   <span style="color:red">Легких торможений: <?echo $trr[$c]['brake1'];?></span><br>
						<span style="color:red">Средних торможений: <?echo $trr[$c]['brake2'];?></span><br>
						<span style="color:red">Крутых торможений: <?echo $trr[$c]['brake3'];?></span><br>
						
						</td><td>
						
						
					
						<span style="color:red">Слабых превышений: <?echo $trr[$c]['prev1'];?></span><br>
						<span style="color:red">Средних превышений: <?echo $trr[$c]['prev2'];?></span><br>
						<span style="color:red">Жестких превышений: <?echo $trr[$c]['prev3'];?></span><br>
						</td><td>
						<span style="color:red">Время поездки: <?echo round($trr[$c]['time']/3600,2);?> ч.</span><br>
						<span style="color:red">Счет поездки: <?echo floor($trr[$c]['score']);?> points</span><br>
					
				</td>
			</tr>
		<?}?>
		</table>
		<br>
			<?
			if (($trr['tscore'] != 0)&&($trr['total_trips'] > 1)) {
?>
				<span style="color:red">Общий счет последних <?echo $trr['total_trips']; ?> поездок: <?echo floor($trr['tscore']);?> points</span><?
			}
		} 
		else if ((isset($trr['total_trips']))&&($trr['total_trips'] == 0)) {
		?>
		<h3>Нет данных по поездкам или данные еще не поступили.</h3>
	<?}
?>
</div>