<div id="content" class="pageContent">
	<a href="/user/csv/<? echo $Id;?>">Ссылка на файл событий для скачивания</a>
<table border=1>
	<tr>
		<td>AccX</td>
		<td>AccY</td>
		<td>Distance</td>
		<td>Lat</td>
		<td>Lng</td>
		<td>direction</td>
		<td>compass</td>
		<td>speed</td>
		<td>timestamp</td>
		<td>Описание события </td>
	</tr>	
<?for($i=0;$i<count($trr);$i++){?>
<tr>
		<td><?echo $trr[$i]['accx'];?></td>
		<td><?echo $trr[$i]['accy'];?></td>
		<td><?echo $trr[$i]['distance'];?></td>
		<td><?echo $trr[$i]['lat'];?></td>
		<td><?echo $trr[$i]['lng'];?></td>
		<td><?echo $trr[$i]['direction'];?></td>
		<td><?echo $trr[$i]['compass'];?></td>
		<td><?echo $trr[$i]['speed'];?></td>
		<td><?echo $trr[$i]['utimestamp'];?></td>
		<td><?
		$what_to_write="";
		if( $trr[$i]['TypeAcc']=="acc1 started")$what_to_write.="<span style=\"color:green\">Начато легкое ускорение</span><br/>";
		else if( $trr[$i]['TypeAcc']=="acc1 continued")$what_to_write.="<span style=\"color:green\">Продолжается легкое ускорение</span><br/>";
		else if( $trr[$i]['TypeAcc']=="acc2 started")$what_to_write.="<span style=\"color:orange\">Начато среднее ускорение</span><br/>";
		else if( $trr[$i]['TypeAcc']=="acc2 continued")$what_to_write.="<span style=\"color:orange\">Продолжается среднее ускорение</span><br/>";
		else if( $trr[$i]['TypeAcc']=="acc3 started")$what_to_write.="<span style=\"color:red\">Начато сильное ускорение</span><br/>";
		else if( $trr[$i]['TypeAcc']=="acc3 continued")$what_to_write.="<span style=\"color:red\">Продолжается сильное ускорение</span><br/>";	
	    else if( $trr[$i]['TypeAcc']=="brake1 started")$what_to_write.="<span style=\"color:green\">Начато легкое торможение</span><br/>";
		else if( $trr[$i]['TypeAcc']=="brake1 continued")$what_to_write.="<span style=\"color:green\">Продолжается легкое торможение</span><br/>";
		else if( $trr[$i]['TypeAcc']=="brake2 started")$what_to_write.="<span style=\"color:orange\">Начато среднее торможение</span><br/>";
		else if( $trr[$i]['TypeAcc']=="brake2 continued")$what_to_write.="<span style=\"color:orange\">Продолжается среднее торможение</span><br/>";
		else if( $trr[$i]['TypeAcc']=="brake3 started")$what_to_write.="<span style=\"color:red\">Начато сильное торможение</span><br/>";
		else if( $trr[$i]['TypeAcc']=="brake3 continued")$what_to_write.="<span style=\"color:red\">Продолжается сильное торможение</span><br/>";	
		if($trr[$i]['TurnType']=="left turn started")$what_to_write.="<i>Начат левый поворот</i><br/>";
		else if($trr[$i]['TurnType']=="left turn continued")$what_to_write.="<i>Продолжается левый поворот</i><br/>";
		else if($trr[$i]['TurnType']=="left turn finished")$what_to_write.="<i>Поворот налево закончен</i><br/>";
		else if($trr[$i]['TurnType']=="right turn started")$what_to_write.="<i>Начат правый поворот</i><br/>";
		else if($trr[$i]['TurnType']=="right turn continued")$what_to_write.="<i>Продолжается правый поворот</i><br/>";
		else if($trr[$i]['TurnType']=="right turn finished")$what_to_write.="<i>Поворот направо закончен</i><br/>";
		
		
		if($trr[$i]['TypeSpeed']=="s1")$what_to_write.="<span style=\"color:green\">Легкое превышение скорости</span><br/>";
		else if($trr[$i]['TypeSpeed']=="s2")$what_to_write.="<span style=\"color:orange\">Среднее превышение скорости</span><br/>";
		else if($trr[$i]['TypeSpeed']=="s3")$what_to_write.="<span style=\"color:red\">Сильное превышение скорости</span><br/>";
echo $what_to_write;
		?></td>
	</tr>	

<?}?>
</table>
</div>
