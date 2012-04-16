<div id="content" class="pageContent">
Все пользователи системы 
<table border=1>
		<?foreach ($retdata as $row){?>
				<?if($row['Rights']==2)$type="Эксперт системы";
				if($row['Rights']==1)$type="Данные подтверждены";
				if($row['Rights']==0)$type="Не подтвердено";
				?>
				<tr><td><a href="/user/viewuser/<?echo $row['Login'];?>"><?echo $row['Login'];?></a></td><td><?echo $row['FName'];?></td><td><?echo $row['SName'];?></td> <td><?echo $type;?></td>
				
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
				
				
				
				
				</tr>
		<?}?>
</table>

</div>
