<div id="content" class="pageContent">
	<center><a href="/user/logout">Выйти</a></center><br>
	<center><? echo $name." ". $sname;?>, PeacockTeam приветствует Вас!</center><br/>
	<center><a href="/map">Карта ваших маршрутов</a></center>
	<?
if($rights==2)
{?>

	echo "Таблица пользователей, которых Вы можете просмотреть<br/>
	<table>
		
	<?
	foreach ($retdata as $row)	
			{ 
				echo "<tr><td><a href=/user/viewuser/".$row['Login'].">".$row['Login']."</a></td><td>".$row['FName']."</td><td>".$row['SName']."</td> </tr>";
			}
}?>
	</table>
</div>
