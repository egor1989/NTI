var map;
// Filter distance (km)
var dmax = 0.001;

function initGMap() {
    map = new OpenLayers.Map('map', {allOverlays: true});
    map.addControl(new OpenLayers.Control.LayerSwitcher());
    
    var gsat = new OpenLayers.Layer.Google(
        "Google Satellite",
        {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22, visibility: false}
    );
    var gphy = new OpenLayers.Layer.Google(
        "Google Physical",
        {type: google.maps.MapTypeId.TERRAIN, visibility: false}
    );
    var gmap = new OpenLayers.Layer.Google(
        "Google Streets", // the default
        {numZoomLevels: 20, visibility: true}
    );
    var ghyb = new OpenLayers.Layer.Google(
        "Google Hybrid",
        {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 22, visibility: false}
    );

    map.addLayers([gsat, gphy, gmap, ghyb]);

    // Google.v3 uses EPSG:900913 as projection, so we have to
    // transform our coordinates
    map.setCenter(new OpenLayers.LonLat(10.2, 48.9).transform(
        new OpenLayers.Projection("EPSG:4326"),
        map.getProjectionObject()
    ), 5);
}

function handler(request) 
{   document.getElementById('wait').style.display='none';
	// Response as text
    if(!request.responseText) 
    {	
		return;
	}
	var i;
	var t = request.responseText.replace(/\r/g,'').split('\n');
	var features = new Array();
	var pt = new Array();
	var filteredPt = new Array();
	var j=0;
	for (i=0; i<t.length; i++) if (t[i]!="")
	{	// New point 
		pt[i] = t[i].split(',');
		// Filter distance on great circle
		if (dmax>0)
		{	// Don't filter the first one or when tagged
			lon1 = Number(pt[i][1])*Math.PI/180;
			lat1 = Number(pt[i][0])*Math.PI/180;
			filteredPt[j]=pt[i];
			j++;
		}
	}
	  var points = [];
	for (i=1; i<filteredPt.length; i++)
	{
		var p = new OpenLayers.Geometry.Point(Number(filteredPt[i][1]), Number(filteredPt[i][0]));
		p.transform(new OpenLayers.Projection('EPSG:4326'), this.getProjection());
		speed = filteredPt[i][3];
		var color = "#00ff00";
		var type=0;
		if((filteredPt[i][4])=="Acc" && Number(filteredPt[i][5])==1){color = "#00FF00";type=0.3;}
		else if((filteredPt[i][4])=="Acc" && Number(filteredPt[i][5])==2){color = "#00FF00";type=0.5;}
		else if((filteredPt[i][4])=="Acc" && Number(filteredPt[i][5])==3){color = "#00FF00";type=0.8;}
		else if((filteredPt[i][4])=="Brake" && Number(filteredPt[i][5])==1){color = "#FF0000";type=0.3;}
		else if((filteredPt[i][4])=="Brake" && Number(filteredPt[i][5])==2){color = "#FF0000";type=0.5;}
		else if((filteredPt[i][4])=="Brake" && Number(filteredPt[i][5])==3){color = "#FF0000";type=0.8;}
			else if((filteredPt[i][4])=="LeftTurn" && Number(filteredPt[i][5])==1){color = "#FFFF00";type=0.3;}
		else if((filteredPt[i][4])=="LeftTurn" && Number(filteredPt[i][5])==2){color = "#FFFF00";type=0.5;}
		else if((filteredPt[i][4])=="LeftTurn" && Number(filteredPt[i][5])==3){color = "#FFFF00";type=0.8;}
		else if((filteredPt[i][4])=="RightTurn" && Number(filteredPt[i][5])==1){color = "#9370DB";type=0.3;}
		else if((filteredPt[i][4])=="RightTurn" && Number(filteredPt[i][5])==2){color = "#9370DB";type=0.5;}
		else if((filteredPt[i][4])=="RightTurn" && Number(filteredPt[i][5])==3){color = "#9370DB";type=0.8;}
		else if((filteredPt[i][4])=="Speed" && Number(filteredPt[i][5])==1){color = "#808080";type=0.3;}
		else if((filteredPt[i][4])=="Speed" && Number(filteredPt[i][5])==2){color = "#808080";type=0.5;}
		else if((filteredPt[i][4])=="Speed" && Number(filteredPt[i][5])==3){color = "#808080";type=0.8;}

 
  
    points.push(new OpenLayers.Geometry.Point(p.x,p.y), 
			{	
				speed : Number(filteredPt[i][2]),
				radius : 1+(Number(filteredPt[i][5])),
				time : filteredPt[i][3],
				label : filteredPt[i][4],
				typeTurn : filteredPt[i][5],
				typeG:filteredPt[i][6],
				duration:filteredPt[i][7]
			});


		feature = new OpenLayers.Feature.Vector
		(	new OpenLayers.Geometry.Point(p.x,p.y), 
			{	
				speed : Number(filteredPt[i][2]),
				radius : 4,
				time : filteredPt[i][3],
				label : filteredPt[i][4],
				typeTurn : filteredPt[i][5],
				typeG:filteredPt[i][6]
			}, { fillColor: color,
				 pointRadius: 4,
                 strokeColor: color,
				 strokeWidth: type,
				 graphicName:"circle",
                 fillOpacity: type,
                   strokeOpacity: 0.1,
                 cursor:"pointer"
                }
		);
		if((filteredPt[i][4])!="Normal")
		features.push(feature);
   }
    	 var style_green =
    {
        strokeColor: "#00FF00",
        strokeOpacity: 0.2,
        strokeWidth: 6
    };
 var lineString = new OpenLayers.Geometry.LineString(points);
    var lineFeature = new OpenLayers.Feature.Vector(lineString, null, style_green);

   // Add to the layer

   this.amLayer.addFeatures(lineFeature);
      this.amLayer.addFeatures(features);
  	// Center
	var e = this.amLayer.getDataExtent();
	if (e) this.zoomToExtent(e);
}

function init()
{	// Init an OpenLayers Google Map
	initGMap();
	
	// Legend
	var styleMap = new OpenLayers.StyleMap({
                "default": new OpenLayers.Style({
					pointRadius: "${radius}",	// radius as attribute
					graphicName:"circle",
                    fillColor: "#fc6",
                    fillOpacity: 0.5,
                    strokeColor: "#fc6",
                    strokeWidth: 2,
                    cursor:"pointer"
                }),
                "select": new OpenLayers.Style({
                    strokeWidth: 4,
                    fillColor: "#6cf",
                    strokeColor: "#39f"
                })
            });

	// Add a new layer
	var layer = new OpenLayers.Layer.Vector("NTI",{ styleMap: styleMap, opacity:1, visibility:1 });
	map.amLayer = layer;
	map.addLayer(layer);

    // Create a select feature control and add it to the map.
    var select = new OpenLayers.Control.SelectFeature(layer, {
        hover: true
    });
    map.addControl(select);
    select.activate();
    // Show information when select
    layer.events.on
    ({  "featureselected": function(e) {
            document.getElementById("status").innerHTML = 
				"Speed : "+e.feature.attributes.speed+" km/h<br/>"
				+"Time : "+e.feature.attributes.time+"<br/>"
				+"Duration : "+e.feature.attributes.duration+" <br/>"
				+"Label : "+e.feature.attributes.label+" <br/>"
				+"G : "+e.feature.attributes.typeG+" <br/>"
				+"Weight: "+e.feature.attributes.typeTurn+" <br/>";
        },
        "featureunselected": function(e) { document.getElementById("status").innerHTML = ""; }
    });

	// AddFeatures
	var request = OpenLayers.Request.GET
	({	url: "/functions/get",
		callback: handler,
		scope:map
	});
	
}
