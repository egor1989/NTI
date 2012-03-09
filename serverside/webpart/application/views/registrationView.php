<div id="content" class="pageContent">
	<div  id="regform" class="regform">
		<?if(isset($specinfo))echo $specinfo."<br/><br/><br/>";?>
		 <form action="http://nti.goodroads.ru/user/registration" method="post" accept-charset="utf-8" id="registrationForm">
		<table>
			<tr>
				<td> Имя </td>
				<td><td><input type="text" name="fname" value="" id="fname"  /></td>
			</tr>
			<tr>
				<td> Фамилия </td>
			<td><td><input type="text" name="sname" value="" id="sname"  /></td>
			<tr>
				<td> Email </td>
				<td><td><input type="text" name="email" value="" id="email"  /></td>
			</tr>
			<tr>
				<td> Логин </td>
				<td><td><input type="text" name="login" value="" id="login"  /></td>
			</tr>
			<tr>
				<td> Пароль </td>
				<td><td><input type="text" name="password" value="" id="password"  /></td>
			</tr>
			<tr>
				<td><input type="submit" name="registration" value="Зарегистрироваться" id="registration"  /></td>
			<td></td>
			</tr>
		</table>
		</form>
</div>
</div>
