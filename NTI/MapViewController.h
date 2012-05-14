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

-(void) parseRoute: (NSArray*) routeArray;
-(void) zoomInOnRoute;
-(void) mapWaitingState;
-(void) mapDrawRoute;
-(void) mapStopWaitingState;
-(MKPolyline*) createRouteLine:(NSArray*) normalPointsArray;
-(void) specialPointsDraw:(NSArray*) specialPointsArray withType: (int) pointType andStrong: (int) pointStrong;


@end