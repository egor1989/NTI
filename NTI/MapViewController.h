//
//  MapViewController.h
//  NTI
//
//  Created by Mike on 10.04.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <MapKit/MapKit.h>
#import "ServerCommunication.h"
#import "SBJsonParser.h"

@interface MapViewController : UIViewController <MKMapViewDelegate>{
    
	// the map view
	MKMapView* _mapView;
	
	// the data representing the route points. 
	MKPolyline* _routeLine;
	
    
	// the view we create for the line on the map
//	MKPolylineView* routeLineView;
	
	// the rect that bounds the loaded points
	MKMapRect _routeRect;
    
    IBOutlet UIActivityIndicatorView *waintingIndicator;
    IBOutlet UIView *grayView;
    
    ServerCommunication *serverCommunication;
    
    MKCircle *circleSpecial;
    bool isFirstRect;
    
    MKMapPoint northEastPoint; 
	MKMapPoint southWestPoint; 
}

@property (nonatomic, retain) IBOutlet MKMapView* mapView;
@property (nonatomic, retain) MKPolyline* routeLine;
//@property (nonatomic, retain) MKPolylineView* routeLineView;

// load the points of the route from the data source, in this case
// a CSV file. 
-(void) loadRoute: (NSArray*) routeArray;

// use the computed _routeRect to zoom in on the route. 
-(void) zoomInOnRoute;
-(void) mapWaitingState;
-(void) mapDrawRoute;
-(MKPolyline*) normalPointsDraw:(NSArray*) normalPointsArray;
-(void) specialPointsDraw:(NSArray*) specialPointsArray: (int) pointType;


@end