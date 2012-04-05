<div id="content" class="pageContent">
	<?if(isset($some_info))echo $some_info."<br/>";?>
	<?if($isfounded==1){?>
	<table>
			<?foreach ($search_result as $row)	
			{ 
				if($row['Relation']==3)echo "<tr><td><a href=/user/viewuser/".$row['Login'].">".$row['Login']."</a></td><td><a href=/user/viewuser/".$row['Login'].">".$row['FName']." ".$row['SName']."</a></td> <td><a href=/user/add/".$row['Login']."><img src=/css/images/adduser.png></a></td></tr>";
				if($row['Relation']==2)echo "<tr><td><a href=/user/viewuser/".$row['Login'].">".$row['Login']."</a></td><td><a href=/user/viewuser/".$row['Login'].">".$row['FName']." ".$row['SName']."</a></td> <td><img src=/css/images/waitforaccept.png><a href=/user/removeaccept/".$row['Login']."><img src=/css/images/removeraccept.png></a></td></tr>";
				if($row['Relation']==1)echo "<tr><td><a href=/user/viewuser/".$row['Login'].">".$row['Login']."</a></td><td><a href=/user/viewuser/".$row['Login'].">".$row['FName']." ".$row['SName']."</a></td> <td><a href=/user/delete/".$row['Login']."><img src=/css/images/removeuser.png></a></td></tr>";
			}?>
	</table>
	<?}?>
	<?if($isfounded==0)echo "По вашему запросу ничего не найти";?>
	
		 <form action="http://nti.goodroads.ru/user/search" method="post" accept-charset="utf-8" id="searchForm">
		<table>
			<tr>
				<td> Введите имя для поиска </td>
				<td><td><input type="text" name="name" value="" id="name"  /></td>
			</tr>
				<td><input type="submit" name="seach" value="Найти" id="search"  /></td>
			<td></td>
			</tr>
		</table>
		</form>
		Последние зарегистрировавшиеся пользователи
		<table border=1>
		<?
			foreach ($users as $row)	
			{ 
				echo "<tr><td><a href=/user/viewuser/".$row['Login'].">".$row['Login']."</a></td><td>".$row['FName']."</td><td>".$row['SName']."</td> </tr>";
			}
		?>
		</table>
		
		Ваши заявки, находящиеся в обработке<br/>
		<?if($tickets){?>
		<table border=1>
		<?
			foreach ($tickets as $row)	
			{ 
				echo "<tr><td><a href=/user/viewuser/".$row['Login'].">".$row['Login']."</a></td><td>".$row['FName']."</td><td>".$row['SName']."</td> </tr>";
			}
		?>
		</table>
		<?}
		else
		{?>
		Заявок не найдено 
		<?}?>
</div>
