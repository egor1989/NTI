<div id="content" class="pageContent">

		Новый заявки<br/>
		<?$this->load->helper('form');?>
		<?if($tickets){?>
		
		<table border=1>
		<?
			foreach ($tickets as $row)	
			{ 
				
				echo "<tr><td><a href=/user/viewuser/".$row['ULogin'].">".$row['ULogin']."</a> ".$row['UFName'].",".$row['USName']."</td><td>Запрос на добавление от</td><td><a href=/user/viewuser/".$row['CKLogin'].">".$row['CKLogin']."</a> ".$row['CKName'].",".$row['CKSName']."</td>";
				echo "<td>";
				echo form_open('/admin/approve/');
				echo form_hidden('relation', $row['RequestId']);
				echo form_submit('accept', 'Accept');
				echo form_close();
				echo"</td><td>";
				echo form_open('/admin/dismiss/');
				echo form_hidden('relation', $row['RequestId']);
				echo form_submit('accept', 'Denie');
				echo form_close();
				echo "</td></tr>\n";
			}
		?>
		</table>
		<?}?>
		

		
		
	
		


</div>
