<html>
<head>
	<title>Untitled</title>
</head>
<body>
	<h3 align="left">Time: from/till (like YYYY-mm-dd p.e: 2011-11-25)</h3>
	<?php echo form_open('lays/search');?>
	<p>
		<label for="time1">From</label>
		<input type="text" name="t1" id="t1" />
	</p>
	<p>
		<label for="time2">Till</label>
		<input type="text" name="t2" id="t2" />
	</p>
	<p>
		<input type="submit" value="Search" />
	</p>
	<?php echo form_close(); ?>
	<hr>
	<br>
	<!--
	<p><?php echo "���������: $total_acc"; ?></p>
	<p><?php echo "����������: $total_brake"; ?></p>
	<p><?php echo "���������: $total_turn"; ?></p>
	<p><?php echo "����������� �����: $total_time"; ?></p>
	<p><?php echo "����� ����: $total_score"; ?></p>
	-->
</body>
</html>