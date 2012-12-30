

		Все заявки<br/>
		<?$this->load->helper('form');?>
		<?if($retdata){?>
		
		<table border=0>
		<?
			foreach ($retdata as $row)	
			{ 
				if($row['Status']<=2)
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
				else if($row['Status']==3)
				{
					echo "<tr><td><a href=/user/viewuser/".$row['ULogin'].">".$row['ULogin']."</a> ".$row['UFName'].",".$row['USName']."</td><td>Запрос был отклонен</td><td><a href=/user/viewuser/".$row['CKLogin'].">".$row['CKLogin']."</a> ".$row['CKName'].",".$row['CKSName']."</td>";
					echo "<td>";
					echo"</td><td>";
					echo "</td></tr>\n";
			
				}
				else
				{
					echo "<tr><td><a href=/user/viewuser/".$row['ULogin'].">".$row['ULogin']."</a> ".$row['UFName'].",".$row['USName']."</td><td>Запрос был одобрен</td><td><a href=/user/viewuser/".$row['CKLogin'].">".$row['CKLogin']."</a> ".$row['CKName'].",".$row['CKSName']."</td>";
					echo "<td>";
					echo"</td><td>";
					echo "</td></tr>\n";
			
				}
			}
		?>
		</table>
		<?}?>

