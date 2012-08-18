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
		if (badMarkers)map.removeLayer(badMarkers);

	
			goodMarkers = new OpenLayers.Layer.WMS(
			  "Gradient",
			  "http://188.138.112.98:8080/geoserver/gwc/service/wms",
			  {'layers':'world:goodroads',
			  	transparent: "true",
			  	name:"roads",
tiles:"true",
		        format: "image/png"} );

		        		lightMarkers = new OpenLayers.Layer.WMS(
			  "Gradient",
			  "http://188.138.112.98:8080/geoserver/gwc/service/wms",
			  {'layers':'world:yellowroads',
			  	transparent: "true",
			  	name:"irregularity",
tiles:"true",
		        format: "image/png"} );
		        		medMarkers = new OpenLayers.Layer.WMS(
			  "Gradient",
			  "http://188.138.112.98:8080/geoserver/gwc/service/wms",
			  {'layers':'world:worseroads',
			  	transparent: "true",
			  	name:"cracks",
				tiles:"true",
		        format: "image/png"} );
		        		badMarkers = new OpenLayers.Layer.WMS(
			  "Gradient",
			  "http://188.138.112.98:8080/geoserver/gwc/service/wms",
			  {'layers':'world:badroads',
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
		
			badMarkers = new OpenLayers.Layer.WMS(
			  "Holes",
			  "http://188.138.112.98:8080/geoserver/gwc/service/wms",
			  {'layers':'world:groupedholes',
			  	transparent: "true",
			  	name:"reallyholes",
				tiles:"true",
		        format: "image/png"} );
		    
			                map.addLayer(badMarkers);
		
		
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
   var mapnik         = new OpenLayers.Layer.OSM();
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
		//	map.addLayer(new OpenLayers.Layer.OSM.Toolserver('bw-noicons'));
	map.addLayer(mapnik);
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
 }
 


 function sendmail()
 {
 mail=$("#subject_mail").val();
 text=$("#text_mail").val();
 address=$("#address_mail").val();
$.post('/mail', {mail:mail,text:text,address:address}, function(data) {});
SetVisibilityFeedBox(0);
 }
 
 
  //Check what you have done
function searchbyname(e){
	var query = e;
	Utils.searchAddress(query, function(places) {
		if (places.length > 0) {
			var place = places[0];
			map.setCenter(new OpenLayers.LonLat(place["lon"], place["lat"])
			                            .transform(map.displayProjection, map.projection));
			                            map.zoomTo(9);
		} else {
			// TODO: handle err, say about it
			console.error("address not found: ", query);
		}
	});
}
 
 
 
 
 
 //Check what you have done
function search(){
	var query = $("#query").val();
	Utils.searchAddress(query, function(places) {
		if (places.length > 0) {
			var place = places[0];
			map.setCenter(new OpenLayers.LonLat(place["lon"], place["lat"])
			                            .transform(map.displayProjection, map.projection));
			                            map.zoomTo(10);
		} else {
			// TODO: handle err, say about it
			console.error("address not found: ", query);
		}
	});
}

window.Utils = {
	searchAddress: function(address, cb) {	
		$.ajax({
			url: "http://nominatim.openstreetmap.org/search",
			dataType: "json",
			data: {
				format: "json",
				q: address,
				addressdetails: 1,
				limit: 10
			},
			global: false,
			success: function(data) {
				cb(data);
			},
			error: function() {
				cb(null);
			}
		});
	},
};

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
 
