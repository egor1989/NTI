<div id="content" class="pageContent">
       <p id="wait" style="position:absolute; border:2px solid #369; background-color:white; z-index:1000; margin:2em; padding:0.2em 1em; font-size:3em;">Loading...</p>
       <table>
       <tr>
		   <td>
        <div id="map" class="smallmap"></div>
        </td><td>
        <div id="docs">
            <p id=status style="border-top:1px solid #BBB; border-bottom:1px solid #BBB;">
            </p>
        </div>
         </td>
        </tr>
		</table>
		<table border="1">
			<tr>
				<th>
					Время поездки
				</th>
				<th colspan=5>

					Результат поездки
				</th>
			</tr>
			<tr>
				<td>
					<br>
					<span style="color:black"> Начало поездки:<br><?echo date('d.m.Y H:i:s',$trr['TimeStart']); ?></span><br>
					<span style="color:black"> Конец  поездки:<br><?echo date('d.m.Y H:i:s',$trr['TimeEnd']);?></span><br/>
					
				</td>
				<td>	
				    <span style="color:red">Легких поворотов: <?echo $trr['TotalTurn1Count'];?></span><br>
					<span style="color:red">Средних поворотов: <?echo $trr['TotalTurn2Count'];?></span><br>
					<span style="color:red">Крутых поворотов: <?echo $trr['TotalTurn3Count'];?></span><br>
				</td>
				<td>		
					<span style="color:red">Легких ускорений: <?echo $trr['TotalAcc1Count'];?></span><br>
					<span style="color:red">Средних ускорений: <?echo $trr['TotalAcc2Count'];?></span><br>
					<span style="color:red">Резких ускорений: <?echo $trr['TotalAcc3Count'];?></span><br>
				</td>
				<td>
				    <span style="color:red">Легких торможений: <?echo $trr['TotalBrake1Count'];?></span><br>
					<span style="color:red">Средних торможений: <?echo $trr['TotalBrake2Count'];?></span><br>
					<span style="color:red">Крутых торможений: <?echo $trr['TotalBrake3Count'];?></span><br>
				</td>
				<td>
					<span style="color:red">Слабых превышений: <?echo $trr['TotalSpeed1Count'];?></span><br>
					<span style="color:red">Средних превышений: <?echo $trr['TotalSpeed2Count'];?></span><br>
					<span style="color:red">Жестких превышений: <?echo $trr['TotalSpeed3Count'];?></span><br>
				</td>
				<td>
					<span style="color:red">Километраж поездки: <?echo round($trr['total_dist'],2);?> км.</span><br>
					<span style="color:red">Очки ускорений: <?echo floor($trr['total_acc_score']);?> points</span><br>
					<span style="color:red">Очки торможений: <?echo floor($trr['total_brk_score']);?> points</span><br>
					<span style="color:red">Очки поворотов: <?echo floor($trr['total_crn_score']);?> points</span><br>
					<span style="color:red">Очки превышений: <?echo floor($trr['total_spd_score']);?> points</span><br>
					<span style="color:red">Счет поездки: <?echo floor($trr['total_all_score']);?> points</span><br>
				</td>
			</tr>
		</table>
		
		
		
</div>
