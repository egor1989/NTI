<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="SHORTCUT ICON" HREF="/favicon.ico">
<title>Goodroads NTI</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="<?if(!isset($descr))echo "Проект по автоматизированному контролю состояния дорожного покрытия GoodRoads";else echo $descr;?>">
<meta name="Keywords" content="<?if(!isset($keywords))echo "goodroads,дорожное покрытие,гудроадс,ямы на дорогах, качество покрытия, качество асфальта, качество дороги";else echo $keywords;?>">
<link rel="stylesheet" type="text/css" href="/css/main.css" /> 
</head>
<body>
  <div itemscope itemtype="http://schema.org/Organization"> 
    <meta itemprop="url" content="peacockteam.org" /> 
 
    <meta itemprop="name" content="PeacockTeam" /> 
    <meta itemprop="description" content="Making this world better" /> 
 
    <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"> 
      <meta itemprop="addressLocality" content="Saint-Petersburg" /> 
      <meta itemprop="addressCountry" content="Russia" /> 
    </div> 
 
    <div itemprop="employees"> 
      <div  itemprop="person" itemscope itemtype="http://schema.org/Person"> 
        <meta itemprop="name" content="Pavel Ershov"> 
        <meta itemprop="url" content="http://vk.com/ershovp"> 
      </div> 
      <div  itemprop="person" itemscope itemtype="http://schema.org/Person"> 
        <meta itemprop="name" content="Egor Ivanov"> 
        <meta itemprop="url" content="http://www.linkedin.com/in/egor7ivanov"> 
        <meta itemprop="url" content="http://vk.com/egor7ivanov"> 
      </div> 
      <div  itemprop="person" itemscope itemtype="http://schema.org/Person"> 
        <meta itemprop="name" content="Sergey Khavrenko"> 
      </div> 
      <div  itemprop="person" itemscope itemtype="http://schema.org/Person"> 
        <meta itemprop="name" content="Vlad Efremov"> 
      </div> 
      <div  itemprop="person" itemscope itemtype="http://schema.org/Person"> 
        <meta itemprop="name" content="Artem Bankin"> 
      </div>    
      <div  itemprop="person" itemscope itemtype="http://schema.org/Person"> 
        <meta itemprop="name" content="Ivan Ushkevich"> 
      </div> 
      <div  itemprop="person" itemscope itemtype="http://schema.org/Person"> 
        <meta itemprop="name" content="Ruslan Kreymer"> 
      </div> 
      <div  itemprop="person" itemscope itemtype="http://schema.org/Person"> 
        <meta itemprop="name" content="Elena Alekseenko"> 
      </div> 
      <div  itemprop="person" itemscope itemtype="http://schema.org/Person"> 
        <meta itemprop="name" content="Mikhail Vakulenko"> 
      </div> 
      <div  itemprop="person" itemscope itemtype="http://schema.org/Person"> 
        <meta itemprop="name" content="Vitaly Ivanov"> 
      </div> 
      <div  itemprop="person" itemscope itemtype="http://schema.org/Person"> 
        <meta itemprop="name" content="Alex Kossakovsky"> 
      </div> 
      <div  itemprop="person" itemscope itemtype="http://schema.org/Person"> 
        <meta itemprop="name" content="Alex Nikolaev"> 
      </div> 
      <div  itemprop="person" itemscope itemtype="http://schema.org/Person"> 
        <meta itemprop="name" content="Olga Mikhaylova"> 
      </div> 
      <div  itemprop="person" itemscope itemtype="http://schema.org/Person"> 
        <meta itemprop="name" content="Nastia Kaprova"> 
      </div> 
    </div> 
 
    <meta itemprop="telephone" content="+7 921 936-10-22" /> 
 
    <div itemscope itemtype="schema.org/ImageObject"> 
      <meta itemprop="name" conent="Logo" /> 
      <meta content="http://goodroads.ru/css/images/peacock.png" itemprop="contentURL" /> 
    </div> 
  </div> 

<div id="header" class="header">
	    <div class="header_left_shadow"></div>
    <div class="header_right_shadow"></div>
          <a href="/"><div class="logo"><img src="/css/images/logo2.png"></div></a>
 <?if(!isset($show_menu))
 {//Забил хуй на хуки ядра , тк пока не надо - статичность же!
 if(isset($rights) && $rights==2)
 {?>         
<div id="menu" class="menu"> 
	<div id="menuitems" class="menuitems"> 
		<div id="menuitem1" class="menuitem"><a href="/search" class="hrefmenu" >Поиск пользователей</a></div> 
		<div id="menuitem4" class="menuitem "><a href="/user/logout" class="hrefmenu" >Выход</a></div> 
	</div>
</div> 
<?}
else if(isset($rights) && $rights==3){?>
<div id="menu" class="menu"> 
	<div id="menuitems" class="menuitems"> 
		<div id="menuitem1" class="menuitem"><a href="/search" class="hrefmenu" >Поиск</a></div> 
		<div id="menuitem2" class="menuitem"><a href="/all" class="hrefmenu" >Управление пользователями</a></div> 
		<div id="menuitem2" class="menuitem"><a href="/tickets" class="hrefmenu" >Все заявки</a></div> 
		<div id="menuitem4" class="menuitem"><a href="/user/logout" class="hrefmenu" >Выход</a></div> 
	</div>
</div> 
<?}else
{?>
<div id="menu" class="menu"> 
	<div id="menuitems" class="menuitems"> 
		<div id="menuitem1" class="menuitem"><a href="/user/search" class="hrefmenu" >Подробная информация по движению</a></div> 
		<div id="menuitem3" class="menuitem "><a href="/user/logout" class="hrefmenu" >Выход</a></div> 
	</div>
</div> 
<?}}?>
    </div>
    

		
