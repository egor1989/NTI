	<?if(isset($tickets) && $tickets != 0) {?> 
	Ваши заявки<br/>
		<table border=1>
		<?
			foreach ($tickets as $row)	
			{ 
				if($row['Status']==1)
				{
					echo "<tr><td><a href=/user/search/".$row['Id'].">".$row['Login']."</a></td><td>".$row['FName']."</td><td>".$row['SName']."</td><td>Заявка на добавление </td><td>";

						echo form_open('/user/removeaccept/');
						echo form_hidden('userid', $row['Id']);
						echo form_submit('delete', 'Удалить заявку');
						echo form_close();

					
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
Пользователи системы 

<table border=1>
	
		<?if($retdata!=false)
		
		if($rights==3)
		{
		
		foreach ($retdata as $row){?>
				<?
				if($row['Rights']==1)$type="<span style='font-size:10px'>Данные подтверждены</span>";
				if($row['Rights']==0)$type="<span style='font-size:10px'>Не подтверждено</span>";
				if($row['Rights']==2)$type="<span style='font-size:10px'>Эксперт системы</span>";
				?>
				<tr><td><a href="/user/viewuser/<?echo $row['Id'];?>"><?echo $row['Login'];?></a></td><td><?echo $row['FName'];?></td><td><?echo $row['SName'];?></td> <td><?echo $type;?></td>
				
				<td>
				<?
				if($row['Deleted']==0)
				{
					echo form_open('/admin/banuser/');
					echo form_hidden('userid', $row['Id']);
					echo form_submit('bann', 'Ban');
					echo form_close();
				}
				else
				{
					echo form_open('/admin/unbanuser/');
					echo form_hidden('userid', $row['Id']);
					echo form_submit('bann', 'UnBan');
					echo form_close();
				}
				?>
				</td>
				<td>
				<?
				if($row['Deleted']==0) {
					echo form_open('/admin/chrights/');
					echo form_hidden('userid', $row['Id']);
					$attr = array('name'=>'nrights', 'value'=>'', 'size'=>'1', 'maxlength'=>'1');
					echo form_input($attr);
					echo form_submit('newrights', 'Изменить права');
					echo form_close();
				}
				?>	
				</td>
				<td>
				<?
				if($row['Deleted']==0) {
					echo form_open('/admin/chpassword/');
					echo form_hidden('userid', $row['Id']);
					$attr = array('name'=>'npassword', 'value'=>'', 'size'=>'10', 'maxlength'=>'32');
					echo form_input($attr);
					echo form_submit('newpassword', 'Изменить пароль');
					echo form_close();
				}
				?>	
				</td>

				
				
				
				</tr>
		<?}
	}
	else//Для эксперта, нах ему искать, пусть смотрит
	{
		foreach ($retdata as $row){
			if($row['Rights']!=2)
							if($row['Deleted']==0)

			
			?>
		
		
			<tr><td><a href="/user/viewuser/<?echo $row['Id'];?>"><?echo $row['Login'];?></a></td><td><?echo $row['FName'];?></td><td><?echo $row['SName'];?></td>
				<td>
				<?

					if($row['rels']==0)
					{
						echo form_open('/user/addaccept/');
						echo form_hidden('userid', $row['Id']);
						echo form_submit('addaccept', 'Добавить заявку');
						echo form_close();
					}
					else
					{		
						echo form_open('/user/deleteaccept/');
						echo form_hidden('userid', $row['Id']);
						echo form_submit('delete', 'Заявка на удаление пользователя');
						echo form_close();
					}
				
				?>	
				</td>

	<?
	
		}
			}
		
		
		
		?>
		
</table>
<? echo $pager;?>

