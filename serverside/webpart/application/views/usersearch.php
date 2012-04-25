<div id="content" class="pageContent">
	<?if(isset($some_info))echo $some_info;?>
	<?if($isfounded==1){?>
	<table>
			<?foreach ($search_result as $row)	
			{ 
				if($row['Relation']==3){?>
					<tr><td><a href="/user/viewuser/<?echo $row['Login'];?>"><?echo $row['Login'];?></a></td><td><a href="/user/viewuser/<?echo $row['Login'];?>"><?echo $row['FName'];?>&nbsp;<?echo $row['SName'];?></a></td> <td>
					<?
					if($rights==2)
					{
						echo form_open('/user/addaccept/');
						echo form_hidden('userid', $row['Id']);
						echo form_submit('addaccept', 'Добавить заявку');
						echo form_close();
					}
					?>
					</td></tr>
					
					
							<?}if($row['Relation']==2){?>
			
				<tr><td><a href="/user/viewuser/<?echo $row['Login'];?>"><?echo $row['Login'];?></a></td><td><a href="/user/viewuser/<?echo $row['Login'];?>"><?echo $row['FName'];?>&nbsp;<?echo $row['SName'];?></a></td> <td>
					<?
					if($rights==2)
					{
				
						echo form_open('/user/removeaccept/');
						echo form_hidden('userid', $row['Id']);
						echo form_submit('removeaccept', 'Удалить заявку');
						echo form_close();
					}
					?>
					</td></tr>
				
				
				
				
				
				<?}if($row['Relation']==1){?>
				<tr><td><a href="/user/viewuser/<?echo $row['Login'];?>"><?echo $row['Login'];?></a></td><td><a href="/user/viewuser/<?echo $row['Login'];?>"><?echo $row['FName'];?>&nbsp;<?echo $row['SName'];?></a></td> <td>
					<?
						if($rights==2)
					{
				
						echo form_open('/user/delete/');
						echo form_hidden('userid', $row['Id']);
						echo form_submit('delete', 'Удалить');
						echo form_close();
					}
					?>
					</td></tr>
				<?}?>
			<?}?>
	</table>
	<?}?>
	<?if($isfounded==0)echo "По вашему запросу ничего не найти<br/>";?>
	

			Введите имя для поиска
			<?
				echo form_open('/search');
				echo form_input('name', '');
				echo form_submit('accept', 'Accept');
				echo form_close();
		
			?>		
			<br><br>
	<table border=1>
		<tr>
			<td>
				Логин
			</td>
			<td>
				Фамилия, Имя
			</td>
			<td>
				Действие
			</td>
		</tr>
		<?foreach($ept as $r) {?>
		<tr>
			<td>
				<?echo $r['Login']; ?>
			</td>
			<td>
				<?echo $r['FName']." ".$r['SName']; ?>
			</td>
			<td>
				<?
				if ($r['Button'] == 1) {
					echo form_open('/user/addaccept/');
					echo form_hidden('userid', $r['Id']);
					echo form_submit('adduser', 'Добавить');
					echo form_close();
				} else {
					echo form_open('/user/deleteaccept/');
					echo form_hidden('userid', $r['Id']);
					echo form_submit('deluser', 'Удалить');
					echo form_close();
				}
				?>
			</td>
		</tr>
		<?}?>
	</table>
</div>






















