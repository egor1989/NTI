<div id="content" class="pageContent">
	<center><? echo $name." ". $sname;?>, PeacockTeam приветствует Вас!</center><br/>
	<?if($rights!=2){?><center><a href="/map">Карта ваших маршрутов</a></center><?}?>
	<?
if($rights==2)
{?>

	
	<table>
	<?
	foreach ($retdata as $row)	
			{ 
				echo "<tr><td><a href=/user/viewuser/".$row['Login'].">".$row['Login']."</a></td><td>".$row['FName']."</td><td>".$row['SName']."</td> </tr>";
			}
?>
	</table>
	
	
		<table>
	<?
	foreach ($unregistered_data as $row)	
			{ 
				echo "<tr><td><a href=/map/viewdata/".$row['Id'].">".$row['Insert_Time']."</a></td></tr>";
			}
}?>
	</table>
	
</div>
