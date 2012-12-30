
		<div  id="regform" class="regform">
		<?if(isset($specinfo))echo $specinfo."<br/><br/><br/>";?>
			<?php 
		$this->load->helper('form');
		$formAttr = array('id' => 'recoveryForm');
		echo form_open('remember/newpassword', $formAttr);
		echo "<table>";
			echo "<tr>";
				echo "<td> Новый пароль: </td>";
				echo "<td>" . form_input(array('name'=>'password', 'id'=>'password')). "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td></td>";
				echo "<td>" . form_hidden(array('userkey'=>$hidden_info)). "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<center><td>" . form_submit(array('name'=>'recovery', 'id'=>'recovery', 'value'=>'Восстановить')). "</td></center>";
			echo "</tr>";

		echo "</table>";	
			echo form_close();
	?>
		

</div>

