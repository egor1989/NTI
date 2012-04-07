<div id="content" class="pageContent">

		Новый заявки<br/>
		<?if($tickets){?>
		<table border=1>
		<?
			foreach ($tickets as $row)	
			{ 
				echo "<tr><td><a href=/user/viewuser/".$row['Login'].">".$row['Login']."</a></td><td>".$row['FName']."</td><td>".$row['SName']."</td> </tr>";
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
