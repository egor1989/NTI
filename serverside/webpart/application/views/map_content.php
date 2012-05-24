<div id="content" class="pageContent">
	<?if ($userslist != -1) {?>
		<table border=1>
		<?
		$i=0;
		while (isset($userslist[$i])) {?>
			<tr>
				<td>
					<?if (isset($userslist[$i]['Login']))?><span style="font-size: 14px;"><a href="/map/viewtrips/<?echo $userslist[$i]['Id'];?>"><?echo $userslist[$i]['Login'];?></a></span>
				</td>
				
				<td>
					<?if (isset($userslist[$i]['FName']))?><span style="font-size: 14px;"><?echo $userslist[$i]['FName'];?></span>
				</td>
				
				<td>
					<?if (isset($userslist[$i]['SName']))?><span style="font-size: 14px;"><?echo $userslist[$i]['SName'];?></span>
				</td>
			</tr>
		
	
		<?
		$i++;
		}?>
		</table>
	<?
	} else {?>
		<span style="font-size: 12px;">Не найдено ни одного пользователя.</span>
	<?
	}?>

</div>