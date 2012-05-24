<div id="content" class="pageContent">
Пользователи системы 

<table border=1>
	
		<?if($retdata!=false)
		foreach ($retdata as $row){?>
				<?
				if($row['Rights']==1)$type="Данные подтверждены";
				if($row['Rights']==0)$type="Не подтверждено";
				if($row['Rights']==2)$type="Эксперт системы";
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
				<td>
				<?
				if($row['Deleted']==0) {
					echo form_open('/map');
					//echo form_hidden('userid', $row['Id']);
					$attr = array('name'=>'map', 'value'=>'Карта');
					echo form_submit($attr);
					echo form_close();
				}
				?>	
				</td>
				
				
				
				</tr>
		<?}?>
		
</table>
<? echo $pager;?>
</div>
