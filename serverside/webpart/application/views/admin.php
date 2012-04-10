<div id="content" class="pageContent">

		Новый заявки<br/>
		<?if($tickets){?>
		
		<table border=1>
		<?
			foreach ($tickets as $row)	
			{ 
				echo "<tr><td><a href=/user/viewuser/".$row['ULogin'].">".$row['ULogin']."</a> ".$row['UFName'].",".$row['USName']."</td><td>Запрос на добавление от</td><td><a href=/user/viewuser/".$row['CKLogin'].">".$row['CKLogin']."</a> ".$row['CKName'].",".$row['CKSName']."</td><td><a href='/admin/approve/".$row['RequestId']."'>Одобрить</a></td><td><a href='/admin/approve/".$row['RequestId']."'>Отклонить</a></td></tr>";
			}
		?>
		</table>
		<?}?>
		
		Эксперты системы 
		<table border=1>
		<tr><td>Логин</td><td>Имя</td><td>Фамилия</td><td>Тип Доступа</td> </tr>
		<?
			foreach ($experts as $row)	
			{ 
				echo "<tr><td><a href=/user/viewuser/".$row['Login'].">".$row['Login']."</a></td><td>".$row['FName']."</td><td>".$row['SName']."</td> </tr>";
			}
		?>
		</table>
		
		
		Пользователи системы 
		<table border=1>
		<tr><td>Логин</td><td>Имя</td><td>Фамилия</td><td>Тип Доступа</td> </tr>
		<?
			foreach ($users as $row)	
			{ 
				echo "<tr><td><a href=/user/viewuser/".$row['Login'].">".$row['Login']."</a></td><td>".$row['FName']."</td><td>".$row['SName']."</td> </tr>";
			}
		?>
		</table>
		


</div>
