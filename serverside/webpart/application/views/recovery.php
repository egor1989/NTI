<div id="content" class="pageContent">
		<div  id="regform" class="regform">
		<?if(isset($specinfo))echo $specinfo."<br/><br/><br/>";?>
		 <form action="http://nti.goodroads.ru/remember/newpassword" method="post" accept-charset="utf-8" id="recoveryForm">
		<table>
			<tr>
				<td> Введите новый пароль </td>
				<td><input type="text" name="password" value="" id="password"  /></td>
			</tr>
			<tr>
				<td><input type="hidden" name="userkey" value="<?echo $hidden_info?>" id="userkey"  /></td>
			</tr>
			<tr>
				<td><input type="submit" name="recovery" value="Восстановить" id="recovery"  /></td>
			<td></td>
			</tr>
		</table>
		</form>
</div>
</div>
