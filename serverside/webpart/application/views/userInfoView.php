﻿	<?if(isset($tickets) && $tickets != 0) {?> 
	Ваши заявки<br/>
		<table border=1>
		<?
			foreach ($tickets as $row)	
			{ 
				if($row['Status']==1)
				{
						if($row['Type']==0)
						{
								echo "<tr><td><a href=/user/search/".$row['Id'].">".$row['Login']."</a></td><td>".$row['FName']."</td><td>".$row['SName']."</td><td>Заявка на добавление </td><td>";
				
							echo form_open('/user/removeaccept/');
							echo form_hidden('userid', $row['Id']);
							echo form_submit('delete', 'Удалить заявку');
							echo form_close();
						}
						if($row['Type']==1)
						{
								echo "<tr><td><a href=/user/search/".$row['Id'].">".$row['Login']."</a></td><td>".$row['FName']."</td><td>".$row['SName']."</td><td>Заявка на добавление прав</td><td>";
				
							echo form_open('/user/removeacceptExpUseMap/');
							echo form_hidden('userid', $row['Id']);
							echo form_submit('delete', 'Удалить заявку');
							echo form_close();
						}
					echo"</td>  </tr>";	
				}
				if($row['Status']==2)
				{
							echo "<tr><td><a href=/user/search/".$row['Id'].">".$row['Login']."</a></td><td>".$row['FName']."</td><td>".$row['SName']."</td><td>Заявка на удаление </td><td> ";
							echo form_open('/user/removeaccept/');
							echo form_hidden('userid', $row['Id']);
							echo form_submit('delete', 'Удалить заявку');
							echo form_close();
						

					echo"</td>  </tr>";		
				}
			}
		?>
		</table>
		<?}?>


<?if ($users == -1) {
	?><span style="color:black;font-size: 12px;">На данный момент, у вас 0 (ноль) пользователей.</span><br>
<?}
else if ($users == 1) {
		?>
Ваши пользователи:<br/>
	
	<table border=1 >
		<tr>
			<?if($rights!=3){?>
			<td></td><?}?>
			<td>Логин</td>
			<td>Имя</td>
			<td>Фамилия</td>
			<td colspan=5>Статистика</td>
			<td>Поиск по датам</td>
		</tr>
	
	<?foreach ($retdata as $row){ ?>

		<tr>
			<?if($rights!=3){?>
			<td>
			<?
						echo form_open('/user/deleteaccept/');
						echo form_hidden('userid', $row['Id']);
						echo form_submit('delete', 'Удалить');
						echo form_close();
						?>
	
						
						
				</td>
						<?}?>

						
			<td><a href="/user/viewuser/<?echo $row['Id'];?>"><?echo $row['Login'];?></a>
			<?if( $row['Type']==0){?>
			<?
						echo form_open('/user/addacceptExpUseMap/');
						echo form_hidden('userid', $row['Id']);
						echo form_submit('rightsrequest','Запрос доступа');
						echo form_close();
						?>
				
			<?}?>	
			
			
			
			
			</td><td><?echo $row['FName'];?></td><td><?echo $row['SName'];?></td> 
				<?if($row['stats']['is_set']==1){?>
				
			<td style="width:250px;">
					<span style="color:green;font-size: 12px;">Легких поворотов: <?echo $row['stats']['total_turn1'];?></span><br>
					<span style="color:orange;font-size: 12px;">Средних поворотов: <?echo $row['stats']['total_turn2'];?></span><br>
					<span style="color:red;font-size: 12px;">Крутых поворотов: <?echo $row['stats']['total_turn3'];?></span><br>
		
			</td>
			<td style="width:250px;">
				<span style="color:green;font-size: 12px;">Легких ускорений: <?echo $row['stats']['total_acc1'];?></span><br>
				<span style="color:orange;font-size: 12px;">Средних ускорений: <?echo $row['stats']['total_acc2'];?></span><br>
				<span style="color:red;font-size: 12px;">Резких ускорений: <?echo $row['stats']['total_acc3'];?></span><br>
			</td>
			<td style="width:250px;">
				<span style="color:green;font-size: 12px;">Легких торможений: <?echo $row['stats']['total_brake1'];?></span><br>
				<span style="color:orange;font-size: 12px;">Средних торможений: <?echo $row['stats']['total_brake2'];?></span><br>
				<span style="color:red;font-size: 12px;">Крутых торможений: <?echo $row['stats']['total_brake3'];?></span><br>
			</td>
			<td style="width:250px;">
				<span style="color:green;font-size: 12px;">Слабых превышений: <?echo $row['stats']['total_prev1'];?></span><br>
				<span style="color:orange;font-size: 12px;">Средних превышений: <?echo $row['stats']['total_prev2'];?></span><br>
				<span style="color:red;font-size: 12px;">Жестких превышений: <?echo $row['stats']['total_prev3'];?></span><br>
			</td>
			<td style="width:250px;">
				<span style="font-size: 12px;">Всего поворотов: <?echo $row['stats']['total_turns'];?></span><br>
				<span style="font-size: 12px;">Всего ускорений: <?echo $row['stats']['total_accs'];?></span><br>
				<span style="font-size: 12px;">Всего торможений: <?echo $row['stats']['total_brakes'];?></span><br>
				<span style="color:red;font-size: 12px;">Всего превышений: <?echo $row['stats']['total_excesses'];?></span><br>
				
				<span style="font-size: 12px;">Затрачено времени: <?echo round($row['stats']['total_time']/3600,2);?> ч.</span><br>
				<span style="font-size: 12px;">Очки ускорений: <?echo ($row['stats']['total_acc_score']);?></span><br>
				<span style="font-size: 12px;">Очки торможений: <?echo ($row['stats']['total_brk_score']);?></span><br>
				<span style="font-size: 12px;">Очки поворотов: <?echo ($row['stats']['total_crn_score']);?></span><br>
				<span style="font-size: 12px;">Очки превышений: <?echo ($row['stats']['total_spd_score']);?></span><br>
				<span style="font-size: 12px;">Всего поездок: <?echo ($row['stats']['total_trips']);?></span><br>
				<span style="font-size: 12px;">Общий километраж: <?echo floor($row['stats']['total_dist']);?> км.</span><br>
				<span style="font-size: 12px;">Суммарный счет: <?echo ($row['stats']['total_all_score']);?></span><br>
			</td>
					<?}
					else{?>
					
					<td><span style="color:red">Данных не найдено</span><br>	</td>
					<td><span style="color:red">Данных не найдено</span><br>	</td>
					<td><span style="color:red">Данных не найдено</span><br>	</td>
					<td><span style="color:red">Данных не найдено</span><br>	</td>
					<td><span style="color:red">Данных не найдено</span><br>	</td>
					<?}?>
					</td><td><a href="/user/search/<?echo $row['Id'];?>">Найти</a></td></tr>
		<?}?>
	</table>
<?}?>
	

