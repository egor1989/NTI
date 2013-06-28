<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="SHORTCUT ICON" HREF="/favicon.ico">
<title>Goodroads NTI</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="<?if(!isset($descr))echo "Проект по автоматизированному контролю состояния дорожного покрытия GoodRoads";else echo $descr;?>">
<meta name="Keywords" content="<?if(!isset($keywords))echo "goodroads,дорожное покрытие,гудроадс,ямы на дорогах, качество покрытия, качество асфальта, качество дороги";else echo $keywords;?>">
<link rel="stylesheet" href="../../css/ui-lightness/jquery-ui-1.8.20.custom.css">
<script src="../../js/libs/jquery-1.7.2.js"></script>
<script src="../../js/libs/jquery.ui.core.js"></script>
<script src="../../js/libs/jquery.ui.widget.js"></script>
<script src="../../js/libs/jquery.ui.datepicker.js"></script>
<?if(isset($map_type))if($map_type==3){?>
        <link rel="stylesheet" href="http://openlayers.org/dev/theme/default/style.css" type="text/css">
        <link rel="stylesheet" href="http://openlayers.org/dev/theme/default/google.css" type="text/css">
        <script src="http://maps.google.com/maps/api/js?v=3.5&amp;sensor=false"></script>
        <script src="http://openlayers.org/dev/OpenLayers.js"></script>
<?}?>



<?if(isset($map_type) && $map_type==3){?>
<script src="/js/nti.js"></script>
	<?}?>
<script>
	$(function() {
		$( ".dateField" ).datepicker({ dateFormat: "dd-mm-yy" });
		//
	});
</script>
	<link rel="stylesheet" type="text/css" href="/css/main.css" /> 
	<link rel="stylesheet" href="/css/style.css" type="text/css" media="screen" />

</head>
<body <?if(isset($map_type) && $map_type==3){?>onload="init();"<?}?>>
    
    <div class="wrapper">
	<div class="header">
		<div class="headerLogo">
			<a href="/"></a>
		</div>
		<div class="headerLogin">
			<a class="dotted-link" href="/user/logout">Выйти</a>
		</div>
		<div class="headerMenu">
			<table cellpadding="0" cellspacing="0">
				<tr>
					
					 <?if(!isset($show_menu))
 {//Забил хуй на хуки ядра , тк пока не надо - статичность же!
 if(isset($rights) && $rights==2)
 {?>         

		<td class="headerMenu__firstItem"><a href="/all" class="hrefmenu" >Поиск пользователей</a></td>
		<td class="headerMenu__lastItem"><a href="/fback" class="hrefmenu" >Обратная связь</a></td>

<?}
else if(isset($rights) && $rights==3){?>

		<td class="headerMenu__firstItem"><a href="/all/users" class="hrefmenu" >Управление пользователями</a></td> 
		<td  class="headerMenu__lastItem"><a href="/all/ck" class="hrefmenu" >Управление экспертами</a></td>


<?}else
{?>

		<td class="headerMenu__firstItem"><a href="/user/search" class="hrefmenu" >Подробная информация по движению</a></td>
		<td class="headerMenu__lastItem"><a href="/fback" class="hrefmenu" >Обратная связь</a></td>

<?}}?>
					
					
				</tr>
			</table>
		</div>
	</div>
 	<div class="middle">   
    
    

		
