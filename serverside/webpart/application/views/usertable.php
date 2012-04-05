<div id="content" class="pageContent">
	Пользователи, которых Вы можете просматривать 
<table border=1>
		<?
			foreach ($users as $row)	
			{ 
				echo "<tr><td><a href=/user/viewuser/".$row['Login'].">".$row['Login']."</a></td><td>".$row['FName']."</td><td>".$row['SName']."</td> <td><a href=/user/delete/".$row['Login']."><img src=/css/images/removeuser.png></a></td></tr>";
			}
		?>
</table>

</div>
