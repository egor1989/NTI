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
					<span style="color:black"> Начало поездки:<br><?echo gmdate('d.m.Y H:i:s',$trr[$c]['TimeStart']); ?></span><br>
					<span style="color:black"> Конец  поездки:<br><?echo gmdate('d.m.Y H:i:s',$trr[$c]['TimeEnd']);?></span><br/>
					<?if(isset($rights) && $rights>=1){?>
					<a href="/user/raw/<?echo $trr[$c]['Id'];?>">Просмотреть данные</a><br/>
					<?}?>
					<?if(isset($rights) && $rights>=1){?>
					<a href="/map/viewdata/<?echo $trr[$c]['Id'];?>">Карта данных</a><br/>
										<table border=0>
						<tr>
					<td><span style="font-size:12px">K<sub>скор.</sub>=<?echo $trr[$c]['SpeedK'];?></span></td>
					<td><span style="font-size:12px">K<sub>пов.</sub>=<?echo $trr[$c]['TurnK'];?></span></td>
					
					</tr><tr>
					<td><span style="font-size:12px">K<sub>уск.</sub>=<?echo $trr[$c]['AccK'];?></span></td>
					<td><span style="font-size:12px">K<sub>торм.</sub>=<?echo $trr[$c]['BrakeK'];?>	</span></td>
					</tr>				
					</table>
					<?}?>
					
					
				</td>
				<td>	
				    <span style="color:green">Легких поворотов:</span> <?echo $trr[$c]['TotalTurn1Count'];?><br>
					<span style="color:orange">Средних поворотов:</span> <?echo $trr[$c]['TotalTurn2Count'];?><br>
					<span style="color:red">Крутых поворотов: </span><?echo $trr[$c]['TotalTurn3Count'];?><br>
				</td>
				<td>		
					<span style="color:green">Легких ускорений:</span> <?echo $trr[$c]['TotalAcc1Count'];?><br>
					<span style="color:orange">Средних ускорений:</span> <?echo $trr[$c]['TotalAcc2Count'];?><br>
					<span style="color:red">Резких ускорений: </span><?echo $trr[$c]['TotalAcc3Count'];?><br>
				</td>
				<td>
				    <span style="color:green">Легких торможений: </span><?echo $trr[$c]['TotalBrake1Count'];?><br>
					<span style="color:orange">Средних торможений:</span> <?echo $trr[$c]['TotalBrake2Count'];?><br>
					<span style="color:red">Крутых торможений:</span> <?echo $trr[$c]['TotalBrake3Count'];?><br>
				</td>
				<td>
					<span style="color:green">Слабых превышений: </span><?echo $trr[$c]['TotalSpeed1Count'];?><br>
					<span style="color:orange">Средних превышений: </span><?echo $trr[$c]['TotalSpeed2Count'];?><br>
					<span style="color:red">Жестких превышений:</span> <?echo $trr[$c]['TotalSpeed3Count'];?><br>
				</td>
				<td>
					<span >Километраж поездки: <?echo round($trr[$c]['total_dist'],2);?> км.</span><br>
					<span >Очки ускорений: <?echo ($trr[$c]['total_acc_score']);?></span><br>
					<span >Очки торможений: <?echo ($trr[$c]['total_brk_score']);?></span><br>
					<span>Очки поворотов: <?echo ($trr[$c]['total_crn_score']);?></span><br>
					<span >Очки превышений: <?echo ($trr[$c]['total_spd_score']);?></span><br>
					<span>Счет поездки: <?echo ($trr[$c]['total_all_score']);?></span><br>
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
