var initialLocation;
var browserSupportFlag =  new Boolean();
var map;
var gltln;
var points = [];
var path;
var geocoder = new google.maps.Geocoder();
var caction;
var point1,point2;
var wcobject;//Current working object
var current_step=0;
var user_points=[];
var creation=0;
var lid=0;
var circle_array=[];
var ca=0;
var circle=[];
var ci=0;
var circle_pos=[];
var intervalID;
var load_e=0;
var line_i=0;
var police=[];
var user_view=0;
var LintervalID;
var ln=[];
var show_point;
var decoded=[];
var decoded_length;
var decoded_interval;






function circle_visible(strt)
{					var point = new google.maps.LatLng(circle_array[strt][1],circle_array[strt][2]);
		
  circle[ci]  = new google.maps.Marker({
      position: point,
      map: map,
      icon: circle_array[strt][3],
	   zIndex:circle_array[strt][5],
	   'id':circle_array[strt][0]
  });
			
			
				
						google.maps.event.addListener(circle[ci], "click", function(event) {

		var myJSONObject = {"Id": circle_array[strt][0]};
		var data = {"method":'delete_holes',"params": myJSONObject};
		$.ajax({url:"http://goodroads.ru/another/api.php",type: 'post',data: 'data=' + $.toJSON(data), success: function(data) {}}); 		


			this.setMap(null);
	}); 

						
					ci++;
}



function delete_circle()
{
	var i=0;
	var inputobj = document.getElementById( "Circle_Id" ); 
	alert(inputobj.value);
	$.post('/hole/delete', {id:inputobj.value}, function(data) 	{	});
}





 function initialize(target) {
 show_point=0;
	     var myOptions = {zoom: 15,disableDoubleClickZoom: true,mapTypeId: google.maps.MapTypeId.ROADMAP};
		map = new google.maps.Map(document.getElementById(target), myOptions);
		
		google.maps.event.addListener(map, "click", function(event) {
				var mapType        = map.mapTypes[map.getMapTypeId()];
				//Данные функции будут работать только в режиме редактора
			
				if(caction==1)add_point_map(event.latLng);//Добавить маркер на карту
				if(caction==2)manage_line_map(event.latLng);//Добавить линию на карту. 
				if(caction==5)manage_user_road(event.latLng);//Добавить линию на карту. 
				
				});
initialLocation = new google.maps.LatLng(59.9334277552709, 30.34217793670655);

				map.setCenter(initialLocation,10);
		if(navigator.geolocation) {
		browserSupportFlag = true;
		navigator.geolocation.getCurrentPosition(function(position) {
		initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
		map.setCenter(initialLocation,15);
	
		var marker = new google.maps.Marker({position: initialLocation,map: map});
      }, function() {});
  } 

	google.maps.event.addListener(map, "load", function(overlay,latlng) {

        });

show_point=1;
var pnt1;
var pnt2;

  google.maps.event.addListener(map, 'idle', function() {
  if(show_point==1)
  {
	  pnt1=map.getBounds().getNorthEast();
	  pnt2=map.getBounds().getSouthWest();
	  for(i=0;i<ci;i++)circle[i].setMap(null);
	$.post('/functions/load', {maxlat:pnt1.lat(),maxlng:pnt1.lng(),minlat:pnt2.lat(),minlng:pnt2.lng(),zoom:10}, function(data) {
	if(data)
	{
			myData = JSON.parse(data);
			l = myData.length;
			ca=0;
	
			for(i=1;i<l; i++)
			{
				circle_array[ca] = new Array();
				circle_array[ca][0]=myData[i].Id;
if( myData[i].Weight<5)circle_array[ca][3]="../css/green8x8.png";
				if(myData[i].Weight>=5 && myData[i].Weight<10){circle_array[ca][3]="../css/green8x8.png";circle_array[ca][5]=10;}
				if(myData[i].Weight>=10 && myData[i].Weight<20){circle_array[ca][3]="../css/yellow8x8.png";circle_array[ca][5]=20;}
				if(myData[i].Weight>=20 && myData[i].Weight<40){circle_array[ca][3]="../css/orange8x8.png";circle_array[ca][5]=30;}
				if(myData[i].Weight>=30 && myData[i].Weight<=50){circle_array[ca][3]="../css/orange8x8.png";circle_array[ca][5]=40;}
				if(myData[i].Weight>=50){circle_array[ca][3]="../css/red8x8.png";circle_array[ca][5]=50;}
				circle_array[ca][1]=myData[i].Lat;
				circle_array[ca][2]=myData[i].Lng;
				circle_array[ca][4]=myData[i].Geocoded;
				
				ca++;
		
			}
		}
		intervalID=setInterval(function (){if(ca-->0){circle_visible(ca);}else{clearInterval(intervalID);}}, 5);

	}); 
		}
  });


 }

  $(document).ready(function() {
  

 });
 
