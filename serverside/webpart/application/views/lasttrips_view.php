<div id="content" class="pageContent">
<?
	
	if ((isset($trr['total_trips']))&&($trr['total_trips']> 0)) {
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
		<?
		for ($c=0;$c<$trr['total_trips'];$c++) {?>
			<tr>
				<td>
					<br>
					<span style="color:black"> Начало поездки:<br><?echo date('d.m.Y H:i:s',$trr[$c]['TimeStart']); ?></span><br>
					<span style="color:black"> Конец  поездки:<br><?echo date('d.m.Y H:i:s',$trr[$c]['TimeEnd']);?></span><br/>
					<a href="/user/raw/<?echo $trr[$c]['Id'];?>">Просмотреть данные</a><br/>
					<?if(isset($rights) && $rights==1){?>
					<a href="/map/viewdata/<?echo $trr[$c]['Id'];?>">Карта данных</a><br/>
					<?}?>
					
					
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
					<span style="color:red">Очки ускорений: <?echo floor($trr[$c]['total_acc_score']);?> points</span><br>
					<span style="color:red">Очки торможений: <?echo floor($trr[$c]['total_brk_score']);?> points</span><br>
					<span style="color:red">Очки поворотов: <?echo floor($trr[$c]['total_crn_score']);?> points</span><br>
					<span style="color:red">Очки превышений: <?echo floor($trr[$c]['total_spd_score']);?> points</span><br>
					<span style="color:red">Счет поездки: <?echo floor($trr[$c]['total_all_score']);?> points</span><br>
				</td>
			</tr>
		<?}?>
		</table>
		<br>
			<?
			if (($trr['tscore'] != 0)&&($trr['total_trips'] > 1)) {
?>
								<span style="color:red">Суммарный счет последних <?echo $trr['total_trips']; ?> поездок: <?echo floor($trr['tscore']);?> points</span>
				<?
			}
		} 
		else if ((isset($trr['total_trips']))&&($trr['total_trips'] == -1)) {
		?>
		<h3>Нет данных по поездкам или данные еще не поступили.</h3>
	<?}
?>
</div>
