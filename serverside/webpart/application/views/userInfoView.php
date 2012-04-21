<div id="content" class="pageContent">
	<center><? echo $name." ". $sname;?>, PeacockTeam приветствует Вас!</center><br/><br/><br/>	
	<?if($rights!=2){?><center><a href="/map">Карта ваших маршрутов</a></center><?}?>
	<?
if($rights==2)
{?>
Ваши пользователи:<br/>
	
	<table>
	<?
	foreach ($retdata as $row)	
			{ 
				echo "<tr><td><a href=/user/search/".$row['Id'].">".$row['Login']."</a></td><td>".$row['FName']."</td><td>".$row['SName']."</td> </tr>";
			}
	?>
	</table>
	
	<?if($tickets){?>
	Ваши заявки<br/>
		<table border=1>
		<?
			foreach ($tickets as $row)	
			{ 
				echo "<tr><td><a href=/user/search/".$row['Id'].">".$row['Login']."</a></td><td>".$row['FName']."</td><td>".$row['SName']."</td> </tr>";
			}
		?>
		</table>
		<?}?>
	
	
	
<?}?>
	
</div>
