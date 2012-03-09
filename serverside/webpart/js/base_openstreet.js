var initialLocation;
var map;
 var layerMarkers;
 var show_type=0;//Показывает какой текущий слой выбран
 var fromProjection;
 var toProjection;
 var goodMarkers;
 var lightMarkers;
 var medMarkers;
 var badMarkers;
 var roads;
 var decoded=[];
var decoded_length;
var decoded_interval;
  
  function visibility_controller()
  {

	
	 if($('#greenCHX').attr('checked')=="checked")goodMarkers.setVisibility(true);
	 else goodMarkers.setVisibility(false);
	 if($('#yellowCHX').attr('checked')=="checked")lightMarkers.setVisibility(true);
	 else lightMarkers.setVisibility(false);
	  if($('#orangeCHX').attr('checked')=="checked") medMarkers.setVisibility(true);
	 else medMarkers.setVisibility(false);
	 if($('#redCHX').attr('checked')=="checked")badMarkers.setVisibility(true);
	 else badMarkers.setVisibility(false);



  }
  
  
  
  
 function change_type(type)
 {

		if(type==1)
		{
		if (goodMarkers)map.removeLayer(goodMarkers);
		if (lightMarkers)map.removeLayer(lightMarkers);
		if (medMarkers)map.removeLayer(medMarkers);
		if (badMarkers)map.removeLayer(badMarkers);
	 
	
			goodMarkers = new OpenLayers.Layer.WMS(
			  "Gradient",
			  "http://188.138.112.98:8080/geoserver/gwc/service/wms",
			  {'layers':'goodroads:goodroads_goodview',
			  	transparent: "true",
			  	name:"goodroads",
tiles:"true",
		        format: "image/png"} );
		      
		        		lightMarkers = new OpenLayers.Layer.WMS(
			  "Gradient",
			  "http://188.138.112.98:8080/geoserver/gwc/service/wms",
			  {'layers':'goodroads:goodroads_yellowview',
			  	transparent: "true",
			  	name:"irregularity",
tiles:"true",
		        format: "image/png"} );
		        		medMarkers = new OpenLayers.Layer.WMS(
			  "Gradient",
			  "http://188.138.112.98:8080/geoserver/gwc/service/wms",
			  {'layers':'goodroads:goodroads_worseview',
			  	transparent: "true",
			  	name:"cracks",
				tiles:"true",
		        format: "image/png"} );
		        		badMarkers = new OpenLayers.Layer.WMS(
			  "Gradient",
			  "http://188.138.112.98:8080/geoserver/gwc/service/wms",
			  {'layers':'goodroads:goodroads_badview',
			  	transparent: "true",
			  	name:"holes,trails",
tiles:"true",
		        format: "image/png"} );
		    
			                map.addLayer(goodMarkers);
          
                map.addLayer(lightMarkers);
                map.addLayer(medMarkers);
                map.addLayer(badMarkers);
         

		}
	 else
	 {
		if (goodMarkers)map.removeLayer(goodMarkers);
		if (lightMarkers)map.removeLayer(lightMarkers);
		if (medMarkers)map.removeLayer(medMarkers);
		if (badMarkers)map.removeLayer(badMarkers);
	        goodMarkers = new OpenLayers.Layer.Markers("delightful driving");
			lightMarkers = new OpenLayers.Layer.Markers("irregularity");
			medMarkers = new OpenLayers.Layer.Markers("cracks");
			badMarkers = new OpenLayers.Layer.Markers("holes, bumps, rails");
				goodMarkers.setZIndex( 10 );
                lightMarkers.setZIndex( 11 );
                medMarkers.setZIndex( 12 );
                badMarkers.setZIndex( 13 );
                map.addLayer(goodMarkers);
                map.addLayer(lightMarkers);
                map.addLayer(medMarkers);
                map.addLayer(badMarkers);
	 	 visibility_controller();
	  var res=map.getExtent().transform(toProjection,fromProjection);;
       var maxlng=res.right;
    var minlng=res.left;
    var maxlat=res.top;
    var minlat=res.bottom;

     $.post('/functions/load_temp', {maxlng: maxlng,maxlat:maxlat,minlng:minlng,minlat:minlat,zoom:map.getZoom()}, function(data) {
	if(data)
	{
			myData = JSON.parse(data);
			l = myData.length;
			var ca;
			for(i=1;i<l; i++)
			{
			
				if( myData[i].Weight<5)ca="../css/green8x8.png";
				if(myData[i].Weight>=5 && myData[i].Weight<10)ca="../css/green8x8.png";;
				if(myData[i].Weight>=10 && myData[i].Weight<20)ca="../css/yellow8x8.png";
				if(myData[i].Weight>=20 && myData[i].Weight<40)ca="../css/orange8x8.png";
				if(myData[i].Weight>=30 && myData[i].Weight<=50)ca="../css/orange8x8.png";
				if(myData[i].Weight>=50)ca="../css/red8x8.png";
							
				var size = new OpenLayers.Size(10,10);
				var icon = new OpenLayers.Icon(ca, size);
						var point = new OpenLayers.LonLat(myData[i].Lng,myData[i].Lat).transform(fromProjection,toProjection);
				if(myData[i].Weight<10)goodMarkers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(myData[i].Lng,myData[i].Lat).transform(fromProjection,toProjection),icon));					
				if(myData[i].Weight>=10 && myData[i].Weight<20)lightMarkers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(myData[i].Lng,myData[i].Lat).transform(fromProjection,toProjection),icon));					
				if(myData[i].Weight>=20 && myData[i].Weight<50)medMarkers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(myData[i].Lng,myData[i].Lat).transform(fromProjection,toProjection),icon));					
				if(myData[i].Weight>=50)badMarkers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(myData[i].Lng,myData[i].Lat).transform(fromProjection,toProjection),icon));					

			}
		}
		

	}); 
		 map.events.register("moveend", map, function() {
       
       	goodMarkers.clearMarkers();
       	lightMarkers.clearMarkers();
       	medMarkers.clearMarkers();
       	badMarkers.clearMarkers();
		var res=map.getExtent().transform(toProjection,fromProjection);;
    

     
 
    var maxlng=res.right;
    var minlng=res.left;
    var maxlat=res.top;
    var minlat=res.bottom;

     $.post('/functions/load_temp', {maxlng: maxlng,maxlat:maxlat,minlng:minlng,minlat:minlat,zoom:map.getZoom()}, function(data) {
	if(data)
	{
			myData = JSON.parse(data);
			l = myData.length;
			var ca;
			for(i=1;i<l; i++)
			{
			
				if( myData[i].Weight<5)ca="../css/green8x8.png";
				if(myData[i].Weight>=5 && myData[i].Weight<10)ca="../css/green8x8.png";;
				if(myData[i].Weight>=10 && myData[i].Weight<20)ca="../css/yellow8x8.png";
				if(myData[i].Weight>=20 && myData[i].Weight<40)ca="../css/orange8x8.png";
				if(myData[i].Weight>=30 && myData[i].Weight<=50)ca="../css/orange8x8.png";
				if(myData[i].Weight>=50)ca="../css/red8x8.png";
							
				var size = new OpenLayers.Size(10,10);
				var icon = new OpenLayers.Icon(ca, size);
						var point = new OpenLayers.LonLat(myData[i].Lng,myData[i].Lat).transform(fromProjection,toProjection);
				if(myData[i].Weight<10)goodMarkers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(myData[i].Lng,myData[i].Lat).transform(fromProjection,toProjection),icon));					
				if(myData[i].Weight>=10 && myData[i].Weight<20)lightMarkers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(myData[i].Lng,myData[i].Lat).transform(fromProjection,toProjection),icon));					
				if(myData[i].Weight>=20 && myData[i].Weight<50)medMarkers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(myData[i].Lng,myData[i].Lat).transform(fromProjection,toProjection),icon));					
				if(myData[i].Weight>=50)badMarkers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(myData[i].Lng,myData[i].Lat).transform(fromProjection,toProjection),icon));					

			}
		}
		

	});     
});
}
 }
 function initialize(target) {
 var lon = 30.31619;
	var lat = 59.92767;
	var zoom = 12;
	 fromProjection = new OpenLayers.Projection("EPSG:4326"); // transform from WGS 1984
     toProjection = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
			map = new OpenLayers.Map (target, {
				controls:[
					new OpenLayers.Control.Navigation(),
					new OpenLayers.Control.PanZoomBar(),
					new OpenLayers.Control.Permalink(),
					new OpenLayers.Control.Attribution()],
				maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
				maxResolution: 156543.0399,
				numZoomLevels: 19,
				units: 'm',
				projection: new OpenLayers.Projection("EPSG:900913"),
				displayProjection: new OpenLayers.Projection("EPSG:4326")
			} );

			OpenLayers.Layer.OSM.Toolserver = OpenLayers.Class(OpenLayers.Layer.OSM, {
			initialize: function(name, options) {
					var url = [
						"http://a.www.toolserver.org/tiles/" + name + "/${z}/${x}/${y}.png", 
						"http://b.www.toolserver.org/tiles/" + name + "/${z}/${x}/${y}.png", 
						"http://c.www.toolserver.org/tiles/" + name + "/${z}/${x}/${y}.png"
					];
					options = OpenLayers.Util.extend({numZoomLevels: 19}, options);
					OpenLayers.Layer.OSM.prototype.initialize.apply(this, [name, url, options]);
				},
				CLASS_NAME: "OpenLayers.Layer.OSM.Toolserver"
			});
			// basemap
			map.addLayer(new OpenLayers.Layer.OSM.Toolserver('bw-noicons'));
	
			var proj = new OpenLayers.Projection("EPSG:4326");
			var initialLocation = new OpenLayers.LonLat(30.34217793670655,59.9334277552709);
			initialLocation.transform(proj, map.getProjectionObject());
			map.setCenter(initialLocation);
			map.zoomTo(15);
			
			if(navigator.geolocation) {
			browserSupportFlag = true;
			navigator.geolocation.getCurrentPosition(function(position) {
			initialLocation = new OpenLayers.LonLat(position.coords.longitude,position.coords.latitude);
			initialLocation.transform(proj, map.getProjectionObject());

			map.setCenter(initialLocation);
			map.zoomTo(15);
      }, function() {});
  } 
change_type(1);
load_to_decode();
 }
 
  function recode(e)
{

var point = new google.maps.LatLng(decoded[e][1],decoded[e][2]);
var request = { origin:point,  destination:point,travelMode: google.maps.TravelMode.WALKING}; 
var directionsService = new google.maps.DirectionsService();

directionsService.route(request, function(response, status) 
{ 
	
	if (status == google.maps.DirectionsStatus.OK) 
		{ 
			var steps = response.routes[0].legs[0].steps; 
			for(var step = 0; step < 1; step++) 
			{
				var circle_lat=steps[step].start_point.lat();
				var circle_lng=steps[step].start_point.lng();
				
			
				var myJSONObject = {"Lat": circle_lat, "Lng":circle_lng,"Result":"yes", "Id": decoded[e][0]};
				var data = {"method":'updateGEO',"params": myJSONObject};

				$.ajax({url:"http://goodroads.ru/another/api.php",type: 'post',data: 'data=' + $.toJSON(data), success: function(data) {}}); 
				
			} 
		}
		if(status==google.maps.DirectionsStatus.ZERO_RESULTS)
		{
				var myJSONObject = {"Lat": decoded[e][1], "Lng":decoded[e][2],"Result":"no", "Id": decoded[e][0]};
				var data = {"method":'updateGEO',"params": myJSONObject};
				$.ajax({url:"http://goodroads.ru/another/api.php",type: 'post',data: 'data=' + $.toJSON(data), success: function(data) {}}); 		
		}
		
});
}

  function load_to_decode()
{
	decoded_length=0;
	$.post('/functions/get_to_decode', {}, function(data) {
	if(data)
	{
		
			myData = JSON.parse(data);
			l = myData.length;
			for(i=1;i<l; i++)
			{
				decoded[decoded_length] = new Array();
				decoded[decoded_length][0]=myData[i].Id;
				decoded[decoded_length][1]=myData[i].Lat;
				decoded[decoded_length][2]=myData[i].Lng;
				decoded_length++;
			}
		}
		decoded_length--;
	
	decoded_interval=setInterval(function (){if(decoded_length>0){recode(decoded_length);decoded_length--;}else{clearInterval(decoded_interval);}}, 6000);

	});
	
}
 

 function sendmail()
 {
 mail=$("#subject_mail").val();
 text=$("#text_mail").val();
 address=$("#address_mail").val();
$.post('/mail', {mail:mail,text:text,address:address}, function(data) {});
SetVisibilityFeedBox(0);
 }
 
  
  $(document).ready(function() {
  $('#btnSetDotMap').click(function() {
  change_type(0);
});
$('#btnSetGradMap').click(function() {
  change_type(1);
});
  $('#greenCHX').click(function() {
  visibility_controller();
});
  $('#yellowCHX').click(function() {
  visibility_controller();
});
  $('#orangeCHX').click(function() {
  visibility_controller();
});
  $('#redCHX').click(function() {
  visibility_controller();
});

 });
 
