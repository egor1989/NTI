<div id="content" class="pageContent">
               		  <?$this->load->library('session');
					$lang=$this->session->userdata('language');
        if($lang=="rus"){?>
        <div id="PageHeader" class="pageHeader">
            Статистика
        </div>
        
        <div class="social">
        <!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
<a class="addthis_button_preferred_1"></a>
<a class="addthis_button_preferred_2"></a>
<a class="addthis_button_preferred_3"></a>
<a class="addthis_button_preferred_4"></a>
<a class="addthis_button_compact"></a>
<a class="addthis_counter addthis_bubble_style"></a>
</div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4ebfd2c338fa5981"></script>
<!-- AddThis Button END -->
        
        </div>


       <div class="statContent">
      
      <div style="width: 300px; color: #666; width: 100%; font-size: 12px;">
       Пользователей с приложением GoodRoads <font style="color: #000"><?echo $user_count;?></font>

       <h3>ТОП 20</h3>
          <table border=1 cellspacing=5>
			  <tr>
				  <td>
					  
       <table cellspacing=5>
		    <tr>
		   <td><font style="color: #000">Позиция</font></td>
		<td><font style="color: #000">Имя пользователя </font></td>
		<td><font style="color: #000">Баллы пользователя</font></td>
		<td><font style="color: #000">Километраж</font></td>
       </tr>
		   
		   
		          <? for($i=0;$i<count($userstats)/2;$i++){?>
       <tr>
		   <td><?echo $i+1;?> </td>
		<td><?echo $userstats[$i]['login'];?> </td>
		<td><?echo $userstats[$i]['raiting'];?></td>
		<td><?echo round($userstats[$i]['km'],1);?></td>

       </tr>
       <?}?>
       </table>
       </td>
       <td>
              <table cellspacing=5>
		    <tr>
		   <td><font style="color: #000">Позиция</font></td>
		<td><font style="color: #000">Имя пользователя </font></td>
		<td><font style="color: #000">Баллы пользователя</font></td>
		<td><font style="color: #000">Километраж</font></td>
       </tr>
		   
		   
		          <? for($i=count($userstats)/2;$i<count($userstats);$i++){?>
       <tr>
		   <td><?echo $i+1;?> </td>
		<td><?echo $userstats[$i]['login'];?> </td>
		<td><?echo $userstats[$i]['raiting'];?></td>
		<td><?echo round($userstats[$i]['km'],1);?></td>

       </tr>
       <?}?>
       </table>
       
       
       </td>
       </tr>
       </table>
       </div>

       <div style="font-size: 14px; margin-top:10px;"></div>
       Статистика по городам<br/><br/><br/>
       <span style='color:green'><b>Хороших дорог:</b>      <?echo round($GoodLength/1000,2);?> км.</span><br/>
       <span style='color:yellow'><b>Трещины, неровности:</b>     <?echo round($YellowLength/1000,2);?>км.</span><br/>
       <span style='color:orange'><b>Кочки:</b>    <?echo round($WorseLength/1000,2);?> км.</span><br/>
       <span style='color:red'><b>Ямы, выбоины:</b>    <?echo round($BadLength/1000,2);?> км.</span><br/>
       </div>
       <div style="width: 80%; margin-top: 10px; margin-right: 10%; position: relative; float: left;">            

       <div class="statCity">Санкт-Петербург</div>
       <div class="statCity statCityUnselected">Москва</div>
       <div class="statCity statCityUnselected">Псков</div>
       <div class="statCity statCityUnselected">Хельсинки</div>

       </div><br/>

       
        <div class="statSpbInfo">
       
       </div>
      
       <?}else{?>
              <div id="PageHeader" class="pageHeader">
            Статистика
        </div>
        
        <div class="social">
        <!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
<a class="addthis_button_preferred_1"></a>
<a class="addthis_button_preferred_2"></a>
<a class="addthis_button_preferred_3"></a>
<a class="addthis_button_preferred_4"></a>
<a class="addthis_button_compact"></a>
<a class="addthis_counter addthis_bubble_style"></a>
</div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4ebfd2c338fa5981"></script>
<!-- AddThis Button END -->
        
        </div>


       <div class="statContent">
      
      <div style="width: 300px; color: #666; width: 100%; font-size: 12px;">
     Users with GoodRoads application <font style="color: #000"><?echo $user_count;?></font>
       </div>

       <div style="font-size: 14px; margin-top:10px;"></div>
       Cities statistics<br/><br/>
        <span style='color:green'><b>Goodroads:</b>      <?echo round($GoodLength/1000,2);?> км.</span><br/>
       <span style='color:yellow'><b>Watch out:</b>     <?echo round($YellowLength/1000,2);?>км.</span><br/>
       <span style='color:orange'><b>Shakes:</b>    <?echo round($WorseLength/1000,2);?> км.</span><br/>
       <span style='color:red'><b>Poor Roads:</b>    <?echo round($BadLength/1000,2);?> км.</span><br/>
       </div>
       <div style="width: 80%; margin-top: 10px; margin-right: 10%; position: relative; float: left;">            

       <div class="statCity">Saint-Petersburg</div>
       <div class="statCity statCityUnselected">Moscow</div>
       <div class="statCity statCityUnselected">Pskov</div>
       <div class="statCity statCityUnselected">Helsinki</div>
       </div>
       <div class="statSpbInfo">
       
       </div>
       <?}?>
    </div>
