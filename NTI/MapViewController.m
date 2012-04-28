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
//@synthesize routeLineView = routeLineView;

//routePointsReceived

// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
    [super viewDidLoad];
//    isUserLoadRoute = NO;
    
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
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(mapStopWaitingState)
     name: @"routePointsReceivedWithError"
     object: nil];
    
    isFirstRect = YES;
    
    if ([ServerCommunication checkInternetConnection]) {
        [serverCommunication getRouteFromServer:0];
    }
	
}

-(void) mapWaitingState: (NSNotification*) TheNotice{
    if ([ServerCommunication checkInternetConnection]){
        waintingIndicator.hidden = NO;
        [waintingIndicator startAnimating];
        grayView.hidden = NO;
        NSLog(@"getRoute");
        [serverCommunication getRouteFromServer:[[TheNotice object] timeIntervalSince1970]];
    }
}

-(void) mapStopWaitingState{
    [waintingIndicator stopAnimating];
    grayView.hidden = YES;
}

-(void) mapDrawRoute: (NSNotification*) TheNotice{
    SBJsonParser *jsonParser = [SBJsonParser new];
    NSArray *answerArray = [[NSArray alloc] init];
    NSArray *routeArray = [[NSArray alloc] init];
    answerArray = [jsonParser objectWithString:[TheNotice object] error:NULL];
    routeArray = [answerArray valueForKey:@"result"];
    
    [waintingIndicator stopAnimating];
    grayView.hidden = YES;
    
    [self.mapView removeOverlays: self.mapView.overlays];
    [self loadRoute:routeArray];
    [self zoomInOnRoute];
}

-(void) loadRoute: (NSArray*) routeArray
{
//    NSArray *error = [answer valueForKey:@"error"];
//    NSString *info = [error valueForKey:@"info"];
//    NSInteger code = [[error valueForKey:@"code"] intValue];
//    NSLog(@"result=%@ info=%@ code=%d", result, info, code);
    NSArray *point = [[NSArray alloc] init];
    NSMutableArray *routeLineArray = [[NSMutableArray alloc] init];
    NSMutableArray *normalPointsArray = [[NSMutableArray alloc] init];
    NSMutableArray *specialPointsArray1 = [[NSMutableArray alloc] init];
    NSMutableArray *specialPointsArray2 = [[NSMutableArray alloc] init];
    NSMutableArray *specialPointsArray3 = [[NSMutableArray alloc] init];
    NSMutableArray *specialPointsArray4 = [[NSMutableArray alloc] init];
    NSMutableArray *specialPointsArray5 = [[NSMutableArray alloc] init];
    NSMutableArray *specialPointsArray6 = [[NSMutableArray alloc] init];
    if ([routeArray isEqual: @"null"]){
        NSLog(@"noHoles");
    }
    else
        for (point in routeArray){
            if ([[point valueForKey:@"type"] doubleValue] == 0){  
                if ([[point valueForKey:@"lat"] doubleValue]>0.1) {
                    NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                    [normalPointsArray addObject:latLngArray];
                }
            }
            else if ([[point valueForKey:@"type"] doubleValue] == -3){
                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [specialPointsArray1 addObject:latLngArray];
            }
            else if ([[point valueForKey:@"type"] doubleValue] == -2){
                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [specialPointsArray2 addObject:latLngArray];
            }
            else if ([[point valueForKey:@"type"] doubleValue] == -1){
                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [specialPointsArray3 addObject:latLngArray];
            }
            else if ([[point valueForKey:@"type"] doubleValue] == 1){
                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [specialPointsArray4 addObject:latLngArray];
            }
            else if ([[point valueForKey:@"type"] doubleValue] == 2){
                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [specialPointsArray5 addObject:latLngArray];
            } 
            else if ([[point valueForKey:@"type"] doubleValue] == 3){
                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [specialPointsArray6 addObject:latLngArray];
            }
            else if ([[point valueForKey:@"type"] doubleValue] == 42){
                if (normalPointsArray.count != 0){
                    [routeLineArray addObject:[self normalPointsDraw:normalPointsArray]];
                }
                [normalPointsArray removeAllObjects];
            }

        }
    
    [routeLineArray addObject:[self normalPointsDraw:normalPointsArray]];
    [self specialPointsDraw:specialPointsArray1:1];
    [self specialPointsDraw:specialPointsArray2:2];
    [self specialPointsDraw:specialPointsArray3:3];
    [self specialPointsDraw:specialPointsArray4:4];
    [self specialPointsDraw:specialPointsArray5:5];
    [self specialPointsDraw:specialPointsArray6:6];
	
   // MKPolyline *polyLine;
   for (_routeLine in routeLineArray){
        [_mapView addOverlay:_routeLine];
    }
}

-(MKPolyline*) normalPointsDraw:(NSArray*) normalPointsArray1{
//    isUserLoadRoute = YES;
	MKMapPoint* pointArr = malloc(sizeof(CLLocationCoordinate2D) * normalPointsArray1.count);
    
	for(int idx = 0; idx < normalPointsArray1.count; idx++)
	{
		NSArray* currentPoint = [normalPointsArray1 objectAtIndex:idx];
        
		CLLocationDegrees latitude  = [[currentPoint objectAtIndex:0] doubleValue];
		CLLocationDegrees longitude = [[currentPoint objectAtIndex:1] doubleValue];
		CLLocationCoordinate2D coordinate = CLLocationCoordinate2DMake(latitude, longitude);
		MKMapPoint point = MKMapPointForCoordinate(coordinate);
        
		if (isFirstRect) {
			northEastPoint = point;
			southWestPoint = point;
            isFirstRect = NO;
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
    
	self.routeLine = [MKPolyline polylineWithPoints:pointArr count:normalPointsArray1.count];
	_routeRect = MKMapRectMake(southWestPoint.x, southWestPoint.y, northEastPoint.x - southWestPoint.x, northEastPoint.y - southWestPoint.y);
    
	free(pointArr);
    
    return self.routeLine;
    
//	if (nil != self.routeLine) {
//		[self.mapView addOverlay:self.routeLine];
//	}
//    self.routeLine = nil;

}

-(void) specialPointsDraw:(NSArray*) specialPointsArray: (int) pointType{
	for(int idx = 0; idx < specialPointsArray.count; idx++)
	{
		NSArray* currentPoint = [specialPointsArray objectAtIndex:idx];
        
		CLLocationDegrees latitude  = [[currentPoint objectAtIndex:0] doubleValue];
		CLLocationDegrees longitude = [[currentPoint objectAtIndex:1] doubleValue];
		CLLocationCoordinate2D coordinate = CLLocationCoordinate2DMake(latitude, longitude);
        
        circleSpecial = [MKCircle circleWithCenterCoordinate:coordinate radius:6];
        switch (pointType) {
            case 1:
                [circleSpecial setTitle:@"-3"];
                [self.mapView addOverlay:circleSpecial];
                break;
            case 2:
                [circleSpecial setTitle:@"-2"];
                [self.mapView addOverlay:circleSpecial];
                break;
            case 3:
                [circleSpecial setTitle:@"-1"];
                [self.mapView addOverlay:circleSpecial];
                break;
            case 4:
                [circleSpecial setTitle:@"1"];
                [self.mapView addOverlay:circleSpecial];
                break;
            case 5:
                [circleSpecial setTitle:@"2"];
                [self.mapView addOverlay:circleSpecial];
                break;
            case 6:
                [circleSpecial setTitle:@"3"];
                [self.mapView addOverlay:circleSpecial];
                break;
            default:
                break;
        }
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
}


#pragma mark MKMapViewDelegate
- (MKOverlayView *)mapView:(MKMapView *)mapView viewForOverlay:(id <MKOverlay>)overlay
{
	
	if(overlay == self.routeLine)
	{
        
        MKOverlayView* overlayView = nil;
        MKPolylineView *routeLineView = [[MKPolylineView alloc] initWithPolyline:self.routeLine];
        routeLineView.fillColor = [UIColor blackColor];
        routeLineView.strokeColor = [UIColor blackColor];
        routeLineView.lineWidth = 3;
    
		overlayView = routeLineView;
		
        return overlayView;
	}
    
    MKCircle *circle = overlay;
    MKCircleView *circleView = [[MKCircleView alloc] initWithCircle:overlay];
    if([circle.title isEqualToString:@"-3"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:1.0 green:0.0 blue:0.0 alpha:1.0];
    }
    else if([circle.title isEqualToString:@"-2"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:1.0 green:0.0 blue:0.0 alpha:0.6];
    }
    else if([circle.title isEqualToString:@"-1"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:1.0 green:0.0 blue:0.0 alpha:0.3];
    }
    else if([circle.title isEqualToString:@"1"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:0.0 green:1.0 blue:0.0 alpha:0.3];
    }
    else if([circle.title isEqualToString:@"2"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:0.0 green:1.0 blue:0.0 alpha:0.6];
    }
    else if([circle.title isEqualToString:@"3"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:0.0 green:1.0 blue:0.0 alpha:1.0];
    }
    
    return circleView;
}
@end
