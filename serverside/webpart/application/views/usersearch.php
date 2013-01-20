
	<?if(isset($some_info))echo $some_info;?>
				Введите имя для поиска
			<?
				echo form_open('/search');
				echo form_input('name', '');
				echo form_submit('accept', 'Accept');
				echo form_close();
		
			?>	
	<?if($isfounded==1){?>
	
	<table>
			<?foreach ($search_result as $row)	
			{ 
				if($row['Relation']==3){?>
					<tr><td><a href="/user/viewuser/<?echo $row['Id'];?>"><?echo $row['Login'];?></a></td><td><a href="/user/viewuser/<?echo $row['Id'];?>"><?echo $row['FName'];?>&nbsp;<?echo $row['SName'];?></a></td> <td>
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
			
				<tr><td><a href="/user/viewuser/<?echo $row['Id'];?>"><?echo $row['Login'];?></a></td><td><a href="/user/viewuser/<?echo $row['Id'];?>"><?echo $row['FName'];?>&nbsp;<?echo $row['SName'];?></a></td> <td>
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
				<tr><td><a href="/user/viewuser/<?echo $row['Id'];?>"><?echo $row['Login'];?></a></td><td><a href="/user/viewuser/<?echo $row['Id'];?>"><?echo $row['FName'];?>&nbsp;<?echo $row['SName'];?></a></td> <td>
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






















