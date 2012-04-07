<div id="content" class="pageContent">
		<div  id="regform" class="regform">
		<?if(isset($specinfo))echo $specinfo."<br/><br/><br/>";?>
		 <form action="http://nti.goodroads.ru/remember/recover" method="post" accept-charset="utf-8" id="recoveryForm">
		<table>
			<tr>
				<td> Введите адрес Вашей почты </td>
				<td><td><input type="text" name="email" value="" id="email"  /></td>
			</tr>
			<tr>
				<td><input type="submit" name="recovery" value="Восстановить" id="recovery"  /></td>
			<td></td>
			</tr>
		</table>
		</form>
</div>
</div>
