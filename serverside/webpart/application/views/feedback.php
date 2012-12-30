
	<?echo form_open('fback/send');
		$nd = array(
              'id'          => 'ndata',
			  'name'		=> 'ndata',
              'maxlength'   => '32',
              'size'        => '32',
              'style'       => 'width:200px'
        );?>
		<p>Ваше имя:</p><?echo form_input($nd);
		$md = array(
              'id'          => 'mdata',
			  'name'		=> 'mdata',
              'maxlength'   => '32',
              'size'        => '32',
              'style'       => 'width:200px'
        );?>
		<p>E-mail для ответа:</p><?echo form_input($md);
		?>
		<p>Ваше обращение: </p><?echo form_textarea('tdata','');?><br>
		<?echo form_submit('sdata','Отправить');
	echo form_close();?>
	
	<?
	if ($derr == 1) echo "Пожалуйста, введите корректное имя от 4 до 32 символов.";
	if ($derr == 2) echo "Пожалуйста, введите корректный email-адрес от 6 до 32 символов.";
	?>

