//
//  MapViewController.m
//  NTI
//
//  Created by Mike on 10.04.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "MapViewController.h"

@implementation MapViewController
@synthesize mapView = _mapView;
@synthesize routeLine = _routeLine;
@synthesize routeLineView = routeLineView;

//routePointsReceived

// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
    [super viewDidLoad];
    
    [_mapView setDelegate:self];
    serverCommunication = [[ServerCommunication alloc]init ];
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(mapWaitingState:)
     name: @"routePointsRequestSend"
     object: nil];
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(mapDrawRoute:)
     name: @"routePointsReceived"
     object: nil];
    
	// create the overlay
//	[self loadRoute];
	
	// add the overlay to the map
	if (nil != self.routeLine) {
		[self.mapView addOverlay:self.routeLine];
	}
	
	// zoom in on the route. 
	[self zoomInOnRoute];
	
}

-(void) mapWaitingState: (NSNotification*) TheNotice{
    waintingIndicator.hidden = NO;
    [waintingIndicator startAnimating];
    grayView.hidden = NO;
    NSLog(@"getRoute");
    [serverCommunication getRouteFromServer:[[TheNotice object] timeIntervalSince1970]];
}

-(void) mapDrawRoute: (NSNotification*) TheNotice{
    [waintingIndicator stopAnimating];
    grayView.hidden = YES;
    
	[self loadRoute:[TheNotice object]];
    //возможно понадобится чистить слой 
	[self zoomInOnRoute];
}

-(void) loadRoute: (NSString*) routeString
{
    SBJsonParser *jsonParser = [SBJsonParser new];
    NSArray *routeArray = [[NSArray alloc] init ];
    routeArray = [jsonParser objectWithString:routeString error:NULL];
    NSArray *pointsArray = [[NSArray alloc] init ]; 
    pointsArray = [routeArray valueForKey:@"result"]; 
//    NSArray *error = [answer valueForKey:@"error"];
//    NSString *info = [error valueForKey:@"info"];
//    NSInteger code = [[error valueForKey:@"code"] intValue];
//    NSLog(@"result=%@ info=%@ code=%d", result, info, code);
    NSArray *point = [[NSArray alloc] init];
    NSMutableArray *normalPointsArray = [[NSMutableArray alloc] init];
    NSMutableArray *leftTurnStartedPointsArray = [[NSMutableArray alloc] init];
    
    if ([pointsArray isEqual: @"empty"]){
        NSLog(@"noHoles");
    }
    else
        for (point in pointsArray){
            if ([[point valueForKey:@"type"] isEqual:@"normal point"]){                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [normalPointsArray addObject:latLngArray];
            }
            else if ([[point valueForKey:@"type"] isEqual:@"left turn started"]){
                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [leftTurnStartedPointsArray addObject:latLngArray];
            }
        }
    [self normalPointsDraw:normalPointsArray];
    [self leftTurnStartedPointsDraw:leftTurnStartedPointsArray];
		
}

-(void) normalPointsDraw:(NSArray*) normalPointsArray{
    MKMapPoint northEastPoint; 
	MKMapPoint southWestPoint; 
    
	MKMapPoint* pointArr = malloc(sizeof(CLLocationCoordinate2D) * normalPointsArray.count);
    
	for(int idx = 0; idx < normalPointsArray.count; idx++)
	{
		NSArray* currentPoint = [normalPointsArray objectAtIndex:idx];
        
		CLLocationDegrees latitude  = [[currentPoint objectAtIndex:0] doubleValue];
		CLLocationDegrees longitude = [[currentPoint objectAtIndex:1] doubleValue];
		CLLocationCoordinate2D coordinate = CLLocationCoordinate2DMake(latitude, longitude);
		MKMapPoint point = MKMapPointForCoordinate(coordinate);
        
		if (idx == 0) {
			northEastPoint = point;
			southWestPoint = point;
		}
		else 
		{
			if (point.x > northEastPoint.x) 
				northEastPoint.x = point.x;
			if(point.y > northEastPoint.y)
				northEastPoint.y = point.y;
			if (point.x < southWestPoint.x) 
				southWestPoint.x = point.x;
			if (point.y < southWestPoint.y) 
				southWestPoint.y = point.y;
		}
        
		pointArr[idx] = point;
        
	}
    
	self.routeLine = [MKPolyline polylineWithPoints:pointArr count:normalPointsArray.count];
	_routeRect = MKMapRectMake(southWestPoint.x, southWestPoint.y, northEastPoint.x - southWestPoint.x, northEastPoint.y - southWestPoint.y);
    
	free(pointArr);
    
	if (nil != self.routeLine) {
		[self.mapView addOverlay:self.routeLine];
	}

}

-(void) leftTurnStartedPointsDraw:(NSArray*) leftTurnStartedPointsArray{
	for(int idx = 0; idx < leftTurnStartedPointsArray.count; idx++)
	{
		NSArray* currentPoint = [leftTurnStartedPointsArray objectAtIndex:idx];
        
		CLLocationDegrees latitude  = [[currentPoint objectAtIndex:0] doubleValue];
		CLLocationDegrees longitude = [[currentPoint objectAtIndex:1] doubleValue];
		CLLocationCoordinate2D coordinate = CLLocationCoordinate2DMake(latitude, longitude);
        MKCircle *circle = [MKCircle circleWithCenterCoordinate:coordinate radius:5];
        [self.mapView addOverlay:circle];
        
	}
    
}

-(void) zoomInOnRoute
{
	[self.mapView setVisibleMapRect:_routeRect];
}

- (void)didReceiveMemoryWarning {
	// Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
	
}


- (void)dealloc 
{
	self.mapView = nil;
	self.routeLine = nil;
	self.routeLineView = nil;
}


#pragma mark MKMapViewDelegate
- (MKOverlayView *)mapView:(MKMapView *)mapView viewForOverlay:(id <MKOverlay>)overlay
{
	MKOverlayView* overlayView = nil;
	
	if(overlay == self.routeLine)
	{
		//if we have not yet created an overlay view for this overlay, create it now. 
		if(nil == self.routeLineView)
		{
			routeLineView = [[MKPolylineView alloc] initWithPolyline:self.routeLine];
			routeLineView.fillColor = [UIColor blackColor];
			self.routeLineView.strokeColor = [UIColor blackColor];
			self.routeLineView.lineWidth = 3;
		}
		
		overlayView = self.routeLineView;
		
        return overlayView;
	}
	
    
//    if(overlay == self.routeLine)
//	{
        MKCircleView *circleView = [[MKCircleView alloc] initWithCircle:overlay];
        circleView.lineWidth = 8.0;
        circleView.strokeColor = [UIColor redColor];
        return circleView;
//    }
	
}
@end
