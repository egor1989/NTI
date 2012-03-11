<?
session_start();
require "dbconn.php";
$dbcnx = mysql_connect($dblocation, $dbuser, $dbpasswd);
if (!$dbcnx)    {      echo "<p>DAMN it is baaad mySQL</p>";    }  
if (!mysql_select_db($dbname,$dbcnx) )    {      echo "<p>Error in database...damn</p>";    }  
mysql_query('SET NAMES utf8'); 
if($_SESSION['UID'])
{
$uid=$_SESSION['UID'];
$sql=mysql_query("Select Rights from User where id=$uid");
$row = mysql_fetch_assoc($sql);
$user_right=$row['Rights'];
}
if(!isset($_GET['type']))$mtype=1;
if($_GET['type']=="yandex")$mtype=2;
else
$mtype=1;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<LINK REL="SHORTCUT ICON" HREF="http://goodroads.ru/favicon.png">
<title>GoodRoads</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="Проект по автоматизированному контролю состояния дорожного покрытия GoodRoads">

<meta name="Keywords" content="goodroads,дорожное покрытие,гудроадс,ямы на дорогах, качество покрытия, качество асфальта, качество дороги">
<link rel="stylesheet" type="text/css" href="style.css" />
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<link href="css.css" media="screen" rel="stylesheet" type="text/css" /> 
<link href="demo1/style.css" class="piro_style" media="screen" title="white" rel="stylesheet" type="text/css" /> 
<link href="../news/news.css" media="screen" rel="stylesheet" type="text/css" /> 

<script type="text/javascript" src="/js/jquery-1.6.1.js"></script>
<script type="text/javascript" src="/js/jquery.form.js"></script>
<script type="text/javascript" src="/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/js/jquery.idTabs.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
<script src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript" src="http://code.google.com/apis/gears/gears_init.js"></script>
<script type="text/javascript" src="/js/pirobox.js"></script>  
<script type="text/javascript" src="http://userapi.com/js/api/openapi.js"></script>
<script type="text/javascript" src="/js/loginCheckForm.js"></script>
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
<script type="text/javascript" src="/js/progressBar.js"></script>


  <script src="http://api-maps.yandex.ru/1.1/index.xml?key=AMBeNU4BAAAAJiQSTQIAHR1a7SIzYGmMSXaQqoDgJTxTm_UAAAAAAAAAAAAErgUdpYGhqo_mEVUwOZ1s8zxuBw=="
	type="text/javascript"></script>
<script type="text/javascript">
  VK.init({apiId: 2393864, onlyWidgets: true});
</script>

<script type="text/javascript" src="base.js"></script>

 <script type="text/javascript" >
	 
	 
  $(document).ready(function() {
  $( "#dialog" ).dialog({ autoOpen: false });
  $( "#Pointdialog" ).dialog({ autoOpen: false });
  $( "#newdialog" ).dialog({ autoOpen: false });
  $( "#picturemanage" ).dialog({ autoOpen: false });
  $( "#newpointinfo" ).dialog({ autoOpen: false });
  $( "#newrd" ).dialog({ autoOpen: false });
  $( "#userroad" ).dialog({ autoOpen: false });
 
  var options = { target: "#myForm",timeout: 3000};
  $('#myForm').submit(function() {var options = {target: "#myForm",timeout: 3000};
    $(this).ajaxSubmit(options); 
	return false;
  }); 
 
    $('#Image_Upload').submit(function() { 
   var options = {
   target: "#Image_Upload",
   timeout: 3000	
  };
    $(this).ajaxSubmit(options); 
    return false;
  });
      
$('#ChangeProp').submit(function() { 
   var options = {
   target: "#ChangeProp",
      timeout: 3000	
  };
    $(this).ajaxSubmit(options); 
    return false;
  });
  
 
$().piroBox({
			my_speed: 400,
			bg_alpha: 0.1,
			slideShow : true,
			slideSpeed : 4,
			close_all : '.piro_close,.piro_overlay'
	});
 
  $('#ChangePropLine').submit(function() { 
   var options = {
   target: "#ChangePropLine",
   timeout: 3000	
  };
    $(this).ajaxSubmit(options); 
   var cl=$("#Line_color").val();
   var wg=$("#Line_weight").val();
   var olaid="#"+ola;
   $(olaid).remove();
    var poly = new google.maps.Polyline({
    path: points,    strokeColor: cl,
    strokeOpacity: 1.0,
    strokeWeight: wg,
	'id':ola
  });

  poly.setMap(map);
    return false;
  });
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li:first").addClass("active").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content

	//On Click Event
	$("ul.tabs li").click(function() {

		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content

		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});
	


	
 });
 
</script> 

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-24587443-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<script type="text/javascript" src="/js/jquery.json-2.2.min.js"></script>
<script type="text/javascript" src="/js/md5.js"></script>
<script>
	
	 $(document).ready(function(){  
				$('#loginBtn').click(function(){  
				if((document.getElementById("login").value.length != 0) && (document.getElementById("password").value.length > 5)){
					function send() {
							var formData = {
							"hash": calcMD5(document.getElementById("login").value + calcMD5(document.getElementById("password").value))
							};
							$.ajax({
								url:'login/login.php'
								, type:'POST'
								, data:'jsonData=' + $.toJSON(formData)
								, success: function(res){},
								async: false
							});
						return false;
					}
					send();
				}
			}); 	
        });
</script>
</head>

<?if($_GET['view']!="full")
{
	?> 
	<?if($mtype==1){?>
<body id ="placeToLoad" onload="initialize('content'); initProgressBar(); test();">
	<?}?>
	<?if($mtype==2){?>
<body id ="placeToLoad" onload="load_yandex_map(); initProgressBar(); test();">
	<?}?>
	
	<? }else {?>
<body id = "placeToLoad" onload="initialize('contentfull'); initProgressBar(); test();">
<?}?>	
	<script> var curr =0; var t=setInterval(function(){calcProgress(curr, 100); curr++;},1000); </script>
  <div id="progressBar">
	<div id="progressBarMsg">
		<div id="sliderWrapper">
			<div id="slider"></div>
		</div>
	</div>
  </div>
  
  <?if($_GET['view']!="full")
{
	?>
	
        <div id="maket">
  
	
						<div id="header">
						<img id="logo" src="GR.png">
				
						
				<div id="menu">
					<div style="position:absolute;top:50px;">
						<a href=#1 style="position:relative;left:-50px;top:15px;"><img src='buttons/poput4_100.png'></a>
						<a href=#2 style="position:relative;left:-20px;top:15px;"><img src='buttons/marsh100.png'></a>
					</div>
					<div style="position:absolute;top:0px;">
						<a href=# style="position:absolute;left:15px;top:5px;" onclick="action(5)"><img src='buttons/moimarsh100.png'></a>
						<a href="about.php" style="position:absolute;left:15px;top:130px;">		   <img src='buttons/help100.png' ></a>
					</div>
				</div>
			</div>
			<div id="left">
				<div id="newsheader">
					Новости
					<a href="/news.php">Все ></a>
				</div>
				<div id="newsbody">
				<?if($user_right)
{?>
		<a href="/news.php?type=new" >создать новою запись</a>

<?}?>
				<?
				
$sql=mysql_query("Select * from News where Active=1 order by Id Desc Limit 5");
	while($row = mysql_fetch_array($sql))
	{
	$title=$row['Title'];
	$info=$row['Short_Info'];
	echo "<div id='newsmini'><div id='title'><a href='../news.php?type=show&id=".$row['Id']."'>".$title."</a>";
	if($user_right==3)
	{
		echo "<div id='toolbar'>";
	?>
<a href="../news.php?type=edit&id=<?echo $row['Id'];?>"><img src="../news/edit.png"></a>
<a href="../news.php?type=delete&id=<?echo $row['Id'];?>"><img src="../news/delete.png"></a>
<?
if($row['Moded']==1){?><img src="../news/moded.png">
<?}else {?><a href="../news.php?type=moded&id=<?echo $row['Id'];?>"><img src="../news/unmoded.png"></a>
<?}

if($row['Active']==1){?><a href="../news.php?type=unactive&id=<?echo $row['Id'];?>"><img src="../news/activ.png"></a>
<?}else {?><a href="../news.php?type=active&id=<?echo $row['Id'];?>"><img src="../news/unactiv.png"></a><?}
	echo "</div></div>";
}
else
{
echo "</div>";
}
	echo $info;
echo "<div id='newsfooter'><a href='../news.php?type=show&id=".$row['Id']."'>Комментариев:0</a>";
$cd=split(" ",$row['Creation_Date']);
echo "&nbsp;&nbsp;&nbsp;&nbsp;".$cd[0];


echo "</div>";
	echo "</div>";				
	}
?>				
							
				</div>
				<div id="registration">
				<?if(!$_SESSION['UID']){ ?>
				<a href=#><img src="Twitter.png"></a>
									<?} ?>
				<?if(empty($_SESSION['UID'])){
				include_once("login/example.php");
				/*$vk = new Auth_Vkontakte();
				$vk_auth = $vk->is_auth();
				include ("templates/index.html");*/
				}
				?>
				<?php if(empty($_SESSION['UID'])){?>
				<form method ="POST" action = "/users/login.php" onsubmit="return loginCheckForm(this);">
					<table cols=2>
						<tr>
							<td>Логин: </td>
							<td><input type="text" name="login" id="login"/></td>
						</tr>
						<tr>
							<td>Пароль: </td>
							<td><input type="password" name="password" id="password"/></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" name="loginBtn" id="loginBtn" value="Вход"></td>
						</tr>
						<tr>
							<td></td>
							<td><a href="../regFormNew.php"><input type="button" name="send" value="Регистрация"></a></td>
						</tr>
					</table>
				</form>
				<script>VK.init({apiId: 2393864});</script>
		<div id="vk_auth"></div>
		<script type="text/javascript">
			var userInfo;
			VK.Widgets.Auth("vk_auth", {width: "100px", onAuth: function(data) {
				$.get("/authTest/vkontakteReg.php", { id: data['uid'], fName: data['first_name'], sName: data['last_name'], 
					photo: data['photo'], photo: data['photo_rec']} , function(inf){
					alert("data: " + inf);
					window.location.href = "http://goodroads.ru/index.php?id="+data['uid'];
				});
				} 
			});
		</script>
				<?php } else { 
				include ("login/viewUserInfo.php"); ?>
				
				<?php } ?>
				</div>

		<!-- Put this div tag to the place, where Auth block will be -->
		
			</div>
			

			<div id="toolbar">
			
			
				<img src="caution.png" onclick="look_on_road()">
				<a href ="?view=full"><img src="fullscr.png"></a>
				<?if($mtype==2){?><a href ="?type=google"><img src="g.png"></a><?}?>
				<?if($mtype==1){?><a href ="?type=yandex"><img src="y.png"></a><?}?>
			</div>
			

		<div id="content"></div>
			<div id="footer">
				GoodRoads &copy;&nbsp;&nbsp;&nbsp;&nbsp;2011
 
<?
if($_SESSION['UID'])
{
?>



			<img src="point.png" onclick="action(1)">
			<img src="../images/line.JPG" onclick="action(2)">
			<img src="profile.png" onclick="action(3)">
			<img src="area.png" onclick="action(4)">


         
            <?
}
?>
      </div>
      </div>
<?}
else
{
	?>
	<div id="contentfull"></div>
	<?}?> 
<div id="dialog" title="Информация о дороге">
<div class="pmanage">
<img src='./icons/left.png' class="lrbutton" onclick="move_point_gall(-1)">
<a class="pirobox_gall" id="limghrf" ><img id="limghrftr" width=30%/></a> 
<img src='./icons/right.png'  class="lrbutton" onclick="move_point_gall(1)"><br/>
</div>

<span id="Line_info"></span><br/>
<span id="Line_color"></span><br/>
<span id="Line_weight"></span><br/>
<img src="add.png" onclick="open_road_window(0);">
<img src="allroad.png" onclick="open_road_window(1);">
<img src="activate.png" onclick="activate_element(2);">
<img src="delete.png" onclick="delete_road();">
</div>


<div id="Pointdialog" title="Информация о выбранной точке">
<div class="pmanage">

<img src='./icons/left.png'  class="lrbutton" onclick="move_point_gall(-1)">
<a class="pirobox_gall" id="imghrf" ><img id="imghrftr" width=30%/></a> 
<img src='./icons/right.png'  class="lrbutton" onclick="move_point_gall(1)"><br/>
</div>
<span id="Point_info"></span><br/>
<span id="Point_Address"></span><br/>
<span id="Point_status"></span><br/>
<img src="add.png" onclick="open_point_window(0);">
<img src="add.png" onclick="open_point_window(1);">

<img src="activate.png" onclick="activate_element(1);">
<img src="delete.png" onclick="delete_point();">

</div>

<div id="newdialog" title="Создание нового блока информации">
		<input type='hidden' id='Line_Id' name='Line_id' /><br/>
		Цвет<br/>
		<input type='text' id='NLine_color' name='NLine_color' /><br/>
		Вес<br/>
		<input type='text' id='NLine_weight' name='NLine_weight' /><br/>
		Игформация<br/>
		<input type='text' id='NLine_info' name='NLine_info' /><br/>
		<input type='button' value='Enter' onclick="make_new_line_info()"/>		
</div>
<div id="newrd" title="Общая информация о дороге">
		<input type='text' id='Road_color' name='Road_color' /><br/>
		<input type='text' id='Road_info' name='Road_info' /><br/>
		<input type='text' id='Road_status' name='Road_status' /><br/>
		<input type='button' value='Enter' onclick="update_road_info()"/>		
</div>
<div id="picturemanage" title="Управление фотографиями" >
<form id="Image_Upload" enctype="multipart/form-data" action="./functions/picture_upload.php" method="post">
		<input type="hidden" name="MAX_FILE_SIZE" value="400000" />
		<input type='hidden' id='oid' name='oid'/><br/>
		Заголовок<br/><input type='tdatabaseext' id='otitle' name='otitle'/><br/>
		Фотография для элемента <br/>
		<input name="userfile" type="file" /><br/>
		<input type="submit" value="Send File" />
	</form>
</div>
<div id="newpointinfo" title="Создание нового блока информации" >
		<input type='hidden' id='Point_Id' name='Point_Id' /><br/>
		Information<br/>
		<input type='text' id='NPoint_info' name='NPoint_info' /><br/>
		Address<br/>
		<input type='text' id='NPoint_address' name='NPoint_address' /><br/>
		Status information<br/>
		<input type='text' id='NPoint_status' name='NPoint_status' /><br/>
		<input type='button' value='Enter' onclick="make_new_point_info()"/>		
</div>


<div id="userroad" title="Информация о дороге" >
		Information<br/>
		<input type='text' id='User_Road_info' name='User_Road_info' /><br/>
		<img src="activate.png" onclick="user_apply_road()">
		<img src="delete.png" onclick="user_delete_road()">
</div>

 
</body>
</html>
