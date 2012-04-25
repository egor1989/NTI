<div id="content" class="pageContent">
	<?if(isset($some_info))echo $some_info;?>
	<table border=1>
		<tr>
			<td>
				Логин
			</td>
			<td>
				Фамилия, Имя
			</td>
			<td>
				Действие
			</td>
		</tr>
		<?foreach($ept as $r) {?>
		<tr>
			<td>
				<?echo $r['Login']; ?>
			</td>
			<td>
				<?echo $r['FName']." ".$r['SName']; ?>
			</td>
			<td>
				<?
				if ($r['Button'] == 1) {
					echo form_open('/admin/add/');
					echo form_hidden('userid', $r['Id']);
					echo form_hidden('ckid', $ckid);
					echo form_submit('adduser', 'Добавить');
					echo form_close();
				} else {
					//echo form_open('/user/deleteaccept/');
					echo form_open('/admin/delete/');
					echo form_hidden('userid', $r['Id']);
					echo form_hidden('ckid', $ckid);
					echo form_submit('deluser', 'Удалить');
					echo form_close();
				}
				?>
			</td>
		</tr>
		<?}?>
	</table>
</div>






















