<div id="content" class="pageContent">
		<div  id="regform" class="regform">
		<?if(isset($specinfo))echo $specinfo."<br/><br/><br/>";?>
	
			<?
			 $formAttr = array('id' => 'recoveryForm');
				echo form_open('remember/recover', $formAttr);
		?>
		<table>
			<tr>
				<td> Введите адрес Вашей почты </td>
				<td><td><?echo  form_input(array('name'=>'email', 'id'=>'email'));?></td>
			</tr>
			<tr>
				<td><?echo form_submit('recovery', 'Восстановить');?></td>
			<td></td>
			</tr>
		</table>
<?echo form_close();?>
</div>
</div>
