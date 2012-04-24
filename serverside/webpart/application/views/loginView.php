<div id="content" class="pageContent">
	<div  id="loginview" class="loginview">
	<?php 
		$this->load->helper('form');
		$formAttr = array('id' => 'registrationForm');
		echo form_open('user/authorization', $formAttr);
		echo "<table>";
			echo "<tr>";
				echo "<td> Имя пользователя: </td>";
				echo "<td>" . form_input(array('name'=>'login', 'id'=>'login')). "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td> Пароль: </td>";
				echo "<td>" . form_password(array('name'=>'password', 'id'=>'password')). "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td><a href='../user/registrationFormView'><input type='button' name='send' value='Регистрация'></a></td>";
				echo "<center><td>" . form_submit(array('name'=>'registration', 'id'=>'registration', 'value'=>'Войти')). "</td></center>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>";
				
				echo "<a href='/remember'>Восстановление пароля</a>";
				
				echo"</td>";

			echo "</tr>";
		echo "</table>";	
			echo form_close();
	?>
	
	</div>
			</div>
