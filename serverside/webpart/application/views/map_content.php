<div id="content" class="pageContent">
       <p id="wait" style="position:absolute; border:2px solid #369; background-color:white; z-index:1000; margin:2em; padding:0.2em 1em; font-size:3em;">Loading...</p>
       <table width=100%>
       <tr>
		   <td width=80%>
        <div id="map" class="smallmap"></div>
        </td><td width=20%>
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
					<span style="color:black"> Начало поездки:<br><?date_default_timezone_set('Europe/Moscow'); echo date('d.m.Y H:i:s',$trr['TimeStart']); ?></span><br>
					<span style="color:black"> Конец  поездки:<br><?date_default_timezone_set('Europe/Moscow'); echo date('d.m.Y H:i:s',$trr['TimeEnd']);?></span><br/>
										<table border=0>
						<tr>
					<td><span style="font-size:12px">K<sub>скор.</sub>=<?echo $trr['SpeedK'];?></span></td>
					<td><span style="font-size:12px">K<sub>пов.</sub>=<?echo $trr['TurnK'];?></span></td>
					
					</tr><tr>
					<td><span style="font-size:12px">K<sub>уск.</sub>=<?echo $trr['AccK'];?></span></td>
					<td><span style="font-size:12px">K<sub>торм.</sub>=<?echo $trr['BrakeK'];?>	</span></td>
					</tr>				
					</table>
					
				</td>
				<td>	
				    <span style="color:green">Легких поворотов: </span><?echo $trr['TotalTurn1Count'];?><br>
					<span style="color:orange">Средних поворотов: </span><?echo $trr['TotalTurn2Count'];?><br>
					<span style="color:red">Крутых поворотов: </span><?echo $trr['TotalTurn3Count'];?><br>
				</td>
				<td>		
					<span style="color:green">Легких ускорений:</span> <?echo $trr['TotalAcc1Count'];?><br>
					<span style="color:orange">Средних ускорений:</span> <?echo $trr['TotalAcc2Count'];?><br>
					<span style="color:red">Резких ускорений: </span><?echo $trr['TotalAcc3Count'];?><br>
				</td>
				<td>
				    <span style="color:green">Легких торможений: </span><?echo $trr['TotalBrake1Count'];?><br>
					<span style="color:orange">Средних торможений: </span><?echo $trr['TotalBrake2Count'];?><br>
					<span style="color:red">Крутых торможений: </span><?echo $trr['TotalBrake3Count'];?><br>
				</td>
				<td>
					<span style="color:green">Слабых превышений:</span> <?echo $trr['TotalSpeed1Count'];?><br>
					<span style="color:orange">Средних превышений: </span><?echo $trr['TotalSpeed2Count'];?><br>
					<span style="color:red">Жестких превышений: </span><?echo $trr['TotalSpeed3Count'];?><br>
				</td>
				<td>
					<span>Километраж поездки: <?echo round($trr['total_dist'],2);?> км.</span><br>
					<span>Очки ускорений: <?echo floor($trr['total_acc_score']);?> </span><br>
					<span>Очки торможений: <?echo floor($trr['total_brk_score']);?> </span><br>
					<span>Очки поворотов: <?echo floor($trr['total_crn_score']);?> </span><br>
					<span >Очки превышений: <?echo floor($trr['total_spd_score']);?> </span><br>
					<span >Счет поездки: <?echo floor($trr['total_all_score']);?> </span><br>
				</td>
			</tr>
		</table>
		
		
		
</div>
