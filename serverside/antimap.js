
var map;
// Filter distance (km)
var dmax = 0.001;

function initGMap() {
    map = new OpenLayers.Map('map', {allOverlays: true});
    map.addControl(new OpenLayers.Control.LayerSwitcher());
    
    var gsat = new OpenLayers.Layer.Google(
        "Google Satellite",
        {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22}
    );
    var gphy = new OpenLayers.Layer.Google(
        "Google Physical",
        {type: google.maps.MapTypeId.TERRAIN, visibility: false}
    );
    var gmap = new OpenLayers.Layer.Google(
        "Google Streets", // the default
        {numZoomLevels: 20, visibility: false}
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

// length on great circle (km)
function gCercle (lon1, lat1, lon2, lat2)
{	return 2 * 6367 *
			Math.asin( Math.sqrt ( 
					Math.pow(Math.sin((lat1-lat2)/2),2)
				+	( Math.cos(lat1) * Math.cos(lat2) * Math.pow(Math.sin((lon1-lon2)/2),2))
				));
}

function handler(request) 
{   document.getElementById('wait').style.display='none';
	// Response as text
    if(!request.responseText) 
    {	alert ('ERROR');
		return;
	}
	
	var deltaSpeed=0;
	var exSpeed=0;
	var acc1=0;
	var acc2=0;
	var acc3=0;
	var brake1=0;
	var brake2=0;
	var brake3=0;
	var turn1=0;
	var turn2=0;
	var turn3=0;
	var accel = new Array();
	var turn = new Array();
	var typeTurn = new Array();
	var typeAcc = new Array();
	var acc = 0;
	var i;
	var t = request.responseText.replace(/\r/g,'').split('\n');
	var features = new Array();
	var pt = new Array();
	var filteredPt = new Array();
	var j=0;
	//читаем из файла и фильтруем
	for (i=0; i<t.length; i++) if (t[i]!="")
	{	// New point 
		pt[i] = t[i].split(',');
		if (pt[i].length < 6) 
		{	alert ("ERROR : "+t[i]);
			return;
		}
		// Filter distance on great circle
		if (dmax>0)
		{	// Don't filter the first one or when tagged
			if (pt[i][6]=="" && i>0)
			{	// Distance
				var d = gCercle(lon1,lat1, Number(pt[i][1])*Math.PI/180,Number(pt[i][0])*Math.PI/180);
				if (d < dmax) continue;
			}
			lon1 = Number(pt[i][1])*Math.PI/180;
			lat1 = Number(pt[i][0])*Math.PI/180;
			filteredPt[j]=pt[i];
			j++;
		}
	}
	for (i=1; i<filteredPt.length; i++)
	{
	// Add the feature
		var p = new OpenLayers.Geometry.Point(Number(filteredPt[i][1]), Number(filteredPt[i][0]));
		p.transform(new OpenLayers.Projection('EPSG:4326'), this.getProjection());
		typeTurn[0] = "normal point";
		typeAcc[0] = "normal point";
		var sevTurn = 0;
		var sevAcc = 0;
		speed = filteredPt[i][3];
		var deltaTime = (filteredPt[i][5] - filteredPt[i-1][5])/1000;
		if ((i!=0)&&((filteredPt[i][1]-filteredPt[i-1][1])!=0)){
			turn[i] = Math.atan((filteredPt[i][0]-filteredPt[i-1][0])/(filteredPt[i][1]-filteredPt[i-1][1]));
			turn[0] = 0;
			var deltaTurn = turn[i] - turn[i-1];
			wAcc = Math.abs(deltaTurn/deltaTime);
			radius = speed/wAcc;
			//var acc = (filteredPt[i][3] - filteredPt[i-1][3])/(filteredPt[i][5] - filteredPt[i][5])/3600;
			if (speed > 70) exSpeed++;
			if ((wAcc<1)||(isNaN(wAcc))) {
			  sevTurn = 0;
			} else if (wAcc<2){
			  sevTurn = 1;
			  turn1++;
			} else if (wAcc<3){
			  sevTurn = 2;
			  turn2++;
			} else {
			  sevTurn = 3;
			  turn3++;
			}
			if ((typeTurn[i-1] == "left turn finished")||(typeTurn[i-1] == "right turn finished")||(typeTurn[i-1] == undefined)||(speed == 0)){
				typeTurn[i] = "normal point";
			} else if (deltaTurn > 0.05){
				if (typeTurn[i-1] == "normal point") typeTurn[i] = "left turn started";
				if ((typeTurn[i-1] == "left turn started")||(typeTurn[i-1] == "left turn continued")) typeTurn[i] = "left turn continued";
				if ((typeTurn[i-1] == "right turn started")||(typeTurn[i-1] == "right turn continued")) typeTurn[i] = "right turn finished";
			} else if (deltaTurn < -0.05){
			    if (typeTurn[i-1] == "normal point") typeTurn[i] = "right turn started";
				if ((typeTurn[i-1] == "right turn started")||(typeTurn[i-1] == "right turn continued")) typeTurn[i] = "right turn continued";
				if ((typeTurn[i-1] == "left turn started")||(typeTurn[i-1] == "left turn continued")) typeTurn[i] = "left turn finished";
			} else {
			    if (typeTurn[i-1] == "normal point") typeTurn[i] = "normal point";
				if ((typeTurn[i-1] == "left turn started")||(typeTurn[i-1] == "left turn continued")) typeTurn[i] = "left turn finished";
				if ((typeTurn[i-1] == "right turn started")||(typeTurn[i-1] == "right turn continued")) typeTurn[i] = "right turn finished";
			} 
		} else {
		  typeTurn[i] = "normal point";
		  sevTurn[i] = 0;
		  wAcc = 0;
		  radius = 0;
		}
		
		var timeSum = 0;
		var sumSpeed = 0;
		if ((i!=0)&&(deltaTime!=0)){
			deltaSpeed = speed - filteredPt[i-1][3];
			accel[i] = deltaSpeed/deltaTime;
			if (accel[i]<-7.5) {
			  sevAcc = -3;
			  brake3++;
			} else if (accel[i]<-6){
			  sevAcc = -2;
			  brake2++;
			} else if (accel[i]<-4.5){
			  sevAcc = -1;
			  brake1++;
			} else if (accel[i]>5){
			  sevAcc = 3;
			  acc3++;
			} else if (accel[i]>4){
			  sevAcc = 2;
			  acc2++;
			} else if (accel[i]>3.5){
			  sevAcc = 1;
			  acc1++;
			} else {
			  sevAcc = 0;
			}
		}
			/*if (typeAcc[i-1]=="normal point") {
			  if (deltaSpeed > 0){
			    typeAcc = "start accel";
			    accel[i] = deltaSpeed/deltaTime;
			    timeSum = filteredPt[i][5];
			    sumSpeed = speed;
		      }
			  if (deltaSpeed < 0) {
			    typeAcc = "start brake";
			    accel[i] = deltaSpeed/deltaTime;
			    timeSum = filteredPt[i][5];
			    sumSpeed = speed;
		    } else {
			    typeAcc = "normal point";
			  }
			}
			if (typeAcc[i-1]=="start accel") {
			  if (deltaSpeed > 0){
			    typeAcc = "continue accel";
			    accel[i] = deltaSpeed/deltaTime;
			    sumSpeed += speed;
				timeSum += filteredPt[i][5];
		      }
			  if (deltaSpeed < 0) {
			    typeAcc = "start brake";
			    accel[i] = deltaSpeed/deltaTime;
			    timeSum = filteredPt[i][5];
			    sumSpeed = speed;
		    } else {
			    typeAcc = "normal point";
			  }
			}
			if (typeAcc[i-1]=="start brake") {
			  if (deltaSpeed > 0){
			    typeAcc = "start accel";
			    accel[i] = deltaSpeed/deltaTime;
			    sumSpeed = speed;
		      }
			  if (deltaSpeed < 0) {
			    typeAcc = "continue brake";
			    accel[i] = deltaSpeed/deltaTime;
			    curTimeStart = filteredPt[i][5];
			    sumSpeed = speed;
		    } else {
			    typeAcc = "normal point";
			  }
			}*/
		/*for acc and brake*/
	    var color = "white";
		if (sevAcc==1) color = "#c3eb0d";
		if (sevAcc==2) color = "#0deb12";
		if (sevAcc==3) color = "#0deb88";
	    if (sevAcc==-1) color = "#ebc10d";
		if (sevAcc==-2) color = "#eb610d";
		if (sevAcc==-3) color = "#eb0d1b";
		
		/* for cornering
		if (sevTurn==1) color = "green";
		if (sevTurn==2) color = "#fc6";
		if (sevTurn==3) color = "black";
		*/
		feature = new OpenLayers.Feature.Vector
		(	new OpenLayers.Geometry.Point(p.x,p.y), 
			{	rot : 360-Number(filteredPt[i][2]),
				speed : Number(filteredPt[i][3]),
				radius : 3+(Number(filteredPt[i][3])/10),
				dist : Number(filteredPt[i][4]),
				time : Number(deltaTime),
				label : Number(filteredPt[i][6]),
				typeTurn : String(typeTurn[i]),
				radiusTurn: Number(radius),
				wAcc : Number(wAcc),
				severityTurn : Number(sevTurn),
				typeAcc : String(typeAcc[i]),
				severityAcc : Number(sevAcc)
			}, { fillColor: color,
			     rotation: 360-Number(filteredPt[i][2]),			// rotation as attribute
				 pointRadius: 3+(Number(filteredPt[i][3])/10),	// radius as attribute
                 strokeColor: color,
				 strokeWidth: 1,
				 graphicName:"triangle",
                 fillOpacity: 0.5,
                 cursor:"pointer"
                }
		);
		features.push(feature);
   }
   
	alert("количество поворотов по жесткости" + turn1+ " " + turn2 + " " + turn3);
	alert("превышение скорости в сек" + exSpeed);
	alert("количество резких ускорений по жесткости" + acc1+ " " + acc2 + " " + acc3);
	alert("количество резких торможений по жесткости" + brake1 + " " + brake2 + " " +brake3);
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
					graphicName:"triangle",
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
				+"RadiusTurn: "+e.feature.attributes.radiusTurn+" <br/>"
				+"wAcc: "+e.feature.attributes.wAcc+" <br/>"
				+"severityTurn: "+e.feature.attributes.severityTurn+" <br/>"
				+"TypeAcc: "+e.feature.attributes.typeAcc+" <br/>"
				+"SeverityAcc: "+e.feature.attributes.severityAcc;
        },
        "featureunselected": function(e) { document.getElementById("status").innerHTML = ""; }
    });

	// AddFeatures
	var request = OpenLayers.Request.GET
	({	url: "data/1.csv",
		callback: handler,
		scope:map
	});
	
}