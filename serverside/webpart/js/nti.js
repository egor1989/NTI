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
		if (pt[i].length < 6) 
		{	
			return;
		}
		// Filter distance on great circle
		if (dmax>0)
		{	// Don't filter the first one or when tagged
			lon1 = Number(pt[i][1])*Math.PI/180;
			lat1 = Number(pt[i][0])*Math.PI/180;
			filteredPt[j]=pt[i];
			j++;
		}
	}
	for (i=1; i<filteredPt.length; i++)
	{
		var p = new OpenLayers.Geometry.Point(Number(filteredPt[i][1]), Number(filteredPt[i][0]));
		p.transform(new OpenLayers.Projection('EPSG:4326'), this.getProjection());
		speed = filteredPt[i][3];
		var deltaTime = (filteredPt[i][5] - filteredPt[i-1][5])/1000;
	    var color = "#00ff00";



		if(Number(filteredPt[i][10])==1 && Number(filteredPt[i][11])==1)color = "#F0FFFF";
		else if(Number(filteredPt[i][10])==1 && Number(filteredPt[i][11])==2)color = "#7CFC00";
		else if(Number(filteredPt[i][10])==1 && Number(filteredPt[i][11])==3)color = "#228B22";
		
		else if(Number(filteredPt[i][10])==2 && Number(filteredPt[i][11])==1)color = "#FF6347";
		else if(Number(filteredPt[i][10])==2 && Number(filteredPt[i][11])==2)color = "#FF4500";
		else if(Number(filteredPt[i][10])==2 && Number(filteredPt[i][11])==3)color = "#FF0000";
		
		else if(Number(filteredPt[i][10])==3 && Number(filteredPt[i][11])==1)color = "#FFFFE0";
		else if(Number(filteredPt[i][10])==3 && Number(filteredPt[i][11])==2)color = "#EEDD82";
		else if(Number(filteredPt[i][10])==3 && Number(filteredPt[i][11])==3)color = "#FFFF00";
		
		if(Number(filteredPt[i][10])==4 && Number(filteredPt[i][11])==0)color = "#9932CC";

		feature = new OpenLayers.Feature.Vector
		(	new OpenLayers.Geometry.Point(p.x,p.y), 
			{	rot : 360-Number(filteredPt[i][2]),
				speed : Number(filteredPt[i][3]),
				radius : 1+(Number(filteredPt[i][3])/20),
				dist : Number(filteredPt[i][4]),
				time : Number(deltaTime),
				label : filteredPt[i][9],
				typeTurn : String(filteredPt[i][8]),
				severityTurn : Number(filteredPt[i][7]),
				severityAcc : Number(filteredPt[i][6])
			}, { fillColor: color,
			     rotation: 360-Number(filteredPt[i][2]),			// rotation as attribute
				 pointRadius: 1+(Number(filteredPt[i][3])/20),	// radius as attribute
                 strokeColor: color,
				 strokeWidth: 1,
				 graphicName:"circle",
                 fillOpacity: 0.5,
                 cursor:"pointer"
                }
		);
		features.push(feature);
   }
    	

   // Add to the layer
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
                    rotation: "${rot}",			// rotation as attribute
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
				 "Compass : "+e.feature.attributes.rot+"&deg;<br/>"
				+"Speed : "+e.feature.attributes.speed+" km/h<br/>"
				+"Distance : "+e.feature.attributes.dist+" km<br/>"
				+"Time : "+e.feature.attributes.time+"<br/>"
				+"Label : "+e.feature.attributes.label+" <br/>"
				+"TypeTurn: "+e.feature.attributes.typeTurn+" <br/>"
				+"severityTurn: "+e.feature.attributes.severityTurn+" <br/>"
				+"SeverityAcc: "+e.feature.attributes.severityAcc;
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
