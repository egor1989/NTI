
	<a href="/user/csv/<? echo $Id;?>">Ссылка на файл событий для скачивания</a>
<table border=1>
	<tr>
		<td><span style="font-size:10pt">Время</span></td>
		<td><span style="font-size:10pt">Тип события</span></td>
		<td><span style="font-size:10pt">Сила события</span></td>
		<td><span style="font-size:10pt">GLat</span></td>
		<td><span style="font-size:10pt">GLng</span></td>
		<td><span style="font-size:10pt">Направление</span></td>
		<td><span style="font-size:10pt">Минимальная скорость</span></td>
		<td><span style="font-size:10pt">Максимальная скорость</span></td>
		<td><span style="font-size:10pt">Продолж.(с)</span></td>
		<td><span style="font-size:10pt">Скорость перед событием</span></td>
		<td><span style="font-size:10pt">Направление перед событием</span> </td>
		<td><span style="font-size:10pt">Lat</span> </td>
		<td><span style="font-size:10pt">Lng</span> </td>
	</tr>	
<?for($i=0;$i<count($trr);$i++){?>
<tr>
			
		<td><span style="font-size:10pt"><?echo $trr[$i]['time'];?></span></td>
		<?
		if($trr[$i]['type']=="Acc")$what="Ускорение";
		if($trr[$i]['type']=="Brake")$what="Торможение";
		if($trr[$i]['type']=="Speed")$what="Превышение скорости";
		if($trr[$i]['type']=="LeftTurn")$what="Левый поворот";
		if($trr[$i]['type']=="RightTurn")$what="Правый поворот";
		?>
		<td><span style="font-size:10pt"><?echo $what;?></span></td>
		<?if($trr[$i]['weight']==1){?>
		<td><span style="font-size:10pt;color:green"><?echo $trr[$i]['weight'];?></span></td><?}?>
		<?if($trr[$i]['weight']==2){?>
		<td><span style="font-size:10pt;color:orange"><?echo $trr[$i]['weight'];?></span></td><?}?>
		<?if($trr[$i]['weight']==3){?>
		<td><span style="font-size:10pt;color:red"><?echo $trr[$i]['weight'];?></span></td><?}?>
		<td><span style="font-size:10pt"><?echo $trr[$i]['accx'];?></span></td>
		<td><span style="font-size:10pt"><?echo 0;?></td>
		<td><span style="font-size:10pt"><?echo $trr[$i]['compass'];?></span></td>
		<td><span style="font-size:10pt"><?echo $trr[$i]['minspeed'];?></span></td>
		<td><span style="font-size:10pt"><?echo $trr[$i]['maxspeed'];?></span></td>
		<td><span style="font-size:10pt"><?echo $trr[$i]['duration'];?></span></td>
		<td><span style="font-size:10pt"><?echo $trr[$i]['prespeed'];?></span></td>
		<td><span style="font-size:10pt"><?echo $trr[$i]['predir'];?></span></td>
		<td><span style="font-size:10pt"><?echo $trr[$i]['lat'];?></span></td>
		<td><span style="font-size:10pt"><?echo $trr[$i]['lng'];?></span></td>

	</tr>	

<?}?>
</table>

