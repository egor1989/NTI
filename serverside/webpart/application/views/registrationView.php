<div id="content" class="pageContent">
	<div  id="regform" class="regform">
		После регистрации Вам будет выслана ссылка для подтверждения аккаунта.
		<?if(isset($specinfo))echo $specinfo."<br/><br/><br/>";?>
<?echo form_open('/user/registration');?>		<table>
			<tr>
				<td> Имя </td>
				<td><?echo form_input('fname', '');?></td>
			</tr>
			<tr>
				<td> Фамилия </td>
			<td><?echo form_input('sname', '');?></td>
			<tr>
				<td> Email </td>
				<td><?echo form_input('email', '');?></td>
			</tr>
			<tr>
				<td> Пароль </td>
				<td><?echo form_input('password', '');?></td>
			</tr>
			<tr>
				<td><?echo form_submit('registration', 'Зарегистрироваться');?></td>
			<td></td>
			</tr>
		</table>
		<?echo form_close();?>
</div>
</div>

				
				
				
			
				
